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
 * @since 0.9.12
 */

namespace PDepend\Source\AST;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTIssetExpression} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 0.9.12
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTIssetExpression
 * @group unittest
 */
class ASTIssetExpressionTest extends ASTNodeTestCase
{
    /**
     * testIssetExpressionGraphWithMultipleVariables
     *
     * @return void
     */
    public function testIssetExpressionGraphWithMultipleVariables(): void
    {
        $expr = $this->getFirstIssetExpressionInFunction(__METHOD__);
        $vars = $expr->findChildrenOfType('PDepend\\Source\\AST\\ASTVariable');
        $this->assertCount(3, $vars);
    }

    /**
     * testIssetExpressionGraphWithStaticProperty
     *
     * @return void
     */
    public function testIssetExpressionGraphWithStaticProperty(): void
    {
        $expr = $this->getFirstIssetExpressionInFunction(__METHOD__);
        $vars = $expr->findChildrenOfType('PDepend\\Source\\AST\\ASTMemberPrimaryPrefix');
        $this->assertCount(1, $vars);
    }

    /**
     * testIssetExpressionGraphWithArrayProperty
     *
     * @return void
     */
    public function testIssetExpressionGraphWithArrayProperty(): void
    {
        $expr = $this->getFirstIssetExpressionInFunction(__METHOD__);
        $vars = $expr->findChildrenOfType('PDepend\\Source\\AST\\ASTMemberPrimaryPrefix');
        $this->assertCount(1, $vars);
    }

    /**
     * testIssetExpressionHasExpectedStartLine
     *
     * @return void
     */
    public function testIssetExpressionHasExpectedStartLine(): void
    {
        $expr = $this->getFirstIssetExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * testIssetExpressionHasExpectedStartColumn
     *
     * @return void
     */
    public function testIssetExpressionHasExpectedStartColumn(): void
    {
        $expr = $this->getFirstIssetExpressionInFunction(__METHOD__);
        $this->assertEquals(9, $expr->getStartColumn());
    }

    /**
     * testIssetExpressionHasExpectedEndLine
     *
     * @return void
     */
    public function testIssetExpressionHasExpectedEndLine(): void
    {
        $expr = $this->getFirstIssetExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getEndLine());
    }

    /**
     * testIssetExpressionHasExpectedEndColumn
     *
     * @return void
     */
    public function testIssetExpressionHasExpectedEndColumn(): void
    {
        $expr = $this->getFirstIssetExpressionInFunction(__METHOD__);
        $this->assertEquals(30, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return \PDepend\Source\AST\ASTIssetExpression
     */
    private function getFirstIssetExpressionInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase,
            'PDepend\\Source\\AST\\ASTIssetExpression'
        );
    }
}
