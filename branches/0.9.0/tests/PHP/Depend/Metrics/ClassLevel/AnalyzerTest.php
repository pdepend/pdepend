<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/File.php';
require_once 'PHP/Depend/Code/Interface.php';
require_once 'PHP/Depend/Code/Method.php';
require_once 'PHP/Depend/Code/NodeIterator.php';
require_once 'PHP/Depend/Code/Package.php';
require_once 'PHP/Depend/Code/Property.php';
require_once 'PHP/Depend/Metrics/ClassLevel/Analyzer.php';
require_once 'PHP/Depend/Metrics/CodeRank/Analyzer.php';
require_once 'PHP/Depend/Metrics/CyclomaticComplexity/Analyzer.php';

/**
 * Test case for the class level analyzer.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Metrics_ClassLevel_AnalyzerTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the {@link PHP_Depend_Metrics_ClassLevel_Analyzer::analyzer()}
     * method fails with an exception if no cc analyzer was set.
     *
     * @return void
     */
    public function testAnalyzerFailsWithoutCCAnalyzerFail()
    {
        $package  = new PHP_Depend_Code_Package('package1');
        $packages = new PHP_Depend_Code_NodeIterator(array($package));
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        
        $this->setExpectedException('RuntimeException', 'Missing required CC analyzer.');
        
        $analyzer->analyze($packages);
    }
    
    /**
     * Tests that {@link PHP_Depend_Metrics_ClassLevel_Analyzer::addAnalyzer()}
     * fails for an invalid child analyzer.
     *
     * @return void
     */
    public function testAddAnalyzerFailsForAnInvalidAnalyzerTypeFail()
    {
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        
        $this->setExpectedException('InvalidArgumentException', 'CC Analyzer required.');

        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CodeRank_Analyzer());
    }
    
    /**
     * Tests that the analyzer calculates the correct DIT values.
     *
     * @return void
     */
    public function testCalculateDITMetricNoInheritance()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($package->getClasses()->current());
        $this->assertEquals(0, $metrics['dit']);
    }

    /**
     * Tests that the analyzer calculates the correct DIT values.
     *
     * @return void
     */
    public function testCalculateDITMetricOneLevelInheritance()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($package->getClasses()->current());
        $this->assertEquals(1, $metrics['dit']);
    }

    /**
     * Tests that the analyzer calculates the correct DIT values.
     *
     * @return void
     */
    public function testCalculateDITMetricTwoLevelNoInheritance()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($package->getClasses()->current());
        $this->assertEquals(2, $metrics['dit']);
    }

    /**
     * Tests that the analyzer calculates the correct DIT values.
     *
     * @return void
     */
    public function testCalculateDITMetricThreeLevelNoInheritance()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($package->getClasses()->current());
        $this->assertEquals(3, $metrics['dit']);
    }

    /**
     * Tests that the analyzer calculates the correct DIT values.
     *
     * @return void
     */
    public function testCalculateDITMetricFourLevelNoInheritance()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($package->getClasses()->current());
        $this->assertEquals(4, $metrics['dit']);
    }
    
    /**
     * Tests that the analyzer calculates the correct IMPL values.
     *
     * @return void
     */
    public function testCalculateIMPLMetric()
    {
        $file = new PHP_Depend_Code_File(null);
        
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
        
        $interfsA->setSourceFile($file);
        $interfsB->setSourceFile($file);
        $interfsC->setSourceFile($file);
        $interfsD->setSourceFile($file);
        $interfsE->setSourceFile($file);
        $interfsF->setSourceFile($file);
        $classA->setSourceFile($file);
        $classB->setSourceFile($file);
        $classC->setSourceFile($file);
        
        $interfsB->addDependency($interfsA); // interface B extends A {}
        $interfsC->addDependency($interfsA); // interface C extends A {}
        $interfsD->addDependency($interfsB); // interface D extends B, E
        $interfsD->addDependency($interfsE); // interface D extends B, E
        $interfsE->addDependency($interfsF); // interface E extends F
        
        $classA->addDependency($interfsE); // class A implements E, C {}
        $classA->addDependency($interfsC); // class A implements E, C {}
        
        $classB->addDependency($interfsD); // class B extends C implements D, A {}
        $classB->addDependency($interfsA); // class B extends C implements D, A {}
        
        $classC->addDependency($interfsC); // class C implements C {}
        
        $classB->addDependency($classC); // class B extends C implements D, A {}
        
        $packages = new PHP_Depend_Code_NodeIterator(array($package1, $package2));
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
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
        $file = new PHP_Depend_Code_File(null);
        
        $package = new PHP_Depend_Code_Package('package');
        
        $classA = $package->addType(new PHP_Depend_Code_Class('A'));
        $classA->addMethod(new PHP_Depend_Code_Method('a'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PRIVATE);
        $classA->addMethod(new PHP_Depend_Code_Method('b'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED);
        $classA->addMethod(new PHP_Depend_Code_Method('c'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PUBLIC);
        $classA->addProperty(new PHP_Depend_Code_Property('$a'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PRIVATE);
        $classA->addProperty(new PHP_Depend_Code_Property('$b'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED);
        $classA->addProperty(new PHP_Depend_Code_Property('$c'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PUBLIC);
        
        $classB  = $package->addType(new PHP_Depend_Code_Class('B'));
        $classB->addMethod(new PHP_Depend_Code_Method('a'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PRIVATE);
        $classB->addMethod(new PHP_Depend_Code_Method('b'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED);
        $classB->addMethod(new PHP_Depend_Code_Method('c'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PUBLIC);
        $classB->addProperty(new PHP_Depend_Code_Property('$a'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PRIVATE);
        $classB->addProperty(new PHP_Depend_Code_Property('$b'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED);
        $classB->addProperty(new PHP_Depend_Code_Property('$c'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PUBLIC);
               
        $classC  = $package->addType(new PHP_Depend_Code_Class('C'));
        $classC->addMethod(new PHP_Depend_Code_Method('a'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PRIVATE);
        $classC->addMethod(new PHP_Depend_Code_Method('b'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED);
        $classC->addMethod(new PHP_Depend_Code_Method('c'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PUBLIC);
        $classC->addProperty(new PHP_Depend_Code_Property('$a'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PRIVATE);
        $classC->addProperty(new PHP_Depend_Code_Property('$b'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED);
        $classC->addProperty(new PHP_Depend_Code_Property('$c'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PUBLIC);
               
        $classA->setSourceFile($file);
        $classB->setSourceFile($file);
        $classC->setSourceFile($file);
               
        $classA->addDependency($classB); // class A extends B {}
        $classB->addDependency($classC); // class B extends C {}
        
        $packages = new PHP_Depend_Code_NodeIterator(array($package));
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
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
        $file = new PHP_Depend_Code_File(null);
        
        $package = new PHP_Depend_Code_Package('package');
        
        $classA = $package->addType(new PHP_Depend_Code_Class('A'));
        $classA->addMethod(new PHP_Depend_Code_Method('a'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PRIVATE);
        $classA->addMethod(new PHP_Depend_Code_Method('b'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED);
        $classA->addMethod(new PHP_Depend_Code_Method('c'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PUBLIC);
        $classA->addProperty(new PHP_Depend_Code_Property('$a'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PRIVATE);
        $classA->addProperty(new PHP_Depend_Code_Property('$b'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED);
        $classA->addProperty(new PHP_Depend_Code_Property('$c'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PUBLIC);
        
        $classB  = $package->addType(new PHP_Depend_Code_Class('B'));
        $classB->addMethod(new PHP_Depend_Code_Method('a'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PRIVATE);
        $classB->addMethod(new PHP_Depend_Code_Method('b'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED);
        $classB->addMethod(new PHP_Depend_Code_Method('c'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PUBLIC);
        $classB->addProperty(new PHP_Depend_Code_Property('$a'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PRIVATE);
        $classB->addProperty(new PHP_Depend_Code_Property('$b'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED);
        $classB->addProperty(new PHP_Depend_Code_Property('$c'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PUBLIC);
               
        $classA->addDependency($classB); // class A extends B {}
               
        $classA->setSourceFile($file);
        $classB->setSourceFile($file);
        
        $packages = new PHP_Depend_Code_NodeIterator(array($package));
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
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
        $file = new PHP_Depend_Code_File(null);
        
        $package = new PHP_Depend_Code_Package('package');
        
        $classA = $package->addType(new PHP_Depend_Code_Class('A'));
        $classA->addMethod(new PHP_Depend_Code_Method('a'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PRIVATE);
        $classA->addMethod(new PHP_Depend_Code_Method('b'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED);
        $classA->addMethod(new PHP_Depend_Code_Method('c'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PUBLIC);
        $classA->addProperty(new PHP_Depend_Code_Property('$a'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PRIVATE);
        $classA->addProperty(new PHP_Depend_Code_Property('$b'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED);
        $classA->addProperty(new PHP_Depend_Code_Property('$c'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PUBLIC);
               
        $classB = $package->addType(new PHP_Depend_Code_Class('B'));
        $classB->addProperty(new PHP_Depend_Code_Property('$a'))
               ->setModifiers(PHP_Depend_ConstantsI::IS_PUBLIC);
               
        $classA->setSourceFile($file);
        $classB->setSourceFile($file);
        
        $classA->addDependency($classB); // class A extends B {}
        
        $packages = new PHP_Depend_Code_NodeIterator(array($package));
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
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
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($package->getClasses()->current());
        $this->assertEquals(4, $metrics['varsi']);
    }

    /**
     * Tests that the analyzer calculates the correct VARSi metric
     *
     * @return void
     */
    public function testCalculateVARSiMetricWithInheritance()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($package->getClasses()->current());
        $this->assertEquals(5, $metrics['varsi']);
    }
    
    /**
     * Tests that the analyzer calculates the correct VARSnp metric
     *
     * @return void
     */
    public function testCalculateVARSnpMetric()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($package->getClasses()->current());
        $this->assertEquals(2, $metrics['varsnp']);
    }

    /**
     * Tests that the analyzer calculates the correct VARSnp metric
     *
     * @return void
     */
    public function testCalculateVARSnpMetricWithInheritance()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($package->getClasses()->current());
        $this->assertEquals(1, $metrics['varsnp']);
    }
    
    /**
     * Tests that the analyzer calculates the correct WMC metric. 
     *
     * @return void
     */
    public function testCalculateWMCMetric()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($package->getClasses()->current());
        $this->assertEquals(3, $metrics['wmc']);      
    }

    /**
     * Tests that the analyzer calculates the correct WMC metric.
     *
     * @return void
     */
    public function testCalculateWMCMetricOneLevelInheritance()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($package->getClasses()->current());
        $this->assertEquals(3, $metrics['wmc']);
    }

    /**
     * Tests that the analyzer calculates the correct WMC metric.
     *
     * @return void
     */
    public function testCalculateWMCMetricTwoLevelInheritance()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($package->getClasses()->current());
        $this->assertEquals(3, $metrics['wmc']);
    }
    
    /**
     * Tests that the analyzer calculates the correct WMCi metric. 
     *
     * @return void
     */    
    public function testCalculateWMCiMetric()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($package->getClasses()->current());
        $this->assertEquals(3, $metrics['wmci']);
    }

    /**
     * Tests that the analyzer calculates the correct WMCi metric.
     *
     * @return void
     */
    public function testCalculateWMCiMetricOneLevelInheritance()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($package->getClasses()->current());
        $this->assertEquals(4, $metrics['wmci']);
    }

    /**
     * Tests that the analyzer calculates the correct WMCi metric.
     *
     * @return void
     */
    public function testCalculateWMCiMetricTwoLevelInheritance()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($package->getClasses()->current());
        $this->assertEquals(5, $metrics['wmci']);
    }
    
    /**
     * Tests that the analyzer calculates the correct WMCnp metric. 
     *
     * @return void
     */
    public function testCalculateWMCnpMetric()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($package->getClasses()->current());
        $this->assertEquals(1, $metrics['wmcnp']);
    }

    /**
     * Tests that the analyzer calculates the correct WMCnp metric.
     *
     * @return void
     */
    public function testCalculateWMCnpMetricOneLevelInheritance()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($package->getClasses()->current());
        $this->assertEquals(2, $metrics['wmcnp']);
    }

    /**
     * Tests that the analyzer calculates the correct WMCnp metric.
     *
     * @return void
     */
    public function testCalculateWMCnpMetricTwoLevelInheritance()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($package->getClasses()->current());
        $this->assertEquals(1, $metrics['wmcnp']);
    }
}