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
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

namespace PHP\Depend\Source\AST;

/**
 * Test case for the {@link \PHP\Depend\Source\AST\ASTPropertyPostfix} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @covers \PHP\Depend\Source\Language\PHP\AbstractPHPParser
 * @covers \PHP\Depend\Source\Language\PHP\PHPBuilder
 * @covers \PHP\Depend\Source\AST\ASTPropertyPostfix
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 *
 * @covers \PHP\Depend\Source\Language\PHP\AbstractPHPParser
 * @covers \PHP\Depend\Source\AST\ASTPropertyPostfix
 */
class ASTPropertyPostfixTest extends \PHP\Depend\Source\AST\ASTNodeTest
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
            \PHP\Depend\Source\AST\ASTVariable::CLAZZ,
            \PHP\Depend\Source\AST\ASTPropertyPostfix::CLAZZ,
            \PHP\Depend\Source\AST\ASTArrayIndexExpression::CLAZZ,
            \PHP\Depend\Source\AST\ASTVariable::CLAZZ,
            \PHP\Depend\Source\AST\ASTLiteral::CLAZZ
        );

        $this->assertGraphEquals($prefix, $expected);
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
            \PHP\Depend\Source\AST\ASTVariable::CLAZZ,
            \PHP\Depend\Source\AST\ASTPropertyPostfix::CLAZZ,
            \PHP\Depend\Source\AST\ASTArrayIndexExpression::CLAZZ,
            \PHP\Depend\Source\AST\ASTIdentifier::CLAZZ,
            \PHP\Depend\Source\AST\ASTVariable::CLAZZ
        );

        $this->assertGraphEquals($prefix, $expected);
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
            \PHP\Depend\Source\AST\ASTVariable::CLAZZ,
            \PHP\Depend\Source\AST\ASTPropertyPostfix::CLAZZ,
            \PHP\Depend\Source\AST\ASTIdentifier::CLAZZ
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
            \PHP\Depend\Source\AST\ASTVariable::CLAZZ,
            \PHP\Depend\Source\AST\ASTPropertyPostfix::CLAZZ,
            \PHP\Depend\Source\AST\ASTVariable::CLAZZ
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
            \PHP\Depend\Source\AST\ASTVariable::CLAZZ,
            \PHP\Depend\Source\AST\ASTPropertyPostfix::CLAZZ,
            \PHP\Depend\Source\AST\ASTVariableVariable::CLAZZ,
            \PHP\Depend\Source\AST\ASTVariable::CLAZZ
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
            \PHP\Depend\Source\AST\ASTVariable::CLAZZ,
            \PHP\Depend\Source\AST\ASTPropertyPostfix::CLAZZ,
            \PHP\Depend\Source\AST\ASTCompoundVariable::CLAZZ,
            \PHP\Depend\Source\AST\ASTExpression::CLAZZ,
            \PHP\Depend\Source\AST\ASTConstant::CLAZZ,
            \PHP\Depend\Source\AST\ASTExpression::CLAZZ,
            \PHP\Depend\Source\AST\ASTLiteral::CLAZZ
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
            \PHP\Depend\Source\AST\ASTVariable::CLAZZ,
            \PHP\Depend\Source\AST\ASTPropertyPostfix::CLAZZ,
            \PHP\Depend\Source\AST\ASTCompoundExpression::CLAZZ,
            \PHP\Depend\Source\AST\ASTVariable::CLAZZ
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
            \PHP\Depend\Source\AST\ASTClassOrInterfaceReference::CLAZZ,
            \PHP\Depend\Source\AST\ASTPropertyPostfix::CLAZZ,
            \PHP\Depend\Source\AST\ASTVariable::CLAZZ
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
            \PHP\Depend\Source\AST\ASTVariable::CLAZZ,
            \PHP\Depend\Source\AST\ASTPropertyPostfix::CLAZZ,
            \PHP\Depend\Source\AST\ASTVariable::CLAZZ
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
            \PHP\Depend\Source\AST\ASTSelfReference::CLAZZ,
            \PHP\Depend\Source\AST\ASTPropertyPostfix::CLAZZ,
            \PHP\Depend\Source\AST\ASTVariable::CLAZZ
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
            \PHP\Depend\Source\AST\ASTParentReference::CLAZZ,
            \PHP\Depend\Source\AST\ASTPropertyPostfix::CLAZZ,
            \PHP\Depend\Source\AST\ASTVariable::CLAZZ
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
            \PHP\Depend\Source\AST\ASTVariable::CLAZZ,
            \PHP\Depend\Source\AST\ASTPropertyPostfix::CLAZZ,
            \PHP\Depend\Source\AST\ASTArrayIndexExpression::CLAZZ,
            \PHP\Depend\Source\AST\ASTIdentifier::CLAZZ,
            \PHP\Depend\Source\AST\ASTLiteral::CLAZZ
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
            \PHP\Depend\Source\AST\ASTSelfReference::CLAZZ,
            \PHP\Depend\Source\AST\ASTPropertyPostfix::CLAZZ,
            \PHP\Depend\Source\AST\ASTArrayIndexExpression::CLAZZ,
            \PHP\Depend\Source\AST\ASTVariable::CLAZZ,
            \PHP\Depend\Source\AST\ASTLiteral::CLAZZ
        );

        $this->assertGraphEquals($prefix, $expected);
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     * @expectedException \PHP\Depend\Source\Parser\InvalidStateException
     */
    public function testPropertyPostfixSelfVariableInFunctionThrowsExpectedException()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     * @expectedException \PHP\Depend\Source\Parser\InvalidStateException
     */
    public function testPropertyPostfixParentVariableInFunctionThrowsExpectedException()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     * @expectedException \PHP\Depend\Source\Parser\InvalidStateException
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
        $this->assertEquals(4, $postfix->getStartLine());
    }

    /**
     * testPropertyPostfixHasExpectedStartColumn
     *
     * @return void
     */
    public function testPropertyPostfixHasExpectedStartColumn()
    {
        $postfix = $this->_getFirstPropertyPostfixInFunction(__METHOD__);
        $this->assertEquals(21, $postfix->getStartColumn());
    }

    /**
     * testPropertyPostfixHasExpectedEndLine
     *
     * @return void
     */
    public function testPropertyPostfixHasExpectedEndLine()
    {
        $postfix = $this->_getFirstPropertyPostfixInFunction(__METHOD__);
        $this->assertEquals(4, $postfix->getEndLine());
    }

    /**
     * testPropertyPostfixHasExpectedEndColumn
     *
     * @return void
     */
    public function testPropertyPostfixHasExpectedEndColumn()
    {
        $postfix = $this->_getFirstPropertyPostfixInFunction(__METHOD__);
        $this->assertEquals(23, $postfix->getEndColumn());
    }

    /**
     * Creates a field declaration node.
     *
     * @return \PHP\Depend\Source\AST\ASTPropertyPostfix
     */
    protected function createNodeInstance()
    {
        return new \PHP\Depend\Source\AST\ASTPropertyPostfix(__CLASS__);
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return \PHP\Depend\Source\AST\ASTPropertyPostfix
     */
    private function _getFirstPropertyPostfixInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            \PHP\Depend\Source\AST\ASTPropertyPostfix::CLAZZ
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return \PHP\Depend\Source\AST\ASTMemberPrimaryPrefix
     */
    private function _getFirstMemberPrimaryPrefixInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, \PHP\Depend\Source\AST\ASTMemberPrimaryPrefix::CLAZZ
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return \PHP\Depend\Source\AST\ASTMemberPrimaryPrefix
     */
    private function _getFirstMemberPrimaryPrefixInClass($testCase)
    {
        return $this->getFirstNodeOfTypeInClass(
            $testCase, \PHP\Depend\Source\AST\ASTMemberPrimaryPrefix::CLAZZ
        );
    }
}
