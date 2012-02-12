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
 * Test case for the {@link PHP_Depend_Code_ASTTraitAdaptationAlias} class.
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
 * @covers PHP_Depend_Code_ASTTraitAdaptationAlias
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTTraitAdaptationAliasTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testGetNewNameReturnsNullByDefault
     * 
     * @return void
     */
    public function testGetNewNameReturnsNullByDefault()
    {
        $alias = $this->_getFirstTraitAdaptationAliasInClass();
        $this->assertNull($alias->getNewName());
    }

    /**
     * testGetNewNameReturnsExpectedValue
     *
     * @return void
     */
    public function testGetNewNameReturnsExpectedValue()
    {
        $alias = $this->_getFirstTraitAdaptationAliasInClass();
        $this->assertEquals('myMethodAlias', $alias->getNewName());
    }

    /**
     * testGetNewModifierReturnsMinusOneByDefault
     *
     * @return void
     */
    public function testGetNewModifierReturnsMinusOneByDefault()
    {
        $alias = $this->_getFirstTraitAdaptationAliasInClass();
        $this->assertEquals(-1, $alias->getNewModifier());
    }

    /**
     * testGetNewModifierReturnsExpectedIsPublicValue
     *
     * @return void
     */
    public function testGetNewModifierReturnsExpectedIsPublicValue()
    {
        $alias = $this->_getFirstTraitAdaptationAliasInClass();
        $this->assertEquals(
            PHP_Depend_ConstantsI::IS_PUBLIC,
            $alias->getNewModifier()
        );
    }

    /**
     * testGetNewModifierReturnsExpectedIsProtectedValue
     *
     * @return void
     */
    public function testGetNewModifierReturnsExpectedIsProtectedValue()
    {
        $alias = $this->_getFirstTraitAdaptationAliasInClass();
        $this->assertEquals(
            PHP_Depend_ConstantsI::IS_PROTECTED,
            $alias->getNewModifier()
        );
    }

    /**
     * testMagicSleepMethodReturnsExpectedSetOfPropertyNames
     *
     * @return void
     */
    public function testMagicSleepMethodReturnsExpectedSetOfPropertyNames()
    {
        $alias = new PHP_Depend_Code_ASTTraitAdaptationAlias();
        $this->assertSame(
            array(
                'newName',
                'newModifier',
                'comment',
                'metadata',
                'nodes'
            ),
            $alias->__sleep()
        );
    }

    /**
     * testGetNewModifierReturnsExpectedIsPrivateValue
     *
     * @return void
     */
    public function testGetNewModifierReturnsExpectedIsPrivateValue()
    {
        $alias = $this->_getFirstTraitAdaptationAliasInClass();
        $this->assertEquals(
            PHP_Depend_ConstantsI::IS_PRIVATE,
            $alias->getNewModifier()
        );
    }

    /**
     * testTraitAdaptationAlias
     *
     * @return PHP_Depend_Code_ASTTraitAdaptationAlias
     * @since 1.0.2
     */
    public function testTraitAdaptationAlias()
    {
        $alias = $this->_getFirstTraitAdaptationAliasInClass();
        $this->assertInstanceOf(PHP_Depend_Code_ASTTraitAdaptationAlias::CLAZZ, $alias);

        return $alias;
    }
    
    /**
     * testTraitAdaptationAliasHasExpectedStartLine
     *
     * @param PHP_Depend_Code_ASTTraitAdaptationAlias $alias
     *
     * @return void
     * @depends testTraitAdaptationAlias
     */
    public function testTraitAdaptationAliasHasExpectedStartLine($alias)
    {
        $this->assertEquals(6, $alias->getStartLine());
    }

    /**
     * testTraitAdaptationAliasHasExpectedStartColumn
     *
     * @param PHP_Depend_Code_ASTTraitAdaptationAlias $alias
     *
     * @return void
     * @depends testTraitAdaptationAlias
     */
    public function testTraitAdaptationAliasHasExpectedStartColumn($alias)
    {
        $this->assertEquals(9, $alias->getStartColumn());
    }

    /**
     * testTraitAdaptationAliasHasExpectedEndLine
     *
     * @param PHP_Depend_Code_ASTTraitAdaptationAlias $alias
     *
     * @return void
     * @depends testTraitAdaptationAlias
     */
    public function testTraitAdaptationAliasHasExpectedEndLine($alias)
    {
        $this->assertEquals(6, $alias->getEndLine());
    }

    /**
     * testTraitAdaptationAliasHasExpectedEndColumn
     *
     * @param PHP_Depend_Code_ASTTraitAdaptationAlias $alias
     *
     * @return void
     * @depends testTraitAdaptationAlias
     */
    public function testTraitAdaptationAliasHasExpectedEndColumn($alias)
    {
        $this->assertEquals(46, $alias->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return PHP_Depend_Code_ASTTraitAdaptationAlias
     */
    private function _getFirstTraitAdaptationAliasInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(),
            PHP_Depend_Code_ASTTraitAdaptationAlias::CLAZZ
        );
    }

    /**
     * testTraitReference
     *
     * @return PHP_Depend_Code_ASTTraitReference
     * @since 1.0.2
     */
    public function testTraitReference()
    {
        $reference = $this->_getFirstTraitReferenceInClass();
        $this->assertInstanceOf(PHP_Depend_Code_ASTTraitReference::CLAZZ, $reference);

        return $reference;
    }

    /**
     * testTraitReferenceHasExpectedStartLine
     *
     * @param PHP_Depend_Code_ASTTraitReference $reference
     *
     * @return void
     * @depends testTraitReference
     */
    public function testTraitReferenceHasExpectedStartLine($reference)
    {
        $this->assertEquals(7, $reference->getStartLine());
    }

    /**
     * testTraitReferenceHasExpectedStartColumn
     *
     * @param PHP_Depend_Code_ASTTraitReference $reference
     *
     * @return void
     * @depends testTraitReference
     */
    public function testTraitReferenceHasExpectedStartColumn($reference)
    {
        $this->assertEquals(9, $reference->getStartColumn());
    }

    /**
     * testTraitReferenceHasExpectedEndLine
     *
     * @param PHP_Depend_Code_ASTTraitReference $reference
     *
     * @return void
     * @depends testTraitReference
     */
    public function testTraitReferenceHasExpectedEndLine($reference)
    {
        $this->assertEquals(7, $reference->getEndLine());
    }

    /**
     * testTraitReferenceHasExpectedEndColumn
     *
     * @param PHP_Depend_Code_ASTTraitReference $reference
     *
     * @return void
     * @depends testTraitReference
     */
    public function testTraitReferenceHasExpectedEndColumn($reference)
    {
        $this->assertEquals(36, $reference->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return PHP_Depend_Code_ASTTraitReference
     */
    private function _getFirstTraitReferenceInClass()
    {
        return $this->_getFirstTraitAdaptationAliasInClass()
            ->getFirstChildOfType(PHP_Depend_Code_ASTTraitReference::CLAZZ);
    }
}

