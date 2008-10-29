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

require_once 'PHP/Reflection/AST/NodeI.php';

/**
 * Base interface for functions and classes.
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
interface PHP_Reflection_AST_MethodOrFunctionI extends PHP_Reflection_AST_NodeI
{

    /**
     * Returns the tokens found in the function body.
     *
     * @return array(mixed)
     */
    function getTokens();
    
    /**
     * This method should return <b>true</b> when the context method or function
     * returns a reference.
     *
     * @return boolean
     */
    function returnsReference();

    /**
     * Returns all {@link PHP_Reflection_AST_ClassOrInterfaceI} objects this 
     * function depends on.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    function getDependencies();

    /**
     * Returns the return type of this callable. By default and for scalar types
     * this will be <b>null</b>.
     *
     * @return PHP_Reflection_AST_ClassOrInterfaceI
     */
    function getReturnType();

    /**
     * Returns an iterator with {@link PHP_Reflection_AST_ClassOrInterfaceI}
     * nodes thrown by this function or method.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    function getExceptionTypes();

    /**
     * Returns an iterator with all method/function parameters.
     *
     * <b>NOTE:</b> All node iterators return an alphabetic ordered list of
     * nodes. Use the {@link PHP_Reflection_AST_Parameter::getPosition()} for
     * the correct parameter position.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    function getParameters();
}