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

require_once 'PHP/Depend/Code/NodeVisitor.php';
require_once 'PHP/Depend/Metrics/AnalyzerI.php';
require_once 'PHP/Depend/Metrics/Dependency/Package.php';

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
    implements PHP_Depend_Code_NodeVisitor,
               PHP_Depend_Metrics_AnalyzerI
{
    /**
     * Already created package instances.
     *
     * @type array<PHP_Depend_Metrics_Dependency_Package>
     * @var array(string=>PHP_Depend_Metrics_Dependency_Package) $packages
     */
    protected $packages = array();
    
    /**
     * Mapping of outgoing dependencies.
     * 
     * @type array<SplObjectStorage>
     * @var array(string=>SplObjectStorage) $efferents
     */
    protected $efferents = array();
    
    /**
     * Mapping of incoming dependencies.
     *
     * @type array<SplObjectStorage>
     * @var array(string=>SplObjectStorage) $afferents
     */
    protected $afferents = array();
    
    /**
     * The generated project metrics.
     *
     * @type ArrayIterator
     * @var ArrayIterator $metrics
     */
    protected $metrics = null;
    
    protected $nodeMetrics = array();
    
    /**
     * Processes all {@link PHP_Depend_Code_Package} code nodes.
     *
     * @param PHP_Depend_Code_NodeIterator $packages All code packages.
     * 
     * @return void
     */
    public function analyze(PHP_Depend_Code_NodeIterator $packages)
    {
        foreach ($packages as $package) {
            $package->accept($this);
        }
        
        $this->prepareMetrics();
        
        $this->calculateAbstractness();
        $this->calculateInstability();
        $this->calculateDistance();
    }
    
    public function getStats($uuid)
    {
        if (isset($this->nodeMetrics[$uuid])) {
            return $this->nodeMetrics[$uuid];
        }
        return array();
    }
    
    /**
     * Returns the generated project metrics.
     *
     * @return array(string=>PHP_Depend_Metrics_Dependency_Package)
     */
    public function getPackages()
    {
        if ($this->metrics !== null) {
            return $this->metrics;
        }

        $metrics = array();
        foreach ($this->packages as $name => $package) {
            
            $package->setAfferents($this->createPackages($this->afferents[$name]));
            $package->setEfferents($this->createPackages($this->efferents[$name]));
            
            $metrics[$name] = $package;
        }

        $this->metrics = new ArrayIterator($metrics);
        
        return $this->metrics;
    }
    
    /**
     * Visits a function node. 
     *
     * @param PHP_Depend_Code_Function $function The current function node.
     * 
     * @return void
     */
    public function visitFunction(PHP_Depend_Code_Function $function)
    {
        $package = $function->getPackage();
        $this->createPackage($package);
        // TODO: Implement functions
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
            $this->initPackageMetric($depPkgUUID);
            
            if (!in_array($depPkgUUID, $this->nodeMetrics[$pkgUUID]['ce'])) {
                $this->nodeMetrics[$pkgUUID]['ce'][]    = $depPkgUUID;
                $this->nodeMetrics[$depPkgUUID]['ca'][] = $pkgUUID;
            }
        }
        
        
        $package     = $method->getParent()->getPackage();
        $packageName = $package->getName();
        
        foreach ($method->getDependencies() as $dep) {
            // Skip for this package
            if ($dep->getPackage() === $package) {
                continue;
            }
            
            $depPkgName = $dep->getPackage()->getName();
            
            $this->createPackage($dep->getPackage());
            if (!$this->efferents[$packageName]->contains($dep->getPackage())) {
                $this->efferents[$packageName]->attach($dep->getPackage());
            }
            if (!$this->afferents[$depPkgName]->contains($package)) {
                $this->afferents[$depPkgName]->attach($package);
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
        $this->initPackageMetric($package->getUUID());

        foreach ($package->getTypes() as $type) {
            $type->accept($this);
        }
    }
    
    /**
     * Visits a property node. 
     *
     * @param PHP_Depend_Code_Property $property The property class node.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitor::visitProperty()
     */
    public function visitProperty(PHP_Depend_Code_Property $property)
    {
        
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
            $this->initPackageMetric($depPkgUUID);
            
            if (!in_array($depPkgUUID, $this->nodeMetrics[$pkgUUID]['ce'])) {
                $this->nodeMetrics[$pkgUUID]['ce'][]    = $depPkgUUID;
                $this->nodeMetrics[$depPkgUUID]['ca'][] = $pkgUUID;
            }
        }
        
        $pkgName = $type->getPackage()->getName();
        
        $this->createPackage($type->getPackage());
        
        foreach ($type->getDependencies() as $dep) {
            $depPkgName = $dep->getPackage()->getName();
            
            if ($dep->getPackage() !== $type->getPackage()) {
           
                $this->createPackage($dep->getPackage());
                
                if (!$this->efferents[$pkgName]->contains($dep->getPackage())) {
                    $this->efferents[$pkgName]->attach($dep->getPackage());
                    $this->afferents[$depPkgName]->attach($type->getPackage());
                }
            }
        }

        foreach ($type->getMethods() as $method) {
            $method->accept($this);
        }
    }
    
    protected function initPackageMetric($uuid)
    {
        if (!isset($this->nodeMetrics[$uuid])) {
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
    
    protected function prepareMetrics()
    {
        foreach ($this->nodeMetrics as $uuid => $metrics) {
            $this->nodeMetrics[$uuid]['ca'] = count($metrics['ca']);
            $this->nodeMetrics[$uuid]['ce'] = count($metrics['ce']);
        }
    }
    
    protected function calculateAbstractness()
    {
        foreach ($this->nodeMetrics as $uuid => $metrics) {
            if ($metrics['tc'] === 0) {
                continue;
            }
            $this->nodeMetrics[$uuid]['a'] = ($metrics['ac'] / $metrics['tc']);
        }
    }
    
    protected function calculateInstability()
    {
        foreach ($this->nodeMetrics as $uuid => $metrics) {
            // Count total incoming and outgoing dependencies
            $total = ($metrics['ca'] + $metrics['ce']);
            
            if ($total === 0) {
                continue;
            }
            $this->nodeMetrics[$uuid]['i'] = ($metrics['ce'] / $total);
        }
    }
    
    protected function calculateDistance()
    {
        foreach ($this->nodeMetrics as $uuid => $metrics) {
            $this->nodeMetrics[$uuid]['d'] = abs(($metrics['a'] + $metrics['i']) - 1);
        }
    }
    
    /**
     * Factory and singleton for metrics package objects.
     *
     * @param PHP_Depend_Code_Package $package The associated code package.
     * 
     * @return PHP_Depend_Metrics_Dependency_Package
     */
    protected function createPackage(PHP_Depend_Code_Package $package)
    {
        $name = $package->getName();
        
        // Check for an existing instance
        if (!isset($this->packages[$name])) {
            
            $packageInstance = new PHP_Depend_Metrics_Dependency_Package($package);
            
            // Create a new package
            $this->packages[$name]  = $packageInstance;
            $this->efferents[$name] = new SplObjectStorage();
            $this->afferents[$name] = new SplObjectStorage();
        }    
        // Return the package instance
        return $this->packages[$name];
    }
    
    /**
     * Factory/Singleton for a set of metric package objects
     *
     * @param SplObjectStorage $packages The input code packages.
     * 
     * @return array(PHP_Depend_Metrics_Dependency_Package)
     */
    protected function createPackages(SplObjectStorage $packages)
    {
        $output = array();
        foreach ($packages as $package) {
            $output[] = $this->createPackage($package);
        }
        return $output;
    }
}