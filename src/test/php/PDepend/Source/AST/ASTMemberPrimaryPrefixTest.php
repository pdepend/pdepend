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
 * Test case for the {@link \PDepend\Source\AST\ASTMemberPrimaryPrefix} class.
 *
 * @covers \PDepend\Source\AST\ASTMemberPrimaryPrefix
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTMemberPrimaryPrefixTest extends ASTNodeTestCase
{
    /**
     * testMemberPrimaryPrefixGraphDereferencedFromArray
     *
     * @since 1.0.0
     */
    public function testMemberPrimaryPrefixGraphDereferencedFromArray(): void
    {
        $this->getFirstMemberPrimaryPrefixInFunction();
    }

    /**
     * testMemberPrimaryPrefixGraphInIssetExpression
     *
     * Source:
     * <code>
     * $object->plots[0]->coords[0][$i]
     * </code>
     *
     * AST:
     * <code>
     * - ASTMemberPrimaryPrefix
     *   - ASTVariable                    ->  $object
     *   - ASTMemberPrimaryPrefix         ->
     *     - ASTPropertyPostfix           ->  plots
     *       - ASTArrayIndexExpression
     *         - ASTIdentifier            ->  plots
     *         - ASTLiteral               ->  0
     *     - ASTPropertyPostfix           ->  coords
     *       - ASTArrayIndexExpression
     *         - ASTArrayIndexExpression
     *           - ASTIdentifier          ->  coords
     *           - ASTLiteral             ->  0
     *         - ASTVariable              ->  $i
     * </code>
     *
     * @since 1.0.0
     */
    public function testMemberPrimaryPrefixGraphInIssetExpression(): void
    {
        $this->assertGraph(
            $this->getFirstMemberPrimaryPrefixInFunction(),
            [
                ASTVariable::class . ' ($object)',
                ASTMemberPrimaryPrefix::class . ' (->)', [
                    ASTPropertyPostfix::class . ' (plots)', [
                        ASTArrayIndexExpression::class . ' ()', [
                            ASTIdentifier::class . ' (plots)',
                            ASTLiteral::class . ' (0)']],
                    ASTPropertyPostfix::class . ' (coords)', [
                        ASTArrayIndexExpression::class . ' ()', [
                            ASTArrayIndexExpression::class . ' ()', [
                                ASTIdentifier::class . ' (coords)',
                                ASTLiteral::class . ' (0)'],
                            ASTVariable::class . ' ($i)']]],
            ]
        );
    }

    /**
     * testMemberPrimaryPrefixGraphWithDynamicClassAndStaticConstant
     *
     * Source:
     * <code>
     * $class::X;
     * </code>
     *
     * AST:
     * <code>
     * - ASTMemberPrimaryPrefix
     *   - ASTVariable            ->  $class
     *   - ASTConstantPostfix     ->  ::
     *     - ASTIdentifier        ->  X
     * </code>
     *
     * @since 1.0.0
     */
    public function testMemberPrimaryPrefixGraphWithDynamicClassAndStaticConstant(): void
    {
        $this->assertGraphEquals(
            $this->getFirstMemberPrimaryPrefixInFunction(),
            [
                ASTVariable::class,
                ASTConstantPostfix::class,
                ASTIdentifier::class,
            ]
        );
    }

    /**
     * testMemberPrimaryPrefixGraphWithDynamicClassAndStaticProperty
     *
     * Source:
     * <code>
     * $class::$property;
     * </code>
     *
     * AST:
     * <code>
     * - ASTMemberPrimaryPrefix
     *   - ASTVariable            ->  $class
     *   - ASTPropertyPostfix     ->  ::
     *     - ASTVariable          ->  $property
     * </code>
     *
     * @since 1.0.0
     */
    public function testMemberPrimaryPrefixGraphWithDynamicClassAndStaticProperty(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraphEquals(
            $prefix,
            [
                ASTVariable::class,
                ASTPropertyPostfix::class,
                ASTVariable::class,
            ]
        );
    }

    /**
     * testMemberPrimaryPrefixGraphWithDynamicClassAndStaticMethod
     *
     * Source:
     * <code>
     * $class::method(42);
     * </code>
     *
     * AST:
     * <code>
     * - ASTMemberPrimaryPrefix
     *   - ASTVariable            ->  $class
     *   - ASTMethodPostfix       ->  ::
     *     - ASTIdentifier        ->  method
     *     - ASTArguments         ->  ( )
     *       - ASTLiteral         ->  24
     * </code>
     *
     * @since 1.0.0
     */
    public function testMemberPrimaryPrefixGraphWithDynamicClassAndStaticMethod(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraphEquals(
            $prefix,
            [
                ASTVariable::class,
                ASTMethodPostfix::class,
                ASTIdentifier::class,
                ASTArguments::class,
                ASTLiteral::class,
            ]
        );
    }

    /**
     * testMemberPrimaryPrefixGraphWithDynamicClassAndDynamicMethod
     *
     * Source:
     * <code>
     * $class::$method(23);
     * </code>
     *
     * AST:
     * <code>
     * - ASTMemberPrimaryPrefix
     *   - ASTVariable            ->  $class
     *   - ASTMethodPostfix       ->  ::
     *     - ASTVariable          ->  $method
     *     - ASTArguments         ->  ( )
     *       - ASTLiteral         ->  23
     * </code>
     *
     * @since 1.0.0
     */
    public function testMemberPrimaryPrefixGraphWithDynamicClassAndDynamicMethod(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraphEquals(
            $prefix,
            [
                ASTVariable::class,
                ASTMethodPostfix::class,
                ASTVariable::class,
                ASTArguments::class,
                ASTLiteral::class,
            ]
        );
    }

    /**
     * testMemberPrimaryPrefixGraphStartedWithAllocationAndMethodInvocation
     *
     * Source:
     * <code>
     * (new MyClass())->foo();
     * </code>
     *
     * AST:
     * <code>
     * - ASTMemberPrimaryPrefix
     *   - ASTAllocationExpression  ->  new
     *     - ASTClassReference      ->  MyClass
     *     - ASTArguments           ->  ( )
     *   - ASTMethodPostfix         ->  ->
     *     - ASTIdentifier          ->  foo
     *     - ASTArguments           ->  ( )
     * </code>
     */
    public function testMemberPrimaryPrefixGraphStartedWithAllocationAndMethodInvocation(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraph(
            $prefix,
            [
                ASTAllocationExpression::class . ' (new)', [
                    ASTClassReference::class . ' (MyClass)',
                    ASTArguments::class . ' ()'],
                ASTMethodPostfix::class . ' (foo)', [
                    ASTIdentifier::class . ' (foo)',
                    ASTArguments::class . ' ()',
                ]]
        );
    }

    /**
     * testMemberPrimaryPrefixGraphStartedWithAllocationAndMethodChain
     *
     * Source:
     * <code>
     * (new MyClass)->foo()->bar();
     * </code>
     *
     * AST:
     * <code>
     * - ASTMemberPrimaryPrefix
     *   - ASTAllocationExpression  ->  new
     *     - ASTClassReference      ->  MyClass
     *   - ASTMemberPrimaryPrefix
     *     - ASTMethodPostfix         ->  ->
     *       - ASTIdentifier          ->  foo
     *       - ASTArguments           ->  ( )
     *     - ASTMethodPostfix         ->  ->
     *       - ASTIdentifier          ->  bar
     *       - ASTArguments           ->  ( )
     * </code>
     */
    public function testMemberPrimaryPrefixGraphStartedWithAllocationAndMethodChain(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraph(
            $prefix,
            [
                ASTAllocationExpression::class . ' (new)', [
                    ASTClassReference::class . ' (MyClass)'],
                ASTMemberPrimaryPrefix::class . ' (->)', [
                    ASTMethodPostfix::class . ' (foo)', [
                        ASTIdentifier::class . ' (foo)',
                        ASTArguments::class . ' ()'],
                    ASTMethodPostfix::class . ' (bar)', [
                        ASTIdentifier::class . ' (bar)',
                        ASTArguments::class . ' ()']],
            ]
        );
    }

    /**
     * testMemberPrimaryPrefixGraphStartedWithAllocationAndPropertyAccess
     *
     * Source:
     * <code>
     * (new MyClass())->foo;
     * </code>
     *
     * AST:
     * <code>
     * - ASTMemberPrimaryPrefix
     *   - ASTAllocationExpression  ->  new
     *     - ASTClassReference      ->  MyClass
     *     - ASTArguments           ->  ( )
     *   - ASTPropertyPostfix       ->  ->
     *     - ASTIdentifier          ->  foo
     * </code>
     */
    public function testMemberPrimaryPrefixGraphStartedWithAllocationAndPropertyAccess(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraph(
            $prefix,
            [
                ASTAllocationExpression::class . ' (new)', [
                    ASTClassReference::class . ' (MyClass)',
                    ASTArguments::class . ' ()'],
                ASTPropertyPostfix::class . ' (foo)', [
                    ASTIdentifier::class . ' (foo)'],
            ]
        );
    }

    /**
     * testMemberPrimaryPrefixGraphStartedWithAllocationAndPropertyChain
     *
     * Source:
     * <code>
     * (new MyClass)->foo->bar;
     * </code>
     *
     * AST:
     * <code>
     * - ASTMemberPrimaryPrefix
     *   - ASTAllocationExpression  ->  new
     *     - ASTClassReference      ->  MyClass
     *   - ASTMemberPrimaryPrefix
     *     - ASTPropertyPostfix       ->  ->
     *       - ASTIdentifier          ->  foo
     *     - ASTPropertyPostfix       ->  ->
     *       - ASTIdentifier          ->  bar
     * </code>
     */
    public function testMemberPrimaryPrefixGraphStartedWithAllocationAndPropertyChain(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraph(
            $prefix,
            [
                ASTAllocationExpression::class . ' (new)', [
                    ASTClassReference::class . ' (MyClass)'],
                ASTMemberPrimaryPrefix::class . ' (->)', [
                    ASTPropertyPostfix::class . ' (foo)', [
                        ASTIdentifier::class . ' (foo)'],
                    ASTPropertyPostfix::class . ' (bar)', [
                        ASTIdentifier::class . ' (bar)']],
            ]
        );
    }

    /**
     * testMemberPrimaryPrefixGraphForObjectPropertyAccess
     *
     * <code>
     * $obj->foo = 42;
     * </code>
     */
    public function testMemberPrimaryPrefixGraphForObjectPropertyAccess(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraphEquals(
            $prefix,
            [
                ASTVariable::class,
                ASTPropertyPostfix::class,
                ASTIdentifier::class,
            ]
        );
    }

    /**
     * testMemberPrimaryPrefixGraphForObjectWithVariablePropertyAccess
     *
     * Source:
     * <code>
     * $object->$property = 23;
     * </code>
     *
     * AST:
     * <code>
     * - ASTMemberPrimaryPrefix
     *   - ASTVariable            ->  $object
     *   - ASTPropertyPostfix     ->  ->
     *     - ASTVariable          ->  $property
     * </code>
     *
     * @since 1.0.0
     */
    public function testMemberPrimaryPrefixGraphForObjectWithVariablePropertyAccess(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraphEquals(
            $prefix,
            [
                ASTVariable::class,
                ASTPropertyPostfix::class,
                ASTVariable::class,
            ]
        );
    }

    /**
     * testMemberPrimaryPrefixGraphForObjectMethodAccess
     *
     * <code>
     * $obj->foo();
     * </code>
     */
    public function testMemberPrimaryPrefixGraphForObjectMethodAccess(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraphEquals(
            $prefix,
            [
                ASTVariable::class,
                ASTMethodPostfix::class,
                ASTIdentifier::class,
                ASTArguments::class,
            ]
        );
    }

    /**
     * testMemberPrimaryPrefixGraphForObjectWithVariableMethodAccess
     *
     * Source:
     * <code>
     * $object->$method(23, 42);
     * </code>
     *
     * AST:
     * <code>
     * - ASTMemberPrimaryPrefix
     *   - ASTVariable            ->  $object
     *   - ASTMethodPostfix       ->  ->
     *     - ASTVariable          ->  $method
     *     - ASTArguments         ->  ( )
     *       - ASTLiteral         ->  23
     *       - ASTLiteral         ->  42
     * </code>
     *
     * @since 1.0.0
     */
    public function testMemberPrimaryPrefixGraphForObjectWithVariableMethodAccess(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraph(
            $prefix,
            [
                ASTVariable::class . ' ($object)',
                ASTMethodPostfix::class . ' ($method)', [
                    ASTVariable::class . ' ($method)',
                    ASTArguments::class . ' ()', [
                        ASTLiteral::class . ' (23)',
                        ASTLiteral::class . ' (42)']],
            ]
        );
    }

    /**
     * testGraphDereferencedArrayFromFunctionCallAndMethodInvocation
     *
     * Source:
     * <code>
     * foo()[42]->bar()
     * </code>
     *
     * AST:
     * <code>
     * - ASTMemberPrimaryPrefix
     *   - ASTIndexExpression      ->  [ ]
     *     - ASTFunctionPostfix
     *       - ASTIdentifier       ->  foo
     *       - ASTArguments        ->  ( )
     *     - ASTLiteral            ->  42
     *   - ASTMethodPostfix        ->  ->
     *     - ASTIdentifier         ->  bar
     *     - ASTArguments          ->  ( )
     * </code>
     *
     * @since 1.0.0
     */
    public function testGraphDereferencedArrayFromFunctionCallAndMethodInvocation(): void
    {
        $this->assertGraphEquals(
            $this->getFirstMemberPrimaryPrefixInFunction(),
            [
                ASTArrayIndexExpression::class,
                ASTFunctionPostfix::class,
                ASTIdentifier::class,
                ASTArguments::class,
                ASTLiteral::class,
                ASTMethodPostfix::class,
                ASTIdentifier::class,
                ASTArguments::class,
            ]
        );
    }

    /**
     * testMemberPrimaryPrefixGraphForChainedObjectMemberAccess
     *
     * Source:
     * <code>
     * $obj->foo->bar()->baz();
     * </code>
     */
    public function testMemberPrimaryPrefixGraphForChainedObjectMemberAccess(): void
    {
        $this->assertGraph(
            $this->getFirstMemberPrimaryPrefixInFunction(),
            [
                ASTVariable::class . ' ($obj)',
                ASTMemberPrimaryPrefix::class . ' (->)', [
                    ASTPropertyPostfix::class . ' (foo)', [
                        ASTIdentifier::class . ' (foo)'],
                    ASTMemberPrimaryPrefix::class . ' (->)', [
                        ASTMethodPostfix::class . ' (bar)', [
                            ASTIdentifier::class . ' (bar)',
                            ASTArguments::class . ' ()'],
                        ASTMethodPostfix::class . ' (baz)', [
                            ASTIdentifier::class . ' (baz)',
                            ASTArguments::class . ' ()']]],
            ]
        );
    }

    /**
     * testGraphDereferencedArrayFromFunctionCallAndMultipleMethodInvocations
     *
     * Source:
     * <code>
     * foo(23)[42]->bar()[17]->baz()[0]
     * </code>
     */
    public function testGraphDereferencedArrayFromFunctionCallAndMultipleMethodInvocations(): void
    {
        $this->assertGraph(
            $this->getFirstMemberPrimaryPrefixInFunction(),
            [
                ASTArrayIndexExpression::class . ' ()', [
                    ASTMemberPrimaryPrefix::class . ' (->)', [
                        ASTArrayIndexExpression::class . ' ()', [
                            ASTFunctionPostfix::class . ' (foo)', [
                                ASTIdentifier::class . ' (foo)',
                                ASTArguments::class . ' ()', [
                                    ASTLiteral::class . ' (23)']],
                            ASTLiteral::class . ' (42)'],
                        ASTMethodPostfix::class . ' (bar)', [
                            ASTIdentifier::class . ' (bar)',
                            ASTArguments::class . ' ()']],
                    ASTLiteral::class . ' (17)'],
                ASTMethodPostfix::class . ' (baz)', [
                    ASTIdentifier::class . ' (baz)',
                    ASTArguments::class . ' ()'],
            ]
        );
    }

    /**
     * testGraphDereferencedArrayFromStaticMethodCallAndMultipleMethodInvocations
     *
     * Source:
     * <code>
     * Clazz::foo(23)[42]->bar()[17]->baz()[0]
     * </code>
     */
    public function testGraphDereferencedArrayFromStaticMethodCallAndMultipleMethodInvocations(): void
    {
        $this->assertGraph(
            $this->getFirstMemberPrimaryPrefixInFunction(),
            [
                ASTArrayIndexExpression::class . ' ()', [
                    ASTMemberPrimaryPrefix::class . ' (->)', [
                        ASTArrayIndexExpression::class . ' ()', [
                            ASTMemberPrimaryPrefix::class . ' (::)', [
                                ASTClassOrInterfaceReference::class . ' (Clazz)',
                                ASTMethodPostfix::class . ' (foo)', [
                                    ASTIdentifier::class . ' (foo)',
                                    ASTArguments::class . ' ()', [
                                        ASTLiteral::class . ' (23)']]],
                            ASTLiteral::class . ' (42)'],
                        ASTMethodPostfix::class . ' (bar)', [
                            ASTIdentifier::class . ' (bar)',
                            ASTArguments::class . ' ()']],
                    ASTLiteral::class . ' (17)'],
                ASTMethodPostfix::class . ' (baz)', [
                    ASTIdentifier::class . ' (baz)',
                    ASTArguments::class . ' ()'],
            ]
        );
    }

    /**
     * testGraphDereferencedArrayFromVariableClassStaticMethodCallAndMultipleMethodInvocations
     *
     * Source:
     * <code>
     * $class::foo(23)[42]->bar()[17]->baz()[0]
     * </code>
     */
    public function testGraphDereferencedArrayFromVariableClassStaticMethodCallAndMultipleMethodInvocations(): void
    {
        $this->assertGraph(
            $this->getFirstMemberPrimaryPrefixInFunction(),
            [
                ASTArrayIndexExpression::class . ' ()', [
                    ASTMemberPrimaryPrefix::class . ' (->)', [
                        ASTArrayIndexExpression::class . ' ()', [
                            ASTMemberPrimaryPrefix::class . ' (::)', [
                                ASTVariable::class . ' ($class)',
                                ASTMethodPostfix::class . ' (foo)', [
                                    ASTIdentifier::class . ' (foo)',
                                    ASTArguments::class . ' ()', [
                                        ASTLiteral::class . ' (23)']]],
                            ASTLiteral::class . ' (42)'],
                        ASTMethodPostfix::class . ' (bar)', [
                            ASTIdentifier::class . ' (bar)',
                            ASTArguments::class . ' ()']],
                    ASTLiteral::class . ' (17)'],
                ASTMethodPostfix::class . ' (baz)', [
                    ASTIdentifier::class . ' (baz)',
                    ASTArguments::class . ' ()'],
            ]
        );
    }

    /**
     * testObjectMemberPrimaryPrefixHasExpectedStartLine
     */
    public function testObjectMemberPrimaryPrefixHasExpectedStartLine(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        static::assertEquals(4, $prefix->getStartLine());
    }

    /**
     * testObjectMemberPrimaryPrefixHasExpectedStartColumn
     */
    public function testObjectMemberPrimaryPrefixHasExpectedStartColumn(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        static::assertEquals(5, $prefix->getStartColumn());
    }

    /**
     * testObjectMemberPrimaryPrefixHasExpectedEndLine
     */
    public function testObjectMemberPrimaryPrefixHasExpectedEndLine(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        static::assertEquals(6, $prefix->getEndLine());
    }

    /**
     * testObjectMemberPrimaryPrefixHasExpectedEndColumn
     */
    public function testObjectMemberPrimaryPrefixHasExpectedEndColumn(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        static::assertEquals(10, $prefix->getEndColumn());
    }

    /**
     * testObjectPropertyMemberPrimaryPrefixIsStaticReturnsFalse
     */
    public function testObjectPropertyMemberPrimaryPrefixIsStaticReturnsFalse(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        static::assertFalse($prefix->isStatic());
    }

    /**
     * testObjectMethodMemberPrimaryPrefixIsStaticReturnsFalse
     */
    public function testObjectMethodMemberPrimaryPrefixIsStaticReturnsFalse(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        static::assertFalse($prefix->isStatic());
    }

    /**
     * testClassPropertyMemberPrimaryPrefixIsStaticReturnsTrue
     */
    public function testClassPropertyMemberPrimaryPrefixIsStaticReturnsTrue(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        static::assertTrue($prefix->isStatic());
    }

    /**
     * testClassMethodMemberPrimaryPrefixIsStaticReturnsTrue
     */
    public function testClassMethodMemberPrimaryPrefixIsStaticReturnsTrue(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        static::assertTrue($prefix->isStatic());
    }

    /**
     * Returns a test member primary prefix.
     */
    private function getFirstMemberPrimaryPrefixInFunction(): ASTMemberPrimaryPrefix
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTMemberPrimaryPrefix::class
        );
    }
}
