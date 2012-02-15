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
 * @author     Joey Mazzarelli
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTListExpression} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @author     Joey Mazzarelli
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 *
 * @covers PHP_Depend_Parser
 * @covers PHP_Depend_Code_ASTListExpression
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTListExpressionTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testListExpression
     *
     * @return PHP_Depend_Code_ASTListExpression
     * @since 1.0.2
     */
    public function testListExpression()
    {
        $expr = $this->_getFirstListExpressionInFunction();
        $this->assertInstanceOf(PHP_Depend_Code_ASTListExpression::CLAZZ, $expr);

        return $expr;
    }

    /**
     * Tests the start line value.
     *
     * @param PHP_Depend_Code_ASTListExpression $expr
     *
     * @return void
     * @depends testListExpression
     */
    public function testListExpressionHasExpectedStartLine($expr)
    {
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @param PHP_Depend_Code_ASTListExpression $expr
     *
     * @return void
     * @depends testListExpression
     */
    public function testListExpressionHasExpectedStartColumn($expr)
    {
        $this->assertEquals(5, $expr->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @param PHP_Depend_Code_ASTListExpression $expr
     *
     * @return void
     * @depends testListExpression
     */
    public function testListExpressionHasExpectedEndLine($expr)
    {
        $this->assertEquals(4, $expr->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @param PHP_Depend_Code_ASTListExpression $expr
     *
     * @return void
     * @depends testListExpression
     */
    public function testListExpressionHasExpectedEndColumn($expr)
    {
        $this->assertEquals(16, $expr->getEndColumn());
    }

    /**
     * testListExpressionWithNestedList
     *
     * @return PHP_Depend_Code_ASTListExpression
     * @since 1.0.2
     */
    public function testListExpressionWithNestedList()
    {
        $expr = $this->_getFirstListExpressionInFunction();
        $this->assertInstanceOf(PHP_Depend_Code_ASTListExpression::CLAZZ, $expr);

        return $expr;
    }

    /**
     * testListExpressionWithNestedListHasExpectedStartLine
     *
     * @param PHP_Depend_Code_ASTListExpression $expr
     *
     * @return void
     * @since 1.0.2
     * @depends testListExpressionWithNestedList
     */
    public function testListExpressionWithNestedListHasExpectedStartLine($expr)
    {
        $this->assertEquals(4, $expr->getStartLine());
    }
    
    /**
     * testListExpressionWithNestedListHasExpectedStartColumn
     *
     * @param PHP_Depend_Code_ASTListExpression $expr
     *
     * @return void
     * @since 1.0.2
     * @depends testListExpressionWithNestedList
     */
    public function testListExpressionWithNestedListHasExpectedStartColumn($expr)
    {
        $this->assertEquals(5, $expr->getStartColumn());
    }
    
    /**
     * testListExpressionWithNestedListHasExpectedEndLine
     *
     * @param PHP_Depend_Code_ASTListExpression $expr
     *
     * @return void
     * @since 1.0.2
     * @depends testListExpressionWithNestedList
     */
    public function testListExpressionWithNestedListHasExpectedEndLine($expr)
    {
        $this->assertEquals(4, $expr->getEndLine());
    }
    
    /**
     * testListExpressionWithNestedListHasExpectedEndColumn
     *
     * @param PHP_Depend_Code_ASTListExpression $expr
     *
     * @return void
     * @since 1.0.2
     * @depends testListExpressionWithNestedList
     */
    public function testListExpressionWithNestedListHasExpectedEndColumn($expr)
    {
        $this->assertEquals(42, $expr->getEndColumn());
    }

    /**
     * Tests the list supports many variables in it
     *
     * @return void
     */
    public function testListExpressionSupportsManyVariables()
    {
        $expr = $this->_getFirstListExpressionInFunction();
        $vars = $expr->getChildren();
        $this->assertEquals(3, count($vars));
    }

    /**
     * Tests the list supports a single variable
     *
     * @return void
     */
    public function testListExpressionSupportsSingleVariable()
    {
        $expr = $this->_getFirstListExpressionInFunction();
        $vars = $expr->getChildren();
        $this->assertEquals(1, count($vars));
    }

    /**
     * Tests the list supports commas without variables
     *
     * @return void
     */
    public function testListExpressionSupportsExtraCommas()
    {
        $expr = $this->_getFirstListExpressionInFunction();
        $vars = $expr->getChildren();
        $this->assertEquals(3, count($vars));
    }

    /**
     * testListExpressionWithComments
     *
     * @return void
     */
    public function testListExpressionWithComments()
    {
        $expr = $this->_getFirstListExpressionInFunction();
        $vars = $expr->getChildren();
        $this->assertEquals(3, count($vars));
    }

    /**
     * testListExpressionWithoutChildExpression
     *
     * @return void
     */
    public function testListExpressionWithoutChildExpression()
    {
        $expr = $this->_getFirstListExpressionInFunction();
        $vars = $expr->getChildren();
        $this->assertEquals(0, count($vars));
    }

    /**
     * testListExpressionWithVariableVariable
     *
     * @return void
     */
    public function testListExpressionWithVariableVariable()
    {
        $expr = $this->_getFirstListExpressionInFunction();
        $var  = $expr->getChild(0);

        $this->assertInstanceOf(PHP_Depend_Code_ASTVariableVariable::CLAZZ, $var);
    }

    /**
     * testListExpressionWithCompoundVariable
     *
     * @return void
     */
    public function testListExpressionWithCompoundVariable()
    {
        $expr = $this->_getFirstListExpressionInFunction();
        $var  = $expr->getChild(0);

        $this->assertInstanceOf(PHP_Depend_Code_ASTCompoundVariable::CLAZZ, $var);
    }

    /**
     * testListExpressionWithArrayElement
     *
     * @return void
     */
    public function testListExpressionWithArrayElement()
    {
        $expr = $this->_getFirstListExpressionInFunction();
        $var  = $expr->getChild(0);

        $this->assertInstanceOf(PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ, $var);
    }

    /**
     * testListExpressionWithObjectProperty
     *
     * @return void
     */
    public function testListExpressionWithObjectProperty()
    {
        $expr = $this->_getFirstListExpressionInFunction();
        $var  = $expr->getChild(0);

        $this->assertInstanceOf(PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ, $var);
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return PHP_Depend_Code_ASTListExpression
     */
    private function _getFirstListExpressionInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            PHP_Depend_Code_ASTListExpression::CLAZZ
        );
    }
}
