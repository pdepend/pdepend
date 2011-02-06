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
require_once 'PHP/Depend/Code/ASTCompoundVariable.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTString} class.
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
class PHP_Depend_Code_ASTStringTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testAcceptInvokesVisitOnGivenVisitor
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTString
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptInvokesVisitOnGivenVisitor()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitString'));

        $node = new PHP_Depend_Code_ASTString();
        $node->accept($visitor);
    }

    /**
     * testAcceptReturnsReturnValueOfVisitMethod
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTString
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptReturnsReturnValueOfVisitMethod()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitString'))
            ->will($this->returnValue(42));

        $node = new PHP_Depend_Code_ASTString();
        self::assertEquals(42, $node->accept($visitor));
    }

    /**
     * testDoubleQuoteStringContainsTwoChildNodes
     *
     * @return void
     * @covers PHP_Depend_Code_ASTString
     * @covers PHP_Depend_Parser::_parseString
     * @covers PHP_Depend_Parser::_parseLiteralOrString
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDoubleQuoteStringContainsTwoChildNodes()
    {
        $string = $this->_getFirstStringInFunction(__METHOD__);
        $this->assertEquals(2, count($string->getChildren()));
    }

    /**
     * testDoubleQuoteStringContainsExpectedTextContent
     *
     * @return void
     * @covers PHP_Depend_Code_ASTString
     * @covers PHP_Depend_Parser::_parseString
     * @covers PHP_Depend_Parser::_parseLiteralOrString
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDoubleQuoteStringContainsExpectedTextContent()
    {
        $string = $this->_getFirstStringInFunction(__METHOD__);
        $this->assertContains("Hello", $string->getChild(0)->getImage());
    }

    /**
     * testBacktickExpressionContainsTwoChildNodes
     *
     * @return void
     * @covers PHP_Depend_Code_ASTString
     * @covers PHP_Depend_Parser::_parseString
     * @covers PHP_Depend_Parser::_parseLiteralOrString
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testBacktickExpressionContainsTwoChildNodes()
    {
        $string = $this->_getFirstStringInFunction(__METHOD__);
        $this->assertEquals(2, count($string->getChildren()));
    }

    /**
     * testBacktickExpressionContainsExpectedCompoundVariable
     *
     * @return void
     * @covers PHP_Depend_Code_ASTString
     * @covers PHP_Depend_Parser::_parseString
     * @covers PHP_Depend_Parser::_parseLiteralOrString
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testBacktickExpressionContainsExpectedCompoundVariable()
    {
        $string = $this->_getFirstStringInFunction(__METHOD__);
        $this->assertType(PHP_Depend_Code_ASTCompoundVariable::CLAZZ, $string->getChild(0));
    }

    /**
     * testDoubleQuoteStringWithEmbeddedComplexBacktickExpression
     *
     * @return void
     * @covers PHP_Depend_Code_ASTString
     * @covers PHP_Depend_Parser::_parseString
     * @covers PHP_Depend_Parser::_parseLiteralOrString
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDoubleQuoteStringWithEmbeddedComplexBacktickExpression()
    {
        $string = $this->_getFirstStringInFunction(__METHOD__);
        $actual = array();
        foreach ($string->getChildren() as $child) {
            $actual[] = $child->getImage();
        }
        $expected = array("Issue `", '$ticketNo', '`');

        $this->assertEquals($expected, $actual);
    }

    /**
     * testBacktickExpressionWithEmbeddedComplexDoubleQuoteString
     *
     * @return void
     * @covers PHP_Depend_Code_ASTString
     * @covers PHP_Depend_Parser::_parseString
     * @covers PHP_Depend_Parser::_parseLiteralOrString
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testBacktickExpressionWithEmbeddedComplexDoubleQuoteString()
    {
        $string = $this->_getFirstStringInFunction(__METHOD__);
        $actual = array();
        foreach ($string->getChildren() as $child) {
            $actual[] = $child->getImage();
        }
        $expected = array('Issue "', '$ticketNo', '"');
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * testDoubleQuoteStringContainsVariable
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Code_ASTString
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDoubleQuoteStringContainsVariable()
    {
        $string = $this->_getFirstStringInFunction(__METHOD__);
        self::assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $string->getChild(0));
    }

    /**
     * testDoubleQuoteStringContainsVariableAfterNotOperator
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Code_ASTString
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDoubleQuoteStringContainsVariableAfterNotOperator()
    {
        $string = $this->_getFirstStringInFunction(__METHOD__);
        self::assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $string->getChild(1));
    }

    /**
     * testDoubleQuoteStringContainsVariableAfterSilenceOperator
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Code_ASTString
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDoubleQuoteStringContainsVariableAfterSilenceOperator()
    {
        $string = $this->_getFirstStringInFunction(__METHOD__);
        self::assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $string->getChild(1));
    }

    /**
     * testDoubleQuoteStringContainsCompoundVariable
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Code_ASTString
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDoubleQuoteStringContainsCompoundVariable()
    {
        $string = $this->_getFirstStringInFunction(__METHOD__);
        self::assertType(PHP_Depend_Code_ASTCompoundVariable::CLAZZ, $string->getChild(0));
    }

    /**
     * testDoubleQuoteStringContainsCompoundExpressionAfterLiteral
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Code_ASTString
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDoubleQuoteStringContainsCompoundExpressionAfterLiteral()
    {
        $string = $this->_getFirstStringInFunction(__METHOD__);
        self::assertType(PHP_Depend_Code_ASTCompoundExpression::CLAZZ, $string->getChild(1));
    }

    /**
     * testDoubleQuoteStringContainsVariableAfterDollarTwoLiterals
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Code_ASTString
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDoubleQuoteStringContainsVariableAfterDollarTwoLiterals()
    {
        $string = $this->_getFirstStringInFunction(__METHOD__);
        self::assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $string->getChild(1));
    }

    /**
     * testDoubleQuoteStringContainsDollarLiteralForVariableVariable
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Code_ASTString
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDoubleQuoteStringContainsDollarLiteralForVariableVariable()
    {
        $string = $this->_getFirstStringInFunction(__METHOD__);
        self::assertType(PHP_Depend_Code_ASTLiteral::CLAZZ, $string->getChild(0));
    }

    /**
     * Tests that an invalid literal results in the expected exception.
     *
     * @return void
     * @covers PHP_Depend_Code_ASTString
     * @covers PHP_Depend_Parser::_parseLiteralOrString
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     * @expectedException PHP_Depend_Parser_TokenException
     */
    public function testUnclosedDoubleQuoteStringResultsInExpectedException()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * Creates a string node.
     *
     * @return PHP_Depend_Code_ASTString
     */
    protected function createNodeInstance()
    {
        return new PHP_Depend_Code_ASTString();
    }

    /**
     * Returns a test member primary prefix.
     *
     * @param string $testCase The calling test case.
     *
     * @return PHP_Depend_Code_ASTString
     */
    private function _getFirstStringInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTString::CLAZZ
        );
    }
}