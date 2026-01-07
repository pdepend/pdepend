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

use PDepend\Source\AST\ASTAttribute;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\Language\PHP\AbstractPHPParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
#[CoversClass(AbstractPHPParser::class)]
#[Group('unittest')]
#[Group('php8')]
class AttributeTest extends PHPParserVersion81TestCase
{
    public function testAttribute(): void
    {
        $namespace = $this->parseCodeResourceForTest()->current();

        $classlikes = $namespace->getTypes();

        // Attribute1
        static::assertCount(1, $classlikes[0]->findChildrenOfType(ASTAttribute::class));

        /** @var ASTClass */
        $a = $classlikes[1];

        $aAttributes = $a->findChildrenOfType(ASTAttribute::class);
        static::assertCount(12, $aAttributes);
        // Attribute2
        static::assertSame($a, $aAttributes[0]->getParent());
        // Attribute3
        static::assertSame($a, $aAttributes[1]->getParent());

        // Prop
        static::assertCount(1, $a->getProperties()[0]->getAttributes());

        $methods = $a->getAllMethods();

        // Promo1
        static::assertCount(1, $methods['__construct']->getParameters()[0]->getAttributes());
        // Promo2 & Promo3
        static::assertCount(2, $methods['__construct']->getParameters()[1]->getAttributes());
        // Promo4
        static::assertCount(1, $methods['__construct']->getParameters()[2]->getAttributes());

        $getById = $methods['getbyid'];
        // Route
        static::assertSame($getById, $getById->findChildrenOfType(ASTAttribute::class)[0]->getParent());

        $bar = $methods['bar'];
        // Bar
        static::assertSame($bar, $bar->findChildrenOfType(ASTAttribute::class)[0]->getParent());

        $foobar = $methods['foobar'];
        // Foo, Bar
        static::assertSame($foobar, $foobar->findChildrenOfType(ASTAttribute::class)[0]->getParent());

        $foobar2 = $methods['foobar2'];
        // Foo, Bar,
        static::assertSame($foobar2, $foobar2->findChildrenOfType(ASTAttribute::class)[0]->getParent());

        $bMethod = $methods['b'];
        $parameter = $bMethod->getParameters()[0];
        static::assertSame('$bar', $parameter->getImage());
        // Foo
        static::assertCount(1, $parameter->getAttributes());

        $function = $namespace->getFunctions()->current();
        // FunBar
        static::assertSame($function, $function->findChildrenOfType(ASTAttribute::class)[0]->getParent());

        /** @var ASTClass */
        $b = $classlikes[2];
        $bAttributes = $b->findChildrenOfType(ASTAttribute::class);
        static::assertCount(1, $bAttributes);
        // Foo
        static::assertSame($b, $bAttributes[0]->getParent());
    }
}
