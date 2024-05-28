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

namespace PDepend\Source\Language\PHP;

use PDepend\AbstractTestCase;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\Tokenizer\Tokenizer;
use PDepend\Source\Tokenizer\Tokens;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\PHPTokenizerInternal} class.
 *
 * @covers \PDepend\Source\Language\PHP\PHPTokenizerInternal
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class PHPTokenizerInternalTest extends AbstractTestCase
{
    /**
     * testTokenizerReturnsExpectedConstantForTraitKeyword
     *
     * @since 1.0.0
     */
    public function testTokenizerReturnsExpectedConstantForTraitKeyword(): void
    {
        static::assertEquals(
            [
                Tokens::T_OPEN_TAG,
                Tokens::T_TRAIT,
                Tokens::T_STRING,
                Tokens::T_CURLY_BRACE_OPEN,
                Tokens::T_PUBLIC,
                Tokens::T_FUNCTION,
                Tokens::T_STRING,
                Tokens::T_PARENTHESIS_OPEN,
                Tokens::T_PARENTHESIS_CLOSE,
                Tokens::T_CURLY_BRACE_OPEN,
                Tokens::T_RETURN,
                Tokens::T_LNUMBER,
                Tokens::T_SEMICOLON,
                Tokens::T_CURLY_BRACE_CLOSE,
                Tokens::T_CURLY_BRACE_CLOSE,
            ],
            $this->getTokenTypesForTest()
        );
    }

    /**
     * testTokenizerReturnsExpectedConstantForTraitMagicConstant
     *
     * @since 1.0.0
     */
    public function testTokenizerReturnsExpectedConstantForTraitMagicConstant(): void
    {
        static::assertEquals(
            [
                Tokens::T_OPEN_TAG,
                Tokens::T_TRAIT,
                Tokens::T_STRING,
                Tokens::T_CURLY_BRACE_OPEN,
                Tokens::T_PUBLIC,
                Tokens::T_FUNCTION,
                Tokens::T_STRING,
                Tokens::T_PARENTHESIS_OPEN,
                Tokens::T_PARENTHESIS_CLOSE,
                Tokens::T_CURLY_BRACE_OPEN,
                Tokens::T_RETURN,
                Tokens::T_TRAIT_C,
                Tokens::T_SEMICOLON,
                Tokens::T_CURLY_BRACE_CLOSE,
                Tokens::T_CURLY_BRACE_CLOSE,
            ],
            $this->getTokenTypesForTest()
        );
    }

    /**
     * Tests the tokenizer with a source file that contains only classes.
     */
    public function testInternalWithClasses(): void
    {
        $expected = [
            Tokens::T_OPEN_TAG,
            Tokens::T_DOC_COMMENT,
            Tokens::T_ABSTRACT,
            Tokens::T_CLASS,
            Tokens::T_STRING,
            Tokens::T_CURLY_BRACE_OPEN,
            Tokens::T_CURLY_BRACE_CLOSE,
            Tokens::T_CLASS,
            Tokens::T_STRING,
            Tokens::T_EXTENDS,
            Tokens::T_STRING,
            Tokens::T_CURLY_BRACE_OPEN,
            Tokens::T_PUBLIC,
            Tokens::T_FUNCTION,
            Tokens::T_STRING,
            Tokens::T_PARENTHESIS_OPEN,
            Tokens::T_PARENTHESIS_CLOSE,
            Tokens::T_CURLY_BRACE_OPEN,
            Tokens::T_STRING,
            Tokens::T_DOUBLE_COLON,
            Tokens::T_STRING,
            Tokens::T_PARENTHESIS_OPEN,
            Tokens::T_PARENTHESIS_CLOSE,
            Tokens::T_SEMICOLON,
            Tokens::T_VARIABLE,
            Tokens::T_EQUAL,
            Tokens::T_CONSTANT_ENCAPSED_STRING,
            Tokens::T_SEMICOLON,
            Tokens::T_VARIABLE,
            Tokens::T_EQUAL,
            Tokens::T_TRUE,
            Tokens::T_SEMICOLON,
            Tokens::T_CURLY_BRACE_CLOSE,
            Tokens::T_CURLY_BRACE_CLOSE,
        ];

        static::assertEquals($expected, $this->getTokenTypesForTest());
    }

    /**
     * Tests the tokenizer with a source file that contains mixed content of
     * classes and functions.
     */
    public function testInternalWithMixedContent(): void
    {
        $tokenizer = new PHPTokenizerInternal();
        $tokenizer->setSourceFile($this->createCodeResourceUriForTest());

        $expected = [
            [Tokens::T_OPEN_TAG, 1],
            [Tokens::T_COMMENT, 2],
            [Tokens::T_FUNCTION, 5],
            [Tokens::T_STRING, 5],
            [Tokens::T_PARENTHESIS_OPEN, 5],
            [Tokens::T_VARIABLE, 5],
            [Tokens::T_COMMA, 5],
            [Tokens::T_VARIABLE, 5],
            [Tokens::T_PARENTHESIS_CLOSE, 5],
            [Tokens::T_CURLY_BRACE_OPEN, 5],
            [Tokens::T_NEW, 6],
            [Tokens::T_STRING, 6],
            [Tokens::T_PARENTHESIS_OPEN, 6],
            [Tokens::T_VARIABLE, 6],
            [Tokens::T_COMMA, 6],
            [Tokens::T_VARIABLE, 6],
            [Tokens::T_PARENTHESIS_CLOSE, 6],
            [Tokens::T_SEMICOLON, 6],
            [Tokens::T_CURLY_BRACE_CLOSE, 7],
            [Tokens::T_DOC_COMMENT, 10],
            [Tokens::T_CLASS, 13],
            [Tokens::T_STRING, 13],
            [Tokens::T_CURLY_BRACE_OPEN, 13],
            [Tokens::T_COMMENT, 14],
            [Tokens::T_CURLY_BRACE_CLOSE, 15],
            [Tokens::T_CLOSE_TAG, 16],
        ];

        $actual = [];
        while (is_object($token = $tokenizer->next())) {
            $actual[] = [$token->type, $token->startLine];
        }

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the tokenizer returns <b>T_BOF</b> if there is no previous
     * token.
     */
    public function testInternalReturnsBOFTokenForPrevCall(): void
    {
        $tokenizer = new PHPTokenizerInternal();
        $tokenizer->setSourceFile($this->createCodeResourceUriForTest());

        static::assertEquals(Tokenizer::T_BOF, $tokenizer->prev());
    }

    /**
     * Tests the tokenizer with a combination of procedural code and functions.
     */
    public function testInternalWithProceduralCodeAndFunction(): void
    {
        $expected = [
            Tokens::T_OPEN_TAG,
            Tokens::T_FUNCTION,
            Tokens::T_STRING,
            Tokens::T_PARENTHESIS_OPEN,
            Tokens::T_VARIABLE,
            Tokens::T_PARENTHESIS_CLOSE,
            Tokens::T_CURLY_BRACE_OPEN,
            Tokens::T_VARIABLE,
            Tokens::T_EQUAL,
            Tokens::T_NEW,
            Tokens::T_VARIABLE,
            Tokens::T_PARENTHESIS_OPEN,
            Tokens::T_PARENTHESIS_CLOSE,
            Tokens::T_SEMICOLON,
            Tokens::T_CURLY_BRACE_CLOSE,
            Tokens::T_VARIABLE,
            Tokens::T_EQUAL,
            Tokens::T_STRING,
            Tokens::T_PARENTHESIS_OPEN,
            Tokens::T_CONSTANT_ENCAPSED_STRING,
            Tokens::T_PARENTHESIS_CLOSE,
            Tokens::T_SEMICOLON,
            Tokens::T_STRING,
            Tokens::T_PARENTHESIS_OPEN,
            Tokens::T_ARRAY,
            Tokens::T_PARENTHESIS_OPEN,
            Tokens::T_VARIABLE,
            Tokens::T_COMMA,
            Tokens::T_CONSTANT_ENCAPSED_STRING,
            Tokens::T_PARENTHESIS_CLOSE,
            Tokens::T_PARENTHESIS_CLOSE,
            Tokens::T_SEMICOLON,
            Tokens::T_CLOSE_TAG,
        ];

        static::assertEquals($expected, $this->getTokenTypesForTest());
    }

    /**
     * Test case for undetected static method call added.
     */
    public function testInternalStaticCallBug01(): void
    {
        $expected = [
            Tokens::T_OPEN_TAG,
            Tokens::T_DOC_COMMENT,
            Tokens::T_CLASS,
            Tokens::T_STRING,
            Tokens::T_CURLY_BRACE_OPEN,
            Tokens::T_PUBLIC,
            Tokens::T_FUNCTION,
            Tokens::T_STRING,
            Tokens::T_PARENTHESIS_OPEN,
            Tokens::T_PARENTHESIS_CLOSE,
            Tokens::T_CURLY_BRACE_OPEN,
            Tokens::T_STRING,
            Tokens::T_DOUBLE_COLON,
            Tokens::T_STRING,
            Tokens::T_PARENTHESIS_OPEN,
            Tokens::T_PARENTHESIS_CLOSE,
            Tokens::T_SEMICOLON,
            Tokens::T_CURLY_BRACE_CLOSE,
            Tokens::T_CURLY_BRACE_CLOSE,
        ];

        static::assertEquals($expected, $this->getTokenTypesForTest());
    }

    /**
     * Tests that the tokenizer handles the following syntax correct.
     *
     * <code>
     * class Foo {
     *     public function formatBug09($x) {
     *         self::${$x};
     *     }
     * }
     * </code>
     *
     * http://bugs.xplib.de/index.php?do=details&task_id=9&project=3
     */
    public function testInternalDollarSyntaxBug09(): void
    {
        $expected = [
            Tokens::T_OPEN_TAG,
            Tokens::T_DOC_COMMENT,
            Tokens::T_CLASS,
            Tokens::T_STRING,
            Tokens::T_CURLY_BRACE_OPEN,
            Tokens::T_PUBLIC,
            Tokens::T_FUNCTION,
            Tokens::T_STRING,
            Tokens::T_PARENTHESIS_OPEN,
            Tokens::T_VARIABLE,
            Tokens::T_PARENTHESIS_CLOSE,
            Tokens::T_CURLY_BRACE_OPEN,
            Tokens::T_SELF, // SELF
            Tokens::T_DOUBLE_COLON,
            Tokens::T_DOLLAR,
            Tokens::T_CURLY_BRACE_OPEN,
            Tokens::T_VARIABLE,
            Tokens::T_CURLY_BRACE_CLOSE,
            Tokens::T_SEMICOLON,
            Tokens::T_CURLY_BRACE_CLOSE,
            Tokens::T_CURLY_BRACE_CLOSE,
        ];

        static::assertEquals($expected, $this->getTokenTypesForTest());
    }

    /**
     * Test case for the inline html bug.
     */
    public function testTokenizerWithInlineHtmlBug24(): void
    {
        if (! ini_get('short_open_tag')) {
            static::markTestSkipped('Must enable short_open_tag');
        }

        $tokenizer = new PHPTokenizerInternal();
        $tokenizer->setSourceFile($this->createCodeResourceUriForTest());

        $expected = [
            [Tokens::T_OPEN_TAG, 1],
            [Tokens::T_CLASS, 2],
            [Tokens::T_STRING, 2],
            [Tokens::T_CURLY_BRACE_OPEN, 3],
            [Tokens::T_FUNCTION, 4],
            [Tokens::T_STRING, 4],
            [Tokens::T_PARENTHESIS_OPEN, 4],
            [Tokens::T_PARENTHESIS_CLOSE, 4],
            [Tokens::T_CURLY_BRACE_OPEN, 5],
            [Tokens::T_CLOSE_TAG, 6],
            [Tokens::T_NO_PHP, 7],
            [Tokens::T_OPEN_TAG_WITH_ECHO, 7],
            [Tokens::T_STRING, 7],
            [Tokens::T_SEMICOLON, 7],
            [Tokens::T_CLOSE_TAG, 7],
            [Tokens::T_NO_PHP, 7],
            [Tokens::T_OPEN_TAG, 8],
            [Tokens::T_ECHO, 8],
            [Tokens::T_STRING, 8],
            [Tokens::T_PARENTHESIS_OPEN, 8],
            [Tokens::T_VARIABLE, 8],
            [Tokens::T_PARENTHESIS_CLOSE, 8],
            [Tokens::T_SEMICOLON, 8],
            [Tokens::T_CLOSE_TAG, 8],
            [Tokens::T_NO_PHP, 8],
            [Tokens::T_OPEN_TAG, 10],
            [Tokens::T_CURLY_BRACE_CLOSE, 11],
            [Tokens::T_FUNCTION, 13],
            [Tokens::T_STRING, 13],
            [Tokens::T_PARENTHESIS_OPEN, 13],
            [Tokens::T_PARENTHESIS_CLOSE, 13],
            [Tokens::T_CURLY_BRACE_OPEN, 14],
            [Tokens::T_CURLY_BRACE_CLOSE, 16],
            [Tokens::T_CURLY_BRACE_CLOSE, 17],
        ];

        $actual = [];
        while (is_object($token = $tokenizer->next())) {
            $actual[] = [$token->type, $token->startLine];
        }

        static::assertSame($expected, $actual);
    }

    /**
     * Tests the tokenizer's column calculation implementation.
     */
    public function testTokenizerCalculatesCorrectColumnForInlinePhpIssue88(): void
    {
        if (! ini_get('short_open_tag')) {
            static::markTestSkipped('Must enable short_open_tag');
        }

        $tokenizer = new PHPTokenizerInternal();
        $tokenizer->setSourceFile($this->createCodeResourceUriForTest());

        $expected = [
            [Tokens::T_NO_PHP, "<html>\n    <head>\n        <title>", 1, 3, 1, 15],
            [Tokens::T_OPEN_TAG_WITH_ECHO, '<?=', 3, 3, 16, 18],
            [Tokens::T_VARIABLE, '$foo', 3, 3, 19, 22],
            [Tokens::T_SEMICOLON, ';', 3, 3, 23, 23],
            [Tokens::T_CLOSE_TAG, '?>', 3, 3, 24, 25],
            [Tokens::T_NO_PHP, "</title>\n    </head>\n    <body>", 3, 5, 26, 10],
            [Tokens::T_OPEN_TAG, '<?', 6, 6, 9, 10],
            [Tokens::T_ECHO, 'echo', 6, 6, 12, 15],
            [Tokens::T_VARIABLE, '$bar', 6, 6, 17, 20],
            [Tokens::T_SEMICOLON, ';', 6, 6, 21, 21],
            [Tokens::T_CLOSE_TAG, '?>', 6, 6, 23, 24],
            [Tokens::T_NO_PHP, "    </body>\n</html>", 7, 8, 1, 7],
        ];

        $actual = [];
        while (is_object($token = $tokenizer->next())) {
            $actual[] = [
                $token->type,
                $token->image,
                $token->startLine,
                $token->endLine,
                $token->startColumn,
                $token->endColumn,
            ];
        }

        static::assertSame($expected, $actual);
    }

    /**
     * Tests the tokenizer support short-echo-tags with multiple variables separated by comas.
     */
    public function testTokenizingShortTagsWithMultipleVariables(): void
    {
        $tokenizer = new PHPTokenizerInternal();
        $tokenizer->setSourceFile($this->createCodeResourceUriForTest());

        $expected = [
            [Tokens::T_OPEN_TAG_WITH_ECHO, '<?=', 1, 1, 1, 3],
            [Tokens::T_VARIABLE, '$foo', 1, 1, 4, 7],
            [Tokens::T_COMMA, ',', 1, 1, 8, 8],
            [Tokens::T_VARIABLE, '$bar', 1, 1, 9, 12],
            [Tokens::T_CLOSE_TAG, '?>', 1, 1, 13, 14],
        ];

        $actual = [];
        while (is_object($token = $tokenizer->next())) {
            $actual[] = [
                $token->type,
                $token->image,
                $token->startLine,
                $token->endLine,
                $token->startColumn,
                $token->endColumn,
            ];
        }

        static::assertSame($expected, $actual);

        $list = $this->parseCodeResourceForTest();

        static::assertInstanceOf(ASTArtifactList::class, $list);
    }

    /**
     * Tests the tokenizer's column calculation implementation.
     */
    public function testTokenizerCalculatesCorrectColumnForInlinePhpInTextIssue88(): void
    {
        $tokenizer = new PHPTokenizerInternal();
        $tokenizer->setSourceFile($this->createCodeResourceUriForTest());

        $expected = [
            [Tokens::T_NO_PHP, 'Hello', 1, 1, 1, 5],
            [Tokens::T_OPEN_TAG, '<?php', 1, 1, 7, 11],
            [Tokens::T_ECHO, 'echo', 1, 1, 13, 16],
            [Tokens::T_VARIABLE, '$user', 1, 1, 18, 22],
            [Tokens::T_SEMICOLON, ';', 1, 1, 23, 23],
            [Tokens::T_CLOSE_TAG, '?>', 1, 1, 25, 26],
            [Tokens::T_NO_PHP, "\nthis is a simple letter to users of", 2, 3, 1, 35],
            [Tokens::T_OPEN_TAG, '<?php', 3, 3, 37, 41],
            [Tokens::T_PRINT, 'print', 3, 3, 43, 47],
            [Tokens::T_VARIABLE, '$service', 3, 3, 49, 56],
            [Tokens::T_SEMICOLON, ';', 3, 3, 57, 57],
            [Tokens::T_CLOSE_TAG, '?>', 3, 3, 59, 60],
            [Tokens::T_NO_PHP, ".\n\nManuel", 3, 5, 61, 6],
        ];

        $actual = [];
        while (is_object($token = $tokenizer->next())) {
            $actual[] = [
                $token->type,
                $token->image,
                $token->startLine,
                $token->endLine,
                $token->startColumn,
                $token->endColumn,
            ];
        }

        static::assertSame($expected, $actual);
    }

    /**
     * testTokenizerSubstitutesDollarCurlyOpenWithTwoSeparateTokens
     */
    public function testTokenizerSubstitutesDollarCurlyOpenWithTwoSeparateTokens(): void
    {
        $tokenizer = new PHPTokenizerInternal();
        $tokenizer->setSourceFile($this->createCodeResourceUriForTest());

        $actual = [];
        while (is_object($token = $tokenizer->next())) {
            $actual[] = [$token->type, $token->startColumn, $token->endColumn];
        }

        $expected = [
            [Tokens::T_OPEN_TAG, 1, 5],
            [Tokens::T_DOLLAR, 1, 1],
            [Tokens::T_CURLY_BRACE_OPEN, 2, 2],
            [Tokens::T_VARIABLE, 3, 6],
            [Tokens::T_CONCAT, 8, 8],
            [Tokens::T_VARIABLE, 10, 13],
            [Tokens::T_CURLY_BRACE_CLOSE, 14, 14],
            [Tokens::T_SEMICOLON, 15, 15],
        ];

        static::assertEquals($expected, $actual);
    }

    /**
     * testReturnsExpectedTokensForStringWithEmbeddedBacktickExpression
     */
    public function testReturnsExpectedTokensForStringWithEmbeddedBacktickExpression(): void
    {
        $tokenizer = new PHPTokenizerInternal();
        $tokenizer->setSourceFile($this->createCodeResourceUriForTest());

        $actual = [];
        while (is_object($token = $tokenizer->next())) {
            $actual[] = [$token->type];
        }

        $expected = [
            [Tokens::T_OPEN_TAG],
            [Tokens::T_DOUBLE_QUOTE],
            [Tokens::T_ENCAPSED_AND_WHITESPACE],
            [Tokens::T_VARIABLE],
            [Tokens::T_BACKTICK],
            [Tokens::T_DOUBLE_QUOTE],
            [Tokens::T_SEMICOLON],
        ];

        static::assertEquals($expected, $actual);
    }

    /**
     * testReturnsExpectedTokensForBacktickExpressionWithEmbeddedString
     */
    public function testReturnsExpectedTokensForBacktickExpressionWithEmbeddedString(): void
    {
        $expected = [
            Tokens::T_OPEN_TAG,
            Tokens::T_BACKTICK,
            Tokens::T_ENCAPSED_AND_WHITESPACE,
            Tokens::T_VARIABLE,
            Tokens::T_DOUBLE_QUOTE,
            Tokens::T_BACKTICK,
            Tokens::T_SEMICOLON,
        ];

        static::assertEquals($expected, $this->getTokenTypesForTest());
    }

    /**
     * Returns an array with the token types found in a file associated with
     * the currently running test.
     *
     * @return array<int>
     */
    private function getTokenTypesForTest(): array
    {
        $tokenizer = new PHPTokenizerInternal();
        $tokenizer->setSourceFile($this->createCodeResourceUriForTest());

        $types = [];
        while (is_object($token = $tokenizer->next())) {
            $types[] = $token->type;
        }

        return $types;
    }
}
