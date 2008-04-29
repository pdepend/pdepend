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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/Node.php';
require_once 'PHP/Depend/Code/NodeIterator.php';

/**
 * Represents a php package node.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Code_Package implements PHP_Depend_Code_Node
{
    /**
     * The package name.
     *
     * @type string
     * @var string $name
     */
    protected $name = '';
    
    /**
     * List of all {@link PHP_Depend_Code_Type} objects for this package.
     *
     * @type array<PHP_Depend_Code_Type>
     * @var array(PHP_Depend_Code_Type) $types
     */
    protected $types = array();
    
    /**
     * List of all standalone {@link PHP_Depend_Code_Function} objects in this
     * package.
     *
     * @type array<PHP_Depend_Code_Function>
     * @var array(PHP_Depend_Code_Function) $functions
     */
    protected $functions = array();
    
    /**
     * Constructs a new package for the given <b>$name</b>
     *
     * @param string $name The package name.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }
    
    /**
     * Returns the package name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Returns an iterator with all {@link PHP_Depend_Code_Class} instances
     * within this package.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getClasses()
    {
        return new PHP_Depend_Code_NodeIterator(
            $this->types, 
            'PHP_Depend_Code_Class'
        );
    }
    
    /**
     * Returns an iterator with all {@link PHP_Depend_Code_Interface} instances
     * within this package.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getInterfaces()
    {
        return new PHP_Depend_Code_NodeIterator(
            $this->types, 
            'PHP_Depend_Code_Interface'
        );
    }
    
    /**
     * Returns all {@link PHP_Depend_Code_Type} objects in this package.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getTypes()
    {
        return new PHP_Depend_Code_NodeIterator($this->types);
    }
    
    /**
     * Adds the given type to this package.
     *
     * @param PHP_Depend_Code_Type $type The new package type.
     * 
     * @return void
     */
    public function addType(PHP_Depend_Code_Type $type)
    {
        if ($type->getPackage() !== null) {
            $type->getPackage()->removeType($type);
        }
        
        // Set this as class package
        $type->setPackage($this);
        // Append class to internal list
        $this->types[] = $type;
    }
    
    /**
     * Removes the given type instance from this package.
     *
     * @param PHP_Depend_Code_Type $type The type instance to remove.
     * 
     * @return void
     */
    public function removeType(PHP_Depend_Code_Type $type)
    {
        if (($i = array_search($type, $this->types, true)) !== false) {
            // Remove class from internal list
            unset($this->types[$i]);
            // Remove this as parent
            $type->setPackage(null);
        }
    }
    
    /**
     * Returns all {@link PHP_Depend_Code_Function} objects in this package.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getFunctions()
    {
        return new PHP_Depend_Code_NodeIterator($this->functions);
    }
    
    /**
     * Adds the given function to this package.
     *
     * @param PHP_Depend_Code_Function $function The new package function.
     * 
     * @return void
     */
    public function addFunction(PHP_Depend_Code_Function $function)
    {
        if ($function->getPackage() !== null) {
            $function->getPackage()->removeFunction($function);
        }

        // Set this as function package
        $function->setPackage($this);
        // Append function to internal list
        $this->functions[] = $function;
    }
    
    /**
     * Removes the given function from this package.
     *
     * @param PHP_Depend_Code_Function $function The function to remove
     * 
     * @return void
     */
    public function removeFunction(PHP_Depend_Code_Function $function)
    {
        if (($i = array_search($function, $this->functions, true)) !== false) {
            // Remove function from internal list
            unset($this->functions[$i]);
            // Remove this as parent
            $function->setPackage(null);
        }
    }
    
    /**
     * Returns <b>true</b> if this package or one of the efferent packages 
     * contains a cycle.
     *
     * @return boolean
     */
    public function containsCycle()
    {
        return $this->collectCycle(new SplObjectStorage());
    }

    /**
     * Collects a single cycle that is reachable by this package. All packages
     * that are part of the cylce are stored in the given {@link SplObjectStorage}
     * instance. 
     *
     * @param SplObjectStorage $storage The cycle package object store.
     * 
     * @return boolean If this method detects a cycle the return value is <b>true</b>
     *                 otherwise this method will return <b>false</b>.
     */
    public function collectCycle(SplObjectStorage $storage)
    {
        if ($storage->contains($this)) {
            $storage->rewind();
            while (($tmp = $storage->current()) !== $this) {
                $storage->detach($tmp);
            }
            return true;
        }
        
        $storage->attach($this);

        foreach ($this->getTypes() as $class) {
            foreach ($class->getDependencies() as $dependency) {
                $package = $dependency->getPackage();
                if ($package !== $this && $package->collectCycle($storage)) {
                    return true;
                }
            }
            foreach ($class->getMethods() as $method) {
                foreach ($method->getDependencies() as $dependency) {
                    $package = $dependency->getPackage();
                    if ($package !== $this && $package->collectCycle($storage)) {
                        return true;
                    }
                }                
            }
        }
        $storage->detach($this);
        
        return false;
    }

    /**
     * Visitor method for node tree traversal.
     *
     * @param PHP_Depend_Code_NodeVisitor $visitor The context visitor 
     *                                             implementation.
     * 
     * @return void
     */
    public function accept(PHP_Depend_Code_NodeVisitor $visitor)
    {
        $visitor->visitPackage($this);
    }
}