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

use PDepend\Source\Builder\BuilderContext;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * description
 *
 * @covers \PDepend\Source\AST\ASTClassOrInterfaceReference
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTClassOrInterfaceReferenceTest extends ASTNodeTestCase
{
    /**
     * testReturnValueOfMagicSleepContainsContextProperty
     */
    public function testReturnValueOfMagicSleepContainsContextProperty(): void
    {
        $reference = new ASTClassOrInterfaceReference(
            $this->getBuilderContextMock(),
            __CLASS__
        );

        static::assertEquals(
            [
                'context',
                'comment',
                'metadata',
                'nodes',
            ],
            $reference->__sleep()
        );
    }

    /**
     * testGetTypeDelegatesToBuilderContextGetClassOrInterface
     */
    public function testGetTypeDelegatesToBuilderContextGetClassOrInterface(): void
    {
        $class = $this->getMockBuilder(ASTClass::class)
            ->setConstructorArgs([__CLASS__])
            ->getMock();
        $context = $this->getBuilderContextMock();
        $context->expects(static::once())
            ->method('getClassOrInterface')
            ->with(static::equalTo(__CLASS__))
            ->will(static::returnValue($class));

        $reference = new ASTClassOrInterfaceReference(
            $context,
            __CLASS__
        );

        $reference->getType();
    }

    /**
     * testGetTypeCachesReturnValueOfBuilderContextGetClassOrInterface
     */
    public function testGetTypeCachesReturnValueOfBuilderContextGetClassOrInterface(): void
    {
        $class = $this->getMockBuilder(ASTClass::class)
            ->setConstructorArgs([__CLASS__])
            ->getMock();
        $context = $this->getBuilderContextMock();
        $context->expects(static::exactly(1))
            ->method('getClassOrInterface')
            ->with(static::equalTo(__CLASS__))
            ->will(static::returnValue($class));

        $reference = new ASTClassOrInterfaceReference(
            $context,
            __CLASS__
        );

        $reference->getType();
    }

    /**
     * testReferenceHasExpectedStartLine
     */
    public function testReferenceHasExpectedStartLine(): void
    {
        $reference = $this->getFirstReferenceInFunction();
        static::assertEquals(2, $reference->getStartLine());
    }

    /**
     * testReferenceHasExpectedStartColumn
     */
    public function testReferenceHasExpectedStartColumn(): void
    {
        $reference = $this->getFirstReferenceInFunction();
        static::assertEquals(14, $reference->getStartColumn());
    }

    /**
     * testReferenceHasExpectedEndLine
     */
    public function testReferenceHasExpectedEndLine(): void
    {
        $reference = $this->getFirstReferenceInFunction();
        static::assertEquals(2, $reference->getEndLine());
    }

    /**
     * testReferenceHasExpectedEndColumn
     */
    public function testReferenceHasExpectedEndColumn(): void
    {
        $reference = $this->getFirstReferenceInFunction();
        static::assertEquals(29, $reference->getEndColumn());
    }

    /**
     * testReferenceInInterfaceExtendsHasExpectedStartLine
     *
     * @since 0.10.5
     */
    public function testReferenceInInterfaceExtendsHasExpectedStartLine(): void
    {
        $reference = $this->getFirstReferenceInInterface();
        static::assertEquals(3, $reference->getStartLine());
    }

    /**
     * testReferenceInInterfaceExtendsHasExpectedStartColumn
     *
     * @since 0.10.5
     */
    public function testReferenceInInterfaceExtendsHasExpectedStartColumn(): void
    {
        $reference = $this->getFirstReferenceInInterface();
        static::assertEquals(13, $reference->getStartColumn());
    }

    /**
     * testReferenceInInterfaceExtendsHasExpectedEndLine
     *
     * @since 0.10.5
     */
    public function testReferenceInInterfaceExtendsHasExpectedEndLine(): void
    {
        $reference = $this->getFirstReferenceInInterface();
        static::assertEquals(3, $reference->getEndLine());
    }

    /**
     * testReferenceInInterfaceExtendsHasExpectedEndColumn
     *
     * @since 0.10.5
     */
    public function testReferenceInInterfaceExtendsHasExpectedEndColumn(): void
    {
        $reference = $this->getFirstReferenceInInterface();
        static::assertEquals(15, $reference->getEndColumn());
    }

    /**
     * testReferenceInClassImplementsHasExpectedStartLine
     *
     * @since 0.10.5
     */
    public function testReferenceInClassImplementsHasExpectedStartLine(): void
    {
        $reference = $this->getFirstReferenceInClass();
        static::assertEquals(2, $reference->getStartLine());
    }

    /**
     * testReferenceInClassImplementsHasExpectedStartColumn
     *
     * @since 0.10.5
     */
    public function testReferenceInClassImplementsHasExpectedStartColumn(): void
    {
        $reference = $this->getFirstReferenceInClass();
        static::assertEquals(68, $reference->getStartColumn());
    }

    /**
     * testReferenceInClassImplementsHasExpectedEndLine
     *
     * @since 0.10.5
     */
    public function testReferenceInClassImplementsHasExpectedEndLine(): void
    {
        $reference = $this->getFirstReferenceInClass();
        static::assertEquals(2, $reference->getEndLine());
    }

    /**
     * testReferenceInClassImplementsHasExpectedEndColumn
     *
     * @since 0.10.5
     */
    public function testReferenceInClassImplementsHasExpectedEndColumn(): void
    {
        $reference = $this->getFirstReferenceInClass();
        static::assertEquals(68, $reference->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstReferenceInFunction(): ASTClassOrInterfaceReference
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTClassOrInterfaceReference::class
        );
    }

    /**
     * Returns the first reference node for the currently executed test case.
     *
     * @since 0.10.5
     */
    private function getFirstReferenceInClass(): ASTClassOrInterfaceReference
    {
        return $this->getFirstNodeOfTypeInClass(
            ASTClassOrInterfaceReference::class
        );
    }

    /**
     * Returns the first reference node for the currently executed test case.
     *
     * @since 0.10.5
     */
    private function getFirstReferenceInInterface(): ASTClassOrInterfaceReference
    {
        return $this->getFirstNodeOfTypeInInterface(
            ASTClassOrInterfaceReference::class
        );
    }

    /**
     * Creates a concrete node implementation.
     */
    protected function createNodeInstance(): AbstractASTNode|ASTAnonymousClass
    {
        return new ASTClassOrInterfaceReference(
            $this->getBuilderContextMock(),
            __CLASS__
        );
    }

    /**
     * Returns a mocked builder context instance.
     */
    protected function getBuilderContextMock(): BuilderContext&MockObject
    {
        $context = $this->getMockBuilder(BuilderContext::class)
            ->getMock();

        return $context;
    }
}
