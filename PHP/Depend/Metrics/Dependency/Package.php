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

require_once 'PHP/Depend/Metrics/Class.php';
require_once 'PHP/Depend/Metrics/Package.php';

/**
 * Special metrics package implementation for the dependency metric.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_Dependency_Package extends PHP_Depend_Metrics_Package
{
    /**
     * Concrete {@link PHP_Depend_Metrics_Class} objects that are part of this
     * package.
     *
     * @type array<PHP_Depend_Metrics_Class>
     * @var array(PHP_Depend_Metrics_Class) $concreteClasses
     */
    protected $concreteClasses = array();
    
    /**
     * Abstract {@link PHP_Depend_Metrics_Class} objects that are part of this
     * package.
     *
     * @type array<PHP_Depend_Metrics_Class>
     * @var array(PHP_Depend_Metrics_Class) $concreteClasses
     */
    protected $abstractClasses = array();
    
    /**
     * List of {@link PHP_Depend_Metrics_Dependency_Package} objects that 
     * internal classes depend on.
     *
     * @type array<PHP_Depend_Metrics_Dependency_Package>
     * @var array(PHP_Depend_Metrics_Dependency_Package) $efferents
     */
    protected $efferents = array();
    
    /**
     * List of {@link PHP_Depend_Metrics_Dependency_Package} objects that depend 
     * on classes from this package.
     *
     * @type array<PHP_Depend_Metrics_Dependency_Package>
     * @var array(PHP_Depend_Metrics_Dependency_Package) $afferents
     */
    protected $afferents = array();
    
    /**
     * Constructs a new package metrics instance.
     *
     * @param PHP_Depend_Code_Package $package The associated code package.
     */
    public function __construct(PHP_Depend_Code_Package $package)
    {
        parent::__construct($package);
        
        foreach ($package->getClasses() as $class) {
            if ($class->isAbstract()) {
                $this->abstractClasses[] = new PHP_Depend_Metric_Class($class);
            } else {
                $this->concreteClasses[] = new PHP_Depend_Metric_Class($class);
            }
        }
    }
    
    /**
     * Returns all concrete classes in this package
     *
     * @return Iterator
     */
    public function getConcreteClasses()
    {
        return new ArrayIterator($this->concreteClasses);
    }
    
    /**
     * Returns all abstract classes and interfaces in this package
     *
     * @return Iterator
     */
    public function getAbstractClasses()
    {
        return new ArrayIterator($this->abstractClasses);
    }
    
    /**
     * Returns {@link PHP_Depend_Metrics_Dependency_Package} objects that depend 
     * on classes from this package.
     *
     * @return Iterator
     */
    public function getAfferents()
    {
        return new ArrayIterator($this->afferents);
    }
    
    /**
     * Sets all dependent {@link PHP_Depend_Metrics_Dependency_Package} objects.
     *
     * @param array $afferents The incoming package dependencies.
     * 
     * @return void
     */
    public function setAfferents(array $afferents)
    {
        $this->afferents = $afferents;
    }
    
    /**
     * Returns {@link PHP_Depend_Metrics_Dependency_Package} objects that 
     * classes of this package depend on.
     *
     * @return Iterator
     */
    public function getEfferents()
    {
        return new ArrayIterator($this->efferents);
    }
    
    /**
     * Sets all {@link PHP_Depend_Metrics_Dependency_Package} objects that 
     * classes of this package depend on.
     *
     * @param array $efferents The outgoing package dependencies.
     * 
     * @return void
     */
    public function setEfferents(array $efferents)
    {
        $this->efferents = $efferents;
    }
    
    /**
     * Returns the total number of all classes and interfaces in this package.
     *
     * @return integer
     */
    public function getTotalClassCount()
    {
        return $this->getConcreteClassCount() + $this->getAbstractClassCount();
    }
    
    /**
     * Returns the number of concrete classes in this package.
     *
     * @return integer
     */
    public function getConcreteClassCount()
    {
        return count($this->concreteClasses);
    }
    
    /**
     * Returns the number of abstract classes in this package.
     *
     * @return integer
     */
    public function getAbstractClassCount()
    {
        return count($this->abstractClasses);
    }
    
    /**
     * The number of other packages that depend upon classes within the package 
     * is an indicator of the package's responsibility.
     *
     * @return integer The afferent coupling (Ca) of this package.
     */
    public function afferentCoupling()
    {
        return count($this->afferents);
    }
    
    /**
     * The number of other packages that the classes in the package depend upon 
     * is an indicator of the package's independence.
     *
     * @return integer The efferent coupling (Ce) of this package.
     */
    public function efferentCoupling()
    {
        return count($this->efferents);
    }
    
    /**
     * Returns the package abstractness (0-1).
     *
     * @return float
     */
    public function abstractness()
    {
        if ($this->getTotalClassCount() === 0) {
            return 0;
        }
        return ($this->getAbstractClassCount() / $this->getTotalClassCount());
    }
    
    /**
     * Returns the package instability (0-1).
     *
     * @return float
     */
    public function instability()
    {
        if (($total = count($this->efferents) + count($this->afferents)) === 0) {
            return 0;
        }
        return (count($this->efferents) / $total);
    }
    
    /**
     * Returns the package's distance from the main sequence (D).
     *
     * @return float
     */
    public function distance()
    {
        return abs(($this->abstractness() + $this->instability()) - 1);
    }
}