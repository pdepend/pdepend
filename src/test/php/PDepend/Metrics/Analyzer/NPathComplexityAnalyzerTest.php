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
use PDepend\Source\AST\AbstractASTCallable;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTMethod;
use PDepend\Util\Cache\CacheDriver;
use PDepend\Util\Cache\Driver\MemoryCacheDriver;

/**
 * Test case for the NPath complexity analyzer.
 *
 * @covers \PDepend\Metrics\AbstractCachingAnalyzer
 * @covers \PDepend\Metrics\Analyzer\NPathComplexityAnalyzer
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @group unittest
 */
class NPathComplexityAnalyzerTest extends AbstractMetricsTestCase
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
     * testAnalyzerRestoresExpectedFunctionMetricsFromCache
     *
     * @since 1.0.0
     */
    public function testAnalyzerRestoresExpectedFunctionMetricsFromCache(): void
    {
        $namespaces = $this->parseCodeResourceForTest();
        $function = $namespaces->current()
            ->getFunctions()
            ->current();

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics0 = $analyzer->getNodeMetrics($function);

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics1 = $analyzer->getNodeMetrics($function);

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
        $method = $namespaces->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics0 = $analyzer->getNodeMetrics($method);

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics1 = $analyzer->getNodeMetrics($method);

        static::assertEquals($metrics0, $metrics1);
    }

    /**
     * testNPathComplexityForNestedIfStatementsWithScope
     *
     * @since 0.9.12
     */
    public function testNPathComplexityForNestedIfStatementsWithScope(): void
    {
        static::assertEquals(4, $this->calculateMethodMetric());
    }

    /**
     * testNPathComplexityForNestedIfStatementsWithoutScope
     *
     * @since 0.9.12
     */
    public function testNPathComplexityForNestedIfStatementsWithoutScope(): void
    {
        static::assertEquals(4, $this->calculateMethodMetric());
    }

    /**
     * testNPathComplexityForSiblingConditionalExpressions
     *
     * @since 0.9.12
     */
    public function testNPathComplexityForSiblingConditionalExpressions(): void
    {
        static::assertEquals(4, $this->calculateFunctionMetric());
    }

    /**
     * testNPathComplexityForSiblingExpressions
     *
     * @since 0.9.12
     * @todo What happens with boolean/logical expressions within the body of
     *       any other statement/expression?
     */
    public function testNPathComplexityForSiblingExpressions(): void
    {
        static::assertEquals(6, $this->calculateFunctionMetric());
    }

    /**
     * testNPathComplexityForTwoSiblingIfStatetements
     *
     * @since 0.9.12
     */
    public function testNPathComplexityForTwoSiblingIfStatetements(): void
    {
        static::assertEquals(4, $this->calculateFunctionMetric());
    }

    /**
     * testNPathComplexityForForeachStatementWithNestedIfStatetements
     *
     * @since 0.9.12
     */
    public function testNPathComplexityForForeachStatementWithNestedIfStatetements(): void
    {
        static::assertEquals(3, $this->calculateFunctionMetric());
    }

    /**
     * testNPathComplexityForSiblingIfStatementsAndForeachStatement
     *
     * @since 0.9.12
     */
    public function testNPathComplexityForSiblingIfStatementsAndForeachStatement(): void
    {
        static::assertEquals(12, $this->calculateFunctionMetric());
    }

    /**
     * testNPathComplexityForComplexFunction
     *
     * @since 0.9.12
     */
    public function testNPathComplexityForComplexFunction(): void
    {
        static::assertEquals(24, $this->calculateFunctionMetric());
    }

    /**
     * testNPathComplexityForConditionalsInArrayDeclaration
     *
     * @since 0.9.12
     */
    public function testNPathComplexityForComplexNestedControlStatements(): void
    {
        static::assertEquals(63, $this->calculateFunctionMetric());
    }

    /**
     * testNPathComplexityForConditionalsInArrayDeclaration
     *
     * @since 0.9.12
     */
    public function testNPathComplexityForConditionalsInArrayDeclaration(): void
    {
        static::assertEquals(16, $this->calculateFunctionMetric());
    }

    /**
     * testNPathComplexityIsZeroForEmptyMethod
     */
    public function testNPathComplexityIsZeroForEmptyMethod(): void
    {
        static::assertEquals(1, $this->calculateMethodMetric());
    }

    /**
     * Tests a method body with a simple if statement.
     */
    public function testNPathComplexityForMethodWithSimpleIfStatement(): void
    {
        static::assertEquals(2, $this->calculateMethodMetric());
    }

    /**
     * Tests a method body with a simple if statement with dynamic identifier.
     */
    public function testNPathComplexityForIfStatementWithNestedDynamicIdentifier(): void
    {
        static::assertEquals(2, $this->calculateMethodMetric());
    }

    /**
     * Tests the analyzer implementation against consecutive if-statements.
     */
    public function testNPathComplexityForConsecutiveIfStatements(): void
    {
        static::assertEquals(80, $this->calculateMethodMetric());
    }

    /**
     * Tests the analyzer implementation against multiple if-else-if statements.
     */
    public function testNPathComplexityForConsecutiveIfElseIfStatements(): void
    {
        static::assertEquals(4, $this->calculateMethodMetric());
    }

    /**
     * Tests the analyzer implementation against multiple if-elseif statements.
     */
    public function testNPathComplexityForConsecutiveIfElsifStatements(): void
    {
        static::assertEquals(4, $this->calculateMethodMetric());
    }

    /**
     * Tests the analyzer implementation against an empty while statement.
     */
    public function testNPathComplexityForEmptyWhileStatement(): void
    {
        static::assertEquals(3, $this->calculateMethodMetric());
    }

    /**
     * Tests the anaylzer with nested while statements.
     */
    public function testNPathComplexityForNestedWhileStatements(): void
    {
        static::assertEquals(5, $this->calculateMethodMetric());
    }

    /**
     * Tests the npath algorithm with a simple do-while statement.
     */
    public function testNPathComplexityForSimpleDoWhileStatement(): void
    {
        static::assertEquals(3, $this->calculateMethodMetric());
    }

    /**
     * Tests the analyzer with a simple for statement.
     */
    public function testNPathComplexityForSimpleForStatement(): void
    {
        static::assertEquals(2, $this->calculateMethodMetric());
    }

    /**
     * Tests the analyzer with a complex for statement.
     */
    public function testNPathComplexityForComplexForStatement(): void
    {
        static::assertEquals(4, $this->calculateMethodMetric());
    }

    /**
     * Tests the analyzer implementation with a simple foreach statement.
     */
    public function testNPathComplexityForSimpleForeachStatement(): void
    {
        static::assertEquals(2, $this->calculateMethodMetric());
    }

    /**
     * Tests the algorithm with a simple return statement.
     */
    public function testNPathComplexityForSimpleReturnStatement(): void
    {
        static::assertEquals(1, $this->calculateMethodMetric());
    }

    /**
     * Tests the algorithm with a return statement that contains boolean expressions.
     */
    public function testNPathComplexityForReturnStatementWithBooleanExpressions(): void
    {
        static::assertEquals(2, $this->calculateMethodMetric());
    }

    /**
     * Tests the algorithm with a return statement that contains a conditional.
     */
    public function testNPathComplexityForReturnStatementWithConditionalStatement(): void
    {
        static::assertEquals(2, $this->calculateMethodMetric());
    }

    /**
     * Tests the algorithm with a simple switch statement that contains one case
     * child.
     */
    public function testNPathComplexityForSimpleSwitchStatement(): void
    {
        static::assertEquals(1, $this->calculateMethodMetric());
    }

    /**
     * Tests the algorithm with a switch statement that contains multiple case
     * statements.
     */
    public function testNPathComplexityForSwitchStatementWithMultipleCaseStatements(): void
    {
        static::assertEquals(5, $this->calculateMethodMetric());
    }

    /**
     * Tests the algorithm with a switch statement that contains complex case
     * statements.
     */
    public function testNPathComplexityForSwitchStatementWithComplexCaseStatements(): void
    {
        static::assertEquals(8, $this->calculateMethodMetric());
    }

    /**
     * Tests the algorithm with a simple try statement.
     */
    public function testNPathComplexityForSimpleTryCatchStatement(): void
    {
        static::assertEquals(2, $this->calculateMethodMetric());
    }

    /**
     * Tests the algorithm with a try statement with multiple catch statements.
     */
    public function testNPathComplexityForTryStatementWithMutlipleCatchStatements(): void
    {
        static::assertEquals(5, $this->calculateMethodMetric());
    }

    /**
     * Tests the algorithm with a try statement with nested if statements.
     */
    public function testNPathComplexityForTryCatchStatementWithNestedIfStatements(): void
    {
        static::assertEquals(5, $this->calculateMethodMetric());
    }

    /**
     * Tests the algorithm with a conditional statement.
     */
    public function testNPathComplexityForSimpleConditionalStatement(): void
    {
        static::assertEquals(2, $this->calculateMethodMetric());
    }

    /**
     * Tests the algorithm with nested conditional statements.
     */
    public function testNPathComplexityForTwoNestedConditionalStatements(): void
    {
        static::assertEquals(4, $this->calculateMethodMetric());
    }

    /**
     * Tests the algorithm with nested conditional statements.
     */
    public function testNPathComplexityForThreeNestedConditionalStatements(): void
    {
        static::assertEquals(6, $this->calculateMethodMetric());
    }

    /**
     * Tests the algorithm with a conditional statement with boolean/logical
     * expressions.
     */
    public function testNPathComplexityForConditionalStatementWithLogicalExpressions(): void
    {
        static::assertEquals(5, $this->calculateMethodMetric());
    }

    /**
     * Tests the algorithm implementation against an existing bug in the
     * combination of return, conditional and if statement.
     */
    public function testNPathComplexityForReturnStatementWithConditional(): void
    {
        $npath = $this->calculateMethodMetric();
        static::assertEquals(3, $npath);
    }

    /**
     * Returns the NPath Complexity of the first function found in source file
     * associated with the calling test case.
     *
     * @since 0.9.12
     */
    private function calculateFunctionMetric(): int
    {
        return $this->calculateNPathComplexity(
            $this->getFirstFunctionForTestCaseInternal()
        );
    }

    /**
     * Parses the source code associated with the calling test case and returns
     * the first function found in the test case source file.
     *
     * @since 0.9.12
     */
    private function getFirstFunctionForTestCaseInternal(): ASTFunction
    {
        return $this->parseTestCaseSource($this->getCallingTestMethod())
            ->current()
            ->getFunctions()
            ->current();
    }

    /**
     * Returns the NPath Complexity of the first method found in source file
     * associated with the calling test case.
     *
     * @since 0.9.12
     */
    private function calculateMethodMetric(): int
    {
        return $this->calculateNPathComplexity(
            $this->getFirstMethodForTestCaseInternal()
        );
    }

    /**
     * Parses the source code associated with the calling test case and returns
     * the first method found in the test case source file.
     *
     * @since 0.9.12
     */
    private function getFirstMethodForTestCaseInternal(): ASTMethod
    {
        return $this->parseTestCaseSource($this->getCallingTestMethod())
            ->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();
    }

    /**
     * Calculates the NPath complexity for the given callable instance.
     *
     * @since 0.9.12
     */
    private function calculateNPathComplexity(AbstractASTCallable $callable): int
    {
        $analyzer = $this->createAnalyzer();
        $analyzer->dispatch($callable);

        $metrics = $analyzer->getNodeMetrics($callable);

        return $metrics['npath'];
    }

    /**
     * Creates a ready to use npath complexity analyzer.
     *
     * @since 1.0.0
     */
    private function createAnalyzer(): NPathComplexityAnalyzer
    {
        $analyzer = new NPathComplexityAnalyzer();
        $analyzer->setCache($this->cache);

        return $analyzer;
    }
}
