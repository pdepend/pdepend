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
 * Test case for the {@link PHP_Depend_Code_ASTPropertyPostfix} class.
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
 * @covers PHP_Depend_Builder_Default
 * @covers PHP_Depend_Code_ASTPropertyPostfix
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 *
 * @covers PHP_Depend_Parser
 * @covers PHP_Depend_Code_ASTPropertyPostfix
 */
class PHP_Depend_Code_ASTPropertyPostfixTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testGetImageForArrayIndexedRegularProperty
     * 
     * @return void
     */
    public function testGetImageForArrayIndexedRegularProperty()
    {
        $postfix = $this->_getFirstPropertyPostfixInFunction();
        $this->assertEquals('property', $postfix->getImage());
    }

    /**
     * testGetImageForMultiDimensionalArrayIndexedRegularProperty
     *
     * @return void
     */
    public function testGetImageForMultiDimensionalArrayIndexedRegularProperty()
    {
        $postfix = $this->_getFirstPropertyPostfixInFunction();
        $this->assertEquals('property', $postfix->getImage());
    }

    /**
     * testGetImageForVariableProperty
     * 
     * @return void
     */
    public function testGetImageForVariableProperty()
    {
        $postfix = $this->_getFirstPropertyPostfixInFunction();
        $this->assertEquals('$property', $postfix->getImage());
    }

    /**
     * testGetImageForArrayIndexedVariableProperty
     * 
     * @return void
     */
    public function testGetImageForArrayIndexedVariableProperty()
    {
        $postfix = $this->_getFirstPropertyPostfixInFunction();
        $this->assertEquals('$property', $postfix->getImage());
    }

    /**
     * testPropertyPostfixGraphForArrayElementInvocation
     *
     * <code>
     * $this->$foo[0];
     * </code>
     *
     * @return void
     */
    public function testPropertyPostfixGraphForArrayElementInvocation()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInClass(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
            PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ,
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTLiteral::CLAZZ
        );

        self::assertGraphEquals($prefix, $expected);
    }

    /**
     * testPropertyPostfixGraphForPropertyArrayElementInvocation
     *
     * <code>
     * $this->foo[$bar]();
     * </code>
     *
     * @return void
     */
    public function testPropertyPostfixGraphForPropertyArrayElementInvocation()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInClass(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
            PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ,
            PHP_Depend_Code_ASTIdentifier::CLAZZ,
            PHP_Depend_Code_ASTVariable::CLAZZ
        );

        self::assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     */
    public function testPropertyPostfixStructureForSimpleIdentifierAccess()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInFunction(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
            PHP_Depend_Code_ASTIdentifier::CLAZZ
        );

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     */
    public function testPropertyPostfixStructureForVariableAccess()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInFunction(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
            PHP_Depend_Code_ASTVariable::CLAZZ
        );

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     */
    public function testPropertyPostfixStructureForVariableVariableAccess()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInFunction(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
            PHP_Depend_Code_ASTVariableVariable::CLAZZ,
            PHP_Depend_Code_ASTVariable::CLAZZ
        );

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     */
    public function testPropertyPostfixStructureForCompoundVariableAccess()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInFunction(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
            PHP_Depend_Code_ASTCompoundVariable::CLAZZ,
            PHP_Depend_Code_ASTExpression::CLAZZ,
            PHP_Depend_Code_ASTConstant::CLAZZ,
            PHP_Depend_Code_ASTExpression::CLAZZ,
            PHP_Depend_Code_ASTLiteral::CLAZZ
        );

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     */
    public function testPropertyPostfixStructureForCompoundExpressionAccess()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInFunction(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
            PHP_Depend_Code_ASTCompoundExpression::CLAZZ,
            PHP_Depend_Code_ASTVariable::CLAZZ
        );

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     */
    public function testPropertyPostfixStructureForStaticVariableAccess()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInFunction(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ,
            PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
            PHP_Depend_Code_ASTVariable::CLAZZ
        );

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testPropertyPostfixStructureForStaticAccessOnVariable()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInFunction(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
            PHP_Depend_Code_ASTVariable::CLAZZ
        );

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     */
    public function testPropertyPostfixStructureForSelfVariableAccess()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInClass(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTSelfReference::CLAZZ,
            PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
            PHP_Depend_Code_ASTVariable::CLAZZ
        );

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     */
    public function testPropertyPostfixStructureForParentVariableAccess()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInClass(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTParentReference::CLAZZ,
            PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
            PHP_Depend_Code_ASTVariable::CLAZZ
        );

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * testPropertyPostfixGraphForObjectPropertyArrayIndexExpression
     *
     * <code>
     * $this->arguments[42] = func_get_args();
     * </code>
     *
     * @return void
     */
    public function testPropertyPostfixGraphForObjectPropertyArrayIndexExpression()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInClass(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
            PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ,
            PHP_Depend_Code_ASTIdentifier::CLAZZ,
            PHP_Depend_Code_ASTLiteral::CLAZZ
        );

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * testPropertyPostfixGraphForPropertyArrayIndexExpression
     *
     * <code>
     * self::$arguments[42] = func_get_args();
     * </code>
     *
     * @return void
     */
    public function testPropertyPostfixGraphForStaticPropertyArrayIndexExpression()
    {
        $prefix   = $this->_getFirstMemberPrimaryPrefixInClass(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTSelfReference::CLAZZ,
            PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
            PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ,
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTLiteral::CLAZZ
        );

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     * @expectedException PHP_Depend_Parser_InvalidStateException
     */
    public function testPropertyPostfixSelfVariableInFunctionThrowsExpectedException()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     * @expectedException PHP_Depend_Parser_InvalidStateException
     */
    public function testPropertyPostfixParentVariableInFunctionThrowsExpectedException()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     * @expectedException PHP_Depend_Parser_InvalidStateException
     */
    public function testPropertyPostfixParentVariableInClassWithoutParentThrowsExpectedException()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testPropertyPostfixHasExpectedStartLine
     *
     * @return void
     */
    public function testPropertyPostfixHasExpectedStartLine()
    {
        $postfix = $this->_getFirstPropertyPostfixInFunction(__METHOD__);
        self::assertEquals(4, $postfix->getStartLine());
    }

    /**
     * testPropertyPostfixHasExpectedStartColumn
     *
     * @return void
     */
    public function testPropertyPostfixHasExpectedStartColumn()
    {
        $postfix = $this->_getFirstPropertyPostfixInFunction(__METHOD__);
        self::assertEquals(21, $postfix->getStartColumn());
    }

    /**
     * testPropertyPostfixHasExpectedEndLine
     *
     * @return void
     */
    public function testPropertyPostfixHasExpectedEndLine()
    {
        $postfix = $this->_getFirstPropertyPostfixInFunction(__METHOD__);
        self::assertEquals(4, $postfix->getEndLine());
    }

    /**
     * testPropertyPostfixHasExpectedEndColumn
     *
     * @return void
     */
    public function testPropertyPostfixHasExpectedEndColumn()
    {
        $postfix = $this->_getFirstPropertyPostfixInFunction(__METHOD__);
        self::assertEquals(23, $postfix->getEndColumn());
    }

    /**
     * Creates a field declaration node.
     *
     * @return PHP_Depend_Code_ASTPropertyPostfix
     */
    protected function createNodeInstance()
    {
        return new PHP_Depend_Code_ASTPropertyPostfix(__CLASS__);
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return PHP_Depend_Code_ASTPropertyPostfix
     */
    private function _getFirstPropertyPostfixInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            PHP_Depend_Code_ASTPropertyPostfix::CLAZZ
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
}
