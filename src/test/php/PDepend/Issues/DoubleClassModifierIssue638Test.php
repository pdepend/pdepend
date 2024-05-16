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

namespace PDepend\Issues;

use PDepend\Source\AST\State;
use PDepend\Source\Parser\UnexpectedTokenException;
use PDepend\Source\Tokenizer\Token;
use PDepend\Source\Tokenizer\Tokens;

/**
 * Test case for issue #638, php 8.2 readonly allows double class modifiers.
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class DoubleClassModifierIssue638Test extends AbstractFeatureTestCase
{
    /**
     * Tests that a class can have a readonly modifier
     */
    public function testReadonlyClass(): void
    {
        $class = $this->getFirstClassForTestCase();

        $expected = [
            new Token(Tokens::T_READONLY, 'readonly', 2, 2, 1, 8),
            new Token(Tokens::T_CLASS, 'class', 2, 2, 10, 14),
            new Token(Tokens::T_STRING, 'Foo', 2, 2, 16, 18),
            new Token(Tokens::T_CURLY_BRACE_OPEN, '{', 3, 3, 1, 1),
            new Token(Tokens::T_CURLY_BRACE_CLOSE, '}', 4, 4, 1, 1),
        ];

        static::assertEquals($expected, $class->getTokens());
        static::assertSame(0, ~State::IS_READONLY & $class->getModifiers());
        static::assertTrue($class->isReadonly());
    }

    /**
     * Tests that a class can have an abstract modifier
     */
    public function testAbstractClass(): void
    {
        $class = $this->getFirstClassForTestCase();

        $expected = [
            new Token(Tokens::T_ABSTRACT, 'abstract', 2, 2, 1, 8),
            new Token(Tokens::T_CLASS, 'class', 2, 2, 10, 14),
            new Token(Tokens::T_STRING, 'Foo', 2, 2, 16, 18),
            new Token(Tokens::T_CURLY_BRACE_OPEN, '{', 3, 3, 1, 1),
            new Token(Tokens::T_CURLY_BRACE_CLOSE, '}', 4, 4, 1, 1),
        ];

        static::assertEquals($expected, $class->getTokens());
        static::assertSame(0, ~State::IS_EXPLICIT_ABSTRACT & $class->getModifiers());
        static::assertTrue($class->isAbstract());
    }

    /**
     * Tests that a class can have a final modifier
     */
    public function testFinalClass(): void
    {
        $class = $this->getFirstClassForTestCase();

        $expected = [
            new Token(Tokens::T_FINAL, 'final', 2, 2, 1, 5),
            new Token(Tokens::T_CLASS, 'class', 2, 2, 7, 11),
            new Token(Tokens::T_STRING, 'Foo', 2, 2, 13, 15),
            new Token(Tokens::T_CURLY_BRACE_OPEN, '{', 3, 3, 1, 1),
            new Token(Tokens::T_CURLY_BRACE_CLOSE, '}', 4, 4, 1, 1),
        ];

        static::assertEquals($expected, $class->getTokens());
        static::assertSame(0, ~State::IS_FINAL & $class->getModifiers());
        static::assertTrue($class->isFinal());
    }

    /**
     * Tests that a class can have an abstract and readonly modifier
     */
    public function testAbstractReadonlyClass(): void
    {
        $class = $this->getFirstClassForTestCase();

        $expected = [
            new Token(Tokens::T_ABSTRACT, 'abstract', 2, 2, 1, 8),
            new Token(Tokens::T_READONLY, 'readonly', 2, 2, 10, 17),
            new Token(Tokens::T_CLASS, 'class', 2, 2, 19, 23),
            new Token(Tokens::T_STRING, 'Foo', 2, 2, 25, 27),
            new Token(Tokens::T_CURLY_BRACE_OPEN, '{', 3, 3, 1, 1),
            new Token(Tokens::T_CURLY_BRACE_CLOSE, '}', 4, 4, 1, 1),
        ];

        static::assertEquals($expected, $class->getTokens());

        $expectedModifiers = ~State::IS_READONLY & ~State::IS_EXPLICIT_ABSTRACT;
        static::assertSame(0, $expectedModifiers & $class->getModifiers());

        static::assertTrue($class->isReadonly());
        static::assertTrue($class->isAbstract());
    }

    /**
     * Tests that a class can have a readonly and abstract modifier
     */
    public function testReadonlyAbstractClass(): void
    {
        $class = $this->getFirstClassForTestCase();

        $expected = [
            new Token(Tokens::T_READONLY, 'readonly', 2, 2, 1, 8),
            new Token(Tokens::T_ABSTRACT, 'abstract', 2, 2, 10, 17),
            new Token(Tokens::T_CLASS, 'class', 2, 2, 19, 23),
            new Token(Tokens::T_STRING, 'Foo', 2, 2, 25, 27),
            new Token(Tokens::T_CURLY_BRACE_OPEN, '{', 3, 3, 1, 1),
            new Token(Tokens::T_CURLY_BRACE_CLOSE, '}', 4, 4, 1, 1),
        ];

        static::assertEquals($expected, $class->getTokens());

        $expectedModifiers = ~State::IS_READONLY & ~State::IS_EXPLICIT_ABSTRACT;
        static::assertSame(0, $expectedModifiers & $class->getModifiers());

        static::assertTrue($class->isReadonly());
        static::assertTrue($class->isAbstract());
    }

    /**
     * Tests that a class can have a final and readonly modifier
     */
    public function testFinalReadonlyClass(): void
    {
        $class = $this->getFirstClassForTestCase();

        $expected = [
            new Token(Tokens::T_FINAL, 'final', 2, 2, 1, 5),
            new Token(Tokens::T_READONLY, 'readonly', 2, 2, 7, 14),
            new Token(Tokens::T_CLASS, 'class', 2, 2, 16, 20),
            new Token(Tokens::T_STRING, 'Foo', 2, 2, 22, 24),
            new Token(Tokens::T_CURLY_BRACE_OPEN, '{', 3, 3, 1, 1),
            new Token(Tokens::T_CURLY_BRACE_CLOSE, '}', 4, 4, 1, 1),
        ];

        static::assertEquals($expected, $class->getTokens());

        $expectedModifiers = ~State::IS_READONLY & ~State::IS_FINAL;
        static::assertSame(0, $expectedModifiers & $class->getModifiers());

        static::assertTrue($class->isFinal());
        static::assertTrue($class->isReadonly());
    }

    /**
     * Tests that a class can have a readonly and final modifier
     */
    public function testReadonlyFinalClass(): void
    {
        $class = $this->getFirstClassForTestCase();

        $expected = [
            new Token(Tokens::T_READONLY, 'readonly', 2, 2, 1, 8),
            new Token(Tokens::T_FINAL, 'final', 2, 2, 10, 14),
            new Token(Tokens::T_CLASS, 'class', 2, 2, 16, 20),
            new Token(Tokens::T_STRING, 'Foo', 2, 2, 22, 24),
            new Token(Tokens::T_CURLY_BRACE_OPEN, '{', 3, 3, 1, 1),
            new Token(Tokens::T_CURLY_BRACE_CLOSE, '}', 4, 4, 1, 1),
        ];

        static::assertEquals($expected, $class->getTokens());

        $expectedModifiers = ~State::IS_READONLY & ~State::IS_FINAL;
        static::assertSame(0, $expectedModifiers & $class->getModifiers());

        static::assertTrue($class->isFinal());
        static::assertTrue($class->isReadonly());
    }

    /**
     * Tests that a class can have a readonly and final modifier
     */
    public function testAbstractFinalReadonlyClass(): void
    {
        $this->expectException(UnexpectedTokenException::class);

        $this->getFirstClassForTestCase();
    }
}
