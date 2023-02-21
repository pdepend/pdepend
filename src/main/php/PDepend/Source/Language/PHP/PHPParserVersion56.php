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
 *
 * @since 2.3
 */

namespace PDepend\Source\Language\PHP;

use PDepend\Source\AST\ASTArguments;
use PDepend\Source\AST\ASTConstant;
use PDepend\Source\AST\ASTNamedArgument;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\AST\ASTValue;
use PDepend\Source\Parser\UnexpectedTokenException;
use PDepend\Source\Tokenizer\FullTokenizer;
use PDepend\Source\Tokenizer\Tokenizer;
use PDepend\Source\Tokenizer\Tokens;

/**
 * Concrete parser implementation that supports features up to PHP version 5.6.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @since 2.3
 */
abstract class PHPParserVersion56 extends PHPParserVersion55
{
    /**
     * Parses additional static values that are valid in the supported php version.
     *
     * @throws UnexpectedTokenException
     *
     * @return ASTValue|null
     */
    protected function parseStaticValueVersionSpecific(ASTValue $value)
    {
        $expressions = array();

        while (($tokenType = $this->tokenizer->peek()) != Tokenizer::T_EOF) {
            switch ($tokenType) {
                case Tokens::T_COMMA:
                case Tokens::T_CLOSE_TAG:
                case Tokens::T_COLON:
                case Tokens::T_DOUBLE_ARROW:
                case Tokens::T_END_HEREDOC:
                case Tokens::T_PARENTHESIS_CLOSE:
                case Tokens::T_SEMICOLON:
                case Tokens::T_SQUARED_BRACKET_CLOSE:
                    break 2;
                case Tokens::T_SELF:
                case Tokens::T_STRING:
                case Tokens::T_PARENT:
                case Tokens::T_STATIC:
                case Tokens::T_DOLLAR:
                case Tokens::T_VARIABLE:
                case Tokens::T_BACKSLASH:
                case Tokens::T_NAMESPACE:
                    $expressions[] = $this->parseVariableOrConstantOrPrimaryPrefix();
                    break;
                case ($this->isArrayStartDelimiter()):
                    $expressions[] = $this->doParseArray(true);
                    break;
                case Tokens::T_NULL:
                case Tokens::T_TRUE:
                case Tokens::T_FALSE:
                case Tokens::T_LNUMBER:
                case Tokens::T_DNUMBER:
                case Tokens::T_BACKTICK:
                case Tokens::T_DOUBLE_QUOTE:
                case Tokens::T_CONSTANT_ENCAPSED_STRING:
                    $expressions[] = $this->parseLiteralOrString();
                    break;
                case Tokens::T_QUESTION_MARK:
                    $expressions[] = $this->parseConditionalExpression();
                    break;
                case Tokens::T_BOOLEAN_AND:
                    $expressions[] = $this->parseBooleanAndExpression();
                    break;
                case Tokens::T_BOOLEAN_OR:
                    $expressions[] = $this->parseBooleanOrExpression();
                    break;
                case Tokens::T_LOGICAL_AND:
                    $expressions[] = $this->parseLogicalAndExpression();
                    break;
                case Tokens::T_LOGICAL_OR:
                    $expressions[] = $this->parseLogicalOrExpression();
                    break;
                case Tokens::T_LOGICAL_XOR:
                    $expressions[] = $this->parseLogicalXorExpression();
                    break;
                case Tokens::T_PARENTHESIS_OPEN:
                    $expressions[] = $this->parseParenthesisExpressionOrPrimaryPrefix();
                    break;
                case Tokens::T_START_HEREDOC:
                    $expressions[] = $this->parseHeredoc();
                    break;
                case Tokens::T_SL:
                    $expressions[] = $this->parseShiftLeftExpression();
                    break;
                case Tokens::T_SR:
                    $expressions[] = $this->parseShiftRightExpression();
                    break;
                case Tokens::T_ELLIPSIS:
                    $this->checkEllipsisInExpressionSupport();
                    // no break
                case Tokens::T_STRING_VARNAME: // TODO: Implement this
                case Tokens::T_PLUS: // TODO: Make this a arithmetic expression
                case Tokens::T_MINUS:
                case Tokens::T_MUL:
                case Tokens::T_DIV:
                case Tokens::T_MOD:
                case Tokens::T_POW:
                case Tokens::T_IS_EQUAL: // TODO: Implement compare expressions
                case Tokens::T_IS_NOT_EQUAL:
                case Tokens::T_IS_IDENTICAL:
                case Tokens::T_IS_NOT_IDENTICAL:
                case Tokens::T_BITWISE_OR:
                case Tokens::T_BITWISE_AND:
                case Tokens::T_BITWISE_NOT:
                case Tokens::T_BITWISE_XOR:
                case Tokens::T_IS_GREATER_OR_EQUAL:
                case Tokens::T_IS_SMALLER_OR_EQUAL:
                case Tokens::T_ANGLE_BRACKET_OPEN:
                case Tokens::T_ANGLE_BRACKET_CLOSE:
                case Tokens::T_EMPTY:
                case Tokens::T_CONCAT:
                    $token = $this->consumeToken($tokenType);

                    $expr = $this->builder->buildAstExpression($token->image);
                    $expr->configureLinesAndColumns(
                        $token->startLine,
                        $token->endLine,
                        $token->startColumn,
                        $token->endColumn
                    );

                    $expressions[] = $expr;
                    break;
                case Tokens::T_EQUAL:
                case Tokens::T_OR_EQUAL:
                case Tokens::T_SL_EQUAL:
                case Tokens::T_SR_EQUAL:
                case Tokens::T_AND_EQUAL:
                case Tokens::T_DIV_EQUAL:
                case Tokens::T_MOD_EQUAL:
                case Tokens::T_MUL_EQUAL:
                case Tokens::T_XOR_EQUAL:
                case Tokens::T_PLUS_EQUAL:
                case Tokens::T_MINUS_EQUAL:
                case Tokens::T_CONCAT_EQUAL:
                case Tokens::T_COALESCE_EQUAL:
                    $expressions[] = $this->parseAssignmentExpression(
                        array_pop($expressions)
                    );
                    break;
                case Tokens::T_DIR:
                case Tokens::T_FILE:
                case Tokens::T_LINE:
                case Tokens::T_NS_C:
                case Tokens::T_FUNC_C:
                case Tokens::T_CLASS_C:
                case Tokens::T_METHOD_C:
                    $expressions[] = $this->parseConstant();
                    break;
                // TODO: Handle comments here
                case Tokens::T_COMMENT:
                case Tokens::T_DOC_COMMENT:
                    $this->consumeToken($tokenType);
                    break;
                case Tokens::T_AT:
                case Tokens::T_EXCLAMATION_MARK:
                    $token = $this->consumeToken($tokenType);

                    $expr = $this->builder->buildAstUnaryExpression($token->image);
                    $expr->configureLinesAndColumns(
                        $token->startLine,
                        $token->endLine,
                        $token->startColumn,
                        $token->endColumn
                    );

                    $expressions[] = $expr;
                    break;
                default:
                    throw new UnexpectedTokenException(
                        $this->tokenizer->next(),
                        $this->tokenizer->getSourceFile()
                    );
            }
        }

        $expressions = $this->reduce($expressions);

        $count = count($expressions);
        if ($count == 0) {
            return null;
        } elseif ($count == 1) {
            // @todo ASTValue must be a valid node.
            $value->setValue($expressions[0]);

            return $value;
        }

        $expr = $this->builder->buildAstExpression();
        foreach ($expressions as $node) {
            $expr->addChild($node);
        }
        $expr->configureLinesAndColumns(
            $expressions[0]->getStartLine(),
            $expressions[$count - 1]->getEndLine(),
            $expressions[0]->getStartColumn(),
            $expressions[$count - 1]->getEndColumn()
        );

        // @todo ASTValue must be a valid node.
        $value->setValue($expr);

        return $value;
    }

