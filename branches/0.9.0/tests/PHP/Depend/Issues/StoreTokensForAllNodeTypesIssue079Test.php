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
 * @subpackage Issues
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for issue #79 where we should store the tokens for each created
 * ast node.
 *
 * http://tracker.pdepend.org/pdepend/issue_tracker/issue/79
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Issues
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Issues_StoreTokensForAllNodeTypesIssue079Test extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the parser stores the expected property tokens.
     *
     * @return void
     */
    public function testParserStoresPropertyTokensWithoutDefaultValue()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');
        $property = $packages->current()
            ->getClasses()
            ->current()
            ->getProperties()
            ->current();

        $expected = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PRIVATE, 'private', 3, 3, 5, 11),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STATIC, 'static', 3, 3, 13, 18),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$bar', 3, 3, 20, 23),
        );

        $this->assertEquals($expected, $property->getTokens());
    }

    /**
     * Tests that the parser stores the expected property tokens.
     *
     * @return void
     */
    public function testParserStoresPropertyTokensWithDefaultValueArray()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');
        $property = $packages->current()
            ->getClasses()
            ->current()
            ->getProperties()
            ->current();

        $expected = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PUBLIC, 'public', 3, 3, 5, 10),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$bar', 3, 3, 12, 15),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_EQUAL, '=', 3, 3, 17, 17),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_ARRAY, 'array', 3, 3, 19, 23),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 3, 3, 24, 24),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LNUMBER, '23', 4, 4, 9, 10),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COMMA, ',', 4, 4, 11, 11),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LNUMBER, '42', 4, 4, 13, 14),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 5, 5, 5, 5),
        );

        $this->assertEquals($expected, $property->getTokens());
    }

    /**
     * Tests that the parser stores the expected property tokens.
     *
     * @return void
     */
    public function testParserStoresPropertyTokensWithInlineCommentsAndDefaultValue()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');
        $property = $packages->current()
            ->getClasses()
            ->current()
            ->getProperties()
            ->current();

        $expected = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PROTECTED, 'protected', 3, 3, 5, 13),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COMMENT, '/*foo*/', 3, 3, 15, 21),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STATIC, 'static', 4, 4, 5, 10),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COMMENT, '// bar', 5, 5, 5, 10),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$value', 6, 6, 5, 10),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COMMENT, '#test', 7, 7, 5, 9),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_EQUAL, '=', 9, 9, 5, 5),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LNUMBER, '42', 9, 9, 7, 8),
        );

        $this->assertEquals($expected, $property->getTokens());
    }
}
?>
