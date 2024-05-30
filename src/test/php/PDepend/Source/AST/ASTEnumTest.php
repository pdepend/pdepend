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

namespace PDepend\Source\AST;

use InvalidArgumentException;
use PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy;
use PDepend\Metrics\Analyzer\CodeRankAnalyzer\PropertyStrategy;
use PDepend\Source\Language\PHP\PHPBuilder;
use PDepend\Util\Cache\Driver\MemoryCacheDriver;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTForInit} class.
 *
 * @covers \PDepend\Source\AST\ASTForInit
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTEnumTest extends AbstractASTArtifactTestCase
{
    /**
     * testForInitHasExpectedStartLine
     */
    public function testSerialization(): void
    {
        $enum = $this->createItem();
        $serializedClass = serialize($enum);

        /** @var ASTEnum $enum */
        $deserializedEnum = unserialize($serializedClass);

        static::assertInstanceOf(ASTEnum::class, $deserializedEnum);
        static::assertSame('test', $deserializedEnum->getImage());
        static::assertInstanceOf(ASTScalarType::class, $deserializedEnum->getType());
        static::assertSame('string', $deserializedEnum->getType()?->getImage());
    }

    public function testVisit(): void
    {
        $enum = $this->createItem();
        $strategy = new PropertyStrategy();
        $strategy->dispatch($enum);
        static::assertSame([], $strategy->getCollectedNodes());

        $strategy = new MethodStrategy();
        $strategy->dispatch($enum);

        $nodes = [];

        foreach ($strategy->getCollectedNodes() as $node) {
            $class = $node['type'];

            if (!isset($nodes[$class])) {
                $nodes[$class] = [];
            }

            $nodes[$class][] = $node['name'];
        }

        static::assertSame([
            ASTEnum::class => ['test'],
            ASTClass::class => ['test'],
            ASTNamespace::class => ['+global', '+global'],
        ], $nodes);
    }

    public function testSetTokensWithEmptyArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('An AST node should contain at least one token');

        $enum = new ASTEnum('FooBar');
        $enum->setTokens([]);
    }

    protected function createItem(): AbstractASTArtifact
    {
        $builder = new PHPBuilder();
        $builder->setCache(new MemoryCacheDriver());
        $enum = $builder->buildEnum('test', new ASTScalarType('string'));
        $enum->setNamespace(new ASTNamespace('+global'));

        $method = new ASTMethod('foobar');
        $method->addChild(new ASTFormalParameters());
        $method->setReturnClassReference($builder->buildAstClassOrInterfaceReference($enum->getImage()));

        $enum->addMethod($method);

        return $enum;
    }
}
