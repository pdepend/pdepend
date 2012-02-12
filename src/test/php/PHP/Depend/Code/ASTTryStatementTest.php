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
 * Test case for the {@link PHP_Depend_Code_ASTTryStatement} class.
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
 * @covers PHP_Depend_Code_ASTTryStatement
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTTryStatementTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testTryStatement
     *
     * @return PHP_Depend_Code_ASTTryStatement
     * @since 1.0.2
     */
    public function testTryStatement()
    {
        $stmt = $this->_getFirstTryStatementInFunction();
        $this->assertInstanceOf(PHP_Depend_Code_ASTTryStatement::CLAZZ, $stmt);

        return $stmt;
    }

    /**
     * Tests that the try-statement has the expected start line value.
     *
     * @param PHP_Depend_Code_ASTTryStatement $stmt
     *
     * @return void
     * @depends testTryStatement
     */
    public function testTryStatementHasExpectedStartLine($stmt)
    {
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * Tests that the try-statement has the expected start column value.
     *
     * @param PHP_Depend_Code_ASTTryStatement $stmt
     *
     * @return void
     * @depends testTryStatement
     */
    public function testTryStatementHasExpectedStartColumn($stmt)
    {
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * Tests that the try-statement has the expected end line value.
     *
     * @param PHP_Depend_Code_ASTTryStatement $stmt
     *
     * @return void
     * @depends testTryStatement
     */
    public function testTryStatementHasExpectedEndLine($stmt)
    {
        $this->assertEquals(8, $stmt->getEndLine());
    }

    /**
     * Tests that the try-statement has the expected end column value.
     *
     * @param PHP_Depend_Code_ASTTryStatement $stmt
     *
     * @return void
     * @depends testTryStatement
     */
    public function testTryStatementHasExpectedEndColumn($stmt)
    {
        $this->assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testFirstChildOfTryStatementIsInstanceOfScopeStatement
     *
     * @param PHP_Depend_Code_ASTTryStatement $stmt
     *
     * @return void
     * @depends testTryStatement
     */
    public function testFirstChildOfTryStatementIsInstanceOfScopeStatement($stmt)
    {
        $this->assertInstanceOf(PHP_Depend_Code_ASTScopeStatement::CLAZZ, $stmt->getChild(0));
    }

    /**
     * testSecondChildOfTryStatementIsInstanceOfCatchStatement
     *
     * @param PHP_Depend_Code_ASTTryStatement $stmt
     *
     * @return void
     * @depends testTryStatement
     */
    public function testSecondChildOfTryStatementIsInstanceOfCatchStatement($stmt)
    {
        $this->assertInstanceOf(PHP_Depend_Code_ASTCatchStatement::CLAZZ, $stmt->getChild(1));
    }

    /**
     * testTryStatementContainsMultipleChildInstancesOfCatchStatement
     *
     * @return void
     */
    public function testTryStatementContainsMultipleChildInstancesOfCatchStatement()
    {
        $actual = array();
        foreach ($this->_getFirstTryStatementInFunction(__METHOD__)->getChildren() as $child) {
            $actual[] = get_class($child);
        }

        $expected = array(
            PHP_Depend_Code_ASTScopeStatement::CLAZZ,
            PHP_Depend_Code_ASTCatchStatement::CLAZZ,
            PHP_Depend_Code_ASTCatchStatement::CLAZZ,
            PHP_Depend_Code_ASTCatchStatement::CLAZZ,
            PHP_Depend_Code_ASTCatchStatement::CLAZZ,
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testParserThrowsExceptionWhenNoCatchStatementFollows
     *
     * @return void
     * @expectedException PHP_Depend_Parser_UnexpectedTokenException
     */
    public function testParserThrowsExceptionWhenNoCatchStatementFollows()
    {
        $this->_getFirstTryStatementInFunction();
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return PHP_Depend_Code_ASTTryStatement
     */
    private function _getFirstTryStatementInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            PHP_Depend_Code_ASTTryStatement::CLAZZ
        );
    }
}
