<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@pdepend.org>.
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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 * @since      0.9.12
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTRequireExpression} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 * @since      0.9.12
 *
 * @covers PHP_Depend_Parser
 * @covers PHP_Depend_Code_ASTRequireExpression
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTRequireExpressionTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testIsOnceReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsOnceReturnsFalseByDefault()
    {
        $expr = new PHP_Depend_Code_ASTRequireExpression();
        $this->assertFalse($expr->isOnce());
    }

    /**
     * testIsOnceReturnsTrueForRequireOnceExpression
     *
     * @return void
     */
    public function testIsOnceReturnsTrueForRequireOnceExpression()
    {
        $expr = $this->_getFirstRequireExpressionInFunction();
        $this->assertTrue($expr->isOnce());
    }

    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     *
     * @return void
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames()
    {
        $expr = new PHP_Depend_Code_ASTRequireExpression();
        self::assertEquals(
            array(
                'once',
                'comment',
                'metadata',
                'nodes'
            ),
            $expr->__sleep()
        );
    }

    /**
     * testRequireExpression
     *
     * @return PHP_Depend_Code_ASTRequireExpression
     * @since 1.0.2
     */
    public function testRequireExpression()
    {
        $expr = $this->_getFirstRequireExpressionInFunction();
        $this->assertInstanceOf(PHP_Depend_Code_ASTRequireExpression::CLAZZ, $expr);

        return $expr;
    }

    /**
     * testRequireExpressionHasExpectedStartLine
     *
     * @param PHP_Depend_Code_ASTRequireExpression $expr
     *
     * @return void
     * @depends testRequireExpression
     */
    public function testRequireExpressionHasExpectedStartLine($expr)
    {
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * testRequireExpressionHasExpectedStartColumn
     *
     * @param PHP_Depend_Code_ASTRequireExpression $expr
     *
     * @return void
     * @depends testRequireExpression
     */
    public function testRequireExpressionHasExpectedStartColumn($expr)
    {
        $this->assertEquals(5, $expr->getStartColumn());
    }

    /**
     * testRequireExpressionHasExpectedEndLine
     *
     * @param PHP_Depend_Code_ASTRequireExpression $expr
     *
     * @return void
     * @depends testRequireExpression
     */
    public function testRequireExpressionHasExpectedEndLine($expr)
    {
        $this->assertEquals(4, $expr->getEndLine());
    }

    /**
     * testRequireExpressionHasExpectedEndColumn
     *
     * @param PHP_Depend_Code_ASTRequireExpression $expr
     *
     * @return void
     * @depends testRequireExpression
     */
    public function testRequireExpressionHasExpectedEndColumn($expr)
    {
        $this->assertEquals(35, $expr->getEndColumn());
    }

    /**
     * testRequireExpressionWithParenthesis
     *
     * @return PHP_Depend_Code_ASTRequireExpression
     * @since 1.0.2
     */
    public function testRequireExpressionWithParenthesis()
    {
        $expr = $this->_getFirstRequireExpressionInFunction();
        $this->assertInstanceOf(PHP_Depend_Code_ASTRequireExpression::CLAZZ, $expr);

        return $expr;
    }

    /**
     * testRequireExpressionWithParenthesisHasExpectedStartLine
     *
     * @param PHP_Depend_Code_ASTRequireExpression $expr
     *
     * @return void
     * @depends testRequireExpressionWithParenthesis
     */
    public function testRequireExpressionWithParenthesisHasExpectedStartLine($expr)
    {
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * testRequireExpressionWithParenthesisHasExpectedStartColumn
     *
     * @param PHP_Depend_Code_ASTRequireExpression $expr
     *
     * @return void
     * @depends testRequireExpressionWithParenthesis
     */
    public function testRequireExpressionWithParenthesisHasExpectedStartColumn($expr)
    {
        $this->assertEquals(5, $expr->getStartColumn());
    }

    /**
     * testRequireExpressionWithParenthesisHasExpectedEndLine
     *
     * @param PHP_Depend_Code_ASTRequireExpression $expr
     *
     * @return void
     * @depends testRequireExpressionWithParenthesis
     */
    public function testRequireExpressionWithParenthesisHasExpectedEndLine($expr)
    {
        $this->assertEquals(6, $expr->getEndLine());
    }

    /**
     * testRequireExpressionWithParenthesisHasExpectedEndColumn
     *
     * @param PHP_Depend_Code_ASTRequireExpression $expr
     *
     * @return void
     * @depends testRequireExpressionWithParenthesis
     */
    public function testRequireExpressionWithParenthesisHasExpectedEndColumn($expr)
    {
        $this->assertEquals(5, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return PHP_Depend_Code_ASTRequireExpression
     */
    private function _getFirstRequireExpressionInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            PHP_Depend_Code_ASTRequireExpression::CLAZZ
        );
    }
}
