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

namespace PDepend\Issues;

use PDepend\Source\Tokenizer\Token;
use PDepend\Source\Tokenizer\Tokens;

/**
 * Test case for issue #79 where we should store the tokens for each created
 * ast node.
 *
 * http://tracker.pdepend.org/pdepend/issue_tracker/issue/79
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @group unittest
 */
class StoreTokensForAllNodeTypesIssue079Test extends AbstractFeatureTest
{
    /**
     * Tests that the parameter contains the start line of the first token.
     *
     * @return void
     * @covers \PDepend\Source\AST\ASTParameter
     */
    public function testParameterContainsStartLineOfFirstToken()
    {
        $parameters = self::parseTestCaseSource(__METHOD__)
            ->current()
            ->getFunctions()
            ->current()
            ->getParameters();

        $this->assertEquals(4, $parameters[0]->getStartLine());
    }

    /**
     * Tests that the parameter contains the end line of the last token.
     *
     * @return void
     * @covers \PDepend\Source\AST\ASTParameter
     */
    public function testParameterContainsEndLineOfLastToken()
    {
        $parameters = self::parseTestCaseSource(__METHOD__)
            ->current()
            ->getFunctions()
            ->current()
            ->getParameters();

        $this->assertEquals(11, $parameters[0]->getEndLine());
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
            '\\PDepend\\Source\\Parser\\UnexpectedTokenException',
            'Unexpected token: array, line: 4, col: 17, file: '
        );

