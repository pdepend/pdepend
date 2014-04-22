<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Source\AST;

/**
 * Test case for the {@link ASTPostfixExpression} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTPostfixExpression
 * @group unittest
 */
class ASTPostfixExpressionTest extends ASTNodeTest
{
    /**
     * testIncrementPostfixExpressionOnStaticClassMember
     *
     * @return void
     */
    public function testIncrementPostfixExpressionOnStaticClassMember()
    {
        $expr = $this->_getFirstPostfixExpressionInClass(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTClassOrInterfaceReference',
                'PDepend\\Source\\AST\\ASTPropertyPostfix',
                'PDepend\\Source\\AST\\ASTVariable'
            )
        );
    }

    /**
     * testIncrementPostfixExpressionOnSelfClassMember
     *
     * @return void
     */
    public function testIncrementPostfixExpressionOnSelfClassMember()
    {
        $expr = $this->_getFirstPostfixExpressionInClass(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTSelfReference',
                'PDepend\\Source\\AST\\ASTPropertyPostfix',
                'PDepend\\Source\\AST\\ASTVariable'
            )
        );
    }

    /**
     * testIncrementPostfixExpressionOnParentClassMember
     *
     * @return void
     */
    public function testIncrementPostfixExpressionOnParentClassMember()
    {
        $expr = $this->_getFirstPostfixExpressionInClass(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTParentReference',
                'PDepend\\Source\\AST\\ASTPropertyPostfix',
                'PDepend\\Source\\AST\\ASTVariable'
            )
        );
    }

    /**
     * testIncrementPostfixExpressionOnThisObjectMember
     *
     * @return void
     */
    public function testIncrementPostfixExpressionOnThisObjectMember()
    {
        $expr = $this->_getFirstPostfixExpressionInClass(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTPropertyPostfix',
                'PDepend\\Source\\AST\\ASTIdentifier'
            )
        );
    }

    /**
     * testIncrementPostfixExpressionOnFunctionPostfix
     *
     * @return void
     */
    public function testIncrementPostfixExpressionOnFunctionPostfix()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PDepend\\Source\\AST\\ASTFunctionPostfix',
                'PDepend\\Source\\AST\\ASTIdentifier',
                'PDepend\\Source\\AST\\ASTArguments',
                'PDepend\\Source\\AST\\ASTVariable'
            )
        );
    }

    /**
     * testIncrementPostfixExpressionOnVariableVariable
     *
     * @return void
     */
    public function testIncrementPostfixExpressionOnVariableVariable()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PDepend\\Source\\AST\\ASTVariableVariable',
                'PDepend\\Source\\AST\\ASTVariableVariable',
                'PDepend\\Source\\AST\\ASTVariable'
            )
        );
    }

    /**
     * testIncrementPostfixExpressionOnCompoundVariable
     *
     * @return void
     */
    public function testIncrementPostfixExpressionOnCompoundVariable()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PDepend\\Source\\AST\\ASTCompoundVariable',
                'PDepend\\Source\\AST\\ASTConstant'
            )
        );
    }

    /**
     * testIncrementPostfixExpressionOnObjectMethodPostfix
     *
     * @return void
     */
    public function testIncrementPostfixExpressionOnObjectMethodPostfix()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTMethodPostfix',
                'PDepend\\Source\\AST\\ASTIdentifier',
                'PDepend\\Source\\AST\\ASTArguments',
                'PDepend\\Source\\AST\\ASTVariable'
            )
        );
    }

    /**
     * testIncrementPostfixExpressionOnStaticMethodPostfix
     *
     * @return void
     */
    public function testIncrementPostfixExpressionOnStaticMethodPostfix()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTClassOrInterfaceReference',
                'PDepend\\Source\\AST\\ASTMethodPostfix',
                'PDepend\\Source\\AST\\ASTIdentifier',
                'PDepend\\Source\\AST\\ASTArguments'
            )
        );
    }

    /**
     * testIncrementPostfixExpressionArrayPropertyPostfix
     * 
     * @return void
     */
    public function testIncrementPostfixExpressionArrayPropertyPostfix()
    {
        $expr = $this->_getFirstPostfixExpressionInClass(__METHOD__)->getParent();
        $this->assertGraphEquals(
            $expr,
            array(
                'PDepend\\Source\\AST\\ASTPostfixExpression',
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTPropertyPostfix',
                'PDepend\\Source\\AST\\ASTArrayIndexExpression',
                'PDepend\\Source\\AST\\ASTIdentifier',
                'PDepend\\Source\\AST\\ASTVariable'
            )
        );
    }
    
    /**
     * testIncrementPostfixExpressionHasExpectedStartLine
     *
     * @return void
     */
    public function testIncrementPostfixExpressionHasExpectedStartLine()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertEquals(5, $expr->getStartLine());
    }

    /**
     * testIncrementPostfixExpressionHasExpectedStartColumn
     *
     * @return void
     */
    public function testIncrementPostfixExpressionHasExpectedStartColumn()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertEquals(9, $expr->getStartColumn());
    }

    /**
     * testIncrementPostfixExpressionHasExpectedEndLine
     *
     * @return void
     */
    public function testIncrementPostfixExpressionHasExpectedEndLine()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertEquals(7, $expr->getEndLine());
    }

    /**
     * testIncrementPostfixExpressionHasExpectedEndColumn
     *
     * @return void
     */
    public function testIncrementPostfixExpressionHasExpectedEndColumn()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertEquals(14, $expr->getEndColumn());
    }

    /**
     * testDecrementPostfixExpressionArrayPropertyPostfix
     *
     * @return void
     */
    public function testDecrementPostfixExpressionArrayPropertyPostfix()
    {
        $expr = $this->_getFirstPostfixExpressionInClass(__METHOD__)->getParent();
        $this->assertGraphEquals(
            $expr,
            array(
                'PDepend\\Source\\AST\\ASTPostfixExpression',
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTPropertyPostfix',
                'PDepend\\Source\\AST\\ASTArrayIndexExpression',
                'PDepend\\Source\\AST\\ASTIdentifier',
                'PDepend\\Source\\AST\\ASTVariable'
            )
        );
    }
    
    /**
     * testDecrementPostfixExpressionHasExpectedStartLine
     *
     * @return void
     */
    public function testDecrementPostfixExpressionHasExpectedStartLine()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertEquals(7, $expr->getStartLine());
    }

    /**
     * testDecrementPostfixExpressionHasExpectedStartColumn
     *
     * @return void
     */
    public function testDecrementPostfixExpressionHasExpectedStartColumn()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertEquals(17, $expr->getStartColumn());
    }

    /**
     * testDecrementPostfixExpressionHasExpectedEndLine
     *
     * @return void
     */
    public function testDecrementPostfixExpressionHasExpectedEndLine()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertEquals(9, $expr->getEndLine());
    }

    /**
     * testDecrementPostfixExpressionHasExpectedEndColumn
     *
     * @return void
     */
    public function testDecrementPostfixExpressionHasExpectedEndColumn()
    {
        $expr = $this->_getFirstPostfixExpressionInFunction(__METHOD__);
        $this->assertEquals(10, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return ASTPostfixExpression
     */
    private function _getFirstPostfixExpressionInClass($testCase)
    {
        return $this->getFirstNodeOfTypeInClass(
            $testCase,
            'PDepend\\Source\\AST\\ASTPostfixExpression'
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return ASTPostfixExpression
     */
    private function _getFirstPostfixExpressionInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, 'PDepend\\Source\\AST\\ASTPostfixExpression'
        );
    }
}
