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
     * List of all {@link PHP_Depend_Code_Class} objects for this package.
     *
     * @type array<PHP_Depend_Code_Class>
     * @var array(PHP_Depend_Code_Class) $classes
     */
    protected $classes = array();
    
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
     * Returns all {@link PHP_Depend_Code_Class} objects in this package.
     *
     * @return Iterator
     */
    public function getClasses()
    {
        return new ArrayIterator($this->classes);
    }
    
    /**
     * Adds the given class to this package.
     *
     * @param PHP_Depend_Code_Class $class The new package class.
     * 
     * @return void
     */
    public function addClass(PHP_Depend_Code_Class $class)
    {
        if ($class->getPackage()) {
            $class->getPackage()->removeClass($class);
        }
        
        // Set this as class package
        $class->setPackage($this);
        // Append class to internal list
        $this->classes[] = $class;
    }
    
    /**
     * Removes the given class from this package.
     *
     * @param PHP_Depend_Code_Class $class The class to remove.
     * 
     * @return void
     */
    public function removeClass(PHP_Depend_Code_Class $class)
    {
        // Remove this package
        $class->setPackage(null);
        // Remove class from internal list
        foreach ($this->classes as $i => $c) {
            if ($c === $class) {
                unset($this->classes[$i]);
                break;
            }
        }
    }
    
    /**
     * Returns all {@link PHP_Depend_Code_Function} objects in this package.
     *
     * @return Iterator
     */
    public function getFunctions()
    {
        return new ArrayIterator($this->functions);
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
        foreach ($this->functions as $i => $f) {
            if ($f === $function) {
                // Remove function from internal list
                unset($this->functions[$i]);
                // Remove this as parent
                $function->setPackage(null);
                
                break;
            }
        }
    }
    
    /**
     * Visitor method for node tree traversal.
     *
     * @param PHP_Depend_Code_NodeVisitor $visitor The context visitor implementation.
     * 
     * @return void
     */
    public function accept(PHP_Depend_Code_NodeVisitor $visitor)
    {
        $visitor->visitPackage($this);
    }
}