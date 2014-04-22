<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Source\AST;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTFieldDeclaration} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTFieldDeclaration
 * @group unittest
 */
class ASTFieldDeclarationTest extends \PDepend\Source\AST\ASTNodeTest
{
    /**
     * Tests that a field declaration contains the expected class reference.
     *
     * @return void
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
     */
    public function testFieldDeclarationNotContainsClassReferenceWithAnnotationsDisabled()
    {
        $namespaces = self::parseCodeResourceForTest(true);

        $class = $namespaces->current()
            ->getClasses()
            ->current();

        $declaration = $class->getFirstChildOfType(
            'PDepend\\Source\\AST\\ASTFieldDeclaration'
        );
        $reference = $declaration->getFirstChildOfType(
            'PDepend\\Source\\AST\\ASTClassOrInterfaceReference'
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
     * @dataProvider dataProviderSetModifiersAcceptsExpectedModifierCombinations
     */
    public function testSetModifiersAcceptsExpectedModifierCombinations($modifiers)
    {
        $declaration = new \PDepend\Source\AST\ASTFieldDeclaration();
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
     * @dataProvider dataProviderSetModifiersThrowsExpectedExceptionForInvalidModifiers
     */
    public function testSetModifiersThrowsExpectedExceptionForInvalidModifiers($modifiers)
    {
        $declaration = new \PDepend\Source\AST\ASTFieldDeclaration();

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
     */
    public function testIsPublicReturnsFalseByDefault()
    {
        $declaration = $this->createNodeInstance();
        $this->assertFalse($declaration->isPublic());
    }

    /**
     * testIsPublicReturnsTrueWhenCorrespondingModifierWasSet
     *
     * @return void
     */
    public function testIsPublicReturnsTrueWhenCorrespondingModifierWasSet()
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
    public function testIsProtectedReturnsFalseByDefault()
    {
        $declaration = $this->createNodeInstance();
        $this->assertFalse($declaration->isProtected());
    }

    /**
     * testIsProtectedReturnsTrueWhenCorrespondingModifierWasSet
     *
     * @return void
     */
    public function testIsProtectedReturnsTrueWhenCorrespondingModifierWasSet()
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
    public function testIsPrivateReturnsFalseByDefault()
    {
        $declaration = $this->createNodeInstance();
        $this->assertFalse($declaration->isPrivate());
    }

    /**
     * testIsPrivateReturnsTrueWhenCorrespondingModifierWasSet
     *
     * @return void
     */
    public function testIsPrivateReturnsTrueWhenCorrespondingModifierWasSet()
    {
        $declaration = $this->createNodeInstance();
        $declaration->setModifiers(State::IS_PRIVATE);

        $this->assertTrue($declaration->isPrivate());
    }

    /**
     * testIsStaticReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsStaticReturnsFalseByDefault()
    {
        $declaration = $this->createNodeInstance();
        $this->assertFalse($declaration->isStatic());
    }

    /**
     * testIsStaticReturnsTrueWhenCorrespondingModifierWasSet
     *
     * @return void
     */
    public function testIsStaticReturnsTrueWhenCorrespondingModifierWasSet()
    {
        $declaration = $this->createNodeInstance();
        $declaration->setModifiers(State::IS_STATIC);

        $this->assertTrue($declaration->isStatic());
    }

    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     *
     * @return void
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames()
    {
        $declaration = $this->createNodeInstance();
        $this->assertEquals(
            array(
                'comment',
                'metadata',
                'nodes'
            ),
            $declaration->__sleep()
        );
    }

    /**
     * testFieldDeclarationHasExpectedStartLine
     *
     * @return void
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
     * @return \PDepend\Source\AST\ASTFieldDeclaration
     */
    private function _getFirstFieldDeclarationInClass($testCase)
    {
        return $this->getFirstNodeOfTypeInClass(
            $testCase, 'PDepend\\Source\\AST\\ASTFieldDeclaration'
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
            array(State::IS_PRIVATE),
            array(State::IS_PROTECTED),
            array(State::IS_PUBLIC),
            array(
                State::IS_PRIVATE |
                State::IS_STATIC
            ),
            array(
                State::IS_PROTECTED |
                State::IS_STATIC
            ),
            array(
                State::IS_PUBLIC |
                State::IS_STATIC
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
            array(State::IS_ABSTRACT),
            array(State::IS_FINAL),
            array(
                State::IS_PRIVATE |
                State::IS_ABSTRACT
            ),
            array(
                State::IS_PROTECTED |
                State::IS_ABSTRACT
            ),
            array(
                State::IS_PUBLIC |
                State::IS_FINAL
            ),
            array(
                State::IS_PUBLIC |
                State::IS_STATIC |
                State::IS_FINAL
            ),
        );
    }
}
?>
