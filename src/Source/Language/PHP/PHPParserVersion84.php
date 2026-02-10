<?php

/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2025 Oliver Eglseder <oliver.eglseder@co-stack.com>.
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
 * @copyright 2025 Oliver Eglseder <oliver.eglseder@co-stack.com>. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @since 3.0
 */

namespace PDepend\Source\Language\PHP;

use PDepend\Source\AST\AbstractASTClassOrInterface;
use PDepend\Source\AST\AbstractASTNode;
use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\AST\ASTFormalParameter;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\AST\State;
use PDepend\Source\Parser\UnexpectedTokenException;
use PDepend\Source\Tokenizer\Token;
use PDepend\Source\Tokenizer\Tokens;

/**
 * Concrete parser implementation that supports features up to PHP version 8.4
 *
 * @copyright 2025 Oliver Eglseder <oliver.eglseder@co-stack.com>. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @since 3.0
 */
abstract class PHPParserVersion84 extends PHPParserVersion83
{
    /**
     * This method will be called when the base parser cannot handle an expression
     * in the base version. In this method you can implement version specific
     * expressions.
     *
     * @throws UnexpectedTokenException
     */
    protected function parseOptionalExpressionForVersion(): ?ASTNode
    {
        return $this->parseExpressionVersion84()
            ?: parent::parseOptionalExpressionForVersion();
    }

