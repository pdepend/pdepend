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

use PDepend\Source\Builder\Builder;
use PDepend\Source\Builder\BuilderContext;
use PDepend\Source\Builder\BuilderContext\GlobalBuilderContext;
use PDepend\Source\Parser\InvalidStateException;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTStaticReference} class.
 *
 * @covers \PDepend\Source\AST\ASTStaticReference
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTStaticReferenceTest extends ASTNodeTestCase
{
    /**
     * testGetTypeReturnsInjectedConstructorTargetArgument
     */
    public function testGetTypeReturnsInjectedConstructorTargetArgument(): void
    {
        $target = $this->getMockForAbstractClass(
            AbstractASTClassOrInterface::class,
            [__CLASS__]
        );
        $context = $this->getMockBuilder(BuilderContext::class)
            ->getMock();

        $reference = new ASTStaticReference($context, $target);
        static::assertSame($target, $reference->getType());
    }

    /**
     * testGetTypeInvokesBuilderContextWhenTypeInstanceIsNull
     */
    public function testGetTypeInvokesBuilderContextWhenTypeInstanceIsNull(): void
    {
        $target = $this->getMockForAbstractClass(
            AbstractASTClassOrInterface::class,
            [__CLASS__]
        );

        $builder = $this->getMockBuilder(Builder::class)
            ->getMock();
        $builder->expects(static::once())
            ->method('getClassOrInterface');

        $context = new GlobalBuilderContext($builder);

        $reference = new ASTStaticReference($context, $target);
        $reference = unserialize(serialize($reference));
        static::assertInstanceOf(ASTStaticReference::class, $reference);
        $reference->getType();
    }

    /**
     * Tests that an invalid static results in the expected exception.
     */
    public function testStaticReferenceAllocationOutsideOfClassScopeThrowsExpectedException(): void
    {
        $this->expectException(
            InvalidStateException::class
        );
        $this->expectExceptionMessage(
            'The keyword "static" was used outside of a class/method scope.'
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that an invalid static results in the expected exception.
     */
    public function testStaticReferenceMemberPrimaryPrefixOutsideOfClassScopeThrowsExpectedException(): void
    {
        $this->expectException(
            InvalidStateException::class
        );
        $this->expectExceptionMessage(
            'The keyword "static" was used outside of a class/method scope.'
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * testMagicSelfReturnsExpectedSetOfPropertyNames
     */
    public function testMagicSelfReturnsExpectedSetOfPropertyNames(): void
    {
        $reference = $this->createNodeInstance();
        static::assertEquals(
            [
                'qualifiedName',
                'context',
                'comment',
                'metadata',
                'nodes',
            ],
            $reference->__sleep()
        );
    }

    /**
     * testGetImageReturnsExpectedValue
     *
     * @since 1.0.0
     */
    public function testGetImageReturnsExpectedValue(): void
    {
        $reference = $this->createNodeInstance();
        static::assertEquals('static', $reference->getImage());
    }

    /**
     * testStaticReference
     *
     * @since 1.0.2
     */
    public function testStaticReference(): ASTStaticReference
    {
        $reference = $this->getFirstStaticReferenceInClass();
        static::assertInstanceOf(ASTStaticReference::class, $reference);

        return $reference;
    }

    /**
     * testStaticReferenceHasExpectedStartLine
     *
     * @depends testStaticReference
     */
    public function testStaticReferenceHasExpectedStartLine(ASTStaticReference $reference): void
    {
        static::assertEquals(5, $reference->getStartLine());
    }

    /**
     * testStaticReferenceHasExpectedStartColumn
     *
     * @depends testStaticReference
     */
    public function testStaticReferenceHasExpectedStartColumn(ASTStaticReference $reference): void
    {
        static::assertEquals(13, $reference->getStartColumn());
    }

    /**
     * testStaticReferenceHasExpectedEndLine
     *
     * @depends testStaticReference
     */
    public function testStaticReferenceHasExpectedEndLine(ASTStaticReference $reference): void
    {
        static::assertEquals(5, $reference->getEndLine());
    }

    /**
     * testStaticReferenceHasExpectedEndColumn
     *
     * @depends testStaticReference
     */
    public function testStaticReferenceHasExpectedEndColumn(ASTStaticReference $reference): void
    {
        static::assertEquals(18, $reference->getEndColumn());
    }

    /**
     * Creates a concrete node implementation.
     */
    protected function createNodeInstance(): ASTStaticReference
    {
        $context = $this->getMockBuilder(BuilderContext::class)
            ->getMock();

        return new ASTStaticReference(
            $context,
            $this->getMockForAbstractClass(
                AbstractASTClassOrInterface::class,
                [__CLASS__]
            )
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstStaticReferenceInClass(): ASTStaticReference
    {
        return $this->getFirstNodeOfTypeInClass(
            ASTStaticReference::class
        );
    }
}
