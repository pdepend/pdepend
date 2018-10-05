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
 * @since 2.3
 */

namespace PDepend\Source\Language\PHP;

use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\State;
use PDepend\Source\Parser\InvalidStateException;
use PDepend\Source\Parser\UnexpectedTokenException;
use PDepend\Source\Tokenizer\Tokenizer;
use PDepend\Source\Tokenizer\Tokens;

/**
 * Concrete parser implementation that supports features up to PHP version 7.1.
 *
 * TODO:
 * - void
 *   http://php.net/manual/en/migration71.new-features.php#migration71.new-features.void-functions
 * - Symmetric array destructuring
 *   http://php.net/manual/en/migration71.new-features.php#migration71.new-features.symmetric-array-destructuring
 * - Class constant visibility
 *   http://php.net/manual/en/migration71.new-features.php#migration71.new-features.class-constant-visibility
 * - Multi catch exception handling
 *   http://php.net/manual/en/migration71.new-features.php#migration71.new-features.mulit-catch-exception-handling
 * - see full list
 *   http://php.net/manual/en/migration71.php
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 2.4
 */
abstract class PHPParserVersion71 extends PHPParserVersion70
{
    /**
     * @return \PDepend\Source\AST\ASTType
     */
    protected function parseReturnTypeHint()
    {
        $this->consumeComments();

        $tokenType = $this->tokenizer->peek();
        if (Tokens::T_QUESTION_MARK === $tokenType) {
            $this->consumeToken(Tokens::T_QUESTION_MARK);
        }

        return parent::parseReturnTypeHint();
    }

    /**
     * This method parses a formal parameter in all it's variations.
     *
     * <code>
     * //                ------------
     * function traverse(Iterator $it) {}
     * //                ------------
     *
     * //                ---------
     * function traverse(array $ar) {}
     * //                ---------
     *
     * //                ---
     * function traverse(&$x) {}
     * //                ---
     * </code>
     *
     * @return \PDepend\Source\AST\ASTFormalParameter
     */
    protected function parseFormalParameterOrTypeHintOrByReference()
    {
        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();
        if ($tokenType === Tokens::T_QUESTION_MARK) {
            $this->consumeToken(Tokens::T_QUESTION_MARK);
        }

        return parent::parseFormalParameterOrTypeHintOrByReference();
    }

    /**
     * Parses a type hint that is valid in the supported PHP version.
     *
     * @return \PDepend\Source\AST\ASTNode
     */
    protected function parseTypeHint()
    {
        $tokenType = $this->tokenizer->peek();
        if (Tokens::T_QUESTION_MARK === $tokenType) {
            $this->consumeToken(Tokens::T_QUESTION_MARK);
        }

        return parent::parseTypeHint();
    }

    /**
     * Override this in later PHPParserVersions as necessary
     * @param integer $tokenType
     * @param integer $modifiers
     * @return \PDepend\Source\AST\ASTConstantDefinition;
     * @throws UnexpectedTokenException
     */
    protected function parseUnknownDeclaration($tokenType, $modifiers)
    {
        if ($tokenType == Tokens::T_CONST) {
            $definition = $this->parseConstantDefinition();
            $constantModifiers = $this->getModifiersForConstantDefinition($tokenType, $modifiers);
            $definition->setModifiers($constantModifiers);
            return $definition;
        }
        return parent::parseUnknownDeclaration($tokenType, $modifiers);
    }
    
    private function getModifiersForConstantDefinition($tokenType, $modifiers)
    {
        $allowed = State::IS_PUBLIC | State::IS_PROTECTED | State::IS_PRIVATE;
        $modifiers &= $allowed;
      
        if ($this->classOrInterface instanceof ASTInterface && ($modifiers & (State::IS_PROTECTED | State::IS_PRIVATE)) !== 0) {
            throw new InvalidStateException(
                $this->tokenizer->next()->startLine,
                (string) $this->compilationUnit,
                sprintf(
                   'Constant can\'t be declared private or protected in ' .
                    'interface "%s".',
                    $this->classOrInterface->getName()
                )
            );
        }
            
        return $modifiers;
    }
    
    /**
     * Tests if the given image is a PHP 7 type hint.
     *
     * @param string $image
     * @return boolean
     */
    protected function isScalarOrCallableTypeHint($image)
    {
        switch (strtolower($image)) {
            case 'iterable':
            case 'void':
                return true;
        }

        return parent::isScalarOrCallableTypeHint($image);
    }

    /**
     * Parses a scalar type hint or a callable type hint.
     *
     * @param string $image
     * @return \PDepend\Source\AST\ASTType
     */
    protected function parseScalarOrCallableTypeHint($image)
    {
        switch (strtolower($image)) {
            case 'void':
                return $this->builder->buildAstScalarType($image);
            case 'iterable':
                return $this->builder->buildAstTypeIterable();
        }

        return parent::parseScalarOrCallableTypeHint($image);
    }

    protected function parseCatchExceptionClass(\PDepend\Source\AST\ASTCatchStatement $stmt) {
        do {
            $repeat = false;
            parent::parseCatchExceptionClass($stmt);

            if (Tokens::T_BITWISE_OR === $this->tokenizer->peek()) {
                $this->consumeToken(Tokens::T_BITWISE_OR);
                $repeat = true;
            }
        } while ($repeat === true);
    }

