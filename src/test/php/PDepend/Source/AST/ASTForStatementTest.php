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
 * @covers \PDepend\Source\AST\ASTForStatement
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTForStatementTest extends ASTNodeTestCase
{
    /**
     * Tests the start line value.
     */
    public function testForStatementHasExpectedStartLine(): void
    {
        $stmt = $this->getFirstForStatementInFunction();
        static::assertEquals(4, $stmt->getStartLine());
    }

    /**
     * Tests the start column value.
     */
    public function testForStatementHasExpectedStartColumn(): void
    {
        $stmt = $this->getFirstForStatementInFunction();
        static::assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * Tests the end line value.
     */
    public function testForStatementHasExpectedEndLine(): void
    {
        $stmt = $this->getFirstForStatementInFunction();
        static::assertEquals(6, $stmt->getEndLine());
    }

    /**
     * Tests the end column value.
     */
    public function testForStatementHasExpectedEndColumn(): void
    {
        $stmt = $this->getFirstForStatementInFunction();
        static::assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testForExpressionHasExpectedStartLine
     */
    public function testForExpressionHasExpectedStartLine(): void
    {
        $stmt = $this->getFirstForStatementInFunction();
        static::assertEquals(4, $stmt->getChild(1)->getStartLine());
    }

    /**
     * testForExpressionHasExpectedStartColumn
     */
    public function testForExpressionHasExpectedStartColumn(): void
    {
        $stmt = $this->getFirstForStatementInFunction();
        static::assertEquals(27, $stmt->getChild(1)->getStartColumn());
    }

    /**
     * testForExpressionHasExpectedEndLine
     */
    public function testForExpressionHasExpectedEndLine(): void
    {
        $stmt = $this->getFirstForStatementInFunction();
        static::assertEquals(4, $stmt->getChild(1)->getEndLine());
    }

    /**
     * testForExpressionHasExpectedEndColumn
     */
    public function testForExpressionHasExpectedEndColumn(): void
    {
        $stmt = $this->getFirstForStatementInFunction();
        static::assertEquals(33, $stmt->getChild(1)->getEndColumn());
    }

    /**
     * testFirstChildOfForStatementIsInstanceOfForInit
     */
    public function testFirstChildOfForStatementIsInstanceOfForInit(): void
    {
        $stmt = $this->getFirstForStatementInFunction();
        static::assertInstanceOf(ASTForInit::class, $stmt->getChild(0));
    }

    /**
     * testFirstChildOfForStatementCanBeLeftBlank
     */
    public function testFirstChildOfForStatementCanBeLeftBlank(): void
    {
        $stmt = $this->getFirstForStatementInFunction();
        static::assertInstanceOf(ASTExpression::class, $stmt->getChild(0));
    }

    /**
     * testParserHandlesBooleanLiteralInForInit
     */
    public function testParserHandlesBooleanLiteralInForInit(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * testSecondChildOfForStatementIsInstanceOfExpression
     */
    public function testSecondChildOfForStatementIsInstanceOfExpression(): void
    {
        $stmt = $this->getFirstForStatementInFunction();
        static::assertInstanceOf(ASTExpression::class, $stmt->getChild(1));
    }

    /**
     * testSecondChildOfForStatementCanBeLeftBlank
     */
    public function testSecondChildOfForStatementCanBeLeftBlank(): void
    {
        $stmt = $this->getFirstForStatementInFunction();
        static::assertInstanceOf(ASTForUpdate::class, $stmt->getChild(1));
    }

    /**
     * testThirdChildOfForStatementIsInstanceOfForUpdate
     */
    public function testThirdChildOfForStatementIsInstanceOfForUpdate(): void
    {
        $stmt = $this->getFirstForStatementInFunction();
        static::assertInstanceOf(ASTForUpdate::class, $stmt->getChild(2));
    }

    /**
     * testThirdChildOfForStatementCanBeLeftBlank
     */
    public function testThirdChildOfForStatementCanBeLeftBlank(): void
    {
        $stmt = $this->getFirstForStatementInFunction();
        static::assertInstanceOf(ASTScopeStatement::class, $stmt->getChild(2));
    }

    /**
     * testFourthChildOfForStatementIsInstanceOfScopeStatement
     */
    public function testFourthChildOfForStatementIsInstanceOfScopeStatement(): void
    {
        $stmt = $this->getFirstForStatementInFunction();
        static::assertInstanceOf(ASTScopeStatement::class, $stmt->getChild(3));
    }

    /**
     * testFourthChildOfForStatementIsInstanceOfStatement
     */
    public function testFourthChildOfForStatementIsInstanceOfStatement(): void
    {
        $stmt = $this->getFirstForStatementInFunction();
        static::assertInstanceOf(ASTStatement::class, $stmt->getChild(3));
    }

    /**
     * testParserResetsScopeTreeForEmptyForInit
     */
    public function testParserResetsScopeTreeForEmptyForInit(): void
    {
        $class = $this->getFirstClassForTestCase();

        $actual = [$class->getStartLine(), $class->getEndLine()];
        $expected = [5, 14];

        static::assertEquals($expected, $actual);
    }

    /**
     * testParserResetsScopeTreeForEmptyForExpression
     */
    public function testParserResetsScopeTreeForEmptyForExpression(): void
    {
        $class = $this->getFirstClassForTestCase();

        $actual = [$class->getStartLine(), $class->getEndLine()];
        $expected = [5, 14];

        static::assertEquals($expected, $actual);
    }

    /**
     * testForStatementAlternativeScopeHasExpectedStartLine
     */
    public function testForStatementAlternativeScopeHasExpectedStartLine(): void
    {
        $stmt = $this->getFirstForStatementInFunction();
        static::assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testForStatementAlternativeScopeHasExpectedStartColumn
     */
    public function testForStatementAlternativeScopeHasExpectedStartColumn(): void
    {
        $stmt = $this->getFirstForStatementInFunction();
        static::assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testForStatementAlternativeScopeHasExpectedEndLine
     */
    public function testForStatementAlternativeScopeHasExpectedEndLine(): void
    {
        $stmt = $this->getFirstForStatementInFunction();
        static::assertEquals(12, $stmt->getEndLine());
    }

    /**
     * testForStatementAlternativeScopeHasExpectedEndColumn
     */
    public function testForStatementAlternativeScopeHasExpectedEndColumn(): void
    {
        $stmt = $this->getFirstForStatementInFunction();
        static::assertEquals(11, $stmt->getEndColumn());
    }

    /**
     * testForStatementTerminatedByPhpCloseTag
     */
    public function testForStatementTerminatedByPhpCloseTag(): void
    {
        $stmt = $this->getFirstForStatementInFunction();
        static::assertEquals(9, $stmt->getEndColumn());
    }

    /**
     * testParserHandlesBooleanLiteralInForExpression
     */
    public function testParserHandlesBooleanLiteralInForExpression(): void
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserResetsScopeTreeForEmptyForUpdate
     */
    public function testParserResetsScopeTreeForEmptyForUpdate(): void
    {
        $class = $this->getFirstClassForTestCase();

        $actual = [$class->getStartLine(), $class->getEndLine()];
        $expected = [5, 14];

        static::assertEquals($expected, $actual);
    }

    /**
     * testParserHandlesParenthesisExpressionInForUpdate
     */
    public function testParserHandlesParenthesisExpressionInForUpdate(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * testParserHandlesBooleanLiteralInForUpdate
     */
    public function testParserHandlesBooleanLiteralInForUpdate(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstForStatementInFunction(): ASTForStatement
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTForStatement::class
        );
    }
}
