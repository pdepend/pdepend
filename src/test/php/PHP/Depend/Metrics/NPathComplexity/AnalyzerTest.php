<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for the NPath complexity analyzer.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 * 
 * @covers PHP_Depend_Metrics_AbstractCachingAnalyzer
 * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
 * @group pdepend
 * @group pdepend::metrics
 * @group pdepend::metrics::npathcomplexity
 * @group unittest
 */
class PHP_Depend_Metrics_NPathComplexity_AnalyzerTest extends PHP_Depend_Metrics_AbstractTest
{
    /**
     * @var PHP_Depend_Util_Cache_Driver
     * @since 1.0.0
     */
    private $_cache;

    /**
     * Initializes a in memory cache.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_cache = new PHP_Depend_Util_Cache_Driver_Memory();
    }

    /**
     * testAnalyzerRestoresExpectedFunctionMetricsFromCache
     *
     * @return void
     * @since 1.0.0
     */
    public function testAnalyzerRestoresExpectedFunctionMetricsFromCache()
    {
        $packages = self::parseCodeResourceForTest();
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics0 = $analyzer->getNodeMetrics($function);

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics1 = $analyzer->getNodeMetrics($function);

        $this->assertEquals($metrics0, $metrics1);
    }

    /**
     * testAnalyzerRestoresExpectedMethodMetricsFromCache
     *
     * @return void
     * @since 1.0.0
     */
    public function testAnalyzerRestoresExpectedMethodMetricsFromCache()
    {
        $packages = self::parseCodeResourceForTest();
        $method   = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics0 = $analyzer->getNodeMetrics($method);

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics1 = $analyzer->getNodeMetrics($method);

        $this->assertEquals($metrics0, $metrics1);
    }

    /**
     * testNPathComplexityForNestedIfStatementsWithScope
     *
     * @return void
     * @since 0.9.12
     */
    public function testNPathComplexityForNestedIfStatementsWithScope()
    {
        self::assertEquals(4, $this->_calculateMethodMetric());
    }

    /**
     * testNPathComplexityForNestedIfStatementsWithoutScope
     *
     * @return void
     * @since 0.9.12
     */
    public function testNPathComplexityForNestedIfStatementsWithoutScope()
    {
        self::assertEquals(4, $this->_calculateMethodMetric());
    }

    /**
     * testNPathComplexityForSiblingConditionalExpressions
     *
     * @return void
     * @since 0.9.12
     */
    public function testNPathComplexityForSiblingConditionalExpressions()
    {
        self::assertEquals(25, $this->_calculateFunctionMetric());
    }

    /**
     * testNPathComplexityForSiblingExpressions
     *
     * @return void
     * @since 0.9.12
     * @todo What happens with boolean/logical expressions within the body of
     *       any other statement/expression?
     */
    public function testNPathComplexityForSiblingExpressions()
    {
        self::assertEquals(15, $this->_calculateFunctionMetric());
    }

    /**
     * testNPathComplexityForTwoSiblingIfStatetements
     *
     * @return void
     * @since 0.9.12
     */
    public function testNPathComplexityForTwoSiblingIfStatetements()
    {
        self::assertEquals(4, $this->_calculateFunctionMetric());
    }

    /**
     * testNPathComplexityForForeachStatementWithNestedIfStatetements
     *
     * @return void
     * @since 0.9.12
     */
    public function testNPathComplexityForForeachStatementWithNestedIfStatetements()
    {
        self::assertEquals(3, $this->_calculateFunctionMetric());
    }

    /**
     * testNPathComplexityForSiblingIfStatementsAndForeachStatement
     *
     * @return void
     * @since 0.9.12
     */
    public function testNPathComplexityForSiblingIfStatementsAndForeachStatement()
    {
        self::assertEquals(12, $this->_calculateFunctionMetric());
    }

    /**
     * testNPathComplexityForComplexFunction
     *
     * @return void
     * @since 0.9.12
     */
    public function testNPathComplexityForComplexFunction()
    {
        self::assertEquals(60, $this->_calculateFunctionMetric());
    }

    /**
     * testNPathComplexityForConditionalsInArrayDeclaration
     *
     * @return void
     * @since 0.9.12
     */
    public function testNPathComplexityForComplexNestedControlStatements()
    {
        self::assertEquals(63, $this->_calculateFunctionMetric());
    }
    
