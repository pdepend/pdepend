<?php
/**
 * This file is part of PHP_Reflection.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pdepend.org>.
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
 * @package    PHP_Reflection
 * @subpackage AST
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Reflection/AST/AbstractNode.php';

/**
 * Abstract base class for code item.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage AST
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
abstract class PHP_Reflection_AST_AbstractItem 
    extends PHP_Reflection_AST_AbstractNode
{
    /**
     * The line number where the item declaration starts.
     *
     * @var integer $_line
     */
    private $_line = 0;
    
    /**
     * The line number where the item declaration ends.
     *
     * @var integer $_endLine
     */
    private $_endLine = 0;
    
    /**
     * The source file for this item.
     *
     * @var PHP_Reflection_AST_File $_sourceFile
     */
    private $_sourceFile = null;
    
    /**
     * The comment for this type.
     *
     * @var string $_docComment
     */
    private $_docComment = null;
    
    /**
     * Constructs a new item for the given <b>$name</b> and <b>$startLine</b>.
     *
     * @param string  $name The item name.
     * @param integer $line The item declaration line number.
     */
    public function __construct($name, $line = 0)
    {
        parent::__construct($name);
        
        $this->_line = $line;
    }
    
    /**
     * Returns the line number where the item declaration can be found.
     *
     * @return integer
     */
    public function getLine()
    {
        return $this->_line;
    }
    
    /**
     * Sets the start line for this item.
     *
     * @param integer $startLine The start line for this item.
     * 
     * @return void
     */
    public function setLine($startLine)
    {
        if ($this->_line === 0) {
            $this->_line = $startLine;
        }
    }
    
    /**
     * Returns the line number where the item declaration ends.
     *
     * @return integer The last source line for this item.
     */
    public function getEndLine()
    {
        return $this->_endLine;
    }
    
    /**
     * Sets the end line for this item.
     *
     * @param integer $endLine The end line for this item
     * 
     * @return void
     */
    public function setEndLine($endLine)
    {
        if ($this->_endLine === 0) {
            $this->_endLine = $endLine;
        }
    }
    
    /**
     * Returns the source file for this item.
     *
     * @return PHP_Reflection_AST_File
     */
    public function getSourceFile()
    {
        return $this->_sourceFile;
    }
    
    /**
     * Sets the source file for this item.
     * 
     * @param PHP_Reflection_AST_File $sourceFile The item source file.
     *
     * @return void
     */
    public function setSourceFile(PHP_Reflection_AST_File $sourceFile)
    {
        if ($this->_sourceFile === null || $this->_sourceFile->getFileName() === null) {
            $this->_sourceFile = $sourceFile;
        }
    }
    
    /**
     * Returns the doc comment for this item or <b>null</b>.
     *
     * @return string
     */
    public function getDocComment()
    {
        return $this->_docComment;
    }
    
    /**
     * Sets the doc comment for this item.
     *
     * @param string $docComment The doc comment block.
     * 
     * @return void
     */
    public function setDocComment($docComment)
    {
        $this->_docComment = $docComment;
    }
}