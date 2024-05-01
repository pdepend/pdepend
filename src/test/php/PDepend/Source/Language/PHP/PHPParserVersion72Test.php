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
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTConstantDeclarator;
use PDepend\Source\AST\ASTConstantDefinition;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\AST\ASTParameter;
use PDepend\Source\AST\ASTReturnStatement;
use PDepend\Source\AST\ASTValue;
use PDepend\Source\Builder\Builder;
use PDepend\Source\Tokenizer\Token;
use PDepend\Source\Tokenizer\Tokenizer;
use PDepend\Source\Tokenizer\Tokens;
use PDepend\Util\Cache\CacheDriver;
use PDepend\Util\Cache\Driver\MemoryCacheDriver;
use ReflectionMethod;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\PHPParserVersion72} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @covers \PDepend\Source\Language\PHP\PHPParserVersion72
 * @group unittest
 */
class PHPParserVersion72Test extends AbstractTestCase
{
    /**
     * testParserAllowsKeywordCallableAsPropertyName
     *
     * @return void
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
        $dependencies = $classes[0]->findChildrenOfType('PDepend\\Source\\AST\\ASTClassOrInterfaceReference');

        $this->assertCount(1, $dependencies);

        return $dependencies;
    }

    /**
     * Tests that the parser throws an exception when trying to parse an array
     * when being at the end of the file.
     *
     * @return void
     */
    public function testParserThrowsUnexpectedTokenExceptionForArrayWithEOF(): void
    {
        $this->expectException(
            '\\PDepend\\Source\\Parser\\TokenStreamEndException'
        );
        $this->expectExceptionMessage(
            'Unexpected end of token stream in file:'
        );

        $cache = new MemoryCacheDriver();
        $builder = new PHPBuilder();
        /** @var Tokenizer $tokenizer */
        $tokenizer = $this->getMockBuilder('PDepend\\Source\\Tokenizer\\Tokenizer')
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
     *
     * @return void
     */
    public function testParserHandlesBinaryIntegerLiteral(): void
    {
        $method  = $this->getFirstMethodForTestCase();
        $literal = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTLiteral');

        $this->assertEquals('0b0100110100111', $literal->getImage());
    }

    /**
     * testParserHandlesStaticMemberExpressionSyntax
     *
     * @return void
     */
    public function testParserHandlesStaticMemberExpressionSyntax(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        $expr = $function->getFirstChildOfType('PDepend\\Source\\AST\\ASTCompoundExpression');

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTCompoundExpression', $expr);
    }

    /**
     * testParserThrowsExpectedExceptionForTraitAsClassName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForTraitAsClassName(): void
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTraitAsFunctionName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForTraitAsFunctionName(): void
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTraitAsInterfaceName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForTraitAsInterfaceName(): void
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTraitAsNamespaceName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForTraitAsNamespaceName(): void
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTraitAsCalledFunction
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForTraitAsCalledFunction(): void
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsClassName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsClassName(): void
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsFunctionName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsFunctionName(): void
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsInterfaceName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsInterfaceName(): void
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsInterfaceName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsNamespaceName(): void
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsCalledFunction
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsCalledFunction(): void
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForCallableAsClassName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForCallableAsClassName(): void
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForCallableAsFunctionName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForCallableAsFunctionName(): void
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForCallableAsInterfaceName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForCallableAsInterfaceName(): void
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForCallableAsInterfaceName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForCallableAsNamespaceName(): void
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForCallableAsCalledFunction
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForCallableAsCalledFunction(): void
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * @return void
     */
    public function testMagicTraitConstantInString(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * Tests that ::class is allowed PHP >= 5.5.
     *
     * @return void
     */
    public function testDoubleColonClass(): void
    {
        $this->assertInstanceOf('PDepend\Source\AST\ASTArtifactList', $this->parseCodeResourceForTest());
    }

    /**
     * testComplexExpressionInParameterInitializer
     *
     * @return void
     */
    public function testComplexExpressionInParameterInitializer(): void
    {
        $node = $this->getFirstFunctionForTestCase()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTFormalParameter');

        $this->assertNotNull($node);
    }

    /**
     * testComplexExpressionInConstantInitializer
     *
     * @return void
     */
    public function testComplexExpressionInConstantDeclarator(): void
    {
        $node = $this->getFirstClassForTestCase()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTConstantDeclarator');

        $this->assertNotNull($node);
    }

    /**
     * testComplexExpressionInFieldDeclaration
     *
     * @return void
     */
    public function testComplexExpressionInFieldDeclaration(): void
    {
        $node = $this->getFirstClassForTestCase()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTFieldDeclaration');

        $this->assertNotNull($node);
    }

    /**
     * testPowExpressionInMethodBody
     *
     * @return void
     */
    public function testPowExpressionInMethodBody(): void
    {
        $node = $this->getFirstClassForTestCase()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTReturnStatement');

        $this->assertSame('**', $node->getChild(0)->getChild(1)->getImage());
    }

    /**
     * testPowExpressionInFieldDeclaration
     *
     * @return void
     */
    public function testPowExpressionInFieldDeclaration(): void
    {
        $node = $this->getFirstClassForTestCase()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTFieldDeclaration');

        $this->assertNotNull($node);
    }

    /**
     * @return void
     */
    public function testUseStatement(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * @return void
     */
    public function testEllipsisOperatorInFunctionCall(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * Test that static array property is well linked to its self:: / static:: accesses.
     *
     * @return void
     */
    public function testStaticArrayProperty(): void
    {
        /** @var ASTReturnStatement[] $returnStatements */
        $returnStatements = $this
            ->getFirstMethodForTestCase()
            ->findChildrenOfType('PDepend\\Source\\AST\\ASTReturnStatement');

        /** @var ASTMemberPrimaryPrefix $memberPrefix */
        $memberPrefix = $returnStatements[0]->getChild(0);
        $this->assertInstanceOf('PDepend\Source\AST\ASTMemberPrimaryPrefix', $memberPrefix);
        $this->assertTrue($memberPrefix->isStatic());
        $this->assertInstanceOf('PDepend\Source\AST\ASTSelfReference', $memberPrefix->getChild(0));
        $children = $memberPrefix->getChild(1)->getChildren();
        $this->assertCount(1, $children);
        $this->assertInstanceOf('PDepend\Source\AST\ASTArrayIndexExpression', $children[0]);
        $children = $children[0]->getChildren();
        $this->assertCount(2, $children);
        $this->assertInstanceOf('PDepend\Source\AST\ASTVariable', $children[0]);
        $this->assertInstanceOf('PDepend\Source\AST\ASTLiteral', $children[1]);
        $this->assertSame('$foo', $children[0]->getImage());
        $this->assertSame("'bar'", $children[1]->getImage());
    }

    /**
     * Tests issue with constant array concatenation.
     * https://github.com/pdepend/pdepend/issues/299
     *
     * @return void
     */
    public function testConstantArrayConcatenation(): void
    {
        /** @var ASTClass $class */
        $class = $this->getFirstClassForTestCase();

        /** @var ASTConstantDefinition[] $sontants */
        $constants = $class->getChildren();

        $this->assertCount(2, $constants);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTConstantDefinition', $constants[0]);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTConstantDefinition', $constants[1]);

        /** @var ASTConstantDeclarator[] $declarators */
        $declarators = $constants[1]->getChildren();

        $this->assertCount(1, $declarators);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTConstantDeclarator', $declarators[0]);

        /** @var ASTExpression $expression */
        $expression = $declarators[0]->getValue()->getValue();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTExpression', $expression);

        $nodes = $expression->getChildren();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMemberPrimaryPrefix', $nodes[0]);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTExpression', $nodes[1]);
        $this->assertSame('+', $nodes[1]->getImage());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTArray', $nodes[2]);

        $nodes = $nodes[0]->getChildren();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTSelfReference', $nodes[0]);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTConstantPostfix', $nodes[1]);
        $this->assertSame('A', $nodes[1]->getImage());
    }

    /**
     * Tests that the parser throws an exception when trying to parse a value
     * when given a non-value token type.
     *
     * @return void
     */
    public function testParserThrowsUnexpectedTokenExceptionForOF(): void
    {
        $this->expectException(
            '\\PDepend\\Source\\Parser\\UnexpectedTokenException'
        );
        $this->expectExceptionMessage(
            'Unexpected token: function, line: 1, col: 1, file:'
        );

        $cache = new MemoryCacheDriver();
        $builder = new PHPBuilder();
        /** @var Tokenizer $tokenizer */
        $tokenizer = $this->getMockBuilder('PDepend\\Source\\Tokenizer\\Tokenizer')
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
     *
     * @return void
     */
    public function testFormalParameterScalarTypeHintInt(): void
    {
        $type = $this->getFirstFormalParameterForTestCase()->getType();

        $this->assertTrue($type->isScalar());
        $this->assertEquals('int', $type->getImage());
    }

    /**
     * testFormalParameterScalarTypeHintString
     *
     * @return void
     */
    public function testFormalParameterScalarTypeHintString(): void
    {
        $type = $this->getFirstFormalParameterForTestCase()->getType();

        $this->assertTrue($type->isScalar());
        $this->assertEquals('string', $type->getImage());
    }

    /**
     * testFormalParameterScalarTypeHintFloat
     *
     * @return void
     */
    public function testFormalParameterScalarTypeHintFloat(): void
    {
        $type = $this->getFirstFormalParameterForTestCase()->getType();

        $this->assertTrue($type->isScalar());
        $this->assertEquals('float', $type->getImage());
    }

    /**
     * testFormalParameterScalarTypeHintBool
     *
     * @return void
     */
    public function testFormalParameterScalarTypeHintBool(): void
    {
        $type = $this->getFirstFormalParameterForTestCase()->getType();

        $this->assertTrue($type->isScalar());
        $this->assertEquals('bool', $type->getImage());
    }

    /**
     * testFormalParameterStillWorksWithTypeHintArray
     *
     * @return void
     */
    public function testFormalParameterStillWorksWithTypeHintArray(): void
    {
        $type = $this->getFirstFormalParameterForTestCase()->getChild(0);

        $this->assertFalse($type->isScalar());
    }

    /**
     * testFunctionReturnTypeHintInt
     *
     * @return void
     */
    public function testFunctionReturnTypeHintInt(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        $this->assertTrue($type->isScalar());
        $this->assertSame('int', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintFloat
     *
     * @return void
     */
    public function testFunctionReturnTypeHintFloat(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        $this->assertTrue($type->isScalar());
        $this->assertSame('float', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintString
     *
     * @return void
     */
    public function testFunctionReturnTypeHintString(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        $this->assertTrue($type->isScalar());
        $this->assertSame('string', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintBool
     *
     * @return void
     */
    public function testFunctionReturnTypeHintBool(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        $this->assertTrue($type->isScalar());
        $this->assertSame('bool', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintArray
     *
     * @return void
     */
    public function testFunctionReturnTypeHintArray(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        $this->assertTrue($type->isArray());
        $this->assertSame('array', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintCallable
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return void
     */
    public function testClosureReturnTypeHintInt(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        $this->assertTrue($type->isScalar());
        $this->assertSame('int', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintFloat
     *
     * @return void
     */
    public function testClosureReturnTypeHintFloat(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        $this->assertTrue($type->isScalar());
        $this->assertSame('float', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintString
     *
     * @return void
     */
    public function testClosureReturnTypeHintString(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        $this->assertTrue($type->isScalar());
        $this->assertSame('string', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintBool
     *
     * @return void
     */
    public function testClosureReturnTypeHintBool(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        $this->assertTrue($type->isScalar());
        $this->assertSame('bool', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintArray
     *
     * @return void
     */
    public function testClosureReturnTypeHintArray(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        $this->assertTrue($type->isArray());
        $this->assertSame('array', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintCallable
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return void
     */
    public function testSpaceshipOperatorWithStrings(): void
    {
        $expr = $this->getFirstClassMethodForTestCase()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTExpression')
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTExpression');

        $this->assertSame('<=>', $expr->getImage());
    }

    /**
     * testSpaceshipOperatorWithNumbers
     *
     * @return void
     */
    public function testSpaceshipOperatorWithNumbers(): void
    {
        $expr = $this->getFirstClassMethodForTestCase()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTExpression')
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTExpression');

        $this->assertSame('<=>', $expr->getImage());
    }

    /**
     * testSpaceshipOperatorWithArrays
     *
     * @return \PDepend\Source\AST\ASTExpression
     */
    public function testSpaceshipOperatorWithArrays()
    {
        $expr = $this->getFirstClassMethodForTestCase()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTExpression')
            ->getChild(1);

        $this->assertSame('<=>', $expr->getImage());

        return $expr;
    }

    /**
     * @param \PDepend\Source\AST\ASTExpression $expr
     * @return void
     * @depends testSpaceshipOperatorWithArrays
     */
    public function testSpaceshipOperatorHasExpectedStartLine(ASTExpression $expr): void
    {
        $this->assertSame(6, $expr->getStartLine());
    }

    /**
     * @param \PDepend\Source\AST\ASTExpression $expr
     * @return void
     * @depends testSpaceshipOperatorWithArrays
     */
    public function testSpaceshipOperatorHasExpectedEndLine(ASTExpression $expr): void
    {
        $this->assertSame(6, $expr->getEndLine());
    }

    /**
     * @param \PDepend\Source\AST\ASTExpression $expr
     * @return void
     * @depends testSpaceshipOperatorWithArrays
     */
    public function testSpaceshipOperatorHasExpectedStartColumn(ASTExpression $expr): void
    {
        $this->assertSame(27, $expr->getStartColumn());
    }

    /**
     * @param \PDepend\Source\AST\ASTExpression $expr
     * @return void
     * @depends testSpaceshipOperatorWithArrays
     */
    public function testSpaceshipOperatorHasExpectedEndColumn(ASTExpression $expr): void
    {
        $this->assertSame(29, $expr->getEndColumn());
    }

    /**
     * testNullCoalesceOperator
     *
     * @return void
     */
    public function testNullCoalesceOperator(): void
    {
        $expr = $this->getFirstClassMethodForTestCase()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTExpression')
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTExpression');

        $this->assertSame('??', $expr->getImage());
    }

    /**
     * @return void
     */
    public function testListKeywordAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertNotNull($method);
    }

    /**
     * @return void
     */
    public function testListKeywordAsFunctionNameThrowsException(): void
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * @return \PDepend\Source\AST\ASTNamespace
     */
    public function testGroupUseStatement()
    {
        $namespaces = $this->parseCodeResourceForTest();
        $this->assertNotNull($namespaces);

        return $namespaces[0];
    }

    /**
     * @param \PDepend\Source\AST\ASTNamespace $namespace
     * @return void
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
     * @param \PDepend\Source\AST\ASTNamespace $namespace
     * @return void
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

    /**
     * @return void
     */
    public function testUniformVariableSyntax(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * @return void
     */
    public function testConstantNameArray(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * @return void
     */
    public function testClassConstantNames(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * @return void
     */
    public function testClassConstantNamesAccessed(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * @return void
     */
    public function testClassMethodNames(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * @return void
     */
    public function testClassMethodNamesInvoked(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * @return void
     */
    public function testYieldFrom(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * @return void
     */
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
        $constants = $classes[0]->findChildrenOfType('PDepend\\Source\\AST\\ASTConstantDeclarator');

        $this->assertSame('trait', $constants[0]->getImage());
        $this->assertSame('callable', $constants[1]->getImage());
        $this->assertSame('insteadof', $constants[2]->getImage());
    }

    public function testCallableKeywordAsClassName(): void
    {
        $this->expectException(
            '\\PDepend\\Source\\Parser\\UnexpectedTokenException'
        );
        $this->expectExceptionMessage(
            'Unexpected token: callable, line: 3, col: 7, file: '
        );

        $this->parseCodeResourceForTest();
    }

    public function testTraitKeywordAsClassName(): void
    {
        $this->expectException(
            '\\PDepend\\Source\\Parser\\UnexpectedTokenException'
        );
        $this->expectExceptionMessage(
            'Unexpected token: trait, line: 3, col: 7, file: '
        );

        $this->parseCodeResourceForTest();
    }

    public function testInsteadofKeywordAsClassName(): void
    {
        $this->expectException(
            '\\PDepend\\Source\\Parser\\UnexpectedTokenException'
        );
        $this->expectExceptionMessage(
            'Unexpected token: insteadof, line: 3, col: 7, file: '
        );

        $this->parseCodeResourceForTest();
    }

    public function testCallableKeywordAsInterfaceName(): void
    {
        $this->expectException(
            '\\PDepend\\Source\\Parser\\UnexpectedTokenException'
        );
        $this->expectExceptionMessage(
            'Unexpected token: callable, line: 3, col: 11, file: '
        );

        $this->parseCodeResourceForTest();
    }

    public function testTraitKeywordAsInterfaceName(): void
    {
        $this->expectException(
            '\\PDepend\\Source\\Parser\\UnexpectedTokenException'
        );
        $this->expectExceptionMessage(
            'Unexpected token: trait, line: 3, col: 11, file: '
        );

        $this->parseCodeResourceForTest();
    }

    public function testInsteadofKeywordAsInterfaceName(): void
    {
        $this->expectException(
            '\\PDepend\\Source\\Parser\\UnexpectedTokenException'
        );
        $this->expectExceptionMessage(
            'Unexpected token: insteadof, line: 3, col: 11, file: '
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that the parser does not throw an exception when it detects a reserved
     * keyword in constant class names.
     *
     * @return void
     */
    public function testReservedKeyword(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * @return void
     */
    public function testConstVisibility(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * @return void
     */
    public function testConstVisibilityInInterfacePublic(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * @return void
     */
    public function testConstVisibilityInInterfaceProtected(): void
    {
        $this->expectException(
            '\\PDepend\\Source\\Parser\\InvalidStateException'
        );
        $this->expectExceptionMessage(
            'Constant can\'t be declared private or protected in interface "TestInterface".'
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * @return void
     */
    public function testConstVisibilityInInterfacePrivate(): void
    {
        $this->expectException(
            '\\PDepend\\Source\\Parser\\InvalidStateException'
        );
        $this->expectExceptionMessage(
            'Constant can\'t be declared private or protected in interface "TestInterface".'
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * @return void
     */
    public function testCatchMultipleExceptionClasses(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * @return void
     */
    public function testNullableTypeHintParameter(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * @return void
     */
    public function testNullableTypeHintReturn(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * @return void
     */
    public function testParseListWithVariableKey(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * @return void
     */
    public function testIterableTypeHintParameter(): void
    {
        $type = $this->getFirstFormalParameterForTestCase()->getType();

        $this->assertFalse($type->isScalar());
        $this->assertTrue($type->isArray());
        $this->assertSame('iterable', $type->getImage());
    }

    /**
     * @return void
     */
    public function testIterableTypeHintReturn(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        $this->assertFalse($type->isScalar());
        $this->assertTrue($type->isArray());
        $this->assertSame('iterable', $type->getImage());
    }

    /**
     * @return void
     */
    public function testVoidTypeHintReturn(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        $this->assertTrue($type->isScalar());
        $this->assertFalse($type->isArray());
        $this->assertSame('void', $type->getImage());
    }

    /**
     * @return void
     */
    public function testVoidTypeHintReturnNamespaced(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();
        $this->assertTrue($type->isScalar());
        $this->assertFalse($type->isArray());
        $this->assertSame('void', $type->getImage());
    }

    /**
     * testSymmetricArrayDestructuringEmptySlot
     *
     * @return void
     */
    public function testSymmetricArrayDestructuringEmptySlot(): void
    {
        /** @var ASTArray $expr */
        $array = $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTArray'
        );
        $this->assertCount(1, $array->getChildren());
        $this->assertSame('$b', $array->getChild(0)->getChild(0)->getImage());
    }

    /**
     * @return void
     */
    public function testClassStartLine(): void
    {
        $this->assertSame(6, $this->getFirstClassForTestCase()->getStartLine());
    }

    /**
     * @return void
     */
    public function testObjectTypeHintReturn(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        $this->assertFalse($type->isScalar(), 'object should not be scalar according to https://www.php.net/manual/en/function.is-scalar.php');
        $this->assertFalse($type->isArray());
        $this->assertSame('object', $type->getImage());
    }

    /**
     * @return void
     */
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
     * @return \PDepend\Source\AST\ASTNamespace
     */
    public function testGroupUseStatementTrailingComma()
    {
        $namespaces = $this->parseCodeResourceForTest();
        $this->assertGreaterThan(0, count($namespaces));
        $this->assertContainsOnlyInstancesOf('PDepend\\Source\\AST\\ASTNamespace', $namespaces);
    }

    /**
     * @param \PDepend\Source\Tokenizer\Tokenizer $tokenizer
     * @param \PDepend\Source\Builder\Builder $builder
     * @param \PDepend\Util\Cache\CacheDriver $cache
     * @return \PDepend\Source\Language\PHP\AbstractPHPParser
     */
    protected function createPHPParser(Tokenizer $tokenizer, Builder $builder, CacheDriver $cache)
    {
        return $this->getAbstractClassMock(
            'PDepend\\Source\\Language\\PHP\\PHPParserVersion72',
            array($tokenizer, $builder, $cache)
        );
    }

    /**
     */
    public function testTrailingCommasInUnsetCall(): void
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);
        $this->expectExceptionMessage('Unexpected token: ), line: 4, col: 14');

        $this->parseCodeResourceForTest();
    }
}
