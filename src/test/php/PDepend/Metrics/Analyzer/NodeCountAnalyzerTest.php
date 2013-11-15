<?php
/**
 * This file is part of PDepend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Metrics\Analyzer;

use PDepend\Metrics\AbstractMetricsTest;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTNamespace;

/**
 * Test case for the node count analyzer.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @covers \PDepend\Metrics\Analyzer\NodeCountAnalyzer
 * @group unittest
 */
class NodeCountAnalyzerTest extends AbstractMetricsTest
{
    /**
     * testVisitClassIgnoresClassesThatAreNotUserDefined
     * 
     * @return void
     */
    public function testVisitClassIgnoresClassesThatAreNotUserDefined()
    {
        $notUserDefined = $this->createClassFixture();

        $namespace = new ASTNamespace('PDepend');
        $namespace->addType($notUserDefined);

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze(new ASTArtifactList(array($namespace)));

        $metrics = $analyzer->getNodeMetrics($namespace);
        $this->assertEquals(0, $metrics['noc']);
    }

    /**
     * testVisitClassCountsClassesThatAreNotUserDefined
     *
     * @return void
     */
    public function testVisitClassCountsClassesThatAreNotUserDefined()
    {
        $userDefined = $this->createClassFixture();
        $userDefined->setUserDefined();

        $namespace = new ASTNamespace('PDepend');
        $namespace->addType($userDefined);

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze(new ASTArtifactList(array($namespace)));

        $metrics = $analyzer->getNodeMetrics($namespace);
        $this->assertEquals(1, $metrics['noc']);
    }

    /**
     * testVisitClassIgnoresInterfacesThatAreNotUserDefined
     *
     * @return void
     */
    public function testVisitClassIgnoresInterfacesThatAreNotUserDefined()
    {
        $notUserDefined = $this->createInterfaceFixture();

        $namespace = new ASTNamespace('PDepend');
        $namespace->addType($notUserDefined);

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze(new ASTArtifactList(array($namespace)));

        $metrics = $analyzer->getNodeMetrics($namespace);
        $this->assertEquals(0, $metrics['noi']);
    }

    /**
     * testVisitClassCountsInterfacesThatAreNotUserDefined
     *
     * @return void
     */
    public function testVisitClassCountsInterfacesThatAreNotUserDefined()
    {
        $userDefined = $this->createInterfaceFixture();
        $userDefined->setUserDefined();

        $namespace = new ASTNamespace('PDepend');
        $namespace->addType($userDefined);

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze(new ASTArtifactList(array($namespace)));

        $metrics = $analyzer->getNodeMetrics($namespace);
        $this->assertEquals(1, $metrics['noi']);
    }

    /**
     * Tests that the analyzer calculates the correct number of packages value.
     *
     * @return void
     */
    public function testCalculatesExpectedNumberOfPackages()
    {
        $namespaces = self::parseTestCaseSource(__METHOD__);
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);
        
        $metrics = $analyzer->getProjectMetrics();
        $this->assertEquals(3, $metrics['nop']);
    }
    
    /**
     * testCalculatesExpectedNumberOfClassesInProject
     *
     * @return void
     */
    public function testCalculatesExpectedNumberOfClassesInProject()
    {
        $namespaces = self::parseTestCaseSource(__METHOD__);
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);
        
        $metrics = $analyzer->getProjectMetrics();
        $this->assertEquals(6, $metrics['noc']);
    }

    /**
     * testCalculatesExpectedNumberOfClassesInPackages
     *
     * @return void
     */
    public function testCalculatesExpectedNumberOfClassesInPackages()
    {
        $namespaces = self::parseTestCaseSource(__METHOD__);
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = array();
        foreach ($namespaces as $namespace) {
            $metrics[$namespace->getName()] = $analyzer->getNodeMetrics($namespace);
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
     */
    public function testCalculatesExpectedNumberOfInterfacesInProject()
    {
        $namespaces = self::parseTestCaseSource(__METHOD__);
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);
        
        $metrics = $analyzer->getProjectMetrics();
        $this->assertEquals(9, $metrics['noi']);
    }

    /**
     * testCalculatesExpectedNumberOfInterfacesInPackages
     *
     * @return void
     */
    public function testCalculatesExpectedNumberOfInterfacesInPackages()
    {
        $namespaces = self::parseTestCaseSource(__METHOD__);
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = array();
        foreach ($namespaces as $namespace) {
            $metrics[$namespace->getName()] = $analyzer->getNodeMetrics($namespace);
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
     */
    public function testCalculatesExpectedNumberOfMethodsInProject()
    {
        $namespaces = self::parseTestCaseSource(__METHOD__);
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);
        
        $metrics = $analyzer->getProjectMetrics();
        $this->assertEquals(9, $metrics['nom']);
    }

    /**
     * testCalculatesExpectedNumberOfMethodsInPackages
     *
     * @return void
     */
    public function testCalculatesExpectedNumberOfMethodsInPackages()
    {
        $namespaces = self::parseTestCaseSource(__METHOD__);
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = array();
        foreach ($namespaces as $namespace) {
            $metrics[$namespace->getName()] = $analyzer->getNodeMetrics($namespace);
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
     */
    public function testCalculatesExpectedNumberOfFunctionsInProject()
    {
        $namespaces = self::parseTestCaseSource(__METHOD__);
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = $analyzer->getProjectMetrics();
        $this->assertEquals(6, $metrics['nof']);
    }

    /**
     * testCalculatesExpectedNumberOfFunctionsInPackages
     *
     * @return void
     */
    public function testCalculatesExpectedNumberOfFunctionsInPackages()
    {
        $namespaces = self::parseTestCaseSource(__METHOD__);
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = array();
        foreach ($namespaces as $namespace) {
            $metrics[$namespace->getName()] = $analyzer->getNodeMetrics($namespace);
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

    /**
     * @return \PDepend\Metrics\Analyzer\NodeCountAnalyzer
     */
    private function createAnalyzer()
    {
        return new NodeCountAnalyzer();
    }
}
