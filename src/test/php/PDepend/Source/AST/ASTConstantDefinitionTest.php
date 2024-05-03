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
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTConstantDefinition
 * @group unittest
 */
class ASTConstantDefinitionTest extends ASTNodeTestCase
{
    /**
     * Tests that the field declaration <b>setModifiers()</b> method accepts all
     * valid combinations of modifiers.
     *
     * @param integer $modifiers Combinations of valid modifiers.
     * @return void
     * @dataProvider dataProviderSetModifiersAcceptsExpectedModifierCombinations
     */
    public function testSetModifiersAcceptsExpectedModifierCombinations($modifiers): void
    {
        $definition = new ASTConstantDefinition();

        $definition->setModifiers($modifiers);
        $this->assertEquals($modifiers, $definition->getModifiers());
    }

    /**
     * Tests that the <b>setModifiers()</b> method throws an exception when an
     * invalid modifier or modifier combination was set.
     *
     * @param integer $modifiers Combinations of invalid modifiers.
     *
     * @return void
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
     *
     * @return void
     */
    public function testIsPublicReturnsFalseByDefault(): void
    {
        $declaration = $this->createNodeInstance();
        $this->assertFalse($declaration->isPublic());
    }

    /**
     * testIsPublicReturnsTrueWhenCorrespondingModifierWasSet
     *
     * @return void
     */
    public function testIsPublicReturnsTrueWhenCorrespondingModifierWasSet(): void
    {
        $declaration = $this->createNodeInstance();
        $declaration->setModifiers(State::IS_PUBLIC);

        $this->assertTrue($declaration->isPublic());
    }

    /**
     * testIsProtectedReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsProtectedReturnsFalseByDefault(): void
    {
        $declaration = $this->createNodeInstance();
        $this->assertFalse($declaration->isProtected());
    }


    /**
     * testIsProtectedReturnsTrueWhenCorrespondingModifierWasSet
     *
     * @return void
     */
    public function testIsProtectedReturnsTrueWhenCorrespondingModifierWasSet(): void
    {
        $declaration = $this->createNodeInstance();
        $declaration->setModifiers(State::IS_PROTECTED);

        $this->assertTrue($declaration->isProtected());
    }

    /**
     * testIsPrivateReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsPrivateReturnsFalseByDefault(): void
    {
        $declaration = $this->createNodeInstance();
        $this->assertFalse($declaration->isPrivate());
    }

    /**
     * testIsPrivateReturnsTrueWhenCorrespondingModifierWasSet
     *
     * @return void
     */
    public function testIsPrivateReturnsTrueWhenCorrespondingModifierWasSet(): void
    {
        $declaration = $this->createNodeInstance();
        $declaration->setModifiers(State::IS_PRIVATE);

        $this->assertTrue($declaration->isPrivate());
    }

    /**
     * Tests that the constant definition has the expected doc comment block.
     *
     * @return void
     */
    public function testConstantDefinitionHasExpectedDocComment(): void
    {
        $constant = $this->getFirstConstantDefinitionInClass();
        $this->assertEquals(
            "/**\n" .
            "     * Foo bar baz foobar.\n" .
            "     */",
            $constant->getComment()
        );
    }

    /**
     * Tests that the constant definition has the expected doc comment block.
     *
     * @return void
     */
    public function testConstantDefinitionHasExpectedDocCommentWithInlineCommentBetween(): void
    {
        $constant = $this->getFirstConstantDefinitionInClass();
        $this->assertEquals(
            "/**\n" .
            "     * Foo bar baz foobar.\n" .
            "     */",
            $constant->getComment()
        );
    }

    /**
     * testConstantDefinition
     *
     * @return \PDepend\Source\AST\ASTConstantDefinition
     * @since 1.0.2
     */
    public function testConstantDefinition()
    {
        $constant = $this->getFirstConstantDefinitionInClass();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTConstantDefinition', $constant);

        return $constant;
    }

    /**
     * testConstantDefinitionHasExpectedStartLine
     *
     * @param \PDepend\Source\AST\ASTConstantDefinition $constant
     *
     * @return void
     * @depends testConstantDefinition
     */
    public function testConstantDefinitionHasExpectedStartLine($constant): void
    {
        $this->assertEquals(4, $constant->getStartLine());
    }

