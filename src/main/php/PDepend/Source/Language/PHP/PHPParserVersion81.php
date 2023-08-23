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
 * @since 2.11
 */

namespace PDepend\Source\Language\PHP;

use PDepend\Source\AST\ASTArguments;
use PDepend\Source\AST\ASTEnum;
use PDepend\Source\AST\ASTIntersectionType;
use PDepend\Source\AST\ASTScalarType;
use PDepend\Source\AST\ASTType;
use PDepend\Source\AST\ASTValue;
use PDepend\Source\AST\State;
use PDepend\Source\Parser\ParserException;
use PDepend\Source\Tokenizer\Tokens;

/**
 * Concrete parser implementation that supports features up to PHP version 8.1.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @since 2.11
 */
abstract class PHPParserVersion81 extends PHPParserVersion80
{
    /**
     * Regular expression for integer numbers representation.
     * (PHP 7.4 added support for underscores, octal explicit notation still disallowed.)
     *
     * @see https://github.com/php/doc-en/blob/d494ffa4d9f83b60fe66972ec2c0cf0301513b4a/language/types/integer.xml#L77-L89
     */
    const REGEXP_INTEGER = '(
                       0
                       |
                       [1-9][0-9]*(?:_[0-9]+)*
                       |
                       0[xX][0-9a-fA-F]+(?:_[0-9a-fA-F]+)*
                       |
                       0[oO]?[0-7]+(?:_[0-7]+)*
                       |
                       0[bB][01]+(?:_[01]+)*
                     )x';

    /**
     * Tests if the given image is a PHP 8.1 type hint.
     *
     * @param string $image
     *
     * @return bool
     */
    protected function isScalarOrCallableTypeHint($image)
    {
        if (strtolower($image) === 'never') {
            return true;
        }

        return parent::isScalarOrCallableTypeHint($image);
    }

    /**
     * Parses a scalar type hint or a callable type hint.
     *
     * @param string $image
     */
    protected function parseScalarOrCallableTypeHint($image)
    {
        if (strtolower($image) === 'never') {
            return $this->builder->buildAstScalarType($image);
        }

        return parent::parseScalarOrCallableTypeHint($image);
    }

    /**
     * @inheritDoc
     */
    protected function parseConstructFormalParameterModifiers()
    {
        return $this->checkReadonlyToken()
            | parent::parseConstructFormalParameterModifiers()
            | $this->checkReadonlyToken();
    }

    /**
     * This method will parse a default value after a parameter/static variable/constant
     * declaration.
     *
     * @return ASTValue
     *
     * @since 2.11.0
     */
    protected function parseVariableDefaultValue()
    {
        if ($this->tokenizer->peek() === Tokens::T_NEW) {
            $defaultValue = new ASTValue();
            $defaultValue->setValue($this->parseAllocationExpression());

            return $defaultValue;
        }

        return parent::parseVariableDefaultValue();
    }

    /**
     * Parses enum declaration. available since PHP 8.1. Ex.:
     *  enum Suit: string { case HEARTS = 'hearts'; }
     *
     * @return ASTEnum
     */
    protected function parseEnumDeclaration()
    {
        $this->tokenStack->push();

        $enum = $this->parseEnumSignature();
        $enum = $this->parseTypeBody($enum);
        $enum->setTokens($this->tokenStack->pop());

        $this->reset();

        return $enum;
    }

    /**
     * @param ASTType $firstType
     *
     * @return ASTIntersectionType
     */
    protected function parseIntersectionTypeHint($firstType)
    {
        $token = $this->tokenizer->currentToken();
        $types = array($firstType);

        while ($this->tokenizer->peekNext() !== Tokens::T_VARIABLE && $this->addTokenToStackIfType(Tokens::T_BITWISE_AND)) {
            $types[] = $this->parseSingleTypeHint();
        }

        $intersectionType = $this->builder->buildAstIntersectionType();
        foreach ($types as $type) {
            // no scalars are allowed as intersection types
            if ($type instanceof ASTScalarType) {
                throw new ParserException(
                    $type->getImage() . ' can not be used in an intersection type',
                    0,
                    $this->getUnexpectedTokenException($token)
                );
            }

            $intersectionType->addChild($type);
        }

        return $intersectionType;
    }

    /**
     * @inheritDoc
     */
    protected function parseTypeHintCombination($type)
    {
        $peek = $this->tokenizer->peek();

        if ($peek === Tokens::T_BITWISE_OR) {
            return $this->parseUnionTypeHint($type);
        }

        $peekNext = $this->tokenizer->peekNext();
        // sniff for &, but avoid by_reference &$variable and &...$variables.
        if ($peek === Tokens::T_BITWISE_AND && $peekNext !== Tokens::T_VARIABLE && $peekNext !== Tokens::T_ELLIPSIS) {
            return $this->parseIntersectionTypeHint($type);
        }

        return $type;
    }

    /**
     * @return ASTArguments
     */
    protected function parseArgumentList(ASTArguments $arguments)
    {
        $this->consumeComments();

        // peek if there's an ellipsis to determine variadic placeholder
        $ellipsis  = Tokens::T_ELLIPSIS === $this->tokenizer->peek();

        $arguments = parent::parseArgumentList($arguments);

        // ellipsis and no further arguments => variadic placeholder foo(...)
        if ($ellipsis === true && count($arguments->getChildren()) === 0) {
            $arguments->setVariadicPlaceholder();
        }

        return $arguments;
    }

    /**
     * @return int
     */
    private function checkReadonlyToken()
    {
        if ($this->addTokenToStackIfType(Tokens::T_READONLY)) {
            return State::IS_READONLY;
        }

        return 0;
    }
}
