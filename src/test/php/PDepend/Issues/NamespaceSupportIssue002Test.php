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

namespace PDepend\Issues;

use PDepend\Source\Parser\UnexpectedTokenException;

/**
 * Test case for ticket 002, PHP 5.3 namespace support.
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class NamespaceSupportIssue002Test extends AbstractFeatureTestCase
{
    /**
     * Tests that the parser handles a simple use statement as expected.
     */
    public function testParserHandlesSimpleUseDeclaration(): void
    {
        $namespace = $this->getFirstClassForTestCase()
            ->getParentClass()
            ->getNamespace();

        $this->assertEquals('foo', $namespace->getName());
    }

    /**
     * Tests that the parser handles multiple, comma separated use declarations.
     */
    public function testParserHandlesMultipleUseDeclarations(): void
    {
        $class = $this->parseSource('issues/002-002-use-declaration.php')
            ->current()
            ->getClasses()
            ->current();

        $parentClass = $class->getParentClass();
        $this->assertEquals('FooBar', $parentClass->getName());
        $this->assertEquals('foo', $parentClass->getNamespace()->getName());

        $interface = $class->getInterfaces()->current();
        $this->assertEquals('Bar', $interface->getName());
        $this->assertEquals('foo', $interface->getNamespace()->getName());
    }

    /**
     * Tests that parser handles a use declaration case insensitive.
     */
    public function testParserHandlesUseDeclarationCaseInsensitive(): void
    {
        $namespaces = $this->parseSource('issues/002-003-use-declaration.php');

        $class = $namespaces->current()
                          ->getClasses()
                          ->current();

        $parentClass = $class->getParentClass();
        $this->assertEquals('Bar', $parentClass->getName());
        $this->assertEquals('foo\bar', $parentClass->getNamespace()->getName());
    }

    /**
     * Tests that parser throws an expected exception.
     */
    public function testParserThrowsExpectedExceptionWhenUseDeclarationContextEndsOnBackslash(): void
    {
        $this->expectException(
            UnexpectedTokenException::class
        );
        $this->expectExceptionMessage(
            'Unexpected token: as, line: 2, col: 19, file: '
        );

        $this->parseSource('issues/002-004-use-declaration.php');
    }

    /**
     * Tests that the parser handles a namespace declaration with namespace
     * identifier and curly brace syntax.
     */
    public function testParserHandlesNamespaceDeclarationWithIdentifierAndCurlyBraceSyntax(): void
    {
        $namespaces = $this->parseTestCaseSource(__METHOD__);
        $this->assertEquals('foo', $namespaces->current()->getName());
    }

    /**
     * testParserDoesNotAddEmptyNamespaceToResultSet
     */
    public function testParserDoesNotAddEmptyNamespaceToResultSet(): void
    {
        $namespaces = $this->parseTestCaseSource(__METHOD__);
        $this->assertCount(0, $namespaces);
    }

    /**
     * Tests that the parser handles a namespace declaration with namespace
     * identifier and semicolon syntax.
     */
    public function testParserHandlesNamespaceDeclarationWithIdentifierAndSemicolonSyntax(): void
    {
        $namespaces = $this->parseTestCaseSource(__METHOD__);
        $this->assertEquals(__FUNCTION__, $namespaces->current()->getName());
    }

    /**
     * Tests that the parser handles a namespace declaration without namespace
     * identifier and semicolon syntax.
     */
    public function testParserHandlesNamespaceDeclarationWithoutIdentifierAndCurlyBraceSyntax(): void
    {
        $namespaces = $this->parseSource('issues/002-007-namespace-declaration.php');

        $this->assertEquals('', $namespaces->current()->getName());
    }

    /**
     * Tests that the parser does not accept an empty namespace identifier for
     * the semicolon syntax.
     */
    public function testParserThrowsExpectedExceptionForNamespaceDeclarationWithoutIdentifierAndSemicolonSyntax(): void
    {
        $this->expectException(
            UnexpectedTokenException::class
        );
        $this->expectExceptionMessage(
            'Unexpected token: ;, line: 2, col: 18, file: '
        );

        $this->parseSource('issues/002-008-namespace-declaration.php');
    }

    /**
     * Tests that the parser does not accept a leading backslash in a namespace
     * identifier.
     */
    public function testParserThrowsExpectedExceptionForLeadingBackslashInIdentifier(): void
    {
        $this->expectException(
            UnexpectedTokenException::class
        );
        $this->expectExceptionMessage(
            'Unexpected token: {, line: 2, col: 13, file: '
        );

        $this->parseSource('issues/002-009-namespace-declaration.php');
    }

    /**
     * Tests that an existing namespace declaration has a higher priority than
     * a simply package annotation.
     */
    public function testNamespaceHasHigherPriorityThanPackageAnnotationSemicolonSyntax(): void
    {
        $namespaces = $this->parseSource('issues/002-010-namespace-has-higher-priority.php');

        $class = $namespaces->current()
                          ->getClasses()
                          ->current();

        $this->assertEquals('bar', $class->getNamespace()->getName());
    }

    /**
     * Tests that an existing namespace declaration has a higher priority than
     * a simply package annotation.
     */
    public function testNamespaceHasHigherPriorityThanPackageAnnotationCurlyBraceSyntax(): void
    {
        $namespaces = $this->parseSource('issues/002-011-namespace-has-higher-priority.php');

        $class = $namespaces->current()
                          ->getClasses()
                          ->current();

        $this->assertEquals('bar', $class->getNamespace()->getName());
    }

    /**
     * Tests that the parser handles multiple namespaces in a single file correct.
     */
    public function testParserHandlesFileWithMultipleNamespacesCorrectSemicolonSyntax(): void
    {
        $namespaces = $this->parseSource('issues/002-012-multiple-namespaces.php');

        $this->assertEquals(3, $namespaces->count());

        $namespace = $namespaces->current();
        $types = $namespace->getTypes();
        $this->assertEquals('bar', $namespace->getName());
        $this->assertEquals('BarFoo', $types->current()->getName());

        $namespaces->next();

        $namespace = $namespaces->current();
        $types = $namespace->getTypes();
        $this->assertEquals('foo', $namespace->getName());
        $this->assertEquals('FooBar', $types->current()->getName());

        $namespaces->next();

        $namespace = $namespaces->current();
        $types = $namespace->getTypes();
        $this->assertEquals('baz', $namespace->getName());
        $this->assertEquals('FooBaz', $types->current()->getName());
    }

    /**
     * Tests that the parser handles multiple namespaces in a single file correct.
     */
    public function testParserHandlesFileWithMultipleNamespacesCorrectCurlyBraceSyntax(): void
    {
        $namespaces = $this->parseSource('issues/002-013-multiple-namespaces.php');

        $this->assertEquals(3, $namespaces->count());

        $namespace = $namespaces->current();
        $types = $namespace->getTypes();
        $this->assertEquals('bar', $namespace->getName());
        $this->assertEquals('BarFoo', $types->current()->getName());

        $namespaces->next();

        $namespace = $namespaces->current();
        $types = $namespace->getTypes();
        $this->assertEquals('foo', $namespace->getName());
        $this->assertEquals('FooBar', $types->current()->getName());

        $namespaces->next();

        $namespace = $namespaces->current();
        $types = $namespace->getTypes();
        $this->assertEquals('baz', $namespace->getName());
        $this->assertEquals('FooBaz', $types->current()->getName());
    }

    /**
     * Tests that the parser adds a function to a declared namespace.
     */
    public function testParserAddsFunctionToDeclaredNamespaceSemicolonSyntax(): void
    {
        $namespaces = $this->parseSource('issues/002-014-namespace-function.php');
        $function = $namespaces->current()
                             ->getFunctions()
                             ->current();

        $this->assertEquals('foo\bar', $function->getNamespace()->getName());
    }

    /**
     * Tests that the parser expands a local name within the signature of a
     * namespace class or interface correct.
     *
     * @param string $fileName Name of the test file.
     * @param string $namespaceName Name of the expected namespace.
     * @dataProvider dataProviderParserResolvesQualifiedTypeNameInTypeSignature
     */
    public function testParserResolvesQualifiedTypeNameInTypeSignature($fileName, $namespaceName): void
    {
        $dependency = $this->parseSource($fileName)
            ->current()
            ->getTypes()
            ->current()
            ->getDependencies()
            ->current();

        $this->assertEquals($namespaceName, $dependency->getNamespace()->getName());
    }

    /**
     * Tests that the parser expands a local name within the body of a
     * namespaced function correct.
     *
     * @param string $fileName Name of the test file.
     * @param string $namespaceName Name of the expected namespace.
     * @dataProvider dataProviderParserResolvesQualifiedTypeNameInFunction
     */
    public function testParserResolvesQualifiedTypeNameInFunction($fileName, $namespaceName): void
    {
        $namespaces = $this->parseSource($fileName);
        $function = $namespaces->current()
                             ->getFunctions()
                             ->current();

        $dependency = $function->getDependencies()
                               ->current();

        $this->assertEquals($namespaceName, $dependency->getNamespace()->getName());
        $this->assertStringContainsString(
            $function->getNamespace()->getName(),
            $dependency->getNamespace()->getName()
        );
    }

    /**
     * Tests that the parser does not expand a qualified name within the
     * signature of a namespaced class or interface correct.
     *
     * @param string $fileName Name of the test file.
     * @param string $namespaceName Name of the expected namespace.
     * @dataProvider dataProviderParserKeepsQualifiedTypeNameInTypeSignature
     */
    public function testParserKeepsQualifiedTypeNameInTypeSignature($fileName, $namespaceName): void
    {
        $dependency = $this->parseSource($fileName)
            ->current()
            ->getTypes()
            ->current()
            ->getDependencies()
            ->current();

        $this->assertEquals($namespaceName, $dependency->getNamespace()->getName());
    }

    /**
     * Tests that the parser does not expand a qualified name within the body of
     * a namespaced function correct.
     *
     * @param string $fileName Name of the test file.
     * @param string $namespaceName Name of the expected namespace.
     * @dataProvider dataProviderParserKeepsQualifiedTypeNameInFunction
     */
    public function testParserKeepsQualifiedTypeNameInFunction($fileName, $namespaceName): void
    {
        $dependency = $this->parseSource($fileName)
            ->current()
            ->getFunctions()
            ->current()
            ->getDependencies()
            ->current();

        $this->assertEquals($namespaceName, $dependency->getNamespace()->getName());
    }

    /**
     * Tests that the parser resolves a type name when the name is prefixed with
     * PHP's namespace keyword.
     *
     * @param string $fileName Name of the test file.
     * @param string $namespaceName Name of the expected namespace.
     * @dataProvider dataProviderParserResolvesNamespaceKeywordInTypeSignatureSemicolonSyntax
     */
    public function testParserResolvesNamespaceKeywordInTypeSignatureSemicolonSyntax($fileName, $namespaceName): void
    {
        $dependency = $this->parseSource($fileName)
            ->current()
            ->getTypes()
            ->current()
            ->getDependencies()
            ->current();

        $this->assertEquals($namespaceName, $dependency->getNamespace()->getName());
    }

    /**
     * Tests that the parser resolves a type name when the name is prefixed with
     * PHP's namespace keyword.
     *
     * @param string $fileName Name of the test file.
     * @param string $namespaceName Name of the expected namespace.
     * @dataProvider dataProviderParserResolvesNamespaceKeywordInFunctionSemicolonSyntax
     */
    public function testParserResolvesNamespaceKeywordInFunctionSemicolonSyntax($fileName, $namespaceName): void
    {
        $dependency = $this->parseSource($fileName)
            ->current()
            ->getFunctions()
            ->current()
            ->getDependencies()
            ->current();

        $this->assertEquals($namespaceName, $dependency->getNamespace()->getName());
    }

    /**
     * Tests that the parser resolves a type name when the name is prefixed with
     * PHP's namespace keyword.
     *
     * @param string $fileName Name of the test file.
     * @param string $namespaceName Name of the expected namespace.
     * @dataProvider dataProviderParserResolvesNamespaceKeywordInTypeSignatureCurlyBraceSyntax
     */
    public function testParserResolvesNamespaceKeywordInTypeSignatureCurlyBraceSyntax($fileName, $namespaceName): void
    {
        $dependency = $this->parseSource($fileName)
            ->current()
            ->getTypes()
            ->current()
            ->getDependencies()
            ->current();

        $this->assertEquals($namespaceName, $dependency->getNamespace()->getName());
    }

    /**
     * Tests that the parser resolves a type name when the name is prefixed with
     * PHP's namespace keyword.
     *
     * @param string $fileName Name of the test file.
     * @param string $namespaceName Name of the expected namespace.
     * @dataProvider dataProviderParserResolvesNamespaceKeywordInFunctionCurlyBraceSyntax
     */
    public function testParserResolvesNamespaceKeywordInFunctionCurlyBraceSyntax($fileName, $namespaceName): void
    {
        $dependency = $this->parseSource($fileName)
            ->current()
            ->getFunctions()
            ->current()
            ->getDependencies()
            ->current();

        $this->assertEquals($namespaceName, $dependency->getNamespace()->getName());
    }

    /**
     * Data provider method that returns test data for class name resolving
     * tests.
     *
     * @return array
     */
    public static function dataProviderParserResolvesQualifiedTypeNameInFunction()
    {
        return [
            ['issues/002-015-resolve-qualified-type-names.php', 'foo\bar'],
            ['issues/002-019-resolve-qualified-type-names.php', 'foo\bar'],
            ['issues/002-023-resolve-qualified-type-names.php', 'foo\baz'],
            ['issues/002-027-resolve-qualified-type-names.php', 'foo\bar'],
            ['issues/002-047-resolve-qualified-type-names.php', 'foo\foo'],
            ['issues/002-051-resolve-qualified-type-names.php', 'baz\baz'],
        ];
    }

    /**
     * Data provider method that returns test data for class name resolving
     * tests.
     *
     * @return array
     */
    public static function dataProviderParserResolvesQualifiedTypeNameInTypeSignature()
    {
        return [
            ['issues/002-031-resolve-qualified-type-names.php', 'baz'],
            ['issues/002-035-resolve-qualified-type-names.php', 'baz'],
            ['issues/002-039-resolve-qualified-type-names.php', 'baz'],
            ['issues/002-043-resolve-qualified-type-names.php', 'foo\bar'],
            ['issues/002-046-resolve-qualified-type-names.php', 'foo\foo'],
        ];
    }

    /**
     * Data provider method that returns test data for class name resolving
     * tests.
     *
     * @return array
     */
    public static function dataProviderParserKeepsQualifiedTypeNameInFunction()
    {
        return [
            ['issues/002-016-resolve-qualified-type-names.php', ''],
            ['issues/002-020-resolve-qualified-type-names.php', ''],
            ['issues/002-024-resolve-qualified-type-names.php', 'baz'],
            ['issues/002-028-resolve-qualified-type-names.php', 'bar'],
            ['issues/002-048-resolve-qualified-type-names.php', 'foo'],
            ['issues/002-052-resolve-qualified-type-names.php', 'bar'],
        ];
    }

    /**
     * Data provider method that returns test data for class name resolving
     * tests.
     *
     * @return array
     */
    public static function dataProviderParserKeepsQualifiedTypeNameInTypeSignature()
    {
        return [
            ['issues/002-032-resolve-qualified-type-names.php', 'foo\bar'],
            ['issues/002-036-resolve-qualified-type-names.php', 'foo\bar'],
            ['issues/002-040-resolve-qualified-type-names.php', 'foo\bar'],
        ];
    }

    /**
     * Data provider method that returns test data for class name resolving
     * tests.
     *
     * @return array
     */
    public static function dataProviderParserResolvesNamespaceKeywordInFunctionSemicolonSyntax()
    {
        return [
            ['issues/002-017-resolve-qualified-type-names.php', 'foo\bar'],
            ['issues/002-021-resolve-qualified-type-names.php', 'foo\bar'],
            ['issues/002-025-resolve-qualified-type-names.php', 'foo\bar\baz'],
            ['issues/002-029-resolve-qualified-type-names.php', 'foo\bar\baz'],
            ['issues/002-049-resolve-qualified-type-names.php', 'bar\bar'],
        ];
    }

    /**
     * Data provider method that returns test data for class name resolving
     * tests.
     *
     * @return array
     */
    public static function dataProviderParserResolvesNamespaceKeywordInTypeSignatureSemicolonSyntax()
    {
        return [
            ['issues/002-033-resolve-qualified-type-names.php', 'baz\foo'],
            ['issues/002-037-resolve-qualified-type-names.php', 'baz\foo'],
            ['issues/002-041-resolve-qualified-type-names.php', 'baz\foo'],
            ['issues/002-044-resolve-qualified-type-names.php', 'foo\foo'],
        ];
    }

    /**
     * Data provider method that returns test data for class name resolving
     * tests.
     *
     * @return array
     */
    public static function dataProviderParserResolvesNamespaceKeywordInFunctionCurlyBraceSyntax()
    {
        return [
            ['issues/002-018-resolve-qualified-type-names.php', ''],
            ['issues/002-022-resolve-qualified-type-names.php', ''],
            ['issues/002-026-resolve-qualified-type-names.php', 'baz'],
            ['issues/002-030-resolve-qualified-type-names.php', 'baz'],
            ['issues/002-050-resolve-qualified-type-names.php', 'baz\baz'],
        ];
    }

    /**
     * Data provider method that returns test data for class name resolving
     * tests.
     *
     * @return array
     */
    public static function dataProviderParserResolvesNamespaceKeywordInTypeSignatureCurlyBraceSyntax()
    {
        return [
            ['issues/002-034-resolve-qualified-type-names.php', 'baz\foo'],
            ['issues/002-038-resolve-qualified-type-names.php', 'baz\foo'],
            ['issues/002-042-resolve-qualified-type-names.php', 'baz\foo'],
            ['issues/002-045-resolve-qualified-type-names.php', 'foo\foo'],
        ];
    }
}
