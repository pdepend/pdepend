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
 * Test case for the {@link PHP_Depend_Code_ASTMemberPrimaryPrefix} class.
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
 * @covers PHP_Depend_Code_ASTMemberPrimaryPrefix
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTMemberPrimaryPrefixTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testMemberPrimaryPrefixGraphDereferencedFromArray
     *
     * @return void
     * @since 1.0.0
     */
    public function testMemberPrimaryPrefixGraphDereferencedFromArray()
    {
        $this->_getFirstMemberPrimaryPrefixInFunction();
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
    public function testMemberPrimaryPrefixGraphInIssetExpression()
    {
        $this->assertGraph(
            $this->_getFirstMemberPrimaryPrefixInFunction(),
            array(
                PHP_Depend_Code_ASTVariable::CLAZZ                          . ' ($object)',
                PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ               . ' (->)', array(
                    PHP_Depend_Code_ASTPropertyPostfix::CLAZZ               . ' (plots)', array(
                        PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ      . ' ()', array(
                            PHP_Depend_Code_ASTIdentifier::CLAZZ            . ' (plots)',
                            PHP_Depend_Code_ASTLiteral::CLAZZ               . ' (0)')),
                    PHP_Depend_Code_ASTPropertyPostfix::CLAZZ               . ' (coords)', array(
                        PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ      . ' ()', array(
                            PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ  . ' ()', array(
                                PHP_Depend_Code_ASTIdentifier::CLAZZ        . ' (coords)',
                                PHP_Depend_Code_ASTLiteral::CLAZZ           . ' (0)'),
                            PHP_Depend_Code_ASTVariable::CLAZZ              . ' ($i)')))
            )
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
    public function testMemberPrimaryPrefixGraphWithDynamicClassAndStaticConstant()
    {
        $this->assertGraphEquals(
            $this->_getFirstMemberPrimaryPrefixInFunction(),
            array(
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTConstantPostfix::CLAZZ,
                PHP_Depend_Code_ASTIdentifier::CLAZZ
            )
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
    public function testMemberPrimaryPrefixGraphWithDynamicClassAndStaticProperty()
    {
        $prefix = $this->_getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraphEquals(
            $prefix,
            array(
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
                PHP_Depend_Code_ASTVariable::CLAZZ
            )
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
    public function testMemberPrimaryPrefixGraphWithDynamicClassAndStaticMethod()
    {
        $prefix = $this->_getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraphEquals(
            $prefix,
            array(
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
                PHP_Depend_Code_ASTIdentifier::CLAZZ,
                PHP_Depend_Code_ASTArguments::CLAZZ,
                PHP_Depend_Code_ASTLiteral::CLAZZ
            )
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
    public function testMemberPrimaryPrefixGraphWithDynamicClassAndDynamicMethod()
    {
        $prefix = $this->_getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraphEquals(
            $prefix,
            array(
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTArguments::CLAZZ,
                PHP_Depend_Code_ASTLiteral::CLAZZ
            )
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
    public function testMemberPrimaryPrefixGraphStartedWithAllocationAndMethodInvocation()
    {
        $prefix = $this->_getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraph(
            $prefix,
            array(
                PHP_Depend_Code_ASTAllocationExpression::CLAZZ . ' (new)', array(
                    PHP_Depend_Code_ASTClassReference::CLAZZ   . ' (MyClass)',
                    PHP_Depend_Code_ASTArguments::CLAZZ        . ' ()'),
                PHP_Depend_Code_ASTMethodPostfix::CLAZZ        . ' (foo)', array(
                    PHP_Depend_Code_ASTIdentifier::CLAZZ       . ' (foo)',
                    PHP_Depend_Code_ASTArguments::CLAZZ        . ' ()'
            ))
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
    public function testMemberPrimaryPrefixGraphStartedWithAllocationAndMethodChain()
    {
        $prefix = $this->_getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraph(
            $prefix,
            array(
                PHP_Depend_Code_ASTAllocationExpression::CLAZZ . ' (new)', array(
                    PHP_Depend_Code_ASTClassReference::CLAZZ   . ' (MyClass)'),
                PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ  . ' (->)', array(
                    PHP_Depend_Code_ASTMethodPostfix::CLAZZ    . ' (foo)', array(
                        PHP_Depend_Code_ASTIdentifier::CLAZZ   . ' (foo)',
                        PHP_Depend_Code_ASTArguments::CLAZZ    . ' ()'),
                    PHP_Depend_Code_ASTMethodPostfix::CLAZZ    . ' (bar)', array(
                        PHP_Depend_Code_ASTIdentifier::CLAZZ   . ' (bar)',
                        PHP_Depend_Code_ASTArguments::CLAZZ    . ' ()'))
            )
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
    public function testMemberPrimaryPrefixGraphStartedWithAllocationAndPropertyAccess()
    {
        $prefix = $this->_getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraph(
            $prefix,
            array(
                PHP_Depend_Code_ASTAllocationExpression::CLAZZ . ' (new)', array(
                    PHP_Depend_Code_ASTClassReference::CLAZZ   . ' (MyClass)',
                    PHP_Depend_Code_ASTArguments::CLAZZ        . ' ()'),
                PHP_Depend_Code_ASTPropertyPostfix::CLAZZ      . ' (foo)', array(
                    PHP_Depend_Code_ASTIdentifier::CLAZZ       . ' (foo)')
            )
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
    public function testMemberPrimaryPrefixGraphStartedWithAllocationAndPropertyChain()
    {
        $prefix = $this->_getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraph(
            $prefix,
            array(
                PHP_Depend_Code_ASTAllocationExpression::CLAZZ . ' (new)', array(
                    PHP_Depend_Code_ASTClassReference::CLAZZ   . ' (MyClass)'),
                PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ  . ' (->)', array(
                    PHP_Depend_Code_ASTPropertyPostfix::CLAZZ  . ' (foo)', array(
                        PHP_Depend_Code_ASTIdentifier::CLAZZ   . ' (foo)'),
                    PHP_Depend_Code_ASTPropertyPostfix::CLAZZ  . ' (bar)', array(
                        PHP_Depend_Code_ASTIdentifier::CLAZZ   . ' (bar)'))
            )
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
    public function testMemberPrimaryPrefixGraphForObjectPropertyAccess()
    {
        $prefix = $this->_getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraphEquals(
            $prefix,
            array(
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
                PHP_Depend_Code_ASTIdentifier::CLAZZ
            )
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
    public function testMemberPrimaryPrefixGraphForObjectWithVariablePropertyAccess()
    {
        $prefix = $this->_getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraphEquals(
            $prefix,
            array(
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
                PHP_Depend_Code_ASTVariable::CLAZZ
            )
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
    public function testMemberPrimaryPrefixGraphForObjectMethodAccess()
    {
        $prefix = $this->_getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraphEquals(
            $prefix,
            array(
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
                PHP_Depend_Code_ASTIdentifier::CLAZZ,
                PHP_Depend_Code_ASTArguments::CLAZZ
            )
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
    public function testMemberPrimaryPrefixGraphForObjectWithVariableMethodAccess()
    {
        $prefix = $this->_getFirstMemberPrimaryPrefixInFunction();
        $this->assertGraph(
            $prefix,
            array(
                PHP_Depend_Code_ASTVariable::CLAZZ        . ' ($object)',
                PHP_Depend_Code_ASTMethodPostfix::CLAZZ   . ' ($method)', array(
                    PHP_Depend_Code_ASTVariable::CLAZZ    . ' ($method)',
                    PHP_Depend_Code_ASTArguments::CLAZZ   . ' ()', array(
                        PHP_Depend_Code_ASTLiteral::CLAZZ . ' (23)',
                        PHP_Depend_Code_ASTLiteral::CLAZZ . ' (42)'))
            )
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
    public function testGraphDereferencedArrayFromFunctionCallAndMethodInvocation()
    {
        $this->assertGraphEquals(
            $this->_getFirstMemberPrimaryPrefixInFunction(),
            array(
                PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ,
                PHP_Depend_Code_ASTFunctionPostfix::CLAZZ,
                PHP_Depend_Code_ASTIdentifier::CLAZZ,
                PHP_Depend_Code_ASTArguments::CLAZZ,
                PHP_Depend_Code_ASTLiteral::CLAZZ,
                PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
                PHP_Depend_Code_ASTIdentifier::CLAZZ,
                PHP_Depend_Code_ASTArguments::CLAZZ
            )
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
    public function testMemberPrimaryPrefixGraphForChainedObjectMemberAccess()
    {
        $this->assertGraph(
            $this->_getFirstMemberPrimaryPrefixInFunction(),
            array(
                PHP_Depend_Code_ASTVariable::CLAZZ                    . ' ($obj)',
                PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ         . ' (->)',     array(
                    PHP_Depend_Code_ASTPropertyPostfix::CLAZZ         . ' (foo)',  array(
                        PHP_Depend_Code_ASTIdentifier::CLAZZ          . ' (foo)'),
                        PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ . ' (->)', array(
                            PHP_Depend_Code_ASTMethodPostfix::CLAZZ   . ' (bar)', array(
                                PHP_Depend_Code_ASTIdentifier::CLAZZ  . ' (bar)',
                                PHP_Depend_Code_ASTArguments::CLAZZ   . ' ()'),
                            PHP_Depend_Code_ASTMethodPostfix::CLAZZ   . ' (baz)', array(
                                PHP_Depend_Code_ASTIdentifier::CLAZZ  . ' (baz)',
                                PHP_Depend_Code_ASTArguments::CLAZZ   . ' ()')))
            )
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
    public function testGraphDereferencedArrayFromFunctionCallAndMultipleMethodInvocations()
    {
        $this->assertGraph(
            $this->_getFirstMemberPrimaryPrefixInFunction(),
            array(
                PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ         . ' ()',    array(
                    PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ      . ' (->)',  array(
                        PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ . ' ()',    array(
                            PHP_Depend_Code_ASTFunctionPostfix::CLAZZ  . ' (foo)', array(
                                PHP_Depend_Code_ASTIdentifier::CLAZZ   . ' (foo)',
                                PHP_Depend_Code_ASTArguments::CLAZZ    . ' ()',    array(
                                    PHP_Depend_Code_ASTLiteral::CLAZZ  . ' (23)')),
                            PHP_Depend_Code_ASTLiteral::CLAZZ          . ' (42)'),
                        PHP_Depend_Code_ASTMethodPostfix::CLAZZ        . ' (bar)', array(
                            PHP_Depend_Code_ASTIdentifier::CLAZZ       . ' (bar)',
                            PHP_Depend_Code_ASTArguments::CLAZZ        . ' ()')),
                    PHP_Depend_Code_ASTLiteral::CLAZZ                  . ' (17)'),
                PHP_Depend_Code_ASTMethodPostfix::CLAZZ                . ' (baz)', array(
                    PHP_Depend_Code_ASTIdentifier::CLAZZ               . ' (baz)',
                    PHP_Depend_Code_ASTArguments::CLAZZ                . ' ()')
            )
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
    public function testGraphDereferencedArrayFromStaticMethodCallAndMultipleMethodInvocations()
    {
        $this->assertGraph(
            $this->_getFirstMemberPrimaryPrefixInFunction(),
            array(
                PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ                      . ' ()',    array(
                    PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ                   . ' (->)',  array(
                        PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ              . ' ()',    array(
                            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ           . ' (::)', array(
                                PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ . ' (Clazz)',
                                PHP_Depend_Code_ASTMethodPostfix::CLAZZ             . ' (foo)', array(
                                    PHP_Depend_Code_ASTIdentifier::CLAZZ            . ' (foo)',
                                    PHP_Depend_Code_ASTArguments::CLAZZ             . ' ()',    array(
                                        PHP_Depend_Code_ASTLiteral::CLAZZ           . ' (23)'))),
                            PHP_Depend_Code_ASTLiteral::CLAZZ                       . ' (42)'),
                        PHP_Depend_Code_ASTMethodPostfix::CLAZZ                     . ' (bar)', array(
                            PHP_Depend_Code_ASTIdentifier::CLAZZ                    . ' (bar)',
                            PHP_Depend_Code_ASTArguments::CLAZZ                     . ' ()')),
                    PHP_Depend_Code_ASTLiteral::CLAZZ                               . ' (17)'),
                PHP_Depend_Code_ASTMethodPostfix::CLAZZ                             . ' (baz)', array(
                    PHP_Depend_Code_ASTIdentifier::CLAZZ                            . ' (baz)',
                    PHP_Depend_Code_ASTArguments::CLAZZ                             . ' ()')
            )
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
    public function testGraphDereferencedArrayFromVariableClassStaticMethodCallAndMultipleMethodInvocations()
    {
        $this->assertGraph(
            $this->_getFirstMemberPrimaryPrefixInFunction(),
            array(
                PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ                      . ' ()',    array(
                    PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ                   . ' (->)',  array(
                        PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ              . ' ()',    array(
                            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ           . ' (::)', array(
                                PHP_Depend_Code_ASTVariable::CLAZZ                  . ' ($class)',
                                PHP_Depend_Code_ASTMethodPostfix::CLAZZ             . ' (foo)', array(
                                    PHP_Depend_Code_ASTIdentifier::CLAZZ            . ' (foo)',
                                    PHP_Depend_Code_ASTArguments::CLAZZ             . ' ()',    array(
                                        PHP_Depend_Code_ASTLiteral::CLAZZ           . ' (23)'))),
                            PHP_Depend_Code_ASTLiteral::CLAZZ                       . ' (42)'),
                        PHP_Depend_Code_ASTMethodPostfix::CLAZZ                     . ' (bar)', array(
                            PHP_Depend_Code_ASTIdentifier::CLAZZ                    . ' (bar)',
                            PHP_Depend_Code_ASTArguments::CLAZZ                     . ' ()')),
                    PHP_Depend_Code_ASTLiteral::CLAZZ                               . ' (17)'),
                PHP_Depend_Code_ASTMethodPostfix::CLAZZ                             . ' (baz)', array(
                    PHP_Depend_Code_ASTIdentifier::CLAZZ                            . ' (baz)',
                    PHP_Depend_Code_ASTArguments::CLAZZ                             . ' ()')
            )
        );
    }

    /**
     * testObjectMemberPrimaryPrefixHasExpectedStartLine
     *
     * @return void
     */
    public function testObjectMemberPrimaryPrefixHasExpectedStartLine()
    {
        $prefix = $this->_getFirstMemberPrimaryPrefixInFunction();
        $this->assertEquals(4, $prefix->getStartLine());
    }

    /**
     * testObjectMemberPrimaryPrefixHasExpectedStartColumn
     *
     * @return void
     */
    public function testObjectMemberPrimaryPrefixHasExpectedStartColumn()
    {
        $prefix = $this->_getFirstMemberPrimaryPrefixInFunction();
        $this->assertEquals(5, $prefix->getStartColumn());
    }

    /**
     * testObjectMemberPrimaryPrefixHasExpectedEndLine
     *
     * @return void
     */
    public function testObjectMemberPrimaryPrefixHasExpectedEndLine()
    {
        $prefix = $this->_getFirstMemberPrimaryPrefixInFunction();
        $this->assertEquals(6, $prefix->getEndLine());
    }

    /**
     * testObjectMemberPrimaryPrefixHasExpectedEndColumn
     *
     * @return void
     */
    public function testObjectMemberPrimaryPrefixHasExpectedEndColumn()
    {
        $prefix = $this->_getFirstMemberPrimaryPrefixInFunction();
        $this->assertEquals(10, $prefix->getEndColumn());
    }

    /**
     * testObjectPropertyMemberPrimaryPrefixIsStaticReturnsFalse
     *
     * @return void
     */
    public function testObjectPropertyMemberPrimaryPrefixIsStaticReturnsFalse()
    {
        $prefix = $this->_getFirstMemberPrimaryPrefixInFunction();
        $this->assertFalse($prefix->isStatic());
    }

    /**
     * testObjectMethodMemberPrimaryPrefixIsStaticReturnsFalse
     *
     * @return void
     */
    public function testObjectMethodMemberPrimaryPrefixIsStaticReturnsFalse()
    {
        $prefix = $this->_getFirstMemberPrimaryPrefixInFunction();
        $this->assertFalse($prefix->isStatic());
    }

    /**
     * testClassPropertyMemberPrimaryPrefixIsStaticReturnsTrue
     *
     * @return void
     */
    public function testClassPropertyMemberPrimaryPrefixIsStaticReturnsTrue()
    {
        $prefix = $this->_getFirstMemberPrimaryPrefixInFunction();
        $this->assertTrue($prefix->isStatic());
    }

    /**
     * testClassMethodMemberPrimaryPrefixIsStaticReturnsTrue
     *
     * @return void
     */
    public function testClassMethodMemberPrimaryPrefixIsStaticReturnsTrue()
    {
        $prefix = $this->_getFirstMemberPrimaryPrefixInFunction();
        $this->assertTrue($prefix->isStatic());
    }

    /**
     * Returns a test member primary prefix.
     *
     * @return PHP_Depend_Code_ASTMemberPrimaryPrefix
     */
    private function _getFirstMemberPrimaryPrefixInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );
    }
}