    /**
     * Parses use declarations that are valid in the supported php version.
     *
     * @return void
     */
    protected function parseUseDeclarations()
    {
        // Consume use keyword
        $this->consumeToken(Tokens::T_USE);
        $this->consumeComments();

        // Consume const and function tokens
        $nextToken = $this->tokenizer->peek();
        switch ($nextToken) {
            case Tokens::T_CONST:
            case Tokens::T_FUNCTION:
                $this->consumeToken($nextToken);
        }

        // Parse all use declarations
        $this->parseUseDeclaration();
        $this->consumeComments();

        // Consume closing semicolon
        $this->consumeToken(Tokens::T_SEMICOLON);

        // Reset any previous state
        $this->reset();
    }

    /**
     * This method will be called when the base parser cannot handle an expression
     * in the base version. In this method you can implement version specific
     * expressions.
     *
     * @throws UnexpectedTokenException
     *
     * @return ASTNode
     *
     * @since 2.2
     */
    protected function parseOptionalExpressionForVersion()
    {
        if ($expression = $this->parseExpressionVersion56()) {
            return $expression;
        }
        return parent::parseOptionalExpressionForVersion();
    }

    /**
     * In this method we implement parsing of PHP 5.6 specific expressions.
     *
     * @return ASTNode|null
     *
     * @since 2.3
     */
    protected function parseExpressionVersion56()
    {
        $this->consumeComments();
        $nextTokenType = $this->tokenizer->peek();

        switch ($nextTokenType) {
            case Tokens::T_POW:
                $token = $this->consumeToken($nextTokenType);

                $expr = $this->builder->buildAstExpression($token->image);
                $expr->configureLinesAndColumns(
                    $token->startLine,
                    $token->endLine,
                    $token->startColumn,
                    $token->endColumn
                );

                return $expr;
        }

        return null;
    }

