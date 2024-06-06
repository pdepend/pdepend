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

/**
 * Test case for the {@link \PDepend\Source\AST\ASTInstanceOfExpression} class.
 *
 * @covers \PDepend\Source\AST\ASTInstanceOfExpression
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTInstanceOfExpressionTest extends ASTNodeTestCase
{
    /**
     * Tests that the created instanceof object graph has the expected structure.
     */
    public function testInstanceOfExpressionGraphWithStringIdentifier(): void
    {
        $this->assertInstanceOfGraphStatic(
            $this->parseCodeResourceForTest()
                ->current()
                ->getFunctions()
                ->current(),
            __FUNCTION__
        );
    }

    /**
     * Tests that the created instanceof object graph has the expected structure.
     */
    public function testInstanceOfExpressionGraphWithLocalNamespaceIdentifier(): void
    {
        $this->assertInstanceOfGraphStatic(
            $this->parseCodeResourceForTest()
                ->current()
                ->getFunctions()
                ->current(),
            'foo\bar\Baz'
        );
    }

    /**
     * Tests that the created instanceof object graph has the expected structure.
     */
    public function testInstanceOfExpressionGraphWithAbsoluteNamespaceIdentifier(): void
    {
        $this->assertInstanceOfGraphStatic(
            $this->parseCodeResourceForTest()
                ->current()
                ->getFunctions()
                ->current(),
            '\foo\bar\Baz'
        );
    }

    /**
     * Tests that the created instanceof object graph has the expected structure.
     */
    public function testInstanceOfExpressionGraphWithAliasedNamespaceIdentifier(): void
    {
        $this->assertInstanceOfGraphStatic(
            $this->parseCodeResourceForTest()
                ->current()
                ->getFunctions()
                ->current(),
            '\foo\bar\Baz'
        );
    }

    /**
     * Tests that the created instanceof object graph has the expected structure.
     */
    public function testInstanceOfExpressionGraphWithStdClass(): void
    {
        $this->assertInstanceOfGraphStatic(
            $this->parseCodeResourceForTest()
                ->current()
                ->getFunctions()
                ->current(),
            'stdClass'
        );
    }

    /**
     * Tests that the created instanceof object graph has the expected structure.
     */
    public function testInstanceOfExpressionGraphWithPHPIncompleteClass(): void
    {
        $this->assertInstanceOfGraphStatic(
            $this->parseCodeResourceForTest()
                ->current()
                ->getFunctions()
                ->current(),
            '__PHP_Incomplete_Class'
        );
    }

    /**
     * Tests that the created instanceof object graph has the expected structure.
     */
    public function testInstanceOfExpressionGraphWithStaticProperty(): void
    {
        $this->assertInstanceOfGraphProperty(
            $this->parseCodeResourceForTest()
                ->current()
                ->getFunctions()
                ->current(),
            '::'
        );
    }

    /**
     * Tests the instanceof expression object graph.
     *
     * @param ASTNode $parent The parent ast node.
     * @param string $image The expected type image.
     */
    protected function assertInstanceOfGraphStatic(ASTNode $parent, string $image): void
    {
        $this->assertInstanceOfGraph(
            $parent,
            $image,
            ASTClassOrInterfaceReference::class
        );
    }

    /**
     * Tests the instanceof expression object graph.
     *
     * @param ASTNode $parent The parent ast node.
     * @param string $image The expected type image.
     */
    protected function assertInstanceOfGraphProperty(ASTNode $parent, string $image): void
    {
        $this->assertInstanceOfGraph(
            $parent,
            $image,
            ASTMemberPrimaryPrefix::class
        );
    }

    /**
     * Tests the instanceof expression object graph.
     *
     * @param ASTNode $parent The parent ast node.
     * @param string $image The expected type image.
     * @param class-string $type The expected class or interface type.
     */
    protected function assertInstanceOfGraph(ASTNode $parent, string $image, string $type): void
    {
        $instanceOf = $parent->getFirstChildOfType(
            ASTInstanceOfExpression::class
        );

        $reference = $instanceOf?->getChild(0);
        static::assertInstanceOf($type, $reference);
        static::assertEquals($image, $reference->getImage());
    }

    /**
     * Creates a arguments node.
     */
    protected function createNodeInstance(): ASTInstanceOfExpression
    {
        return new ASTInstanceOfExpression();
    }
}
