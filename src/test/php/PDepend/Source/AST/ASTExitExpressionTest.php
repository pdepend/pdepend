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
     * @since 1.0.1
     */
    public function testExitExpressionWithExitCode(): ASTExitExpression
    {
        $expr = $this->getFirstExitExpressionInFunction();
        static::assertInstanceOf(ASTExitExpression::class, $expr);

        return $expr;
    }

    /**
     * testExitExpressionWithExitCodeHasExpectedStartLine
     *
     * @since 1.0.1
     *
     * @depends testExitExpressionWithExitCode
     */
    public function testExitExpressionWithExitCodeHasExpectedStartLine(ASTExitExpression $expr): void
    {
        static::assertEquals(4, $expr->getStartLine());
    }

    /**
     * testExitExpressionWithExitCodeHasExpectedEndLine
     *
     * @since 1.0.1
     *
     * @depends testExitExpressionWithExitCode
     */
    public function testExitExpressionWithExitCodeHasExpectedEndLine(ASTExitExpression $expr): void
    {
        static::assertEquals(6, $expr->getEndLine());
    }

    /**
     * testExitExpressionWithExitCodeHasExpectedStartColumn
     *
     * @since 1.0.1
     *
     * @depends testExitExpressionWithExitCode
     */
    public function testExitExpressionWithExitCodeHasExpectedStartColumn(ASTExitExpression $expr): void
    {
        static::assertEquals(5, $expr->getStartColumn());
    }

    /**
     * testExitExpressionWithExitCodeHasExpectedEndColumn
     *
     * @since 1.0.1
     *
     * @depends testExitExpressionWithExitCode
     */
    public function testExitExpressionWithExitCodeHasExpectedEndColumn(ASTExitExpression $expr): void
    {
        static::assertEquals(5, $expr->getEndColumn());
    }

    /**
     * testExitExpressionWithEmptyArgs
     *
     * @since 1.0.1
     */
    public function testExitExpressionWithEmptyArgs(): ASTExitExpression
    {
        $expr = $this->getFirstExitExpressionInFunction();
        static::assertInstanceOf(ASTExitExpression::class, $expr);

        return $expr;
    }

    /**
     * testExitExpressionWithEmptyArgsHasExpectedStartLine
     *
     * @since 1.0.1
     *
     * @depends testExitExpressionWithEmptyArgs
     */
    public function testExitExpressionWithEmptyArgsHasExpectedStartLine(ASTExitExpression $expr): void
    {
        static::assertEquals(4, $expr->getStartLine());
    }

    /**
     * testExitExpressionWithEmptyArgsHasExpectedEndLine
     *
     * @since 1.0.1
     *
     * @depends testExitExpressionWithEmptyArgs
     */
    public function testExitExpressionWithEmptyArgsHasExpectedEndLine(ASTExitExpression $expr): void
    {
        static::assertEquals(4, $expr->getEndLine());
    }

    /**
     * testExitExpressionWithEmptyArgsHasExpectedStartColumn
     *
     * @since 1.0.1
     *
     * @depends testExitExpressionWithEmptyArgs
     */
    public function testExitExpressionWithEmptyArgsHasExpectedStartColumn(ASTExitExpression $expr): void
    {
        static::assertEquals(5, $expr->getStartColumn());
    }

    /**
     * testExitExpressionWithEmptyArgsHasExpectedEndColumn
     *
     * @since 1.0.1
     *
     * @depends testExitExpressionWithEmptyArgs
     */
    public function testExitExpressionWithEmptyArgsHasExpectedEndColumn(ASTExitExpression $expr): void
    {
        static::assertEquals(10, $expr->getEndColumn());
    }

    /**
     * testExitExpressionWithoutArgs
     *
     * @since 1.0.1
     */
    public function testExitExpressionWithoutArgs(): ASTExitExpression
    {
        $expr = $this->getFirstExitExpressionInFunction();
        static::assertInstanceOf(ASTExitExpression::class, $expr);

        return $expr;
    }

    /**
     * testExitExpressionWithoutArgsHasExpectedStartLine
     *
     * @depends testExitExpressionWithoutArgs
     */
    public function testExitExpressionWithoutArgsHasExpectedStartLine(ASTExitExpression $expr): void
    {
        static::assertEquals(4, $expr->getStartLine());
    }

    /**
     * testExitExpressionWithoutArgsHasExpectedStartColumn
     *
     * @depends testExitExpressionWithoutArgs
     */
    public function testExitExpressionWithoutArgsHasExpectedStartColumn(ASTExitExpression $expr): void
    {
        static::assertEquals(5, $expr->getStartColumn());
    }

    /**
     * testExitExpressionHasExpectedEndLineWithoutArgs
     *
     * @depends testExitExpressionWithoutArgs
     */
    public function testExitExpressionWithoutArgsHasExpectedEndLine(ASTExitExpression $expr): void
    {
        static::assertEquals(4, $expr->getEndLine());
    }

    /**
     * testExitExpressionHasExpectedEndColumnWithoutArgs
     *
     * @depends testExitExpressionWithoutArgs
     */
    public function testExitExpressionWithoutArgsHasExpectedEndColumn(ASTExitExpression $expr): void
    {
        static::assertEquals(8, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstExitExpressionInFunction(): ASTExitExpression
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTExitExpression::class
        );
    }
}
