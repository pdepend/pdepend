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

namespace PDepend\Source\AST;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTForStatement} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTForStatement
 * @group unittest
 */
class ASTForStatementTest extends ASTNodeTest
{
    /**
     * Tests the start line value.
     *
     * @return void
     */
    public function testForStatementHasExpectedStartLine()
    {
        $stmt = $this->getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @return void
     */
    public function testForStatementHasExpectedStartColumn()
    {
        $stmt = $this->getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @return void
     */
    public function testForStatementHasExpectedEndLine()
    {
        $stmt = $this->getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(6, $stmt->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @return void
     */
    public function testForStatementHasExpectedEndColumn()
    {
        $stmt = $this->getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testForExpressionHasExpectedStartLine
     *
     * @return void
     */
    public function testForExpressionHasExpectedStartLine()
    {
        $stmt = $this->getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getChild(1)->getStartLine());
    }

    /**
     * testForExpressionHasExpectedStartColumn
     *
     * @return void
     */
    public function testForExpressionHasExpectedStartColumn()
    {
        $stmt = $this->getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(27, $stmt->getChild(1)->getStartColumn());
    }

    /**
     * testForExpressionHasExpectedEndLine
     *
     * @return void
     */
    public function testForExpressionHasExpectedEndLine()
    {
        $stmt = $this->getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getChild(1)->getEndLine());
    }

    /**
     * testForExpressionHasExpectedEndColumn
     *
     * @return void
     */
    public function testForExpressionHasExpectedEndColumn()
    {
        $stmt = $this->getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(33, $stmt->getChild(1)->getEndColumn());
    }

    /**
     * testFirstChildOfForStatementIsInstanceOfForInit
     *
     * @return void
     */
    public function testFirstChildOfForStatementIsInstanceOfForInit()
    {
        $stmt = $this->getFirstForStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTForInit', $stmt->getChild(0));
    }

    /**
     * testFirstChildOfForStatementCanBeLeftBlank
     *
     * @return void
     */
    public function testFirstChildOfForStatementCanBeLeftBlank()
    {
        $stmt = $this->getFirstForStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTExpression', $stmt->getChild(0));
    }


    /**
     * testParserHandlesBooleanLiteralInForInit
     *
     * @return void
     */
    public function testParserHandlesBooleanLiteralInForInit()
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * testSecondChildOfForStatementIsInstanceOfExpression
     *
     * @return void
     */
    public function testSecondChildOfForStatementIsInstanceOfExpression()
    {
        $stmt = $this->getFirstForStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTExpression', $stmt->getChild(1));
    }

    /**
     * testSecondChildOfForStatementCanBeLeftBlank
     *
     * @return void
     */
    public function testSecondChildOfForStatementCanBeLeftBlank()
    {
        $stmt = $this->getFirstForStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTForUpdate', $stmt->getChild(1));
    }

    /**
     * testThirdChildOfForStatementIsInstanceOfForUpdate
     *
     * @return void
     */
    public function testThirdChildOfForStatementIsInstanceOfForUpdate()
    {
        $stmt = $this->getFirstForStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTForUpdate', $stmt->getChild(2));
    }

    /**
     * testThirdChildOfForStatementCanBeLeftBlank
     *
     * @return void
     */
    public function testThirdChildOfForStatementCanBeLeftBlank()
    {
        $stmt = $this->getFirstForStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScopeStatement', $stmt->getChild(2));
    }

    /**
     * testFourthChildOfForStatementIsInstanceOfScopeStatement
     *
     * @return void
     */
    public function testFourthChildOfForStatementIsInstanceOfScopeStatement()
    {
        $stmt = $this->getFirstForStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScopeStatement', $stmt->getChild(3));
    }

    /**
     * testFourthChildOfForStatementIsInstanceOfStatement
     *
     * @return void
     */
    public function testFourthChildOfForStatementIsInstanceOfStatement()
    {
        $stmt = $this->getFirstForStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTStatement', $stmt->getChild(3));
    }

    /**
     * testParserResetsScopeTreeForEmptyForInit
     *
     * @return void
     */
    public function testParserResetsScopeTreeForEmptyForInit()
    {
        $class = $this->getFirstClassForTestCase(__METHOD__);

        $actual   = array($class->getStartLine(), $class->getEndLine());
        $expected = array(5, 14);

        $this->assertEquals($expected, $actual);
    }

    /**
     * testParserResetsScopeTreeForEmptyForExpression
     *
     * @return void
     */
    public function testParserResetsScopeTreeForEmptyForExpression()
    {
        $class = $this->getFirstClassForTestCase(__METHOD__);

        $actual   = array($class->getStartLine(), $class->getEndLine());
        $expected = array(5, 14);

        $this->assertEquals($expected, $actual);
    }

    /**
     * testForStatementAlternativeScopeHasExpectedStartLine
     *
     * @return void
     */
    public function testForStatementAlternativeScopeHasExpectedStartLine()
    {
        $stmt = $this->getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testForStatementAlternativeScopeHasExpectedStartColumn
     *
     * @return void
     */
    public function testForStatementAlternativeScopeHasExpectedStartColumn()
    {
        $stmt = $this->getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testForStatementAlternativeScopeHasExpectedEndLine
     *
     * @return void
     */
    public function testForStatementAlternativeScopeHasExpectedEndLine()
    {
        $stmt = $this->getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(12, $stmt->getEndLine());
    }

    /**
     * testForStatementAlternativeScopeHasExpectedEndColumn
     *
     * @return void
     */
    public function testForStatementAlternativeScopeHasExpectedEndColumn()
    {
        $stmt = $this->getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(11, $stmt->getEndColumn());
    }

    /**
     * testForStatementTerminatedByPhpCloseTag
     *
     * @group end-st
     *
     * @return void
     */
    public function testForStatementTerminatedByPhpCloseTag()
    {
        $stmt = $this->getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(9, $stmt->getEndColumn());
    }

    /**
     * testParserHandlesBooleanLiteralInForExpression
     *
     * @return void
     */
    public function testParserHandlesBooleanLiteralInForExpression()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserResetsScopeTreeForEmptyForUpdate
     *
     * @return void
     */
    public function testParserResetsScopeTreeForEmptyForUpdate()
    {
        $class = $this->getFirstClassForTestCase(__METHOD__);

        $actual   = array($class->getStartLine(), $class->getEndLine());
        $expected = array(5, 14);

        $this->assertEquals($expected, $actual);
    }

    /**
     * testParserHandlesParenthesisExpressionInForUpdate
     *
     * @return void
     */
    public function testParserHandlesParenthesisExpressionInForUpdate()
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * testParserHandlesBooleanLiteralInForUpdate
     *
     * @return void
     */
    public function testParserHandlesBooleanLiteralInForUpdate()
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return \PDepend\Source\AST\ASTForStatement
     */
    private function getFirstForStatementInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase,
            'PDepend\\Source\\AST\\ASTForStatement'
        );
    }
}
