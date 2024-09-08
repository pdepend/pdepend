<?php

/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2017 Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Report\Jdepend;

use DOMDocument;
use PDepend\Metrics\Analyzer;
use PDepend\Metrics\Analyzer\DependencyAnalyzer;
use PDepend\Report\CodeAwareGenerator;
use PDepend\Report\FileAwareGenerator;
use PDepend\Report\NoLogOutputException;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\ASTVisitor\AbstractASTVisitor;
use PDepend\Util\FileUtil;
use PDepend\Util\ImageConvert;
use PDepend\Util\Utf8Util;
use RuntimeException;

/**
 * Generates a chart with the aggregated metrics.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class Chart extends AbstractASTVisitor implements CodeAwareGenerator, FileAwareGenerator
{
    /** The output file name. */
    private string $logFile;

    /**
     * The context source code.
     *
     * @var ASTArtifactList<ASTNamespace>
     */
    private ASTArtifactList $code;

    /** The context analyzer instance. */
    private DependencyAnalyzer $analyzer;

    /**
     * Sets the output log file.
     *
     * @param string $logFile The output log file.
     */
    public function setLogFile(string $logFile): void
    {
        $this->logFile = $logFile;
    }

    /**
     * Returns an <b>array</b> with accepted analyzer types. These types can be
     * concrete analyzer classes or one of the descriptive analyzer interfaces.
     *
     * @return array<string>
     */
    public function getAcceptedAnalyzers(): array
    {
        return ['pdepend.analyzer.dependency'];
    }

    /**
     * Sets the context code nodes.
     *
     * @param ASTArtifactList<ASTNamespace> $artifacts
     */
    public function setArtifacts(ASTArtifactList $artifacts): void
    {
        $this->code = $artifacts;
    }

    /**
     * Adds an analyzer to log. If this logger accepts the given analyzer it
     * with return <b>true</b>, otherwise the return value is <b>false</b>.
     *
     * @param Analyzer $analyzer The analyzer to log.
     */
    public function log(Analyzer $analyzer): bool
    {
        if ($analyzer instanceof DependencyAnalyzer) {
            $this->analyzer = $analyzer;

            return true;
        }

        return false;
    }

    /**
     * Closes the logger process and writes the output file.
     *
     * @throws NoLogOutputException If the no log target exists.
     * @throws RuntimeException
     */
    public function close(): void
    {
        // Check for configured log file
        if (!isset($this->logFile)) {
            throw new NoLogOutputException($this);
        }

        $bias = 0.1;

        $svg = new DOMDocument('1.0', 'UTF-8');
        $templatePath = __DIR__ . '/chart.svg';
        $template = file_get_contents($templatePath);
        if (!$template) {
            throw new RuntimeException('Missing ' . $templatePath);
        }
        $svg->loadXML($template);

        $layer = $svg->getElementById('jdepend.layer');
        if (!$layer) {
            throw new RuntimeException('Missing jdepend.layer element');
        }

        $bad = $svg->getElementById('jdepend.bad');
        if (!$bad?->parentNode) {
            throw new RuntimeException('Missing jdepend.bad element');
        }
        $bad->removeAttribute('xml:id');

        $good = $svg->getElementById('jdepend.good');
        if (!$good?->parentNode) {
            throw new RuntimeException('Missing jdepend.good element');
        }
        $good->removeAttribute('xml:id');

        $legendTemplate = $svg->getElementById('jdepend.legend');
        if (!$legendTemplate?->parentNode) {
            throw new RuntimeException('Missing legend parent element');
        }
        $legendTemplate->removeAttribute('xml:id');

        foreach ($this->getItems() as $item) {
            $element = $item['distance'] < $bias ? $good : $bad;
            $ellipse = $element->cloneNode(true);

            $a = $item['ratio'] / 15;
            $e = (50 - $item['ratio']) + ($item['abstraction'] * 320);
            $f = (20 - $item['ratio'] + 190) - ($item['instability'] * 190);

            $transform = "matrix({$a}, 0, 0, {$a}, {$e}, {$f})";

            $ellipse->setAttribute('id', uniqid('pdepend_'));
            $ellipse->setAttribute('title', $item['name']);
            $ellipse->setAttribute('transform', $transform);

            $layer->appendChild($ellipse);

            $result = preg_match('#\\\\([^\\\\]+)$#', $item['name'], $found);
            if ($result) {
                $angle = random_int(0, 314) / 100 - 1.57;
                $legend = $legendTemplate->cloneNode(true);
                $legend->setAttribute('x', (string) ($e + $item['ratio'] * (1 + cos($angle))));
                $legend->setAttribute('y', (string) ($f + $item['ratio'] * (1 + sin($angle))));
                $legend->nodeValue = $found[1];
                $legendTemplate->parentNode->appendChild($legend);
            }
        }

        $bad->parentNode->removeChild($bad);
        $good->parentNode->removeChild($good);
        $legendTemplate->parentNode->removeChild($legendTemplate);

        $temp = FileUtil::getSysTempDir();
        $temp .= '/' . uniqid('pdepend_') . '.svg';
        $svg->save($temp);

        ImageConvert::convert($temp, $this->logFile);

        // Remove temp file
        unlink($temp);
    }

    /**
     * @return array<int, array{size: int, abstraction: int, instability: int, distance: int, name: string, ratio: int}>
     */
    private function getItems(): array
    {
        $items = [];
        foreach ($this->code as $namespace) {
            if (!$namespace->isUserDefined()) {
                continue;
            }

            $metrics = $this->analyzer->getStats($namespace);

            if (count($metrics) === 0) {
                continue;
            }

            $items[] = [
                'size' => $metrics['cc'] + $metrics['ac'],
                'abstraction' => $metrics['a'],
                'instability' => $metrics['i'],
                'distance' => $metrics['d'],
                'ratio' => 15,
                'name' => Utf8Util::ensureEncoding($namespace->getImage()),
            ];
        }

        // Sort items by size
        usort(
            $items,
            static fn(array $a, array $b) => $a['size'] <=> $b['size'],
        );

        if ($items) {
            $max = $items[count($items) - 1]['size'];
            $min = $items[0]['size'];

            $diff = (($max - $min) / 10);

            foreach ($items as &$item) {
                if ($diff !== 0) {
                    $item['ratio'] = 5 + (($item['size'] - $min) / $diff);
                }
            }
        }

        return $items;
    }
}
