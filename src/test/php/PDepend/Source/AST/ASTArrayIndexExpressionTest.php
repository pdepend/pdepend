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
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Source\AST;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTArrayIndexExpression} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTIndexExpression
 * @covers \PDepend\Source\AST\ASTArrayIndexExpression
 * @group unittest
 */
class ASTArrayIndexExpressionTest extends \PDepend\Source\AST\ASTNodeTest
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
                'PDepend\\Source\\AST\\ASTFunctionPostfix',
                'PDepend\\Source\\AST\\ASTIdentifier',
                'PDepend\\Source\\AST\\ASTArguments',
                'PDepend\\Source\\AST\\ASTLiteral'
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
                'PDepend\\Source\\AST\\ASTFunctionPostfix',
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTArguments',
                'PDepend\\Source\\AST\\ASTLiteral'
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
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTMethodPostfix',
                'PDepend\\Source\\AST\\ASTIdentifier',
                'PDepend\\Source\\AST\\ASTArguments',
                'PDepend\\Source\\AST\\ASTLiteral'
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
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTMethodPostfix',
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTArguments',
                'PDepend\\Source\\AST\\ASTLiteral'
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
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTClassOrInterfaceReference',
                'PDepend\\Source\\AST\\ASTMethodPostfix',
                'PDepend\\Source\\AST\\ASTIdentifier',
                'PDepend\\Source\\AST\\ASTArguments',
                'PDepend\\Source\\AST\\ASTLiteral'
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
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTClassOrInterfaceReference',
                'PDepend\\Source\\AST\\ASTMethodPostfix',
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTArguments',
                'PDepend\\Source\\AST\\ASTLiteral'
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
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTLiteral'
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
                'PDepend\\Source\\AST\\ASTIdentifier',
                'PDepend\\Source\\AST\\ASTLiteral'
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
                'PDepend\\Source\\AST\\ASTArrayIndexExpression',
                'PDepend\\Source\\AST\\ASTArrayIndexExpression',
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTLiteral',
                'PDepend\\Source\\AST\\ASTLiteral',
                'PDepend\\Source\\AST\\ASTLiteral'
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
     * @return \PDepend\Source\AST\ASTArrayIndexExpression
     */
    private function _getFirstArrayIndexExpressionInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(), 
            'PDepend\\Source\\AST\\ASTArrayIndexExpression'
        );
    }
}