    /**
     * This method parses a single list-statement node.
     *
     * @param bool $oldStyle
     *
     * @return \PDepend\Source\AST\ASTListExpression
     * @since 0.9.12
     */
    protected function parseListExpression($oldStyle = null)
    {
        $this->tokenStack->push();

        if ($oldStyle === null) {
            // Variant can initially be null, but must be same if nested
            $oldStyle = $this->tokenizer->peek() === Tokens::T_LIST;
        }

        if ($oldStyle) {
            $token = $this->consumeToken(Tokens::T_LIST);
        } else {
            $token = $this->consumeToken(Tokens::T_SQUARED_BRACKET_OPEN);
        }

        $this->consumeComments();

        $list = $this->builder->buildAstListExpression($token->image);

        if ($oldStyle) {
            $this->consumeToken(Tokens::T_PARENTHESIS_OPEN);
        }
        $this->consumeComments();

        $withKeys = null;

        while (($tokenType = $this->tokenizer->peek()) !== Tokenizer::T_EOF) {
            // The variable is optional:
            //   list(, , , , $something) = ...;
            // is valid.
            switch ($tokenType) {
                case Tokens::T_COMMA:
                    $this->consumeToken(Tokens::T_COMMA);
                    $this->consumeComments();
                    break;
                case Tokens::T_PARENTHESIS_CLOSE:
                case Tokens::T_SQUARED_BRACKET_CLOSE:
                    break 2;
                case Tokens::T_CONSTANT_ENCAPSED_STRING:
                    if ($withKeys !== null && !$withKeys) {
                        throw new InvalidStateException(
                            $this->tokenizer->next()->startLine,
                            (string) $this->compilationUnit,
                            'Cannot mix keyed and unkeyed array entries in assignments'
                        );
                    }

                    $withKeys = true;
                    $list->addChild($this->parseArrayElement());
                    break;
                case Tokens::T_LIST:
                    if (!$oldStyle) {
                        throw new InvalidStateException(
                            $this->tokenizer->next()->startLine,
                            (string) $this->compilationUnit,
                            'Cannot mix [] and list()'
                        );
                    }

                    $list->addChild($this->parseListExpression($oldStyle));
                    $this->consumeComments();
                    break;
                case Tokens::T_SQUARED_BRACKET_OPEN:
                    if ($oldStyle) {
                        // Old and new style must not be nested
                        throw new InvalidStateException(
                            $this->tokenizer->next()->startLine,
                            (string) $this->compilationUnit,
                            'Cannot mix [] and list()'
                        );
                    }

                    $list->addChild($this->parseListExpression($oldStyle));
                    $this->consumeComments();
                    break;
                default:
                    if ($withKeys !== null && $withKeys) {
                        throw new InvalidStateException(
                            $this->tokenizer->next()->startLine,
                            (string) $this->compilationUnit,
                            'Cannot mix keyed and unkeyed array entries in assignments'
                        );
                    }

                    $withKeys = false;
                    $list->addChild($this->parseVariableOrConstantOrPrimaryPrefix());
                    $this->consumeComments();
                    break;
            }
        }

        if ($oldStyle) {
            $this->consumeToken(Tokens::T_PARENTHESIS_CLOSE);
        } else {
            $this->consumeToken(Tokens::T_SQUARED_BRACKET_CLOSE);
        }

        return $this->setNodePositionsAndReturn($list);
    }

    /**
     * This method parses a single foreach-statement node.
     *
     * @return \PDepend\Source\AST\ASTForeachStatement
     * @since 0.9.8
     */
    protected function parseForeachStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(Tokens::T_FOREACH);

        $foreach = $this->builder->buildAstForeachStatement($token->image);

        $this->consumeComments();
        $this->consumeToken(Tokens::T_PARENTHESIS_OPEN);

        $foreach->addChild($this->parseExpression());

        $this->consumeToken(Tokens::T_AS);
        $this->consumeComments();

        if ($this->tokenizer->peek() === Tokens::T_BITWISE_AND) {
            $foreach->addChild($this->parseVariableOrMemberByReference());
        } else {
            if (in_array($this->tokenizer->peek(), array(Tokens::T_LIST, Tokens::T_SQUARED_BRACKET_OPEN), true)) {
                $foreach->addChild($this->parseListExpression());
            } else {
                $foreach->addChild($this->parseVariableOrConstantOrPrimaryPrefix());

                if ($this->tokenizer->peek() === Tokens::T_DOUBLE_ARROW) {
                    $this->consumeToken(Tokens::T_DOUBLE_ARROW);

                    if (in_array($this->tokenizer->peek(), array(Tokens::T_LIST, Tokens::T_SQUARED_BRACKET_OPEN), true)) {
                        $foreach->addChild($this->parseListExpression());
                    } else {
                        $foreach->addChild($this->parseVariableOrMemberOptionalByReference());
                    }
                }
            }
        }

        $this->consumeComments();
        $this->consumeToken(Tokens::T_PARENTHESIS_CLOSE);

        return $this->setNodePositionsAndReturn(
            $this->parseStatementBody($foreach)
        );
    }
}
