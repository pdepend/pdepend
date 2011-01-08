<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

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
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Metrics_NodeCount_AnalyzerTest extends PHP_Depend_Metrics_AbstractTest
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
     * @covers PHP_Depend_Metrics_NodeCount_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodecount
     * @group unittest
     */
    public function testCalculatesExpectedNumberOfPackages()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze($packages);
        
        $metrics = $analyzer->getProjectMetrics();
        $this->assertEquals(3, $metrics['nop']);
    }
    
    /**
     * testCalculatesExpectedNumberOfClassesInProject
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeCount_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodecount
     * @group unittest
     */
    public function testCalculatesExpectedNumberOfClassesInProject()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze($packages);
        
        $metrics = $analyzer->getProjectMetrics();
        $this->assertEquals(6, $metrics['noc']);
    }

    /**
     * testCalculatesExpectedNumberOfClassesInPackages
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeCount_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodecount
     * @group unittest
     */
    public function testCalculatesExpectedNumberOfClassesInPackages()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze($packages);

        $metrics = array();
        foreach ($packages as $package) {
            $metrics[$package->getName()] = $analyzer->getNodeMetrics($package);
        }

        $this->assertEquals(
            array(
                'A' => array('noc' => 3, 'noi' => 0, 'nom' => 0, 'nof' => 0),
                'B' => array('noc' => 2, 'noi' => 0, 'nom' => 0, 'nof' => 0),
                'C' => array('noc' => 1, 'noi' => 0, 'nom' => 0, 'nof' => 0),
            ),
            $metrics
        );
    }
    
    /**
     * testCalculatesExpectedNumberOfInterfacesInProject
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeCount_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodecount
     * @group unittest
     */
    public function testCalculatesExpectedNumberOfInterfacesInProject()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze($packages);
        
        $metrics = $analyzer->getProjectMetrics();
        $this->assertEquals(9, $metrics['noi']);
    }

    /**
     * testCalculatesExpectedNumberOfInterfacesInPackages
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeCount_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodecount
     * @group unittest
     */
    public function testCalculatesExpectedNumberOfInterfacesInPackages()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze($packages);

        $metrics = array();
        foreach ($packages as $package) {
            $metrics[$package->getName()] = $analyzer->getNodeMetrics($package);
        }

        $this->assertEquals(
            array(
                'A' => array('noc' => 0, 'noi' => 1, 'nom' => 0, 'nof' => 0),
                'B' => array('noc' => 0, 'noi' => 2, 'nom' => 0, 'nof' => 0),
                'C' => array('noc' => 0, 'noi' => 3, 'nom' => 0, 'nof' => 0),
            ),
            $metrics
        );
    }
    
    /**
     * testCalculatesExpectedNumberOfMethodsInProject
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeCount_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodecount
     * @group unittest
     */
    public function testCalculatesExpectedNumberOfMethodsInProject()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze($packages);
        
        $metrics = $analyzer->getProjectMetrics();
        $this->assertEquals(9, $metrics['nom']);
    }

    /**
     * testCalculatesExpectedNumberOfMethodsInPackages
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeCount_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodecount
     * @group unittest
     */
    public function testCalculatesExpectedNumberOfMethodsInPackages()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze($packages);

        $metrics = array();
        foreach ($packages as $package) {
            $metrics[$package->getName()] = $analyzer->getNodeMetrics($package);
        }

        $this->assertEquals(
            array(
                'A' => array('noc' => 2, 'noi' => 1, 'nom' => 4, 'nof' => 0),
                'B' => array('noc' => 0, 'noi' => 2, 'nom' => 3, 'nof' => 0),
                'C' => array('noc' => 0, 'noi' => 1, 'nom' => 2, 'nof' => 0),
            ),
            $metrics
        );
    }

    /**
     * testCalculatesExpectedNumberOfFunctionsInProject
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeCount_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodecount
     * @group unittest
     */
    public function testCalculatesExpectedNumberOfFunctionsInProject()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getProjectMetrics();
        $this->assertEquals(6, $metrics['nof']);
    }

    /**
     * testCalculatesExpectedNumberOfFunctionsInPackages
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeCount_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodecount
     * @group unittest
     */
    public function testCalculatesExpectedNumberOfFunctionsInPackages()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze($packages);

        $metrics = array();
        foreach ($packages as $package) {
            $metrics[$package->getName()] = $analyzer->getNodeMetrics($package);
        }

        $this->assertEquals(
            array(
                'A' => array('noc' => 0, 'noi' => 0, 'nom' => 0, 'nof' => 3),
                'B' => array('noc' => 0, 'noi' => 0, 'nom' => 0, 'nof' => 2),
                'C' => array('noc' => 0, 'noi' => 0, 'nom' => 0, 'nof' => 1),
            ),
            $metrics
        );
    }
}