    /**
     * testConstantDefinitionHasExpectedStartColumn
     *
     * @param \PDepend\Source\AST\ASTConstantDefinition $constant
     *
     * @return void
     * @depends testConstantDefinition
     */
    public function testConstantDefinitionHasExpectedStartColumn($constant): void
    {
        $this->assertEquals(5, $constant->getStartColumn());
    }

    /**
     * testConstantDefinitionHasExpectedEndLine
     *
     * @param \PDepend\Source\AST\ASTConstantDefinition $constant
     *
     * @return void
     * @depends testConstantDefinition
     */
    public function testConstantDefinitionHasExpectedEndLine($constant): void
    {
        $this->assertEquals(7, $constant->getEndLine());
    }

    /**
     * testConstantDefinitionHasExpectedEndColumn
     *
     * @param \PDepend\Source\AST\ASTConstantDefinition $constant
     *
     * @return void
     * @depends testConstantDefinition
     */
    public function testConstantDefinitionHasExpectedEndColumn($constant): void
    {
        $this->assertEquals(12, $constant->getEndColumn());
    }

    /**
     * testConstantDefinitionWithDeclarators
     *
     * @return \PDepend\Source\AST\ASTConstantDefinition
     * @since 1.0.2
     */
    public function testConstantDefinitionWithDeclarators()
    {
        $constant = $this->getFirstConstantDefinitionInClass();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTConstantDefinition', $constant);

        return $constant;
    }

    /**
     * testConstantDefinitionWithDeclaratorsHasExpectedStartLine
     *
     * @param \PDepend\Source\AST\ASTConstantDefinition $constant
     *
     * @return void
     * @since 1.0.2
     * @depends testConstantDefinitionWithDeclarators
     */
    public function testConstantDefinitionWithDeclaratorsHasExpectedStartLine($constant): void
    {
        $this->assertEquals(4, $constant->getStartLine());
    }
    
    /**
     * testConstantDefinitionWithDeclaratorsHasExpectedStartColumn
     *
     * @param \PDepend\Source\AST\ASTConstantDefinition $constant
     *
     * @return void
     * @since 1.0.2
     * @depends testConstantDefinitionWithDeclarators
     */
    public function testConstantDefinitionWithDeclaratorsHasExpectedStartColumn($constant): void
    {
        $this->assertEquals(5, $constant->getStartColumn());
    }
    
    /**
     * testConstantDefinitionWithDeclaratorsHasExpectedEndLine
     *
     * @param \PDepend\Source\AST\ASTConstantDefinition $constant
     *
     * @return void
     * @since 1.0.2
     * @depends testConstantDefinitionWithDeclarators
     */
    public function testConstantDefinitionWithDeclaratorsHasExpectedEndLine($constant): void
    {
        $this->assertEquals(6, $constant->getEndLine());
    }
    
    /**
     * testConstantDefinitionWithDeclaratorsHasExpectedEndColumn
     *
     * @param \PDepend\Source\AST\ASTConstantDefinition $constant
     *
     * @return void
     * @since 1.0.2
     * @depends testConstantDefinitionWithDeclarators
     */
    public function testConstantDefinitionWithDeclaratorsHasExpectedEndColumn($constant): void
    {
        $this->assertEquals(18, $constant->getEndColumn());
    }

    /**
     * testConstantDefinitionInGlobalScope
     *
     * @return void
     * @since 1.0.2
     */
    public function testConstantDefinitionInGlobalScope(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * testConstantDefinitionInNamespaceScope
     *
     * @return void
     * @since 1.0.2
     */
    public function testConstantDefinitionInNamespaceScope(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return \PDepend\Source\AST\ASTConstantDefinition
     */
    private function getFirstConstantDefinitionInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTConstantDefinition'
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
                State::IS_ABSTRACT
            ],
            [
                State::IS_PROTECTED |
                State::IS_ABSTRACT
            ],
            [
                State::IS_PUBLIC |
                State::IS_STATIC
            ],
            [
                State::IS_PROTECTED |
                State::IS_STATIC
            ],
            [
                State::IS_PRIVATE |
                State::IS_STATIC
            ],
        ];
    }
}
