<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

namespace PHP\Depend\Report\Summary;

use PHP\Depend\Metrics\Analyzer;
use PHP\Depend\Metrics\AnalyzerNodeAware;
use PHP\Depend\Metrics\AnalyzerProjectAware;
use PHP\Depend\Report\GeneratorCodeAware;
use PHP\Depend\Report\GeneratorFileAware;
use PHP\Depend\Report\NoLogOutputException;
use PHP\Depend\Source\AST\AbstractASTArtifact;
use PHP\Depend\Source\AST\ASTArtifactList;
use PHP\Depend\Source\AST\ASTClass;
use PHP\Depend\Source\AST\ASTCompilationUnit;
use PHP\Depend\Source\AST\ASTFunction;
use PHP\Depend\Source\AST\ASTInterface;
use PHP\Depend\Source\AST\ASTMethod;
use PHP\Depend\Source\AST\ASTNamespace;
use PHP\Depend\TreeVisitor\AbstractTreeVisitor;

/**
 * This logger generates a summary xml document with aggregated project, class,
 * method and file metrics.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
class Xml extends AbstractTreeVisitor implements GeneratorCodeAware, GeneratorFileAware
{
    /**
     * The type of this class.
     */
    const CLAZZ = __CLASS__;

    /**
     * The log output file.
     *
     * @var string
     */
    private $logFile = null;

    /**
     * The raw {@link \PHP\Depend\Source\AST\ASTNamespace} instances.
     *
     * @var \PHP\Depend\Source\AST\ASTArtifactList
     */
    protected $code = null;

    /**
     * Set of all analyzed files.
     *
     * @var \PHP\Depend\Source\AST\ASTCompilationUnit[]
     */
    protected $fileSet = array();

    /**
     * List of all analyzers that implement the node aware interface
     * {@link \PHP\Depend\Metrics\AnalyzerNodeAware}.
     *
     * @var \PHP\Depend\Metrics\AnalyzerNodeAware[]
     */
    private $nodeAwareAnalyzers = array();

    /**
     * List of all analyzers that implement the node aware interface
     * {@link \PHP\Depend\Metrics\AnalyzerProjectAware}.
     *
     * @var \PHP\Depend\Metrics\AnalyzerProjectAware[]
     */
    private $projectAwareAnalyzers = array();

    /**
     * The internal used xml stack.
     *
     * @var DOMElement[]
     */
    private $xmlStack = array();

    /**
     * Sets the output log file.
     *
     * @param string $logFile The output log file.
     *
     * @return void
     */
    public function setLogFile($logFile)
    {
        $this->logFile = $logFile;
    }

    /**
     * Returns an <b>array</b> with accepted analyzer types. These types can be
     * concrete analyzer classes or one of the descriptive analyzer interfaces.
     *
     * @return array(string)
     */
    public function getAcceptedAnalyzers()
    {
        return array(
            'PHP\\Depend\\Metrics\\AnalyzerNodeAware',
            'PHP\\Depend\\Metrics\\AnalyzerProjectAware'
        );
    }

    /**
     * Sets the context code nodes.
     *
     * @param \PHP\Depend\Source\AST\ASTArtifactList $artifacts
     * @return void
     */
    public function setArtifacts(ASTArtifactList $artifacts)
    {
        $this->code = $artifacts;
    }

    /**
     * Adds an analyzer to log. If this logger accepts the given analyzer it
     * with return <b>true</b>, otherwise the return value is <b>false</b>.
     *
     * @param \PHP\Depend\Metrics\Analyzer $analyzer The analyzer to log.
     * @return boolean
     */
    public function log(Analyzer $analyzer)
    {
        $accepted = false;
        if ($analyzer instanceof AnalyzerProjectAware) {
            $this->projectAwareAnalyzers[] = $analyzer;

            $accepted = true;
        }
        if ($analyzer instanceof AnalyzerNodeAware) {
            $this->nodeAwareAnalyzers[] = $analyzer;

            $accepted = true;
        }
        return $accepted;
    }

    /**
     * Closes the logger process and writes the output file.
     *
     * @return void
     * @throws \PHP\Depend\Report\NoLogOutputException If the no log target exists.
     */
    public function close()
    {
        if ($this->logFile === null) {
            throw new NoLogOutputException($this);
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');

        $dom->formatOutput = true;

        $metrics = $dom->createElement('metrics');
        $metrics->setAttribute('generated', date('Y-m-d\TH:i:s'));
        $metrics->setAttribute('pdepend', '@package_version@');

        foreach ($this->getProjectMetrics() as $name => $value) {
            $metrics->setAttribute($name, $value);
        }

        array_push($this->xmlStack, $metrics);

        foreach ($this->code as $node) {
            $node->accept($this);
        }

        if (count($this->fileSet) > 0) {
            $filesXml = $dom->createElement('files');
            foreach ($this->fileSet as $file) {
                $fileXml = $dom->createElement('file');
                $fileXml->setAttribute('name', $file->getFileName());

                $this->writeNodeMetrics($fileXml, $file);

                $filesXml->appendChild($fileXml);
            }
            $metrics->insertBefore($filesXml, $metrics->firstChild);
        }

        $dom->appendChild($metrics);

        $dom->save($this->logFile);
    }

    /**
     * Returns an array with all collected project metrics.
     *
     * @return array(string=>mixed)
     * @since 0.9.10
     */
    private function getProjectMetrics()
    {
        $projectMetrics = array();
        foreach ($this->projectAwareAnalyzers as $analyzer) {
            $projectMetrics = array_merge(
                $projectMetrics,
                $analyzer->getProjectMetrics()
            );
        }
        ksort($projectMetrics);

        return $projectMetrics;
    }

    /**
     * Visits a class node.
     *
     * @param \PHP\Depend\Source\AST\ASTClass $class
     * @return void
     */
    public function visitClass(ASTClass $class)
    {
        if (!$class->isUserDefined()) {
            return;
        }

        $xml = end($this->xmlStack);
        $doc = $xml->ownerDocument;

        $classXml = $doc->createElement('class');
        $classXml->setAttribute('name', $class->getName());

        $this->writeNodeMetrics($classXml, $class);
        $this->writeFileReference($classXml, $class->getSourceFile());

        $xml->appendChild($classXml);

        array_push($this->xmlStack, $classXml);

        foreach ($class->getMethods() as $method) {
            $method->accept($this);
        }
        foreach ($class->getProperties() as $property) {
            $property->accept($this);
        }

        array_pop($this->xmlStack);
    }

    /**
     * Visits a function node.
     *
     * @param \PHP\Depend\Source\AST\ASTFunction $function
     * @return void
     */
    public function visitFunction(ASTFunction $function)
    {
        $xml = end($this->xmlStack);
        $doc = $xml->ownerDocument;

        $functionXml = $doc->createElement('function');
        $functionXml->setAttribute('name', $function->getName());

        $this->writeNodeMetrics($functionXml, $function);
        $this->writeFileReference($functionXml, $function->getSourceFile());

        $xml->appendChild($functionXml);
    }

    /**
     * Visits a code interface object.
     *
     * @param \PHP\Depend\Source\AST\ASTInterface $interface
     * @return void
     */
    public function visitInterface(ASTInterface $interface)
    {
        // Empty implementation, because we don't want interface methods.
    }

    /**
     * Visits a method node.
     *
     * @param \PHP\Depend\Source\AST\ASTMethod $method
     * @return void
     */
    public function visitMethod(ASTMethod $method)
    {
        $xml = end($this->xmlStack);
        $doc = $xml->ownerDocument;

        $methodXml = $doc->createElement('method');
        $methodXml->setAttribute('name', $method->getName());

        $this->writeNodeMetrics($methodXml, $method);

        $xml->appendChild($methodXml);
    }

    /**
     * Visits a package node.
     *
     * @param \PHP\Depend\Source\AST\ASTNamespace $namespace
     * @return void
     */
    public function visitNamespace(ASTNamespace $namespace)
    {
        $xml = end($this->xmlStack);
        $doc = $xml->ownerDocument;

        $packageXml = $doc->createElement('package');
        $packageXml->setAttribute('name', $namespace->getName());

        $this->writeNodeMetrics($packageXml, $namespace);

        array_push($this->xmlStack, $packageXml);

        foreach ($namespace->getTypes() as $type) {
            $type->accept($this);
        }
        foreach ($namespace->getFunctions() as $function) {
            $function->accept($this);
        }

        array_pop($this->xmlStack);

        if ($packageXml->firstChild === null) {
            return;
        }

        $xml->appendChild($packageXml);
    }

    /**
     * Aggregates all metrics for the given <b>$node</b> instance and adds them
     * to the <b>\DOMElement</b>
     *
     * @param \DOMElement $xml
     * @param \PHP\Depend\Source\AST\AbstractASTArtifact $node
     * @return void
     */
    protected function writeNodeMetrics(\DOMElement $xml, AbstractASTArtifact $node)
    {
        $metrics = array();
        foreach ($this->nodeAwareAnalyzers as $analyzer) {
            $metrics = array_merge($metrics, $analyzer->getNodeMetrics($node));
        }
        ksort($metrics);

        foreach ($metrics as $name => $value) {
            $xml->setAttribute($name, $value);
        }
    }

    /**
     * Appends a file reference element to the given <b>$xml</b> element.
     *
     * <code>
     *   <class name="PHP_Depend">
     *     <file name="PHP/Depend.php" />
     *   </class>
     * </code>
     *
     * @param \DOMElement $xml  The parent xml element.
     * @param \PHP\Depend\Source\AST\ASTCompilationUnit $compilationUnit The code file instance.
     * @return void
     */
    protected function writeFileReference(\DOMElement $xml, ASTCompilationUnit $compilationUnit = null)
    {
        if (in_array($compilationUnit, $this->fileSet, true) === false) {
            $this->fileSet[] = $compilationUnit;
        }

        $fileXml = $xml->ownerDocument->createElement('file');
        $fileXml->setAttribute('name', $compilationUnit->getFileName());

        $xml->appendChild($fileXml);
    }
}
