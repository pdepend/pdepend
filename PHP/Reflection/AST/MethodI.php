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

require_once 'PHP/Reflection/AST/CallableI.php';

/**
 * Base interface for method nodes.
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
interface PHP_Reflection_AST_MethodI extends PHP_Reflection_AST_CallableI
{
    /**
     * Marks the method as abstract.
     */
    const IS_ABSTRACT = ReflectionMethod::IS_ABSTRACT;
    
    /**
     * Marks the method as final
     */
    const IS_FINAL = ReflectionMethod::IS_FINAL;
    
    /**
     * Marks the method as public.
     */
    const IS_PUBLIC = ReflectionMethod::IS_PUBLIC;
    
    /**
     * Marks the method as protected.
     */
    const IS_PROTECTED = ReflectionMethod::IS_PROTECTED;
    
    /**
     * Marks the method as private.
     */
    const IS_PRIVATE = ReflectionMethod::IS_PRIVATE;
    
    /**
     * Marks the method as static.
     */
    const IS_STATIC = ReflectionMethod::IS_STATIC;
    
    /**
     * Returns the declared modifiers for this method.
     *
     * @return integer
     */
    function getModifiers();
    
    /**
     * Returns <b>true</b> if this is an abstract method.
     *
     * @return boolean
     */
    function isAbstract();
    
    /**
     * Returns <b>true</b> if this node is marked as public, otherwise the 
     * returned value will be <b>false</b>.
     *
     * @return boolean
     */
    function isPublic();
    
    /**
     * Returns <b>true</b> if this node is marked as protected, otherwise the 
     * returned value will be <b>false</b>.
     *
     * @return boolean
     */
    function isProtected();
    
    /**
     * Returns <b>true</b> if this node is marked as private, otherwise the 
     * returned value will be <b>false</b>.
     *
     * @return boolean
     */
    function isPrivate();
    
    /**
     * Returns <b>true</b> if this node is marked as final, otherwise the return
     * value will be <b>false</b>.
     *
     * @return boolean
     */
    function isFinal();
    
    /**
     * Returns <b>true</b> when this method node is marked as static, otherwise
     * the return value will be <b>false</b>.
     *
     * @return boolean
     */
    function isStatic();
    
    /**
     * Returns the parent class or interface node.
     *
     * @return PHP_Reflection_AST_ClassOrInterfaceI
     */
    function getParent();
}