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

require_once 'PHP/Reflection/Ast/AbstractCallable.php';
require_once 'PHP/Reflection/Ast/VisibilityAwareI.php';

/**
 * Represents a php method node.
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
class PHP_Reflection_Ast_Method 
    extends PHP_Reflection_Ast_AbstractCallable
    implements PHP_Reflection_Ast_VisibilityAwareI
{
    /**
     * Marks this method as abstract.
     *
     * @type boolean
     * @var boolean $abstract
     */
    protected $abstract = false;
    
    /**
     * Set defined visibility for this method.
     *
     * @type integer
     * @var integer $visibility
     */
    protected $visibility = -1;
    
    /**
     * The parent type object.
     *
     * @type PHP_Reflection_Ast_AbstractType
     * @var PHP_Reflection_Ast_AbstractType $parent
     */
    protected $parent = null;
    
    /**
     * Method position within its parent class or interface.
     *
     * @type integer
     * @var integer $_position
     */
    private $_position = 0;
    
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
     * Returns <b>true</b> if this is an abstract method.
     *
     * @return boolean
     */
    public function isAbstract()
    {
        return $this->abstract;
    }
    
    /**
     * Marks this as an abstract method.
     *
     * @param boolean $abstract Set this to <b>true</b> for an abstract method.
     * 
     * @return void
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;
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
     * Returns the parent type object or <b>null</b>
     *
     * @return PHP_Reflection_Ast_AbstractType|null
     */
    public function getParent()
    {
        return $this->parent;
    }
    
    /**
     * Sets the parent type object.
     *
     * @param PHP_Reflection_Ast_AbstractType $parent The parent type.
     * 
     * @return void
     */
    public function setParent(PHP_Reflection_Ast_AbstractType $parent = null)
    {
        $this->parent = $parent;
    }
    
    /**
     * Returns the position of this method within the parent class or interface.
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->_position;
    }
    
    /**
     * Sets the source position of this method.
     *
     * @param integer $position Position within the parent class or interface.
     * 
     * @return void
     */
    public function setPosition($position)
    {
        $this->_position = (int) $position;
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
        $visitor->visitMethod($this);
    }
}