<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pmanuel-pichler.de>.
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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

/**
 * Base interface for all php code tokenizers.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
interface PHP_Depend_Code_Tokenizer
{
    /**
     * Marks the end of the token stream.
     */
    const T_EOF = -1;
    
    /**
     * Marks a class token.
     */
    const T_CLASS = 1;
    
    /**
     * Marks an interface token.
     */
    const T_INTERFACE = 2;
    
    /**
     * Marks an abstract token.
     */
    const T_ABSTRACT = 3;
    
    /**
     * Marks a curly brace open.
     */
    const T_CURLY_BRACE_OPEN = 4;
    
    /**
     * Marks a curly brace close.
     */
    const T_CURLY_BRACE_CLOSE = 5;
    
    /**
     * Marks a parenthesis open.
     */
    const T_PARENTHESIS_OPEN = 6;
    
    /**
     * Marks a parenthesis close.
     */
    const T_PARENTHESIS_CLOSE = 7;
    
    /**
     * Marks a new token.
     */
    const T_NEW = 8;
    
    /**
     * Marks a function.
     */
    const T_FUNCTION = 9;
    
    /**
     * Marks a double colon.
     */
    const T_DOUBLE_COLON = 10;
    
    /**
     * Marks a string token.
     */
    const T_STRING = 11;
    
    /**
     * Marks a doc comment.
     */
    const T_DOC_COMMENT = 12;
    
    /**
     * Marks a semicolon.
     */
    const T_SEMICOLON = 13;
    
    /**
     * Returns the name of the source file.
     *
     * @return string
     */
    function getSourceFile();
    
    /**
     * Returns the next token or {@link PHP_Depend_Code_Tokenizer::T_EOF} if 
     * there is no next token.
     *
     * @return array|integer
     */
    function next();
    
    /**
     * Returns the next token type or {@link PHP_Depend_Code_Tokenizer::T_EOF} if 
     * there is no next token.
     *
     * @return integer
     */
    function peek();
}