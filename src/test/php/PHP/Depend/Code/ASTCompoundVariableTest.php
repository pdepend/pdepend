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

require_once 'PHP/Depend/Code/ASTString.php';
require_once 'PHP/Depend/Code/ASTLiteral.php';
require_once 'PHP/Depend/Code/ASTCompoundVariable.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTCompoundVariable} class.
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
class PHP_Depend_Code_ASTCompoundVariableTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testAcceptInvokesVisitOnGivenVisitor
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTCompoundVariable
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptInvokesVisitOnGivenVisitor()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitCompoundVariable'));

        $node = new PHP_Depend_Code_ASTCompoundVariable();
        $node->accept($visitor);
    }

    /**
     * testAcceptReturnsReturnValueOfVisitMethod
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTCompoundVariable
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptReturnsReturnValueOfVisitMethod()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitCompoundVariable'))
            ->will($this->returnValue(42));

        $node = new PHP_Depend_Code_ASTCompoundVariable();
        self::assertEquals(42, $node->accept($visitor));
    }

    /**
     * Tests that a parsed compound variable has the expected object graph.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTCompoundVariable
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testCompoundVariableGraphWithInlineLiteral()
    {
        $variable = $this->_getFirstVariableInFunction(__METHOD__);

        $string = $variable->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTString::CLAZZ, $string);
    }

    /**
     * Tests that a parsed compound variable has the expected object graph.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTCompoundVariable
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testCompoundVariableGraphWithInlineConstantEscapedLiteral()
    {
        $variable = $this->_getFirstVariableInFunction(__METHOD__);

        $literal = $variable->getChild(0);
        $this->assertEquals("'FOO{\$bar}'", $literal->getImage());
    }

    /**
     * Tests that a parsed compound variable has the expected object graph.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTCompoundVariable
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testCompoundVariableGraphWithInlineBacktickLiteral()
    {
        $variable = $this->_getFirstVariableInFunction(__METHOD__);

        $string = $variable->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTString::CLAZZ, $string);
    }

    /**
     * Tests that a parsed compound variable has the expected object graph.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTCompoundVariable
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testCompoundVariableGraphWithMemberPrimaryPrefix()
    {
        $variable = $this->_getFirstVariableInFunction(__METHOD__);
        $expected = array(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ,
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTMethodPostfix::CLAZZ,
            PHP_Depend_Code_ASTIdentifier::CLAZZ,
            PHP_Depend_Code_ASTArguments::CLAZZ
        );

        $this->assertGraphEquals($variable, $expected);
    }

    /**
     * Tests that an invalid compound variable results in the expected exception.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTCompoundVariable
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     * @expectedException PHP_Depend_Parser_TokenStreamEndException
     */
    public function testUnclosedCompoundVariableThrowsExpectedException()
    {
        self::parseCodeResourceForTest();
    }
    
    /**
     * Tests the start line value.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTCompoundVariable
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testCompoundVariableHasExpectedStartLine()
    {
        $variable = $this->_getFirstVariableInFunction(__METHOD__);
        $this->assertSame(4, $variable->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTCompoundVariable
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testCompoundVariableHasExpectedStartColumn()
    {
        $variable = $this->_getFirstVariableInFunction(__METHOD__);
        $this->assertSame(5, $variable->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTCompoundVariable
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testCompoundVariableHasExpectedEndLine()
    {
        $variable = $this->_getFirstVariableInFunction(__METHOD__);
        $this->assertSame(7, $variable->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTCompoundVariable
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testCompoundVariableHasExpectedEndColumn()
    {
        $variable = $this->_getFirstVariableInFunction(__METHOD__);
        $this->assertSame(11, $variable->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTCompoundVariable
     */
    private function _getFirstVariableInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTCompoundVariable::CLAZZ
        );
    }
}