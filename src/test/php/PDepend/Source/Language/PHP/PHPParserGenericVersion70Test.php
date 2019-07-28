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

use PDepend\AbstractTest;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTNamespace;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\PHPParserGeneric} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\Language\PHP\PHPParserGeneric
 * @group unittest
 */
class PHPParserGenericVersion70Test extends AbstractTest
{
    /**
     * testFormalParameterScalarTypeHintInt
     *
     * @return void
     */
    public function testFormalParameterScalarTypeHintInt()
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
    public function testFormalParameterScalarTypeHintString()
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
    public function testFormalParameterScalarTypeHintFloat()
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
    public function testFormalParameterScalarTypeHintBool()
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
    public function testFormalParameterStillWorksWithTypeHintArray()
    {
        $type = $this->getFirstFormalParameterForTestCase()->getChild(0);

        $this->assertFalse($type->isScalar());
    }

    /**
     * testFunctionReturnTypeHintInt
     *
     * @return void
     */
    public function testFunctionReturnTypeHintInt()
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
    public function testFunctionReturnTypeHintFloat()
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
    public function testFunctionReturnTypeHintString()
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
    public function testFunctionReturnTypeHintBool()
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
    public function testFunctionReturnTypeHintArray()
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
    public function testFunctionReturnTypeHintCallable()
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
    public function testFunctionReturnTypeHintClass()
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
    public function testClosureReturnTypeHintInt()
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
    public function testClosureReturnTypeHintFloat()
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
    public function testClosureReturnTypeHintString()
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
    public function testClosureReturnTypeHintBool()
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
    public function testClosureReturnTypeHintArray()
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
    public function testClosureReturnTypeHintCallable()
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
    public function testClosureReturnTypeHintClass()
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
    public function testSpaceshipOperatorWithStrings()
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
    public function testSpaceshipOperatorWithNumbers()
    {
        $expr = $this->getFirstClassMethodForTestCase()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTExpression')
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTExpression');

        $this->assertSame('<=>', $expr->getImage());
    }

    /**
     * testSpaceshipOperatorWithArrays
     *
     * @return \PDepend\Source\AST\ASTNode
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
    public function testSpaceshipOperatorHasExpectedStartLine(ASTExpression $expr)
    {
        $this->assertSame(6, $expr->getStartLine());
    }

    /**
     * @param \PDepend\Source\AST\ASTExpression $expr
     * @return void
     * @depends testSpaceshipOperatorWithArrays
     */
    public function testSpaceshipOperatorHasExpectedEndLine(ASTExpression $expr)
    {
        $this->assertSame(6, $expr->getEndLine());
    }

    /**
     * @param \PDepend\Source\AST\ASTExpression $expr
     * @return void
     * @depends testSpaceshipOperatorWithArrays
     */
    public function testSpaceshipOperatorHasExpectedStartColumn(ASTExpression $expr)
    {
        $this->assertSame(27, $expr->getStartColumn());
    }

    /**
     * @param \PDepend\Source\AST\ASTExpression $expr
     * @return void
     * @depends testSpaceshipOperatorWithArrays
     */
    public function testSpaceshipOperatorHasExpectedEndColumn(ASTExpression $expr)
    {
        $this->assertSame(29, $expr->getEndColumn());
    }

    /**
     * testNullCoalesceOperator
     *
     * @return void
     */
    public function testNullCoalesceOperator()
    {
        $expr = $this->getFirstClassMethodForTestCase()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTExpression')
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTExpression');

        $this->assertSame('??', $expr->getImage());
    }

    /**
     * @return void
     */
    public function testListKeywordAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertNotNull($method);
    }

    /**
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testListKeywordAsFunctionNameThrowsException()
    {
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
    public function testGroupUseStatementClassNameResolution(ASTNamespace $namespace)
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
    public function testGroupUseStatementAliasResolution(ASTNamespace $namespace)
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
    public function testUniformVariableSyntax()
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * @return void
     */
    public function testConstantNameArray()
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * @return void
     */
    public function testClassConstantNames()
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * @return void
     */
    public function testClassConstantNamesAccessed()
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * @return void
     */
    public function testClassMethodNames()
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * @return void
     */
    public function testClassMethodNamesInvoked()
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * @return void
     */
    public function testMethodsCanBeCallOnInstancesReturnedByInvokableObject()
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }
}
