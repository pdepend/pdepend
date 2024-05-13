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
 * @since 1.0.0
 */

namespace PDepend\Source\AST;

use PDepend\Source\Parser\InvalidStateException;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTTraitAdaptationPrecedence} class.
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTTraitAdaptationPrecedence
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 1.0.0
 *
 * @group unittest
 */
class ASTTraitAdaptationPrecedenceTest extends ASTNodeTestCase
{
    /**
     * testTraitAdaptationPrecedenceHasExpectedNumberOfTraitReferences
     */
    public function testTraitAdaptationPrecedenceHasExpectedNumberOfTraitReferences(): void
    {
        $stmt = $this->getFirstTraitAdaptationPrecedenceInClass();
        $this->assertCount(
            3,
            $stmt->findChildrenOfType(
                ASTTraitReference::class
            )
        );
    }

    /**
     * testTraitAdaptationPrecedenceWithoutQualifiedReferenceThrowsExpectedException
     */
    public function testTraitAdaptationPrecedenceWithoutQualifiedReferenceThrowsExpectedException(): void
    {
        $this->expectException(InvalidStateException::class);

        $this->getFirstTraitAdaptationPrecedenceInClass();
    }

    /**
     * testTraitAdaptationPrecedence
     *
     * @return ASTTraitAdaptationPrecedence
     * @since 1.0.2
     */
    public function testTraitAdaptationPrecedence()
    {
        $precedence = $this->getFirstTraitAdaptationPrecedenceInClass();
        $this->assertInstanceOf(ASTTraitAdaptationPrecedence::class, $precedence);

        return $precedence;
    }

    /**
     * testTraitAdaptationPrecedenceHasExpectedStartLine
     *
     * @param ASTTraitAdaptationPrecedence $precedence
     *
     * @depends testTraitAdaptationPrecedence
     */
    public function testTraitAdaptationPrecedenceHasExpectedStartLine($precedence): void
    {
        $this->assertEquals(6, $precedence->getStartLine());
    }

    /**
     * testTraitAdaptationPrecedenceHasExpectedStartColumn
     *
     * @param ASTTraitAdaptationPrecedence $precedence
     *
     * @depends testTraitAdaptationPrecedence
     */
    public function testTraitAdaptationPrecedenceHasExpectedStartColumn($precedence): void
    {
        $this->assertEquals(9, $precedence->getStartColumn());
    }

    /**
     * testTraitAdaptationPrecedenceHasExpectedEndLine
     *
     * @param ASTTraitAdaptationPrecedence $precedence
     *
     * @depends testTraitAdaptationPrecedence
     */
    public function testTraitAdaptationPrecedenceHasExpectedEndLine($precedence): void
    {
        $this->assertEquals(8, $precedence->getEndLine());
    }

    /**
     * testTraitAdaptationPrecedenceHasExpectedEndColumn
     *
     * @param ASTTraitAdaptationPrecedence $precedence
     *
     * @depends testTraitAdaptationPrecedence
     */
    public function testTraitAdaptationPrecedenceHasExpectedEndColumn($precedence): void
    {
        $this->assertEquals(56, $precedence->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return ASTTraitAdaptationPrecedence
     */
    private function getFirstTraitAdaptationPrecedenceInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(),
            ASTTraitAdaptationPrecedence::class
        );
    }

    /**
     * testTraitReference
     *
     * @return ASTTraitReference
     * @since 1.0.2
     */
    public function testTraitReference()
    {
        $reference = $this->getFirstTraitReferenceInClass();
        $this->assertInstanceOf(ASTTraitReference::class, $reference);

        return $reference;
    }

    /**
     * testTraitReferenceHasExpectedStartLine
     *
     * @param ASTTraitReference $reference
     *
     * @depends testTraitReference
     */
    public function testTraitReferenceHasExpectedStartLine($reference): void
    {
        $this->assertEquals(6, $reference->getStartLine());
    }

    /**
     * testTraitReferenceHasExpectedStartColumn
     *
     * @param ASTTraitReference $reference
     *
     * @depends testTraitReference
     */
    public function testTraitReferenceHasExpectedStartColumn($reference): void
    {
        $this->assertEquals(9, $reference->getStartColumn());
    }

    /**
     * testTraitReferenceHasExpectedEndLine
     *
     * @param ASTTraitReference $reference
     *
     * @depends testTraitReference
     */
    public function testTraitReferenceHasExpectedEndLine($reference): void
    {
        $this->assertEquals(6, $reference->getEndLine());
    }

    /**
     * testTraitReferenceHasExpectedEndColumn
     *
     * @param ASTTraitReference $reference
     *
     * @depends testTraitReference
     */
    public function testTraitReferenceHasExpectedEndColumn($reference): void
    {
        $this->assertEquals(36, $reference->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return ASTTraitReference
     */
    private function getFirstTraitReferenceInClass()
    {
        return $this->getFirstTraitAdaptationPrecedenceInClass()
            ->getFirstChildOfType(ASTTraitReference::class);
    }
}
