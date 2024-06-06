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

use ReflectionException;
use ReflectionMethod;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTTraitUseStatement} class.
 *
 * @covers \PDepend\Source\AST\ASTTraitUseStatement
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 1.0.0
 *
 * @group unittest
 */
class ASTTraitUseStatementTest extends ASTNodeTestCase
{
    /**
     * testHasExcludeForReturnsFalseIfNoInsteadExists
     */
    public function testHasExcludeForReturnsFalseIfNoInsteadExists(): void
    {
        $class = $this->getFirstClassForTestCase();
        $useStmt = $class->getFirstChildOfType(ASTTraitUseStatement::class);
        static::assertNotNull($useStmt);
        $methods = $useStmt->getAllMethods();

        static::assertArrayHasKey(0, $methods);
        static::assertFalse($useStmt->hasExcludeFor($methods[0]));
    }

    /**
     * testHasExcludeForReturnsFalseIfMethodNotAffectedByInstead
     */
    public function testHasExcludeForReturnsFalseIfMethodNotAffectedByInstead(): void
    {
        $class = $this->getFirstClassForTestCase();
        $useStmt = $class->getFirstChildOfType(ASTTraitUseStatement::class);
        static::assertNotNull($useStmt);
        $methods = $useStmt->getAllMethods();

        static::assertArrayHasKey(0, $methods);
        static::assertFalse($useStmt->hasExcludeFor($methods[0]));
    }

    /**
     * testHasExcludeForReturnsTrueIfMethodAffectedByInstead
     */
    public function testHasExcludeForReturnsTrueIfMethodAffectedByInstead(): void
    {
        $class = $this->getFirstClassForTestCase();
        $useStmt = $class->getFirstChildOfType(ASTTraitUseStatement::class);
        static::assertNotNull($useStmt);
        $methods = $useStmt->getAllMethods();

        static::assertArrayHasKey(0, $methods);
        static::assertTrue($useStmt->hasExcludeFor($methods[0]));
    }

    /**
     * testHasExcludeForReturnsTrueIfMethodAffectedBySecondInstead
     */
    public function testHasExcludeForReturnsTrueIfMethodAffectedBySecondInstead(): void
    {
        $class = $this->getFirstClassForTestCase();
        $useStmt = $class->getFirstChildOfType(
            ASTTraitUseStatement::class
        );
        static::assertNotNull($useStmt);
        $methods = $useStmt->getAllMethods();

        static::assertArrayHasKey(0, $methods);
        static::assertTrue($useStmt->hasExcludeFor($methods[0]));
    }

    /**
     * testTraitUseInsteadOfSelf
     *
     * @throws ReflectionException
     *
     * @group issue-154
     */
    public function testTraitUseInsteadOfSelf(): void
    {
        /** @var AbstractASTClassOrInterface $class */
        $class = $this->getFirstClassForTestCase();
        $getTraitMethods = new ReflectionMethod($class, 'getTraitMethods');
        $getTraitMethods->setAccessible(true);
        $methods = $getTraitMethods->invoke($class);

        static::assertIsArray($methods);
        static::assertSame(['test'], array_keys($methods));
    }

    /**
     * testTraitMethodAlias
     *
     * @throws ReflectionException
     *
     * @group issue-154
     */
    public function testTraitMethodAlias(): void
    {
        /** @var AbstractASTClassOrInterface $class */
        $class = $this->getFirstClassForTestCase();
        $getTraitMethods = new ReflectionMethod($class, 'getTraitMethods');
        $getTraitMethods->setAccessible(true);
        $methods = $getTraitMethods->invoke($class);

        static::assertIsArray($methods);
        static::assertSame(['testa', 'testb'], array_keys($methods));
    }

