<?php

/**
 * This file is part of PDepend.
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

namespace PDepend\Source\Language\PHP\Features\PHP82;

use PDepend\Source\AST\ASTClassOrInterfaceReference;
use PDepend\Source\AST\ASTIntersectionType;
use PDepend\Source\AST\ASTScalarType;
use PDepend\Source\AST\ASTUnionType;

/**
 * @covers \PDepend\Source\Language\PHP\PHPParserVersion82
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 * @group php8.2
 */
class DisjunctiveNormalFormTypesTest extends PHPParserVersion82TestCase
{
    public function testReturnParenthesesFirst(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $type = $method->getReturnType();

        static::assertInstanceOf(ASTUnionType::class, $type);
        $children = $type->getChildren();

        static::assertCount(2, $children);
        static::assertInstanceOf(ASTIntersectionType::class, $children[0]);
        static::assertSame('A&B', $children[0]->getImage());

        static::assertInstanceOf(ASTClassOrInterfaceReference::class, $children[1]);
        static::assertSame('D', $children[1]->getImage());
    }

    public function testReturnParenthesesLast(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $type = $method->getReturnType();

        static::assertInstanceOf(ASTUnionType::class, $type);
        $children = $type->getChildren();

        static::assertCount(3, $children);
        static::assertInstanceOf(ASTScalarType::class, $children[0]);
        static::assertSame('null', $children[0]->getImage());

        static::assertInstanceOf(ASTUnionType::class, $children[1]);
        static::assertSame('A|B|C', $children[1]->getImage());

        static::assertInstanceOf(ASTIntersectionType::class, $children[2]);
        static::assertSame('D&E&F', $children[2]->getImage());
    }

    public function testReturnNestedParentheses(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $type = $method->getReturnType();

        static::assertInstanceOf(ASTUnionType::class, $type);
        static::assertSame('(A&B&C)|true|(D&((E&F)|G))', $type->getImage());
    }

    public function testParameterParenthesesFirst(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $parameters = $method->getParameters();
        static::assertCount(1, $parameters);
        $parameter = $parameters[0];
        $type = $parameter->getFormalParameter()->getType();

        static::assertInstanceOf(ASTUnionType::class, $type);
        $children = $type->getChildren();

        static::assertCount(3, $children);
        static::assertInstanceOf(ASTIntersectionType::class, $children[0]);
        static::assertSame('A&B', $children[0]->getImage());

        static::assertInstanceOf(ASTClassOrInterfaceReference::class, $children[1]);
        static::assertSame('D', $children[1]->getImage());

        static::assertInstanceOf(ASTClassOrInterfaceReference::class, $children[1]);
        static::assertSame('null', $children[2]->getImage());
    }

    public function testParameterParenthesesLast(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $parameters = $method->getParameters();
        static::assertCount(1, $parameters);
        $parameter = $parameters[0];
        $type = $parameter->getFormalParameter()->getType();

        static::assertInstanceOf(ASTUnionType::class, $type);
        $children = $type->getChildren();

        static::assertCount(3, $children);
        static::assertInstanceOf(ASTScalarType::class, $children[0]);
        static::assertSame('null', $children[0]->getImage());

        static::assertInstanceOf(ASTUnionType::class, $children[1]);
        static::assertSame('A|B|C', $children[1]->getImage());

        static::assertInstanceOf(ASTIntersectionType::class, $children[2]);
        static::assertSame('D&E&F', $children[2]->getImage());
    }

    public function testParameterNestedParentheses(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $parameters = $method->getParameters();
        static::assertCount(1, $parameters);
        $parameter = $parameters[0];
        $type = $parameter->getFormalParameter()->getType();

        static::assertInstanceOf(ASTUnionType::class, $type);
        static::assertSame('(A&B&C)|true|(D&((E&F)|G))', $type->getImage());
    }
}
