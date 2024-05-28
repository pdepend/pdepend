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
 * @since 0.9.12
 */

namespace PDepend\Source\AST;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTRequireExpression} class.
 *
 * @covers \PDepend\Source\AST\ASTRequireExpression
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 0.9.12
 *
 * @group unittest
 */
class ASTRequireExpressionTest extends ASTNodeTestCase
{
    /**
     * testIsOnceReturnsFalseByDefault
     */
    public function testIsOnceReturnsFalseByDefault(): void
    {
        $expr = new ASTRequireExpression();
        static::assertFalse($expr->isOnce());
    }

    /**
     * testIsOnceReturnsTrueForRequireOnceExpression
     */
    public function testIsOnceReturnsTrueForRequireOnceExpression(): void
    {
        $expr = $this->getFirstRequireExpressionInFunction();
        static::assertTrue($expr->isOnce());
    }

    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames(): void
    {
        $expr = new ASTRequireExpression();
        static::assertEquals(
            [
                'once',
                'comment',
                'metadata',
                'nodes',
            ],
            $expr->__sleep()
        );
    }

    /**
     * testRequireExpression
     *
     * @since 1.0.2
     */
    public function testRequireExpression(): ASTRequireExpression
    {
        $expr = $this->getFirstRequireExpressionInFunction();
        static::assertInstanceOf(ASTRequireExpression::class, $expr);

        return $expr;
    }

    /**
     * testRequireExpressionHasExpectedStartLine
     *
     * @depends testRequireExpression
     */
    public function testRequireExpressionHasExpectedStartLine(ASTRequireExpression $expr): void
    {
        static::assertEquals(4, $expr->getStartLine());
    }

    /**
     * testRequireExpressionHasExpectedStartColumn
     *
     * @depends testRequireExpression
     */
    public function testRequireExpressionHasExpectedStartColumn(ASTRequireExpression $expr): void
    {
        static::assertEquals(5, $expr->getStartColumn());
    }

    /**
     * testRequireExpressionHasExpectedEndLine
     *
     * @depends testRequireExpression
     */
    public function testRequireExpressionHasExpectedEndLine(ASTRequireExpression $expr): void
    {
        static::assertEquals(4, $expr->getEndLine());
    }

    /**
     * testRequireExpressionHasExpectedEndColumn
     *
     * @depends testRequireExpression
     */
    public function testRequireExpressionHasExpectedEndColumn(ASTRequireExpression $expr): void
    {
        static::assertEquals(35, $expr->getEndColumn());
    }

    /**
     * testRequireExpressionWithParenthesis
     *
     * @since 1.0.2
     */
    public function testRequireExpressionWithParenthesis(): ASTRequireExpression
    {
        $expr = $this->getFirstRequireExpressionInFunction();
        static::assertInstanceOf(ASTRequireExpression::class, $expr);

        return $expr;
    }

    /**
     * testRequireExpressionWithParenthesisHasExpectedStartLine
     *
     * @depends testRequireExpressionWithParenthesis
     */
    public function testRequireExpressionWithParenthesisHasExpectedStartLine(ASTRequireExpression $expr): void
    {
        static::assertEquals(4, $expr->getStartLine());
    }

    /**
     * testRequireExpressionWithParenthesisHasExpectedStartColumn
     *
     * @depends testRequireExpressionWithParenthesis
     */
    public function testRequireExpressionWithParenthesisHasExpectedStartColumn(ASTRequireExpression $expr): void
    {
        static::assertEquals(5, $expr->getStartColumn());
    }

    /**
     * testRequireExpressionWithParenthesisHasExpectedEndLine
     *
     * @depends testRequireExpressionWithParenthesis
     */
    public function testRequireExpressionWithParenthesisHasExpectedEndLine(ASTRequireExpression $expr): void
    {
        static::assertEquals(6, $expr->getEndLine());
    }

    /**
     * testRequireExpressionWithParenthesisHasExpectedEndColumn
     *
     * @depends testRequireExpressionWithParenthesis
     */
    public function testRequireExpressionWithParenthesisHasExpectedEndColumn(ASTRequireExpression $expr): void
    {
        static::assertEquals(5, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstRequireExpressionInFunction(): ASTRequireExpression
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTRequireExpression::class
        );
    }
}
