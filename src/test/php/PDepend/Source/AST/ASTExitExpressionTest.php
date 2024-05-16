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
 * Test case for the {@link \PDepend\Source\AST\ASTExitExpression} class.
 *
 * @covers \PDepend\Source\AST\ASTExitExpression
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTExitExpressionTest extends ASTNodeTestCase
{
    /**
     * testExitExpressionWithExitCode
     *
     * @return ASTExitExpression
     * @since 1.0.1
     */
    public function testExitExpressionWithExitCode()
    {
        $expr = $this->getFirstExitExpressionInFunction();
        static::assertInstanceOf(ASTExitExpression::class, $expr);

        return $expr;
    }

    /**
     * testExitExpressionWithExitCodeHasExpectedStartLine
     *
     * @param ASTExitExpression $expr
     * @since 1.0.1
     *
     * @depends testExitExpressionWithExitCode
     */
    public function testExitExpressionWithExitCodeHasExpectedStartLine($expr): void
    {
        static::assertEquals(4, $expr->getStartLine());
    }

    /**
     * testExitExpressionWithExitCodeHasExpectedEndLine
     *
     * @param ASTExitExpression $expr
     * @since 1.0.1
     *
     * @depends testExitExpressionWithExitCode
     */
    public function testExitExpressionWithExitCodeHasExpectedEndLine($expr): void
    {
        static::assertEquals(6, $expr->getEndLine());
    }

    /**
     * testExitExpressionWithExitCodeHasExpectedStartColumn
     *
     * @param ASTExitExpression $expr
     * @since 1.0.1
     *
     * @depends testExitExpressionWithExitCode
     */
    public function testExitExpressionWithExitCodeHasExpectedStartColumn($expr): void
    {
        static::assertEquals(5, $expr->getStartColumn());
    }

    /**
     * testExitExpressionWithExitCodeHasExpectedEndColumn
     *
     * @param ASTExitExpression $expr
     * @since 1.0.1
     *
     * @depends testExitExpressionWithExitCode
     */
    public function testExitExpressionWithExitCodeHasExpectedEndColumn($expr): void
    {
        static::assertEquals(5, $expr->getEndColumn());
    }

    /**
     * testExitExpressionWithEmptyArgs
     *
     * @return ASTExitExpression
     * @since 1.0.1
     */
    public function testExitExpressionWithEmptyArgs()
    {
        $expr = $this->getFirstExitExpressionInFunction();
        static::assertInstanceOf(ASTExitExpression::class, $expr);

        return $expr;
    }

    /**
     * testExitExpressionWithEmptyArgsHasExpectedStartLine
     *
     * @param ASTExitExpression $expr
     * @since 1.0.1
     *
     * @depends testExitExpressionWithEmptyArgs
     */
    public function testExitExpressionWithEmptyArgsHasExpectedStartLine($expr): void
    {
        static::assertEquals(4, $expr->getStartLine());
    }

    /**
     * testExitExpressionWithEmptyArgsHasExpectedEndLine
     *
     * @param ASTExitExpression $expr
     * @since 1.0.1
     *
     * @depends testExitExpressionWithEmptyArgs
     */
    public function testExitExpressionWithEmptyArgsHasExpectedEndLine($expr): void
    {
        static::assertEquals(4, $expr->getEndLine());
    }

    /**
     * testExitExpressionWithEmptyArgsHasExpectedStartColumn
     *
     * @param ASTExitExpression $expr
     * @since 1.0.1
     *
     * @depends testExitExpressionWithEmptyArgs
     */
    public function testExitExpressionWithEmptyArgsHasExpectedStartColumn($expr): void
    {
        static::assertEquals(5, $expr->getStartColumn());
    }

    /**
     * testExitExpressionWithEmptyArgsHasExpectedEndColumn
     *
     * @param ASTExitExpression $expr
     * @since 1.0.1
     *
     * @depends testExitExpressionWithEmptyArgs
     */
    public function testExitExpressionWithEmptyArgsHasExpectedEndColumn($expr): void
    {
        static::assertEquals(10, $expr->getEndColumn());
    }

    /**
     * testExitExpressionWithoutArgs
     *
     * @return ASTExitExpression
     * @since 1.0.1
     */
    public function testExitExpressionWithoutArgs()
    {
        $expr = $this->getFirstExitExpressionInFunction();
        static::assertInstanceOf(ASTExitExpression::class, $expr);

        return $expr;
    }

    /**
     * testExitExpressionWithoutArgsHasExpectedStartLine
     *
     * @param ASTExitExpression $expr
     *
     * @depends testExitExpressionWithoutArgs
     */
    public function testExitExpressionWithoutArgsHasExpectedStartLine($expr): void
    {
        static::assertEquals(4, $expr->getStartLine());
    }

    /**
     * testExitExpressionWithoutArgsHasExpectedStartColumn
     *
     * @param ASTExitExpression $expr
     *
     * @depends testExitExpressionWithoutArgs
     */
    public function testExitExpressionWithoutArgsHasExpectedStartColumn($expr): void
    {
        static::assertEquals(5, $expr->getStartColumn());
    }

    /**
     * testExitExpressionHasExpectedEndLineWithoutArgs
     *
     * @param ASTExitExpression $expr
     *
     * @depends testExitExpressionWithoutArgs
     */
    public function testExitExpressionWithoutArgsHasExpectedEndLine($expr): void
    {
        static::assertEquals(4, $expr->getEndLine());
    }

    /**
     * testExitExpressionHasExpectedEndColumnWithoutArgs
     *
     * @param ASTExitExpression $expr
     *
     * @depends testExitExpressionWithoutArgs
     */
    public function testExitExpressionWithoutArgsHasExpectedEndColumn($expr): void
    {
        static::assertEquals(8, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return ASTExitExpression
     */
    private function getFirstExitExpressionInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            ASTExitExpression::class
        );
    }
}