    /**
     * In this method we implement parsing of PHP 8.4 specific expressions.
     */
    protected function parseExpressionVersion84(): ?ASTNode
    {
        $this->consumeComments();
        $nextTokenType = $this->tokenizer->peek();

        if ($nextTokenType === Tokens::T_OBJECT_OPERATOR) {
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

    protected function parseConstructFormalParameterModifiers(): int
    {
        /** @var array<int, int> */
        static $states = [
            Tokens::T_PUBLIC => State::IS_PUBLIC,
            Tokens::T_PROTECTED => State::IS_PROTECTED,
            Tokens::T_PRIVATE => State::IS_PRIVATE,
            Tokens::T_PRIVATE_SET => State::IS_PRIVATE_SET,
            Tokens::T_PROTECTED_SET => State::IS_PROTECTED_SET,
            Tokens::T_PUBLIC_SET => State::IS_PUBLIC,
        ];

        $modifier = $this->checkReadonlyToken();
        $token = $this->tokenizer->peek();

        if (isset($states[$token])) {
            $modifier |= $states[$token];
            $next = $this->tokenizer->next();
            assert($next instanceof Token);
            $this->tokenStack->add($next);

            $token = $this->tokenizer->peek();

            if (isset($states[$token])) {
                $modifier |= $states[$token];
                $next = $this->tokenizer->next();
                assert($next instanceof Token);
                $this->tokenStack->add($next);
            }
        }

        return $modifier | $this->checkReadonlyToken();
    }

    protected function parseUnknownDeclaration(int $tokenType, int $modifiers): AbstractASTNode
    {
        // Handle Asymmetric Property Visibility
        if (in_array($tokenType, [Tokens::T_PRIVATE_SET, Tokens::T_PROTECTED_SET, Tokens::T_PUBLIC_SET], true)) {
            switch ($tokenType) {
                case Tokens::T_PRIVATE_SET:
                    $modifiers |= State::IS_PRIVATE_SET;

                    break;

                case Tokens::T_PROTECTED_SET:
                    $modifiers |= State::IS_PROTECTED_SET;

                    break;

                case Tokens::T_PUBLIC_SET:
                    $modifiers |= State::IS_PUBLIC;

                    break;
            }

            $this->consumeToken($tokenType);
            $this->consumeComments();

            $tokenType = $this->tokenizer->peek();
        }

        return parent::parseUnknownDeclaration($tokenType, $modifiers);
    }

    protected function parseUnknownTypeBody(
        int $tokenType,
        AbstractASTClassOrInterface $classOrInterface,
        int $modifiers
    ): void {
        if (in_array($tokenType, [Tokens::T_PRIVATE_SET, Tokens::T_PROTECTED_SET, Tokens::T_PUBLIC_SET], true)) {
            $methodOrProperty = $this->parseUnknownDeclaration($tokenType, $modifiers);
            $classOrInterface->addChild($methodOrProperty);
            $this->reset();

            return;
        }

        parent::parseUnknownTypeBody($tokenType, $classOrInterface, $modifiers);
    }

    protected function isStaticValueTerminator(int $tokenType): bool
    {
        return $tokenType === Tokens::T_CURLY_BRACE_OPEN
            || parent::isStaticValueTerminator($tokenType);
    }

    protected function isStaticValueVersionSpecificTerminator(int $tokenType): bool
    {
        return $tokenType === Tokens::T_CURLY_BRACE_OPEN
            || parent::isStaticValueVersionSpecificTerminator($tokenType);
    }

    protected function parseFieldTermination(int $tokenType, ASTFieldDeclaration $declaration): void
    {
        if ($tokenType === Tokens::T_SEMICOLON) {
            $this->consumeToken(Tokens::T_SEMICOLON);

            return;
        }

        $this->parsePropertyHook($tokenType, $declaration);
    }

    protected function parsePromotedParameterExtensions(ASTFormalParameter $parameter): void
    {
        $tokenType = $this->tokenizer->peek();
        if ($tokenType === Tokens::T_CURLY_BRACE_OPEN) {
            $this->parsePropertyHook($tokenType, $parameter);
        }
    }

    private function parsePropertyHook(int $tokenType, ASTFieldDeclaration|ASTFormalParameter $declaration): void
    {
        $this->consumeToken(Tokens::T_CURLY_BRACE_OPEN);

        $this->consumeComments();

        $tokenType = $this->tokenizer->peek();

        while ($tokenType === Tokens::T_ATTRIBUTE) {
            $this->parseAttributeExpression();
            $this->consumeComments();
            $tokenType = $this->tokenizer->peek();
        }

        while ($tokenType === Tokens::T_STRING || $tokenType === Tokens::T_FINAL) {
            $modifiers = State::IS_PUBLIC;

            if ($tokenType === Tokens::T_FINAL) {
                $modifiers |= State::IS_FINAL;
                $this->consumeToken(Tokens::T_FINAL);
            }

            $token = $this->tokenizer->currentToken();
            assert($token instanceof Token);
            $hook = $this->builder->buildPropertyHook($token->image);
            $this->attachAttributes($hook);

            if ($declaration->isProtected()) {
                $modifiers = ($modifiers & ~State::IS_PUBLIC & ~State::IS_PRIVATE) | State::IS_PROTECTED;
            } elseif ($declaration->isPrivate()) {
                $modifiers = ($modifiers & ~State::IS_PUBLIC & ~State::IS_PROTECTED) | State::IS_PRIVATE;
            }
            if ($token->image === 'set') {
                if ($declaration->isProtectedSet()) {
                    $modifiers = ($modifiers & ~State::IS_PUBLIC & ~State::IS_PRIVATE) | State::IS_PROTECTED;
                } elseif ($declaration->isPrivateSet()) {
                    $modifiers = ($modifiers & ~State::IS_PUBLIC & ~State::IS_PROTECTED) | State::IS_PRIVATE;
                }
            }

            $this->consumeToken(Tokens::T_STRING);

            $tokenType = $this->tokenizer->peek();

            if ($tokenType === Tokens::T_SEMICOLON) {
                $this->consumeToken(Tokens::T_SEMICOLON);
                $modifiers |= State::IS_ABSTRACT;
            } else {
                if ($tokenType === Tokens::T_PARENTHESIS_OPEN) {
                    $hook->addChild($this->parseFormalParameters($hook));
                    $tokenType = $this->tokenizer->peek();
                }

                if ($tokenType === Tokens::T_CURLY_BRACE_OPEN) {
                    $hook->addChild($this->parseScope());
                } else {
                    $hook->addChild(
                        $this->buildReturnStatement(
                            $this->consumeToken(Tokens::T_DOUBLE_ARROW)
                        )
                    );
                    $this->consumeToken(Tokens::T_SEMICOLON);
                }
            }

            $hook->setModifiers($modifiers);

            $declaration->addChild($hook);

            $tokenType = $this->tokenizer->peek();
        }

        $this->consumeToken(Tokens::T_CURLY_BRACE_CLOSE);
    }
}
