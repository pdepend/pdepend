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
use PDepend\Source\ASTVisitor\ASTVisitor;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTTrait} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 1.0.0
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTTrait
 * @covers \PDepend\Source\AST\AbstractASTType
 * @group unittest
 */
class ASTTraitTest extends AbstractASTArtifactTestCase
{
    /**
     * testGetAllMethodsOnSimpleTraitReturnsExpectedResult
     *
     * @return void
     */
    public function testGetAllMethodsOnSimpleTraitReturnsExpectedResult(): void
    {
        $trait = $this->getFirstTraitForTest();
        $this->assertEquals(
            ['foo', 'bar', 'baz'],
            array_keys($trait->getAllMethods())
        );
    }

    /**
     * testGetAllMethodsOnTraitUsingTraitReturnsExpectedResult
     *
     * @return void
     */
    public function testGetAllMethodsOnTraitUsingTraitReturnsExpectedResult(): void
    {
        $trait = $this->getFirstTraitForTest();
        $this->assertEquals(
            ['foo', 'bar', 'baz'],
            array_keys($trait->getAllMethods())
        );
    }

    /**
     * testGetAllMethodsWithRedeclaredMethodReturnsExpectedInstance
     *
     * @return void
     */
    public function testGetAllMethodsWithRedeclaredMethodReturnsExpectedInstance(): void
    {
        $trait   = $this->getFirstTraitForTest();
        $methods = $trait->getAllMethods();

        $this->assertSame($trait, $methods['foo']->getParent());
    }

    /**
     * testGetAllMethodsWithAliasedMethodCollision
     *
     * @return void
     */
    public function testGetAllMethodsWithAliasedMethodCollision(): void
    {
        $trait = $this->getFirstTraitForTest();
        $this->assertEquals(
            ['foo', 'bar'],
            array_keys($trait->getAllMethods())
        );
    }

