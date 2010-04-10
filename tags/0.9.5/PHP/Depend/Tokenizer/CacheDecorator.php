<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @subpackage Tokenizer
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once 'PHP/Depend/StorageRegistry.php';
require_once 'PHP/Depend/TokenizerI.php';
require_once 'PHP/Depend/Code/File.php';

/**
 * This is a cache decorator for a concrete tokenizer implementation.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Tokenizer
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Tokenizer_CacheDecorator implements PHP_Depend_TokenizerI
{
    /**
     * The decorated tokenizer instance.
     *
     * @var PHP_Depend_TokenizerI $_tokenizer
     */
    private $_tokenizer = null;

    /**
     * The context source file instance.
     *
     * @var PHP_Depend_Code_File $_sourceFile
     */
    private $_sourceFile = null;

    /**
     * The token object for the current context file.
     *
     * @var array(PHP_Depend_Token) $_tokens
     */
    private $_tokens = array();

    /**
     * The current iteration/tokenizer offset.
     *
     * @var integer $_index
     */
    private $_index = 0;

    /**
     * The number of tokens in the current token stream.
     *
     * @var integer $_count
     */
    private $_count = 0;

    /**
     * Constructs a new tokenizer decorator instance.
     *
     * @param PHP_Depend_TokenizerI $tokenizer The decorated tokenizer instance.
     */
    public function __construct(PHP_Depend_TokenizerI $tokenizer)
    {
        $this->_tokenizer = $tokenizer;
    }

    /**
     * Returns the name of the source file.
     *
     * @return string
     */
    public function getSourceFile()
    {
        return $this->_sourceFile;
    }

    /**
     * Sets a new php source file.
     *
     * @param string $sourceFile A php source file.
     *
     * @return void
     */
    public function setSourceFile($sourceFile)
    {
        $storage = PHP_Depend_StorageRegistry::get(PHP_Depend::PARSER_STORAGE);

        $id    = '$Id$';
        $key   = md5_file($sourceFile);
        $group = 'parser-tokenizer';

        $tokens = $storage->restore($key, $group, $id);
        if (is_array($tokens)) {
            $this->_sourceFile = new PHP_Depend_Code_File($sourceFile);
            $this->_sourceFile->setTokens($tokens);
        } else {
            $this->_tokenizer->setSourceFile($sourceFile);
            $this->_sourceFile = $this->_tokenizer->getSourceFile();

            $tokens = $this->_sourceFile->getTokens();
            $storage->store($tokens, $key, $group, $id);
        }

        $this->_tokens = $tokens;
        $this->_index  = 0;
        $this->_count  = count($tokens);
    }

    /**
     * Returns the next token or {@link PHP_Depend_TokenizerI::T_EOF} if
     * there is no next token.
     *
     * @return array|integer
     */
    public function next()
    {
        if ($this->_index < $this->_count) {
            return $this->_tokens[$this->_index++];
        }
        return self::T_EOF;
    }

    /**
     * Returns the next token type or {@link PHP_Depend_TokenizerI::T_EOF} if
     * there is no next token.
     *
     * @return integer
     */
    public function peek()
    {
        if (isset($this->_tokens[$this->_index])) {
            return $this->_tokens[$this->_index]->type;
        }
        return self::T_EOF;
    }

    /**
     * Returns the previous token type or {@link PHP_Depend_TokenizerI::T_BOF}
     * if there is no previous token.
     *
     * @return integer
     */
    public function prev()
    {
        if ($this->_index > 1) {
            return $this->_tokens[$this->_index - 2]->type;
        }
        return self::T_BOF;
    }
}
?>
