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
 */

namespace PDepend\Bugs;

use PDepend\Source\Tokenizer\Tokens;

/**
 * Test case for bug #30.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @ticket 30
 *
 * @group regressiontest
 */
class ParserBug030Test extends AbstractRegressionTestCase
{
    /**
     * Tests that the parser sets the correct type tokens.
     *
     * http://bugs.xplib.de/index.php?do=details&task_id=30&project=3
     */
    public function testCorrectClassTokensAreSet(): void
    {
        $testClass = $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current();

        $expected = [
            [Tokens::T_ABSTRACT, 2],
            [Tokens::T_CLASS, 2],
            [Tokens::T_STRING, 2],
            [Tokens::T_EXTENDS, 2],
            [Tokens::T_STRING, 2],
            [Tokens::T_CURLY_BRACE_OPEN, 3],
            [Tokens::T_DOC_COMMENT, 4],
            [Tokens::T_PUBLIC, 11],
            [Tokens::T_FUNCTION, 11],
            [Tokens::T_STRING, 11],
            [Tokens::T_PARENTHESIS_OPEN, 11],
            [Tokens::T_PARENTHESIS_CLOSE, 11],
            [Tokens::T_CURLY_BRACE_OPEN, 12],
            [Tokens::T_VARIABLE, 13],
            [Tokens::T_EQUAL, 13],
            [Tokens::T_STRING, 13],
            [Tokens::T_PARENTHESIS_OPEN, 13],
            [Tokens::T_FILE, 13],
            [Tokens::T_PARENTHESIS_CLOSE, 13],
            [Tokens::T_CONCAT, 13],
            [Tokens::T_CONSTANT_ENCAPSED_STRING, 13],
            [Tokens::T_SEMICOLON, 13],
            [Tokens::T_VARIABLE, 14],
            [Tokens::T_EQUAL, 14],
            [Tokens::T_NEW, 14],
            [Tokens::T_STRING, 14],
            [Tokens::T_PARENTHESIS_OPEN, 14],
            [Tokens::T_VARIABLE, 14],
            [Tokens::T_PARENTHESIS_CLOSE, 14],
            [Tokens::T_SEMICOLON, 14],
            [Tokens::T_VARIABLE, 15],
            [Tokens::T_EQUAL, 15],
            [Tokens::T_NEW, 15],
            [Tokens::T_STRING, 15],
            [Tokens::T_PARENTHESIS_OPEN, 15],
            [Tokens::T_PARENTHESIS_CLOSE, 15],
            [Tokens::T_SEMICOLON, 15],
            [Tokens::T_VARIABLE, 16],
            [Tokens::T_EQUAL, 16],
            [Tokens::T_NEW, 16],
            [Tokens::T_STRING, 16],
            [Tokens::T_PARENTHESIS_OPEN, 16],
            [Tokens::T_VARIABLE, 16],
            [Tokens::T_COMMA, 16],
            [Tokens::T_VARIABLE, 16],
            [Tokens::T_PARENTHESIS_CLOSE, 16],
            [Tokens::T_SEMICOLON, 16],
            [Tokens::T_VARIABLE, 18],
            [Tokens::T_OBJECT_OPERATOR, 18],
            [Tokens::T_STRING, 18],
            [Tokens::T_PARENTHESIS_OPEN, 18],
            [Tokens::T_PARENTHESIS_CLOSE, 18],
            [Tokens::T_SEMICOLON, 18],

            [Tokens::T_VARIABLE, 20],
            [Tokens::T_EQUAL, 20],
            [Tokens::T_VARIABLE, 20],
            [Tokens::T_OBJECT_OPERATOR, 20],
            [Tokens::T_STRING, 20],
            [Tokens::T_PARENTHESIS_OPEN, 20],
            [Tokens::T_PARENTHESIS_CLOSE, 20],
            [Tokens::T_OBJECT_OPERATOR, 20],
            [Tokens::T_STRING, 20],
            [Tokens::T_PARENTHESIS_OPEN, 20],
            [Tokens::T_PARENTHESIS_CLOSE, 20],
            [Tokens::T_SEMICOLON, 20],

            [Tokens::T_VARIABLE, 21],
            [Tokens::T_EQUAL, 21],
            [Tokens::T_VARIABLE, 21],
            [Tokens::T_OBJECT_OPERATOR, 21],
            [Tokens::T_STRING, 21],
            [Tokens::T_PARENTHESIS_OPEN, 21],
            [Tokens::T_PARENTHESIS_CLOSE, 21],
            [Tokens::T_SEMICOLON, 21],

            [Tokens::T_VARIABLE, 23],
            [Tokens::T_OBJECT_OPERATOR, 23],
            [Tokens::T_STRING, 23],
            [Tokens::T_PARENTHESIS_OPEN, 23],
            [Tokens::T_LNUMBER, 23],
            [Tokens::T_COMMA, 23],
            [Tokens::T_VARIABLE, 23],
            [Tokens::T_OBJECT_OPERATOR, 23],
            [Tokens::T_STRING, 23],
            [Tokens::T_PARENTHESIS_OPEN, 23],
            [Tokens::T_PARENTHESIS_CLOSE, 23],
            [Tokens::T_PARENTHESIS_CLOSE, 23],
            [Tokens::T_SEMICOLON, 23],

            [Tokens::T_VARIABLE, 24],
            [Tokens::T_EQUAL, 24],
            [Tokens::T_VARIABLE, 24],
            [Tokens::T_OBJECT_OPERATOR, 24],
            [Tokens::T_STRING, 24],
            [Tokens::T_PARENTHESIS_OPEN, 24],
            [Tokens::T_PARENTHESIS_CLOSE, 24],
            [Tokens::T_OBJECT_OPERATOR, 24],
            [Tokens::T_STRING, 24],
            [Tokens::T_PARENTHESIS_OPEN, 24],
            [Tokens::T_PARENTHESIS_CLOSE, 24],
            [Tokens::T_SEMICOLON, 24],

            [Tokens::T_VARIABLE, 25],
            [Tokens::T_OBJECT_OPERATOR, 25],
            [Tokens::T_STRING, 25],
            [Tokens::T_PARENTHESIS_OPEN, 25],
            [Tokens::T_LNUMBER, 25],
            [Tokens::T_COMMA, 25],
            [Tokens::T_VARIABLE, 25],
            [Tokens::T_OBJECT_OPERATOR, 25],
            [Tokens::T_STRING, 25],
            [Tokens::T_PARENTHESIS_OPEN, 25],
            [Tokens::T_PARENTHESIS_CLOSE, 25],
            [Tokens::T_PARENTHESIS_CLOSE, 25],
            [Tokens::T_SEMICOLON, 25],

            [Tokens::T_CURLY_BRACE_CLOSE, 26],

            [Tokens::T_DOC_COMMENT, 28],
            [Tokens::T_PROTECTED, 33],
            [Tokens::T_ABSTRACT, 33],
            [Tokens::T_FUNCTION, 33],
            [Tokens::T_STRING, 33],
            [Tokens::T_PARENTHESIS_OPEN, 33],
            [Tokens::T_PARENTHESIS_CLOSE, 33],
            [Tokens::T_SEMICOLON, 33],

            [Tokens::T_CURLY_BRACE_CLOSE, 34],
        ];

        $actual = [];
        foreach ($testClass->getTokens() as $token) {
            $actual[] = [$token->type, $token->startLine];
        }
        static::assertSame($expected, $actual);
    }
}
