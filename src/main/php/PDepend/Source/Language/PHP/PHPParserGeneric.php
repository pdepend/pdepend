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
 * @since 0.9.20
 */

namespace PDepend\Source\Language\PHP;

use PDepend\Source\Tokenizer\Tokens;

/**
 * Concrete parser implementation that is very tolerant and accepts language
 * constructs and keywords that are reserved in newer php versions, but not in
 * older versions.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 0.9.20
 */
class PHPParserGeneric extends PHPParserVersion83
{
    /**
     * Tests if the give token is a valid function name in the supported PHP
     * version.
     *
     * @since 2.3
     */
    protected function isFunctionName(int $tokenType): bool
    {
        return match ($tokenType) {
            Tokens::T_CLONE,
            Tokens::T_STRING,
            Tokens::T_USE,
            Tokens::T_GOTO,
            Tokens::T_NULL,
            Tokens::T_SELF,
            Tokens::T_TRUE,
            Tokens::T_FALSE,
            Tokens::T_TRAIT,
            Tokens::T_INSTEADOF,
            Tokens::T_NAMESPACE,
            Tokens::T_DIR,
            Tokens::T_NS_C,
            Tokens::T_YIELD,
            Tokens::T_PARENT,
            Tokens::T_TRAIT_C => true,
            default => false,
        };
    }
}
