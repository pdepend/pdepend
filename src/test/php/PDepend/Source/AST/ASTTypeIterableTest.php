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
 * Test case for the {@link \PDepend\Source\AST\ASTTypeIterable} class.
 *
 * @covers \PDepend\Source\AST\ASTTypeIterable
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTTypeIterableTest extends ASTNodeTestCase
{
    /**
     * testIterableType
     *
     * @return ASTTypeIterable
     * @since 2.5.1
     */
    public function testIterableType()
    {
        $type = $this->getFirstArrayTypeInFunction();
        static::assertInstanceOf(ASTTypeIterable::class, $type);

        return $type;
    }

    /**
     * testIterableTypeHasExpectedStartLine
     *
     * @param ASTTypeIterable $type
     *
     * @depends testIterableType
     */
    public function testIterableTypeHasExpectedStartLine($type): void
    {
        static::assertEquals(2, $type->getStartLine());
    }

    /**
     * testIterableTypeHasExpectedStartColumn
     *
     * @param ASTTypeIterable $type
     *
     * @depends testIterableType
     */
    public function testIterableTypeHasExpectedStartColumn($type): void
    {
        static::assertEquals(24, $type->getStartColumn());
    }

    /**
     * testIterableTypeHasExpectedEndLine
     *
     * @param ASTTypeIterable $type
     *
     * @depends testIterableType
     */
    public function testIterableTypeHasExpectedEndLine($type): void
    {
        static::assertEquals(2, $type->getEndLine());
    }

    /**
     * testIterableTypeHasExpectedEndColumn
     *
     * @param ASTTypeIterable $type
     *
     * @depends testIterableType
     */
    public function testIterableTypeHasExpectedEndColumn($type): void
    {
        static::assertEquals(31, $type->getEndColumn());
    }

    /**
     * testIsArrayReturnsTrue
     */
    public function testIsArrayReturnsTrue(): void
    {
        $type = new ASTTypeIterable();
        static::assertTrue($type->isArray());
    }

    /**
     * testIsPrimitiveReturnsFalse
     */
    public function testIsPrimitiveReturnsFalse(): void
    {
        $type = new ASTTypeIterable();
        static::assertFalse($type->isScalar());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return ASTTypeIterable
     */
    private function getFirstArrayTypeInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            ASTTypeIterable::class
        );
    }
}
