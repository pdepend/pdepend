<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

require_once 'PHP/Depend/Parser/VersionAllParser.php';
require_once 'PHP/Depend/Builder/Default.php';
require_once 'PHP/Depend/Tokenizer/Internal.php';

/**
 * Test case implementation for the PHP_Depend code parser.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_Depend_ParserTest extends PHP_Depend_AbstractTest
{
    /**
     * testParserHandlesMaxNestingLevel
     * 
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParseMixedCode()
    {
        $expected = array(
            'pkg1'                                =>  true,
            'pkg2'                                =>  true,
            'pkg3'                                =>  true,
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserWithUnclosedClassFail()
    {
        $sourceFile = dirname(__FILE__) . '/_code/not_closed_class.txt';
        $this->setExpectedException(
            'PHP_Depend_Parser_TokenStreamEndException',
            "Unexpected end of token stream in file: {$sourceFile}."
        );

        self::parseSource($sourceFile);
    }

    /**
     * Tests that the parser throws an exception if it reaches the end of the
     * stream but not all function curly braces are closed.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserWithUnclosedFunctionFail()
    {
        $sourceFile = dirname(__FILE__) . '/_code/not_closed_function.txt';
        $this->setExpectedException(
            'PHP_Depend_Parser_TokenStreamEndException',
            'Unexpected end of token stream in file: '
        );

        self::parseSource($sourceFile);
    }

    /**
     * Tests that the parser throws an exception if it finds an invalid
     * function signature.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserWithInvalidFunction1Fail()
    {
        $this->setExpectedException(
            'RuntimeException',
            'Unexpected token: (, line: 3, col: 23, file: '
        );

        self::parseSource('invalid_function1.txt');
    }

    /**
     * Tests that the parser throws an exception if it finds an invalid
     * function signature.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserWithInvalidFunction2Fail()
    {
        $this->setExpectedException(
            'RuntimeException',
            "Unexpected token: Bar, line: 3, col: 18, file: "
        );

        self::parseSource('invalid_function2.txt');
    }

    /**
     * Test case for parser bug 01 that doesn't add dependencies for static
     * method calls.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserStaticCallBug01()
    {
        $packages = self::parseSource('bugs/001.php');
        $this->assertEquals(1, $packages->count());

        $package = $packages->current();
        $this->assertEquals('package0', $package->getName());
        $classes = $package->getTypes();
        $this->assertEquals(1, $classes->count());
        $methods = $classes->current()->getMethods();
        $this->assertEquals(1, $methods->count());
        $this->assertEquals(1, $methods->current()->getDependencies()->count());
    }

    /**
     * Tests that the parser sets the correct line number for a function.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserDoesntReuseTypeComment()
    {
        $packages = self::parseSource('comments/constant.php');
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserDoesntReuseFunctionComment()
    {
        $packages = self::parseSource('comments/function.php');
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserSetsCorrectClassStartLineNumber()
    {
        $this->assertEquals(30, $this->getMixedCodeClass()->getStartLine());
    }

    /**
     * Tests that the parser sets the correct end line number for a class.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserSetsCorrectClassEndLineNumber()
    {
        $this->assertEquals(49, $this->getMixedCodeClass()->getEndLine());
    }

    /**
     * Tests that the parser sets the correct start line number for class methods.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserSetsCorrectInterfaceStartLineNumber()
    {
        $this->assertEquals(15, $this->getMixedCodeInterface()->getStartLine());
    }

    /**
     * Tests that the parser sets the correct end line number for an interface.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserSetsAllInterfaceMethodsAbstract()
    {
        $methods = $this->getMixedCodeInterfaceMethods();
        $this->assertTrue($methods->current()->isAbstract());
    }

    /**
     * testParserHandlesClassWithMultipleImplementedInterfaces
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserSetsCorrectFunctionReturnType()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/comments/function1.php');

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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserSetsCorrectPropertyVisibility()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/comments/property.php');

        $nodes = $packages->current()
                          ->getTypes()
                          ->current()
                          ->getProperties();

        $this->assertTrue($nodes->current()->isPrivate());
        $nodes->next();
        $this->assertTrue($nodes->current()->isPublic());
        $nodes->next();
        $this->assertTrue($nodes->current()->isProtected());
        $nodes->next();
        $this->assertTrue($nodes->current()->isProtected());
    }

    /**
     * Tests that the parser sets property types for non scalar properties.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserSetsCorrectPropertyTypes()
    {
        $nodes = self::parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current()
            ->getProperties();

        $this->assertEquals('$property1', $nodes->current()->getName());
        $this->assertEquals('MyPropertyClass2', $nodes->current()->getClass()->getName());
        $nodes->next();
        $this->assertEquals('$property2', $nodes->current()->getName());
        $this->assertEquals('MyPropertyClass2', $nodes->current()->getClass()->getName());
        $nodes->next();
        $this->assertEquals('$property3', $nodes->current()->getName());
        $this->assertEquals('MyPropertyClass2', $nodes->current()->getClass()->getName());
        $nodes->next();
        $this->assertEquals('$property4', $nodes->current()->getName());
        $this->assertEquals('MyPropertyClass2', $nodes->current()->getClass()->getName());
        $nodes->next();
        $this->assertEquals('$property5', $nodes->current()->getName());
        $this->assertNull($nodes->current()->getClass());
        $nodes->next();
        $this->assertEquals('$property6', $nodes->current()->getName());
        $this->assertNull($nodes->current()->getClass());
    }

    /**
     * Tests that the parser recognizes the first type defined in a doc comment.
     *
     * <code>
     *   @var false|null|Runtime
     *
     *   // Results in
     *   Runtime
     * </code>
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserSetsExpectedPropertyTypeForChainedComment()
    {
        $packages = self::parseCodeResourceForTest();

        $class = $packages->current()->getTypes()->current();
        self::assertEquals('Parser', $class->getName());

        $property = $class->getProperties()->current();
        self::assertEquals('Runtime', $property->getClass()->getName());
    }

    /**
     * Tests that the parser recognizes the first type defined in a doc comment.
     *
     * <code>
     *   @var array(Session|Runtime)
     *
     *   // Results in
     *   Session
     * </code>
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     *   @return false|null|Runtime
     *
     *   // Results in
     *   Runtime
     * </code>
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserSetsExpectedReturnTypeForChainedComment()
    {
        $packages = self::parseCodeResourceForTest();

        $class = $packages->current()->getTypes()->current();
        $this->assertType('PHP_Depend_Code_Class', $class);
        $this->assertSame('Parser', $class->getName());

        $method = $class->getMethods()->current();
        $this->assertType('PHP_Depend_Code_Method', $method);

        $type = $method->getReturnClass();
        $this->assertType('PHP_Depend_Code_Class', $type);
        $this->assertSame('Runtime', $type->getName());
    }

    /**
     * Tests that the parser recognizes the first type defined in a doc comment.
     *
     * <code>
     *   @return array(integer => null|Session|Runtime)
     *
     *   // Results in
     *   Session
     * </code>
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserSetsExpectedReturnTypeForChainedCommentInArray()
    {
        $packages = self::parseCodeResourceForTest();

        $class = $packages->current()->getTypes()->current();
        $this->assertType('PHP_Depend_Code_Class', $class);
        $this->assertSame('Parser', $class->getName());

        $method = $class->getMethods()->current();
        $this->assertType('PHP_Depend_Code_Method', $method);

        $type = $method->getReturnClass();
        $this->assertSame('Session', $type->getName());
    }

    /**
     * Tests that the parser sets property types for non scalar properties.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserSetsCorrectClassOrInterfaceDocComment()
    {
        $expected = array(
            "/**\n * Sample comment.\n */",
            null,
            null,
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser::_getNamespaceOrPackageName
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser::_getNamespaceOrPackageName
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser::_parseQualifiedName
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserHandlesDoubleQuoteStringAsConstantDefaultValue()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParserHandlesDoubleQuoteStringWithEscapedVariable
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserNotHandlesDoubleQuoteStringWithVariableAndParenthesisAsFunctionCall()
    {
        $function = self::parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current();

        $string   = $function->getFirstChildOfType(PHP_Depend_Code_ASTString::CLAZZ);
        $variable = $string->getChild(0);

        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $variable);
    }

    /**
     * testParserNotHandlesDoubleQuoteStringWithVariableAndEqualAsAssignment
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserNotHandlesDoubleQuoteStringWithVariableAndEqualAsAssignment()
    {
        $function = self::parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current();

        $string   = $function->getFirstChildOfType(PHP_Depend_Code_ASTString::CLAZZ);
        $variable = $string->getChild(0);

        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $variable);
    }

    /**
     * testParserHandlesStringWithQuestionMarkNotAsTernaryOperator
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
        $this->assertType(PHP_Depend_Code_ASTLiteral::CLAZZ, $string->getChild(1));
    }

    /**
     * testParserStopsProcessingWhenCacheContainsValidResult
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
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
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParseClosureAsFunctionArgument()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParseNowdocInMethodBody
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParseNowdocInMethodBody()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParseDoWhileStatement
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParseDoWhileStatement()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParserHandlesCompoundExpressionInArrayBrackets
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserHandlesCompoundExpressionInArrayBrackets()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParserHandlesEmptyNonePhpCodeInMethodBody
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserHandlesEmptyNonePhpCodeInMethodBody()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParserHandlesPhpCloseTagInMethodBody
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserHandlesPhpCloseTagInMethodBody()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParserHandlesMultiplePhpCloseTagsInMethodBody
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserHandlesMultiplePhpCloseTagsInMethodBody()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParseExpressionUntilThrowsExceptionForUnclosedStatement
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     * @expectedException PHP_Depend_Parser_UnexpectedTokenException
     */
    public function testParseExpressionUntilThrowsExceptionForUnclosedStatement()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * Tests that the parser ignores variable class instantiation.
     *
     * http://bugs.xplib.de/index.php?do=details&task_id=10&project=3
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testVariableClassNameBug10()
    {
        $package = self::parseSource(dirname(__FILE__) . '/_code/bugs/006.php')->current();
        $class   = $package->getClasses()->current();
        $method  = $class->getMethods()->current();

        $this->assertEquals('package10', $package->getName());
        $this->assertEquals('VariableClassNamesBug10', $class->getName());
        $this->assertEquals('foo10', $method->getName());
        $this->assertEquals(0, count($method->getDependencies()));
    }

    /**
     * testParserCurlyBraceBug11
     *
     * @return void
     * @covers stdClass
     * @group pdepend
     * @group pdepend::parser
     * @group regressiontest
     */
    public function testParserCurlyBraceBug11()
    {
        $package   = self::parseSource(dirname(__FILE__) . '/_code/bugs/007.php')->current();
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
     * @covers stdClass
     * @group pdepend
     * @group pdepend::parser
     * @group regressiontest
     */
    public function testParserCurlyBraceBug12()
    {
        $package = self::parseSource(dirname(__FILE__) . '/_code/bugs/008.php')->current();
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
     * @covers stdClass
     * @group pdepend
     * @group pdepend::parser
     * @group regressiontest
     */
    public function testParserBacktickExpressionBug15()
    {
        $package = self::parseSource(dirname(__FILE__) . '/_code/bugs/015.php')->current();
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
     * @covers stdClass
     * @group pdepend
     * @group pdepend::parser
     * @group regressiontest
     */
    public function testParserSetsCorrectTypeTokensIssue30()
    {
        $packages = self::parseSource('bugs/030.php');
        $this->assertEquals(1, $packages->count());

        $testClass = $packages->current()
            ->getClasses()
            ->current();

        $expected = array(
            array(PHP_Depend_TokenizerI::T_ABSTRACT, 2),
            array(PHP_Depend_TokenizerI::T_CLASS, 2),
            array(PHP_Depend_TokenizerI::T_STRING, 2),
            array(PHP_Depend_TokenizerI::T_EXTENDS, 2),
            array(PHP_Depend_TokenizerI::T_STRING, 2),
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

        $actual = array();
        foreach ($testClass->getTokens() as $token) {
            $actual[] = array($token->type, $token->startLine);
        }
        $this->assertSame($expected, $actual);
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
     * @covers stdClass
     * @group pdepend
     * @group pdepend::parser
     * @group regressiontest
     */
    public function testParserDetectsTypeWithinInstanceOfOperatorIssue16()
    {
        $packages = self::parseSource('bugs/016-1.php');
        $this->assertEquals(1, $packages->count()); // +global

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
     * @covers stdClass
     * @group pdepend
     * @group pdepend::parser
     * @group regressiontest
     * @todo TODO: It would be a cool feature if PHP_Depend would replace such
     *             combinations (T_VARIABLE = T_CONSTANT_ENCAPSED_STRING with
     *             T_INSTANCEOF + T_VARIABLE).
     */
    public function testParserIgnoresDynamicInstanceOfOperatorIssue16()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/bugs/016-2.php');
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
     * @covers stdClass
     * @group pdepend
     * @group pdepend::parser
     * @group regressiontest
     */
    public function testParserDetectsTypeWithinCatchBlockIssue17()
    {
        $packages = self::parseSource('bugs/017-1.php');
        $this->assertEquals(1, $packages->count()); // +global

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
     * @covers stdClass
     * @group pdepend
     * @group pdepend::parser
     * @group regressiontest
     */
    public function testParserDetectsOnlyTypeHintsWithinTheFunctionSignatureBug33()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/bugs/033-1.php');
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
     * @covers stdClass
     * @group pdepend
     * @group pdepend::parser
     * @group regressiontest
     */
    public function testParserDetectsOnlyTypeHintsWithinTheMethodSignatureBug33()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/_code/bugs/033-2.php');
        $this->assertEquals(1, $packages->count()); // +global

        $class = $packages->current()
            ->getClasses()
            ->current();
            
        $this->assertEquals('PHP_Depend_Parser', $class->getName());

        $method = $class->getMethods()->current();
        $this->assertEquals('parse', $method->getName());

        $this->assertEquals(1, $method->getDependencies()->count());
    }

    /**
     * Tests that the parser sets the source file of an interface method.
     *
     * @return void
     * @covers stdClass
     * @group pdepend
     * @group pdepend::parser
     * @group regressiontest
     */
    public function testParserSetsSourceFileForInterfaceMethodBug89()
    {
        $fileName = dirname(__FILE__) . '/_code/bugs/059-003-function-source-file.php';

        $packages = self::parseSource($fileName);
        $this->assertEquals(1, $packages->count()); // +global

        $interface = $packages->current()->getInterfaces()->current();
        $this->assertType('PHP_Depend_Code_Interface', $interface);

        $method = $interface->getMethods()->current();
        $this->assertType('PHP_Depend_Code_Method', $method);

        $sourceFile = $method->getSourceFile();
        self::assertEquals($fileName, $sourceFile->getFileName());
    }

    /**
     * Tests that the parser sets the source file of a class method.
     *
     * @return void
     * @covers stdClass
     * @group pdepend
     * @group pdepend::parser
     * @group regressiontest
     */
    public function testParserSetsSourceFileForClassMethodBug89()
    {
        $fileName = dirname(__FILE__) . '/_code/bugs/059-004-function-source-file.php';

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
     * @covers stdClass
     * @group pdepend
     * @group pdepend::parser
     * @group regressiontest
     */
    public function testParserSetsSourceFileForClassPropertyBug89()
    {
        $fileName = dirname(__FILE__) . '/_code/bugs/059-005-property-source-file.php';

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
     * Tests that parser handles a php 5.3 static method call correct.
     *
     * <code>
     * PHP\Depend\Parser::call();
     * </code>
     *
     * @return void
     * @covers stdClass
     * @group pdepend
     * @group pdepend::parser
     * @group regressiontest
     */
    public function testParserHandlesStaticMethodCallInFunctionBodyBug69()
    {
        $packages = self::parseSource('bugs/069-1-static-expression.php');
        $function = $packages->current()
                             ->getFunctions()
                             ->current();

        $package = $function->getDependencies()
                            ->current()
                            ->getPackage();

        $this->assertSame('PHP\Depend', $package->getName());
    }

    /**
     * Tests that parser handles a php 5.3 static method call correct.
     *
     * <code>
     * \PHP\Depend\Parser::call();
     * </code>
     *
     * @return void
     * @covers stdClass
     * @group pdepend
     * @group pdepend::parser
     * @group regressiontest
     */
    public function testParserHandlesStaticMethodLeadingBackslashCallInFunctionBodyBug69()
    {
        $packages = self::parseSource('bugs/069-2-static-expression.php');
        $function = $packages->current()
                             ->getFunctions()
                             ->current();

        $package = $function->getDependencies()
                            ->current()
                            ->getPackage();

        $this->assertSame('PHP\Depend', $package->getName());
    }

    /**
     * Tests that parser does not handle a php 5.3 function call as dependency.
     *
     * <code>
     * \PHP\Depend\Parser\call();
     * </code>
     *
     * @return void
     * @covers stdClass
     * @group pdepend
     * @group pdepend::parser
     * @group regressiontest
     */
    public function testParserDoesNotHandleQualifiedFunctionCallAsDependencyInFunctionBodyBug69()
    {
        $packages = self::parseSource('bugs/069-3-static-expression.php');
        $function = $packages->current()
                             ->getFunctions()
                             ->current();

        $this->assertSame(0, $function->getDependencies()->count());
    }

    /**
     * Tests that parser handles a php 5.3 property access as dependency.
     *
     * <code>
     * \PHP\Depend\Parser::$prop;
     * </code>
     *
     * @return void
     * @covers stdClass
     * @group pdepend
     * @group pdepend::parser
     * @group regressiontest
     */
    public function testParserHandlesQualifiedPropertyAccessAsDependencyInFunctionBodyBug69()
    {
        $packages = self::parseSource('bugs/069-4-static-expression.php');
        $function = $packages->current()
                             ->getFunctions()
                             ->current();

        $package = $function->getDependencies()
                            ->current()
                            ->getPackage();

        $this->assertSame('PHP\Depend', $package->getName());
    }

    /**
     * Tests that parser handles a php 5.3 constant access as dependency.
     *
     * <code>
     * \PHP\Depend\Parser::CONSTANT;
     * </code>
     *
     * @return void
     * @covers stdClass
     * @group pdepend
     * @group pdepend::parser
     * @group regressiontest
     */
    public function testParserHandlesQualifiedConstantAccessAsDependencyInFunctionBodyBug69()
    {
        $packages = self::parseSource('bugs/069-5-static-expression.php');
        $function = $packages->current()
                             ->getFunctions()
                             ->current();

        $package = $function->getDependencies()
                            ->current()
                            ->getPackage();

        $this->assertSame('PHP\Depend', $package->getName());
    }

    /**
     * Returns all packages in the mixed code example.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    protected function parseMixedCode()
    {
        return self::parseSource('mixed_code.php');
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
