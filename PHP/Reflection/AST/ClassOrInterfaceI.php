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

require_once 'PHP/Reflection/AST/NodeI.php';

/**
 * Base interface for a class or interface node.
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
interface PHP_Reflection_AST_ClassOrInterfaceI extends PHP_Reflection_AST_NodeI
{
    /**
     * Returns <b>true</b> if this is an abstract class or an interface.
     *
     * @return boolean
     */
    function isAbstract();
    
    /**
     * This method will return the constant instance for the given name.
     *
     * @param string $name The constant name.
     * 
     * @return PHP_Reflection_AST_ClassOrInterfaceConstant
     * @throws PHP_Reflection_Exceptions_UnknownNodeException If no node exists
     *                                                        for the given name.
     */
    function getConstant($name);
    
    /**
     * Returns all {@link PHP_Reflection_AST_ClassOrInterfaceConstant} objects 
     * in this class or interface node.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    function getConstants();
    
    /**
     * Returns all {@link PHP_Reflection_AST_ClassOrInterfaceI} objects this node 
     * depends on.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    function getDependencies();
    
    /**
     * This method will return the method instance for the given name.
     *
     * @param string $name The method name.
     * 
     * @return PHP_Reflection_AST_MethodI
     * @throws PHP_Reflection_Exceptions_UnknownNodeException If no node exists
     *                                                        for the given name.
     */
    function getMethod($name);
    
    /**
     * Returns all {@link PHP_Reflection_AST_MethodI} objects in this type.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    function getMethods();
    
    /**
     * Returns the parent package for this class.
     *
     * @return PHP_Reflection_AST_Package
     */
    function getPackage();
    
    /**
     * Checks that this user type is a subtype of the given <b>$classOrInterface</b>
     * instance.
     *
     * @param PHP_Reflection_AST_ClassOrInterfaceI $classOrInterface 
     *        The possible parent node.
     * 
     * @return boolean
     */
    function isSubtypeOf(PHP_Reflection_AST_ClassOrInterfaceI $classOrInterface);
}