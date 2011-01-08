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
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

require_once 'PHP/Depend/Code/ASTArrayIndexExpression.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTArrayIndexExpression} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Code_ASTArrayIndexExpressionTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testAcceptInvokesVisitOnGivenVisitor
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTArrayIndexExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptInvokesVisitOnGivenVisitor()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitArrayIndexExpression'));

        $node = new PHP_Depend_Code_ASTArrayIndexExpression();
        $node->accept($visitor);
    }

    /**
     * testAcceptReturnsReturnValueOfVisitMethod
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTArrayIndexExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptReturnsReturnValueOfVisitMethod()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitArrayIndexExpression'))
            ->will($this->returnValue(42));

        $node = new PHP_Depend_Code_ASTArrayIndexExpression();
        self::assertEquals(42, $node->accept($visitor));
    }
    
    /**
     * testArrayIndexExpressionGraphForVariable
     *
     * <code>
     * $array[42];
     * </code>
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArrayIndexExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArrayIndexExpressionGraphForVariable()
    {
        $expr = $this->_getFirstArrayIndexExpressionInFunction(__METHOD__);
        $expected   = array(
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTLiteral::CLAZZ
        );

        $this->assertGraphEquals($expr, $expected);
    }

    /**
     * testArrayIndexExpressionGraphForProperty
     *
     * <code>
     * $object->foo[42];
     * </code>
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArrayIndexExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArrayIndexExpressionGraphForProperty()
    {
        $expr = $this->_getFirstArrayIndexExpressionInFunction(__METHOD__);
        $expected   = array(
            PHP_Depend_Code_ASTIdentifier::CLAZZ,
            PHP_Depend_Code_ASTLiteral::CLAZZ
        );

        $this->assertGraphEquals($expr, $expected);
    }

    /**
     * testArrayIndexExpressionGraphForChainedArrayAccess
     *
     * <code>
     * $array[0][0][0];
     * </code>
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArrayIndexExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArrayIndexExpressionGraphForChainedArrayAccess()
    {
        $expr = $this->_getFirstArrayIndexExpressionInFunction(__METHOD__);
        $expected   = array(
            PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ,
            PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ,
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTLiteral::CLAZZ,
            PHP_Depend_Code_ASTLiteral::CLAZZ,
            PHP_Depend_Code_ASTLiteral::CLAZZ
        );

        $this->assertGraphEquals($expr, $expected);
    }

    /**
     * testArrayIndexExpressionHasExpectedStartLine
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArrayIndexExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArrayIndexExpressionHasExpectedStartLine()
    {
        $expr = $this->_getFirstArrayIndexExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * testArrayIndexExpressionHasExpectedStartColumn
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArrayIndexExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArrayIndexExpressionHasExpectedStartColumn()
    {
        $expr = $this->_getFirstArrayIndexExpressionInFunction(__METHOD__);
        $this->assertEquals(10, $expr->getStartColumn());
    }

    /**
     * testArrayIndexExpressionHasExpectedEndLine
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArrayIndexExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArrayIndexExpressionHasExpectedEndLine()
    {
        $expr = $this->_getFirstArrayIndexExpressionInFunction(__METHOD__);
        $this->assertEquals(6, $expr->getEndLine());
    }

    /**
     * testArrayIndexExpressionHasExpectedEndColumn
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArrayIndexExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArrayIndexExpressionHasExpectedEndColumn()
    {
        $expr = $this->_getFirstArrayIndexExpressionInFunction(__METHOD__);
        $this->assertEquals(13, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTArrayIndexExpression
     */
    private function _getFirstArrayIndexExpressionInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ
        );
    }
}