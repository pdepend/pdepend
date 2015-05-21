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
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since     0.9.20
 */

namespace PDepend\Source\Language\PHP;

use PDepend\Source\AST\ASTValue;
use PDepend\Source\Parser\TokenStreamEndException;
use PDepend\Source\Parser\UnexpectedTokenException;
use PDepend\Source\Tokenizer\Tokenizer;
use PDepend\Source\Tokenizer\Tokens;

/**
 * Concrete parser implementation that is very tolerant and accepts language
 * constructs and keywords that are reserved in newer php versions, but not in
 * older versions.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since     0.9.20
 */
class PHPParserGeneric extends AbstractPHPParser
{
    /**
     * Tests if the given token type is a reserved keyword in the supported PHP
     * version.
     *
     * @param  $tokenType
     * @return boolean
     * @since  1.1.1
     */
    protected function isKeyword($tokenType)
    {
        switch ($tokenType) {
            case Tokens::T_CLASS:
            case Tokens::T_INTERFACE:
                return true;
        }
        return false;
    }

    /**
     * Will return <b>true</b> if the given <b>$tokenType</b> is a valid class
     * name part.
     *
     * @param integer $tokenType The type of a parsed token.
     *
     * @return boolean
     * @since  0.10.6
     */
    protected function isClassName($tokenType)
    {
        switch ($tokenType) {
            case Tokens::T_DIR:
            case Tokens::T_USE:
            case Tokens::T_GOTO:
            case Tokens::T_NULL:
            case Tokens::T_NS_C:
            case Tokens::T_TRUE:
            case Tokens::T_CLONE:
            case Tokens::T_FALSE:
            case Tokens::T_TRAIT:
            case Tokens::T_STRING:
            case Tokens::T_TRAIT_C:
            case Tokens::T_INSTEADOF:
            case Tokens::T_NAMESPACE:
                return true;
        }
        return false;
    }

    /**
     * Parses a valid class or interface name and returns the image of the parsed
     * token.
     *
     * @return string
     * @throws \PDepend\Source\Parser\TokenStreamEndException
     * @throws \PDepend\Source\Parser\UnexpectedTokenException
     */
    protected function parseClassName()
    {
        $type = $this->tokenizer->peek();
        
        if ($this->isClassName($type)) {
            return $this->consumeToken($type)->image;
        } elseif ($type === Tokenizer::T_EOF) {
            throw new TokenStreamEndException($this->tokenizer);
        }
        
        throw new UnexpectedTokenException(
            $this->tokenizer->next(),
            $this->tokenizer->getSourceFile()
        );
    }

    /**
     * Parses a function name from the given tokenizer and returns the string
     * literal representing the function name. If no valid token exists in the
     * token stream, this method will throw an exception.
     *
     * @return string
     * @throws \PDepend\Source\Parser\UnexpectedTokenException
     * @throws \PDepend\Source\Parser\TokenStreamEndException
     */
    public function parseFunctionName()
    {
        $type = $this->tokenizer->peek();
        switch ($type) {
            case Tokens::T_CLONE:
            case Tokens::T_STRING:
            case Tokens::T_USE:
            case Tokens::T_GOTO:
            case Tokens::T_NULL:
            case Tokens::T_SELF:
            case Tokens::T_TRUE:
            case Tokens::T_FALSE:
            case Tokens::T_TRAIT:
            case Tokens::T_INSTEADOF:
            case Tokens::T_NAMESPACE:
            case Tokens::T_DIR:
            case Tokens::T_NS_C:
            case Tokens::T_PARENT:
            case Tokens::T_TRAIT_C:
                return $this->consumeToken($type)->image;
            case Tokenizer::T_EOF:
                throw new TokenStreamEndException($this->tokenizer);
        }
        throw new UnexpectedTokenException(
            $this->tokenizer->next(),
            $this->tokenizer->getSourceFile()
        );
    }

    /**
     * Tests if the given token type is a valid formal parameter in the supported
     * PHP version.
     *
     * @param integer $tokenType Numerical token identifier.
     *
     * @return boolean
     * @since  1.0.0
     */
    protected function isFormalParameterTypeHint($tokenType)
    {
        switch ($tokenType) {
            case Tokens::T_STRING:
            case Tokens::T_CALLABLE:
            case Tokens::T_BACKSLASH:
            case Tokens::T_NAMESPACE:
                return true;
        }
        return false;
    }

    /**
     * Parses a formal parameter type hint that is valid in the supported PHP
     * version.
     *
     * @return \PDepend\Source\AST\ASTNode
     * @since  1.0.0
     */
    protected function parseFormalParameterTypeHint()
    {
        switch ($this->tokenizer->peek()) {
            case Tokens::T_CALLABLE:
                $this->consumeToken(Tokens::T_CALLABLE);
                $type = $this->builder->buildAstTypeCallable();
                break;
            case Tokens::T_STRING:
            case Tokens::T_BACKSLASH:
            case Tokens::T_NAMESPACE:
                $name = $this->parseQualifiedName();

                if (0 === strcasecmp('callable', $name)) {
                    $type = $this->builder->buildAstTypeCallable();
                } else {
                    $type = $this->builder->buildAstClassOrInterfaceReference($name);
                }
                break;
        }
        return $type;
    }

