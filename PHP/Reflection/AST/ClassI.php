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

require_once 'PHP/Reflection/AST/ClassOrInterfaceI.php';

/**
 * This interface represents a class node within the syntax tree.
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
interface PHP_Reflection_AST_ClassI extends PHP_Reflection_AST_ClassOrInterfaceI
{
    /**
     * Has this class the final modifier.
     */
    const IS_FINAL = ReflectionClass::IS_FINAL;
    
    /**
     * Is this class explicit marked with the abstract modifier.
     */
    const IS_EXPLICIT_ABSTRACT = ReflectionClass::IS_EXPLICIT_ABSTRACT;
    
    /**
     * Is this class implicit marked due to its children.
     */
    const IS_IMPLICIT_ABSTRACT = ReflectionClass::IS_IMPLICIT_ABSTRACT;
    
    /**
     * Returns the declared modifiers for this class.
     *
     * @return integer
     */
    function getModifiers();
    
    /**
     * Returns <b>true</b> if this class is markes as final.
     *
     * @return boolean
     */
    function isFinal();
    
    /**
     * Returns the parent class or <b>null</b> if this class has no parent.
     *
     * @return PHP_Reflection_AST_ClassI
     */
    function getParentClass();
    
    /**
     * Returns a node iterator with all {@link PHP_Reflection_AST_InterfaceI}
     * nodes this class implements.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    function getImplementedInterfaces();
    
    /**
     * Will return a class property for the given node. Please note that this
     * method requires a property name without leading '$' character.
     *
     * @param string $name The property name.
     * 
     * @return PHP_Reflection_AST_PropertyI The property instance.
     * @throws PHP_Reflection_Exceptions_UnknownNodeException If no node exists
     *                                                        for the given name.
     */
    function getProperty($name);
    
    /**
     * Returns a node iterator with all {@link PHP_Reflection_AST_PropertyI}
     * nodes for this class.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    function getProperties();
}