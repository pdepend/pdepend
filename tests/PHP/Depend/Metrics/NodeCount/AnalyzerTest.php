<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008-2010, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/Interface.php';
require_once 'PHP/Depend/Code/Method.php';
require_once 'PHP/Depend/Code/NodeIterator.php';
require_once 'PHP/Depend/Code/Package.php';
require_once 'PHP/Depend/Metrics/NodeCount/Analyzer.php';

/**
 * Test case for the node count analyzer.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Metrics_NodeCount_AnalyzerTest extends PHP_Depend_AbstractTest
{
    /**
     * testVisitClassIgnoresClassesThatAreNotUserDefined
     * 
     * @return void
     * @covers PHP_Depend_Metrics_NodeCount_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodecount
     * @group unittest
     */
    public function testVisitClassIgnoresClassesThatAreNotUserDefined()
    {
        $notUserDefined = new PHP_Depend_Code_Class('Pichler');

        $package = new PHP_Depend_Code_Package('PHP_Depend');
        $package->addType($notUserDefined);

        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze(new PHP_Depend_Code_NodeIterator(array($package)));

        $metrics = $analyzer->getNodeMetrics($package);
        $this->assertEquals(0, $metrics['noc']);
    }

    /**
     * testVisitClassCountsClassesThatAreNotUserDefined
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeCount_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodecount
     * @group unittest
     */
    public function testVisitClassCountsClassesThatAreNotUserDefined()
    {

        $userDefined = new PHP_Depend_Code_Class('Manuel');
        $userDefined->setUserDefined();

        $package = new PHP_Depend_Code_Package('PHP_Depend');
        $package->addType($userDefined);

        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze(new PHP_Depend_Code_NodeIterator(array($package)));

        $metrics = $analyzer->getNodeMetrics($package);
        $this->assertEquals(1, $metrics['noc']);
    }

    /**
     * testVisitClassIgnoresInterfacesThatAreNotUserDefined
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeCount_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodecount
     * @group unittest
     */
    public function testVisitClassIgnoresInterfacesThatAreNotUserDefined()
    {
        $notUserDefined = new PHP_Depend_Code_Interface('Pichler');

        $package = new PHP_Depend_Code_Package('PHP_Depend');
        $package->addType($notUserDefined);

        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze(new PHP_Depend_Code_NodeIterator(array($package)));

        $metrics = $analyzer->getNodeMetrics($package);
        $this->assertEquals(0, $metrics['noi']);
    }

    /**
     * testVisitClassCountsInterfacesThatAreNotUserDefined
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeCount_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodecount
     * @group unittest
     */
    public function testVisitClassCountsInterfacesThatAreNotUserDefined()
    {

        $userDefined = new PHP_Depend_Code_Interface('Manuel');
        $userDefined->setUserDefined();

        $package = new PHP_Depend_Code_Package('PHP_Depend');
        $package->addType($userDefined);

        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze(new PHP_Depend_Code_NodeIterator(array($package)));

        $metrics = $analyzer->getNodeMetrics($package);
        $this->assertEquals(1, $metrics['noi']);
    }

    /**
     * Tests that the analyzer calculates the correct number of packages value.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectNumberOfPackages()
    {
        $packageA = new PHP_Depend_Code_Package('A');
        $packageB = new PHP_Depend_Code_Package('B');
        $packageC = new PHP_Depend_Code_Package('C');
        
        $packages = array($packageA, $packageB, $packageC);
        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze(new PHP_Depend_Code_NodeIterator($packages));
        
        $project = $analyzer->getProjectMetrics();
        
        $this->assertArrayHasKey('nop', $project);
        $this->assertEquals(3, $project['nop']);
    }
    
    /**
     * Tests that the analyzer calculates the correct number of classes values.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectNumberOfClasses()
    {
        $packageA = new PHP_Depend_Code_Package('A');
        $packageA->addType(new PHP_Depend_Code_Class('A1'));
        $packageA->addType(new PHP_Depend_Code_Class('A2'));
        $packageA->addType(new PHP_Depend_Code_Class('A3'));
        $packageB = new PHP_Depend_Code_Package('B');
        $packageB->addType(new PHP_Depend_Code_Class('B1'));
        $packageB->addType(new PHP_Depend_Code_Class('B2'));
        $packageC = new PHP_Depend_Code_Package('C');
        $packageC->addType(new PHP_Depend_Code_Class('C1'));
        
        $packages = array($packageA, $packageB, $packageC);
        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze(new PHP_Depend_Code_NodeIterator($packages));
        
        $project = $analyzer->getProjectMetrics();
        
        $this->assertArrayHasKey('noc', $project);
        $this->assertEquals(6, $project['noc']);
        
        $metrics = $analyzer->getNodeMetrics($packageA);
        $this->assertArrayHasKey('noc', $metrics);
        $this->assertEquals(3, $metrics['noc']);
        
        $metrics = $analyzer->getNodeMetrics($packageB);
        $this->assertArrayHasKey('noc', $metrics);
        $this->assertEquals(2, $metrics['noc']);
        
        $metrics = $analyzer->getNodeMetrics($packageC);
        $this->assertArrayHasKey('noc', $metrics);
        $this->assertEquals(1, $metrics['noc']);
    }
    
    /**
     * Tests that the analyzer calculates the correct number of interfaces values.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectNumberOfInterfaces()
    {
        $packageA = new PHP_Depend_Code_Package('A');
        $packageA->addType(new PHP_Depend_Code_Class('A1'));
        $packageA->addType(new PHP_Depend_Code_Class('A2'));
        $packageA->addType(new PHP_Depend_Code_Interface('A3'));
        $packageB = new PHP_Depend_Code_Package('B');
        $packageB->addType(new PHP_Depend_Code_Interface('B1'));
        $packageB->addType(new PHP_Depend_Code_Interface('B2'));
        $packageC = new PHP_Depend_Code_Package('C');
        $packageC->addType(new PHP_Depend_Code_Interface('C1'));
        
        $packages = array($packageA, $packageB, $packageC);
        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze(new PHP_Depend_Code_NodeIterator($packages));
        
        $project = $analyzer->getProjectMetrics();
        
        $this->assertArrayHasKey('noi', $project);
        $this->assertEquals(4, $project['noi']);
        
        $metrics = $analyzer->getNodeMetrics($packageA);
        $this->assertArrayHasKey('noi', $metrics);
        $this->assertEquals(1, $metrics['noi']);
        
        $metrics = $analyzer->getNodeMetrics($packageB);
        $this->assertArrayHasKey('noi', $metrics);
        $this->assertEquals(2, $metrics['noi']);
        
        $metrics = $analyzer->getNodeMetrics($packageC);
        $this->assertArrayHasKey('noi', $metrics);
        $this->assertEquals(1, $metrics['noi']);
    }
    
    /**
     * Tests that the analyzer calculates the correct number of methods values.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectNumberOfMethods()
    {
        $packageA = new PHP_Depend_Code_Package('A');
        
        $classA1 = $packageA->addType(new PHP_Depend_Code_Class('A1'));
        $classA1->addMethod(new PHP_Depend_Code_Method('a1a'));
        $classA1->addMethod(new PHP_Depend_Code_Method('a1b'));
        
        $classA2 = $packageA->addType(new PHP_Depend_Code_Class('A2'));
        $classA2->addMethod(new PHP_Depend_Code_Method('a2a'));
        
        $interfsA3 = $packageA->addType(new PHP_Depend_Code_Interface('A3'));
        $interfsA3->addMethod(new PHP_Depend_Code_Method('a3a'));
        
        $packageB = new PHP_Depend_Code_Package('B');
        $interfsB1 = $packageB->addType(new PHP_Depend_Code_Interface('B1'));
        $interfsB1->addMethod(new PHP_Depend_Code_Method('b1a'));
        $interfsB1->addMethod(new PHP_Depend_Code_Method('b1b'));
        
        $interfsB2 = $packageB->addType(new PHP_Depend_Code_Interface('B2'));
        $interfsB2->addMethod(new PHP_Depend_Code_Method('b2a'));
        
        $packageC  = new PHP_Depend_Code_Package('C');
        $interfsC1 = $packageC->addType(new PHP_Depend_Code_Interface('C1'));
        $interfsC1->addMethod(new PHP_Depend_Code_Method('c1a'));
        $interfsC1->addMethod(new PHP_Depend_Code_Method('c1b'));
        
        $packages = array($packageA, $packageB, $packageC);
        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze(new PHP_Depend_Code_NodeIterator($packages));
        
        $project = $analyzer->getProjectMetrics();
        
        $this->assertArrayHasKey('nom', $project);
        $this->assertEquals(9, $project['nom']);
        
        $metrics = $analyzer->getNodeMetrics($packageA);
        $this->assertArrayHasKey('nom', $metrics);
        $this->assertEquals(4, $metrics['nom']);
        
        $metrics = $analyzer->getNodeMetrics($classA1);
        $this->assertArrayHasKey('nom', $metrics);
        $this->assertEquals(2, $metrics['nom']);
        
        $metrics = $analyzer->getNodeMetrics($classA2);
        $this->assertArrayHasKey('nom', $metrics);
        $this->assertEquals(1, $metrics['nom']);
        
        $metrics = $analyzer->getNodeMetrics($interfsA3);
        $this->assertArrayHasKey('nom', $metrics);
        $this->assertEquals(1, $metrics['nom']);
        
        $metrics = $analyzer->getNodeMetrics($packageB);
        $this->assertArrayHasKey('nom', $metrics);
        $this->assertEquals(3, $metrics['nom']);
        
        $metrics = $analyzer->getNodeMetrics($interfsB1);
        $this->assertArrayHasKey('nom', $metrics);
        $this->assertEquals(2, $metrics['nom']);
        
        $metrics = $analyzer->getNodeMetrics($interfsB2);
        $this->assertArrayHasKey('nom', $metrics);
        $this->assertEquals(1, $metrics['nom']);
        
        $metrics = $analyzer->getNodeMetrics($packageC);
        $this->assertArrayHasKey('nom', $metrics);
        $this->assertEquals(2, $metrics['nom']);
        
        $metrics = $analyzer->getNodeMetrics($interfsC1);
        $this->assertArrayHasKey('nom', $metrics);
        $this->assertEquals(2, $metrics['nom']);        
    }
    
    /**
     * Tests that the analyzer calculates the correct number of functions values.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectNumberOfFunctions()
    {
        $packageA = new PHP_Depend_Code_Package('A');
        $packageA->addFunction(new PHP_Depend_Code_Function('a1'));
        $packageA->addFunction(new PHP_Depend_Code_Function('a2'));
        $packageA->addFunction(new PHP_Depend_Code_Function('a3'));
        $packageB = new PHP_Depend_Code_Package('B');
        $packageB->addFunction(new PHP_Depend_Code_Function('b1'));
        $packageB->addFunction(new PHP_Depend_Code_Function('b2'));
        $packageC = new PHP_Depend_Code_Package('C');
        $packageC->addFunction(new PHP_Depend_Code_Function('c1'));
        
        $packages = array($packageA, $packageB, $packageC);
        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze(new PHP_Depend_Code_NodeIterator($packages));
        
        $project = $analyzer->getProjectMetrics();
        
        $this->assertArrayHasKey('nof', $project);
        $this->assertEquals(6, $project['nof']);
        
        $metrics = $analyzer->getNodeMetrics($packageA);
        $this->assertArrayHasKey('nof', $metrics);
        $this->assertEquals(3, $metrics['nof']);
        
        $metrics = $analyzer->getNodeMetrics($packageB);
        $this->assertArrayHasKey('nof', $metrics);
        $this->assertEquals(2, $metrics['nof']);
        
        $metrics = $analyzer->getNodeMetrics($packageC);
        $this->assertArrayHasKey('nof', $metrics);
        $this->assertEquals(1, $metrics['nof']);
    }
}