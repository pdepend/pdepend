<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2010, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

require_once 'PHP/Depend/Code/ASTArrayExpression.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTArrayExpression} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Code_ASTArrayExpressionTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testArrayExpressionGraphForVariable
     *
     * <code>
     * $array[42];
     * </code>
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArrayExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArrayExpressionGraphForVariable()
    {
        $expression = $this->_getFirstArrayExpressionInFunction(__METHOD__);
        $expected   = array(
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTLiteral::CLAZZ
        );

        $this->assertGraphEquals($expression, $expected);
    }

    /**
     * testArrayExpressionGraphForProperty
     *
     * <code>
     * $object->foo[42];
     * </code>
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArrayExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArrayExpressionGraphForProperty()
    {
        $expression = $this->_getFirstArrayExpressionInFunction(__METHOD__);
        $expected   = array(
            PHP_Depend_Code_ASTIdentifier::CLAZZ,
            PHP_Depend_Code_ASTLiteral::CLAZZ
        );

        $this->assertGraphEquals($expression, $expected);
    }

    /**
     * testArrayExpressionGraphForChainedArrayAccess
     *
     * <code>
     * $array[0][0][0];
     * </code>
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArrayExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArrayExpressionGraphForChainedArrayAccess()
    {
        $expression = $this->_getFirstArrayExpressionInFunction(__METHOD__);
        $expected   = array(
            PHP_Depend_Code_ASTArrayExpression::CLAZZ,
            PHP_Depend_Code_ASTArrayExpression::CLAZZ,
            PHP_Depend_Code_ASTVariable::CLAZZ,
            PHP_Depend_Code_ASTLiteral::CLAZZ,
            PHP_Depend_Code_ASTLiteral::CLAZZ,
            PHP_Depend_Code_ASTLiteral::CLAZZ
        );

        $this->assertGraphEquals($expression, $expected);
    }

    /**
     * testArrayExpressionHasExpectedStartLine
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArrayExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArrayExpressionHasExpectedStartLine()
    {
        $expression = $this->_getFirstArrayExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expression->getStartLine());
    }

    /**
     * testArrayExpressionHasExpectedStartColumn
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArrayExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArrayExpressionHasExpectedStartColumn()
    {
        $expression = $this->_getFirstArrayExpressionInFunction(__METHOD__);
        $this->assertEquals(10, $expression->getStartColumn());
    }

    /**
     * testArrayExpressionHasExpectedEndLine
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArrayExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArrayExpressionHasExpectedEndLine()
    {
        $expression = $this->_getFirstArrayExpressionInFunction(__METHOD__);
        $this->assertEquals(6, $expression->getEndLine());
    }

    /**
     * testArrayExpressionHasExpectedEndColumn
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTArrayExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testArrayExpressionHasExpectedEndColumn()
    {
        $expression = $this->_getFirstArrayExpressionInFunction(__METHOD__);
        $this->assertEquals(13, $expression->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTArrayExpression
     */
    private function _getFirstArrayExpressionInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTArrayExpression::CLAZZ
        );
    }
}