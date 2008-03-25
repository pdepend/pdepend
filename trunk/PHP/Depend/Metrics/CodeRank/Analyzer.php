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
    const DAMPING_FACTOR = 0.85;
    
    protected $classNodes = array();
    
    protected $classRank = null;
    
    protected $packageNodes = array();
    
    protected $packageRank = null;
    
    public function getPackageRank()
    {
        if ($this->packageRank === null) {
            $this->packageRank = $this->buildPackageRank();
        }
        return $this->packageRank;
    }
    
    public function getClassRank()
    {
        if ($this->classRank === null) {
            $this->classRank = $this->buildClassRank();
        }
        return $this->classRank;
    }
    
    /**
     * @see PHP_Depend_Code_NodeVisitor::visitClass()
     *
     * @param PHP_Depend_Code_Class $class
     */
    public function visitClass(PHP_Depend_Code_Class $class)
    {
        $className = $class->getPackage()->getName() . '::' . $class->getName();
        
        if (!isset($this->classNodes[$className])) {
            $this->classNodes[$className] = array(
                'in'    =>  array(),
                'out'   =>  array(),
                'code'  =>  $class,
            );
        }
        
        foreach ($class->getDependencies() as $dep) {
            
            $depClassName = $dep->getPackage()->getName() . '::' . $dep->getName();
            
            if (!isset($this->classNodes[$depClassName])) {
                $this->classNodes[$depClassName] = array(
                    'in'    =>  array(),
                    'out'   =>  array(),
                    'code'  =>  $dep,
                );
            }
            $this->classNodes[$className]['in'][$depClassName]  = $depClassName;
            $this->classNodes[$depClassName]['out'][$className] = $className;
        }
    }
    
    /**
     * @see PHP_Depend_Code_NodeVisitor::visitFunction()
     *
     * @param PHP_Depend_Code_Function $function
     */
    public function visitFunction(PHP_Depend_Code_Function $function)
    {
        //TODO - Insert your code here
    }
    
    /**
     * @see PHP_Depend_Code_NodeVisitor::visitMethod()
     *
     * @param PHP_Depend_Code_Method $method
     */
    public function visitMethod(PHP_Depend_Code_Method $method)
    {
        //TODO - Insert your code here
    }
    
    /**
     * @see PHP_Depend_Code_NodeVisitor::visitPackage()
     *
     * @param PHP_Depend_Code_Package $package
     */
    public function visitPackage(PHP_Depend_Code_Package $package)
    {
        foreach($package->getClasses() as $class) {
            $class->accept($this);
        }
        foreach($package->getFunctions() as $function) {
            $function->accept($this);
        }
    }
    
    protected function buildClassRank()
    {
        $ranks = array();
        foreach ($this->classNodes as $class => $info) {
            $ranks[$class] = new PHP_Depend_Metrics_CodeRank_Class($info['code']);
        }
        foreach ($this->buildCodeRank($this->classNodes, 'in', 'out') as $class => $rank) {
            $ranks[$class]->setCodeRank($rank);
        }
        foreach ($this->buildCodeRank($this->classNodes, 'out', 'in') as $class => $rank) {
            $ranks[$class]->setReverseCodeRank($rank);
        }
        /*
        print_r($ranks);
        
        print_r($this->buildCodeRank($this->classNodes, 'in', 'out'));
        print_r($this->buildCodeRank($this->classNodes, 'out', 'in'));
        */
        return array_values($ranks);
    }
    
    protected function buildPackageRank()
    {
        
    }
    
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
    
    protected function buildCodeRank(array $nodes, $id1, $id2)
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