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

namespace PDepend\Report\Overview;

use DOMDocument;
use PDepend\Metrics\Analyzer;
use PDepend\Metrics\Analyzer\CouplingAnalyzer;
use PDepend\Metrics\Analyzer\CyclomaticComplexityAnalyzer;
use PDepend\Metrics\Analyzer\InheritanceAnalyzer;
use PDepend\Metrics\Analyzer\NodeCountAnalyzer;
use PDepend\Metrics\Analyzer\NodeLocAnalyzer;
use PDepend\Report\FileAwareGenerator;
use PDepend\Report\NoLogOutputException;
use PDepend\Util\FileUtil;
use PDepend\Util\ImageConvert;
use RuntimeException;

/**
 * This logger generates a system overview pyramid, as described in the book
 * <b>Object-Oriented Metrics in Practice</b>.
 *
 * http://www.springer.com/computer/programming/book/978-3-540-24429-5
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class Pyramid implements FileAwareGenerator
{
    /** The output file name. */
    private string $logFile;

    /** The used coupling analyzer. */
    private CouplingAnalyzer $coupling;

    /** The used cyclomatic complexity analyzer. */
    private CyclomaticComplexityAnalyzer $cyclomaticComplexity;

    /** The used inheritance analyzer. */
    private InheritanceAnalyzer $inheritance;

    /** The used node count analyzer. */
    private NodeCountAnalyzer $nodeCount;

    /** The used node loc analyzer. */
    private NodeLocAnalyzer $nodeLoc;

    /**
     * Holds defined thresholds for the computed proportions. This set is based
     * on java thresholds, we should find better values for php projects.
     *
     * @var array<string, array<int, float>>
     */
    private array $thresholds = [
        'cyclo-loc' => [0.16, 0.20, 0.24],
        'loc-nom' => [7, 10, 13],
        'nom-noc' => [4, 7, 10],
        'noc-nop' => [6, 17, 26],
        'calls-nom' => [2.01, 2.62, 3.2],
        'fanout-calls' => [0.56, 0.62, 0.68],
        'andc' => [0.25, 0.41, 0.57],
        'ahh' => [0.09, 0.21, 0.32],
    ];

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
        return [
            'pdepend.analyzer.coupling',
            'pdepend.analyzer.cyclomatic_complexity',
            'pdepend.analyzer.inheritance',
            'pdepend.analyzer.node_count',
            'pdepend.analyzer.node_loc',
        ];
    }

    /**
     * Adds an analyzer to log. If this logger accepts the given analyzer it
     * with return <b>true</b>, otherwise the return value is <b>false</b>.
     *
     * @param Analyzer $analyzer The analyzer to log.
     */
    public function log(Analyzer $analyzer): bool
    {
        if ($analyzer instanceof CyclomaticComplexityAnalyzer) {
            $this->cyclomaticComplexity = $analyzer;
        } elseif ($analyzer instanceof CouplingAnalyzer) {
            $this->coupling = $analyzer;
        } elseif ($analyzer instanceof InheritanceAnalyzer) {
            $this->inheritance = $analyzer;
        } elseif ($analyzer instanceof NodeCountAnalyzer) {
            $this->nodeCount = $analyzer;
        } elseif ($analyzer instanceof NodeLocAnalyzer) {
            $this->nodeLoc = $analyzer;
        } else {
            return false;
        }

        return true;
    }

    /**
     * Closes the logger process and writes the output file.
     *
     * @throws NoLogOutputException
     */
    public function close(): void
    {
        // Check for configured log file
        if (!isset($this->logFile)) {
            throw new NoLogOutputException($this);
        }

        $metrics = $this->collectMetrics();
        $proportions = $this->computeProportions($metrics);

        $svg = new DOMDocument('1.0', 'UTF-8');
        $templatePath = __DIR__ . '/pyramid.svg';
        $template = file_get_contents($templatePath);
        if (!$template) {
            throw new RuntimeException('Missing ' . $templatePath);
        }
        $svg->loadXML($template);

        $items = $proportions + $metrics;
        foreach ($items as $name => $value) {
            $node = $svg->getElementById("pdepend.{$name}");
            if (!$node) {
                throw new RuntimeException("Document is missing the pdepend.{$name} elsement");
            }
            $node->nodeValue = (string) $value;

            if (($threshold = $this->computeThreshold($name, $value)) === null) {
                continue;
            }
            if (($color = $svg->getElementById("threshold.{$threshold}")) === null) {
                continue;
            }
            if (($rect = $svg->getElementById("rect.{$name}")) === null) {
                continue;
            }
            preg_match('/fill:(#[^;"]+)/', $color->getAttribute('style'), $match);
            if (!isset($match[1])) {
                continue;
            }

            $style = $rect->getAttribute('style');
            $style = preg_replace('/fill:#[^;"]+/', "fill:{$match[1]}", $style) ?? $style;
            $rect->setAttribute('style', $style);
        }

        $temp = FileUtil::getSysTempDir();
        $temp .= '/' . uniqid('pdepend_') . '.svg';
        $svg->save($temp);

        ImageConvert::convert($temp, $this->logFile);

        // Remove temp file
        unlink($temp);
    }

    /**
     * Computes the threshold (low, average, high) for the given value and metric.
     * If no threshold is defined for the given name, this method will return
     * <b>null</b>.
     *
     * @param string $name The metric/field identfier.
     * @param float $value The metric/field value.
     */
    private function computeThreshold(string $name, float $value): ?string
    {
        if (!isset($this->thresholds[$name])) {
            return null;
        }

        $threshold = $this->thresholds[$name];
        if ($value <= $threshold[0]) {
            return 'low';
        }
        if ($value >= $threshold[2]) {
            return 'high';
        }
        $low = $value - $threshold[0];
        $avg = $threshold[1] - $value;

        if ($low < $avg) {
            return 'low';
        }

        return 'average';
    }

    /**
     * Computes the proportions between the given metrics.
     *
     * @param array<string, float> $metrics The aggregated project metrics.
     * @return array<string, float>
     */
    private function computeProportions(array $metrics): array
    {
        $orders = [
            ['cyclo', 'loc', 'nom', 'noc', 'nop'],
            ['fanout', 'calls', 'nom'],
        ];

        $proportions = [];
        foreach ($orders as $names) {
            for ($i = 1, $c = count($names); $i < $c; ++$i) {
                $value1 = $metrics[$names[$i]];
                $value2 = $metrics[$names[$i - 1]];

                $identifier = "{$names[$i - 1]}-{$names[$i]}";

                $proportions[$identifier] = 0;
                if ($value1 > 0) {
                    $proportions[$identifier] = round($value2 / $value1, 3);
                }
            }
        }

        return $proportions;
    }

    /**
     * Aggregates the required metrics from the registered analyzers.
     *
     * @return array<string, float|int>
     * @throws RuntimeException If one of the required analyzers isn't set.
     */
    private function collectMetrics(): array
    {
        if (!isset($this->coupling)) {
            throw new RuntimeException('Missing Coupling analyzer.');
        }
        if (!isset($this->cyclomaticComplexity)) {
            throw new RuntimeException('Missing Cyclomatic Complexity analyzer.');
        }
        if (!isset($this->inheritance)) {
            throw new RuntimeException('Missing Inheritance analyzer.');
        }
        if (!isset($this->nodeCount)) {
            throw new RuntimeException('Missing Node Count analyzer.');
        }
        if (!isset($this->nodeLoc)) {
            throw new RuntimeException('Missing Node LOC analyzer.');
        }

        $coupling = $this->coupling->getProjectMetrics();
        $cyclomatic = $this->cyclomaticComplexity->getProjectMetrics();
        $inheritance = $this->inheritance->getProjectMetrics();
        $nodeCount = $this->nodeCount->getProjectMetrics();
        $nodeLoc = $this->nodeLoc->getProjectMetrics();

        return [
            'cyclo' => $cyclomatic['ccn2'],
            'loc' => $nodeLoc['eloc'],
            'nom' => ($nodeCount['nom'] + $nodeCount['nof']),
            'noc' => $nodeCount['noc'],
            'nop' => $nodeCount['nop'],
            'ahh' => round($inheritance['ahh'], 3),
            'andc' => round($inheritance['andc'], 3),
            'fanout' => $coupling['fanout'],
            'calls' => $coupling['calls'],
        ];
    }
}
