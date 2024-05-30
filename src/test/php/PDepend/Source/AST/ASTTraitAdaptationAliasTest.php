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

/**
 * Test case for the {@link \PDepend\Source\AST\ASTTraitAdaptationAlias} class.
 *
 * @covers \PDepend\Source\AST\ASTTraitAdaptationAlias
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 1.0.0
 *
 * @group unittest
 */
class ASTTraitAdaptationAliasTest extends ASTNodeTestCase
{
    /**
     * testGetNewNameReturnsNullByDefault
     */
    public function testGetNewNameReturnsNullByDefault(): void
    {
        $alias = $this->getFirstTraitAdaptationAliasInClass();
        static::assertNull($alias->getNewName());
    }

    /**
     * testGetNewNameReturnsExpectedValue
     */
    public function testGetNewNameReturnsExpectedValue(): void
    {
        $alias = $this->getFirstTraitAdaptationAliasInClass();
        static::assertEquals('myMethodAlias', $alias->getNewName());
    }

    /**
     * testGetNewModifierReturnsMinusOneByDefault
     */
    public function testGetNewModifierReturnsMinusOneByDefault(): void
    {
        $alias = $this->getFirstTraitAdaptationAliasInClass();
        static::assertEquals(-1, $alias->getNewModifier());
    }

    /**
     * testGetNewModifierReturnsExpectedIsPublicValue
     */
    public function testGetNewModifierReturnsExpectedIsPublicValue(): void
    {
        $alias = $this->getFirstTraitAdaptationAliasInClass();
        static::assertEquals(
            State::IS_PUBLIC,
            $alias->getNewModifier()
        );
    }

    /**
     * testGetNewModifierReturnsExpectedIsProtectedValue
     */
    public function testGetNewModifierReturnsExpectedIsProtectedValue(): void
    {
        $alias = $this->getFirstTraitAdaptationAliasInClass();
        static::assertEquals(
            State::IS_PROTECTED,
            $alias->getNewModifier()
        );
    }

    /**
     * testMagicSleepMethodReturnsExpectedSetOfPropertyNames
     */
    public function testMagicSleepMethodReturnsExpectedSetOfPropertyNames(): void
    {
        $alias = new ASTTraitAdaptationAlias();
        static::assertSame(
            [
                'newName',
                'newModifier',
                'comment',
                'metadata',
                'nodes',
            ],
            $alias->__sleep()
        );
    }

    /**
     * testGetNewModifierReturnsExpectedIsPrivateValue
     */
    public function testGetNewModifierReturnsExpectedIsPrivateValue(): void
    {
        $alias = $this->getFirstTraitAdaptationAliasInClass();
        static::assertEquals(State::IS_PRIVATE, $alias->getNewModifier());
    }

    /**
     * testTraitAdaptationAlias
     *
     * @since 1.0.2
     */
    public function testTraitAdaptationAlias(): ASTTraitAdaptationAlias
    {
        $alias = $this->getFirstTraitAdaptationAliasInClass();
        static::assertInstanceOf(ASTTraitAdaptationAlias::class, $alias);

        return $alias;
    }

    /**
     * testTraitAdaptationAliasHasExpectedStartLine
     *
     * @depends testTraitAdaptationAlias
     */
    public function testTraitAdaptationAliasHasExpectedStartLine(ASTTraitAdaptationAlias $alias): void
    {
        static::assertEquals(6, $alias->getStartLine());
    }

    /**
     * testTraitAdaptationAliasHasExpectedStartColumn
     *
     * @depends testTraitAdaptationAlias
     */
    public function testTraitAdaptationAliasHasExpectedStartColumn(ASTTraitAdaptationAlias $alias): void
    {
        static::assertEquals(9, $alias->getStartColumn());
    }

    /**
     * testTraitAdaptationAliasHasExpectedEndLine
     *
     * @depends testTraitAdaptationAlias
     */
    public function testTraitAdaptationAliasHasExpectedEndLine(ASTTraitAdaptationAlias $alias): void
    {
        static::assertEquals(6, $alias->getEndLine());
    }

    /**
     * testTraitAdaptationAliasHasExpectedEndColumn
     *
     * @depends testTraitAdaptationAlias
     */
    public function testTraitAdaptationAliasHasExpectedEndColumn(ASTTraitAdaptationAlias $alias): void
    {
        static::assertEquals(46, $alias->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstTraitAdaptationAliasInClass(): ASTTraitAdaptationAlias
    {
        return $this->getFirstNodeOfTypeInClass(
            ASTTraitAdaptationAlias::class
        );
    }

    /**
     * testTraitReference
     *
     * @since 1.0.2
     */
    public function testTraitReference(): ASTTraitReference
    {
        $reference = $this->getFirstTraitReferenceInClass();
        static::assertInstanceOf(ASTTraitReference::class, $reference);

        return $reference;
    }

    /**
     * testTraitReferenceHasExpectedStartLine
     *
     * @depends testTraitReference
     */
    public function testTraitReferenceHasExpectedStartLine(ASTTraitReference $reference): void
    {
        static::assertEquals(7, $reference->getStartLine());
    }

    /**
     * testTraitReferenceHasExpectedStartColumn
     *
     * @depends testTraitReference
     */
    public function testTraitReferenceHasExpectedStartColumn(ASTTraitReference $reference): void
    {
        static::assertEquals(9, $reference->getStartColumn());
    }

    /**
     * testTraitReferenceHasExpectedEndLine
     *
     * @depends testTraitReference
     */
    public function testTraitReferenceHasExpectedEndLine(ASTTraitReference $reference): void
    {
        static::assertEquals(7, $reference->getEndLine());
    }

    /**
     * testTraitReferenceHasExpectedEndColumn
     *
     * @depends testTraitReference
     */
    public function testTraitReferenceHasExpectedEndColumn(ASTTraitReference $reference): void
    {
        static::assertEquals(36, $reference->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstTraitReferenceInClass(): ASTTraitReference
    {
        $node = $this->getFirstTraitAdaptationAliasInClass()
            ->getFirstChildOfType(ASTTraitReference::class);
        static::assertNotNull($node);

        return $node;
    }
}
