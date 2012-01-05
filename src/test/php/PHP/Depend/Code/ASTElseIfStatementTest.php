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
 * Test case for the {@link PHP_Depend_Code_ASTElseIfStatement} class.
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
 * @covers PHP_Depend_Code_ASTElseIfStatement
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTElseIfStatementTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testHasElseMethodReturnsFalseByDefault
     *
     * @return void
     */
    public function testHasElseMethodReturnsFalseByDefault()
    {
        $stmt = $this->_getFirstElseIfStatementInFunction(__METHOD__);
        $this->assertFalse($stmt->hasElse());
    }

    /**
     * testHasElseMethodReturnsTrueWhenElseIfBranchExists
     *
     * @return void
     */
    public function testHasElseMethodReturnsTrueWhenElseIfBranchExists()
    {
        $stmt = $this->_getFirstElseIfStatementInFunction(__METHOD__);
        $this->assertTrue($stmt->hasElse());
    }

    /**
     * testHasElseMethodReturnsTrueWhenElseBranchWithIfExists
     *
     * @return void
     */
    public function testHasElseMethodReturnsTrueWhenElseBranchWithIfExists()
    {
        $stmt = $this->_getFirstElseIfStatementInFunction(__METHOD__);
        $this->assertTrue($stmt->hasElse());
    }

    /**
     * testHasElseMethodReturnsTrueWhenElseBranchExists
     *
     * @return void
     */
    public function testHasElseMethodReturnsTrueWhenElseBranchExists()
    {
        $stmt = $this->_getFirstElseIfStatementInFunction(__METHOD__);
        $this->assertTrue($stmt->hasElse());
    }

    /**
     * Tests the generated object graph of an elseif statement.
     *
     * @return void
     */
    public function testElseIfStatementGraphWithBooleanExpressions()
    {
        $stmt = $this->_getFirstElseIfStatementInFunction(__METHOD__);
        $this->assertEquals(2, count($stmt->getChildren()));
    }
    
    /**
     * testFirstChildOfElseIfStatementIsInstanceOfExpression
     *
     * @return void
     */
    public function testFirstChildOfElseIfStatementIsInstanceOfExpression()
    {
        $stmt = $this->_getFirstElseIfStatementInFunction(__METHOD__);
        $this->assertInstanceOf(PHP_Depend_Code_ASTExpression::CLAZZ, $stmt->getChild(0));
    }
    
    /**
     * testSecondChildOfElseIfStatementIsInstanceOfScopeStatement
     *
     * @return void
     */
    public function testSecondChildOfElseIfStatementIsInstanceOfScopeStatement()
    {
        $stmt = $this->_getFirstElseIfStatementInFunction(__METHOD__);
        $this->assertInstanceOf(PHP_Depend_Code_ASTScopeStatement::CLAZZ, $stmt->getChild(1));
    }

    /**
     * Tests the start line value.
     *
     * @return void
     */
    public function testElseIfStatementHasExpectedStartLine()
    {
        $stmt = $this->_getFirstElseIfStatementInFunction(__METHOD__);
        $this->assertEquals(6, $stmt->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @return void
     */
    public function testElseIfStatementHasExpectedStartColumn()
    {
        $stmt = $this->_getFirstElseIfStatementInFunction(__METHOD__);
        $this->assertEquals(7, $stmt->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @return void
     */
    public function testElseIfStatementHasExpectedEndLine()
    {
        $stmt = $this->_getFirstElseIfStatementInFunction(__METHOD__);
        $this->assertEquals(8, $stmt->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @return void
     */
    public function testElseIfStatementHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstElseIfStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getEndColumn());
    }
    
    /**
     * testElseIfStatementWithoutScopeStatementBody
     *
     * @return void
     */
    public function testElseIfStatementWithoutScopeStatementBody()
    {
        $stmt = $this->_getFirstElseIfStatementInFunction(__METHOD__);
        $this->assertInstanceOf(PHP_Depend_Code_ASTForeachStatement::CLAZZ, $stmt->getChild(1));
    }

    /**
     * testElseIfStatementAlternativeScopeHasExpectedStartLine
     *
     * @return void
     */
    public function testElseIfStatementAlternativeScopeHasExpectedStartLine()
    {
        $stmt = $this->_getFirstElseIfStatementInFunction(__METHOD__);
        $this->assertEquals(6, $stmt->getStartLine());
    }

    /**
     * testElseIfStatementAlternativeScopeHasExpectedStartColumn
     *
     * @return void
     */
    public function testElseIfStatementAlternativeScopeHasExpectedStartColumn()
    {
        $stmt = $this->_getFirstElseIfStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testElseIfStatementAlternativeScopeHasExpectedEndLine
     *
     * @return void
     */
    public function testElseIfStatementAlternativeScopeHasExpectedEndLine()
    {
        $stmt = $this->_getFirstElseIfStatementInFunction(__METHOD__);
        $this->assertEquals(11, $stmt->getEndLine());
    }

    /**
     * testElseIfStatementAlternativeScopeHasExpectedEndColumn
     *
     * @return void
     */
    public function testElseIfStatementAlternativeScopeHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstElseIfStatementInFunction(__METHOD__);
        $this->assertEquals(9, $stmt->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTElseIfStatement
     */
    private function _getFirstElseIfStatementInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTElseIfStatement::CLAZZ
        );
    }
}
