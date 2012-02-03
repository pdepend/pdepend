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
 * Test case for the {@link PHP_Depend_Code_ASTArrayIndexExpression} class.
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
 * @covers PHP_Depend_Code_ASTIndexExpression
 * @covers PHP_Depend_Code_ASTArrayIndexExpression
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTArrayIndexExpressionTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testArrayIndexGraphDereferencedFromFunctionCall
     *
     * Source:
     * <code>
     * foo()[42]
     * </code>
     *
     * AST:
     * <code>
     * - ASTIndexExpression
     *   - ASTFunctionPostfix
     *     - ASTIdentifier
     *     - ASTArguments
     *   - ASTLiteral
     * </code>
     *
     * @return void
     * @since 1.0.0
     */
    public function testArrayIndexGraphDereferencedFromFunctionCall()
    {
        $this->assertGraphEquals(
            $this->_getFirstArrayIndexExpressionInFunction(),
            array(
                PHP_Depend_Code_ASTFunctionPostfix::CLAZZ,
                PHP_Depend_Code_ASTIdentifier::CLAZZ,
                PHP_Depend_Code_ASTArguments::CLAZZ,
                PHP_Depend_Code_ASTLiteral::CLAZZ
            )
        );
    }

    /**
     * testArrayIndexGraphDereferencedFromVariableFunctionCall
     *
     * Source:
     * <code>
     * $function()[23]
     * </code>
     *
     * AST:
     * <code>
     * - ASTIndexExpression
     *   - ASTFunctionPostfix
     *     - ASTVariable
     *     - ASTArguments
     *   - ASTLiteral
     * </code>
     *
     * @return void
     * @since 1.0.0
     */
    public function testArrayIndexGraphDereferencedFromVariableFunctionCall()
    {
        $this->assertGraphEquals(
            $this->_getFirstArrayIndexExpressionInFunction(),
            array(
                PHP_Depend_Code_ASTFunctionPostfix::CLAZZ,
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTArguments::CLAZZ,
                PHP_Depend_Code_ASTLiteral::CLAZZ
            )
        );
    }

    /**
     * testArrayIndexGraphDereferencedFromMethodCall
     *
     * Source:
     * <code>
     * $object->method()[42]
     * </code>
     *
     * AST:
     * <code>
     * - ASTIndexExpression
     *   - ASTMemberPrimaryPrefix
     *     - ASTVariable
     *     - ASTMethodPostfix
     *       - ASTIdentifier
     *       - ASTArguments
     *   - ASTLiteral
     * </code>
     *
     * @return void
     * @since 1.0.0
     */
    public function testArrayIndexGraphDereferencedFromMethodCall()
    {
        $this->assertGraphEquals(
            $this->_getFirstArrayIndexExpressionInFunction(),
            array(
                PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ,
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
                PHP_Depend_Code_ASTIdentifier::CLAZZ,
                PHP_Depend_Code_ASTArguments::CLAZZ,
                PHP_Depend_Code_ASTLiteral::CLAZZ
            )
        );
    }

    /**
     * testArrayIndexGraphDereferencedFromVariableMethodCall
     *
     * Source:
     * <code>
     * $object->$method()[23]
     * </code>
     *
     * AST:
     * <code>
     * - ASTIndexExpression
     *   - ASTMemberPrimaryPrefix
     *     - ASTVariable
     *     - ASTMethodPostfix
     *       - ASTVariable
     *       - ASTArguments
     *   - ASTLiteral
     * </code>
     *
     * @return void
     * @since 1.0.0
     */
    public function testArrayIndexGraphDereferencedFromVariableMethodCall()
    {
        $this->assertGraphEquals(
            $this->_getFirstArrayIndexExpressionInFunction(),
            array(
                PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ,
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTArguments::CLAZZ,
                PHP_Depend_Code_ASTLiteral::CLAZZ
            )
        );
    }

    /**
     * testArrayIndexGraphDereferencedFromStaticMethodCall
     *
     * Source:
     * <code>
     * Clazz::method()[42]
     * </code>
     *
     * AST:
     * <code>
     * - ASTIndexExpression
     *   - ASTMemberPrimaryPrefix
     *     - ASTClassOrInterfaceReference
     *     - ASTMethodPostfix
     *       - ASTIdentifier
     *       - ASTArguments
     *   - ASTLiteral
     * </code>
     *
     * @return void
     * @since 1.0.0
     */
    public function testArrayIndexGraphDereferencedFromStaticMethodCall()
    {
        $this->assertGraphEquals(
            $this->_getFirstArrayIndexExpressionInFunction(),
            array(
                PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ,
                PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ,
                PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
                PHP_Depend_Code_ASTIdentifier::CLAZZ,
                PHP_Depend_Code_ASTArguments::CLAZZ,
                PHP_Depend_Code_ASTLiteral::CLAZZ
            )
        );
    }

    /**
     * testArrayIndexGraphDereferencedFromVariableStaticMethodCall
     *
     * Source:
     * <code>
     * Clazz::$method()[23]
     * </code>
     *
     * AST:
     * <code>
     * - ASTIndexExpression
     *   - ASTMemberPrimaryPrefix
     *     - ASTClassOrInterfaceReference
     *     - ASTMethodPostfix
     *       - ASTVariable
     *       - ASTArguments
     *   - ASTLiteral
     * </code>
     *
     * @return void
     * @since 1.0.0
     */
    public function testArrayIndexGraphDereferencedFromVariableStaticMethodCall()
    {
        $this->assertGraphEquals(
            $this->_getFirstArrayIndexExpressionInFunction(),
            array(
                PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ,
                PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ,
                PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTArguments::CLAZZ,
                PHP_Depend_Code_ASTLiteral::CLAZZ
            )
        );
    }

    /**
     * testArrayIndexExpressionGraphForVariable
     *
     * <code>
     * $array[42];
     * </code>
     *
     * @return void
     */
    public function testArrayIndexExpressionGraphForVariable()
    {
        $this->assertGraphEquals(
            $this->_getFirstArrayIndexExpressionInFunction(),
            array(
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTLiteral::CLAZZ
            )
        );
    }

    /**
     * testArrayIndexExpressionGraphForProperty
     *
     * <code>
     * $object->foo[42];
     * </code>
     *
     * @return void
     */
    public function testArrayIndexExpressionGraphForProperty()
    {
        $this->assertGraphEquals(
            $this->_getFirstArrayIndexExpressionInFunction(),
            array(
                PHP_Depend_Code_ASTIdentifier::CLAZZ,
                PHP_Depend_Code_ASTLiteral::CLAZZ
            )
        );
    }

    /**
     * testArrayIndexExpressionGraphForChainedArrayAccess
     *
     * <code>
     * $array[0][0][0];
     * </code>
     *
     * @return void
     */
    public function testArrayIndexExpressionGraphForChainedArrayAccess()
    {
        $this->assertGraphEquals(
            $this->_getFirstArrayIndexExpressionInFunction(),
            array(
                PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ,
                PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ,
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTLiteral::CLAZZ,
                PHP_Depend_Code_ASTLiteral::CLAZZ,
                PHP_Depend_Code_ASTLiteral::CLAZZ
            )
        );
    }

    /**
     * testArrayIndexExpressionHasExpectedStartLine
     *
     * @return void
     */
    public function testArrayIndexExpressionHasExpectedStartLine()
    {
        $expr = $this->_getFirstArrayIndexExpressionInFunction();
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * testArrayIndexExpressionHasExpectedStartColumn
     *
     * @return void
     */
    public function testArrayIndexExpressionHasExpectedStartColumn()
    {
        $expr = $this->_getFirstArrayIndexExpressionInFunction();
        $this->assertEquals(10, $expr->getStartColumn());
    }

    /**
     * testArrayIndexExpressionHasExpectedEndLine
     *
     * @return void
     */
    public function testArrayIndexExpressionHasExpectedEndLine()
    {
        $expr = $this->_getFirstArrayIndexExpressionInFunction();
        $this->assertEquals(6, $expr->getEndLine());
    }

    /**
     * testArrayIndexExpressionHasExpectedEndColumn
     *
     * @return void
     */
    public function testArrayIndexExpressionHasExpectedEndColumn()
    {
        $expr = $this->_getFirstArrayIndexExpressionInFunction();
        $this->assertEquals(13, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return PHP_Depend_Code_ASTArrayIndexExpression
     */
    private function _getFirstArrayIndexExpressionInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(), 
            PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ
        );
    }
}
