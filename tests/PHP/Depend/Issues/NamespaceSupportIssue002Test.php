<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for ticket 002, PHP 5.3 namespace support.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Issues
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Issues_NamespaceSupportIssue002Test extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the parser handles a simple use statement as expected.
     *
     * @return void
     */
    public function testParserHandlesSimpleUseDeclaration()
    {
        $packages = self::parseSource('issues/002-001-use-declaration.php');

        $class = $packages->current()
                          ->getClasses()
                          ->current();

        $parentClass = $class->getParentClass();
        $this->assertSame('Bar', $parentClass->getName());
        $this->assertSame('foo', $parentClass->getPackage()->getName());
    }

    /**
     * Tests that the parser handles multiple, comma separated use declarations.
     *
     * @return void
     */
    public function testParserHandlesMultipleUseDeclarations()
    {
        $packages = self::parseSource('issues/002-002-use-declaration.php');

        $class = $packages->current()
                          ->getClasses()
                          ->current();

        $parentClass = $class->getParentClass();
        $this->assertSame('FooBar', $parentClass->getName());
        $this->assertSame('foo', $parentClass->getPackage()->getName());

        $interface = $class->getInterfaces()->current();
        $this->assertSame('Bar', $interface->getName());
        $this->assertSame('foo', $interface->getPackage()->getName());
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
        $this->assertSame('Bar', $parentClass->getName());
        $this->assertSame('\foo\bar', $parentClass->getPackage()->getName());
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
        $packages = self::parseSource('issues/002-005-namespace-declaration.php');

        $this->assertSame('foo', $packages->current()->getName());
    }

    /**
     * Tests that the parser handles a namespace declaration with namespace
     * identifier and semicolon syntax.
     *
     * @return void
     */
    public function testParserHandlesNamespaceDeclarationWithIdentifierAndSemicolonSyntax()
    {
        $packages = self::parseSource('issues/002-006-namespace-declaration.php');

        $this->assertSame('foo', $packages->current()->getName());
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

        $this->assertSame('', $packages->current()->getName());
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

        $this->assertSame('bar', $class->getPackage()->getName());
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

        $this->assertSame('bar', $class->getPackage()->getName());
    }

    /**
     * Tests that the parser handles multiple namespaces in a single file correct.
     *
     * @return void
     */
    public function testParserHandlesFileWithMultipleNamespacesCorrectSemicolonSyntax()
    {
        $packages = self::parseSource('issues/002-012-multiple-namespaces.php');

        $this->assertSame(3, $packages->count());
        
        $package = $packages->current();
        $types   = $package->getTypes();
        $this->assertSame('bar', $package->getName());
        $this->assertSame(1, $types->count());
        $this->assertSame('BarFoo', $types->current()->getName());

        $packages->next();

        $package = $packages->current();
        $types   = $package->getTypes();
        $this->assertSame('baz', $package->getName());
        $this->assertSame(1, $types->count());
        $this->assertSame('FooBaz', $types->current()->getName());

        $packages->next();

        $package = $packages->current();
        $types   = $package->getTypes();
        $this->assertSame('foo', $package->getName());
        $this->assertSame(1, $types->count());
        $this->assertSame('FooBar', $types->current()->getName());
    }

    /**
     * Tests that the parser handles multiple namespaces in a single file correct.
     *
     * @return void
     */
    public function testParserHandlesFileWithMultipleNamespacesCorrectCurlyBraceSyntax()
    {
        $packages = self::parseSource('issues/002-013-multiple-namespaces.php');

        $this->assertSame(3, $packages->count());

        $package = $packages->current();
        $types   = $package->getTypes();
        $this->assertSame('bar', $package->getName());
        $this->assertSame(1, $types->count());
        $this->assertSame('BarFoo', $types->current()->getName());

        $packages->next();

        $package = $packages->current();
        $types   = $package->getTypes();
        $this->assertSame('baz', $package->getName());
        $this->assertSame(1, $types->count());
        $this->assertSame('FooBaz', $types->current()->getName());

        $packages->next();

        $package = $packages->current();
        $types   = $package->getTypes();
        $this->assertSame('foo', $package->getName());
        $this->assertSame(1, $types->count());
        $this->assertSame('FooBar', $types->current()->getName());
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

        $this->assertSame('foo\bar', $function->getPackage()->getName());
    }

    /**
     * Tests that the parser expands a local name within the body of a
     * namespaced function correct.
     *
     * @param string $fileName      Name of the test file.
     * @param string $namespaceName Name of the expected namespace.
     *
     * @return void
     * @dataProvider dataProviderParserResolvesQualifiedTypeName
     */
    public function testParserResolvesQualifiedTypeName($fileName, $namespaceName)
    {
        $packages = self::parseSource($fileName);
        $function = $packages->current()
                             ->getFunctions()
                             ->current();

        $dependency = $function->getDependencies()
                               ->current();

        $this->assertSame($namespaceName, $dependency->getPackage()->getName());
        $this->assertContains($function->getPackage()->getName(),
                              $dependency->getPackage()->getName());
    }

    /**
     * Tests that the parser does not expand a qualified name within the body of
     * a namespaced function correct.
     *
     * @param string $fileName      Name of the test file.
     * @param string $namespaceName Name of the expected namespace.
     *
     * @return void
     * @dataProvider dataProviderParserKeepsQualifiedTypeName
     */
    public function testParserKeepsQualifiedTypeName($fileName, $namespaceName)
    {
        $packages = self::parseSource($fileName);

        // Namespace '' found by expression: new \Foo;
        $class = $packages->current()
                          ->getClasses()
                          ->current();

        // Next package 'foo\bar' declared in file.
        $packages->next();

        // Get namespaced function
        $function = $packages->current()
                             ->getFunctions()
                             ->current();

        $dependency = $function->getDependencies()
                               ->current();

        $this->assertSame($class, $dependency);
        $this->assertSame($namespaceName, $dependency->getPackage()->getName());
    }

    /**
     * Tests that the parser resolves a type name when the name is prefixed with
     * PHP's namespace keyword.
     *
     * @param string $fileName      Name of the test file.
     * @param string $namespaceName Name of the expected namespace.
     *
     * @return void
     * @dataProvider dataProviderParserResolvesNamespaceKeywordSemicolonSyntax
     */
    public function testParserResolvesNamespaceKeywordSemicolonSyntax($fileName, $namespaceName)
    {
        $packages = self::parseSource($fileName);
        $function = $packages->current()
                             ->getFunctions()
                             ->current();

        $dependency = $function->getDependencies()
                               ->current();

        $this->assertSame($namespaceName, $dependency->getPackage()->getName());
    }

    /**
     * Tests that the parser resolves a type name when the name is prefixed with
     * PHP's namespace keyword.
     *
     * @param string $fileName      Name of the test file.
     * @param string $namespaceName Name of the expected namespace.
     *
     * @return void
     * @dataProvider dataProviderParserResolvesNamespaceKeywordCurlyBraceSyntax
     */
    public function testParserResolvesNamespaceKeywordCurlyBraceSyntax($fileName, $namespaceName)
    {
        $packages = self::parseSource($fileName);
        $function = $packages->current()
                             ->getFunctions()
                             ->current();

        $dependency = $function->getDependencies()
                               ->current();

        $this->assertSame($namespaceName, $dependency->getPackage()->getName());
    }

    /**
     * Data provider method that returns test data for class name resolving
     * tests.
     *
     * @return array
     */
    public static function dataProviderParserResolvesQualifiedTypeName()
    {
        return array(
            array('issues/002-015-resolve-qualified-type-names.php', 'foo\bar'),
            array('issues/002-019-resolve-qualified-type-names.php', 'foo\bar'),
            array('issues/002-023-resolve-qualified-type-names.php', 'foo\baz'),
        );
    }

    /**
     * Data provider method that returns test data for class name resolving
     * tests.
     *
     * @return array
     */
    public static function dataProviderParserKeepsQualifiedTypeName()
    {
        return array(
            array('issues/002-016-resolve-qualified-type-names.php', ''),
            array('issues/002-020-resolve-qualified-type-names.php', ''),
            array('issues/002-024-resolve-qualified-type-names.php', '\baz'),
        );
    }

    /**
     * Data provider method that returns test data for class name resolving
     * tests.
     *
     * @return array
     */
    public static function dataProviderParserResolvesNamespaceKeywordSemicolonSyntax()
    {
        return array(
            array('issues/002-017-resolve-qualified-type-names.php', 'foo\bar'),
            array('issues/002-021-resolve-qualified-type-names.php', 'foo\bar'),
            array('issues/002-025-resolve-qualified-type-names.php', 'foo\bar\baz'),
        );
    }

    /**
     * Data provider method that returns test data for class name resolving
     * tests.
     *
     * @return array
     */
    public static function dataProviderParserResolvesNamespaceKeywordCurlyBraceSyntax()
    {
        return array(
            array('issues/002-018-resolve-qualified-type-names.php', ''),
            array('issues/002-022-resolve-qualified-type-names.php', ''),
            array('issues/002-026-resolve-qualified-type-names.php', 'baz'),
        );
    }
}
?>
