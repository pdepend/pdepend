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

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/Interface.php';
require_once 'PHP/Depend/Code/Method.php';
require_once 'PHP/Depend/Code/NodeIterator.php';
require_once 'PHP/Depend/Code/Package.php';
require_once 'PHP/Depend/Code/Property.php';
require_once 'PHP/Depend/Metrics/ClassLevel/Analyzer.php';

/**
 * Test case for the class level analyzer.
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
class PHP_Depend_Metrics_ClassLevel_AnalyzerTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the analyzer calculates the correct DIT values.
     *
     * @return void
     */
    public function testCalculateDITMetric()
    {
        $package = new PHP_Depend_Code_Package('package1');
        $interfs = $package->addType(new PHP_Depend_Code_Interface('interfs'));
        $classA  = $package->addType(new PHP_Depend_Code_Class('classA'));
        $classB  = $package->addType(new PHP_Depend_Code_Class('classB'));
        $classC  = $package->addType(new PHP_Depend_Code_Class('classC'));
        $classD  = $package->addType(new PHP_Depend_Code_Class('classD'));
        $classE  = $package->addType(new PHP_Depend_Code_Class('classE'));
        $classF  = $package->addType(new PHP_Depend_Code_Class('classF'));
        
        $interfs->addChildType($classA); // class A implements I // DIT = 0 
        $classA->addChildType($classB);  // class B extends A {} // DIT = 1
        $classB->addChildType($classC);  // class C extends B {} // DIT = 2
        $classC->addChildType($classD);  // class D extends C {} // DIT = 3
        $classC->addChildType($classE);  // class E extends C {} // DIT = 3
        $classE->addChildType($classF);  // class F extends E {} // DIT = 4
        
        $packages = new PHP_Depend_Code_NodeIterator(array($package));
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->analyze($packages);
        
        $m = $analyzer->getNodeMetrics($classA);
        $this->assertEquals(0, $m['dit']);
        $m = $analyzer->getNodeMetrics($classB);
        $this->assertEquals(1, $m['dit']);
        $m = $analyzer->getNodeMetrics($classC);
        $this->assertEquals(2, $m['dit']);
        $m = $analyzer->getNodeMetrics($classD);
        $this->assertEquals(3, $m['dit']);
        $m = $analyzer->getNodeMetrics($classE);
        $this->assertEquals(3, $m['dit']);
        $m = $analyzer->getNodeMetrics($classF);
        $this->assertEquals(4, $m['dit']);
    }
    
    /**
     * Tests that the analyzer calculates the correct IMPL values.
     *
     * @return void
     */
    public function testCalculateIMPLMetric()
    {
        $package1 = new PHP_Depend_Code_Package('package1');
        $interfsA = $package1->addType(new PHP_Depend_Code_Interface('A'));
        $interfsB = $package1->addType(new PHP_Depend_Code_Interface('B'));
        $interfsC = $package1->addType(new PHP_Depend_Code_Interface('C'));
        $interfsD = $package1->addType(new PHP_Depend_Code_Interface('D'));
        $interfsE = $package1->addType(new PHP_Depend_Code_Interface('E'));
        $interfsF = $package1->addType(new PHP_Depend_Code_Interface('F'));
        
        $package2 = new PHP_Depend_Code_Package('package2');
        $classA   = $package2->addType(new PHP_Depend_Code_Class('A'));
        $classB   = $package2->addType(new PHP_Depend_Code_Class('B'));
        $classC   = $package2->addType(new PHP_Depend_Code_Class('C'));
        
        $interfsA->addChildType($interfsB); // interface B extends A {}
        $interfsA->addChildType($interfsC); // interface C extends A {}
        $interfsB->addChildType($interfsD); // interface D extends B, E
        $interfsE->addChildType($interfsD); // interface D extends B, E
        $interfsF->addChildType($interfsE); // interface E extends F
        
        $interfsE->addChildType($classA); // class A implements E, C {}
        $interfsC->addChildType($classA); // class A implements E, C {}
        
        $interfsD->addChildType($classB); // class B extends C implements D, A {}
        $interfsA->addChildType($classB); // class B extends C implements D, A {}
        
        $interfsC->addChildType($classC); // class C implements C {}
        
        $classC->addChildType($classB); // class B extends C implements D, A {}
        
        $packages = new PHP_Depend_Code_NodeIterator(array($package1, $package2));
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->analyze($packages);
        
        $m = $analyzer->getNodeMetrics($classA);
        $this->assertEquals(4, $m['impl']);
        $m = $analyzer->getNodeMetrics($classB);
        $this->assertEquals(6, $m['impl']);
        $m = $analyzer->getNodeMetrics($classC);
        $this->assertEquals(2, $m['impl']);
    }
    
    /**
     * Tests that the calculated Class Interface Size(CSI) is correct.
     *
     * @return void
     */
    public function testCalculateCISMetric()
    {
        $package = new PHP_Depend_Code_Package('package');
        
        $classA = $package->addType(new PHP_Depend_Code_Class('A'));
        $classA->addMethod(new PHP_Depend_Code_Method('a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classA->addMethod(new PHP_Depend_Code_Method('b'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classA->addMethod(new PHP_Depend_Code_Method('c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
        $classA->addProperty(new PHP_Depend_Code_Property('$a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classA->addProperty(new PHP_Depend_Code_Property('$b'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classA->addProperty(new PHP_Depend_Code_Property('$c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
        
        $classB  = $package->addType(new PHP_Depend_Code_Class('B'));
        $classB->addMethod(new PHP_Depend_Code_Method('a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classB->addMethod(new PHP_Depend_Code_Method('b'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classB->addMethod(new PHP_Depend_Code_Method('c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
        $classB->addProperty(new PHP_Depend_Code_Property('$a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classB->addProperty(new PHP_Depend_Code_Property('$b'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classB->addProperty(new PHP_Depend_Code_Property('$c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
               
        $classC  = $package->addType(new PHP_Depend_Code_Class('C'));
        $classC->addMethod(new PHP_Depend_Code_Method('a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classC->addMethod(new PHP_Depend_Code_Method('b'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classC->addMethod(new PHP_Depend_Code_Method('c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
        $classC->addProperty(new PHP_Depend_Code_Property('$a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classC->addProperty(new PHP_Depend_Code_Property('$b'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classC->addProperty(new PHP_Depend_Code_Property('$c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
               
        $classB->addChildType($classA); // class A extends B {}
        $classC->addChildType($classB); // class B extends C {}
        
        $packages = new PHP_Depend_Code_NodeIterator(array($package));
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->analyze($packages);
        
        $m = $analyzer->getNodeMetrics($classA);
        $this->assertEquals(2, $m['cis']);
        $m = $analyzer->getNodeMetrics($classB);
        $this->assertEquals(2, $m['cis']);
        $m = $analyzer->getNodeMetrics($classC);
        $this->assertEquals(2, $m['cis']);
    }
    
    /**
     * Tests that the calculated Class SiZe(CSZ) metric is correct.
     *
     * @return void
     */
    public function testCalculateCSZMetric()
    {
        $package = new PHP_Depend_Code_Package('package');
        
        $classA = $package->addType(new PHP_Depend_Code_Class('A'));
        $classA->addMethod(new PHP_Depend_Code_Method('a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classA->addMethod(new PHP_Depend_Code_Method('b'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classA->addMethod(new PHP_Depend_Code_Method('c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
        $classA->addProperty(new PHP_Depend_Code_Property('$a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classA->addProperty(new PHP_Depend_Code_Property('$b'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classA->addProperty(new PHP_Depend_Code_Property('$c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
        
        $classB  = $package->addType(new PHP_Depend_Code_Class('B'));
        $classB->addMethod(new PHP_Depend_Code_Method('a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classB->addMethod(new PHP_Depend_Code_Method('b'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classB->addMethod(new PHP_Depend_Code_Method('c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
        $classB->addProperty(new PHP_Depend_Code_Property('$a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classB->addProperty(new PHP_Depend_Code_Property('$b'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classB->addProperty(new PHP_Depend_Code_Property('$c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
               
        $classB->addChildType($classA); // class A extends B {}
        
        $packages = new PHP_Depend_Code_NodeIterator(array($package));
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->analyze($packages);
        
        $m = $analyzer->getNodeMetrics($classA);
        $this->assertEquals(6, $m['csz']);
        $m = $analyzer->getNodeMetrics($classB);
        $this->assertEquals(6, $m['csz']);
    }
    
    /**
     * Tests that the analyzer calculates the correct VARS metric
     *
     * @return void
     */
    public function testCalculateVARSMetric()
    {
        $package = new PHP_Depend_Code_Package('package');
        
        $classA = $package->addType(new PHP_Depend_Code_Class('A'));
        $classA->addMethod(new PHP_Depend_Code_Method('a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classA->addMethod(new PHP_Depend_Code_Method('b'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classA->addMethod(new PHP_Depend_Code_Method('c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
        $classA->addProperty(new PHP_Depend_Code_Property('$a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classA->addProperty(new PHP_Depend_Code_Property('$b'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classA->addProperty(new PHP_Depend_Code_Property('$c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
               
        $classB = $package->addType(new PHP_Depend_Code_Class('B'));
        $classB->addProperty(new PHP_Depend_Code_Property('$a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
        
        $classB->addChildType($classA); // class A extends B {}
        
        $packages = new PHP_Depend_Code_NodeIterator(array($package));
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->analyze($packages);
        
        $m = $analyzer->getNodeMetrics($classA);
        $this->assertEquals(3, $m['vars']);
        $m = $analyzer->getNodeMetrics($classB);
        $this->assertEquals(1, $m['vars']);
    }
    
    /**
     * Tests that the analyzer calculates the correct VARSi metric
     *
     * @return void
     */
    public function testCalculateVARSiMetric()
    {
        $package = new PHP_Depend_Code_Package('package');
        
        $classA = $package->addType(new PHP_Depend_Code_Class('A'));
        $classA->addMethod(new PHP_Depend_Code_Method('a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classA->addMethod(new PHP_Depend_Code_Method('b'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classA->addMethod(new PHP_Depend_Code_Method('c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
        $classA->addProperty(new PHP_Depend_Code_Property('$a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classA->addProperty(new PHP_Depend_Code_Property('$b'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classA->addProperty(new PHP_Depend_Code_Property('$c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
               
        $classB = $package->addType(new PHP_Depend_Code_Class('B'));
        $classB->addProperty(new PHP_Depend_Code_Property('$a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classB->addProperty(new PHP_Depend_Code_Property('$d'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classB->addProperty(new PHP_Depend_Code_Property('$e'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classB->addProperty(new PHP_Depend_Code_Property('$f'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
        
        $classB->addChildType($classA); // class A extends B {}
        
        $packages = new PHP_Depend_Code_NodeIterator(array($package));
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->analyze($packages);
        
        $m = $analyzer->getNodeMetrics($classA);
        $this->assertEquals(5, $m['varsi']);
        $m = $analyzer->getNodeMetrics($classB);
        $this->assertEquals(4, $m['varsi']);
    }
    
    /**
     * Tests that the analyzer calculates the correct VARSnp metric
     *
     * @return void
     */
    public function testCalculateVARSnpMetric()
    {
        $package = new PHP_Depend_Code_Package('package');
        
        $classA = $package->addType(new PHP_Depend_Code_Class('A'));
        $classA->addMethod(new PHP_Depend_Code_Method('a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classA->addMethod(new PHP_Depend_Code_Method('b'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classA->addMethod(new PHP_Depend_Code_Method('c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
        $classA->addProperty(new PHP_Depend_Code_Property('$a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classA->addProperty(new PHP_Depend_Code_Property('$b'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classA->addProperty(new PHP_Depend_Code_Property('$c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
               
        $classB = $package->addType(new PHP_Depend_Code_Class('B'));
        $classB->addProperty(new PHP_Depend_Code_Property('$c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
        $classB->addProperty(new PHP_Depend_Code_Property('$d'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classB->addProperty(new PHP_Depend_Code_Property('$e'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classB->addProperty(new PHP_Depend_Code_Property('$f'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
        
        $classB->addChildType($classA); // class A extends B {}
        
        $packages = new PHP_Depend_Code_NodeIterator(array($package));
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->analyze($packages);
        
        $m = $analyzer->getNodeMetrics($classA);
        $this->assertEquals(1, $m['varsnp']);
        $m = $analyzer->getNodeMetrics($classB);
        $this->assertEquals(2, $m['varsnp']);
    }
    
    /**
     * Tests that the analyzer calculates the correct WMC metric. 
     *
     * @return void
     */
    public function testCalculateWMCMetric()
    {
        $package = new PHP_Depend_Code_Package('package');
        
        $classA = $package->addType(new PHP_Depend_Code_Class('A'));
        $classA->addMethod(new PHP_Depend_Code_Method('a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classA->addMethod(new PHP_Depend_Code_Method('b'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classA->addMethod(new PHP_Depend_Code_Method('c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
        $classA->addProperty(new PHP_Depend_Code_Property('$a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        
        $classB  = $package->addType(new PHP_Depend_Code_Class('B'));
        $classB->addMethod(new PHP_Depend_Code_Method('a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classB->addMethod(new PHP_Depend_Code_Method('b'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classB->addMethod(new PHP_Depend_Code_Method('c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
               
        $classC  = $package->addType(new PHP_Depend_Code_Class('C'));
        $classC->addMethod(new PHP_Depend_Code_Method('a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classC->addMethod(new PHP_Depend_Code_Method('b'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classC->addMethod(new PHP_Depend_Code_Method('c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
        $classC->addProperty(new PHP_Depend_Code_Property('$c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
               
        $classB->addChildType($classA); // class A extends B {}
        $classC->addChildType($classB); // class B extends C {}
        
        $packages = new PHP_Depend_Code_NodeIterator(array($package));
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->analyze($packages);
        
        $m = $analyzer->getNodeMetrics($classA);
        $this->assertEquals(3, $m['wmc']);
        $m = $analyzer->getNodeMetrics($classB);
        $this->assertEquals(3, $m['wmc']);
        $m = $analyzer->getNodeMetrics($classC);
        $this->assertEquals(3, $m['wmc']);
    }
    
    /**
     * Tests that the analyzer calculates the correct WMCi metric. 
     *
     * @return void
     */    
    public function testCalculateWMCiMetric()
    {
        $package = new PHP_Depend_Code_Package('package');
        
        $classA = $package->addType(new PHP_Depend_Code_Class('A'));
        $classA->addMethod(new PHP_Depend_Code_Method('a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classA->addMethod(new PHP_Depend_Code_Method('b'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classA->addMethod(new PHP_Depend_Code_Method('c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
        $classA->addProperty(new PHP_Depend_Code_Property('$a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        
        $classB  = $package->addType(new PHP_Depend_Code_Class('B'));
        $classB->addMethod(new PHP_Depend_Code_Method('a2'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classB->addMethod(new PHP_Depend_Code_Method('b2'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classB->addMethod(new PHP_Depend_Code_Method('c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
               
        $classC  = $package->addType(new PHP_Depend_Code_Class('C'));
        $classC->addMethod(new PHP_Depend_Code_Method('a3'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classC->addMethod(new PHP_Depend_Code_Method('b2'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classC->addMethod(new PHP_Depend_Code_Method('c2'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
        $classC->addProperty(new PHP_Depend_Code_Property('$c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
               
        $classB->addChildType($classA); // class A extends B {}
        $classC->addChildType($classB); // class B extends C {}
        
        $packages = new PHP_Depend_Code_NodeIterator(array($package));
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->analyze($packages);
        
        $m = $analyzer->getNodeMetrics($classA);
        $this->assertEquals(5, $m['wmci']);
        $m = $analyzer->getNodeMetrics($classB);
        $this->assertEquals(4, $m['wmci']);
        $m = $analyzer->getNodeMetrics($classC);
        $this->assertEquals(3, $m['wmci']);
    }
    
    /**
     * Tests that the analyzer calculates the correct WMCnp metric. 
     *
     * @return void
     */    
    public function testCalculateWMCnpMetric()
    {
        $package = new PHP_Depend_Code_Package('package');
        
        $classA = $package->addType(new PHP_Depend_Code_Class('A'));
        $classA->addMethod(new PHP_Depend_Code_Method('a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classA->addMethod(new PHP_Depend_Code_Method('b'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classA->addMethod(new PHP_Depend_Code_Method('c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
        $classA->addProperty(new PHP_Depend_Code_Property('$a'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        
        $classB  = $package->addType(new PHP_Depend_Code_Class('B'));
        $classB->addMethod(new PHP_Depend_Code_Method('a2'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classB->addMethod(new PHP_Depend_Code_Method('b2'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
        $classB->addMethod(new PHP_Depend_Code_Method('c2'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
               
        $classC  = $package->addType(new PHP_Depend_Code_Class('C'));
        $classC->addMethod(new PHP_Depend_Code_Method('a3'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE);
        $classC->addMethod(new PHP_Depend_Code_Method('b3'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED);
        $classC->addMethod(new PHP_Depend_Code_Method('c3'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
        $classC->addProperty(new PHP_Depend_Code_Property('$c'))
               ->setVisibility(PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC);
               
        $classB->addChildType($classA); // class A extends B {}
        $classC->addChildType($classB); // class B extends C {}
        
        $packages = new PHP_Depend_Code_NodeIterator(array($package));
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->analyze($packages);
        
        $m = $analyzer->getNodeMetrics($classA);
        $this->assertEquals(1, $m['wmcnp']);
        $m = $analyzer->getNodeMetrics($classB);
        $this->assertEquals(2, $m['wmcnp']);
        $m = $analyzer->getNodeMetrics($classC);
        $this->assertEquals(1, $m['wmcnp']);
    }
}