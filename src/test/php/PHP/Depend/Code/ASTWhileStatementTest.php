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
 * Test case for the {@link PHP_Depend_Code_ASTWhileStatement} class.
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
 * @covers PHP_Depend_Code_ASTWhileStatement
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTWhileStatementTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * Tests the generated object graph of a while statement.
     *
     * @return void
     */
    public function testWhileStatementGraphWithBooleanExpressions()
    {
        $stmt = $this->_getFirstWhileStatementInFunction();
        $this->assertEquals(2, count($stmt->getChildren()));
    }

    /**
     * testFirstChildOfWhileStatementIsASTExpression
     *
     * @return void
     */
    public function testFirstChildOfWhileStatementIsASTExpression()
    {
        $stmt = $this->_getFirstWhileStatementInFunction();
        $this->assertInstanceOf(PHP_Depend_Code_ASTExpression::CLAZZ, $stmt->getChild(0));
    }

    /**
     * testSecondChildOfWhileStatementIsASTScopeStatement
     *
     * @return void
     */
    public function testSecondChildOfWhileStatementIsASTScopeStatement()
    {
        $stmt = $this->_getFirstWhileStatementInFunction();
        $this->assertInstanceOf(PHP_Depend_Code_ASTScopeStatement::CLAZZ, $stmt->getChild(1));
    }

    /**
     * testWhileStatement
     *
     * @return PHP_Depend_Code_ASTWhileStatement
     * @since 1.0.2
     */
    public function testWhileStatement()
    {
        $stmt = $this->_getFirstWhileStatementInFunction();
        $this->assertInstanceOf(PHP_Depend_Code_ASTWhileStatement::CLAZZ, $stmt);

        return $stmt;
    }

    /**
     * testWhileStatementHasExpectedStartLine
     *
     * @param PHP_Depend_Code_ASTWhileStatement $stmt
     *
     * @return void
     * @depends testWhileStatement
     */
    public function testWhileStatementHasExpectedStartLine($stmt)
    {
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testWhileStatementHasExpectedStartColumn
     *
     * @param PHP_Depend_Code_ASTWhileStatement $stmt
     *
     * @return void
     * @depends testWhileStatement
     */
    public function testWhileStatementHasExpectedStartColumn($stmt)
    {
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testWhileStatementHasExpectedEndLine
     *
     * @param PHP_Depend_Code_ASTWhileStatement $stmt
     *
     * @return void
     * @depends testWhileStatement
     */
    public function testWhileStatementHasExpectedEndLine($stmt)
    {
        $this->assertEquals(6, $stmt->getEndLine());
    }

    /**
     * testWhileStatementHasExpectedEndColumn
     *
     * @param PHP_Depend_Code_ASTWhileStatement $stmt
     *
     * @return void
     * @depends testWhileStatement
     */
    public function testWhileStatementHasExpectedEndColumn($stmt)
    {
        $this->assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testWhileStatementWithAlternativeScope
     *
     * @return PHP_Depend_Code_ASTWhileStatement
     * @since 1.0.2
     */
    public function testWhileStatementWithAlternativeScope()
    {
        $stmt = $this->_getFirstWhileStatementInFunction();
        $this->assertInstanceOf(PHP_Depend_Code_ASTWhileStatement::CLAZZ, $stmt);

        return $stmt;
    }

    /**
     * testWhileStatementAlternativeScopeHasExpectedStartLine
     *
     * @param PHP_Depend_Code_ASTWhileStatement $stmt
     *
     * @return void
     * @depends testWhileStatementWithAlternativeScope
     */
    public function testWhileStatementAlternativeScopeHasExpectedStartLine($stmt)
    {
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testWhileStatementAlternativeScopeHasExpectedStartColumn
     *
     * @param PHP_Depend_Code_ASTWhileStatement $stmt
     *
     * @return void
     * @depends testWhileStatementWithAlternativeScope
     */
    public function testWhileStatementAlternativeScopeHasExpectedStartColumn($stmt)
    {
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testWhileStatementAlternativeScopeHasExpectedEndLine
     *
     * @param PHP_Depend_Code_ASTWhileStatement $stmt
     *
     * @return void
     * @depends testWhileStatementWithAlternativeScope
     */
    public function testWhileStatementAlternativeScopeHasExpectedEndLine($stmt)
    {
        $this->assertEquals(8, $stmt->getEndLine());
    }

    /**
     * testWhileStatementAlternativeScopeHasExpectedEndColumn
     *
     * @param PHP_Depend_Code_ASTWhileStatement $stmt
     *
     * @return void
     * @depends testWhileStatementWithAlternativeScope
     */
    public function testWhileStatementAlternativeScopeHasExpectedEndColumn($stmt)
    {
        $this->assertEquals(13, $stmt->getEndColumn());
    }

    /**
     * testWhileStatementTerminatedByPhpCloseTag
     *
     * @return void
     */
    public function testWhileStatementTerminatedByPhpCloseTag()
    {
        $stmt = $this->_getFirstWhileStatementInFunction();
        self::assertEquals(9, $stmt->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return PHP_Depend_Code_ASTWhileStatement
     */
    private function _getFirstWhileStatementInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            PHP_Depend_Code_ASTWhileStatement::CLAZZ
        );
    }
}
