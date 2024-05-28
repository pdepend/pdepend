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
 * Test case for the {@link \PDepend\Source\AST\ASTUnaryExpression} class.
 *
 * @covers \PDepend\Source\AST\ASTUnaryExpression
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTUnaryExpressionTest extends ASTNodeTestCase
{
    /**
     * testUnaryExpression
     *
     * @since 1.0.2
     */
    public function testUnaryExpression(): ASTUnaryExpression
    {
        $expr = $this->getFirstUnaryExpressionInFunction();
        static::assertInstanceOf(ASTUnaryExpression::class, $expr);

        return $expr;
    }

    /**
     * testUnaryExpressionHasExpectedStartLine
     *
     * @depends testUnaryExpression
     */
    public function testUnaryExpressionHasExpectedStartLine(ASTUnaryExpression $expr): void
    {
        static::assertSame(4, $expr->getStartLine());
    }

    /**
     * testUnaryExpressionHasExpectedEndLine
     *
     * @depends testUnaryExpression
     */
    public function testUnaryExpressionHasExpectedEndLine(ASTUnaryExpression $expr): void
    {
        static::assertSame(5, $expr->getEndLine());
    }

    /**
     * testUnaryExpressionHasExpectedStartColumn
     *
     * @depends testUnaryExpression
     */
    public function testUnaryExpressionHasExpectedStartColumn(ASTUnaryExpression $expr): void
    {
        static::assertSame(22, $expr->getStartColumn());
    }

    /**
     * testUnaryExpressionHasExpectedEndColumn
     *
     * @depends testUnaryExpression
     */
    public function testUnaryExpressionHasExpectedEndColumn(ASTUnaryExpression $expr): void
    {
        static::assertSame(14, $expr->getEndColumn());
    }

    public function testUnaryExpressionNot(): ASTUnaryExpression
    {
        $expr = $this->getFirstUnaryExpressionInFunction();
        static::assertInstanceOf(ASTUnaryExpression::class, $expr);

        return $expr;
    }

    /**
     * @depends testUnaryExpressionNot
     */
    public function testUnaryExpressionNotHasExpectedStartLine(ASTUnaryExpression $expr): void
    {
        static::assertSame(4, $expr->getStartLine());
    }

    /**
     * @depends testUnaryExpressionNot
     */
    public function testUnaryExpressionNotHasExpectedEndLine(ASTUnaryExpression $expr): void
    {
        static::assertSame(6, $expr->getEndLine());
    }

    /**
     * @depends testUnaryExpressionNot
     */
    public function testUnaryExpressionNotHasExpectedStartColumn(ASTUnaryExpression $expr): void
    {
        static::assertSame(12, $expr->getStartColumn());
    }

    /**
     * @depends testUnaryExpressionNot
     */
    public function testUnaryExpressionNotHasExpectedEndColumn(ASTUnaryExpression $expr): void
    {
        static::assertSame(5, $expr->getEndColumn());
    }

    public function testUnaryExpressionSuppressWarning(): ASTUnaryExpression
    {
        $expr = $this->getFirstUnaryExpressionInFunction();
        static::assertInstanceOf(ASTUnaryExpression::class, $expr);

        return $expr;
    }

    /**
     * @depends testUnaryExpressionSuppressWarning
     */
    public function testUnaryExpressionSuppressWarningHasExpectedStartLine(ASTUnaryExpression $expr): void
    {
        static::assertSame(4, $expr->getStartLine());
    }

    /**
     * @depends testUnaryExpressionSuppressWarning
     */
    public function testUnaryExpressionSuppressWarningHasExpectedEndLine(ASTUnaryExpression $expr): void
    {
        static::assertSame(4, $expr->getEndLine());
    }

    /**
     * @depends testUnaryExpressionSuppressWarning
     */
    public function testUnaryExpressionSuppressWarningHasExpectedStartColumn(ASTUnaryExpression $expr): void
    {
        static::assertSame(12, $expr->getStartColumn());
    }

    /**
     * @depends testUnaryExpressionSuppressWarning
     */
    public function testUnaryExpressionSuppressWarningHasExpectedEndColumn(ASTUnaryExpression $expr): void
    {
        static::assertSame(47, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstUnaryExpressionInFunction(): ASTUnaryExpression
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTUnaryExpression::class
        );
    }
}
