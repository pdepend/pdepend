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

require_once 'PHP/Depend/Code/ASTForStatement.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTForStatement} class.
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
class PHP_Depend_Code_ASTForStatementTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testAcceptInvokesVisitOnGivenVisitor
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptInvokesVisitOnGivenVisitor()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitForStatement'));

        $stmt = new PHP_Depend_Code_ASTForStatement();
        $stmt->accept($visitor);
    }

    /**
     * testAcceptReturnsReturnValueOfVisitMethod
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptReturnsReturnValueOfVisitMethod()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitForStatement'))
            ->will($this->returnValue(42));

        $stmt = new PHP_Depend_Code_ASTForStatement();
        self::assertEquals(42, $stmt->accept($visitor));
    }

    /**
     * Tests the start line value.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testForStatementHasExpectedStartLine()
    {
        $stmt = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testForStatementHasExpectedStartColumn()
    {
        $stmt = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testForStatementHasExpectedEndLine()
    {
        $stmt = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(6, $stmt->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testForStatementHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testForExpressionHasExpectedStartLine
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testForExpressionHasExpectedStartLine()
    {
        $stmt = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getChild(1)->getStartLine());
    }

    /**
     * testForExpressionHasExpectedStartColumn
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testForExpressionHasExpectedStartColumn()
    {
        $stmt = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(27, $stmt->getChild(1)->getStartColumn());
    }

    /**
     * testForExpressionHasExpectedEndLine
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testForExpressionHasExpectedEndLine()
    {
        $stmt = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getChild(1)->getEndLine());
    }

    /**
     * testForExpressionHasExpectedEndColumn
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testForExpressionHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(33, $stmt->getChild(1)->getEndColumn());
    }

    /**
     * testFirstChildOfForStatementIsInstanceOfForInit
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testFirstChildOfForStatementIsInstanceOfForInit()
    {
        $stmt = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertType(PHP_Depend_Code_ASTForInit::CLAZZ, $stmt->getChild(0));
    }

    /**
     * testFirstChildOfForStatementCanBeLeftBlank
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testFirstChildOfForStatementCanBeLeftBlank()
    {
        $stmt = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertType(PHP_Depend_Code_ASTExpression::CLAZZ, $stmt->getChild(0));
    }


    /**
     * testParserHandlesBooleanLiteralInForInit
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testParserHandlesBooleanLiteralInForInit()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testSecondChildOfForStatementIsInstanceOfExpression
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testSecondChildOfForStatementIsInstanceOfExpression()
    {
        $stmt = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertType(PHP_Depend_Code_ASTExpression::CLAZZ, $stmt->getChild(1));
    }

    /**
     * testSecondChildOfForStatementCanBeLeftBlank
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testSecondChildOfForStatementCanBeLeftBlank()
    {
        $stmt = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertType(PHP_Depend_Code_ASTForUpdate::CLAZZ, $stmt->getChild(1));
    }

    /**
     * testThirdChildOfForStatementIsInstanceOfForUpdate
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testThirdChildOfForStatementIsInstanceOfForUpdate()
    {
        $stmt = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertType(PHP_Depend_Code_ASTForUpdate::CLAZZ, $stmt->getChild(2));
    }

    /**
     * testThirdChildOfForStatementCanBeLeftBlank
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testThirdChildOfForStatementCanBeLeftBlank()
    {
        $stmt = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertType(PHP_Depend_Code_ASTScopeStatement::CLAZZ, $stmt->getChild(2));
    }

    /**
     * testFourthChildOfForStatementIsInstanceOfScopeStatement
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testFourthChildOfForStatementIsInstanceOfScopeStatement()
    {
        $stmt = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertType(PHP_Depend_Code_ASTScopeStatement::CLAZZ, $stmt->getChild(3));
    }

    /**
     * testFourthChildOfForStatementIsInstanceOfStatement
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testFourthChildOfForStatementIsInstanceOfStatement()
    {
        $stmt = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertType(PHP_Depend_Code_ASTStatement::CLAZZ, $stmt->getChild(3));
    }

    /**
     * testParserResetsScopeTreeForEmptyForInit
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testParserResetsScopeTreeForEmptyForInit()
    {
        $class = $this->getFirstClassForTestCase(__METHOD__);

        $actual   = array($class->getStartLine(), $class->getEndLine());
        $expected = array(5, 14);

        $this->assertEquals($expected, $actual);
    }

    /**
     * testParserResetsScopeTreeForEmptyForExpression
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testParserResetsScopeTreeForEmptyForExpression()
    {
        $class = $this->getFirstClassForTestCase(__METHOD__);

        $actual   = array($class->getStartLine(), $class->getEndLine());
        $expected = array(5, 14);

        $this->assertEquals($expected, $actual);
    }

    /**
     * testForStatementAlternativeScopeHasExpectedStartLine
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testForStatementAlternativeScopeHasExpectedStartLine()
    {
        $stmt = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testForStatementAlternativeScopeHasExpectedStartColumn
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testForStatementAlternativeScopeHasExpectedStartColumn()
    {
        $stmt = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testForStatementAlternativeScopeHasExpectedEndLine
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testForStatementAlternativeScopeHasExpectedEndLine()
    {
        $stmt = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(12, $stmt->getEndLine());
    }

    /**
     * testForStatementAlternativeScopeHasExpectedEndColumn
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testForStatementAlternativeScopeHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(11, $stmt->getEndColumn());
    }

    /**
     * testForStatementTerminatedByPhpCloseTag
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testForStatementTerminatedByPhpCloseTag()
    {
        $stmt = $this->_getFirstForStatementInFunction(__METHOD__);
        self::assertEquals(9, $stmt->getEndColumn());
    }

    /**
     * testParserHandlesBooleanLiteralInForExpression
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testParserHandlesBooleanLiteralInForExpression()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParserResetsScopeTreeForEmptyForUpdate
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testParserResetsScopeTreeForEmptyForUpdate()
    {
        $class = $this->getFirstClassForTestCase(__METHOD__);

        $actual   = array($class->getStartLine(), $class->getEndLine());
        $expected = array(5, 14);

        $this->assertEquals($expected, $actual);
    }

    /**
     * testParserHandlesParenthesisExpressionInForUpdate
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testParserHandlesParenthesisExpressionInForUpdate()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParserHandlesBooleanLiteralInForUpdate
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTForStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testParserHandlesBooleanLiteralInForUpdate()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTForStatement
     */
    private function _getFirstForStatementInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTForStatement::CLAZZ
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTForStatement
     */
    private function _getFirstForStatementInClass($testCase)
    {
        return $this->getFirstNodeOfTypeInClass(
            $testCase, PHP_Depend_Code_ASTForStatement::CLAZZ
        );
    }
}
