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

require_once 'PHP/Reflection/Ast/AbstractItem.php';
require_once 'PHP/Reflection/Ast/TypeAwareI.php';
require_once 'PHP/Reflection/Ast/VisibilityAwareI.php';

/**
 * This code class represents a class property.
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
class PHP_Reflection_Ast_Property
       extends PHP_Reflection_Ast_AbstractItem
    implements PHP_Reflection_Ast_TypeAwareI,
               PHP_Reflection_Ast_VisibilityAwareI
{
    /**
     * Set defined visibility for this method.
     *
     * @type integer
     * @var integer $_visibility
     */
    private $_visibility = -1;
    
    /**
     * The parent type object.
     *
     * @type PHP_Reflection_Ast_Class
     * @var PHP_Reflection_Ast_Class $_parent
     */
    private $_parent = null;
    
    /**
     * The type for this property. This value is <b>null</b> by default and for
     * scalar types.
     *
     * @type PHP_Reflection_Ast_AbstractType
     * @var PHP_Reflection_Ast_AbstractType $_type
     */
    private $_type = null;
    
    /**
     * Declared modifiers for this method.
     * 
     * <ul>
     *   <li>ReflectionMethod::IS_ABSTRACT</li>
     *   <li>ReflectionMethod::IS_FINAL</li>
     *   <li>ReflectionMethod::IS_PUBLIC</li>
     *   <li>ReflectionMethod::IS_PROTECTED</li>
     *   <li>ReflectionMethod::IS_PRIVATE</li>
     *   <li>ReflectionMethod::IS_STATIC</li>
     * </ul>
     *
     * @var unknown_type
     */
    private $_modifiers = ReflectionMethod::IS_PUBLIC;
    
    /**
     * Sets the modifiers for this method.
     *
     * @param integer $modifiers The method modifiers.
     * 
     * @return void
     */
    public function setModifiers($modifiers)
    {
        $this->_modifiers = (int) $modifiers;
        
        // Check visibility
        if ($this->isPrivate() === false && $this->isProtected() === false) {
            $this->_modifiers |= ReflectionMethod::IS_PUBLIC;
        }
    }
    
    /**
     * Returns the declared modifiers for this method.
     *
     * @return integer
     */
    public function getModifiers()
    {
        return $this->_modifiers;
    }
    
    /**
     * Returns <b>true</b> if this node is marked as public, otherwise the 
     * returned value will be <b>false</b>.
     *
     * @return boolean
     */
    public function isPublic()
    {
        return (ReflectionMethod::IS_PUBLIC === (
            $this->_modifiers & ReflectionMethod::IS_PUBLIC
        ));
    }
    
    /**
     * Returns <b>true</b> if this node is marked as protected, otherwise the 
     * returned value will be <b>false</b>.
     *
     * @return boolean
     */
    public function isProtected()
    {
        return (ReflectionMethod::IS_PROTECTED === (
            $this->_modifiers & ReflectionMethod::IS_PROTECTED
        ));
    }
    
    /**
     * Returns <b>true</b> if this node is marked as private, otherwise the 
     * returned value will be <b>false</b>.
     *
     * @return boolean
     */
    public function isPrivate()
    {
        return (ReflectionMethod::IS_PRIVATE === (
            $this->_modifiers & ReflectionMethod::IS_PRIVATE
        ));
    }
    
    /**
     * Returns the parent class object or <b>null</b>
     *
     * @return PHP_Reflection_Ast_Class|null
     */
    public function getParent()
    {
        return $this->_parent;
    }
    
    /**
     * Sets the parent class object.
     *
     * @param PHP_Reflection_Ast_Class $parent The parent class.
     * 
     * @return void
     */
    public function setParent(PHP_Reflection_Ast_Class $parent = null)
    {
        $this->_parent = $parent;
    }
    
    /**
     * Returns the type of this property. This method will return <b>null</b>
     * for all scalar type, only class properties will have a type.
     *
     * @return PHP_Reflection_Ast_AbstractType
     */
    public function getType()
    {
        return $this->_type;
    }
    
    /**
     * Sets the type of this property.
     *
     * @param PHP_Reflection_Ast_AbstractType $type The property type.
     * 
     * @return void
     */
    public function setType(PHP_Reflection_Ast_AbstractType $type)
    {
        $this->_type = $type;
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
        $visitor->visitProperty($this);
    }
                   
}