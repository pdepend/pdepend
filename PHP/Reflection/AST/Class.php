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

require_once 'PHP/Reflection/AST/AbstractClassOrInterface.php';
require_once 'PHP/Reflection/AST/ClassI.php';
require_once 'PHP/Reflection/AST/Iterator.php';
require_once 'PHP/Reflection/Exceptions/UnknownNodeException.php';

/**
 * Represents a php class node.
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
class PHP_Reflection_AST_Class 
       extends PHP_Reflection_AST_AbstractClassOrInterface
    implements PHP_Reflection_AST_ClassI
{
    /**
     * Declared modifiers for this class.
     * 
     * <ul>
     *   <li>ReflectionClass::IS_EXPLICIT_ABSTRACT</li>
     *   <li>ReflectionClass::IS_FINAL</li>
     *   <li>ReflectionClass::IS_IMPLICIT_ABSTRACT</li>
     * </ul>
     *
     * @var unknown_type
     */
    private $_modifiers = 0;
    
    /**
     * The parent class instance for this class.
     *
     * @var PHP_Reflection_AST_Class $_parentClass
     */
    private $_parentClass = null;
    
    /**
     * List of direct child classes of this class.
     *
     * @var array(PHP_Reflection_AST_ClassI) $_childClasses
     */
    private $_childClasses = array();
    
    /**
     * List of implemented interfaces for this class.
     *
     * @var array(PHP_Reflection_AST_InterfaceI) $_implementedInterfaces
     */
    private $_implementedInterfaces = array();
    
    /**
     * List of associated properties.
     *
     * @var array(PHP_Reflection_AST_Property) $_properties
     */
    private $_properties = array();
    
    /**
     * Sets the modifiers for this class.
     *
     * @param integer $modifiers The class modifiers.
     * 
     * @return void
     */
    public function setModifiers($modifiers)
    {
        $this->_modifiers = (int) $modifiers;
    }
    
    /**
     * Returns the declared modifiers for this class.
     *
     * @return integer
     */
    public function getModifiers()
    {
        return $this->_modifiers;
    }
    
    /**
     * Returns <b>true</b> if this is an abstract class or an interface.
     *
     * @return boolean
     */
    public function isAbstract()
    {
        return (
            self::IS_EXPLICIT_ABSTRACT === (
                $this->_modifiers & self::IS_EXPLICIT_ABSTRACT
            ) || (
                self::IS_IMPLICIT_ABSTRACT === (
                    $this->_modifiers & self::IS_IMPLICIT_ABSTRACT
                )
            )
        );
    }
    
    /**
     * Returns <b>true</b> if this class is markes as final.
     *
     * @return boolean
     */
    public function isFinal()
    {
        return (self::IS_FINAL === ($this->_modifiers & self::IS_FINAL));
    }
    
    /**
     * Returns the parent class or <b>null</b> if this class has no parent.
     *
     * @return PHP_Reflection_AST_ClassI
     */
    public function getParentClass()
    {
        return $this->filterNode($this->_parentClass);
    }
    
    /**
     * Sets the parent class node for this class.
     *
     * @param PHP_Reflection_AST_ClassI $parentClass The parent class.
     * 
     * @return void
     */
    public function setParentClass(PHP_Reflection_AST_ClassI $parentClass)
    {
        // Store parent class reference
        $this->_parentClass = $parentClass;
    }
    
    /**
     * Returns a node iterator with all implemented interfaces.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    public function getImplementedInterfaces()
    {
        $interfaces = array();
        foreach ($this->_implementedInterfaces as $interface) {
            // Add this interface first
            $interfaces[] = $interface;
            // Append all parent interfaces
            foreach ($interface->getParentInterfaces() as $parentInterface) {
                if (in_array($parentInterface, $interfaces, true) === false) {
                    $interfaces[] = $parentInterface;
                }
            }
        }
        
        if (($parent = $this->getParentClass()) !== null) {
            foreach ($parent->getImplementedInterfaces() as $interface) {
                $interfaces[] = $interface;
            }
        }
        return new PHP_Reflection_AST_Iterator($interfaces);
    }
    
    /**
     * Adds an interface node to the list of implemented interfaces.
     *
     * @param PHP_Reflection_AST_InterfaceI $interface
     * The implemented interface node.
     * 
     * @return void
     */
    public function addImplementedInterface(PHP_Reflection_AST_InterfaceI $interface)
    {
        // Each class can implement an interface only one time
        if (in_array($interface, $this->_implementedInterfaces, true) === false) {
            // Store interface reference
            $this->_implementedInterfaces[] = $interface;
        }
    }
    
    /**
     * Returns all {@link PHP_Reflection_AST_ClassOrInterfaceI} objects this 
     * type depends on.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    public function getDependencies()
    {
        $dependencies = $this->_implementedInterfaces;
        if ($this->_parentClass !== null) {
            $dependencies[] = $this->_parentClass;
        }

        return new PHP_Reflection_AST_Iterator($dependencies);
    }
    
    /**
     * Will return a class property for the given node. Please note that this
     * method requires a property name without leading '$' character.
     *
     * @param string $name The property name.
     * 
     * @return PHP_Reflection_AST_PropertyI The property instance.
     * @throws PHP_Reflection_Exceptions_UnknownNodeException If no node exists
     *                                                        for the given name.
     */
    public function getProperty($name)
    {
        foreach ($this->_properties as $property) {
            if ($property->getName() === $name) {
                return $property;
            }
        }
        throw new PHP_Reflection_Exceptions_UnknownNodeException($name);
    }
    
    /**
     * Returns all properties for this class.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    public function getProperties()
    {
        return new PHP_Reflection_AST_Iterator($this->_properties);
    }
    
    /**
     * Adds a new property to this class instance.
     *
     * @param PHP_Reflection_AST_Property $property The new class property.
     * 
     * @return PHP_Reflection_AST_Property
     */
    public function addProperty(PHP_Reflection_AST_Property $property)
    {
        if (in_array($property, $this->_properties, true) === false) {
            // Add to internal list
            $this->_properties[] = $property;
            // Set this as parent
            $property->setParent($this);
        }
        return $property;
    }
    
    /**
     * Removes the given property from this class.
     *
     * @param PHP_Reflection_AST_Property $property The property to remove.
     * 
     * @return void
     */
    public function removeProperty(PHP_Reflection_AST_Property $property)
    {
        if (($i = array_search($property, $this->_properties, true)) !== false) {
            // Remove this as parent
            $property->setParent(null);
            // Remove from internal property list
            unset($this->_properties[$i]);
        }
    }
    
    /**
     * Checks that this user type is a subtype of the given <b>$classOrInterface</b>
     * instance.
     *
     * @param PHP_Reflection_AST_ClassOrInterfaceI $classOrInterface
     * The possible parent node.
     * 
     * @return boolean
     */
    public function isSubtypeOf(
                        PHP_Reflection_AST_ClassOrInterfaceI $classOrInterface)
    {
        if ($classOrInterface === $this) {
            return true;
        } else if ($classOrInterface instanceof PHP_Reflection_AST_Interface) {
            foreach ($this->getImplementedInterfaces() as $interface) {
                if ($interface === $classOrInterface) {
                    return true;
                }
            }
        } else if (($parent = $this->getParentClass()) !== null) {
            if ($parent === $classOrInterface) {
                return true;
            }
            return $parent->isSubtypeOf($classOrInterface);
        }
        return false;
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
        $visitor->visitClass($this);
    }
}