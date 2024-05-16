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
 * @since 0.10.5
 */

namespace PDepend\Source\Parser;

use PDepend\Source\AST\ASTClassOrInterfaceReference;

/**
 * Test case for the namespace resolving in the {@link \PDepend\Source\Language\PHP\AbstractPHPParser} class.
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 0.10.5
 *
 * @group unittest
 */
class NamespaceResovingTest extends AbstractParserTestCase
{
    /**
     * testNamespacesAreCorrectlyLookedUp
     */
    public function testNamespacesAreCorrectlyLookedUp(): void
    {
        $method = $this->getFirstClassMethodForTestCase();

        $actual = [];
        foreach ($method->findChildrenOfType(ASTClassOrInterfaceReference::class) as $reference) {
            $actual[] = $reference->getImage();
        }

        static::assertEquals(
            [
                'Foo\Bar\Bar',
                '\Bar\Baz',
                '\Something',
                '\Test',
                'Foo\Bar\Other',
                '\Baz\Foo\Bar',
            ],
            $actual
        );
    }

    /**
     * testNamespacesAreLookedUpCorrectlyInFirstOfMultipleNamespaces
     */
    public function testNamespacesAreLookedUpCorrectlyInFirstOfMultipleNamespaces(): void
    {
        $method = $this->getFirstClassMethodForTestCase();

        $actual = [];
        foreach ($method->findChildrenOfType(ASTClassOrInterfaceReference::class) as $reference) {
            $actual[] = $reference->getImage();
        }

        static::assertEquals(
            [
                'Foo\Bar\Bar',
                '\Bar\Baz',
                '\Something',
                '\Test',
                'Foo\Bar\Other',
                '\Baz\Foo\Bar',
            ],
            $actual
        );
    }

    /**
     * testNamespacesAreLookedUpCorrectlyInSecondOfMultipleNamespaces
     */
    public function testNamespacesAreLookedUpCorrectlyInSecondOfMultipleNamespaces(): void
    {
        $namespaces = $this->parseCodeResourceForTest();
        $namespaces->next();

        $method = $namespaces->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $actual = [];
        foreach ($method->findChildrenOfType(ASTClassOrInterfaceReference::class) as $reference) {
            $actual[] = $reference->getImage();
        }

        static::assertEquals(
            [
                'Bar\Baz\Bar',
                'Bar\Baz\Baz',
                'Bar\Baz\Something',
                'Bar\Baz\T',
                'Bar\Baz\Other',
                'Bar\Baz\Foo\Bar',
                '\Foo\Bar\Abc',
            ],
            $actual
        );
    }

    /**
     * testNamespacesAreLookedUpCorrectlyInThirdOfMultipleNamespaces
     */
    public function testNamespacesAreLookedUpCorrectlyInThirdOfMultipleNamespaces(): void
    {
        $namespaces = $this->parseCodeResourceForTest();
        $namespaces->next();
        $namespaces->next();

        $method = $namespaces->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $actual = [];
        foreach ($method->findChildrenOfType(ASTClassOrInterfaceReference::class) as $reference) {
            $actual[] = $reference->getImage();
        }

        static::assertEquals(
            [
                '\Bar',
                '\Foo\Bar',
                '\Foo\Bar\Xyz',
            ],
            $actual
        );
    }
}
