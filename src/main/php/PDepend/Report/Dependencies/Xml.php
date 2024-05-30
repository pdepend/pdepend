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

namespace PDepend\Report\Dependencies;

use DOMDocument;
use DOMElement;
use PDepend\Metrics\Analyzer;
use PDepend\Metrics\Analyzer\ClassDependencyAnalyzer;
use PDepend\Report\CodeAwareGenerator;
use PDepend\Report\FileAwareGenerator;
use PDepend\Report\NoLogOutputException;
use PDepend\Source\AST\AbstractASTArtifact;
use PDepend\Source\AST\AbstractASTClassOrInterface;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTCompilationUnit;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\AST\ASTTrait;
use PDepend\Source\ASTVisitor\AbstractASTVisitor;
use PDepend\Util\Utf8Util;
use RuntimeException;

/**
 * This logger generates a summary xml document with aggregated project, class,
 * method and file dependencies.
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

    /** The log output file. */
    private string $logFile;

    private ClassDependencyAnalyzer $dependencyAnalyzer;

    /**
     * The internal used xml stack.
     *
     * @var DOMElement[]
     */
    private array $xmlStack = [];

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
            'pdepend.analyzer.class_dependency',
        ];
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
        if ($analyzer instanceof ClassDependencyAnalyzer) {
            $this->dependencyAnalyzer = $analyzer;

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
        if (!isset($this->logFile)) {
            throw new NoLogOutputException($this);
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $dependencies = $dom->createElement('dependencies');
        $dependencies->setAttribute('generated', date('Y-m-d\TH:i:s'));
        $dependencies->setAttribute('pdepend', '@package_version@');
        $this->xmlStack[] = $dependencies;

        foreach ($this->code as $node) {
            $this->dispatch($node);
        }

        $dom->appendChild($dependencies);

        $buffer = $dom->saveXML();
        file_put_contents($this->logFile, $buffer);
    }

    /**
     * Visits a class node.
     */
    public function visitClass(ASTClass $class): void
    {
        $this->generateTypeXml($class, 'class');
    }

    /**
     * Visits a trait node.
     */
    public function visitTrait(ASTTrait $trait): void
    {
        $this->generateTypeXml($trait, 'trait');
    }

    /**
     * Generates the XML for a class or trait node.
     *
     * @throws RuntimeException
     */
    private function generateTypeXml(AbstractASTClassOrInterface $type, string $typeIdentifier): void
    {
        if (!$type->isUserDefined()) {
            return;
        }

        $xml = end($this->xmlStack);
        if (!$xml) {
            return;
        }

        $doc = $xml->ownerDocument;
        if (!$doc) {
            throw new RuntimeException('Missing owner docuemtn');
        }

        $typeXml = $doc->createElement($typeIdentifier);
        $typeXml->setAttribute('name', Utf8Util::ensureEncoding($type->getImage()));
        $xml->appendChild($typeXml);

        $this->xmlStack[] = $typeXml;
        $this->writeNodeDependencies($typeXml, $type);
        $this->writeFileReference($typeXml, $type->getCompilationUnit());
        array_pop($this->xmlStack);
    }

    /**
     * Visits a function node.
     */
    public function visitFunction(ASTFunction $function): void
    {
        // Do not care
    }

    /**
     * Visits a code interface object.
     */
    public function visitInterface(ASTInterface $interface): void
    {
        $this->generateTypeXml($interface, 'interface');
    }

    /**
     * Visits a namespace node.
     *
     * @throws RuntimeException
     */
    public function visitNamespace(ASTNamespace $namespace): void
    {
        $xml = end($this->xmlStack);
        if (!$xml) {
            return;
        }

        $doc = $xml->ownerDocument;
        if (!$doc) {
            throw new RuntimeException('Missing owner docuemtn');
        }

        $packageXml = $doc->createElement('package');
        $packageXml->setAttribute('name', Utf8Util::ensureEncoding($namespace->getImage()));

        $this->xmlStack[] = $packageXml;

        foreach ($namespace->getTypes() as $type) {
            $this->dispatch($type);
        }
        foreach ($namespace->getFunctions() as $function) {
            $this->dispatch($function);
        }

        array_pop($this->xmlStack);

        if ($packageXml->firstChild === null) {
            return;
        }

        $xml->appendChild($packageXml);
    }

    /**
     * Aggregates all dependencies for the given <b>$node</b> instance and adds them
     * to the <b>\DOMElement</b>
     *
     * @throws RuntimeException
     */
    protected function writeNodeDependencies(DOMElement $xml, AbstractASTArtifact $node): void
    {
        if (!isset($this->dependencyAnalyzer)) {
            return;
        }

        $doc = $xml->ownerDocument;
        if (!$doc) {
            throw new RuntimeException('Missing owner docuemtn');
        }

        $efferentXml = $doc->createElement('efferent');
        $xml->appendChild($efferentXml);
        foreach ($this->dependencyAnalyzer->getEfferents($node) as $type) {
            $typeXml = $doc->createElement('type');
            $namespace = $type->getNamespaceName();
            if ($namespace) {
                $typeXml->setAttribute('namespace', Utf8Util::ensureEncoding($namespace));
            }
            $typeXml->setAttribute('name', Utf8Util::ensureEncoding($type->getImage()));

            $efferentXml->appendChild($typeXml);
        }

        $afferentXml = $doc->createElement('afferent');
        $xml->appendChild($afferentXml);
        foreach ($this->dependencyAnalyzer->getAfferents($node) as $type) {
            $typeXml = $doc->createElement('type');
            $namespace = $type->getNamespaceName();
            if ($namespace) {
                $typeXml->setAttribute('namespace', Utf8Util::ensureEncoding($namespace));
            }
            $typeXml->setAttribute('name', Utf8Util::ensureEncoding($type->getImage()));

            $afferentXml->appendChild($typeXml);
        }
    }

    /**
     * Appends a file reference element to the given <b>$xml</b> element.
     *
     * <code>
     *   <class name="\PDepend\Engine">
     *     <file name="PDepend/Engine.php" />
     *   </class>
     * </code>
     *
     * @param DOMElement $xml The parent xml element.
     * @param ?ASTCompilationUnit $compilationUnit The code file instance.
     * @throws RuntimeException
     */
    protected function writeFileReference(DOMElement $xml, ?ASTCompilationUnit $compilationUnit = null): void
    {
        if ($compilationUnit && !in_array($compilationUnit, $this->fileSet, true)) {
            $this->fileSet[] = $compilationUnit;
        }

        if (!$xml->ownerDocument) {
            throw new RuntimeException('Missing owner docuemtn');
        }

        $fileXml = $xml->ownerDocument->createElement('file');
        $fileName = $compilationUnit?->getFileName();
        if ($fileName) {
            $fileXml->setAttribute('name', Utf8Util::ensureEncoding($fileName));
        }

        $xml->appendChild($fileXml);
    }
}
