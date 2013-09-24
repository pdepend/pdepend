<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

namespace PHP\Depend\Source\AST;

/**
 * Test case for the {@link \PHP\Depend\Source\AST\ASTExitExpression} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @covers \PHP\Depend\Source\Language\PHP\AbstractPHPParser
 * @covers \PHP\Depend\Source\AST\ASTExitExpression
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class ASTExitExpressionTest extends \PHP\Depend\Source\AST\ASTNodeTest
{
    /**
     * testExitExpressionWithExitCode
     *
     * @return \PHP\Depend\Source\AST\ASTExitExpression
     * @since 1.0.1
     */
    public function testExitExpressionWithExitCode()
    {
        $expr = $this->_getFirstExitExpressionInFunction();
        $this->assertInstanceOf(\PHP\Depend\Source\AST\ASTExitExpression::CLAZZ, $expr);

        return $expr;
    }

    /**
     * testExitExpressionWithExitCodeHasExpectedStartLine
     *
     * @param \PHP\Depend\Source\AST\ASTExitExpression $expr
     *
     * @return void
     * @since 1.0.1
     * @depends testExitExpressionWithExitCode
     */
    public function testExitExpressionWithExitCodeHasExpectedStartLine($expr)
    {
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * testExitExpressionWithExitCodeHasExpectedEndLine
     *
     * @param \PHP\Depend\Source\AST\ASTExitExpression $expr
     *
     * @return void
     * @since 1.0.1
     * @depends testExitExpressionWithExitCode
     */
    public function testExitExpressionWithExitCodeHasExpectedEndLine($expr)
    {
        $this->assertEquals(6, $expr->getEndLine());
    }

    /**
     * testExitExpressionWithExitCodeHasExpectedStartColumn
     *
     * @param \PHP\Depend\Source\AST\ASTExitExpression $expr
     *
     * @return void
     * @since 1.0.1
     * @depends testExitExpressionWithExitCode
     */
    public function testExitExpressionWithExitCodeHasExpectedStartColumn($expr)
    {
        $this->assertEquals(5, $expr->getStartColumn());
    }

    /**
     * testExitExpressionWithExitCodeHasExpectedEndColumn
     *
     * @param \PHP\Depend\Source\AST\ASTExitExpression $expr
     *
     * @return void
     * @since 1.0.1
     * @depends testExitExpressionWithExitCode
     */
    public function testExitExpressionWithExitCodeHasExpectedEndColumn($expr)
    {
        $this->assertEquals(5, $expr->getEndColumn());
    }

    /**
     * testExitExpressionWithEmptyArgs
     *
     * @return \PHP\Depend\Source\AST\ASTExitExpression
     * @since 1.0.1
     */
    public function testExitExpressionWithEmptyArgs()
    {
        $expr = $this->_getFirstExitExpressionInFunction();
        $this->assertInstanceOf(\PHP\Depend\Source\AST\ASTExitExpression::CLAZZ, $expr);

        return $expr;
    }

    /**
     * testExitExpressionWithEmptyArgsHasExpectedStartLine
     *
     * @param \PHP\Depend\Source\AST\ASTExitExpression $expr
     *
     * @return void
     * @since 1.0.1
     * @depends testExitExpressionWithEmptyArgs
     */
    public function testExitExpressionWithEmptyArgsHasExpectedStartLine($expr)
    {
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * testExitExpressionWithEmptyArgsHasExpectedEndLine
     *
     * @param \PHP\Depend\Source\AST\ASTExitExpression $expr
     *
     * @return void
     * @since 1.0.1
     * @depends testExitExpressionWithEmptyArgs
     */
    public function testExitExpressionWithEmptyArgsHasExpectedEndLine($expr)
    {
        $this->assertEquals(4, $expr->getEndLine());
    }

    /**
     * testExitExpressionWithEmptyArgsHasExpectedStartColumn
     *
     * @param \PHP\Depend\Source\AST\ASTExitExpression $expr
     *
     * @return void
     * @since 1.0.1
     * @depends testExitExpressionWithEmptyArgs
     */
    public function testExitExpressionWithEmptyArgsHasExpectedStartColumn($expr)
    {
        $this->assertEquals(5, $expr->getStartColumn());
    }

    /**
     * testExitExpressionWithEmptyArgsHasExpectedEndColumn
     *
     * @param \PHP\Depend\Source\AST\ASTExitExpression $expr
     *
     * @return void
     * @since 1.0.1
     * @depends testExitExpressionWithEmptyArgs
     */
    public function testExitExpressionWithEmptyArgsHasExpectedEndColumn($expr)
    {
        $this->assertEquals(10, $expr->getEndColumn());
    }

    /**
     * testExitExpressionWithoutArgs
     *
     * @return \PHP\Depend\Source\AST\ASTExitExpression
     * @since 1.0.1
     */
    public function testExitExpressionWithoutArgs()
    {
        $expr = $this->_getFirstExitExpressionInFunction();
        $this->assertInstanceOf(\PHP\Depend\Source\AST\ASTExitExpression::CLAZZ, $expr);

        return $expr;
    }

    /**
     * testExitExpressionWithoutArgsHasExpectedStartLine
     *
     * @param \PHP\Depend\Source\AST\ASTExitExpression $expr
     *
     * @return void
     * @depends testExitExpressionWithoutArgs
     */
    public function testExitExpressionWithoutArgsHasExpectedStartLine($expr)
    {
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * testExitExpressionWithoutArgsHasExpectedStartColumn
     *
     * @param \PHP\Depend\Source\AST\ASTExitExpression $expr
     *
     * @return void
     * @depends testExitExpressionWithoutArgs
     */
    public function testExitExpressionWithoutArgsHasExpectedStartColumn($expr)
    {
        $this->assertEquals(5, $expr->getStartColumn());
    }

    /**
     * testExitExpressionHasExpectedEndLineWithoutArgs
     *
     * @param \PHP\Depend\Source\AST\ASTExitExpression $expr
     *
     * @return void
     * @depends testExitExpressionWithoutArgs
     */
    public function testExitExpressionWithoutArgsHasExpectedEndLine($expr)
    {
        $this->assertEquals(4, $expr->getEndLine());
    }

    /**
     * testExitExpressionHasExpectedEndColumnWithoutArgs
     *
     * @param \PHP\Depend\Source\AST\ASTExitExpression $expr
     *
     * @return void
     * @depends testExitExpressionWithoutArgs
     */
    public function testExitExpressionWithoutArgsHasExpectedEndColumn($expr)
    {
        $this->assertEquals(8, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return \PHP\Depend\Source\AST\ASTExitExpression
     */
    private function _getFirstExitExpressionInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            \PHP\Depend\Source\AST\ASTExitExpression::CLAZZ
        );
    }
}
