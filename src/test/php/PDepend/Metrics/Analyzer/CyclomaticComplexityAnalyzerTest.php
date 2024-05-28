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
use PDepend\Source\AST\ASTArtifact;
use PDepend\Util\Cache\CacheDriver;
use PDepend\Util\Cache\Driver\MemoryCacheDriver;

/**
 * Test case for the cyclomatic analyzer.
 *
 * @covers \PDepend\Metrics\AbstractCachingAnalyzer
 * @covers \PDepend\Metrics\Analyzer\CyclomaticComplexityAnalyzer
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @group unittest
 */
class CyclomaticComplexityAnalyzerTest extends AbstractMetricsTestCase
{
    /** @since 1.0.0 */
    private CacheDriver $cache;

    /**
     * Initializes a in memory cache.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->cache = new MemoryCacheDriver();
    }

    /**
     * testGetCCNReturnsZeroForUnknownNode
     */
    public function testGetCCNReturnsZeroForUnknownNode(): void
    {
        $analyzer = $this->createAnalyzer();
        $astArtifact = $this->getMockBuilder(ASTArtifact::class)
            ->getMock();
        static::assertEquals(0, $analyzer->getCcn($astArtifact));
    }

    /**
     * testGetCCN2ReturnsZeroForUnknownNode
     */
    public function testGetCCN2ReturnsZeroForUnknownNode(): void
    {
        $analyzer = $this->createAnalyzer();
        $astArtifact = $this->getMockBuilder(ASTArtifact::class)
            ->getMock();
        static::assertEquals(0, $analyzer->getCcn2($astArtifact));
    }

    /**
     * Tests that the analyzer calculates the correct function cc numbers.
     */
    public function testCalculateFunctionCCNAndCNN2(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $actual = [];
        $expected = [
            'pdepend1' => ['ccn' => 5, 'ccn2' => 6],
            'pdepend2' => ['ccn' => 7, 'ccn2' => 10],
        ];

        foreach ($namespaces[0]->getFunctions() as $function) {
            $actual[$function->getImage()] = $analyzer->getNodeMetrics($function);
        }

        ksort($expected);
        ksort($actual);

        static::assertEquals($expected, $actual);
    }

    /**
     * testCalculateFunctionCCNAndCNN2ProjectMetrics
     */
    public function testCalculateFunctionCCNAndCNN2ProjectMetrics(): void
    {
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($this->parseTestCaseSource(__METHOD__));

        $expected = ['ccn' => 12, 'ccn2' => 16];
        $actual = $analyzer->getProjectMetrics();

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates the correct method cc numbers.
     */
    public function testCalculateMethodCCNAndCNN2(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $classes = $namespaces[0]->getClasses();
        $methods = $classes[0]->getMethods();

        $actual = [];
        $expected = [
            'pdepend1' => ['ccn' => 5, 'ccn2' => 6],
            'pdepend2' => ['ccn' => 7, 'ccn2' => 10],
        ];

        foreach ($methods as $method) {
            $actual[$method->getImage()] = $analyzer->getNodeMetrics($method);
        }

        ksort($expected);
        ksort($actual);

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer also detects a conditional expression nested in a
     * compound expression.
     */
    public function testCalculateCCNWithConditionalExprInCompoundExpr(): void
    {
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($this->parseTestCaseSource(__METHOD__));

        $expected = ['ccn' => 2, 'ccn2' => 2];
        $actual = $analyzer->getProjectMetrics();

        static::assertEquals($expected, $actual);
    }

    /**
     * testCalculateExpectedCCNForDoWhileStatement
     */
    public function testCalculateExpectedCCNForDoWhileStatement(): void
    {
        $namespaces = $this->parseCodeResourceForTest();
        $functions = $namespaces[0]->getFunctions();

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        static::assertEquals(3, $analyzer->getCcn($functions[0]));
    }

    /**
     * testCalculateExpectedCCN2ForDoWhileStatement
     */
    public function testCalculateExpectedCCN2ForDoWhileStatement(): void
    {
        $namespaces = $this->parseCodeResourceForTest();
        $functions = $namespaces[0]->getFunctions();

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        static::assertEquals(3, $analyzer->getCcn2($functions[0]));
    }

    /**
     * Tests that the analyzer ignores the default label in a switch statement.
     */
    public function testCalculateCCNIgnoresDefaultLabelInSwitchStatement(): void
    {
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($this->parseTestCaseSource(__METHOD__));

        $expected = ['ccn' => 3, 'ccn2' => 3];
        $actual = $analyzer->getProjectMetrics();

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer counts all case labels in a switch statement.
     */
    public function testCalculateCCNCountsAllCaseLabelsInSwitchStatement(): void
    {
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($this->parseTestCaseSource(__METHOD__));

        $expected = ['ccn' => 4, 'ccn2' => 4];
        $actual = $analyzer->getProjectMetrics();

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer detects expressions in a for loop.
     */
    public function testCalculateCCNDetectsExpressionsInAForLoop(): void
    {
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($this->parseTestCaseSource(__METHOD__));

        $expected = ['ccn' => 2, 'ccn2' => 4];
        $actual = $analyzer->getProjectMetrics();

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer detects expressions in a while loop.
     */
    public function testCalculateCCNDetectsExpressionsInAWhileLoop(): void
    {
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($this->parseTestCaseSource(__METHOD__));

        $expected = ['ccn' => 2, 'ccn2' => 4];
        $actual = $analyzer->getProjectMetrics();

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer aggregates the correct project metrics.
     */
    public function testCalculateProjectMetrics(): void
    {
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($this->parseTestCaseSource(__METHOD__));

        $expected = ['ccn' => 24, 'ccn2' => 32];
        $actual = $analyzer->getProjectMetrics();

        static::assertEquals($expected, $actual);
    }

    /**
     * testAnalyzerAlsoCalculatesCCNAndCCN2OfClosureInMethod
     */
    public function testAnalyzerAlsoCalculatesCCNAndCCN2OfClosureInMethod(): void
    {
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($this->parseTestCaseSource(__METHOD__));

        $expected = ['ccn' => 3, 'ccn2' => 3];
        $actual = $analyzer->getProjectMetrics();

        static::assertEquals($expected, $actual);
    }

    /**
     * testAnalyzerRestoresExpectedFunctionMetricsFromCache
     *
     * @since 1.0.0
     */
    public function testAnalyzerRestoresExpectedFunctionMetricsFromCache(): void
    {
        $namespaces = $this->parseCodeResourceForTest();
        $functions = $namespaces[0]->getFunctions();

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics0 = $analyzer->getNodeMetrics($functions[0]);

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics1 = $analyzer->getNodeMetrics($functions[0]);

        static::assertEquals($metrics0, $metrics1);
    }

    /**
     * testAnalyzerRestoresExpectedMethodMetricsFromCache
     *
     * @since 1.0.0
     */
    public function testAnalyzerRestoresExpectedMethodMetricsFromCache(): void
    {
        $namespaces = $this->parseCodeResourceForTest();
        $classes = $namespaces[0]->getClasses();
        $methods = $classes[0]->getMethods();

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics0 = $analyzer->getNodeMetrics($methods[0]);

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics1 = $analyzer->getNodeMetrics($methods[0]);

        static::assertEquals($metrics0, $metrics1);
    }

    /**
     * Returns a pre configured ccn analyzer.
     *
     * @since 1.0.0
     */
    private function createAnalyzer(): CyclomaticComplexityAnalyzer
    {
        $analyzer = new CyclomaticComplexityAnalyzer();
        $analyzer->setCache($this->cache);

        return $analyzer;
    }
}
