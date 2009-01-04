<?php
/**
 * This file is part of PHP_Reflection.
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
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage AST
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Reflection/AST/AbstractNode.php';
require_once 'PHP/Reflection/AST/PackageI.php';
require_once 'PHP/Reflection/AST/Iterator.php';
require_once 'PHP/Reflection/AST/Iterator/TypeFilter.php';

/**
 * Represents a php package node.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage AST
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_AST_Package 
       extends PHP_Reflection_AST_AbstractNode
    implements PHP_Reflection_AST_PackageI
{
    /**
     * List of all {@link PHP_Reflection_AST_AbstractClassOrInterface} nodes
     * defined in this package.
     *
     * @var array(PHP_Reflection_AST_AbstractClassOrInterface) $_classOrInterfaceList
     */
    private $_classOrInterfaceList = array();
    
    /**
     * List of all standalone {@link PHP_Reflection_AST_Function} objects in this
     * package.
     *
     * @var array(PHP_Reflection_AST_Function) $_functionList
     */
    private $_functionList = array();
    
    /**
     * Returns an iterator with all {@link PHP_Reflection_AST_ClassI} instances
     * within this package.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    public function getClasses()
    {
        $type = 'PHP_Reflection_AST_Class';
        
        $classes = new PHP_Reflection_AST_Iterator($this->_classOrInterfaceList);
        $classes->addFilter(new PHP_Reflection_AST_Iterator_TypeFilter($type));
        
        return $classes;
    }
    
    /**
     * Returns an iterator with all {@link PHP_Reflection_AST_InterfaceI} 
     * instances within this package.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    public function getInterfaces()
    {
        $type = 'PHP_Reflection_AST_Interface';
        
        $classes = new PHP_Reflection_AST_Iterator($this->_classOrInterfaceList);
        $classes->addFilter(new PHP_Reflection_AST_Iterator_TypeFilter($type));
        
        return $classes;
    }
    
    /**
     * Returns all {@link PHP_Reflection_AST_ClassOrInterfaceI} objects in this 
     * package.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    public function getTypes()
    {
        return new PHP_Reflection_AST_Iterator($this->_classOrInterfaceList);
    }
    
    /**
     * Adds the given type to this package and returns the input type instance.
     *
     * @param PHP_Reflection_AST_AbstractClassOrInterface $type The new package type.
     * 
     * @return PHP_Reflection_AST_AbstractClassOrInterface
     */
    public function addType(PHP_Reflection_AST_AbstractClassOrInterface $type)
    {
        // Skip if this package already contains this type
        if (in_array($type, $this->_classOrInterfaceList, true)) {
            return;
        }
        
        if ($type->getPackage() !== null) {
            $type->getPackage()->removeType($type);
        }
        
        // Set this as class package
        $type->setPackage($this);
        // Append class to internal list
        $this->_classOrInterfaceList[] = $type;
        
        return $type;
    }
    
    /**
     * Removes the given type instance from this package.
     *
     * @param PHP_Reflection_AST_AbstractClassOrInterface $type The type instance to remove.
     * 
     * @return void
     */
    public function removeType(PHP_Reflection_AST_AbstractClassOrInterface $type)
    {
        if (($i = array_search($type, $this->_classOrInterfaceList, true)) !== false) {
            // Remove class from internal list
            unset($this->_classOrInterfaceList[$i]);
            // Remove this as parent
            $type->setPackage(null);
        }
    }
    
    /**
     * Returns all {@link PHP_Reflection_AST_FunctionI} objects in this package.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    public function getFunctions()
    {
        return new PHP_Reflection_AST_Iterator($this->_functionList);
    }
    
    /**
     * Adds the given function to this package and returns the input instance.
     *
     * @param PHP_Reflection_AST_Function $function The new package function.
     * 
     * @return PHP_Reflection_AST_Function
     */
    public function addFunction(PHP_Reflection_AST_Function $function)
    {
        if ($function->getPackage() !== null) {
            $function->getPackage()->removeFunction($function);
        }

        // Set this as function package
        $function->setPackage($this);
        // Append function to internal list
        $this->_functionList[] = $function;
        
        return $function;
    }
    
    /**
     * Removes the given function from this package.
     *
     * @param PHP_Reflection_AST_Function $function The function to remove
     * 
     * @return void
     */
    public function removeFunction(PHP_Reflection_AST_Function $function)
    {
        if (($i = array_search($function, $this->_functionList, true)) !== false) {
            // Remove function from internal list
            unset($this->_functionList[$i]);
            // Remove this as parent
            $function->setPackage(null);
        }
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
        $visitor->visitPackage($this);
    }
}