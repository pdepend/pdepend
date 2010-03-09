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

require_once 'PHP/Depend/Code/ASTForStatement.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTForStatement} class.
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
class PHP_Depend_Code_ASTForStatementTest extends PHP_Depend_Code_ASTNodeTest
{
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
        $statement = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(4, $statement->getStartLine());
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
        $statement = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(5, $statement->getStartColumn());
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
        $statement = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(6, $statement->getEndLine());
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
        $statement = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(5, $statement->getEndColumn());
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
        $statement = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(4, $statement->getChild(1)->getStartLine());
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
        $statement = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(27, $statement->getChild(1)->getStartColumn());
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
        $statement = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(4, $statement->getChild(1)->getEndLine());
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
        $statement = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertEquals(33, $statement->getChild(1)->getEndColumn());
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
        $statement = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertType(PHP_Depend_Code_ASTForInit::CLAZZ, $statement->getChild(0));
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
        $statement = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertType(PHP_Depend_Code_ASTExpression::CLAZZ, $statement->getChild(0));
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
        $statement = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertType(PHP_Depend_Code_ASTExpression::CLAZZ, $statement->getChild(1));
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
        $statement = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertType(PHP_Depend_Code_ASTForUpdate::CLAZZ, $statement->getChild(1));
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
        $statement = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertType(PHP_Depend_Code_ASTForUpdate::CLAZZ, $statement->getChild(2));
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
        $statement = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertType(PHP_Depend_Code_ASTScopeStatement::CLAZZ, $statement->getChild(2));
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
        $statement = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertType(PHP_Depend_Code_ASTScopeStatement::CLAZZ, $statement->getChild(3));
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
        $statement = $this->_getFirstForStatementInFunction(__METHOD__);
        $this->assertType(PHP_Depend_Code_ASTStatement::CLAZZ, $statement->getChild(3));
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
        $this->_getFirstForStatementInClass(__METHOD__);
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