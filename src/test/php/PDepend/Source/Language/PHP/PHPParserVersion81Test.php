<?php

/**
 * This file is part of PDepend.
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

namespace PDepend\Source\Language\PHP;

use OutOfBoundsException;
use PDepend\AbstractTestCase;
use PDepend\Source\AST\AbstractASTClassOrInterface;
use PDepend\Source\AST\ASTArguments;
use PDepend\Source\AST\ASTArray;
use PDepend\Source\AST\ASTArrayElement;
use PDepend\Source\AST\ASTArrayIndexExpression;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTAssignmentExpression;
use PDepend\Source\AST\ASTCatchStatement;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTClassOrInterfaceReference;
use PDepend\Source\AST\ASTClosure;
use PDepend\Source\AST\ASTCompoundExpression;
use PDepend\Source\AST\ASTConstantDeclarator;
use PDepend\Source\AST\ASTConstantDefinition;
use PDepend\Source\AST\ASTConstantPostfix;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\AST\ASTFormalParameter;
use PDepend\Source\AST\ASTFormalParameters;
use PDepend\Source\AST\ASTFunctionPostfix;
use PDepend\Source\AST\ASTHeredoc;
use PDepend\Source\AST\ASTInstanceOfExpression;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\AST\ASTParameter;
use PDepend\Source\AST\ASTReturnStatement;
use PDepend\Source\AST\ASTScalarType;
use PDepend\Source\AST\ASTSelfReference;
use PDepend\Source\AST\ASTType;
use PDepend\Source\AST\ASTTypeArray;
use PDepend\Source\AST\ASTTypeCallable;
use PDepend\Source\AST\ASTTypeIterable;
use PDepend\Source\AST\ASTUnionType;
use PDepend\Source\AST\ASTValue;
use PDepend\Source\AST\ASTVariable;
use PDepend\Source\AST\ASTVariableDeclarator;
use PDepend\Source\Builder\Builder;
use PDepend\Source\Parser\InvalidStateException;
use PDepend\Source\Parser\TokenStreamEndException;
use PDepend\Source\Parser\UnexpectedTokenException;
use PDepend\Source\Tokenizer\Token;
use PDepend\Source\Tokenizer\Tokenizer;
use PDepend\Source\Tokenizer\Tokens;
use PDepend\Util\Cache\CacheDriver;
use PDepend\Util\Cache\Driver\MemoryCacheDriver;
use ReflectionMethod;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\AbstractPHPParser} class.
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @group unittest
 */
class PHPParserVersion81Test extends AbstractTestCase
{
    /**
     * testParserAllowsKeywordCallableAsPropertyName
     */
    public function testParserAllowsKeywordCallableAsPropertyName(): void
    {
        $method = $this->getFirstClassMethodForTestCase();
        static::assertNotNull($method);
    }

    /**
     * @return AbstractASTClassOrInterface[]
     */
    public function testParserResolvesDependenciesInDocComments(): array
    {
        $namespaces = $this->parseCodeResourceForTest();
        $classes = $namespaces[0]->getClasses();

        /** @var AbstractASTClassOrInterface[] */
        $dependencies = $classes[0]->findChildrenOfType(ASTClassOrInterfaceReference::class);

        static::assertCount(1, $dependencies);

        return $dependencies;
    }

    /**
     * Tests that the parser throws an exception when trying to parse an array
     * when being at the end of the file.
     */
    public function testParserThrowsUnexpectedTokenExceptionForArrayWithEOF(): void
    {
        $this->expectException(
            TokenStreamEndException::class
        );
        $this->expectExceptionMessage(
            'Unexpected end of token stream in file:'
        );

        $cache = new MemoryCacheDriver();
        $builder = new PHPBuilder();

        $tokenizer = $this->getMockBuilder(Tokenizer::class)
            ->getMock();
        $tokenizer
            ->method('peek')
            ->willReturn(Tokenizer::T_EOF);
        $tokenizer
            ->method('next')
            ->willReturn(null);
        $parser = $this->createPHPParser($tokenizer, $builder, $cache);
        $parseArray = new ReflectionMethod($parser, 'parseArray');
        $parseArray->setAccessible(true);
        $parseArray->invoke($parser, new ASTArray());
    }

    /**
     * testParserHandlesBinaryIntegerLiteral
     */
    public function testParserHandlesBinaryIntegerLiteral(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $literal = $method->getFirstChildOfType(ASTLiteral::class);

        static::assertEquals('0b0100110100111', $literal?->getImage());
    }

    /**
     * testParserHandlesStaticMemberExpressionSyntax
     */
    public function testParserHandlesStaticMemberExpressionSyntax(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        $expr = $function->getFirstChildOfType(ASTCompoundExpression::class);

        static::assertInstanceOf(ASTCompoundExpression::class, $expr);
    }

