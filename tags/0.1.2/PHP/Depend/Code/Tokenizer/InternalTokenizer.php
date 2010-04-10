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

require_once 'PHP/Depend/Code/Tokenizer.php';

/**
 * This tokenizer uses the internal {@link token_get_all()} function as token stream
 * generator.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 *
 */
class PHP_Depend_Code_Tokenizer_InternalTokenizer implements PHP_Depend_Code_Tokenizer
{
    /**
     * Mapping between php internal tokens and php depend tokens.
     *
     * @type array<integer>
     * @var array(integer=>integer) $tokenMap
     */
    protected static $tokenMap = array(
        T_NEW           =>  self::T_NEW,
        T_CLASS         =>  self::T_CLASS,
        T_STRING        =>  self::T_STRING,
        T_FUNCTION      =>  self::T_FUNCTION,
        T_ABSTRACT      =>  self::T_ABSTRACT,
        T_INTERFACE     =>  self::T_INTERFACE,
        T_CURLY_OPEN    =>  self::T_CURLY_BRACE_OPEN,
        T_DOC_COMMENT   =>  self::T_DOC_COMMENT,
        T_DOUBLE_COLON  =>  self::T_DOUBLE_COLON,
    );
    
    /**
     * Mapping between php internal text tokens an php depend numeric tokens.
     *
     * @type array<integer>
     * @var array(string=>integer) $literalMap
     */
    protected static $literalMap = array(
        ';'  =>  self::T_SEMICOLON,
        '{'  =>  self::T_CURLY_BRACE_OPEN,
        '}'  =>  self::T_CURLY_BRACE_CLOSE,
        '('  =>  self::T_PARENTHESIS_OPEN,
        ')'  =>  self::T_PARENTHESIS_CLOSE,
    );
    
    /**
     * List of ignorable <b>T_STRING</b> tokens that are php keywords.
     *
     * @type array<boolean>
     * @var array(string=>boolean) $ignoreMap
     */
    protected static $ignoreMap = array(
        'null'    =>  true,
        'array'   =>  true,
        'parent'  =>  true,
        'true'    =>  true,
        'false'   =>  true,
    );
    
    /**
     * The name of the source file.
     *
     * @type string
     * @var string $sourceFile
     */
    protected $sourceFile = '';
    
    /**
     * Count of all tokens.
     *
     * @type integer
     * @var integer $count
     */
    protected $count = 0;

    /**
     * Internal stream pointer index.
     *
     * @type integer
     * @var integer $index
     */
    protected $index = 0;
    
    /**
     * Prepared token list.
     *
     * @type array<array>
     * @var array(array) $tokens
     */
    protected $tokens = array();
    
    /**
     * Constructs a new tokenizer for the given file.
     *
     * @param string $sourceFile A php source file.
     */
    public function __construct($sourceFile)
    {
        $this->sourceFile = $sourceFile;
        
        $this->tokenize();
    }
    
    /**
     * Returns the name of the source file.
     *
     * @return string
     */
    public function getSourceFile()
    {
        return $this->sourceFile;
    }
    
    /**
     * Returns the next token or {@link PHP_Depend_Code_Tokenizer::T_EOF} if 
     * there is no next token.
     *
     * @return array|integer
     */
    public function next()
    {
        if ($this->index < $this->count) {
            return $this->tokens[$this->index++];
        }
        return self::T_EOF;
    }
    
    /**
     * Returns the next token type or {@link PHP_Depend_Code_Tokenizer::T_EOF} if 
     * there is no next token.
     *
     * @return integer
     */
    public function peek()
    {
        if ($this->index < $this->count) {
            return $this->tokens[$this->index][0];
        }
        return self::T_EOF;
    }
    
    /**
     * Tokenizes the content of the source file with {@link token_get_all()} and
     * filters this token stream.
     * 
     * @return void
     */
    protected function tokenize()
    {
        $source = file_get_contents($this->sourceFile);
        $tokens = token_get_all($source);
        
        foreach ($tokens as $token) {
            $newToken = null;
            if (is_string($token)) {
                if (isset(self::$literalMap[$token])) {
                    $newToken = array(self::$literalMap[$token], $token);
                }
            } else {
                $value = strtolower($token[1]);
                if (!isset(self::$ignoreMap[$value]) 
                  && isset(self::$tokenMap[$token[0]])) {
                    
                    $newToken = array(self::$tokenMap[$token[0]], $token[1]);
                }
            }
            
            if ($newToken !== null) {
                $this->tokens[] = $newToken;
            }
        }
        
        $this->count = count($this->tokens);
    }
}