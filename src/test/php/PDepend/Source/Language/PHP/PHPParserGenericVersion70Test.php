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
 * @since     0.9.20
 */

namespace PDepend\Source\Language\PHP;

use PDepend\AbstractTestCase;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\Parser\UnexpectedTokenException;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\PHPParserGeneric} class.
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\Language\PHP\PHPParserGeneric
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class PHPParserGenericVersion70Test extends AbstractTestCase
{
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

        static::assertFalse($type->isScalar());
    }

    /**
     * testFunctionReturnTypeHintInt
     */
    public function testFunctionReturnTypeHintInt(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        static::assertTrue($type->isScalar());
        static::assertSame('int', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintSelf
     */
    public function testFunctionReturnTypeHintSelf(): void
    {
        $type = $this->getFirstMethodForTestCase()->getReturnType();

        static::assertFalse($type->isScalar());
        static::assertSame('self', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintParent
     */
    public function testFunctionReturnTypeHintParent(): void
    {
        $type = $this->getFirstMethodForTestCase()->getReturnType();

        static::assertFalse($type->isScalar());
        static::assertSame('parent', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintFloat
     */
    public function testFunctionReturnTypeHintFloat(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        static::assertTrue($type->isScalar());
        static::assertSame('float', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintString
     */
    public function testFunctionReturnTypeHintString(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        static::assertTrue($type->isScalar());
        static::assertSame('string', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintBool
     */
    public function testFunctionReturnTypeHintBool(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        static::assertTrue($type->isScalar());
        static::assertSame('bool', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintArray
     */
    public function testFunctionReturnTypeHintArray(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        static::assertTrue($type->isArray());
        static::assertSame('array', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintCallable
     */
    public function testFunctionReturnTypeHintCallable(): void
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

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

        static::assertTrue($type->isScalar());
        static::assertSame('int', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintFloat
     */
    public function testClosureReturnTypeHintFloat(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        static::assertTrue($type->isScalar());
        static::assertSame('float', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintString
     */
    public function testClosureReturnTypeHintString(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        static::assertTrue($type->isScalar());
        static::assertSame('string', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintBool
     */
    public function testClosureReturnTypeHintBool(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        static::assertTrue($type->isScalar());
        static::assertSame('bool', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintArray
     */
    public function testClosureReturnTypeHintArray(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

        static::assertTrue($type->isArray());
        static::assertSame('array', $type->getImage());
    }

    /**
     * testClosureReturnTypeHintCallable
     */
    public function testClosureReturnTypeHintCallable(): void
    {
        $type = $this->getFirstClosureForTestCase()->getReturnType();

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
            ->getFirstChildOfType(ASTExpression::class);

        static::assertSame('<=>', $expr->getImage());
    }

    /**
     * testSpaceshipOperatorWithNumbers
     */
    public function testSpaceshipOperatorWithNumbers(): void
    {
        $expr = $this->getFirstClassMethodForTestCase()
            ->getFirstChildOfType(ASTExpression::class)
            ->getFirstChildOfType(ASTExpression::class);

        static::assertSame('<=>', $expr->getImage());
    }

    /**
     * testSpaceshipOperatorWithArrays
     *
     * @return ASTNode
     */
    public function testSpaceshipOperatorWithArrays()
    {
        $expr = $this->getFirstClassMethodForTestCase()
            ->getFirstChildOfType(ASTExpression::class)
            ->getChild(1);

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
            ->getFirstChildOfType(ASTExpression::class);

        static::assertSame('??', $expr->getImage());
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

    /**
     * @return ASTNamespace
     */
    public function testGroupUseStatement()
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

        static::assertEquals(
            'FooLibrary\Bar\Baz\ClassD',
            $class->getParentClass()->getNamespacedName()
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

    public function testMethodsCanBeCallOnInstancesReturnedByInvokableObject(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    public function testMultipleArgumentsInInvocation(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }
}
