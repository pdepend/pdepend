<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@pdepend.org>.
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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Issues
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

/**
 * Test case for ticket 002, PHP 5.3 namespace support.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Issues
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 *
 * @covers PHP_Depend_Parser
 * @group pdepend
 * @group pdepend::issues
 * @group pdepend::parser
 * @group unittest
 */
class PHP_Depend_Issues_NamespaceSupportIssue002Test extends PHP_Depend_Issues_AbstractTest
{
    /**
     * Tests that the parser handles a simple use statement as expected.
     *
     * @return void
     */
    public function testParserHandlesSimpleUseDeclaration()
    {
        $package = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getParentClass()
            ->getPackage();

        self::assertEquals('foo', $package->getName());
    }

    /**
     * Tests that the parser handles multiple, comma separated use declarations.
     *
     * @return void
     */
    public function testParserHandlesMultipleUseDeclarations()
    {
        $class = self::parseSource('issues/002-002-use-declaration.php')
            ->current()
            ->getClasses()
            ->current();

        $parentClass = $class->getParentClass();
        self::assertEquals('FooBar', $parentClass->getName());
        self::assertEquals('foo', $parentClass->getPackage()->getName());

        $interface = $class->getInterfaces()->current();
        self::assertEquals('Bar', $interface->getName());
        self::assertEquals('foo', $interface->getPackage()->getName());
    }

    /**
     * Tests that parser handles a use declaration case insensitive.
     *
     * @return void
     */
    public function testParserHandlesUseDeclarationCaseInsensitive()
    {
        $packages = self::parseSource('issues/002-003-use-declaration.php');

        $class = $packages->current()
                          ->getClasses()
                          ->current();

        $parentClass = $class->getParentClass();
        self::assertEquals('Bar', $parentClass->getName());
        self::assertEquals('foo\bar', $parentClass->getPackage()->getName());
    }

