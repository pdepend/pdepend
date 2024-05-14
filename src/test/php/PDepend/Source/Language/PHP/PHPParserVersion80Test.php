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
use PDepend\Source\AST\ASTCatchStatement;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\AST\ASTUnionType;
use PDepend\Source\AST\ASTVariableDeclarator;
use PDepend\Source\Builder\Builder;
use PDepend\Source\Tokenizer\Tokenizer;
use PDepend\Util\Cache\CacheDriver;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\AbstractPHPParser} class.
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @group unittest
 */
class PHPParserVersion80Test extends AbstractTestCase
{
    /**
     * testCatchWithoutVariable
     */
    public function testCatchWithoutVariable(): void
    {
        $catchStatement = $this->getFirstMethodForTestCase()->getFirstChildOfType(
            ASTCatchStatement::class
        );

        static::assertCount(2, $catchStatement->getChildren());
    }

    /**
     * testFunctionReturnTypeHintStatic
     */
    public function testFunctionReturnTypeHintStatic(): void
    {
        $type = $this->getFirstMethodForTestCase()->getReturnType();

        static::assertFalse($type->isScalar());
        static::assertSame('static', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintNullableStatic
     */
    public function testFunctionReturnTypeHintNullableStatic(): void
    {
        $type = $this->getFirstMethodForTestCase()->getReturnType();

        static::assertFalse($type->isScalar());
        static::assertSame('static', $type->getImage());
    }

    /**
     * testFunctionReturnTypeHintStaticWithComments
     */
    public function testFunctionReturnTypeHintStaticWithComments(): void
    {
        $type = $this->getFirstMethodForTestCase()->getReturnType();

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
        /** @var ASTClass $class */
        $class = $this->getFirstClassForTestCase();
        $children = $class->getChildren();

        static::assertTrue($children[0]->hasType());

        /** @var array[] $declarations */
        $declarations = array_map(function (ASTFieldDeclaration $child) {
            $childChildren = $child->getChildren();

            return [
                $child->hasType() ? $child->getType() : null,
                $childChildren[1],
            ];
        }, $children);

        foreach ([
            ['null|int|float', '$number', ASTUnionType::class],
        ] as $index => $expected) {
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
