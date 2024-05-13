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
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTTraitAdaptationAlias
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
        $this->assertNull($alias->getNewName());
    }

    /**
     * testGetNewNameReturnsExpectedValue
     */
    public function testGetNewNameReturnsExpectedValue(): void
    {
        $alias = $this->getFirstTraitAdaptationAliasInClass();
        $this->assertEquals('myMethodAlias', $alias->getNewName());
    }

    /**
     * testGetNewModifierReturnsMinusOneByDefault
     */
    public function testGetNewModifierReturnsMinusOneByDefault(): void
    {
        $alias = $this->getFirstTraitAdaptationAliasInClass();
        $this->assertEquals(-1, $alias->getNewModifier());
    }

    /**
     * testGetNewModifierReturnsExpectedIsPublicValue
     */
    public function testGetNewModifierReturnsExpectedIsPublicValue(): void
    {
        $alias = $this->getFirstTraitAdaptationAliasInClass();
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertSame(
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
        $this->assertEquals(State::IS_PRIVATE, $alias->getNewModifier());
    }

    /**
     * testTraitAdaptationAlias
     *
     * @return ASTTraitAdaptationAlias
     * @since 1.0.2
     */
    public function testTraitAdaptationAlias()
    {
        $alias = $this->getFirstTraitAdaptationAliasInClass();
        $this->assertInstanceOf(ASTTraitAdaptationAlias::class, $alias);

        return $alias;
    }

    /**
     * testTraitAdaptationAliasHasExpectedStartLine
     *
     * @param ASTTraitAdaptationAlias $alias
     *
     * @depends testTraitAdaptationAlias
     */
    public function testTraitAdaptationAliasHasExpectedStartLine($alias): void
    {
        $this->assertEquals(6, $alias->getStartLine());
    }

    /**
     * testTraitAdaptationAliasHasExpectedStartColumn
     *
     * @param ASTTraitAdaptationAlias $alias
     *
     * @depends testTraitAdaptationAlias
     */
    public function testTraitAdaptationAliasHasExpectedStartColumn($alias): void
    {
        $this->assertEquals(9, $alias->getStartColumn());
    }

    /**
     * testTraitAdaptationAliasHasExpectedEndLine
     *
     * @param ASTTraitAdaptationAlias $alias
     *
     * @depends testTraitAdaptationAlias
     */
    public function testTraitAdaptationAliasHasExpectedEndLine($alias): void
    {
        $this->assertEquals(6, $alias->getEndLine());
    }

    /**
     * testTraitAdaptationAliasHasExpectedEndColumn
     *
     * @param ASTTraitAdaptationAlias $alias
     *
     * @depends testTraitAdaptationAlias
     */
    public function testTraitAdaptationAliasHasExpectedEndColumn($alias): void
    {
        $this->assertEquals(46, $alias->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return ASTTraitAdaptationAlias
     */
    private function getFirstTraitAdaptationAliasInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(),
            ASTTraitAdaptationAlias::class
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
        $this->assertEquals(7, $reference->getStartLine());
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
        $this->assertEquals(7, $reference->getEndLine());
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
        return $this->getFirstTraitAdaptationAliasInClass()
            ->getFirstChildOfType(ASTTraitReference::class);
    }
}
