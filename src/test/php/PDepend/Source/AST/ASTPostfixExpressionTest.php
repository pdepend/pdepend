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
 * Test case for the {@link ASTPostfixExpression} class.
 *
 * @covers \PDepend\Source\AST\ASTPostfixExpression
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTPostfixExpressionTest extends ASTNodeTestCase
{
    /**
     * testIncrementPostfixExpressionOnStaticClassMember
     */
    public function testIncrementPostfixExpressionOnStaticClassMember(): void
    {
        $expr = $this->getFirstPostfixExpressionInClass();
        $this->assertGraphEquals(
            $expr,
            [
                ASTMemberPrimaryPrefix::class,
                ASTClassOrInterfaceReference::class,
                ASTPropertyPostfix::class,
                ASTVariable::class,
            ]
        );
    }

    /**
     * testIncrementPostfixExpressionOnSelfClassMember
     */
    public function testIncrementPostfixExpressionOnSelfClassMember(): void
    {
        $expr = $this->getFirstPostfixExpressionInClass();
        $this->assertGraphEquals(
            $expr,
            [
                ASTMemberPrimaryPrefix::class,
                ASTSelfReference::class,
                ASTPropertyPostfix::class,
                ASTVariable::class,
            ]
        );
    }

    /**
     * testIncrementPostfixExpressionOnParentClassMember
     */
    public function testIncrementPostfixExpressionOnParentClassMember(): void
    {
        $expr = $this->getFirstPostfixExpressionInClass();
        $this->assertGraphEquals(
            $expr,
            [
                ASTMemberPrimaryPrefix::class,
                ASTParentReference::class,
                ASTPropertyPostfix::class,
                ASTVariable::class,
            ]
        );
    }

    /**
     * testIncrementPostfixExpressionOnThisObjectMember
     */
    public function testIncrementPostfixExpressionOnThisObjectMember(): void
    {
        $expr = $this->getFirstPostfixExpressionInClass();
        $this->assertGraphEquals(
            $expr,
            [
                ASTMemberPrimaryPrefix::class,
                ASTVariable::class,
                ASTPropertyPostfix::class,
                ASTIdentifier::class,
            ]
        );
    }

    /**
     * testIncrementPostfixExpressionOnFunctionPostfix
     */
    public function testIncrementPostfixExpressionOnFunctionPostfix(): void
    {
        $expr = $this->getFirstPostfixExpressionInFunction();
        $this->assertGraphEquals(
            $expr,
            [
                ASTFunctionPostfix::class,
                ASTIdentifier::class,
                ASTArguments::class,
                ASTVariable::class,
            ]
        );
    }

    /**
     * testIncrementPostfixExpressionOnVariableVariable
     */
    public function testIncrementPostfixExpressionOnVariableVariable(): void
    {
        $expr = $this->getFirstPostfixExpressionInFunction();
        $this->assertGraphEquals(
            $expr,
            [
                ASTVariableVariable::class,
                ASTVariableVariable::class,
                ASTVariable::class,
            ]
        );
    }

    /**
     * testIncrementPostfixExpressionOnCompoundVariable
     */
    public function testIncrementPostfixExpressionOnCompoundVariable(): void
    {
        $expr = $this->getFirstPostfixExpressionInFunction();
        $this->assertGraphEquals(
            $expr,
            [
                ASTCompoundVariable::class,
                ASTConstant::class,
            ]
        );
    }

    /**
     * testIncrementPostfixExpressionOnObjectMethodPostfix
     */
    public function testIncrementPostfixExpressionOnObjectMethodPostfix(): void
    {
        $expr = $this->getFirstPostfixExpressionInFunction();
        $this->assertGraphEquals(
            $expr,
            [
                ASTMemberPrimaryPrefix::class,
                ASTVariable::class,
                ASTMethodPostfix::class,
                ASTIdentifier::class,
                ASTArguments::class,
                ASTVariable::class,
            ]
        );
    }

    /**
     * testIncrementPostfixExpressionOnStaticMethodPostfix
     */
    public function testIncrementPostfixExpressionOnStaticMethodPostfix(): void
    {
        $expr = $this->getFirstPostfixExpressionInFunction();
        $this->assertGraphEquals(
            $expr,
            [
                ASTMemberPrimaryPrefix::class,
                ASTClassOrInterfaceReference::class,
                ASTMethodPostfix::class,
                ASTIdentifier::class,
                ASTArguments::class,
            ]
        );
    }

    /**
     * testIncrementPostfixExpressionArrayPropertyPostfix
     */
    public function testIncrementPostfixExpressionArrayPropertyPostfix(): void
    {
        $expr = $this->getFirstPostfixExpressionInClass()->getParent();
        static::assertNotNull($expr);
        $this->assertGraphEquals(
            $expr,
            [
                ASTPostfixExpression::class,
                ASTMemberPrimaryPrefix::class,
                ASTVariable::class,
                ASTPropertyPostfix::class,
                ASTArrayIndexExpression::class,
                ASTIdentifier::class,
                ASTVariable::class,
            ]
        );
    }

    /**
     * testIncrementPostfixExpressionHasExpectedStartLine
     */
    public function testIncrementPostfixExpressionHasExpectedStartLine(): void
    {
        $expr = $this->getFirstPostfixExpressionInFunction();
        static::assertEquals(5, $expr->getStartLine());
    }

    /**
     * testIncrementPostfixExpressionHasExpectedStartColumn
     */
    public function testIncrementPostfixExpressionHasExpectedStartColumn(): void
    {
        $expr = $this->getFirstPostfixExpressionInFunction();
        static::assertEquals(9, $expr->getStartColumn());
    }

    /**
     * testIncrementPostfixExpressionHasExpectedEndLine
     */
    public function testIncrementPostfixExpressionHasExpectedEndLine(): void
    {
        $expr = $this->getFirstPostfixExpressionInFunction();
        static::assertEquals(7, $expr->getEndLine());
    }

    /**
     * testIncrementPostfixExpressionHasExpectedEndColumn
     */
    public function testIncrementPostfixExpressionHasExpectedEndColumn(): void
    {
        $expr = $this->getFirstPostfixExpressionInFunction();
        static::assertEquals(14, $expr->getEndColumn());
    }

    /**
     * testDecrementPostfixExpressionArrayPropertyPostfix
     */
    public function testDecrementPostfixExpressionArrayPropertyPostfix(): void
    {
        $expr = $this->getFirstPostfixExpressionInClass()->getParent();
        static::assertNotNull($expr);
        $this->assertGraphEquals(
            $expr,
            [
                ASTPostfixExpression::class,
                ASTMemberPrimaryPrefix::class,
                ASTVariable::class,
                ASTPropertyPostfix::class,
                ASTArrayIndexExpression::class,
                ASTIdentifier::class,
                ASTVariable::class,
            ]
        );
    }

    /**
     * testDecrementPostfixExpressionHasExpectedStartLine
     */
    public function testDecrementPostfixExpressionHasExpectedStartLine(): void
    {
        $expr = $this->getFirstPostfixExpressionInFunction();
        static::assertEquals(7, $expr->getStartLine());
    }

    /**
     * testDecrementPostfixExpressionHasExpectedStartColumn
     */
    public function testDecrementPostfixExpressionHasExpectedStartColumn(): void
    {
        $expr = $this->getFirstPostfixExpressionInFunction();
        static::assertEquals(17, $expr->getStartColumn());
    }

    /**
     * testDecrementPostfixExpressionHasExpectedEndLine
     */
    public function testDecrementPostfixExpressionHasExpectedEndLine(): void
    {
        $expr = $this->getFirstPostfixExpressionInFunction();
        static::assertEquals(9, $expr->getEndLine());
    }

    /**
     * testDecrementPostfixExpressionHasExpectedEndColumn
     */
    public function testDecrementPostfixExpressionHasExpectedEndColumn(): void
    {
        $expr = $this->getFirstPostfixExpressionInFunction();
        static::assertEquals(10, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstPostfixExpressionInClass(): ASTPostfixExpression
    {
        return $this->getFirstNodeOfTypeInClass(
            ASTPostfixExpression::class
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstPostfixExpressionInFunction(): ASTPostfixExpression
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTPostfixExpression::class
        );
    }
}
