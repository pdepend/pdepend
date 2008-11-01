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
 * @subpackage AST
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Reflection/Visitor/ListenerI.php';

/**
 * This abstract class provides a default implementation of the node visitor
 * listener.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage AST
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
abstract class PHP_Reflection_Visitor_AbstractListener
    implements PHP_Reflection_Visitor_ListenerI
{
    /**
     * Is called when the visitor starts a new class instance.
     *
     * @param PHP_Reflection_AST_Class $class The context class instance.
     *
     * @return void
     */
    public function startVisitClass(PHP_Reflection_AST_Class $class)
    {
        $this->startVisitNode($class);
    }

    /**
     * Is called when the visitor ends with a class instance.
     *
     * @param PHP_Reflection_AST_Class $class The context class instance.
     *
     * @return void
     */
    public function endVisitClass(PHP_Reflection_AST_Class $class)
    {
        $this->endVisitNode($class);
    }

    /**
     * Is called when the visitor starts a new file instance.
     *
     * @param PHP_Reflection_AST_File $file The context file instance.
     *
     * @return void
     */
    public function startVisitFile(PHP_Reflection_AST_File $file)
    {
        $this->startVisitNode($file);
    }

    /**
     * Is called when the visitor ends with a file instance.
     *
     * @param PHP_Reflection_AST_File $file The context file instance.
     *
     * @return void
     */
    public function endVisitFile(PHP_Reflection_AST_File $file)
    {
        $this->endVisitNode($file);
    }

    /**
     * Is called when the visitor starts a new function instance.
     *
     * @param PHP_Reflection_AST_Function $function The context function instance.
     *
     * @return void
     */
    public function startVisitFunction(PHP_Reflection_AST_Function $function)
    {
        $this->startVisitNode($function);
    }

    /**
     * Is called when the visitor ends with a function instance.
     *
     * @param PHP_Reflection_AST_Function $function The context function instance.
     *
     * @return void
     */
    public function endVisitFunction(PHP_Reflection_AST_Function $function)
    {
        $this->endVisitNode($function);
    }

    /**
     * Is called when the visitor starts a new interface instance.
     *
     * @param PHP_Reflection_AST_Interface $interface The context interface instance.
     *
     * @return void
     */
    public function startVisitInterface(PHP_Reflection_AST_Interface $interface)
    {
        $this->startVisitNode($interface);
    }

    /**
     * Is called when the visitor ends with an interface instance.
     *
     * @param PHP_Reflection_AST_Interface $interface The context interface instance.
     *
     * @return void
     */
    public function endVisitInterface(PHP_Reflection_AST_Interface $interface)
    {
        $this->endVisitNode($interface);
    }

    /**
     * Is called when the visitor starts a new method instance.
     *
     * @param PHP_Reflection_AST_MethodI $method The context method instance.
     *
     * @return void
     */
    public function startVisitMethod(PHP_Reflection_AST_MethodI $method)
    {
        $this->startVisitNode($method);
    }

    /**
     * Is called when the visitor ends with a method instance.
     *
     * @param PHP_Reflection_AST_MethodI $method The context method instance.
     *
     * @return void
     */
    public function endVisitMethod(PHP_Reflection_AST_MethodI $method)
    {
        $this->endVisitNode($method);
    }

    /**
     * Is called when the visitor starts a new package instance.
     *
     * @param PHP_Reflection_AST_PackageI $package The context package instance.
     *
     * @return void
     */
    public function startVisitPackage(PHP_Reflection_AST_PackageI $package)
    {
        $this->startVisitNode($package);
    }

    /**
     * Is called when the visitor ends with a package instance.
     *
     * @param PHP_Reflection_AST_PackageI $package The context package instance.
     *
     * @return void
     */
    public function endVisitPackage(PHP_Reflection_AST_PackageI $package)
    {
        $this->endVisitNode($package);
    }

    /**
     * Is called when the visitor starts a new parameter instance.
     *
     * @param PHP_Reflection_AST_Parameter $parameter The context parameter instance.
     *
     * @return void
     */
    public function startVisitParameter(PHP_Reflection_AST_Parameter $parameter)
    {
        $this->startVisitNode($parameter);
    }

    /**
     * Is called when the visitor ends with a parameter instance.
     *
     * @param PHP_Reflection_AST_Package $parameter The context parameter instance.
     *
     * @return void
     */
    public function endVisitParameter(PHP_Reflection_AST_Parameter $parameter)
    {
        $this->endVisitNode($parameter);
    }

    /**
     * Is called when the visitor starts a new property instance.
     *
     * @param PHP_Reflection_AST_Property $property The context property instance.
     *
     * @return void
     */
    public function startVisitProperty(PHP_Reflection_AST_Property $property)
    {
        $this->startVisitNode($property);
    }

    /**
     * Is called when the visitor ends with a property instance.
     *
     * @param PHP_Reflection_AST_Property $property The context property instance.
     *
     * @return void
     */
    public function endVisitProperty(PHP_Reflection_AST_Property $property)
    {
        $this->endVisitNode($property);
    }

    /**
     * Is called when the visitor starts a new constant instance.
     *
     * @param PHP_Reflection_AST_ClassOrInterfaceConstant $constant
     * The context constant.
     *
     * @return void
     */
    public function startVisitTypeConstant(
                        PHP_Reflection_AST_ClassOrInterfaceConstant $constant)
    {
        $this->startVisitNode($constant);
    }

    /**
     * Is called when the visitor ends with a constant instance.
     *
     * @param PHP_Reflection_AST_ClassOrInterfaceConstant $constant
     * The context constant.
     *
     * @return void
     */
    public function endVisitTypeConstant(
                        PHP_Reflection_AST_ClassOrInterfaceConstant $constant)
    {
        $this->endVisitNode($constant);
    }

    /**
     * Generic notification method that is called for every node start.
     *
     * @param PHP_Reflection_AST_NodeI $node The context node instance.
     *
     * @return void
     */
    protected function startVisitNode(PHP_Reflection_AST_NodeI $node)
    {

    }

    /**
     * Generic notification method that is called when the node processing ends.
     *
     * @param PHP_Reflection_AST_NodeI $node The context node instance.
     *
     * @return void
     */
    protected function endVisitNode(PHP_Reflection_AST_NodeI $node)
    {

    }
}