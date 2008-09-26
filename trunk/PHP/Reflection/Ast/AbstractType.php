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
require_once 'PHP/Reflection/Ast/DependencyAwareI.php';
require_once 'PHP/Reflection/Util/UUID.php';

/**
 * Represents an interface or a class type.
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
abstract class PHP_Reflection_Ast_AbstractType
       extends PHP_Reflection_Ast_AbstractItem 
    implements PHP_Reflection_Ast_NodeI,
               PHP_Reflection_Ast_DependencyAwareI
{
    /**
     * List of {@link PHP_Reflection_Ast_AbstractType} objects this type depends on.
     *
     * @type array<PHP_Reflection_Ast_AbstractType>
     * @var array(PHP_Reflection_Ast_AbstractType) $dependencies
     */
    protected $dependencies = array();
    
    /**
     * List of {@link PHP_Reflection_Ast_AbstractType} objects that extend or implement 
     * this type. 
     *
     * @type array<PHP_Reflection_Ast_AbstractType>
     * @var array(PHP_Reflection_Ast_AbstractType) $children
     */
    protected $children = array();
    
    /**
     * The parent package for this class.
     *
     * @type PHP_Reflection_Ast_Package
     * @var PHP_Reflection_Ast_Package $_package
     */
    private $_package = null;
    
    /**
     * List of {@link PHP_Reflection_Ast_Method} objects in this class.
     *
     * @type array<PHP_Reflection_Ast_Method>
     * @var array(PHP_Reflection_Ast_Method) $_methods
     */
    private $_methods = array();
    
    /**
     * The tokens for this type.
     *
     * @type array<array>
     * @var array(array) $_tokens
     */
    private $_tokens = array();
    
    /**
     * List of {@link PHP_Reflection_Ast_TypeConstant} objects that belong to this 
     * type. 
     *
     * @type array<PHP_Reflection_Ast_TypeConstant>
     * @var array(PHP_Reflection_Ast_TypeConstant) $_constants
     */
    private $_constants = array();
    
    /**
     * Type position within the source code file.
     *
     * @type integer
     * @var integer $_position
     */
    private $_position = 0;
    
    /**
     * Returns all {@link PHP_Reflection_Ast_TypeConstant} objects in this type.
     *
     * @return PHP_Reflection_Ast_Iterator
     */
    public function getConstants()
    {
        return new PHP_Reflection_Ast_Iterator($this->_constants);
    }
    
    /**
     * Adds the given constant to this type.
     *
     * @param PHP_Reflection_Ast_TypeConstant $constant A new type constant.
     * 
     * @return PHP_Reflection_Ast_TypeConstant
     */
    public function addConstant(PHP_Reflection_Ast_TypeConstant $constant)
    {
        if ($constant->getParent() !== null) {
            $constant->getParent()->removeConstant($constant);
        }
        // Set this as owner type
        $constant->setParent($this);
        // Store constant
        $this->_constants[] = $constant;
        
        return $constant;
    }
    
    /**
     * Removes the given constant from this type.
     *
     * @param PHP_Reflection_Ast_TypeConstant $constant The constant to remove.
     * 
     * @return void
     */
    public function removeConstant(PHP_Reflection_Ast_TypeConstant $constant)
    {
        if (($i = array_search($constant, $this->_constants, true)) !== false) {
            // Remove this as owner
            $constant->setParent(null);
            // Remove from internal list
            unset($this->_constants[$i]);
        }
    }
    
    /**
     * Returns all {@link PHP_Reflection_Ast_Method} objects in this type.
     *
     * @return PHP_Reflection_Ast_Iterator
     */
    public function getMethods()
    {
        return new PHP_Reflection_Ast_Iterator($this->_methods);
    }
    
    /**
     * Adds the given method to this type.
     *
     * @param PHP_Reflection_Ast_Method $method A new type method.
     * 
     * @return PHP_Reflection_Ast_Method
     */
    public function addMethod(PHP_Reflection_Ast_Method $method)
    {
        if ($method->getParent() !== null) {
            $method->getParent()->removeMethod($method);
        }
        // Set this as owner type
        $method->setParent($this);
        // Store method
        $this->_methods[] = $method;
        
        return $method;
    }
    
    /**
     * Removes the given method from this class.
     *
     * @param PHP_Reflection_Ast_Method $method The method to remove.
     * 
     * @return void
     */
    public function removeMethod(PHP_Reflection_Ast_Method $method)
    {
        if (($i = array_search($method, $this->_methods, true)) !== false) {
            // Remove this as owner
            $method->setParent(null);
            // Remove from internal list
            unset($this->_methods[$i]);
        }
    }
    
    /**
     * Returns all {@link PHP_Reflection_Ast_AbstractType} objects this type depends on.
     *
     * @return PHP_Reflection_Ast_Iterator
     */
    public function getDependencies()
    {
        return new PHP_Reflection_Ast_Iterator($this->dependencies);
    }
    
    /**
     * Adds the given {@link PHP_Reflection_Ast_AbstractType} object as dependency.
     *
     * @param PHP_Reflection_Ast_AbstractType $type A type this function depends on.
     * 
     * @return void
     */
    public function addDependency(PHP_Reflection_Ast_AbstractType $type)
    {
        if (array_search($type, $this->dependencies, true) === false) {
            // Store type dependency
            $this->dependencies[] = $type;
            // Add this as child type
            $type->addChildType($this);
        }
    }
    
    /**
     * Removes the given {@link PHP_Reflection_Ast_AbstractType} object from the 
     * dependency list.
     *
     * @param PHP_Reflection_Ast_AbstractType $type A type to remove.
     * 
     * @return void
     */
    public function removeDependency(PHP_Reflection_Ast_AbstractType $type)
    {
        if (($i = array_search($type, $this->dependencies, true)) !== false) {
            // Remove from internal list
            unset($this->dependencies[$i]);
            // Remove this as child type
            $type->removeChildType($this);
        }
    }
    
    /**
     * Returns an iterator with all child types for this type.
     *
     * @return PHP_Reflection_Ast_Iterator
     */
    public function getChildTypes()
    {
        return new PHP_Reflection_Ast_Iterator($this->children);
    }
    
    /**
     * Adds a type instance that extends or implements this type.
     *
     * @param PHP_Reflection_Ast_AbstractType $type The child type instance.
     * 
     * @return PHP_Reflection_Ast_AbstractType
     */
    public function addChildType(PHP_Reflection_Ast_AbstractType $type)
    {
        if (array_search($type, $this->children, true) === false) {
            // First add the type as child
            $this->children[] = $type;
            // Try to add this as dependency...
            $type->addDependency($this);
        }
        return $type;
    }
    
    /**
     * Removes the given type from the list of known children.
     *
     * @param PHP_Reflection_Ast_AbstractType $type The child type instance.
     * 
     * @return void
     */
    public function removeChildType(PHP_Reflection_Ast_AbstractType $type)
    {
        if (($i = array_search($type, $this->children, true)) !== false) {
            // First remove this child
            unset($this->children[$i]);
            // Try to remove this as dependency
            $type->removeDependency($this);
        }
    }
    
    /**
     * Returns an <b>array</b> with all tokens within this type.
     *
     * @return array(array)
     */
    public function getTokens()
    {
        return $this->_tokens;
    }
    
    /**
     * Sets the tokens for this type.
     *
     * @param array(array) $tokens The generated tokens.
     * 
     * @return void
     */
    public function setTokens(array $tokens)
    {
        $this->_tokens = $tokens;
    }
    
    /**
     * Returns the parent package for this class.
     *
     * @return PHP_Reflection_Ast_Package
     */
    public function getPackage()
    {
        return $this->_package;
    }
    
    /**
     * Sets the parent package for this class.
     *
     * @param PHP_Reflection_Ast_Package $package The parent package.
     * 
     * @return void
     */
    public function setPackage(PHP_Reflection_Ast_Package $package = null)
    {
        $this->_package = $package;
    }
    
    /**
     * Returns the position of this type within the source file.
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->_position;
    }
    
    /**
     * Sets the source position of this type.
     *
     * @param integer $position Position within the source file.
     * 
     * @return void
     */
    public function setPosition($position)
    {
        $this->_position = (int) $position;
    }
    
    /**
     * Returns <b>true</b> if this is an abstract class or an interface.
     *
     * @return boolean
     */
    public abstract function isAbstract();
    
    /**
     * Checks that this user type is a subtype of the given <b>$type</b> instance.
     *
     * @param PHP_Reflection_Ast_AbstractType $type The possible parent type instance.
     * 
     * @return boolean
     */
    public abstract function isSubtypeOf(PHP_Reflection_Ast_AbstractType $type);
}