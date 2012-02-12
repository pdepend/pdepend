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
 * Test case for the {@link PHP_Depend_Code_ASTScopeStatement} class.
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
 * @covers PHP_Depend_Code_ASTScopeStatement
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTScopeStatementTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testParserHandlesInlineScopeStatement
     *
     * @return PHP_Depend_Code_ASTScopeStatement
     * @since 1.0.0
     */
    public function testParserHandlesInlineScopeStatement()
    {
        $stmt = $this->_getFirstScopeStatementInFunction();
        $this->assertEquals(1, count($stmt->getChildren()));

        return $stmt;
    }

    /**
     * testInlineScopeStatementHasExpectedStartLine
     *
     * @param PHP_Depend_Code_ASTScopeStatement $stmt
     *
     * @return PHP_Depend_Code_ASTScopeStatement
     * @since 1.0.0
     * @depends testParserHandlesInlineScopeStatement
     */
    public function testInlineScopeStatementHasExpectedStartLine($stmt)
    {
        $this->assertEquals(4, $stmt->getStartLine());

        return $stmt;
    }

    /**
     * testInlineScopeStatementHasExpectedStartColumn
     *
     * @param PHP_Depend_Code_ASTScopeStatement $stmt
     *
     * @return PHP_Depend_Code_ASTScopeStatement
     * @since 1.0.0
     * @depends testInlineScopeStatementHasExpectedStartLine
     */
    public function testInlineScopeStatementHasExpectedStartColumn($stmt)
    {
        $this->assertEquals(5, $stmt->getStartColumn());

        return $stmt;
    }

    /**
     * testInlineScopeStatementHasExpectedEndLine
     *
     * @param PHP_Depend_Code_ASTScopeStatement $stmt
     *
     * @return PHP_Depend_Code_ASTScopeStatement
     * @since 1.0.0
     * @depends testInlineScopeStatementHasExpectedStartColumn
     */
    public function testInlineScopeStatementHasExpectedEndLine($stmt)
    {
        $this->assertEquals(5, $stmt->getEndLine());

        return $stmt;
    }

    /**
     * testInlineScopeStatementHasExpectedEndColumn
     *
     * @param PHP_Depend_Code_ASTScopeStatement $stmt
     *
     * @return PHP_Depend_Code_ASTScopeStatement
     * @since 1.0.0
     * @depends testInlineScopeStatementHasExpectedEndLine
     */
    public function testInlineScopeStatementHasExpectedEndColumn($stmt)
    {
        $this->assertEquals(20, $stmt->getEndColumn());
    }

    /**
     * testScopeStatement
     *
     * @return PHP_Depend_Code_ASTScopeStatement
     * @since 1.0.2
     */
    public function testScopeStatement()
    {
        $stmt = $this->_getFirstScopeStatementInFunction();
        $this->assertInstanceOf(PHP_Depend_Code_ASTScopeStatement::CLAZZ, $stmt);

        return $stmt;
    }
    
    /**
     * Tests that the scope-statement has the expected start line value.
     *
     * @param PHP_Depend_Code_ASTScopeStatement $stmt
     *
     * @return void
     * @depends testScopeStatement
     */
    public function testScopeStatementHasExpectedStartLine($stmt)
    {
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * Tests that the scope-statement has the expected start column value.
     *
     * @param PHP_Depend_Code_ASTScopeStatement $stmt
     *
     * @return void
     * @depends testScopeStatement
     */
    public function testScopeStatementHasExpectedStartColumn($stmt)
    {
        $this->assertEquals(34, $stmt->getStartColumn());
    }

    /**
     * Tests that the scope-statement has the expected end line value.
     *
     * @param PHP_Depend_Code_ASTScopeStatement $stmt
     *
     * @return void
     * @depends testScopeStatement
     */
    public function testScopeStatementHasExpectedEndLine($stmt)
    {
        $this->assertEquals(6, $stmt->getEndLine());
    }

    /**
     * Tests that the scope-statement has the expected end column value.
     *
     * @param PHP_Depend_Code_ASTScopeStatement $stmt
     *
     * @return void
     * @depends testScopeStatement
     */
    public function testScopeStatementHasExpectedEndColumn($stmt)
    {
        $this->assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testScopeStatementWithAlternative
     * 
     * @return PHP_Depend_Code_ASTScopeStatement
     * @since 1.0.2
     */
    public function testScopeStatementWithAlternative()
    {
        $stmt = $this->_getFirstScopeStatementInFunction();
        $this->assertInstanceOf(PHP_Depend_Code_ASTScopeStatement::CLAZZ, $stmt);

        return $stmt;
    }

    /**
     * testScopeStatementWithAlternativeHasExpectedStartLine
     *
     * @param PHP_Depend_Code_ASTScopeStatement $stmt
     *
     * @return void
     * @depends testScopeStatementWithAlternative
     */
    public function testScopeStatementWithAlternativeHasExpectedStartLine($stmt)
    {
        $this->assertEquals(6, $stmt->getStartLine());
    }

    /**
     * testScopeStatementWithAlternativeHasExpectedStartColumn
     *
     * @param PHP_Depend_Code_ASTScopeStatement $stmt
     *
     * @return void
     * @depends testScopeStatementWithAlternative
     */
    public function testScopeStatementWithAlternativeHasExpectedStartColumn($stmt)
    {
        $this->assertEquals(13, $stmt->getStartColumn());
    }

    /**
     * testScopeStatementWithAlternativeHasExpectedEndLine
     *
     * @param PHP_Depend_Code_ASTScopeStatement $stmt
     *
     * @return void
     * @depends testScopeStatementWithAlternative
     */
    public function testScopeStatementWithAlternativeHasExpectedEndLine($stmt)
    {
        $this->assertEquals(17, $stmt->getEndLine());
    }

    /**
     * testScopeStatementWithAlternativeHasExpectedEndColumn
     *
     * @param PHP_Depend_Code_ASTScopeStatement $stmt
     *
     * @return void
     * @depends testScopeStatementWithAlternative
     */
    public function testScopeStatementWithAlternativeHasExpectedEndColumn($stmt)
    {
        $this->assertEquals(15, $stmt->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return PHP_Depend_Code_ASTScopeStatement
     */
    private function _getFirstScopeStatementInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            PHP_Depend_Code_ASTScopeStatement::CLAZZ
        );
    }
}