    /**
     * testGetAllMethodsWithAliasedMethodTwice
     *
     * @return void
     */
    public function testGetAllMethodsWithAliasedMethodTwice(): void
    {
        $trait = $this->getFirstTraitForTest();
        $this->assertEquals(
            ['foo', 'bar'],
            array_keys($trait->getAllMethods())
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedToPublic
     *
     * @return void
     */
    public function testGetAllMethodsWithVisibilityChangedToPublic(): void
    {
        $trait   = $this->getFirstTraitForTest();
        $methods = $trait->getAllMethods();

        $this->assertEquals(
            State::IS_PUBLIC,
            $methods['foo']->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedToProtected
     *
     * @return void
     */
    public function testGetAllMethodsWithVisibilityChangedToProtected(): void
    {
        $trait   = $this->getFirstTraitForTest();
        $methods = $trait->getAllMethods();

        $this->assertEquals(
            State::IS_PROTECTED,
            $methods['foo']->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedToPrivate
     *
     * @return void
     */
    public function testGetAllMethodsWithVisibilityChangedToPrivate(): void
    {
        $trait   = $this->getFirstTraitForTest();
        $methods = $trait->getAllMethods();

        $this->assertEquals(
            State::IS_PRIVATE,
            $methods['foo']->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedKeepsAbstractModifier
     *
     * @return void
     */
    public function testGetAllMethodsWithVisibilityChangedKeepsAbstractModifier(): void
    {
        $trait   = $this->getFirstTraitForTest();
        $methods = $trait->getAllMethods();

        $this->assertEquals(
            State::IS_PROTECTED | State::IS_ABSTRACT,
            $methods['foo']->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedKeepsStaticModifier
     *
     * @return void
     */
    public function testGetAllMethodsWithVisibilityChangedKeepsStaticModifier(): void
    {
        $trait   = $this->getFirstTraitForTest();
        $methods = $trait->getAllMethods();

        $this->assertEquals(
            State::IS_PUBLIC | State::IS_STATIC,
            $methods['foo']->getModifiers()
        );
    }

    /**
     * testGetAllMethodsHandlesTraitMethodPrecedence
     *
     * @return void
     */
    public function testGetAllMethodsHandlesTraitMethodPrecedence(): void
    {
        $trait   = $this->getFirstTraitForTest();
        $methods = $trait->getAllMethods();

        $this->assertEquals(
            'testGetAllMethodsHandlesTraitMethodPrecedenceUsedTraitOne',
            $methods['foo']->getParent()->getName()
        );
    }

    /**
     * testGetAllMethodsExcludeTraitMethodWithPrecedence
     *
     * @return void
     */
    public function testGetAllMethodsExcludeTraitMethodWithPrecedence(): void
    {
        $trait = $this->getFirstTraitForTest();
        $this->assertCount(1, $trait->getAllMethods());
    }

    /**
     * testGetAllMethodsWithMethodCollisionThrowsExpectedException
     *
     * @return void
     * @covers \PDepend\Source\AST\ASTTraitMethodCollisionException
     *
     * @group issue-154
     */
    public function testGetAllMethodsWithMethodCollisionThrowsExpectedException(): void
    {
        $this->expectException(\PDepend\Source\AST\ASTTraitMethodCollisionException::class);

        $trait = $this->getFirstTraitForTest();
        $trait->getAllMethods();
    }

    /**
     * testGetAllMethodsWithAbstractMethods
     * case with abstract methods.
     * No ASTTraitMethodCollisionException is thrown if only one method is not abstract.
     * Fix issue 154.
     *
     * @return void
     * @covers \PDepend\Source\AST\ASTTraitMethodCollisionException
     *
     * @group issue-154
     */
    public function testGetAllMethodsWithAbstractMethods(): void
    {
        $trait = $this->getFirstTraitForTest();
        $this->assertCount(1, $trait->getAllMethods());
    }

    /**
     * testGetAllChildrenReturnsAnEmptyArrayByDefault
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetAllChildrenReturnsAnEmptyArrayByDefault(): void
    {
        $trait = new ASTTrait(__CLASS__);
        $this->assertSame([], $trait->getChildren());
    }

    /**
     * testGetAllChildrenReturnsArrayWithExpectedNumberOfNodes
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetAllChildrenReturnsArrayWithExpectedNumberOfNodes(): void
    {
        $trait = $this->getFirstTraitForTest();
        $this->assertCount(2, $trait->getChildren());
    }

    /**
     * testTraitHasExpectedStartLine
     *
     * @return void
     */
    public function testTraitHasExpectedStartLine(): void
    {
        $trait = $this->getFirstTraitForTest();
        $this->assertEquals(5, $trait->getStartLine());
    }

    /**
     * testTraitHasExpectedEndLine
     *
     * @return void
     */
    public function testTraitHasExpectedEndLine(): void
    {
        $trait = $this->getFirstTraitForTest();
        $this->assertEquals(11, $trait->getEndLine());
    }

    /**
     * testTraitHasExpectedPackage
     *
     * @return void
     */
    public function testTraitHasExpectedPackage(): void
    {
        $trait = $this->getFirstTraitForTest();
        $this->assertEquals('org.pdepend', $trait->getNamespace()->getName());
    }

    /**
     * testTraitHasExpectedNamespace
     *
     * @return void
     */
    public function testTraitHasExpectedNamespace(): void
    {
        $trait = $this->getFirstTraitForTest();
        $this->assertEquals('org\pdepend\code', $trait->getNamespace()->getName());
    }

    /**
     * testGetNamespaceNameReturnsExpectedName
     *
     * @return void
     */
    public function testGetNamespaceNameReturnsExpectedName(): void
    {
        $trait = $this->getFirstTraitForTest();
        $this->assertEquals('org\pdepend\code', $trait->getNamespaceName());
    }

    /**
     * testGetMethodsReturnsExpectedNumberOfMethods
     *
     * @return void
     */
    public function testGetMethodsReturnsExpectedNumberOfMethods(): void
    {
        $trait = $this->getFirstTraitForTest();
        $this->assertCount(3, $trait->getMethods());
    }

    /**
     * testTraitCanUseParentKeywordInMethodBody
     *
     * @return void
     */
    public function testTraitCanUseParentKeywordInMethodBody(): void
    {
        $trait = $this->getFirstTraitForTest();
        $this->assertNotNull($trait);
    }

    /**
     * testTraitCanUseParentKeywordAsMethodTypeHint
     *
     * @return void
     */
    public function testTraitCanUseParentKeywordAsMethodTypeHint(): void
    {
        $trait = $this->getFirstTraitForTest();
        $this->assertNotNull($trait);
    }

    /**
     * @return void
     */
    public function testGetNamespacedName(): void
    {
        $trait = new ASTTrait('MyTrait');
        $this->assertSame('MyTrait', $trait->getNamespacedName());
    }

    /**
     * @return void
     */
    public function testGetNamespacedNameWithNamespaceDeclaration(): void
    {
        $trait = new ASTTrait('MyTrait');
        $trait->setNamespace(new ASTNamespace('My\\Namespace'));

        $this->assertSame('My\\Namespace\\MyTrait', $trait->getNamespacedName());
    }

    /**
     * @return void
     */
    public function testGetNamespacedNameWithPackageAnnotation(): void
    {
        $namespace = new ASTNamespace('My\\Namespace');
        $namespace->setPackageAnnotation(true);

        $Trait = new ASTTrait('MyTrait');
        $Trait->setNamespace($namespace);

        $this->assertSame('MyTrait', $Trait->getNamespacedName());
    }

    /**
     * testAcceptInvokesVisitTraitOnGivenVisitor
     *
     * @return void
     */
    public function testAcceptInvokesVisitTraitOnGivenVisitor(): void
    {
        $visitor = $this->getMockBuilder('PDepend\\Source\\ASTVisitor\\ASTVisitor')
            ->disableOriginalClone()
            ->getMock();
        $visitor->expects($this->once())
            ->method('visitTrait')
            ->with($this->isInstanceOf('PDepend\\Source\\AST\\ASTTrait'));

        $trait = new ASTTrait('MyTrait');
        $trait->accept($visitor);
    }

    /**
     * testMagicWakeupCallsRegisterTraitOnBuilderContext
     *
     * @return void
     */
    public function testMagicWakeupCallsRegisterTraitOnBuilderContext(): void
    {
        $context = $this->getMockBuilder('PDepend\\Source\\Builder\\BuilderContext')
            ->disableOriginalClone()
            ->getMock();
        $context->expects($this->once())
            ->method('registerTrait')
            ->with($this->isInstanceOf('PDepend\\Source\\AST\\ASTTrait'));

        $trait = new ASTTrait(__FUNCTION__);
        $trait->setContext($context);
        $trait->__wakeup();
    }

    /**
     * Returns the first trait found the code under test.
     *
     * @return \PDepend\Source\AST\ASTTrait
     */
    protected function getFirstTraitForTest()
    {
        return parent::getFirstTraitForTestCase();
    }

    /**
     * Creates an item instance.
     *
     * @return \PDepend\Source\AST\AbstractASTArtifact
     */
    protected function createItem()
    {
        return new ASTTrait(__CLASS__);
    }
}
