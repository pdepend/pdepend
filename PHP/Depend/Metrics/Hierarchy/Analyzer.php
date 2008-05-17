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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/NodeVisitor.php';
require_once 'PHP/Depend/Metrics/AnalyzerI.php';
require_once 'PHP/Depend/Metrics/FilterAwareI.php';
require_once 'PHP/Depend/Metrics/ResultSetI.php';
require_once 'PHP/Depend/Metrics/ResultSet/ProjectAwareI.php';

/**
 * This analyzer calculates class/package hierarchy metrics.
 * 
 * This analyzer expects that a node list filter is set, before it starts the 
 * analyze process. This filter will suppress PHP internal and external library
 * stuff.
 * 
 * This analyzer is based on the following metric set:
 * - http://www.aivosto.com/project/help/pm-oo-misc.html
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_Hierarchy_Analyzer
    implements PHP_Depend_Code_NodeVisitor,
               PHP_Depend_Metrics_AnalyzerI,
               PHP_Depend_Metrics_FilterAwareI,
               PHP_Depend_Metrics_ResultSetI,
               PHP_Depend_Metrics_ResultSet_ProjectAwareI
{
    /**
     * Number of all analyzed packages.
     * 
     * @type integer
     * @var integer $pkg
     */
    protected $pkg = 0;
    
    /**
     * Number of all analyzed functions.
     *
     * @type integer
     * @var integer $fcs
     */
    protected $fcs = 0;
    
    /**
     * Number of all analyzed classes.
     *
     * @type integer
     * @var integer $cls
     */
    protected $cls = 0;
    
    /**
     * Number of all analyzed abstract classes.
     *
     * @type integer
     * @var integer $clsa
     */
    protected $clsa = 0;
    
    /**
     * Number of all analyzed interfaces.
     *
     * @type integer 
     * @var integer $interfs
     */
    protected $interfs = 0;
    
    /**
     * Number of all root classes within the analyzed source code.
     *
     * @type integer
     * @var integer $roots
     */
    protected $roots = 0;
    
    /**
     * Number of all leaf classes within the analyzed source code
     *
     * @type integer
     * @var integer $leafs
     */
    protected $leafs = 0;
    
    /**
     * The maximum depth of inheritance tree value within the analyzed source code.
     *
     * @type integer
     * @var integer $maxDIT
     */
    protected $maxDIT = 0;
    
    /**
     * @see PHP_Depend_Metrics_AnalyzerI::analyze()
     *
     * @param PHP_Depend_Code_NodeIterator $packages
     * @return PHP_Depend_Metrics_ResultSetI
     */
    public function analyze(PHP_Depend_Code_NodeIterator $packages)
    {
        foreach ($packages as $package) {
            $package->accept($this);
        }
        
        return $this;
    }
    
    /**
     * Provides the project summary metrics as an <b>array</b>.
     *
     * @return array(string=>mixed)
     */
    public function getProjectMetrics()
    {
        return array(
            'pkg'      =>  $this->pkg,
            'fcs'      =>  $this->fcs,
            'interfs'  =>  $this->interfs,
            'cls'      =>  $this->cls,
            'clsa'     =>  $this->clsa,
            'clsc'     =>  $this->cls - $this->clsa,
            'roots'    =>  $this->roots,
            'leafs'    =>  $this->leafs,
            'maxDIT'   =>  $this->maxDIT,
        );
    }
    
    /**
     * @see PHP_Depend_Code_NodeVisitor::visitClass()
     *
     * @param PHP_Depend_Code_Class $class
     */
    public function visitClass(PHP_Depend_Code_Class $class)
    {
        ++$this->cls;
        
        if ($class->isAbstract()) {
            ++$this->clsa;
        }
        
        if ($class->getChildClasses()->count() === 0) {
            ++$this->leafs;
        } else if ($class->getParentClass() === null) {
            ++$this->roots;
        }
        
        $this->maxDIT = max($this->maxDIT, $this->getClassDIT($class));
        
        foreach ($class->getMethods() as $method) {
            $method->accept($this);
        }
    }
    
    /**
     * @see PHP_Depend_Code_NodeVisitor::visitFunction()
     *
     * @param PHP_Depend_Code_Function $function
     */
    public function visitFunction(PHP_Depend_Code_Function $function)
    {
        ++$this->fcs;
    }
    
    /**
     * @see PHP_Depend_Code_NodeVisitor::visitInterface()
     *
     * @param PHP_Depend_Code_Interface $interface
     */
    public function visitInterface(PHP_Depend_Code_Interface $interface)
    {
        ++$this->interfs;
        
        foreach ($interface->getMethods() as $method) {
            $method->accept($this);
        }
    }
    
    /**
     * @see PHP_Depend_Code_NodeVisitor::visitMethod()
     *
     * @param PHP_Depend_Code_Method $method
     */
    public function visitMethod(PHP_Depend_Code_Method $method)
    {
        
    }
    
    /**
     * @see PHP_Depend_Code_NodeVisitor::visitPackage()
     *
     * @param PHP_Depend_Code_Package $package
     */
    public function visitPackage(PHP_Depend_Code_Package $package)
    {
        ++$this->pkg;
        
        foreach ($package->getTypes() as $type) {
            $type->accept($this);
        }
        
        foreach ($package->getFunctions() as $function) {
            $function->accept($this);
        }
    }
    
    /**
     * Returns the depth of inheritance tree value for the given class.
     *
     * @param PHP_Depend_Code_Class $class The context code class instance.
     */
    protected function getClassDIT(PHP_Depend_Code_Class $class)
    {
        $dit = 0;
        while (($class = $class->getParentClass()) !== null) {
            ++$dit;
        }
        return $dit;
    }

}