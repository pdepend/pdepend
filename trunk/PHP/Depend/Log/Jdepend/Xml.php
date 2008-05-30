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
class PHP_Depend_Log_Jdepend_Xml 
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
    
    /**
     * The Packages dom element.
     *
     * @type DOMElement
     * @var DOMElement $packages
     */
    protected $packages = null;
    
    /**
     * The Cycles dom element.
     *
     * @type DOMElement
     * @var DOMElement $cycles
     */
    protected $cycles = null;
    
    /**
     * The concrete classes element for the current package.
     *
     * @type DOMElement
     * @var DOMElement $concreteClasses
     */
    protected $concreteClasses = null;
    
    /**
     * The abstract classes element for the current package.
     *
     * @type DOMElement
     * @var DOMElement $abstractClasses
     */
    protected $abstractClasses = null;
    
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

        $jdepend = $dom->createElement('PDepend');
        
        $this->packages = $jdepend->appendChild($dom->createElement('Packages'));
        $this->cycles   = $jdepend->appendChild($dom->createElement('Cycles'));
        
        foreach ($this->code as $node)
        {
            $node->accept($this);
        }
        
        $dom->appendChild($jdepend);
        $dom->save($this->fileName);
    }
    
    public function visitClass(PHP_Depend_Code_Class $class)
    {
        $doc = $this->packages->ownerDocument;
        
        $classXml = $doc->createElement('Class');
        $classXml->setAttribute('sourceFile', (string) $class->getSourceFile());
        $classXml->appendChild($doc->createTextNode($class->getName()));
        
        if ($class->isAbstract()) {
            $this->abstractClasses->appendChild($classXml);
        } else {
            $this->concreteClasses->appendChild($classXml);
        }
    }
    
    public function visitFunction(PHP_Depend_Code_Function $function)
    {

    }
    
    public function visitInterface(PHP_Depend_Code_Interface $interface)
    {
        $doc = $this->abstractClasses->ownerDocument;
        
        $classXml = $doc->createElement('Class');
        $classXml->setAttribute('sourceFile', (string) $interface->getSourceFile());
        $classXml->appendChild($doc->createTextNode($interface->getName()));
        
        $this->abstractClasses->appendChild($classXml);
    }
    
    public function visitMethod(PHP_Depend_Code_Method $method)
    {

    }
    
    public function visitPackage(PHP_Depend_Code_Package $package)
    {
        $doc = $this->packages->ownerDocument;
        
        $this->concreteClasses = $doc->createElement('ConcreteClasses');
        $this->abstractClasses = $doc->createElement('AbstractClasses');

        $packageXml = $doc->createElement('Package');
        $packageXml->setAttribute('name', $package->getName());
        $packageXml->appendChild($this->concreteClasses);
        $packageXml->appendChild($this->abstractClasses);
        
        foreach ($package->getTypes() as $type) {
            $type->accept($this);
        }
            
        $this->packages->appendChild($packageXml);
    }
    
    public function visitProperty(PHP_Depend_Code_Property $property)
    {
        
    }
}