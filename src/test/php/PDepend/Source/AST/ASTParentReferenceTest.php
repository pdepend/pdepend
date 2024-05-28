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

use PDepend\Source\Parser\InvalidStateException;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTParentReference} class.
 *
 * @covers \PDepend\Source\AST\ASTParentReference
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTParentReferenceTest extends ASTNodeTestCase
{
    /** The mocked reference instance. */
    protected ASTClassOrInterfaceReference&MockObject $referenceMock;

    /**
     * testGetTypeDelegatesCallToInjectedReferenceObject
     */
    public function testGetTypeDelegatesCallToInjectedReferenceObject(): void
    {
        $reference = $this->createNodeInstance();
        $this->referenceMock->expects(static::once())
            ->method('getType');

        $reference->getType();
    }

    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames(): void
    {
        $reference = $this->createNodeInstance();
        static::assertEquals(
            [
                'reference',
                'context',
                'comment',
                'metadata',
                'nodes',
            ],
            $reference->__sleep()
        );
    }

    /**
     * testParentReferenceAllocationOutsideOfClassScopeThrowsExpectedException
     */
    public function testParentReferenceAllocationOutsideOfClassScopeThrowsExpectedException(): void
    {
        $this->expectException(InvalidStateException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParentReferenceInClassWithoutParentThrowsException
     */
    public function testParentReferenceInClassWithoutParentThrowsException(): void
    {
        $this->expectException(InvalidStateException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParentReferenceMemberPrimaryPrefixOutsideOfClassScopeThrowsExpectedException
     */
    public function testParentReferenceMemberPrimaryPrefixOutsideOfClassScopeThrowsExpectedException(): void
    {
        $this->expectException(InvalidStateException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testGetImageReturnsExpectedValue
     *
     * @since 1.0.0
     */
    public function testGetImageReturnsExpectedValue(): void
    {
        $reference = $this->createNodeInstance();
        static::assertEquals('parent', $reference->getImage());
    }

    /**
     * testParentReferenceHasExpectedStartLine
     */
    public function testParentReferenceHasExpectedStartLine(): void
    {
        $reference = $this->getFirstParentReferenceInClass();
        static::assertEquals(5, $reference->getStartLine());
    }

    /**
     * testParentReferenceHasExpectedStartColumn
     */
    public function testParentReferenceHasExpectedStartColumn(): void
    {
        $reference = $this->getFirstParentReferenceInClass();
        static::assertEquals(20, $reference->getStartColumn());
    }

    /**
     * testParentReferenceHasExpectedEndLine
     */
    public function testParentReferenceHasExpectedEndLine(): void
    {
        $reference = $this->getFirstParentReferenceInClass();
        static::assertEquals(5, $reference->getEndLine());
    }

    /**
     * testParentReferenceHasExpectedEndColumn
     */
    public function testParentReferenceHasExpectedEndColumn(): void
    {
        $reference = $this->getFirstParentReferenceInClass();
        static::assertEquals(25, $reference->getEndColumn());
    }

    /**
     * Creates a concrete node implementation.
     */
    protected function createNodeInstance(): ASTParentReference
    {
        $this->referenceMock = $this->getMockBuilder(ASTClassOrInterfaceReference::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new ASTParentReference($this->referenceMock);
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstParentReferenceInClass(): ASTParentReference
    {
        return $this->getFirstNodeOfTypeInClass(
            ASTParentReference::class
        );
    }
}
