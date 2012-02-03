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
 * Test case for the {@link PHP_Depend_Code_ASTMethodPostfix} class.
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
 * @covers PHP_Depend_Code_ASTInvocation
 * @covers PHP_Depend_Code_ASTMethodPostfix
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTMethodPostfixTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testGetImageForVariableMethod
     *
     * <code>
     * $object->$method(23);
     * </code>
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetImageForVariableMethod()
    {
        $postfix = $this->_getFirstMethodPostfixInFunction();
        $this->assertEquals('$method', $postfix->getImage());
    }

    /**
     * testGetImageForVariableStaticMethod
     *
     * <code>
     * Clazz::$method(23);
     * </code>
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetImageForVariableStaticMethod()
    {
        $postfix = $this->_getFirstMethodPostfixInFunction();
        $this->assertEquals('$method', $postfix->getImage());
    }

    /**
     * testGetImageForArrayIndexedVariableStaticMethod
     *
     * <code>
     * Clazz::$method[42](23);
     * </code>
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetImageForArrayIndexedVariableStaticMethod()
    {
        $postfix = $this->_getFirstMethodPostfixInFunction();
        $this->assertEquals('$method', $postfix->getImage());
    }

    /**
     * testGetImageForMultiArrayIndexedVariableStaticMethod
     *
     * <code>
     * Clazz::$method[42][17][23]();
     * </code>
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetImageForMultiArrayIndexedVariableStaticMethod()
    {
        $postfix = $this->_getFirstMethodPostfixInFunction();
        $this->assertEquals('$method', $postfix->getImage());
    }

    /**
     * testMethodPostfixGraphForVariable
     *
     * @return void
     */
    public function testMethodPostfixGraphForVariable()
    {
        $postfix  = $this->_getFirstMemberPrimaryPrefixInFunction(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ,
            PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTArguments::CLAZZ
        );

        $this->assertGraphEquals($postfix, $expected);
    }

    /**
     * testMethodPostfixGraphForArrayIndexedVariable
     *
     * @return void
     */
    public function testMethodPostfixGraphForArrayIndexedVariable()
    {
        $postfix  = $this->_getFirstMemberPrimaryPrefixInFunction(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ,
            PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
            PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ,
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTLiteral::CLAZZ,
            PHP_Depend_Code_ASTArguments::CLAZZ
        );

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
     * @return void
     * @since 1.0.0
     */
    public function testMethodPostfixGraphForCompoundExpression()
    {
        $this->assertGraph(
            $this->_getFirstMethodPostfixInFunction(),
            array(
                PHP_Depend_Code_ASTCompoundExpression::CLAZZ . " ()", array(
                PHP_Depend_Code_ASTLiteral::CLAZZ            . " ('method')"),
                PHP_Depend_Code_ASTArguments::CLAZZ          . " ()"
            )
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
     * @return void
     * @since 1.0.0
     */
    public function testMethodPostfixGraphForCompoundVariable()
    {
        $this->assertGraph(
            $this->_getFirstMethodPostfixInFunction(),
            array(
                PHP_Depend_Code_ASTCompoundVariable::CLAZZ . " ($)", array(
                PHP_Depend_Code_ASTLiteral::CLAZZ          . " ('method')"),
                PHP_Depend_Code_ASTArguments::CLAZZ        . " ()"
            )
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
     * @return void
     * @since 1.0.0
     */
    public function testMethodPostfixGraphForVariableVariable()
    {
        $this->assertGraph(
            $this->_getFirstMethodPostfixInFunction(),
            array(
                PHP_Depend_Code_ASTVariableVariable::CLAZZ . ' ($)', array(
                PHP_Depend_Code_ASTVariable::CLAZZ         . ' ($method)'),
                PHP_Depend_Code_ASTArguments::CLAZZ        . ' ()'
            )
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
     * @return void
     * @since 1.0.0
     */
    public function testStaticMethodPostfixGraphForCompoundExpression()
    {
        $this->assertGraph(
            $this->_getFirstMethodPostfixInFunction(),
            array(
                PHP_Depend_Code_ASTCompoundExpression::CLAZZ . " ()", array(
                PHP_Depend_Code_ASTLiteral::CLAZZ            . " ('method')"),
                PHP_Depend_Code_ASTArguments::CLAZZ          . " ()"
            )
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
     * @return void
     * @since 1.0.0
     */
    public function testStaticMethodPostfixGraphForCompoundVariable()
    {
        $this->assertGraph(
            $this->_getFirstMethodPostfixInFunction(),
            array(
                PHP_Depend_Code_ASTCompoundVariable::CLAZZ . " ($)", array(
                PHP_Depend_Code_ASTLiteral::CLAZZ          . " ('method')"),
                PHP_Depend_Code_ASTArguments::CLAZZ        . " ()"
            )
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
     * @return void
     * @since 1.0.0
     */
    public function testStaticMethodPostfixGraphForVariableVariable()
    {
        $this->assertGraph(
            $this->_getFirstMethodPostfixInFunction(),
            array(
                PHP_Depend_Code_ASTVariableVariable::CLAZZ . ' ($)', array(
                PHP_Depend_Code_ASTVariable::CLAZZ         . ' ($method)'),
                PHP_Depend_Code_ASTArguments::CLAZZ        . ' ()'
            )
        );
    }

    /**
     * testObjectMethodPostfixHasExpectedStartLine
     *
     * @return void
     */
    public function testObjectMethodPostfixHasExpectedStartLine()
    {
        $postfix = $this->_getFirstMethodPostfixInFunction();
        self::assertEquals(6, $postfix->getStartLine());
    }

    /**
     * testObjectMethodPostfixHasExpectedStartColumn
     *
     * @return void
     */
    public function testObjectMethodPostfixHasExpectedStartColumn()
    {
        $postfix = $this->_getFirstMethodPostfixInFunction();
        self::assertEquals(13, $postfix->getStartColumn());
    }

    /**
     * testObjectMethodPostfixHasExpectedEndLine
     *
     * @return void
     */
    public function testObjectMethodPostfixHasExpectedEndLine()
    {
        $postfix = $this->_getFirstMethodPostfixInFunction();
        self::assertEquals(7, $postfix->getEndLine());
    }

    /**
     * testObjectMethodPostfixHasExpectedEndColumn
     *
     * @return void
     */
    public function testObjectMethodPostfixHasExpectedEndColumn()
    {
        $postfix = $this->_getFirstMethodPostfixInFunction();
        self::assertEquals(17, $postfix->getEndColumn());
    }

    /**
     * testClassMethodPostfixHasExpectedStartLine
     *
     * @return void
     */
    public function testClassMethodPostfixHasExpectedStartLine()
    {
        $postfix = $this->_getFirstMethodPostfixInFunction();
        self::assertEquals(6, $postfix->getStartLine());
    }

    /**
     * testClassMethodPostfixHasExpectedStartColumn
     *
     * @return void
     */
    public function testClassMethodPostfixHasExpectedStartColumn()
    {
        $postfix = $this->_getFirstMethodPostfixInFunction();
        self::assertEquals(13, $postfix->getStartColumn());
    }

    /**
     * testClassMethodPostfixHasExpectedEndLine
     *
     * @return void
     */
    public function testClassMethodPostfixHasExpectedEndLine()
    {
        $postfix = $this->_getFirstMethodPostfixInFunction();
        self::assertEquals(7, $postfix->getEndLine());
    }

    /**
     * testClassMethodPostfixHasExpectedEndColumn
     *
     * @return void
     */
    public function testClassMethodPostfixHasExpectedEndColumn()
    {
        $postfix = $this->_getFirstMethodPostfixInFunction();
        self::assertEquals(17, $postfix->getEndColumn());
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForSimpleInvocation()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInFunction(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
            PHP_Depend_Code_ASTIdentifier::CLAZZ,
            PHP_Depend_Code_ASTArguments::CLAZZ
        );

        self::assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForVariableInvocation()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInFunction(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTArguments::CLAZZ
        );

        self::assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForVariableVariableInvocation()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInFunction(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
            PHP_Depend_Code_ASTVariableVariable::CLAZZ,
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTArguments::CLAZZ
        );

        self::assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForCompoundVariableInvocation()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInFunction(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
            PHP_Depend_Code_ASTCompoundVariable::CLAZZ,
            PHP_Depend_Code_ASTConstant::CLAZZ,
            PHP_Depend_Code_ASTArguments::CLAZZ
        );

        self::assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     * 
     * @return void
     */
    public function testMethodPostfixStructureForSimpleStaticInvocation()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInFunction(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ,
            PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
            PHP_Depend_Code_ASTIdentifier::CLAZZ,
            PHP_Depend_Code_ASTArguments::CLAZZ
        );

        self::assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForVariableStaticInvocation()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInFunction(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ,
            PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTArguments::CLAZZ
        );

        self::assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForVariableVariableStaticInvocation()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInFunction(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ,
            PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
            PHP_Depend_Code_ASTVariableVariable::CLAZZ,
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTArguments::CLAZZ
        );

        self::assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForCompoundVariableStaticInvocation()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInFunction(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ,
            PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
            PHP_Depend_Code_ASTCompoundVariable::CLAZZ,
            PHP_Depend_Code_ASTConstant::CLAZZ,
            PHP_Depend_Code_ASTArguments::CLAZZ,
        );

        self::assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForVariableCompoundVariableStaticInvocation()
    {
        $prefix = $this->_getFirstMemberPrimaryPrefixInFunction(__METHOD__);
        
        $expected = array(
            PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ,
            PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
            PHP_Depend_Code_ASTVariableVariable::CLAZZ,
            PHP_Depend_Code_ASTCompoundVariable::CLAZZ,
            PHP_Depend_Code_ASTConstant::CLAZZ,
            PHP_Depend_Code_ASTArguments::CLAZZ,
        );

        self::assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForStaticInvocationWithConsecutiveInvocation()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInFunction(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ,
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ,
            PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
            PHP_Depend_Code_ASTIdentifier::CLAZZ,
            PHP_Depend_Code_ASTArguments::CLAZZ,
            PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
            PHP_Depend_Code_ASTIdentifier::CLAZZ,
            PHP_Depend_Code_ASTArguments::CLAZZ
        );

        self::assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForStaticInvocationOnVariable()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInFunction(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
            PHP_Depend_Code_ASTIdentifier::CLAZZ,
            PHP_Depend_Code_ASTArguments::CLAZZ
        );

        self::assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForSelfInvocation()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInClass(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTSelfReference::CLAZZ,
            PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
            PHP_Depend_Code_ASTIdentifier::CLAZZ,
            PHP_Depend_Code_ASTArguments::CLAZZ,
        );

        self::assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForParentInvocation()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInClass(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTParentReference::CLAZZ,
            PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
            PHP_Depend_Code_ASTIdentifier::CLAZZ,
            PHP_Depend_Code_ASTArguments::CLAZZ,
        );

        self::assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object graph.
     *
     * @return void
     */
    public function testMethodPostfixGraphForStaticReferenceInvocation()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInClass(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTStaticReference::CLAZZ,
            PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
            PHP_Depend_Code_ASTIdentifier::CLAZZ,
            PHP_Depend_Code_ASTArguments::CLAZZ,
        );

        self::assertGraphEquals($prefix, $expected);
    }

    /**
     * testMethodPostfixGraphForArrayElementInvocation
     *
     * <code>
     * $this->$foo[0]();
     * </code>
     *
     * @return void
     */
    public function testMethodPostfixGraphForVariableArrayElementInvocation()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInClass(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
            PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ,
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTLiteral::CLAZZ,
            PHP_Depend_Code_ASTArguments::CLAZZ,
        );

        self::assertGraphEquals($prefix, $expected);
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return PHP_Depend_Code_ASTMethodPostfix
     */
    private function _getFirstMethodPostfixInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            self::getCallingTestMethod(),
            PHP_Depend_Code_ASTMethodPostfix::CLAZZ
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTMemberPrimaryPrefix
     */
    private function _getFirstMemberPrimaryPrefixInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTMemberPrimaryPrefix
     */
    private function _getFirstMemberPrimaryPrefixInClass($testCase)
    {
        return $this->getFirstNodeOfTypeInClass(
            $testCase, PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );
    }

    /**
     * Creates a method postfix node.
     *
     * @return PHP_Depend_Code_ASTMethodPostfix
     */
    protected function createNodeInstance()
    {
        return new PHP_Depend_Code_ASTMethodPostfix(__FUNCTION__);
    }
}