    /**
     * @return ASTConstant|ASTNamedArgument
     */
    protected function parseConstantArgument(ASTConstant $constant, ASTArguments $arguments)
    {
        return $constant;
    }

    /**
     * @return ASTArguments
     */
    protected function parseArgumentList(ASTArguments $arguments)
    {
        while (true) {
            $this->consumeComments();

            if (Tokens::T_ELLIPSIS === $this->tokenizer->peek()) {
                $this->consumeToken(Tokens::T_ELLIPSIS);
            }

            $expr = $this->parseArgumentExpression();

            if ($expr instanceof ASTConstant) {
                $expr = $this->parseConstantArgument($expr, $arguments);
            }

            if (!$expr || !$this->addChildToList($arguments, $expr)) {
                break;
            }
        }

        return $arguments;
    }

    /**
     * @return ASTNode|null
     */
    protected function parseArgumentExpression()
    {
        return $this->parseOptionalExpression();
    }

    /**
     * Parses the value of a php constant. By default this can be only static
     * values that were allowed in the oldest supported PHP version.
     *
     * @return ASTValue
     */
    protected function parseConstantDeclaratorValue()
    {
        if ($this->isFollowedByStaticValueOrStaticArray()) {
            return $this->parseVariableDefaultValue();
        }

        // Else it would be provided as ASTLiteral or expressions object.
        $value = new ASTValue();
        $value->setValue($this->parseOptionalExpression());

        return $value;
    }

    /**
     * Determines if the following expression can be stored as a static value.
     *
     * @return bool
     */
    protected function isFollowedByStaticValueOrStaticArray()
    {
        // If we can't anticipate, we should assume it can be a dynamic value
        if (!($this->tokenizer instanceof FullTokenizer)) {
            return false;
        }

        for ($i = 0; $type = $this->tokenizer->peekAt($i); $i++) {
            switch ($type) {
                case Tokens::T_COMMENT:
                case Tokens::T_DOC_COMMENT:
                case Tokens::T_ARRAY:
                case Tokens::T_SQUARED_BRACKET_OPEN:
                case Tokens::T_SQUARED_BRACKET_CLOSE:
                case Tokens::T_PARENTHESIS_OPEN:
                case Tokens::T_PARENTHESIS_CLOSE:
                case Tokens::T_COMMA:
                case Tokens::T_DOUBLE_ARROW:
                case Tokens::T_NULL:
                case Tokens::T_TRUE:
                case Tokens::T_FALSE:
                case Tokens::T_LNUMBER:
                case Tokens::T_DNUMBER:
                case Tokens::T_STRING:
                case Tokens::T_EQUAL:
                case Tokens::T_START_HEREDOC:
                case Tokens::T_END_HEREDOC:
                case Tokens::T_ENCAPSED_AND_WHITESPACE:
                    break;

                case Tokens::T_SEMICOLON:
                case Tokenizer::T_EOF:
                    return true;

                default:
                    return false;
            }
        }

        return false;
    }
}
