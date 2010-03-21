<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2010, Manuel Pichler <mapi@pdepend.org>.
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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once 'PHP/Depend/Parser/FunctionNameParser.php';
require_once 'PHP/Depend/Parser/TokenStreamEndException.php';
require_once 'PHP/Depend/Parser/UnexpectedTokenException.php';

/**
 * Default implementation of the function name parser. This implementation is
 * not fixed to a special php version and its keywords.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Parser_FunctionNameParserImpl
    implements PHP_Depend_Parser_FunctionNameParser
{
    /**
     * The used tokenizer instance.
     *
     * @var PHP_Depend_TokenizerI
     */
    private $_tokenizer = null;

    /**
     * The used token stack.
     *
     * @var PHP_Depend_Parser_TokenStack
     */
    private $_tokenStack = null;

    /**
     * Setter method for the context tokenizer instance.
     *
     * @param PHP_Depend_TokenizerI $tokenizer The context tokenizer.
     *
     * @return void
     */
    public function setTokenizer(PHP_Depend_TokenizerI $tokenizer)
    {
        $this->_tokenizer = $tokenizer;
    }

    /**
     * Setter method for the context token stack instance.
     *
     * @param PHP_Depend_Parser_TokenStack $tokenStack The context token stack
     *
     * @return void
     */
    public function setTokenStack(PHP_Depend_Parser_TokenStack $tokenStack)
    {
        $this->_tokenStack = $tokenStack;
    }
    
    /**
     * Parses a function name from the given tokenizer and returns the string
     * literal representing the function name. If no valid token exists in the
     * token stream, this method will throw an exception.
     *
     * @return string
     * @throws PHP_Depend_Parser_UnexpectedTokenException When the next available
     *         token does not represent a valid php function name.
     * @throws PHP_Depend_Parser_TokenStreamEndException When there is no next
     *         token available in the given token stream.
     */
    public function parse()
    {
        switch ($this->_tokenizer->peek()) {

        case PHP_Depend_TokenizerI::T_STRING:
        case PHP_Depend_TokenizerI::T_USE:
        case PHP_Depend_TokenizerI::T_GOTO:
        case PHP_Depend_TokenizerI::T_NULL:
        case PHP_Depend_TokenizerI::T_SELF:
        case PHP_Depend_TokenizerI::T_TRUE:
        case PHP_Depend_TokenizerI::T_FALSE:
        case PHP_Depend_TokenizerI::T_NAMESPACE:
        case PHP_Depend_TokenizerI::T_DIR:
        case PHP_Depend_TokenizerI::T_NS_C:
        case PHP_Depend_TokenizerI::T_PARENT:
            $token = $this->_tokenizer->next();
            $this->_tokenStack->add($token);

            return $token->image;

        case PHP_Depend_TokenizerI::T_EOF:
            throw new PHP_Depend_Parser_TokenStreamEndException($this->_tokenizer);
        }
        throw new PHP_Depend_Parser_UnexpectedTokenException(
            $this->_tokenizer->next(),
            $this->_tokenizer->getSourceFile()
        );
    }
}