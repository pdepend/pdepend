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
use PDepend\Source\AST\ASTCallable;
use PDepend\Source\AST\ASTCatchStatement;
use PDepend\Source\AST\ASTConstant;
use PDepend\Source\AST\ASTFormalParameter;
use PDepend\Source\AST\ASTIdentifier;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\AST\ASTScalarType;
use PDepend\Source\AST\ASTType;
use PDepend\Source\AST\ASTUnionType;
use PDepend\Source\AST\State;
use PDepend\Source\Parser\ParserException;
use PDepend\Source\Parser\UnexpectedTokenException;
use PDepend\Source\Tokenizer\Token;
use PDepend\Source\Tokenizer\Tokens;

/**
 * Concrete parser implementation that supports features up to PHP version 8.0.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @since 2.9
 */
abstract class PHPParserVersion80 extends PHPParserVersion74
{
    protected $possiblePropertyTypes = array(
        Tokens::T_STRING,
        Tokens::T_ARRAY,
        Tokens::T_QUESTION_MARK,
        Tokens::T_BACKSLASH,
        Tokens::T_CALLABLE,
        Tokens::T_SELF,
        Tokens::T_NULL,
        Tokens::T_FALSE,
    );

    /**
     * Will return <b>true</b> if the given <b>$tokenType</b> is a valid class
     * name part.
     *
     * @param int $tokenType The type of a parsed token.
     *
     * @return bool
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
            case Tokens::T_CALLABLE:
            case Tokens::T_INSTEADOF:
            case Tokens::T_NAMESPACE:
            case Tokens::T_READONLY:
                return true;
        }

        return false;
    }

    protected function isTypeHint($tokenType)
    {
        switch ($tokenType) {
            case Tokens::T_NULL:
            case Tokens::T_FALSE:
            case Tokens::T_STATIC:
                return true;
            default:
                return parent::isTypeHint($tokenType);
        }
    }

    /**
     * This method will be called when the base parser cannot handle an expression
     * in the base version. In this method you can implement version specific
     * expressions.
     *
     * @throws UnexpectedTokenException
     *
     * @return ASTNode
     */
    protected function parseOptionalExpressionForVersion()
    {
        return $this->parseExpressionVersion80()
            ?: parent::parseOptionalExpressionForVersion();
    }

