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
 * Test case for the {@link \PDepend\Source\AST\ASTConstantDefinition} class.
 *
 * @covers \PDepend\Source\AST\ASTConstantDefinition
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTConstantDefinitionTest extends ASTNodeTestCase
{
    /**
     * Tests that the field declaration <b>setModifiers()</b> method accepts all
     * valid combinations of modifiers.
     *
     * @param int $modifiers Combinations of valid modifiers.
     * @dataProvider dataProviderSetModifiersAcceptsExpectedModifierCombinations
     */
    public function testSetModifiersAcceptsExpectedModifierCombinations($modifiers): void
    {
        $definition = new ASTConstantDefinition();

        $definition->setModifiers($modifiers);
        static::assertEquals($modifiers, $definition->getModifiers());
    }

    /**
     * Tests that the <b>setModifiers()</b> method throws an exception when an
     * invalid modifier or modifier combination was set.
     *
     * @param int $modifiers Combinations of invalid modifiers.
     * @dataProvider dataProviderSetModifiersThrowsExpectedExceptionForInvalidModifiers
     */
    public function testSetModifiersThrowsExpectedExceptionForInvalidModifiers($modifiers): void
    {
        $definition = new ASTConstantDefinition();

        $this->expectException(
            'InvalidArgumentException'
        );
        $this->expectExceptionMessage(
            'Invalid field modifiers given, allowed modifiers are ' .
            'IS_PUBLIC, IS_PROTECTED, IS_PRIVATE and IS_FINAL.'
        );

        $definition->setModifiers($modifiers);
    }

    /**
     * testIsPublicReturnsFalseByDefault
     */
    public function testIsPublicReturnsFalseByDefault(): void
    {
        $declaration = $this->createNodeInstance();
        static::assertFalse($declaration->isPublic());
    }

    /**
     * testIsPublicReturnsTrueWhenCorrespondingModifierWasSet
     */
    public function testIsPublicReturnsTrueWhenCorrespondingModifierWasSet(): void
    {
        $declaration = $this->createNodeInstance();
        $declaration->setModifiers(State::IS_PUBLIC);

        static::assertTrue($declaration->isPublic());
    }

    /**
     * testIsProtectedReturnsFalseByDefault
     */
    public function testIsProtectedReturnsFalseByDefault(): void
    {
        $declaration = $this->createNodeInstance();
        static::assertFalse($declaration->isProtected());
    }

    /**
     * testIsProtectedReturnsTrueWhenCorrespondingModifierWasSet
     */
    public function testIsProtectedReturnsTrueWhenCorrespondingModifierWasSet(): void
    {
        $declaration = $this->createNodeInstance();
        $declaration->setModifiers(State::IS_PROTECTED);

        static::assertTrue($declaration->isProtected());
    }

    /**
     * testIsPrivateReturnsFalseByDefault
     */
    public function testIsPrivateReturnsFalseByDefault(): void
    {
        $declaration = $this->createNodeInstance();
        static::assertFalse($declaration->isPrivate());
    }

    /**
     * testIsPrivateReturnsTrueWhenCorrespondingModifierWasSet
     */
    public function testIsPrivateReturnsTrueWhenCorrespondingModifierWasSet(): void
    {
        $declaration = $this->createNodeInstance();
        $declaration->setModifiers(State::IS_PRIVATE);

        static::assertTrue($declaration->isPrivate());
    }

    /**
     * Tests that the constant definition has the expected doc comment block.
     */
    public function testConstantDefinitionHasExpectedDocComment(): void
    {
        $constant = $this->getFirstConstantDefinitionInClass();
        static::assertEquals(
            "/**\n" .
            "     * Foo bar baz foobar.\n" .
            '     */',
            $constant->getComment()
        );
    }

    /**
     * Tests that the constant definition has the expected doc comment block.
     */
    public function testConstantDefinitionHasExpectedDocCommentWithInlineCommentBetween(): void
    {
        $constant = $this->getFirstConstantDefinitionInClass();
        static::assertEquals(
            "/**\n" .
            "     * Foo bar baz foobar.\n" .
            '     */',
            $constant->getComment()
        );
    }

    /**
     * testConstantDefinition
     *
     * @return ASTConstantDefinition
     * @since 1.0.2
     */
    public function testConstantDefinition()
    {
        $constant = $this->getFirstConstantDefinitionInClass();
        static::assertInstanceOf(ASTConstantDefinition::class, $constant);

        return $constant;
    }

    /**
     * testConstantDefinitionHasExpectedStartLine
     *
     * @param ASTConstantDefinition $constant
     *
     * @depends testConstantDefinition
     */
    public function testConstantDefinitionHasExpectedStartLine($constant): void
    {
        static::assertEquals(4, $constant->getStartLine());
    }

    /**
     * testConstantDefinitionHasExpectedStartColumn
     *
     * @param ASTConstantDefinition $constant
     *
     * @depends testConstantDefinition
     */
    public function testConstantDefinitionHasExpectedStartColumn($constant): void
    {
        static::assertEquals(5, $constant->getStartColumn());
    }

    /**
     * testConstantDefinitionHasExpectedEndLine
     *
     * @param ASTConstantDefinition $constant
     *
     * @depends testConstantDefinition
     */
    public function testConstantDefinitionHasExpectedEndLine($constant): void
    {
        static::assertEquals(7, $constant->getEndLine());
    }

    /**
     * testConstantDefinitionHasExpectedEndColumn
     *
     * @param ASTConstantDefinition $constant
     *
     * @depends testConstantDefinition
     */
    public function testConstantDefinitionHasExpectedEndColumn($constant): void
    {
        static::assertEquals(12, $constant->getEndColumn());
    }

    /**
     * testConstantDefinitionWithDeclarators
     *
     * @return ASTConstantDefinition
     * @since 1.0.2
     */
    public function testConstantDefinitionWithDeclarators()
    {
        $constant = $this->getFirstConstantDefinitionInClass();
        static::assertInstanceOf(ASTConstantDefinition::class, $constant);

        return $constant;
    }

    /**
     * testConstantDefinitionWithDeclaratorsHasExpectedStartLine
     *
     * @param ASTConstantDefinition $constant
     * @since 1.0.2
     *
     * @depends testConstantDefinitionWithDeclarators
     */
    public function testConstantDefinitionWithDeclaratorsHasExpectedStartLine($constant): void
    {
        static::assertEquals(4, $constant->getStartLine());
    }

    /**
     * testConstantDefinitionWithDeclaratorsHasExpectedStartColumn
     *
     * @param ASTConstantDefinition $constant
     * @since 1.0.2
     *
     * @depends testConstantDefinitionWithDeclarators
     */
    public function testConstantDefinitionWithDeclaratorsHasExpectedStartColumn($constant): void
    {
        static::assertEquals(5, $constant->getStartColumn());
    }

    /**
     * testConstantDefinitionWithDeclaratorsHasExpectedEndLine
     *
     * @param ASTConstantDefinition $constant
     * @since 1.0.2
     *
     * @depends testConstantDefinitionWithDeclarators
     */
    public function testConstantDefinitionWithDeclaratorsHasExpectedEndLine($constant): void
    {
        static::assertEquals(6, $constant->getEndLine());
    }

    /**
     * testConstantDefinitionWithDeclaratorsHasExpectedEndColumn
     *
     * @param ASTConstantDefinition $constant
     * @since 1.0.2
     *
     * @depends testConstantDefinitionWithDeclarators
     */
    public function testConstantDefinitionWithDeclaratorsHasExpectedEndColumn($constant): void
    {
        static::assertEquals(18, $constant->getEndColumn());
    }

    /**
     * testConstantDefinitionInGlobalScope
     *
     * @since 1.0.2
     */
    public function testConstantDefinitionInGlobalScope(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * testConstantDefinitionInNamespaceScope
     *
     * @since 1.0.2
     */
    public function testConstantDefinitionInNamespaceScope(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return ASTConstantDefinition
     */
    private function getFirstConstantDefinitionInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(),
            ASTConstantDefinition::class
        );
    }

    /**
     * Returns valid field declation modifiers.
     *
     * @return array
     */
    public static function dataProviderSetModifiersAcceptsExpectedModifierCombinations()
    {
        return [
            [State::IS_PRIVATE],
            [State::IS_PROTECTED],
            [State::IS_PUBLIC],
        ];
    }

    /**
     * Returns invalid field declation modifiers.
     *
     * @return array
     */
    public static function dataProviderSetModifiersThrowsExpectedExceptionForInvalidModifiers()
    {
        return [
            [State::IS_ABSTRACT],
            [State::IS_STATIC],
            [
                State::IS_PRIVATE |
                State::IS_ABSTRACT,
            ],
            [
                State::IS_PROTECTED |
                State::IS_ABSTRACT,
            ],
            [
                State::IS_PUBLIC |
                State::IS_STATIC,
            ],
            [
                State::IS_PROTECTED |
                State::IS_STATIC,
            ],
            [
                State::IS_PRIVATE |
                State::IS_STATIC,
            ],
        ];
    }
}
