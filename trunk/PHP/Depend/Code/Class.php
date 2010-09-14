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
 * Represents a php class node.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Code_Class implements PHP_Depend_Code_Node
{
    /**
     * The name for this class.
     *
     * @type string
     * @var string $name
     */
    protected $name = '';
    
    /**
     * The source file for this class.
     *
     * @type string
     * @var string $sourceFile
     */
    protected $sourceFile = '';
    
    /**
     * The parent package for this class.
     *
     * @type PHP_Depend_Code_Package
     * @var PHP_Depend_Code_Package $package
     */
    protected $package = null;
    
    /**
     * Marks this class as abstract.
     *
     * @type boolean
     * @var boolean $abstract
     */
    protected $abstract = false;
    
    /**
     * List of {@link PHP_Depend_Code_Method} objects in this class.
     *
     * @type array<PHP_Depend_Code_Method>
     * @var array(PHP_Depend_Code_Method) $methods
     */
    protected $methods = array();
    
    /**
     * List of {@link PHP_Depend_Code_Class} objects this class depends on.
     *
     * @type array<PHP_Depend_Code_Class>
     * @var array(PHP_Depend_Code_Class) $dependencies
     */
    protected $dependencies = array();
    
    /**
     * Constructs a new class for the given <b>$name</b> and <b>$sourceFile</b>.
     *
     * @param string $name       The class name.
     * @param string $sourceFile The source file for this class.
     */
    public function __construct($name, $sourceFile)
    {
        $this->name       = $name;
        $this->sourceFile = $sourceFile;
    }
    
    /**
     * Returns the class name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Returns the source file for this class.
     *
     * @return string
     */
    public function getSourceFile()
    {
        return $this->sourceFile;
    }
    
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
     * Returns all {@link PHP_Depend_Code_Method} object in this class.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getMethods()
    {
        return new PHP_Depend_Code_NodeIterator($this->methods);
    }
    
    /**
     * Adds the given method to this class.
     *
     * @param PHP_Depend_Code_Method $method A new class method.
     * 
     * @return void
     */
    public function addMethod(PHP_Depend_Code_Method $method)
    {
        if ($method->getClass() !== null) {
            $method->getClass()->removeMethod($method);
        }
        // Set this as owner class
        $method->setClass($this);
        // Store clas
        $this->methods[] = $method;
    }
    
    /**
     * Removes the given method from this class.
     *
     * @param PHP_Depend_Code_Method $method The method to remove.
     * 
     * @return void
     */
    public function removeMethod(PHP_Depend_Code_Method $method)
    {
        if (($i = array_search($method, $this->methods, true)) !== false) {
            // Remove this as owner
            $method->setClass(null);
            // Remove from internal list
            unset($this->methods[$i]);
        }
    }
    
    /**
     * Returns all {@link PHP_Depend_Code_Class} objects this class depends on.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getDependencies()
    {
        return new PHP_Depend_Code_NodeIterator($this->dependencies);
    }
    
    /**
     * Adds the given {@link PHP_Depend_Code_Class} object as dependency.
     *
     * @param PHP_Depend_Code_Class $class A class this function depends on.
     * 
     * @return void
     */
    public function addDependency(PHP_Depend_Code_Class $class)
    {
        if (array_search($class, $this->dependencies, true) === false) {
            $this->dependencies[] = $class;
        }
    }
    
    /**
     * Removes the given {@link PHP_Depend_Code_Class} object from the dependency
     * list.
     *
     * @param PHP_Depend_Code_Class $class A class to remove.
     * 
     * @return void
     */
    public function removeDependency(PHP_Depend_Code_Class $class)
    {
        if (($i = array_search($class, $this->dependencies, true)) !== false) {
            // Remove from internal list
            unset($this->dependencies[$idx]);
        }
    }
    
    /**
     * Returns the parent package for this class.
     *
     * @return PHP_Depend_Code_Package
     */
    public function getPackage()
    {
        return $this->package;
    }
    
    /**
     * Sets the parent package for this class.
     *
     * @param PHP_Depend_Code_Package $package The parent package.
     * 
     * @return void
     */
    public function setPackage(PHP_Depend_Code_Package $package = null)
    {
        $this->package = $package;
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
        $visitor->visitClass($this);
    }
}