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
use DOMElement;
use DOMNode;
use PDepend\Metrics\Analyzer;
use PDepend\Metrics\Analyzer\DependencyAnalyzer;
use PDepend\Report\CodeAwareGenerator;
use PDepend\Report\FileAwareGenerator;
use PDepend\Report\NoLogOutputException;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTCompilationUnit;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\ASTVisitor\AbstractASTVisitor;
use PDepend\Util\Utf8Util;
use RuntimeException;

/**
 * Generates an xml document with the aggregated metrics. The format is borrowed
 * from <a href="http://clarkware.com/software/JDepend.html">JDepend</a>.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class Xml extends AbstractASTVisitor implements CodeAwareGenerator, FileAwareGenerator
{
    /**
     * The raw {@link ASTNamespace} instances.
     *
     * @var ASTArtifactList<ASTNamespace>
     */
    protected ASTArtifactList $code;

    /**
     * Set of all analyzed files.
     *
     * @var ASTCompilationUnit[]
     */
    protected array $fileSet = [];

    /**
     * List of all generated project metrics.
     *
     * @var array<string, mixed>
     */
    protected array $projectMetrics = [];

    /**
     * List of all collected node metrics.
     *
     * @var array<string, array<string, array<int, string>>>
     */
    protected array $nodeMetrics = [];

    /** The dependency result set. */
    protected DependencyAnalyzer $analyzer;

    /** The Packages dom element. */
    protected DOMNode $packages;

    /** The Cycles dom element. */
    protected DOMNode $cycles;

    /** The concrete classes element for the current package. */
    protected DOMElement $concreteClasses;

    /** The abstract classes element for the current package. */
    protected DOMElement $abstractClasses;

    /** The output log file. */
    private string $logFile;

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
     */
    public function close(): void
    {
        // Check for configured output
        if (!isset($this->logFile)) {
            throw new NoLogOutputException($this);
        }

        $dom = new DOMDocument('1.0', 'UTF-8');

        $dom->formatOutput = true;

        $jdepend = $dom->createElement('PDepend');

        $this->packages = $jdepend->appendChild($dom->createElement('Packages'));
        $this->cycles = $jdepend->appendChild($dom->createElement('Cycles'));

        foreach ($this->code as $node) {
            $this->dispatch($node);
        }

        $dom->appendChild($jdepend);
        $buffer = $dom->saveXML();
        file_put_contents($this->logFile, $buffer);
    }

    /**
     * Visits a class node.
     *
     * @throws RuntimeException
     */
    public function visitClass(ASTClass $class): void
    {
        if (!$class->isUserDefined()) {
            return;
        }

        $doc = $this->packages->ownerDocument;
        if (!$doc) {
            throw new RuntimeException('Missing owner docuemtn');
        }

        $classXml = $doc->createElement('Class');
        $classXml->setAttribute('sourceFile', (string) $class->getCompilationUnit());
        $classXml->appendChild(
            $doc->createTextNode(
                Utf8Util::ensureEncoding($class->getImage()),
            ),
        );

        if ($class->isAbstract()) {
            $this->abstractClasses->appendChild($classXml);
        } else {
            $this->concreteClasses->appendChild($classXml);
        }
    }

    /**
     * Visits a code interface object.
     *
     * @throws RuntimeException
     */
    public function visitInterface(ASTInterface $interface): void
    {
        if (!$interface->isUserDefined()) {
            return;
        }

        $doc = $this->abstractClasses->ownerDocument;
        if (!$doc) {
            throw new RuntimeException('Missing owner docuemtn');
        }

        $classXml = $doc->createElement('Class');
        $classXml->setAttribute('sourceFile', (string) $interface->getCompilationUnit());
        $classXml->appendChild(
            $doc->createTextNode(
                Utf8Util::ensureEncoding($interface->getImage()),
            ),
        );

        $this->abstractClasses->appendChild($classXml);
    }

    /**
     * Visits a package node.
     *
     * @throws RuntimeException
     */
    public function visitNamespace(ASTNamespace $namespace): void
    {
        if (!$namespace->isUserDefined()) {
            return;
        }

        $stats = $this->analyzer->getStats($namespace);
        if (count($stats) === 0) {
            return;
        }

        $doc = $this->packages->ownerDocument;
        if (!$doc) {
            throw new RuntimeException('Missing owner docuemtn');
        }

        $this->concreteClasses = $doc->createElement('ConcreteClasses');
        $this->abstractClasses = $doc->createElement('AbstractClasses');

        $packageXml = $doc->createElement('Package');
        $packageXml->setAttribute('name', Utf8Util::ensureEncoding($namespace->getImage()));

        $statsXml = $doc->createElement('Stats');
        $statsXml->appendChild($doc->createElement('TotalClasses'))
            ->appendChild($doc->createTextNode((string) $stats['tc']));
        $statsXml->appendChild($doc->createElement('ConcreteClasses'))
            ->appendChild($doc->createTextNode((string) $stats['cc']));
        $statsXml->appendChild($doc->createElement('AbstractClasses'))
            ->appendChild($doc->createTextNode((string) $stats['ac']));
        $statsXml->appendChild($doc->createElement('Ca'))
            ->appendChild($doc->createTextNode((string) $stats['ca']));
        $statsXml->appendChild($doc->createElement('Ce'))
            ->appendChild($doc->createTextNode((string) $stats['ce']));
        $statsXml->appendChild($doc->createElement('A'))
            ->appendChild($doc->createTextNode((string) $stats['a']));
        $statsXml->appendChild($doc->createElement('I'))
            ->appendChild($doc->createTextNode((string) $stats['i']));
        $statsXml->appendChild($doc->createElement('D'))
            ->appendChild($doc->createTextNode((string) $stats['d']));

        $dependsUpon = $doc->createElement('DependsUpon');
        foreach ($this->analyzer->getEfferents($namespace) as $efferent) {
            $efferentXml = $doc->createElement('Package');
            $efferentXml->appendChild(
                $doc->createTextNode(
                    Utf8Util::ensureEncoding($efferent->getImage()),
                ),
            );

            $dependsUpon->appendChild($efferentXml);
        }

        $usedBy = $doc->createElement('UsedBy');
        foreach ($this->analyzer->getAfferents($namespace) as $afferent) {
            $afferentXml = $doc->createElement('Package');
            $afferentXml->appendChild(
                $doc->createTextNode(
                    Utf8Util::ensureEncoding($afferent->getImage()),
                ),
            );

            $usedBy->appendChild($afferentXml);
        }

        $packageXml->appendChild($statsXml);
        $packageXml->appendChild($this->concreteClasses);
        $packageXml->appendChild($this->abstractClasses);
        $packageXml->appendChild($dependsUpon);
        $packageXml->appendChild($usedBy);

        if (($cycles = $this->analyzer->getCycle($namespace)) !== null) {
            $cycleXml = $doc->createElement('Package');
            $cycleXml->setAttribute('Name', Utf8Util::ensureEncoding($namespace->getImage()));

            foreach ($cycles as $cycle) {
                $cycleXml->appendChild($doc->createElement('Package'))
                    ->appendChild(
                        $doc->createTextNode(
                            Utf8Util::ensureEncoding($cycle->getImage()),
                        ),
                    );
            }

            $this->cycles->appendChild($cycleXml);
        }

        foreach ($namespace->getTypes() as $type) {
            $this->dispatch($type);
        }

        if (
            $this->concreteClasses->firstChild === null
            && $this->abstractClasses->firstChild === null
        ) {
            return;
        }

        $this->packages->appendChild($packageXml);
    }
}
