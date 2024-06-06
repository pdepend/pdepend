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
 * Test case for the {@link \PDepend\Source\AST\ASTPropertyPostfix} class.
 *
 * @covers \PDepend\Source\AST\ASTClassFqnPostfix
 *
 * @group unittest
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\Language\PHP\PHPBuilder
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class ASTClassFqnPostfixTest extends ASTNodeTestCase
{
    /**
     * testGetImage
     */
    public function testGetImage(): void
    {
        $postfix = $this->getFirstClassFqnPostfixInClass();
        static::assertEquals('class', $postfix->getImage());
    }

    /**
     * testClassFqnPostfixStructureWithStatic
     */
    public function testClassFqnPostfixStructureWithStatic(): void
    {
        $this->assertGraphEquals(
            $this->getFirstMemberPrimaryPrefixInClass(),
            [
                ASTStaticReference::class,
                ASTClassFqnPostfix::class,
            ]
        );
    }

    /**
     * testGetImageWorksCaseInsensitive
     */
    public function testGetImageWorksCaseInsensitive(): void
    {
        $postfix = $this->getFirstClassFqnPostfixInClass();
        static::assertEquals('class', $postfix->getImage());
    }

    /**
     * testClassFqnPostfixStructureWithSelf
     */
    public function testClassFqnPostfixStructureWithSelf(): void
    {
        $this->assertGraphEquals(
            $this->getFirstMemberPrimaryPrefixInClass(),
            [
                ASTSelfReference::class,
                ASTClassFqnPostfix::class,
            ]
        );
    }

    /**
     * testClassFqnPostfixStructureWithParent
     */
    public function testClassFqnPostfixStructureWithParent(): void
    {
        $this->assertGraphEquals(
            $this->getFirstMemberPrimaryPrefixInClass(),
            [
                ASTParentReference::class,
                ASTClassFqnPostfix::class,
            ]
        );
    }

    /**
     * testClassFqnPostfixStructureWithClassName
     */
    public function testClassFqnPostfixStructureWithClassName(): void
    {
        $this->assertGraphEquals(
            $this->getFirstMemberPrimaryPrefixInClass(),
            [
                ASTClassOrInterfaceReference::class,
                ASTClassFqnPostfix::class,
            ]
        );
    }

    /**
     * testClassFqnPostfixStructureAsPropertyInitializer
     *
     * <code>
     * protected $foo = \Iterator::class;
     * </code>
     */
    public function testClassFqnPostfixAsPropertyInitializer(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());

        /** @var ASTFieldDeclaration $fieldDeclaration */
        $fieldDeclaration = $this->getFirstClassForTestCase()->getChild(0);

        static::assertInstanceOf(ASTFieldDeclaration::class, $fieldDeclaration);

        /** @var ASTVariableDeclarator $variableDeclarator */
        $variableDeclarator = $fieldDeclaration->getChild(0);

        static::assertInstanceOf(ASTVariableDeclarator::class, $variableDeclarator);
        static::assertTrue($variableDeclarator->getValue()?->isValueAvailable());

        /** @var ASTClassOrInterfaceReference $classReference */
        $classReference = $variableDeclarator->getValue()?->getValue();
        static::assertInstanceOf(ASTClassOrInterfaceReference::class, $classReference);

        static::assertSame('\\Iterator', $classReference->getImage());
    }

    /**
     * testClassFqnPostfixStructureAsPropertyInitializerWithSelf
     *
     * <code>
     * protected $foo = self::class;
     * </code>
     */
    public function testClassFqnPostfixAsPropertyInitializerWithSelf(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());

        /** @var ASTFieldDeclaration $fieldDeclaration */
        $fieldDeclaration = $this->getFirstClassForTestCase()->getChild(0);

        static::assertInstanceOf(ASTFieldDeclaration::class, $fieldDeclaration);

        /** @var ASTVariableDeclarator $variableDeclarator */
        $variableDeclarator = $fieldDeclaration->getChild(0);

        static::assertInstanceOf(ASTVariableDeclarator::class, $variableDeclarator);
        static::assertTrue($variableDeclarator->getValue()?->isValueAvailable());

        /** @var ASTSelfReference $classReference */
        $classReference = $variableDeclarator->getValue()?->getValue();
        static::assertInstanceOf(ASTSelfReference::class, $classReference);

        static::assertSame('self', $classReference->getImage());
    }

    /**
     * testClassFqnPostfixAsParameterInitializer
     */
    public function testClassFqnPostfixAsParameterInitializer(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());

        /** @var ASTParameter[] $parameters */
        $parameters = $this->getFirstClassMethodForTestCase()->getParameters();
        static::assertCount(1, $parameters);

        static::assertInstanceOf(ASTParameter::class, $parameters[0]);
        $value = $parameters[0]->getDefaultValue();
        static::assertInstanceOf(ASTNode::class, $value);

        static::assertSame('testClassFqnPostfixAsParameterInitializer', $value->getImage());
    }

    /**
     * testClassFqnPostfixAsParameterInitializerWithSelf
     */
    public function testClassFqnPostfixAsParameterInitializerWithSelf(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());

        /** @var ASTParameter[] $parameters */
        $parameters = $this->getFirstClassMethodForTestCase()->getParameters();
        static::assertCount(1, $parameters);

        static::assertInstanceOf(ASTParameter::class, $parameters[0]);
        $value = $parameters[0]->getDefaultValue();
        static::assertInstanceOf(ASTNode::class, $value);

        static::assertSame('self', $value->getImage());
    }

    /**
     * testClassFqnPostfixAsConstantInitializer
     */
    public function testClassFqnPostfixAsConstantInitializer(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());

        /** @var ASTConstantDefinition $constantDefinition */
        $constantDefinition = $this->getFirstClassForTestCase()->getChild(0);

        static::assertInstanceOf(ASTConstantDefinition::class, $constantDefinition);

        /** @var ASTConstantDeclarator $constantDefinition */
        $constantDeclarator = $constantDefinition->getChild(0);

        static::assertInstanceOf(ASTConstantDeclarator::class, $constantDeclarator);
    }

    /**
     * testClassFqnPostfixAsConstantInitializerWithSelf
     */
    public function testClassFqnPostfixAsConstantInitializerWithSelf(): void
    {
        static::assertNotNull($this->parseCodeResourceForTest());

        /** @var ASTConstantDefinition $constantDefinition */
        $constantDefinition = $this->getFirstClassForTestCase()->getChild(0);

        static::assertInstanceOf(ASTConstantDefinition::class, $constantDefinition);

        /** @var ASTConstantDeclarator $constantDefinition */
        $constantDeclarator = $constantDefinition->getChild(0);

        static::assertInstanceOf(ASTConstantDeclarator::class, $constantDeclarator);
    }

    /**
     * testClassFqnPostfixHasExpectedStartLine
     */
    public function testClassFqnPostfixHasExpectedStartLine(): void
    {
        $postfix = $this->getFirstClassFqnPostfixInClass();
        static::assertEquals(6, $postfix->getStartLine());
    }

    /**
     * testClassFqnPostfixHasExpectedStartColumn
     */
    public function testClassFqnPostfixHasExpectedStartColumn(): void
    {
        $postfix = $this->getFirstClassFqnPostfixInClass();
        static::assertEquals(26, $postfix->getStartColumn());
    }

    /**
     * testClassFqnPostfixHasExpectedEndLine
     */
    public function testClassFqnPostfixHasExpectedEndLine(): void
    {
        $postfix = $this->getFirstClassFqnPostfixInClass();
        static::assertEquals(6, $postfix->getEndLine());
    }

    /**
     * testClassFqnPostfixHasExpectedEndColumn
     */
    public function testClassFqnPostfixHasExpectedEndColumn(): void
    {
        $postfix = $this->getFirstClassFqnPostfixInClass();
        static::assertEquals(63, $postfix->getEndColumn());
    }

    /**
     * Creates a field declaration node.
     */
    protected function createNodeInstance(): ASTClassFqnPostfix
    {
        return new ASTClassFqnPostfix(__CLASS__);
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstClassFqnPostfixInClass(): ASTClassFqnPostfix
    {
        return $this->getFirstNodeOfTypeInClass(
            ASTClassFqnPostfix::class
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstMemberPrimaryPrefixInClass(): ASTMemberPrimaryPrefix
    {
        return $this->getFirstNodeOfTypeInClass(
            ASTMemberPrimaryPrefix::class
        );
    }
}
