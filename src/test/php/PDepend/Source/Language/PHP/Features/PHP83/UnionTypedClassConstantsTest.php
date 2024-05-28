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

namespace PDepend\Source\Language\PHP\Features\PHP83;

use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTClassOrInterfaceReference;
use PDepend\Source\AST\ASTConstantDeclarator;
use PDepend\Source\AST\ASTConstantPostfix;
use PDepend\Source\AST\ASTEnum;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTScalarType;
use PDepend\Source\AST\ASTTrait;
use PDepend\Source\AST\ASTUnionType;
use PDepend\Source\AST\ASTValue;
use PDepend\Source\Parser\UnexpectedTokenException;

/**
 * @covers \PDepend\Source\AST\ASTConstantDeclarator
 * @covers \PDepend\Source\Language\PHP\PHPParserVersion83
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 * @group php8.3
 */
class UnionTypedClassConstantsTest extends PHPParserVersion83TestCase
{
    public function testInterface(): void
    {
        /** @var ASTInterface $interface */
        $interface = $this->getFirstInterfaceForTestCase();

        $constantDeclarator = $interface->getChild(0)->getChild(0);
        static::assertInstanceOf(ASTConstantDeclarator::class, $constantDeclarator);

        /** @var ASTUnionType $type */
        $type = $constantDeclarator->getType();
        static::assertCount(3, $type->getChildren());
        static::assertInstanceOf(ASTUnionType::class, $type);
        static::assertInstanceOf(ASTScalarType::class, $type->getChild(0));
        static::assertSame('string', $type->getChild(0)->getImage());
        static::assertInstanceOf(ASTScalarType::class, $type->getChild(1));
        static::assertSame('int', $type->getChild(1)->getImage());
        static::assertInstanceOf(ASTScalarType::class, $type->getChild(2));
        static::assertSame('null', $type->getChild(2)->getImage());

        /** @var ASTValue $value */
        $value = $constantDeclarator->getValue();
        static::assertInstanceOf(ASTValue::class, $value);

        /** @var ASTMemberPrimaryPrefix $constant */
        $constant = $interface->getConstant('TEST');
        static::assertInstanceOf(ASTMemberPrimaryPrefix::class, $constant);
        static::assertSame($constant, $value->getValue());

        $children = $constant->getChildren();
        static::assertCount(2, $children);
        static::assertInstanceOf(ASTClassOrInterfaceReference::class, $children[0]);
        static::assertSame('E', $children[0]->getImage());
        static::assertInstanceOf(ASTConstantPostfix::class, $children[1]);
        static::assertSame('TEST', $children[1]->getImage());
    }

    public function testEnum(): void
    {
        /** @var ASTEnum $enum */
        $enum = $this->parseCodeResourceForTest()
            ->current()
            ->getEnums()
            ->current();

        $constantDeclarator = $enum->getChild(0)->getChild(0);
        static::assertInstanceOf(ASTConstantDeclarator::class, $constantDeclarator);

        /** @var ASTUnionType $type */
        $type = $constantDeclarator->getType();
        static::assertCount(3, $type->getChildren());
        static::assertInstanceOf(ASTUnionType::class, $type);
        static::assertInstanceOf(ASTScalarType::class, $type->getChild(0));
        static::assertSame('string', $type->getChild(0)->getImage());
        static::assertInstanceOf(ASTScalarType::class, $type->getChild(1));
        static::assertSame('int', $type->getChild(1)->getImage());
        static::assertInstanceOf(ASTScalarType::class, $type->getChild(2));
        static::assertSame('null', $type->getChild(2)->getImage());

        /** @var ASTValue $value */
        $value = $constantDeclarator->getValue();
        static::assertInstanceOf(ASTValue::class, $value);

        /** @var ASTLiteral $constant */
        $constant = $enum->getConstant('TEST');
        static::assertInstanceOf(ASTLiteral::class, $constant);
        static::assertSame($constant, $value->getValue());
        static::assertSame('"Test1"', $constant->getImage());
    }

