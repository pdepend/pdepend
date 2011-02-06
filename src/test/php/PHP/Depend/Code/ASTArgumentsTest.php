<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

require_once 'PHP/Depend/Code/ASTArguments.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTArguments} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Code_ASTArgumentsTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testAcceptInvokesVisitOnGivenVisitor
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTArguments
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptInvokesVisitOnGivenVisitor()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitArguments'));

        $node = new PHP_Depend_Code_ASTArguments();
        $node->accept($visitor);
    }

    /**
     * testAcceptReturnsReturnValueOfVisitMethod
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTArguments
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptReturnsReturnValueOfVisitMethod()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitArguments'))
            ->will($this->returnValue(42));

        $node = new PHP_Depend_Code_ASTArguments();
        self::assertEquals(42, $node->accept($visitor));
    }
    
    /**
     * Tests that the parser adds the expected childs to an argument instance.
     * 
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArguments
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArgumentsContainsStaticMethodPostfixExpression()
    {
        $arguments = $this->_getFirstArgumentsOfFunction(__METHOD__);

        $prefix = $arguments->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ, $prefix);

        $reference = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ, $reference);

        $postfix = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTMethodPostfix::CLAZZ, $postfix);
    }

    /**
     * Tests that the parser adds the expected childs to an argument instance.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArguments
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArgumentsContainsMethodPostfixExpression()
    {
        $arguments = $this->_getFirstArgumentsOfFunction(__METHOD__);

        $prefix = $arguments->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ, $prefix);

        $variable = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $variable);

        $postfix = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTMethodPostfix::CLAZZ, $postfix);
    }

    /**
     * Tests that the parser adds the expected childs to an argument instance.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArguments
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArgumentsContainsConstantsPostfixExpression()
    {
        $arguments = $this->_getFirstArgumentsOfFunction(__METHOD__);

        $prefix = $arguments->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ, $prefix);

        $reference = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ, $reference);

        $postfix = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTConstantPostfix::CLAZZ, $postfix);
    }

    /**
     * Tests that the parser adds the expected childs to an argument instance.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArguments
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArgumentsContainsPropertyPostfixExpression()
    {
        $arguments = $this->_getFirstArgumentsOfFunction(__METHOD__);

        $prefix = $arguments->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ, $prefix);

        $reference = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ, $reference);

        $postfix = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTPropertyPostfix::CLAZZ, $postfix);
    }

    /**
     * Tests that the parser adds the expected childs to an argument instance.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArguments
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArgumentsContainsSelfPropertyPostfixExpression()
    {
        $packages = self::parseCodeResourceForTest();
        $method   = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $arguments = $method->getFirstChildOfType(
            PHP_Depend_Code_ASTArguments::CLAZZ
        );

        $prefix = $arguments->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ, $prefix);

        $reference = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTSelfReference::CLAZZ, $reference);

        $postfix = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTPropertyPostfix::CLAZZ, $postfix);
    }

    /**
     * Tests that the parser adds the expected childs to an argument instance.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArguments
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArgumentsContainsParentMethodPostfixExpression()
    {
        $packages = self::parseCodeResourceForTest();
        $method   = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $arguments = $method->getFirstChildOfType(
            PHP_Depend_Code_ASTArguments::CLAZZ
        );

        $prefix = $arguments->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ, $prefix);

        $reference = $prefix->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTParentReference::CLAZZ, $reference);

        $postfix = $prefix->getChild(1);
        $this->assertType(PHP_Depend_Code_ASTMethodPostfix::CLAZZ, $postfix);
    }

    /**
     * Tests that the parser adds the expected childs to an argument instance.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArguments
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArgumentsContainsAllocationExpression()
    {
        $arguments = $this->_getFirstArgumentsOfFunction(__METHOD__);

        $allocation = $arguments->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTAllocationExpression::CLAZZ, $allocation);
    }

    /**
     * Tests that the parser adds the expected childs to an argument instance.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArguments
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArgumentsWithSeveralParameters()
    {
        $arguments = $this->_getFirstArgumentsOfFunction(__METHOD__);

        $postfix = $arguments->getFirstChildOfType(
            PHP_Depend_Code_ASTFunctionPostfix::CLAZZ
        );
        $this->assertType(PHP_Depend_Code_ASTFunctionPostfix::CLAZZ, $postfix);
    }

    /**
     * Tests that the parser adds the expected childs to an argument instance.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArguments
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArgumentsWithInlineComments()
    {
        $arguments = $this->_getFirstArgumentsOfFunction(__METHOD__);

        $child = $arguments->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $child);
    }

    /**
     * Tests that the parser adds the expected childs to an argument instance.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArguments
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArgumentsWithInlineConcatExpression()
    {
        $arguments = $this->_getFirstArgumentsOfFunction(__METHOD__);

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
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArguments
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     * @expectedException PHP_Depend_Parser_UnexpectedTokenException
     */
    public function testUnclosedArgumentsExpressionThrowsExpectedException()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * Tests the start line value of an arguments instance.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArguments
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArgumentsHasExpectedStartLine()
    {
        $arguments = $this->_getFirstArgumentsOfFunction(__METHOD__);
        $this->assertEquals(5, $arguments->getStartLine());
    }

    /**
     * Tests the start column value of an arguments instance.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArguments
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArgumentsHasExpectedStartColumn()
    {
        $arguments = $this->_getFirstArgumentsOfFunction(__METHOD__);
        $this->assertEquals(8, $arguments->getStartColumn());
    }

    /**
     * Tests the end line value of an arguments instance.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArguments
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArgumentsHasExpectedEndLine()
    {
        $arguments = $this->_getFirstArgumentsOfFunction(__METHOD__);
        $this->assertEquals(7, $arguments->getEndLine());
    }

    /**
     * Tests the end column value of an arguments instance.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArguments
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArgumentsHasExpectedEndColumn()
    {
        $arguments = $this->_getFirstArgumentsOfFunction(__METHOD__);
        $this->assertEquals(21, $arguments->getEndColumn());
    }

    /**
     * Returns an arguments instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTArguments
     */
    private function _getFirstArgumentsOfFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase,
            PHP_Depend_Code_ASTArguments::CLAZZ
        );
    }
}
