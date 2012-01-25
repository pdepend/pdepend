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
 * @subpackage Bugs
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       https://pdepend.org
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

/**
 * Test case for bug #30.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Bugs
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       https://pdepend.org
 *
 * @ticket 30
 * @covers stdClass
 * @group pdepend
 * @group pdepend::bugs
 * @group regressiontest
 */
class PHP_Depend_Bugs_ParserBug030Test extends PHP_Depend_Bugs_AbstractTest
{
    /**
     * Tests that the parser sets the correct type tokens.
     *
     * http://bugs.xplib.de/index.php?do=details&task_id=30&project=3
     *
     * @return void
     */
    public function testCorrectClassTokensAreSet()
    {
        $testClass = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current();

        $expected = array(
            array(PHP_Depend_TokenizerI::T_ABSTRACT, 2),
            array(PHP_Depend_TokenizerI::T_CLASS, 2),
            array(PHP_Depend_TokenizerI::T_STRING, 2),
            array(PHP_Depend_TokenizerI::T_EXTENDS, 2),
            array(PHP_Depend_TokenizerI::T_STRING, 2),
            array(PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN, 3),
            array(PHP_Depend_TokenizerI::T_DOC_COMMENT, 4),
            array(PHP_Depend_TokenizerI::T_PUBLIC, 11),
            array(PHP_Depend_TokenizerI::T_FUNCTION, 11),
            array(PHP_Depend_TokenizerI::T_STRING, 11),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 11),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 11),
            array(PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN, 12),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 13),
            array(PHP_Depend_TokenizerI::T_EQUAL, 13),
            array(PHP_Depend_TokenizerI::T_STRING, 13),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 13),
            array(PHP_Depend_TokenizerI::T_FILE, 13),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 13),
            array(PHP_Depend_TokenizerI::T_CONCAT, 13),
            array(PHP_Depend_TokenizerI::T_CONSTANT_ENCAPSED_STRING, 13),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 13),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 14),
            array(PHP_Depend_TokenizerI::T_EQUAL, 14),
            array(PHP_Depend_TokenizerI::T_NEW, 14),
            array(PHP_Depend_TokenizerI::T_STRING, 14),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 14),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 14),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 14),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 14),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 15),
            array(PHP_Depend_TokenizerI::T_EQUAL, 15),
            array(PHP_Depend_TokenizerI::T_NEW, 15),
            array(PHP_Depend_TokenizerI::T_STRING, 15),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 15),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 15),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 15),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 16),
            array(PHP_Depend_TokenizerI::T_EQUAL, 16),
            array(PHP_Depend_TokenizerI::T_NEW, 16),
            array(PHP_Depend_TokenizerI::T_STRING, 16),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 16),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 16),
            array(PHP_Depend_TokenizerI::T_COMMA, 16),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 16),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 16),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 16),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 18),
            array(PHP_Depend_TokenizerI::T_OBJECT_OPERATOR, 18),
            array(PHP_Depend_TokenizerI::T_STRING, 18),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 18),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 18),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 18),

            array(PHP_Depend_TokenizerI::T_VARIABLE, 20),
            array(PHP_Depend_TokenizerI::T_EQUAL, 20),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 20),
            array(PHP_Depend_TokenizerI::T_OBJECT_OPERATOR, 20),
            array(PHP_Depend_TokenizerI::T_STRING, 20),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 20),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 20),
            array(PHP_Depend_TokenizerI::T_OBJECT_OPERATOR, 20),
            array(PHP_Depend_TokenizerI::T_STRING, 20),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 20),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 20),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 20),

            array(PHP_Depend_TokenizerI::T_VARIABLE, 21),
            array(PHP_Depend_TokenizerI::T_EQUAL, 21),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 21),
            array(PHP_Depend_TokenizerI::T_OBJECT_OPERATOR, 21),
            array(PHP_Depend_TokenizerI::T_STRING, 21),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 21),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 21),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 21),

            array(PHP_Depend_TokenizerI::T_VARIABLE, 23),
            array(PHP_Depend_TokenizerI::T_OBJECT_OPERATOR, 23),
            array(PHP_Depend_TokenizerI::T_STRING, 23),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 23),
            array(PHP_Depend_TokenizerI::T_LNUMBER, 23),
            array(PHP_Depend_TokenizerI::T_COMMA, 23),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 23),
            array(PHP_Depend_TokenizerI::T_OBJECT_OPERATOR, 23),
            array(PHP_Depend_TokenizerI::T_STRING, 23),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 23),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 23),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 23),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 23),

            array(PHP_Depend_TokenizerI::T_VARIABLE, 24),
            array(PHP_Depend_TokenizerI::T_EQUAL, 24),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 24),
            array(PHP_Depend_TokenizerI::T_OBJECT_OPERATOR, 24),
            array(PHP_Depend_TokenizerI::T_STRING, 24),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 24),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 24),
            array(PHP_Depend_TokenizerI::T_OBJECT_OPERATOR, 24),
            array(PHP_Depend_TokenizerI::T_STRING, 24),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 24),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 24),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 24),

            array(PHP_Depend_TokenizerI::T_VARIABLE, 25),
            array(PHP_Depend_TokenizerI::T_OBJECT_OPERATOR, 25),
            array(PHP_Depend_TokenizerI::T_STRING, 25),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 25),
            array(PHP_Depend_TokenizerI::T_LNUMBER, 25),
            array(PHP_Depend_TokenizerI::T_COMMA, 25),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 25),
            array(PHP_Depend_TokenizerI::T_OBJECT_OPERATOR, 25),
            array(PHP_Depend_TokenizerI::T_STRING, 25),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 25),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 25),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 25),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 25),

            array(PHP_Depend_TokenizerI::T_CURLY_BRACE_CLOSE, 26),

            array(PHP_Depend_TokenizerI::T_DOC_COMMENT, 28),
            array(PHP_Depend_TokenizerI::T_PROTECTED, 33),
            array(PHP_Depend_TokenizerI::T_ABSTRACT, 33),
            array(PHP_Depend_TokenizerI::T_FUNCTION, 33),
            array(PHP_Depend_TokenizerI::T_STRING, 33),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 33),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 33),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 33),

            array(PHP_Depend_TokenizerI::T_CURLY_BRACE_CLOSE, 34),
        );

        $actual = array();
        foreach ($testClass->getTokens() as $token) {
            $actual[] = array($token->type, $token->startLine);
        }
        $this->assertSame($expected, $actual);
    }
}
