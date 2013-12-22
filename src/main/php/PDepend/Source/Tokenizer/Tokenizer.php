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
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Source\Tokenizer;

/**
 * Define PHP 5.4 __TRAIT__ token constant.
 */
if (!defined('T_TRAIT_C')) {
    define('T_TRAIT_C', 42000);
}

/**
 * Define PHP 5.4 'trait' token constant.
 */
if (!defined('T_TRAIT')) {
    define('T_TRAIT', 42001);
}

/**
 * Define PHP 5.4 'insteadof' token constant.
 */
if (!defined('T_INSTEADOF')) {
    define('T_INSTEADOF', 42002);
}

/**
 * Define PHP 5.3 __NAMESPACE__ token constant.
 */
if (!defined('T_NS_C')) {
    define('T_NS_C', 42003);
}

/**
 * Define PHP 5.3 'use' token constant
 */
if (!defined('T_USE')) {
    define('T_USE', 42004);
}

/**
 * Define PHP 5.3 'namespace' token constant.
 */
if (!defined('T_NAMESPACE')) {
    define('T_NAMESPACE', 42005);
}

/**
 * Define PHP 5.3's '__DIR__' token constant.
 */
if (!defined('T_DIR')) {
    define('T_DIR', 42006);
}

/**
 * Define PHP 5.3's 'T_GOTO' token constant.
 */
if (!defined('T_GOTO')) {
    define('T_GOTO', 42007);
}

/**
 * Define PHP 5.4's 'T_CALLABLE' token constant
 */
if (!defined('T_CALLABLE')) {
    define('T_CALLABLE', 42008);
}

/**
 * Define PHP 5.5's 'T_YIELD' token constant
 */
if (!defined('T_YIELD')) {
    define('T_YIELD', 267);
}

/**
 * Base interface for all php code tokenizers.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
interface Tokenizer
{
    /**
     * Marks the end of the token stream.
     */
    const T_EOF = -1;

    /**
     * Marks the beginning of the token stream.
     */
    const T_BOF = -2;

    /**
     * Returns the name of the source file.
     *
     * @return string
     */
    public function getSourceFile();

    /**
     * Sets a new php source file.
     *
     * @param string $sourceFile A php source file.
     *
     * @return void
     */
    public function setSourceFile($sourceFile);

    /**
     * Returns the next token or {@link \PDepend\Source\Tokenizer\Tokenizer::T_EOF} if
     * there is no next token.
     *
     * @return \PDepend\Source\Tokenizer\Token
     */
    public function next();

    /**
     * Returns the next token type or {@link \PDepend\Source\Tokenizer\Tokenizer::T_EOF} if
     * there is no next token.
     *
     * @return integer
     */
    public function peek();
    
    /**
     * Returns the type of next token, after the current token. This method
     * ignores all comments between the current and the next token.
     *
     * @return integer
     * @since 0.9.12
     */
    public function peekNext();

    /**
     * Returns the previous token type or {@link \PDepend\Source\Tokenizer\Tokenizer::T_BOF}
     * if there is no previous token.
     *
     * @return integer
     */
    public function prev();
}
