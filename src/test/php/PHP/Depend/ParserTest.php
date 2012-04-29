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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

/**
 * Test case implementation for the PHP_Depend code parser.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 *
 * @covers PHP_Depend_Parser
 * @group pdepend
 * @group pdepend::parser
 * @group unittest
 */
class PHP_Depend_ParserTest extends PHP_Depend_AbstractTest
{
    /**
     * testParserHandlesMaxNestingLevel
     *
     * @return void
     */
    public function testParserHandlesMaxNestingLevel()
    {
        if (version_compare(phpversion(), '5.2.10') < 0) {
            $this->markTestSkipped();
        }

        ini_set('xdebug.max_nesting_level', '100');

        $cache   = new PHP_Depend_Util_Cache_Driver_Memory();
        $builder = new PHP_Depend_Builder_Default();

        $tokenizer = new PHP_Depend_Tokenizer_Internal();
        $tokenizer->setSourceFile(self::createCodeResourceUriForTest());

        $parser = new PHP_Depend_Parser_VersionAllParser($tokenizer, $builder, $cache);
        $parser->setMaxNestingLevel(512);
        $parser->parse();
    }

    /**
     * Tests the main parse method.
     *
     * @return void
     */
    public function testParseMixedCode()
    {
        $expected = array(
            'pkg1'                                =>  true,
            'pkg2'                                =>  true,
            'pkg3'                                =>  true,
            PHP_Depend_BuilderI::DEFAULT_PACKAGE  =>  true
        );

        $tmp = self::parseCodeResourceForTest();
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
        $sourceFile = self::createCodeResourceUriForTest();
        $this->setExpectedException(
            'PHP_Depend_Parser_TokenStreamEndException',
            "Unexpected end of token stream in file: {$sourceFile}."
        );

        self::parseCodeResourceForTest();
    }

    /**
     * Tests that the parser throws an exception if it reaches the end of the
     * stream but not all function curly braces are closed.
     *
     * @return void
     */
    public function testParserWithUnclosedFunctionFail()
    {
        $this->setExpectedException(
            'PHP_Depend_Parser_TokenStreamEndException',
            'Unexpected end of token stream in file: '
        );

        self::parseCodeResourceForTest();
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
            'Unexpected token: (, line: 3, col: 23, file: '
        );

