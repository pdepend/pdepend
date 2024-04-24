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

use PDepend\Source\AST\ASTEnum;
use PDepend\Source\AST\ASTEnumCase;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTParameter;

/**
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @covers \PDepend\Source\Language\PHP\PHPParserVersion81
 * @group unittest
 * @group php8.1
 */
class EnumTest extends PHPParserVersion81TestCase
{
    /**
     * @return void
     */
    public function testEnum()
    {
        $types = $this->parseCodeResourceForTest()
            ->current()
            ->getTypes();

        $this->assertCount(4, $types);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTInterface', $types[0]);
        $this->assertSame('HasColor', $types[0]->getName());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTEnum', $types[1]);
        $this->assertSame('Suit', $types[1]->getName());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTClass', $types[2]);
        $this->assertSame('UseEnum', $types[2]->getName());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTEnum', $types[3]);
        $this->assertSame('SpecialCases', $types[3]->getName());

        $methods = $types[1]->getMethods();

        $this->assertCount(1, $methods);
        $this->assertSame('getColor', $methods[0]->getName());

        $methods = $types[2]->getMethods();

        $this->assertCount(3, $methods);
        $this->assertSame('foo', $methods[0]->getName());
        $this->assertSame('getSuiteColor', $methods[1]->getName());
        $this->assertSame('areDiamondsRed', $methods[2]->getName());

        /** @var ASTParameter[] $parameters */
        $parameters = $methods[1]->getParameters();
        $this->assertCount(1, $parameters);

        /** @var ASTEnum $enum */
        $enum = $parameters[0]->getClass();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTEnum', $enum);
        $this->assertSame('Suit', $enum->getName());
        $this->assertSame('string', $enum->getType()->getImage());
        $this->assertTrue($enum->isBacked());
        $this->assertTrue($enum->isFinal());
        $this->assertFalse($enum->isAnonymous());
        $this->assertFalse($enum->isAbstract());
        $this->assertCount(0, $enum->getProperties());
        $this->assertSame(
            array(
                'UnitEnum' => 'UnitEnum',
                'BackedEnum' => 'BackedEnum',
                'HasColor' => 'HasColor',
            ),
            array_map(
                function (ASTInterface $interface) {
                    return $interface->getName();
                },
                iterator_to_array($enum->getInterfaces())
            )
        );
        $this->assertSame(
            array(
                'cases' => 'cases',
                'from' => 'from',
                'tryfrom' => 'tryFrom',
                'getcolor' => 'getColor',
            ),
            array_map(
                function (ASTMethod $interface) {
                    return $interface->getName();
                },
                $enum->getAllMethods()
            )
        );
        $cases = $enum->getCases();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTArtifactList', $cases);
        $this->assertSame(
            array(
                'HEARTS' => "'hearts'",
                'DIAMONDS' => "'diamonds'",
                'CLUBS' => "'clubs'",
                'SPADES' => "'spades'",
            ),
            array_map(
                function (ASTEnumCase $case) {
                    return $case->getValue()->getImage();
                },
                iterator_to_array($cases)
            )
        );
    }
}
