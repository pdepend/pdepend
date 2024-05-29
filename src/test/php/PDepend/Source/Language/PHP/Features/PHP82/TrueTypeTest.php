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

use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\AST\ASTParameter;
use PDepend\Source\AST\ASTScalarType;
use PDepend\Source\AST\ASTVariableDeclarator;

/**
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @group unittest
 * @group php8.2
 */
class TrueTypeTest extends PHPParserVersion82TestCase
{
    public function testTypedProperties(): void
    {
        /** @var ASTClass $class */
        $class = $this->getFirstClassForTestCase();
        $children = $class->getChildren();

        $declarations = array_map(function (ASTNode $child): array {
            static::assertInstanceOf(ASTFieldDeclaration::class, $child);
            $childChildren = $child->getChildren();

            return [
                $child->hasType() ? $child->getType() : null,
                $childChildren[1],
            ];
        }, $children);

        foreach ([['true', '$truthy']] as $index => $expected) {
            [$expectedType, $expectedVariable] = $expected;
            $expectedTypeClass = ASTScalarType::class;
            [$type, $variable] = $declarations[$index];

            static::assertInstanceOf(
                $expectedTypeClass,
                $type,
                "Wrong type for $expectedType $expectedVariable"
            );
            static::assertSame(ltrim($expectedType, '?'), $type->getImage());
            static::assertInstanceOf(
                ASTVariableDeclarator::class,
                $variable,
                "Wrong variable for $expectedType $expectedVariable"
            );
            static::assertSame($expectedVariable, $variable->getImage());
        }
    }

    public function testReturnTypes(): void
    {
        $class = $this->getFirstClassForTestCase();

        /** @var ASTMethod[] $methods */
        $methods = $class->getMethods();

        /** @var ASTScalarType $truthy */
        $truthy = $methods[0]->getReturnType();

        static::assertInstanceOf(ASTScalarType::class, $truthy);
        static::assertSame('true', $truthy->getImage());
        static::assertTrue($truthy->isTrue());
    }

    public function testParameters(): void
    {
        $method = $this->getFirstMethodForTestCase();

        $parameters = $method->getParameters();
        static::assertInstanceOf(ASTParameter::class, $parameters[0]);
        $truthy = $parameters[0];

        static::assertFalse($truthy->allowsNull());

        $truthy = $truthy->getFormalParameter()->getType();

        static::assertInstanceOf(ASTScalarType::class, $truthy);
        static::assertSame('true', $truthy->getImage());
        static::assertSame(3, $truthy->getStartLine());
        static::assertSame(29, $truthy->getStartColumn());
        static::assertSame(3, $truthy->getEndLine());
        static::assertSame(32, $truthy->getEndColumn());
    }
}
