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
 * @since 1.0.0
 */

namespace PDepend\Source\AST;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTArray} class.
 *
 * @covers \PDepend\Source\AST\ASTArray
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 1.0.0
 *
 * @group unittest
 */
class ASTArrayTest extends ASTNodeTestCase
{
    /**
     * testArrayGraphForEmptyArrayDefinition
     *
     * Source:
     * <code>
     * array()
     * </code>
     *
     * AST:
     * <code>
     * - ASTArray
     * </code>
     */
    public function testArrayGraphForEmptyArrayDefinition(): void
    {
        $this->assertGraph(
            $this->getFirstArrayInFunction(),
            []
        );
    }

    /**
     * testArrayGraphForEmptyShortArrayDefinition
     *
     * Source:
     * <code>
     * []
     * </code>
     *
     * AST:
     * <code>
     * - ASTArray
     * </code>
     */
    public function testArrayGraphForEmptyShortArrayDefinition(): void
    {
        $this->assertGraph(
            $this->getFirstArrayInFunction(),
            []
        );
    }

    /**
     * Tests the start line value of an array instance.
     */
    public function testArrayHasExpectedStartLine(): void
    {
        $array = $this->getFirstArrayInFunction();
        static::assertEquals(4, $array->getStartLine());
    }

    /**
     * Tests the start column value of an array instance.
     */
    public function testArrayHasExpectedStartColumn(): void
    {
        $array = $this->getFirstArrayInFunction();
        static::assertEquals(12, $array->getStartColumn());
    }

    /**
     * Tests the end line value of an array instance.
     */
    public function testArrayHasExpectedEndLine(): void
    {
        $array = $this->getFirstArrayInFunction();
        static::assertEquals(13, $array->getEndLine());
    }

    /**
     * Tests the end column value of an array instance.
     */
    public function testArrayHasExpectedEndColumn(): void
    {
        $array = $this->getFirstArrayInFunction();
        static::assertEquals(5, $array->getEndColumn());
    }

    /**
     * Returns an array instance for the currently executed test case.
     *
     * @return ASTArray
     */
    private function getFirstArrayInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            ASTArray::class
        );
    }
}
