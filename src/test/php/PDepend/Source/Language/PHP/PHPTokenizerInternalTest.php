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

namespace PDepend\Source\Language\PHP;

use PDepend\AbstractTest;
use PDepend\Source\Tokenizer\Tokenizer;
use PDepend\Source\Tokenizer\Tokens;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\PHPTokenizerInternal} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\PHPTokenizerInternal
 * @group unittest
 */
class PHPTokenizerInternalTest extends AbstractTest
{
    /**
     * testTokenizerReturnsExpectedConstantForTraitKeyword
     * 
     * @return void
     * @since 1.0.0
     */
    public function testTokenizerReturnsExpectedConstantForTraitKeyword()
    {
        $this->assertEquals(
            array(
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
            ),
            $this->_getTokenTypesForTest()
        );
    }

    /**
     * testTokenizerReturnsExpectedConstantForTraitMagicConstant
     *
     * @return void
     * @since 1.0.0
     */
    public function testTokenizerReturnsExpectedConstantForTraitMagicConstant()
    {
        $this->assertEquals(
            array(
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
            ),
            $this->_getTokenTypesForTest()
        );
    }

    /**
     * Tests the tokenizer with a source file that contains only classes.
     *
     * @return void
     */
    public function testInternalWithClasses()
    {
        $expected = array(
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
            Tokens::T_CURLY_BRACE_CLOSE
        );

        $this->assertEquals($expected, $this->_getTokenTypesForTest());
    }

