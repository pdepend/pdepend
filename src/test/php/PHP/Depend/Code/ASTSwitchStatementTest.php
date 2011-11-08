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

/**
 * Test case for the {@link PHP_Depend_Code_ASTSwitchStatement} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 *
 * @covers PHP_Depend_Parser
 * @covers PHP_Depend_Code_ASTSwitchStatement
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTSwitchStatementTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * Tests the generated object graph of a switch statement.
     *
     * @return void
     */
    public function testSwitchStatementGraphWithBooleanExpressions()
    {
        $stmt = $this->_getFirstSwitchStatementInFunction(__METHOD__);
        $children  = $stmt->getChildren();

        $this->assertInstanceOf(PHP_Depend_Code_ASTExpression::CLAZZ, $children[0]);
    }

    /**
     * Tests the generated object graph of a switch statement.
     *
     * @return void
     */
    public function testSwitchStatementGraphWithLabels()
    {
        $stmt = $this->_getFirstSwitchStatementInFunction(__METHOD__);
        $children  = $stmt->getChildren();

        $this->assertInstanceOf(PHP_Depend_Code_ASTSwitchLabel::CLAZZ, $children[1]);
        $this->assertInstanceOf(PHP_Depend_Code_ASTSwitchLabel::CLAZZ, $children[2]);
    }

    /**
     * Tests the start line value.
     *
     * @return void
     */
    public function testSwitchStatementHasExpectedStartLine()
    {
        $stmt = $this->_getFirstSwitchStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @return void
     */
    public function testSwitchStatementHasExpectedStartColumn()
    {
        $stmt = $this->_getFirstSwitchStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @return void
     */
    public function testSwitchStatementHasExpectedEndLine()
    {
        $stmt = $this->_getFirstSwitchStatementInFunction(__METHOD__);
        $this->assertEquals(8, $stmt->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @return void
     */
    public function testSwitchStatementHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstSwitchStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testParserIgnoresDocCommentInSwitchStatement
     *
     * @return void
     */
    public function testParserIgnoresDocCommentInSwitchStatement()
    {
        $this->_getFirstSwitchStatementInFunction(__METHOD__);
    }

    /**
     * testParserIgnoresCommentInSwitchStatement
     *
     * @return void
     */
    public function testParserIgnoresCommentInSwitchStatement()
    {
        $this->_getFirstSwitchStatementInFunction(__METHOD__);
    }

    /**
     * testInvalidStatementInSwitchStatementResultsInExpectedException
     *
     * @return void
     * @expectedException PHP_Depend_Parser_UnexpectedTokenException
     */
    public function testInvalidStatementInSwitchStatementResultsInExpectedException()
    {
        $this->_getFirstSwitchStatementInFunction(__METHOD__);
    }

    /**
     * testUnclosedSwitchStatementResultsInExpectedException
     *
     * @return void
     * @expectedException PHP_Depend_Parser_TokenStreamEndException
     */
    public function testUnclosedSwitchStatementResultsInExpectedException()
    {
        $this->_getFirstSwitchStatementInFunction(__METHOD__);
    }

    /**
     * testSwitchStatementAlternativeScopeHasExpectedStartLine
     *
     * @return void
     */
    public function testSwitchStatementAlternativeScopeHasExpectedStartLine()
    {
        $stmt = $this->_getFirstSwitchStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testSwitchStatementAlternativeScopeHasExpectedStartColumn
     *
     * @return void
     */
    public function testSwitchStatementAlternativeScopeHasExpectedStartColumn()
    {
        $stmt = $this->_getFirstSwitchStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testSwitchStatementAlternativeScopeHasExpectedEndLine
     *
     * @return void
     */
    public function testSwitchStatementAlternativeScopeHasExpectedEndLine()
    {
        $stmt = $this->_getFirstSwitchStatementInFunction(__METHOD__);
        $this->assertEquals(25, $stmt->getEndLine());
    }

    /**
     * testSwitchStatementAlternativeScopeHasExpectedEndColumn
     *
     * @return void
     */
    public function testSwitchStatementAlternativeScopeHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstSwitchStatementInFunction(__METHOD__);
        $this->assertEquals(14, $stmt->getEndColumn());
    }

    /**
     * testSwitchStatementTerminatedByPhpCloseTag
     *
     * @return void
     */
    public function testSwitchStatementTerminatedByPhpCloseTag()
    {
        $stmt = $this->_getFirstSwitchStatementInFunction(__METHOD__);
        self::assertEquals(9, $stmt->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTSwitchStatement
     */
    private function _getFirstSwitchStatementInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTSwitchStatement::CLAZZ
        );
    }
}