    /**
     * In this method we implement parsing of PHP 8.0 specific expressions.
     *
     * @return ?ASTNode
     */
    protected function parseExpressionVersion80()
    {
        $this->consumeComments();
        $nextTokenType = $this->tokenizer->peek();

        switch ($nextTokenType) {
            case Tokens::T_NULLSAFE_OBJECT_OPERATOR:
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
     * This method parse a formal parameter and all the stuff that may be allowed
     * before it according to the PHP level (type hint, passing by reference, property promotion).
     *
     * @return ASTFormalParameter|ASTNode
     */
    protected function parseFormalParameterOrPrefix(ASTCallable $callable)
    {
        $modifier = 0;

        if ($callable instanceof ASTMethod && $callable->getName() === '__construct') {
            $modifier = $this->parseConstructFormalParameterModifiers();
        }

        $parameter = parent::parseFormalParameterOrPrefix($callable);

        if ($modifier && $parameter instanceof ASTFormalParameter) {
            $parameter->setModifiers($modifier);
        }

        return $parameter;
    }

    /**
     * Parse the modifiers for construct parameter
     *
     * @return int
     */
    protected function parseConstructFormalParameterModifiers()
    {
        static $states = array(
            Tokens::T_PUBLIC    => State::IS_PUBLIC,
            Tokens::T_PROTECTED => State::IS_PROTECTED,
            Tokens::T_PRIVATE   => State::IS_PRIVATE,
        );

        $modifier = 0;
        $token = $this->tokenizer->peek();

        if (isset($states[$token])) {
            $modifier |= $states[$token];
            $next = $this->tokenizer->next();
            assert($next instanceof Token);
            $this->tokenStack->add($next);
        }

        return $modifier;
    }

    protected function parseArgumentExpression()
    {
        if ($this->tokenizer->peekNext() === Tokens::T_COLON) {
            $token = $this->tokenizer->currentToken();
            $image = $token->image;

            // Variable RegExp from https://www.php.net/manual/en/language.variables.basics.php
            if (preg_match('/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/', $image)) {
                $this->consumeToken($token->type);

                return $this->builder->buildAstConstant($image);
            }
        }

        return parent::parseArgumentExpression();
    }

    protected function parseConstantArgument(ASTConstant $constant, ASTArguments $arguments)
    {
        if ($this->tokenizer->peek() === Tokens::T_COLON) {
            $token = $this->tokenizer->next();
            assert($token instanceof Token);
            $this->tokenStack->add($token);

            return $this->builder->buildAstNamedArgument(
                $constant->getImage(),
                $this->parseOptionalExpression()
            );
        }

        return $constant;
    }

    protected function parseFunctionPostfix(ASTNode $node)
    {
        if (!($node instanceof ASTIdentifier) || $node->getImageWithoutNamespace() !== 'match') {
            return parent::parseFunctionPostfix($node);
        }

        $image = $this->extractPostfixImage($node);

        $function = $this->builder->buildAstFunctionPostfix($image);
        $function->addChild($node);

        $this->consumeComments();

        $this->tokenStack->push();

        $function->addChild(
            $this->parseArgumentsParenthesesContent(
                $this->builder->buildAstMatchArgument()
            )
        );

        $this->consumeComments();
        $this->consumeToken(Tokens::T_CURLY_BRACE_OPEN);

        $matchBlock = $this->builder->buildAstMatchBlock();

        while ($this->tokenizer->peek() !== Tokens::T_CURLY_BRACE_CLOSE) {
            $matchBlock->addChild($this->parseMatchEntry());

            $this->consumeComments();

            if ($this->tokenizer->peek() === Tokens::T_COMMA) {
                $this->consumeToken(Tokens::T_COMMA);
                $this->consumeComments();
            }
        }

        $this->consumeToken(Tokens::T_CURLY_BRACE_CLOSE);

        $function->addChild($matchBlock);

        return $function;
    }

    /**
     * @return ASTType
     */
    protected function parseEndReturnTypeHint()
    {
        return $this->parseTypeHint();
    }

    /**
     * @return ASTType
     */
    protected function parseSingleTypeHint()
    {
        $this->consumeComments();

        switch ($this->tokenizer->peek()) {
            case Tokens::T_ARRAY:
                $type = $this->parseArrayType();
                break;
            case Tokens::T_SELF:
                $type = $this->parseSelfType();
                break;
            case Tokens::T_PARENT:
                $type = $this->parseParentType();
                break;
            case Tokens::T_STATIC:
                $type = $this->parseStaticType();
                break;
            case Tokens::T_NULL:
                $type = new ASTScalarType('null');
                $token = $this->tokenizer->next();
                assert($token instanceof Token);
                $this->tokenStack->add($token);
                break;
            case Tokens::T_FALSE:
                $type = new ASTScalarType('false');
                $token = $this->tokenizer->next();
                assert($token instanceof Token);
                $this->tokenStack->add($token);
                break;
            default:
                $type = parent::parseTypeHint();
                break;
        }

        $this->consumeComments();

        return $type;
    }

    /**
     * @param ASTType $firstType
     *
     * @return ASTUnionType
     */
    protected function parseUnionTypeHint($firstType)
    {
        $types = array($firstType);

        while ($this->tokenizer->peek() === Tokens::T_BITWISE_OR) {
            $token = $this->tokenizer->next();
            assert($token instanceof Token);
            $this->tokenStack->add($token);
            $types[] = $this->parseSingleTypeHint();
        }

        $unionType = $this->builder->buildAstUnionType();
        foreach ($types as $type) {
            $unionType->addChild($type);
        }

        return $unionType;
    }

    /**
     * @param ASTType $type
     *
     * @return ASTType
     */
    protected function parseTypeHintCombination($type)
    {
        if ($this->tokenizer->peek() === Tokens::T_BITWISE_OR) {
            return $this->parseUnionTypeHint($type);
        }

        return $type;
    }

    /**
     * @param ASTNode $type
     *
     * @return bool
     */
    protected function canNotBeStandAloneType($type)
    {
        return $type instanceof ASTScalarType && ($type->isFalse() || $type->isNull());
    }

    /**
     * @return ASTType
     */
    protected function parseTypeHint()
    {
        $this->consumeComments();
        $token = $this->tokenizer->currentToken();
        $type = $this->parseSingleTypeHint();

        $type = $this->parseTypeHintCombination($type);

        if ($this->canNotBeStandAloneType($type)) {
            throw new ParserException(
                $type->getImage() . ' can not be used as a standalone type',
                0,
                $this->getUnexpectedTokenException($token)
            );
        }

        return $type;
    }

    /**
     * This method parses assigned variable in catch statement.
     *
     * @param ASTCatchStatement $stmt The owning catch statement.
     *
     * @return void
     */
    protected function parseCatchVariable(ASTCatchStatement $stmt)
    {
        if ($this->tokenizer->peek() === Tokens::T_VARIABLE) {
            parent::parseCatchVariable($stmt);
        }
    }

    protected function allowTrailingCommaInClosureUseList()
    {
        return true;
    }

    /**
     * use of trailing comma in formal parameters list is allowed since PHP 8.0
     * example function foo(string $bar, int $baz,)
     *
     * @return bool
     */
    protected function allowTrailingCommaInFormalParametersList()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function isNextTokenObjectOperator()
    {
        return in_array($this->tokenizer->peek(), array(
            Tokens::T_OBJECT_OPERATOR,
            Tokens::T_NULLSAFE_OBJECT_OPERATOR,
        ), true);
    }

    protected function consumeObjectOperatorToken()
    {
        return $this->consumeToken(
            $this->tokenizer->peek() === Tokens::T_NULLSAFE_OBJECT_OPERATOR
                ? Tokens::T_NULLSAFE_OBJECT_OPERATOR
                : Tokens::T_OBJECT_OPERATOR
        );
    }

    protected function parseThrowExpression()
    {
        if ($this->tokenizer->peek() === Tokens::T_THROW) {
            return $this->parseThrowStatement(array(
                Tokens::T_SEMICOLON,
                Tokens::T_COMMA,
                Tokens::T_COLON,
                Tokens::T_PARENTHESIS_CLOSE,
                Tokens::T_SQUARED_BRACKET_CLOSE
            ));
        }

        throw $this->getUnexpectedNextTokenException();
    }
}
