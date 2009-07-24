<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

require_once 'PHP/Depend/Code/ASTCompoundVariable.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTCompoundVariable} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Code_ASTCompoundVariableTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * Tests that a parsed compound variable has the expected object graph.
     *
     * @return void
     */
    public function testCompoundVariableGraphWithInlineLiteral()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $variable = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTCompoundVariable::CLAZZ
        );

        $expression = $variable->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTCompoundExpression::CLAZZ, $expression);

        $literal = $expression->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTLiteral::CLAZZ, $literal);
        $this->assertSame('"FOO{$bar}"', $literal->getImage());
    }

    /**
     * Tests that a parsed compound variable has the expected object graph.
     *
     * @return void
     */
    public function testCompoundVariableGraphWithInlineConstantEscapedLiteral()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $variable = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTCompoundVariable::CLAZZ
        );

        $expression = $variable->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTCompoundExpression::CLAZZ, $expression);

        $literal = $expression->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTLiteral::CLAZZ, $literal);
        $this->assertSame("'FOO{\$bar}'", $literal->getImage());
    }

    /**
     * Tests that a parsed compound variable has the expected object graph.
     *
     * @return void
     */
    public function testCompoundVariableGraphWithInlineBacktickLiteral()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $variable = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTCompoundVariable::CLAZZ
        );

        $expression = $variable->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTCompoundExpression::CLAZZ, $expression);

        $literal = $expression->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTLiteral::CLAZZ, $literal);
        $this->assertSame('`cat /tmp/classes | grep PHP_Depend_Parser`', $literal->getImage());
    }

    /**
     * Tests that a parsed compound variable has the expected object graph.
     *
     * @return void
     */
    public function testCompoundVariableGraphWithMemberPrimaryPrefix()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $variable = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTCompoundVariable::CLAZZ
        );

        $expression = $variable->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTCompoundExpression::CLAZZ, $expression);

        $primaryPrefix = $expression->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ, $primaryPrefix);

        $variable = $primaryPrefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $variable);

        $postfix = $primaryPrefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTMethodPostfix::CLAZZ, $postfix);
    }

    /**
     * Tests that an invalid compound variable results in the expected exception.
     *
     * @return void
     */
    public function testUnclosedCompoundVariableThrowsExpectedException()
    {
        $this->setExpectedException('PHP_Depend_Parser_TokenStreamEndException');
        self::parseTestCaseSource(__METHOD__);
    }

    /**
     * Creates a concrete node implementation.
     *
     * @return PHP_Depend_Code_ASTCompoundVariable
     */
    protected function createNodeInstance()
    {
        return new PHP_Depend_Code_ASTCompoundVariable('$');
    }
}