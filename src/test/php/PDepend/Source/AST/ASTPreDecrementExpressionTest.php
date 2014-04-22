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
 * Test case for the {@link \PDepend\Source\AST\ASTPreDecrementExpression} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTPreDecrementExpression
 * @group unittest
 */
class ASTPreDecrementExpressionTest extends ASTNodeTest
{
    /**
     * testPreDecrementExpressionOnStaticClassMember
     *
     * @return void
     */
    public function testPreDecrementExpressionOnStaticClassMember()
    {
        $expr = $this->_getFirstPreDecrementExpressionInFunction(__METHOD__);
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
     * testPreDecrementExpressionOnSelfClassMember
     *
     * @return void
     */
    public function testPreDecrementExpressionOnSelfClassMember()
    {
        $expr = $this->_getFirstPreDecrementExpressionInClass(__METHOD__);
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
     * testPreDecrementExpressionOnParentClassMember
     *
     * @return void
     */
    public function testPreDecrementExpressionOnParentClassMember()
    {
        $expr = $this->_getFirstPreDecrementExpressionInClass(__METHOD__);
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
     * testPreDecrementExpressionOnFunctionPostfix
     *
     * @return void
     */
    public function testPreDecrementExpressionOnFunctionPostfix()
    {
        $expr = $this->_getFirstPreDecrementExpressionInFunction(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PDepend\\Source\\AST\\ASTFunctionPostfix',
                'PDepend\\Source\\AST\\ASTIdentifier',
                'PDepend\\Source\\AST\\ASTArguments'
            )
        );
    }

    /**
     * testPreDecrementExpressionOnStaticVariableMember
     *
     * @return void
     */
    public function testPreDecrementExpressionOnStaticVariableMember()
    {
        $expr = $this->_getFirstPreDecrementExpressionInFunction(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTPropertyPostfix',
                'PDepend\\Source\\AST\\ASTVariable'
            )
        );
    }

    /**
     * testPreDecrementExpressionHasExpectedStartLine
     *
     * @return void
     */
    public function testPreDecrementExpressionHasExpectedStartLine()
    {
        $expr = $this->_getFirstPreDecrementExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * testPreDecrementExpressionHasExpectedStartColumn
     *
     * @return void
     */
    public function testPreDecrementExpressionHasExpectedStartColumn()
    {
        $expr = $this->_getFirstPreDecrementExpressionInFunction(__METHOD__);
        $this->assertEquals(12, $expr->getStartColumn());
    }

    /**
     * testPreDecrementExpressionHasExpectedEndLine
     *
     * @return void
     */
    public function testPreDecrementExpressionHasExpectedEndLine()
    {
        $expr = $this->_getFirstPreDecrementExpressionInFunction(__METHOD__);
        $this->assertEquals(7, $expr->getEndLine());
    }

    /**
     * testPreDecrementExpressionHasExpectedEndColumn
     *
     * @return void
     */
    public function testPreDecrementExpressionHasExpectedEndColumn()
    {
        $expr = $this->_getFirstPreDecrementExpressionInFunction(__METHOD__);
        $this->assertEquals(21, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     * @return \PDepend\Source\AST\ASTPreDecrementExpression
     */
    private function _getFirstPreDecrementExpressionInClass($testCase)
    {
        return $this->getFirstNodeOfTypeInClass(
            $testCase,
            'PDepend\\Source\\AST\\ASTPreDecrementExpression'
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return \PDepend\Source\AST\ASTPreDecrementExpression
     */
    private function _getFirstPreDecrementExpressionInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, 'PDepend\\Source\\AST\\ASTPreDecrementExpression'
        );
    }
}
