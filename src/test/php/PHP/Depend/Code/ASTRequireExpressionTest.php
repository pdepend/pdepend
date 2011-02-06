<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 * @since      0.9.12
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

require_once 'PHP/Depend/Code/ASTRequireExpression.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTRequireExpression} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 * @since      0.9.12
 *
 * @covers PHP_Depend_Parser
 * @covers PHP_Depend_Builder_Default
 * @covers PHP_Depend_Code_ASTRequireExpression
 */
class PHP_Depend_Code_ASTRequireExpressionTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testAcceptInvokesVisitOnGivenVisitor
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptInvokesVisitOnGivenVisitor()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitRequireExpression'));

        $expr = new PHP_Depend_Code_ASTRequireExpression();
        $expr->accept($visitor);
    }

    /**
     * testAcceptReturnsReturnValueOfVisitMethod
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptReturnsReturnValueOfVisitMethod()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitRequireExpression'))
            ->will($this->returnValue(42));

        $expr = new PHP_Depend_Code_ASTRequireExpression();
        self::assertEquals(42, $expr->accept($visitor));
    }

    /**
     * testIsOnceReturnsFalseByDefault
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
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
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testIsOnceReturnsTrueForRequireOnceExpression()
    {
        $expr = $this->_getFirstRequireExpressionInFunction(__METHOD__);
        $this->assertTrue($expr->isOnce());
    }

    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames()
    {
        $expr = new PHP_Depend_Code_ASTRequireExpression();
        self::assertEquals(
            array(
                'once',
                'image',
                'comment',
                'startLine',
                'startColumn',
                'endLine',
                'endColumn',
                'nodes'
            ),
            $expr->__sleep()
        );
    }

    /**
     * testRequireExpressionHasExpectedStartLine
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testRequireExpressionHasExpectedStartLine()
    {
        $expr = $this->_getFirstRequireExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * testRequireExpressionHasExpectedStartColumn
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testRequireExpressionHasExpectedStartColumn()
    {
        $expr = $this->_getFirstRequireExpressionInFunction(__METHOD__);
        $this->assertEquals(5, $expr->getStartColumn());
    }

    /**
     * testRequireExpressionHasExpectedEndLine
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testRequireExpressionHasExpectedEndLine()
    {
        $expr = $this->_getFirstRequireExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getEndLine());
    }

    /**
     * testRequireExpressionHasExpectedEndColumn
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testRequireExpressionHasExpectedEndColumn()
    {
        $expr = $this->_getFirstRequireExpressionInFunction(__METHOD__);
        $this->assertEquals(35, $expr->getEndColumn());
    }

    /**
     * testRequireExpressionWithParenthesisHasExpectedStartLine
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testRequireExpressionWithParenthesisHasExpectedStartLine()
    {
        $expr = $this->_getFirstRequireExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * testRequireExpressionWithParenthesisHasExpectedStartColumn
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testRequireExpressionWithParenthesisHasExpectedStartColumn()
    {
        $expr = $this->_getFirstRequireExpressionInFunction(__METHOD__);
        $this->assertEquals(5, $expr->getStartColumn());
    }

    /**
     * testRequireExpressionWithParenthesisHasExpectedEndLine
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testRequireExpressionWithParenthesisHasExpectedEndLine()
    {
        $expr = $this->_getFirstRequireExpressionInFunction(__METHOD__);
        $this->assertEquals(6, $expr->getEndLine());
    }

    /**
     * testRequireExpressionWithParenthesisHasExpectedEndColumn
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testRequireExpressionWithParenthesisHasExpectedEndColumn()
    {
        $expr = $this->_getFirstRequireExpressionInFunction(__METHOD__);
        $this->assertEquals(5, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTRequireExpression
     */
    private function _getFirstRequireExpressionInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTRequireExpression::CLAZZ
        );
    }
}