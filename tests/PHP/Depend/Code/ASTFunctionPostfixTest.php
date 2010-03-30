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

/**
 * Test case for the {@link PHP_Depend_Code_ASTFunctionPostfix} class.
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
class PHP_Depend_Code_ASTFunctionPostfixTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * Tests that a parsed function postfix has the expected object structure.
     *
     * @return void
     */
    public function testFunctionPostfixStructureSimple()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $postfix = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTFunctionPostfix::CLAZZ
        );
        
        $identifier = $postfix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTIdentifier::CLAZZ, $identifier);
        $this->assertSame(__FUNCTION__, $identifier->getImage());

        $arguments = $postfix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTArguments::CLAZZ, $arguments);
    }

    /**
     * Tests that a parsed function postfix has the expected object structure.
     *
     * @return void
     */
    public function testFunctionPostfixStructureVariable()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $prefix = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTFunctionPostfix::CLAZZ
        );
        $this->assertType(PHP_Depend_Code_ASTFunctionPostfix::CLAZZ, $prefix);

        $variable = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $variable);

        $arguments = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTArguments::CLAZZ, $arguments);
    }

    /**
     * Tests that a parsed function postfix has the expected object structure.
     *
     * @return void
     */
    public function testFunctionPostfixStructureCompoundVariable()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $prefix = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTFunctionPostfix::CLAZZ
        );
        $this->assertType(PHP_Depend_Code_ASTFunctionPostfix::CLAZZ, $prefix);

        $variable = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTCompoundVariable::CLAZZ, $variable);

        $expression = $variable->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTCompoundExpression::CLAZZ, $expression);

//        $constant = $expression->getChild(0);
//        $this->assertType(PHP_Depend_Code_ASTConstant::CLAZZ, $constant);

        $arguments = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTArguments::CLAZZ, $arguments);
    }

    /**
     * Tests that a parsed function postfix has the expected object structure.
     *
     * @return void
     */
    public function testFunctionPostfixStructureWithMemberPrimaryPrefixMethod()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $prefix = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

        $function = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTFunctionPostfix::CLAZZ, $function);

        $functionName = $function->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTIdentifier::CLAZZ, $functionName);
        $this->assertSame(__FUNCTION__, $functionName->getImage());

        $functionArgs = $function->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTArguments::CLAZZ, $functionArgs);

        $method = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTMethodPostfix::CLAZZ, $method);

        $methodName = $method->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTIdentifier::CLAZZ, $methodName);

        $methodArgs = $method->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTArguments::CLAZZ, $methodArgs);
    }

    /**
     * Tests that a parsed function postfix has the expected object structure.
     *
     * @return void
     */
    public function testFunctionPostfixStructureWithMemberPrimaryPrefixProperty()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $prefix = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );

        $function = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTFunctionPostfix::CLAZZ, $function);

        $functionName = $function->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTIdentifier::CLAZZ, $functionName);
        $this->assertSame(__FUNCTION__, $functionName->getImage());

        $functionArgs = $function->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTArguments::CLAZZ, $functionArgs);

        $property = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTPropertyPostfix::CLAZZ, $property);

        $propertyName = $property->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTIdentifier::CLAZZ, $propertyName);
    }

    /**
     * Creates a field declaration node.
     *
     * @return PHP_Depend_Code_ASTFunctionPostfix
     */
    protected function createNodeInstance()
    {
        return new PHP_Depend_Code_ASTFunctionPostfix(__FUNCTION__);
    }
}