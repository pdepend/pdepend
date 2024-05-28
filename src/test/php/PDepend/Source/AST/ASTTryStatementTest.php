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

use PDepend\Source\Parser\UnexpectedTokenException;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTTryStatement} class.
 *
 * @covers \PDepend\Source\AST\ASTTryStatement
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTTryStatementTest extends ASTNodeTestCase
{
    /**
     * testTryStatement
     *
     * @since 1.0.2
     */
    public function testTryStatement(): ASTTryStatement
    {
        $stmt = $this->getFirstTryStatementInFunction();
        static::assertInstanceOf(ASTTryStatement::class, $stmt);

        return $stmt;
    }

    /**
     * Tests that the try-statement has the expected start line value.
     *
     * @depends testTryStatement
     */
    public function testTryStatementHasExpectedStartLine(ASTTryStatement $stmt): void
    {
        static::assertEquals(4, $stmt->getStartLine());
    }

    /**
     * Tests that the try-statement has the expected start column value.
     *
     * @depends testTryStatement
     */
    public function testTryStatementHasExpectedStartColumn(ASTTryStatement $stmt): void
    {
        static::assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * Tests that the try-statement has the expected end line value.
     *
     * @depends testTryStatement
     */
    public function testTryStatementHasExpectedEndLine(ASTTryStatement $stmt): void
    {
        static::assertEquals(8, $stmt->getEndLine());
    }

    /**
     * Tests that the try-statement has the expected end column value.
     *
     * @depends testTryStatement
     */
    public function testTryStatementHasExpectedEndColumn(ASTTryStatement $stmt): void
    {
        static::assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testFirstChildOfTryStatementIsInstanceOfScopeStatement
     *
     * @depends testTryStatement
     */
    public function testFirstChildOfTryStatementIsInstanceOfScopeStatement(ASTTryStatement $stmt): void
    {
        static::assertInstanceOf(ASTScopeStatement::class, $stmt->getChild(0));
    }

    /**
     * testSecondChildOfTryStatementIsInstanceOfCatchStatement
     *
     * @depends testTryStatement
     */
    public function testSecondChildOfTryStatementIsInstanceOfCatchStatement(ASTTryStatement $stmt): void
    {
        static::assertInstanceOf(ASTCatchStatement::class, $stmt->getChild(1));
    }

    /**
     * testTryStatementContainsMultipleChildInstancesOfCatchStatement
     */
    public function testTryStatementContainsMultipleChildInstancesOfCatchStatement(): void
    {
        $actual = [];
        foreach ($this->getFirstTryStatementInFunction()->getChildren() as $child) {
            $actual[] = $child::class;
        }

        $expected = [
            ASTScopeStatement::class,
            ASTCatchStatement::class,
            ASTCatchStatement::class,
            ASTCatchStatement::class,
            ASTCatchStatement::class,
        ];

        static::assertEquals($expected, $actual);
    }

    /**
     * testParserThrowsExceptionWhenNoCatchStatementFollows
     */
    public function testParserThrowsExceptionWhenNoCatchStatementFollows(): void
    {
        $this->expectException(UnexpectedTokenException::class);

        $this->getFirstTryStatementInFunction();
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstTryStatementInFunction(): ASTTryStatement
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTTryStatement::class
        );
    }
}
