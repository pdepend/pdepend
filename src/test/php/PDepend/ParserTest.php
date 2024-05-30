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

use PDepend\Source\AST\ASTArtifact;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTForeachStatement;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\AST\ASTParentReference;
use PDepend\Source\AST\ASTString;
use PDepend\Source\AST\ASTVariable;
use PDepend\Source\AST\State;
use PDepend\Source\Builder\Builder;
use PDepend\Source\Language\PHP\PHPBuilder;
use PDepend\Source\Language\PHP\PHPParserGeneric;
use PDepend\Source\Language\PHP\PHPTokenizerInternal;
use PDepend\Source\Parser\MissingValueException;
use PDepend\Source\Parser\TokenStreamEndException;
use PDepend\Source\Parser\UnexpectedTokenException;
use PDepend\Source\Tokenizer\Token;
use PDepend\Source\Tokenizer\Tokens;
use PDepend\Util\Cache\Driver\MemoryCacheDriver;
use RuntimeException;

/**
 * Test case implementation for the PDepend code parser.
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @group unittest
 */
class ParserTest extends AbstractTestCase
{
    /**
     * testParserHandlesMaxNestingLevel
     */
    public function testParserHandlesMaxNestingLevel(): void
    {
        ini_set('xdebug.max_nesting_level', '100');

        $cache = new MemoryCacheDriver();
        $builder = new PHPBuilder();

        $tokenizer = new PHPTokenizerInternal();
        $tokenizer->setSourceFile($this->createCodeResourceUriForTest());

        $parser = new PHPParserGeneric($tokenizer, $builder, $cache);
        $parser->setMaxNestingLevel(512);
        $parser->parse();
    }

    /**
     * Tests the main parse method.
     */
    public function testParseMixedCode(): void
    {
        $expected = [
            'pkg1' => true,
            'pkg2' => true,
            'pkg3' => true,
            Builder::DEFAULT_NAMESPACE => true,
        ];

        $tmp = $this->parseCodeResourceForTest();
        $namespaces = [];

        static::assertCount(4, $tmp);

        foreach ($tmp as $namespace) {
            static::assertArrayHasKey($namespace->getImage(), $expected);
            unset($expected[$namespace->getImage()]);
            $namespaces[$namespace->getImage()] = $namespace;
        }
        static::assertCount(0, $expected);

        static::assertSame(1, $namespaces['pkg1']->getFunctions()->count());
        static::assertSame(1, $namespaces['pkg1']->getTypes()->count());
        static::assertFalse($namespaces['pkg1']->getTypes()->current()->isAbstract());

        static::assertSame(1, $namespaces['pkg2']->getTypes()->count());
        static::assertTrue($namespaces['pkg2']->getTypes()->current()->isAbstract());
        static::assertSame(
            123456781234567812345678,
            $namespaces['pkg2']->getTypes()->current()->getConstant('BIZ')
        );
        static::assertSame(
            0x12345678123456781,
            $namespaces['pkg2']->getTypes()->current()->getConstant('FOOBAR')
        );

        static::assertSame(1, $namespaces['pkg3']->getTypes()->count());
        static::assertTrue($namespaces['pkg3']->getTypes()->current()->isAbstract());
    }

