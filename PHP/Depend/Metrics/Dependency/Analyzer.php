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

/**
 * This visitor generates the metrics for the analyzed packages.
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
class PHP_Depend_Metrics_Dependency_Analyzer 
       extends PHP_Depend_Code_NodeVisitor_AbstractDefaultVisitor
    implements PHP_Depend_Metrics_AnalyzerI
{
    protected $nodeMetrics = null;
    
    protected $nodeSet = array();
    
    private $_efferentNodes = array();
    
    private $_afferentNodes = array();
    
    /**
     * This property holds the UUID ids of all skipable paths when this class
     * collects the cycles. 
     *
     * @type array<boolean>
     * @var array(string=>boolean) $_skipablePaths
     */
    private $_skipablePaths = array();
    
    /**
     * All collected cycles for the input code.
     * 
     * <code>
     * array(
     *     <package-uuid> => array(
     *         PHP_Depend_Code_Package {},
     *         PHP_Depend_Code_Package {},
     *     ),
     *     <package-uuid> => array(
     *         PHP_Depend_Code_Package {},
     *         PHP_Depend_Code_Package {},
     *     ),
     * )
     * </code>
     *
     * @type array<array>
     * @var array(string=>array) $_collectedCycles
     */
    private $_collectedCycles = array();
    
    /**
     * Processes all {@link PHP_Depend_Code_Package} code nodes.
     *
     * @param PHP_Depend_Code_NodeIterator $packages All code packages.
     * 
     * @return void
     */
    public function analyze(PHP_Depend_Code_NodeIterator $packages)
    {
        if ($this->nodeMetrics !== null) {
            return;
        }
        $this->nodeMetrics = array();
        
        foreach ($packages as $package) {
            $package->accept($this);
        }
        
        $this->postProcess();
        
        $this->calculateAbstractness();
        $this->calculateInstability();
        $this->calculateDistance();
    }
    
    /**
     * Returns the statistics for the requested node.
     *
     * @param string $uuid The unique node identifier.
     * 
     * @return array
     */
    public function getStats($uuid)
    {
        if (isset($this->nodeMetrics[$uuid])) {
            return $this->nodeMetrics[$uuid];
        }
        return array();
    }
    
    /**
     * Returns an array of all afferent nodes.
     *
     * @param string $uuid The unique node identifier.
     * 
     * @return array(PHP_Depend_Code_NodeI)
     */
    public function getAfferents($uuid)
    {
        if (isset($this->_afferentNodes[$uuid])) {
            return $this->_afferentNodes[$uuid];
        }
        return array();
    }
    
    /**
     * Returns an array of all efferent nodes.
     *
     * @param string $uuid The unique node identifier.
     * 
     * @return array(PHP_Depend_Code_NodeI)
     */
    public function getEfferents($uuid)
    {
        if (isset($this->_efferentNodes[$uuid])) {
            return $this->_efferentNodes[$uuid];
        }
        return array();
    }
    
    /**
     * Returns an array of nodes that build a cycle for the requested node or it
     * returns <b>null</b> if no cycle exists .
     *
     * @param string $uuid The unique node identifier.
     * 
     * @return array(PHP_Depend_Code_NodeI)
     */
    public function getCycle($uuid)
    {
        if (isset($this->_collectedCycles[$uuid])) {
            return $this->_collectedCycles[$uuid];
        }
        return null;
    }
    
    /**
     * Visits a method node. 
     *
     * @param PHP_Depend_Code_Class $method The method class node.
     * 
     * @return void
     */
    public function visitMethod(PHP_Depend_Code_Method $method)
    {
        // Get context package uuid
        $pkgUUID = $method->getParent()->getPackage()->getUUID();
        
        // Traverse all dependencies
        foreach ($method->getDependencies() as $dep) {
            // Get dependent package uuid
            $depPkgUUID = $dep->getPackage()->getUUID();
            
            // Skip if context and dependency are equal
            if ($depPkgUUID === $pkgUUID) {
                continue;
            }
            
            // Create a container for this dependency
            $this->initPackageMetric($dep->getPackage());
            
            if (!in_array($depPkgUUID, $this->nodeMetrics[$pkgUUID]['ce'])) {
                $this->nodeMetrics[$pkgUUID]['ce'][]    = $depPkgUUID;
                $this->nodeMetrics[$depPkgUUID]['ca'][] = $pkgUUID;
            }
        }
    }
    
    /**
     * Visits a package node. 
     *
     * @param PHP_Depend_Code_Class $package The package class node.
     * 
     * @return void
     */
    public function visitPackage(PHP_Depend_Code_Package $package)
    {
        $this->initPackageMetric($package);
        
        $this->nodeSet[$package->getUUID()] = $package;

        foreach ($package->getTypes() as $type) {
            $type->accept($this);
        }

        $storage = new SplObjectStorage();
        if ($this->collectCycle($storage, $package) === true) {
            $this->_collectedCycles[$package->getUUID()] = array();
            foreach ($storage as $pkg) {
                $this->_collectedCycles[$package->getUUID()][] = $pkg;
            }
        }
    }
    
    /**
     * Visits a class node. 
     *
     * @param PHP_Depend_Code_Class $class The current class node.
     * 
     * @return void
     */
    public function visitClass(PHP_Depend_Code_Class $class)
    {
        $this->visitType($class);
    }
    
    /**
     * Visits an interface node. 
     *
     * @param PHP_Depend_Code_Interface $interface The current interface node.
     * 
     * @return void
     */
    public function visitInterface(PHP_Depend_Code_Interface $interface)
    {
        $this->visitType($interface);
    }
    
    /**
     * Generic visit method for classes and interfaces. Both visit methods 
     * delegate calls to this method.
     *
     * @param PHP_Depend_Code_AbstractType $type The context type instance.
     * 
     * @return void
     */
    protected function visitType(PHP_Depend_Code_AbstractType $type)
    {
        // Get context package uuid
        $pkgUUID = $type->getPackage()->getUUID();
        
        // Increment total classes count
        ++$this->nodeMetrics[$pkgUUID]['tc'];
        
        // Check for abstract or concrete class
        if ($type->isAbstract()) {
            ++$this->nodeMetrics[$pkgUUID]['ac'];
        } else {
            ++$this->nodeMetrics[$pkgUUID]['cc'];
        }
        
        // Traverse all dependencies
        foreach ($type->getDependencies() as $dep) {
            // Get dependent package uuid
            $depPkgUUID = $dep->getPackage()->getUUID();
            
            // Skip if context and dependency are equal
            if ($depPkgUUID === $pkgUUID) {
                continue;
            }
            
            // Create a container for this dependency
            $this->initPackageMetric($dep->getPackage());
            
            if (!in_array($depPkgUUID, $this->nodeMetrics[$pkgUUID]['ce'])) {
                $this->nodeMetrics[$pkgUUID]['ce'][]    = $depPkgUUID;
                $this->nodeMetrics[$depPkgUUID]['ca'][] = $pkgUUID;
            }
        }

        foreach ($type->getMethods() as $method) {
            $method->accept($this);
        }
    }
    
    protected function initPackageMetric(PHP_Depend_Code_Package $package)
    {
        $uuid = $package->getUUID();
        
        if (!isset($this->nodeMetrics[$uuid])) {
            // Store a package reference 
            $this->nodeSet[$uuid] = $package;
            
            // Create empty metrics for this package
            $this->nodeMetrics[$uuid] = array(
                'tc'  =>  0,
                'cc'  =>  0,
                'ac'  =>  0,
                'ca'  =>  array(),
                'ce'  =>  array(),
                'a'   =>  0,
                'i'   =>  0,
                'd'   =>  0
            );
        }
    }
    
    protected function postProcess()
    {
        foreach ($this->nodeMetrics as $uuid => $metrics) {
            
            // Store afferent nodes for uuid
            $this->_afferentNodes[$uuid] = array();
            foreach ($metrics['ca'] as $caUUID) {
                $this->_afferentNodes[$uuid][] = $this->nodeSet[$caUUID];
            }
            sort($this->_afferentNodes[$uuid]);
            
            // Store efferent nodes for uuid
            $this->_efferentNodes[$uuid] = array();
            foreach ($metrics['ce'] as $ceUUID) {
                $this->_efferentNodes[$uuid][] = $this->nodeSet[$ceUUID];
            }
            sort($this->_efferentNodes[$uuid]);
            
            $this->nodeMetrics[$uuid]['ca'] = count($metrics['ca']);
            $this->nodeMetrics[$uuid]['ce'] = count($metrics['ce']);
        }
    }
    
    protected function calculateAbstractness()
    {
        foreach ($this->nodeMetrics as $uuid => $metrics) {
            if ($metrics['tc'] !== 0) {
                $this->nodeMetrics[$uuid]['a'] = ($metrics['ac'] / $metrics['tc']);
            }
            
        }
    }
    
    protected function calculateInstability()
    {
        foreach ($this->nodeMetrics as $uuid => $metrics) {
            // Count total incoming and outgoing dependencies
            $total = ($metrics['ca'] + $metrics['ce']);
            
            if ($total !== 0) {
                $this->nodeMetrics[$uuid]['i'] = ($metrics['ce'] / $total);
            }
        }
    }
    
    protected function calculateDistance()
    {
        foreach ($this->nodeMetrics as $uuid => $metrics) {
            $this->nodeMetrics[$uuid]['d'] = abs(($metrics['a'] + $metrics['i']) - 1);
        }
    }

    /**
     * Collects a single cycle that is reachable by this package. All packages
     * that are part of the cylce are stored in the given {@link SplObjectStorage}
     * instance. 
     *
     * @param SplObjectStorage $storage The cycle package object store.
     * 
     * @return boolean If this method detects a cycle the return value is <b>true</b>
     *                 otherwise this method will return <b>false</b>.
     */
    protected function collectCycle(SplObjectStorage $storage, PHP_Depend_Code_Package $package)
    {
        if ($storage->contains($package)) {
            $storage->rewind();
            while (($tmp = $storage->current()) !== $package) {
                $storage->detach($tmp);
            }
            return true;
        }
        
        $storage->attach($package);

        foreach ($package->getTypes() as $class) {
            
            // Create a path identifier
            $pathID = $package->getUUID() . '#' . $class->getUUID();
            
            // There is no cycle for this combination, if this id already exists.
            if (isset($this->_skipablePaths[$pathID])) {
                continue;
            }
            
            // Traverse all direct class dependencies
            foreach ($class->getDependencies() as $dependency) {
                $pkg = $dependency->getPackage();
                if ($pkg !== $package && $this->collectCycle($storage, $pkg)) {
                    return true;
                }
            }
            // Traverse all indirect class dependencies
            foreach ($class->getMethods() as $method) {
                foreach ($method->getDependencies() as $dependency) {
                    $pkg = $dependency->getPackage();
                    if ($pkg !== $package && $this->collectCycle($storage, $pkg)) {
                        return true;
                    }
                }                
            }
            
            // No cycle detected, so mark as skipable
            $this->_skipablePaths[$pathID] = true;
        }
        $storage->detach($package);
        
        return false;
    }
}