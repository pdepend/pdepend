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
use PDepend\Source\AST\ASTClass;

/**
 * Test case for the hierarchy analyzer.
 *
 * @covers \PDepend\Metrics\Analyzer\HierarchyAnalyzer
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @group unittest
 */
class HierarchyAnalyzerTest extends AbstractMetricsTestCase
{
    /**
     * testCalculatesExpectedNumberOfLeafClasses
     */
    public function testCalculatesExpectedNumberOfLeafClasses(): void
    {
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($this->parseTestCaseSource(__METHOD__));

        $metrics = $analyzer->getProjectMetrics();
        static::assertEquals(2, $metrics['leafs']);
    }

    /**
     * testCalculatesExpectedNumberOfAbstractClasses
     */
    public function testCalculatesExpectedNumberOfAbstractClasses(): void
    {
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($this->parseTestCaseSource(__METHOD__));

        $metrics = $analyzer->getProjectMetrics();
        static::assertEquals(1, $metrics['clsa']);
    }

    /**
     * testCalculatesExpectedNumberOfConcreteClasses
     */
    public function testCalculatesExpectedNumberOfConcreteClasses(): void
    {
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($this->parseTestCaseSource(__METHOD__));

        $metrics = $analyzer->getProjectMetrics();
        static::assertEquals(2, $metrics['clsc']);
    }

    /**
     * testCalculatesExpectedNumberOfRootClasses
     */
    public function testCalculatesExpectedNumberOfRootClasses(): void
    {
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($this->parseTestCaseSource(__METHOD__));

        $metrics = $analyzer->getProjectMetrics();
        static::assertEquals(1, $metrics['roots']);
    }

    /**
     * testCalculatedLeafsMetricDoesNotContainNotUserDefinedClasses
     */
    public function testCalculatedLeafsMetricDoesNotContainNotUserDefinedClasses(): void
    {
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($this->parseTestCaseSource(__METHOD__));

        $metrics = $analyzer->getProjectMetrics();
        static::assertEquals(0, $metrics['leafs']);
    }

    /**
     * testAnalyzerIgnoresClassesThatAreNotUserDefined
     */
    public function testAnalyzerIgnoresClassesThatAreNotUserDefined(): void
    {
        $class = new ASTClass(null);

        $analyzer = $this->createAnalyzer();
        $analyzer->visitClass($class);

        $metrics = $analyzer->getNodeMetrics($class);
        static::assertEquals([], $metrics);
    }

    /**
     * Tests that {@link \PDepend\Metrics\Analyzer\HierarchyAnalyzer::getNodeMetrics()}
     * returns an empty <b>array</b> for an unknown node id.
     */
    public function testGetNodeMetricsForUnknownId(): void
    {
        $class = new ASTClass('PDepend');
        $analyzer = $this->createAnalyzer();

        static::assertSame([], $analyzer->getNodeMetrics($class));
    }

    private function createAnalyzer(): HierarchyAnalyzer
    {
        return new HierarchyAnalyzer();
    }
}
