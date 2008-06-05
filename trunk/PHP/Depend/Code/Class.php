<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pmanuel-pichler.de>.
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
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
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
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Code_Class extends PHP_Depend_Code_AbstractType
{
    /**
     * Marks this class as abstract.
     *
     * @type boolean
     * @var boolean $abstract
     */
    protected $abstract = false;
    
    /**
     * List of associated properties.
     *
     * @type array<PHP_Depend_Code_Property>
     * @var array(PHP_Depend_Code_Property) $properties
     */
    protected $properties = array();
    
    /**
     * Returns <b>true</b> if this is an abstract class or an interface.
     *
     * @return boolean
     */
    public function isAbstract()
    {
        return $this->abstract;
    }
    
    /**
     * Marks this as an abstract class or interface.
     *
     * @param boolean $abstract Set this to <b>true</b> for an abstract class.
     * 
     * @return void
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;
    }
    
    /**
     * Returns the parent class or <b>null</b> if this class has no parent.
     *
     * @return PHP_Depend_Code_Class
     */
    public function getParentClass()
    {
        // We know that a class has only 'extends' and 'implements' dependencies
        foreach ($this->getDependencies() as $dependency) {
            if ($dependency instanceof PHP_Depend_Code_Class) {
                return $dependency;
            }
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
        $type   = 'PHP_Depend_Code_Interface';
        $filter = new PHP_Depend_Code_NodeIterator_TypeFilter($type);
        
        $interfaces = $this->getDependencies();
        $interfaces->addFilter($filter);
        
        return $interfaces;
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
        return new PHP_Depend_Code_NodeIterator($this->properties);
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
        if (in_array($property, $this->properties, true) === false) {
            // Add to internal list
            $this->properties[] = $property;
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
        if (($i = array_search($property, $this->properties, true)) !== false) {
            // Remove this as parent
            $property->setParent(null);
            // Remove from internal property list
            unset($this->properties[$i]);
        }
    }
    
    /**
     * Visitor method for node tree traversal.
     *
     * @param PHP_Depend_Code_NodeVisitorI $visitor The context visitor 
     *                                              implementation.
     * 
     * @return void
     */
    public function accept(PHP_Depend_Code_NodeVisitorI $visitor)
    {
        $visitor->visitClass($this);
    }
}