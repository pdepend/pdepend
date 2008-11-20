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

require_once 'PHP/Reflection/AST/AbstractSourceElement.php';
require_once 'PHP/Reflection/AST/LiteralI.php';

/**
 * This class represents literal data like integers, floats and strings.
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
class PHP_Reflection_AST_Literal
       extends PHP_Reflection_AST_AbstractSourceElement
    implements PHP_Reflection_AST_LiteralI
{
    /**
     * The raw string representation of this literal.
     *
     * @var string $_data
     */
    private $_data = null;

    /**
     * Is this an integer literal?
     *
     * @var boolean $_int
     */
    private $_int = false;

    /**
     * Is this a float literal?
     *
     * @var boolean $_float
     */
    private $_float = false;

    /**
     * Is this a string literal?
     *
     * @var boolean $_string
     */
    private $_string = false;

    /**
     * Constructs a new literal node instance.
     *
     * @param integer $line The line number of this literal node.
     */
    public function __construct($line)
    {
        parent::__construct(self::NODE_NAME, $line);
    }

    /**
     * Returns the string representation of this literal.
     *
     * @return string
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Sets the raw string representation of this literal.
     *
     * @param string $data The raw data.
     *
     * @return void
     */
    public function setData($data)
    {
        $this->_data = $data;
    }

    /**
     * This method will return <b>true</b> when this node represents an integer.
     *
     * @return boolean
     */
    public function isInt()
    {
        return $this->_int;
    }

    /**
     * Marks this literal as an integer.
     *
     * @return void
     */
    public function setInt()
    {
        $this->_int = true;
    }

    /**
     * This method will return <b>true</b> when this node represents a float.
     *
     * @return boolean
     */
    public function isFloat()
    {
        return $this->_float;
    }

    /**
     * Marks this literal as a float.
     *
     * @return void
     */
    public function setFloat()
    {
        $this->_float = true;
    }

    /**
     * This method will return <b>true</b> when this node represents a string.
     *
     * @return boolean
     */
    public function isString()
    {
        return $this->_string;
    }

    /**
     * Marks this literal as a string.
     *
     * @return void
     */
    public function setString()
    {
        $this->_string = true;
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
        $visitor->visitLiteral($this);
    }
}
?>