    /**
     * testGetAllMethodsOnClassWithParentReturnsTraitMethod
     */
    public function testGetAllMethodsOnClassWithParentReturnsTraitMethod(): void
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        static::assertInstanceOf(
            ASTTrait::class,
            $methods[0]->getParent()
        );
    }

    /**
     * testGetAllMethodsOnClassWithParentAndPrecedenceReturnsParentMethod
     *
     * @since 1.0.0
     */
    public function testGetAllMethodsOnClassWithParentAndPrecedenceReturnsParentMethod(): void
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        static::assertInstanceOf(
            ASTTrait::class,
            $methods[0]->getParent()
        );
    }

    /**
     * testGetAllMethodsOnTraitUsingTraitReturnsExpectedResult
     *
     * @since 1.0.0
     */
    public function testGetAllMethodsOnTraitUsingTraitReturnsExpectedResult(): void
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        static::assertEquals('foo', $methods[0]->getImage());
    }

    /**
     * testGetAllMethodsWithAliasedMethodCollision
     */
    public function testGetAllMethodsWithAliasedMethodCollision(): void
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        static::assertCount(2, $useStmt->getAllMethods());
    }

    /**
     * testGetAllMethodsWithAliasedMethodTwice
     */
    public function testGetAllMethodsWithAliasedMethodTwice(): void
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        static::assertCount(2, $useStmt->getAllMethods());
    }

    /**
     * testGetAllMethodsWithVisibilityChangedToPublic
     */
    public function testGetAllMethodsWithVisibilityChangedToPublic(): void
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        static::assertEquals(
            State::IS_PUBLIC,
            $methods[0]->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedToProtected
     */
    public function testGetAllMethodsWithVisibilityChangedToProtected(): void
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        static::assertEquals(
            State::IS_PROTECTED,
            $methods[0]->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedToPrivate
     */
    public function testGetAllMethodsWithVisibilityChangedToPrivate(): void
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        static::assertEquals(
            State::IS_PRIVATE,
            $methods[0]->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedKeepsAbstractModifier
     */
    public function testGetAllMethodsWithVisibilityChangedKeepsAbstractModifier(): void
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        static::assertEquals(
            State::IS_PROTECTED | State::IS_ABSTRACT,
            $methods[0]->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedKeepsStaticModifier
     */
    public function testGetAllMethodsWithVisibilityChangedKeepsStaticModifier(): void
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        static::assertEquals(
            State::IS_PUBLIC | State::IS_STATIC,
            $methods[0]->getModifiers()
        );
    }

    /**
     * testGetAllMethodsHandlesTraitMethodPrecedence
     */
    public function testGetAllMethodsHandlesTraitMethodPrecedence(): void
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        static::assertEquals(
            'testGetAllMethodsHandlesTraitMethodPrecedenceUsedTraitOne',
            $methods[0]->getParent()?->getImage()
        );
    }

    /**
     * testGetAllMethodsExcludeTraitMethodWithPrecedence
     */
    public function testGetAllMethodsExcludeTraitMethodWithPrecedence(): void
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        static::assertCount(2, $useStmt->getAllMethods());
    }

    /**
     * testTraitUseStatementWithSimpleAliasHasExpectedEndLine
     */
    public function testTraitUseStatementWithSimpleAliasHasExpectedEndLine(): void
    {
        $stmt = $this->getFirstTraitUseStatementInClass();
        static::assertEquals(6, $stmt->getEndLine());
    }

    /**
     * testTraitUseStatementWithSimpleAliasHasExpectedEndColumn
     */
    public function testTraitUseStatementWithSimpleAliasHasExpectedEndColumn(): void
    {
        $stmt = $this->getFirstTraitUseStatementInClass();
        static::assertEquals(21, $stmt->getEndColumn());
    }

    /**
     * testTraitUseStatementWithQualifiedAliasHasExpectedEndLine
     */
    public function testTraitUseStatementWithQualifiedAliasHasExpectedEndLine(): void
    {
        $stmt = $this->getFirstTraitUseStatementInClass();
        static::assertEquals(6, $stmt->getEndLine());
    }

    /**
     * testTraitUseStatementWithQualifiedAliasHasExpectedEndColumn
     */
    public function testTraitUseStatementWithQualifiedAliasHasExpectedEndColumn(): void
    {
        $stmt = $this->getFirstTraitUseStatementInClass();
        static::assertEquals(21, $stmt->getEndColumn());
    }

    /**
     * testTraitUseStatementWithSingleInsteadofHasExpectedEndLine
     */
    public function testTraitUseStatementWithSingleInsteadofHasExpectedEndLine(): void
    {
        $stmt = $this->getFirstTraitUseStatementInClass();
        static::assertEquals(9, $stmt->getEndLine());
    }

    /**
     * testTraitUseStatementWithSingleInsteadofHasExpectedEndColumn
     */
    public function testTraitUseStatementWithSingleInsteadofHasExpectedEndColumn(): void
    {
        $stmt = $this->getFirstTraitUseStatementInClass();
        static::assertEquals(93, $stmt->getEndColumn());
    }

    /**
     * testTraitUseStatementWithMultipleInsteadofHasExpectedEndLine
     */
    public function testTraitUseStatementWithMultipleInsteadofHasExpectedEndLine(): void
    {
        $stmt = $this->getFirstTraitUseStatementInClass();
        static::assertEquals(11, $stmt->getEndLine());
    }

    /**
     * testTraitUseStatementWithMultipleInsteadofHasExpectedEndColumn
     */
    public function testTraitUseStatementWithMultipleInsteadofHasExpectedEndColumn(): void
    {
        $stmt = $this->getFirstTraitUseStatementInClass();
        static::assertEquals(97, $stmt->getEndColumn());
    }

    /**
     * testTraitUseStatement
     *
     * @since 1.0.2
     */
    public function testTraitUseStatement(): ASTTraitUseStatement
    {
        $stmt = $this->getFirstTraitUseStatementInClass();
        static::assertInstanceOf(ASTTraitUseStatement::class, $stmt);

        return $stmt;
    }

    /**
     * testTraitUseStatementHasExpectedStartLine
     *
     * @depends testTraitUseStatement
     */
    public function testTraitUseStatementHasExpectedStartLine(ASTTraitUseStatement $stmt): void
    {
        static::assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testTraitUseStatementHasExpectedStartColumn
     *
     * @depends testTraitUseStatement
     */
    public function testTraitUseStatementHasExpectedStartColumn(ASTTraitUseStatement $stmt): void
    {
        static::assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testTraitUseStatementHasExpectedEndLine
     *
     * @depends testTraitUseStatement
     */
    public function testTraitUseStatementHasExpectedEndLine(ASTTraitUseStatement $stmt): void
    {
        static::assertEquals(9, $stmt->getEndLine());
    }

    /**
     * testTraitUseStatementHasExpectedEndColumn
     *
     * @depends testTraitUseStatement
     */
    public function testTraitUseStatementHasExpectedEndColumn(ASTTraitUseStatement $stmt): void
    {
        static::assertEquals(13, $stmt->getEndColumn());
    }

    /**
     * testTraitUseStatementInTrait
     *
     * @since 1.0.2
     */
    public function testTraitUseStatementInTrait(): ASTTraitUseStatement
    {
        $stmt = $this->getFirstTraitUseStatementInTrait();
        static::assertInstanceOf(ASTTraitUseStatement::class, $stmt);

        return $stmt;
    }

    /**
     * testTraitUseStatementInTraitHasExpectedStartLine
     *
     * @depends testTraitUseStatementInTrait
     */
    public function testTraitUseStatementInTraitHasExpectedStartLine(ASTTraitUseStatement $stmt): void
    {
        static::assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testTraitUseStatementInTraitHasExpectedStartColumn
     *
     * @depends testTraitUseStatementInTrait
     */
    public function testTraitUseStatementInTraitHasExpectedStartColumn(ASTTraitUseStatement $stmt): void
    {
        static::assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testTraitUseStatementInTraitHasExpectedEndLine
     *
     * @depends testTraitUseStatementInTrait
     */
    public function testTraitUseStatementInTraitHasExpectedEndLine(ASTTraitUseStatement $stmt): void
    {
        static::assertEquals(4, $stmt->getEndLine());
    }

    /**
     * testTraitUseStatementInTraitHasExpectedEndColumn
     *
     * @depends testTraitUseStatementInTrait
     */
    public function testTraitUseStatementInTraitHasExpectedEndColumn(ASTTraitUseStatement $stmt): void
    {
        static::assertEquals(19, $stmt->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstTraitUseStatementInClass(): ASTTraitUseStatement
    {
        return $this->getFirstNodeOfTypeInClass(
            ASTTraitUseStatement::class
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstTraitUseStatementInTrait(): ASTTraitUseStatement
    {
        return $this->getFirstNodeOfTypeInTrait(
            ASTTraitUseStatement::class
        );
    }
}