        self::parseTestCaseSource(__METHOD__);
    }

    /**
     * Tests that the parser stores the expected function tokens.
     *
     * @return void
     * @covers \PDepend\Source\AST\ASTFunction
     */
    public function testParserStoresExpectedFunctionTokens()
    {
        $function = self::parseTestCaseSource(__METHOD__)
            ->current()
            ->getFunctions()
            ->current();

        $expected = array(
            new Token(Tokens::T_FUNCTION, 'function', 7, 7, 1, 8),
            new Token(Tokens::T_STRING, 'foo', 7, 7, 10, 12),
            new Token(Tokens::T_PARENTHESIS_OPEN, '(', 7, 7, 13, 13),
            new Token(Tokens::T_PARENTHESIS_CLOSE, ')', 7, 7, 14, 14),
            new Token(Tokens::T_CURLY_BRACE_OPEN, '{', 8, 8, 1, 1),
            new Token(Tokens::T_RETURN, 'return', 9, 9, 5, 10),
            new Token(Tokens::T_FALSE, 'false', 9, 9, 12, 16),
            new Token(Tokens::T_SEMICOLON, ';', 9, 9, 17, 17),
            new Token(Tokens::T_CURLY_BRACE_CLOSE, '}', 10, 10, 1, 1),
        );

        $this->assertEquals($expected, $function->getTokens());
    }

    /**
     * Tests that the parser stores the expected function tokens.
     *
     * @return void
     * @covers \PDepend\Source\AST\ASTFunction
     */
    public function testParserStoresExpectedFunctionTokensWithParameters()
    {
        $function = self::parseTestCaseSource(__METHOD__)
            ->current()
            ->getFunctions()
            ->current();

        $expected = array(
            new Token(Tokens::T_FUNCTION, 'function', 7, 7, 1, 8),
            new Token(Tokens::T_STRING, 'foo', 7, 7, 10, 12),
            new Token(Tokens::T_PARENTHESIS_OPEN, '(', 7, 7, 13, 13),
            new Token(Tokens::T_VARIABLE, '$foo', 7, 7, 14, 17),
            new Token(Tokens::T_COMMA, ',', 7, 7, 18, 18),
            new Token(Tokens::T_VARIABLE, '$bar', 7, 7, 20, 23),
            new Token(Tokens::T_EQUAL, '=', 7, 7, 25, 25),
            new Token(Tokens::T_LNUMBER, '42', 7, 7, 27, 28),
            new Token(Tokens::T_COMMA, ',', 7, 7, 29, 29),
            new Token(Tokens::T_VARIABLE, '$baz', 7, 7, 31, 34),
            new Token(Tokens::T_EQUAL, '=', 7, 7, 36, 36),
            new Token(Tokens::T_STRING, 'T_42', 7, 7, 38, 41),
            new Token(Tokens::T_PARENTHESIS_CLOSE, ')', 7, 7, 42, 42),
            new Token(Tokens::T_CURLY_BRACE_OPEN, '{', 8, 8, 1, 1),
            new Token(Tokens::T_RETURN, 'return', 9, 9, 5, 10),
            new Token(Tokens::T_FALSE, 'false', 9, 9, 12, 16),
            new Token(Tokens::T_SEMICOLON, ';', 9, 9, 17, 17),
            new Token(Tokens::T_CURLY_BRACE_CLOSE, '}', 10, 10, 1, 1),
        );

        $this->assertEquals($expected, $function->getTokens());
    }

    /**
     * Tests that the function uses the start line of the first token.
     *
     * @return void
     * @covers \PDepend\Source\AST\ASTFunction
     */
    public function testFunctionContainsStartLineOfFirstToken()
    {
        $function = self::parseTestCaseSource(__METHOD__)
            ->current()
            ->getFunctions()
            ->current();

        $tokens = $function->getTokens();
        $token  = reset($tokens);

        $this->assertEquals($token->startLine, $function->getStartLine());
    }

    /**
     * Tests that the function uses the end line of the last token.
     *
     * @return void
     * @covers \PDepend\Source\AST\ASTFunction
     */
    public function testFunctionContainsEndLineOfLastToken()
    {
        $function = self::parseTestCaseSource(__METHOD__)
            ->current()
            ->getFunctions()
            ->current();

        $tokens = $function->getTokens();
        $token  = end($tokens);

        $this->assertEquals($token->endLine, $function->getEndLine());
    }

    /**
     * Tests that the parser stores the expected method tokens.
     *
     * @return void
     * @covers \PDepend\Source\AST\ASTMethod
     */
    public function testParserStoresExpectedMethodTokens()
    {
        $method = self::parseTestCaseSource(__METHOD__)
            ->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $expected = array(
            new Token(Tokens::T_PUBLIC, 'public', 7, 7, 5, 10),
            new Token(Tokens::T_FUNCTION, 'function', 7, 7, 12, 19),
            new Token(Tokens::T_STRING, 'foo', 7, 7, 21, 23),
            new Token(Tokens::T_PARENTHESIS_OPEN, '(', 7, 7, 24, 24),
            new Token(Tokens::T_PARENTHESIS_CLOSE, ')', 7, 7, 25, 25),
            new Token(Tokens::T_CURLY_BRACE_OPEN, '{', 8, 8, 5, 5),
            new Token(Tokens::T_RETURN, 'return', 9, 9, 9, 14),
            new Token(Tokens::T_FALSE, 'false', 9, 9, 16, 20),
            new Token(Tokens::T_SEMICOLON, ';', 9, 9, 21, 21),
            new Token(Tokens::T_CURLY_BRACE_CLOSE, '}', 10, 10, 5, 5),
        );

        $this->assertEquals($expected, $method->getTokens());
    }

    /**
     * Tests that the parser stores the expected method tokens.
     *
     * @return void
     * @covers \PDepend\Source\AST\ASTMethod
     */
    public function testParserStoresExpectedMethodTokensWithStaticModifier()
    {
        $method = self::parseTestCaseSource(__METHOD__)
            ->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $expected = array(
            new Token(Tokens::T_STATIC, 'static', 7, 7, 5, 10),
            new Token(Tokens::T_PUBLIC, 'public', 7, 7, 12, 17),
            new Token(Tokens::T_FUNCTION, 'function', 7, 7, 19, 26),
            new Token(Tokens::T_STRING, 'foo', 7, 7, 28, 30),
            new Token(Tokens::T_PARENTHESIS_OPEN, '(', 7, 7, 31, 31),
            new Token(Tokens::T_PARENTHESIS_CLOSE, ')', 7, 7, 32, 32),
            new Token(Tokens::T_CURLY_BRACE_OPEN, '{', 8, 8, 5, 5),
            new Token(Tokens::T_RETURN, 'return', 9, 9, 9, 14),
            new Token(Tokens::T_FALSE, 'false', 9, 9, 16, 20),
            new Token(Tokens::T_SEMICOLON, ';', 9, 9, 21, 21),
            new Token(Tokens::T_CURLY_BRACE_CLOSE, '}', 10, 10, 5, 5),
        );

        $this->assertEquals($expected, $method->getTokens());
    }

    /**
     * Tests that the parser stores the expected method tokens.
     *
     * @return void
     * @covers \PDepend\Source\AST\ASTMethod
     */
    public function testParserStoresExpectedMethodTokensWithStaticAndFinalModifiers()
    {
        $method = self::parseTestCaseSource(__METHOD__)
            ->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $expected = array(
            new Token(Tokens::T_STATIC, 'static', 7, 7, 5, 10),
            new Token(Tokens::T_PUBLIC, 'public', 7, 7, 12, 17),
            new Token(Tokens::T_FINAL, 'final', 7, 7, 19, 23),
            new Token(Tokens::T_FUNCTION, 'function', 7, 7, 25, 32),
            new Token(Tokens::T_STRING, 'foo', 7, 7, 34, 36),
            new Token(Tokens::T_PARENTHESIS_OPEN, '(', 7, 7, 37, 37),
            new Token(Tokens::T_PARENTHESIS_CLOSE, ')', 7, 7, 38, 38),
            new Token(Tokens::T_CURLY_BRACE_OPEN, '{', 8, 8, 5, 5),
            new Token(Tokens::T_RETURN, 'return', 9, 9, 9, 14),
            new Token(Tokens::T_FALSE, 'false', 9, 9, 16, 20),
            new Token(Tokens::T_SEMICOLON, ';', 9, 9, 21, 21),
            new Token(Tokens::T_CURLY_BRACE_CLOSE, '}', 10, 10, 5, 5),
        );

        $this->assertEquals($expected, $method->getTokens());
    }

    /**
     * Tests that the method uses the start line of the first token.
     *
     * @return void
     * @covers \PDepend\Source\AST\ASTMethod
     */
    public function testMethodContainsStartLineOfFirstToken()
    {
        $method = self::parseTestCaseSource(__METHOD__)
            ->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $tokens = $method->getTokens();
        $token  = reset($tokens);

        $this->assertEquals($token->startLine, $method->getStartLine());
    }

    /**
     * Tests that the method uses the end line of the last token.
     *
     * @return void
     * @covers \PDepend\Source\AST\ASTMethod
     */
    public function testMethodContainsEndLineOfLastToken()
    {
        $method = self::parseTestCaseSource(__METHOD__)
            ->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $tokens = $method->getTokens();
        $token  = end($tokens);

        $this->assertEquals($token->endLine, $method->getEndLine());
    }

    /**
     * Tests that the parser stores the expected class tokens.
     *
     * @return void
     * @covers \PDepend\Source\AST\ASTClass
     */
    public function testParserStoresExpectedClassTokens()
    {
        $class = self::parseTestCaseSource(__METHOD__)
            ->current()
            ->getClasses()
            ->current();

        $expected = array(
            new Token(Tokens::T_CLASS, 'class', 2, 2, 1, 5),
            new Token(Tokens::T_STRING, 'Foo', 2, 2, 7, 9),
            new Token(Tokens::T_CURLY_BRACE_OPEN, '{', 3, 3, 1, 1),
            new Token(Tokens::T_PUBLIC, 'public', 4, 4, 5, 10),
            new Token(Tokens::T_FUNCTION, 'function', 4, 4, 12, 19),
            new Token(Tokens::T_STRING, 'bar', 4, 4, 21, 23),
            new Token(Tokens::T_PARENTHESIS_OPEN, '(', 4, 4, 24, 24),
            new Token(Tokens::T_PARENTHESIS_CLOSE, ')', 4, 4, 25, 25),
            new Token(Tokens::T_CURLY_BRACE_OPEN, '{', 4, 4, 27, 27),
            new Token(Tokens::T_CURLY_BRACE_CLOSE, '}', 4, 4, 28, 28),
            new Token(Tokens::T_CURLY_BRACE_CLOSE, '}', 5, 5, 1, 1),
        );

        $this->assertEquals($expected, $class->getTokens());
    }

    /**
     * Tests that the parser stores the expected class tokens.
     *
     * @return void
     * @covers \PDepend\Source\AST\ASTClass
     */
    public function testParserStoresExpectedClassTokensWithFinalModifier()
    {
        $class = self::parseTestCaseSource(__METHOD__)
            ->current()
            ->getClasses()
            ->current();

        $expected = array(
            new Token(Tokens::T_FINAL, 'final', 2, 2, 1, 5),
            new Token(Tokens::T_CLASS, 'class', 2, 2, 7, 11),
            new Token(Tokens::T_STRING, 'Foo', 2, 2, 13, 15),
            new Token(Tokens::T_CURLY_BRACE_OPEN, '{', 3, 3, 1, 1),
            new Token(Tokens::T_PUBLIC, 'public', 4, 4, 5, 10),
            new Token(Tokens::T_FUNCTION, 'function', 4, 4, 12, 19),
            new Token(Tokens::T_STRING, 'bar', 4, 4, 21, 23),
            new Token(Tokens::T_PARENTHESIS_OPEN, '(', 4, 4, 24, 24),
            new Token(Tokens::T_PARENTHESIS_CLOSE, ')', 4, 4, 25, 25),
            new Token(Tokens::T_CURLY_BRACE_OPEN, '{', 4, 4, 27, 27),
            new Token(Tokens::T_CURLY_BRACE_CLOSE, '}', 4, 4, 28, 28),
            new Token(Tokens::T_CURLY_BRACE_CLOSE, '}', 5, 5, 1, 1),
        );

        $this->assertEquals($expected, $class->getTokens());
    }

    /**
     * Tests that the parser stores the expected class tokens.
     *
     * @return void
     * @covers \PDepend\Source\AST\ASTClass
     */
    public function testParserStoresExpectedClassTokensWithAbstractModifier()
    {
        $class = self::parseTestCaseSource(__METHOD__)
            ->current()
            ->getClasses()
            ->current();

        $expected = array(
            new Token(Tokens::T_ABSTRACT, 'abstract', 2, 2, 1, 8),
            new Token(Tokens::T_CLASS, 'class', 2, 2, 10, 14),
            new Token(Tokens::T_STRING, 'Foo', 2, 2, 16, 18),
            new Token(Tokens::T_CURLY_BRACE_OPEN, '{', 3, 3, 1, 1),
            new Token(Tokens::T_PUBLIC, 'public', 4, 4, 5, 10),
            new Token(Tokens::T_FUNCTION, 'function', 4, 4, 12, 19),
            new Token(Tokens::T_STRING, 'bar', 4, 4, 21, 23),
            new Token(Tokens::T_PARENTHESIS_OPEN, '(', 4, 4, 24, 24),
            new Token(Tokens::T_PARENTHESIS_CLOSE, ')', 4, 4, 25, 25),
            new Token(Tokens::T_CURLY_BRACE_OPEN, '{', 4, 4, 27, 27),
            new Token(Tokens::T_CURLY_BRACE_CLOSE, '}', 4, 4, 28, 28),
            new Token(Tokens::T_CURLY_BRACE_CLOSE, '}', 5, 5, 1, 1),
        );

        $this->assertEquals($expected, $class->getTokens());
    }

    /**
     * Tests that the parser stores the expected interface tokens.
     *
     * @return void
     * @covers \PDepend\Source\AST\ASTInterface
     */
    public function testParserStoresExpectedInterfaceTokens()
    {
        $interface = self::parseTestCaseSource(__METHOD__)
            ->current()
            ->getInterfaces()
            ->current();

        $expected = array(
            new Token(Tokens::T_INTERFACE, 'interface', 2, 2, 1, 9),
            new Token(Tokens::T_STRING, 'Foo', 2, 2, 11, 13),
            new Token(Tokens::T_CURLY_BRACE_OPEN, '{', 3, 3, 1, 1),
            new Token(Tokens::T_PUBLIC, 'public', 4, 4, 5, 10),
            new Token(Tokens::T_FUNCTION, 'function', 4, 4, 12, 19),
            new Token(Tokens::T_STRING, 'bar', 4, 4, 21, 23),
            new Token(Tokens::T_PARENTHESIS_OPEN, '(', 4, 4, 24, 24),
            new Token(Tokens::T_PARENTHESIS_CLOSE, ')', 4, 4, 25, 25),
            new Token(Tokens::T_SEMICOLON, ';', 4, 4, 26, 26),
            new Token(Tokens::T_CURLY_BRACE_CLOSE, '}', 5, 5, 1, 1),
        );

        $this->assertEquals($expected, $interface->getTokens());
    }
}
