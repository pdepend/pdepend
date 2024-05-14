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

use PDepend\AbstractTestCase;
use PDepend\Source\AST\ASTArray;
use PDepend\Source\AST\ASTArrayIndexExpression;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTClassOrInterfaceReference;
use PDepend\Source\AST\ASTCompoundExpression;
use PDepend\Source\AST\ASTConstantDeclarator;
use PDepend\Source\AST\ASTConstantDefinition;
use PDepend\Source\AST\ASTConstantPostfix;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\AST\ASTFormalParameter;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\AST\ASTParameter;
use PDepend\Source\AST\ASTReturnStatement;
use PDepend\Source\AST\ASTSelfReference;
use PDepend\Source\AST\ASTValue;
use PDepend\Source\AST\ASTVariable;
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
class PHPParserVersion72Test extends AbstractTestCase
{
    /**
     * testParserAllowsKeywordCallableAsPropertyName
     */
    public function testParserAllowsKeywordCallableAsPropertyName(): void
    {
        $method = $this->getFirstClassMethodForTestCase();
        $this->assertNotNull($method);
    }

    /**
     * @return \PDepend\Source\AST\AbstractASTClassOrInterface[]
     */
    public function testParserResolvesDependenciesInDocComments()
    {
        $namespaces = $this->parseCodeResourceForTest();
        $classes = $namespaces[0]->getClasses();
        $dependencies = $classes[0]->findChildrenOfType(ASTClassOrInterfaceReference::class);

        $this->assertCount(1, $dependencies);

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
        /** @var Tokenizer $tokenizer */
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

        $this->assertEquals('0b0100110100111', $literal->getImage());
    }

