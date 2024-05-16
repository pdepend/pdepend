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
 * Test case for the {@link \PDepend\Source\AST\ASTIncludeExpression} class.
 *
 * @covers \PDepend\Source\AST\ASTIncludeExpression
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 0.9.12
 *
 * @group unittest
 */
class ASTIncludeExpressionTest extends ASTNodeTestCase
{
    /**
     * testIsOnceReturnsFalseByDefault
     */
    public function testIsOnceReturnsFalseByDefault(): void
    {
        $expr = new ASTIncludeExpression();
        static::assertFalse($expr->isOnce());
    }

    /**
     * testIsOnceReturnsTrueForIncludeOnceExpression
     */
    public function testIsOnceReturnsTrueForIncludeOnceExpression(): void
    {
        $expression = $this->getFirstIncludeExpressionInFunction(__METHOD__);
        static::assertTrue($expression->isOnce());
    }

    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames(): void
    {
        $expr = new ASTIncludeExpression();
        static::assertEquals(
            [
                'once',
                'comment',
                'metadata',
                'nodes',
            ],
            $expr->__sleep()
        );
    }

    /**
     * testIncludeExpressionHasExpectedStartLine
     */
    public function testIncludeExpressionHasExpectedStartLine(): void
    {
        $expression = $this->getFirstIncludeExpressionInFunction(__METHOD__);
        static::assertEquals(4, $expression->getStartLine());
    }

    /**
     * testIncludeExpressionHasExpectedStartColumn
     */
    public function testIncludeExpressionHasExpectedStartColumn(): void
    {
        $expression = $this->getFirstIncludeExpressionInFunction(__METHOD__);
        static::assertEquals(5, $expression->getStartColumn());
    }

    /**
     * testIncludeExpressionHasExpectedEndLine
     */
    public function testIncludeExpressionHasExpectedEndLine(): void
    {
        $expression = $this->getFirstIncludeExpressionInFunction(__METHOD__);
        static::assertEquals(4, $expression->getEndLine());
    }

    /**
     * testIncludeExpressionHasExpectedEndColumn
     */
    public function testIncludeExpressionHasExpectedEndColumn(): void
    {
        $expression = $this->getFirstIncludeExpressionInFunction(__METHOD__);
        static::assertEquals(35, $expression->getEndColumn());
    }

    /**
     * testIncludeExpressionWithParenthesisHasExpectedStartLine
     */
    public function testIncludeExpressionWithParenthesisHasExpectedStartLine(): void
    {
        $expression = $this->getFirstIncludeExpressionInFunction(__METHOD__);
        static::assertEquals(4, $expression->getStartLine());
    }

    /**
     * testIncludeExpressionWithParenthesisHasExpectedStartColumn
     */
    public function testIncludeExpressionWithParenthesisHasExpectedStartColumn(): void
    {
        $expression = $this->getFirstIncludeExpressionInFunction(__METHOD__);
        static::assertEquals(5, $expression->getStartColumn());
    }

    /**
     * testIncludeExpressionWithParenthesisHasExpectedEndLine
     */
    public function testIncludeExpressionWithParenthesisHasExpectedEndLine(): void
    {
        $expression = $this->getFirstIncludeExpressionInFunction(__METHOD__);
        static::assertEquals(6, $expression->getEndLine());
    }

    /**
     * testIncludeExpressionWithParenthesisHasExpectedEndColumn
     */
    public function testIncludeExpressionWithParenthesisHasExpectedEndColumn(): void
    {
        $expression = $this->getFirstIncludeExpressionInFunction(__METHOD__);
        static::assertEquals(5, $expression->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     * @return ASTIncludeExpression
     */
    private function getFirstIncludeExpressionInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase,
            ASTIncludeExpression::class
        );
    }
}
