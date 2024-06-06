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

namespace PDepend\Source\Language\PHP\Features\PHP81;

use PDepend\Source\AST\AbstractASTClassOrInterface;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTEnum;
use PDepend\Source\AST\ASTEnumCase;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTParameter;

/**
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @group unittest
 * @group php8.1
 */
class EnumTest extends PHPParserVersion81TestCase
{
    public function testEnum(): void
    {
        $types = $this->parseCodeResourceForTest()
            ->current()
            ->getTypes();

        static::assertCount(4, $types);
        static::assertInstanceOf(ASTInterface::class, $types[0]);
        static::assertSame('HasColor', $types[0]->getImage());
        static::assertInstanceOf(ASTEnum::class, $types[1]);
        static::assertSame('Suit', $types[1]->getImage());
        static::assertInstanceOf(ASTClass::class, $types[2]);
        static::assertSame('UseEnum', $types[2]->getImage());
        static::assertInstanceOf(ASTEnum::class, $types[3]);
        static::assertSame('SpecialCases', $types[3]->getImage());

        $methods = $types[1]->getMethods();

        static::assertCount(1, $methods);
        static::assertSame('getColor', $methods[0]->getImage());

        $methods = $types[2]->getMethods();

        static::assertCount(3, $methods);
        static::assertSame('foo', $methods[0]->getImage());
        static::assertSame('getSuiteColor', $methods[1]->getImage());
        static::assertSame('areDiamondsRed', $methods[2]->getImage());

        /** @var ASTParameter[] $parameters */
        $parameters = $methods[1]->getParameters();
        static::assertCount(1, $parameters);

        /** @var ASTEnum $enum */
        $enum = $parameters[0]->getClass();

        static::assertInstanceOf(ASTEnum::class, $enum);
        static::assertSame('Suit', $enum->getImage());
        static::assertSame('string', $enum->getType()?->getImage());
        static::assertTrue($enum->isBacked());
        static::assertTrue($enum->isFinal());
        static::assertFalse($enum->isAnonymous());
        static::assertFalse($enum->isAbstract());
        static::assertCount(0, $enum->getProperties());
        static::assertSame(
            [
                'UnitEnum' => 'UnitEnum',
                'BackedEnum' => 'BackedEnum',
                'HasColor' => 'HasColor',
            ],
            array_map(
                static fn(AbstractASTClassOrInterface $interface) => $interface->getImage(),
                iterator_to_array($enum->getInterfaces())
            )
        );
        static::assertSame(
            [
                'cases' => 'cases',
                'from' => 'from',
                'tryfrom' => 'tryFrom',
                'getcolor' => 'getColor',
            ],
            array_map(
                static fn(ASTMethod $interface) => $interface->getImage(),
                $enum->getAllMethods()
            )
        );
        $cases = $enum->getCases();
        static::assertInstanceOf(ASTArtifactList::class, $cases);
        static::assertSame(
            [
                'HEARTS' => "'hearts'",
                'DIAMONDS' => "'diamonds'",
                'CLUBS' => "'clubs'",
                'SPADES' => "'spades'",
            ],
            array_map(
                static fn(ASTEnumCase $case) => $case->getValue()?->getImage(),
                iterator_to_array($cases)
            )
        );
    }
}
