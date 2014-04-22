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
 * Test case for the {@link \PDepend\Source\AST\ASTMethodPostfix} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTInvocation
 * @covers \PDepend\Source\AST\ASTMethodPostfix
 * @group unittest
 */
class ASTMethodPostfixTest extends \PDepend\Source\AST\ASTNodeTest
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
            'PDepend\\Source\\AST\\ASTClassOrInterfaceReference',
            'PDepend\\Source\\AST\\ASTMethodPostfix',
            'PDepend\\Source\\AST\\ASTVariable',
            'PDepend\\Source\\AST\\ASTArguments'
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
            'PDepend\\Source\\AST\\ASTClassOrInterfaceReference',
            'PDepend\\Source\\AST\\ASTMethodPostfix',
            'PDepend\\Source\\AST\\ASTArrayIndexExpression',
            'PDepend\\Source\\AST\\ASTVariable',
            'PDepend\\Source\\AST\\ASTLiteral',
            'PDepend\\Source\\AST\\ASTArguments'
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
                'PDepend\\Source\\AST\\ASTCompoundExpression' . " ()", array(
                'PDepend\\Source\\AST\\ASTLiteral'            . " ('method')"),
                'PDepend\\Source\\AST\\ASTArguments'          . " ()"
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
                'PDepend\\Source\\AST\\ASTCompoundVariable' . " ($)", array(
                'PDepend\\Source\\AST\\ASTLiteral'          . " ('method')"),
                'PDepend\\Source\\AST\\ASTArguments'        . " ()"
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
                'PDepend\\Source\\AST\\ASTVariableVariable' . ' ($)', array(
                'PDepend\\Source\\AST\\ASTVariable'         . ' ($method)'),
                'PDepend\\Source\\AST\\ASTArguments'        . ' ()'
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
                'PDepend\\Source\\AST\\ASTCompoundExpression' . " ()", array(
                'PDepend\\Source\\AST\\ASTLiteral'            . " ('method')"),
                'PDepend\\Source\\AST\\ASTArguments'          . " ()"
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
                'PDepend\\Source\\AST\\ASTCompoundVariable' . " ($)", array(
                'PDepend\\Source\\AST\\ASTLiteral'          . " ('method')"),
                'PDepend\\Source\\AST\\ASTArguments'        . " ()"
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
                'PDepend\\Source\\AST\\ASTVariableVariable' . ' ($)', array(
                'PDepend\\Source\\AST\\ASTVariable'         . ' ($method)'),
                'PDepend\\Source\\AST\\ASTArguments'        . ' ()'
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
        $this->assertEquals(6, $postfix->getStartLine());
    }

    /**
     * testObjectMethodPostfixHasExpectedStartColumn
     *
     * @return void
     */
    public function testObjectMethodPostfixHasExpectedStartColumn()
    {
        $postfix = $this->_getFirstMethodPostfixInFunction();
        $this->assertEquals(13, $postfix->getStartColumn());
    }

    /**
     * testObjectMethodPostfixHasExpectedEndLine
     *
     * @return void
     */
    public function testObjectMethodPostfixHasExpectedEndLine()
    {
        $postfix = $this->_getFirstMethodPostfixInFunction();
        $this->assertEquals(7, $postfix->getEndLine());
    }

    /**
     * testObjectMethodPostfixHasExpectedEndColumn
     *
     * @return void
     */
    public function testObjectMethodPostfixHasExpectedEndColumn()
    {
        $postfix = $this->_getFirstMethodPostfixInFunction();
        $this->assertEquals(17, $postfix->getEndColumn());
    }

    /**
     * testClassMethodPostfixHasExpectedStartLine
     *
     * @return void
     */
    public function testClassMethodPostfixHasExpectedStartLine()
    {
        $postfix = $this->_getFirstMethodPostfixInFunction();
        $this->assertEquals(6, $postfix->getStartLine());
    }

    /**
     * testClassMethodPostfixHasExpectedStartColumn
     *
     * @return void
     */
    public function testClassMethodPostfixHasExpectedStartColumn()
    {
        $postfix = $this->_getFirstMethodPostfixInFunction();
        $this->assertEquals(13, $postfix->getStartColumn());
    }

    /**
     * testClassMethodPostfixHasExpectedEndLine
     *
     * @return void
     */
    public function testClassMethodPostfixHasExpectedEndLine()
    {
        $postfix = $this->_getFirstMethodPostfixInFunction();
        $this->assertEquals(7, $postfix->getEndLine());
    }

    /**
     * testClassMethodPostfixHasExpectedEndColumn
     *
     * @return void
     */
    public function testClassMethodPostfixHasExpectedEndColumn()
    {
        $postfix = $this->_getFirstMethodPostfixInFunction();
        $this->assertEquals(17, $postfix->getEndColumn());
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
            'PDepend\\Source\\AST\\ASTVariable',
            'PDepend\\Source\\AST\\ASTMethodPostfix',
            'PDepend\\Source\\AST\\ASTIdentifier',
            'PDepend\\Source\\AST\\ASTArguments'
        );

        $this->assertGraphEquals($prefix, $expected);
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
            'PDepend\\Source\\AST\\ASTVariable',
            'PDepend\\Source\\AST\\ASTMethodPostfix',
            'PDepend\\Source\\AST\\ASTVariable',
            'PDepend\\Source\\AST\\ASTArguments'
        );

        $this->assertGraphEquals($prefix, $expected);
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
            'PDepend\\Source\\AST\\ASTVariable',
            'PDepend\\Source\\AST\\ASTMethodPostfix',
            'PDepend\\Source\\AST\\ASTVariableVariable',
            'PDepend\\Source\\AST\\ASTVariable',
            'PDepend\\Source\\AST\\ASTArguments'
        );

        $this->assertGraphEquals($prefix, $expected);
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
            'PDepend\\Source\\AST\\ASTVariable',
            'PDepend\\Source\\AST\\ASTMethodPostfix',
            'PDepend\\Source\\AST\\ASTCompoundVariable',
            'PDepend\\Source\\AST\\ASTConstant',
            'PDepend\\Source\\AST\\ASTArguments'
        );

        $this->assertGraphEquals($prefix, $expected);
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
            'PDepend\\Source\\AST\\ASTClassOrInterfaceReference',
            'PDepend\\Source\\AST\\ASTMethodPostfix',
            'PDepend\\Source\\AST\\ASTIdentifier',
            'PDepend\\Source\\AST\\ASTArguments'
        );

        $this->assertGraphEquals($prefix, $expected);
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
            'PDepend\\Source\\AST\\ASTClassOrInterfaceReference',
            'PDepend\\Source\\AST\\ASTMethodPostfix',
            'PDepend\\Source\\AST\\ASTVariable',
            'PDepend\\Source\\AST\\ASTArguments'
        );

        $this->assertGraphEquals($prefix, $expected);
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
            'PDepend\\Source\\AST\\ASTClassOrInterfaceReference',
            'PDepend\\Source\\AST\\ASTMethodPostfix',
            'PDepend\\Source\\AST\\ASTVariableVariable',
            'PDepend\\Source\\AST\\ASTVariable',
            'PDepend\\Source\\AST\\ASTArguments'
        );

        $this->assertGraphEquals($prefix, $expected);
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
            'PDepend\\Source\\AST\\ASTClassOrInterfaceReference',
            'PDepend\\Source\\AST\\ASTMethodPostfix',
            'PDepend\\Source\\AST\\ASTCompoundVariable',
            'PDepend\\Source\\AST\\ASTConstant',
            'PDepend\\Source\\AST\\ASTArguments',
        );

        $this->assertGraphEquals($prefix, $expected);
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
            'PDepend\\Source\\AST\\ASTClassOrInterfaceReference',
            'PDepend\\Source\\AST\\ASTMethodPostfix',
            'PDepend\\Source\\AST\\ASTVariableVariable',
            'PDepend\\Source\\AST\\ASTCompoundVariable',
            'PDepend\\Source\\AST\\ASTConstant',
            'PDepend\\Source\\AST\\ASTArguments',
        );

        $this->assertGraphEquals($prefix, $expected);
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
            'PDepend\\Source\\AST\\ASTClassOrInterfaceReference',
            'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
            'PDepend\\Source\\AST\\ASTMethodPostfix',
            'PDepend\\Source\\AST\\ASTIdentifier',
            'PDepend\\Source\\AST\\ASTArguments',
            'PDepend\\Source\\AST\\ASTMethodPostfix',
            'PDepend\\Source\\AST\\ASTIdentifier',
            'PDepend\\Source\\AST\\ASTArguments'
        );

        $this->assertGraphEquals($prefix, $expected);
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
            'PDepend\\Source\\AST\\ASTVariable',
            'PDepend\\Source\\AST\\ASTMethodPostfix',
            'PDepend\\Source\\AST\\ASTIdentifier',
            'PDepend\\Source\\AST\\ASTArguments'
        );

        $this->assertGraphEquals($prefix, $expected);
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
            'PDepend\\Source\\AST\\ASTSelfReference',
            'PDepend\\Source\\AST\\ASTMethodPostfix',
            'PDepend\\Source\\AST\\ASTIdentifier',
            'PDepend\\Source\\AST\\ASTArguments',
        );

        $this->assertGraphEquals($prefix, $expected);
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
            'PDepend\\Source\\AST\\ASTParentReference',
            'PDepend\\Source\\AST\\ASTMethodPostfix',
            'PDepend\\Source\\AST\\ASTIdentifier',
            'PDepend\\Source\\AST\\ASTArguments',
        );

        $this->assertGraphEquals($prefix, $expected);
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
            'PDepend\\Source\\AST\\ASTStaticReference',
            'PDepend\\Source\\AST\\ASTMethodPostfix',
            'PDepend\\Source\\AST\\ASTIdentifier',
            'PDepend\\Source\\AST\\ASTArguments',
        );

        $this->assertGraphEquals($prefix, $expected);
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
            'PDepend\\Source\\AST\\ASTVariable',
            'PDepend\\Source\\AST\\ASTMethodPostfix',
            'PDepend\\Source\\AST\\ASTArrayIndexExpression',
            'PDepend\\Source\\AST\\ASTVariable',
            'PDepend\\Source\\AST\\ASTLiteral',
            'PDepend\\Source\\AST\\ASTArguments',
        );

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return \PDepend\Source\AST\ASTMethodPostfix
     */
    private function _getFirstMethodPostfixInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            self::getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTMethodPostfix'
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return \PDepend\Source\AST\ASTMemberPrimaryPrefix
     */
    private function _getFirstMemberPrimaryPrefixInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, 'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix'
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return \PDepend\Source\AST\ASTMemberPrimaryPrefix
     */
    private function _getFirstMemberPrimaryPrefixInClass($testCase)
    {
        return $this->getFirstNodeOfTypeInClass(
            $testCase, 'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix'
        );
    }

    /**
     * Creates a method postfix node.
     *
     * @return \PDepend\Source\AST\ASTMethodPostfix
     */
    protected function createNodeInstance()
    {
        return new \PDepend\Source\AST\ASTMethodPostfix(__FUNCTION__);
    }
}