    /**
     * Parses an integer value.
     *
     * @return \PDepend\Source\AST\ASTLiteral
     * @throws \PDepend\Source\Parser\UnexpectedTokenException
     * @since  1.0.0
     */
    protected function parseIntegerNumber()
    {
        $token = $this->consumeToken(Tokens::T_LNUMBER);

        if ('0' === $token->image) {
            if (Tokens::T_STRING === $this->tokenizer->peek()) {
                $token1 = $this->consumeToken(Tokens::T_STRING);
                if (preg_match('(^b[01]+$)', $token1->image)) {
                    $token->image     = $token->image . $token1->image;
                    $token->endLine   = $token1->endLine;
                    $token->endColumn = $token1->endColumn;
                } else {
                    throw new UnexpectedTokenException(
                        $token1,
                        $this->tokenizer->getSourceFile()
                    );
                }
            }
        }

        $literal = $this->builder->buildAstLiteral($token->image);
        $literal->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );

        return $literal;
    }

    /**
     * This method parses a PHP version specific identifier for method and
     * property postfix expressions.
     *
     * @return \PDepend\Source\AST\ASTNode
     * @since  1.0.0
     */
    protected function parsePostfixIdentifier()
    {
        switch ($this->tokenizer->peek()) {
            case Tokens::T_STRING:
                $node = $this->parseLiteral();
                break;
            case Tokens::T_CURLY_BRACE_OPEN:
                $node = $this->parseCompoundExpression();
                break;
            default:
                $node = $this->parseCompoundVariableOrVariableVariableOrVariable();
                break;
        }
        return $this->parseOptionalIndexExpression($node);
    }

    /**
     * Implements some quirks and hacks to support php here- and now-doc for
     * PHP 5.2.x versions :/
     *
     * @return \PDepend\Source\AST\ASTHeredoc
     * @since  1.0.0
     */
    protected function parseHeredoc()
    {
        $heredoc = parent::parseHeredoc();
        if (version_compare(phpversion(), "5.3.0alpha") >= 0) {
            return $heredoc;
        }

        // Consume dangling semicolon
        $this->tokenizer->next();

        $token = $this->tokenizer->next();
        preg_match('(/\*(\'|")\*/)', $token->image, $match);

        return $heredoc;
    }

    /**
     * Tests if the next token is a valid array start delimiter in the supported
     * PHP version.
     *
     * @return boolean
     * @since  1.0.0
     */
    protected function isArrayStartDelimiter()
    {
        switch ($this->tokenizer->peek()) {
            case Tokens::T_ARRAY:
            case Tokens::T_SQUARED_BRACKET_OPEN:
                return true;
        }
        return false;
    }

    /**
     * Parses a php array declaration.
     *
     * @param  \PDepend\Source\AST\ASTArray $array
     * @param  boolean                      $static
     * @return \PDepend\Source\AST\ASTArray
     * @since  1.0.0
     */
    protected function parseArray(\PDepend\Source\AST\ASTArray $array, $static = false)
    {
        switch ($this->tokenizer->peek()) {
            case Tokens::T_ARRAY:
                $this->consumeToken(Tokens::T_ARRAY);
                $this->consumeComments();
                $this->consumeToken(Tokens::T_PARENTHESIS_OPEN);
                $this->parseArrayElements($array, Tokens::T_PARENTHESIS_CLOSE, $static);
                $this->consumeToken(Tokens::T_PARENTHESIS_CLOSE);
                break;
            default:
                $this->consumeToken(Tokens::T_SQUARED_BRACKET_OPEN);
                $this->parseArrayElements($array, Tokens::T_SQUARED_BRACKET_CLOSE, $static);
                $this->consumeToken(Tokens::T_SQUARED_BRACKET_CLOSE);
                break;
        }
        return $array;
    }

    /**
     * Parses additional static values that are valid in the supported php version.
     *
     * @param  \PDepend\Source\AST\ASTValue $value
     * @return \PDepend\Source\AST\ASTValue
     * @throws \PDepend\Source\Parser\UnexpectedTokenException
     * @todo   Handle shift left/right expressions in ASTValue
     */
    protected function parseStaticValueVersionSpecific(ASTValue $value)
    {
        switch ($this->tokenizer->peek()) {
            case Tokens::T_SL:
                $shift = $this->parseShiftLeftExpression();
                $this->parseStaticValue();
                break;
            case Tokens::T_SR:
                $shift = $this->parseShiftRightExpression();
                $this->parseStaticValue();
                break;
            default:
                throw new UnexpectedTokenException(
                    $this->tokenizer->next(),
                    $this->tokenizer->getSourceFile()
                );
        }

        return $value;
    }

    /**
     * This method will parse a formal parameter. A formal parameter is at least
     * a variable name, but can also contain a default parameter value.
     *
     * <code>
     * //               --  -------
     * function foo(Bar $x, $y = 42) {}
     * //               --  -------
     * </code>
     *
     * @return \PDepend\Source\AST\ASTFormalParameter
     * @since 2.0.7
     */
    protected function parseFormalParameter()
    {
        $parameter = $this->builder->buildAstFormalParameter();

        if (Tokens::T_ELLIPSIS === $this->tokenizer->peek()) {
            $this->consumeToken(Tokens::T_ELLIPSIS);
            $this->consumeComments();

            $parameter->setVariableArgList();
        }

        $parameter->addChild($this->parseVariableDeclarator());

        return $parameter;
    }
}
