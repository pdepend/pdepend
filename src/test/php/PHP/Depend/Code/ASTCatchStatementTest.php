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
 * Test case for the {@link PHP_Depend_Code_ASTCatchStatement} class.
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
 * @covers PHP_Depend_Code_ASTCatchStatement
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTCatchStatementTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * Tests the start line value.
     *
     * @return void
     */
    public function testCatchStatementHasExpectedStartLine()
    {
        $stmt = $this->_getFirstCatchStatementInFunction(__METHOD__);
        $this->assertEquals(6, $stmt->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @return void
     */
    public function testCatchStatementHasExpectedStartColumn()
    {
        $stmt = $this->_getFirstCatchStatementInFunction(__METHOD__);
        $this->assertEquals(7, $stmt->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @return void
     */
    public function testCatchStatementHasExpectedEndLine()
    {
        $stmt = $this->_getFirstCatchStatementInFunction(__METHOD__);
        $this->assertEquals(6, $stmt->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @return void
     */
    public function testCatchStatementHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstCatchStatementInFunction(__METHOD__);
        $this->assertEquals(29, $stmt->getEndColumn());
    }

    /**
     * testCatchStatementVariableHasExpectedStartLine
     *
     * @return void
     */
    public function testCatchStatementVariableHasExpectedStartLine()
    {
        $variable = $this->_getFirstCatchStatementInFunction(__METHOD__)
            ->getFirstChildOfType(PHP_Depend_Code_ASTVariable::CLAZZ);
        $this->assertEquals(8, $variable->getStartLine());
    }

    /**
     * testCatchStatementVariableHasExpectedStartColumn
     *
     * @return void
     */
    public function testCatchStatementVariableHasExpectedStartColumn()
    {
        $variable = $this->_getFirstCatchStatementInFunction(__METHOD__)
            ->getFirstChildOfType(PHP_Depend_Code_ASTVariable::CLAZZ);
        $this->assertEquals(9, $variable->getStartColumn());
    }

    /**
     * testCatchStatementVariableHasExpectedEndLine
     *
     * @return void
     */
    public function testCatchStatementVariableHasExpectedEndLine()
    {
        $variable = $this->_getFirstCatchStatementInFunction(__METHOD__)
            ->getFirstChildOfType(PHP_Depend_Code_ASTVariable::CLAZZ);
        $this->assertEquals(8, $variable->getStartLine());
    }

    /**
     * testCatchStatementVariableHasExpectedEndColumn
     *
     * @return void
     */
    public function testCatchStatementVariableHasExpectedEndColumn()
    {
        $variable = $this->_getFirstCatchStatementInFunction(__METHOD__)
            ->getFirstChildOfType(PHP_Depend_Code_ASTVariable::CLAZZ);
        $this->assertEquals(10, $variable->getEndColumn());
    }

    /**
     * testThirdChildOfCatchStatementIsScopeStatement
     *
     * @return void
     */
    public function testThirdChildOfCatchStatementIsScopeStatement()
    {
        $stmt = $this->_getFirstCatchStatementInFunction(__METHOD__);
        $this->assertInstanceOf(PHP_Depend_Code_ASTScopeStatement::CLAZZ, $stmt->getChild(2));
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTCatchStatement
     */
    private function _getFirstCatchStatementInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTCatchStatement::CLAZZ
        );
    }
}
