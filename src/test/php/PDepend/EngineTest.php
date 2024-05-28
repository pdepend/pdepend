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

namespace PDepend;

use InvalidArgumentException;
use PDepend\Source\AST\ASTNamespace;

/**
 * Test case for \PDepend\Engine facade.
 *
 * @covers \PDepend\Engine
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class EngineTest extends AbstractTestCase
{
    /**
     * Tests that the {@link \PDepend\Engine::addDirectory()} method
     * fails with an exception for an invalid directory.
     */
    public function testAddInvalidDirectoryFail(): void
    {
        $dir = __DIR__ . '/foobar';
        $msg = "Invalid directory '{$dir}' added.";

        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage($msg);

        $engine = $this->createEngineFixture();
        $engine->addDirectory($dir);
    }

    /**
     * Tests that the {@link \PDepend\Engine::addDirectory()} method
     * with an existing directory.
     */
    public function testAddDirectory(): void
    {
        $engine = $this->createEngineFixture();
        $engine->addDirectory($this->createCodeResourceUriForTest());
    }

    /**
     * testAnalyzeMethodReturnsAnIterator
     */
    public function testAnalyzeMethodReturnsAnIterator(): void
    {
        $engine = $this->createEngineFixture();
        $engine->addDirectory($this->createCodeResourceUriForTest());
        $engine->addFileFilter(new Input\ExtensionFilter(['php']));

        static::assertInstanceOf('Iterator', $engine->analyze());
    }

    /**
     * Tests the {@link \PDepend\Engine::analyze()} method and the return
     * value.
     */
    public function testAnalyze(): void
    {
        $engine = $this->createEngineFixture();
        $engine->addDirectory($this->createCodeResourceUriForTest());
        $engine->addFileFilter(new Input\ExtensionFilter(['php']));

        $metrics = $engine->analyze();

        $expected = [
            'package1' => true,
            'package2' => true,
            'package3' => true,
        ];

        foreach ($metrics as $metric) {
            unset($expected[$metric->getImage()]);
        }

        static::assertCount(0, $expected);
    }

    /**
     * Tests that {@link \PDepend\Engine::analyze()} throws an exception
     * if no source directory was set.
     */
    public function testAnalyzeThrowsAnExceptionForNoSourceDirectory(): void
    {
        $engine = $this->createEngineFixture();
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('No source directory and file set.');
        $engine->analyze();
    }

    /**
     * testAnalyzeReturnsEmptyIteratorWhenNoPackageExists
     */
    public function testAnalyzeReturnsEmptyIteratorWhenNoPackageExists(): void
    {
        $engine = $this->createEngineFixture();
        $engine->addDirectory($this->createCodeResourceUriForTest());
        $engine->addFileFilter(new Input\ExtensionFilter([__METHOD__]));

        static::assertCount(0, $engine->analyze());
    }

    /**
     * Tests that {@link \PDepend\Engine::analyze()} configures the
     * ignore annotations option correct.
     */
    public function testAnalyzeSetsWithoutAnnotations(): void
    {
        $engine = $this->createEngineFixture();
        $engine->addDirectory($this->createCodeResourceUriForTest());
        $engine->addFileFilter(new Input\ExtensionFilter(['inc']));
        $engine->setWithoutAnnotations();
        $namespaces = $engine->analyze();

        static::assertEquals(2, $namespaces->count());
        static::assertEquals('pdepend.test', $namespaces->current()->getImage());

        $function = $namespaces->current()->getFunctions()->current();

        static::assertNotNull($function);
        static::assertEquals('foo', $function->getImage());
        static::assertEquals(0, $function->getExceptionClasses()->count());
    }

    /**
     * Tests that the {@link \PDepend\Engine::countClasses()} method
     * returns the expected number of classes.
     */
    public function testCountClasses(): void
    {
        $engine = $this->createEngineFixture();
        $engine->addDirectory($this->createCodeResourceUriForTest());
        $engine->addFileFilter(new Input\ExtensionFilter(['php']));
        $engine->analyze();

        static::assertEquals(10, $engine->countClasses());
    }

    /**
     * Tests that the {@link \PDepend\Engine::countClasses()} method fails
     * with an exception if the code was not analyzed before.
     */
    public function testCountClassesWithoutAnalyzeFail(): void
    {
        $this->expectException(
            'RuntimeException'
        );
        $this->expectExceptionMessage(
            'countClasses() doesn\'t work before the source was analyzed.'
        );

        $engine = $this->createEngineFixture();
        $engine->addDirectory($this->createCodeResourceUriForTest());
        $engine->countClasses();
    }

    /**
     * Tests that the {@link \PDepend\Engine::countNamespaces()} method
     * returns the expected number of namespaces.
     */
    public function testCountNamespaces(): void
    {
        $engine = $this->createEngineFixture();
        $engine->addDirectory($this->createCodeResourceUriForTest());
        $engine->analyze();

        static::assertEquals(4, $engine->countNamespaces());
    }

    /**
     * Tests that the {@link \PDepend\Engine::countNamespaces()} method
     * fails with an exception if the code was not analyzed before.
     */
    public function testCountNamespacesWithoutAnalyzeFail(): void
    {
        $this->expectException(
            'RuntimeException'
        );
        $this->expectExceptionMessage(
            'countNamespaces() doesn\'t work before the source was analyzed.'
        );

        $engine = $this->createEngineFixture();
        $engine->addDirectory($this->createCodeResourceUriForTest());
        $engine->countNamespaces();
    }

    /**
     * Tests that the {@link \PDepend\Engine::getNamespace()} method
     * returns the expected {@link \PDepend\Source\AST\ASTNamespace} objects.
     */
    public function testGetNamespace(): void
    {
        $engine = $this->createEngineFixture();
        $engine->addDirectory($this->createCodeResourceUriForTest());
        $engine->analyze();

        $namespaces = [
            'package1',
            'package2',
            'package3',
        ];

        $className = ASTNamespace::class;

        foreach ($namespaces as $namespace) {
            static::assertInstanceOf($className, $engine->getNamespace($namespace));
        }
    }

    /**
     * Tests that the {@link \PDepend\Engine::getNamespace()} method fails
     * with an exception if the code was not analyzed before.
     */
    public function testGetNamespaceWithoutAnalyzeFail(): void
    {
        $this->expectException(
            'RuntimeException'
        );
        $this->expectExceptionMessage(
            'getNamespace() doesn\'t work before the source was analyzed.'
        );

        $engine = $this->createEngineFixture();
        $engine->addDirectory($this->createCodeResourceUriForTest());
        $engine->getNamespace('package1');
    }

    /**
     * Tests that the {@link \PDepend\Engine::getNamespace()} method fails
     * with an exception if you request an invalid package.
     */
    public function testGetNamespacesWithUnknownPackageFail(): void
    {
        $this->expectException(
            'OutOfBoundsException'
        );
        $this->expectExceptionMessage(
            'Unknown namespace "nspace".'
        );

        $engine = $this->createEngineFixture();
        $engine->addDirectory($this->createCodeResourceUriForTest());
        $engine->analyze();
        $engine->getNamespace('nspace');
    }

    /**
     * Tests that the {@link \PDepend\Engine::getNamespaces()} method
     * returns the expected {@link \PDepend\Source\AST\ASTNamespace} objects
     * and reuses the result of {@link \PDepend\Engine::analyze()}.
     */
    public function testGetNamespaces(): void
    {
        $engine = $this->createEngineFixture();
        $engine->addDirectory($this->createCodeResourceUriForTest());

        $namespace1 = $engine->analyze();
        $namespace2 = $engine->getNamespaces();

        static::assertNotNull($namespace1);

        static::assertSame($namespace1, $namespace2);
    }

    /**
     * Tests that the {@link \PDepend\Engine::getNamespaces()} method
     * fails with an exception if the code was not analyzed before.
     */
    public function testGetNamespacesWithoutAnalyzeFail(): void
    {
        $this->expectException(
            'RuntimeException'
        );
        $this->expectExceptionMessage(
            'getNamespaces() doesn\'t work before the source was analyzed.'
        );

        $engine = $this->createEngineFixture();
        $engine->addDirectory($this->createCodeResourceUriForTest());
        $engine->getNamespaces();
    }

    /**
     * Tests the newly added support for single file handling.
     */
    public function testSupportForSingleFileIssue90(): ASTNamespace
    {
        $engine = $this->createEngineFixture();
        $engine->addFile($this->createCodeResourceUriForTest());
        $engine->analyze();

        $namespaces = $engine->getNamespaces();
        static::assertCount(1, $namespaces);

        return $namespaces[0];
    }

    /**
     * @depends testSupportForSingleFileIssue90
     */
    public function testSupportForSingleFileIssue90ExpectedNumberOfClasses(ASTNamespace $namespace): void
    {
        static::assertCount(1, $namespace->getClasses());
    }

    /**
     * @depends testSupportForSingleFileIssue90
     */
    public function testSupportForSingleFileIssue90ExpectedNumberOfInterfaces(ASTNamespace $namespace): void
    {
        static::assertCount(1, $namespace->getInterfaces());
    }

    /**
     * Tests that the addFile() method throws the expected exception when an
     * added file does not exist.
     */
    public function testAddFileMethodThrowsExpectedExceptionForFileThatNotExists(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $filePath = $this->createRunResourceURI('pdepend_');
        unlink($filePath);

        $engine = $this->createEngineFixture();
        $engine->addFile($filePath);
    }
}
