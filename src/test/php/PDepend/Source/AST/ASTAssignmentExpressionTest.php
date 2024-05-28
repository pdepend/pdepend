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
 * Test case for the {@link \PDepend\Source\AST\ASTAssignmentExpression} class.
 *
 * @covers \PDepend\Source\AST\ASTAssignmentExpression
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTAssignmentExpressionTest extends ASTNodeTestCase
{
    /**
     * testAssignmentExpressionFromMethodInvocation
     */
    public function testAssignmentExpressionFromMethodInvocation(): void
    {
        $this->assertGraphEquals(
            $this->getFirstAssignmentExpressionInFunction(),
            [
                ASTVariable::class,
                ASTMemberPrimaryPrefix::class,
                ASTVariable::class,
                ASTMethodPostfix::class,
                ASTIdentifier::class,
                ASTArguments::class,
            ]
        );
    }

    /**
     * testAssignmentExpressionFromPropertyAccess
     */
    public function testAssignmentExpressionFromPropertyAccess(): void
    {
        $this->assertGraphEquals(
            $this->getFirstAssignmentExpressionInFunction(),
            [
                ASTVariable::class,
                ASTMemberPrimaryPrefix::class,
                ASTVariable::class,
                ASTPropertyPostfix::class,
                ASTIdentifier::class,
            ]
        );
    }

    /**
     * testAssignmentExpressionFromFunctionReturnValue
     */
    public function testAssignmentExpressionFromFunctionReturnValue(): void
    {
        $this->assertGraphEquals(
            $this->getFirstAssignmentExpressionInFunction(),
            [
                ASTVariable::class,
                ASTMemberPrimaryPrefix::class,
                ASTFunctionPostfix::class,
                ASTIdentifier::class,
                ASTArguments::class,
                ASTPropertyPostfix::class,
                ASTIdentifier::class,
            ]
        );
    }

    /**
     * Tests the resulting object graph.
     */
    public function testAssignmentExpressionGraphForIntegerLiteral(): void
    {
        $this->assertGraphEquals(
            $this->getFirstAssignmentExpressionInFunction(),
            [
                ASTVariable::class,
                ASTLiteral::class,
            ]
        );
    }

    /**
     * Tests the resulting object graph.
     */
    public function testAssignmentExpressionGraphForFloatLiteral(): void
    {
        $this->assertGraphEquals(
            $this->getFirstAssignmentExpressionInFunction(),
            [
                ASTVariable::class,
                ASTLiteral::class,
            ]
        );
    }

    /**
     * testAssignmentExpressionWithEqual
     *
     * @since 1.0.1
     */
    public function testAssignmentExpressionWithEqual(): void
    {
        $expr = $this->getFirstAssignmentExpressionInFunction();
        static::assertEquals('=', $expr->getImage());
    }

    /**
     * testAssignmentExpressionWithAndEqual
     *
     * @since 1.0.1
     */
    public function testAssignmentExpressionWithAndEqual(): void
    {
        $expr = $this->getFirstAssignmentExpressionInFunction();
        static::assertEquals('&=', $expr->getImage());
    }

    /**
     * testAssignmentExpressionWithConcatEqual
     *
     * @since 1.0.1
     */
    public function testAssignmentExpressionWithConcatEqual(): void
    {
        $expr = $this->getFirstAssignmentExpressionInFunction();
        static::assertEquals('.=', $expr->getImage());
    }

    /**
     * testAssignmentExpressionWithDivEqual
     *
     * @since 1.0.1
     */
    public function testAssignmentExpressionWithDivEqual(): void
    {
        $expr = $this->getFirstAssignmentExpressionInFunction();
        static::assertEquals('/=', $expr->getImage());
    }

    /**
     * testAssignmentExpressionWithMinusEqual
     *
     * @since 1.0.1
     */
    public function testAssignmentExpressionWithMinusEqual(): void
    {
        $expr = $this->getFirstAssignmentExpressionInFunction();
        static::assertEquals('-=', $expr->getImage());
    }

    /**
     * testAssignmentExpressionWithModEqual
     *
     * @since 1.0.1
     */
    public function testAssignmentExpressionWithModEqual(): void
    {
        $expr = $this->getFirstAssignmentExpressionInFunction();
        static::assertEquals('%=', $expr->getImage());
    }

    /**
     * testAssignmentExpressionWithMulEqual
     *
     * @since 1.0.1
     */
    public function testAssignmentExpressionWithMulEqual(): void
    {
        $expr = $this->getFirstAssignmentExpressionInFunction();
        static::assertEquals('*=', $expr->getImage());
    }

    /**
     * testAssignmentExpressionWithOrEqual
     *
     * @since 1.0.1
     */
    public function testAssignmentExpressionWithOrEqual(): void
    {
        $expr = $this->getFirstAssignmentExpressionInFunction();
        static::assertEquals('|=', $expr->getImage());
    }

    /**
     * testAssignmentExpressionWithPlusEqual
     *
     * @since 1.0.1
     */
    public function testAssignmentExpressionWithPlusEqual(): void
    {
        $expr = $this->getFirstAssignmentExpressionInFunction();
        static::assertEquals('+=', $expr->getImage());
    }

    /**
     * testAssignmentExpressionWithXorEqual
     *
     * @since 1.0.1
     */
    public function testAssignmentExpressionWithXorEqual(): void
    {
        $expr = $this->getFirstAssignmentExpressionInFunction();
        static::assertEquals('^=', $expr->getImage());
    }

    /**
     * testAssignmentExpressionWithShiftLeftEqual
     *
     * @since 1.0.1
     */
    public function testAssignmentExpressionWithShiftLeftEqual(): void
    {
        $expr = $this->getFirstAssignmentExpressionInFunction();
        static::assertEquals('<<=', $expr->getImage());
    }

    /**
     * testAssignmentExpressionWithShiftRightEqual
     *
     * @since 1.0.1
     */
    public function testAssignmentExpressionWithShiftRightEqual(): void
    {
        $expr = $this->getFirstAssignmentExpressionInFunction();
        static::assertEquals('>>=', $expr->getImage());
    }

    /**
     * testVariableAssignmentExpression
     *
     * @since 1.0.1
     */
    public function testVariableAssignmentExpression(): ASTAssignmentExpression
    {
        $expr = $this->getFirstAssignmentExpressionInFunction();
        static::assertInstanceOf(ASTAssignmentExpression::class, $expr);

        return $expr;
    }

    /**
     * Tests the start line of an assignment-expression.
     *
     * @depends testVariableAssignmentExpression
     */
    public function testVariableAssignmentExpressionHasExpectedStartLine(ASTAssignmentExpression $expr): void
    {
        static::assertEquals(4, $expr->getStartLine());
    }

    /**
     * Tests the start column of an assignment-expression.
     *
     * @depends testVariableAssignmentExpression
     */
    public function testVariableAssignmentExpressionHasExpectedStartColumn(ASTAssignmentExpression $expr): void
    {
        static::assertEquals(5, $expr->getStartColumn());
    }

    /**
     * Tests the end line of an assignment-expression.
     *
     * @depends testVariableAssignmentExpression
     */
    public function testVariableAssignmentExpressionHasExpectedEndLine(ASTAssignmentExpression $expr): void
    {
        static::assertEquals(6, $expr->getEndLine());
    }

    /**
     * Tests the end column of an assignment-expression.
     *
     * @depends testVariableAssignmentExpression
     */
    public function testVariableAssignmentExpressionHasExpectedEndColumn(ASTAssignmentExpression $expr): void
    {
        static::assertEquals(5, $expr->getEndColumn());
    }

    /**
     * testStaticPropertyAssignmentExpression
     *
     * @since 1.0.1
     */
    public function testStaticPropertyAssignmentExpression(): ASTAssignmentExpression
    {
        $expr = $this->getFirstAssignmentExpressionInFunction();
        static::assertInstanceOf(ASTAssignmentExpression::class, $expr);

        return $expr;
    }

    /**
     * Tests the start line of an assignment-expression.
     *
     * @depends testStaticPropertyAssignmentExpression
     */
    public function testStaticPropertyAssignmentExpressionHasExpectedStartLine(ASTAssignmentExpression $expr): void
    {
        static::assertEquals(4, $expr->getStartLine());
    }

    /**
     * Tests the start column of an assignment-expression.
     *
     * @depends testStaticPropertyAssignmentExpression
     */
    public function testStaticPropertyAssignmentExpressionHasExpectedStartColumn(ASTAssignmentExpression $expr): void
    {
        static::assertEquals(5, $expr->getStartColumn());
    }

    /**
     * Tests the end line of an assignment-expression.
     *
     * @depends testStaticPropertyAssignmentExpression
     */
    public function testStaticPropertyAssignmentExpressionHasExpectedEndLine(ASTAssignmentExpression $expr): void
    {
        static::assertEquals(4, $expr->getEndLine());
    }

    /**
     * Tests the end column of an assignment-expression.
     *
     * @depends testStaticPropertyAssignmentExpression
     */
    public function testStaticPropertyAssignmentExpressionHasExpectedEndColumn(ASTAssignmentExpression $expr): void
    {
        static::assertEquals(60, $expr->getEndColumn());
    }

    /**
     * testObjectPropertyAssignmentExpression
     *
     * @since 1.0.1
     */
    public function testObjectPropertyAssignmentExpression(): ASTAssignmentExpression
    {
        $expr = $this->getFirstAssignmentExpressionInFunction();
        static::assertInstanceOf(ASTAssignmentExpression::class, $expr);

        return $expr;
    }

    /**
     * Tests the start line of an assignment-expression.
     *
     * @depends testObjectPropertyAssignmentExpression
     */
    public function testObjectPropertyAssignmentExpressionHasExpectedStartLine(ASTAssignmentExpression $expr): void
    {
        static::assertEquals(4, $expr->getStartLine());
    }

    /**
     * Tests the start column of an assignment-expression.
     *
     * @depends testObjectPropertyAssignmentExpression
     */
    public function testObjectPropertyAssignmentExpressionHasExpectedStartColumn(ASTAssignmentExpression $expr): void
    {
        static::assertEquals(5, $expr->getStartColumn());
    }

    /**
     * Tests the end line of an assignment-expression.
     *
     * @depends testObjectPropertyAssignmentExpression
     */
    public function testObjectPropertyAssignmentExpressionHasExpectedEndLine(ASTAssignmentExpression $expr): void
    {
        static::assertEquals(5, $expr->getEndLine());
    }

    /**
     * Tests the end column of an assignment-expression.
     *
     * @depends testObjectPropertyAssignmentExpression
     */
    public function testObjectPropertyAssignmentExpressionHasExpectedEndColumn(ASTAssignmentExpression $expr): void
    {
        static::assertEquals(15, $expr->getEndColumn());
    }

    /**
     * testChainedPropertyAssignmentExpression
     *
     * @since 1.0.1
     */
    public function testChainedPropertyAssignmentExpression(): ASTAssignmentExpression
    {
        $expr = $this->getFirstAssignmentExpressionInFunction();
        static::assertInstanceOf(ASTAssignmentExpression::class, $expr);

        return $expr;
    }

    /**
     * Tests the start line of an assignment-expression.
     *
     * @depends testChainedPropertyAssignmentExpression
     */
    public function testChainedPropertyAssignmentExpressionHasExpectedStartLine(ASTAssignmentExpression $expr): void
    {
        static::assertEquals(4, $expr->getStartLine());
    }

    /**
     * Tests the start column of an assignment-expression.
     *
     * @depends testChainedPropertyAssignmentExpression
     */
    public function testChainedPropertyAssignmentExpressionHasExpectedStartColumn(ASTAssignmentExpression $expr): void
    {
        static::assertEquals(5, $expr->getStartColumn());
    }

    /**
     * Tests the end column of an assignment-expression.
     *
     * @depends testChainedPropertyAssignmentExpression
     */
    public function testChainedPropertyAssignmentExpressionHasExpectedEndColumn(ASTAssignmentExpression $expr): void
    {
        static::assertEquals(23, $expr->getEndColumn());
    }

    /**
     * Tests the end line of an assignment-expression.
     *
     * @depends testChainedPropertyAssignmentExpression
     */
    public function testChainedPropertyAssignmentExpressionHasExpectedEndLine(ASTAssignmentExpression $expr): void
    {
        static::assertEquals(8, $expr->getEndLine());
    }

    /**
     * Returns a test assignment-expression.
     */
    private function getFirstAssignmentExpressionInFunction(): ASTAssignmentExpression
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTAssignmentExpression::class
        );
    }
}
