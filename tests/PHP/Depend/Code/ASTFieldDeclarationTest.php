<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

require_once 'PHP/Depend/Code/ASTFieldDeclaration.php';
require_once 'PHP/Depend/ConstantsI.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTFieldDeclaration} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 *
 * @covers PHP_Depend_Parser
 * @covers PHP_Depend_Builder_Default
 * @covers PHP_Depend_Code_ASTFieldDeclaration
 */
class PHP_Depend_Code_ASTFieldDeclarationTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testAcceptInvokesVisitOnGivenVisitor
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptInvokesVisitOnGivenVisitor()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitFieldDeclaration'));

        $node = new PHP_Depend_Code_ASTFieldDeclaration();
        $node->accept($visitor);
    }

    /**
     * testAcceptReturnsReturnValueOfVisitMethod
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptReturnsReturnValueOfVisitMethod()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitFieldDeclaration'))
            ->will($this->returnValue(42));

        $node = new PHP_Depend_Code_ASTFieldDeclaration();
        self::assertEquals(42, $node->accept($visitor));
    }

    /**
     * Tests that a field declaration contains the expected class reference.
     * 
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testFieldDeclarationContainsClassReferenceWithAnnotationsEnabled()
    {
        $declaration = $this->_getFirstFieldDeclarationInClass(__METHOD__);

        $reference = $declaration->getChild(0);
        $this->assertEquals(__FUNCTION__, $reference->getType()->getName());
    }

    /**
     * Tests that a field declaration does not contain a class reference.
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testFieldDeclarationNotContainsClassReferenceWithAnnotationsDisabled()
    {
        $packages = self::parseCodeResourceForTest(true);

        $class = $packages->current()
            ->getClasses()
            ->current();

        $declaration = $class->getFirstChildOfType(
            PHP_Depend_Code_ASTFieldDeclaration::CLAZZ
        );
        $reference = $declaration->getFirstChildOfType(
            PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ
        );
        $this->assertNull($reference);
    }

    /**
     * Tests that the field declaration <b>setModifiers()</b> method accepts all
     * valid combinations of modifiers.
     *
     * @param integer $modifiers Combinations of valid modifiers.
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     * @dataProvider dataProviderSetModifiersAcceptsExpectedModifierCombinations
     */
    public function testSetModifiersAcceptsExpectedModifierCombinations($modifiers)
    {
        $declaration = new PHP_Depend_Code_ASTFieldDeclaration();
        $declaration->setModifiers($modifiers);
        $this->assertEquals($modifiers, $declaration->getModifiers());
    }

    /**
     * Tests that the <b>setModifiers()</b> method throws an exception when an
     * invalid modifier or modifier combination was set.
     *
     * @param integer $modifiers Combinations of invalid modifiers.
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     * @dataProvider dataProviderSetModifiersThrowsExpectedExceptionForInvalidModifiers
     */
    public function testSetModifiersThrowsExpectedExceptionForInvalidModifiers($modifiers)
    {
        $declaration = new PHP_Depend_Code_ASTFieldDeclaration();

        $this->setExpectedException(
            'InvalidArgumentException',
            'Invalid field modifiers given, allowed modifiers are ' .
            'IS_PUBLIC, IS_PROTECTED, IS_PRIVATE and IS_STATIC.'
        );

        $declaration->setModifiers($modifiers);
    }

    /**
     * testIsPublicReturnsFalseByDefault
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testIsPublicReturnsFalseByDefault()
    {
        $declaration = $this->createNodeInstance();
        self::assertFalse($declaration->isPublic());
    }

    /**
     * testIsPublicReturnsTrueWhenCorrespondingModifierWasSet
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testIsPublicReturnsTrueWhenCorrespondingModifierWasSet()
    {
        $declaration = $this->createNodeInstance();
        $declaration->setModifiers(PHP_Depend_ConstantsI::IS_PUBLIC);
        
        self::assertTrue($declaration->isPublic());
    }

    /**
     * testIsProtectedReturnsFalseByDefault
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testIsProtectedReturnsFalseByDefault()
    {
        $declaration = $this->createNodeInstance();
        self::assertFalse($declaration->isProtected());
    }

    /**
     * testIsProtectedReturnsTrueWhenCorrespondingModifierWasSet
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testIsProtectedReturnsTrueWhenCorrespondingModifierWasSet()
    {
        $declaration = $this->createNodeInstance();
        $declaration->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED);

        self::assertTrue($declaration->isProtected());
    }

    /**
     * testIsPrivateReturnsFalseByDefault
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testIsPrivateReturnsFalseByDefault()
    {
        $declaration = $this->createNodeInstance();
        self::assertFalse($declaration->isPrivate());
    }

    /**
     * testIsPrivateReturnsTrueWhenCorrespondingModifierWasSet
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testIsPrivateReturnsTrueWhenCorrespondingModifierWasSet()
    {
        $declaration = $this->createNodeInstance();
        $declaration->setModifiers(PHP_Depend_ConstantsI::IS_PRIVATE);

        self::assertTrue($declaration->isPrivate());
    }

    /**
     * testIsStaticReturnsFalseByDefault
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testIsStaticReturnsFalseByDefault()
    {
        $declaration = $this->createNodeInstance();
        self::assertFalse($declaration->isStatic());
    }

    /**
     * testIsStaticReturnsTrueWhenCorrespondingModifierWasSet
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testIsStaticReturnsTrueWhenCorrespondingModifierWasSet()
    {
        $declaration = $this->createNodeInstance();
        $declaration->setModifiers(PHP_Depend_ConstantsI::IS_STATIC);

        self::assertTrue($declaration->isStatic());
    }

    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames()
    {
        $declaration = $this->createNodeInstance();
        self::assertEquals(
            array(
                'modifiers',
                'image',
                'comment',
                'startLine',
                'startColumn',
                'endLine',
                'endColumn',
                'nodes'
            ),
            $declaration->__sleep()
        );
    }

    /**
     * testFieldDeclarationHasExpectedStartLine
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testFieldDeclarationHasExpectedStartLine()
    {
        $declaration = $this->_getFirstFieldDeclarationInClass(__METHOD__);
        $this->assertEquals(4, $declaration->getStartLine());
    }

    /**
     * testFieldDeclarationHasExpectedStartColumn
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testFieldDeclarationHasExpectedStartColumn()
    {
        $declaration = $this->_getFirstFieldDeclarationInClass(__METHOD__);
        $this->assertEquals(5, $declaration->getStartColumn());
    }

    /**
     * testFieldDeclarationHasExpectedEndLine
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testFieldDeclarationHasExpectedEndLine()
    {
        $declaration = $this->_getFirstFieldDeclarationInClass(__METHOD__);
        $this->assertEquals(5, $declaration->getEndLine());
    }

    /**
     * testFieldDeclarationHasExpectedEndColumn
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testFieldDeclarationHasExpectedEndColumn()
    {
        $declaration = $this->_getFirstFieldDeclarationInClass(__METHOD__);
        $this->assertEquals(22, $declaration->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTFieldDeclaration
     */
    private function _getFirstFieldDeclarationInClass($testCase)
    {
        return $this->getFirstNodeOfTypeInClass(
            $testCase, PHP_Depend_Code_ASTFieldDeclaration::CLAZZ
        );
    }

    /**
     * Returns valid field declation modifiers.
     *
     * @return array
     */
    public static function dataProviderSetModifiersAcceptsExpectedModifierCombinations()
    {
        return array(
            array(PHP_Depend_ConstantsI::IS_PRIVATE),
            array(PHP_Depend_ConstantsI::IS_PROTECTED),
            array(PHP_Depend_ConstantsI::IS_PUBLIC),
            array(
                PHP_Depend_ConstantsI::IS_PRIVATE |
                PHP_Depend_ConstantsI::IS_STATIC
            ),
            array(
                PHP_Depend_ConstantsI::IS_PROTECTED |
                PHP_Depend_ConstantsI::IS_STATIC
            ),
            array(
                PHP_Depend_ConstantsI::IS_PUBLIC |
                PHP_Depend_ConstantsI::IS_STATIC
            ),
        );
    }

    /**
     * Returns invalid field declation modifiers.
     *
     * @return array
     */
    public static function dataProviderSetModifiersThrowsExpectedExceptionForInvalidModifiers()
    {
        return array(
            array(PHP_Depend_ConstantsI::IS_ABSTRACT),
            array(PHP_Depend_ConstantsI::IS_FINAL),
            array(
                PHP_Depend_ConstantsI::IS_PRIVATE |
                PHP_Depend_ConstantsI::IS_ABSTRACT
            ),
            array(
                PHP_Depend_ConstantsI::IS_PROTECTED |
                PHP_Depend_ConstantsI::IS_ABSTRACT
            ),
            array(
                PHP_Depend_ConstantsI::IS_PUBLIC |
                PHP_Depend_ConstantsI::IS_FINAL
            ),
            array(
                PHP_Depend_ConstantsI::IS_PUBLIC |
                PHP_Depend_ConstantsI::IS_STATIC |
                PHP_Depend_ConstantsI::IS_FINAL
            ),
        );
    }
}
?>
