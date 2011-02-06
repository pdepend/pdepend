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

/**
 * Test case for the {@link PHP_Depend_Code_ASTAssignmentExpression} class.
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
class PHP_Depend_Code_ASTAssignmentExpressionTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testAssignmentExpressionFromMethodInvocation
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAssignmentExpressionFromMethodInvocation()
    {
        $this->assertGraphEquals(
            $this->_getFirstAssignmentExpressionInFunction(__METHOD__),
            array(
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ,
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
                PHP_Depend_Code_ASTIdentifier::CLAZZ,
                PHP_Depend_Code_ASTArguments::CLAZZ
            )
        );
    }

    /**
     * testAssignmentExpressionFromPropertyAccess
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAssignmentExpressionFromPropertyAccess()
    {
        $this->assertGraphEquals(
            $this->_getFirstAssignmentExpressionInFunction(__METHOD__),
            array(
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ,
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
                PHP_Depend_Code_ASTIdentifier::CLAZZ
            )
        );
    }

    /**
     * testAssignmentExpressionFromFunctionReturnValue
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAssignmentExpressionFromFunctionReturnValue()
    {
        $this->assertGraphEquals(
            $this->_getFirstAssignmentExpressionInFunction(__METHOD__),
            array(
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ,
                PHP_Depend_Code_ASTFunctionPostfix::CLAZZ,
                PHP_Depend_Code_ASTIdentifier::CLAZZ,
                PHP_Depend_Code_ASTArguments::CLAZZ,
                PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
                PHP_Depend_Code_ASTIdentifier::CLAZZ
            )
        );
    }

    /**
     * testAcceptInvokesVisitOnGivenVisitor
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptInvokesVisitOnGivenVisitor()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitAssignmentExpression'));

        $node = new PHP_Depend_Code_ASTAssignmentExpression();
        $node->accept($visitor);
    }

    /**
     * testAcceptReturnsReturnValueOfVisitMethod
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptReturnsReturnValueOfVisitMethod()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitAssignmentExpression'))
            ->will($this->returnValue(42));

        $node = new PHP_Depend_Code_ASTAssignmentExpression();
        self::assertEquals(42, $node->accept($visitor));
    }

    /**
     * Tests the resulting object graph.
     * 
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAssignmentExpressionGraphForIntegerLiteral()
    {
        $this->assertGraphEquals(
            $this->_getFirstAssignmentExpressionInFunction(__METHOD__),
            array(
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTLiteral::CLAZZ
            )
        );
    }

    /**
     * Tests the resulting object graph.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAssignmentExpressionGraphForFloatLiteral()
    {
        $this->assertGraphEquals(
            $this->_getFirstAssignmentExpressionInFunction(__METHOD__),
            array(
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTLiteral::CLAZZ
            )
        );
    }

    /**
     * Tests the start line of an assignment-expression.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testVariableAssignmentExpressionHasExpectedStartLine()
    {
        $expr = $this->_getFirstAssignmentExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * Tests the start column of an assignment-expression.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testVariableAssignmentExpressionHasExpectedStartColumn()
    {
        $expr = $this->_getFirstAssignmentExpressionInFunction(__METHOD__);
        $this->assertEquals(5, $expr->getStartColumn());
    }

    /**
     * Tests the end line of an assignment-expression.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testVariableAssignmentExpressionHasExpectedEndLine()
    {
        $expr = $this->_getFirstAssignmentExpressionInFunction(__METHOD__);
        $this->assertEquals(6, $expr->getEndLine());
    }

    /**
     * Tests the end column of an assignment-expression.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testVariableAssignmentExpressionHasExpectedEndColumn()
    {
        $expr = $this->_getFirstAssignmentExpressionInFunction(__METHOD__);
        $this->assertEquals(5, $expr->getEndColumn());
    }

    /**
     * Tests the start line of an assignment-expression.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testStaticPropertyAssignmentExpressionHasExpectedStartLine()
    {
        $expr = $this->_getFirstAssignmentExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * Tests the start column of an assignment-expression.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testStaticPropertyAssignmentExpressionHasExpectedStartColumn()
    {
        $expr = $this->_getFirstAssignmentExpressionInFunction(__METHOD__);
        $this->assertEquals(5, $expr->getStartColumn());
    }

    /**
     * Tests the end line of an assignment-expression.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testStaticPropertyAssignmentExpressionHasExpectedEndLine()
    {
        $expr = $this->_getFirstAssignmentExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getEndLine());
    }

    /**
     * Tests the end column of an assignment-expression.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testStaticPropertyAssignmentExpressionHasExpectedEndColumn()
    {
        $expr = $this->_getFirstAssignmentExpressionInFunction(__METHOD__);
        $this->assertEquals(80, $expr->getEndColumn());
    }

    /**
     * Tests the start line of an assignment-expression.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testObjectPropertyAssignmentExpressionHasExpectedStartLine()
    {
        $expr = $this->_getFirstAssignmentExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * Tests the start column of an assignment-expression.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testObjectPropertyAssignmentExpressionHasExpectedStartColumn()
    {
        $expr = $this->_getFirstAssignmentExpressionInFunction(__METHOD__);
        $this->assertEquals(5, $expr->getStartColumn());
    }

    /**
     * Tests the end line of an assignment-expression.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testObjectPropertyAssignmentExpressionHasExpectedEndLine()
    {
        $expr = $this->_getFirstAssignmentExpressionInFunction(__METHOD__);
        $this->assertEquals(5, $expr->getEndLine());
    }

    /**
     * Tests the end column of an assignment-expression.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testObjectPropertyAssignmentExpressionHasExpectedEndColumn()
    {
        $expr = $this->_getFirstAssignmentExpressionInFunction(__METHOD__);
        $this->assertEquals(15, $expr->getEndColumn());
    }

    /**
     * Tests the start line of an assignment-expression.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testChainedPropertyAssignmentExpressionHasExpectedStartLine()
    {
        $expr = $this->_getFirstAssignmentExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * Tests the start column of an assignment-expression.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testChainedPropertyAssignmentExpressionHasExpectedStartColumn()
    {
        $expr = $this->_getFirstAssignmentExpressionInFunction(__METHOD__);
        $this->assertEquals(5, $expr->getStartColumn());
    }

    /**
     * Tests the end column of an assignment-expression.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testChainedPropertyAssignmentExpressionHasExpectedEndColumn()
    {
        $expr = $this->_getFirstAssignmentExpressionInFunction(__METHOD__);
        $this->assertEquals(23, $expr->getEndColumn());
    }

    /**
     * Tests the end line of an assignment-expression.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAssignmentExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testChainedPropertyAssignmentExpressionHasExpectedEndLine()
    {
        $expr = $this->_getFirstAssignmentExpressionInFunction(__METHOD__);
        $this->assertEquals(8, $expr->getEndLine());
    }

    /**
     * Returns a test assignment-expression.
     *
     * @param string $testCase The calling test case.
     *
     * @return PHP_Depend_Code_ASTAssignmentExpression
     */
    private function _getFirstAssignmentExpressionInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTAssignmentExpression::CLAZZ
        );
    }
}