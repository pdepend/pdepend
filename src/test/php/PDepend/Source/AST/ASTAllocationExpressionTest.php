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
 * Test case for the {@link \PDepend\Source\AST\ASTAllocationExpression} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTAllocationExpression
 * @group unittest
 */
class ASTAllocationExpressionTest extends \PDepend\Source\AST\ASTNodeTest
{
    /**
     * Tests the implementation with an allocation expression without arguments.
     *
     * @return void
     */
    public function testAllocationExpressionWithoutArguments()
    {
        $expr = $this->getFirstAllocationExpressionInFunction(__METHOD__);
        $args = $expr->findChildrenOfType('PDepend\\Source\\AST\\ASTArguments');

        $this->assertEquals(0, count($args));
    }

    /**
     * Tests the implementation with an allocation expression with arguments.
     *
     * @return void
     */
    public function testAllocationExpressionWithArguments()
    {
        $expr = $this->getFirstAllocationExpressionInFunction(__METHOD__);
        $args = $expr->findChildrenOfType('PDepend\\Source\\AST\\ASTArguments');

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
        $expr = $this->getFirstAllocationExpressionInFunction(__METHOD__);
        $arg  = $expr->getFirstChildOfType('PDepend\\Source\\AST\\ASTArguments');

        $this->assertEquals($expr, $arg->getParent());
    }

    /**
     * Tests the start line of an allocation expression.
     *
     * @return void
     */
    public function testAllocationExpressionHasExpectedStartLine()
    {
        $expr = $this->getFirstAllocationExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * Tests the start column of an allocation expression.
     *
     * @return void
     */
    public function testAllocationExpressionHasExpectedStartColumn()
    {
        $expr = $this->getFirstAllocationExpressionInFunction(__METHOD__);
        $this->assertEquals(5, $expr->getStartColumn());
    }

    /**
     * Tests the end line of an allocation expression.
     *
     * @return void
     */
    public function testAllocationExpressionHasExpectedEndLine()
    {
        $expr = $this->getFirstAllocationExpressionInFunction(__METHOD__);
        $this->assertEquals(8, $expr->getEndLine());
    }

    /**
     * Tests the end column of an allocation expression.
     *
     * @return void
     */
    public function testAllocationExpressionHasExpectedEndColumn()
    {
        $expr = $this->getFirstAllocationExpressionInFunction(__METHOD__);
        $this->assertEquals(13, $expr->getEndColumn());
    }

    /**
     * Returns a test allocation expression.
     *
     * @param string $testCase The calling test case.
     *
     * @return \PDepend\Source\AST\ASTAllocationExpression
     */
    private function getFirstAllocationExpressionInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase,
            'PDepend\\Source\\AST\\ASTAllocationExpression'
        );
    }
}
