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
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

namespace PHP\Depend\Source\AST;

/**
 * Test case for the {@link \PHP\\Depend\\Source\\AST\\ASTPreIncrementExpression} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @covers \PHP\Depend\Source\Language\PHP\AbstractPHPParser
 * @covers \PHP\Depend\Source\Language\PHP\PHPBuilder
 * @covers \PHP\\Depend\\Source\\AST\\ASTPreIncrementExpression
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class ASTPreIncrementExpressionTest extends ASTNodeTest
{
    /**
     * testPreIncrementExpressionOnStaticClassMember
     *
     * @return void
     */
    public function testPreIncrementExpressionOnStaticClassMember()
    {
        $expr = $this->_getFirstPreIncrementExpressionInFunction(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PHP\\Depend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PHP\\Depend\\Source\\AST\\ASTClassOrInterfaceReference',
                'PHP\\Depend\\Source\\AST\\ASTPropertyPostfix',
                'PHP\\Depend\\Source\\AST\\ASTVariable'
            )
        );
    }

    /**
     * testPreIncrementExpressionOnSelfClassMember
     *
     * @return void
     */
    public function testPreIncrementExpressionOnSelfClassMember()
    {
        $expr = $this->_getFirstPreIncrementExpressionInClass(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PHP\\Depend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PHP\\Depend\\Source\\AST\\ASTSelfReference',
                'PHP\\Depend\\Source\\AST\\ASTPropertyPostfix',
                'PHP\\Depend\\Source\\AST\\ASTVariable'
            )
        );
    }

    /**
     * testPreIncrementExpressionOnParentClassMember
     *
     * @return void
     */
    public function testPreIncrementExpressionOnParentClassMember()
    {
        $expr = $this->_getFirstPreIncrementExpressionInClass(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PHP\\Depend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PHP\\Depend\\Source\\AST\\ASTParentReference',
                'PHP\\Depend\\Source\\AST\\ASTPropertyPostfix',
                'PHP\\Depend\\Source\\AST\\ASTVariable'
            )
        );
    }

    /**
     * testPreIncrementExpressionOnFunctionPostfix
     *
     * @return void
     */
    public function testPreIncrementExpressionOnFunctionPostfix()
    {
        $expr = $this->_getFirstPreIncrementExpressionInFunction(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PHP\\Depend\\Source\\AST\\ASTFunctionPostfix',
                'PHP\\Depend\\Source\\AST\\ASTIdentifier',
                'PHP\\Depend\\Source\\AST\\ASTArguments'
            )
        );
    }

    /**
     * testPreIncrementExpressionOnStaticVariableMember
     *
     * @return void
     */
    public function testPreIncrementExpressionOnStaticVariableMember()
    {
        $expr = $this->_getFirstPreIncrementExpressionInFunction(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PHP\\Depend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PHP\\Depend\\Source\\AST\\ASTVariable',
                'PHP\\Depend\\Source\\AST\\ASTPropertyPostfix',
                'PHP\\Depend\\Source\\AST\\ASTVariable'
            )
        );
    }

    /**
     * testPreIncrementExpressionsInArithmeticOperation
     * 
     * @return void
     */
    public function testPreIncrementExpressionsInArithmeticOperation()
    {
        $exprs = $this->getFirstClassForTestCase()
            ->getMethods()
            ->current()
            ->findChildrenOfType(ASTPreIncrementExpression::CLAZZ);

        $this->assertEquals(2, count($exprs));
    }

    /**
     * testPreIncrementExpressionHasExpectedStartLine
     *
     * @return void
     */
    public function testPreIncrementExpressionHasExpectedStartLine()
    {
        $expr = $this->_getFirstPreIncrementExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * testPreIncrementExpressionHasExpectedStartColumn
     *
     * @return void
     */
    public function testPreIncrementExpressionHasExpectedStartColumn()
    {
        $expr = $this->_getFirstPreIncrementExpressionInFunction(__METHOD__);
        $this->assertEquals(13, $expr->getStartColumn());
    }

    /**
     * testPreIncrementExpressionHasExpectedEndLine
     *
     * @return void
     */
    public function testPreIncrementExpressionHasExpectedEndLine()
    {
        $expr = $this->_getFirstPreIncrementExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getEndLine());
    }

    /**
     * testPreIncrementExpressionHasExpectedEndColumn
     *
     * @return void
     */
    public function testPreIncrementExpressionHasExpectedEndColumn()
    {
        $expr = $this->_getFirstPreIncrementExpressionInFunction(__METHOD__);
        $this->assertEquals(20, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     * @return \PHP\Depend\Source\AST\ASTPreIncrementExpression
     */
    private function _getFirstPreIncrementExpressionInClass($testCase)
    {
        return $this->getFirstNodeOfTypeInClass(
            $testCase, ASTPreIncrementExpression::CLAZZ
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     * @return \PHP\Depend\Source\AST\ASTPreIncrementExpression
     */
    private function _getFirstPreIncrementExpressionInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, ASTPreIncrementExpression::CLAZZ
        );
    }
}
