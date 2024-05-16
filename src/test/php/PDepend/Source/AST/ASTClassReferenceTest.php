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

/**
 * Test case for the {@link \PDepend\Source\AST\ASTClassReference} class.
 *
 * @covers \PDepend\Source\AST\ASTClassReference
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTClassReferenceTest extends ASTNodeTestCase
{
    /**
     * testGetTypeDelegatesToBuilderContextGetClass
     */
    public function testGetTypeDelegatesToBuilderContextGetClass(): void
    {
        $context = $this->getBuilderContextMock();
        $context->expects(static::once())
            ->method('getClass')
            ->with(static::equalTo(__CLASS__))
            ->will(static::returnValue($this));

        $reference = new ASTClassReference($context, __CLASS__);
        $reference->getType();
    }

    /**
     * testGetTypeCachesReturnValueOfBuilderContextGetClass
     */
    public function testGetTypeCachesReturnValueOfBuilderContextGetClass(): void
    {
        $context = $this->getBuilderContextMock();
        $context->expects(static::exactly(1))
            ->method('getClass')
            ->with(static::equalTo(__CLASS__))
            ->will(static::returnValue($this));

        $reference = new ASTClassReference($context, __CLASS__);
        $reference->getType();
    }

    /**
     * testReturnValueOfMagicSleepContainsContextProperty
     */
    public function testReturnValueOfMagicSleepContainsContextProperty(): void
    {
        static::assertEquals(
            [
                'context',
                'comment',
                'metadata',
                'nodes',
            ],
            $this->createNodeInstance()->__sleep()
        );
    }

    /**
     * testClassReferenceHasExpectedStartLine
     */
    public function testClassReferenceHasExpectedStartLine(): void
    {
        $reference = $this->getFirstReferenceInFunction(__METHOD__);
        static::assertEquals(4, $reference->getStartLine());
    }

    /**
     * testClassReferenceHasExpectedStartColumn
     */
    public function testClassReferenceHasExpectedStartColumn(): void
    {
        $reference = $this->getFirstReferenceInFunction(__METHOD__);
        static::assertEquals(16, $reference->getStartColumn());
    }

    /**
     * testClassReferenceHasExpectedEndLine
     */
    public function testClassReferenceHasExpectedEndLine(): void
    {
        $reference = $this->getFirstReferenceInFunction(__METHOD__);
        static::assertEquals(4, $reference->getEndLine());
    }

    /**
     * testClassReferenceHasExpectedEndColumn
     */
    public function testClassReferenceHasExpectedEndColumn(): void
    {
        $reference = $this->getFirstReferenceInFunction(__METHOD__);
        static::assertEquals(18, $reference->getEndColumn());
    }

    /**
     * testReferenceInClassExtendsHasExpectedStartLine
     *
     * @since 0.10.5
     */
    public function testReferenceInClassExtendsHasExpectedStartLine(): void
    {
        $reference = $this->getFirstReferenceInClass();
        static::assertEquals(2, $reference->getStartLine());
    }

    /**
     * testReferenceInClassExtendsHasExpectedStartColumn
     *
     * @since 0.10.5
     */
    public function testReferenceInClassExtendsHasExpectedStartColumn(): void
    {
        $reference = $this->getFirstReferenceInClass();
        static::assertEquals(65, $reference->getStartColumn());
    }

    /**
     * testReferenceInClassExtendsHasExpectedEndLine
     *
     * @since 0.10.5
     */
    public function testReferenceInClassExtendsHasExpectedEndLine(): void
    {
        $reference = $this->getFirstReferenceInClass();
        static::assertEquals(2, $reference->getEndLine());
    }

    /**
     * testReferenceInClassExtendsHasExpectedEndColumn
     *
     * @since 0.10.5
     */
    public function testReferenceInClassExtendsHasExpectedEndColumn(): void
    {
        $reference = $this->getFirstReferenceInClass();
        static::assertEquals(65, $reference->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     * @return ASTClassReference
     */
    private function getFirstReferenceInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase,
            ASTClassReference::class
        );
    }

    /**
     * Returns the first reference node for the currently executed test case.
     *
     * @return ASTClassReference
     * @since 0.10.5
     */
    private function getFirstReferenceInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(),
            ASTClassReference::class
        );
    }

    /**
     * Creates a concrete node implementation.
     *
     * @return ASTNode
     */
    protected function createNodeInstance()
    {
        return new ASTClassReference(
            $this->getBuilderContextMock(),
            __CLASS__
        );
    }

    /**
     * Returns a mocked builder context instance.
     *
     * @return BuilderContext
     */
    protected function getBuilderContextMock()
    {
        $context = $this->getMockBuilder(BuilderContext::class)
            ->getMock();

        return $context;
    }
}
