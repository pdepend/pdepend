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

require_once 'PHP/Reflection/VisitorI.php';

/**
 * This abstract visitor implementation provides a default traversal algorithm
 * that can be used for custom visitors. 
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
abstract class PHP_Reflection_Visitor_AbstractVisitor 
    implements PHP_Reflection_VisitorI
{
    /**
     * List of all registered listeners.
     *
     * @type array<PHP_Reflection_Visitor_ListenerI>
     * @var array(PHP_Reflection_Visitor_ListenerI) $_listeners
     */
    private $_listeners = array();
    
    /**
     * Returns an iterator with all registered visit listeners.
     *
     * @return Iterator
     */
    public function getVisitListeners()
    {
        return new ArrayIterator($this->_listeners);
    }
    
    /**
     * Adds a new listener to this node visitor.
     *
     * @param PHP_Reflection_Visitor_ListenerI $listener The new visit listener.
     * 
     * @return void
     */
    public function addVisitListener(PHP_Reflection_Visitor_ListenerI $listener)
    {
        if (in_array($listener, $this->_listeners, true) === false) {
            $this->_listeners[] = $listener;
        }
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
        if (($i = array_search($listener, $this->_listeners, true)) !== false) {
            unset($this->_listeners[$i]);
        }
    }
    
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
        $this->fireStartClass($class);
        
        $class->getSourceFile()->accept($this);
        
        foreach ($class->getConstants() as $constant) {
            $constant->accept($this);
        }
        foreach ($class->getProperties() as $property) {
            $property->accept($this);
        }
        foreach ($class->getMethods() as $method) {
            $method->accept($this);
        }
        
        $this->fireEndClass($class);
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
        $this->fireStartFile($file);
        $this->fireEndFile($file);
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
        $this->fireStartFunction($function);
        
        $function->getSourceFile()->accept($this);
        
        foreach ($function->getParameters() as $parameter) {
            $parameter->accept($this);
        }
        
        $this->fireEndFunction($function);
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
        $this->fireStartInterface($interface);
        
        $interface->getSourceFile()->accept($this);
    
        foreach ($interface->getConstants() as $constant) {
            $constant->accept($this);
        }
        foreach ($interface->getMethods() as $method) {
            $method->accept($this);
        }
        
        $this->fireEndInterface($interface);
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
        $this->fireStartMethod($method);
        
        foreach ($method->getParameters() as $parameter) {
            $parameter->accept($this);
        }
        
        $this->fireEndMethod($method);
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
        $this->fireStartPackage($package);
        
        foreach ($package->getClasses() as $class) {
            $class->accept($this);
        }
        foreach ($package->getInterfaces() as $interface) {
            $interface->accept($this);
        }
        foreach ($package->getFunctions() as $function) {
            $function->accept($this);
        }
        
        $this->fireEndPackage($package);
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
        $this->fireStartParameter($parameter);
        $this->fireEndParameter($parameter);
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
        $this->fireStartProperty($property);
        
        if (($value = $property->getValue()) !== null) {
            $value->accept($this);
        }
        
        $this->fireEndProperty($property);
    }
    
    /**
     * Visits a class constant node. 
     *
     * @param PHP_Reflection_Ast_ClassOrInterfaceConstant $const The current constant node.
     * 
     * @return void
     */
    public function visitTypeConstant(PHP_Reflection_Ast_ClassOrInterfaceConstant $const)
    {
        $this->fireStartTypeConstant($const);
        
        if (($value = $const->getValue()) !== null) {
            $value->accept($this);
        }
        
        $this->fireEndTypeConstant($const);
    }
    
    /**
     * Visits an array expression node
     *
     * @param PHP_Reflection_Ast_ArrayExpression $expr The current array expression.
     * 
     * @return void
     */
    public function visitArrayExpression(PHP_Reflection_Ast_ArrayExpression $expr)
    {
        // TODO Implement fireStartArrayExpression()
        foreach ($expr->getElements() as $element) {
            $element->accept($this);
        }
        // TODO Implement fireEndArrayExpression()
    }
    
    /**
     * Visits an array element node.
     *
     * @param PHP_Reflection_Ast_ArrayElement $elem The current array element.
     * 
     * @return void
     */
    public function visitArrayElement(PHP_Reflection_Ast_ArrayElement $elem)
    {
        // TODO Implement fireStartArrayElement()
        $elem->getKey()->accept($this);
        $elem->getValue()->accept($this);
        // TODO Implement fireEndArrayElement()
    }
    
    /**
     * Visits a constant reference node.
     *
     * @param PHP_Reflection_Ast_ConstantValue $constRef The current const ref.
     * 
     * @return void
     */
    public function visitConstantValue(PHP_Reflection_Ast_ConstantValue $constRef)
    {
        // TODO Implement fireStartConstantValue()
        
        // TODO Implement fireEndConstantValue()
    }
    
    /**
     * Visits a class or interface constant reference
     *
     * @param PHP_Reflection_Ast_ClassOrInterfaceConstantValue $constRef
     *        The reference instance.
     * 
     * @return void
     */
    public function visitClassOrInterfaceConstantValue(
                PHP_Reflection_Ast_ClassOrInterfaceConstantValue $constRef)
    {
        // TODO Implement fireStartClassOrInterfaceConstantValue()

        // TODO Implement fireEndClassOrInterfaceConstantValue()
    }
                
    /**
     * Visits a general value.
     *
     * @param PHP_Reflection_Ast_MemberValueI $value The value instance.
     * 
     * @return void
     */
    public function visitValue(PHP_Reflection_Ast_MemberValueI $value)
    {
        // TODO Implement fireStartValue()

        // TODO Implement fireEndValue()
    }
    
    /**
     * Sends a start class event.
     *
     * @param PHP_Reflection_Ast_Class $class The context class instance.
     * 
     * @return void
     */
    protected function fireStartClass(PHP_Reflection_Ast_Class $class)
    {
        foreach ($this->_listeners as $listener) {
            $listener->startVisitClass($class);
        }
    }
    
    /**
     * Sends an end class event.
     *
     * @param PHP_Reflection_Ast_Class $class The context class instance.
     * 
     * @return void
     */
    protected function fireEndClass(PHP_Reflection_Ast_Class $class)
    {
        foreach ($this->_listeners as $listener) {
            $listener->endVisitClass($class);
        }
    }
    
    /**
     * Sends a start file event.
     *
     * @param PHP_Reflection_Ast_File $file The context file.
     * 
     * @return void
     */
    protected function fireStartFile(PHP_Reflection_Ast_File $file)
    {
        foreach ($this->_listeners as $listener) {
            $listener->startVisitFile($file);
        }
    }
    
    /**
     * Sends an end file event.
     *
     * @param PHP_Reflection_Ast_File $file The context file instance.
     * 
     * @return void
     */
    protected function fireEndFile(PHP_Reflection_Ast_File $file)
    {
        foreach ($this->_listeners as $listener) {
            $listener->endVisitFile($file);
        }
    }
    
    /**
     * Sends a start function event.
     *
     * @param PHP_Reflection_Ast_Function $function The context function instance.
     * 
     * @return void
     */
    protected function fireStartFunction(PHP_Reflection_Ast_Function $function)
    {
        foreach ($this->_listeners as $listener) {
            $listener->startVisitFunction($function);
        }
    }
    
    /**
     * Sends an end function event.
     *
     * @param PHP_Reflection_Ast_Function $function The context function instance.
     * 
     * @return void
     */
    protected function fireEndFunction(PHP_Reflection_Ast_Function $function)
    {
        foreach ($this->_listeners as $listener) {
            $listener->endVisitFunction($function);
        }
    }
    
    /**
     * Sends a start interface event.
     *
     * @param PHP_Reflection_Ast_Interface $interface The context interface.
     * 
     * @return void
     */
    protected function fireStartInterface(PHP_Reflection_Ast_Interface $interface)
    {
        foreach ($this->_listeners as $listener) {
            $listener->startVisitInterface($interface);
        }
    }
    
    /**
     * Sends an end interface event.
     *
     * @param PHP_Reflection_Ast_Interface $interface The context interface.
     * 
     * @return void
     */
    protected function fireEndInterface(PHP_Reflection_Ast_Interface $interface)
    {
        foreach ($this->_listeners as $listener) {
            $listener->endVisitInterface($interface);
        }
    }
    
    /**
     * Sends a start method event.
     *
     * @param PHP_Reflection_Ast_Method $method The context method instance.
     * 
     * @return void
     */
    protected function fireStartMethod(PHP_Reflection_Ast_Method $method)
    {
        foreach ($this->_listeners as $listener) {
            $listener->startVisitMethod($method);
        }
    }
    
    /**
     * Sends an end method event.
     *
     * @param PHP_Reflection_Ast_Method $method The context method instance.
     * 
     * @return void
     */
    protected function fireEndMethod(PHP_Reflection_Ast_Method $method)
    {
        foreach ($this->_listeners as $listener) {
            $listener->endVisitMethod($method);
        }
    }
    
    /**
     * Sends a start package event. 
     *
     * @param PHP_Reflection_Ast_Package $package The context package instance.
     * 
     * @return void
     */
    protected function fireStartPackage(PHP_Reflection_Ast_Package $package)
    {
        foreach ($this->_listeners as $listener) {
            $listener->startVisitPackage($package);
        }
    }
    
    /**
     * Sends an end package event.
     *
     * @param PHP_Reflection_Ast_Package $package The context package instance.
     * 
     * @return void
     */
    protected function fireEndPackage(PHP_Reflection_Ast_Package $package)
    {
        foreach ($this->_listeners as $listener) {
            $listener->endVisitPackage($package);
        }
    }
    
    /**
     * Sends a start parameter event. 
     *
     * @param PHP_Reflection_Ast_Parameter $parameter The context parameter instance.
     * 
     * @return void
     */
    protected function fireStartParameter(PHP_Reflection_Ast_Parameter $parameter)
    {
        foreach ($this->_listeners as $listener) {
            $listener->startVisitParameter($parameter);
        }
    }
    
    /**
     * Sends a end parameter event. 
     *
     * @param PHP_Reflection_Ast_Parameter $parameter The context parameter instance.
     * 
     * @return void
     */
    protected function fireEndParameter(PHP_Reflection_Ast_Parameter $parameter)
    {
        foreach ($this->_listeners as $listener) {
            $listener->endVisitParameter($parameter);
        }
    }
    
    /**
     * Sends a start property event. 
     *
     * @param PHP_Reflection_Ast_Property $property The context property instance.
     * 
     * @return void
     */
    protected function fireStartProperty(PHP_Reflection_Ast_Property $property)
    {
        foreach ($this->_listeners as $listener) {
            $listener->startVisitProperty($property);
        }
    }
    
    /**
     * Sends an end property event.
     *
     * @param PHP_Reflection_Ast_Property $property The context property instance.
     * 
     * @return void
     */
    protected function fireEndProperty(PHP_Reflection_Ast_Property $property)
    {
        foreach ($this->_listeners as $listener) {
            $listener->endVisitProperty($property);
        }
    }
    
    /**
     * Sends a start constant event. 
     *
     * @param PHP_Reflection_Ast_ClassOrInterfaceConstant $const The context constant.
     * 
     * @return void
     */
    protected function fireStartTypeConstant(PHP_Reflection_Ast_ClassOrInterfaceConstant $const)
    {
        foreach ($this->_listeners as $listener) {
            $listener->startVisitTypeConstant($const);
        }
    }
    
    /**
     * Sends an end constant event.
     *
     * @param PHP_Reflection_Ast_ClassOrInterfaceConstant $const The context constant.
     * 
     * @return void
     */
    protected function fireEndTypeConstant(PHP_Reflection_Ast_ClassOrInterfaceConstant $const)
    {
        foreach ($this->_listeners as $listener) {
            $listener->endVisitTypeConstant($const);
        }
    }
}