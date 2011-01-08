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

require_once 'PHP/Depend/Code/Method.php';
require_once 'PHP/Depend/Metrics/NPathComplexity/Analyzer.php';

/**
 * Test case for the npath complexity analyzer.
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
class PHP_Depend_Metrics_NPathComplexity_AnalyzerTest extends PHP_Depend_Metrics_AbstractTest
{
    /**
     * testNPathComplexityForNestedIfStatementsWithScope
     *
     * @return unknown_type
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     * @since 0.9.12
     */
    public function testNPathComplexityForNestedIfStatementsWithScope()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(4, $npath);
    }

    /**
     * testNPathComplexityForNestedIfStatementsWithoutScope
     *
     * @return unknown_type
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     * @since 0.9.12
     */
    public function testNPathComplexityForNestedIfStatementsWithoutScope()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(4, $npath);
    }

    /**
     * testNPathComplexityForSiblingConditionalExpressions
     *
     * @return unknown_type
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     * @since 0.9.12
     */
    public function testNPathComplexityForSiblingConditionalExpressions()
    {
        $npath = $this->_getNPathComplexityForFirstFunctionInTestSource(__METHOD__);
        $this->assertEquals(25, $npath);
    }

    /**
     * testNPathComplexityForSiblingExpressions
     *
     * @return unknown_type
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     * @since 0.9.12
     * @todo What happens with boolean/logical expressions within the body of
     *       any other statement/expression?
     */
    public function testNPathComplexityForSiblingExpressions()
    {
        $npath = $this->_getNPathComplexityForFirstFunctionInTestSource(__METHOD__);
        $this->assertEquals(15, $npath);
    }

    /**
     * testNPathComplexityForTwoSiblingIfStatetements
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     * @since 0.9.12
     */
    public function testNPathComplexityForTwoSiblingIfStatetements()
    {
        $npath = $this->_getNPathComplexityForFirstFunctionInTestSource(__METHOD__);
        $this->assertEquals(4, $npath);
    }

    /**
     * testNPathComplexityForForeachStatementWithNestedIfStatetements
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     * @since 0.9.12
     */
    public function testNPathComplexityForForeachStatementWithNestedIfStatetements()
    {
        $npath = $this->_getNPathComplexityForFirstFunctionInTestSource(__METHOD__);
        $this->assertEquals(3, $npath);
    }

    /**
     * testNPathComplexityForSiblingIfStatementsAndForeachStatement
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     * @since 0.9.12
     */
    public function testNPathComplexityForSiblingIfStatementsAndForeachStatement()
    {
        $npath = $this->_getNPathComplexityForFirstFunctionInTestSource(__METHOD__);
        $this->assertEquals(12, $npath);
    }

    /**
     * testNPathComplexityForComplexFunction
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     * @since 0.9.12
     */
    public function testNPathComplexityForComplexFunction()
    {
        $npath = $this->_getNPathComplexityForFirstFunctionInTestSource(__METHOD__);
        $this->assertEquals(60, $npath);
    }

    /**
     * testNPathComplexityForConditionalsInArrayDeclaration
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     * @since 0.9.12
     */
    public function testNPathComplexityForComplexNestedControlStatements()
    {
        $npath = $this->_getNPathComplexityForFirstFunctionInTestSource(__METHOD__);
        $this->assertEquals(63, $npath);
    }
    
    /**
     * testNPathComplexityForConditionalsInArrayDeclaration
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     * @since 0.9.12
     * @todo Fix this, once the AST is complete
     */
    public function testNPathComplexityForConditionalsInArrayDeclaration()
    {
        $npath = $this->_getNPathComplexityForFirstFunctionInTestSource(__METHOD__);
        //$this->assertEquals(625, $npath);
        $this->assertEquals(17, $npath);
    }

    /**
     * testNPathComplexityIsZeroForEmptyMethod
     *
     * @return unknown_type
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityIsZeroForEmptyMethod()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(1, $npath);
    }

    /**
     * Tests a method body with a simple if statement.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForMethodWithSimpleIfStatement()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(2, $npath);
    }

    /**
     * Tests a method body with a simple if statement with dynamic identifier.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForIfStatementWithNestedDynamicIdentifier()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(2, $npath);
    }

    /**
     * Tests the analyzer implementation against consecutive if-statements.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForConsecutiveIfStatements()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(80, $npath);
    }

    /**
     * Tests the analyzer implementation against multiple if-else-if statements.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForConsecutiveIfElseIfStatements()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(4, $npath);
    }

    /**
     * Tests the analyzer implementation against multiple if-elseif statements.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForConsecutiveIfElsifStatements()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(4, $npath);
    }

    /**
     * Tests the analyzer implementation against an empty while statement.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForEmptyWhileStatement()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(3, $npath);
    }

    /**
     * Tests the anaylzer with nested while statements.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForNestedWhileStatements()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(5, $npath);
    }

    /**
     * Tests the npath algorithm with a simple do-while statement.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForSimpleDoWhileStatement()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(3, $npath);
    }

    /**
     * Tests the analyzer with a simple for statement.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForSimpleForStatement()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(2, $npath);
    }

    /**
     * Tests the analyzer with a complex for statement.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForComplexForStatement()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(4, $npath);
    }

    /**
     * Tests the analyzer implementation with a simple foreach statement.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForSimpleForeachStatement()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(2, $npath);
    }

    /**
     * Tests the algorithm with a simple return statement.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForSimpleReturnStatement()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(1, $npath);
    }

    /**
     * Tests the algorithm with a return statement that contains boolean expressions.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForReturnStatementWithBooleanExpressions()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(2, $npath);
    }

    /**
     * Tests the algorithm with a return statement that contains a conditional.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForReturnStatementWithConditionalStatement()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(5, $npath);
    }

    /**
     * Tests the algorithm with a simple switch statement that contains one case
     * child.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForSimpleSwitchStatement()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(1, $npath);
    }

    /**
     * Tests the algorithm with a switch statement that contains multiple case
     * statements.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForSwitchStatementWithMultipleCaseStatements()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(5, $npath);
    }

    /**
     * Tests the algorithm with a switch statement that contains complex case
     * statements.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForSwitchStatementWithComplexCaseStatements()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(8, $npath);
    }

    /**
     * Tests the algorithm with a simple try statement.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForSimpleTryCatchStatement()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(2, $npath);
    }

    /**
     * Tests the algorithm with a try statement with multiple catch statements.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForTryStatementWithMutlipleCatchStatements()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(5, $npath);
    }

    /**
     * Tests the algorithm with a try statement with nested if statements.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForTryCatchStatementWithNestedIfStatements()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(5, $npath);
    }

    /**
     * Tests the algorithm with a conditional statement.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForSimpleConditionalStatement()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(5, $npath);
    }

    /**
     * Tests the algorithm with nested conditional statements.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForTwoNestedConditionalStatements()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(9, $npath);
    }

    /**
     * Tests the algorithm with nested conditional statements.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForThreeNestedConditionalStatements()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(13, $npath);
    }

    /**
     * Tests the algorithm with a conditional statement with boolean/logical
     * expressions.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForConditionalStatementWithLogicalExpressions()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(6, $npath);
    }

    /**
     * Tests the algorithm implementation against an existing bug in the
     * combination of return, conditional and if statement.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NPathComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::npathcomplexity
     * @group unittest
     */
    public function testNPathComplexityForReturnStatementWithConditional()
    {
        $npath = $this->_getNPathComplexityForFirstMethodInTestSource(__METHOD__);
        $this->assertEquals(6, $npath);
    }

    /**
     * Returns the NPath Complexity of the first function found in source file
     * associated with the calling test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return integer
     * @since 0.9.12
     */
    private function _getNPathComplexityForFirstFunctionInTestSource($testCase)
    {
        return $this->_calculateNPathComplexity(
            $this->_getFirstFunctionForTestCase($testCase)
        );
    }

    /**
     * Parses the source code associated with the calling test case and returns
     * the first function found in the test case source file.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_Function
     * @since 0.9.12
     */
    private function _getFirstFunctionForTestCase($testCase)
    {
        return self::parseTestCaseSource($testCase)
            ->current()
            ->getFunctions()
            ->current();
    }

    /**
     * Returns the NPath Complexity of the first method found in source file
     * associated with the calling test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return integer
     * @since 0.9.12
     */
    private function _getNPathComplexityForFirstMethodInTestSource($testCase)
    {
        return $this->_calculateNPathComplexity(
            $this->_getFirstMethodForTestCase($testCase)
        );
    }

    /**
     * Parses the source code associated with the calling test case and returns
     * the first method found in the test case source file.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_Method
     * @since 0.9.12
     */
    private function _getFirstMethodForTestCase($testCase)
    {
        return self::parseTestCaseSource($testCase)
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
        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $callable->accept($analyzer);

        $metrics = $analyzer->getNodeMetrics($callable);
        return $metrics['npath'];
    }
}