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
 * Test case for the {@link \PDepend\Source\AST\ASTMethodPostfix} class.
 *
 * @covers \PDepend\Source\AST\ASTInvocation
 * @covers \PDepend\Source\AST\ASTMethodPostfix
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTMethodPostfixTest extends ASTNodeTestCase
{
    /**
     * testGetImageForVariableMethod
     *
     * <code>
     * $object->$method(23);
     * </code>
     *
     * @since 1.0.0
     */
    public function testGetImageForVariableMethod(): void
    {
        $postfix = $this->getFirstMethodPostfixInFunction();
        static::assertEquals('$method', $postfix->getImage());
    }

    /**
     * testGetImageForVariableStaticMethod
     *
     * <code>
     * Clazz::$method(23);
     * </code>
     *
     * @since 1.0.0
     */
    public function testGetImageForVariableStaticMethod(): void
    {
        $postfix = $this->getFirstMethodPostfixInFunction();
        static::assertEquals('$method', $postfix->getImage());
    }

    /**
     * testGetImageForArrayIndexedVariableStaticMethod
     *
     * <code>
     * Clazz::$method[42](23);
     * </code>
     *
     * @since 1.0.0
     */
    public function testGetImageForArrayIndexedVariableStaticMethod(): void
    {
        $postfix = $this->getFirstMethodPostfixInFunction();
        static::assertEquals('$method', $postfix->getImage());
    }

    /**
     * testGetImageForMultiArrayIndexedVariableStaticMethod
     *
     * <code>
     * Clazz::$method[42][17][23]();
     * </code>
     *
     * @since 1.0.0
     */
    public function testGetImageForMultiArrayIndexedVariableStaticMethod(): void
    {
        $postfix = $this->getFirstMethodPostfixInFunction();
        static::assertEquals('$method', $postfix->getImage());
    }

    /**
     * testMethodPostfixGraphForVariable
     */
    public function testMethodPostfixGraphForVariable(): void
    {
        $postfix = $this->getFirstMemberPrimaryPrefixInFunction();
        $expected = [
            ASTClassOrInterfaceReference::class,
            ASTMethodPostfix::class,
            ASTVariable::class,
            ASTArguments::class,
        ];

        $this->assertGraphEquals($postfix, $expected);
    }

    /**
     * testMethodPostfixGraphForArrayIndexedVariable
     */
    public function testMethodPostfixGraphForArrayIndexedVariable(): void
    {
        $postfix = $this->getFirstMemberPrimaryPrefixInFunction();
        $expected = [
            ASTClassOrInterfaceReference::class,
            ASTMethodPostfix::class,
            ASTArrayIndexExpression::class,
            ASTVariable::class,
            ASTLiteral::class,
            ASTArguments::class,
        ];

        $this->assertGraphEquals($postfix, $expected);
    }

    /**
     * testMethodPostfixGraphForCompoundExpression
     *
     * Source:
     * <code>
     * $object->{'method'}();
     * </code>
     *
     * AST:
     * <code>
     * - ASTMethodPostfix
     *   - ASTCompoundExpression  ->  { }
     *     - ASTString            ->  'method'
     *   - ASTArguments           ->  ( )
     * </code>
     *
     * @since 1.0.0
     */
    public function testMethodPostfixGraphForCompoundExpression(): void
    {
        $this->assertGraph(
            $this->getFirstMethodPostfixInFunction(),
            [
                ASTCompoundExpression::class . ' ()', [
                    ASTLiteral::class . " ('method')"],
                ASTArguments::class . ' ()',
            ]
        );
    }

    /**
     * testMethodPostfixGraphForCompoundVariable
     *
     * Source:
     * <code>
     * $object->${'method'}();
     * </code>
     *
     * AST:
     * <code>
     * - ASTMethodPostfix
     *   - ASTCompoundVariable  ->  ${ }
     *     - ASTString          ->  'method'
     *   - ASTArguments         ->  ( )
     * </code>
     *
     * @since 1.0.0
     */
    public function testMethodPostfixGraphForCompoundVariable(): void
    {
        $this->assertGraph(
            $this->getFirstMethodPostfixInFunction(),
            [
                ASTCompoundVariable::class . ' ($)', [
                    ASTLiteral::class . " ('method')"],
                ASTArguments::class . ' ()',
            ]
        );
    }

    /**
     * testMethodPostfixGraphForVariableVariable
     *
     * Source:
     * <code>
     * $object->$$method();
     * </code>
     *
     * AST:
     * <code>
     * - ASTMethodPostfix
     *   - ASTVariableVariable  ->  $
     *     - ASTVariable        ->  $method
     *   - ASTArguments         ->  ( )
     * </code>
     *
     * @since 1.0.0
     */
    public function testMethodPostfixGraphForVariableVariable(): void
    {
        $this->assertGraph(
            $this->getFirstMethodPostfixInFunction(),
            [
                ASTVariableVariable::class . ' ($)', [
                    ASTVariable::class . ' ($method)'],
                ASTArguments::class . ' ()',
            ]
        );
    }

    /**
     * testStaticMethodPostfixGraphForCompoundExpression
     *
     * Source:
     * <code>
     * MyClass::{'method'}();
     * </code>
     *
     * AST:
     * <code>
     * - ASTMethodPostfix
     *   - ASTCompoundExpression  ->  { }
     *     - ASTString            ->  'method'
     *   - ASTArguments           ->  ( )
     * </code>
     *
     * @since 1.0.0
     */
    public function testStaticMethodPostfixGraphForCompoundExpression(): void
    {
        $this->assertGraph(
            $this->getFirstMethodPostfixInFunction(),
            [
                ASTCompoundExpression::class . ' ()', [
                    ASTLiteral::class . " ('method')"],
                ASTArguments::class . ' ()',
            ]
        );
    }

    /**
     * testStaticMethodPostfixGraphForCompoundVariable
     *
     * Source:
     * <code>
     * MyClass::${'method'}();
     * </code>
     *
     * AST:
     * <code>
     * - ASTMethodPostfix
     *   - ASTCompoundVariable  ->  ${ }
     *     - ASTString          ->  'method'
     *   - ASTArguments         ->  ( )
     * </code>
     *
     * @since 1.0.0
     */
    public function testStaticMethodPostfixGraphForCompoundVariable(): void
    {
        $this->assertGraph(
            $this->getFirstMethodPostfixInFunction(),
            [
                ASTCompoundVariable::class . ' ($)', [
                    ASTLiteral::class . " ('method')"],
                ASTArguments::class . ' ()',
            ]
        );
    }

    /**
     * testStaticMethodPostfixGraphForVariableVariable
     *
     * Source:
     * <code>
     * MyClass::$$method();
     * </code>
     *
     * AST:
     * <code>
     * - ASTMethodPostfix
     *   - ASTVariableVariable  ->  $
     *     - ASTVariable        ->  $method
     *   - ASTArguments         ->  ( )
     * </code>
     *
     * @since 1.0.0
     */
    public function testStaticMethodPostfixGraphForVariableVariable(): void
    {
        $this->assertGraph(
            $this->getFirstMethodPostfixInFunction(),
            [
                ASTVariableVariable::class . ' ($)', [
                    ASTVariable::class . ' ($method)'],
                ASTArguments::class . ' ()',
            ]
        );
    }

    /**
     * testObjectMethodPostfixHasExpectedStartLine
     */
    public function testObjectMethodPostfixHasExpectedStartLine(): void
    {
        $postfix = $this->getFirstMethodPostfixInFunction();
        static::assertEquals(6, $postfix->getStartLine());
    }

    /**
     * testObjectMethodPostfixHasExpectedStartColumn
     */
    public function testObjectMethodPostfixHasExpectedStartColumn(): void
    {
        $postfix = $this->getFirstMethodPostfixInFunction();
        static::assertEquals(13, $postfix->getStartColumn());
    }

    /**
     * testObjectMethodPostfixHasExpectedEndLine
     */
    public function testObjectMethodPostfixHasExpectedEndLine(): void
    {
        $postfix = $this->getFirstMethodPostfixInFunction();
        static::assertEquals(7, $postfix->getEndLine());
    }

    /**
     * testObjectMethodPostfixHasExpectedEndColumn
     */
    public function testObjectMethodPostfixHasExpectedEndColumn(): void
    {
        $postfix = $this->getFirstMethodPostfixInFunction();
        static::assertEquals(17, $postfix->getEndColumn());
    }

    /**
     * testClassMethodPostfixHasExpectedStartLine
     */
    public function testClassMethodPostfixHasExpectedStartLine(): void
    {
        $postfix = $this->getFirstMethodPostfixInFunction();
        static::assertEquals(6, $postfix->getStartLine());
    }

    /**
     * testClassMethodPostfixHasExpectedStartColumn
     */
    public function testClassMethodPostfixHasExpectedStartColumn(): void
    {
        $postfix = $this->getFirstMethodPostfixInFunction();
        static::assertEquals(13, $postfix->getStartColumn());
    }

    /**
     * testClassMethodPostfixHasExpectedEndLine
     */
    public function testClassMethodPostfixHasExpectedEndLine(): void
    {
        $postfix = $this->getFirstMethodPostfixInFunction();
        static::assertEquals(7, $postfix->getEndLine());
    }

    /**
     * testClassMethodPostfixHasExpectedEndColumn
     */
    public function testClassMethodPostfixHasExpectedEndColumn(): void
    {
        $postfix = $this->getFirstMethodPostfixInFunction();
        static::assertEquals(17, $postfix->getEndColumn());
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     */
    public function testMethodPostfixStructureForSimpleInvocation(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $expected = [
            ASTVariable::class,
            ASTMethodPostfix::class,
            ASTIdentifier::class,
            ASTArguments::class,
        ];

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     */
    public function testMethodPostfixStructureForVariableInvocation(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $expected = [
            ASTVariable::class,
            ASTMethodPostfix::class,
            ASTVariable::class,
            ASTArguments::class,
        ];

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     */
    public function testMethodPostfixStructureForVariableVariableInvocation(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $expected = [
            ASTVariable::class,
            ASTMethodPostfix::class,
            ASTVariableVariable::class,
            ASTVariable::class,
            ASTArguments::class,
        ];

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     */
    public function testMethodPostfixStructureForCompoundVariableInvocation(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $expected = [
            ASTVariable::class,
            ASTMethodPostfix::class,
            ASTCompoundVariable::class,
            ASTConstant::class,
            ASTArguments::class,
        ];

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     */
    public function testMethodPostfixStructureForSimpleStaticInvocation(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $expected = [
            ASTClassOrInterfaceReference::class,
            ASTMethodPostfix::class,
            ASTIdentifier::class,
            ASTArguments::class,
        ];

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     */
    public function testMethodPostfixStructureForVariableStaticInvocation(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $expected = [
            ASTClassOrInterfaceReference::class,
            ASTMethodPostfix::class,
            ASTVariable::class,
            ASTArguments::class,
        ];

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     */
    public function testMethodPostfixStructureForVariableVariableStaticInvocation(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $expected = [
            ASTClassOrInterfaceReference::class,
            ASTMethodPostfix::class,
            ASTVariableVariable::class,
            ASTVariable::class,
            ASTArguments::class,
        ];

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     */
    public function testMethodPostfixStructureForCompoundVariableStaticInvocation(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $expected = [
            ASTClassOrInterfaceReference::class,
            ASTMethodPostfix::class,
            ASTCompoundVariable::class,
            ASTConstant::class,
            ASTArguments::class,
        ];

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     */
    public function testMethodPostfixStructureForVariableCompoundVariableStaticInvocation(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();

        $expected = [
            ASTClassOrInterfaceReference::class,
            ASTMethodPostfix::class,
            ASTVariableVariable::class,
            ASTCompoundVariable::class,
            ASTConstant::class,
            ASTArguments::class,
        ];

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     */
    public function testMethodPostfixStructureForStaticInvocationWithConsecutiveInvocation(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $expected = [
            ASTClassOrInterfaceReference::class,
            ASTMemberPrimaryPrefix::class,
            ASTMethodPostfix::class,
            ASTIdentifier::class,
            ASTArguments::class,
            ASTMethodPostfix::class,
            ASTIdentifier::class,
            ASTArguments::class,
        ];

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     */
    public function testMethodPostfixStructureForStaticInvocationOnVariable(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $expected = [
            ASTVariable::class,
            ASTMethodPostfix::class,
            ASTIdentifier::class,
            ASTArguments::class,
        ];

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     */
    public function testMethodPostfixStructureForSelfInvocation(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInClass();
        $expected = [
            ASTSelfReference::class,
            ASTMethodPostfix::class,
            ASTIdentifier::class,
            ASTArguments::class,
        ];

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     */
    public function testMethodPostfixStructureForParentInvocation(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInClass();
        $expected = [
            ASTParentReference::class,
            ASTMethodPostfix::class,
            ASTIdentifier::class,
            ASTArguments::class,
        ];

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object graph.
     */
    public function testMethodPostfixGraphForStaticReferenceInvocation(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInClass();
        $expected = [
            ASTStaticReference::class,
            ASTMethodPostfix::class,
            ASTIdentifier::class,
            ASTArguments::class,
        ];

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * testMethodPostfixGraphForArrayElementInvocation
     *
     * <code>
     * $this->$foo[0]();
     * </code>
     */
    public function testMethodPostfixGraphForVariableArrayElementInvocation(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInClass();
        $expected = [
            ASTVariable::class,
            ASTMethodPostfix::class,
            ASTArrayIndexExpression::class,
            ASTVariable::class,
            ASTLiteral::class,
            ASTArguments::class,
        ];

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstMethodPostfixInFunction(): ASTMethodPostfix
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTMethodPostfix::class
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstMemberPrimaryPrefixInFunction(): ASTMemberPrimaryPrefix
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTMemberPrimaryPrefix::class
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstMemberPrimaryPrefixInClass(): ASTMemberPrimaryPrefix
    {
        return $this->getFirstNodeOfTypeInClass(
            ASTMemberPrimaryPrefix::class
        );
    }

    /**
     * Creates a method postfix node.
     */
    protected function createNodeInstance(): ASTMethodPostfix
    {
        return new ASTMethodPostfix(__FUNCTION__);
    }
}