    /**
     * testParserHandlesStaticMemberExpressionSyntax
     */
    public function testParserHandlesStaticMemberExpressionSyntax(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        $expr = $function->getFirstChildOfType(ASTCompoundExpression::class);

        $this->assertInstanceOf(ASTCompoundExpression::class, $expr);
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
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * Tests that ::class is allowed PHP >= 5.5.
     */
    public function testDoubleColonClass(): void
    {
        $this->assertInstanceOf(ASTArtifactList::class, $this->parseCodeResourceForTest());
    }

    /**
     * testComplexExpressionInParameterInitializer
     */
    public function testComplexExpressionInParameterInitializer(): void
    {
        $node = $this->getFirstFunctionForTestCase()
            ->getFirstChildOfType(ASTFormalParameter::class);

        $this->assertNotNull($node);
    }

    /**
     * testComplexExpressionInConstantInitializer
     */
    public function testComplexExpressionInConstantDeclarator(): void
    {
        $node = $this->getFirstClassForTestCase()
            ->getFirstChildOfType(ASTConstantDeclarator::class);

        $this->assertNotNull($node);
    }

    /**
     * testComplexExpressionInFieldDeclaration
     */
    public function testComplexExpressionInFieldDeclaration(): void
    {
        $node = $this->getFirstClassForTestCase()
            ->getFirstChildOfType(ASTFieldDeclaration::class);

        $this->assertNotNull($node);
    }

    /**
     * testPowExpressionInMethodBody
     */
    public function testPowExpressionInMethodBody(): void
    {
        $node = $this->getFirstClassForTestCase()
            ->getFirstChildOfType(ASTReturnStatement::class);

        $this->assertSame('**', $node->getChild(0)->getChild(1)->getImage());
    }

    /**
     * testPowExpressionInFieldDeclaration
     */
    public function testPowExpressionInFieldDeclaration(): void
    {
        $node = $this->getFirstClassForTestCase()
            ->getFirstChildOfType(ASTFieldDeclaration::class);

        $this->assertNotNull($node);
    }

    public function testUseStatement(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    public function testEllipsisOperatorInFunctionCall(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
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
        $this->assertInstanceOf(ASTMemberPrimaryPrefix::class, $memberPrefix);
        $this->assertTrue($memberPrefix->isStatic());
        $this->assertInstanceOf(ASTSelfReference::class, $memberPrefix->getChild(0));
        $children = $memberPrefix->getChild(1)->getChildren();
        $this->assertCount(1, $children);
        $this->assertInstanceOf(ASTArrayIndexExpression::class, $children[0]);
        $children = $children[0]->getChildren();
        $this->assertCount(2, $children);
        $this->assertInstanceOf(ASTVariable::class, $children[0]);
        $this->assertInstanceOf(ASTLiteral::class, $children[1]);
        $this->assertSame('$foo', $children[0]->getImage());
        $this->assertSame("'bar'", $children[1]->getImage());
    }

    /**
     * Tests issue with constant array concatenation.
     * https://github.com/pdepend/pdepend/issues/299
     */
    public function testConstantArrayConcatenation(): void
    {
        /** @var ASTClass $class */
        $class = $this->getFirstClassForTestCase();

        /** @var ASTConstantDefinition[] $sontants */
        $constants = $class->getChildren();

        $this->assertCount(2, $constants);
        $this->assertInstanceOf(ASTConstantDefinition::class, $constants[0]);
        $this->assertInstanceOf(ASTConstantDefinition::class, $constants[1]);

        /** @var ASTConstantDeclarator[] $declarators */
        $declarators = $constants[1]->getChildren();

        $this->assertCount(1, $declarators);
        $this->assertInstanceOf(ASTConstantDeclarator::class, $declarators[0]);

        /** @var ASTExpression $expression */
        $expression = $declarators[0]->getValue()->getValue();

        $this->assertInstanceOf(ASTExpression::class, $expression);

        $nodes = $expression->getChildren();
        $this->assertInstanceOf(ASTMemberPrimaryPrefix::class, $nodes[0]);
        $this->assertInstanceOf(ASTExpression::class, $nodes[1]);
        $this->assertSame('+', $nodes[1]->getImage());
        $this->assertInstanceOf(ASTArray::class, $nodes[2]);

        $nodes = $nodes[0]->getChildren();
        $this->assertInstanceOf(ASTSelfReference::class, $nodes[0]);
        $this->assertInstanceOf(ASTConstantPostfix::class, $nodes[1]);
        $this->assertSame('A', $nodes[1]->getImage());
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
        /** @var Tokenizer $tokenizer */
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

        $this->assertTrue($type->isScalar());
        $this->assertEquals('int', $type->getImage());
    }

    /**
     * testFormalParameterScalarTypeHintString
     */
    public function testFormalParameterScalarTypeHintString(): void
    {
        $type = $this->getFirstFormalParameterForTestCase()->getType();

        $this->assertTrue($type->isScalar());
        $this->assertEquals('string', $type->getImage());
    }

    /**
     * testFormalParameterScalarTypeHintFloat
     */
    public function testFormalParameterScalarTypeHintFloat(): void
    {
        $type = $this->getFirstFormalParameterForTestCase()->getType();

        $this->assertTrue($type->isScalar());
        $this->assertEquals('float', $type->getImage());
    }

    /**
     * testFormalParameterScalarTypeHintBool
     */
    public function testFormalParameterScalarTypeHintBool(): void
    {
        $type = $this->getFirstFormalParameterForTestCase()->getType();

        $this->assertTrue($type->isScalar());
        $this->assertEquals('bool', $type->getImage());
    }

    /**
     * testFormalParameterStillWorksWithTypeHintArray
     */
    public function testFormalParameterStillWorksWithTypeHintArray(): void
    {
        $type = $this->getFirstFormalParameterForTestCase()->getChild(0);

        $this->assertFalse($type->isScalar());
    }

    /**
     * testFunctionReturnTypeHintInt
     */
    public function testFunctionReturnTypeHintInt(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        $this->assertTrue($type->isScalar());
        $this->assertSame('int', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintFloat
     */
    public function testFunctionReturnTypeHintFloat(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        $this->assertTrue($type->isScalar());
        $this->assertSame('float', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintString
     */
    public function testFunctionReturnTypeHintString(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        $this->assertTrue($type->isScalar());
        $this->assertSame('string', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintBool
     */
    public function testFunctionReturnTypeHintBool(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        $this->assertTrue($type->isScalar());
        $this->assertSame('bool', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintArray
     */
    public function testFunctionReturnTypeHintArray(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        $this->assertTrue($type->isArray());
        $this->assertSame('array', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintCallable
     */
    public function testFunctionReturnTypeHintCallable(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        $this->assertFalse($type->isScalar());
        $this->assertFalse($type->isArray());

        $this->assertSame('callable', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintClass
     */
    public function testFunctionReturnTypeHintClass(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        $this->assertFalse($type->isScalar());
        $this->assertFalse($type->isArray());

        $this->assertSame('\\Iterator', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintInt
     */
    public function testClosureReturnTypeHintInt(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        $this->assertTrue($type->isScalar());
        $this->assertSame('int', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintFloat
     */
    public function testClosureReturnTypeHintFloat(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        $this->assertTrue($type->isScalar());
        $this->assertSame('float', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintString
     */
    public function testClosureReturnTypeHintString(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        $this->assertTrue($type->isScalar());
        $this->assertSame('string', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintBool
     */
    public function testClosureReturnTypeHintBool(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        $this->assertTrue($type->isScalar());
        $this->assertSame('bool', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintArray
     */
    public function testClosureReturnTypeHintArray(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        $this->assertTrue($type->isArray());
        $this->assertSame('array', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintCallable
     */
    public function testClosureReturnTypeHintCallable(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        $this->assertFalse($type->isScalar());
        $this->assertFalse($type->isArray());

        $this->assertSame('callable', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintClass
     */
    public function testClosureReturnTypeHintClass(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        $this->assertFalse($type->isScalar());
        $this->assertFalse($type->isArray());

        $this->assertSame('\\Iterator', $type->getImage());
    }

    /**
     * testSpaceshipOperatorWithStrings
     */
    public function testSpaceshipOperatorWithStrings(): void
    {
        $expr = $this->getFirstClassMethodForTestCase()
            ->getFirstChildOfType(ASTExpression::class)
            ->getFirstChildOfType(ASTExpression::class);

        $this->assertSame('<=>', $expr->getImage());
    }

    /**
     * testSpaceshipOperatorWithNumbers
     */
    public function testSpaceshipOperatorWithNumbers(): void
    {
        $expr = $this->getFirstClassMethodForTestCase()
            ->getFirstChildOfType(ASTExpression::class)
            ->getFirstChildOfType(ASTExpression::class);

        $this->assertSame('<=>', $expr->getImage());
    }

    /**
     * testSpaceshipOperatorWithArrays
     *
     * @return ASTExpression
     */
    public function testSpaceshipOperatorWithArrays()
    {
        $expr = $this->getFirstClassMethodForTestCase()
            ->getFirstChildOfType(ASTExpression::class)
            ->getChild(1);

        $this->assertSame('<=>', $expr->getImage());

        return $expr;
    }

    /**
     * @depends testSpaceshipOperatorWithArrays
     */
    public function testSpaceshipOperatorHasExpectedStartLine(ASTExpression $expr): void
    {
        $this->assertSame(6, $expr->getStartLine());
    }

    /**
     * @depends testSpaceshipOperatorWithArrays
     */
    public function testSpaceshipOperatorHasExpectedEndLine(ASTExpression $expr): void
    {
        $this->assertSame(6, $expr->getEndLine());
    }

    /**
     * @depends testSpaceshipOperatorWithArrays
     */
    public function testSpaceshipOperatorHasExpectedStartColumn(ASTExpression $expr): void
    {
        $this->assertSame(27, $expr->getStartColumn());
    }

    /**
     * @depends testSpaceshipOperatorWithArrays
     */
    public function testSpaceshipOperatorHasExpectedEndColumn(ASTExpression $expr): void
    {
        $this->assertSame(29, $expr->getEndColumn());
    }

    /**
     * testNullCoalesceOperator
     */
    public function testNullCoalesceOperator(): void
    {
        $expr = $this->getFirstClassMethodForTestCase()
            ->getFirstChildOfType(ASTExpression::class)
            ->getFirstChildOfType(ASTExpression::class);

        $this->assertSame('??', $expr->getImage());
    }

    public function testListKeywordAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertNotNull($method);
    }

    public function testListKeywordAsFunctionNameThrowsException(): void
    {
        $this->expectException(UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * @return ASTNamespace
     */
    public function testGroupUseStatement()
    {
        $namespaces = $this->parseCodeResourceForTest();
        $this->assertNotNull($namespaces);

        return $namespaces[0];
    }

    /**
     * @depends testGroupUseStatement
     */
    public function testGroupUseStatementClassNameResolution(ASTNamespace $namespace): void
    {
        $classes = $namespace->getClasses();
        $class = $classes[0];

        $this->assertEquals(
            'FooLibrary\Bar\Baz\ClassB',
            $class->getParentClass()->getNamespacedName()
        );
    }

    /**
     * @depends testGroupUseStatement
     */
    public function testGroupUseStatementAliasResolution(ASTNamespace $namespace): void
    {
        $classes = $namespace->getClasses();
        $class = $classes[1];

        $this->assertEquals(
            'FooLibrary\Bar\Baz\ClassD',
            $class->getParentClass()->getNamespacedName()
        );
    }

    public function testUniformVariableSyntax(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    public function testConstantNameArray(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    public function testClassConstantNames(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    public function testClassConstantNamesAccessed(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    public function testClassMethodNames(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    public function testClassMethodNamesInvoked(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    public function testYieldFrom(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    public function testParseList(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    public function testParenthesisAroundCallableParsesArguments(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    public function testKeywordsAsMethodNames(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $classes = $namespaces[0]->getClasses();
        $methods = $classes[0]->getMethods();

        $this->assertSame('trait', $methods[0]->getName());
        $this->assertSame('callable', $methods[1]->getName());
        $this->assertSame('insteadof', $methods[2]->getName());
    }

    public function testKeywordsAsConstants(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $classes = $namespaces[0]->getClasses();
        /** @var ASTConstantDeclarator[] $constants */
        $constants = $classes[0]->findChildrenOfType(ASTConstantDeclarator::class);

        $this->assertSame('trait', $constants[0]->getImage());
        $this->assertSame('callable', $constants[1]->getImage());
        $this->assertSame('insteadof', $constants[2]->getImage());
    }

    /**
     * Tests that the parser does not throw an exception when it detects a reserved
     * keyword in constant class names.
     */
    public function testReservedKeyword(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    public function testConstVisibility(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    public function testConstVisibilityInInterfacePublic(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
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
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    public function testNullableTypeHintParameter(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    public function testNullableTypeHintReturn(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    public function testParseListWithVariableKey(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    public function testIterableTypeHintParameter(): void
    {
        $type = $this->getFirstFormalParameterForTestCase()->getType();

        $this->assertFalse($type->isScalar());
        $this->assertTrue($type->isArray());
        $this->assertSame('iterable', $type->getImage());
    }

    public function testIterableTypeHintReturn(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        $this->assertFalse($type->isScalar());
        $this->assertTrue($type->isArray());
        $this->assertSame('iterable', $type->getImage());
    }

    public function testVoidTypeHintReturn(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        $this->assertTrue($type->isScalar());
        $this->assertFalse($type->isArray());
        $this->assertSame('void', $type->getImage());
    }

    public function testVoidTypeHintReturnNamespaced(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();
        $this->assertTrue($type->isScalar());
        $this->assertFalse($type->isArray());
        $this->assertSame('void', $type->getImage());
    }

    /**
     * testSymmetricArrayDestructuringEmptySlot
     */
    public function testSymmetricArrayDestructuringEmptySlot(): void
    {
        /** @var ASTArray $expr */
        $array = $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            ASTArray::class
        );
        $this->assertCount(1, $array->getChildren());
        $this->assertSame('$b', $array->getChild(0)->getChild(0)->getImage());
    }

    public function testClassStartLine(): void
    {
        $this->assertSame(6, $this->getFirstClassForTestCase()->getStartLine());
    }

    public function testObjectTypeHintReturn(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        $this->assertFalse($type->isScalar(), 'object should not be scalar according to https://www.php.net/manual/en/function.is-scalar.php');
        $this->assertFalse($type->isArray());
        $this->assertSame('object', $type->getImage());
    }

    public function testObjectTypeHintParameter(): void
    {
        $type = $this->getFirstFormalParameterForTestCase()->getType();

        $this->assertFalse($type->isScalar(), 'object should not be scalar according to https://www.php.net/manual/en/function.is-scalar.php');
        $this->assertFalse($type->isArray());
        $this->assertSame('object', $type->getImage());
    }

    public function testAbstractMethodOverriding(): void
    {
        /** @var ASTArtifactList $classes */
        $classes = $this->parseCodeResourceForTest()->current()->getClasses();
        /** @var ASTClass $class */
        $class = $classes[1];
        /** @var ASTArtifactList $classes */
        $methods = $class->getMethods();
        /** @var ASTMethod $method */
        $method = $methods[0];
        /** @var ASTArtifactList $parameters */
        $parameters = $method->getParameters();
        /** @var ASTParameter $parameter */
        $parameter = $parameters[0];

        $this->assertTrue($method->isAbstract());
        $this->assertSame('int', $method->getReturnType()->getImage());
        $this->assertNull($parameter->getClass());
    }

    /**
     * @return ASTNamespace
     */
    public function testGroupUseStatementTrailingComma()
    {
        $namespaces = $this->parseCodeResourceForTest();
        $this->assertGreaterThan(0, count($namespaces));
        $this->assertContainsOnlyInstancesOf(ASTNamespace::class, $namespaces);
    }

    /**
     * @return AbstractPHPParser
     */
    protected function createPHPParser(Tokenizer $tokenizer, Builder $builder, CacheDriver $cache)
    {
        return $this->getAbstractClassMock(
            'PDepend\\Source\\Language\\PHP\\AbstractPHPParser',
            [$tokenizer, $builder, $cache]
        );
    }
}
