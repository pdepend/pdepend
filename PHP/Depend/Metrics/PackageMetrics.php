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

/**
 * Represents the metrics for a php package.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_PackageMetrics
{
    /**
     * The context package instance.
     *
     * @type PHP_Depend_Code_Package
     * @var PHP_Depend_Code_Package $package
     */
    protected $package = null;
    
    /**
     * Number of concrete classes in this package.
     *
     * @type integer
     * @var integer $cc
     */
    protected $cc = 0;
    
    /**
     * Number of abstract classes in this package.
     *
     * @type integer
     * @var integer $ac
     */
    protected $ac = 0;
    
    /**
     * Number of packages that internal classes depend on.
     * 
     * @type integer
     * @var integer $ca
     */
    protected $ca = 0;
    
    /**
     * Number of packages that depend on internal classes.
     * 
     * @type integer
     * @var integer $ce
     */
    protected $ce = 0;
    
    /**
     * The package abstractness (0-1).
     *
     * @type float
     * @var float $a
     */
    protected $a = 0;
    
    /**
     * The package instability (0-1).
     *
     * @type float
     * @var float $i
     */
    protected $i = 0;
    
    /**
     * The package's distance from the main sequence (D).
     *
     * @type float
     * @var float $d
     */
    protected $d = 0;
    
    /**
     * The total number of all classes and interfaces in this package
     *
     * @type integer
     * @var integer $tc
     */
    protected $tc = 0;
    
    protected $concreteClasses = array();
    
    protected $abstractClasses = array();
    
    /**
     * List of {@link PHP_Depend_Code_Package} objects that internal classes
     * depend on.
     *
     * @type array<PHP_Depend_Code_Package>
     * @var array(PHP_Depend_Code_Package) $efferents
     */
    protected $efferents = array();
    
    /**
     * List of {@link PHP_Depend_Code_Package} objects that depend on classes
     * from this package.
     *
     * @type array<PHP_Depend_Code_Package>
     * @var array(PHP_Depend_Code_Package) $afferents
     */
    protected $afferents = array();
    
    /**
     * Constructs a new package metrics instance.
     *
     * @param PHP_Depend_Code_Package        $pkg The context package.
     * @param array(PHP_Depend_Code_Class)   $cc  Concrete classes.
     * @param array(PHP_Depend_Code_Class)   $ac  Abstract classes and interfaces.
     * @param array(PHP_Depend_Code_Package) $ca  Incoming dependencies.
     * @param array(PHP_Depend_Code_Package) $ce  Outgoing dependencies.
     */
    public function __construct(PHP_Depend_Code_Package $pkg, array $cc, array $ac, array $ca, array $ce)
    {
        $this->concreteClasses = $cc;
        $this->abstractClasses = $ac;
        
        $this->efferents = $ce;
        $this->afferents = $ca;

        $this->package = $pkg;
        
        $this->cc = count($cc);
        $this->ac = count($ac);
        $this->ca = count($ca);
        $this->ce = count($ce);
        $this->tc = ($this->cc + $this->ac);
        
        $cea = ($this->ce + $this->ca);
        
        $this->a = ($this->tc === 0 ? 0 : ($this->ac / $this->tc));
        $this->i = ($cea === 0 ? 0 : ($this->ce / $cea));
        $this->d = abs(($this->a + $this->i) - 1);
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
     * Returns {@link PHP_Depend_Code_Package} objects that depend on classes
     * from this package.
     *
     * @return Iterator
     */
    public function getAfferents()
    {
        return $this->afferents;
    }
    
    /**
     * Returns {@link PHP_Depend_Code_Package} objects that internal classes
     * depend on.
     *
     * @return Iterator
     */
    public function getEfferents()
    {
        return new ArrayIterator($this->efferents);
    }
    
    /**
     * Returns the context package object.
     *
     * @return PHP_Depend_Code_Package
     */
    public function getPackage()
    {
        return $this->package;
    }
    
    /**
     * Returns the name of the context package.
     *
     * @return string
     */
    public function getName()
    {
        return $this->package->getName();
    }
    
    /**
     * Returns the total number of all classes and interfaces in this package.
     *
     * @return integer
     */
    public function getTotalClassCount()
    {
        return $this->tc;
    }
    
    /**
     * Returns the number of concrete classes in this package.
     *
     * @return integer
     */
    public function getConcreteClassCount()
    {
        return $this->cc;
    }
    
    /**
     * Returns the number of abstract classes in this package.
     *
     * @return integer
     */
    public function getAbstractClassCount()
    {
        return $this->ac;
    }
    
    /**
     * The number of other packages that depend upon classes within the package 
     * is an indicator of the package's responsibility.
     *
     * @return integer The afferent coupling (Ca) of this package.
     */
    public function afferentCoupling()
    {
        return $this->ca;
    }
    
    /**
     * The number of other packages that the classes in the package depend upon 
     * is an indicator of the package's independence.
     *
     * @return integer The efferent coupling (Ce) of this package.
     */
    public function efferentCoupling()
    {
        return $this->ce;
    }
    
    /**
     * Returns the package abstractness (0-1).
     *
     * @return float
     */
    public function abstractness()
    {
        return $this->a;
    }
    
    /**
     * Returns the package instability (0-1).
     *
     * @return float
     */
    public function instability()
    {
        return $this->i;
    }
    
    /**
     * Returns the package's distance from the main sequence (D).
     *
     * @return float
     */
    public function distance()
    {
        return $this->d;
    }
}