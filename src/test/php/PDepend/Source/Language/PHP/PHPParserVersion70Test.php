<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2015, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2015 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 2.3
 */

namespace PDepend\Source\Language\PHP;

use PDepend\AbstractTest;
use PDepend\Source\Builder\Builder;
use PDepend\Source\Tokenizer\Tokenizer;
use PDepend\Util\Cache\CacheDriver;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\PHPParserVersion70} class.
 *
 * @copyright 2008-2015 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 2.3
 *
 * @covers \PDepend\Source\Language\PHP\PHPParserVersion70
 * @group unittest
 */
class PHPParserVersion70Test extends AbstractTest
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

        $this->assertSame('\Iterator', $type->getImage());
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
     * @return void
     */
    public function testSpaceshipOperatorWithArrays()
    {
        $expr = $this->getFirstClassMethodForTestCase()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTExpression')
            ->getChild(1);

        $this->assertSame('<=>', $expr->getImage());
    }

    /**
     * testSpaceshipOperatorStartLine
     *
     * @return void
     */
    public function testSpaceshipOperatorStartLine()
    {
        $expr = $this->getFirstClassMethodForTestCase()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTExpression')
            ->getChild(1);

        $this->assertSame(6, $expr->getStartLine());
    }

    /**
     * testSpaceshipOperatorEndLine
     *
     * @return void
     */
    public function testSpaceshipOperatorEndLine()
    {
        $expr = $this->getFirstClassMethodForTestCase()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTExpression')
            ->getChild(1);

        $this->assertSame(6, $expr->getEndLine());
    }

    /**
     * testSpaceshipOperatorStartColumn
     *
     * @return void
     */
    public function testSpaceshipOperatorStartColumn()
    {
        $expr = $this->getFirstClassMethodForTestCase()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTExpression')
            ->getChild(1);

        $this->assertSame(27, $expr->getStartColumn());
    }

    /**
     * testSpaceshipOperatorEndColumn
     *
     * @return void
     */
    public function testSpaceshipOperatorEndColumn()
    {
        $expr = $this->getFirstClassMethodForTestCase()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTExpression')
            ->getChild(1);

        $this->assertSame(29, $expr->getEndColumn());
    }

    /**
     * @param \PDepend\Source\Tokenizer\Tokenizer $tokenizer
     * @param \PDepend\Source\Builder\Builder $builder
     * @param \PDepend\Util\Cache\CacheDriver $cache
     * @return \PDepend\Source\Language\PHP\AbstractPHPParser
     */
    protected function createPHPParser(Tokenizer $tokenizer, Builder $builder, CacheDriver $cache)
    {
        return $this->getMockForAbstractClass(
            'PDepend\\Source\\Language\\PHP\\PHPParserVersion70',
            array($tokenizer, $builder, $cache)
        );
    }
}
