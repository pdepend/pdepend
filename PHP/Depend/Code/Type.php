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

require_once 'PHP/Depend/Code/AbstractItem.php';
require_once 'PHP/Depend/Code/DependencyNode.php';
require_once 'PHP/Depend/Util/UUID.php';

/**
 * Represents an interface or a class type.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
abstract class PHP_Depend_Code_Type
       extends PHP_Depend_Code_AbstractItem 
    implements PHP_Depend_Code_DependencyNode
{   
    /**
     * The parent package for this class.
     *
     * @type PHP_Depend_Code_Package
     * @var PHP_Depend_Code_Package $package
     */
    protected $package = null;
    
    /**
     * List of {@link PHP_Depend_Code_Method} objects in this class.
     *
     * @type array<PHP_Depend_Code_Method>
     * @var array(PHP_Depend_Code_Method) $methods
     */
    protected $methods = array();
    
    /**
     * List of {@link PHP_Depend_Code_Type} objects this type depends on.
     *
     * @type array<PHP_Depend_Code_Type>
     * @var array(PHP_Depend_Code_Type) $dependencies
     */
    protected $dependencies = array();
    
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
        if ($method->getParent() !== null) {
            $method->getParent()->removeMethod($method);
        }
        // Set this as owner class
        $method->setParent($this);
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
            $method->setParent(null);
            // Remove from internal list
            unset($this->methods[$i]);
        }
    }
    
    /**
     * Returns all {@link PHP_Depend_Code_Type} objects this type depends on.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getDependencies()
    {
        return new PHP_Depend_Code_NodeIterator($this->dependencies);
    }
    
    /**
     * Adds the given {@link PHP_Depend_Code_Type} object as dependency.
     *
     * @param PHP_Depend_Code_Type $type A type this function depends on.
     * 
     * @return void
     */
    public function addDependency(PHP_Depend_Code_Type $type)
    {
        if (array_search($type, $this->dependencies, true) === false) {
            $this->dependencies[] = $type;
        }
    }
    
    /**
     * Removes the given {@link PHP_Depend_Code_Type} object from the dependency
     * list.
     *
     * @param PHP_Depend_Code_Type $type A type to remove.
     * 
     * @return void
     */
    public function removeDependency(PHP_Depend_Code_Type $type)
    {
        if (($i = array_search($type, $this->dependencies, true)) !== false) {
            // Remove from internal list
            unset($this->dependencies[$i]);
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
     * Returns <b>true</b> if this is an abstract class or an interface.
     *
     * @return boolean
     */
    public abstract function isAbstract();
}