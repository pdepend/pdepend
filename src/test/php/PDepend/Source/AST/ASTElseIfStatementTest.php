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
 * Test case for the {@link \PDepend\Source\AST\ASTElseIfStatement} class.
 *
 * @covers \PDepend\Source\AST\ASTElseIfStatement
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTElseIfStatementTest extends ASTNodeTestCase
{
    /**
     * testHasElseMethodReturnsFalseByDefault
     */
    public function testHasElseMethodReturnsFalseByDefault(): void
    {
        $stmt = $this->getFirstElseIfStatementInFunction();
        static::assertFalse($stmt->hasElse());
    }

    /**
     * testHasElseMethodReturnsTrueWhenElseIfBranchExists
     */
    public function testHasElseMethodReturnsTrueWhenElseIfBranchExists(): void
    {
        $stmt = $this->getFirstElseIfStatementInFunction();
        static::assertTrue($stmt->hasElse());
    }

    /**
     * testHasElseMethodReturnsTrueWhenElseBranchWithIfExists
     */
    public function testHasElseMethodReturnsTrueWhenElseBranchWithIfExists(): void
    {
        $stmt = $this->getFirstElseIfStatementInFunction();
        static::assertTrue($stmt->hasElse());
    }

    /**
     * testHasElseMethodReturnsTrueWhenElseBranchExists
     */
    public function testHasElseMethodReturnsTrueWhenElseBranchExists(): void
    {
        $stmt = $this->getFirstElseIfStatementInFunction();
        static::assertTrue($stmt->hasElse());
    }

    /**
     * Tests the generated object graph of an elseif statement.
     */
    public function testElseIfStatementGraphWithBooleanExpressions(): void
    {
        $stmt = $this->getFirstElseIfStatementInFunction();
        static::assertCount(2, $stmt->getChildren());
    }

    /**
     * testFirstChildOfElseIfStatementIsInstanceOfExpression
     */
    public function testFirstChildOfElseIfStatementIsInstanceOfExpression(): void
    {
        $stmt = $this->getFirstElseIfStatementInFunction();
        static::assertInstanceOf(ASTExpression::class, $stmt->getChild(0));
    }

    /**
     * testSecondChildOfElseIfStatementIsInstanceOfScopeStatement
     */
    public function testSecondChildOfElseIfStatementIsInstanceOfScopeStatement(): void
    {
        $stmt = $this->getFirstElseIfStatementInFunction();
        static::assertInstanceOf(ASTScopeStatement::class, $stmt->getChild(1));
    }

    /**
     * Tests the start line value.
     */
    public function testElseIfStatementHasExpectedStartLine(): void
    {
        $stmt = $this->getFirstElseIfStatementInFunction();
        static::assertEquals(6, $stmt->getStartLine());
    }

    /**
     * Tests the start column value.
     */
    public function testElseIfStatementHasExpectedStartColumn(): void
    {
        $stmt = $this->getFirstElseIfStatementInFunction();
        static::assertEquals(7, $stmt->getStartColumn());
    }

    /**
     * Tests the end line value.
     */
    public function testElseIfStatementHasExpectedEndLine(): void
    {
        $stmt = $this->getFirstElseIfStatementInFunction();
        static::assertEquals(8, $stmt->getEndLine());
    }

    /**
     * Tests the end column value.
     */
    public function testElseIfStatementHasExpectedEndColumn(): void
    {
        $stmt = $this->getFirstElseIfStatementInFunction();
        static::assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testElseIfStatementWithoutScopeStatementBody
     */
    public function testElseIfStatementWithoutScopeStatementBody(): void
    {
        $stmt = $this->getFirstElseIfStatementInFunction();
        static::assertInstanceOf(ASTForeachStatement::class, $stmt->getChild(1));
    }

    /**
     * testElseIfStatementAlternativeScopeHasExpectedStartLine
     */
    public function testElseIfStatementAlternativeScopeHasExpectedStartLine(): void
    {
        $stmt = $this->getFirstElseIfStatementInFunction();
        static::assertEquals(6, $stmt->getStartLine());
    }

    /**
     * testElseIfStatementAlternativeScopeHasExpectedStartColumn
     */
    public function testElseIfStatementAlternativeScopeHasExpectedStartColumn(): void
    {
        $stmt = $this->getFirstElseIfStatementInFunction();
        static::assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testElseIfStatementAlternativeScopeHasExpectedEndLine
     */
    public function testElseIfStatementAlternativeScopeHasExpectedEndLine(): void
    {
        $stmt = $this->getFirstElseIfStatementInFunction();
        static::assertEquals(11, $stmt->getEndLine());
    }

    /**
     * testElseIfStatementAlternativeScopeHasExpectedEndColumn
     */
    public function testElseIfStatementAlternativeScopeHasExpectedEndColumn(): void
    {
        $stmt = $this->getFirstElseIfStatementInFunction();
        static::assertEquals(9, $stmt->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstElseIfStatementInFunction(): ASTElseIfStatement
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTElseIfStatement::class
        );
    }
}
