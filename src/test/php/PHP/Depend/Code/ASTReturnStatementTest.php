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
 * Test case for the {@link PHP_Depend_Code_ASTReturnStatement} class.
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
 * @covers PHP_Depend_Code_ASTReturnStatement
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTReturnStatementTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testReturnStatement
     *
     * @return PHP_Depend_Code_ASTReturnStatement
     * @since 1.0.2
     */
    public function testReturnStatement()
    {
        $stmt = $this->_getFirstReturnStatementInFunction();
        $this->assertInstanceOf(PHP_Depend_Code_ASTReturnStatement::CLAZZ, $stmt);

        return $stmt;
    }

    /**
     * testReturnStatementHasExpectedStartLine
     *
     * @param PHP_Depend_Code_ASTReturnStatement $stmt
     *
     * @return void
     * @depends testReturnStatement
     */
    public function testReturnStatementHasExpectedStartLine($stmt)
    {
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testReturnStatementHasExpectedStartColumn
     *
     * @param PHP_Depend_Code_ASTReturnStatement $stmt
     *
     * @return void
     * @depends testReturnStatement
     */
    public function testReturnStatementHasExpectedStartColumn($stmt)
    {
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testReturnStatementHasExpectedEndLine
     *
     * @param PHP_Depend_Code_ASTReturnStatement $stmt
     *
     * @return void
     * @depends testReturnStatement
     */
    public function testReturnStatementHasExpectedEndLine($stmt)
    {
        $this->assertEquals(7, $stmt->getEndLine());
    }

    /**
     * testReturnStatementHasExpectedEndColumn
     *
     * @param PHP_Depend_Code_ASTReturnStatement $stmt
     *
     * @return void
     * @depends testReturnStatement
     */
    public function testReturnStatementHasExpectedEndColumn($stmt)
    {
        $this->assertEquals(6, $stmt->getEndColumn());
    }

    /**
     * testParserHandlesEmptyReturnStatement
     *
     * @return void
     */
    public function testParserHandlesEmptyReturnStatement()
    {
        $stmt = $this->_getFirstReturnStatementInFunction();
        $this->assertEquals(12, $stmt->getEndColumn());
    }

    /**
     * testParserHandlesReturnStatementWithSimpleBoolean
     *
     * @return void
     */
    public function testParserHandlesReturnStatementWithSimpleBoolean()
    {
        $stmt = $this->_getFirstReturnStatementInFunction();
        $this->assertEquals(17, $stmt->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return PHP_Depend_Code_ASTReturnStatement
     */
    private function _getFirstReturnStatementInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            PHP_Depend_Code_ASTReturnStatement::CLAZZ
        );
    }
}
