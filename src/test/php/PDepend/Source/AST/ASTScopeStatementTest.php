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
 * Test case for the {@link \PDepend\Source\AST\ASTScopeStatement} class.
 *
 * @covers \PDepend\Source\AST\ASTScopeStatement
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTScopeStatementTest extends ASTNodeTestCase
{
    /**
     * testParserHandlesInlineScopeStatement
     *
     * @since 1.0.0
     */
    public function testParserHandlesInlineScopeStatement(): ASTScopeStatement
    {
        $stmt = $this->getFirstScopeStatementInFunction();
        static::assertCount(1, $stmt->getChildren());

        return $stmt;
    }

    /**
     * testInlineScopeStatementHasExpectedStartLine
     *
     * @since 1.0.0
     *
     * @depends testParserHandlesInlineScopeStatement
     */
    public function testInlineScopeStatementHasExpectedStartLine(ASTScopeStatement $stmt): ASTScopeStatement
    {
        static::assertEquals(4, $stmt->getStartLine());

        return $stmt;
    }

    /**
     * testInlineScopeStatementHasExpectedStartColumn
     *
     * @since 1.0.0
     *
     * @depends testInlineScopeStatementHasExpectedStartLine
     */
    public function testInlineScopeStatementHasExpectedStartColumn(ASTScopeStatement $stmt): ASTScopeStatement
    {
        static::assertEquals(5, $stmt->getStartColumn());

        return $stmt;
    }

    /**
     * testInlineScopeStatementHasExpectedEndLine
     *
     * @since 1.0.0
     *
     * @depends testInlineScopeStatementHasExpectedStartColumn
     */
    public function testInlineScopeStatementHasExpectedEndLine(ASTScopeStatement $stmt): ASTScopeStatement
    {
        static::assertEquals(5, $stmt->getEndLine());

        return $stmt;
    }

    /**
     * testInlineScopeStatementHasExpectedEndColumn
     *
     * @since 1.0.0
     *
     * @depends testInlineScopeStatementHasExpectedEndLine
     */
    public function testInlineScopeStatementHasExpectedEndColumn(ASTScopeStatement $stmt): void
    {
        static::assertEquals(20, $stmt->getEndColumn());
    }

    /**
     * testScopeStatement
     *
     * @since 1.0.2
     */
    public function testScopeStatement(): ASTScopeStatement
    {
        $stmt = $this->getFirstScopeStatementInFunction();
        static::assertInstanceOf(ASTScopeStatement::class, $stmt);

        return $stmt;
    }

    /**
     * Tests that the scope-statement has the expected start line value.
     *
     * @depends testScopeStatement
     */
    public function testScopeStatementHasExpectedStartLine(ASTScopeStatement $stmt): void
    {
        static::assertEquals(4, $stmt->getStartLine());
    }

    /**
     * Tests that the scope-statement has the expected start column value.
     *
     * @depends testScopeStatement
     */
    public function testScopeStatementHasExpectedStartColumn(ASTScopeStatement $stmt): void
    {
        static::assertEquals(34, $stmt->getStartColumn());
    }

    /**
     * Tests that the scope-statement has the expected end line value.
     *
     * @depends testScopeStatement
     */
    public function testScopeStatementHasExpectedEndLine(ASTScopeStatement $stmt): void
    {
        static::assertEquals(6, $stmt->getEndLine());
    }

    /**
     * Tests that the scope-statement has the expected end column value.
     *
     * @depends testScopeStatement
     */
    public function testScopeStatementHasExpectedEndColumn(ASTScopeStatement $stmt): void
    {
        static::assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testScopeStatementWithAlternative
     *
     * @since 1.0.2
     */
    public function testScopeStatementWithAlternative(): ASTScopeStatement
    {
        $stmt = $this->getFirstScopeStatementInFunction();
        static::assertInstanceOf(ASTScopeStatement::class, $stmt);

        return $stmt;
    }

    /**
     * testScopeStatementWithAlternativeHasExpectedStartLine
     *
     * @depends testScopeStatementWithAlternative
     */
    public function testScopeStatementWithAlternativeHasExpectedStartLine(ASTScopeStatement $stmt): void
    {
        static::assertEquals(6, $stmt->getStartLine());
    }

    /**
     * testScopeStatementWithAlternativeHasExpectedStartColumn
     *
     * @depends testScopeStatementWithAlternative
     */
    public function testScopeStatementWithAlternativeHasExpectedStartColumn(ASTScopeStatement $stmt): void
    {
        static::assertEquals(13, $stmt->getStartColumn());
    }

    /**
     * testScopeStatementWithAlternativeHasExpectedEndLine
     *
     * @depends testScopeStatementWithAlternative
     */
    public function testScopeStatementWithAlternativeHasExpectedEndLine(ASTScopeStatement $stmt): void
    {
        static::assertEquals(17, $stmt->getEndLine());
    }

    /**
     * testScopeStatementWithAlternativeHasExpectedEndColumn
     *
     * @depends testScopeStatementWithAlternative
     */
    public function testScopeStatementWithAlternativeHasExpectedEndColumn(ASTScopeStatement $stmt): void
    {
        static::assertEquals(15, $stmt->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstScopeStatementInFunction(): ASTScopeStatement
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTScopeStatement::class
        );
    }
}
