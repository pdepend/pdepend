<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
  */

namespace PDepend;

use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTForeachStatement;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTString;
use PDepend\Source\AST\ASTVariable;
use PDepend\Source\AST\State;
use PDepend\Source\Language\PHP\PHPBuilder;
use PDepend\Source\Language\PHP\PHPParserGeneric;
use PDepend\Source\Language\PHP\PHPTokenizerInternal;
use PDepend\Source\Tokenizer\Token;
use PDepend\Source\Tokenizer\Tokens;
use PDepend\Util\Cache\Driver\MemoryCacheDriver;

/**
 * Test case implementation for the PDepend code parser.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @group unittest
 */
class ParserTest extends AbstractTest
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

        $cache   = new MemoryCacheDriver();
        $builder = new PHPBuilder();

        $tokenizer = new PHPTokenizerInternal();
        $tokenizer->setSourceFile(self::createCodeResourceUriForTest());

        $parser = new PHPParserGeneric($tokenizer, $builder, $cache);
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
            'pkg1'                               =>  true,
            'pkg2'                               =>  true,
            'pkg3'                               =>  true,
            \PDepend\Source\Builder\Builder::DEFAULT_NAMESPACE  =>  true
        );

        $tmp = self::parseCodeResourceForTest();
        $namespaces = array();

        $this->assertEquals(4, count($tmp));

        foreach ($tmp as $namespace) {
            $this->assertArrayHasKey($namespace->getName(), $expected);
            unset($expected[$namespace->getName()]);
            $namespaces[$namespace->getName()] = $namespace;
        }
        $this->assertEquals(0, count($expected));

        $this->assertEquals(1, $namespaces['pkg1']->getFunctions()->count());
        $this->assertEquals(1, $namespaces['pkg1']->getTypes()->count());
        $this->assertFalse($namespaces['pkg1']->getTypes()->current()->isAbstract());

        $this->assertEquals(1, $namespaces['pkg2']->getTypes()->count());
        $this->assertTrue($namespaces['pkg2']->getTypes()->current()->isAbstract());

        $this->assertEquals(1, $namespaces['pkg3']->getTypes()->count());
        $this->assertTrue($namespaces['pkg3']->getTypes()->current()->isAbstract());
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
            '\\PDepend\\Source\\Parser\\TokenStreamEndException',
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
            '\\PDepend\\Source\\Parser\\TokenStreamEndException',
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
            '\\RuntimeException',
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
            '\\RuntimeException',
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
        $namespaces = self::parseCodeResourceForTest();
        $functions = $namespaces[1]->getFunctions();

        $this->assertEquals(7, $functions[0]->getStartLine());
    }

    /**
     * Tests that the parser sets the correct tokens for a function.
     *
     * @return void
     */
    public function testParserSetsCorrectFunctionTokens()
    {
        $tokens = array(
            new Token(Tokens::T_FUNCTION, 'function', 5, 5, 1, 8),
            new Token(Tokens::T_STRING, 'foo', 5, 5, 10, 12),
            new Token(Tokens::T_PARENTHESIS_OPEN, '(', 5, 5, 13, 13),
            new Token(Tokens::T_VARIABLE, '$foo', 5, 5, 14, 17),
            new Token(Tokens::T_EQUAL, '=', 5, 5, 19, 19),
            new Token(Tokens::T_ARRAY, 'array', 5, 5, 21, 25),
            new Token(Tokens::T_PARENTHESIS_OPEN, '(', 5, 5, 26, 26),
            new Token(Tokens::T_PARENTHESIS_CLOSE, ')', 5, 5, 27, 27),
            new Token(Tokens::T_PARENTHESIS_CLOSE, ')', 5, 5, 28, 28),
            new Token(Tokens::T_CURLY_BRACE_OPEN, '{', 5, 5, 30, 30),
            new Token(Tokens::T_FOREACH, 'foreach', 6, 6, 5, 11),
            new Token(Tokens::T_PARENTHESIS_OPEN, '(', 6, 6, 13, 13),
            new Token(Tokens::T_VARIABLE, '$foo', 6, 6, 14, 17),
            new Token(Tokens::T_AS, 'as', 6, 6, 19, 20),
            new Token(Tokens::T_VARIABLE, '$bar', 6, 6, 22, 25),
            new Token(Tokens::T_PARENTHESIS_CLOSE, ')', 6, 6, 26, 26),
            new Token(Tokens::T_CURLY_BRACE_OPEN, '{', 6, 6, 28, 28),
            new Token(Tokens::T_STRING, 'FooBar', 7, 7, 9, 14),
            new Token(Tokens::T_DOUBLE_COLON, '::', 7, 7, 15, 16),
            new Token(Tokens::T_STRING, 'y', 7, 7, 17, 17),
            new Token(Tokens::T_PARENTHESIS_OPEN, '(', 7, 7, 18, 18),
            new Token(Tokens::T_VARIABLE, '$bar', 7, 7, 19, 22),
            new Token(Tokens::T_PARENTHESIS_CLOSE, ')', 7, 7, 23, 23),
            new Token(Tokens::T_SEMICOLON, ';', 7, 7, 24, 24),
            new Token(Tokens::T_CURLY_BRACE_CLOSE, '}', 8, 8, 5, 5),
            new Token(Tokens::T_CURLY_BRACE_CLOSE, '}', 9, 9, 1, 1),
        );

        $namespaces = self::parseSource('/Parser/parser-sets-expected-function-tokens.php');
        $functions = $namespaces[0]->getFunctions();

        $this->assertEquals($tokens, $functions[0]->getTokens());
    }

    /**
     * Tests that the parser sets a detected file comment.
     *
     * @return void
     */
    public function testParserSetsCorrectFileComment()
    {
        $namespaces = self::parseCodeResourceForTest();
        $this->assertEquals(1, $namespaces->count()); // default

        $namespace = $namespaces[0];
        $this->assertEquals('default\package', $namespace->getName());

        $class = $namespace->getClasses()->current();
        $this->assertNotNull($class);

        $actual = $class->getCompilationUnit()->getDocComment();
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
        $namespaces = self::parseCodeResourceForTest();
        $this->assertEquals(1, $namespaces->count()); // +global

        $namespace = $namespaces[0];
        $this->assertEquals('+global', $namespace->getName());

        $class = $namespace->getClasses()->current();
        $this->assertNotNull($class);

        $actual = $class->getCompilationUnit()->getDocComment();
        $this->assertNull($actual);
    }

    /**
     * Tests that the parser doesn't reuse a function comment as file comment.
     *
     * @return void
     */
    public function testParserDoesntReuseFunctionComment()
    {
        $namespaces = self::parseCodeResourceForTest();
        $this->assertEquals(1, $namespaces->count()); // +global

        $namespace = $namespaces[0];
        $this->assertEquals('+global', $namespace->getName());

        $function = $namespace->getFunctions()->current();
        $this->assertNotNull($function);

        $actual = $function->getCompilationUnit()->getDocComment();
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

        $this->assertEquals(3, count($class->getInterfaces()));
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

        $this->assertEquals(3, count($class->getInterfaces()));
    }

    /**
     * Tests that the parser sets the correct line number for methods.
     *
     * @return void
     */
    public function testParserSetsCorrectMethodLineNumber()
    {
        $namespaces = self::parseCodeResourceForTest();

        $method = $namespaces[2]
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
        $namespaces = self::parseCodeResourceForTest();
        $function = $namespaces->current()
            ->getFunctions()
            ->current();

        $dependencies = $function->getDependencies();
        $this->assertEquals('PDepend1', $dependencies->current()->getNamespace()->getName());
        $dependencies->next();
        $this->assertEquals('PDepend2', $dependencies->current()->getNamespace()->getName());
    }

    /**
     * Tests that doc comment blocks are added to a function.
     *
     * @return void
     */
    public function testParserSetsCorrectFunctionDocComment()
    {
        $namespaces = self::parseCodeResourceForTest();

        $nodes = $namespaces->current()->getFunctions();
        $this->doTestParserSetsCorrectDocComment($nodes, 0);
    }

    /**
     * Tests that the parser sets the correct function return type.
     *
     * @return \PDepend\Source\AST\ASTFunction[]
     */
    public function testParserSetsCorrectFunctionReturnType()
    {
        $namespaces = self::parseCodeResourceForTest();

        $functions = $namespaces[0]->getFunctions();
        $this->assertEquals(3, count($functions));

        return $functions;
    }

    /**
     * @param \PDepend\Source\AST\ASTFunction[] $functions
     * @return void
     * @depends PDepend\ParserTest::testParserSetsCorrectFunctionReturnType
     */
    public function testParserSetsFunctionReturnTypeToNull($functions)
    {
        $this->assertSame(
            array(
                'function' => 'func1',
                'returnClass' => null
            ),
            array(
                'function' => $functions[0]->getName(),
                'returnClass' => $functions[0]->getReturnClass()
            )
        );
    }

    /**
     * @param \PDepend\Source\AST\ASTFunction[] $functions
     * @return void
     * @depends PDepend\ParserTest::testParserSetsCorrectFunctionReturnType
     */
    public function testParserSetsExpectedFunctionReturnTypeOfFunctionTwo($functions)
    {
        $this->assertSame(
            array(
                'function' => 'func2',
                'returnClass' => 'SplObjectStore'
            ),
            array(
                'function' => $functions[1]->getName(),
                'returnClass' => $functions[1]->getReturnClass()->getName()
            )
        );
    }

    /**
     * @param \PDepend\Source\AST\ASTFunction[] $functions
     * @return void
     * @depends PDepend\ParserTest::testParserSetsCorrectFunctionReturnType
     */
    public function testParserSetsExpectedFunctionReturnTypeOfFunctionThree($functions)
    {
        $this->assertSame(
            array(
                'function' => 'func3',
                'returnClass' => 'SplObjectStore'
            ),
            array(
                'function' => $functions[2]->getName(),
                'returnClass' => $functions[2]->getReturnClass()->getName()
            )
        );
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

        $this->assertEquals(
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

        $this->assertSame(array(0, null, 0, null, 0, null), $actual);
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

        $this->assertEquals(
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

        $this->assertEquals(
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
        $methods = self::parseCodeResourceForTest(true)
            ->current()
            ->getTypes()
            ->current()
            ->getMethods();

        $actual = array();
        foreach ($methods as $method) {
            $actual[] = $method->getExceptionClasses()->count();
            $actual[] = $method->getReturnClass();
        }

        $this->assertSame(array(0, null, 0, null, 0, null), $actual);
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

        $this->assertEquals(
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

        $this->assertEquals(
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

        $this->assertEquals('Runtime', $class->getName());
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

        $this->assertEquals('Session', $type->getName());
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

        $this->assertEquals(array(null, null, null, null, null, null), $actual);
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

        $namespaces = self::parseCodeResourceForTest();
        foreach ($namespaces[0]->getTypes() as $type) {
            $actual[] = $type->getDocComment();
        }

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the parser supports sub packages.
     *
     * @return void
     */
    public function testParserSubpackageSupport()
    {
        $namespace = self::parseCodeResourceForTest()->current();
        $this->assertEquals('PHP\Depend', $namespace->getName());
    }

    /**
     * Tests that the parser supports sub packages.
     *
     * @return \PDepend\Source\AST\ASTNamespace[]
     */
    public function testParserSetsFileLevelFunctionPackage()
    {
        $namespaces = self::parseCodeResourceForTest();

        $this->assertEquals(2, count($namespaces));

        return $namespaces;
    }

    /**
     * @param \PDepend\Source\AST\ASTNamespace[] $namespaces
     * @return void
     * @depends PDepend\ParserTest::testParserSetsFileLevelFunctionPackage
     */
    public function testParserSetsFileLevelFunctionPackageNumberOfFunctionsInFirstNamespace($namespaces)
    {
        $functions = $namespaces[0]->getFunctions();
        $this->assertEquals(2, count($functions));
    }

    /**
     * @param \PDepend\Source\AST\ASTNamespace[] $namespaces
     * @return void
     * @depends PDepend\ParserTest::testParserSetsFileLevelFunctionPackage
     */
    public function testParserSetsFileLevelFunctionPackageNumberOfFunctionsInSecondNamespace($namespaces)
    {
        $functions = $namespaces[1]->getFunctions();
        $this->assertEquals(1, count($functions));
    }

    /**
     * @param \PDepend\Source\AST\ASTNamespace[] $namespaces
     * @return void
     * @depends PDepend\ParserTest::testParserSetsFileLevelFunctionPackage
     */
    public function testParserSetsFileExpectedPackageForFirstFunctionInFirstNamespace($namespaces)
    {
        $functions = $namespaces[0]->getFunctions();

        $this->assertEquals('PHP\\Depend', $functions[0]->getNamespace()->getName());
    }

    /**
     * @param \PDepend\Source\AST\ASTNamespace[] $namespaces
     * @return void
     * @depends PDepend\ParserTest::testParserSetsFileLevelFunctionPackage
     */
    public function testParserSetsFileExpectedPackageForSecondFunctionInFirstNamespace($namespaces)
    {
        $functions = $namespaces[0]->getFunctions();

        $this->assertEquals('PHP\\Depend', $functions[1]->getNamespace()->getName());
    }

    /**
     * @param \PDepend\Source\AST\ASTNamespace[] $namespaces
     * @return void
     * @depends PDepend\ParserTest::testParserSetsFileLevelFunctionPackage
     */
    public function testParserSetsFileExpectedPackageForFirstFunctionInSecondNamespace($namespaces)
    {
        $functions = $namespaces[1]->getFunctions();

        $this->assertEquals('PDepend\\Test', $functions[0]->getNamespace()->getName());
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

        $this->assertTrue($class->isAbstract());
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

        $this->assertSame(
            State::IS_EXPLICIT_ABSTRACT,
            $class->getModifiers() & State::IS_EXPLICIT_ABSTRACT
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

        $this->assertTrue($class->isFinal());
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

        $this->assertSame(State::IS_FINAL, $class->getModifiers() & State::IS_FINAL
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

        $foreach = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTForeachStatement');
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
            '\\PDepend\\Source\\Parser\\UnexpectedTokenException',
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
            '\\PDepend\\Source\\Parser\\UnexpectedTokenException',
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
            '\\PDepend\\Source\\Parser\\UnexpectedTokenException',
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
            '\\PDepend\\Source\\Parser\\UnexpectedTokenException',
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
            '\\PDepend\\Source\\Parser\\UnexpectedTokenException',
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
        $parameters = $this->getFirstClassMethodForTestCase()->getParameters();
        $this->assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the parser handles the self keyword as parameter type hint.
     *
     * @return void
     */
    public function testParserHandlesSelfKeywordAsParameterTypeHint()
    {
        $parameters = $this->getFirstClassMethodForTestCase()->getParameters();
        $this->assertNotNull($parameters[0]);
    }

    /**
     * testParserSetsBestMatchForParameterTypeHintEvenWhenNameEquals
     *
     * @return void
     */
    public function testParserSetsBestMatchForParameterTypeHintEvenWhenNameEquals()
    {
        $namespaces = $this->parseCodeResourceForTest();
        $classes = $namespaces[0]->getClasses();
        $methods = $classes[1]->getMethods();
        $parameters = $methods[0]->getParameters();

        $this->assertSame($classes[0], $parameters[0]->getClass());
        $this->assertNotSame($classes[1], $parameters[0]->getClass());
    }

    /**
     * Tests that the parser translates the self keyword into the same instance,
     * even when a similar class exists.
     *
     * @return void
     */
    public function testParserSetsTheReallySameParameterHintInstanceForKeywordSelf()
    {
        $class = $this->getFirstClassForTestCase();
        $methods = $class->getMethods();
        $parameters = $methods[0]->getParameters();

        $this->assertSame($class, $parameters[0]->getClass());
    }

    /**
     * testParserStripsLeadingSlashFromNamespacedClassName
     *
     * @return void
     */
    public function testParserStripsLeadingSlashFromNamespacedClassName()
    {
        $namespace = self::parseCodeResourceForTest()->current();
        $this->assertEquals('foo', $namespace->getName());
    }

    /**
     * testParserStripsLeadingSlashFromNamespacedClassName
     *
     * @return void
     */
    public function testParserStripsLeadingSlashFromNamespaceAliasedClassName()
    {
        $namespace = $this->getFirstClassForTestCase()
            ->getParentClass()
            ->getNamespace();

        $this->assertEquals('foo\bar\baz', $namespace->getName());
    }

    /**
     * testParserStripsLeadingSlashFromInheritNamespacedClassName
     *
     * @return void
     */
    public function testParserStripsLeadingSlashFromInheritNamespacedClassName()
    {
        $namespace = $this->getFirstClassForTestCase()
            ->getParentClass()
            ->getNamespace();

        $this->assertEquals('bar', $namespace->getName());
    }

    /**
     * testParserThrowsExpectedExceptionWhenDefaultStaticDefaultValueNotExists
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\MissingValueException
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

        $string = $function->getFirstChildOfType('PDepend\\Source\\AST\\ASTString');
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

        $string = $function->getFirstChildOfType('PDepend\\Source\\AST\\ASTString');
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

        $string   = $function->getFirstChildOfType('PDepend\\Source\\AST\\ASTString');
        $variable = $string->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $variable);
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

        $string   = $function->getFirstChildOfType('PDepend\\Source\\AST\\ASTString');
        $variable = $string->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $variable);
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

        $string = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTString');
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTLiteral', $string->getChild(1));
    }

    /**
     * testParserStopsProcessingWhenCacheContainsValidResult
     *
     * @return void
     */
    public function testParserStopsProcessingWhenCacheContainsValidResult()
    {
        $builder = $this->getMock('\\PDepend\\Source\\Builder\\Builder');

        $tokenizer = new PHPTokenizerInternal();
        $tokenizer->setSourceFile(__FILE__);

        $cache = $this->createCacheFixture();
        $cache->expects($this->once())
            ->method('restore')
            ->will(self::returnValue(true));
        $cache->expects($this->never())
            ->method('store');

        $parser = new PHPParserGeneric(
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
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
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
     * @return \PDepend\Source\AST\ASTInterface
     */
    protected function getInterfaceForTest()
    {
        $namespaces = self::parseCodeResourceForTest();

        return $namespaces[2]->getTypes()->current();
    }

    /**
     * Returns the methods of an interface from the mixed code test file.
     *
     * @return \PDepend\Source\AST\ASTMethod[]
     */
    protected function getInterfaceMethodsForTest()
    {
        $namespaces = self::parseCodeResourceForTest();

        return $namespaces[2]
            ->getInterfaces()
            ->current()
            ->getMethods();
    }

    /**
     * Returns a class instance from the mixed code test file.
     *
     * @return \PDepend\Source\AST\ASTClass
     */
    protected function getClassForTest()
    {
        $namespaces = self::parseCodeResourceForTest();
        return $namespaces[1]->getTypes()->current();
    }

    /**
     * Returns the methods of a class from the mixed code test file.
     *
     * @return \PDepend\Source\AST\ASTMethod[]
     */
    protected function getClassMethodsForTest()
    {
        $namespaces = self::parseCodeResourceForTest();
        return $namespaces[1]->getClasses()->current()->getMethods();
    }

    /**
     * Generic comment test method.
     *
     * @param \PDepend\Source\AST\ASTArtifactList $nodes
     * @param integer $indent
     * @return void
     */
    protected function doTestParserSetsCorrectDocComment(ASTArtifactList $nodes, $indent = 1)
    {
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
