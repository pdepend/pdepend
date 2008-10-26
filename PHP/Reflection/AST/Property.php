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
require_once 'PHP/Reflection/AST/PropertyI.php';
require_once 'PHP/Reflection/AST/TypeAwareI.php';

/**
 * This code class represents a class property.
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
class PHP_Reflection_AST_Property
       extends PHP_Reflection_AST_AbstractSourceElement
    implements PHP_Reflection_AST_PropertyI,
               PHP_Reflection_AST_TypeAwareI
{
    /**
     * The parent type object.
     *
     * @var PHP_Reflection_AST_Class $_parent
     */
    private $_parent = null;
    
    /**
     * The type for this property. This value is <b>null</b> by default and for
     * scalar types.
     *
     * @var PHP_Reflection_AST_ClassOrInterfaceI $_type
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
     * The value of this property instance.
     *
     * @var PHP_Reflection_AST_MemberValueI $_value
     */
    private $_value = null;
    
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
        return (self::IS_PUBLIC === ($this->_modifiers & self::IS_PUBLIC));
    }
    
    /**
     * Returns <b>true</b> if this node is marked as protected, otherwise the 
     * returned value will be <b>false</b>.
     *
     * @return boolean
     */
    public function isProtected()
    {
        return (self::IS_PROTECTED === ($this->_modifiers & self::IS_PROTECTED));
    }
    
    /**
     * Returns <b>true</b> if this node is marked as private, otherwise the 
     * returned value will be <b>false</b>.
     *
     * @return boolean
     */
    public function isPrivate()
    {
        return (self::IS_PRIVATE === ($this->_modifiers & self::IS_PRIVATE));
    }
    
    /**
     * Returns <b>true</b> if this property node is marked as static, otherwise
     * the return value is <b>false</b>.
     * 
     * @return boolean
     */
    public function isStatic()
    {
        return (self::IS_STATIC === ($this->_modifiers & self::IS_STATIC));
    }
    
    /**
     * Returns the parent class object or <b>null</b>
     *
     * @return PHP_Reflection_AST_ClassI
     */
    public function getParent()
    {
        return $this->_parent;
    }
    
    /**
     * Sets the parent class object.
     *
     * @param PHP_Reflection_AST_Class $parent The parent class.
     * 
     * @return void
     */
    public function setParent(PHP_Reflection_AST_Class $parent = null)
    {
        $this->_parent = $parent;
    }
    
    /**
     * Returns the type of this property. This method will return <b>null</b>
     * for all scalar type, only class properties will have a type.
     *
     * @return PHP_Reflection_AST_ClassOrInterfaceI
     */
    public function getType()
    {
        return $this->_type;
    }
    
    /**
     * Sets the class or interface type for this property node.
     *
     * @param PHP_Reflection_AST_ClassOrInterfaceI $type The property type.
     * 
     * @return void
     */
    public function setType(PHP_Reflection_AST_ClassOrInterfaceI $type)
    {
        $this->_type = $type;
    }
    
    /**
     * Returns the value of this property.
     *
     * @return PHP_Reflection_AST_MemberValueI
     */
    public function getValue()
    {
        return $this->_value;
    }
    
    /**
     * Sets the declared value for this property.
     *
     * @param PHP_Reflection_AST_MemberValueI $value The value instance.
     * 
     * @return void
     */
    public function setValue(PHP_Reflection_AST_MemberValueI $value)
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
        $visitor->visitProperty($this);
    }
                   
}