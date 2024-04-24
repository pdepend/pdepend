<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2017 Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Source\AST;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTExpression} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTExpression
 * @group unittest
 */
class ASTExpressionTest extends ASTNodeTest
{
    /**
     * testExpressionHasExpectedNumberOfChildNodes
     *
     * @return void
     */
    public function testExpressionHasExpectedNumberOfChildNodes()
    {
        $expr = $this->getFirstExpressionInFunction(__METHOD__);
        $this->assertCount(5, $expr->getChild(0)->getChildren());
    }

    /**
     * Tests the generated object graph of an expression node.
     *
     * @return void
     */
    public function testExpressionGraphWithBooleanExpressions()
    {
        $expr = $this->getFirstExpressionInFunction(__METHOD__);
        $expected   = array(
            'PDepend\\Source\\AST\\ASTExpression',
            'PDepend\\Source\\AST\\ASTVariable',
            'PDepend\\Source\\AST\\ASTBooleanAndExpression',
            'PDepend\\Source\\AST\\ASTVariable',
            'PDepend\\Source\\AST\\ASTBooleanOrExpression',
            'PDepend\\Source\\AST\\ASTVariable',
        );

        $this->assertGraphEquals($expr, $expected);
    }

    /**
     * testExpressionHasExpectedStartLine
     *
     * @return void
     */
    public function testExpressionHasExpectedStartLine()
    {
        $expr = $this->getFirstExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * testExpressionHasExpectedStartColumn
     *
     * @return void
     */
    public function testExpressionHasExpectedStartColumn()
    {
        $expr = $this->getFirstExpressionInFunction(__METHOD__);
        $this->assertEquals(8, $expr->getStartColumn());
    }

    /**
     * testExpressionHasExpectedEndLine
     *
     * @return void
     */
    public function testExpressionHasExpectedEndLine()
    {
        $expr = $this->getFirstExpressionInFunction(__METHOD__);
        $this->assertEquals(6, $expr->getEndLine());
    }

    /**
     * testExpressionHasExpectedEndColumn
     *
     * @return void
     */
    public function testExpressionHasExpectedEndColumn()
    {
        $expr = $this->getFirstExpressionInFunction(__METHOD__);
        $this->assertEquals(14, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return \PDepend\Source\AST\ASTExpression
     */
    private function getFirstExpressionInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase,
            'PDepend\\Source\\AST\\ASTExpression'
        );
    }
}
