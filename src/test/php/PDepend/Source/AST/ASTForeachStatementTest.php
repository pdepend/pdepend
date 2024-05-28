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

use PDepend\Source\Parser\UnexpectedTokenException;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTForeachStatement} class.
 *
 * @covers \PDepend\Source\AST\ASTForeachStatement
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTForeachStatementTest extends ASTNodeTestCase
{
    /**
     * testThirdChildOfForeachStatementIsASTScopeStatement
     */
    public function testThirdChildOfForeachStatementIsASTScopeStatement(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertInstanceOf(ASTScopeStatement::class, $stmt->getChild(2));
    }

    /**
     * Tests the start line value.
     */
    public function testForeachStatementHasExpectedStartLine(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertEquals(4, $stmt->getStartLine());
    }

    /**
     * Tests the start column value.
     */
    public function testForeachStatementHasExpectedStartColumn(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * Tests the end line value.
     */
    public function testForeachStatementHasExpectedEndLine(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertEquals(6, $stmt->getEndLine());
    }

    /**
     * Tests the end column value.
     */
    public function testForeachStatementHasExpectedEndColumn(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testForeachStatementContainsListExpressionAsFirstChild
     */
    public function testForeachStatementContainsExpressionAsFirstChild(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertInstanceOf(ASTExpression::class, $stmt->getChild(0));
    }

    /**
     * testForeachStatementWithoutKeyAndWithValue
     */
    public function testForeachStatementWithoutKeyAndWithValue(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertInstanceOf(ASTVariable::class, $stmt->getChild(1));
    }

    /**
     * testForeachStatementWithoutKeyAndWithValueByReference
     */
    public function testForeachStatementWithoutKeyAndWithValueByReference(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertInstanceOf(ASTUnaryExpression::class, $stmt->getChild(1));
    }

    /**
     * testForeachStatementWithKeyAndValue
     */
    public function testForeachStatementWithKeyAndValue(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertInstanceOf(ASTVariable::class, $stmt->getChild(2));
    }

    /**
     * testForeachStatementWithKeyAndValueByReference
     */
    public function testForeachStatementWithKeyAndValueByReference(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertInstanceOf(ASTUnaryExpression::class, $stmt->getChild(2));
    }

    /**
     * testForeachStatementWithObjectPropertyByReference
     */
    public function testForeachStatementWithObjectPropertyByReference(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertInstanceOf(ASTUnaryExpression::class, $stmt->getChild(1));
    }

    /**
     * testForeachStatementWithKeyAndObjectPropertyByReference
     */
    public function testForeachStatementWithKeyAndObjectPropertyByReference(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertInstanceOf(ASTUnaryExpression::class, $stmt->getChild(2));
    }

    /**
     * testForeachStatementWithObjectPropertyAsKey
     */
    public function testForeachStatementWithObjectPropertyAsKey(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertInstanceOf(ASTMemberPrimaryPrefix::class, $stmt->getChild(1));
    }

    /**
     * testForeachStatementWithObjectPropertyAsValue
     */
    public function testForeachStatementWithObjectPropertyAsValue(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertInstanceOf(ASTMemberPrimaryPrefix::class, $stmt->getChild(1));
    }

    /**
     * testForeachStatementWithObjectPropertyAsKeyAndValue
     */
    public function testForeachStatementWithObjectPropertyAsKeyAndValue(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertInstanceOf(ASTMemberPrimaryPrefix::class, $stmt->getChild(1));
    }

    /**
     * testForeachStatementThrowsExpectedExceptionForKeyByReference
     */
    public function testForeachStatementThrowsExpectedExceptionForKeyByReference(): void
    {
        $this->expectException(UnexpectedTokenException::class);

        $this->getFirstForeachStatementInFunction();
    }

    /**
     * testForeachStatementWithCommentBeforeClosingParenthesis
     */
    public function testForeachStatementWithCommentBeforeClosingParenthesis(): void
    {
        $this->getFirstForeachStatementInFunction();
    }

    /**
     * testForeachStatementAlternativeScopeHasExpectedStartLine
     */
    public function testForeachStatementAlternativeScopeHasExpectedStartLine(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testForeachStatementAlternativeScopeHasExpectedStartColumn
     */
    public function testForeachStatementAlternativeScopeHasExpectedStartColumn(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testForeachStatementAlternativeScopeHasExpectedEndLine
     */
    public function testForeachStatementAlternativeScopeHasExpectedEndLine(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertEquals(6, $stmt->getEndLine());
    }

    /**
     * testForeachStatementAlternativeScopeHasExpectedEndColumn
     */
    public function testForeachStatementAlternativeScopeHasExpectedEndColumn(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertEquals(15, $stmt->getEndColumn());
    }

    /**
     * testForeachStatementTerminatedByPhpCloseTag
     */
    public function testForeachStatementTerminatedByPhpCloseTag(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertEquals(9, $stmt->getEndColumn());
    }

    /**
     * testForeachStatementWithList
     */
    public function testForeachStatementWithList(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertInstanceOf(ASTListExpression::class, $stmt->getChild(1));
    }

    /**
     * testForeachStatementWithKeyAndList
     */
    public function testForeachStatementWithKeyAndList(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertInstanceOf(ASTListExpression::class, $stmt->getChild(2));
    }

    /**
     * testForeachStatementWithList
     */
    public function testForeachStatementWithShortList(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertInstanceOf(ASTListExpression::class, $stmt->getChild(1));
    }

    /**
     * testForeachStatementWithList
     */
    public function testForeachStatementWithKeyAndShortList(): void
    {
        $stmt = $this->getFirstForeachStatementInFunction();
        static::assertInstanceOf(ASTListExpression::class, $stmt->getChild(2));
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstForeachStatementInFunction(): ASTForeachStatement
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTForeachStatement::class
        );
    }
}
