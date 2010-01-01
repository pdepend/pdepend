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
     */
    public function testPropertyPostfixStructureForSimpleIdentifierAccess()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $prefix = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

        $variable = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $variable);
        $this->assertSame('$object', $variable->getImage());

        $postfix = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTPropertyPostfix::CLAZZ, $postfix);
        $this->assertSame('bar', $postfix->getImage());

        $identifier = $postfix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTIdentifier::CLAZZ, $identifier);
        $this->assertSame('bar', $identifier->getImage());
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     */
    public function testPropertyPostfixStructureForVariableAccess()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $prefix = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

        $variable = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $variable);
        $this->assertSame('$object', $variable->getImage());

        $postfix = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTPropertyPostfix::CLAZZ, $postfix);
        $this->assertSame('$bar', $postfix->getImage());

        $identifier = $postfix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $identifier);
        $this->assertSame('$bar', $identifier->getImage());
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     */
    public function testPropertyPostfixStructureForVariableVariableAccess()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $prefix = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

        $variable = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $variable);
        $this->assertSame('$object', $variable->getImage());

        $postfix = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTPropertyPostfix::CLAZZ, $postfix);
        $this->assertSame('$', $postfix->getImage());

        $varVariable = $postfix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariableVariable::CLAZZ, $varVariable);
        $this->assertSame('$', $varVariable->getImage());

        $identifier = $varVariable->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $identifier);
        $this->assertSame('$bar', $identifier->getImage());
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     */
    public function testPropertyPostfixStructureForCompoundVariableAccess()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $prefix = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

        $variable = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $variable);
        $this->assertSame('$object', $variable->getImage());

        $postfix = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTPropertyPostfix::CLAZZ, $postfix);
        $this->assertSame('$', $postfix->getImage());

        $compound = $postfix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTCompoundVariable::CLAZZ, $compound);
        $this->assertSame('$', $compound->getImage());

        $expression = $compound->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTCompoundExpression::CLAZZ, $expression);

        $constant = $expression->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTConstant::CLAZZ, $constant);
        $this->assertSame('BAR', $constant->getImage());
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     */
    public function testPropertyPostfixStructureForCompoundExpressionAccess()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $prefix = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

        $variable = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $variable);
        $this->assertSame('$object', $variable->getImage());

        $postfix = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTPropertyPostfix::CLAZZ, $postfix);

        $expression = $postfix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTCompoundExpression::CLAZZ, $expression);

        $varexpr = $expression->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $varexpr);
        $this->assertSame('$bar', $varexpr->getImage());
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     */
    public function testPropertyPostfixStructureForStaticVariableAccess()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $prefix = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

        $reference = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ, $reference);
        $this->assertSame('Foo', $reference->getImage());

        $postfix = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTPropertyPostfix::CLAZZ, $postfix);
        $this->assertSame('$bar', $postfix->getImage());

        $variable = $postfix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $variable);
        $this->assertSame('$bar', $variable->getImage());
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testPropertyPostfixStructureForStaticAccessOnVariable()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $prefix = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

        $variable = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $variable);
        $this->assertSame('::', $prefix->getImage());

        $postfix = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTPropertyPostfix::CLAZZ, $postfix);

        $property = $postfix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $property);
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     */
    public function testPropertyPostfixStructureForSelfVariableAccess()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $method   = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $prefix = $method->getFirstChildOfType(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

        $self = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTSelfReference::CLAZZ, $self);

        $postfix = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTPropertyPostfix::CLAZZ, $postfix);

        $variable = $postfix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $variable);
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     */
    public function testPropertyPostfixSelfVariableInFunctionThrowsExpectedException()
    {
        $this->setExpectedException(
            'PHP_Depend_Parser_InvalidStateException',
            'The keyword "self" was used outside of a class/method scope.'
        );

        $packages = self::parseTestCaseSource(__METHOD__);
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     */
    public function testPropertyPostfixStructureForParentVariableAccess()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $method   = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $prefix = $method->getFirstChildOfType(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

        $parent = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTParentReference::CLAZZ, $parent);

        $postfix = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTPropertyPostfix::CLAZZ, $postfix);

        $variable = $postfix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $variable);
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     */
    public function testPropertyPostfixParentVariableInFunctionThrowsExpectedException()
    {
        $this->setExpectedException(
            'PHP_Depend_Parser_InvalidStateException',
            'The keyword "parent" was used outside of a class/method scope.'
        );

        $packages = self::parseTestCaseSource(__METHOD__);
    }

    /**
     * Tests that a parsed property postfix has the expected object structure.
     *
     * @return void
     */
    public function testPropertyPostfixParentVariableInClassWithoutParentThrowsExpectedException()
    {
        $this->setExpectedException(
            'PHP_Depend_Parser_InvalidStateException',
            'The keyword "parent" was used but the class "' . __FUNCTION__ . '" does not declare a parent.'
        );

        $packages = self::parseTestCaseSource(__METHOD__);
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
