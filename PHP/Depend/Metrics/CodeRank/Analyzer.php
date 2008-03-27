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
require_once 'PHP/Depend/Metrics/CodeRank/Class.php';
require_once 'PHP/Depend/Metrics/CodeRank/Package.php';

/**
 * Calculates the code ranke metric for classes and packages. 
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_CodeRank_Analyzer implements PHP_Depend_Code_NodeVisitor
{
    /**
     * The used damping factor.
     */
    const DAMPING_FACTOR = 0.85;
    
    /**
     * The found class nodes.
     *
     * @type array
     * @var array $classNodes
     */
    protected $classNodes = array();
    
    /**
     * The calculated class ranks.
     *
     * @type array<PHP_Depend_Metrics_CodeRank_Class>
     * @var array(PHP_Depend_Metrics_CodeRank_Class) $classRank
     */
    protected $classRank = null;
    
    /**
     * The found package nodes.
     *
     * @type array
     * @var array $packageNodes
     */
    protected $packageNodes = array();
    
    /**
     * The calculated package ranks.
     *
     * @type array<PHP_Depend_Metrics_CodeRank_Package>
     * @var array(PHP_Depend_Metrics_CodeRank_Package) $packageRank
     */
    protected $packageRank = null;
    
    /**
     * Returns the package rank for all packages.
     *
     * @return array(PHP_Depend_Metrics_CodeRank_Package)
     */
    public function getPackageRank()
    {
        if ($this->packageRank === null) {
            // The result class
            $class = 'PHP_Depend_Metrics_CodeRank_Package';
            // Build package code rank
            $this->packageRank = $this->buildCodeRank($this->packageNodes, $class);
        }
        return $this->packageRank;
    }
    
    /**
     * Returns the class rank for all classes.
     *
     * @return array(PHP_Depend_Metrics_CodeRank_Class)
     */
    public function getClassRank()
    {
        if ($this->classRank === null) {
            // The result class
            $class = 'PHP_Depend_Metrics_CodeRank_Class';
            // Build class code rank
            $this->classRank = $this->buildCodeRank($this->classNodes, $class);
        }
        return $this->classRank;
    }
    
    /**
     * Visits a code class object.
     *
     * @param PHP_Depend_Code_Class $class The context code class.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitor::visitClass()
     */
    public function visitClass(PHP_Depend_Code_Class $class)
    {
        $packageName = $class->getPackage()->getName();
        $className   = $packageName . '::' . $class->getName();
        
        if (!isset($this->classNodes[$className])) {
            $this->classNodes[$className] = array(
                'in'    =>  array(),
                'out'   =>  array(),
                'code'  =>  $class,
            );
        }
        
        foreach ($class->getDependencies() as $dep) {
            
            $depPackageName = $dep->getPackage()->getName();
            $depClassName   = $depPackageName . '::' . $dep->getName();
            
            if (!isset($this->classNodes[$depClassName])) {
                $this->classNodes[$depClassName] = array(
                    'in'    =>  array(),
                    'out'   =>  array(),
                    'code'  =>  $dep,
                );
            }
            $this->classNodes[$className]['in'][$depClassName]  = $depClassName;
            $this->classNodes[$depClassName]['out'][$className] = $className;
            
            if (!isset($this->packageNodes[$depPackageName])) {
                $this->packageNodes[$depPackageName] = array(
                    'in'    =>  array(),
                    'out'   =>  array(),
                    'code'  =>  $dep->getPackage(),
                );
            }
            if (!isset($this->packageNodes[$packageName]['in'][$depPackageName])) {
                $this->packageNodes[$packageName]['in'][$depPackageName]  = $depPackageName;
                $this->packageNodes[$depPackageName]['out'][$packageName] = $packageName;
            }
        }
    }
    
    /**
     * Visits a code function object.
     *
     * @param PHP_Depend_Code_Function $function The context code function.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitor::visitFunction()
     */
    public function visitFunction(PHP_Depend_Code_Function $function)
    {
        //TODO - Insert your code here
    }
    
    /**
     * Visits a code method object.
     *
     * @param PHP_Depend_Code_Method $method The context code method.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitor::visitMethod()
     */
    public function visitMethod(PHP_Depend_Code_Method $method)
    {
        //TODO - Insert your code here
    }
    
    /**
     * Visits a code package object.
     *
     * @param PHP_Depend_Code_Package $package The context code package.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitor::visitPackage()
     */
    public function visitPackage(PHP_Depend_Code_Package $package)
    {
        $packageName = $package->getName();
        if (!isset($this->packageNodes[$packageName])) {
            $this->packageNodes[$packageName] = array(
                'in'    =>  array(),
                'out'   =>  array(),
                'code'  =>  $package
            );
        }
        
        foreach ($package->getClasses() as $class) {
            $class->accept($this);
        }
        foreach ($package->getFunctions() as $function) {
            $function->accept($this);
        }
    }
    
    /**
     * Generates the forward and reverse code rank for the given <b>$nodes</b>.
     *
     * @param array  $nodes List of nodes.
     * @param string $class The metric model class.
     * 
     * @return array(stdClass) Code rank <b>$class</b> objects,
     */
    protected function buildCodeRank(array $nodes, $class)
    {
        $ranks = array();
        
        foreach ($nodes as $name => $info) {
            $ranks[$name] = new $class($info['code']);
        }
        foreach ($this->computeCodeRank($nodes, 'out', 'in') as $name => $rank) {
            $ranks[$name]->setCodeRank($rank);
        }
        foreach ($this->computeCodeRank($nodes, 'in', 'out') as $name => $rank) {
            $ranks[$name]->setReverseCodeRank($rank);
        }

        return array_values($ranks);
    }
    
    /**
     * Sorts the given <b>$nodes</b> set.
     *
     * @param array  $nodes List of nodes.
     * @param string $dir1  Identifier for the incoming edges.
     * @param string $dir2  Identifier for the outgoing edges.
     * 
     * @return array
     */
    protected function topologicalSort(array $nodes, $dir1, $dir2)
    {
        $leafs  = array();
        $sorted = array();
        
        // Collect all leaf nodes
        foreach ($nodes as $name => $node) {
            if (count($node[$dir1]) === 0) {
                unset($nodes[$name]);
                $leafs[$name] = $node;
            }
        }

        while (($leaf = reset($leafs)) !== false) {
            $name = key($leafs);
            
            $sorted[$name] = $leaf;
            
            unset($leafs[$name]);
            
            foreach ($leaf[$dir2] as $refName) {
                // Remove edge between these two nodes 
                unset($nodes[$refName][$dir1][$name]);
                
                // If the referenced node has no incoming/outgoing edges,
                // put it in the list of leaf nodes.
                if (count($nodes[$refName][$dir1]) === 0) {
                    $leafs[$refName] = $nodes[$refName];
                    // Remove node from all nodes
                    unset($nodes[$refName]);
                }
            }
        }
        
        if (count($nodes) > 0) {
            throw new RuntimeException('The object structure contains cycles');
        }
        
        return array_keys($sorted);
    }
    
    /**
     * Calculates the code rank for the given <b>$nodes</b> set.
     *
     * @param array  $nodes List of nodes. 
     * @param string $id1   Identifier for the incoming edges.
     * @param string $id2   Identifier for the outgoing edges.
     * 
     * @return array(string=>float)
     */
    protected function computeCodeRank(array $nodes, $id1, $id2)
    {
        $d = self::DAMPING_FACTOR;
        
        $ranks = array();
        foreach ($this->topologicalSort($nodes, $id1, $id2) as $name) {
            $rank = 0.0;
            foreach ($nodes[$name][$id1] as $refName) {
                $diff = 1;
                if (($count = count($nodes[$refName][$id2])) > 0) {
                    $diff = $count;
                }
                $rank += ($ranks[$refName] / $diff);
            }
            
            $ranks[$name] = (1 - $d) + $d * $rank;
        }
        return $ranks;
    }
}