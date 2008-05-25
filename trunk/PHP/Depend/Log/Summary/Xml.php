<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pmanuel-pichler.de>.
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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Log
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/NodeVisitor.php';
require_once 'PHP/Depend/Log/LoggerI.php';
require_once 'PHP/Depend/Metrics/ResultSet/NodeAwareI.php';
require_once 'PHP/Depend/Metrics/ResultSet/ProjectAwareI.php';

/**
 * 
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Log
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Log_Summary_Xml 
    implements PHP_Depend_Code_NodeVisitor, PHP_Depend_Log_LoggerI
{
    protected $fileName = '';
    
    /**
     * The raw {@link PHP_Depend_Code_Package} instances.
     *
     * @type PHP_Depend_Code_NodeIterator
     * @var PHP_Depend_Code_NodeIterator $code
     */
    protected $code = null;
    
    /**
     * Set of all analyzed files.
     *
     * @type array<PHP_Depend_Code_File>
     * @var array(string=>PHP_Depend_Code_File) $fileSet
     */
    protected $fileSet = array();
    
    /**
     * List of all generated project metrics.
     *
     * @type array<mixed>
     * @var array(string=>mixed) $projectMetrics
     */
    protected $projectMetrics = array();
    
    /**
     * List of all collected node metrics.
     *
     * @type array<array>
     * @var array(string=>array) $nodeMetrics
     */
    protected $nodeMetrics = array();
    
    protected $xmlStack = array();
    
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }
    
    public function setCode(PHP_Depend_Code_NodeIterator $code)
    {
        $this->code = $code;
    }
    
    public function log(PHP_Depend_Metrics_ResultSetI $resultSet)
    {
        $accept = false;
        
        if ($resultSet instanceof PHP_Depend_Metrics_ResultSet_ProjectAwareI) {
            // Collect all project metrics
            $this->projectMetrics = array_merge(
                $this->projectMetrics,
                $resultSet->getProjectMetrics()
            );
            
            $accept = true;
        }
        if ($resultSet instanceof PHP_Depend_Metrics_ResultSet_NodeAwareI) {
            // Collect all node metrics
            $this->nodeMetrics = array_merge_recursive(
                $this->nodeMetrics,
                $resultSet->getAllNodeMetrics()
            );
            
            $accept = true;
        }
        
        return $accept;
    }
    
    public function close()
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        
        ksort($this->projectMetrics);
        
        $metrics = $dom->createElement('metrics');
        
        foreach ($this->projectMetrics as $name => $value) {
            $metrics->setAttribute($name, $value);
        }
        
        array_push($this->xmlStack, $metrics);
        
        foreach ($this->code as $node)
        {
            $node->accept($this);
        }
        
        if (count($this->fileSet) > 0) {
            $filesXml = $dom->createElement('files');
            foreach ($this->fileSet as $file) {
                $fileXml = $dom->createElement('file');
                $fileXml->setAttribute('name', $file->getFileName());
                
                $this->writeNodeMetrics($fileXml, $file->getUUID());
            
                $filesXml->appendChild($fileXml);
            }
            $metrics->insertBefore($filesXml, $metrics->firstChild);
        }
        
        $dom->appendChild($metrics);
        
        $dom->save($this->fileName);
    }
    
    public function visitClass(PHP_Depend_Code_Class $class)
    {
        $xml = end($this->xmlStack);
        $doc = $xml->ownerDocument;
        
        $classXml = $doc->createElement('class');
        $classXml->setAttribute('name', $class->getName());
            
        $this->writeNodeMetrics($classXml, $class->getUUID());
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
    
    public function visitFunction(PHP_Depend_Code_Function $function)
    {
        $xml = end($this->xmlStack);
        $doc = $xml->ownerDocument;
        
        $functionXml = $doc->createElement('function');
        $functionXml->setAttribute('name', $function->getName());
            
        $this->writeNodeMetrics($functionXml, $function->getUUID());
        $this->writeFileReference($functionXml, $function->getSourceFile());
            
        $xml->appendChild($functionXml);
    }
    
    public function visitInterface(PHP_Depend_Code_Interface $interface)
    {
        
    }
    
    public function visitMethod(PHP_Depend_Code_Method $method)
    {
        $xml = end($this->xmlStack);
        $doc = $xml->ownerDocument;
        
        $methodXml = $doc->createElement('method');
        $methodXml->setAttribute('name', $method->getName());
            
        $this->writeNodeMetrics($methodXml, $method->getUUID());
            
        $xml->appendChild($methodXml);
    }
    
    public function visitPackage(PHP_Depend_Code_Package $package)
    {
        $xml = end($this->xmlStack);
        $doc = $xml->ownerDocument;
        
        $packageXml = $doc->createElement('package');
        $packageXml->setAttribute('name', $package->getName());
            
        $this->writeNodeMetrics($packageXml, $package->getUUID());
        
        array_push($this->xmlStack, $packageXml);
            
        foreach ($package->getTypes() as $type) {
            $type->accept($this);
        }
        foreach ($package->getFunctions() as $function) {
            $function->accept($this);
        }
        
        array_pop($this->xmlStack);
            
        $xml->appendChild($packageXml);
    }
    
    public function visitProperty(PHP_Depend_Code_Property $property)
    {
        
    }
    
    protected function writeNodeMetrics(DOMElement $xml, $uuid)
    {
        if (!isset($this->nodeMetrics[$uuid])) {
            return;
        }
        
        $metrics = $this->nodeMetrics[$uuid];
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
     * @param DOMElement           $xml  The parent xml element.
     * @param PHP_Depend_Code_File $file The code file instance.
     * 
     * @return void
     */
    protected function writeFileReference(DOMElement $xml, PHP_Depend_Code_File $file = null)
    {
        if ($file === null) {
            return;
        }
        
        if (in_array($file, $this->fileSet, true) === false) {
            $this->fileSet[] = $file;
        }
        
        $fileXml = $xml->ownerDocument->createElement('file');
        $fileXml->setAttribute('name', $file->getFileName());
        
        $xml->appendChild($fileXml);
    }
}