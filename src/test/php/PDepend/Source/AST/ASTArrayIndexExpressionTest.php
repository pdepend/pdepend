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
 * Test case for the {@link \PDepend\Source\AST\ASTArrayIndexExpression} class.
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTIndexExpression
 * @covers \PDepend\Source\AST\ASTArrayIndexExpression
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTArrayIndexExpressionTest extends ASTNodeTestCase
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
     * @since 1.0.0
     */
    public function testArrayIndexGraphDereferencedFromFunctionCall(): void
    {
        $this->assertGraphEquals(
            $this->getFirstArrayIndexExpressionInFunction(),
            [
                'PDepend\\Source\\AST\\ASTFunctionPostfix',
                'PDepend\\Source\\AST\\ASTIdentifier',
                'PDepend\\Source\\AST\\ASTArguments',
                'PDepend\\Source\\AST\\ASTLiteral',
            ]
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
     * @since 1.0.0
     */
    public function testArrayIndexGraphDereferencedFromVariableFunctionCall(): void
    {
        $this->assertGraphEquals(
            $this->getFirstArrayIndexExpressionInFunction(),
            [
                'PDepend\\Source\\AST\\ASTFunctionPostfix',
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTArguments',
                'PDepend\\Source\\AST\\ASTLiteral',
            ]
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
     * @since 1.0.0
     */
    public function testArrayIndexGraphDereferencedFromMethodCall(): void
    {
        $this->assertGraphEquals(
            $this->getFirstArrayIndexExpressionInFunction(),
            [
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTMethodPostfix',
                'PDepend\\Source\\AST\\ASTIdentifier',
                'PDepend\\Source\\AST\\ASTArguments',
                'PDepend\\Source\\AST\\ASTLiteral',
            ]
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
     * @since 1.0.0
     */
    public function testArrayIndexGraphDereferencedFromVariableMethodCall(): void
    {
        $this->assertGraphEquals(
            $this->getFirstArrayIndexExpressionInFunction(),
            [
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTMethodPostfix',
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTArguments',
                'PDepend\\Source\\AST\\ASTLiteral',
            ]
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
     * @since 1.0.0
     */
    public function testArrayIndexGraphDereferencedFromStaticMethodCall(): void
    {
        $this->assertGraphEquals(
            $this->getFirstArrayIndexExpressionInFunction(),
            [
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTClassOrInterfaceReference',
                'PDepend\\Source\\AST\\ASTMethodPostfix',
                'PDepend\\Source\\AST\\ASTIdentifier',
                'PDepend\\Source\\AST\\ASTArguments',
                'PDepend\\Source\\AST\\ASTLiteral',
            ]
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
     * @since 1.0.0
     */
    public function testArrayIndexGraphDereferencedFromVariableStaticMethodCall(): void
    {
        $this->assertGraphEquals(
            $this->getFirstArrayIndexExpressionInFunction(),
            [
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTClassOrInterfaceReference',
                'PDepend\\Source\\AST\\ASTMethodPostfix',
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTArguments',
                'PDepend\\Source\\AST\\ASTLiteral',
            ]
        );
    }

    /**
     * testArrayIndexExpressionGraphForVariable
     *
     * <code>
     * $array[42];
     * </code>
     */
    public function testArrayIndexExpressionGraphForVariable(): void
    {
        $this->assertGraphEquals(
            $this->getFirstArrayIndexExpressionInFunction(),
            [
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTLiteral',
            ]
        );
    }

    /**
     * testArrayIndexExpressionGraphForProperty
     *
     * <code>
     * $object->foo[42];
     * </code>
     */
    public function testArrayIndexExpressionGraphForProperty(): void
    {
        $this->assertGraphEquals(
            $this->getFirstArrayIndexExpressionInFunction(),
            [
                'PDepend\\Source\\AST\\ASTIdentifier',
                'PDepend\\Source\\AST\\ASTLiteral',
            ]
        );
    }

    /**
     * testArrayIndexExpressionGraphForChainedArrayAccess
     *
     * <code>
     * $array[0][0][0];
     * </code>
     */
    public function testArrayIndexExpressionGraphForChainedArrayAccess(): void
    {
        $this->assertGraphEquals(
            $this->getFirstArrayIndexExpressionInFunction(),
            [
                'PDepend\\Source\\AST\\ASTArrayIndexExpression',
                'PDepend\\Source\\AST\\ASTArrayIndexExpression',
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTLiteral',
                'PDepend\\Source\\AST\\ASTLiteral',
                'PDepend\\Source\\AST\\ASTLiteral',
            ]
        );
    }

    /**
     * testArrayIndexExpressionHasExpectedStartLine
     */
    public function testArrayIndexExpressionHasExpectedStartLine(): void
    {
        $expr = $this->getFirstArrayIndexExpressionInFunction();
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * testArrayIndexExpressionHasExpectedStartColumn
     */
    public function testArrayIndexExpressionHasExpectedStartColumn(): void
    {
        $expr = $this->getFirstArrayIndexExpressionInFunction();
        $this->assertEquals(10, $expr->getStartColumn());
    }

    /**
     * testArrayIndexExpressionHasExpectedEndLine
     */
    public function testArrayIndexExpressionHasExpectedEndLine(): void
    {
        $expr = $this->getFirstArrayIndexExpressionInFunction();
        $this->assertEquals(6, $expr->getEndLine());
    }

    /**
     * testArrayIndexExpressionHasExpectedEndColumn
     */
    public function testArrayIndexExpressionHasExpectedEndColumn(): void
    {
        $expr = $this->getFirstArrayIndexExpressionInFunction();
        $this->assertEquals(13, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return ASTArrayIndexExpression
     */
    private function getFirstArrayIndexExpressionInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTArrayIndexExpression'
        );
    }
}
