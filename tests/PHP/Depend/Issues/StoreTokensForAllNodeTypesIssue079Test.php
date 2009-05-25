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

    /**
     * Tests that the parser stores the expected property tokens.
     *
     * @return void
     */
    public function testParserStoresParameterTokensWithoutDefaultValue()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');

        $parameter = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters()
            ->current();

        $expected = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$bar', 3, 3, 25, 28),
        );

        $this->assertEquals($expected, $parameter->getTokens());
    }

    /**
     * Tests that the parser stores the expected property tokens.
     *
     * @return void
     */
    public function testParserStoresParameterTokensWithDefaultValueTrue()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');

        $parameter = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters()
            ->current();

        $expected = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$bar', 3, 3, 25, 28),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_EQUAL, '=', 4, 4, 9, 9),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 5, 5, 5, 8),
        );

        $this->assertEquals($expected, $parameter->getTokens());
    }

    /**
     * Tests that the parser stores the expected property tokens.
     *
     * @return void
     */
    public function testParserStoresParameterTokensWithDefaultValueArrayAndComments()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');

        $parameter = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters()
            ->current();

        $expected = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$bar', 3, 3, 25, 28),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_DOC_COMMENT, '/** $bar*/', 3, 3, 29, 38),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_EQUAL, '=', 3, 3, 39, 39),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_ARRAY, 'array', 3, 3, 40, 44),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 3, 3, 45, 45),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COMMENT, '/*data*/', 3, 3, 46, 53),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 3, 3, 54, 54),
        );

        $this->assertEquals($expected, $parameter->getTokens());
    }

    /**
     * Tests that the parser stores the expected property tokens.
     *
     * @return void
     */
    public function testParserStoresParameterTokensForMultipleParameters()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');

        $parameters = $packages->current()
            ->getFunctions()
            ->current()
            ->getParameters();

        $expected = array(
            array(
                new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$bar', 2, 2, 14, 17),
                new PHP_Depend_Token(PHP_Depend_ConstantsI::T_EQUAL, '=', 2, 2, 19, 19),
                new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LNUMBER, '42', 2, 2, 21, 22),
            ),
            array(
                new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$baz', 2, 2, 25, 28),
                new PHP_Depend_Token(PHP_Depend_ConstantsI::T_EQUAL, '=', 2, 2, 30, 30),
                new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FALSE, 'false', 2, 2, 32, 36),
            ),
            array(
                new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$foo', 2, 2, 39, 42),
            ),
        );

        foreach ($parameters as $parameter) {
            $this->assertEquals(array_shift($expected), $parameter->getTokens());
        }
        $this->assertSame(0, count($expected));
    }

    /**
     * Tests that the parameter contains the start line of the first token.
     *
     * @return void
     */
    public function testParameterContainsStartLineOfFirstToken()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');

        $parameter = $packages->current()
            ->getFunctions()
            ->current()
            ->getParameters()
            ->current();

        $this->assertSame(4, $parameter->getStartLine());
    }

    /**
     * Tests that the parameter contains the end line of the last token.
     *
     * @return void
     */
    public function testParameterContainsEndLineOfLastToken()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');

        $parameter = $packages->current()
            ->getFunctions()
            ->current()
            ->getParameters()
            ->current();

        $this->assertSame(11, $parameter->getEndLine());
    }

    /**
     * Tests that the parser stores the expected constant tokens.
     *
     * @return void
     */
    public function testParserStoresConstantTokensWithSignedDefaultValue()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');
        $constant = $packages->current()
            ->getClasses()
            ->current()
            ->getConstants()
            ->current();

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
    public function testParserStoresConstantTokensWithInlineComments()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');
        $constant = $packages->current()
            ->getClasses()
            ->current()
            ->getConstants()
            ->current();

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
     * Tests that the parser throws an exception when a constant declaration
     * contains an invalid token.
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForArrayInConstantDeclaration()
    {
        $this->setExpectedException(
            'PHP_Depend_Parser_UnexpectedTokenException',
            'Unexpected token: array, line: 4, col: 17, file: '
        );

        self::parseSource('issues/079/' . __FUNCTION__ . '.php');
    }

    /**
     * Tests that the constant contains the start line of the first token.
     *
     * @return void
     */
    public function testConstantContainsStartLineOfFirstToken()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');
        $constant = $packages->current()
            ->getClasses()
            ->current()
            ->getConstants()
            ->current();

        $this->assertSame(3, $constant->getStartLine());
    }

    /**
     * Tests that the constant contains the end line of the last token.
     *
     * @return void
     */
    public function testConstantContainsEndLineOfLastToken()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');
        $constant = $packages->current()
            ->getClasses()
            ->current()
            ->getConstants()
            ->current();

        $this->assertSame(7, $constant->getEndLine());
    }

    /**
     * Tests that the parser stores the expected function tokens.
     *
     * @return void
     */
    public function testParserStoresExpectedFunctionTokens()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $expected = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FUNCTION, 'function', 7, 7, 1, 8),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'foo', 7, 7, 10, 12),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 7, 7, 13, 13),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 7, 7, 14, 14),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 8, 8, 1, 1),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_RETURN, 'return', 9, 9, 5, 10),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FALSE, 'false', 9, 9, 12, 16),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 9, 9, 17, 17),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 10, 10, 1, 1),
        );

        $this->assertEquals($expected, $function->getTokens());
    }

    /**
     * Tests that the parser stores the expected function tokens.
     *
     * @return void
     */
    public function testParserStoresExpectedFunctionTokensWithParameters()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $expected = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FUNCTION, 'function', 7, 7, 1, 8),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'foo', 7, 7, 10, 12),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 7, 7, 13, 13),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$foo', 7, 7, 14, 17),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COMMA, ',', 7, 7, 18, 18),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$bar', 7, 7, 20, 23),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_EQUAL, '=', 7, 7, 25, 25),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LNUMBER, '42', 7, 7, 27, 28),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COMMA, ',', 7, 7, 29, 29),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$baz', 7, 7, 31, 34),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_EQUAL, '=', 7, 7, 36, 36),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'T_42', 7, 7, 38, 41),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 7, 7, 42, 42),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 8, 8, 1, 1),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_RETURN, 'return', 9, 9, 5, 10),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FALSE, 'false', 9, 9, 12, 16),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 9, 9, 17, 17),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 10, 10, 1, 1),
        );

        $this->assertEquals($expected, $function->getTokens());
    }

    /**
     * Tests that the function uses the start line of the first token.
     *
     * @return void
     */
    public function testFunctionContainsStartLineOfFirstToken()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $tokens = $function->getTokens();
        $token  = reset($tokens);

        $this->assertSame(2, $token->startLine);
        $this->assertSame($token->startLine, $function->getStartLine());
    }

    /**
     * Tests that the function uses the end line of the last token.
     *
     * @return void
     */
    public function testFunctionContainsEndLineOfLastToken()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $tokens = $function->getTokens();
        $token  = end($tokens);

        $this->assertSame(7, $token->endLine);
        $this->assertSame($token->endLine, $function->getEndLine());
    }

    /**
     * Tests that the parser stores the expected method tokens.
     *
     * @return void
     */
    public function testParserStoresExpectedMethodTokens()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');

        $method = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $expected = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PUBLIC, 'public', 7, 7, 5, 10),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FUNCTION, 'function', 7, 7, 12, 19),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'foo', 7, 7, 21, 23),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 7, 7, 24, 24),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 7, 7, 25, 25),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 8, 8, 5, 5),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_RETURN, 'return', 9, 9, 9, 14),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FALSE, 'false', 9, 9, 16, 20),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 9, 9, 21, 21),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 10, 10, 5, 5),
        );

        $this->assertEquals($expected, $method->getTokens());
    }

    /**
     * Tests that the parser stores the expected method tokens.
     *
     * @return void
     */
    public function testParserStoresExpectedMethodTokensWithStaticModifier()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');

        $method = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $expected = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STATIC, 'static', 7, 7, 5, 10),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PUBLIC, 'public', 7, 7, 12, 17),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FUNCTION, 'function', 7, 7, 19, 26),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'foo', 7, 7, 28, 30),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 7, 7, 31, 31),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 7, 7, 32, 32),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 8, 8, 5, 5),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_RETURN, 'return', 9, 9, 9, 14),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FALSE, 'false', 9, 9, 16, 20),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 9, 9, 21, 21),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 10, 10, 5, 5),
        );

        $this->assertEquals($expected, $method->getTokens());
    }

    /**
     * Tests that the parser stores the expected method tokens.
     *
     * @return void
     */
    public function testParserStoresExpectedMethodTokensWithStaticAndFinalModifiers()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');

        $method = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $expected = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STATIC, 'static', 7, 7, 5, 10),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PUBLIC, 'public', 7, 7, 12, 17),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FINAL, 'final', 7, 7, 19, 23),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FUNCTION, 'function', 7, 7, 25, 32),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'foo', 7, 7, 34, 36),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 7, 7, 37, 37),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 7, 7, 38, 38),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 8, 8, 5, 5),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_RETURN, 'return', 9, 9, 9, 14),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FALSE, 'false', 9, 9, 16, 20),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 9, 9, 21, 21),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 10, 10, 5, 5),
        );

        $this->assertEquals($expected, $method->getTokens());
    }

    /**
     * Tests that the method uses the start line of the first token.
     *
     * @return void
     */
    public function testMethodContainsStartLineOfFirstToken()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');

        $method = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $tokens = $method->getTokens();
        $token  = reset($tokens);

        $this->assertSame(4, $token->startLine);
        $this->assertSame($token->startLine, $method->getStartLine());
    }

    /**
     * Tests that the method uses the end line of the last token.
     *
     * @return void
     */
    public function testMethodContainsEndLineOfLastToken()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');

        $method = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $tokens = $method->getTokens();
        $token  = end($tokens);

        $this->assertSame(11, $token->endLine);
        $this->assertSame($token->endLine, $method->getEndLine());
    }

    /**
     * Tests that the parser stores the expected class tokens.
     *
     * @return void
     */
    public function testParserStoresExpectedClassTokens()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');

        $class = $packages->current()
            ->getClasses()
            ->current();

        $expected = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CLASS, 'class', 2, 2, 1, 5),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'Foo', 2, 2, 7, 9),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 3, 3, 1, 1),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PUBLIC, 'public', 4, 4, 5, 10),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FUNCTION, 'function', 4, 4, 12, 19),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'bar', 4, 4, 21, 23),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 4, 4, 24, 24),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 4, 4, 25, 25),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 4, 4, 27, 27),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 4, 4, 28, 28),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 5, 5, 1, 1),
        );

        $this->assertEquals($expected, $class->getTokens());
    }

    /**
     * Tests that the parser stores the expected class tokens.
     *
     * @return void
     */
    public function testParserStoresExpectedClassTokensWithFinalModifier()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');

        $class = $packages->current()
            ->getClasses()
            ->current();

        $expected = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FINAL, 'final', 2, 2, 1, 5),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CLASS, 'class', 2, 2, 7, 11),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'Foo', 2, 2, 13, 15),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 3, 3, 1, 1),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PUBLIC, 'public', 4, 4, 5, 10),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FUNCTION, 'function', 4, 4, 12, 19),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'bar', 4, 4, 21, 23),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 4, 4, 24, 24),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 4, 4, 25, 25),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 4, 4, 27, 27),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 4, 4, 28, 28),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 5, 5, 1, 1),
        );

        $this->assertEquals($expected, $class->getTokens());
    }

    /**
     * Tests that the parser stores the expected class tokens.
     *
     * @return void
     */
    public function testParserStoresExpectedClassTokensWithAbstractModifier()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');

        $class = $packages->current()
            ->getClasses()
            ->current();

        $expected = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_ABSTRACT, 'abstract', 2, 2, 1, 8),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CLASS, 'class', 2, 2, 10, 14),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'Foo', 2, 2, 16, 18),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 3, 3, 1, 1),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PUBLIC, 'public', 4, 4, 5, 10),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FUNCTION, 'function', 4, 4, 12, 19),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'bar', 4, 4, 21, 23),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 4, 4, 24, 24),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 4, 4, 25, 25),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 4, 4, 27, 27),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 4, 4, 28, 28),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 5, 5, 1, 1),
        );

        $this->assertEquals($expected, $class->getTokens());
    }

    /**
     * Tests that the parser stores the expected interface tokens.
     *
     * @return void
     */
    public function testParserStoresExpectedInterfaceTokens()
    {
        $packages = self::parseSource('issues/079/' . __FUNCTION__ . '.php');

        $interface = $packages->current()
            ->getInterfaces()
            ->current();

        $expected = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_INTERFACE, 'interface', 2, 2, 1, 9),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'Foo', 2, 2, 11, 13),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 3, 3, 1, 1),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PUBLIC, 'public', 4, 4, 5, 10),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FUNCTION, 'function', 4, 4, 12, 19),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'bar', 4, 4, 21, 23),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 4, 4, 24, 24),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 4, 4, 25, 25),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 4, 4, 26, 26),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 5, 5, 1, 1),
        );

        $this->assertEquals($expected, $interface->getTokens());
    }
}
?>
