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
use PDepend\Source\AST\ASTConstantDeclarator;
use PDepend\Source\AST\ASTEnum;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTScalarType;
use PDepend\Source\AST\ASTTrait;
use PDepend\Source\AST\ASTUnionType;
use PDepend\Source\AST\ASTValue;

/**
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @covers \PDepend\Source\Language\PHP\PHPParserVersion83
 * @covers \PDepend\Source\AST\ASTConstantDeclarator
 * @group unittest
 * @group php8.3
 */
class UnionTypedClassConstantsTest extends PHPParserVersion83Test
{
    /**
     * @return void
     */
    public function testInterface()
    {
        /** @var ASTInterface $interface */
        $interface = $this->getFirstInterfaceForTestCase();
        /** @var ASTConstantDeclarator $constant */
        $constantDeclarator = $interface->getChild(0)->getChild(0);
        /** @var ASTUnionType $type */
        $type = $constantDeclarator->getType();
        $this->assertCount(3, $type->getChildren());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTUnionType', $type);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScalarType', $type->getChild(0));
        $this->assertSame('string', $type->getChild(0)->getImage());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScalarType', $type->getChild(1));
        $this->assertSame('int', $type->getChild(1)->getImage());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScalarType', $type->getChild(2));
        $this->assertSame('null', $type->getChild(2)->getImage());

        /** @var ASTValue $value */
        $value = $constantDeclarator->getValue();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTValue', $value);

        /** @var ASTMemberPrimaryPrefix $constant */
        $constant = $interface->getConstant('TEST');
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMemberPrimaryPrefix', $constant);
        $this->assertSame($constant, $value->getValue());

        $children = $constant->getChildren();
        $this->assertCount(2, $children);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTClassOrInterfaceReference', $children[0]);
        $this->assertSame('E', $children[0]->getImage());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTConstantPostfix', $children[1]);
        $this->assertSame('TEST', $children[1]->getImage());
    }

    /**
     * @return void
     */
    public function testEnum()
    {
        /** @var ASTEnum $enum */
        $enum = $this->parseCodeResourceForTest()
            ->current()
            ->getEnums()
            ->current();
        /** @var ASTConstantDeclarator $constant */
        $constantDeclarator = $enum->getChild(0)->getChild(0);
        /** @var ASTUnionType $type */
        $type = $constantDeclarator->getType();
        $this->assertCount(3, $type->getChildren());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTUnionType', $type);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScalarType', $type->getChild(0));
        $this->assertSame('string', $type->getChild(0)->getImage());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScalarType', $type->getChild(1));
        $this->assertSame('int', $type->getChild(1)->getImage());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScalarType', $type->getChild(2));
        $this->assertSame('null', $type->getChild(2)->getImage());

        /** @var ASTValue $value */
        $value = $constantDeclarator->getValue();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTValue', $value);

        /** @var ASTLiteral $constant */
        $constant = $enum->getConstant('TEST');
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTLiteral', $constant);
        $this->assertSame($constant, $value->getValue());
        $this->assertSame('"Test1"', $constant->getImage());
    }

    /**
     * @return void
     */
    public function testTrait()
    {
        /** @var ASTTrait $trait */
        $trait = $this->parseCodeResourceForTest()
            ->current()
            ->getTraits()
            ->current();
        /** @var ASTConstantDeclarator $constant */
        $constantDeclarator = $trait->getChild(0)->getChild(0);
        /** @var ASTUnionType $type */
        /** @var ASTUnionType $type */
        $type = $constantDeclarator->getType();
        $this->assertCount(2, $type->getChildren());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTUnionType', $type);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScalarType', $type->getChild(0));
        $this->assertSame('string', $type->getChild(0)->getImage());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScalarType', $type->getChild(1));
        $this->assertSame('int', $type->getChild(1)->getImage());

        /** @var ASTValue $value */
        $value = $constantDeclarator->getValue();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTValue', $value);

        /** @var ASTMemberPrimaryPrefix $constant */
        $constant = $trait->getConstant('TEST');
        $this->assertSame($constant, $value->getValue());

        $children = $constant->getChildren();
        $this->assertCount(2, $children);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTClassOrInterfaceReference', $children[0]);
        $this->assertSame('E', $children[0]->getImage());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTConstantPostfix', $children[1]);
        $this->assertSame('TEST', $children[1]->getImage());
    }

    /**
     * @return void
     */
    public function testClass()
    {
        $classes = $this->parseCodeResourceForTest()
            ->current()
            ->getClasses();
        /** @var ASTClass $class */
        $class = $classes[0];
        /** @var ASTConstantDeclarator $constant */
        $constantDeclarator = $class->getChild(2)->getChild(0);
        /** @var ASTUnionType $type */
        $type = $constantDeclarator->getType();
        $this->assertCount(2, $type->getChildren());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTUnionType', $type);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScalarType', $type->getChild(0));
        $this->assertSame('string', $type->getChild(0)->getImage());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScalarType', $type->getChild(1));
        $this->assertSame('int', $type->getChild(1)->getImage());

        /** @var ASTValue $value */
        $value = $constantDeclarator->getValue();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTValue', $value);

        /** @var ASTMemberPrimaryPrefix $constant */
        $constant = $class->getConstant('TEST');
        $this->assertSame($constant, $value->getValue());

        $children = $constant->getChildren();
        $this->assertCount(2, $children);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTClassOrInterfaceReference', $children[0]);
        $this->assertSame('E', $children[0]->getImage());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTConstantPostfix', $children[1]);
        $this->assertSame('TEST', $children[1]->getImage());

        /** @var ASTClass $class */
        $class = $classes[1];
        /** @var ASTConstantDeclarator $constant */
        $constantDeclarator = $class->getChild(1)->getChild(0);
        /** @var ASTScalarType $type */
        $type = $constantDeclarator->getType();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScalarType', $type);
        $this->assertSame('string', $type->getImage());
        /** @var ASTValue $value */
        $value = $constantDeclarator->getValue();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTValue', $value);

        /** @var ASTMemberPrimaryPrefix $constant */
        $constant = $class->getConstant('TEST');
        $this->assertSame($constant, $value->getValue());
        $this->assertSame('"Test2"', $constant->getImage());
    }

    /**
     * @return void
     */
    public function testBroken()
    {
        $this->expectException(
            '\\PDepend\\Source\\Parser\\UnexpectedTokenException'
        );
        $this->expectExceptionMessage(
            'Unexpected token: 7, line: 4, col: 11, file: '
        );

        $this->getFirstInterfaceForTestCase();
    }
}
