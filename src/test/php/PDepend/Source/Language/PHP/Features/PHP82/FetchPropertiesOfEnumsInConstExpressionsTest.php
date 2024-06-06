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

use PDepend\Source\AST\ASTArray;
use PDepend\Source\AST\ASTCompoundExpression;
use PDepend\Source\AST\ASTConstantDeclarator;
use PDepend\Source\AST\ASTConstantDefinition;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\AST\ASTProperty;
use PDepend\Source\AST\ASTPropertyPostfix;

/**
 * @covers \PDepend\Source\Language\PHP\PHPParserVersion82
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 * @group php8.2
 */
class FetchPropertiesOfEnumsInConstExpressionsTest extends PHPParserVersion82TestCase
{
    public function testEnumConst(): void
    {
        $enums = $this->parseCodeResourceForTest()
            ->current()
            ->getEnums();

        static::assertSame(1, $enums->count());

        $enum = $enums->current();
        $constants = $enum->getConstants();

        static::assertCount(1, $constants);
        static::assertInstanceOf(ASTArray::class, $constants['C']);

        $elements = $constants['C']->getChildren();
        static::assertCount(1, $elements);
        $children = $elements[0]->getChildren();
        static::assertCount(2, $children);

        static::assertSame('self::B->value', $this->constructImage($children[0]));
        static::assertSame('self::B', $this->constructImage($children[1]));
    }

    public function testVariousUsages(): void
    {
        $classes = $this->parseCodeResourceForTest()
            ->current()
            ->getClasses();

        $d = $classes[0];
        static::assertSame('D', $d->getImage());

        $f = $classes[1];
        static::assertSame('F', $f->getImage());

        $properties = $f->getProperties();
        static::assertSame(1, $properties->count());

        /** @var ASTProperty $property */
        $property = $properties->current();
        $node = $property->getDefaultValue();
        static::assertInstanceOf(ASTNode::class, $node);
        static::assertSame('E::Foo->name', $this->constructImage($node));

        $g = $classes[2];
        static::assertSame('G', $g->getImage());

        /** @var ASTConstantDefinition[] $constants */
        $constants = $g->getChildren();
        static::assertCount(1, $constants);

        /** @var ASTConstantDeclarator[] $declarators */
        $declarators = $constants[0]->getChildren();
        $declaration = $declarators[0];

        static::assertSame('C', $declaration->getImage());
        $node = $declaration->getValue()?->getValue();
        static::assertInstanceOf(ASTNode::class, $node);
        static::assertSame('E::Foo->{VALUE}', $this->constructImage($node));
    }

    public function constructImage(ASTNode $node): string
    {
        $self = $this;

        return implode($node->getImage(), array_map(function ($child) use ($self) {
            if ($child instanceof ASTCompoundExpression) {
                return '{' . $self->constructImage($child) . '}';
            }

            if ($child instanceof ASTMemberPrimaryPrefix || $child instanceof ASTPropertyPostfix) {
                return $self->constructImage($child);
            }

            return $child->getImage();
        }, $node->getChildren()));
    }
}
