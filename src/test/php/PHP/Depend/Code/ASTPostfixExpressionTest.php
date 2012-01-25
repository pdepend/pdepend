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
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTPostfixExpression} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 *
 * @covers PHP_Depend_Parser
 * @covers PHP_Depend_Code_ASTPostfixExpression
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTPostfixExpressionTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testIncrementPostfixExpressionOnStaticClassMember
     *
     * @return void
     */
    public function testIncrementPostfixExpressionOnStaticClassMember()
    {
        $expr = $this->_getFirstPostfixExpressionInClass(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ,
                PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ,
                PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
                PHP_Depend_Code_ASTVariable::CLAZZ
            )
        );
    }

    /**
     * testIncrementPostfixExpressionOnSelfClassMember
     *
     * @return void
     */
    public function testIncrementPostfixExpressionOnSelfClassMember()
    {
        $expr = $this->_getFirstPostfixExpressionInClass(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PHP_Depend_Code_ASTMemberPrimaryPrefix',
                'PHP_Depend_Code_ASTSelfReference',
                'PHP_Depend_Code_ASTPropertyPostfix',
                'PHP_Depend_Code_ASTVariable'
            )
        );
    }

    /**
     * testIncrementPostfixExpressionOnParentClassMember
     *
     * @return void
     */
    public function testIncrementPostfixExpressionOnParentClassMember()
    {
        $expr = $this->_getFirstPostfixExpressionInClass(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PHP_Depend_Code_ASTMemberPrimaryPrefix',
                'PHP_Depend_Code_ASTParentReference',
                'PHP_Depend_Code_ASTPropertyPostfix',
                'PHP_Depend_Code_ASTVariable'
            )
        );
    }

    /**
     * testIncrementPostfixExpressionOnThisObjectMember
     *
     * @return void
     */
    public function testIncrementPostfixExpressionOnThisObjectMember()
    {
        $expr = $this->_getFirstPostfixExpressionInClass(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PHP_Depend_Code_ASTMemberPrimaryPrefix',
                'PHP_Depend_Code_ASTVariable',
                'PHP_Depend_Code_ASTPropertyPostfix',
                'PHP_Depend_Code_ASTIdentifier'
            )
        );
    }

    /**
     * testIncrementPostfixExpressionOnFunctionPostfix
     *
     * @return void
     */
    public function testIncrementPostfixExpressionOnFunctionPostfix()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PHP_Depend_Code_ASTFunctionPostfix',
                'PHP_Depend_Code_ASTIdentifier',
                'PHP_Depend_Code_ASTArguments',
                'PHP_Depend_Code_ASTVariable'
            )
        );
    }

    /**
     * testIncrementPostfixExpressionOnVariableVariable
     *
     * @return void
     */
    public function testIncrementPostfixExpressionOnVariableVariable()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PHP_Depend_Code_ASTVariableVariable',
                'PHP_Depend_Code_ASTVariableVariable',
                'PHP_Depend_Code_ASTVariable'
            )
        );
    }

    /**
     * testIncrementPostfixExpressionOnCompoundVariable
     *
     * @return void
     */
    public function testIncrementPostfixExpressionOnCompoundVariable()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                PHP_Depend_Code_ASTCompoundVariable::CLAZZ,
                PHP_Depend_Code_ASTConstant::CLAZZ
            )
        );
    }

    /**
     * testIncrementPostfixExpressionOnObjectMethodPostfix
     *
     * @return void
     */
    public function testIncrementPostfixExpressionOnObjectMethodPostfix()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PHP_Depend_Code_ASTMemberPrimaryPrefix',
                'PHP_Depend_Code_ASTVariable',
                'PHP_Depend_Code_ASTMethodPostfix',
                'PHP_Depend_Code_ASTIdentifier',
                'PHP_Depend_Code_ASTArguments',
                'PHP_Depend_Code_ASTVariable'
            )
        );
    }

    /**
     * testIncrementPostfixExpressionOnStaticMethodPostfix
     *
     * @return void
     */
    public function testIncrementPostfixExpressionOnStaticMethodPostfix()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PHP_Depend_Code_ASTMemberPrimaryPrefix',
                'PHP_Depend_Code_ASTClassOrInterfaceReference',
                'PHP_Depend_Code_ASTMethodPostfix',
                'PHP_Depend_Code_ASTIdentifier',
                'PHP_Depend_Code_ASTArguments'
            )
        );
    }

    /**
     * testIncrementPostfixExpressionArrayPropertyPostfix
     * 
     * @return void
     */
    public function testIncrementPostfixExpressionArrayPropertyPostfix()
    {
        $expr = $this->_getFirstPostfixExpressionInClass(__METHOD__)->getParent();
        $this->assertGraphEquals(
            $expr,
            array(
                'PHP_Depend_Code_ASTPostfixExpression',
                'PHP_Depend_Code_ASTMemberPrimaryPrefix',
                'PHP_Depend_Code_ASTVariable',
                'PHP_Depend_Code_ASTPropertyPostfix',
                'PHP_Depend_Code_ASTArrayIndexExpression',
                'PHP_Depend_Code_ASTIdentifier',
                'PHP_Depend_Code_ASTVariable'
            )
        );
    }
    
    /**
     * testIncrementPostfixExpressionHasExpectedStartLine
     *
     * @return void
     */
    public function testIncrementPostfixExpressionHasExpectedStartLine()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertEquals(5, $expr->getStartLine());
    }

    /**
     * testIncrementPostfixExpressionHasExpectedStartColumn
     *
     * @return void
     */
    public function testIncrementPostfixExpressionHasExpectedStartColumn()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertEquals(9, $expr->getStartColumn());
    }

    /**
     * testIncrementPostfixExpressionHasExpectedEndLine
     *
     * @return void
     */
    public function testIncrementPostfixExpressionHasExpectedEndLine()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertEquals(7, $expr->getEndLine());
    }

    /**
     * testIncrementPostfixExpressionHasExpectedEndColumn
     *
     * @return void
     */
    public function testIncrementPostfixExpressionHasExpectedEndColumn()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertEquals(14, $expr->getEndColumn());
    }

    /**
     * testDecrementPostfixExpressionArrayPropertyPostfix
     *
     * @return void
     */
    public function testDecrementPostfixExpressionArrayPropertyPostfix()
    {
        $expr = $this->_getFirstPostfixExpressionInClass(__METHOD__)->getParent();
        $this->assertGraphEquals(
            $expr,
            array(
                'PHP_Depend_Code_ASTPostfixExpression',
                'PHP_Depend_Code_ASTMemberPrimaryPrefix',
                'PHP_Depend_Code_ASTVariable',
                'PHP_Depend_Code_ASTPropertyPostfix',
                'PHP_Depend_Code_ASTArrayIndexExpression',
                'PHP_Depend_Code_ASTIdentifier',
                'PHP_Depend_Code_ASTVariable'
            )
        );
    }
    
    /**
     * testDecrementPostfixExpressionHasExpectedStartLine
     *
     * @return void
     */
    public function testDecrementPostfixExpressionHasExpectedStartLine()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertEquals(7, $expr->getStartLine());
    }

    /**
     * testDecrementPostfixExpressionHasExpectedStartColumn
     *
     * @return void
     */
    public function testDecrementPostfixExpressionHasExpectedStartColumn()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertEquals(17, $expr->getStartColumn());
    }

    /**
     * testDecrementPostfixExpressionHasExpectedEndLine
     *
     * @return void
     */
    public function testDecrementPostfixExpressionHasExpectedEndLine()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertEquals(9, $expr->getEndLine());
    }

    /**
     * testDecrementPostfixExpressionHasExpectedEndColumn
     *
     * @return void
     */
    public function testDecrementPostfixExpressionHasExpectedEndColumn()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertEquals(10, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTPostfixExpression
     */
    private function _getFirstPostfixExpressionInClass($testCase)
    {
        return $this->getFirstNodeOfTypeInClass(
            $testCase, PHP_Depend_Code_ASTPostfixExpression::CLAZZ
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTPostfixExpression
     */
    private function _getFirstPostfixExpressionInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTPostfixExpression::CLAZZ
        );
    }
}