    public function testTrait(): void
    {
        /** @var ASTTrait $trait */
        $trait = $this->parseCodeResourceForTest()
            ->current()
            ->getTraits()
            ->current();

        $constantDeclarator = $trait->getChild(0)->getChild(0);
        static::assertInstanceOf(ASTConstantDeclarator::class, $constantDeclarator);

        /** @var ASTUnionType $type */
        /** @var ASTUnionType $type */
        $type = $constantDeclarator->getType();
        static::assertCount(2, $type->getChildren());
        static::assertInstanceOf(ASTUnionType::class, $type);
        static::assertInstanceOf(ASTScalarType::class, $type->getChild(0));
        static::assertSame('string', $type->getChild(0)->getImage());
        static::assertInstanceOf(ASTScalarType::class, $type->getChild(1));
        static::assertSame('int', $type->getChild(1)->getImage());

        /** @var ASTValue $value */
        $value = $constantDeclarator->getValue();
        static::assertInstanceOf(ASTValue::class, $value);

        /** @var ASTMemberPrimaryPrefix $constant */
        $constant = $trait->getConstant('TEST');
        static::assertSame($constant, $value->getValue());

        $children = $constant->getChildren();
        static::assertCount(2, $children);
        static::assertInstanceOf(ASTClassOrInterfaceReference::class, $children[0]);
        static::assertSame('E', $children[0]->getImage());
        static::assertInstanceOf(ASTConstantPostfix::class, $children[1]);
        static::assertSame('TEST', $children[1]->getImage());
    }

    public function testClass(): void
    {
        $classes = $this->parseCodeResourceForTest()
            ->current()
            ->getClasses();

        /** @var ASTClass $class */
        $class = $classes[0];

        $constantDeclarator = $class->getChild(2)->getChild(0);
        static::assertInstanceOf(ASTConstantDeclarator::class, $constantDeclarator);

        /** @var ASTUnionType $type */
        $type = $constantDeclarator->getType();
        static::assertCount(2, $type->getChildren());
        static::assertInstanceOf(ASTUnionType::class, $type);
        static::assertInstanceOf(ASTScalarType::class, $type->getChild(0));
        static::assertSame('string', $type->getChild(0)->getImage());
        static::assertInstanceOf(ASTScalarType::class, $type->getChild(1));
        static::assertSame('int', $type->getChild(1)->getImage());

        /** @var ASTValue $value */
        $value = $constantDeclarator->getValue();
        static::assertInstanceOf(ASTValue::class, $value);

        /** @var ASTMemberPrimaryPrefix $constant */
        $constant = $class->getConstant('TEST');
        static::assertSame($constant, $value->getValue());

        $children = $constant->getChildren();
        static::assertCount(2, $children);
        static::assertInstanceOf(ASTClassOrInterfaceReference::class, $children[0]);
        static::assertSame('E', $children[0]->getImage());
        static::assertInstanceOf(ASTConstantPostfix::class, $children[1]);
        static::assertSame('TEST', $children[1]->getImage());

        /** @var ASTClass $class */
        $class = $classes[1];

        $constantDeclarator = $class->getChild(1)->getChild(0);
        static::assertInstanceOf(ASTConstantDeclarator::class, $constantDeclarator);

        /** @var ASTScalarType $type */
        $type = $constantDeclarator->getType();
        static::assertInstanceOf(ASTScalarType::class, $type);
        static::assertSame('string', $type->getImage());

        /** @var ASTValue $value */
        $value = $constantDeclarator->getValue();
        static::assertInstanceOf(ASTValue::class, $value);

        /** @var ASTMemberPrimaryPrefix $constant */
        $constant = $class->getConstant('TEST');
        static::assertSame($constant, $value->getValue());
        static::assertSame('"Test2"', $constant->getImage());
    }

    public function testBroken(): void
    {
        $this->expectException(
            UnexpectedTokenException::class
        );
        $this->expectExceptionMessage(
            'Unexpected token: 7, line: 4, col: 11, file: '
        );

        $this->getFirstInterfaceForTestCase();
    }
}
