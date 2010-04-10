<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/AbstractType.php';
require_once 'PHP/Depend/Code/NodeIterator.php';

/**
 * Represents a php class node.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Code_Class extends PHP_Depend_Code_AbstractType
{
    /**
     * Marks this class as abstract.
     *
     * @var boolean $_abstract
     */
    private $_abstract = false;

    /**
     * List of associated properties.
     *
     * @var array(PHP_Depend_Code_Property) $_properties
     */
    private $_properties = array();

    /**
     * The modifiers for this class instance.
     *
     * @var integer $_modifiers
     */
    private $_modifiers = 0;

    /**
     * Returns <b>true</b> if this is an abstract class or an interface.
     *
     * @return boolean
     */
    public function isAbstract()
    {
        return (($this->_modifiers & PHP_Depend_ConstantsI::IS_EXPLICIT_ABSTRACT)
                                 === PHP_Depend_ConstantsI::IS_EXPLICIT_ABSTRACT);
    }

    /**
     * This method will return <b>true</b> when this class is declared as final.
     *
     * @return boolean
     */
    public function isFinal()
    {
        return (($this->_modifiers & PHP_Depend_ConstantsI::IS_FINAL)
                                 === PHP_Depend_ConstantsI::IS_FINAL);
    }

    /**
     * Returns the parent class or <b>null</b> if this class has no parent.
     *
     * @return PHP_Depend_Code_Class
     */
    public function getParentClass()
    {
        // We know that a class has only 'extends' and 'implements' dependencies
        foreach ($this->getUnfilteredRawDependencies() as $dependency) {
            if (($dependency instanceof PHP_Depend_Code_Class) === false) {
                continue;
            }
            $collection = PHP_Depend_Code_Filter_Collection::getInstance();
            if ($collection->accept($dependency) === false) {
                return null;
            }
            return $dependency;
        }
        return null;
    }

    /**
     * Returns a node iterator with all implemented interfaces.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getImplementedInterfaces()
    {
        $nodes = array();
        foreach ($this->getUnfilteredRawDependencies() as $dependency) {
            // Add parent interfaces of parent class
            if ($dependency instanceof PHP_Depend_Code_Class) {
                foreach ($dependency->getImplementedInterfaces() as $interface) {
                    $nodes[] = $interface;
                }
                continue;
            }

            // Add this interface first
            $nodes[] = $dependency;
            // Append all parent interfaces
            foreach ($dependency->getParentInterfaces() as $parentInterface) {
                if (in_array($parentInterface, $nodes, true) === false) {
                    $nodes[] = $parentInterface;
                }
            }
        }
        return new PHP_Depend_Code_NodeIterator($nodes);
    }

    /**
     * Returns an iterator with all child classes.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getChildClasses()
    {
        return new PHP_Depend_Code_NodeIterator($this->children);
    }

    /**
     * Returns all properties for this class.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getProperties()
    {
        return new PHP_Depend_Code_NodeIterator($this->_properties);
    }

    /**
     * Adds a new property to this class instance.
     *
     * @param PHP_Depend_Code_Property $property The new class property.
     *
     * @return PHP_Depend_Code_Property
     */
    public function addProperty(PHP_Depend_Code_Property $property)
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
     * @param PHP_Depend_Code_Property $property The property to remove.
     *
     * @return void
     */
    public function removeProperty(PHP_Depend_Code_Property $property)
    {
        if (($i = array_search($property, $this->_properties, true)) !== false) {
            // Remove this as parent
            $property->setParent(null);
            // Remove from internal property list
            unset($this->_properties[$i]);
        }
    }

    /**
     * Checks that this user type is a subtype of the given <b>$type</b> instance.
     *
     * @param PHP_Depend_Code_AbstractType $type The possible parent type instance.
     *
     * @return boolean
     */
    public function isSubtypeOf(PHP_Depend_Code_AbstractType $type)
    {
        if ($type === $this) {
            return true;
        } else if ($type instanceof PHP_Depend_Code_Interface) {
            foreach ($this->getImplementedInterfaces() as $interface) {
                if ($interface === $type) {
                    return true;
                }
            }
        } else if (($parent = $this->getParentClass()) !== null) {
            if ($parent === $type) {
                return true;
            }
            return $parent->isSubtypeOf($type);
        }
        return false;
    }

    /**
     * Returns the declared modifiers for this type.
     *
     * @return integer
     * @since 0.9.4
     */
    public function getModifiers()
    {
        return $this->_modifiers;
    }

    /**
     * This method sets a OR combined integer of the declared modifiers for this
     * node.
     *
     * This method will throw an exception when the value of given <b>$modifiers</b>
     * contains an invalid/unexpected modifier
     *
     * @param integer $modifiers The declared modifiers for this node.
     *
     * @return void
     * @throws InvalidArgumentException If the given modifier contains unexpected
     *                                  values.
     * @since 0.9.4
     */
    public function setModifiers($modifiers)
    {
        if ($this->_modifiers !== 0) {
            return;
        }

        $expected = ~PHP_Depend_ConstantsI::IS_EXPLICIT_ABSTRACT
                  & ~PHP_Depend_ConstantsI::IS_IMPLICIT_ABSTRACT
                  & ~PHP_Depend_ConstantsI::IS_FINAL;

        if (($expected & $modifiers) !== 0) {
            throw new InvalidArgumentException('Invalid class modifier given.');
        }

        $this->_modifiers = $modifiers;
    }

    /**
     * Visitor method for node tree traversal.
     *
     * @param PHP_Depend_VisitorI $visitor The context visitor
     *                                              implementation.
     *
     * @return void
     */
    public function accept(PHP_Depend_VisitorI $visitor)
    {
        $visitor->visitClass($this);
    }

    // DEPRECATED METHODS
    // @codeCoverageIgnoreStart

    /**
     * Marks this as an abstract class or interface.
     *
     * @param boolean $abstract Set this to <b>true</b> for an abstract class.
     *
     * @return void
     * @deprecated Since version 0.9.4, use setModifiers() instead.
     */
    public function setAbstract($abstract)
    {
        fwrite(STDERR, 'Since 0.9.4 setAbstract() is deprecated.' . PHP_EOL);
        $this->_modifiers |= PHP_Depend_ConstantsI::IS_EXPLICIT_ABSTRACT;
    }
    
    // @codeCoverageIgnoreEnd
}