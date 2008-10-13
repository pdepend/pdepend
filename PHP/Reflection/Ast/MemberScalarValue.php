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
 * @subpackage Ast
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Reflection/Ast/StaticScalarValueI.php';

/**
 * Represents a scalar php value. 
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Ast
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_Ast_MemberScalarValue implements PHP_Reflection_Ast_StaticScalarValueI
{
    /**
     * The type of this scalar value.
     *
     * @var integer $_type
     */
    private $_type = null;
    
    /**
     * The value of this scalar instance.
     *
     * @var mixed $_value
     */
    private $_value = null;
    
    /**
     * The unique identifier for this node.
     *
     * @var PHP_Reflection_Util_UUID $_uuid
     */
    private $_uuid = null;
    
    /**
     * Constructs a new scalar instance.
     *
     * @param integer $type  The type of this scalar instance.
     * @param string  $value The raw value. 
     */
    public function __construct($type, $value = null)
    {
        $this->_value = $this->_castValue($type, $value);
        $this->_type  = $type;
        
        // Create a unique identifier for this node
        $this->_uuid = new PHP_Reflection_Util_UUID();
    }
    
    /**
     * Returns the type of this value.
     *
     * @return integer
     */
    public function getType()
    {
        return $this->_type;
    }
    
    /**
     * Returns the value. 
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->_value;
    }
    
    /**
     * Returns an identifier for this node.
     *
     * @return string
     */
    public function getName()
    {
        return '#scalar-value';
    }
    
    /**
     * Returns a unique identifier for this node.
     *
     * @return string
     */
    public function getUUID()
    {
        return (string) $this->_uuid;
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
        $visitor->visitValue($this);
    }
    
    /**
     * This method casts the given string <b>$value</b> into its php representation.
     * 
     * TODO: Review the current string handling we use substr() to remove the
     * leading and closing quote character.
     *
     * @param integer $type  The type of this scalar instance.
     * @param string  $value The raw value.
     * 
     * @return mixed
     */
    private function _castValue($type, $value)
    {
        switch ($type)
        {
            case self::IS_NULL:
                return null;
            
            case self::IS_STRING:
                return substr((string) $value, 1, -1);
                
            case self::IS_DOUBLE:
                return (double) $value;
                
            case self::IS_INTEGER:
                return (integer) $value;
                
            case self::IS_BOOLEAN:
                return (strtolower($value) === 'true');
                
            default:
                throw new ErrorException('Invalid scalar type given.');
        }
    }
}