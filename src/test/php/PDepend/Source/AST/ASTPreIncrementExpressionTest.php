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
 * Test case for the {@link \PDepend\Source\AST\ASTPreIncrementExpression} class.
 *
 * @covers \PDepend\Source\AST\ASTPreIncrementExpression
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\Language\PHP\PHPBuilder
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTPreIncrementExpressionTest extends ASTNodeTestCase
{
    /**
     * testPreIncrementExpressionOnStaticClassMember
     */
    public function testPreIncrementExpressionOnStaticClassMember(): void
    {
        $expr = $this->getFirstPreIncrementExpressionInFunction();
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
     * testPreIncrementExpressionOnSelfClassMember
     */
    public function testPreIncrementExpressionOnSelfClassMember(): void
    {
        $expr = $this->getFirstPreIncrementExpressionInClass();
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
     * testPreIncrementExpressionOnParentClassMember
     */
    public function testPreIncrementExpressionOnParentClassMember(): void
    {
        $expr = $this->getFirstPreIncrementExpressionInClass();
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
     * testPreIncrementExpressionOnFunctionPostfix
     */
    public function testPreIncrementExpressionOnFunctionPostfix(): void
    {
        $expr = $this->getFirstPreIncrementExpressionInFunction();
        $this->assertGraphEquals(
            $expr,
            [
                ASTFunctionPostfix::class,
                ASTIdentifier::class,
                ASTArguments::class,
            ]
        );
    }

    /**
     * testPreIncrementExpressionOnStaticVariableMember
     */
    public function testPreIncrementExpressionOnStaticVariableMember(): void
    {
        $expr = $this->getFirstPreIncrementExpressionInFunction();
        $this->assertGraphEquals(
            $expr,
            [
                ASTMemberPrimaryPrefix::class,
                ASTVariable::class,
                ASTPropertyPostfix::class,
                ASTVariable::class,
            ]
        );
    }

    /**
     * testPreIncrementExpressionsInArithmeticOperation
     */
    public function testPreIncrementExpressionsInArithmeticOperation(): void
    {
        $exprs = $this->getFirstClassForTestCase()
            ->getMethods()
            ->current()
            ->findChildrenOfType(ASTPreIncrementExpression::class);

        static::assertCount(2, $exprs);
    }

    /**
     * testPreIncrementExpressionHasExpectedStartLine
     */
    public function testPreIncrementExpressionHasExpectedStartLine(): void
    {
        $expr = $this->getFirstPreIncrementExpressionInFunction();
        static::assertEquals(4, $expr->getStartLine());
    }

    /**
     * testPreIncrementExpressionHasExpectedStartColumn
     */
    public function testPreIncrementExpressionHasExpectedStartColumn(): void
    {
        $expr = $this->getFirstPreIncrementExpressionInFunction();
        static::assertEquals(13, $expr->getStartColumn());
    }

    /**
     * testPreIncrementExpressionHasExpectedEndLine
     */
    public function testPreIncrementExpressionHasExpectedEndLine(): void
    {
        $expr = $this->getFirstPreIncrementExpressionInFunction();
        static::assertEquals(4, $expr->getEndLine());
    }

    /**
     * testPreIncrementExpressionHasExpectedEndColumn
     */
    public function testPreIncrementExpressionHasExpectedEndColumn(): void
    {
        $expr = $this->getFirstPreIncrementExpressionInFunction();
        static::assertEquals(20, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstPreIncrementExpressionInClass(): ASTPreIncrementExpression
    {
        return $this->getFirstNodeOfTypeInClass(
            ASTPreIncrementExpression::class
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstPreIncrementExpressionInFunction(): ASTPreIncrementExpression
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTPreIncrementExpression::class
        );
    }
}
