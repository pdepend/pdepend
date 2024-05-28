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
 * Test case for the {@link \PDepend\Source\AST\ASTExpression} class.
 *
 * @covers \PDepend\Source\AST\ASTExpression
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTExpressionTest extends ASTNodeTestCase
{
    /**
     * testExpressionHasExpectedNumberOfChildNodes
     */
    public function testExpressionHasExpectedNumberOfChildNodes(): void
    {
        $expr = $this->getFirstExpressionInFunction();
        static::assertCount(5, $expr->getChild(0)->getChildren());
    }

    /**
     * Tests the generated object graph of an expression node.
     */
    public function testExpressionGraphWithBooleanExpressions(): void
    {
        $expr = $this->getFirstExpressionInFunction();
        $expected = [
            ASTExpression::class,
            ASTVariable::class,
            ASTBooleanAndExpression::class,
            ASTVariable::class,
            ASTBooleanOrExpression::class,
            ASTVariable::class,
        ];

        $this->assertGraphEquals($expr, $expected);
    }

    /**
     * testExpressionHasExpectedStartLine
     */
    public function testExpressionHasExpectedStartLine(): void
    {
        $expr = $this->getFirstExpressionInFunction();
        static::assertEquals(4, $expr->getStartLine());
    }

    /**
     * testExpressionHasExpectedStartColumn
     */
    public function testExpressionHasExpectedStartColumn(): void
    {
        $expr = $this->getFirstExpressionInFunction();
        static::assertEquals(8, $expr->getStartColumn());
    }

    /**
     * testExpressionHasExpectedEndLine
     */
    public function testExpressionHasExpectedEndLine(): void
    {
        $expr = $this->getFirstExpressionInFunction();
        static::assertEquals(6, $expr->getEndLine());
    }

    /**
     * testExpressionHasExpectedEndColumn
     */
    public function testExpressionHasExpectedEndColumn(): void
    {
        $expr = $this->getFirstExpressionInFunction();
        static::assertEquals(14, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstExpressionInFunction(): ASTExpression
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTExpression::class
        );
    }
}
