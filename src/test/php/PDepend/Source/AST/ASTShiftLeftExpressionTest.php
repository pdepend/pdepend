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
 * @since 1.0.1
 */

namespace PDepend\Source\AST;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTShiftLeftExpression} class.
 *
 * @covers \PDepend\Source\AST\ASTShiftLeftExpression
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 1.0.1
 *
 * @group unittest
 */
class ASTShiftLeftExpressionTest extends ASTNodeTestCase
{
    /**
     * testShiftLeftExpressionReturnsExpectedImage
     */
    public function testShiftLeftExpressionReturnsExpectedImage(): void
    {
        $expr = new ASTShiftLeftExpression();
        static::assertEquals('<<', $expr->getImage());
    }

    /**
     * testShiftLeftExpression
     *
     * @return ASTShiftLeftExpression
     */
    public function testShiftLeftExpression()
    {
        $expr = $this->getFirstShiftLeftExpressionInFunction();
        static::assertInstanceOf(ASTShiftLeftExpression::class, $expr);

        return $expr;
    }

    /**
     * testShiftLeftExpressionHasExpectedStartLine
     *
     * @param ASTShiftLeftExpression $expr
     *
     * @depends testShiftLeftExpression
     */
    public function testShiftLeftExpressionHasExpectedStartLine($expr): void
    {
        static::assertEquals(6, $expr->getStartLine());
    }

    /**
     * testShiftLeftExpressionHasExpectedStartColumn
     *
     * @param ASTShiftLeftExpression $expr
     *
     * @depends testShiftLeftExpression
     */
    public function testShiftLeftExpressionHasExpectedStartColumn($expr): void
    {
        static::assertEquals(13, $expr->getStartColumn());
    }

    /**
     * testShiftLeftExpressionHasExpectedEndLine
     *
     * @param ASTShiftLeftExpression $expr
     *
     * @depends testShiftLeftExpression
     */
    public function testShiftLeftExpressionHasExpectedEndLine($expr): void
    {
        static::assertEquals(6, $expr->getEndLine());
    }

    /**
     * testShiftLeftExpressionHasExpectedEndColumn
     *
     * @param ASTShiftLeftExpression $expr
     *
     * @depends testShiftLeftExpression
     */
    public function testShiftLeftExpressionHasExpectedEndColumn($expr): void
    {
        static::assertEquals(14, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return ASTShiftLeftExpression
     */
    private function getFirstShiftLeftExpressionInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            ASTShiftLeftExpression::class
        );
    }
}
