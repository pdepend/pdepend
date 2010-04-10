<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2010, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

require_once 'PHP/Depend/Code/ASTPropertyPostfix.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTPropertyPostfix} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Code_ASTPropertyPostfixTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Code_ASTPropertyPostfix
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testPropertyPostfixStructureForSimpleIdentifierAccess()
    {
        $prefix = $this->getFirstNodeOfTypeInFunction(
            __METHOD__, PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

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
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Code_ASTPropertyPostfix
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testPropertyPostfixStructureForVariableAccess()
    {
        $prefix = $this->getFirstNodeOfTypeInFunction(
            __METHOD__, PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

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
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Code_ASTPropertyPostfix
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testPropertyPostfixStructureForVariableVariableAccess()
    {
        $prefix = $this->getFirstNodeOfTypeInFunction(
            __METHOD__, PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

        $this->assertGraphEquals(
            $prefix,
            array(
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
                PHP_Depend_Code_ASTVariableVariable::CLAZZ,
                PHP_Depend_Code_ASTVariable::CLAZZ
            )
        );
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Code_ASTPropertyPostfix
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testPropertyPostfixStructureForCompoundVariableAccess()
    {
        $prefix = $this->getFirstNodeOfTypeInFunction(
            __METHOD__, PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

        $this->assertGraphEquals(
            $prefix,
            array(
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
                PHP_Depend_Code_ASTCompoundVariable::CLAZZ,
                PHP_Depend_Code_ASTCompoundExpression::CLAZZ,
                PHP_Depend_Code_ASTExpression::CLAZZ,
                PHP_Depend_Code_ASTConstant::CLAZZ,
                PHP_Depend_Code_ASTLiteral::CLAZZ
            )
        );
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Code_ASTPropertyPostfix
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testPropertyPostfixStructureForCompoundExpressionAccess()
    {
        $prefix = $this->getFirstNodeOfTypeInFunction(
            __METHOD__, PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

        $this->assertGraphEquals(
            $prefix,
            array(
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
                PHP_Depend_Code_ASTCompoundExpression::CLAZZ,
                PHP_Depend_Code_ASTVariable::CLAZZ
            )
        );
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Code_ASTPropertyPostfix
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testPropertyPostfixStructureForStaticVariableAccess()
    {
        $prefix = $this->getFirstNodeOfTypeInFunction(
            __METHOD__, PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

        $this->assertGraphEquals(
            $prefix,
            array(
                PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ,
                PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
                PHP_Depend_Code_ASTVariable::CLAZZ
            )
        );
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Code_ASTPropertyPostfix
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testPropertyPostfixStructureForStaticAccessOnVariable()
    {
        $prefix = $this->getFirstNodeOfTypeInFunction(
            __METHOD__, PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

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
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Code_ASTPropertyPostfix
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testPropertyPostfixStructureForSelfVariableAccess()
    {
        $prefix = $this->getFirstClassForTestCase(__METHOD__)
            ->getMethods()
            ->current()
            ->getFirstChildOfType(PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ);

        $this->assertGraphEquals(
            $prefix,
            array(
                PHP_Depend_Code_ASTSelfReference::CLAZZ,
                PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
                PHP_Depend_Code_ASTVariable::CLAZZ
            )
        );
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Code_ASTPropertyPostfix
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     * @expectedException PHP_Depend_Parser_InvalidStateException
     */
    public function testPropertyPostfixSelfVariableInFunctionThrowsExpectedException()
    {
        self::parseTestCaseSource(__METHOD__);
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Code_ASTPropertyPostfix
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testPropertyPostfixStructureForParentVariableAccess()
    {
        $prefix = $this->getFirstClassForTestCase(__METHOD__)
            ->getMethods()
            ->current()
            ->getFirstChildOfType(PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ);

        $this->assertGraphEquals(
            $prefix,
            array(
                PHP_Depend_Code_ASTParentReference::CLAZZ,
                PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
                PHP_Depend_Code_ASTVariable::CLAZZ
            )
        );
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Code_ASTPropertyPostfix
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     * @expectedException PHP_Depend_Parser_InvalidStateException
     */
    public function testPropertyPostfixParentVariableInFunctionThrowsExpectedException()
    {
        self::parseTestCaseSource(__METHOD__);
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Code_ASTPropertyPostfix
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     * @expectedException PHP_Depend_Parser_InvalidStateException
     */
    public function testPropertyPostfixParentVariableInClassWithoutParentThrowsExpectedException()
    {
        self::parseTestCaseSource(__METHOD__);
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
}
