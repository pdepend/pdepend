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
 * Test case for the {@link \PDepend\Source\AST\ASTFunctionPostfix} class.
 *
 * @covers \PDepend\Source\AST\ASTFunctionPostfix
 * @covers \PDepend\Source\AST\ASTInvocation
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTFunctionPostfixTest extends ASTNodeTestCase
{
    /**
     * testGetImageForVariableFunction
     *
     * <code>
     * $function(23);
     * </code>
     *
     * @since 1.0.0
     */
    public function testGetImageForVariableFunction(): void
    {
        $postfix = $this->getFirstFunctionPostfixInFunction();
        static::assertEquals('$function', $postfix->getImage());
    }

    /**
     * testGetImageForArrayIndexedVariableFunction
     *
     * <code>
     * $function[42](23);
     * </code>
     *
     * @since 1.0.0
     */
    public function testGetImageForArrayIndexedVariableFunction(): void
    {
        $postfix = $this->getFirstFunctionPostfixInFunction();
        static::assertEquals('$function', $postfix->getImage());
    }

    /**
     * Tests that a parsed function postfix has the expected object structure.
     */
    public function testFunctionPostfixGraphForSimpleInvocation(): void
    {
        $postfix = $this->getFirstFunctionPostfixInFunction();
        $expected = [
            ASTIdentifier::class,
            ASTArguments::class,
            ASTLiteral::class,
        ];

        $this->assertGraphEquals($postfix, $expected);
    }

    /**
     * Tests that a parsed function postfix has the expected object structure.
     */
    public function testFunctionPostfixGraphForVariableInvocation(): void
    {
        $postfix = $this->getFirstFunctionPostfixInFunction();
        $expected = [
            ASTVariable::class,
            ASTArguments::class,
        ];

        $this->assertGraphEquals($postfix, $expected);
    }

    /**
     * Tests that a parsed function postfix has the expected object structure.
     */
    public function testFunctionPostfixGraphForCompoundVariableInvocation(): void
    {
        $postfix = $this->getFirstFunctionPostfixInFunction();
        $expected = [
            ASTCompoundVariable::class,
            ASTConstant::class,
            ASTArguments::class,
            ASTConstant::class,
        ];

        $this->assertGraphEquals($postfix, $expected);
    }

    /**
     * testFunctionPostfixGraphForArrayIndexedVariableInvocation
     */
    public function testFunctionPostfixGraphForArrayIndexedVariableInvocation(): void
    {
        $postfix = $this->getFirstFunctionPostfixInFunction();
        $expected = [
            ASTArrayIndexExpression::class,
            ASTArrayIndexExpression::class,
            ASTVariable::class,
            ASTVariable::class,
            ASTLiteral::class,
            ASTArguments::class,
        ];

        $this->assertGraphEquals($postfix, $expected);
    }

    /**
     * Tests that a parsed function postfix has the expected object structure.
     */
    public function testFunctionPostfixGraphForInvocationWithMemberPrimaryPrefixMethod(): void
    {
        $postfix = $this->getFirstFunctionPostfixInFunction();
        $expected = [
            ASTIdentifier::class,
            ASTArguments::class,
            ASTLiteral::class,
        ];

        $this->assertGraphEquals($postfix, $expected);
    }

    /**
     * Tests that a parsed function postfix has the expected object structure.
     */
    public function testFunctionPostfixGraphForInvocationWithMemberPrimaryPrefixProperty(): void
    {
        $postfix = $this->getFirstFunctionPostfixInFunction();
        $expected = [
            ASTIdentifier::class,
            ASTArguments::class,
            ASTLiteral::class,
        ];

        $this->assertGraphEquals($postfix, $expected);
    }

    /**
     * testFunctionPostfixGraphForObjectProperty
     */
    public function testFunctionPostfixGraphForObjectProperty(): void
    {
        $postfix = $this->getFirstFunctionPostfixInFunction();
        $expected = [
            ASTMemberPrimaryPrefix::class,
            ASTVariable::class,
            ASTPropertyPostfix::class,
            ASTArrayIndexExpression::class,
            ASTIdentifier::class,
            ASTLiteral::class,
            ASTArguments::class,
        ];

        $this->assertGraphEquals($postfix, $expected);
    }

    /**
     * testFunctionPostfixHasExpectedStartLine
     */
    public function testFunctionPostfixHasExpectedStartLine(): void
    {
        $init = $this->getFirstFunctionPostfixInFunction();
        static::assertEquals(4, $init->getStartLine());
    }

    /**
     * testFunctionPostfixHasExpectedStartColumn
     */
    public function testFunctionPostfixHasExpectedStartColumn(): void
    {
        $init = $this->getFirstFunctionPostfixInFunction();
        static::assertEquals(5, $init->getStartColumn());
    }

    /**
     * testFunctionPostfixHasExpectedEndLine
     */
    public function testFunctionPostfixHasExpectedEndLine(): void
    {
        $init = $this->getFirstFunctionPostfixInFunction();
        static::assertEquals(8, $init->getEndLine());
    }

    /**
     * testFunctionPostfixHasExpectedEndColumn
     */
    public function testFunctionPostfixHasExpectedEndColumn(): void
    {
        $init = $this->getFirstFunctionPostfixInFunction();
        static::assertEquals(13, $init->getEndColumn());
    }

    /**
     * Creates a field declaration node.
     */
    protected function createNodeInstance(): ASTFunctionPostfix
    {
        return new ASTFunctionPostfix(__FUNCTION__);
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstFunctionPostfixInFunction(): ASTFunctionPostfix
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTFunctionPostfix::class
        );
    }
}
