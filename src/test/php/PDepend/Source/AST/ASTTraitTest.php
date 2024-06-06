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

use PDepend\Source\Builder\BuilderContext;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTTrait} class.
 *
 * @covers \PDepend\Source\AST\AbstractASTType
 * @covers \PDepend\Source\AST\ASTTrait
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 1.0.0
 *
 * @group unittest
 */
class ASTTraitTest extends AbstractASTArtifactTestCase
{
    /**
     * testGetAllMethodsOnSimpleTraitReturnsExpectedResult
     */
    public function testGetAllMethodsOnSimpleTraitReturnsExpectedResult(): void
    {
        $trait = $this->getFirstTraitForTest();
        static::assertEquals(
            ['foo', 'bar', 'baz'],
            array_keys($trait->getAllMethods())
        );
    }

    /**
     * testGetAllMethodsOnTraitUsingTraitReturnsExpectedResult
     */
    public function testGetAllMethodsOnTraitUsingTraitReturnsExpectedResult(): void
    {
        $trait = $this->getFirstTraitForTest();
        static::assertEquals(
            ['foo', 'bar', 'baz'],
            array_keys($trait->getAllMethods())
        );
    }

    /**
     * testGetAllMethodsWithRedeclaredMethodReturnsExpectedInstance
     */
    public function testGetAllMethodsWithRedeclaredMethodReturnsExpectedInstance(): void
    {
        $trait = $this->getFirstTraitForTest();
        $methods = $trait->getAllMethods();

        static::assertSame($trait, $methods['foo']->getParent());
    }

    /**
     * testGetAllMethodsWithAliasedMethodCollision
     */
    public function testGetAllMethodsWithAliasedMethodCollision(): void
    {
        $trait = $this->getFirstTraitForTest();
        static::assertEquals(
            ['foo', 'bar'],
            array_keys($trait->getAllMethods())
        );
    }

