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
require_once 'PHP/Depend/Metrics/PackageProviderI.php';
require_once 'PHP/Depend/Metrics/ResultSetI.php';
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
               PHP_Depend_Metrics_AnalyzerI,
               PHP_Depend_Metrics_PackageProviderI,
               PHP_Depend_Metrics_ResultSetI
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
    
    /**
     * Processes all {@link PHP_Depend_Code_Package} code nodes.
     *
     * @param PHP_Depend_Code_NodeIterator $packages All code packages.
     * 
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
        foreach ($package->getTypes() as $type) {
            $type->accept($this);
        }
        
        foreach ($package->getFunctions() as $function) {
            $function->accept($this);
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
    
        foreach ($class->getProperties() as $property) {
            $property->accept($this);
        }
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
        $pkgName = $type->getPackage()->getName();
        
        $this->createPackage($type->getPackage());
        
        foreach ($type->getDependencies() as $dep) {
            $depPkgName = $dep->getPackage()->getName();
            
            if ($dep->getPackage() !== $type->getPackage()) {
           
                $this->createPackage($dep->getPackage());
                
                if (!$this->efferents[$pkgName]->contains($dep->getPackage())) {
                    $this->efferents[$pkgName]->attach($dep->getPackage());
                }
                if (!$this->afferents[$depPkgName]->contains($type->getPackage())) {
                    $this->afferents[$depPkgName]->attach($type->getPackage());
                }
            }
        }

        foreach ($type->getMethods() as $method) {
            $method->accept($this);
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