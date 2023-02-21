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

use PDepend\Source\AST\ASTClosure;
use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\AST\ASTType;
use PDepend\Source\Parser\UnexpectedTokenException;
use PDepend\Source\Tokenizer\Tokens;

/**
 * Concrete parser implementation that supports features up to PHP version 7.4.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @since 2.4
 */
abstract class PHPParserVersion74 extends PHPParserVersion73
{
    /**
     * Regular expression for integer numbers representation.
     * (Add support for octal explicit notation.)
     *
     * @see https://php.net/manual/en/language.types.integer.php
     * @see https://github.com/php/doc-en/blob/085c38d45e466691062b4444c71f4dbe4198f884/language/types/integer.xml#L79-L91
     */
    const REGEXP_INTEGER = '/^(
                       0
                       |
                       [1-9][0-9]*(?:_[0-9]+)*
                       |
                       0[xX][0-9a-fA-F]+(?:_[0-9a-fA-F]+)*
                       |
                       0[0-7]+(?:_[0-7]+)*
                       |
                       0[bB][01]+(?:_[01]+)*
                     )$/x';

    /** @var array<int, int> */
    protected $possiblePropertyTypes = array(
        Tokens::T_STRING,
        Tokens::T_ARRAY,
        Tokens::T_QUESTION_MARK,
        Tokens::T_BACKSLASH,
        Tokens::T_CALLABLE,
        Tokens::T_SELF,
    );

    protected function parseUnknownDeclaration($tokenType, $modifiers)
    {
        /**
         * Typed properties
         * https://www.php.net/manual/en/migration74.new-features.php#migration74.new-features.core.typed-properties
         */
        if (in_array($tokenType, $this->possiblePropertyTypes, true)) {
            $type = $this->parseTypeHint();
            $declaration = $this->parseFieldDeclaration();
            $declaration->prependChild($type);
            $declaration->setModifiers($modifiers);

            return $declaration;
        }

        return parent::parseUnknownDeclaration($tokenType, $modifiers);
    }

    /**
     * @return ASTClosure
     */
    protected function parseLambdaFunctionDeclaration()
    {
        $this->tokenStack->push();

        if (Tokens::T_FN === $this->tokenizer->peek()) {
            $this->consumeToken(Tokens::T_FN);
        }

        $closure = $this->builder->buildAstClosure();
        $closure->setReturnsByReference($this->parseOptionalByReference());
        $closure->addChild($this->parseFormalParameters($closure));
        $this->parseCallableDeclarationAddition($closure);

        $closure->addChild(
            $this->buildReturnStatement(
                $this->consumeToken(Tokens::T_DOUBLE_ARROW)
            )
        );

        return $this->setNodePositionsAndReturn($closure);
    }

    /**
     * Override PHP 7.3 checkEllipsisInExpressionSupport to stop throwing the
     * parsing exception.
     *
     * @return void
     */
    protected function checkEllipsisInExpressionSupport()
    {
        // Do not throw the exception from parent PHP 7.3
    }
}
