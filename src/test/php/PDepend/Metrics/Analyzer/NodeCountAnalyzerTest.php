<?php

/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2017 Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Metrics\Analyzer;

use PDepend\Metrics\AbstractMetricsTestCase;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTNamespace;

/**
 * Test case for the node count analyzer.
 *
 * @covers \PDepend\Metrics\Analyzer\NodeCountAnalyzer
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @group unittest
 */
class NodeCountAnalyzerTest extends AbstractMetricsTestCase
{
    /**
     * testVisitClassIgnoresClassesThatAreNotUserDefined
     */
    public function testVisitClassIgnoresClassesThatAreNotUserDefined(): void
    {
        $notUserDefined = $this->createClassFixture();

        $namespace = new ASTNamespace('PDepend');
        $namespace->addType($notUserDefined);

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze(new ASTArtifactList([$namespace]));

        $metrics = $analyzer->getNodeMetrics($namespace);
        static::assertEquals(0, $metrics['noc']);
    }

    /**
     * testVisitClassCountsClassesThatAreNotUserDefined
     */
    public function testVisitClassCountsClassesThatAreNotUserDefined(): void
    {
        $userDefined = $this->createClassFixture();
        $userDefined->setUserDefined();

        $namespace = new ASTNamespace('PDepend');
        $namespace->addType($userDefined);

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze(new ASTArtifactList([$namespace]));

        $metrics = $analyzer->getNodeMetrics($namespace);
        static::assertEquals(1, $metrics['noc']);
    }

    /**
     * testVisitClassIgnoresInterfacesThatAreNotUserDefined
     */
    public function testVisitClassIgnoresInterfacesThatAreNotUserDefined(): void
    {
        $notUserDefined = $this->createInterfaceFixture();

        $namespace = new ASTNamespace('PDepend');
        $namespace->addType($notUserDefined);

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze(new ASTArtifactList([$namespace]));

        $metrics = $analyzer->getNodeMetrics($namespace);
        static::assertEquals(0, $metrics['noi']);
    }

    /**
     * testVisitClassCountsInterfacesThatAreNotUserDefined
     */
    public function testVisitClassCountsInterfacesThatAreNotUserDefined(): void
    {
        $userDefined = $this->createInterfaceFixture();
        $userDefined->setUserDefined();

        $namespace = new ASTNamespace('PDepend');
        $namespace->addType($userDefined);

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze(new ASTArtifactList([$namespace]));

        $metrics = $analyzer->getNodeMetrics($namespace);
        static::assertEquals(1, $metrics['noi']);
    }

    /**
     * Tests that the analyzer calculates the correct number of packages value.
     */
    public function testCalculatesExpectedNumberOfPackages(): void
    {
        $namespaces = $this->parseTestCaseSource(__METHOD__);
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = $analyzer->getProjectMetrics();
        static::assertEquals(3, $metrics['nop']);
    }

    /**
     * testCalculatesExpectedNumberOfClassesInProject
     */
    public function testCalculatesExpectedNumberOfClassesInProject(): void
    {
        $namespaces = $this->parseTestCaseSource(__METHOD__);
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = $analyzer->getProjectMetrics();
        static::assertEquals(6, $metrics['noc']);
    }

    /**
     * testCalculatesExpectedNumberOfClassesInPackages
     */
    public function testCalculatesExpectedNumberOfClassesInPackages(): void
    {
        $namespaces = $this->parseTestCaseSource(__METHOD__);
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = [];
        foreach ($namespaces as $namespace) {
            $metrics[$namespace->getImage()] = $analyzer->getNodeMetrics($namespace);
        }

        static::assertEquals(
            [
                'A' => ['noc' => 3, 'noi' => 0, 'nom' => 0, 'nof' => 0],
                'B' => ['noc' => 2, 'noi' => 0, 'nom' => 0, 'nof' => 0],
                'C' => ['noc' => 1, 'noi' => 0, 'nom' => 0, 'nof' => 0],
            ],
            $metrics
        );
    }

    /**
     * testCalculatesExpectedNumberOfInterfacesInProject
     */
    public function testCalculatesExpectedNumberOfInterfacesInProject(): void
    {
        $namespaces = $this->parseTestCaseSource(__METHOD__);
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = $analyzer->getProjectMetrics();
        static::assertEquals(9, $metrics['noi']);
    }

    /**
     * testCalculatesExpectedNumberOfInterfacesInPackages
     */
    public function testCalculatesExpectedNumberOfInterfacesInPackages(): void
    {
        $namespaces = $this->parseTestCaseSource(__METHOD__);
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = [];
        foreach ($namespaces as $namespace) {
            $metrics[$namespace->getImage()] = $analyzer->getNodeMetrics($namespace);
        }

        static::assertEquals(
            [
                'A' => ['noc' => 0, 'noi' => 1, 'nom' => 0, 'nof' => 0],
                'B' => ['noc' => 0, 'noi' => 2, 'nom' => 0, 'nof' => 0],
                'C' => ['noc' => 0, 'noi' => 3, 'nom' => 0, 'nof' => 0],
            ],
            $metrics
        );
    }

    /**
     * testCalculatesExpectedNumberOfMethodsInProject
     */
    public function testCalculatesExpectedNumberOfMethodsInProject(): void
    {
        $namespaces = $this->parseTestCaseSource(__METHOD__);
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = $analyzer->getProjectMetrics();
        static::assertEquals(9, $metrics['nom']);
    }

    /**
     * testCalculatesExpectedNumberOfMethodsInPackages
     */
    public function testCalculatesExpectedNumberOfMethodsInPackages(): void
    {
        $namespaces = $this->parseTestCaseSource(__METHOD__);
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = [];
        foreach ($namespaces as $namespace) {
            $metrics[$namespace->getImage()] = $analyzer->getNodeMetrics($namespace);
        }

        static::assertEquals(
            [
                'A' => ['noc' => 2, 'noi' => 1, 'nom' => 4, 'nof' => 0],
                'B' => ['noc' => 0, 'noi' => 2, 'nom' => 3, 'nof' => 0],
                'C' => ['noc' => 0, 'noi' => 1, 'nom' => 2, 'nof' => 0],
            ],
            $metrics
        );
    }

    /**
     * testCalculatesExpectedNumberOfFunctionsInProject
     */
    public function testCalculatesExpectedNumberOfFunctionsInProject(): void
    {
        $namespaces = $this->parseTestCaseSource(__METHOD__);
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = $analyzer->getProjectMetrics();
        static::assertEquals(6, $metrics['nof']);
    }

    /**
     * testCalculatesExpectedNumberOfFunctionsInPackages
     */
    public function testCalculatesExpectedNumberOfFunctionsInPackages(): void
    {
        $namespaces = $this->parseTestCaseSource(__METHOD__);
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = [];
        foreach ($namespaces as $namespace) {
            $metrics[$namespace->getImage()] = $analyzer->getNodeMetrics($namespace);
        }

        static::assertEquals(
            [
                'A' => ['noc' => 0, 'noi' => 0, 'nom' => 0, 'nof' => 3],
                'B' => ['noc' => 0, 'noi' => 0, 'nom' => 0, 'nof' => 2],
                'C' => ['noc' => 0, 'noi' => 0, 'nom' => 0, 'nof' => 1],
            ],
            $metrics
        );
    }

    private function createAnalyzer(): NodeCountAnalyzer
    {
        return new NodeCountAnalyzer();
    }
}
