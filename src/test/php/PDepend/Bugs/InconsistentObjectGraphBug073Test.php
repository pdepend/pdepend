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

namespace PDepend\Bugs;

/**
 * Test case for bug 73 that results in an inconsistent object graph and fatal
 * errors.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group regressiontest
 */
class InconsistentObjectGraphBug073Test extends AbstractRegressionTestCase
{
    /**
     * Tests that the parser handles the following code correct:
     *
     * <code>
     * class Foo {}
     * class Bar extends Foo {}
     * interface Foo {}
     * </code>
     */
    public function testParserCreatesExpectedObjectGraphClassDeclaredBeforeInterfaceWithPackage(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $namespace = $namespaces[0];
        static::assertEquals(2, $namespace->getTypes()->count());
        static::assertEquals(2, $namespace->getClasses()->count());

        $namespace = $namespaces[1];
        static::assertEquals(1, $namespace->getTypes()->count());
        static::assertEquals(1, $namespace->getInterfaces()->count());
    }

    /**
     * Tests that the parser handles the following code correct:
     *
     * <code>
     * class Foo {}
     * class Bar extends Foo {}
     * interface Foo {}
     * </code>
     */
    public function testParserCreatesExpectedObjectGraphClassDeclaredBeforeInterfaceWithoutPackage(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $namespace = $namespaces->current();
        static::assertEquals(3, $namespace->getTypes()->count());
        static::assertEquals(2, $namespace->getClasses()->count());
        static::assertEquals(1, $namespace->getInterfaces()->count());
    }

    /**
     * Tests that the parser handles the following code correct:
     *
     * <code>
     * interface Foo {}
     * class Bar implements Foo {}
     * class Foo {}
     * </code>
     */
    public function testParserCreatesExpectedObjectGraphInterfaceDeclaredBeforeClassWithPackage(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $namespace = $namespaces[0];
        static::assertEquals(2, $namespace->getTypes()->count());
        static::assertEquals(1, $namespace->getClasses()->count());
        static::assertEquals(1, $namespace->getInterfaces()->count());

        $namespaces->next();
        $namespace = $namespaces[1];
        static::assertEquals(1, $namespace->getTypes()->count());
        static::assertEquals(1, $namespace->getClasses()->count());
    }

    /**
     * Tests that the parser handles the following code correct:
     *
     * <code>
     * interface Foo {}
     * class Bar implements Foo {}
     * class Foo {}
     * </code>
     */
    public function testParserCreatesExpectedObjectGraphInterfaceDeclaredBeforeClassWithoutPackage(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $namespace = $namespaces->current();
        static::assertEquals(3, $namespace->getTypes()->count());
        static::assertEquals(2, $namespace->getClasses()->count());
        static::assertEquals(1, $namespace->getInterfaces()->count());
    }

    /**
     * Tests that the parser handles the following code correct:
     *
     * <code>
     * class Foo {}
     * class Bar extends Foo {}
     * class Foo {}
     * </code>
     */
    public function testParserCreatesExpectedObjectGraphClassDeclaredBeforeClassWithPackage(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $namespace = $namespaces->current();
        static::assertEquals(2, $namespace->getTypes()->count());
        static::assertEquals(2, $namespace->getClasses()->count());

        $namespaces->next();
        $namespace = $namespaces->current();
        static::assertEquals(1, $namespace->getTypes()->count());
        static::assertEquals(1, $namespace->getClasses()->count());
    }

    /**
     * Tests that the parser handles the following code correct:
     *
     * <code>
     * interface Foo {}
     * interface Bar extends Foo {}
     * interface Foo {}
     * </code>
     */
    public function testParserCreatesExpectedObjectGraphInterfaceDeclaredBeforeInterfaceWithPackage(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $namespace = $namespaces->current();
        static::assertEquals(2, $namespace->getTypes()->count());
        static::assertEquals(2, $namespace->getInterfaces()->count());

        $namespaces->next();
        $namespace = $namespaces->current();
        static::assertEquals(1, $namespace->getTypes()->count());
        static::assertEquals(1, $namespace->getInterfaces()->count());
    }

    /**
     * Tests that the parser handles the following code correct:
     *
     * <code>
     * namespace {
     *     class Foo {}
     *     class Bar extends Foo {}
     * }
     * namespace baz {
     *     interface Foo {}
     * }
     * </code>
     */
    public function testParserCreatesExpectedObjectGraphClassDeclaredBeforeInterfaceWithNamespace(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $namespace = $namespaces->current();
        static::assertEquals(2, $namespace->getTypes()->count());
        static::assertEquals(2, $namespace->getClasses()->count());

        $namespaces->next();
        $namespace = $namespaces->current();
        static::assertEquals(1, $namespace->getTypes()->count());
        static::assertEquals(1, $namespace->getInterfaces()->count());
    }

    /**
     * Tests that the parser handles the following code correct:
     *
     * <code>
     * namespace {
     *     class Bar implements Foo {}
     *     interface Foo {}
     * }
     * namespace baz {
     *     class Foo {}
     * }
     * </code>
     */
    public function testParserCreatesExpectedObjectGraphInterfaceDeclaredBeforeClassWithNamespace(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $namespace = $namespaces->current();
        static::assertEquals(2, $namespace->getTypes()->count());
        static::assertEquals(1, $namespace->getClasses()->count());
        static::assertEquals(1, $namespace->getInterfaces()->count());

        $namespaces->next();
        $namespace = $namespaces->current();
        static::assertEquals(1, $namespace->getTypes()->count());
        static::assertEquals(1, $namespace->getClasses()->count());
    }

    /**
     * Tests that pdepend does not die with a fatal error.
     */
    public function testPHPDependDoesNotDieWithErrorClassDeclaredBeforeInterfaceWithPackage(): void
    {
        $this->createSummaryXmlForCallingTest();
    }

    /**
     * Tests that pdepend does not die with a fatal error.
     */
    public function testPHPDependDoesNotDieWithErrorClassDeclaredBeforeInterfaceWithoutPackage(): void
    {
        $this->createSummaryXmlForCallingTest();
    }

    /**
     * Tests that pdepend does not die with a fatal error.
     */
    public function testPHPDependDoesNotDieWithErrorInterfaceDeclaredBeforeClassWithPackage(): void
    {
        $this->createSummaryXmlForCallingTest();
    }

    /**
     * Tests that pdepend does not die with a fatal error.
     */
    public function testPHPDependDoesNotDieWithErrorInterfaceDeclaredBeforeClassWithoutPackage(): void
    {
        $this->createSummaryXmlForCallingTest();
    }
}