    /**
     * testNPathComplexityForConditionalsInArrayDeclaration
     *
     * @return void
     * @since 0.9.12
     */
    public function testNPathComplexityForConditionalsInArrayDeclaration()
    {
        self::assertEquals(625, $this->_calculateFunctionMetric());
    }

    /**
     * testNPathComplexityIsZeroForEmptyMethod
     *
     * @return void
     */
    public function testNPathComplexityIsZeroForEmptyMethod()
    {
        self::assertEquals(1, $this->_calculateMethodMetric());
    }

    /**
     * Tests a method body with a simple if statement.
     *
     * @return void
     */
    public function testNPathComplexityForMethodWithSimpleIfStatement()
    {
        self::assertEquals(2, $this->_calculateMethodMetric());
    }

    /**
     * Tests a method body with a simple if statement with dynamic identifier.
     *
     * @return void
     */
    public function testNPathComplexityForIfStatementWithNestedDynamicIdentifier()
    {
        self::assertEquals(2, $this->_calculateMethodMetric());
    }

    /**
     * Tests the analyzer implementation against consecutive if-statements.
     *
     * @return void
     */
    public function testNPathComplexityForConsecutiveIfStatements()
    {
        self::assertEquals(80, $this->_calculateMethodMetric());
    }

    /**
     * Tests the analyzer implementation against multiple if-else-if statements.
     *
     * @return void
     */
    public function testNPathComplexityForConsecutiveIfElseIfStatements()
    {
        self::assertEquals(4, $this->_calculateMethodMetric());
    }

    /**
     * Tests the analyzer implementation against multiple if-elseif statements.
     *
     * @return void
     */
    public function testNPathComplexityForConsecutiveIfElsifStatements()
    {
        self::assertEquals(4, $this->_calculateMethodMetric());
    }

    /**
     * Tests the analyzer implementation against an empty while statement.
     *
     * @return void
     */
    public function testNPathComplexityForEmptyWhileStatement()
    {
        self::assertEquals(3, $this->_calculateMethodMetric());
    }

    /**
     * Tests the anaylzer with nested while statements.
     *
     * @return void
     */
    public function testNPathComplexityForNestedWhileStatements()
    {
        self::assertEquals(5, $this->_calculateMethodMetric());
    }

    /**
     * Tests the npath algorithm with a simple do-while statement.
     *
     * @return void
     */
    public function testNPathComplexityForSimpleDoWhileStatement()
    {
        self::assertEquals(3, $this->_calculateMethodMetric());
    }

    /**
     * Tests the analyzer with a simple for statement.
     *
     * @return void
     */
    public function testNPathComplexityForSimpleForStatement()
    {
        self::assertEquals(2, $this->_calculateMethodMetric());
    }

    /**
     * Tests the analyzer with a complex for statement.
     *
     * @return void
     */
    public function testNPathComplexityForComplexForStatement()
    {
        self::assertEquals(4, $this->_calculateMethodMetric());
    }

    /**
     * Tests the analyzer implementation with a simple foreach statement.
     *
     * @return void
     */
    public function testNPathComplexityForSimpleForeachStatement()
    {
        self::assertEquals(2, $this->_calculateMethodMetric());
    }

    /**
     * Tests the algorithm with a simple return statement.
     *
     * @return void
     */
    public function testNPathComplexityForSimpleReturnStatement()
    {
        self::assertEquals(1, $this->_calculateMethodMetric());
    }

    /**
     * Tests the algorithm with a return statement that contains boolean expressions.
     *
     * @return void
     */
    public function testNPathComplexityForReturnStatementWithBooleanExpressions()
    {
        self::assertEquals(2, $this->_calculateMethodMetric());
    }

    /**
     * Tests the algorithm with a return statement that contains a conditional.
     *
     * @return void
     */
    public function testNPathComplexityForReturnStatementWithConditionalStatement()
    {
        self::assertEquals(5, $this->_calculateMethodMetric());
    }

    /**
     * Tests the algorithm with a simple switch statement that contains one case
     * child.
     *
     * @return void
     */
    public function testNPathComplexityForSimpleSwitchStatement()
    {
        self::assertEquals(1, $this->_calculateMethodMetric());
    }

    /**
     * Tests the algorithm with a switch statement that contains multiple case
     * statements.
     *
     * @return void
     */
    public function testNPathComplexityForSwitchStatementWithMultipleCaseStatements()
    {
        self::assertEquals(5, $this->_calculateMethodMetric());
    }

