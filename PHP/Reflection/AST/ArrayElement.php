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
 * This class represents the key value pair for a single array element.
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
class PHP_Reflection_AST_ArrayElement extends PHP_Reflection_AST_AbstractNode
{
    /**
     * Identifier for this node type.
     */
    const NODE_NAME = '#array-element';
    
    /**
     * The key for this array element.
     *
     * @var PHP_Reflection_AST_ExpressionI $_key
     */
    private $_key = null;
    
    /**
     * The value for this array element.
     *
     * @var PHP_Reflection_AST_ExpressionI $_value
     */
    private $_value = null;
    
    /**
     * Constructs a new array element node.
     */
    public function __construct()
    {
        parent::__construct(self::NODE_NAME);
    }
    
    /**
     * Returns the key of this array element.
     *
     * @return PHP_Reflection_AST_ExpressionI
     */
    public function getKey()
    {
        return $this->_key;
    }
    
    /**
     * Sets the key of this array element.
     *
     * @param PHP_Reflection_AST_ExpressionI $key The key expression.
     * 
     * @return void
     */
    public function setKey(PHP_Reflection_AST_ExpressionI $key)
    {
        $this->_key = $key;
    }
    
    /**
     * Returns the value of this array element.
     *
     * @return PHP_Reflection_AST_ExpressionI
     */
    public function getValue()
    {
        return $this->_value;
    }
    
    /**
     * Sets the value of this array element.
     *
     * @param PHP_Reflection_AST_ExpressionI $value The value expression.
     * 
     * @return void
     */
    public function setValue(PHP_Reflection_AST_ExpressionI $value)
    {
        $this->_value = $value;
    }
    
    /**
     * Visitor method for node tree traversal.
     *
     * @param PHP_Reflection_VisitorI $visitor The context visitor implementation.
     * 
     * @return void
     */
    public function accept(PHP_Reflection_VisitorI $visitor)
    {
        $visitor->visitArrayElement($this);
    }
}