    /**
     * Tests that the parser throws an exception if it reaches the end of the
     * stream but not all class curly braces are closed.
     */
    public function testParserWithUnclosedClassFail(): void
    {
        $sourceFile = $this->createCodeResourceUriForTest();
        $this->expectException(
            TokenStreamEndException::class
        );
        $this->expectExceptionMessage(
            "Unexpected end of token stream in file: {$sourceFile}."
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that the parser throws an exception if it reaches the end of the
     * stream but not all function curly braces are closed.
     */
    public function testParserWithUnclosedFunctionFail(): void
    {
        $this->expectException(
            TokenStreamEndException::class
        );
        $this->expectExceptionMessage(
            'Unexpected end of token stream in file: '
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that the parser throws an exception if it finds an invalid
     * function signature.
     */
    public function testParserWithInvalidFunction1Fail(): void
    {
        $this->expectException(
            RuntimeException::class
        );
        $this->expectExceptionMessage(
            'Unexpected token: (, line: 3, col: 23, file: '
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that the parser throws an exception if it finds an invalid
     * function signature.
     */
    public function testParserWithInvalidFunction2Fail(): void
    {
        $this->expectException(
            RuntimeException::class
        );
        $this->expectExceptionMessage(
            'Unexpected token: Bar, line: 3, col: 18, file: '
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that the parser sets the correct line number for a function.
     */
    public function testParserSetsCorrectFunctionLineNumber(): void
    {
        $namespaces = $this->parseCodeResourceForTest();
        $functions = $namespaces[1]->getFunctions();

        static::assertEquals(7, $functions[0]->getStartLine());
    }

    /**
     * Tests that the parser sets the correct tokens for a function.
     */
    public function testParserSetsCorrectFunctionTokens(): void
    {
        $tokens = [
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
        ];

        $namespaces = $this->parseSource('/Parser/parser-sets-expected-function-tokens.php');
        $functions = $namespaces[0]->getFunctions();

        static::assertEquals($tokens, $functions[0]->getTokens());
    }

    /**
     * Tests that the parser sets a detected file comment.
     */
    public function testParserSetsCorrectFileComment(): void
    {
        $namespaces = $this->parseCodeResourceForTest();
        static::assertEquals(1, $namespaces->count()); // default

        $namespace = $namespaces[0];
        static::assertEquals('default\package', $namespace->getImage());

        $class = $namespace->getClasses()->current();
        static::assertNotNull($class);

        $actual = $class->getCompilationUnit()?->getComment();
        static::assertNotNull($actual);

        $expected = "/**\n"
                  . " * FANOUT := 12\n"
                  . " * CALLS  := 10\n"
                  . " *\n"
                  . " * @package default\n"
                  . " * @subpackage package\n"
                  . ' */';

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the parser doesn't reuse a type comment as file comment.
     */
    public function testParserDoesntReuseTypeComment(): void
    {
        $namespaces = $this->parseCodeResourceForTest();
        static::assertEquals(1, $namespaces->count()); // +global

        $namespace = $namespaces[0];
        static::assertEquals('+global', $namespace->getImage());

        $class = $namespace->getClasses()->current();
        static::assertNotNull($class);

        $actual = $class->getCompilationUnit()?->getComment();
        static::assertNull($actual);
    }

    /**
     * Tests that the parser doesn't reuse a function comment as file comment.
     */
    public function testParserDoesntReuseFunctionComment(): void
    {
        $namespaces = $this->parseCodeResourceForTest();
        static::assertEquals(1, $namespaces->count()); // +global

        $namespace = $namespaces[0];
        static::assertEquals('+global', $namespace->getImage());

        $function = $namespace->getFunctions()->current();
        static::assertNotNull($function);

        $actual = $function->getCompilationUnit()?->getComment();
        static::assertNull($actual);
    }

    /**
     * Tests that the parser sets the correct start line number for a class.
     */
    public function testParserSetsCorrectClassStartLineNumber(): void
    {
        static::assertEquals(30, $this->getClassForTest()->getStartLine());
    }

    /**
     * Tests that the parser sets the correct end line number for a class.
     */
    public function testParserSetsCorrectClassEndLineNumber(): void
    {
        static::assertEquals(49, $this->getClassForTest()->getEndLine());
    }

    /**
     * Tests that the parser sets the correct start line number for class methods.
     */
    public function testParserSetsCorrectClassMethodStartLineNumber(): void
    {
        $methods = $this->getClassMethodsForTest();

        static::assertEquals(43, $methods->current()->getStartLine());
        $methods->next();
        static::assertEquals(44, $methods->current()->getStartLine());
    }

    /**
     * Tests that the parser sets the correct end line number for class methods.
     */
    public function testParserSetsCorrectClassMethodEndLineNumber(): void
    {
        $methods = $this->getClassMethodsForTest();

        static::assertEquals(43, $methods->current()->getEndLine());
        $methods->next();
        static::assertEquals(48, $methods->current()->getEndLine());
    }

    /**
     * Tests that the parser sets the correct start line number for an interface.
     */
    public function testParserSetsCorrectInterfaceStartLineNumber(): void
    {
        static::assertEquals(15, $this->getInterfaceForTest()->getStartLine());
    }

    /**
     * Tests that the parser sets the correct end line number for an interface.
     */
    public function testParserSetsCorrectInterfaceEndLineNumber(): void
    {
        static::assertEquals(18, $this->getInterfaceForTest()->getEndLine());
    }

    /**
     * Tests that the parser sets the correct start line number for interface
     * methods.
     */
    public function testParserSetsCorrectInterfaceMethodStartLineNumbers(): void
    {
        $methods = $this->getInterfaceMethodsForTest();
        static::assertEquals(17, $methods->current()->getStartLine());
    }

    /**
     * Tests that the parser sets the correct end line number for interface methods.
     */
    public function testParserSetsCorrectInterfaceMethodEndLineNumbers(): void
    {
        $methods = $this->getInterfaceMethodsForTest();
        static::assertEquals(17, $methods->current()->getEndLine());
    }

    /**
     * Tests that the parser marks all interface methods as abstract.
     */
    public function testParserSetsAllInterfaceMethodsAbstract(): void
    {
        $methods = $this->getInterfaceMethodsForTest();
        static::assertTrue($methods->current()->isAbstract());
    }

    /**
     * testParserHandlesClassWithMultipleImplementedInterfaces
     */
    public function testParserHandlesClassWithMultipleImplementedInterfaces(): void
    {
        $class = $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current();

        static::assertCount(3, $class->getInterfaces());
    }

    /**
     * testParserHandlesInterfaceWithMultipleParentInterfaces
     */
    public function testParserHandlesInterfaceWithMultipleParentInterfaces(): void
    {
        $class = $this->parseCodeResourceForTest()
            ->current()
            ->getInterfaces()
            ->current();

        static::assertCount(3, $class->getInterfaces());
    }

    /**
     * Tests that the parser sets the correct line number for methods.
     */
    public function testParserSetsCorrectMethodLineNumber(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $method = $namespaces[2]
            ->getTypes()
            ->current()
            ->getMethods()
            ->current();

        static::assertEquals(17, $method->getStartLine());
    }

    /**
     * Tests that the parser doesn't mark a non abstract method as abstract.
     */
    public function testParserDoesNotMarkNonAbstractMethodAsAbstract(): void
    {
        $methods = $this->getClassForTest()->getMethods();
        // TODO: Replace this loop with an array(<method>=><abstract>)
        foreach ($methods as $method) {
            static::assertFalse($method->isAbstract());
        }
    }

    /**
     * Tests that the parser marks an abstract method as abstract.
     */
    public function testParserMarksAbstractMethodAsAbstract(): void
    {
        $method = $this->getClassForTest()
            ->getParentClass()
            ?->getMethods()
            ->current();

        static::assertTrue($method?->isAbstract());
    }

    /**
     * Tests that the parser handles PHP 5.3 object namespace + class chaining.
     */
    public function testParserParseNewInstancePHP53(): void
    {
        $namespaces = $this->parseCodeResourceForTest();
        $function = $namespaces->current()
            ->getFunctions()
            ->current();

        $dependencies = $function->getDependencies();
        static::assertEquals('PDepend1', $dependencies->current()->getNamespace()?->getImage());
        $dependencies->next();
        static::assertEquals('PDepend2', $dependencies->current()->getNamespace()?->getImage());
    }

    /**
     * Tests that doc comment blocks are added to a function.
     */
    public function testParserSetsCorrectFunctionDocComment(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $nodes = $namespaces->current()->getFunctions();
        $this->doTestParserSetsCorrectDocComment($nodes, 0);
    }

    /**
     * Tests that the parser sets the correct function return type.
     *
     * @return ASTArtifactList<ASTFunction>
     */
    public function testParserSetsCorrectFunctionReturnType(): ASTArtifactList
    {
        $namespaces = $this->parseCodeResourceForTest();

        $functions = $namespaces[0]->getFunctions();
        static::assertCount(3, $functions);

        return $functions;
    }

    /**
     * @param ASTArtifactList<ASTFunction> $functions
     *
     * @depends testParserSetsCorrectFunctionReturnType
     */
    public function testParserSetsFunctionReturnTypeToNull(ASTArtifactList $functions): void
    {
        static::assertSame(
            [
                'function' => 'func1',
                'returnClass' => null,
            ],
            [
                'function' => $functions[0]->getImage(),
                'returnClass' => $functions[0]->getReturnClass(),
            ]
        );
    }

    /**
     * @param ASTArtifactList<ASTFunction> $functions
     *
     * @depends testParserSetsCorrectFunctionReturnType
     */
    public function testParserSetsExpectedFunctionReturnTypeOfFunctionTwo(ASTArtifactList $functions): void
    {
        static::assertSame(
            [
                'function' => 'func2',
                'returnClass' => 'SplObjectStore',
            ],
            [
                'function' => $functions[1]->getImage(),
                'returnClass' => $functions[1]->getReturnClass()?->getImage(),
            ]
        );
    }

    /**
     * @param ASTArtifactList<ASTFunction> $functions
     *
     * @depends testParserSetsCorrectFunctionReturnType
     */
    public function testParserSetsExpectedFunctionReturnTypeOfFunctionThree(ASTArtifactList $functions): void
    {
        static::assertSame(
            [
                'function' => 'func3',
                'returnClass' => 'SplObjectStore',
            ],
            [
                'function' => $functions[2]->getImage(),
                'returnClass' => $functions[2]->getReturnClass()?->getImage(),
            ]
        );
    }

    /**
     * Tests that the parser sets the correct method exception types.
     */
    public function testParserSetsCorrectFunctionExceptionTypes(): void
    {
        $functions = $this->parseCodeResourceForTest()
            ->current()
            ->getFunctions();

        $actual = [];
        foreach ($functions as $function) {
            foreach ($function->getExceptionClasses() as $exception) {
                $actual[] = "{$function->getImage()} throws {$exception->getImage()}";
            }
        }

        static::assertEquals(
            [
                'func1 throws RuntimeException',
                'func2 throws OutOfRangeException',
                'func2 throws InvalidArgumentException',
            ],
            $actual
        );
    }

    /**
     * Tests that the parser doesn't handle annotations if this is set to true.
     */
    public function testParserHandlesIgnoreAnnotationsCorrectForFunctions(): void
    {
        $functions = $this->parseCodeResourceForTest(true)
            ->current()
            ->getFunctions();

        $actual = [];
        foreach ($functions as $function) {
            $actual[] = $function->getExceptionClasses()->count();
            $actual[] = $function->getReturnClass();
        }

        static::assertSame([0, null, 0, null, 0, null], $actual);
    }

    /**
     * Tests that doc comment blocks are added to a method.
     */
    public function testParserSetsCorrectMethodDocComment(): void
    {
        $nodes = $this->parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current()
            ->getMethods();

        $this->doTestParserSetsCorrectDocComment($nodes);
    }

    /**
     * Tests that the parser sets the correct method return type.
     */
    public function testParserSetsCorrectMethodReturnType(): void
    {
        $nodes = $this->parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current()
            ->getMethods();

        $actual = [];
        foreach ($nodes as $method) {
            $actual[] = $method->getImage();
            $actual[] = $method->getReturnClass()?->getImage();
        }

        static::assertEquals(
            ['__construct', null, 'method1', 'SplObjectStore', 'method2', 'SplSubject'],
            $actual
        );
    }

    /**
     * Tests that the parser sets the correct method exception types.
     */
    public function testParserSetsCorrectMethodExceptionTypes(): void
    {
        $nodes = $this->parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current()
            ->getMethods();

        $actual = [];
        foreach ($nodes as $method) {
            $actual[] = $method->getImage();
            foreach ($method->getExceptionClasses() as $exception) {
                $actual[] = $exception->getImage();
            }
        }

        static::assertEquals(
            [
                '__construct',
                'RuntimeException',
                'method1',
                'OutOfRangeException',
                'OutOfBoundsException',
                'method2',
            ],
            $actual
        );
    }

    /**
     * Tests that the parser doesn't handle annotations if this is set to true.
     */
    public function testParserHandlesIgnoreAnnotationsCorrectForMethods(): void
    {
        $methods = $this->parseCodeResourceForTest(true)
            ->current()
            ->getTypes()
            ->current()
            ->getMethods();

        $actual = [];
        foreach ($methods as $method) {
            $actual[] = $method->getExceptionClasses()->count();
            $actual[] = $method->getReturnClass();
        }

        static::assertSame([0, null, 0, null, 0, null], $actual);
    }

    /**
     * Tests that the parser sets the correct doc comment blocks for properties.
     */
    public function testParserSetsCorrectPropertyDocComment(): void
    {
        $class = $this->parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current();
        static::assertInstanceOf(ASTClass::class, $class);
        $nodes = $class->getProperties();

        $this->doTestParserSetsCorrectDocComment($nodes);
    }

    /**
     * Tests that the parser sets the correct visibility for properties.
     */
    public function testParserSetsCorrectPropertyVisibility(): void
    {
        $class = $this->parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current();
        static::assertInstanceOf(ASTClass::class, $class);
        $nodes = $class->getProperties();

        $actual = [];
        foreach ($nodes as $node) {
            $actual[] = [
                'public' => $node->isPublic(),
                'protected' => $node->isProtected(),
                'private' => $node->isPrivate(),
            ];
        }

        static::assertEquals(
            [
                ['public' => false, 'protected' => false, 'private' => true],
                ['public' => true, 'protected' => false, 'private' => false],
                ['public' => false, 'protected' => true, 'private' => false],
                ['public' => false, 'protected' => true, 'private' => false],
            ],
            $actual
        );
    }

    /**
     * Tests that the parser sets property types for non scalar properties.
     */
    public function testParserSetsCorrectPropertyTypes(): void
    {
        $class = $this->parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current();
        static::assertInstanceOf(ASTClass::class, $class);
        $nodes = $class->getProperties();

        $actual = [];
        foreach ($nodes as $node) {
            $className = $node->getClass()?->getImage();
            $actual[$node->getImage()] = $className;
        }

        static::assertEquals(
            [
                '$property1' => 'MyPropertyClass2',
                '$property2' => 'MyPropertyClass2',
                '$property3' => 'MyPropertyClass2',
                '$property4' => 'MyPropertyClass2',
                '$property5' => null,
                '$property6' => null,
            ],
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
     */
    public function testParserSetsExpectedPropertyTypeForChainedComment(): void
    {
        $class = $this->parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current();
        static::assertInstanceOf(ASTClass::class, $class);
        $class = $class->getProperties()
            ->current()
            ->getClass();

        static::assertEquals('Runtime', $class?->getImage());
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
     */
    public function testParserSetsExpectedPropertyTypeForChainedCommentInArray(): void
    {
        $class = $this->parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current();
        static::assertInstanceOf(ASTClass::class, $class);
        $type = $class->getProperties()
            ->current()
            ->getClass();

        static::assertEquals('Session', $type?->getImage());
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
     */
    public function testParserSetsExpectedReturnTypeForChainedComment(): void
    {
        $type = $this->parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current()
            ->getMethods()
            ->current()
            ->getReturnClass();

        static::assertSame('Runtime', $type?->getImage());
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
     */
    public function testParserSetsExpectedReturnTypeForChainedCommentInArray(): void
    {
        $type = $this->parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current()
            ->getMethods()
            ->current()
            ->getReturnClass();

        static::assertSame('Session', $type?->getImage());
    }

    /**
     * Tests that the parser sets property types for non scalar properties.
     */
    public function testHandlesIgnoreAnnotationsCorrectForProperties(): void
    {
        $class = $this->parseCodeResourceForTest(true)
            ->current()
            ->getTypes()
            ->current();
        static::assertInstanceOf(ASTClass::class, $class);
        $nodes = $class->getProperties();

        $actual = [];
        foreach ($nodes as $property) {
            $actual[] = $property->getClass();
        }

        static::assertEquals([null, null, null, null, null, null], $actual);
    }

    /**
     * Tests that parser sets the correct doc comment blocks for classes and
     * interfaces.
     */
    public function testParserSetsCorrectClassOrInterfaceDocComment(): void
    {
        $actual = [];
        $expected = [
            "/**\n * Sample comment.\n */",
            null,
            null,
            "/**\n * A second comment...\n */",
        ];

        $namespaces = $this->parseCodeResourceForTest();
        foreach ($namespaces[0]->getTypes() as $type) {
            $actual[] = $type->getComment();
        }

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the parser supports sub packages.
     */
    public function testParserSubpackageSupport(): void
    {
        $namespace = $this->parseCodeResourceForTest()->current();
        static::assertEquals('PHP\Depend', $namespace->getImage());
    }

    /**
     * Tests that the parser supports sub packages.
     *
     * @return ASTArtifactList<ASTNamespace>
     */
    public function testParserSetsFileLevelFunctionPackage(): ASTArtifactList
    {
        $namespaces = $this->parseCodeResourceForTest();

        static::assertCount(2, $namespaces);

        return $namespaces;
    }

    /**
     * @param ASTArtifactList<ASTNamespace> $namespaces
     *
     * @depends testParserSetsFileLevelFunctionPackage
     */
    public function testParserSetsFileLevelFunctionPackageNumberOfFunctionsInFirstNamespace(
        ASTArtifactList $namespaces
    ): void {
        $functions = $namespaces[0]->getFunctions();
        static::assertCount(2, $functions);
    }

    /**
     * @param ASTArtifactList<ASTNamespace> $namespaces
     *
     * @depends testParserSetsFileLevelFunctionPackage
     */
    public function testParserSetsFileLevelFunctionPackageNumberOfFunctionsInSecondNamespace(
        ASTArtifactList $namespaces
    ): void {
        $functions = $namespaces[1]->getFunctions();
        static::assertCount(1, $functions);
    }

    /**
     * @param ASTArtifactList<ASTNamespace> $namespaces
     *
     * @depends testParserSetsFileLevelFunctionPackage
     */
    public function testParserSetsFileExpectedPackageForFirstFunctionInFirstNamespace(ASTArtifactList $namespaces): void
    {
        $functions = $namespaces[0]->getFunctions();

        static::assertEquals('PHP\\Depend', $functions[0]->getNamespace()?->getImage());
    }

    /**
     * @param ASTArtifactList<ASTNamespace> $namespaces
     *
     * @depends testParserSetsFileLevelFunctionPackage
     */
    public function testParserSetsFileExpectedPackageForSecondFunctionInFirstNamespace(
        ASTArtifactList $namespaces
    ): void {
        $functions = $namespaces[0]->getFunctions();

        static::assertEquals('PHP\\Depend', $functions[1]->getNamespace()?->getImage());
    }

    /**
     * @param ASTArtifactList<ASTNamespace> $namespaces
     *
     * @depends testParserSetsFileLevelFunctionPackage
     */
    public function testParserSetsFileExpectedPackageForFirstFunctionInSecondNamespace(
        ASTArtifactList $namespaces
    ): void {
        $functions = $namespaces[1]->getFunctions();

        static::assertEquals('PDepend\\Test', $functions[0]->getNamespace()?->getImage());
    }

    /**
     * testParserSetsAbstractPropertyOnClass
     */
    public function testParserSetsAbstractPropertyOnClass(): void
    {
        $class = $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current();

        static::assertTrue($class->isAbstract());
    }

    /**
     * testParserSetsAbstractModifierOnClass
     */
    public function testParserSetsAbstractModifierOnClass(): void
    {
        $class = $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current();

        static::assertSame(
            State::IS_EXPLICIT_ABSTRACT,
            $class->getModifiers() & State::IS_EXPLICIT_ABSTRACT
        );
    }

    /**
     * testParserSetsFinalPropertyOnClass
     */
    public function testParserSetsFinalPropertyOnClass(): void
    {
        $class = $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current();

        static::assertTrue($class->isFinal());
    }

    /**
     * testParserSetsFinalModifierOnClass
     */
    public function testParserSetsFinalModifierOnClass(): void
    {
        $class = $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current();

        static::assertSame(State::IS_FINAL, $class->getModifiers() & State::IS_FINAL);
    }

    /**
     * Tests that the parser handles nested array structures as parameter
     * default value correct.
     */
    public function testParserHandlesNestedArraysAsParameterDefaultValue(): void
    {
        // Current implementation cannot handle nested structures
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserStripsCommentsInParseExpressionUntilCorrect
     */
    public function testParserStripsCommentsInParseExpressionUntilCorrect(): void
    {
        $method = $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $foreach = $method->getFirstChildOfType(ASTForeachStatement::class);
        static::assertNotNull($foreach);
    }

    /**
     * Tests that the parser throws an exception for an unclosed array
     * declaration within the default value of a parameter.
     */
    public function testParserThrowsUnexpectedTokenExceptionForBrokenParameterArrayDefaultValue(): void
    {
        $this->expectException(
            UnexpectedTokenException::class
        );
        $this->expectExceptionMessage(
            'Unexpected token: {, line: 2, col: 29, file: '
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that the parser throws an exception when it detects an invalid
     * token within the parameter declaration of a function or method.
     */
    public function testParserThrowsUnexpectedTokenExceptionForInvalidTokenInParameterDefaultValue(): void
    {
        $this->expectException(
            UnexpectedTokenException::class
        );
        $this->expectExceptionMessage(
            'Unexpected token: &, line: 2, col: 27, file: '
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that the parser throws an exception when it detects an invalid
     * token in a class body.
     */
    public function testParserThrowsUnexpectedTokenExceptionForInvalidTokenInClassBody(): void
    {
        $this->expectException(
            UnexpectedTokenException::class
        );
        $this->expectExceptionMessage(
            'Unexpected token: ;, line: 4, col: 5, file: '
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that the parser throws an exception when it detects an invalid
     * token in a method or property declaration.
     */
    public function testParserThrowsUnexpectedTokenExceptionForInvalidTokenInMethodDeclaration(): void
    {
        $this->expectException(
            UnexpectedTokenException::class
        );
        $this->expectExceptionMessage(
            'Unexpected token: &, line: 4, col: 12, file: '
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that the parser handles the <b>parent</b> keyword within the default
     * value of a function.
     */
    public function testParserHandlesParentKeywordInFunctionParameterDefaultValue(): void
    {
        $parameters = $this->getFirstClassMethodForTestCase()->getParameters();

        static::assertTrue($parameters[0]->isDefaultValueAvailable());
        $value = $parameters[0]->getDefaultValue();
        static::assertInstanceOf(ASTParentReference::class, $value);
        static::assertSame('parent', $value->getImage());
    }

    /**
     * Tests that the parser handles the <b>parent</b> keyword within the default
     * value of a method.
     */
    public function testParserHandlesParentKeywordInMethodParameterDefaultValue(): void
    {
        $parameters = $this->getFirstClassMethodForTestCase()->getParameters();
        static::assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the parser handles the self keyword as parameter type hint.
     */
    public function testParserHandlesSelfKeywordAsParameterTypeHint(): void
    {
        $parameters = $this->getFirstClassMethodForTestCase()->getParameters();
        static::assertNotNull($parameters[0]);
    }

    /**
     * testParserSetsBestMatchForParameterTypeHintEvenWhenNameEquals
     */
    public function testParserSetsBestMatchForParameterTypeHintEvenWhenNameEquals(): void
    {
        $namespaces = $this->parseCodeResourceForTest();
        $classes = $namespaces[0]->getClasses();
        $methods = $classes[1]->getMethods();
        $parameters = $methods[0]->getParameters();

        static::assertSame($classes[0], $parameters[0]->getClass());
        static::assertNotSame($classes[1], $parameters[0]->getClass());
    }

    /**
     * Tests that the parser translates the self keyword into the same instance,
     * even when a similar class exists.
     */
    public function testParserSetsTheReallySameParameterHintInstanceForKeywordSelf(): void
    {
        $class = $this->getFirstClassForTestCase();
        $methods = $class->getMethods();
        $parameters = $methods[0]->getParameters();

        static::assertSame($class, $parameters[0]->getClass());
    }

    /**
     * testParserStripsLeadingSlashFromNamespacedClassName
     */
    public function testParserStripsLeadingSlashFromNamespacedClassName(): void
    {
        $namespace = $this->parseCodeResourceForTest()->current();
        static::assertEquals('foo', $namespace->getImage());
    }

    /**
     * testParserStripsLeadingSlashFromNamespacedClassName
     */
    public function testParserStripsLeadingSlashFromNamespaceAliasedClassName(): void
    {
        $namespace = $this->getFirstClassForTestCase()
            ->getParentClass()
            ?->getNamespace();

        static::assertEquals('foo\bar\baz', $namespace?->getImage());
    }

    /**
     * testParserStripsLeadingSlashFromInheritNamespacedClassName
     */
    public function testParserStripsLeadingSlashFromInheritNamespacedClassName(): void
    {
        $namespace = $this->getFirstClassForTestCase()
            ->getParentClass()
            ?->getNamespace();

        static::assertEquals('bar', $namespace?->getImage());
    }

    /**
     * testParserThrowsExpectedExceptionWhenDefaultStaticDefaultValueNotExists
     */
    public function testParserThrowsExpectedExceptionWhenDefaultStaticDefaultValueNotExists(): void
    {
        $this->expectException(MissingValueException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserHandlesDoubleQuoteStringAsConstantDefaultValue
     */
    public function testParserHandlesDoubleQuoteStringAsConstantDefaultValue(): void
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserHandlesDoubleQuoteStringWithEscapedVariable
     */
    public function testParserHandlesDoubleQuoteStringWithEscapedVariable(): void
    {
        $function = $this->parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current();

        $string = $function->getFirstChildOfType(ASTString::class);
        $image = $string?->getChild(0)->getImage();

        static::assertEquals('\$foobar', $image);
    }

    /**
     * testParserHandlesDoubleQuoteStringWithEscapedDoubleQuote
     */
    public function testParserHandlesDoubleQuoteStringWithEscapedDoubleQuote(): void
    {
        $function = $this->parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current();

        $string = $function->getFirstChildOfType(ASTString::class);
        $image = $string?->getChild(0)->getImage();

        static::assertEquals('\\\\\"', $image);
    }

    /**
     * testParserNotHandlesDoubleQuoteStringWithVariableAndParenthesisAsFunctionCall
     */
    public function testParserNotHandlesDoubleQuoteStringWithVariableAndParenthesisAsFunctionCall(): void
    {
        $function = $this->parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current();

        $string = $function->getFirstChildOfType(ASTString::class);
        $variable = $string?->getChild(0);

        static::assertInstanceOf(ASTVariable::class, $variable);
    }

    /**
     * testParserNotHandlesDoubleQuoteStringWithVariableAndEqualAsAssignment
     */
    public function testParserNotHandlesDoubleQuoteStringWithVariableAndEqualAsAssignment(): void
    {
        $function = $this->parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current();

        $string = $function->getFirstChildOfType(ASTString::class);
        $variable = $string?->getChild(0);

        static::assertInstanceOf(ASTVariable::class, $variable);
    }

    /**
     * testParserHandlesStringWithQuestionMarkNotAsTernaryOperator
     */
    public function testParserHandlesStringWithQuestionMarkNotAsTernaryOperator(): void
    {
        $method = $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $string = $method->getFirstChildOfType(ASTString::class);
        static::assertInstanceOf(ASTLiteral::class, $string?->getChild(1));
    }

    /**
     * testParserStopsProcessingWhenCacheContainsValidResult
     */
    public function testParserStopsProcessingWhenCacheContainsValidResult(): void
    {
        $builder = $this->getMockBuilder(PHPBuilder::class)
            ->getMock();

        $tokenizer = new PHPTokenizerInternal();
        $tokenizer->setSourceFile(__FILE__);

        $cache = $this->createCacheFixture();
        $cache->expects(static::once())
            ->method('restore')
            ->will(static::returnValue(true));
        $cache->expects(static::never())
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
     */
    public function testParseClosureAsFunctionArgument(): void
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParseNowdocInMethodBody
     */
    public function testParseNowdocInMethodBody(): void
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParseDoWhileStatement
     */
    public function testParseDoWhileStatement(): void
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserHandlesCompoundExpressionInArrayBrackets
     */
    public function testParserHandlesCompoundExpressionInArrayBrackets(): void
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserHandlesEmptyNonePhpCodeInMethodBody
     */
    public function testParserHandlesEmptyNonePhpCodeInMethodBody(): void
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserHandlesPhpCloseTagInMethodBody
     */
    public function testParserHandlesPhpCloseTagInMethodBody(): void
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserHandlesMultiplePhpCloseTagsInMethodBody
     */
    public function testParserHandlesMultiplePhpCloseTagsInMethodBody(): void
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParseExpressionUntilThrowsExceptionForUnclosedStatement
     */
    public function testParseExpressionUntilThrowsExceptionForUnclosedStatement(): void
    {
        $this->expectException(UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * Tests to ensure function docblock comments are parsed correctly
     */
    public function testFunctionDocBlockIsCorrectlyParsed(): void
    {
        $function = $this->parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current();

        static::assertEquals(
            $function->getComment(),
            "/**\n * This is the function docblock for foo\n *\n */"
        );
    }

    /**
     * Returns an interface instance from the mixed code test file.
     */
    protected function getInterfaceForTest(): ASTInterface
    {
        $namespaces = $this->parseCodeResourceForTest();

        $interface = $namespaces[2]->getTypes()->current();
        static::assertInstanceOf(ASTInterface::class, $interface);

        return $interface;
    }

    /**
     * Returns the methods of an interface from the mixed code test file.
     *
     * @return ASTArtifactList<ASTMethod>
     */
    protected function getInterfaceMethodsForTest(): ASTArtifactList
    {
        $namespaces = $this->parseCodeResourceForTest();

        return $namespaces[2]
            ->getInterfaces()
            ->current()
            ->getMethods();
    }

    /**
     * Returns a class instance from the mixed code test file.
     */
    protected function getClassForTest(): ASTClass
    {
        $namespaces = $this->parseCodeResourceForTest();

        $class = $namespaces[1]->getTypes()->current();
        static::assertInstanceOf(ASTClass::class, $class);

        return $class;
    }

    /**
     * Returns the methods of a class from the mixed code test file.
     *
     * @return ASTArtifactList<ASTMethod>
     */
    protected function getClassMethodsForTest(): ASTArtifactList
    {
        $namespaces = $this->parseCodeResourceForTest();

        return $namespaces[1]->getClasses()->current()->getMethods();
    }

    /**
     * Generic comment test method.
     *
     * @template T of ASTArtifact
     * @param ASTArtifactList<T> $nodes
     */
    protected function doTestParserSetsCorrectDocComment(ASTArtifactList $nodes, int $indent = 1): void
    {
        $ws = str_repeat(' ', 4 * $indent);

        $expected = [
            "/**\n{$ws} * This is one comment.\n{$ws} */",
            null,
            null,
            "/**\n{$ws} * This is a second comment.\n{$ws} */",
        ];

        $actual = [];
        foreach ($nodes as $callable) {
            $actual[] = $callable->getComment();
        }

        static::assertEquals($expected, $actual);
    }
}
