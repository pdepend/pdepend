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
 * Test case for the {@link \PDepend\Source\AST\ASTWhileStatement} class.
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTWhileStatement
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTWhileStatementTest extends ASTNodeTestCase
{
    /**
     * Tests the generated object graph of a while statement.
     */
    public function testWhileStatementGraphWithBooleanExpressions(): void
    {
        $stmt = $this->getFirstWhileStatementInFunction();
        $this->assertCount(2, $stmt->getChildren());
    }

    /**
     * testFirstChildOfWhileStatementIsASTExpression
     */
    public function testFirstChildOfWhileStatementIsASTExpression(): void
    {
        $stmt = $this->getFirstWhileStatementInFunction();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTExpression', $stmt->getChild(0));
    }

    /**
     * testSecondChildOfWhileStatementIsASTScopeStatement
     */
    public function testSecondChildOfWhileStatementIsASTScopeStatement(): void
    {
        $stmt = $this->getFirstWhileStatementInFunction();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScopeStatement', $stmt->getChild(1));
    }

    /**
     * testWhileStatement
     *
     * @return ASTWhileStatement
     *
     * @since 1.0.2
     */
    public function testWhileStatement()
    {
        $stmt = $this->getFirstWhileStatementInFunction();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTWhileStatement', $stmt);

        return $stmt;
    }

    /**
     * testWhileStatementHasExpectedStartLine
     *
     * @param ASTWhileStatement $stmt
     *
     * @depends testWhileStatement
     */
    public function testWhileStatementHasExpectedStartLine($stmt): void
    {
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testWhileStatementHasExpectedStartColumn
     *
     * @param ASTWhileStatement $stmt
     *
     * @depends testWhileStatement
     */
    public function testWhileStatementHasExpectedStartColumn($stmt): void
    {
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testWhileStatementHasExpectedEndLine
     *
     * @param ASTWhileStatement $stmt
     *
     * @depends testWhileStatement
     */
    public function testWhileStatementHasExpectedEndLine($stmt): void
    {
        $this->assertEquals(6, $stmt->getEndLine());
    }

    /**
     * testWhileStatementHasExpectedEndColumn
     *
     * @param ASTWhileStatement $stmt
     *
     * @depends testWhileStatement
     */
    public function testWhileStatementHasExpectedEndColumn($stmt): void
    {
        $this->assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testWhileStatementWithAlternativeScope
     *
     * @return ASTWhileStatement
     *
     * @since 1.0.2
     */
    public function testWhileStatementWithAlternativeScope()
    {
        $stmt = $this->getFirstWhileStatementInFunction();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTWhileStatement', $stmt);

        return $stmt;
    }

    /**
     * testWhileStatementAlternativeScopeHasExpectedStartLine
     *
     * @param ASTWhileStatement $stmt
     *
     * @depends testWhileStatementWithAlternativeScope
     */
    public function testWhileStatementAlternativeScopeHasExpectedStartLine($stmt): void
    {
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testWhileStatementAlternativeScopeHasExpectedStartColumn
     *
     * @param ASTWhileStatement $stmt
     *
     * @depends testWhileStatementWithAlternativeScope
     */
    public function testWhileStatementAlternativeScopeHasExpectedStartColumn($stmt): void
    {
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testWhileStatementAlternativeScopeHasExpectedEndLine
     *
     * @param ASTWhileStatement $stmt
     *
     * @depends testWhileStatementWithAlternativeScope
     */
    public function testWhileStatementAlternativeScopeHasExpectedEndLine($stmt): void
    {
        $this->assertEquals(8, $stmt->getEndLine());
    }

    /**
     * testWhileStatementAlternativeScopeHasExpectedEndColumn
     *
     * @param ASTWhileStatement $stmt
     *
     * @depends testWhileStatementWithAlternativeScope
     */
    public function testWhileStatementAlternativeScopeHasExpectedEndColumn($stmt): void
    {
        $this->assertEquals(13, $stmt->getEndColumn());
    }

    /**
     * testWhileStatementTerminatedByPhpCloseTag
     */
    public function testWhileStatementTerminatedByPhpCloseTag(): void
    {
        $stmt = $this->getFirstWhileStatementInFunction();
        $this->assertEquals(9, $stmt->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return ASTWhileStatement
     */
    private function getFirstWhileStatementInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTWhileStatement'
        );
    }
}
