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
 * Test case for the {@link \PDepend\Source\AST\ASTFinallyStatement} class.
 *
 * @covers \PDepend\Source\AST\ASTFinallyStatement
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTFinallyStatementTest extends ASTNodeTestCase
{
    /**
     * Tests the start line value.
     */
    public function testFinallyStatementHasExpectedStartLine(): void
    {
        $stmt = $this->getFirstFinallyStatementInFunction(__METHOD__);
        static::assertEquals(8, $stmt->getStartLine());
    }

    /**
     * Tests the start column value.
     */
    public function testFinallyStatementHasExpectedStartColumn(): void
    {
        $stmt = $this->getFirstFinallyStatementInFunction(__METHOD__);
        static::assertEquals(7, $stmt->getStartColumn());
    }

    /**
     * Tests the end line value.
     */
    public function testFinallyStatementHasExpectedEndLine(): void
    {
        $stmt = $this->getFirstFinallyStatementInFunction(__METHOD__);
        static::assertEquals(10, $stmt->getEndLine());
    }

    /**
     * Tests the end column value.
     */
    public function testFinallyStatementHasExpectedEndColumn(): void
    {
        $stmt = $this->getFirstFinallyStatementInFunction(__METHOD__);
        static::assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testOnlyFinallyStatementHasExpectedStartLine
     */
    public function testOnlyFinallyStatementHasExpectedStartLine(): void
    {
        $stmt = $this->getFirstFinallyStatementInFunction(__METHOD__);
        static::assertEquals(6, $stmt->getStartLine());
    }

    /**
     * testOnlyFinallyStatementHasExpectedStartColumn
     */
    public function testOnlyFinallyStatementHasExpectedStartColumn(): void
    {
        $stmt = $this->getFirstFinallyStatementInFunction(__METHOD__);
        static::assertEquals(7, $stmt->getStartColumn());
    }

    /**
     * testOnlyFinallyStatementHasExpectedEndLine
     */
    public function testOnlyFinallyStatementHasExpectedEndLine(): void
    {
        $stmt = $this->getFirstFinallyStatementInFunction(__METHOD__);
        static::assertEquals(8, $stmt->getEndLine());
    }

    /**
     * testOnlyFinallyStatementHasExpectedEndColumn
     */
    public function testOnlyFinallyStatementHasExpectedEndColumn(): void
    {
        $stmt = $this->getFirstFinallyStatementInFunction(__METHOD__);
        static::assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testThirdChildOfFinallyStatementIsScopeStatement
     */
    public function testChildOfFinallyStatementIsScopeStatement(): void
    {
        $stmt = $this->getFirstFinallyStatementInFunction(__METHOD__);
        static::assertInstanceOf(ASTScopeStatement::class, $stmt->getChild(0));
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     * @return ASTFinallyStatement
     */
    private function getFirstFinallyStatementInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase,
            ASTFinallyStatement::class
        );
    }
}
