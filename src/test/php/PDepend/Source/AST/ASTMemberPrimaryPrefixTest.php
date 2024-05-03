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
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTMemberPrimaryPrefix
 * @group unittest
 */
class ASTMemberPrimaryPrefixTest extends ASTNodeTestCase
{
    /**
     * testMemberPrimaryPrefixGraphDereferencedFromArray
     *
     * @return void
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
     * @return void
     * @since 1.0.0
     */
    public function testMemberPrimaryPrefixGraphInIssetExpression(): void
    {
        $this->assertGraph(
            $this->getFirstMemberPrimaryPrefixInFunction(),
            [
                'PDepend\\Source\\AST\\ASTVariable'                          . ' ($object)',
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix'               . ' (->)', [
                    'PDepend\\Source\\AST\\ASTPropertyPostfix'               . ' (plots)', [
                        'PDepend\\Source\\AST\\ASTArrayIndexExpression'      . ' ()', [
                            'PDepend\\Source\\AST\\ASTIdentifier'            . ' (plots)',
                            'PDepend\\Source\\AST\\ASTLiteral'               . ' (0)']],
                    'PDepend\\Source\\AST\\ASTPropertyPostfix'               . ' (coords)', [
                        'PDepend\\Source\\AST\\ASTArrayIndexExpression'      . ' ()', [
                            'PDepend\\Source\\AST\\ASTArrayIndexExpression'  . ' ()', [
                                'PDepend\\Source\\AST\\ASTIdentifier'        . ' (coords)',
                                'PDepend\\Source\\AST\\ASTLiteral'           . ' (0)'],
                            'PDepend\\Source\\AST\\ASTVariable'              . ' ($i)']]]
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
     * @return void
     * @since 1.0.0
     */
    public function testMemberPrimaryPrefixGraphWithDynamicClassAndStaticConstant(): void
    {
        $this->assertGraphEquals(
            $this->getFirstMemberPrimaryPrefixInFunction(),
            [
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTConstantPostfix',
                'PDepend\\Source\\AST\\ASTIdentifier'
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
     * @return void
     * @since 1.0.0
     */
    public function testMemberPrimaryPrefixGraphWithDynamicClassAndStaticProperty(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraphEquals(
            $prefix,
            [
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTPropertyPostfix',
                'PDepend\\Source\\AST\\ASTVariable'
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
     * @return void
     * @since 1.0.0
     */
    public function testMemberPrimaryPrefixGraphWithDynamicClassAndStaticMethod(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraphEquals(
            $prefix,
            [
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTMethodPostfix',
                'PDepend\\Source\\AST\\ASTIdentifier',
                'PDepend\\Source\\AST\\ASTArguments',
                'PDepend\\Source\\AST\\ASTLiteral'
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
     * @return void
     * @since 1.0.0
     */
    public function testMemberPrimaryPrefixGraphWithDynamicClassAndDynamicMethod(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraphEquals(
            $prefix,
            [
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTMethodPostfix',
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTArguments',
                'PDepend\\Source\\AST\\ASTLiteral'
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
     *
     * @return void
     */
    public function testMemberPrimaryPrefixGraphStartedWithAllocationAndMethodInvocation(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraph(
            $prefix,
            [
                'PDepend\\Source\\AST\\ASTAllocationExpression' . ' (new)', [
                    'PDepend\\Source\\AST\\ASTClassReference'   . ' (MyClass)',
                    'PDepend\\Source\\AST\\ASTArguments'        . ' ()'],
                'PDepend\\Source\\AST\\ASTMethodPostfix'        . ' (foo)', [
                    'PDepend\\Source\\AST\\ASTIdentifier'       . ' (foo)',
                    'PDepend\\Source\\AST\\ASTArguments'        . ' ()'
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
     *
     * @return void
     */
    public function testMemberPrimaryPrefixGraphStartedWithAllocationAndMethodChain(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraph(
            $prefix,
            [
                'PDepend\\Source\\AST\\ASTAllocationExpression' . ' (new)', [
                    'PDepend\\Source\\AST\\ASTClassReference'   . ' (MyClass)'],
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix'  . ' (->)', [
                    'PDepend\\Source\\AST\\ASTMethodPostfix'    . ' (foo)', [
                        'PDepend\\Source\\AST\\ASTIdentifier'   . ' (foo)',
                        'PDepend\\Source\\AST\\ASTArguments'    . ' ()'],
                    'PDepend\\Source\\AST\\ASTMethodPostfix'    . ' (bar)', [
                        'PDepend\\Source\\AST\\ASTIdentifier'   . ' (bar)',
                        'PDepend\\Source\\AST\\ASTArguments'    . ' ()']]
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
     *
     * @return void
     */
    public function testMemberPrimaryPrefixGraphStartedWithAllocationAndPropertyAccess(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraph(
            $prefix,
            [
                'PDepend\\Source\\AST\\ASTAllocationExpression' . ' (new)', [
                    'PDepend\\Source\\AST\\ASTClassReference'   . ' (MyClass)',
                    'PDepend\\Source\\AST\\ASTArguments'        . ' ()'],
                'PDepend\\Source\\AST\\ASTPropertyPostfix'      . ' (foo)', [
                    'PDepend\\Source\\AST\\ASTIdentifier'       . ' (foo)']
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
     *
     * @return void
     */
    public function testMemberPrimaryPrefixGraphStartedWithAllocationAndPropertyChain(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraph(
            $prefix,
            [
                'PDepend\\Source\\AST\\ASTAllocationExpression' . ' (new)', [
                    'PDepend\\Source\\AST\\ASTClassReference'   . ' (MyClass)'],
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix'  . ' (->)', [
                    'PDepend\\Source\\AST\\ASTPropertyPostfix'  . ' (foo)', [
                        'PDepend\\Source\\AST\\ASTIdentifier'   . ' (foo)'],
                    'PDepend\\Source\\AST\\ASTPropertyPostfix'  . ' (bar)', [
                        'PDepend\\Source\\AST\\ASTIdentifier'   . ' (bar)']]
            ]
        );
    }

    /**
     * testMemberPrimaryPrefixGraphForObjectPropertyAccess
     *
     * <code>
     * $obj->foo = 42;
     * </code>
     *
     * @return void
     */
    public function testMemberPrimaryPrefixGraphForObjectPropertyAccess(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraphEquals(
            $prefix,
            [
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTPropertyPostfix',
                'PDepend\\Source\\AST\\ASTIdentifier'
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
     * @return void
     * @since 1.0.0
     */
    public function testMemberPrimaryPrefixGraphForObjectWithVariablePropertyAccess(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraphEquals(
            $prefix,
            [
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTPropertyPostfix',
                'PDepend\\Source\\AST\\ASTVariable'
            ]
        );
    }

    /**
     * testMemberPrimaryPrefixGraphForObjectMethodAccess
     *
     * <code>
     * $obj->foo();
     * </code>
     *
     * @return void
     */
    public function testMemberPrimaryPrefixGraphForObjectMethodAccess(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraphEquals(
            $prefix,
            [
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTMethodPostfix',
                'PDepend\\Source\\AST\\ASTIdentifier',
                'PDepend\\Source\\AST\\ASTArguments'
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
     * @return void
     * @since 1.0.0
     */
    public function testMemberPrimaryPrefixGraphForObjectWithVariableMethodAccess(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraph(
            $prefix,
            [
                'PDepend\\Source\\AST\\ASTVariable'        . ' ($object)',
                'PDepend\\Source\\AST\\ASTMethodPostfix'   . ' ($method)', [
                    'PDepend\\Source\\AST\\ASTVariable'    . ' ($method)',
                    'PDepend\\Source\\AST\\ASTArguments'   . ' ()', [
                        'PDepend\\Source\\AST\\ASTLiteral' . ' (23)',
                        'PDepend\\Source\\AST\\ASTLiteral' . ' (42)']]
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
     * @return void
     * @since 1.0.0
     */
    public function testGraphDereferencedArrayFromFunctionCallAndMethodInvocation(): void
    {
        $this->assertGraphEquals(
            $this->getFirstMemberPrimaryPrefixInFunction(),
            [
                'PDepend\\Source\\AST\\ASTArrayIndexExpression',
                'PDepend\\Source\\AST\\ASTFunctionPostfix',
                'PDepend\\Source\\AST\\ASTIdentifier',
                'PDepend\\Source\\AST\\ASTArguments',
                'PDepend\\Source\\AST\\ASTLiteral',
                'PDepend\\Source\\AST\\ASTMethodPostfix',
                'PDepend\\Source\\AST\\ASTIdentifier',
                'PDepend\\Source\\AST\\ASTArguments'
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
     *
     * @return void
     */
    public function testMemberPrimaryPrefixGraphForChainedObjectMemberAccess(): void
    {
        $this->assertGraph(
            $this->getFirstMemberPrimaryPrefixInFunction(),
            [
                'PDepend\\Source\\AST\\ASTVariable'                    . ' ($obj)',
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix'         . ' (->)',     [
                    'PDepend\\Source\\AST\\ASTPropertyPostfix'         . ' (foo)',  [
                        'PDepend\\Source\\AST\\ASTIdentifier'          . ' (foo)'],
                        'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix' . ' (->)', [
                            'PDepend\\Source\\AST\\ASTMethodPostfix'   . ' (bar)', [
                                'PDepend\\Source\\AST\\ASTIdentifier'  . ' (bar)',
                                'PDepend\\Source\\AST\\ASTArguments'   . ' ()'],
                            'PDepend\\Source\\AST\\ASTMethodPostfix'   . ' (baz)', [
                                'PDepend\\Source\\AST\\ASTIdentifier'  . ' (baz)',
                                'PDepend\\Source\\AST\\ASTArguments'   . ' ()']]]
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
     *
     * @return void
     */
    public function testGraphDereferencedArrayFromFunctionCallAndMultipleMethodInvocations(): void
    {
        $this->assertGraph(
            $this->getFirstMemberPrimaryPrefixInFunction(),
            [
                'PDepend\\Source\\AST\\ASTArrayIndexExpression'         . ' ()',    [
                    'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix'      . ' (->)',  [
                        'PDepend\\Source\\AST\\ASTArrayIndexExpression' . ' ()',    [
                            'PDepend\\Source\\AST\\ASTFunctionPostfix'  . ' (foo)', [
                                'PDepend\\Source\\AST\\ASTIdentifier'   . ' (foo)',
                                'PDepend\\Source\\AST\\ASTArguments'    . ' ()',    [
                                    'PDepend\\Source\\AST\\ASTLiteral'  . ' (23)']],
                            'PDepend\\Source\\AST\\ASTLiteral'          . ' (42)'],
                        'PDepend\\Source\\AST\\ASTMethodPostfix'        . ' (bar)', [
                            'PDepend\\Source\\AST\\ASTIdentifier'       . ' (bar)',
                            'PDepend\\Source\\AST\\ASTArguments'        . ' ()']],
                    'PDepend\\Source\\AST\\ASTLiteral'                  . ' (17)'],
                'PDepend\\Source\\AST\\ASTMethodPostfix'                . ' (baz)', [
                    'PDepend\\Source\\AST\\ASTIdentifier'               . ' (baz)',
                    'PDepend\\Source\\AST\\ASTArguments'                . ' ()']
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
     *
     * @return void
     */
    public function testGraphDereferencedArrayFromStaticMethodCallAndMultipleMethodInvocations(): void
    {
        $this->assertGraph(
            $this->getFirstMemberPrimaryPrefixInFunction(),
            [
                'PDepend\\Source\\AST\\ASTArrayIndexExpression'                      . ' ()',    [
                    'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix'                   . ' (->)',  [
                        'PDepend\\Source\\AST\\ASTArrayIndexExpression'              . ' ()',    [
                            'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix'           . ' (::)', [
                                'PDepend\\Source\\AST\\ASTClassOrInterfaceReference' . ' (Clazz)',
                                'PDepend\\Source\\AST\\ASTMethodPostfix'             . ' (foo)', [
                                    'PDepend\\Source\\AST\\ASTIdentifier'            . ' (foo)',
                                    'PDepend\\Source\\AST\\ASTArguments'             . ' ()',    [
                                        'PDepend\\Source\\AST\\ASTLiteral'           . ' (23)']]],
                            'PDepend\\Source\\AST\\ASTLiteral'                       . ' (42)'],
                        'PDepend\\Source\\AST\\ASTMethodPostfix'                     . ' (bar)', [
                            'PDepend\\Source\\AST\\ASTIdentifier'                    . ' (bar)',
                            'PDepend\\Source\\AST\\ASTArguments'                     . ' ()']],
                    'PDepend\\Source\\AST\\ASTLiteral'                               . ' (17)'],
                'PDepend\\Source\\AST\\ASTMethodPostfix'                             . ' (baz)', [
                    'PDepend\\Source\\AST\\ASTIdentifier'                            . ' (baz)',
                    'PDepend\\Source\\AST\\ASTArguments'                             . ' ()']
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
     *
     * @return void
     */
    public function testGraphDereferencedArrayFromVariableClassStaticMethodCallAndMultipleMethodInvocations(): void
    {
        $this->assertGraph(
            $this->getFirstMemberPrimaryPrefixInFunction(),
            [
                'PDepend\\Source\\AST\\ASTArrayIndexExpression'                      . ' ()',    [
                    'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix'                   . ' (->)',  [
                        'PDepend\\Source\\AST\\ASTArrayIndexExpression'              . ' ()',    [
                            'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix'           . ' (::)', [
                                'PDepend\\Source\\AST\\ASTVariable'                  . ' ($class)',
                                'PDepend\\Source\\AST\\ASTMethodPostfix'             . ' (foo)', [
                                    'PDepend\\Source\\AST\\ASTIdentifier'            . ' (foo)',
                                    'PDepend\\Source\\AST\\ASTArguments'             . ' ()',    [
                                        'PDepend\\Source\\AST\\ASTLiteral'           . ' (23)']]],
                            'PDepend\\Source\\AST\\ASTLiteral'                       . ' (42)'],
                        'PDepend\\Source\\AST\\ASTMethodPostfix'                     . ' (bar)', [
                            'PDepend\\Source\\AST\\ASTIdentifier'                    . ' (bar)',
                            'PDepend\\Source\\AST\\ASTArguments'                     . ' ()']],
                    'PDepend\\Source\\AST\\ASTLiteral'                               . ' (17)'],
                'PDepend\\Source\\AST\\ASTMethodPostfix'                             . ' (baz)', [
                    'PDepend\\Source\\AST\\ASTIdentifier'                            . ' (baz)',
                    'PDepend\\Source\\AST\\ASTArguments'                             . ' ()']
            ]
        );
    }

    /**
     * testObjectMemberPrimaryPrefixHasExpectedStartLine
     *
     * @return void
     */
    public function testObjectMemberPrimaryPrefixHasExpectedStartLine(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertEquals(4, $prefix->getStartLine());
    }

    /**
     * testObjectMemberPrimaryPrefixHasExpectedStartColumn
     *
     * @return void
     */
    public function testObjectMemberPrimaryPrefixHasExpectedStartColumn(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertEquals(5, $prefix->getStartColumn());
    }

    /**
     * testObjectMemberPrimaryPrefixHasExpectedEndLine
     *
     * @return void
     */
    public function testObjectMemberPrimaryPrefixHasExpectedEndLine(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertEquals(6, $prefix->getEndLine());
    }

    /**
     * testObjectMemberPrimaryPrefixHasExpectedEndColumn
     *
     * @return void
     */
    public function testObjectMemberPrimaryPrefixHasExpectedEndColumn(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertEquals(10, $prefix->getEndColumn());
    }

    /**
     * testObjectPropertyMemberPrimaryPrefixIsStaticReturnsFalse
     *
     * @return void
     */
    public function testObjectPropertyMemberPrimaryPrefixIsStaticReturnsFalse(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertFalse($prefix->isStatic());
    }

    /**
     * testObjectMethodMemberPrimaryPrefixIsStaticReturnsFalse
     *
     * @return void
     */
    public function testObjectMethodMemberPrimaryPrefixIsStaticReturnsFalse(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertFalse($prefix->isStatic());
    }

    /**
     * testClassPropertyMemberPrimaryPrefixIsStaticReturnsTrue
     *
     * @return void
     */
    public function testClassPropertyMemberPrimaryPrefixIsStaticReturnsTrue(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertTrue($prefix->isStatic());
    }

    /**
     * testClassMethodMemberPrimaryPrefixIsStaticReturnsTrue
     *
     * @return void
     */
    public function testClassMethodMemberPrimaryPrefixIsStaticReturnsTrue(): void
    {
        $prefix = $this->getFirstMemberPrimaryPrefixInFunction();
        $this->assertTrue($prefix->isStatic());
    }

    /**
     * Returns a test member primary prefix.
     *
     * @return \PDepend\Source\AST\ASTMemberPrimaryPrefix
     */
    private function getFirstMemberPrimaryPrefixInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix'
        );
    }
}
