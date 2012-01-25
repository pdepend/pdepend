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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTForeachStatement} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 *
 * @covers PHP_Depend_Parser
 * @covers PHP_Depend_Code_ASTForeachStatement
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTForeachStatementTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testThirdChildOfForeachStatementIsASTScopeStatement
     *
     * @return void
     */
    public function testThirdChildOfForeachStatementIsASTScopeStatement()
    {
        $stmt = $this->_getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf(PHP_Depend_Code_ASTScopeStatement::CLAZZ, $stmt->getChild(2));
    }

    /**
     * Tests the start line value.
     *
     * @return void
     */
    public function testForeachStatementHasExpectedStartLine()
    {
        $stmt = $this->_getFirstForeachStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @return void
     */
    public function testForeachStatementHasExpectedStartColumn()
    {
        $stmt = $this->_getFirstForeachStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @return void
     */
    public function testForeachStatementHasExpectedEndLine()
    {
        $stmt = $this->_getFirstForeachStatementInFunction(__METHOD__);
        $this->assertEquals(6, $stmt->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @return void
     */
    public function testForeachStatementHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstForeachStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testForeachStatementContainsListExpressionAsFirstChild
     *
     * @return void
     */
    public function testForeachStatementContainsExpressionAsFirstChild()
    {
        $stmt = $this->_getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf(PHP_Depend_Code_ASTExpression::CLAZZ, $stmt->getChild(0));
    }

    /**
     * testForeachStatementWithoutKeyAndWithValue
     *
     * @return void
     */
    public function testForeachStatementWithoutKeyAndWithValue()
    {
        $stmt = $this->_getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf(PHP_Depend_Code_ASTVariable::CLAZZ, $stmt->getChild(1));
    }

    /**
     * testForeachStatementWithoutKeyAndWithValueByReference
     *
     * @return void
     */
    public function testForeachStatementWithoutKeyAndWithValueByReference()
    {
        $stmt = $this->_getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf(PHP_Depend_Code_ASTUnaryExpression::CLAZZ, $stmt->getChild(1));
    }

    /**
     * testForeachStatementWithKeyAndValue
     *
     * @return void
     */
    public function testForeachStatementWithKeyAndValue()
    {
        $stmt = $this->_getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf(PHP_Depend_Code_ASTVariable::CLAZZ, $stmt->getChild(2));
    }

    /**
     * testForeachStatementWithKeyAndValueByReference
     *
     * @return void
     */
    public function testForeachStatementWithKeyAndValueByReference()
    {
        $stmt = $this->_getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf(PHP_Depend_Code_ASTUnaryExpression::CLAZZ, $stmt->getChild(2));
    }

    /**
     * testForeachStatementWithObjectPropertyByReference
     *
     * @return void
     */
    public function testForeachStatementWithObjectPropertyByReference()
    {
        $stmt = $this->_getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf(PHP_Depend_Code_ASTUnaryExpression::CLAZZ, $stmt->getChild(1));
    }

    /**
     * testForeachStatementWithKeyAndObjectPropertyByReference
     *
     * @return void
     */
    public function testForeachStatementWithKeyAndObjectPropertyByReference()
    {
        $stmt = $this->_getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf(PHP_Depend_Code_ASTUnaryExpression::CLAZZ, $stmt->getChild(2));
    }

    /**
     * testForeachStatementWithObjectPropertyAsKey
     *
     * @return void
     */
    public function testForeachStatementWithObjectPropertyAsKey()
    {
        $stmt = $this->_getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf(PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ, $stmt->getChild(1));
    }

    /**
     * testForeachStatementWithObjectPropertyAsValue
     *
     * @return void
     */
    public function testForeachStatementWithObjectPropertyAsValue()
    {
        $stmt = $this->_getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf(PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ, $stmt->getChild(1));
    }

    /**
     * testForeachStatementWithObjectPropertyAsKeyAndValue
     *
     * @return void
     */
    public function testForeachStatementWithObjectPropertyAsKeyAndValue()
    {
        $stmt = $this->_getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf(PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ, $stmt->getChild(1));
    }

    /**
     * testForeachStatementThrowsExpectedExceptionForKeyByReference
     *
     * @return void
     * @expectedException PHP_Depend_Parser_UnexpectedTokenException
     */
    public function testForeachStatementThrowsExpectedExceptionForKeyByReference()
    {
        $this->_getFirstForeachStatementInFunction(__METHOD__);
    }

    /**
     * testForeachStatementWithCommentBeforeClosingParenthesis
     *
     * @return void
     */
    public function testForeachStatementWithCommentBeforeClosingParenthesis()
    {
        $this->_getFirstForeachStatementInFunction(__METHOD__);
    }

    /**
     * testForeachStatementAlternativeScopeHasExpectedStartLine
     *
     * @return void
     */
    public function testForeachStatementAlternativeScopeHasExpectedStartLine()
    {
        $stmt = $this->_getFirstForeachStatementInFunction(__METHOD__);
        self::assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testForeachStatementAlternativeScopeHasExpectedStartColumn
     *
     * @return void
     */
    public function testForeachStatementAlternativeScopeHasExpectedStartColumn()
    {
        $stmt = $this->_getFirstForeachStatementInFunction(__METHOD__);
        self::assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testForeachStatementAlternativeScopeHasExpectedEndLine
     *
     * @return void
     */
    public function testForeachStatementAlternativeScopeHasExpectedEndLine()
    {
        $stmt = $this->_getFirstForeachStatementInFunction(__METHOD__);
        self::assertEquals(6, $stmt->getEndLine());
    }

    /**
     * testForeachStatementAlternativeScopeHasExpectedEndColumn
     *
     * @return void
     */
    public function testForeachStatementAlternativeScopeHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstForeachStatementInFunction(__METHOD__);
        self::assertEquals(15, $stmt->getEndColumn());
    }

    /**
     * testForeachStatementTerminatedByPhpCloseTag
     *
     * @return void
     */
    public function testForeachStatementTerminatedByPhpCloseTag()
    {
        $stmt = $this->_getFirstForeachStatementInFunction(__METHOD__);
        self::assertEquals(9, $stmt->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTForeachStatement
     */
    private function _getFirstForeachStatementInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTForeachStatement::CLAZZ
        );
    }
}
