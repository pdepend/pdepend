<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pdepend.org>.
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
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Metrics/AbstractAnalyzer.php';
require_once 'PHP/Depend/Metrics/AnalyzerI.php';
require_once 'PHP/Depend/Metrics/NodeAwareI.php';
require_once 'PHP/Depend/Metrics/ProjectAwareI.php';

/**
 * This analyzer collects different lines of code metrics.
 * 
 * It collects the total Lines Of Code(<b>loc</b>), the None Comment Lines Of
 * Code(<b>ncloc</b>), the Comment Lines Of Code(<b>cloc</b>) and a approximated
 * Executable Lines Of Code(<b>eloc</b>) for files, classes, interfaces, 
 * methods, properties and function.
 * 
 * The current implementation has a limitation, that affects inline comments. 
 * The following code will suppress one line of code.
 * 
 * <code>
 * function foo() {
 *     foobar(); // Bad behaviour...
 * }
 * </code>
 * 
 * The same rule applies to class methods. mapi, <b>PLEASE, FIX THIS ISSUE.</b>
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_NodeLoc_Analyzer
       extends PHP_Depend_Metrics_AbstractAnalyzer
    implements PHP_Depend_Metrics_AnalyzerI,
               PHP_Depend_Metrics_NodeAwareI,
               PHP_Depend_Metrics_ProjectAwareI
{
    /**
     * Collected node metrics
     *
     * @type array<array>
     * @var array(string=>array) $_nodeMetrics
     */
    private $_nodeMetrics = null;
    
    /**
     * Collected project metrics.
     *
     * @type array<array>
     * @var array(string=>integer) $_projectMetrics
     */
    private $_projectMetrics = array(
        'loc'    =>  0,
        'cloc'   =>  0,
        'eloc'   =>  0,
        'ncloc'  =>  0
    );
    
    /**
     * This method will return an <b>array</b> with all generated metric values 
     * for the given <b>$node</b> instance. If there are no metrics for the 
     * requested node, this method will return an empty <b>array</b>.
     * 
     * <code>
     * array(
     *     'loc'    =>  23,
     *     'cloc'   =>  17,
     *     'eloc'   =>  17,
     *     'ncloc'  =>  42
     * )
     * </code>
     *
     * @param PHP_Reflection_Ast_NodeI $node The context node instance.
     * 
     * @return array(string=>mixed)
     */
    public function getNodeMetrics(PHP_Reflection_Ast_NodeI $node)
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
     *     'loc'    =>  23,
     *     'cloc'   =>  17,
     *     'ncloc'  =>  42
     * )
     * </code>
     *
     * @return array(string=>mixed)
     */
    public function getProjectMetrics()
    {
        return $this->_projectMetrics;
    }
    
    /**
     * Processes all {@link PHP_Reflection_Ast_Package} code nodes.
     *
     * @param PHP_Reflection_Ast_Iterator $packages All code packages.
     * 
     * @return void
     */
    public function analyze(PHP_Reflection_Ast_Iterator $packages)
    {
        // Check for previous run
        if ($this->_nodeMetrics === null) {
            
            $this->fireStartAnalyzer();
            
            // Init node metrics
            $this->_nodeMetrics = array();
            
            // Process all packages
            foreach ($packages as $package) {
                $package->accept($this);
            }
            
            $this->fireEndAnalyzer();
        }
    }
    
    /**
     * Visits a class node. 
     *
     * @param PHP_Reflection_Ast_Class $class The current class node.
     * 
     * @return void
     * @see PHP_Reflection_Visitor_AbstractVisitor::visitClass()
     */
    public function visitClass(PHP_Reflection_Ast_Class $class)
    {
        $this->fireStartClass($class);
        
        $class->getSourceFile()->accept($this);
        
        list($cloc, $eloc) = $this->_linesOfCode($class->getTokens());
        
        $loc   = $class->getEndLine() - $class->getStartLine() + 1;
        $ncloc = $loc - $cloc;
        
        $this->_nodeMetrics[$class->getUUID()] = array(
            'loc'    =>  $loc,
            'cloc'   =>  $cloc,
            'eloc'   =>  $eloc,
            'ncloc'  =>  $ncloc,
        );

        foreach ($class->getMethods() as $method) {
            $method->accept($this);
        }
        foreach ($class->getProperties() as $property) {
            $property->accept($this);
        }
        foreach ($class->getConstants() as $constant) {
            $constant->accept($this);
        }
        
        $this->fireEndClass($class);
    }
    
    /**
     * Visits a file node. 
     *
     * @param PHP_Reflection_Ast_File $file The current file node.
     * 
     * @return void
     * @see PHP_Reflection_Visitor_AbstractVisitor::visitFile()
     */
    public function visitFile(PHP_Reflection_Ast_File $file)
    {
        // Skip for dummy files
        if ($file->getFileName() === null) {
            return;
        }
        // Check for initial file
        $uuid = $file->getUUID();
        if (isset($this->_nodeMetrics[$uuid])) {
            return;
        }
        
        $this->fireStartFile($file);
        
        list($cloc, $eloc) = $this->_linesOfCode($file->getTokens());
                
        $loc   = count($file->getLoc());
        $ncloc = $loc - $cloc;

        $this->_nodeMetrics[$uuid] = array(
            'loc'    =>  $loc,
            'cloc'   =>  $cloc,
            'eloc'   =>  $eloc,
            'ncloc'  =>  $ncloc
        );
        
        // Update project metrics
        $this->_projectMetrics['loc']   += $loc;
        $this->_projectMetrics['cloc']  += $cloc;
        $this->_projectMetrics['eloc']  += $eloc;
        $this->_projectMetrics['ncloc'] += $ncloc;
        
        $this->fireEndFile($file);
    }
    
    /**
     * Visits a function node. 
     *
     * @param PHP_Reflection_Ast_Function $function The current function node.
     * 
     * @return void
     * @see PHP_Reflection_Visitor_AbstractVisitor::visitFunction()
     */
    public function visitFunction(PHP_Reflection_Ast_Function $function)
    {
        $this->fireStartFunction($function);
        
        $function->getSourceFile()->accept($this);
        
        list($cloc, $eloc) = $this->_linesOfCode($function->getTokens());
        
        $loc   = $function->getEndLine() - $function->getStartLine() + 1;
        $ncloc = $loc - $cloc;
        
        $this->_nodeMetrics[$function->getUUID()] = array(
            'loc'    =>  $loc,
            'cloc'   =>  $cloc,
            'eloc'   =>  $eloc,
            'ncloc'  =>  $ncloc
        );
        
        $this->fireEndFunction($function);
    }
    
    /**
     * Visits a code interface object.
     *
     * @param PHP_Reflection_Ast_Interface $interface The context code interface.
     * 
     * @return void
     * @see PHP_Reflection_Visitor_AbstractVisitor::visitInterface()
     */
    public function visitInterface(PHP_Reflection_Ast_Interface $interface)
    {
        $this->fireStartInterface($interface);
        
        $interface->getSourceFile()->accept($this);
        
        list($cloc, $eloc) = $this->_linesOfCode($interface->getTokens());
        
        $loc   = $interface->getEndLine() - $interface->getStartLine() + 1;
        $ncloc = $loc - $cloc;

        $this->_nodeMetrics[$interface->getUUID()] = array(
            'loc'    =>  $loc,
            'cloc'   =>  $cloc,
            'eloc'   =>  $eloc,
            'ncloc'  =>  $ncloc
        );
        
        foreach ($interface->getMethods() as $method) {
            $method->accept($this);
        }
        foreach ($interface->getConstants() as $constant) {
            $constant->accept($this);
        }
        
        $this->fireEndInterface($interface);
    }
    
    /**
     * Visits a method node. 
     *
     * @param PHP_Reflection_Ast_Class $method The method class node.
     * 
     * @return void
     * @see PHP_Reflection_Visitor_AbstractVisitor::visitMethod()
     */
    public function visitMethod(PHP_Reflection_Ast_Method $method)
    {
        $this->fireStartMethod($method);
        
        list($cloc, $eloc) = $this->_linesOfCode($method->getTokens());
        
        $loc   = $method->getEndLine() - $method->getStartLine() + 1;
        $ncloc = $loc - $cloc;
        
        $this->_nodeMetrics[$method->getUUID()] = array(
            'loc'    =>  $loc,
            'cloc'   =>  $cloc,
            'eloc'   =>  $eloc,
            'ncloc'  =>  $ncloc
        );

        $this->fireEndMethod($method);
    }
    
    /**
     * Visits a property node. 
     *
     * @param PHP_Reflection_Ast_Property $property The property class node.
     * 
     * @return void
     * @see PHP_Reflection_Visitor_AbstractVisitor::visitProperty()
     */
    public function visitProperty(PHP_Reflection_Ast_Property $property)
    {
        $this->fireStartProperty($property);
        
        $this->_nodeMetrics[$property->getUUID()] = array(
            'loc'    =>  1,
            'cloc'   =>  0,
            'eloc'   =>  0,
            'ncloc'  =>  1
        );

        $this->fireEndProperty($property);
    }
    
    /**
     * Visits a class constant node. 
     *
     * @param PHP_Reflection_Ast_TypeConstant $constant The current constant node.
     * 
     * @return void
     * @see PHP_Reflection_Visitor_AbstractVisitor::visitTypeConstant()
     */
    public function visitTypeConstant(PHP_Reflection_Ast_TypeConstant $constant)
    {
        $this->fireStartTypeConstant($constant);
                
        $this->_nodeMetrics[$constant->getUUID()] = array(
            'loc'    =>  1,
            'cloc'   =>  0,
            'eloc'   =>  0,
            'ncloc'  =>  1
        );
        
        $this->fireEndTypeConstant($constant);
    }
    
    /**
     * Counts the Comment Lines Of Code (CLOC) and a pseudo Executable Lines Of
     * Code (ELOC) values. 
     * 
     * ELOC = Non Whitespace Lines + Non Comment Lines
     * 
     * <code>
     * array(
     *     0  =>  23,  // Comment Lines Of Code
     *     1  =>  42   // Executable Lines Of Code
     * )
     * </code>
     *
     * @param array(array) $tokens The raw token stream.
     * 
     * @return array(integer)
     */
    private function _linesOfCode(array $tokens)
    {
        $clines = array();
        $elines = array();

        $comment = array(
            PHP_Reflection_TokenizerI::T_COMMENT,
            PHP_Reflection_TokenizerI::T_DOC_COMMENT
        );
        
        foreach ($tokens as $token) {
            $lines = count(explode("\n", trim($token[1])));
            
            if (in_array($token[0], $comment) === true) {
                for ($i = 0; $i < $lines; ++$i) {
                    $clines[$token[2] + $i] = true;
                }
            } else {
                for ($i = 0; $i < $lines; ++$i) {
                    $elines[$token[2] + $i] = true;
                }
            }
        }
        return array(count($clines), count($elines));
    }
}