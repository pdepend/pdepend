<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@pdepend.org>.
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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 * @since      1.0.0
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTTraitUseStatement} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 * @since      1.0.0
 *
 * @covers PHP_Depend_Parser
 * @covers PHP_Depend_Code_ASTTraitUseStatement
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTTraitUseStatementTest extends PHP_Depend_Code_ASTNodeTest
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
            PHP_Depend_Code_ASTTraitUseStatement::CLAZZ
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
            PHP_Depend_Code_ASTTraitUseStatement::CLAZZ
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
            PHP_Depend_Code_ASTTraitUseStatement::CLAZZ
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
            PHP_Depend_Code_ASTTraitUseStatement::CLAZZ
        );
        $methods = $useStmt->getAllMethods();

        $this->assertTrue($useStmt->hasExcludeFor($methods[0]));
    }

    /**
     * testGetAllMethodsOnClassWithParentReturnsTraitMethod
     *
     * @return void
     */
    public function testGetAllMethodsOnClassWithParentReturnsTraitMethod()
    {
        $useStmt = $this->_getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        $this->assertInstanceOf(
            PHP_Depend_Code_Trait::CLAZZ,
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
        $useStmt = $this->_getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        $this->assertInstanceOf(
            PHP_Depend_Code_Trait::CLAZZ,
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
        $useStmt = $this->_getFirstTraitUseStatementInClass();
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
        $useStmt = $this->_getFirstTraitUseStatementInClass();
        $this->assertEquals(2, count($useStmt->getAllMethods()));
    }

    /**
     * testGetAllMethodsWithAliasedMethodTwice
     *
     * @return void
     */
    public function testGetAllMethodsWithAliasedMethodTwice()
    {
        $useStmt = $this->_getFirstTraitUseStatementInClass();
        $this->assertEquals(2, count($useStmt->getAllMethods()));
    }

    /**
     * testGetAllMethodsWithVisibilityChangedToPublic
     *
     * @return void
     */
    public function testGetAllMethodsWithVisibilityChangedToPublic()
    {
        $useStmt = $this->_getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        $this->assertEquals(
            PHP_Depend_ConstantsI::IS_PUBLIC,
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
        $useStmt = $this->_getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        $this->assertEquals(
            PHP_Depend_ConstantsI::IS_PROTECTED,
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
        $useStmt = $this->_getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        $this->assertEquals(
            PHP_Depend_ConstantsI::IS_PRIVATE,
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
        $useStmt = $this->_getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        $this->assertEquals(
            PHP_Depend_ConstantsI::IS_PROTECTED | PHP_Depend_ConstantsI::IS_ABSTRACT,
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
        $useStmt = $this->_getFirstTraitUseStatementInClass();
        $methods = $useStmt->getAllMethods();

        $this->assertEquals(
            PHP_Depend_ConstantsI::IS_PUBLIC | PHP_Depend_ConstantsI::IS_STATIC,
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
        $useStmt = $this->_getFirstTraitUseStatementInClass();
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
        $useStmt = $this->_getFirstTraitUseStatementInClass();
        $this->assertSame(2, count($useStmt->getAllMethods()));
    }

    /**
     * testTraitUseStatementWithSimpleAliasHasExpectedEndLine
     *
     * @return void
     */
    public function testTraitUseStatementWithSimpleAliasHasExpectedEndLine()
    {
        $stmt = $this->_getFirstTraitUseStatementInClass();
        $this->assertEquals(6, $stmt->getEndLine());
    }

    /**
     * testTraitUseStatementWithSimpleAliasHasExpectedEndColumn
     *
     * @return void
     */
    public function testTraitUseStatementWithSimpleAliasHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstTraitUseStatementInClass();
        $this->assertEquals(21, $stmt->getEndColumn());
    }

    /**
     * testTraitUseStatementWithQualifiedAliasHasExpectedEndLine
     *
     * @return void
     */
    public function testTraitUseStatementWithQualifiedAliasHasExpectedEndLine()
    {
        $stmt = $this->_getFirstTraitUseStatementInClass();
        $this->assertEquals(6, $stmt->getEndLine());
    }

    /**
     * testTraitUseStatementWithQualifiedAliasHasExpectedEndColumn
     *
     * @return void
     */
    public function testTraitUseStatementWithQualifiedAliasHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstTraitUseStatementInClass();
        $this->assertEquals(21, $stmt->getEndColumn());
    }

    /**
     * testTraitUseStatementWithSingleInsteadofHasExpectedEndLine
     *
     * @return void
     */
    public function testTraitUseStatementWithSingleInsteadofHasExpectedEndLine()
    {
        $stmt = $this->_getFirstTraitUseStatementInClass();
        $this->assertEquals(9, $stmt->getEndLine());
    }

    /**
     * testTraitUseStatementWithSingleInsteadofHasExpectedEndColumn
     *
     * @return void
     */
    public function testTraitUseStatementWithSingleInsteadofHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstTraitUseStatementInClass();
        $this->assertEquals(93, $stmt->getEndColumn());
    }

    /**
     * testTraitUseStatementWithMultipleInsteadofHasExpectedEndLine
     *
     * @return void
     */
    public function testTraitUseStatementWithMultipleInsteadofHasExpectedEndLine()
    {
        $stmt = $this->_getFirstTraitUseStatementInClass();
        $this->assertEquals(11, $stmt->getEndLine());
    }

    /**
     * testTraitUseStatementWithMultipleInsteadofHasExpectedEndColumn
     *
     * @return void
     */
    public function testTraitUseStatementWithMultipleInsteadofHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstTraitUseStatementInClass();
        $this->assertEquals(97, $stmt->getEndColumn());
    }

    /**
     * testTraitUseStatement
     *
     * @return PHP_Depend_Code_ASTTraitUseStatement
     * @since 1.0.2
     */
    public function testTraitUseStatement()
    {
        $stmt = $this->_getFirstTraitUseStatementInClass();
        $this->assertInstanceOf(PHP_Depend_Code_ASTTraitUseStatement::CLAZZ, $stmt);

        return $stmt;
    }

    /**
     * testTraitUseStatementHasExpectedStartLine
     *
     * @param PHP_Depend_Code_ASTTraitUseStatement $stmt
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
     * @param PHP_Depend_Code_ASTTraitUseStatement $stmt
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
     * @param PHP_Depend_Code_ASTTraitUseStatement $stmt
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
     * @param PHP_Depend_Code_ASTTraitUseStatement $stmt
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
     * @return PHP_Depend_Code_ASTTraitUseStatement
     * @since 1.0.2
     */
    public function testTraitUseStatementInTrait()
    {
        $stmt = $this->_getFirstTraitUseStatementInTrait();
        $this->assertInstanceOf(PHP_Depend_Code_ASTTraitUseStatement::CLAZZ, $stmt);

        return $stmt;
    }

    /**
     * testTraitUseStatementInTraitHasExpectedStartLine
     *
     * @param PHP_Depend_Code_ASTTraitUseStatement $stmt
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
     * @param PHP_Depend_Code_ASTTraitUseStatement $stmt
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
     * @param PHP_Depend_Code_ASTTraitUseStatement $stmt
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
     * @param PHP_Depend_Code_ASTTraitUseStatement $stmt
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
     * @return PHP_Depend_Code_ASTTraitUseStatement
     */
    private function _getFirstTraitUseStatementInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(),
            PHP_Depend_Code_ASTTraitUseStatement::CLAZZ
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return PHP_Depend_Code_ASTTraitUseStatement
     */
    private function _getFirstTraitUseStatementInTrait()
    {
        return $this->getFirstNodeOfTypeInTrait(
            PHP_Depend_Code_ASTTraitUseStatement::CLAZZ
        );
    }
}
