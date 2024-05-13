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

use PDepend\Source\Parser\TokenStreamEndException;
use PDepend\Source\Parser\UnexpectedTokenException;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTSwitchStatement} class.
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTSwitchStatement
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTSwitchStatementTest extends ASTNodeTestCase
{
    /**
     * Tests the generated object graph of a switch statement.
     */
    public function testSwitchStatementGraphWithBooleanExpressions(): void
    {
        $stmt = $this->getFirstSwitchStatementInFunction();
        $children = $stmt->getChildren();

        $this->assertInstanceOf(ASTExpression::class, $children[0]);
    }

    /**
     * Tests the generated object graph of a switch statement.
     */
    public function testSwitchStatementGraphWithLabels(): void
    {
        $stmt = $this->getFirstSwitchStatementInFunction();
        $children = $stmt->getChildren();

        $this->assertInstanceOf(ASTSwitchLabel::class, $children[1]);
        $this->assertInstanceOf(ASTSwitchLabel::class, $children[2]);
    }

    /**
     * testSwitchStatement
     *
     * @return ASTSwitchStatement
     * @since 1.0.2
     */
    public function testSwitchStatement()
    {
        $stmt = $this->getFirstSwitchStatementInFunction();
        $this->assertInstanceOf(ASTSwitchStatement::class, $stmt);

        return $stmt;
    }

    /**
     * Tests the start line value.
     *
     * @param ASTSwitchStatement $stmt
     *
     * @depends testSwitchStatement
     */
    public function testSwitchStatementHasExpectedStartLine($stmt): void
    {
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @param ASTSwitchStatement $stmt
     *
     * @depends testSwitchStatement
     */
    public function testSwitchStatementHasExpectedStartColumn($stmt): void
    {
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @param ASTSwitchStatement $stmt
     *
     * @depends testSwitchStatement
     */
    public function testSwitchStatementHasExpectedEndLine($stmt): void
    {
        $this->assertEquals(8, $stmt->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @param ASTSwitchStatement $stmt
     *
     * @depends testSwitchStatement
     */
    public function testSwitchStatementHasExpectedEndColumn($stmt): void
    {
        $this->assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testParserIgnoresDocCommentInSwitchStatement
     */
    public function testParserIgnoresDocCommentInSwitchStatement(): void
    {
        $this->getFirstSwitchStatementInFunction();
    }

    /**
     * testParserIgnoresCommentInSwitchStatement
     */
    public function testParserIgnoresCommentInSwitchStatement(): void
    {
        $this->getFirstSwitchStatementInFunction();
    }

    /**
     * testInvalidStatementInSwitchStatementResultsInExpectedException
     */
    public function testInvalidStatementInSwitchStatementResultsInExpectedException(): void
    {
        $this->expectException(UnexpectedTokenException::class);

        $this->getFirstSwitchStatementInFunction();
    }

    /**
     * testUnclosedSwitchStatementResultsInExpectedException
     */
    public function testUnclosedSwitchStatementResultsInExpectedException(): void
    {
        $this->expectException(TokenStreamEndException::class);

        $this->getFirstSwitchStatementInFunction();
    }

    /**
     * testSwitchStatementWithAlternativeScope
     *
     * @return ASTSwitchStatement
     * @since 1.0.2
     */
    public function testSwitchStatementWithAlternativeScope()
    {
        $stmt = $this->getFirstSwitchStatementInFunction();
        $this->assertInstanceOf(ASTSwitchStatement::class, $stmt);

        return $stmt;
    }

    /**
     * testSwitchStatementAlternativeScopeHasExpectedStartLine
     *
     * @param ASTSwitchStatement $stmt
     *
     * @depends testSwitchStatementWithAlternativeScope
     */
    public function testSwitchStatementAlternativeScopeHasExpectedStartLine($stmt): void
    {
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testSwitchStatementAlternativeScopeHasExpectedStartColumn
     *
     * @param ASTSwitchStatement $stmt
     *
     * @depends testSwitchStatementWithAlternativeScope
     */
    public function testSwitchStatementAlternativeScopeHasExpectedStartColumn($stmt): void
    {
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testSwitchStatementAlternativeScopeHasExpectedEndLine
     *
     * @param ASTSwitchStatement $stmt
     *
     * @depends testSwitchStatementWithAlternativeScope
     */
    public function testSwitchStatementAlternativeScopeHasExpectedEndLine($stmt): void
    {
        $this->assertEquals(25, $stmt->getEndLine());
    }

    /**
     * testSwitchStatementAlternativeScopeHasExpectedEndColumn
     *
     * @param ASTSwitchStatement $stmt
     *
     * @depends testSwitchStatementWithAlternativeScope
     */
    public function testSwitchStatementAlternativeScopeHasExpectedEndColumn($stmt): void
    {
        $this->assertEquals(14, $stmt->getEndColumn());
    }

    /**
     * testSwitchStatementTerminatedByPhpCloseTag
     */
    public function testSwitchStatementTerminatedByPhpCloseTag(): void
    {
        $stmt = $this->getFirstSwitchStatementInFunction();
        $this->assertEquals(9, $stmt->getEndColumn());
    }

    /**
     * testSwitchStatementWithNestedNonePhpCode
     *
     * @return ASTSwitch
     * @since 2.1.0
     */
    public function testSwitchStatementWithNestedNonePhpCode()
    {
        $switch = $this->getFirstSwitchStatementInFunction();
        $this->assertInstanceOf(ASTSwitchStatement::class, $switch);

        return $switch;
    }

    /**
     * testSwitchStatementWithNestedNonePhpCodeStartLine
     *
     * @since 2.1.0
     *
     * @depends testSwitchStatementWithNestedNonePhpCode
     */
    public function testSwitchStatementWithNestedNonePhpCodeStartLine(ASTSwitchStatement $switch): void
    {
        $this->assertSame(5, $switch->getStartLine());
    }

    /**
     * testSwitchStatementWithNestedNonePhpCodeEndLine
     *
     * @since 2.1.0
     *
     * @depends testSwitchStatementWithNestedNonePhpCode
     */
    public function testSwitchStatementWithNestedNonePhpCodeEndLine(ASTSwitchStatement $switch): void
    {
        $this->assertSame(16, $switch->getEndLine());
    }

    /**
     * testSwitchStatementWithNestedNonePhpCodeStartColumn
     *
     * @since 2.1.0
     *
     * @depends testSwitchStatementWithNestedNonePhpCode
     */
    public function testSwitchStatementWithNestedNonePhpCodeStartColumn(ASTSwitchStatement $switch): void
    {
        $this->assertSame(7, $switch->getStartColumn());
    }

    /**
     * testSwitchStatementWithNestedNonePhpCodeEndColumn
     *
     * @since 2.1.0
     *
     * @depends testSwitchStatementWithNestedNonePhpCode
     */
    public function testSwitchStatementWithNestedNonePhpCodeEndColumn(ASTSwitchStatement $switch): void
    {
        $this->assertSame(16, $switch->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return ASTSwitchStatement
     */
    private function getFirstSwitchStatementInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            ASTSwitchStatement::class
        );
    }
}
