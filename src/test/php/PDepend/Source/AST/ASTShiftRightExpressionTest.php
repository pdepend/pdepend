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
 * @since     1.0.1
 */

namespace PDepend\Source\AST;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTShiftRightExpression} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since     1.0.1
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTShiftRightExpression
 * @group unittest
 */
class ASTShiftRightExpressionTest extends \PDepend\Source\AST\ASTNodeTest
{
    /**
     * testShiftRightExpressionReturnsExpectedImage
     *
     * @return void
     */
    public function testShiftRightExpressionReturnsExpectedImage()
    {
        $expr = new \PDepend\Source\AST\ASTShiftRightExpression();
        $this->assertEquals('>>', $expr->getImage());
    }

    /**
     * testShiftRightExpression
     *
     * @return \PDepend\Source\AST\ASTShiftRightExpression
     */
    public function testShiftRightExpression()
    {
        $expr = $this->_getFirstShiftRightExpressionInFunction();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTShiftRightExpression', $expr);

        return $expr;
    }

    /**
     * testShiftRightExpressionHasExpectedStartLine
     *
     * @param \PDepend\Source\AST\ASTShiftRightExpression $expr
     *
     * @return void
     * @depends testShiftRightExpression
     */
    public function testShiftRightExpressionHasExpectedStartLine($expr)
    {
        $this->assertEquals(6, $expr->getStartLine());
    }

    /**
     * testShiftRightExpressionHasExpectedStartColumn
     *
     * @param \PDepend\Source\AST\ASTShiftRightExpression $expr
     *
     * @return void
     * @depends testShiftRightExpression
     */
    public function testShiftRightExpressionHasExpectedStartColumn($expr)
    {
        $this->assertEquals(13, $expr->getStartColumn());
    }

    /**
     * testShiftRightExpressionHasExpectedEndLine
     *
     * @param \PDepend\Source\AST\ASTShiftRightExpression $expr
     *
     * @return void
     * @depends testShiftRightExpression
     */
    public function testShiftRightExpressionHasExpectedEndLine($expr)
    {
        $this->assertEquals(6, $expr->getEndLine());
    }

    /**
     * testShiftRightExpressionHasExpectedEndColumn
     *
     * @param \PDepend\Source\AST\ASTShiftRightExpression $expr
     *
     * @return void
     * @depends testShiftRightExpression
     */
    public function testShiftRightExpressionHasExpectedEndColumn($expr)
    {
        $this->assertEquals(14, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return \PDepend\Source\AST\ASTShiftRightExpression
     */
    private function _getFirstShiftRightExpressionInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTShiftRightExpression'
        );
    }
}