    /**
     * Tests the algorithm with a switch statement that contains complex case
     * statements.
     *
     * @return void
     */
    public function testNPathComplexityForSwitchStatementWithComplexCaseStatements()
    {
        self::assertEquals(8, $this->_calculateMethodMetric());
    }

    /**
     * Tests the algorithm with a simple try statement.
     *
     * @return void
     */
    public function testNPathComplexityForSimpleTryCatchStatement()
    {
        self::assertEquals(2, $this->_calculateMethodMetric());
    }

    /**
     * Tests the algorithm with a try statement with multiple catch statements.
     *
     * @return void
     */
    public function testNPathComplexityForTryStatementWithMutlipleCatchStatements()
    {
        self::assertEquals(5, $this->_calculateMethodMetric());
    }

    /**
     * Tests the algorithm with a try statement with nested if statements.
     *
     * @return void
     */
    public function testNPathComplexityForTryCatchStatementWithNestedIfStatements()
    {
        self::assertEquals(5, $this->_calculateMethodMetric());
    }

    /**
     * Tests the algorithm with a conditional statement.
     *
     * @return void
     */
    public function testNPathComplexityForSimpleConditionalStatement()
    {
        self::assertEquals(5, $this->_calculateMethodMetric());
    }

    /**
     * Tests the algorithm with nested conditional statements.
     *
     * @return void
     */
    public function testNPathComplexityForTwoNestedConditionalStatements()
    {
        self::assertEquals(9, $this->_calculateMethodMetric());
    }

    /**
     * Tests the algorithm with nested conditional statements.
     *
     * @return void
     */
    public function testNPathComplexityForThreeNestedConditionalStatements()
    {
        self::assertEquals(13, $this->_calculateMethodMetric());
    }

    /**
     * Tests the algorithm with a conditional statement with boolean/logical
     * expressions.
     *
     * @return void
     */
    public function testNPathComplexityForConditionalStatementWithLogicalExpressions()
    {
        self::assertEquals(6, $this->_calculateMethodMetric());
    }

    /**
     * Tests the algorithm implementation against an existing bug in the
     * combination of return, conditional and if statement.
     *
     * @return void
     */
    public function testNPathComplexityForReturnStatementWithConditional()
    {
        $npath = $this->_calculateMethodMetric();
        self::assertEquals(6, $npath);
    }

    /**
     * Returns the NPath Complexity of the first function found in source file
     * associated with the calling test case.
     *
     * @return integer
     * @since 0.9.12
     */
    private function _calculateFunctionMetric()
    {
        return $this->_calculateNPathComplexity(
            $this->_getFirstFunctionForTestCase()
        );
    }

    /**
     * Parses the source code associated with the calling test case and returns
     * the first function found in the test case source file.
     *
     * @return PHP_Depend_Code_Function
     * @since 0.9.12
     */
    private function _getFirstFunctionForTestCase()
    {
        return self::parseTestCaseSource(self::getCallingTestMethod())
            ->current()
            ->getFunctions()
            ->current();
    }

    /**
     * Returns the NPath Complexity of the first method found in source file
     * associated with the calling test case.
     *
     * @return integer
     * @since 0.9.12
     */
    private function _calculateMethodMetric()
    {
        return $this->_calculateNPathComplexity(
            $this->_getFirstMethodForTestCase()
        );
    }

    /**
     * Parses the source code associated with the calling test case and returns
     * the first method found in the test case source file.
     *
     * @return PHP_Depend_Code_Method
     * @since 0.9.12
     */
    private function _getFirstMethodForTestCase()
    {
        return self::parseTestCaseSource(self::getCallingTestMethod())
            ->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();
    }

    /**
     * Calculates the NPath complexity for the given callable instance.
     *
     * @param PHP_Depend_Code_AbstractCallable $callable The context callable.
     *
     * @return string
     * @since 0.9.12
     */
    private function _calculateNPathComplexity(PHP_Depend_Code_AbstractCallable $callable)
    {
        $analyzer = $this->_createAnalyzer();
        $callable->accept($analyzer);

        $metrics = $analyzer->getNodeMetrics($callable);
        return $metrics['npath'];
    }

    /**
     * Creates a ready to use npath complexity analyzer.
     *
     * @return PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @since 1.0.0
     */
    private function _createAnalyzer()
    {
        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->setCache($this->_cache);

        return $analyzer;
    }
}
