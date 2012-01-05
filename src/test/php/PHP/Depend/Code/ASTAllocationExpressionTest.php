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
 * Test case for the {@link PHP_Depend_Code_ASTAllocationExpression} class.
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
 * @covers PHP_Depend_Code_ASTAllocationExpression
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTAllocationExpressionTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * Tests the implementation with an allocation expression without arguments.
     *
     * @return void
     */
    public function testAllocationExpressionWithoutArguments()
    {
        $expr = $this->_getFirstAllocationExpressionInFunction(__METHOD__);
        $args = $expr->findChildrenOfType(PHP_Depend_Code_ASTArguments::CLAZZ);

        $this->assertEquals(0, count($args));
    }

    /**
     * Tests the implementation with an allocation expression with arguments.
     *
     * @return void
     */
    public function testAllocationExpressionWithArguments()
    {
        $expr = $this->_getFirstAllocationExpressionInFunction(__METHOD__);
        $args = $expr->findChildrenOfType(PHP_Depend_Code_ASTArguments::CLAZZ);

        $this->assertEquals(1, count($args));
    }

    /**
     * Tests the implementation with an allocation expression with nested
     * expressions that have arguments.
     *
     * @return void
     */
    public function testAllocationExpressionWithNestedArguments()
    {
        $expr = $this->_getFirstAllocationExpressionInFunction(__METHOD__);
        $arg  = $expr->getFirstChildOfType(PHP_Depend_Code_ASTArguments::CLAZZ);

        self::assertEquals($expr, $arg->getParent());
    }

    /**
     * Tests the start line of an allocation expression.
     *
     * @return void
     */
    public function testAllocationExpressionHasExpectedStartLine()
    {
        $expr = $this->_getFirstAllocationExpressionInFunction(__METHOD__);
        self::assertEquals(4, $expr->getStartLine());
    }

    /**
     * Tests the start column of an allocation expression.
     *
     * @return void
     */
    public function testAllocationExpressionHasExpectedStartColumn()
    {
        $expr = $this->_getFirstAllocationExpressionInFunction(__METHOD__);
        self::assertEquals(5, $expr->getStartColumn());
    }

    /**
     * Tests the end line of an allocation expression.
     *
     * @return void
     */
    public function testAllocationExpressionHasExpectedEndLine()
    {
        $expr = $this->_getFirstAllocationExpressionInFunction(__METHOD__);
        self::assertEquals(8, $expr->getEndLine());
    }

    /**
     * Tests the end column of an allocation expression.
     *
     * @return void
     */
    public function testAllocationExpressionHasExpectedEndColumn()
    {
        $expr = $this->_getFirstAllocationExpressionInFunction(__METHOD__);
        self::assertEquals(13, $expr->getEndColumn());
    }

    /**
     * Returns a test allocation expression.
     *
     * @param string $testCase The calling test case.
     *
     * @return PHP_Depend_Code_ASTAllocationExpression
     */
    private function _getFirstAllocationExpressionInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTAllocationExpression::CLAZZ
        );
    }
}
