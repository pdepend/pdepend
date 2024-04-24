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
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 1.0.0
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTTraitUseStatement
 * @group unittest
 */
class ASTTraitUseStatementTest extends ASTNodeTest
{
    /**
     * testHasExcludeForReturnsFalseIfNoInsteadExists
     *
     * @return void
     */
    public function testHasExcludeForReturnsFalseIfNoInsteadExists()
    {
        $class   = $this->getFirstClassForTestCase();
        $useStmt = $class->getFirstChildOfType(
            'PDepend\\Source\\AST\\ASTTraitUseStatement'
        );
        $methods = $useStmt->getAllMethods();

        $this->assertFalse($useStmt->hasExcludeFor($methods[0]));
    }

    /**
     * testHasExcludeForReturnsFalseIfMethodNotAffectedByInstead
     *
     * @return void
     */
    public function testHasExcludeForReturnsFalseIfMethodNotAffectedByInstead()
    {
        $class   = $this->getFirstClassForTestCase();
        $useStmt = $class->getFirstChildOfType(
            'PDepend\\Source\\AST\\ASTTraitUseStatement'
        );
        $methods = $useStmt->getAllMethods();

        $this->assertFalse($useStmt->hasExcludeFor($methods[0]));
    }

    /**
     * testHasExcludeForReturnsTrueIfMethodAffectedByInstead
     *
     * @return void
     */
    public function testHasExcludeForReturnsTrueIfMethodAffectedByInstead()
    {
        $class   = $this->getFirstClassForTestCase();
        $useStmt = $class->getFirstChildOfType(
            'PDepend\\Source\\AST\\ASTTraitUseStatement'
        );
        $methods = $useStmt->getAllMethods();

        $this->assertTrue($useStmt->hasExcludeFor($methods[0]));
    }

    /**
     * testHasExcludeForReturnsTrueIfMethodAffectedBySecondInstead
     *
     * @return void
     */
    public function testHasExcludeForReturnsTrueIfMethodAffectedBySecondInstead()
    {
        $class   = $this->getFirstClassForTestCase();
        $useStmt = $class->getFirstChildOfType(
            'PDepend\\Source\\AST\\ASTTraitUseStatement'
        );
        $methods = $useStmt->getAllMethods();

        $this->assertTrue($useStmt->hasExcludeFor($methods[0]));
    }

    /**
     * testTraitUseInsteadOfSelf
     *
     * @throws ReflectionException
     *
     * @return void
     *
     * @group issue-154
     */
    public function testTraitUseInsteadOfSelf()
    {
        /** @var AbstractASTClassOrInterface $class */
        $class = $this->getFirstClassForTestCase();
        $getTraitMethods = new ReflectionMethod($class, 'getTraitMethods');
        $getTraitMethods->setAccessible(true);
        $methods = $getTraitMethods->invoke($class);

        $this->assertSame(array('test'), array_keys($methods));
    }

    /**
     * testTraitMethodAlias
     *
     * @throws ReflectionException
     *
     * @return void
     *
     * @group issue-154
     */
    public function testTraitMethodAlias()
    {
        /** @var AbstractASTClassOrInterface $class */
        $class = $this->getFirstClassForTestCase();
        $getTraitMethods = new ReflectionMethod($class, 'getTraitMethods');
        $getTraitMethods->setAccessible(true);
        $methods = $getTraitMethods->invoke($class);

        $this->assertSame(array('testa', 'testb'), array_keys($methods));
    }

