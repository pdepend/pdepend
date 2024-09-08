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
 */

namespace PDepend\Source\Tokenizer;

use PDepend\Source\AST\ASTCompilationUnit;

/**
 * Base interface for all php code tokenizers.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
interface Tokenizer
{
    /** Marks the end of the token stream. */
    public const T_EOF = -1;

    /** Marks the beginning of the token stream. */
    public const T_BOF = -2;

    /**
     * Returns the name of the source file.
     */
    public function getSourceFile(): ?ASTCompilationUnit;

    /**
     * Sets a new php source file.
     *
     * @param string $sourceFile A php source file.
     */
    public function setSourceFile(string $sourceFile): void;

    /**
     * Returns the next token or {@link Tokenizer::T_EOF} if
     * there is no next token.
     *
     * @return Token|Tokenizer::T_*
     */
    public function next();

    /**
     * Returns the previous token or null if there is no one yet.
     *
     * @since  2.6.0
     */
    public function prevToken(): ?Token;

    /**
     * Returns the current token or null if there is no more.
     *
     * @since  2.6.0
     */
    public function currentToken(): ?Token;

    /**
     * Returns the next token type or {@link Tokenizer::T_EOF} if
     * there is no next token.
     */
    public function peek(): int;

    /**
     * Returns the type of next token, after the current token. This method
     * ignores all comments between the current and the next token.
     *
     * @since  0.9.12
     */
    public function peekNext(): ?int;

    /**
     * Returns the previous token type or {@link Tokenizer::T_BOF}
     * if there is no previous token.
     */
    public function prev(): int;
}
