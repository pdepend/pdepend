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
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/NodeVisitor/AbstractDefaultVisitor.php';
require_once 'PHP/Depend/Metrics/AnalyzerI.php';
require_once 'PHP/Depend/Metrics/FilterAwareI.php';
require_once 'PHP/Depend/Metrics/NodeAwareI.php';
require_once 'PHP/Depend/Metrics/ProjectAwareI.php';

/**
 * This analyzer collects different count metrics for code artifacts like 
 * classes, methods, functions or packages.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_NodeCount_Analyzer
       extends PHP_Depend_Code_NodeVisitor_AbstractDefaultVisitor
    implements PHP_Depend_Metrics_AnalyzerI,
               PHP_Depend_Metrics_FilterAwareI,
               PHP_Depend_Metrics_NodeAwareI,
               PHP_Depend_Metrics_ProjectAwareI
{
    /**
     * Number Of Packages.
     *
     * @type integer
     * @var integer $_nop
     */
    private $_nop = 0;
    
    /**
     * Number Of Classes.
     *
     * @type integer
     * @var integer $_noc
     */
    private $_noc = 0;
    
    /**
     * Number Of Interfaces.
     *
     * @type integer 
     * @var integer $_noi
     */
    private $_noi = 0;
    
    /**
     * Number Of Methods.
     *
     * @type integer
     * @var integer $_nom
     */
    private $_nom = 0;
    
    /**
     * Number Of Functions.
     *
     * @type integer
     * @var integer $_nof
     */
    private $_nof = 0;
    
    /**
     * Collected node metrics
     *
     * @type array<array>
     * @var array(string=>array) $_nodeMetrics
     */
    private $_nodeMetrics = null;
    
    /**
     * This method will return an <b>array</b> with all generated metric values 
     * for the given <b>$node</b> instance. If there are no metrics for the 
     * requested node, this method will return an empty <b>array</b>.
     * 
     * <code>
     * array(
     *     'noc'  =>  23,
     *     'nom'  =>  17,
     *     'nof'  =>  42
     * )
     * </code>
     *
     * @param PHP_Depend_Code_NodeI $node The context node instance.
     * 
     * @return array(string=>mixed)
     */
    public function getNodeMetrics(PHP_Depend_Code_NodeI $node)
    {
        $metrics = array();
        if (isset($this->_nodeMetrics[$node->getUUID()])) {
            $metrics = $this->_nodeMetrics[$node->getUUID()];
        }
        return $metrics;
    }
    
    /**
     * Provides the project summary as an <b>array</b>.
     * 
     * <code>
     * array(
     *     'nop'  =>  23,
     *     'noc'  =>  17,
     *     'noi'  =>  23,
     *     'nom'  =>  42,
     *     'nof'  =>  17
     * )
     * </code>
     *
     * @return array(string=>mixed)
     */
    public function getProjectMetrics()
    {
        return array(
            'nop'  =>  $this->_nop,
            'noc'  =>  $this->_noc,
            'noi'  =>  $this->_noi,
            'nom'  =>  $this->_nom,
            'nof'  =>  $this->_nof
        );
    }
    
    /**
     * Processes all {@link PHP_Depend_Code_Package} code nodes.
     *
     * @param PHP_Depend_Code_NodeIterator $packages All code packages.
     * 
     * @return void
     */
    public function analyze(PHP_Depend_Code_NodeIterator $packages)
    {
        // Check for previous run
        if ($this->_nodeMetrics === null) {
            // Init node metrics
            $this->_nodeMetrics = array();
            
            // Process all packages
            foreach ($packages as $package) {
                $package->accept($this);
            }
        }
    }

    /**
     * Visits a class node. 
     *
     * @param PHP_Depend_Code_Class $class The current class node.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitorI::visitClass()
     */
    public function visitClass(PHP_Depend_Code_Class $class)
    {
        // Update global class count
        ++$this->_noc;
        
        // Update parent package
        ++$this->_nodeMetrics[$class->getPackage()->getUUID()]['noc'];
        
        $this->_nodeMetrics[$class->getUUID()] = array('nom'  =>  0);
        
        foreach ($class->getMethods() as $method) {
            $method->accept($this);
        }
    }
    
    /**
     * Visits a function node. 
     *
     * @param PHP_Depend_Code_Function $function The current function node.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitorI::visitFunction()
     */
    public function visitFunction(PHP_Depend_Code_Function $function)
    {
        // Update global function count
        ++$this->_nof;
        
        // Update parent package
        ++$this->_nodeMetrics[$function->getPackage()->getUUID()]['nof'];
    }
    
    /**
     * Visits a code interface object.
     *
     * @param PHP_Depend_Code_Interface $interface The context code interface.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitorI::visitInterface()
     */
    public function visitInterface(PHP_Depend_Code_Interface $interface)
    {
        // Update global class count
        ++$this->_noi;
        
        // Update parent package
        ++$this->_nodeMetrics[$interface->getPackage()->getUUID()]['noi'];
        
        $this->_nodeMetrics[$interface->getUUID()] = array('nom'  =>  0);
        
        foreach ($interface->getMethods() as $method) {
            $method->accept($this);
        }
    }
    
    /**
     * Visits a method node. 
     *
     * @param PHP_Depend_Code_Class $method The method class node.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitorI::visitMethod()
     */
    public function visitMethod(PHP_Depend_Code_Method $method)
    {
        // Update global method count
        ++$this->_nom;
        
        $parent = $method->getParent();
        
        // Update parent class or interface
        ++$this->_nodeMetrics[$parent->getUUID()]['nom'];
        // Update parent package
        ++$this->_nodeMetrics[$parent->getPackage()->getUUID()]['nom'];
    }
    
    /**
     * Visits a package node. 
     *
     * @param PHP_Depend_Code_Class $package The package class node.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitorI::visitPackage()
     */
    public function visitPackage(PHP_Depend_Code_Package $package)
    {
        // Update package count
        ++$this->_nop;
        
        $this->_nodeMetrics[$package->getUUID()] = array(
            'noc'  =>  0,
            'noi'  =>  0,
            'nom'  =>  0,
            'nof'  =>  0
        );
        
        
        foreach ($package->getClasses() as $class) {
            $class->accept($this);
        }
        foreach ($package->getInterfaces() as $interface) {
            $interface->accept($this);
        }
        foreach ($package->getFunctions() as $function) {
            $function->accept($this);
        }
    }
}