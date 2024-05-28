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
 * @since 2.11
 */

namespace PDepend\Source\Language\PHP;

use PDepend\Source\AST\ASTConstantDeclarator;
use PDepend\Source\Parser\TokenStreamEndException;
use PDepend\Source\Parser\UnexpectedTokenException;
use PDepend\Source\Tokenizer\Tokens;

/**
 * Concrete parser implementation that supports features up to PHP version 8.2.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 2.12
 */
abstract class PHPParserVersion83 extends PHPParserVersion82
{
    /**
     * Parse a typed constant (PHP >= 8.3).
     *
     * @throws UnexpectedTokenException
     * @throws TokenStreamEndException
     * @since  1.16.0
     */
    protected function parseTypedConstantDeclarator(): ASTConstantDeclarator
    {
        $constantType = $this->parseTypeHint();
        $tokenType = $this->tokenizer->peek();
        $token = $this->consumeToken($tokenType);

        $this->consumeComments();
        $this->consumeToken(Tokens::T_EQUAL);

        // $this->isConstantName($token) must be asserted by the caller
        $declarator = $this->builder->buildAstConstantDeclarator($token->image);
        $declarator->setType($constantType);
        $declarator->setValue($this->parseConstantDeclaratorValue());

        return $this->setNodePositionsAndReturn($declarator);
    }

    /**
     * @since  2.16.3
     */
    protected function isConstantName(int $tokenType): bool
    {
        return parent::isConstantName($tokenType) || $tokenType === Tokens::T_BITWISE_OR;
    }
}