    /**
     * testGetAllMethodsOnClassWithParentReturnsTraitMethod
     *
     * @return void
     */
    public function testGetAllMethodsOnClassWithParentReturnsTraitMethod()
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTTrait',
            $methods[0]->getParent()
        );
    }

    /**
     * testGetAllMethodsOnClassWithParentAndPrecedenceReturnsParentMethod
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetAllMethodsOnClassWithParentAndPrecedenceReturnsParentMethod()
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTTrait',
            $methods[0]->getParent()
        );
    }

    /**
     * testGetAllMethodsOnTraitUsingTraitReturnsExpectedResult
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetAllMethodsOnTraitUsingTraitReturnsExpectedResult()
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        $this->assertEquals('foo', $methods[0]->getName());
    }

    /**
     * testGetAllMethodsWithAliasedMethodCollision
     *
     * @return void
     */
    public function testGetAllMethodsWithAliasedMethodCollision()
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        $this->assertCount(2, $useStmt->getAllMethods());
    }

    /**
     * testGetAllMethodsWithAliasedMethodTwice
     *
     * @return void
     */
    public function testGetAllMethodsWithAliasedMethodTwice()
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        $this->assertCount(2, $useStmt->getAllMethods());
    }

    /**
     * testGetAllMethodsWithVisibilityChangedToPublic
     *
     * @return void
     */
    public function testGetAllMethodsWithVisibilityChangedToPublic()
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        $this->assertEquals(
            State::IS_PUBLIC,
            $methods[0]->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedToProtected
     *
     * @return void
     */
    public function testGetAllMethodsWithVisibilityChangedToProtected()
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        $this->assertEquals(
            State::IS_PROTECTED,
            $methods[0]->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedToPrivate
     *
     * @return void
     */
    public function testGetAllMethodsWithVisibilityChangedToPrivate()
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        $this->assertEquals(
            State::IS_PRIVATE,
            $methods[0]->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedKeepsAbstractModifier
     *
     * @return void
     */
    public function testGetAllMethodsWithVisibilityChangedKeepsAbstractModifier()
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        $this->assertEquals(
            State::IS_PROTECTED | State::IS_ABSTRACT,
            $methods[0]->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedKeepsStaticModifier
     *
     * @return void
     */
    public function testGetAllMethodsWithVisibilityChangedKeepsStaticModifier()
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        $this->assertEquals(
            State::IS_PUBLIC | State::IS_STATIC,
            $methods[0]->getModifiers()
        );
    }

    /**
     * testGetAllMethodsHandlesTraitMethodPrecedence
     *
     * @return void
     */
    public function testGetAllMethodsHandlesTraitMethodPrecedence()
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        $this->assertEquals(
            'testGetAllMethodsHandlesTraitMethodPrecedenceUsedTraitOne',
            $methods[0]->getParent()->getName()
        );
    }

    /**
     * testGetAllMethodsExcludeTraitMethodWithPrecedence
     *
     * @return void
     */
    public function testGetAllMethodsExcludeTraitMethodWithPrecedence()
    {
        $useStmt = $this->getFirstTraitUseStatementInClass();
        $this->assertCount(2, $useStmt->getAllMethods());
    }

    /**
     * testTraitUseStatementWithSimpleAliasHasExpectedEndLine
     *
     * @return void
     */
    public function testTraitUseStatementWithSimpleAliasHasExpectedEndLine()
    {
        $stmt = $this->getFirstTraitUseStatementInClass();
        $this->assertEquals(6, $stmt->getEndLine());
    }

    /**
     * testTraitUseStatementWithSimpleAliasHasExpectedEndColumn
     *
     * @return void
     */
    public function testTraitUseStatementWithSimpleAliasHasExpectedEndColumn()
    {
        $stmt = $this->getFirstTraitUseStatementInClass();
        $this->assertEquals(21, $stmt->getEndColumn());
    }

    /**
     * testTraitUseStatementWithQualifiedAliasHasExpectedEndLine
     *
     * @return void
     */
    public function testTraitUseStatementWithQualifiedAliasHasExpectedEndLine()
    {
        $stmt = $this->getFirstTraitUseStatementInClass();
        $this->assertEquals(6, $stmt->getEndLine());
    }

    /**
     * testTraitUseStatementWithQualifiedAliasHasExpectedEndColumn
     *
     * @return void
     */
    public function testTraitUseStatementWithQualifiedAliasHasExpectedEndColumn()
    {
        $stmt = $this->getFirstTraitUseStatementInClass();
        $this->assertEquals(21, $stmt->getEndColumn());
    }

    /**
     * testTraitUseStatementWithSingleInsteadofHasExpectedEndLine
     *
     * @return void
     */
    public function testTraitUseStatementWithSingleInsteadofHasExpectedEndLine()
    {
        $stmt = $this->getFirstTraitUseStatementInClass();
        $this->assertEquals(9, $stmt->getEndLine());
    }

    /**
     * testTraitUseStatementWithSingleInsteadofHasExpectedEndColumn
     *
     * @return void
     */
    public function testTraitUseStatementWithSingleInsteadofHasExpectedEndColumn()
    {
        $stmt = $this->getFirstTraitUseStatementInClass();
        $this->assertEquals(93, $stmt->getEndColumn());
    }

    /**
     * testTraitUseStatementWithMultipleInsteadofHasExpectedEndLine
     *
     * @return void
     */
    public function testTraitUseStatementWithMultipleInsteadofHasExpectedEndLine()
    {
        $stmt = $this->getFirstTraitUseStatementInClass();
        $this->assertEquals(11, $stmt->getEndLine());
    }

    /**
     * testTraitUseStatementWithMultipleInsteadofHasExpectedEndColumn
     *
     * @return void
     */
    public function testTraitUseStatementWithMultipleInsteadofHasExpectedEndColumn()
    {
        $stmt = $this->getFirstTraitUseStatementInClass();
        $this->assertEquals(97, $stmt->getEndColumn());
    }

    /**
     * testTraitUseStatement
     *
     * @return \PDepend\Source\AST\ASTTraitUseStatement
     * @since 1.0.2
     */
    public function testTraitUseStatement()
    {
        $stmt = $this->getFirstTraitUseStatementInClass();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTTraitUseStatement', $stmt);

        return $stmt;
    }

    /**
     * testTraitUseStatementHasExpectedStartLine
     *
     * @param \PDepend\Source\AST\ASTTraitUseStatement $stmt
     *
     * @return void
     * @depends testTraitUseStatement
     */
    public function testTraitUseStatementHasExpectedStartLine($stmt)
    {
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testTraitUseStatementHasExpectedStartColumn
     *
     * @param \PDepend\Source\AST\ASTTraitUseStatement $stmt
     *
     * @return void
     * @depends testTraitUseStatement
     */
    public function testTraitUseStatementHasExpectedStartColumn($stmt)
    {
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testTraitUseStatementHasExpectedEndLine
     *
     * @param \PDepend\Source\AST\ASTTraitUseStatement $stmt
     *
     * @return void
     * @depends testTraitUseStatement
     */
    public function testTraitUseStatementHasExpectedEndLine($stmt)
    {
        $this->assertEquals(9, $stmt->getEndLine());
    }

    /**
     * testTraitUseStatementHasExpectedEndColumn
     *
     * @param \PDepend\Source\AST\ASTTraitUseStatement $stmt
     *
     * @return void
     * @depends testTraitUseStatement
     */
    public function testTraitUseStatementHasExpectedEndColumn($stmt)
    {
        $this->assertEquals(13, $stmt->getEndColumn());
    }

    /**
     * testTraitUseStatementInTrait
     *
     * @return \PDepend\Source\AST\ASTTraitUseStatement
     * @since 1.0.2
     */
    public function testTraitUseStatementInTrait()
    {
        $stmt = $this->getFirstTraitUseStatementInTrait();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTTraitUseStatement', $stmt);

        return $stmt;
    }

    /**
     * testTraitUseStatementInTraitHasExpectedStartLine
     *
     * @param \PDepend\Source\AST\ASTTraitUseStatement $stmt
     *
     * @return void
     * @depends testTraitUseStatementInTrait
     */
    public function testTraitUseStatementInTraitHasExpectedStartLine($stmt)
    {
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testTraitUseStatementInTraitHasExpectedStartColumn
     *
     * @param \PDepend\Source\AST\ASTTraitUseStatement $stmt
     *
     * @return void
     * @depends testTraitUseStatementInTrait
     */
    public function testTraitUseStatementInTraitHasExpectedStartColumn($stmt)
    {
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testTraitUseStatementInTraitHasExpectedEndLine
     *
     * @param \PDepend\Source\AST\ASTTraitUseStatement $stmt
     *
     * @return void
     * @depends testTraitUseStatementInTrait
     */
    public function testTraitUseStatementInTraitHasExpectedEndLine($stmt)
    {
        $this->assertEquals(4, $stmt->getEndLine());
    }

    /**
     * testTraitUseStatementInTraitHasExpectedEndColumn
     *
     * @param \PDepend\Source\AST\ASTTraitUseStatement $stmt
     *
     * @return void
     * @depends testTraitUseStatementInTrait
     */
    public function testTraitUseStatementInTraitHasExpectedEndColumn($stmt)
    {
        $this->assertEquals(19, $stmt->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return \PDepend\Source\AST\ASTTraitUseStatement
     */
    private function getFirstTraitUseStatementInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTTraitUseStatement'
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return \PDepend\Source\AST\ASTTraitUseStatement
     */
    private function getFirstTraitUseStatementInTrait()
    {
        return $this->getFirstNodeOfTypeInTrait(
            'PDepend\\Source\\AST\\ASTTraitUseStatement'
        );
    }
}
