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
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTConstantDefinition} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 *
 * @covers PHP_Depend_Parser
 * @covers PHP_Depend_Code_ASTConstantDefinition
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTConstantDefinitionTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * Tests that the constant definition has the expected doc comment block.
     *
     * @return void
     */
    public function testConstantDefinitionHasExpectedDocComment()
    {
        $constant = $this->_getFirstConstantDefinitionInClass();
        $this->assertEquals(
            "/**\n" .
            "     * Foo bar baz foobar.\n" .
            "     */",
            $constant->getComment()
        );
    }

    /**
     * Tests that the constant definition has the expected doc comment block.
     *
     * @return void
     */
    public function testConstantDefinitionHasExpectedDocCommentWithInlineCommentBetween()
    {
        $constant = $this->_getFirstConstantDefinitionInClass();
        $this->assertEquals(
            "/**\n" .
            "     * Foo bar baz foobar.\n" .
            "     */",
            $constant->getComment()
        );
    }

    /**
     * testConstantDefinition
     *
     * @return PHP_Depend_Code_ASTConstantDefinition
     * @since 1.0.2
     */
    public function testConstantDefinition()
    {
        $constant = $this->_getFirstConstantDefinitionInClass();
        $this->assertInstanceOf(PHP_Depend_Code_ASTConstantDefinition::CLAZZ, $constant);

        return $constant;
    }

    /**
     * testConstantDefinitionHasExpectedStartLine
     *
     * @param PHP_Depend_Code_ASTConstantDefinition $constant
     *
     * @return void
     * @depends testConstantDefinition
     */
    public function testConstantDefinitionHasExpectedStartLine($constant)
    {
        $this->assertEquals(4, $constant->getStartLine());
    }

    /**
     * testConstantDefinitionHasExpectedStartColumn
     *
     * @param PHP_Depend_Code_ASTConstantDefinition $constant
     *
     * @return void
     * @depends testConstantDefinition
     */
    public function testConstantDefinitionHasExpectedStartColumn($constant)
    {
        $this->assertEquals(5, $constant->getStartColumn());
    }

    /**
     * testConstantDefinitionHasExpectedEndLine
     *
     * @param PHP_Depend_Code_ASTConstantDefinition $constant
     *
     * @return void
     * @depends testConstantDefinition
     */
    public function testConstantDefinitionHasExpectedEndLine($constant)
    {
        $this->assertEquals(7, $constant->getEndLine());
    }

    /**
     * testConstantDefinitionHasExpectedEndColumn
     *
     * @param PHP_Depend_Code_ASTConstantDefinition $constant
     *
     * @return void
     * @depends testConstantDefinition
     */
    public function testConstantDefinitionHasExpectedEndColumn($constant)
    {
        $this->assertEquals(12, $constant->getEndColumn());
    }

    /**
     * testConstantDefinitionWithDeclarators
     *
     * @return PHP_Depend_Code_ASTConstantDefinition
     * @since 1.0.2
     */
    public function testConstantDefinitionWithDeclarators()
    {
        $constant = $this->_getFirstConstantDefinitionInClass();
        $this->assertInstanceOf(PHP_Depend_Code_ASTConstantDefinition::CLAZZ, $constant);

        return $constant;
    }

    /**
     * testConstantDefinitionWithDeclaratorsHasExpectedStartLine
     *
     * @param PHP_Depend_Code_ASTConstantDefinition $constant
     *
     * @return void
     * @since 1.0.2
     * @depends testConstantDefinitionWithDeclarators
     */
    public function testConstantDefinitionWithDeclaratorsHasExpectedStartLine($constant)
    {
        $this->assertEquals(4, $constant->getStartLine());
    }
    
    /**
     * testConstantDefinitionWithDeclaratorsHasExpectedStartColumn
     *
     * @param PHP_Depend_Code_ASTConstantDefinition $constant
     *
     * @return void
     * @since 1.0.2
     * @depends testConstantDefinitionWithDeclarators
     */
    public function testConstantDefinitionWithDeclaratorsHasExpectedStartColumn($constant)
    {
        $this->assertEquals(5, $constant->getStartColumn());
    }
    
    /**
     * testConstantDefinitionWithDeclaratorsHasExpectedEndLine
     *
     * @param PHP_Depend_Code_ASTConstantDefinition $constant
     *
     * @return void
     * @since 1.0.2
     * @depends testConstantDefinitionWithDeclarators
     */
    public function testConstantDefinitionWithDeclaratorsHasExpectedEndLine($constant)
    {
        $this->assertEquals(6, $constant->getEndLine());
    }
    
    /**
     * testConstantDefinitionWithDeclaratorsHasExpectedEndColumn
     *
     * @param PHP_Depend_Code_ASTConstantDefinition $constant
     *
     * @return void
     * @since 1.0.2
     * @depends testConstantDefinitionWithDeclarators
     */
    public function testConstantDefinitionWithDeclaratorsHasExpectedEndColumn($constant)
    {
        $this->assertEquals(18, $constant->getEndColumn());
    }

    /**
     * testConstantDefinitionInGlobalScope
     *
     * @return void
     * @since 1.0.2
     */
    public function testConstantDefinitionInGlobalScope()
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * testConstantDefinitionInNamespaceScope
     *
     * @return void
     * @since 1.0.2
     */
    public function testConstantDefinitionInNamespaceScope()
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return PHP_Depend_Code_ASTConstantDefinition
     */
    private function _getFirstConstantDefinitionInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(), 
            PHP_Depend_Code_ASTConstantDefinition::CLAZZ
        );
    }
}
