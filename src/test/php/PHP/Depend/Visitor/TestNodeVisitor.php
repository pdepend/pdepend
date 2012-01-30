<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@pdepend.org>.
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
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

/**
 * Simple test node visitor implementation.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_Depend_Visitor_TestNodeVisitor implements PHP_Depend_VisitorI
{
    /**
     * The last visited class instance.
     *
     * @var PHP_Depend_Code_Class
     */
    public $class;

    /**
     * The last visited trait instance.
     *
     * @var PHP_Depend_Code_Trait
     * @since 1.0.0
     */
    public $trait;

    /**
     * The last visited interface instance.
     *
     * @var PHP_Depend_Code_Interface
     */
    public $interface;

    /**
     * The last visited method instance.
     *
     * @var PHP_Depend_Code_Method
     */
    public $method;

    /**
     * The last visited package instance.
     *
     * @var PHP_Depend_Code_Package
     */
    public $package;

    /**
     * The last visited parameter instance.
     *
     * @var PHP_Depend_Code_Parameter
     */
    public $parameter;

    /**
     * The last visited property instance.
     *
     * @var PHP_Depend_Code_Property
     */
    public $property;

    /**
     * The last visited function instance.
     *
     * @var PHP_Depend_Code_Function
     */
    public $function;

    /**
     * Adds a new listener to this node visitor.
     *
     * @param PHP_Depend_Visitor_ListenerI $listener The new visit listener.
     *
     * @return void
     */
    public function addVisitListener(PHP_Depend_Visitor_ListenerI $listener)
    {
    }

    /**
     * Visits a class node.
     *
     * @param PHP_Depend_Code_Class $class The current class node.
     *
     * @return void
     */
    public function visitClass(PHP_Depend_Code_Class $class)
    {
        $this->class = $class;
    }

    /**
     * Visits a trait node.
     *
     * @param PHP_Depend_Code_Trait $trait The current trait node.
     *
     * @return void
     * @since 1.0.0
     */
    public function visitTrait(PHP_Depend_Code_Trait $trait)
    {
        $this->trait = $trait;
    }


    /**
     * Visits a code interface object.
     *
     * @param PHP_Depend_Code_Interface $interface The context code interface.
     *
     * @return void
     */
    public function visitInterface(PHP_Depend_Code_Interface $interface)
    {
        $this->interface = $interface;
    }

    /**
     * Visits a method node.
     *
     * @param PHP_Depend_Code_Class $method The method class node.
     *
     * @return void
     */
    public function visitMethod(PHP_Depend_Code_Method $method)
    {
        $this->method = $method;
    }

    /**
     * Visits a package node.
     *
     * @param PHP_Depend_Code_Class $package The package class node.
     *
     * @return void
     */
    public function visitPackage(PHP_Depend_Code_Package $package)
    {
        $this->package = $package;
    }

    /**
     * Visits a parameter node.
     *
     * @param PHP_Depend_Code_Parameter $parameter The parameter node.
     *
     * @return void
     */
    public function visitParameter(PHP_Depend_Code_Parameter $parameter)
    {
        $this->parameter = $parameter;
    }

    /**
     * Visits a property node.
     *
     * @param PHP_Depend_Code_Property $property The property class node.
     *
     * @return void
     * @see PHP_Depend_VisitorI::visitProperty()
     */
    public function visitProperty(PHP_Depend_Code_Property $property)
    {
        $this->property = $property;
    }

    /**
     * Visits a function node.
     *
     * @param PHP_Depend_Code_Function $function The current function node.
     *
     * @return void
     */
    public function visitFunction(PHP_Depend_Code_Function $function)
    {
        $this->function = $function;
    }

    /**
     * Visits a file node.
     *
     * @param PHP_Depend_Code_File $file The current file node.
     *
     * @return void
     * @see PHP_Depend_VisitorI::visitFile()
     */
    public function visitFile(PHP_Depend_Code_File $file)
    {

    }
}