    /**
     * Tests the tokenizer with a source file that contains mixed content of
     * classes and functions.
     *
     * @return void
     */
    public function testInternalWithMixedContent()
    {
        $tokenizer  = new PHPTokenizerInternal();
        $tokenizer->setSourceFile(self::createCodeResourceUriForTest());

        $expected = array(
            array(Tokens::T_OPEN_TAG, 1),
            array(Tokens::T_COMMENT, 2),
            array(Tokens::T_FUNCTION, 5),
            array(Tokens::T_STRING, 5),
            array(Tokens::T_PARENTHESIS_OPEN, 5),
            array(Tokens::T_VARIABLE, 5),
            array(Tokens::T_COMMA, 5),
            array(Tokens::T_VARIABLE, 5),
            array(Tokens::T_PARENTHESIS_CLOSE, 5),
            array(Tokens::T_CURLY_BRACE_OPEN, 5),
            array(Tokens::T_NEW, 6),
            array(Tokens::T_STRING, 6),
            array(Tokens::T_PARENTHESIS_OPEN, 6),
            array(Tokens::T_VARIABLE, 6),
            array(Tokens::T_COMMA, 6),
            array(Tokens::T_VARIABLE, 6),
            array(Tokens::T_PARENTHESIS_CLOSE, 6),
            array(Tokens::T_SEMICOLON, 6),
            array(Tokens::T_CURLY_BRACE_CLOSE, 7),
            array(Tokens::T_DOC_COMMENT, 10),
            array(Tokens::T_CLASS, 13),
            array(Tokens::T_STRING, 13),
            array(Tokens::T_CURLY_BRACE_OPEN, 13),
            array(Tokens::T_COMMENT, 14),
            array(Tokens::T_CURLY_BRACE_CLOSE, 15),
            array(Tokens::T_CLOSE_TAG, 16)
        );

        $actual = array();
        while (is_object($token = $tokenizer->next())) {
            $actual[] = array($token->type, $token->startLine);
        }
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the tokenizer returns <b>T_BOF</b> if there is no previous
     * token.
     *
     * @return void
     */
    public function testInternalReturnsBOFTokenForPrevCall()
    {
        $tokenizer  = new PHPTokenizerInternal();
        $tokenizer->setSourceFile(self::createCodeResourceUriForTest());

        $this->assertEquals(Tokenizer::T_BOF, $tokenizer->prev());
    }

    /**
     * Tests the tokenizer with a combination of procedural code and functions.
     *
     * @return void
     */
    public function testInternalWithProceduralCodeAndFunction()
    {
        $expected = array(
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
            Tokens::T_CLOSE_TAG
        );

        $this->assertEquals($expected, $this->_getTokenTypesForTest());
    }

    /**
     * Test case for undetected static method call added.
     *
     * @return void
     */
    public function testInternalStaticCallBug01()
    {
        $expected = array(
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
        );

        $this->assertEquals($expected, $this->_getTokenTypesForTest());
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
     *
     * @return void
     */
    public function testInternalDollarSyntaxBug09()
    {
        $expected = array(
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
        );

        $this->assertEquals($expected, $this->_getTokenTypesForTest());
    }

    /**
     * Test case for the inline html bug.
     *
     * @return void
     */
    public function testTokenizerWithInlineHtmlBug24()
    {
        $tokenizer  = new PHPTokenizerInternal();
        $tokenizer->setSourceFile(self::createCodeResourceUriForTest());

        $expected = array(
            array(Tokens::T_OPEN_TAG, 1),
            array(Tokens::T_CLASS, 2),
            array(Tokens::T_STRING, 2),
            array(Tokens::T_CURLY_BRACE_OPEN, 3),
            array(Tokens::T_FUNCTION, 4),
            array(Tokens::T_STRING, 4),
            array(Tokens::T_PARENTHESIS_OPEN, 4),
            array(Tokens::T_PARENTHESIS_CLOSE, 4),
            array(Tokens::T_CURLY_BRACE_OPEN, 5),
            array(Tokens::T_CLOSE_TAG, 6),
            array(Tokens::T_NO_PHP, 7),
            array(Tokens::T_OPEN_TAG, 7),
            array(Tokens::T_ECHO, 7),
            array(Tokens::T_STRING, 7),
            array(Tokens::T_SEMICOLON, 7),
            array(Tokens::T_CLOSE_TAG,  7),
            array(Tokens::T_NO_PHP, 7),
            array(Tokens::T_OPEN_TAG, 8),
            array(Tokens::T_ECHO, 8),
            array(Tokens::T_STRING, 8),
            array(Tokens::T_PARENTHESIS_OPEN, 8),
            array(Tokens::T_VARIABLE, 8),
            array(Tokens::T_PARENTHESIS_CLOSE, 8),
            array(Tokens::T_SEMICOLON, 8),
            array(Tokens::T_CLOSE_TAG, 8),
            array(Tokens::T_NO_PHP, 8),
            array(Tokens::T_OPEN_TAG, 10),
            array(Tokens::T_CURLY_BRACE_CLOSE, 11),
            array(Tokens::T_FUNCTION, 13),
            array(Tokens::T_STRING, 13),
            array(Tokens::T_PARENTHESIS_OPEN, 13),
            array(Tokens::T_PARENTHESIS_CLOSE, 13),
            array(Tokens::T_CURLY_BRACE_OPEN, 14),
            array(Tokens::T_CURLY_BRACE_CLOSE, 16),
            array(Tokens::T_CURLY_BRACE_CLOSE, 17),
        );

        $actual = array();
        while (is_object($token = $tokenizer->next())) {
            $actual[] = array($token->type, $token->startLine);
        }

        $this->assertSame($expected, $actual);
    }

    /**
     * Tests that the tokenizer handles a backslash within a string correct,
     * this bug only occures for PHP versions < 5.3.0alpha3.
     *
     * @return void
     */
    public function testTokenizerHandlesBackslashInStringCorrectBug84()
    {
        if (version_compare(phpversion(), '5.3.0alpha3') >= 0) {
            $this->markTestSkipped('Only relevant for php versions < 5.3.0alpha3');
        }

        $tokenizer  = new PHPTokenizerInternal();
        $tokenizer->setSourceFile(self::createCodeResourceUriForTest());

        $expected = array(
            array(Tokens::T_OPEN_TAG, 1),
            array(Tokens::T_VARIABLE, 2),
            array(Tokens::T_EQUAL, 2),
            array(Tokens::T_CONSTANT_ENCAPSED_STRING, 2),
            array(Tokens::T_SEMICOLON, 2),
            array(Tokens::T_CLOSE_TAG, 3),
        );

        $actual = array();
        while (is_object($token = $tokenizer->next())) {
            $actual[] = array($token->type, $token->startLine);
        }

        $this->assertSame($expected, $actual);
    }

    /**
     * Tests the tokenizer's column calculation implementation.
     *
     * @return void
     */
    public function testTokenizerCalculatesCorrectColumnForInlinePhpIssue88()
    {
        $tokenizer  = new PHPTokenizerInternal();
        $tokenizer->setSourceFile(self::createCodeResourceUriForTest());

        $expected = array(
            array(Tokens::T_NO_PHP, '<html>
    <head>
        <title>', 1, 3, 1, 15),
            array(Tokens::T_OPEN_TAG, '<?php', 3, 3, 16, 20),
            array(Tokens::T_ECHO, 'echo', 3, 3, 22, 25),
            array(Tokens::T_VARIABLE, '$foo', 3, 3, 27, 30),
            array(Tokens::T_SEMICOLON, ';', 3, 3, 31, 31),
            array(Tokens::T_CLOSE_TAG, '?>', 3, 3, 32, 33),
            array(Tokens::T_NO_PHP, '</title>
    </head>
    <body>', 3, 5, 34, 10),
            array(Tokens::T_OPEN_TAG, '<?php', 6, 6, 9, 13),
            array(Tokens::T_ECHO, 'echo', 6, 6, 15, 18),
            array(Tokens::T_VARIABLE, '$bar', 6, 6, 20, 23),
            array(Tokens::T_SEMICOLON, ';', 6, 6, 24, 24),
            array(Tokens::T_CLOSE_TAG, '?>', 6, 6, 26, 27),
            array(Tokens::T_NO_PHP, '    </body>
</html>', 7, 8, 1, 7),
        );

        $actual = array();
        while (is_object($token = $tokenizer->next())) {
            $actual[] = array(
                $token->type,
                $token->image,
                $token->startLine,
                $token->endLine,
                $token->startColumn,
                $token->endColumn
            );
        }

        $this->assertSame($expected, $actual);
    }

    /**
     * Tests the tokenizer's column calculation implementation.
     *
     * @return void
     */
    public function testTokenizerCalculatesCorrectColumnForInlinePhpInTextIssue88()
    {
        $tokenizer  = new PHPTokenizerInternal();
        $tokenizer->setSourceFile(self::createCodeResourceUriForTest());

        $expected = array(
            array(Tokens::T_NO_PHP, 'Hello', 1, 1, 1, 5),
            array(Tokens::T_OPEN_TAG, '<?php', 1, 1, 7, 11),
            array(Tokens::T_ECHO, 'echo', 1, 1, 13, 16),
            array(Tokens::T_VARIABLE, '$user', 1, 1, 18, 22),
            array(Tokens::T_SEMICOLON, ';', 1, 1, 23, 23),
            array(Tokens::T_CLOSE_TAG, '?>', 1, 1, 25, 26),
            array(Tokens::T_NO_PHP, '
this is a simple letter to users of', 2, 3, 1, 35),
            array(Tokens::T_OPEN_TAG, '<?php', 3, 3, 37, 41),
            array(Tokens::T_PRINT, 'print', 3, 3, 43, 47),
            array(Tokens::T_VARIABLE, '$service', 3, 3, 49, 56),
            array(Tokens::T_SEMICOLON, ';', 3, 3, 57, 57),
            array(Tokens::T_CLOSE_TAG, '?>', 3, 3, 59, 60),
            array(Tokens::T_NO_PHP, '.

Manuel', 3, 5, 61, 6),
        );

        $actual = array();
        while (is_object($token = $tokenizer->next())) {
            $actual[] = array(
                $token->type,
                $token->image,
                $token->startLine,
                $token->endLine,
                $token->startColumn,
                $token->endColumn
            );
        }

        $this->assertSame($expected, $actual);
    }

    /**
     * testTokenizerSubstitutesDollarCurlyOpenWithTwoSeparateTokens
     *
     * @return void
     */
    public function testTokenizerSubstitutesDollarCurlyOpenWithTwoSeparateTokens()
    {
        $tokenizer = new PHPTokenizerInternal();
        $tokenizer->setSourceFile(self::createCodeResourceUriForTest());

        $actual = array();
        while (is_object($token = $tokenizer->next())) {
            $actual[] = array($token->type, $token->startColumn, $token->endColumn);
        }

        $expected = array(
            array(Tokens::T_OPEN_TAG, 1, 5),
            array(Tokens::T_DOLLAR, 1, 1),
            array(Tokens::T_CURLY_BRACE_OPEN, 2, 2),
            array(Tokens::T_VARIABLE, 3, 6),
            array(Tokens::T_CONCAT, 8, 8),
            array(Tokens::T_VARIABLE, 10, 13),
            array(Tokens::T_CURLY_BRACE_CLOSE, 14, 14),
            array(Tokens::T_SEMICOLON, 15, 15),
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testReturnsExpectedTokensForStringWithEmbeddedBacktickExpression
     *
     * @return void
     */
    public function testReturnsExpectedTokensForStringWithEmbeddedBacktickExpression()
    {
        $tokenizer = new PHPTokenizerInternal();
        $tokenizer->setSourceFile(self::createCodeResourceUriForTest());

        $actual = array();
        while (is_object($token = $tokenizer->next())) {
            $actual[] = array($token->type);
        }

        $expected = array(
            array(Tokens::T_OPEN_TAG),
            array(Tokens::T_DOUBLE_QUOTE),
            array(Tokens::T_ENCAPSED_AND_WHITESPACE),
            array(Tokens::T_VARIABLE),
            array(Tokens::T_BACKTICK),
            array(Tokens::T_DOUBLE_QUOTE),
            array(Tokens::T_SEMICOLON),
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testReturnsExpectedTokensForBacktickExpressionWithEmbeddedString
     *
     * @return void
     */
    public function testReturnsExpectedTokensForBacktickExpressionWithEmbeddedString()
    {
        $expected = array(
            Tokens::T_OPEN_TAG,
            Tokens::T_BACKTICK,
            Tokens::T_ENCAPSED_AND_WHITESPACE,
            Tokens::T_VARIABLE,
            Tokens::T_DOUBLE_QUOTE,
            Tokens::T_BACKTICK,
            Tokens::T_SEMICOLON,
        );

        $this->assertEquals($expected, $this->_getTokenTypesForTest());
    }

    /**
     * Returns an array with the token types found in a file associated with
     * the currently running test.
     *
     * @return array(integer)
     */
    private function _getTokenTypesForTest()
    {
        $tokenizer = new PHPTokenizerInternal();
        $tokenizer->setSourceFile(self::createCodeResourceUriForTest());

        $types = array();
        while (is_object($token = $tokenizer->next())) {
            $types[] = $token->type;
        }
        return $types;
    }
}
