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
 * @since     1.0.0
 */

namespace PDepend\Source\AST;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTTraitAdaptationAlias} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since     1.0.0
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTTraitAdaptationAlias
 * @group unittest
 */
class ASTTraitAdaptationAliasTest extends \PDepend\Source\AST\ASTNodeTest
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
            State::IS_PUBLIC,
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
            State::IS_PROTECTED,
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
        $alias = new \PDepend\Source\AST\ASTTraitAdaptationAlias();
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
        $this->assertEquals(State::IS_PRIVATE, $alias->getNewModifier());
    }

    /**
     * testTraitAdaptationAlias
     *
     * @return \PDepend\Source\AST\ASTTraitAdaptationAlias
     * @since 1.0.2
     */
    public function testTraitAdaptationAlias()
    {
        $alias = $this->_getFirstTraitAdaptationAliasInClass();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTTraitAdaptationAlias', $alias);

        return $alias;
    }
    
    /**
     * testTraitAdaptationAliasHasExpectedStartLine
     *
     * @param \PDepend\Source\AST\ASTTraitAdaptationAlias $alias
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
     * @param \PDepend\Source\AST\ASTTraitAdaptationAlias $alias
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
     * @param \PDepend\Source\AST\ASTTraitAdaptationAlias $alias
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
     * @param \PDepend\Source\AST\ASTTraitAdaptationAlias $alias
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
     * @return \PDepend\Source\AST\ASTTraitAdaptationAlias
     */
    private function _getFirstTraitAdaptationAliasInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTTraitAdaptationAlias'
        );
    }

    /**
     * testTraitReference
     *
     * @return \PDepend\Source\AST\ASTTraitReference
     * @since 1.0.2
     */
    public function testTraitReference()
    {
        $reference = $this->_getFirstTraitReferenceInClass();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTTraitReference', $reference);

        return $reference;
    }

    /**
     * testTraitReferenceHasExpectedStartLine
     *
     * @param \PDepend\Source\AST\ASTTraitReference $reference
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
     * @param \PDepend\Source\AST\ASTTraitReference $reference
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
     * @param \PDepend\Source\AST\ASTTraitReference $reference
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
     * @param \PDepend\Source\AST\ASTTraitReference $reference
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
     * @return \PDepend\Source\AST\ASTTraitReference
     */
    private function _getFirstTraitReferenceInClass()
    {
        return $this->_getFirstTraitAdaptationAliasInClass()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTTraitReference');
    }
}

