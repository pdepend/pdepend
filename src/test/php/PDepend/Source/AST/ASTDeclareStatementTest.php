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
 * @since 0.10.0
 */

namespace PDepend\Source\AST;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTDeclareStatement} class.
 *
 * @covers \PDepend\Source\AST\ASTDeclareStatement
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 0.10.0
 *
 * @group unittest
 */
class ASTDeclareStatementTest extends ASTNodeTestCase
{
    /**
     * testDeclareStatementWithSingleParameter
     */
    public function testDeclareStatementWithSingleParameter(): void
    {
        $stmt = $this->getFirstDeclareStatementInFunction();
        static::assertCount(1, $stmt->getValues());
    }

    /**
     * testDeclareStatementWithMultipleParameter
     */
    public function testDeclareStatementWithMultipleParameter(): void
    {
        $stmt = $this->getFirstDeclareStatementInFunction();
        static::assertCount(2, $stmt->getValues());
    }

    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames(): void
    {
        $stmt = $this->createNodeInstance();
        static::assertEquals(
            [
                'values',
                'comment',
                'metadata',
                'nodes',
            ],
            $stmt->__sleep()
        );
    }

    /**
     * testDeclareStatementHasExpectedStartLine
     */
    public function testDeclareStatementHasExpectedStartLine(): void
    {
        $stmt = $this->getFirstDeclareStatementInFunction();
        static::assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testDeclareStatementHasExpectedStartColumn
     */
    public function testDeclareStatementHasExpectedStartColumn(): void
    {
        $stmt = $this->getFirstDeclareStatementInFunction();
        static::assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testDeclareStatementHasExpectedEndLine
     */
    public function testDeclareStatementHasExpectedEndLine(): void
    {
        $stmt = $this->getFirstDeclareStatementInFunction();
        static::assertEquals(4, $stmt->getEndLine());
    }

    /**
     * testDeclareStatementHasExpectedEndColumn
     */
    public function testDeclareStatementHasExpectedEndColumn(): void
    {
        $stmt = $this->getFirstDeclareStatementInFunction();
        static::assertEquals(22, $stmt->getEndColumn());
    }

    /**
     * testDeclareStatementWithScopeHasExpectedStartLine
     */
    public function testDeclareStatementWithScopeHasExpectedStartLine(): void
    {
        $stmt = $this->getFirstDeclareStatementInFunction();
        static::assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testDeclareStatementWithScopeHasExpectedStartColumn
     */
    public function testDeclareStatementWithScopeHasExpectedStartColumn(): void
    {
        $stmt = $this->getFirstDeclareStatementInFunction();
        static::assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testDeclareStatementWithScopeHasExpectedEndLine
     */
    public function testDeclareStatementWithScopeHasExpectedEndLine(): void
    {
        $stmt = $this->getFirstDeclareStatementInFunction();
        static::assertEquals(10, $stmt->getEndLine());
    }

    /**
     * testDeclareStatementWithScopeHasExpectedEndColumn
     */
    public function testDeclareStatementWithScopeHasExpectedEndColumn(): void
    {
        $stmt = $this->getFirstDeclareStatementInFunction();
        static::assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testDeclareStatementWithAlternativeScopeHasExpectedStartLine
     */
    public function testDeclareStatementWithAlternativeScopeHasExpectedStartLine(): void
    {
        $stmt = $this->getFirstDeclareStatementInFunction();
        static::assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testDeclareStatementWithAlternativeScopeHasExpectedStartColumn
     */
    public function testDeclareStatementWithAlternativeScopeHasExpectedStartColumn(): void
    {
        $stmt = $this->getFirstDeclareStatementInFunction();
        static::assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testDeclareStatementWithAlternativeScopeHasExpectedEndLine
     */
    public function testDeclareStatementWithAlternativeScopeHasExpectedEndLine(): void
    {
        $stmt = $this->getFirstDeclareStatementInFunction();
        static::assertEquals(9, $stmt->getEndLine());
    }

    /**
     * testDeclareStatementWithAlternativeScopeHasExpectedEndColumn
     */
    public function testDeclareStatementWithAlternativeScopeHasExpectedEndColumn(): void
    {
        $stmt = $this->getFirstDeclareStatementInFunction();
        static::assertEquals(15, $stmt->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstDeclareStatementInFunction(): ASTDeclareStatement
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTDeclareStatement::class
        );
    }
}
