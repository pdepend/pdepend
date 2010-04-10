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

require_once 'PHP/Depend/Code/ASTMethodPostfix.php';
require_once 'PHP/Depend/ConstantsI.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTMethodPostfix} class.
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
class PHP_Depend_Code_ASTMethodPostfixTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForSimpleInvocation()
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
        $this->assertType(PHP_Depend_Code_ASTMethodPostfix::CLAZZ, $postfix);
        $this->assertSame('baz', $postfix->getImage());

        $identifier = $postfix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTIdentifier::CLAZZ, $identifier);
        $this->assertSame('baz', $identifier->getImage());

        $arguments = $postfix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTArguments::CLAZZ, $arguments);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForVariableInvocation()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $prefix = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

        $this->assertSame('->', $prefix->getImage());

        $variable = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $variable);
        $this->assertSame('$object', $variable->getImage());

        $postfix = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTMethodPostfix::CLAZZ, $postfix);
        $this->assertSame('$baz', $postfix->getImage());

        $identifier = $postfix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $identifier);
        $this->assertSame('$baz', $identifier->getImage());

        $arguments = $postfix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTArguments::CLAZZ, $arguments);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForVariableVariableInvocation()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $prefix = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

        $this->assertSame('->', $prefix->getImage());

        $variable = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $variable);
        $this->assertSame('$object', $variable->getImage());

        $postfix = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTMethodPostfix::CLAZZ, $postfix);
        $this->assertSame('$', $postfix->getImage());

        $varVariable = $postfix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariableVariable::CLAZZ, $varVariable);
        $this->assertSame('$', $varVariable->getImage());

        $identifier = $varVariable->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $identifier);
        $this->assertSame('$bar', $identifier->getImage());

        $arguments = $postfix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTArguments::CLAZZ, $arguments);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForCompoundVariableInvocation()
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
        $this->assertType(PHP_Depend_Code_ASTMethodPostfix::CLAZZ, $postfix);
        $this->assertSame('$', $postfix->getImage());

        $compound = $postfix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTCompoundVariable::CLAZZ, $compound);
        $this->assertSame('$', $compound->getImage());

        $expression = $compound->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTCompoundExpression::CLAZZ, $expression);

        $this->assertType(PHP_Depend_Code_ASTArguments::CLAZZ, $postfix->getChild(1));
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     * 
     * @return void
     */
    public function testMethodPostfixStructureForSimpleStaticInvocation()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $prefix = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

        $children = $prefix->getChildren();
        $this->assertType(PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ, $children[0]);
        $this->assertSame('Bar', $children[0]->getImage());

        $this->assertType(PHP_Depend_Code_ASTMethodPostfix::CLAZZ, $children[1]);
        $this->assertSame('baz', $children[1]->getImage());

        $children = $children[1]->getChildren();
        $this->assertType(PHP_Depend_Code_ASTIdentifier::CLAZZ, $children[0]);
        $this->assertSame('baz', $children[0]->getImage());

        $this->assertType(PHP_Depend_Code_ASTArguments::CLAZZ, $children[1]);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForVariableStaticInvocation()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $prefix = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

        $children = $prefix->getChildren();
        $this->assertSame(2, count($children));

        $this->assertType(PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ, $children[0]);
        $this->assertSame('Bar', $children[0]->getImage());

        $this->assertType(PHP_Depend_Code_ASTMethodPostfix::CLAZZ, $children[1]);
        $this->assertSame('$baz', $children[1]->getImage());

        $this->assertSame(0, count($children[0]->getChildren()));

        $children = $children[1]->getChildren();
        $this->assertSame(2, count($children));

        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $children[0]);
        $this->assertSame('$baz', $children[0]->getImage());

        $this->assertType(PHP_Depend_Code_ASTArguments::CLAZZ, $children[1]);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForVariableVariableStaticInvocation()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $prefix = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

        $children = $prefix->getChildren();
        $this->assertSame(2, count($children));

        $this->assertType(PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ, $children[0]);
        $this->assertSame('Bar', $children[0]->getImage());

        $this->assertType(PHP_Depend_Code_ASTMethodPostfix::CLAZZ, $children[1]);
        $this->assertSame('$', $children[1]->getImage());

        $this->assertSame(0, count($children[0]->getChildren()));

        $children = $children[1]->getChildren();
        $this->assertSame(2, count($children));

        $this->assertType(PHP_Depend_Code_ASTVariableVariable::CLAZZ, $children[0]);
        $this->assertSame('$', $children[0]->getImage());

        $this->assertType(PHP_Depend_Code_ASTArguments::CLAZZ, $children[1]);

        $children = $children[0]->getChildren();

        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $children[0]);
        $this->assertSame('$baz', $children[0]->getImage());
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForCompoundVariableStaticInvocation()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $prefix = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

        $children = $prefix->getChildren();
        $this->assertSame(2, count($children));

        $this->assertType(PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ, $children[0]);
        $this->assertSame('Bar', $children[0]->getImage());

        $this->assertType(PHP_Depend_Code_ASTMethodPostfix::CLAZZ, $children[1]);
        $this->assertSame('$', $children[1]->getImage());

        $this->assertSame(0, count($children[0]->getChildren()));

        $children = $children[1]->getChildren();
        $this->assertType(PHP_Depend_Code_ASTCompoundVariable::CLAZZ, $children[0]);
        $this->assertSame('$', $children[0]->getImage());

        $this->assertType(PHP_Depend_Code_ASTArguments::CLAZZ, $children[1]);

        $compound = $children[0]->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTCompoundExpression::CLAZZ, $compound);

        $constant = $compound->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTConstant::CLAZZ, $constant);
        $this->assertSame('BAZ', $constant->getImage());
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForVariableCompoundVariableStaticInvocation()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $prefix = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

        $children = $prefix->getChildren();
        $this->assertSame(2, count($children));

        $this->assertType(PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ, $children[0]);
        $this->assertSame('Bar', $children[0]->getImage());

        $this->assertType(PHP_Depend_Code_ASTMethodPostfix::CLAZZ, $children[1]);
        $this->assertSame('$', $children[1]->getImage());

        $this->assertSame(0, count($children[0]->getChildren()));

        $children = $children[1]->getChildren();
        $this->assertSame(2, count($children));

        $this->assertType(PHP_Depend_Code_ASTVariableVariable::CLAZZ, $children[0]);
        $this->assertSame('$', $children[0]->getImage());

        $this->assertType(PHP_Depend_Code_ASTArguments::CLAZZ, $children[1]);

        $children = $children[0]->getChildren();
        $this->assertType(PHP_Depend_Code_ASTCompoundVariable::CLAZZ, $children[0]);
        $this->assertSame('$', $children[0]->getImage());

        $compound = $children[0]->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTCompoundExpression::CLAZZ, $compound);

        $constant = $compound->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTConstant::CLAZZ, $constant);
        $this->assertSame('BAZ', $constant->getImage());
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForStaticInvocationWithConsecutiveInvocation()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $prefix1 = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );
        
        $reference = $prefix1->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ, $reference);
        $this->assertSame('Bar', $reference->getImage());

        $prefix2 = $prefix1->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ, $prefix2);
        $this->assertSame('->', $prefix2->getImage());

        $postfix1 = $prefix2->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTMethodPostfix::CLAZZ, $postfix1);

        $identifier1 = $postfix1->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTIdentifier::CLAZZ, $identifier1);
        $this->assertSame('baz', $identifier1->getImage());

        $postfix2 = $prefix2->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTMethodPostfix::CLAZZ, $postfix2);

        $identifier2 = $postfix2->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTIdentifier::CLAZZ, $identifier2);
        $this->assertSame('foo', $identifier2->getImage());
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForStaticInvocationOnVariable()
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
        $this->assertType(PHP_Depend_Code_ASTMethodPostfix::CLAZZ, $postfix);

        $identifier = $postfix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTIdentifier::CLAZZ, $identifier);

        $arguments = $postfix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTArguments::CLAZZ, $arguments);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForSelfInvocation()
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

        $reference = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTSelfReference::CLAZZ, $reference);

        $postfix = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTMethodPostfix::CLAZZ, $postfix);
    }

    /**
     * Tests that a parsed method postfix has the expected object structure.
     *
     * @return void
     */
    public function testMethodPostfixStructureForParentInvocation()
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

        $reference = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTParentReference::CLAZZ, $reference);

        $postfix = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTMethodPostfix::CLAZZ, $postfix);
    }

    /**
     * Tests that a parsed method postfix has the expected object graph.
     *
     * @return void
     */
    public function testMethodPostfixGraphForStaticReferenceInvocation()
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

        $reference = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTStaticReference::CLAZZ, $reference);

        $postfix = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTMethodPostfix::CLAZZ, $postfix);
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