    /**
     * testParserThrowsExpectedExceptionForTraitAsFunctionName
     */
    public function testParserThrowsExpectedExceptionForTraitAsFunctionName(): void
    {
        $this->expectException(UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTraitAsCalledFunction
     */
    public function testParserThrowsExpectedExceptionForTraitAsCalledFunction(): void
    {
        $this->expectException(UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsFunctionName
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsFunctionName(): void
    {
        $this->expectException(UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsCalledFunction
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsCalledFunction(): void
    {
        $this->expectException(UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForCallableAsFunctionName
     */
    public function testParserThrowsExpectedExceptionForCallableAsFunctionName(): void
    {
        $this->expectException(UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForCallableAsCalledFunction
     */
    public function testParserThrowsExpectedExceptionForCallableAsCalledFunction(): void
    {
        $this->expectException(UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    public function testMagicTraitConstantInString(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * Tests that ::class is allowed PHP >= 5.5.
     */
    public function testDoubleColonClass(): void
    {
        static::assertInstanceOf(ASTArtifactList::class, $this->parseCodeResourceForTest());
    }

    /**
     * testComplexExpressionInParameterInitializer
     */
    public function testComplexExpressionInParameterInitializer(): void
    {
        $node = $this->getFirstFunctionForTestCase()
            ->getFirstChildOfType(ASTFormalParameter::class);

        static::assertNotNull($node);
    }

    /**
     * testComplexExpressionInConstantInitializer
     */
    public function testComplexExpressionInConstantDeclarator(): void
    {
        $node = $this->getFirstClassForTestCase()
            ->getFirstChildOfType(ASTConstantDeclarator::class);

        static::assertNotNull($node);
    }

    /**
     * testComplexExpressionInFieldDeclaration
     */
    public function testComplexExpressionInFieldDeclaration(): void
    {
        $node = $this->getFirstClassForTestCase()
            ->getFirstChildOfType(ASTFieldDeclaration::class);

        static::assertNotNull($node);
    }

    /**
     * testPowExpressionInMethodBody
     */
    public function testPowExpressionInMethodBody(): void
    {
        $node = $this->getFirstClassForTestCase()
            ->getFirstChildOfType(ASTReturnStatement::class);

        static::assertSame('**', $node?->getChild(0)->getChild(1)->getImage());
    }

    /**
     * testPowExpressionInFieldDeclaration
     */
    public function testPowExpressionInFieldDeclaration(): void
    {
        $node = $this->getFirstClassForTestCase()
            ->getFirstChildOfType(ASTFieldDeclaration::class);

        static::assertNotNull($node);
    }

    public function testUseStatement(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    public function testEllipsisOperatorInFunctionCall(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * Test that static array property is well linked to its self:: / static:: accesses.
     */
    public function testStaticArrayProperty(): void
    {
        /** @var ASTReturnStatement[] $returnStatements */
        $returnStatements = $this
            ->getFirstMethodForTestCase()
            ->findChildrenOfType(ASTReturnStatement::class);

        /** @var ASTMemberPrimaryPrefix $memberPrefix */
        $memberPrefix = $returnStatements[0]->getChild(0);
        static::assertInstanceOf(ASTMemberPrimaryPrefix::class, $memberPrefix);
        static::assertTrue($memberPrefix->isStatic());
        static::assertInstanceOf(ASTSelfReference::class, $memberPrefix->getChild(0));
        $children = $memberPrefix->getChild(1)->getChildren();
        static::assertCount(1, $children);
        static::assertInstanceOf(ASTArrayIndexExpression::class, $children[0]);
        $children = $children[0]->getChildren();
        static::assertCount(2, $children);
        static::assertInstanceOf(ASTVariable::class, $children[0]);
        static::assertInstanceOf(ASTLiteral::class, $children[1]);
        static::assertSame('$foo', $children[0]->getImage());
        static::assertSame("'bar'", $children[1]->getImage());
    }

    /**
     * Tests issue with constant array concatenation.
     * https://github.com/pdepend/pdepend/issues/299
     */
    public function testConstantArrayConcatenation(): void
    {
        /** @var ASTClass $class */
        $class = $this->getFirstClassForTestCase();

        /** @var ASTConstantDefinition[] */
        $constants = $class->getChildren();

        static::assertCount(2, $constants);
        static::assertInstanceOf(ASTConstantDefinition::class, $constants[0]);
        static::assertInstanceOf(ASTConstantDefinition::class, $constants[1]);

        /** @var ASTConstantDeclarator[] $declarators */
        $declarators = $constants[1]->getChildren();

        static::assertCount(1, $declarators);
        static::assertInstanceOf(ASTConstantDeclarator::class, $declarators[0]);

        /** @var ASTExpression $expression */
        $expression = $declarators[0]->getValue()?->getValue();

        static::assertInstanceOf(ASTExpression::class, $expression);

        $nodes = $expression->getChildren();
        static::assertInstanceOf(ASTMemberPrimaryPrefix::class, $nodes[0]);
        static::assertInstanceOf(ASTExpression::class, $nodes[1]);
        static::assertSame('+', $nodes[1]->getImage());
        static::assertInstanceOf(ASTArray::class, $nodes[2]);

        $nodes = $nodes[0]->getChildren();
        static::assertInstanceOf(ASTSelfReference::class, $nodes[0]);
        static::assertInstanceOf(ASTConstantPostfix::class, $nodes[1]);
        static::assertSame('A', $nodes[1]->getImage());
    }

    /**
     * Tests that the parser throws an exception when trying to parse a value
     * when given a non-value token type.
     */
    public function testParserThrowsUnexpectedTokenExceptionForOF(): void
    {
        $this->expectException(
            UnexpectedTokenException::class
        );
        $this->expectExceptionMessage(
            'Unexpected token: function, line: 1, col: 1, file:'
        );

        $cache = new MemoryCacheDriver();
        $builder = new PHPBuilder();

        $tokenizer = $this->getMockBuilder(Tokenizer::class)
            ->getMock();
        $tokenizer
            ->method('peek')
            ->willReturn(Tokens::T_FUNCTION);
        $tokenizer
            ->method('next')
            ->willReturn(new Token(Tokens::T_FUNCTION, 'function', 1, 1, 1, 9));
        $parser = $this->createPHPParser($tokenizer, $builder, $cache);
        $parseArray = new ReflectionMethod($parser, 'parseStaticValueVersionSpecific');
        $parseArray->setAccessible(true);
        $parseArray->invoke($parser, new ASTValue());
    }

    /**
     * testFormalParameterScalarTypeHintInt
     */
    public function testFormalParameterScalarTypeHintInt(): void
    {
        $type = $this->getFirstFormalParameterForTestCase()->getType();

        static::assertTrue($type->isScalar());
        static::assertEquals('int', $type->getImage());
    }

    /**
     * testFormalParameterScalarTypeHintString
     */
    public function testFormalParameterScalarTypeHintString(): void
    {
        $type = $this->getFirstFormalParameterForTestCase()->getType();

        static::assertTrue($type->isScalar());
        static::assertEquals('string', $type->getImage());
    }

    /**
     * testFormalParameterScalarTypeHintFloat
     */
    public function testFormalParameterScalarTypeHintFloat(): void
    {
        $type = $this->getFirstFormalParameterForTestCase()->getType();

        static::assertTrue($type->isScalar());
        static::assertEquals('float', $type->getImage());
    }

    /**
     * testFormalParameterScalarTypeHintBool
     */
    public function testFormalParameterScalarTypeHintBool(): void
    {
        $type = $this->getFirstFormalParameterForTestCase()->getType();

        static::assertTrue($type->isScalar());
        static::assertEquals('bool', $type->getImage());
    }

    /**
     * testFormalParameterStillWorksWithTypeHintArray
     */
    public function testFormalParameterStillWorksWithTypeHintArray(): void
    {
        $type = $this->getFirstFormalParameterForTestCase()->getChild(0);

        static::assertInstanceOf(ASTType::class, $type);
        static::assertFalse($type->isScalar());
    }

    /**
     * testFunctionReturnTypeHintInt
     */
    public function testFunctionReturnTypeHintInt(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        static::assertNotNull($type);
        static::assertTrue($type->isScalar());
        static::assertSame('int', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintFloat
     */
    public function testFunctionReturnTypeHintFloat(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        static::assertNotNull($type);
        static::assertTrue($type->isScalar());
        static::assertSame('float', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintString
     */
    public function testFunctionReturnTypeHintString(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        static::assertNotNull($type);
        static::assertTrue($type->isScalar());
        static::assertSame('string', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintBool
     */
    public function testFunctionReturnTypeHintBool(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        static::assertNotNull($type);
        static::assertTrue($type->isScalar());
        static::assertSame('bool', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintArray
     */
    public function testFunctionReturnTypeHintArray(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        static::assertNotNull($type);
        static::assertTrue($type->isArray());
        static::assertSame('array', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintCallable
     */
    public function testFunctionReturnTypeHintCallable(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        static::assertNotNull($type);
        static::assertFalse($type->isScalar());
        static::assertFalse($type->isArray());

        static::assertSame('callable', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintClass
     */
    public function testFunctionReturnTypeHintClass(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        static::assertNotNull($type);
        static::assertFalse($type->isScalar());
        static::assertFalse($type->isArray());

        static::assertSame('\\Iterator', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintInt
     */
    public function testClosureReturnTypeHintInt(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        static::assertNotNull($type);
        static::assertTrue($type->isScalar());
        static::assertSame('int', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintFloat
     */
    public function testClosureReturnTypeHintFloat(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        static::assertNotNull($type);
        static::assertTrue($type->isScalar());
        static::assertSame('float', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintString
     */
    public function testClosureReturnTypeHintString(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        static::assertNotNull($type);
        static::assertTrue($type->isScalar());
        static::assertSame('string', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintBool
     */
    public function testClosureReturnTypeHintBool(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        static::assertNotNull($type);
        static::assertTrue($type->isScalar());
        static::assertSame('bool', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintArray
     */
    public function testClosureReturnTypeHintArray(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        static::assertNotNull($type);
        static::assertTrue($type->isArray());
        static::assertSame('array', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintCallable
     */
    public function testClosureReturnTypeHintCallable(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        static::assertNotNull($type);
        static::assertFalse($type->isScalar());
        static::assertFalse($type->isArray());

        static::assertSame('callable', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintClass
     */
    public function testClosureReturnTypeHintClass(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        static::assertNotNull($type);
        static::assertFalse($type->isScalar());
        static::assertFalse($type->isArray());

        static::assertSame('\\Iterator', $type->getImage());
    }

    /**
     * testSpaceshipOperatorWithStrings
     */
    public function testSpaceshipOperatorWithStrings(): void
    {
        $expr = $this->getFirstClassMethodForTestCase()
            ->getFirstChildOfType(ASTExpression::class)
            ?->getFirstChildOfType(ASTExpression::class);

        static::assertSame('<=>', $expr?->getImage());
    }

    /**
     * testSpaceshipOperatorWithNumbers
     */
    public function testSpaceshipOperatorWithNumbers(): void
    {
        $expr = $this->getFirstClassMethodForTestCase()
            ->getFirstChildOfType(ASTExpression::class)
            ?->getFirstChildOfType(ASTExpression::class);

        static::assertSame('<=>', $expr?->getImage());
    }

    /**
     * testSpaceshipOperatorWithArrays
     */
    public function testSpaceshipOperatorWithArrays(): ASTExpression
    {
        $expr = $this->getFirstClassMethodForTestCase()
            ->getFirstChildOfType(ASTExpression::class)
            ?->getChild(1);

        static::assertInstanceOf(ASTExpression::class, $expr);
        static::assertSame('<=>', $expr->getImage());

        return $expr;
    }

    /**
     * @depends testSpaceshipOperatorWithArrays
     */
    public function testSpaceshipOperatorHasExpectedStartLine(ASTExpression $expr): void
    {
        static::assertSame(6, $expr->getStartLine());
    }

    /**
     * @depends testSpaceshipOperatorWithArrays
     */
    public function testSpaceshipOperatorHasExpectedEndLine(ASTExpression $expr): void
    {
        static::assertSame(6, $expr->getEndLine());
    }

    /**
     * @depends testSpaceshipOperatorWithArrays
     */
    public function testSpaceshipOperatorHasExpectedStartColumn(ASTExpression $expr): void
    {
        static::assertSame(27, $expr->getStartColumn());
    }

    /**
     * @depends testSpaceshipOperatorWithArrays
     */
    public function testSpaceshipOperatorHasExpectedEndColumn(ASTExpression $expr): void
    {
        static::assertSame(29, $expr->getEndColumn());
    }

    /**
     * testNullCoalesceOperator
     */
    public function testNullCoalesceOperator(): void
    {
        $expr = $this->getFirstClassMethodForTestCase()
            ->getFirstChildOfType(ASTExpression::class)
            ?->getFirstChildOfType(ASTExpression::class);

        static::assertSame('??', $expr?->getImage());
    }

    public function testListKeywordAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        static::assertNotNull($method);
    }

    public function testListKeywordAsFunctionNameThrowsException(): void
    {
        $this->expectException(UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    public function testGroupUseStatement(): ASTNamespace
    {
        $namespaces = $this->parseCodeResourceForTest();
        static::assertNotNull($namespaces);

        return $namespaces[0];
    }

    /**
     * @depends testGroupUseStatement
     */
    public function testGroupUseStatementClassNameResolution(ASTNamespace $namespace): void
    {
        $classes = $namespace->getClasses();
        $class = $classes[0];

        static::assertEquals(
            'FooLibrary\Bar\Baz\ClassB',
            $class->getParentClass()?->getNamespacedName()
        );
    }

    /**
     * @depends testGroupUseStatement
     */
    public function testGroupUseStatementAliasResolution(ASTNamespace $namespace): void
    {
        $classes = $namespace->getClasses();
        $class = $classes[1];

        static::assertEquals(
            'FooLibrary\Bar\Baz\ClassD',
            $class->getParentClass()?->getNamespacedName()
        );
    }

    public function testUniformVariableSyntax(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    public function testConstantNameArray(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    public function testClassConstantNames(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    public function testClassConstantNamesAccessed(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    public function testClassMethodNames(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    public function testClassMethodNamesInvoked(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    public function testYieldFrom(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    public function testParseList(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    public function testParenthesisAroundCallableParsesArguments(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    public function testKeywordsAsMethodNames(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $classes = $namespaces[0]->getClasses();
        $methods = $classes[0]->getMethods();

        static::assertSame('trait', $methods[0]->getImage());
        static::assertSame('callable', $methods[1]->getImage());
        static::assertSame('insteadof', $methods[2]->getImage());
    }

    public function testKeywordsAsConstants(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $classes = $namespaces[0]->getClasses();

        /** @var ASTConstantDeclarator[] $constants */
        $constants = $classes[0]->findChildrenOfType(ASTConstantDeclarator::class);

        static::assertSame('trait', $constants[0]->getImage());
        static::assertSame('callable', $constants[1]->getImage());
        static::assertSame('insteadof', $constants[2]->getImage());
    }

    /**
     * Tests that the parser does not throw an exception when it detects a reserved
     * keyword in constant class names.
     */
    public function testReservedKeyword(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    public function testConstVisibility(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    public function testConstVisibilityInInterfacePublic(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    public function testConstVisibilityInInterfaceProtected(): void
    {
        $this->expectException(
            InvalidStateException::class
        );
        $this->expectExceptionMessage(
            'Constant can\'t be declared private or protected in interface "TestInterface".'
        );

        $this->parseCodeResourceForTest();
    }

    public function testConstVisibilityInInterfacePrivate(): void
    {
        $this->expectException(
            InvalidStateException::class
        );
        $this->expectExceptionMessage(
            'Constant can\'t be declared private or protected in interface "TestInterface".'
        );

        $this->parseCodeResourceForTest();
    }

    public function testCatchMultipleExceptionClasses(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    public function testNullableTypeHintParameter(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    public function testNullableTypeHintReturn(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    public function testParseListWithVariableKey(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    public function testIterableTypeHintParameter(): void
    {
        $type = $this->getFirstFormalParameterForTestCase()->getType();

        static::assertFalse($type->isScalar());
        static::assertTrue($type->isArray());
        static::assertSame('iterable', $type->getImage());
    }

    public function testIterableTypeHintReturn(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        static::assertNotNull($type);
        static::assertFalse($type->isScalar());
        static::assertTrue($type->isArray());
        static::assertSame('iterable', $type->getImage());
    }

    public function testVoidTypeHintReturn(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        static::assertNotNull($type);
        static::assertTrue($type->isScalar());
        static::assertFalse($type->isArray());
        static::assertSame('void', $type->getImage());
    }

    public function testVoidTypeHintReturnNamespaced(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        static::assertNotNull($type);
        static::assertTrue($type->isScalar());
        static::assertFalse($type->isArray());
        static::assertSame('void', $type->getImage());
    }

    /**
     * testSymmetricArrayDestructuringEmptySlot
     */
    public function testSymmetricArrayDestructuringEmptySlot(): void
    {
        /** @var ASTArray */
        $array = $this->getFirstNodeOfTypeInFunction(
            ASTArray::class
        );
        static::assertCount(1, $array->getChildren());
        static::assertSame('$b', $array->getChild(0)->getChild(0)->getImage());
    }

    public function testClassStartLine(): void
    {
        static::assertSame(6, $this->getFirstClassForTestCase()->getStartLine());
    }

    public function testObjectTypeHintReturn(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        static::assertNotNull($type);
        static::assertFalse(
            $type->isScalar(),
            'object should not be scalar according to https://www.php.net/manual/en/function.is-scalar.php'
        );
        static::assertFalse($type->isArray());
        static::assertSame('object', $type->getImage());
    }

    public function testObjectTypeHintParameter(): void
    {
        $type = $this->getFirstFormalParameterForTestCase()->getType();

        static::assertFalse(
            $type->isScalar(),
            'object should not be scalar according to https://www.php.net/manual/en/function.is-scalar.php'
        );
        static::assertFalse($type->isArray());
        static::assertSame('object', $type->getImage());
    }

    public function testAbstractMethodOverriding(): void
    {
        $classes = $this->parseCodeResourceForTest()->current()->getClasses();
        $class = $classes[1];
        static::assertInstanceOf(ASTClass::class, $class);
        $method = $class->getMethods()[0];
        static::assertInstanceOf(ASTMethod::class, $method);
        $parameter = $method->getParameters()[0];
        static::assertInstanceOf(ASTParameter::class, $parameter);

        static::assertTrue($method->isAbstract());
        static::assertSame('int', $method->getReturnType()?->getImage());
        static::assertNull($parameter->getClass());
    }

    public function testGroupUseStatementTrailingComma(): void
    {
        $namespaces = $this->parseCodeResourceForTest();
        static::assertGreaterThan(0, count($namespaces));
        static::assertContainsOnlyInstancesOf(ASTNamespace::class, $namespaces);
    }

    public function testHereDocAndNowDoc(): void
    {
        /** @var ASTHeredoc $heredoc */
        $heredoc = $this->getFirstNodeOfTypeInFunction(ASTArray::class);
        $arrayElements = $heredoc->getChildren();
        $children = $arrayElements[0]->getChildren();
        $children = $children[0]->getChildren();

        /** @var ASTLiteral $literal */
        $literal = $children[0];

        static::assertSame('foobar!', $literal->getImage());

        $children = $arrayElements[1]->getChildren();
        $children = $children[0]->getChildren();

        /** @var ASTLiteral $literal */
        $literal = $children[0];

        static::assertSame('second,', $literal->getImage());
    }

    public function testDestructuringArrayReference(): void
    {
        $functionChildren = $this->getFirstFunctionForTestCase()->getChildren();
        $statements = $functionChildren[1]->getChildren();
        $assignments = $statements[1]->getChildren();
        $listElements = $assignments[0]->getChildren();
        $children = $listElements[0]->getChildren();

        /** @var ASTArrayElement $aElement */
        $aElement = $children[0];
        $arrayElement = $children[1];
        $children = $arrayElement->getChildren();
        $subElements = $children[0]->getChildren();

        /** @var ASTArrayElement $bElement */
        $bElement = $subElements[0];

        /** @var ASTArrayElement $cElement */
        $cElement = $subElements[1];

        $aElements = $aElement->getChildren();

        /** @var ASTVariable $aVariable */
        $aVariable = $aElements[0];

        $bElements = $bElement->getChildren();

        /** @var ASTVariable $bVariable */
        $bVariable = $bElements[0];

        $cElements = $cElement->getChildren();

        /** @var ASTVariable $cVariable */
        $cVariable = $cElements[0];

        static::assertTrue($aElement->isByReference());
        static::assertSame('$a', $aVariable->getImage());

        static::assertFalse($bElement->isByReference());
        static::assertSame('$b', $bVariable->getImage());

        static::assertTrue($cElement->isByReference());
        static::assertSame('$c', $cVariable->getImage());
    }

    public function testInstanceOfLiterals(): void
    {
        $functionChildren = $this->getFirstFunctionForTestCase()->getChildren();
        $statements = $functionChildren[1]->getChildren();
        $expressions = $statements[0]->getChildren();
        $expression = $expressions[0]->getChildren();

        /** @var ASTLiteral */
        $literal = $expression[0];

        /** @var ASTInstanceOfExpression $instanceOf */
        $instanceOf = $expression[1];

        /** @var ASTClassOrInterfaceReference[] $variables */
        $variables = $instanceOf->getChildren();

        static::assertCount(2, $expression);
        static::assertInstanceOf(ASTLiteral::class, $literal);
        static::assertSame('false', $literal->getImage());
        static::assertInstanceOf(ASTClassOrInterfaceReference::class, $variables[0]);
        static::assertSame('DateTimeInterface', $variables[0]->getImage());
    }

    public function testTrailingCommasInCall(): void
    {
        $functionChildren = $this->getFirstFunctionForTestCase()->getChildren();
        $statements = $functionChildren[1]->getChildren();

        /** @var ASTFunctionPostfix[] $calls */
        $calls = $statements[0]->getChildren();

        static::assertCount(1, $calls);
        static::assertInstanceOf(ASTFunctionPostfix::class, $calls[0]);

        $children = $calls[0]->getChildren();

        /** @var ASTArguments $arguments */
        $arguments = $children[1];

        static::assertInstanceOf(ASTArguments::class, $arguments);

        $arguments = $arguments->getChildren();

        static::assertCount(1, $arguments);
        static::assertInstanceOf(ASTVariable::class, $arguments[0]);
        static::assertSame('$i', $arguments[0]->getImage());
    }

    public function testTrailingCommasInUnsetCall(): void
    {
        $functionChildren = $this->getFirstFunctionForTestCase()->getChildren();
        $statements = $functionChildren[1]->getChildren();

        /** @var ASTFunctionPostfix[] $calls */
        $calls = $statements[0]->getChildren();

        static::assertCount(1, $calls);
        static::assertSame('$i', $calls[0]->getImage());
    }

    public function testTypedProperties(): void
    {
        $class = $this->getFirstClassForTestCase();
        $children = $class->getChildren();

        $mixedDeclaration = array_shift($children);

        static::assertInstanceOf(ASTFieldDeclaration::class, $mixedDeclaration);
        static::assertFalse($mixedDeclaration->hasType());

        $message = null;

        try {
            $mixedDeclaration->getType();
        } catch (OutOfBoundsException $exception) {
            $message = $exception->getMessage();
        }

        static::assertSame('The parameter does not have a type specification.', $message);

        $declarations = array_map(function (ASTNode $child): array {
            static::assertInstanceOf(ASTFieldDeclaration::class, $child);
            $childChildren = $child->getChildren();

            return [
                $child->hasType() ? $child->getType() : null,
                $childChildren[1],
            ];
        }, $children);

        foreach ([
            ['int', '$id'],
            ['float', '$money'],
            ['bool', '$active'],
            ['string', '$name'],
            ['array', '$list', ASTTypeArray::class],
            ['self', '$parent', ASTSelfReference::class],
            ['callable', '$event', ASTTypeCallable::class],
            ['\Closure', '$fqn', ASTClassOrInterfaceReference::class],
            ['iterable', '$actions', ASTTypeIterable::class],
            ['object', '$bag', ASTClassOrInterfaceReference::class],
            ['Role', '$role', ASTClassOrInterfaceReference::class],
            ['?int', '$idN'],
            ['?float', '$moneyN'],
            ['?bool', '$activeN'],
            ['?string', '$nameN'],
            ['?array', '$listN', ASTTypeArray::class],
            ['?self', '$parentN', ASTSelfReference::class],
            ['?callable', '$eventN', ASTTypeCallable::class],
            ['?\Closure', '$fqnN', ASTClassOrInterfaceReference::class],
            ['?iterable', '$actionsN', ASTTypeIterable::class],
            ['?object', '$bagN', ASTClassOrInterfaceReference::class],
            ['?Role', '$roleN', ASTClassOrInterfaceReference::class],
        ] as $index => $expected
        ) {
            [$expectedType, $expectedVariable] = $expected;
            $expectedTypeClass = $expected[2] ?? ASTScalarType::class;
            [$type, $variable] = $declarations[$index];

            static::assertInstanceOf(
                $expectedTypeClass,
                $type,
                "Wrong type for $expectedType $expectedVariable"
            );
            static::assertSame(ltrim($expectedType, '?'), $type->getImage());
            static::assertInstanceOf(
                ASTVariableDeclarator::class,
                $variable,
                "Wrong variable for $expectedType $expectedVariable"
            );
            static::assertSame($expectedVariable, $variable->getImage());
        }
    }

    public function testSingleTypedProperty(): void
    {
        /** @var ASTClass $class */
        $class = $this->getFirstClassForTestCase();

        /** @var ASTFieldDeclaration $field */
        $field = $class->getChild(0);
        static::assertTrue($field->hasType());
        static::assertSame('int', $field->getType()->getImage());
        static::assertTrue($field->isPrivate());
        static::assertFalse($field->isProtected());
        static::assertFalse($field->isPublic());
    }

    public function testTypedPropertiesSyntaxError(): void
    {
        $this->expectException(UnexpectedTokenException::class);
        $this->expectExceptionMessage(
            'Unexpected token: string, line: 4, col: 16, file:'
        );

        $this->parseCodeResourceForTest();
    }

    public function testArrowFunctions(): void
    {
        /** @var ASTClosure $closure */
        $closure = $this->getFirstNodeOfTypeInFunction(
            ASTFunctionPostfix::class
        )->getChild(1)->getChild(0);

        static::assertInstanceOf(ASTClosure::class, $closure);

        /** @var ASTFormalParameters $parameters */
        $parameters = $closure->getChild(0);
        static::assertInstanceOf(ASTFormalParameters::class, $parameters);
        static::assertCount(1, $parameters->getChildren());

        /** @var ASTFormalParameter $parameter */
        $parameter = $parameters->getChild(0);
        static::assertInstanceOf(ASTFormalParameter::class, $parameter);

        /** @var ASTVariableDeclarator $parameter */
        $variableDeclarator = $parameter->getChild(0);
        static::assertInstanceOf(ASTVariableDeclarator::class, $variableDeclarator);
        static::assertSame('$number', $variableDeclarator->getImage());

        /** @var ASTReturnStatement $parameters */
        $return = $closure->getChild(1);
        static::assertInstanceOf(ASTReturnStatement::class, $return);
        static::assertSame('=>', $return->getImage());
        static::assertCount(1, $return->getChildren());

        /** @var ASTExpression $expression */
        $expression = $return->getChild(0);
        static::assertInstanceOf(ASTExpression::class, $expression);
        static::assertSame([
            ASTVariable::class,
            ASTExpression::class,
            ASTLiteral::class,
        ], array_map('get_class', $expression->getChildren()));
        static::assertSame([
            '$number',
            '*',
            '2',
        ], array_map(fn(ASTNode $node) => $node->getImage(), $expression->getChildren()));
    }

    public function testArrowFunctionsWithReturnType(): void
    {
        /** @var ASTClosure $closure */
        $closure = $this->getFirstNodeOfTypeInFunction(ASTFunctionPostfix::class)->getChild(1)->getChild(0);

        static::assertInstanceOf(ASTClosure::class, $closure);

        /** @var ASTFormalParameters $parameters */
        $parameters = $closure->getChild(0);
        static::assertInstanceOf(ASTFormalParameters::class, $parameters);
        static::assertCount(1, $parameters->getChildren());

        /** @var ASTFormalParameter $parameter */
        $parameter = $parameters->getChild(0);
        static::assertInstanceOf(ASTFormalParameter::class, $parameter);

        /** @var ASTVariableDeclarator $parameter */
        $variableDeclarator = $parameter->getChild(0);
        static::assertInstanceOf(ASTVariableDeclarator::class, $variableDeclarator);
        static::assertSame('$number', $variableDeclarator->getImage());

        /** @var ASTScalarType $parameters */
        $type = $closure->getChild(1);
        static::assertInstanceOf(ASTScalarType::class, $type);
        static::assertSame('int', $type->getImage());

        /** @var ASTReturnStatement $parameters */
        $return = $closure->getChild(2);
        static::assertInstanceOf(ASTReturnStatement::class, $return);
        static::assertSame('=>', $return->getImage());
        static::assertCount(1, $return->getChildren());

        /** @var ASTExpression $expression */
        $expression = $return->getChild(0);
        static::assertInstanceOf(ASTExpression::class, $expression);
        static::assertSame([
            ASTVariable::class,
            ASTExpression::class,
            ASTLiteral::class,
        ], array_map('get_class', $expression->getChildren()));
        static::assertSame([
            '$number',
            '*',
            '2',
        ], array_map(fn(ASTNode $node) => $node->getImage(), $expression->getChildren()));
    }

    public function testTypeCovarianceAndArgumentTypeContravariance(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    public function testNullCoalescingAssignmentOperator(): void
    {
        /** @var ASTAssignmentExpression $assignment */
        $assignment = $this->getFirstNodeOfTypeInFunction(ASTAssignmentExpression::class);

        static::assertSame('??=', $assignment->getImage());
    }

    public function testUnpackingInsideArrays(): void
    {
        $expression = $this->getFirstNodeOfTypeInFunction(
            ASTArray::class
        );
        static::assertSame([
            ASTArrayElement::class,
            ASTArrayElement::class,
            ASTArrayElement::class,
            ASTArrayElement::class,
            ASTArrayElement::class,
        ], array_map('get_class', $expression->getChildren()));

        /** @var ASTNode[] $elements */
        $elements = array_map(fn($node) => $node->getChild(0), $expression->getChildren());
        static::assertSame([
            'PDepend\Source\AST\ASTLiteral',
            'PDepend\Source\AST\ASTLiteral',
            'PDepend\Source\AST\ASTExpression',
            'PDepend\Source\AST\ASTLiteral',
            'PDepend\Source\AST\ASTLiteral',
        ], array_map('get_class', $elements));

        /** @var ASTExpression $expression */
        $expression = $elements[2];
        static::assertSame([
            '...',
            '$numbers',
        ], array_map(fn(ASTNode $node) => $node->getImage(), $expression->getChildren()));
    }

    public function testNumericLiteralSeparator(): void
    {
        $expression = $this->getFirstNodeOfTypeInFunction(ASTExpression::class);
        static::assertSame([
            ASTLiteral::class,
            ASTExpression::class,
            ASTLiteral::class,
            ASTExpression::class,
            ASTLiteral::class,
            ASTExpression::class,
            ASTLiteral::class,
        ], array_map('get_class', $expression->getChildren()));

        static::assertSame('6.674_083e-11', $expression->getChild(0)->getImage());
        static::assertSame('299_792_458', $expression->getChild(2)->getImage());
        static::assertSame('0xCAFE_F00D', $expression->getChild(4)->getImage());
        static::assertSame('0b0101_1111', $expression->getChild(6)->getImage());
    }

    public function testReadOnlyNamedImport(): void
    {
        $this->expectException(OutOfBoundsException::class);

        $this->parseCodeResourceForTest()->current();
    }

    /**
     * testCatchWithoutVariable
     */
    public function testCatchWithoutVariable(): void
    {
        $catchStatement = $this->getFirstMethodForTestCase()->getFirstChildOfType(
            ASTCatchStatement::class
        );

        static::assertCount(2, $catchStatement?->getChildren() ?: []);
    }

    /**
     * testFunctionReturnTypeHintStatic
     */
    public function testFunctionReturnTypeHintStatic(): void
    {
        $type = $this->getFirstMethodForTestCase()->getReturnType();

        static::assertNotNull($type);
        static::assertFalse($type->isScalar());
        static::assertSame('static', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintNullableStatic
     */
    public function testFunctionReturnTypeHintNullableStatic(): void
    {
        $type = $this->getFirstMethodForTestCase()->getReturnType();

        static::assertNotNull($type);
        static::assertFalse($type->isScalar());
        static::assertSame('static', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintStaticWithComments
     */
    public function testFunctionReturnTypeHintStaticWithComments(): void
    {
        $type = $this->getFirstMethodForTestCase()->getReturnType();

        static::assertNotNull($type);
        static::assertFalse($type->isScalar());
        static::assertSame('static', $type->getImage());
    }

    /**
     * testFunctionParameterTypeHintByReferenceVariableArguments
     */
    public function testFunctionParameterTypeHintByReferenceVariableArguments(): void
    {
        $parameters = $this->getFirstFunctionForTestCase()->getParameters();
        $parameter = $parameters[0];
        $formalParameter = $parameter->getFormalParameter();
        $type = $formalParameter->getType();

        static::assertFalse($type->isIntersection());
        static::assertTrue($formalParameter->isPassedByReference());
        static::assertTrue($formalParameter->isVariableArgList());
    }

    public function testTrailingCommaInClosureUseList(): void
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testTrailingCommaInParameterList
     */
    public function testTrailingCommaInParameterList(): void
    {
        $method = $this->getFirstMethodForTestCase();

        static::assertCount(2, $method->getParameters());
    }

    public function testNullableTypedProperties(): void
    {
        $class = $this->getFirstClassForTestCase();
        $children = $class->getChildren();

        $declarations = array_map(function (ASTNode $child): array {
            static::assertInstanceOf(ASTFieldDeclaration::class, $child);
            $childChildren = $child->getChildren();
            static::assertTrue($child->hasType());

            return [
                $child->hasType() ? $child->getType() : null,
                $childChildren[1],
            ];
        }, $children);

        foreach ([
            ['null|int|float', '$number', ASTUnionType::class],
        ] as $index => $expected
        ) {
            [$expectedType, $expectedVariable, $expectedTypeClass] = $expected;
            [$type, $variable] = $declarations[$index];

            static::assertInstanceOf(
                $expectedTypeClass,
                $type,
                "Wrong type for $expectedType $expectedVariable"
            );
            static::assertSame(ltrim($expectedType, '?'), $type->getImage());
            static::assertInstanceOf(
                ASTVariableDeclarator::class,
                $variable,
                "Wrong variable for $expectedType $expectedVariable"
            );
            static::assertSame($expectedVariable, $variable->getImage());
        }
    }

    protected function createPHPParser(Tokenizer $tokenizer, Builder $builder, CacheDriver $cache): AbstractPHPParser
    {
        return $this->getMockForAbstractClass(
            AbstractPHPParser::class,
            [$tokenizer, $builder, $cache]
        );
    }
}