    /**
     * Tests that parser throws an expected exception.
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionWhenUseDeclarationContextEndsOnBackslash()
    {
        $this->setExpectedException(
            'PHP_Depend_Parser_UnexpectedTokenException',
            'Unexpected token: as, line: 2, col: 19, file: '
        );

        self::parseSource('issues/002-004-use-declaration.php');
    }

    /**
     * Tests that the parser handles a namespace declaration with namespace
     * identifier and curly brace syntax.
     *
     * @return void
     */
    public function testParserHandlesNamespaceDeclarationWithIdentifierAndCurlyBraceSyntax()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        self::assertEquals('foo', $packages->current()->getName());
    }

    /**
     * testParserDoesNotAddEmptyNamespaceToResultSet
     *
     * @return void
     */
    public function testParserDoesNotAddEmptyNamespaceToResultSet()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        self::assertEquals(0, count($packages));
    }

    /**
     * Tests that the parser handles a namespace declaration with namespace
     * identifier and semicolon syntax.
     *
     * @return void
     */
    public function testParserHandlesNamespaceDeclarationWithIdentifierAndSemicolonSyntax()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        self::assertEquals(__FUNCTION__, $packages->current()->getName());
    }

    /**
     * Tests that the parser handles a namespace declaration without namespace
     * identifier and semicolon syntax.
     *
     * @return void
     */
    public function testParserHandlesNamespaceDeclarationWithoutIdentifierAndCurlyBraceSyntax()
    {
        $packages = self::parseSource('issues/002-007-namespace-declaration.php');

        self::assertEquals('', $packages->current()->getName());
    }

    /**
     * Tests that the parser does not accept an empty namespace identifier for
     * the semicolon syntax.
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForNamespaceDeclarationWithoutIdentifierAndSemicolonSyntax()
    {
        $this->setExpectedException(
            'PHP_Depend_Parser_UnexpectedTokenException',
            'Unexpected token: ;, line: 2, col: 18, file: '

        );

        self::parseSource('issues/002-008-namespace-declaration.php');
    }

    /**
     * Tests that the parser does not accept a leading backslash in a namespace
     * identifier.
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForLeadingBackslashInIdentifier()
    {
        $this->setExpectedException(
            'PHP_Depend_Parser_UnexpectedTokenException',
            'Unexpected token: {, line: 2, col: 13, file: '

        );

        self::parseSource('issues/002-009-namespace-declaration.php');
    }

    /**
     * Tests that an existing namespace declaration has a higher priority than
     * a simply package annotation.
     *
     * @return void
     */
    public function testNamespaceHasHigherPriorityThanPackageAnnotationSemicolonSyntax()
    {
        $packages = self::parseSource('issues/002-010-namespace-has-higher-priority.php');

        $class = $packages->current()
                          ->getClasses()
                          ->current();

        self::assertEquals('bar', $class->getPackage()->getName());
    }

    /**
     * Tests that an existing namespace declaration has a higher priority than
     * a simply package annotation.
     *
     * @return void
     */
    public function testNamespaceHasHigherPriorityThanPackageAnnotationCurlyBraceSyntax()
    {
        $packages = self::parseSource('issues/002-011-namespace-has-higher-priority.php');

        $class = $packages->current()
                          ->getClasses()
                          ->current();

        self::assertEquals('bar', $class->getPackage()->getName());
    }

    /**
     * Tests that the parser handles multiple namespaces in a single file correct.
     *
     * @return void
     */
    public function testParserHandlesFileWithMultipleNamespacesCorrectSemicolonSyntax()
    {
        $packages = self::parseSource('issues/002-012-multiple-namespaces.php');

        self::assertEquals(3, $packages->count());
        
        $package = $packages->current();
        $types   = $package->getTypes();
        self::assertEquals('bar', $package->getName());
        self::assertEquals('BarFoo', $types->current()->getName());

        $packages->next();

        $package = $packages->current();
        $types   = $package->getTypes();
        self::assertEquals('foo', $package->getName());
        self::assertEquals('FooBar', $types->current()->getName());

        $packages->next();

        $package = $packages->current();
        $types   = $package->getTypes();
        self::assertEquals('baz', $package->getName());
        self::assertEquals('FooBaz', $types->current()->getName());
    }

    /**
     * Tests that the parser handles multiple namespaces in a single file correct.
     *
     * @return void
     */
    public function testParserHandlesFileWithMultipleNamespacesCorrectCurlyBraceSyntax()
    {
        $packages = self::parseSource('issues/002-013-multiple-namespaces.php');

        self::assertEquals(3, $packages->count());

        $package = $packages->current();
        $types   = $package->getTypes();
        self::assertEquals('bar', $package->getName());
        self::assertEquals('BarFoo', $types->current()->getName());

        $packages->next();

        $package = $packages->current();
        $types   = $package->getTypes();
        self::assertEquals('foo', $package->getName());
        self::assertEquals('FooBar', $types->current()->getName());

        $packages->next();

        $package = $packages->current();
        $types   = $package->getTypes();
        self::assertEquals('baz', $package->getName());
        self::assertEquals('FooBaz', $types->current()->getName());
    }

    /**
     * Tests that the parser adds a function to a declared namespace.
     *
     * @return void
     */
    public function testParserAddsFunctionToDeclaredNamespaceSemicolonSyntax()
    {
        $packages = self::parseSource('issues/002-014-namespace-function.php');
        $function = $packages->current()
                             ->getFunctions()
                             ->current();

        self::assertEquals('foo\bar', $function->getPackage()->getName());
    }

    /**
     * Tests that the parser expands a local name within the signature of a
     * namespace class or interface correct.
     *
     * @param string $fileName      Name of the test file.
     * @param string $namespaceName Name of the expected namespace.
     *
     * @return void
     * @dataProvider dataProviderParserResolvesQualifiedTypeNameInTypeSignature
     */
    public function testParserResolvesQualifiedTypeNameInTypeSignature($fileName, $namespaceName)
    {
        $dependency = self::parseSource($fileName)
            ->current()
            ->getTypes()
            ->current()
            ->getDependencies()
            ->current();
        
        self::assertEquals($namespaceName, $dependency->getPackage()->getName());
    }

    /**
     * Tests that the parser expands a local name within the body of a
     * namespaced function correct.
     *
     * @param string $fileName      Name of the test file.
     * @param string $namespaceName Name of the expected namespace.
     *
     * @return void
     * @dataProvider dataProviderParserResolvesQualifiedTypeNameInFunction
     */
    public function testParserResolvesQualifiedTypeNameInFunction($fileName, $namespaceName)
    {
        $packages = self::parseSource($fileName);
        $function = $packages->current()
                             ->getFunctions()
                             ->current();

        $dependency = $function->getDependencies()
                               ->current();

        self::assertEquals($namespaceName, $dependency->getPackage()->getName());
        $this->assertContains(
            $function->getPackage()->getName(),
            $dependency->getPackage()->getName()
        );
    }

    /**
     * Tests that the parser does not expand a qualified name within the
     * signature of a namespaced class or interface correct.
     *
     * @param string $fileName      Name of the test file.
     * @param string $namespaceName Name of the expected namespace.
     *
     * @return void
     * @dataProvider dataProviderParserKeepsQualifiedTypeNameInTypeSignature
     */
    public function testParserKeepsQualifiedTypeNameInTypeSignature($fileName, $namespaceName)
    {
        $dependency = self::parseSource($fileName)
            ->current()
            ->getTypes()
            ->current()
            ->getDependencies()
            ->current();
        
        self::assertEquals($namespaceName, $dependency->getPackage()->getName());
    }

    /**
     * Tests that the parser does not expand a qualified name within the body of
     * a namespaced function correct.
     *
     * @param string $fileName      Name of the test file.
     * @param string $namespaceName Name of the expected namespace.
     *
     * @return void
     * @dataProvider dataProviderParserKeepsQualifiedTypeNameInFunction
     */
    public function testParserKeepsQualifiedTypeNameInFunction($fileName, $namespaceName)
    {
        $dependency = self::parseSource($fileName)
            ->current()
            ->getFunctions()
            ->current()
            ->getDependencies()
            ->current();

        self::assertEquals($namespaceName, $dependency->getPackage()->getName());
    }

    /**
     * Tests that the parser resolves a type name when the name is prefixed with
     * PHP's namespace keyword.
     *
     * @param string $fileName      Name of the test file.
     * @param string $namespaceName Name of the expected namespace.
     *
     * @return void
     * @dataProvider dataProviderParserResolvesNamespaceKeywordInTypeSignatureSemicolonSyntax
     */
    public function testParserResolvesNamespaceKeywordInTypeSignatureSemicolonSyntax($fileName, $namespaceName)
    {
        $dependency = self::parseSource($fileName)
            ->current()
            ->getTypes()
            ->current()
            ->getDependencies()
            ->current();

        self::assertEquals($namespaceName, $dependency->getPackage()->getName());
    }

    /**
     * Tests that the parser resolves a type name when the name is prefixed with
     * PHP's namespace keyword.
     *
     * @param string $fileName      Name of the test file.
     * @param string $namespaceName Name of the expected namespace.
     *
     * @return void
     * @dataProvider dataProviderParserResolvesNamespaceKeywordInFunctionSemicolonSyntax
     */
    public function testParserResolvesNamespaceKeywordInFunctionSemicolonSyntax($fileName, $namespaceName)
    {
        $dependency = self::parseSource($fileName)
            ->current()
            ->getFunctions()
            ->current()
            ->getDependencies()
            ->current();

        self::assertEquals($namespaceName, $dependency->getPackage()->getName());
    }

    /**
     * Tests that the parser resolves a type name when the name is prefixed with
     * PHP's namespace keyword.
     *
     * @param string $fileName      Name of the test file.
     * @param string $namespaceName Name of the expected namespace.
     *
     * @return void
     * @dataProvider dataProviderParserResolvesNamespaceKeywordInTypeSignatureCurlyBraceSyntax
     */
    public function testParserResolvesNamespaceKeywordInTypeSignatureCurlyBraceSyntax($fileName, $namespaceName)
    {
        $dependency = self::parseSource($fileName)
            ->current()
            ->getTypes()
            ->current()
            ->getDependencies()
            ->current();
        
        self::assertEquals($namespaceName, $dependency->getPackage()->getName());
    }

    /**
     * Tests that the parser resolves a type name when the name is prefixed with
     * PHP's namespace keyword.
     *
     * @param string $fileName      Name of the test file.
     * @param string $namespaceName Name of the expected namespace.
     *
     * @return void
     * @dataProvider dataProviderParserResolvesNamespaceKeywordInFunctionCurlyBraceSyntax
     */
    public function testParserResolvesNamespaceKeywordInFunctionCurlyBraceSyntax($fileName, $namespaceName)
    {
        $dependency = self::parseSource($fileName)
            ->current()
            ->getFunctions()
            ->current()
            ->getDependencies()
            ->current();

        self::assertEquals($namespaceName, $dependency->getPackage()->getName());
    }

    /**
     * Data provider method that returns test data for class name resolving
     * tests.
     *
     * @return array
     */
    public static function dataProviderParserResolvesQualifiedTypeNameInFunction()
    {
        return array(
            array('issues/002-015-resolve-qualified-type-names.php', 'foo\bar'),
            array('issues/002-019-resolve-qualified-type-names.php', 'foo\bar'),
            array('issues/002-023-resolve-qualified-type-names.php', 'foo\baz'),
            array('issues/002-027-resolve-qualified-type-names.php', 'foo\bar'),
            array('issues/002-047-resolve-qualified-type-names.php', 'foo\foo'),
            array('issues/002-051-resolve-qualified-type-names.php', 'baz\baz'),
        );
    }

    /**
     * Data provider method that returns test data for class name resolving
     * tests.
     *
     * @return array
     */
    public static function dataProviderParserResolvesQualifiedTypeNameInTypeSignature()
    {
        return array(
            array('issues/002-031-resolve-qualified-type-names.php', 'baz'),
            array('issues/002-035-resolve-qualified-type-names.php', 'baz'),
            array('issues/002-039-resolve-qualified-type-names.php', 'baz'),
            array('issues/002-043-resolve-qualified-type-names.php', 'foo\bar'),
            array('issues/002-046-resolve-qualified-type-names.php', 'foo\foo'),
        );
    }

    /**
     * Data provider method that returns test data for class name resolving
     * tests.
     *
     * @return array
     */
    public static function dataProviderParserKeepsQualifiedTypeNameInFunction()
    {
        return array(
            array('issues/002-016-resolve-qualified-type-names.php', ''),
            array('issues/002-020-resolve-qualified-type-names.php', ''),
            array('issues/002-024-resolve-qualified-type-names.php', 'baz'),
            array('issues/002-028-resolve-qualified-type-names.php', 'bar'),
            array('issues/002-048-resolve-qualified-type-names.php', 'foo'),
            array('issues/002-052-resolve-qualified-type-names.php', 'bar'),
        );
    }

    /**
     * Data provider method that returns test data for class name resolving
     * tests.
     *
     * @return array
     */
    public static function dataProviderParserKeepsQualifiedTypeNameInTypeSignature()
    {
        return array(
            array('issues/002-032-resolve-qualified-type-names.php', 'foo\bar'),
            array('issues/002-036-resolve-qualified-type-names.php', 'foo\bar'),
            array('issues/002-040-resolve-qualified-type-names.php', 'foo\bar'),
        );
    }

    /**
     * Data provider method that returns test data for class name resolving
     * tests.
     *
     * @return array
     */
    public static function dataProviderParserResolvesNamespaceKeywordInFunctionSemicolonSyntax()
    {
        return array(
            array('issues/002-017-resolve-qualified-type-names.php', 'foo\bar'),
            array('issues/002-021-resolve-qualified-type-names.php', 'foo\bar'),
            array('issues/002-025-resolve-qualified-type-names.php', 'foo\bar\baz'),
            array('issues/002-029-resolve-qualified-type-names.php', 'foo\bar\baz'),
            array('issues/002-049-resolve-qualified-type-names.php', 'bar\bar'),
        );
    }

    /**
     * Data provider method that returns test data for class name resolving
     * tests.
     *
     * @return array
     */
    public static function dataProviderParserResolvesNamespaceKeywordInTypeSignatureSemicolonSyntax()
    {
        return array(
            array('issues/002-033-resolve-qualified-type-names.php', 'baz\foo'),
            array('issues/002-037-resolve-qualified-type-names.php', 'baz\foo'),
            array('issues/002-041-resolve-qualified-type-names.php', 'baz\foo'),
            array('issues/002-044-resolve-qualified-type-names.php', 'foo\foo'),
        );
    }

    /**
     * Data provider method that returns test data for class name resolving
     * tests.
     *
     * @return array
     */
    public static function dataProviderParserResolvesNamespaceKeywordInFunctionCurlyBraceSyntax()
    {
        return array(
            array('issues/002-018-resolve-qualified-type-names.php', ''),
            array('issues/002-022-resolve-qualified-type-names.php', ''),
            array('issues/002-026-resolve-qualified-type-names.php', 'baz'),
            array('issues/002-030-resolve-qualified-type-names.php', 'baz'),
            array('issues/002-050-resolve-qualified-type-names.php', 'baz\baz'),
        );
    }

    /**
     * Data provider method that returns test data for class name resolving
     * tests.
     *
     * @return array
     */
    public static function dataProviderParserResolvesNamespaceKeywordInTypeSignatureCurlyBraceSyntax()
    {
        return array(
            array('issues/002-034-resolve-qualified-type-names.php', 'baz\foo'),
            array('issues/002-038-resolve-qualified-type-names.php', 'baz\foo'),
            array('issues/002-042-resolve-qualified-type-names.php', 'baz\foo'),
            array('issues/002-045-resolve-qualified-type-names.php', 'foo\foo'),
        );
    }
}