    /**
     * testGetAllMethodsWithAliasedMethodTwice
     */
    public function testGetAllMethodsWithAliasedMethodTwice(): void
    {
        $trait = $this->getFirstTraitForTest();
        static::assertEquals(
            ['foo', 'bar'],
            array_keys($trait->getAllMethods())
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedToPublic
     */
    public function testGetAllMethodsWithVisibilityChangedToPublic(): void
    {
        $trait = $this->getFirstTraitForTest();
        $methods = $trait->getAllMethods();

        static::assertEquals(
            State::IS_PUBLIC,
            $methods['foo']->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedToProtected
     */
    public function testGetAllMethodsWithVisibilityChangedToProtected(): void
    {
        $trait = $this->getFirstTraitForTest();
        $methods = $trait->getAllMethods();

        static::assertEquals(
            State::IS_PROTECTED,
            $methods['foo']->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedToPrivate
     */
    public function testGetAllMethodsWithVisibilityChangedToPrivate(): void
    {
        $trait = $this->getFirstTraitForTest();
        $methods = $trait->getAllMethods();

        static::assertEquals(
            State::IS_PRIVATE,
            $methods['foo']->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedKeepsAbstractModifier
     */
    public function testGetAllMethodsWithVisibilityChangedKeepsAbstractModifier(): void
    {
        $trait = $this->getFirstTraitForTest();
        $methods = $trait->getAllMethods();

        static::assertEquals(
            State::IS_PROTECTED | State::IS_ABSTRACT,
            $methods['foo']->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedKeepsStaticModifier
     */
    public function testGetAllMethodsWithVisibilityChangedKeepsStaticModifier(): void
    {
        $trait = $this->getFirstTraitForTest();
        $methods = $trait->getAllMethods();

        static::assertEquals(
            State::IS_PUBLIC | State::IS_STATIC,
            $methods['foo']->getModifiers()
        );
    }

    /**
     * testGetAllMethodsHandlesTraitMethodPrecedence
     */
    public function testGetAllMethodsHandlesTraitMethodPrecedence(): void
    {
        $trait = $this->getFirstTraitForTest();
        $methods = $trait->getAllMethods();

        static::assertEquals(
            'testGetAllMethodsHandlesTraitMethodPrecedenceUsedTraitOne',
            $methods['foo']->getParent()?->getImage()
        );
    }

    /**
     * testGetAllMethodsExcludeTraitMethodWithPrecedence
     */
    public function testGetAllMethodsExcludeTraitMethodWithPrecedence(): void
    {
        $trait = $this->getFirstTraitForTest();
        static::assertCount(1, $trait->getAllMethods());
    }

    /**
     * testGetAllMethodsWithMethodCollisionThrowsExpectedException
     *
     * @covers \PDepend\Source\AST\ASTTraitMethodCollisionException
     *
     * @group issue-154
     */
    public function testGetAllMethodsWithMethodCollisionThrowsExpectedException(): void
    {
        $this->expectException(ASTTraitMethodCollisionException::class);

        $trait = $this->getFirstTraitForTest();
        $trait->getAllMethods();
    }

    /**
     * testGetAllMethodsWithAbstractMethods
     * case with abstract methods.
     * No ASTTraitMethodCollisionException is thrown if only one method is not abstract.
     * Fix issue 154.
     *
     * @covers \PDepend\Source\AST\ASTTraitMethodCollisionException
     *
     * @group issue-154
     */
    public function testGetAllMethodsWithAbstractMethods(): void
    {
        $trait = $this->getFirstTraitForTest();
        static::assertCount(1, $trait->getAllMethods());
    }

    /**
     * testGetAllChildrenReturnsAnEmptyArrayByDefault
     *
     * @since 1.0.0
     */
    public function testGetAllChildrenReturnsAnEmptyArrayByDefault(): void
    {
        $trait = new ASTTrait(__CLASS__);
        static::assertSame([], $trait->getChildren());
    }

    /**
     * testGetAllChildrenReturnsArrayWithExpectedNumberOfNodes
     *
     * @since 1.0.0
     */
    public function testGetAllChildrenReturnsArrayWithExpectedNumberOfNodes(): void
    {
        $trait = $this->getFirstTraitForTest();
        static::assertCount(3, $trait->getChildren());
    }

    /**
     * testTraitHasExpectedStartLine
     */
    public function testTraitHasExpectedStartLine(): void
    {
        $trait = $this->getFirstTraitForTest();
        static::assertEquals(5, $trait->getStartLine());
    }

    /**
     * testTraitHasExpectedEndLine
     */
    public function testTraitHasExpectedEndLine(): void
    {
        $trait = $this->getFirstTraitForTest();
        static::assertEquals(11, $trait->getEndLine());
    }

    /**
     * testTraitHasExpectedPackage
     */
    public function testTraitHasExpectedPackage(): void
    {
        $trait = $this->getFirstTraitForTest();
        static::assertEquals('org.pdepend', $trait->getNamespace()?->getImage());
    }

    /**
     * testTraitHasExpectedNamespace
     */
    public function testTraitHasExpectedNamespace(): void
    {
        $trait = $this->getFirstTraitForTest();
        static::assertEquals('org\pdepend\code', $trait->getNamespace()?->getImage());
    }

    /**
     * testGetNamespaceNameReturnsExpectedName
     */
    public function testGetNamespaceNameReturnsExpectedName(): void
    {
        $trait = $this->getFirstTraitForTest();
        static::assertEquals('org\pdepend\code', $trait->getNamespaceName());
    }

    /**
     * testGetMethodsReturnsExpectedNumberOfMethods
     */
    public function testGetMethodsReturnsExpectedNumberOfMethods(): void
    {
        $trait = $this->getFirstTraitForTest();
        static::assertCount(3, $trait->getMethods());
    }

    /**
     * testTraitCanUseParentKeywordInMethodBody
     */
    public function testTraitCanUseParentKeywordInMethodBody(): void
    {
        $trait = $this->getFirstTraitForTest();
        static::assertNotNull($trait);
    }

    /**
     * testTraitCanUseParentKeywordAsMethodTypeHint
     */
    public function testTraitCanUseParentKeywordAsMethodTypeHint(): void
    {
        $trait = $this->getFirstTraitForTest();
        static::assertNotNull($trait);
    }

    public function testGetNamespacedName(): void
    {
        $trait = new ASTTrait('MyTrait');
        static::assertSame('MyTrait', $trait->getNamespacedName());
    }

    public function testGetNamespacedNameWithNamespaceDeclaration(): void
    {
        $trait = new ASTTrait('MyTrait');
        $trait->setNamespace(new ASTNamespace('My\\Namespace'));

        static::assertSame('My\\Namespace\\MyTrait', $trait->getNamespacedName());
    }

    public function testGetNamespacedNameWithPackageAnnotation(): void
    {
        $namespace = new ASTNamespace('My\\Namespace');
        $namespace->setPackageAnnotation(true);

        $Trait = new ASTTrait('MyTrait');
        $Trait->setNamespace($namespace);

        static::assertSame('MyTrait', $Trait->getNamespacedName());
    }

    /**
     * testMagicWakeupCallsRegisterTraitOnBuilderContext
     */
    public function testMagicWakeupCallsRegisterTraitOnBuilderContext(): void
    {
        $context = $this->getMockBuilder(BuilderContext::class)
            ->disableOriginalClone()
            ->getMock();
        $context->expects(static::once())
            ->method('registerTrait')
            ->with(static::isInstanceOf(ASTTrait::class));

        $trait = new ASTTrait(__FUNCTION__);
        $trait->setContext($context);
        $trait->__wakeup();
    }

    /**
     * Returns the first trait found the code under test.
     */
    protected function getFirstTraitForTest(): ASTTrait
    {
        return parent::getFirstTraitForTestCase();
    }

    /**
     * Creates an item instance.
     */
    protected function createItem(): AbstractASTArtifact
    {
        return new ASTTrait(__CLASS__);
    }
}
