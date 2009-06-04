<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

require_once 'PHP/Depend/Code/ASTConstantDefinition.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTConstantDefinition} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Code_ASTConstantDefinitionTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * Tests that the parser stores the expected constant tokens.
     *
     * @return void
     */
    public function testParserStoresConstantDefinitionTokensWithSignedDefaultValue()
    {
        $packages = self::parseSource('code/ASTConstantDefinition/' . __FUNCTION__ . '.php');
        $constant = $packages->current()
            ->getClasses()
            ->current()
            ->getFirstChildOfType('PHP_Depend_Code_ASTConstantDefinition');

        $expected = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CONST, 'const', 3, 3, 5, 9),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'FOO', 3, 3, 11, 13),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_EQUAL, '=', 3, 3, 15, 15),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_MINUS, '-', 3, 3, 17, 17),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PLUS, '+', 3, 3, 18, 18),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LNUMBER, '42', 3, 3, 19, 20),
        );

        $this->assertEquals($expected, $constant->getTokens());
    }

    /**
     * Tests that the parser stores the expected constant tokens.
     *
     * @return void
     */
    public function testParserStoresConstantDefinitionTokensWithInlineComments()
    {
        $packages = self::parseSource('code/ASTConstantDefinition/' . __FUNCTION__ . '.php');
        $constant = $packages->current()
            ->getClasses()
            ->current()
            ->getFirstChildOfType('PHP_Depend_Code_ASTConstantDefinition');

        $expected = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CONST, 'const', 3, 3, 5, 9),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COMMENT, '/*const*/', 3, 3, 10, 18),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'FOO', 4, 4, 5, 7),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_EQUAL, '=', 5, 5, 5, 5),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COMMENT, '//', 6, 6, 5, 6),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 7, 7, 5, 8),
        );

        $this->assertEquals($expected, $constant->getTokens());
    }

    /**
     * Tests that the constant contains the start line of the first token.
     *
     * @return void
     */
    public function testConstantDefinitionContainsStartLineOfFirstToken()
    {
        $packages = self::parseSource('code/ASTConstantDefinition/' . __FUNCTION__ . '.php');
        $constant = $packages->current()
            ->getClasses()
            ->current()
            ->getFirstChildOfType('PHP_Depend_Code_ASTConstantDefinition');

        $this->assertSame(3, $constant->getStartLine());
    }

    /**
     * Tests that the constant contains the end line of the last token.
     *
     * @return void
     */
    public function testConstantDefinitionContainsEndLineOfLastToken()
    {
        $packages = self::parseSource('code/ASTConstantDefinition/' . __FUNCTION__ . '.php');
        $constant = $packages->current()
            ->getClasses()
            ->current()
            ->getFirstChildOfType('PHP_Depend_Code_ASTConstantDefinition');

        $this->assertSame(7, $constant->getEndLine());
    }

    /**
     * Creates a concrete node implementation.
     *
     * @return PHP_Depend_Code_ASTConstantDefinition
     */
    protected function createNodeInstance()
    {
        return new PHP_Depend_Code_ASTConstantDefinition('const');
    }
}
?>
