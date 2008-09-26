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

require_once 'PHP/Reflection/VisitorI.php';

/**
 * Simple test node visitor implementation.
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
class PHP_Reflection_Ast_TestImplAstVisitor implements PHP_Reflection_VisitorI
{
    /**
     * The last visited class instance.
     *
     * @type PHP_Reflection_Ast_Class
     * @var PHP_Reflection_Ast_Class $class
     */
    public $class = null;
    
    /**
     * The last visited interface instance.
     *
     * @type PHP_Reflection_Ast_Interface
     * @var PHP_Reflection_Ast_Interface $interface
     */
    public $interface = null;
    
    /**
     * The last visited method instance.
     *
     * @type PHP_Reflection_Ast_Method
     * @var PHP_Reflection_Ast_Method $method
     */
    public $method = null;
    
    /**
     * The last visited package instance.
     *
     * @type PHP_Reflection_Ast_Package
     * @var PHP_Reflection_Ast_Package $method
     */
    public $package = null;
    
    /**
     * The last visited parameter instance.
     *
     * @type PHP_Reflection_Ast_Parameter
     * @var PHP_Reflection_Ast_Parameter $parameter
     */
    public $parameter = null;
    
    /**
     * The last visited property instance.
     *
     * @type PHP_Reflection_Ast_Property
     * @var PHP_Reflection_Ast_Property $property
     */
    public $property = null;
    
    /**
     * The last visited function instance.
     *
     * @type PHP_Reflection_Ast_Function
     * @var PHP_Reflection_Ast_Function $method
     */
    public $function = null;
    
    /**
     * The last visited type constant instance.
     *
     * @type PHP_Reflection_Ast_TypeConstant
     * @var PHP_Reflection_Ast_TypeConstant $typeConstant
     */
    public $typeConstant = null;
    
    /**
     * Adds a new listener to this node visitor.
     *
     * @param PHP_Reflection_Visitor_ListenerI $listener The new visit listener.
     * 
     * @return void
     */
    public function addVisitListener(PHP_Reflection_Visitor_ListenerI $listener)
    {
    }
    
    /**
     * Removes the listener from this node visitor.
     *
     * @param PHP_Reflection_Visitor_ListenerI $listener The listener to remove.
     * 
     * @return void
     */
    public function removeVisitListener(PHP_Reflection_Visitor_ListenerI $listener)
    {
    }
    
    /**
     * Visits a class node. 
     *
     * @param PHP_Reflection_Ast_Class $class The current class node.
     * 
     * @return void
     */
    public function visitClass(PHP_Reflection_Ast_Class $class)
    {
        $this->class = $class;
    }
    
    /**
     * Visits a code interface object.
     *
     * @param PHP_Reflection_Ast_Interface $interface The context code interface.
     * 
     * @return void
     */
    public function visitInterface(PHP_Reflection_Ast_Interface $interface)
    {
        $this->interface = $interface;
    }
    
    /**
     * Visits a method node. 
     *
     * @param PHP_Reflection_Ast_Class $method The method class node.
     * 
     * @return void
     */
    public function visitMethod(PHP_Reflection_Ast_Method $method)
    {
        $this->method = $method;
    }
    
    /**
     * Visits a package node. 
     *
     * @param PHP_Reflection_Ast_Class $package The package class node.
     * 
     * @return void
     */
    public function visitPackage(PHP_Reflection_Ast_Package $package)
    {
        $this->package = $package;
    }
    
    /**
     * Visits a parameter node.
     *
     * @param PHP_Reflection_Ast_Parameter $parameter The parameter node.
     * 
     * @return void
     */
    public function visitParameter(PHP_Reflection_Ast_Parameter $parameter)
    {
        $this->parameter = $parameter;
    }
    
    /**
     * Visits a property node. 
     *
     * @param PHP_Reflection_Ast_Property $property The property class node.
     * 
     * @return void
     * @see PHP_Reflection_Ast_NodeVisitorI::visitProperty()
     */
    public function visitProperty(PHP_Reflection_Ast_Property $property)
    {
        $this->property = $property;
    }
    
    /**
     * Visits a function node. 
     *
     * @param PHP_Reflection_Ast_Function $function The current function node.
     * 
     * @return void
     */
    public function visitFunction(PHP_Reflection_Ast_Function $function)
    {
        $this->function = $function;
    }
    
    /**
     * Visits a file node. 
     *
     * @param PHP_Reflection_Ast_File $file The current file node.
     * 
     * @return void
     * @see PHP_Reflection_Ast_NodeVisitorI::visitFile()
     */
    public function visitFile(PHP_Reflection_Ast_File $file)
    {
        
    }
    
    /**
     * Visits a class constant node. 
     *
     * @param PHP_Reflection_Ast_TypeConstant $constant The current constant node.
     * 
     * @return void
     */
    public function visitTypeConstant(PHP_Reflection_Ast_TypeConstant $constant)
    {
        $this->typeConstant = $constant;
    }
}