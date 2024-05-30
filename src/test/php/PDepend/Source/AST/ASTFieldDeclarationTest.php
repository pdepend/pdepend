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
 * Test case for the {@link \PDepend\Source\AST\ASTFieldDeclaration} class.
 *
 * @covers \PDepend\Source\AST\ASTFieldDeclaration
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTFieldDeclarationTest extends ASTNodeTestCase
{
    /**
     * Tests that a field declaration contains the expected class reference.
     */
    public function testFieldDeclarationContainsClassReferenceWithAnnotationsEnabled(): void
    {
        $declaration = $this->getFirstFieldDeclarationInClass();

        $reference = $declaration->getChild(0);
        static::assertInstanceof(ASTClassOrInterfaceReference::class, $reference);
        static::assertEquals(__FUNCTION__, $reference->getType()->getImage());
    }

    /**
     * Tests that a field declaration does not contain a class reference.
     */
    public function testFieldDeclarationNotContainsClassReferenceWithAnnotationsDisabled(): void
    {
        $namespaces = $this->parseCodeResourceForTest(true);

        $class = $namespaces->current()
            ->getClasses()
            ->current();

        $declaration = $class->getFirstChildOfType(
            ASTFieldDeclaration::class
        );
        $reference = $declaration?->getFirstChildOfType(
            ASTClassOrInterfaceReference::class
        );
        static::assertNull($reference);
    }

    /**
     * testClassReferenceForJavaStyleArrayNotation
     */
    public function testClassReferenceForJavaStyleArrayNotation(): AbstractASTClassOrInterface
    {
        $declaration = $this->getFirstFieldDeclarationInClass();
        $reference = $declaration->getFirstChildOfType(
            ASTClassOrInterfaceReference::class
        );

        $type = $reference?->getType();

        static::assertNotNull($type);
        static::assertEquals('Sindelfingen', $type->getImage());

        return $type;
    }

    /**
     * @depends testClassReferenceForJavaStyleArrayNotation
     */
    public function testNamespaceForJavaStyleArrayNotation(AbstractASTClassOrInterface $type): void
    {
        static::assertEquals('Java\\Style', $type->getNamespaceName());
    }

    /**
     * Tests that the field declaration <b>setModifiers()</b> method accepts all
     * valid combinations of modifiers.
     *
     * @param int $modifiers Combinations of valid modifiers.
     * @dataProvider dataProviderSetModifiersAcceptsExpectedModifierCombinations
     */
    public function testSetModifiersAcceptsExpectedModifierCombinations(int $modifiers): void
    {
        $declaration = new ASTFieldDeclaration();
        $declaration->setModifiers($modifiers);
        static::assertEquals($modifiers, $declaration->getModifiers());
    }

    /**
     * Tests that the <b>setModifiers()</b> method throws an exception when an
     * invalid modifier or modifier combination was set.
     *
     * @param int $modifiers Combinations of invalid modifiers.
     * @dataProvider dataProviderSetModifiersThrowsExpectedExceptionForInvalidModifiers
     */
    public function testSetModifiersThrowsExpectedExceptionForInvalidModifiers(int $modifiers): void
    {
        $declaration = new ASTFieldDeclaration();

        $this->expectException(
            'InvalidArgumentException'
        );
        $this->expectExceptionMessage(
            'Invalid field modifiers given, allowed modifiers are ' .
            'IS_PUBLIC, IS_PROTECTED, IS_PRIVATE and IS_STATIC.'
        );

        $declaration->setModifiers($modifiers);
    }

    /**
     * testIsPublicReturnsFalseByDefault
     */
    public function testIsPublicReturnsFalseByDefault(): void
    {
        $declaration = $this->createNodeInstance();
        static::assertInstanceof(ASTFieldDeclaration::class, $declaration);
        static::assertFalse($declaration->isPublic());
    }

    /**
     * testIsPublicReturnsTrueWhenCorrespondingModifierWasSet
     */
    public function testIsPublicReturnsTrueWhenCorrespondingModifierWasSet(): void
    {
        $declaration = $this->createNodeInstance();
        static::assertInstanceof(ASTFieldDeclaration::class, $declaration);
        $declaration->setModifiers(State::IS_PUBLIC);

        static::assertTrue($declaration->isPublic());
    }

    /**
     * testIsProtectedReturnsFalseByDefault
     */
    public function testIsProtectedReturnsFalseByDefault(): void
    {
        $declaration = $this->createNodeInstance();
        static::assertInstanceof(ASTFieldDeclaration::class, $declaration);
        static::assertFalse($declaration->isProtected());
    }

    /**
     * testIsProtectedReturnsTrueWhenCorrespondingModifierWasSet
     */
    public function testIsProtectedReturnsTrueWhenCorrespondingModifierWasSet(): void
    {
        $declaration = $this->createNodeInstance();
        static::assertInstanceof(ASTFieldDeclaration::class, $declaration);
        $declaration->setModifiers(State::IS_PROTECTED);

        static::assertTrue($declaration->isProtected());
    }

    /**
     * testIsPrivateReturnsFalseByDefault
     */
    public function testIsPrivateReturnsFalseByDefault(): void
    {
        $declaration = $this->createNodeInstance();
        static::assertInstanceof(ASTFieldDeclaration::class, $declaration);
        static::assertFalse($declaration->isPrivate());
    }

    /**
     * testIsPrivateReturnsTrueWhenCorrespondingModifierWasSet
     */
    public function testIsPrivateReturnsTrueWhenCorrespondingModifierWasSet(): void
    {
        $declaration = $this->createNodeInstance();
        static::assertInstanceof(ASTFieldDeclaration::class, $declaration);
        $declaration->setModifiers(State::IS_PRIVATE);

        static::assertTrue($declaration->isPrivate());
    }

    /**
     * testIsStaticReturnsFalseByDefault
     */
    public function testIsStaticReturnsFalseByDefault(): void
    {
        $declaration = $this->createNodeInstance();
        static::assertInstanceof(ASTFieldDeclaration::class, $declaration);
        static::assertFalse($declaration->isStatic());
    }

    /**
     * testIsStaticReturnsTrueWhenCorrespondingModifierWasSet
     */
    public function testIsStaticReturnsTrueWhenCorrespondingModifierWasSet(): void
    {
        $declaration = $this->createNodeInstance();
        static::assertInstanceof(ASTFieldDeclaration::class, $declaration);
        $declaration->setModifiers(State::IS_STATIC);

        static::assertTrue($declaration->isStatic());
    }

    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames(): void
    {
        $declaration = $this->createNodeInstance();
        static::assertEquals(
            [
                'comment',
                'metadata',
                'nodes',
            ],
            $declaration->__sleep()
        );
    }

    /**
     * testFieldDeclarationHasExpectedStartLine
     */
    public function testFieldDeclarationHasExpectedStartLine(): void
    {
        $declaration = $this->getFirstFieldDeclarationInClass();
        static::assertEquals(4, $declaration->getStartLine());
    }

    /**
     * testFieldDeclarationHasExpectedStartColumn
     */
    public function testFieldDeclarationHasExpectedStartColumn(): void
    {
        $declaration = $this->getFirstFieldDeclarationInClass();
        static::assertEquals(5, $declaration->getStartColumn());
    }

    /**
     * testFieldDeclarationHasExpectedEndLine
     */
    public function testFieldDeclarationHasExpectedEndLine(): void
    {
        $declaration = $this->getFirstFieldDeclarationInClass();
        static::assertEquals(5, $declaration->getEndLine());
    }

    /**
     * testFieldDeclarationHasExpectedEndColumn
     */
    public function testFieldDeclarationHasExpectedEndColumn(): void
    {
        $declaration = $this->getFirstFieldDeclarationInClass();
        static::assertEquals(22, $declaration->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstFieldDeclarationInClass(): ASTFieldDeclaration
    {
        return $this->getFirstNodeOfTypeInClass(
            ASTFieldDeclaration::class
        );
    }

    /**
     * Returns valid field declation modifiers.
     *
     * @return array<mixed>
     */
    public static function dataProviderSetModifiersAcceptsExpectedModifierCombinations(): array
    {
        return [
            [State::IS_PRIVATE],
            [State::IS_PROTECTED],
            [State::IS_PUBLIC],
            [
                State::IS_PRIVATE |
                State::IS_STATIC,
            ],
            [
                State::IS_PROTECTED |
                State::IS_STATIC,
            ],
            [
                State::IS_PUBLIC |
                State::IS_STATIC,
            ],
        ];
    }

    /**
     * Returns invalid field declation modifiers.
     *
     * @return array<mixed>
     */
    public static function dataProviderSetModifiersThrowsExpectedExceptionForInvalidModifiers(): array
    {
        return [
            [State::IS_ABSTRACT],
            [State::IS_FINAL],
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
                State::IS_FINAL,
            ],
            [
                State::IS_PUBLIC |
                State::IS_STATIC |
                State::IS_FINAL,
            ],
        ];
    }
}
