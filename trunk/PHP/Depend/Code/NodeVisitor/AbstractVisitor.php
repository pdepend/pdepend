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

require_once 'PHP/Depend/Code/NodeVisitorI.php';

/**
 * This abstract visitor implementation provides a default traversal algorithm
 * that can be used for custom visitors. 
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
abstract class PHP_Depend_Code_NodeVisitor_AbstractVisitor
    implements PHP_Depend_Code_NodeVisitorI
{
    /**
     * List of all registered listeners.
     *
     * @type array<PHP_Depend_Code_NodeVisitor_ListenerI>
     * @var array(PHP_Depend_Code_NodeVisitor_ListenerI) $_listeners
     */
    private $_listeners = array();
    
    /**
     * Adds a new listener to this node visitor.
     *
     * @param PHP_Depend_Code_NodeVisitor_ListenerI $listener The new visit listener.
     * 
     * @return void
     */
    public function addVisitListener(PHP_Depend_Code_NodeVisitor_ListenerI $listener)
    {
        if (in_array($listener, $this->_listeners, true) === false) {
            $this->_listeners[] = $listener;
        }
    }
    
    /**
     * Removes the listener from this node visitor.
     *
     * @param PHP_Depend_Code_NodeVisitor_ListenerI $listener The listener to remove.
     * 
     * @return void
     */
    public function removeVisitListener(PHP_Depend_Code_NodeVisitor_ListenerI $listener)
    {
        if (($i = array_search($listener, $this->_listeners, true)) !== false) {
            unset($this->_listeners[$i]);
        }
    }
    
    /**
     * Visits a class node. 
     *
     * @param PHP_Depend_Code_Class $class The current class node.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitorI::visitClass()
     */
    public function visitClass(PHP_Depend_Code_Class $class)
    {
        $this->fireStartClass($class);
        
        $class->getSourceFile()->accept($this);
        
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
     * @param PHP_Depend_Code_File $file The current file node.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitorI::visitFile()
     */
    public function visitFile(PHP_Depend_Code_File $file)
    {
        $this->fireStartFile($file);
        $this->fireEndFile($file);
    }
    
    /**
     * Visits a function node. 
     *
     * @param PHP_Depend_Code_Function $function The current function node.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitorI::visitFunction()
     */
    public function visitFunction(PHP_Depend_Code_Function $function)
    {
        $this->fireStartFunction($function);
        
        $function->getSourceFile()->accept($this);
        
        $this->fireEndFunction($function);
    }
    
    /**
     * Visits a code interface object.
     *
     * @param PHP_Depend_Code_Interface $interface The context code interface.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitorI::visitInterface()
     */
    public function visitInterface(PHP_Depend_Code_Interface $interface)
    {
        $this->fireStartInterface($interface);
        
        $interface->getSourceFile()->accept($this);
        
        foreach ($interface->getMethods() as $method) {
            $method->accept($this);
        }
        
        $this->fireEndInterface($interface);
    }
    
    /**
     * Visits a method node. 
     *
     * @param PHP_Depend_Code_Class $method The method class node.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitorI::visitMethod()
     */
    public function visitMethod(PHP_Depend_Code_Method $method)
    {
        $this->fireStartMethod($method);
        $this->fireEndMethod($method);
    }
    
    /**
     * Visits a package node. 
     *
     * @param PHP_Depend_Code_Class $package The package class node.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitorI::visitPackage()
     */
    public function visitPackage(PHP_Depend_Code_Package $package)
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
     * Visits a property node. 
     *
     * @param PHP_Depend_Code_Property $property The property class node.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitorI::visitProperty()
     */
    public function visitProperty(PHP_Depend_Code_Property $property)
    {
        $this->fireStartProperty($property);
        $this->fireEndProperty($property);
    }
    
    /**
     * Sends a start class event.
     *
     * @param PHP_Depend_Code_Class $class The context class instance.
     * 
     * @return void
     */
    protected function fireStartClass(PHP_Depend_Code_Class $class)
    {
        foreach ($this->_listeners as $listener) {
            $listener->startVisitClass($class);
        }
    }
    
    /**
     * Sends an end class event.
     *
     * @param PHP_Depend_Code_Class $class The context class instance.
     * 
     * @return void
     */
    protected function fireEndClass(PHP_Depend_Code_Class $class)
    {
        foreach ($this->_listeners as $listener) {
            $listener->endVisitClass($class);
        }
    }
    
    /**
     * Sends a start file event.
     *
     * @param PHP_Depend_Code_File $file The context file.
     * 
     * @return void
     */
    protected function fireStartFile(PHP_Depend_Code_File $file)
    {
        foreach ($this->_listeners as $listener) {
            $listener->startVisitFile($file);
        }
    }
    
    /**
     * Sends an end file event.
     *
     * @param PHP_Depend_Code_File $file The context file instance.
     * 
     * @return void
     */
    protected function fireEndFile(PHP_Depend_Code_File $file)
    {
        foreach ($this->_listeners as $listener) {
            $listener->endVisitFile($file);
        }
    }
    
    /**
     * Sends a start function event.
     *
     * @param PHP_Depend_Code_Function $function The context function instance.
     * 
     * @return void
     */
    protected function fireStartFunction(PHP_Depend_Code_Function $function)
    {
        foreach ($this->_listeners as $listener) {
            $listener->startVisitFunction($function);
        }
    }
    
    /**
     * Sends an end function event.
     *
     * @param PHP_Depend_Code_Function $function The context function instance.
     * 
     * @return void
     */
    protected function fireEndFunction(PHP_Depend_Code_Function $function)
    {
        foreach ($this->_listeners as $listener) {
            $listener->endVisitFunction($function);
        }
    }
    
    /**
     * Sends a start interface event.
     *
     * @param PHP_Depend_Code_Interface $interface The context interface instance.
     * 
     * @return void
     */
    protected function fireStartInterface(PHP_Depend_Code_Interface $interface)
    {
        foreach ($this->_listeners as $listener) {
            $listener->startVisitInterface($interface);
        }
    }
    
    /**
     * Sends an end interface event.
     *
     * @param PHP_Depend_Code_Interface $interface The context interface instance.
     * 
     * @return void
     */
    protected function fireEndInterface(PHP_Depend_Code_Interface $interface)
    {
        foreach ($this->_listeners as $listener) {
            $listener->endVisitInterface($interface);
        }
    }
    
    /**
     * Sends a start method event.
     *
     * @param PHP_Depend_Code_Method $method The context method instance.
     * 
     * @return void
     */
    protected function fireStartMethod(PHP_Depend_Code_Method $method)
    {
        foreach ($this->_listeners as $listener) {
            $listener->startVisitMethod($method);
        }
    }
    
    /**
     * Sends an end method event.
     *
     * @param PHP_Depend_Code_Method $method The context method instance.
     * 
     * @return void
     */
    protected function fireEndMethod(PHP_Depend_Code_Method $method)
    {
        foreach ($this->_listeners as $listener) {
            $listener->endVisitMethod($method);
        }
    }
    
    /**
     * Sends a start package event. 
     *
     * @param PHP_Depend_Code_Package $package The context package instance.
     * 
     * @return void
     */
    protected function fireStartPackage(PHP_Depend_Code_Package $package)
    {
        foreach ($this->_listeners as $listener) {
            $listener->startVisitPackage($package);
        }
    }
    
    /**
     * Sends an end package event.
     *
     * @param PHP_Depend_Code_Package $package The context package instance.
     * 
     * @return void
     */
    protected function fireEndPackage(PHP_Depend_Code_Package $package)
    {
        foreach ($this->_listeners as $listener) {
            $listener->endVisitPackage($package);
        }
    }
    
    /**
     * Sends a start property event. 
     *
     * @param PHP_Depend_Code_Property $property The context property instance.
     * 
     * @return void
     */
    protected function fireStartProperty(PHP_Depend_Code_Property $property)
    {
        foreach ($this->_listeners as $listener) {
            $listener->startVisitProperty($property);
        }
    }
    
    /**
     * Sends an end property event.
     *
     * @param PHP_Depend_Code_Property $property The context property instance.
     * 
     * @return void
     */
    protected function fireEndProperty(PHP_Depend_Code_Property $property)
    {
        foreach ($this->_listeners as $listener) {
            $listener->endVisitProperty($property);
        }
    }
}