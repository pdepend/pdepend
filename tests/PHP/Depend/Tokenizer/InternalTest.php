<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Tokenizer
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Depend/Token.php';
require_once 'PHP/Depend/Tokenizer/Internal.php';

/**
 * Test case for the {@link PHP_Depend_Tokenizer_Internal} class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Tokenizer
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Tokenizer_InternalTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests the tokenizer with a source file that contains only classes.
     *
     * @return void
     * @covers PHP_Depend_Tokenizer_Internal
     * @group pdepend
     * @group pdepend::tokenizer
     * @group unittest
     */
    public function testInternalWithClasses()
    {
        $sourceFile = realpath(dirname(__FILE__) . '/../_code/classes.php');
        $tokenizer  = new PHP_Depend_Tokenizer_Internal();
        $tokenizer->setSourceFile($sourceFile);

        $expected = array(
            PHP_Depend_TokenizerI::T_OPEN_TAG,
            PHP_Depend_TokenizerI::T_DOC_COMMENT,
            PHP_Depend_TokenizerI::T_ABSTRACT,
            PHP_Depend_TokenizerI::T_CLASS,
            PHP_Depend_TokenizerI::T_STRING,
            PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN,
            PHP_Depend_TokenizerI::T_CURLY_BRACE_CLOSE,
            PHP_Depend_TokenizerI::T_CLASS,
            PHP_Depend_TokenizerI::T_STRING,
            PHP_Depend_TokenizerI::T_EXTENDS,
            PHP_Depend_TokenizerI::T_STRING,
            PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN,
            PHP_Depend_TokenizerI::T_PUBLIC,
            PHP_Depend_TokenizerI::T_FUNCTION,
            PHP_Depend_TokenizerI::T_STRING,
            PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN,
            PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE,
            PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN,
            PHP_Depend_TokenizerI::T_STRING,
            PHP_Depend_TokenizerI::T_DOUBLE_COLON,
            PHP_Depend_TokenizerI::T_STRING,
            PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN,
            PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE,
            PHP_Depend_TokenizerI::T_SEMICOLON,
            PHP_Depend_TokenizerI::T_VARIABLE,
            PHP_Depend_TokenizerI::T_EQUAL,
            PHP_Depend_TokenizerI::T_CONSTANT_ENCAPSED_STRING,
            PHP_Depend_TokenizerI::T_SEMICOLON,
            PHP_Depend_TokenizerI::T_VARIABLE,
            PHP_Depend_TokenizerI::T_EQUAL,
            PHP_Depend_TokenizerI::T_TRUE,
            PHP_Depend_TokenizerI::T_SEMICOLON,
            PHP_Depend_TokenizerI::T_CURLY_BRACE_CLOSE,
            PHP_Depend_TokenizerI::T_CURLY_BRACE_CLOSE
        );

        $actual = array();
        while (is_object($token = $tokenizer->next())) {
            $actual[] = $token->type;
        }

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests the tokenizer with a source file that contains mixed content of
     * classes and functions.
     *
     * @return void
     * @covers PHP_Depend_Tokenizer_Internal
     * @group pdepend
     * @group pdepend::tokenizer
     * @group unittest
     */
    public function testInternalWithMixedContent()
    {
        $sourceFile = realpath(dirname(__FILE__) . '/../_code/func_class.php');
        $tokenizer  = new PHP_Depend_Tokenizer_Internal();
        $tokenizer->setSourceFile($sourceFile);

        $expected = array(
            array(PHP_Depend_TokenizerI::T_OPEN_TAG, 1),
            array(PHP_Depend_TokenizerI::T_COMMENT, 2),
            array(PHP_Depend_TokenizerI::T_FUNCTION, 5),
            array(PHP_Depend_TokenizerI::T_STRING, 5),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 5),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 5),
            array(PHP_Depend_TokenizerI::T_COMMA, 5),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 5),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 5),
            array(PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN, 5),
            array(PHP_Depend_TokenizerI::T_NEW, 6),
            array(PHP_Depend_TokenizerI::T_STRING, 6),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 6),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 6),
            array(PHP_Depend_TokenizerI::T_COMMA, 6),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 6),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 6),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 6),
            array(PHP_Depend_TokenizerI::T_CURLY_BRACE_CLOSE, 7),
            array(PHP_Depend_TokenizerI::T_DOC_COMMENT, 10),
            array(PHP_Depend_TokenizerI::T_CLASS, 13),
            array(PHP_Depend_TokenizerI::T_STRING, 13),
            array(PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN, 13),
            array(PHP_Depend_TokenizerI::T_COMMENT, 14),
            array(PHP_Depend_TokenizerI::T_CURLY_BRACE_CLOSE, 15),
            array(PHP_Depend_TokenizerI::T_CLOSE_TAG, 16)
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
     * @covers PHP_Depend_Tokenizer_Internal
     * @group pdepend
     * @group pdepend::tokenizer
     * @group unittest
     */
    public function testInternalReturnsBOFTokenForPrevCall()
    {
        $sourceFile = realpath(dirname(__FILE__) . '/../_code/func_class.php');
        $tokenizer  = new PHP_Depend_Tokenizer_Internal();
        $tokenizer->setSourceFile($sourceFile);

        $this->assertEquals(PHP_Depend_TokenizerI::T_BOF, $tokenizer->prev());
    }

    /**
     * Tests the tokenizer with a combination of procedural code and functions.
     *
     * @return void
     * @covers PHP_Depend_Tokenizer_Internal
     * @group pdepend
     * @group pdepend::tokenizer
     * @group unittest
     */
    public function testInternalWithProceduralCodeAndFunction()
    {
        $sourceFile = realpath(dirname(__FILE__) . '/../_code/func_code.php');
        $tokenizer  = new PHP_Depend_Tokenizer_Internal();
        $tokenizer->setSourceFile($sourceFile);

        $expected = array(
            PHP_Depend_TokenizerI::T_OPEN_TAG,
            PHP_Depend_TokenizerI::T_FUNCTION,
            PHP_Depend_TokenizerI::T_STRING,
            PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN,
            PHP_Depend_TokenizerI::T_VARIABLE,
            PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE,
            PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN,
            PHP_Depend_TokenizerI::T_VARIABLE,
            PHP_Depend_TokenizerI::T_EQUAL,
            PHP_Depend_TokenizerI::T_NEW,
            PHP_Depend_TokenizerI::T_VARIABLE,
            PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN,
            PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE,
            PHP_Depend_TokenizerI::T_SEMICOLON,
            PHP_Depend_TokenizerI::T_CURLY_BRACE_CLOSE,
            PHP_Depend_TokenizerI::T_VARIABLE,
            PHP_Depend_TokenizerI::T_EQUAL,
            PHP_Depend_TokenizerI::T_STRING,
            PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN,
            PHP_Depend_TokenizerI::T_CONSTANT_ENCAPSED_STRING,
            PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE,
            PHP_Depend_TokenizerI::T_SEMICOLON,
            PHP_Depend_TokenizerI::T_STRING,
            PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN,
            PHP_Depend_TokenizerI::T_ARRAY,
            PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN,
            PHP_Depend_TokenizerI::T_VARIABLE,
            PHP_Depend_TokenizerI::T_COMMA,
            PHP_Depend_TokenizerI::T_CONSTANT_ENCAPSED_STRING,
            PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE,
            PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE,
            PHP_Depend_TokenizerI::T_SEMICOLON,
            PHP_Depend_TokenizerI::T_CLOSE_TAG
        );

        $actual = array();
        while (is_object($token = $tokenizer->next())) {
            $actual[] = $token->type;
        }

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test case for undetected static method call added.
     *
     * @return void
     * @covers PHP_Depend_Tokenizer_Internal
     * @group pdepend
     * @group pdepend::tokenizer
     * @group unittest
     */
    public function testInternalStaticCallBug01()
    {
        $sourceFile = dirname(__FILE__) . '/../_code/bugs/001.php';
        $tokenizer  = new PHP_Depend_Tokenizer_Internal();
        $tokenizer->setSourceFile($sourceFile);

        $expected = array(
            PHP_Depend_TokenizerI::T_OPEN_TAG,
            PHP_Depend_TokenizerI::T_DOC_COMMENT,
            PHP_Depend_TokenizerI::T_CLASS,
            PHP_Depend_TokenizerI::T_STRING,
            PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN,
            PHP_Depend_TokenizerI::T_PUBLIC,
            PHP_Depend_TokenizerI::T_FUNCTION,
            PHP_Depend_TokenizerI::T_STRING,
            PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN,
            PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE,
            PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN,
            PHP_Depend_TokenizerI::T_STRING,
            PHP_Depend_TokenizerI::T_DOUBLE_COLON,
            PHP_Depend_TokenizerI::T_STRING,
            PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN,
            PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE,
            PHP_Depend_TokenizerI::T_SEMICOLON,
            PHP_Depend_TokenizerI::T_CURLY_BRACE_CLOSE,
            PHP_Depend_TokenizerI::T_CURLY_BRACE_CLOSE,
        );

        $actual = array();
        while (is_object($token = $tokenizer->next())) {
            $actual[] = $token->type;
        }

        $this->assertEquals($expected, $actual);
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
     * @covers PHP_Depend_Tokenizer_Internal
     * @group pdepend
     * @group pdepend::tokenizer
     * @group unittest
     */
    public function testInternalDollarSyntaxBug09()
    {
        $sourceFile = dirname(__FILE__) . '/../_code/bugs/005.php';
        $tokenizer  = new PHP_Depend_Tokenizer_Internal();
        $tokenizer->setSourceFile($sourceFile);

        $expected = array(
            PHP_Depend_TokenizerI::T_OPEN_TAG,
            PHP_Depend_TokenizerI::T_DOC_COMMENT,
            PHP_Depend_TokenizerI::T_CLASS,
            PHP_Depend_TokenizerI::T_STRING,
            PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN,
            PHP_Depend_TokenizerI::T_PUBLIC,
            PHP_Depend_TokenizerI::T_FUNCTION,
            PHP_Depend_TokenizerI::T_STRING,
            PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN,
            PHP_Depend_TokenizerI::T_VARIABLE,
            PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE,
            PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN,
            PHP_Depend_TokenizerI::T_SELF, // SELF
            PHP_Depend_TokenizerI::T_DOUBLE_COLON,
            PHP_Depend_TokenizerI::T_DOLLAR,
            PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN,
            PHP_Depend_TokenizerI::T_VARIABLE,
            PHP_Depend_TokenizerI::T_CURLY_BRACE_CLOSE,
            PHP_Depend_TokenizerI::T_SEMICOLON,
            PHP_Depend_TokenizerI::T_CURLY_BRACE_CLOSE,
            PHP_Depend_TokenizerI::T_CURLY_BRACE_CLOSE,
        );

        $actual = array();
        while (is_object($token = $tokenizer->next())) {
            $actual[] = $token->type;
        }

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test case for the inline html bug.
     *
     * @return void
     * @covers PHP_Depend_Tokenizer_Internal
     * @group pdepend
     * @group pdepend::tokenizer
     * @group unittest
     */
    public function testTokenizerWithInlineHtmlBug24()
    {
        $sourceFile = dirname(__FILE__) . '/../_code/bugs/024.php';
        $tokenizer  = new PHP_Depend_Tokenizer_Internal();
        $tokenizer->setSourceFile($sourceFile);

        $expected = array(
            array(PHP_Depend_TokenizerI::T_OPEN_TAG, 1),
            array(PHP_Depend_TokenizerI::T_CLASS, 2),
            array(PHP_Depend_TokenizerI::T_STRING, 2),
            array(PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN, 3),
            array(PHP_Depend_TokenizerI::T_FUNCTION, 4),
            array(PHP_Depend_TokenizerI::T_STRING, 4),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 4),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 4),
            array(PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN, 5),
            array(PHP_Depend_TokenizerI::T_CLOSE_TAG, 6),
            array(PHP_Depend_ConstantsI::T_NO_PHP, 7),
            array(PHP_Depend_TokenizerI::T_OPEN_TAG, 7),
            array(PHP_Depend_TokenizerI::T_ECHO, 7),
            array(PHP_Depend_TokenizerI::T_STRING, 7),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 7),
            array(PHP_Depend_TokenizerI::T_CLOSE_TAG,  7),
            array(PHP_Depend_ConstantsI::T_NO_PHP, 7),
            array(PHP_Depend_TokenizerI::T_OPEN_TAG, 8),
            array(PHP_Depend_TokenizerI::T_ECHO, 8),
            array(PHP_Depend_TokenizerI::T_STRING, 8),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 8),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 8),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 8),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 8),
            array(PHP_Depend_TokenizerI::T_CLOSE_TAG, 8),
            array(PHP_Depend_ConstantsI::T_NO_PHP, 8),
            array(PHP_Depend_TokenizerI::T_OPEN_TAG, 10),
            array(PHP_Depend_TokenizerI::T_CURLY_BRACE_CLOSE, 11),
            array(PHP_Depend_TokenizerI::T_FUNCTION, 13),
            array(PHP_Depend_TokenizerI::T_STRING, 13),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN, 13),
            array(PHP_Depend_TokenizerI::T_PARENTHESIS_CLOSE, 13),
            array(PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN, 14),
            array(PHP_Depend_TokenizerI::T_CURLY_BRACE_CLOSE, 16),
            array(PHP_Depend_TokenizerI::T_CURLY_BRACE_CLOSE, 17),
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
     * @covers PHP_Depend_Tokenizer_Internal
     * @group pdepend
     * @group pdepend::tokenizer
     * @group unittest
     */
    public function testTokenizerHandlesBackslashInStringCorrectBug84()
    {
        if (version_compare(phpversion(), '5.3.0alpha3') >= 0) {
            $this->markTestSkipped('Only relevant for php versions < 5.3.0alpha3');
        }

        $sourceFile = dirname(__FILE__) . '/../_code/bugs/054-namespace-separator.php';
        $tokenizer  = new PHP_Depend_Tokenizer_Internal();
        $tokenizer->setSourceFile($sourceFile);

        $expected = array(
            array(PHP_Depend_TokenizerI::T_OPEN_TAG, 1),
            array(PHP_Depend_TokenizerI::T_VARIABLE, 2),
            array(PHP_Depend_TokenizerI::T_EQUAL, 2),
            array(PHP_Depend_TokenizerI::T_CONSTANT_ENCAPSED_STRING, 2),
            array(PHP_Depend_TokenizerI::T_SEMICOLON, 2),
            array(PHP_Depend_TokenizerI::T_CLOSE_TAG, 3),
        );

        $actual = array();
        while (is_object($token = $tokenizer->next())) {
            $actual[] = array($token->type, $token->startLine);
        }

        $this->assertSame($expected, $actual);
    }

    /**
     * Tests the tokenizers column calculation implementation.
     *
     * @return void
     * @covers PHP_Depend_Tokenizer_Internal
     * @group pdepend
     * @group pdepend::tokenizer
     * @group unittest
     */
    public function testTokenizerCalculatesCorrectColumnForInlinePhpIssue88()
    {
        $sourceFile = dirname(__FILE__) . '/../_code/issues/088-1.phtml';
        $tokenizer  = new PHP_Depend_Tokenizer_Internal();
        $tokenizer->setSourceFile($sourceFile);

        $expected = array(
            array(PHP_Depend_ConstantsI::T_NO_PHP, '<html>
    <head>
        <title>', 1, 3, 1, 15),
            array(PHP_Depend_ConstantsI::T_OPEN_TAG, '<?php', 3, 3, 16, 20),
            array(PHP_Depend_ConstantsI::T_ECHO, 'echo', 3, 3, 22, 25),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$foo', 3, 3, 27, 30),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 3, 3, 31, 31),
            array(PHP_Depend_ConstantsI::T_CLOSE_TAG, '?>', 3, 3, 32, 33),
            array(PHP_Depend_ConstantsI::T_NO_PHP, '</title>
    </head>
    <body>', 3, 5, 34, 10),
            array(PHP_Depend_ConstantsI::T_OPEN_TAG, '<?php', 6, 6, 9, 13),
            array(PHP_Depend_ConstantsI::T_ECHO, 'echo', 6, 6, 15, 18),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$bar', 6, 6, 20, 23),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 6, 6, 24, 24),
            array(PHP_Depend_ConstantsI::T_CLOSE_TAG, '?>', 6, 6, 26, 27),
            array(PHP_Depend_ConstantsI::T_NO_PHP, '    </body>
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
     * Tests the tokenizers column calculation implementation.
     *
     * @return void
     * @covers PHP_Depend_Tokenizer_Internal
     * @group pdepend
     * @group pdepend::tokenizer
     * @group unittest
     */
    public function testTokenizerCalculatesCorrectColumnForInlinePhpInTextIssue88()
    {
        $sourceFile = dirname(__FILE__) . '/../_code/issues/088-2.php';
        $tokenizer  = new PHP_Depend_Tokenizer_Internal();
        $tokenizer->setSourceFile($sourceFile);

        $expected = array(
            array(PHP_Depend_ConstantsI::T_NO_PHP, 'Hello', 1, 1, 1, 5),
            array(PHP_Depend_ConstantsI::T_OPEN_TAG, '<?php', 1, 1, 7, 11),
            array(PHP_Depend_ConstantsI::T_ECHO, 'echo', 1, 1, 13, 16),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$user', 1, 1, 18, 22),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 1, 1, 23, 23),
            array(PHP_Depend_ConstantsI::T_CLOSE_TAG, '?>', 1, 1, 25, 26),
            array(PHP_Depend_ConstantsI::T_NO_PHP, '
this is a simple letter to users of', 2, 3, 1, 35),
            array(PHP_Depend_ConstantsI::T_OPEN_TAG, '<?php', 3, 3, 37, 41),
            array(PHP_Depend_ConstantsI::T_PRINT, 'print', 3, 3, 43, 47),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$service', 3, 3, 49, 56),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 3, 3, 57, 57),
            array(PHP_Depend_ConstantsI::T_CLOSE_TAG, '?>', 3, 3, 59, 60),
            array(PHP_Depend_ConstantsI::T_NO_PHP, '.

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
     * @covers PHP_Depend_Tokenizer_Internal
     * @group pdepend
     * @group pdepend::tokenizer
     * @group unittest
     */
    public function testTokenizerSubstitutesDollarCurlyOpenWithTwoSeparateTokens()
    {
        $tokenizer = new PHP_Depend_Tokenizer_Internal();
        $tokenizer->setSourceFile(
            self::createCodeResourceURI('tokenizer/' . __FUNCTION__ . '.php')
        );

        $actual = array();
        while (is_object($token = $tokenizer->next())) {
            $actual[] = array($token->type, $token->startColumn, $token->endColumn);
        }

        $expected = array(
            array(PHP_Depend_ConstantsI::T_OPEN_TAG, 1, 5),
            array(PHP_Depend_ConstantsI::T_DOLLAR, 1, 1),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, 2, 2),
            array(PHP_Depend_ConstantsI::T_VARIABLE, 3, 6),
            array(PHP_Depend_ConstantsI::T_CONCAT, 8, 8),
            array(PHP_Depend_ConstantsI::T_VARIABLE, 10, 13),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, 14, 14),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, 15, 15),
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testReturnsExpectedTokensForStringWithEmbeddedBacktickExpression
     *
     * @return void
     * @covers PHP_Depend_Tokenizer_Internal
     * @group pdepend
     * @group pdepend::tokenizer
     * @group unittest
     */
    public function testReturnsExpectedTokensForStringWithEmbeddedBacktickExpression()
    {
        $tokenizer = new PHP_Depend_Tokenizer_Internal();
        $tokenizer->setSourceFile(
            self::createCodeResourceURI('tokenizer/' . __FUNCTION__ . '.php')
        );

        $actual = array();
        while (is_object($token = $tokenizer->next())) {
            $actual[] = array($token->type);
        }

        $expected = array(
            array(PHP_Depend_ConstantsI::T_OPEN_TAG),
            array(PHP_Depend_ConstantsI::T_DOUBLE_QUOTE),
            array(PHP_Depend_ConstantsI::T_ENCAPSED_AND_WHITESPACE),
            array(PHP_Depend_ConstantsI::T_VARIABLE),
            array(PHP_Depend_ConstantsI::T_BACKTICK),
            array(PHP_Depend_ConstantsI::T_DOUBLE_QUOTE),
            array(PHP_Depend_ConstantsI::T_SEMICOLON),
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testReturnsExpectedTokensForBacktickExpressionWithEmbeddedString
     *
     * @return void
     * @covers PHP_Depend_Tokenizer_Internal
     * @group pdepend
     * @group pdepend::tokenizer
     * @group unittest
     */
    public function testReturnsExpectedTokensForBacktickExpressionWithEmbeddedString()
    {
        $tokenizer = new PHP_Depend_Tokenizer_Internal();
        $tokenizer->setSourceFile(
            self::createCodeResourceURI('tokenizer/' . __FUNCTION__ . '.php')
        );

        $actual = array();
        while (is_object($token = $tokenizer->next())) {
            $actual[] = array($token->type);
        }

        $expected = array(
            array(PHP_Depend_ConstantsI::T_OPEN_TAG),
            array(PHP_Depend_ConstantsI::T_BACKTICK),
            array(PHP_Depend_ConstantsI::T_ENCAPSED_AND_WHITESPACE),
            array(PHP_Depend_ConstantsI::T_VARIABLE),
            array(PHP_Depend_ConstantsI::T_DOUBLE_QUOTE),
            array(PHP_Depend_ConstantsI::T_BACKTICK),
            array(PHP_Depend_ConstantsI::T_SEMICOLON),
        );

        $this->assertEquals($expected, $actual);
    }
}
