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
 * @subpackage Visitor
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Reflection/Visitor/AbstractVisitor.php';

/**
 * Dummy implementation of the default visitor.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Visitor
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_Visitor_TestImplAbstractVisitor extends PHP_Reflection_Visitor_AbstractVisitor
{
    /**
     * Collected visit order.
     *
     * @type array<integer>
     * @var array(string=>integer)
     */
    public $visits = array();
    
    /**
     * Visits a class node. 
     *
     * @param PHP_Reflection_Ast_ClassI $class The current class node.
     * 
     * @return void
     * @see PHP_Reflection_VisitorI::visitClass()
     */
    public function visitClass(PHP_Reflection_Ast_ClassI $class)
    {
        $this->visits[] = $class->getName();
        
        parent::visitClass($class);
    }
    
    /**
     * Visits a file node. 
     *
     * @param PHP_Reflection_Ast_File $file The current file node.
     * 
     * @return void
     * @see PHP_Reflection_VisitorI::visitFile()
     */
    public function visitFile(PHP_Reflection_Ast_File $file)
    {
        $this->visits[] = $file->getFileName();
        
        parent::visitFile($file);
    }
    
    /**
     * Visits a function node. 
     *
     * @param PHP_Reflection_Ast_Function $function The current function node.
     * 
     * @return void
     * @see PHP_Reflection_VisitorI::visitFunction()
     */
    public function visitFunction(PHP_Reflection_Ast_FunctionI $function)
    {
        $this->visits[] = $function->getName();
        
        parent::visitFunction($function);
    }
    
    /**
     * Visits a code interface object.
     *
     * @param PHP_Reflection_Ast_InterfaceI $interface The context code interface.
     * 
     * @return void
     * @see PHP_Reflection_VisitorI::visitInterface()
     */
    public function visitInterface(PHP_Reflection_Ast_InterfaceI $interface)
    {
        $this->visits[] = $interface->getName();
        
        parent::visitInterface($interface);
    }
    
    /**
     * Visits a method node. 
     *
     * @param PHP_Reflection_Ast_Class $method The method class node.
     * 
     * @return void
     * @see PHP_Reflection_VisitorI::visitMethod()
     */
    public function visitMethod(PHP_Reflection_Ast_Method $method)
    {
        $this->visits[] = $method->getName();
        
        parent::visitMethod($method);
    }
    
    /**
     * Visits a package node. 
     *
     * @param PHP_Reflection_Ast_Class $package The package class node.
     * 
     * @return void
     * @see PHP_Reflection_VisitorI::visitPackage()
     */
    public function visitPackage(PHP_Reflection_Ast_Package $package)
    {
        $this->visits[] = $package->getName();
        
        parent::visitPackage($package);
    }
    
    /**
     * Visits a property node. 
     *
     * @param PHP_Reflection_Ast_Property $property The property class node.
     * 
     * @return void
     * @see PHP_Reflection_VisitorI::visitProperty()
     */
    public function visitProperty(PHP_Reflection_Ast_Property $property)
    {
        $this->visits[] = $property->getName();
        
        parent::visitProperty($property);
    }
}