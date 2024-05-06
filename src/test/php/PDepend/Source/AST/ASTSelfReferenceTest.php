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

use PDepend\Source\Builder\BuilderContext\GlobalBuilderContext;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTSelfReference} class.
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTSelfReference
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTSelfReferenceTest extends ASTNodeTestCase
{
    /**
     * testGetTypeReturnsInjectedConstructorTargetArgument
     */
    public function testGetTypeReturnsInjectedConstructorTargetArgument(): void
    {
        $target = $this->getAbstractClassMock(
            '\\PDepend\\Source\\AST\\AbstractASTClassOrInterface',
            [__CLASS__]
        );
        $context = $this->getMockBuilder('PDepend\\Source\\Builder\\BuilderContext')
            ->getMock();

        $reference = new ASTSelfReference($context, $target);
        $this->assertSame($target, $reference->getType());
    }

    /**
     * testGetTypeInvokesBuilderContextWhenTypeInstanceIsNull
     */
    public function testGetTypeInvokesBuilderContextWhenTypeInstanceIsNull(): void
    {
        $target = $this->getAbstractClassMock(
            '\\PDepend\\Source\\AST\\AbstractASTClassOrInterface',
            [__CLASS__]
        );

        $builder = $this->getMockBuilder('\\PDepend\\Source\\Builder\\Builder')
            ->getMock();
        $builder->expects($this->once())
            ->method('getClassOrInterface');

        $context = new GlobalBuilderContext($builder);

        $reference = new ASTSelfReference($context, $target);
        $reference = unserialize(serialize($reference));
        $reference->getType();
    }

    /**
     * testSelfReferenceAllocationOutsideOfClassScopeThrowsExpectedException
     */
    public function testSelfReferenceAllocationOutsideOfClassScopeThrowsExpectedException(): void
    {
        $this->expectException(\PDepend\Source\Parser\InvalidStateException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testSelfReferenceMemberPrimaryPrefixOutsideOfClassScopeThrowsExpectedException
     */
    public function testSelfReferenceMemberPrimaryPrefixOutsideOfClassScopeThrowsExpectedException(): void
    {
        $this->expectException(\PDepend\Source\Parser\InvalidStateException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testMagicSelfReturnsExpectedSetOfPropertyNames
     */
    public function testMagicSelfReturnsExpectedSetOfPropertyNames(): void
    {
        $reference = $this->createNodeInstance();
        $this->assertEquals(
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
        $this->assertEquals('self', $reference->getImage());
    }

    /**
     * testSelfReference
     *
     * @return ASTSelfReference
     * @since 1.0.2
     */
    public function testSelfReference()
    {
        $reference = $this->getFirstSelfReferenceInClass();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTSelfReference', $reference);

        return $reference;
    }

    /**
     * testSelfReferenceHasExpectedStartLine
     *
     * @param ASTSelfReference $reference
     *
     * @depends testSelfReference
     */
    public function testSelfReferenceHasExpectedStartLine($reference): void
    {
        $this->assertEquals(5, $reference->getStartLine());
    }

    /**
     * testSelfReferenceHasExpectedStartColumn
     *
     * @param ASTSelfReference $reference
     *
     * @depends testSelfReference
     */
    public function testSelfReferenceHasExpectedStartColumn($reference): void
    {
        $this->assertEquals(13, $reference->getStartColumn());
    }

    /**
     * testSelfReferenceHasExpectedEndLine
     *
     * @param ASTSelfReference $reference
     *
     * @depends testSelfReference
     */
    public function testSelfReferenceHasExpectedEndLine($reference): void
    {
        $this->assertEquals(5, $reference->getEndLine());
    }

    /**
     * testSelfReferenceHasExpectedEndColumn
     *
     * @param ASTSelfReference $reference
     *
     * @depends testSelfReference
     */
    public function testSelfReferenceHasExpectedEndColumn($reference): void
    {
        $this->assertEquals(16, $reference->getEndColumn($reference));
    }

    /**
     * Creates a concrete node implementation.
     *
     * @return ASTSelfReference
     */
    protected function createNodeInstance()
    {
        $context = $this->getMockBuilder('PDepend\\Source\\Builder\\BuilderContext')
            ->getMock();

        return new ASTSelfReference(
            $context,
            $this->getAbstractClassMock('\\PDepend\\Source\\AST\\AbstractASTClassOrInterface', [__CLASS__])
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return ASTSelfReference
     */
    private function getFirstSelfReferenceInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTSelfReference'
        );
    }
}