        self::parseCodeResourceForTest();
    }

    /**
     * Tests that the parser throws an exception if it finds an invalid
     * function signature.
     *
     * @return void
     */
    public function testParserWithInvalidFunction2Fail()
    {
        $this->setExpectedException(
            'RuntimeException',
            "Unexpected token: Bar, line: 3, col: 18, file: "
        );

        self::parseCodeResourceForTest();
    }

    /**
     * Tests that the parser sets the correct line number for a function.
     *
     * @return void
     */
    public function testParserSetsCorrectFunctionLineNumber()
    {
        $packages = self::parseCodeResourceForTest();
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
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_FUNCTION, 'function', 5, 5, 1, 8),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_STRING, 'foo', 5, 5, 10, 12),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, '(', 5, 5, 13, 13),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_VARIABLE, '$foo', 5, 5, 14, 17),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_EQUAL, '=', 5, 5, 19, 19),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_ARRAY, 'array', 5, 5, 21, 25),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, '(', 5, 5, 26, 26),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, ')', 5, 5, 27, 27),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, ')', 5, 5, 28, 28),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN, '{', 5, 5, 30, 30),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_FOREACH, 'foreach', 6, 6, 5, 11),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, '(', 6, 6, 13, 13),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_VARIABLE, '$foo', 6, 6, 14, 17),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_AS, 'as', 6, 6, 19, 20),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_VARIABLE, '$bar', 6, 6, 22, 25),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, ')', 6, 6, 26, 26),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN, '{', 6, 6, 28, 28),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_STRING, 'FooBar', 7, 7, 9, 14),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_DOUBLE_COLON, '::', 7, 7, 15, 16),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_STRING, 'y', 7, 7, 17, 17),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, '(', 7, 7, 18, 18),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_VARIABLE, '$bar', 7, 7, 19, 22),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, ')', 7, 7, 23, 23),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_SEMICOLON, ';', 7, 7, 24, 24),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_CURLY_BRACE_CLOSE, '}', 8, 8, 5, 5),
            new PHP_Depend_Token(PHP_Depend_TokenizerI::T_CURLY_BRACE_CLOSE, '}', 9, 9, 1, 1),
        );

        $packages = self::parseSource('/Parser/parser-sets-expected-function-tokens.php');
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $this->assertEquals($tokens, $function->getTokens());
    }

    /**
     * Tests that the parser sets a detected file comment.
     *
     * @return void
     */
    public function testParserSetsCorrectFileComment()
    {
        $packages = self::parseCodeResourceForTest();
        $this->assertEquals(1, $packages->count()); // default

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
        $packages = self::parseCodeResourceForTest();
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
        $packages = self::parseCodeResourceForTest();
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
        $this->assertEquals(30, $this->getClassForTest()->getStartLine());
    }

    /**
     * Tests that the parser sets the correct end line number for a class.
     *
     * @return void
     */
    public function testParserSetsCorrectClassEndLineNumber()
    {
        $this->assertEquals(49, $this->getClassForTest()->getEndLine());
    }

    /**
     * Tests that the parser sets the correct start line number for class methods.
     *
     * @return void
     */
    public function testParserSetsCorrectClassMethodStartLineNumber()
    {
        $methods = $this->getClassMethodsForTest();

        $this->assertEquals(43, $methods->current()->getStartLine());
        $methods->next();
        $this->assertEquals(44, $methods->current()->getStartLine());
    }

    /**
     * Tests that the parser sets the correct end line number for class methods.
     *
     * @return void
     */
    public function testParserSetsCorrectClassMethodEndLineNumber()
    {
        $methods = $this->getClassMethodsForTest();

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
        $this->assertEquals(15, $this->getInterfaceForTest()->getStartLine());
    }

    /**
     * Tests that the parser sets the correct end line number for an interface.
     *
     * @return void
     */
    public function testParserSetsCorrectInterfaceEndLineNumber()
    {
        $this->assertEquals(18, $this->getInterfaceForTest()->getEndLine());
    }

    /**
     * Tests that the parser sets the correct start line number for interface
     * methods.
     *
     * @return void
     */
    public function testParserSetsCorrectInterfaceMethodStartLineNumbers()
    {
        $methods = $this->getInterfaceMethodsForTest();
        $this->assertEquals(17, $methods->current()->getStartLine());
    }

    /**
     * Tests that the parser sets the correct end line number for interface methods.
     *
     * @return void
     */
    public function testParserSetsCorrectInterfaceMethodEndLineNumbers()
    {
        $methods = $this->getInterfaceMethodsForTest();
        $this->assertEquals(17, $methods->current()->getEndLine());
    }

    /**
     * Tests that the parser marks all interface methods as abstract.
     *
     * @return void
     */
    public function testParserSetsAllInterfaceMethodsAbstract()
    {
        $methods = $this->getInterfaceMethodsForTest();
        $this->assertTrue($methods->current()->isAbstract());
    }

    /**
     * testParserHandlesClassWithMultipleImplementedInterfaces
     *
     * @return void
     */
    public function testParserHandlesClassWithMultipleImplementedInterfaces()
    {
        $class = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current();

        self::assertEquals(3, count($class->getInterfaces()));
    }

    /**
     * testParserHandlesInterfaceWithMultipleParentInterfaces
     *
     * @return void
     */
    public function testParserHandlesInterfaceWithMultipleParentInterfaces()
    {
        $class = self::parseCodeResourceForTest()
            ->current()
            ->getInterfaces()
            ->current();

        self::assertEquals(3, count($class->getInterfaces()));
    }

    /**
     * Tests that the parser sets the correct line number for methods.
     *
     * @return void
     */
    public function testParserSetsCorrectMethodLineNumber()
    {
        $packages = self::parseCodeResourceForTest();
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
    public function testParserDoesNotMarkNonAbstractMethodAsAbstract()
    {
        $methods = $this->getClassForTest()->getMethods();
        // TODO: Replace this loop with an array(<method>=><abstract>)
        foreach ($methods as $method) {
            $this->assertFalse($method->isAbstract());
        }
    }

    /**
     * Tests that the parser marks an abstract method as abstract.
     *
     * @return void
     */
    public function testParserMarksAbstractMethodAsAbstract()
    {
        $method = $this->getClassForTest()
                       ->getParentClass()
                       ->getMethods()
                       ->current();

        $this->assertTrue($method->isAbstract());
    }

    /**
     * Tests that the parser handles PHP 5.3 object namespace + class chaining.
     *
     * @return void
     */
    public function testParserParseNewInstancePHP53()
    {
        $packages = self::parseCodeResourceForTest();
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $dependencies = $function->getDependencies();
        $this->assertEquals('php\depend1', $dependencies->current()->getPackage()->getName());
        $dependencies->next();
        $this->assertEquals('php\depend2', $dependencies->current()->getPackage()->getName());
    }

    /**
     * Tests that doc comment blocks are added to a function.
     *
     * @return void
     */
    public function testParserSetsCorrectFunctionDocComment()
    {
        $packages = self::parseCodeResourceForTest();

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
        $packages = self::parseCodeResourceForTest();

        $nodes = $packages->current()->getFunctions();
        $this->assertEquals(3, $nodes->count());

        $this->assertEquals('func1', $nodes->current()->getName());
        $this->assertNull($nodes->current()->getReturnClass());
        $nodes->next();
        $this->assertEquals('func2', $nodes->current()->getName());
        $this->assertEquals('SplObjectStore', $nodes->current()->getReturnClass()->getName());
        $nodes->next();
        $this->assertEquals('func3', $nodes->current()->getName());
        $this->assertEquals('SplObjectStore', $nodes->current()->getReturnClass()->getName());
    }

    /**
     * Tests that the parser sets the correct method exception types.
     *
     * @return void
     */
    public function testParserSetsCorrectFunctionExceptionTypes()
    {
        $functions = self::parseCodeResourceForTest()
            ->current()
            ->getFunctions();

        $actual = array();
        foreach ($functions as $function) {
            foreach ($function->getExceptionClasses() as $exception) {
                $actual[] = "{$function->getName()} throws {$exception->getName()}";
            }
        }

        self::assertEquals(
            array(
                'func1 throws RuntimeException',
                'func2 throws OutOfRangeException',
                'func2 throws InvalidArgumentException',
            ),
            $actual
        );
    }

    /**
     * Tests that the parser doesn't handle annotations if this is set to true.
     *
     * @return void
     */
    public function testParserHandlesIgnoreAnnotationsCorrectForFunctions()
    {
        $functions = self::parseCodeResourceForTest(true)
            ->current()
            ->getFunctions();

        $actual = array();
        foreach ($functions as $function) {
            $actual[] = $function->getExceptionClasses()->count();
            $actual[] = $function->getReturnClass();
        }

        self::assertSame(array(0, null, 0, null, 0, null), $actual);
    }

    /**
     * Tests that doc comment blocks are added to a method.
     *
     * @return void
     */
    public function testParserSetsCorrectMethodDocComment()
    {
        $nodes = self::parseCodeResourceForTest()
            ->current()
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
        $nodes = self::parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current()
            ->getMethods();

        $actual = array();
        foreach ($nodes as $method) {
            $actual[] = $method->getName();
            $actual[] = $method->getReturnClass() ? $method->getReturnClass()->getName() : null;
        }

        self::assertEquals(
            array('__construct', null, 'method1', 'SplObjectStore', 'method2', 'SplSubject'),
            $actual
        );
    }

    /**
     * Tests that the parser sets the correct method exception types.
     *
     * @return void
     */
    public function testParserSetsCorrectMethodExceptionTypes()
    {
        $nodes = self::parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current()
            ->getMethods();

        $actual = array();
        foreach ($nodes as $method) {
            $actual[] = $method->getName();
            foreach ($method->getExceptionClasses() as $exception) {
                $actual[] = $exception->getName();
            }
        }

        self::assertEquals(
            array(
                '__construct',
                'RuntimeException',
                'method1',
                'OutOfRangeException',
                'OutOfBoundsException',
                'method2'
            ),
            $actual
        );
    }

    /**
     * Tests that the parser doesn't handle annotations if this is set to true.
     *
     * @return void
     */
    public function testParserHandlesIgnoreAnnotationsCorrectForMethods()
    {
        $methods = self::parseCodeResourceForTest( true)
            ->current()
            ->getTypes()
            ->current()
            ->getMethods();

        $actual = array();
        foreach ($methods as $method) {
            $actual[] = $method->getExceptionClasses()->count();
            $actual[] = $method->getReturnClass();
        }

        self::assertSame(array(0, null, 0, null, 0, null), $actual);
    }

    /**
     * Tests that the parser sets the correct doc comment blocks for properties.
     *
     * @return void
     */
    public function testParserSetsCorrectPropertyDocComment()
    {
        $nodes    = self::parseCodeResourceForTest()
            ->current()
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
        $nodes = self::parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current()
            ->getProperties();

        $actual = array();
        foreach ($nodes as $node) {
            $actual[] = array(
                'public'     =>  $node->isPublic(),
                'protected'  =>  $node->isProtected(),
                'private'    =>  $node->isPrivate(),
            );
        }

        self::assertEquals(
            array(
                array('public' => false, 'protected' => false, 'private' => true),
                array('public' => true,  'protected' => false, 'private' => false),
                array('public' => false, 'protected' => true,  'private' => false),
                array('public' => false, 'protected' => true,  'private' => false),
            ),
            $actual
        );
    }

    /**
     * Tests that the parser sets property types for non scalar properties.
     *
     * @return void
     */
    public function testParserSetsCorrectPropertyTypes()
    {
        $nodes = self::parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current()
            ->getProperties();

        $actual = array();
        foreach ($nodes as $node) {
            $className = $node->getClass() ? $node->getClass()->getName() : null;
            $actual[$node->getName()] = $className;
        }

        self::assertEquals(
            array(
                '$property1'  =>  'MyPropertyClass2',
                '$property2'  =>  'MyPropertyClass2',
                '$property3'  =>  'MyPropertyClass2',
                '$property4'  =>  'MyPropertyClass2',
                '$property5'  =>  null,
                '$property6'  =>  null,
            ),
            $actual
        );
    }

    /**
     * Tests that the parser recognizes the first type defined in a doc comment.
     *
     * <code>
     * (at)var false|null|Runtime
     *
     * // Results in
     * Runtime
     * </code>
     *
     * @return void
     */
    public function testParserSetsExpectedPropertyTypeForChainedComment()
    {
        $class = self::parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current()
            ->getProperties()
            ->current()
            ->getClass();

        self::assertEquals('Runtime', $class->getName());
    }

    /**
     * Tests that the parser recognizes the first type defined in a doc comment.
     *
     * <code>
     * (at)var array(Session|Runtime)
     *
     * // Results in
     * Session
     * </code>
     *
     * @return void
     */
    public function testParserSetsExpectedPropertyTypeForChainedCommentInArray()
    {
        $type = self::parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current()
            ->getProperties()
            ->current()
            ->getClass();

        self::assertEquals('Session', $type->getName());
    }

    /**
     * Tests that the parser recognizes the first type defined in a doc comment.
     *
     * <code>
     * (at)return false|null|Runtime
     *
     * // Results in
     * Runtime
     * </code>
     *
     * @return void
     */
    public function testParserSetsExpectedReturnTypeForChainedComment()
    {
        $type = self::parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current()
            ->getMethods()
            ->current()
            ->getReturnClass();

        $this->assertSame('Runtime', $type->getName());
    }

    /**
     * Tests that the parser recognizes the first type defined in a doc comment.
     *
     * <code>
     * (at)return array(integer => null|Session|Runtime)
     *
     * // Results in
     * Session
     * </code>
     *
     * @return void
     */
    public function testParserSetsExpectedReturnTypeForChainedCommentInArray()
    {
        $type = self::parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current()
            ->getMethods()
            ->current()
            ->getReturnClass();

        $this->assertSame('Session', $type->getName());
    }

    /**
     * Tests that the parser sets property types for non scalar properties.
     *
     * @return void
     */
    public function testHandlesIgnoreAnnotationsCorrectForProperties()
    {
        $nodes = self::parseCodeResourceForTest(true)
            ->current()
            ->getTypes()
            ->current()
            ->getProperties();

        $actual = array();
        foreach ($nodes as $property) {
            $actual[] = $property->getClass();
        }

        self::assertEquals(array(null, null, null, null, null, null), $actual);
    }

    /**
     * Tests that parser sets the correct doc comment blocks for classes and
     * interfaces.
     *
     * @return void
     */
    public function testParserSetsCorrectClassOrInterfaceDocComment()
    {
        $actual   = array();
        $expected = array(
            "/**\n * Sample comment.\n */",
            null,
            null,
            "/**\n * A second comment...\n */",
        );

        $packages = self::parseCodeResourceForTest();
        foreach ($packages->current()->getTypes() as $type) {
            $actual[] = $type->getDocComment();
        }

        self::assertEquals($expected, $actual);
    }

    /**
     * Tests that the parser supports sub packages.
     *
     * @return void
     */
    public function testParserSubpackageSupport()
    {
        $package = self::parseCodeResourceForTest()->current();
        self::assertEquals('PHP\Depend', $package->getName());
    }

    /**
     * Tests that the parser supports sub packages.
     *
     * @return void
     */
    public function testParserSetsFileLevelFunctionPackage()
    {
        $packages = self::parseCodeResourceForTest();

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
     * testParserSetsAbstractPropertyOnClass
     *
     * @return void
     */
    public function testParserSetsAbstractPropertyOnClass()
    {
        $class = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current();

        self::assertTrue($class->isAbstract());
    }

    /**
     * testParserSetsAbstractModifierOnClass
     *
     * @return void
     */
    public function testParserSetsAbstractModifierOnClass()
    {
        $class = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current();

        self::assertSame(
            PHP_Depend_ConstantsI::IS_EXPLICIT_ABSTRACT,
            $class->getModifiers() & PHP_Depend_ConstantsI::IS_EXPLICIT_ABSTRACT
        );
    }

    /**
     * testParserSetsFinalPropertyOnClass
     *
     * @return void
     */
    public function testParserSetsFinalPropertyOnClass()
    {
        $class = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current();

        self::assertTrue($class->isFinal());
    }

    /**
     * testParserSetsFinalModifierOnClass
     *
     * @return void
     */
    public function testParserSetsFinalModifierOnClass()
    {
        $class = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current();

        self::assertSame(
            PHP_Depend_ConstantsI::IS_FINAL,
            $class->getModifiers() & PHP_Depend_ConstantsI::IS_FINAL
        );
    }

    /**
     * Tests that the parser handles nested array structures as parameter
     * default value correct.
     *
     * @return void
     */
    public function testParserHandlesNestedArraysAsParameterDefaultValue()
    {
        // Current implementation cannot handle nested structures
        self::parseCodeResourceForTest();
    }

    /**
     * testParserStripsCommentsInParseExpressionUntilCorrect
     *
     * @return void
     */
    public function testParserStripsCommentsInParseExpressionUntilCorrect()
    {
        $method = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $foreach = $method->getFirstChildOfType(PHP_Depend_Code_ASTForeachStatement::CLAZZ);
        $this->assertNotNull($foreach);
    }

    /**
     * Tests that the parser throws an exception for an unclosed array
     * declaration within the default value of a parameter.
     *
     * @return void
     */
    public function testParserThrowsUnexpectedTokenExceptionForBrokenParameterArrayDefaultValue()
    {
        $this->setExpectedException(
            'PHP_Depend_Parser_UnexpectedTokenException',
            'Unexpected token: {, line: 2, col: 29, file: '
        );

        self::parseCodeResourceForTest();
    }

    /**
     * Tests that the parser throws an exception when it detects an invalid
     * token within the parameter declaration of a function or method.
     *
     * @return void
     */
    public function testParserThrowsUnexpectedTokenExceptionForInvalidTokenInParameterDefaultValue()
    {
        $this->setExpectedException(
            'PHP_Depend_Parser_UnexpectedTokenException',
            'Unexpected token: &, line: 2, col: 27, file: '
        );

        self::parseCodeResourceForTest();
    }

    /**
     * Tests that the parser throws an exception when it detects an invalid
     * token in a class body.
     *
     * @return void
     */
    public function testParserThrowsUnexpectedTokenExceptionForInvalidTokenInClassBody()
    {
        $this->setExpectedException(
            'PHP_Depend_Parser_UnexpectedTokenException',
            'Unexpected token: ;, line: 4, col: 5, file: '
        );

        self::parseCodeResourceForTest();
    }

    /**
     * Tests that the parser throws an exception when it detects an invalid
     * token in a method or property declaration.
     *
     * @return void
     */
    public function testParserThrowsUnexpectedTokenExceptionForInvalidTokenInMethodDeclaration()
    {
        $this->setExpectedException(
            'PHP_Depend_Parser_UnexpectedTokenException',
            'Unexpected token: &, line: 4, col: 12, file: '
        );

        self::parseCodeResourceForTest();
    }

    /**
     * Tests that the parser throws an exception when it detects an invalid
     * token in a method or property declaration.
     *
     * @return void
     */
    public function testParserThrowsUnexpectedTokenExceptionForInvalidTokenInPropertyDeclaration()
    {
        $this->setExpectedException(
            'PHP_Depend_Parser_UnexpectedTokenException',
            'Unexpected token: const, line: 4, col: 13, file: '
        );

        self::parseCodeResourceForTest();
    }

    /**
     * Tests that the parser handles the <b>parent</b> keyword within the default
     * value of a function.
     *
     * @return void
     */
    public function testParserHandlesParentKeywordInFunctionParameterDefaultValue()
    {
        $parameters = self::parseCodeResourceForTest()->current()
            ->getFunctions()
            ->current()
            ->getParameters();

        $this->assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the parser handles the <b>parent</b> keyword within the default
     * value of a method.
     *
     * @return void
     */
    public function testParserHandlesParentKeywordInMethodParameterDefaultValue()
    {
        $parameters = self::parseCodeResourceForTest()->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters();

        $this->assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the parser handles the self keyword as parameter type hint.
     *
     * @return void
     */
    public function testParserHandlesSelfKeywordAsParameterTypeHint()
    {
        $parameters = self::parseCodeResourceForTest()->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters();

        $this->assertNotNull($parameters[0]);
    }

    /**
     * testParserSetsBestMatchForParameterTypeHintEvenWhenNameEquals
     *
     * @return void
     */
    public function testParserSetsBestMatchForParameterTypeHintEvenWhenNameEquals()
    {
        $classes = self::parseCodeResourceForTest()->current()
            ->getClasses();

        $class1 = $classes->current();
        $classes->next();
        $class2 = $classes->current();

        $parameters = $class2->getMethods()
            ->current()
            ->getParameters();

        $this->assertSame($class1, $parameters[0]->getClass());
        $this->assertNotSame($class2, $parameters[0]->getClass());
    }

    /**
     * Tests that the parser translates the self keyword into the same instance,
     * even when a similar class exists.
     *
     * @return void
     */
    public function testParserSetsTheReallySameParameterHintInstanceForKeywordSelf()
    {
        $class = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current();

        $parameters = $class->getMethods()
            ->current()
            ->getParameters();

        $this->assertSame($class, $parameters[0]->getClass());
    }

    /**
     * testParserStripsLeadingSlashFromNamespacedClassName
     *
     * @return void
     */
    public function testParserStripsLeadingSlashFromNamespacedClassName()
    {
        $package = self::parseCodeResourceForTest()->current();
        $this->assertEquals('foo', $package->getName());
    }

    /**
     * testParserStripsLeadingSlashFromNamespacedClassName
     *
     * @return void
     */
    public function testParserStripsLeadingSlashFromNamespaceAliasedClassName()
    {
        $package = self::parseCodeResourceForTest()->current()
            ->getClasses()
            ->current()
            ->getParentClass()
            ->getPackage();

        $this->assertEquals('foo\bar\baz', $package->getName());
    }

    /**
     * testParserStripsLeadingSlashFromInheritNamespacedClassName
     *
     * @return void
     */
    public function testParserStripsLeadingSlashFromInheritNamespacedClassName()
    {
        $package = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getParentClass()
            ->getPackage();

        $this->assertEquals('bar', $package->getName());
    }

    /**
     * testParserThrowsExpectedExceptionWhenDefaultStaticDefaultValueNotExists
     *
     * @return void
     * @expectedException PHP_Depend_Parser_MissingValueException
     */
    public function testParserThrowsExpectedExceptionWhenDefaultStaticDefaultValueNotExists()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParserHandlesDoubleQuoteStringAsConstantDefaultValue
     *
     * @return void
     */
    public function testParserHandlesDoubleQuoteStringAsConstantDefaultValue()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParserHandlesDoubleQuoteStringWithEscapedVariable
     *
     * @return void
     */
    public function testParserHandlesDoubleQuoteStringWithEscapedVariable()
    {
        $function = self::parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current();

        $string = $function->getFirstChildOfType(PHP_Depend_Code_ASTString::CLAZZ);
        $image  = $string->getChild(0)->getImage();

        $this->assertEquals('\$foobar', $image);
    }

    /**
     * testParserHandlesDoubleQuoteStringWithEscapedDoubleQuote
     *
     * @return void
     */
    public function testParserHandlesDoubleQuoteStringWithEscapedDoubleQuote()
    {
        $function = self::parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current();

        $string = $function->getFirstChildOfType(PHP_Depend_Code_ASTString::CLAZZ);
        $image  = $string->getChild(0)->getImage();

        $this->assertEquals('\\\\\"', $image);
    }

    /**
     * testParserNotHandlesDoubleQuoteStringWithVariableAndParenthesisAsFunctionCall
     *
     * @return void
     */
    public function testParserNotHandlesDoubleQuoteStringWithVariableAndParenthesisAsFunctionCall()
    {
        $function = self::parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current();

        $string   = $function->getFirstChildOfType(PHP_Depend_Code_ASTString::CLAZZ);
        $variable = $string->getChild(0);

        $this->assertInstanceOf(PHP_Depend_Code_ASTVariable::CLAZZ, $variable);
    }

    /**
     * testParserNotHandlesDoubleQuoteStringWithVariableAndEqualAsAssignment
     *
     * @return void
     */
    public function testParserNotHandlesDoubleQuoteStringWithVariableAndEqualAsAssignment()
    {
        $function = self::parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current();

        $string   = $function->getFirstChildOfType(PHP_Depend_Code_ASTString::CLAZZ);
        $variable = $string->getChild(0);

        $this->assertInstanceOf(PHP_Depend_Code_ASTVariable::CLAZZ, $variable);
    }

    /**
     * testParserHandlesStringWithQuestionMarkNotAsTernaryOperator
     *
     * @return void
     */
    public function testParserHandlesStringWithQuestionMarkNotAsTernaryOperator()
    {
        $method = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $string = $method->getFirstChildOfType(PHP_Depend_Code_ASTString::CLAZZ);
        $this->assertInstanceOf(PHP_Depend_Code_ASTLiteral::CLAZZ, $string->getChild(1));
    }

    /**
     * testParserStopsProcessingWhenCacheContainsValidResult
     *
     * @return void
     */
    public function testParserStopsProcessingWhenCacheContainsValidResult()
    {
        $builder = $this->getMock('PHP_Depend_BuilderI');

        $tokenizer = new PHP_Depend_Tokenizer_Internal();
        $tokenizer->setSourceFile(__FILE__);

        $cache = $this->getMock('PHP_Depend_Util_Cache_Driver');
        $cache->expects($this->once())
            ->method('restore')
            ->will(self::returnValue(true));
        $cache->expects($this->never())
            ->method('store');

        $parser = new PHP_Depend_Parser_VersionAllParser(
            $tokenizer,
            $builder,
            $cache
        );
        $parser->parse();
    }

    /**
     * testParseClosureAsFunctionArgument
     *
     * @return void
     */
    public function testParseClosureAsFunctionArgument()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParseNowdocInMethodBody
     *
     * @return void
     */
    public function testParseNowdocInMethodBody()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParseDoWhileStatement
     *
     * @return void
     */
    public function testParseDoWhileStatement()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParserHandlesCompoundExpressionInArrayBrackets
     *
     * @return void
     */
    public function testParserHandlesCompoundExpressionInArrayBrackets()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParserHandlesEmptyNonePhpCodeInMethodBody
     *
     * @return void
     */
    public function testParserHandlesEmptyNonePhpCodeInMethodBody()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParserHandlesPhpCloseTagInMethodBody
     *
     * @return void
     */
    public function testParserHandlesPhpCloseTagInMethodBody()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParserHandlesMultiplePhpCloseTagsInMethodBody
     *
     * @return void
     */
    public function testParserHandlesMultiplePhpCloseTagsInMethodBody()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParseExpressionUntilThrowsExceptionForUnclosedStatement
     *
     * @return void
     * @expectedException PHP_Depend_Parser_UnexpectedTokenException
     */
    public function testParseExpressionUntilThrowsExceptionForUnclosedStatement()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * Tests to ensure function docblock comments are parsed correctly
     *
     * @return void
     */
    public function testFunctionDocBlockIsCorrectlyParsed()
    {
        $function = self::parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current();

        $this->assertEquals( $function->getDocComment(),
            "/**\n * This is the function docblock for foo\n *\n */" );
    }

    /**
     * Returns an interface instance from the mixed code test file.
     *
     * @return PHP_Depend_Code_Interface
     */
    protected function getInterfaceForTest()
    {
        $packages = self::parseCodeResourceForTest();
        $packages->next();
        $packages->next();

        return $packages->current()->getTypes()->current();
    }

    /**
     * Returns the methods of an interface from the mixed code test file.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    protected function getInterfaceMethodsForTest()
    {
        $packages = self::parseCodeResourceForTest();
        $packages->next();
        $packages->next();

        return $packages->current()
            ->getInterfaces()
            ->current()
            ->getMethods();
    }

    /**
     * Returns a class instance from the mixed code test file.
     *
     * @return PHP_Depend_Code_Class
     */
    protected function getClassForTest()
    {
        $packages = self::parseCodeResourceForTest();
        $packages->next();

        return $packages->current()->getTypes()->current();
    }

    /**
     * Returns the methods of a class from the mixed code test file.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    protected function getClassMethodsForTest()
    {
        $packages = self::parseCodeResourceForTest();
        $packages->next();

        return $packages->current()->getClasses()->current()->getMethods();
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
        $indent = 1
    ) {
        $ws = str_repeat(" ", 4 * $indent);

        $expected = array(
            "/**\n{$ws} * This is one comment.\n{$ws} */",
            null,
            null,
            "/**\n{$ws} * This is a second comment.\n{$ws} */",
        );

        $actual = array();
        foreach ($nodes as $callable) {
            $actual[] = $callable->getDocComment();
        }

        $this->assertEquals($expected, $actual);
    }
}
