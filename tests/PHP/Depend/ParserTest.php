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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

require_once 'PHP/Depend/Parser.php';
require_once 'PHP/Depend/Builder/Default.php';
require_once 'PHP/Depend/Tokenizer/Internal.php';

/**
 * Test case implementation for the PHP_Depend code parser.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_ParserTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests the main parse method.
     *
     * @return void
     */
    public function testParseMixedCode()
    {
        $expected = array(
            'pkg1'                                         =>  true,
            'pkg2'                                         =>  true,
            'pkg3'                                         =>  true,
            PHP_Depend_BuilderI::DEFAULT_PACKAGE  =>  true
        );

        $tmp = self::parseSource(dirname(__FILE__) . '/_code/mixed_code.php');
        $packages = array();

        $this->assertEquals(4, $tmp->count());

        foreach ($tmp as $package) {
            $this->assertArrayHasKey($package->getName(), $expected);
            unset($expected[$package->getName()]);
            $packages[$package->getName()] = $package;
        }
        $this->assertEquals(0, count($expected));

        $this->assertEquals(1, $packages['pkg1']->getFunctions()->count());
        $this->assertEquals(1, $packages['pkg1']->getTypes()->count());
        $this->assertFalse($packages['pkg1']->getTypes()->current()->isAbstract());

        $this->assertEquals(1, $packages['pkg2']->getTypes()->count());
        $this->assertTrue($packages['pkg2']->getTypes()->current()->isAbstract());

        $this->assertEquals(1, $packages['pkg3']->getTypes()->count());
        $this->assertTrue($packages['pkg3']->getTypes()->current()->isAbstract());
    }

    /**
     * Tests that the parser throws an exception if it reaches the end of the
     * stream but not all class curly braces are closed.
     *
     * @return void
     */
    public function testParserWithUnclosedClassFail()
    {
        $sourceFile = dirname(__FILE__) . '/_code/not_closed_class.txt';
        $this->setExpectedException(
            'RuntimeException',
            "Invalid state, unclosed class body in file '{$sourceFile}'."
        );

        self::parseSource($sourceFile);
    }

    /**
     * Tests that the parser throws an exception if it reaches the end of the
     * stream but not all function curly braces are closed.
     *
     * @return void
     */
    public function testParserWithUnclosedFunctionFail()
    {
        $sourceFile = dirname(__FILE__) . '/_code/not_closed_function.txt';
        $this->setExpectedException(
            'RuntimeException',
            "Invalid state, unclosed function body in '{$sourceFile}'."
        );

        self::parseSource($sourceFile);
    }

    /**
     * Tests that the parser throws an exception if it finds an invalid
     * function signature.
     *
     * @return void
     */
    public function testParserWithInvalidFunction1Fail()
    {
        $this->setExpectedException(
            'RuntimeException',
            'Invalid function signature.'
        );

        self::parseSource(dirname(__FILE__) . '/_code/invalid_function1.txt');
    }

    /**
     * Tests that the parser throws an exception if it finds an invalid
     * function signature.
     *
     * @return void
     */
    public function testParserWithInvalidFunction2Fail()
    {
        $sourceFile = dirname(__FILE__) . '/_code/invalid_function2.txt';

        $this->setExpectedException(
            'RuntimeException',
            "Invalid token \"Bar\" on line 3 in file: {$sourceFile}."
        );

        self::parseSource($sourceFile);
    }

    /**
     * Test case for parser bug 01 that doesn't add dependencies for static
     * method calls.
     *
     * @return void
     */
    public function testParserStaticCallBug01()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/bugs/01.php');
        $packages->next();
        $this->assertEquals(2, $packages->count());

        $package = $packages->current();
        $this->assertEquals('package0', $package->getName());
        $classes = $package->getTypes();
        $this->assertEquals(1, $classes->count());
        $methods = $classes->current()->getMethods();
        $this->assertEquals(1, $methods->count());
        $this->assertEquals(1, $methods->current()->getDependencies()->count());
    }

    /**
     * Tests that the parser handles function with reference return values
     * correct.
     *
     * @return void
     */
    public function testParserReferenceReturnValueBug08()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/bugs/08.php');
        $package  = $packages->current();

        // Get function
        $function = $package->getFunctions()->current();
        $this->assertEquals('barBug08', $function->getName());

        // Get class method
        $method = $package->getTypes()
                          ->current()
                          ->getMethods()
                          ->current();
        $this->assertEquals('fooBug08', $method->getName());
    }

    /**
     * Tests that the parser sets the correct line number for a function.
     *
     * @return void
     */
    public function testParserSetsCorrectFunctionLineNumber()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/mixed_code.php');
        $packages->next();

        $function = $packages->current()->getFunctions()->current();
        $this->assertEquals(7, $function->getStartLine());
    }

    /**
     * Tests that the parser sets the correct tokens for a function.
     *
     * @return void
     */
    public function testParserSetsCorrectFunctionTokens()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN, '{', 7, 7),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_FOREACH, 'foreach', 8, 8),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, '(', 8, 8),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_VARIABLE, '$foo', 8, 8),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_AS, 'as', 8, 8),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_VARIABLE, '$bar', 8, 8),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, ')', 8, 8),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN, '{', 8, 8),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_STRING, 'FooBar', 9, 9),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_DOUBLE_COLON, '::', 9, 9),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_STRING, 'y', 9, 9),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, '(', 9, 9),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_VARIABLE, '$bar', 9, 9),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, ')', 9, 9),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_SEMICOLON, ';', 9, 9),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_CURLY_BRACE_CLOSE, '}', 10, 10),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_CURLY_BRACE_CLOSE, '}', 11, 11),
        );

        $packages = self::parseSource(dirname(__FILE__) . '/_code/mixed_code.php');
        $packages->next();

        $function = $packages->current()->getFunctions()->current();
        $this->assertEquals($tokens, $function->getTokens());
    }

    /**
     * Tests that the parser sets a detected file comment.
     *
     * @return void
     */
    public function testParserSetsCorrectFileComment()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/coupling/class.php');
        $this->assertEquals(4, $packages->count()); // default, +global, +spl & +standard

        $packages->next();
        $packages->next();
        $packages->next();

        $package = $packages->current();
        $this->assertEquals('default\package', $package->getName());

        $class = $package->getClasses()->current();
        $this->assertNotNull($class);

        $actual = $class->getSourceFile()->getDocComment();
        $this->assertNotNull($actual);

        $expected = "/**\n"
                  . " * FANOUT := 12\n"
                  . " * CALLS  := 10\n"
                  . " *\n"
                  . " * @package default\n"
                  . " * @subpackage package\n"
                  . " */";

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the parser doesn't reuse a type comment as file comment.
     *
     * @return void
     */
    public function testParserDoesntReuseTypeComment()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/comments/constant.php');
        $this->assertEquals(1, $packages->count()); // +global

        $package = $packages->current();
        $this->assertEquals('+global', $package->getName());

        $class = $package->getClasses()->current();
        $this->assertNotNull($class);

        $actual = $class->getSourceFile()->getDocComment();
        $this->assertNull($actual);
    }

    /**
     * Tests that the parser doesn't reuse a function comment as file comment.
     *
     * @return void
     */
    public function testParserDoesntReuseFunctionComment()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/comments/function.php');
        $this->assertEquals(1, $packages->count()); // +global

        $package = $packages->current();
        $this->assertEquals('+global', $package->getName());

        $function = $package->getFunctions()->current();
        $this->assertNotNull($function);

        $actual = $function->getSourceFile()->getDocComment();
        $this->assertNull($actual);
    }

    /**
     * Tests that the parser sets the correct start line number for a class.
     *
     * @return void
     */
    public function testParserSetsCorrectClassStartLineNumber()
    {
        $this->assertEquals(30, $this->getMixedCodeClass()->getStartLine());
    }

    /**
     * Tests that the parser sets the correct end line number for a class.
     *
     * @return void
     */
    public function testParserSetsCorrectClassEndLineNumber()
    {
        $this->assertEquals(49, $this->getMixedCodeClass()->getEndLine());
    }

    /**
     * Tests that the parser sets the correct start line number for class methods.
     *
     * @return void
     */
    public function testParserSetsCorrectClassMethodStartLineNumbers()
    {
        $methods = $this->getMixedCodeClassMethods();

        $this->assertEquals(43, $methods->current()->getStartLine());
        $methods->next();
        $this->assertEquals(44, $methods->current()->getStartLine());
    }

    /**
     * Tests that the parser sets the correct end line number for class methods.
     *
     * @return void
     */
    public function testParserSetsCorrectClassMethodEndLineNumbers()
    {
        $methods = $this->getMixedCodeClassMethods();

        $this->assertEquals(43, $methods->current()->getEndLine());
        $methods->next();
        $this->assertEquals(48, $methods->current()->getEndLine());
    }

    /**
     * Tests that the parser sets the correct start line number for an interface.
     *
     * @return void
     */
    public function testParserSetsCorrectInterfaceStartLineNumber()
    {
        $this->assertEquals(15, $this->getMixedCodeInterface()->getStartLine());
    }

    /**
     * Tests that the parser sets the correct end line number for an interface.
     *
     * @return void
     */
    public function testParserSetsCorrectInterfaceEndLineNumber()
    {
        $this->assertEquals(18, $this->getMixedCodeInterface()->getEndLine());
    }

    /**
     * Tests that the parser sets the correct start line number for interface
     * methods.
     *
     * @return void
     */
    public function testParserSetsCorrectInterfaceMethodStartLineNumbers()
    {
        $methods = $this->getMixedCodeInterfaceMethods();
        $this->assertEquals(17, $methods->current()->getStartLine());
    }

    /**
     * Tests that the parser sets the correct end line number for interface methods.
     *
     * @return void
     */
    public function testParserSetsCorrectInterfaceMethodEndLineNumbers()
    {
        $methods = $this->getMixedCodeInterfaceMethods();
        $this->assertEquals(17, $methods->current()->getEndLine());
    }

    /**
     * Tests that the parser marks all interface methods as abstract.
     *
     * @return void
     */
    public function testParserSetsAllInterfaceMethodsAbstract()
    {
        $methods = $this->getMixedCodeInterfaceMethods();
        $this->assertTrue($methods->current()->isAbstract());
    }

    /**
     * Tests that the parser sets the correct line number for methods.
     *
     * @return void
     */
    public function testParserSetsCorrectMethodLineNumber()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/mixed_code.php');
        $packages->next();
        $packages->next();

        $method = $packages->current()
                           ->getTypes()
                           ->current()
                           ->getMethods()
                           ->current();

        $this->assertEquals(17, $method->getStartLine());
    }

    /**
     * Tests that the parser doesn't mark a non abstract method as abstract.
     *
     * @return void
     */
    public function testParserDoesntMarkNonAbstractMethodAsAbstract()
    {
        $methods = $this->getMixedCodeClass()->getMethods();
        foreach ($methods as $method) {
            $this->assertFalse($method->isAbstract());
        }
    }

    /**
     * Tests that the parser marks an abstract method as abstract.
     *
     * @return void
     */
    public function testParsetMarksAbstractMethodAsAbstract()
    {
        $method = $this->getMixedCodeClass()
                       ->getParentClass()
                       ->getMethods()
                       ->current();

        $this->assertNotNull($method);
        $this->assertTrue($method->isAbstract());
    }

    /**
     * Tests that the parser handles PHP 5.3 object namespace + class chaining.
     *
     * @return void
     */
    public function testParserParseNewInstancePHP53()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/php-5.3/new.txt');

        $this->assertEquals(3, $packages->count());
        $packages->next();
        $this->assertEquals('php\depend1', $packages->current()->getName());
        $packages->next();
        $this->assertEquals('php\depend2', $packages->current()->getName());
    }

    /**
     * Tests that doc comment blocks are added to a function.
     *
     * @return void
     */
    public function testParserSetsCorrectFunctionDocComment()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/comments/function.php');

        $nodes = $packages->current()->getFunctions();
        $this->doTestParserSetsCorrectDocComment($nodes, 0);
    }

    /**
     * Tests that the parser sets the correct function return type.
     *
     * @return void
     */
    public function testParserSetsCorrectFunctionReturnType()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/comments/function1.php');

        $nodes = $packages->current()->getFunctions();
        $this->assertEquals(3, $nodes->count());

        $this->assertEquals('func1', $nodes->current()->getName());
        $this->assertNull($nodes->current()->getReturnType());
        $nodes->next();
        $this->assertEquals('func2', $nodes->current()->getName());
        $this->assertNotNull($nodes->current()->getReturnType());
        $this->assertEquals('SplObjectStore', $nodes->current()->getReturnType()->getName());
        $nodes->next();
        $this->assertEquals('func3', $nodes->current()->getName());
        $this->assertNotNull($nodes->current()->getReturnType());
        $this->assertEquals('SplObjectStore', $nodes->current()->getReturnType()->getName());
    }

    /**
     * Tests that the parser sets the correct method exception types.
     *
     * @return void
     */
    public function testParserSetsCorrectFunctionExceptionTypes()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/comments/function1.php');

        $nodes = $packages->current()
                          ->getFunctions();

        $this->assertEquals(3, $nodes->count());

        $this->assertEquals('func1', $nodes->current()->getName());
        $ex = $nodes->current()->getExceptionTypes();
        $this->assertEquals(1, $ex->count());
        $this->assertEquals('RuntimeException', $ex->current()->getName());

        $nodes->next();

        $this->assertEquals('func2', $nodes->current()->getName());
        $ex = $nodes->current()->getExceptionTypes();
        $this->assertEquals(2, $ex->count());
        $this->assertEquals('InvalidArgumentException', $ex->current()->getName());
        $ex->next();
        $this->assertEquals('OutOfRangeException', $ex->current()->getName());

        $nodes->next();

        $this->assertEquals('func3', $nodes->current()->getName());
        $ex = $nodes->current()->getExceptionTypes();
        $this->assertEquals(0, $ex->count());
    }

    /**
     * Tests that the parser doesn't handle annotations if this is set to true.
     *
     * @return void
     */
    public function testParserHandlesIgnoreAnnotationsCorrectForFunctions()
    {
        $source   = dirname(__FILE__) . '/_code/comments/function1.php';
        $packages = self::parseSource($source, true);

        $nodes = $packages->current()
                          ->getFunctions();

        $this->assertEquals(3, $nodes->count());
        $this->assertEquals(0, $nodes->current()->getExceptionTypes()->count());
        $this->assertNull($nodes->current()->getReturnType());

        $nodes->next();

        $this->assertEquals(3, $nodes->count());
        $this->assertEquals(0, $nodes->current()->getExceptionTypes()->count());
        $this->assertNull($nodes->current()->getReturnType());

        $nodes->next();

        $this->assertEquals(3, $nodes->count());
        $this->assertEquals(0, $nodes->current()->getExceptionTypes()->count());
        $this->assertNull($nodes->current()->getReturnType());
    }

    /**
     * Tests that doc comment blocks are added to a method.
     *
     * @return void
     */
    public function testParserSetsCorrectMethodDocComment()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/comments/method.php');
        $nodes = $packages->current()
                          ->getTypes()
                          ->current()
                          ->getMethods();

        $this->doTestParserSetsCorrectDocComment($nodes);
    }

    /**
     * Tests that the parser sets the correct method return type.
     *
     * @return void
     */
    public function testParserSetsCorrectMethodReturnType()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/comments/method3.php');

        $nodes = $packages->current()
                          ->getTypes()
                          ->current()
                          ->getMethods();
        $this->assertEquals(3, $nodes->count());

        $this->assertEquals('__construct', $nodes->current()->getName());
        $this->assertNull($nodes->current()->getReturnType());
        $nodes->next();
        $this->assertEquals('method1', $nodes->current()->getName());
        $this->assertNotNull($nodes->current()->getReturnType());
        $this->assertEquals('SplObjectStore', $nodes->current()->getReturnType()->getName());
        $nodes->next();
        $this->assertEquals('method2', $nodes->current()->getName());
        $this->assertNotNull($nodes->current()->getReturnType());
        $this->assertEquals('SplSubject', $nodes->current()->getReturnType()->getName());
    }

    /**
     * Tests that the parser sets the correct method exception types.
     *
     * @return void
     */
    public function testParserSetsCorrectMethodExceptionTypes()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/comments/method3.php');

        $nodes = $packages->current()
                          ->getTypes()
                          ->current()
                          ->getMethods();

        $this->assertEquals(3, $nodes->count());

        $this->assertEquals('__construct', $nodes->current()->getName());
        $ex = $nodes->current()->getExceptionTypes();
        $this->assertEquals(1, $ex->count());
        $this->assertEquals('RuntimeException', $ex->current()->getName());

        $nodes->next();

        $this->assertEquals('method1', $nodes->current()->getName());
        $ex = $nodes->current()->getExceptionTypes();
        $this->assertEquals(2, $ex->count());
        $this->assertEquals('OutOfBoundsException', $ex->current()->getName());
        $ex->next();
        $this->assertEquals('OutOfRangeException', $ex->current()->getName());

        $nodes->next();

        $this->assertEquals('method2', $nodes->current()->getName());
        $ex = $nodes->current()->getExceptionTypes();
        $this->assertEquals(0, $ex->count());
    }

    /**
     * Tests that the parser doesn't handle annotations if this is set to true.
     *
     * @return void
     */
    public function testParserHandlesIgnoreAnnotationsCorrectForMethods()
    {
        $source   = dirname(__FILE__) . '/_code/comments/method3.php';
        $packages = self::parseSource($source, true);

        $nodes = $packages->current()
                          ->getTypes()
                          ->current()
                          ->getMethods();

        $this->assertEquals(3, $nodes->count());
        $this->assertEquals(0, $nodes->current()->getExceptionTypes()->count());
        $this->assertNull($nodes->current()->getReturnType());

        $nodes->next();

        $this->assertEquals(3, $nodes->count());
        $this->assertEquals(0, $nodes->current()->getExceptionTypes()->count());
        $this->assertNull($nodes->current()->getReturnType());

        $nodes->next();

        $this->assertEquals(3, $nodes->count());
        $this->assertEquals(0, $nodes->current()->getExceptionTypes()->count());
        $this->assertNull($nodes->current()->getReturnType());
    }

    /**
     * Tests that the parser sets the correct doc comment blocks for properties.
     *
     * @return void
     */
    public function testParserSetsCorrectPropertyDocComment()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/comments/property.php');
        $nodes    = $packages->current()
                             ->getTypes()
                             ->current()
                             ->getProperties();

        $this->doTestParserSetsCorrectDocComment($nodes);
    }

    /**
     * Tests that the parser sets the correct visibility for properties.
     *
     * @return void
     */
    public function testParserSetsCorrectPropertyVisibility()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/comments/property.php');

        $nodes = $packages->current()
                          ->getTypes()
                          ->current()
                          ->getProperties();

        $this->assertTrue($nodes->current()->isProtected());
        $nodes->next();
        $this->assertTrue($nodes->current()->isPrivate());
        $nodes->next();
        $this->assertTrue($nodes->current()->isProtected());
        $nodes->next();
        $this->assertTrue($nodes->current()->isPublic());
    }

    /**
     * Tests that the parser sets property types for non scalar properties.
     *
     * @return void
     */
    public function testParserSetsCorrectPropertyTypes()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/comments/property2.php');

        $nodes = $packages->current()
                          ->getTypes()
                          ->current()
                          ->getProperties();

        $this->assertEquals('$property1', $nodes->current()->getName());
        $this->assertNotNull($nodes->current()->getType());
        $this->assertEquals('MyPropertyClass2', $nodes->current()->getType()->getName());
        $nodes->next();
        $this->assertEquals('$property2', $nodes->current()->getName());
        $this->assertNotNull($nodes->current()->getType());
        $this->assertEquals('MyPropertyClass2', $nodes->current()->getType()->getName());
        $nodes->next();
        $this->assertEquals('$property3', $nodes->current()->getName());
        $this->assertNotNull($nodes->current()->getType());
        $this->assertEquals('MyPropertyClass2', $nodes->current()->getType()->getName());
        $nodes->next();
        $this->assertEquals('$property4', $nodes->current()->getName());
        $this->assertNotNull($nodes->current()->getType());
        $this->assertEquals('MyPropertyClass2', $nodes->current()->getType()->getName());
        $nodes->next();
        $this->assertEquals('$property5', $nodes->current()->getName());
        $this->assertNull($nodes->current()->getType());
        $nodes->next();
        $this->assertEquals('$property6', $nodes->current()->getName());
        $this->assertNull($nodes->current()->getType());
    }

    /**
     * Tests that the parser sets property types for non scalar properties.
     *
     * @return void
     */
    public function testHandlesIgnoreAnnotationsCorrectForProperties()
    {
        $source   = dirname(__FILE__) . '/_code/comments/property2.php';
        $packages = self::parseSource($source, true);

        $nodes = $packages->current()
                          ->getTypes()
                          ->current()
                          ->getProperties();

        $this->assertEquals('$property1', $nodes->current()->getName());
        $this->assertNull($nodes->current()->getType());

        $nodes->next();

        $this->assertEquals('$property2', $nodes->current()->getName());
        $this->assertNull($nodes->current()->getType());

        $nodes->next();

        $this->assertEquals('$property3', $nodes->current()->getName());
        $this->assertNull($nodes->current()->getType());

        $nodes->next();

        $this->assertEquals('$property4', $nodes->current()->getName());
        $this->assertNull($nodes->current()->getType());

        $nodes->next();

        $this->assertEquals('$property5', $nodes->current()->getName());
        $this->assertNull($nodes->current()->getType());

        $nodes->next();

        $this->assertEquals('$property6', $nodes->current()->getName());
        $this->assertNull($nodes->current()->getType());
    }

    /**
     * Tests that parser sets the correct doc comment blocks for classes and
     * interfaces.
     *
     */
    public function testParserSetsCorrectClassOrInterfaceDocComment()
    {
        $expected = array(
            null,
            null,
            "/**\n * Sample comment.\n */",
            "/**\n * A second comment...\n */",
        );

        $packages = self::parseSource(dirname(__FILE__) . '/_code/class_and_interface_comment.php');
        $types    = $packages->current()->getTypes();

        foreach ($types as $type) {
            $comment = array_shift($expected);

            $this->assertEquals($comment, $type->getDocComment());
        }
    }

    /**
     * Tests that the parser supports sub packages.
     *
     * @return void
     */
    public function testParserSubpackageSupport()
    {
        $package = self::parseSource(dirname(__FILE__) . '/_code/package_subpackage_support.php')->current();

        $this->assertEquals('PHP\Depend', $package->getName());
    }

    /**
     * Tests that the parser supports sub packages.
     *
     * @return void
     */
    public function testParserSetsFileLevelFunctionPackage()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/package_file_level.php');

        $package0   = $packages->current();
        $functions0 = $package0->getFunctions();

        $packages->next();

        $package1   = $packages->current();
        $functions1 = $package1->getFunctions();

        $this->assertEquals(2, $functions0->count());
        $this->assertEquals('PHP\Depend', $functions0->current()->getPackage()->getName());
        $functions0->next();
        $this->assertEquals('PHP\Depend', $functions0->current()->getPackage()->getName());

        $this->assertEquals(1, $functions1->count());
        $this->assertEquals('PHP_Depend\Test', $functions1->current()->getPackage()->getName());
    }

    /**
     * Tests that the parser sets the correct constant parent.
     *
     * @return void
     */
    public function testParserSetsCorrectParentForInterfaceConstant()
    {
        $interface = $this->getMixedCodeInterface();
        $constants = $interface->getConstants();

        $this->assertEquals(1, $constants->count());
        $this->assertEquals('FOOBAR', $constants->current()->getName());
        $this->assertSame($interface, $constants->current()->getParent());
    }

    /**
     * Tests that the parser sets the correct constant start line.
     *
     * @return void
     */
    public function testParserSetsCorrectStartLineForInterfaceConstant()
    {
        $interface = $this->getMixedCodeInterface();
        $constants = $interface->getConstants();

        $this->assertEquals(1, $constants->count());
        $this->assertEquals('FOOBAR', $constants->current()->getName());
        $this->assertSame(16, $constants->current()->getStartLine());
    }

    /**
     * Tests that the parser sets the correct constant ebd line.
     *
     * @return void
     */
    public function testParserSetsCorrectEndLineForInterfaceConstant()
    {
        $interface = $this->getMixedCodeInterface();
        $constants = $interface->getConstants();

        $this->assertEquals(1, $constants->count());
        $this->assertEquals('FOOBAR', $constants->current()->getName());
        $this->assertSame(16, $constants->current()->getEndLine());
    }

    /**
     * Tests that the parser sets null comment for no comment constant.
     *
     * @return void
     */
    public function testParserSetsCorrectNullForInterfaceConstantWithoutComment()
    {
        $interface = $this->getMixedCodeInterface();
        $constants = $interface->getConstants();

        $this->assertEquals(1, $constants->count());
        $this->assertEquals('FOOBAR', $constants->current()->getName());
        $this->assertNull($constants->current()->getDocComment());
    }

    /**
     * Tests that the parser sets the correct constant parent.
     *
     * @return void
     */
    public function testParserSetsCorrectParentForClassConstant()
    {
        $class     = $this->getMixedCodeClass();
        $constants = $class->getConstants();

        $this->assertEquals(2, $constants->count());
        $this->assertEquals('BAR', $constants->current()->getName());
        $this->assertSame($class, $constants->current()->getParent());

        $constants->next();

        $this->assertEquals('FOO', $constants->current()->getName());
        $this->assertSame($class, $constants->current()->getParent());
    }

    /**
     * Tests that the parser sets the correct start line number.
     *
     * @return void
     */
    public function testParserSetsCorrectStartLineForClassConstant()
    {
        $class     = $this->getMixedCodeClass();
        $constants = $class->getConstants();

        $this->assertEquals(2, $constants->count());
        $this->assertEquals('BAR', $constants->current()->getName());
        $this->assertEquals(36, $constants->current()->getStartLine());

        $constants->next();

        $this->assertEquals('FOO', $constants->current()->getName());
        $this->assertEquals(31, $constants->current()->getStartLine());
    }

    /**
     * Tests that the parser sets the correct end line number.
     *
     * @return void
     */
    public function testParserSetsCorrectEndLineForClassConstant()
    {
        $class     = $this->getMixedCodeClass();
        $constants = $class->getConstants();

        $this->assertEquals(2, $constants->count());
        $this->assertEquals('BAR', $constants->current()->getName());
        $this->assertEquals(36, $constants->current()->getEndLine());

        $constants->next();

        $this->assertEquals('FOO', $constants->current()->getName());
        $this->assertEquals(31, $constants->current()->getEndLine());
    }

    /**
     * Tests that the parser sets the correct doc comment.
     *
     * @return void
     */
    public function testParserSetsCorrectCommentForClassConstant()
    {
        $class     = $this->getMixedCodeClass();
        $constants = $class->getConstants();

        $expected = '/**
     * My BAR constant.
     */';

        $this->assertEquals(2, $constants->count());
        $this->assertEquals('BAR', $constants->current()->getName());
        $this->assertEquals($expected, $constants->current()->getDocComment());

        $constants->next();

        $this->assertEquals('FOO', $constants->current()->getName());
        $this->assertNull($constants->current()->getDocComment());
    }

    /**
     * Tests that the parser sets the expected abstract modifier.
     *
     * @return void
     */
    public function testParserSetsExpectedAbstractModifier()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/parser/abstract_class.php');
        $this->assertSame(1, $packages->count());

        $class = $packages->current()->getClasses()->current();
        $this->assertType('PHP_Depend_Code_Class', $class);
        $this->assertTrue($class->isAbstract());
        $this->assertSame(PHP_Depend_ConstantsI::IS_EXPLICIT_ABSTRACT,
                          $class->getModifiers() & PHP_Depend_ConstantsI::IS_EXPLICIT_ABSTRACT);
    }

    /**
     * Tests that the parser sets the expected final modifier.
     *
     * @return void
     */
    public function testParserSetsExpectedFinalModifier()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/parser/final_class.php');
        $this->assertSame(1, $packages->count());

        $class = $packages->current()->getClasses()->current();
        $this->assertType('PHP_Depend_Code_Class', $class);
        $this->assertTrue($class->isFinal());
        $this->assertSame(PHP_Depend_ConstantsI::IS_FINAL,
                          $class->getModifiers() & PHP_Depend_ConstantsI::IS_FINAL);
    }

    /**
     * Tests that the parser ignores variable class instantiation.
     *
     * http://bugs.xplib.de/index.php?do=details&task_id=10&project=3
     *
     * @return void
     */
    public function testVariableClassNameBug10()
    {
        $package = self::parseSource(dirname(__FILE__) . '/_code/bugs/10.php')->current();
        $class   = $package->getClasses()->current();
        $method  = $class->getMethods()->current();

        $this->assertEquals('package10', $package->getName());
        $this->assertEquals('VariableClassNamesBug10', $class->getName());
        $this->assertEquals('foo10', $method->getName());
        $this->assertEquals(0, count($method->getDependencies()));
    }

    public function testParserCurlyBraceBug11()
    {
        $package   = self::parseSource(dirname(__FILE__) . '/_code/bugs/11.php')->current();
        $classes   = $package->getClasses();
        $functions = $package->getFunctions();

        $this->assertEquals(1, $classes->count());
        $this->assertEquals(1, $functions->count());

        $methods = $classes->current()->getMethods();

        $this->assertEquals(3, $methods->count());
    }

    /**
     * Tests that the parser handles curly braces in strings correct.
     *
     * http://bugs.xplib.de/index.php?do=details&task_id=12&project=3
     *
     * @return void
     */
    public function testParserCurlyBraceBug12()
    {
        $package = self::parseSource(dirname(__FILE__) . '/_code/bugs/12.php')->current();
        $classes = $package->getClasses();

        $this->assertEquals(1, $classes->count());

        $methods = $classes->current()->getMethods();

        $this->assertEquals(1, $methods->count());
    }

    /**
     * Tests that the parser ignores backtick expressions.
     *
     * http://bugs.xplib.de/index.php?do=details&task_id=15&project=3
     *
     * @return void
     */
    public function testParserBacktickExpressionBug15()
    {
        $package = self::parseSource(dirname(__FILE__) . '/_code/bugs/15.php')->current();
        $classes = $package->getClasses();

        $this->assertEquals(1, $classes->count());
        $methods = $classes->current()->getMethods();
        $this->assertEquals(1, $methods->count());
    }

    /**
     * Tests that the parser sets the correct type tokens.
     *
     * http://bugs.xplib.de/index.php?do=details&task_id=30&project=3
     *
     * @return void
     */
    public function testParserSetsCorrectTypeTokensIssue30()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/bugs/30.php');
        $this->assertEquals(1, $packages->count());

        $classes = $packages->current()->getClasses();
        $this->assertEquals(6, $classes->count());

        $testClass = null;
        foreach ($classes as $class) {
            if ($class->getName() === 'PHP_Depend_ParserTest') {
                $testClass = $class;
                break;
            }
        }

        $this->assertNotNull($testClass);

        $expected = array(
            array(PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN, 3),
            array(PHP_Depend_TokenizerI::T_DOC_COMMENT, 4),
            array(PHP_Depend_TokenizerI::T_PUBLIC, 11),
            array(PHP_Depend_TokenizerI::T_FUNCTION, 11),
            array(PHP_Depend_TokenizerI::T_STRING, 11),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 11),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 11),
            array(PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN, 12),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 13),
            array(PHP_Depend_TokenizerI::T_EQUAL, 13),
            array(PHP_Depend_TokenizerI::T_STRING, 13),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 13),
            array(PHP_Depend_TokenizerI::T_FILE, 13),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 13),
            array(PHP_Depend_TokenizerI::T_CONCAT, 13),
            array(PHP_Depend_TokenizerI::T_CONSTANT_ENCAPSED_STRING, 13),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 13),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 14),
            array(PHP_Depend_TokenizerI::T_EQUAL, 14),
            array(PHP_Depend_TokenizerI::T_NEW, 14),
            array(PHP_Depend_TokenizerI::T_STRING, 14),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 14),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 14),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 14),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 14),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 15),
            array(PHP_Depend_TokenizerI::T_EQUAL, 15),
            array(PHP_Depend_TokenizerI::T_NEW, 15),
            array(PHP_Depend_TokenizerI::T_STRING, 15),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 15),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 15),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 15),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 16),
            array(PHP_Depend_TokenizerI::T_EQUAL, 16),
            array(PHP_Depend_TokenizerI::T_NEW, 16),
            array(PHP_Depend_TokenizerI::T_STRING, 16),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 16),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 16),
            array(PHP_Depend_TokenizerI::T_COMMA, 16),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 16),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 16),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 16),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 18),
            array(PHP_Depend_TokenizerI::T_OBJECT_OPERATOR, 18),
            array(PHP_Depend_TokenizerI::T_STRING, 18),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 18),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 18),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 18),

            array(PHP_Depend_TokenizerI::T_VARIABLE, 20),
            array(PHP_Depend_TokenizerI::T_EQUAL, 20),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 20),
            array(PHP_Depend_TokenizerI::T_OBJECT_OPERATOR, 20),
            array(PHP_Depend_TokenizerI::T_STRING, 20),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 20),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 20),
            array(PHP_Depend_TokenizerI::T_OBJECT_OPERATOR, 20),
            array(PHP_Depend_TokenizerI::T_STRING, 20),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 20),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 20),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 20),

            array(PHP_Depend_TokenizerI::T_VARIABLE, 21),
            array(PHP_Depend_TokenizerI::T_EQUAL, 21),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 21),
            array(PHP_Depend_TokenizerI::T_OBJECT_OPERATOR, 21),
            array(PHP_Depend_TokenizerI::T_STRING, 21),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 21),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 21),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 21),

            array(PHP_Depend_TokenizerI::T_VARIABLE, 23),
            array(PHP_Depend_TokenizerI::T_OBJECT_OPERATOR, 23),
            array(PHP_Depend_TokenizerI::T_STRING, 23),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 23),
            array(PHP_Depend_TokenizerI::T_LNUMBER, 23),
            array(PHP_Depend_TokenizerI::T_COMMA, 23),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 23),
            array(PHP_Depend_TokenizerI::T_OBJECT_OPERATOR, 23),
            array(PHP_Depend_TokenizerI::T_STRING, 23),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 23),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 23),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 23),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 23),

            array(PHP_Depend_TokenizerI::T_VARIABLE, 24),
            array(PHP_Depend_TokenizerI::T_EQUAL, 24),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 24),
            array(PHP_Depend_TokenizerI::T_OBJECT_OPERATOR, 24),
            array(PHP_Depend_TokenizerI::T_STRING, 24),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 24),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 24),
            array(PHP_Depend_TokenizerI::T_OBJECT_OPERATOR, 24),
            array(PHP_Depend_TokenizerI::T_STRING, 24),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 24),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 24),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 24),

            array(PHP_Depend_TokenizerI::T_VARIABLE, 25),
            array(PHP_Depend_TokenizerI::T_OBJECT_OPERATOR, 25),
            array(PHP_Depend_TokenizerI::T_STRING, 25),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 25),
            array(PHP_Depend_TokenizerI::T_LNUMBER, 25),
            array(PHP_Depend_TokenizerI::T_COMMA, 25),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 25),
            array(PHP_Depend_TokenizerI::T_OBJECT_OPERATOR, 25),
            array(PHP_Depend_TokenizerI::T_STRING, 25),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 25),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 25),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 25),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 25),

            array(PHP_Depend_TokenizerI::T_CURLY_BRACE_CLOSE, 26),

            array(PHP_Depend_TokenizerI::T_DOC_COMMENT, 28),
            array(PHP_Depend_TokenizerI::T_PROTECTED, 33),
            array(PHP_Depend_TokenizerI::T_ABSTRACT, 33),
            array(PHP_Depend_TokenizerI::T_FUNCTION, 33),
            array(PHP_Depend_TokenizerI::T_STRING, 33),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 33),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 33),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 33),

            array(PHP_Depend_TokenizerI::T_CURLY_BRACE_CLOSE, 34),
        );

        foreach ($testClass->getTokens() as $token) {
            $expectedToken = array_shift($expected);

            $this->assertNotNull($expectedToken);
            $this->assertEquals($expectedToken[0], $token->type);
        }
    }

    /**
     * Tests that the parser detect a type within an instance of operator.
     *
     * <code>
     * if ($object instanceof SplObjectStorage) {
     *
     * }
     * </code>
     *
     * http://bugs.pdepend.org/index.php?do=details&task_id=16
     *
     * @return void
     */
    public function testParserDetectsTypeWithinInstanceOfOperatorIssue16()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/bugs/16-1.php');
        $this->assertEquals(2, $packages->count()); // +global & +spl

        $functions = $packages->current()->getFunctions();
        $this->assertEquals(1, $functions->count());

        $function = $functions->current();
        $this->assertEquals('pdepend', $function->getName());

        $dependencies = $function->getDependencies();
        $this->assertEquals(1, $dependencies->count());
        $this->assertEquals('SplObjectStorage', $dependencies->current()->getName());
    }

    /**
     * Tests that the parser ignores dynamic(with variables) instanceof operations.
     *
     * <code>
     * $class = 'SplObjectStorage';
     * if ($object instanceof $class) {
     *
     * }
     * </code>
     *
     * http://bugs.pdepend.org/index.php?do=details&task_id=16
     *
     * @return void
     * @todo TODO: It would be a cool feature if PHP_Depend would replace such
     *             combinations (T_VARIABLE = T_CONSTANT_ENCAPSED_STRING with
     *             T_INSTANCEOF + T_VARIABLE).
     */
    public function testParserIgnoresDynamicInstanceOfOperatorIssue16()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/bugs/16-2.php');
        $this->assertEquals(1, $packages->count()); // +global

        $functions = $packages->current()->getFunctions();
        $this->assertEquals(1, $functions->count());

        $function = $functions->current();
        $this->assertEquals('pdepend', $function->getName());

        $dependencies = $function->getDependencies();
        $this->assertEquals(0, $dependencies->count());
    }

    /**
     * Tests that the parser detects a type within a catch block.
     *
     * <code>
     * try {
     *     $foo->bar();
     * } catch (Exception $e) {}
     * </code>
     *
     * http://bugs.pdepend.org/index.php?do=details&task_id=17
     *
     * @return void
     */
    public function testParserDetectsTypeWithinCatchBlockIssue17()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/bugs/17-1.php');
        $this->assertEquals(2, $packages->count()); // +global & +spl

        $functions = $packages->current()->getFunctions();
        $this->assertEquals(1, $functions->count());

        $function = $functions->current();
        $this->assertEquals('pdepend', $function->getName());

        $dependencies = $function->getDependencies();
        $this->assertEquals(1, $dependencies->count());
        $this->assertEquals('OutOfBoundsException', $dependencies->current()->getName());
    }

    /**
     * The type hint detection was broken when a constant was used as default
     * value for a function parameter.
     *
     * http://bugs.pdepend.org/index.php?do=details&task_id=33&project=3
     *
     * @return void
     */
    public function testParserDetectsOnlyTypeHintsWithinTheFunctionSignatureBug33()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/bugs/33-1.php');
        $this->assertEquals(1, $packages->count()); // +global

        $functions = $packages->current()->getFunctions();
        $this->assertEquals(1, $functions->count());

        $function = $functions->current();
        $this->assertEquals('pdepend', $function->getName());

        $this->assertEquals(1, $function->getDependencies()->count());
    }

    /**
     * The type hint detection was broken when a constant was used as default
     * value for a method parameter.
     *
     * http://bugs.pdepend.org/index.php?do=details&task_id=33&project=3
     *
     * @return void
     */
    public function testParserDetectsOnlyTypeHintsWithinTheMethodSignatureBug33()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/bugs/33-2.php');
        $this->assertEquals(1, $packages->count()); // +global

        $classes = $packages->current()->getClasses();
        $this->assertEquals(2, $classes->count());

        $classes->next();

        $class = $classes->current();
        $this->assertEquals('PHP_Depend_Parser', $class->getName());

        $method = $class->getMethods()->current();
        $this->assertEquals('parse', $method->getName());

        $this->assertEquals(1, $method->getDependencies()->count());
    }

    /**
     * Tests that the parser sets the source file of an interface constant.
     *
     * @return void
     */
    public function testParserSetsSourceFileForInterfaceConstantsBug89()
    {
        $fileName = dirname(__FILE__) . '/_code/bugs/089-1.php';

        $packages = self::parseSource($fileName);
        $this->assertEquals(1, $packages->count()); // +global

        $interface = $packages->current()->getInterfaces()->current();
        $this->assertType('PHP_Depend_Code_Interface', $interface);

        $constant = $interface->getConstants()->current();
        $this->assertType('PHP_Depend_Code_TypeConstant', $constant);

        $sourceFile = $constant->getSourceFile();
        $this->assertNotNull($sourceFile);
        $this->assertType('PHP_Depend_Code_File', $sourceFile);
        $this->assertSame($fileName, $sourceFile->getFileName());
    }

    /**
     * Tests that the parser sets the source file of a class constant.
     *
     * @return void
     */
    public function testParserSetsSourceFileForClassConstantsBug89()
    {
        $fileName = dirname(__FILE__) . '/_code/bugs/089-2.php';

        $packages = self::parseSource($fileName);
        $this->assertEquals(1, $packages->count()); // +global

        $class = $packages->current()->getClasses()->current();
        $this->assertType('PHP_Depend_Code_Class', $class);

        $constant = $class->getConstants()->current();
        $this->assertType('PHP_Depend_Code_TypeConstant', $constant);

        $sourceFile = $constant->getSourceFile();
        $this->assertNotNull($sourceFile);
        $this->assertType('PHP_Depend_Code_File', $sourceFile);
        $this->assertSame($fileName, $sourceFile->getFileName());
    }

    /**
     * Tests that the parser sets the source file of an interface method.
     *
     * @return void
     */
    public function testParserSetsSourceFileForInterfaceMethodBug89()
    {
        $fileName = dirname(__FILE__) . '/_code/bugs/089-3.php';

        $packages = self::parseSource($fileName);
        $this->assertEquals(1, $packages->count()); // +global

        $interface = $packages->current()->getInterfaces()->current();
        $this->assertType('PHP_Depend_Code_Interface', $interface);

        $method = $interface->getMethods()->current();
        $this->assertType('PHP_Depend_Code_Method', $method);

        $sourceFile = $method->getSourceFile();
        $this->assertNotNull($sourceFile);
        $this->assertType('PHP_Depend_Code_File', $sourceFile);
        $this->assertSame($fileName, $sourceFile->getFileName());
    }

    /**
     * Tests that the parser sets the source file of a class method.
     *
     * @return void
     */
    public function testParserSetsSourceFileForClassMethodBug89()
    {
        $fileName = dirname(__FILE__) . '/_code/bugs/089-4.php';

        $packages = self::parseSource($fileName);
        $this->assertEquals(1, $packages->count()); // +global

        $class = $packages->current()->getClasses()->current();
        $this->assertType('PHP_Depend_Code_Class', $class);

        $method = $class->getMethods()->current();
        $this->assertType('PHP_Depend_Code_Method', $method);

        $sourceFile = $method->getSourceFile();
        $this->assertNotNull($sourceFile);
        $this->assertType('PHP_Depend_Code_File', $sourceFile);
        $this->assertSame($fileName, $sourceFile->getFileName());
    }

    /**
     * Tests that the parser sets the source file of a class property.
     *
     * @return void
     */
    public function testParserSetsSourceFileForClassPropertyBug89()
    {
        $fileName = dirname(__FILE__) . '/_code/bugs/089-5.php';

        $packages = self::parseSource($fileName);
        $this->assertEquals(1, $packages->count()); // +global

        $class = $packages->current()->getClasses()->current();
        $this->assertType('PHP_Depend_Code_Class', $class);

        $property = $class->getProperties()->current();
        $this->assertType('PHP_Depend_Code_Property', $property);

        $sourceFile = $property->getSourceFile();
        $this->assertNotNull($sourceFile);
        $this->assertType('PHP_Depend_Code_File', $sourceFile);
        $this->assertSame($fileName, $sourceFile->getFileName());
    }

    /**
     * Tests that the parser supports function parameters.
     *
     * @return void
     */
    public function testParserSetsCorrectFunctionParametersIssue32()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/issues/32-1.php');
        $this->assertEquals(1, $packages->count());

        $function = $packages->current()->getFunctions()->current();
        $this->assertNotNull($function);
        $this->assertEquals('pdepend', $function->getName());

        $parameters = $function->getParameters();
        $this->assertEquals(3, $parameters->count());

        // Note alphabetic order
        $parameter = $parameters->current();
        $this->assertEquals('$bar', $parameter->getName());
        $this->assertEquals(1, $parameter->getPosition());
        $this->assertNotNull($parameter->getType());
        $this->assertEquals('Bar', $parameter->getType()->getName());

        $parameters->next();

        $parameter = $parameters->current();
        $this->assertEquals('$foo', $parameter->getName());
        $this->assertEquals(0, $parameter->getPosition());
        $this->assertNull($parameter->getType());

        $parameters->next();

        $parameter = $parameters->current();
        $this->assertEquals('$foobar', $parameter->getName());
        $this->assertEquals(2, $parameter->getPosition());
        $this->assertNull($parameter->getType());
    }

    /**
     * Tests that the parser supports method parameters.
     *
     * @return void
     */
    public function testParserSetsCorrectMethodParametersIssue32()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/issues/32-2.php');
        $this->assertEquals(1, $packages->count());

        $classes = $packages->current()->getClasses();
        $this->assertEquals(2, $classes->count());

        $classes->next();

        $class = $classes->current();
        $this->assertNotNull($class);
        $this->assertEquals('PHP_Depend_Parser', $class->getName());

        $method = $class->getMethods()->current();
        $this->assertNotNull($method);
        $this->assertEquals('parse', $method->getName());

        $parameters = $method->getParameters();
        $this->assertEquals(3, $parameters->count());

        // Note alphabetic order
        $parameter = $parameters->current();
        $this->assertEquals('$bar', $parameter->getName());
        $this->assertEquals(1, $parameter->getPosition());
        $this->assertNotNull($parameter->getType());
        $this->assertEquals('Bar', $parameter->getType()->getName());

        $parameters->next();

        $parameter = $parameters->current();
        $this->assertEquals('$foo', $parameter->getName());
        $this->assertEquals(0, $parameter->getPosition());
        $this->assertNull($parameter->getType());

        $parameters->next();

        $parameter = $parameters->current();
        $this->assertEquals('$foobar', $parameter->getName());
        $this->assertEquals(2, $parameter->getPosition());
        $this->assertNull($parameter->getType());
    }

    /**
     * Tests that the parser sets the correct file position for classes and
     * interfaces.
     *
     * @return void
     */
    public function testParserSetsCorrectTypePositionIssue39()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/issues/39-1.php');
        $types    = $packages->current()->getTypes();

        $this->assertEquals(3, $types->count());

        $expected = array(
            'PHP_Depend_X'  =>  2,
            'PHP_Depend_Y'  =>  1,
            'PHP_Depend_Z'  =>  0
        );

        foreach ($expected as $typeName => $typePosition) {
            $this->assertNotNull($types->current());
            $this->assertEquals($typeName, $types->current()->getName());
            $this->assertEquals($typePosition, $types->current()->getPosition());

            $types->next();
        }
        $this->assertFalse($types->current());
    }

    public function testParserSetsCorrectMethodPositionIssue39()
    {
        $this->markTestIncomplete('Test not implemented yet.');

        $packages = self::parseSource(dirname(__FILE__) . '/_code/issues/39-2.php');
        $types    = $packages->current()->getTypes();

        $this->assertEquals(2, $types->count());
    }

    /**
     * Returns all packages in the mixed code example.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    protected function parseMixedCode()
    {
        return self::parseSource(dirname(__FILE__) . '/_code/mixed_code.php');
    }

    /**
     * Returns an interface instance from the mixed code test file.
     *
     * @return PHP_Depend_Code_Interface
     */
    protected function getMixedCodeInterface()
    {
        $packages = $this->parseMixedCode();
        $packages->next();
        $packages->next();

        return $packages->current()->getTypes()->current();
    }

    /**
     * Returns the methods of an interface from the mixed code test file.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    protected function getMixedCodeInterfaceMethods()
    {
        return $this->getMixedCodeInterface()->getMethods();
    }

    /**
     * Returns a class instance from the mixed code test file.
     *
     * @return PHP_Depend_Code_Class
     */
    protected function getMixedCodeClass()
    {
        $packages = $this->parseMixedCode();
        $packages->next();

        return $packages->current()->getTypes()->current();
    }

    /**
     * Returns the methods of a class from the mixed code test file.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    protected function getMixedCodeClassMethods()
    {
        return $this->getMixedCodeClass()->getMethods();
    }

    /**
     * Generic comment test method.
     *
     * @param PHP_Depend_Code_NodeIterator $nodes  The context nodes.
     * @param integer                      $indent How deep is the commend indented.
     *
     * @return void
     */
    protected function doTestParserSetsCorrectDocComment(
                                            PHP_Depend_Code_NodeIterator $nodes,
                                            $indent = 1)
    {
        $ws = str_repeat(" ", 4 * $indent);

        $expected = array(
            "/**\n{$ws} * This is a second comment.\n{$ws} */",
            "/**\n{$ws} * This is one comment.\n{$ws} */",
            null,
            null,
        );

        foreach ($nodes as $callable) {
            $comment = array_shift($expected);

            $this->assertEquals($comment, $callable->getDocComment());
        }
    }
}