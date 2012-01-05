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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 * @since      0.10.2
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

/**
 * Test case for the {@link PHP_Depend_Parser} class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 * @since      0.10.2
 *
 * @covers PHP_Depend_Parser
 * @group pdepend
 * @group pdepend::parser
 * @group unittest
 */
class PHP_Depend_Parser_ASTArgumentsParsingTest
    extends PHP_Depend_Parser_AbstractTest
{
    /**
     * Tests that the parser adds the expected children to an argument instance.
     *
     * @return void
     */
    public function testArgumentsContainsStaticMethodPostfixExpression()
    {
        self::assertGraphEquals(
            $this->_getFirstArgumentsOfFunction(),
            array(
                PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ,
                PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ,
                PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
                PHP_Depend_Code_ASTIdentifier::CLAZZ,
                PHP_Depend_Code_ASTArguments::CLAZZ
            )
        );
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     *
     * @return void
     */
    public function testArgumentsContainsMethodPostfixExpression()
    {
        self::assertGraphEquals(
            $this->_getFirstArgumentsOfFunction(),
            array(
                PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ,
                PHP_Depend_Code_ASTVariable::CLAZZ,
                PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
                PHP_Depend_Code_ASTIdentifier::CLAZZ,
                PHP_Depend_Code_ASTArguments::CLAZZ
            )
        );
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     *
     * @return void
     */
    public function testArgumentsContainsConstantsPostfixExpression()
    {
        self::assertGraphEquals(
            $this->_getFirstArgumentsOfFunction(),
            array(
                PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ,
                PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ,
                PHP_Depend_Code_ASTConstantPostfix::CLAZZ,
                PHP_Depend_Code_ASTIdentifier::CLAZZ
            )
        );
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     *
     * @return void
     */
    public function testArgumentsContainsPropertyPostfixExpression()
    {
        self::assertGraphEquals(
            $this->_getFirstArgumentsOfFunction(),
            array(
                PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ,
                PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ,
                PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
                PHP_Depend_Code_ASTVariable::CLAZZ
            )
        );
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     *
     * @return void
     */
    public function testArgumentsContainsSelfPropertyPostfixExpression()
    {
        self::assertGraphEquals(
            $this->_getFirstArgumentsOfMethod(),
            array(
                PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ,
                PHP_Depend_Code_ASTSelfReference::CLAZZ,
                PHP_Depend_Code_ASTPropertyPostfix::CLAZZ,
                PHP_Depend_Code_ASTVariable::CLAZZ
            )
        );
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     *
     * @return void
     */
    public function testArgumentsContainsParentMethodPostfixExpression()
    {
        self::assertGraphEquals(
            $this->_getFirstArgumentsOfMethod(),
            array(
                PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ,
                PHP_Depend_Code_ASTParentReference::CLAZZ,
                PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
                PHP_Depend_Code_ASTIdentifier::CLAZZ,
                PHP_Depend_Code_ASTArguments::CLAZZ
            )
        );
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     *
     * @return void
     */
    public function testArgumentsContainsAllocationExpression()
    {
        $arguments = $this->_getFirstArgumentsOfFunction();

        $allocation = $arguments->getChild(0);
        $this->assertInstanceOf(PHP_Depend_Code_ASTAllocationExpression::CLAZZ, $allocation);
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     *
     * @return void
     */
    public function testArgumentsWithSeveralParameters()
    {
        $arguments = $this->_getFirstArgumentsOfFunction();

        $postfix = $arguments->getFirstChildOfType(
            PHP_Depend_Code_ASTFunctionPostfix::CLAZZ
        );
        $this->assertInstanceOf(PHP_Depend_Code_ASTFunctionPostfix::CLAZZ, $postfix);
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     *
     * @return void
     */
    public function testArgumentsWithInlineComments()
    {
        $arguments = $this->_getFirstArgumentsOfFunction();

        $child = $arguments->getChild(0);
        $this->assertInstanceOf(PHP_Depend_Code_ASTVariable::CLAZZ, $child);
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     *
     * @return void
     */
    public function testArgumentsWithInlineConcatExpression()
    {
        $arguments = $this->_getFirstArgumentsOfFunction();

        $postfixes = $arguments->findChildrenOfType(
            PHP_Depend_Code_ASTMethodPostfix::CLAZZ
        );
        $this->assertEquals(1, count($postfixes));
    }

    /**
     * Tests that an invalid arguments expression results in the expected
     * exception.
     *
     * @return void
     * @expectedException PHP_Depend_Parser_UnexpectedTokenException
     */
    public function testUnclosedArgumentsExpressionThrowsExpectedException()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * Returns an arguments instance for the currently executed test case.
     *
     * @return PHP_Depend_Code_ASTArguments
     */
    private function _getFirstArgumentsOfFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            self::getCallingTestMethod(),
            PHP_Depend_Code_ASTArguments::CLAZZ
        );
    }

    /**
     * Returns an arguments instance for the currently executed test case.
     *
     * @return PHP_Depend_Code_ASTArguments
     */
    private function _getFirstArgumentsOfMethod()
    {
        return $this->getFirstNodeOfTypeInClass(
            self::getCallingTestMethod(),
            PHP_Depend_Code_ASTArguments::CLAZZ
        );
    }
}
