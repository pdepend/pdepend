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

require_once 'PHP/Reflection/AST/AbstractSourceElement.php';
require_once 'PHP/Reflection/AST/CallableI.php';

/**
 * Abstract base class for callable objects.
 *
 * Callable objects is a generic parent for methods and functions.
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
abstract class PHP_Reflection_AST_AbstractCallable
       extends PHP_Reflection_AST_AbstractSourceElement
    implements PHP_Reflection_AST_CallableI
{
    /**
     * The tokens for this function.
     *
     * @var array(mixed) $tokens
     */
    protected $tokens = array();

    /**
     * If this is set to <b>true</b> the return value is returned by reference.
     *
     * @var boolean $_returnsReference
     */
    private $_returnsReference = false;

    /**
     * List of {@link PHP_Reflection_AST_AbstractClassOrInterface} objects this function
     * depends on.
     *
     * @var array(PHP_Reflection_AST_AbstractClassOrInterface) $_dependencies
     */
    private $_dependencies = array();

    /**
     * The return type for this callable. By default and for scalar types this
     * will be <b>null</b>.
     *
     * @var AbstractClassOrInterface $_returnType
     */
    private $_returnType = null;

    /**
     * A list of all thrown exception types.
     *
     * @var array(AbstractClassOrInterface) $_exceptionTypes
     */
    private $_exceptionTypes = array();

    /**
     * List of method/function parameters.
     *
     * @var array(PHP_Reflection_AST_Parameter) $_parameters
     */
    private $_parameters = array();

    /**
     * Returns the tokens found in the function body.
     *
     * @return array(mixed)
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * Sets the tokens found in the function body.
     *
     * @param array(mixed) $tokens The body tokens.
     *
     * @return void
     */
    public function setTokens(array $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * This method should return <b>true</b> when the context method or function
     * returns a reference.
     *
     * @return boolean
     */
    public function returnsReference()
    {
        return $this->_returnsReference;
    }

    /**
     * This method will set an internal flag which indicates that the method or
     * function return value is a reference.
     *
     * @param boolean $returnsReference Yes or no?
     *
     * @return void
     */
    public function setReturnsReference($returnsReference)
    {
        $this->_returnsReference = (boolean) $returnsReference;
    }

    /**
     * Returns all {@link PHP_Reflection_AST_ClassOrInterfaceI} objects this
     * function depends on.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    public function getDependencies()
    {
        $dependencies = array();

        $children = $this->findChildrenOfType('PHP_Reflection_AST_ClassOrInterfaceI');
        foreach ($children as $child) {
            $dependencies[] = $child;
        }
        foreach ($this->getParameters() as $parameter) {
            // Skip all scalar parameters
            if (($type = $parameter->getType()) === null) {
                continue;
            }
            // Add only once
            if (in_array($type, $dependencies, true) === false) {
                $dependencies[] = $type;
            }
        }

        return new PHP_Reflection_AST_Iterator($dependencies);
    }

    /**
     * Returns the return type of this callable. By default and for scalar types
     * this will be <b>null</b>.
     *
     * @return PHP_Reflection_AST_ClassOrInterfaceI
     */
    public function getReturnType()
    {
        return $this->_returnType;
    }

    /**
     * Sets the return type of this callable.
     *
     * @param PHP_Reflection_AST_ClassOrInterfaceI $returnType The return type of this.
     *
     * @return void
     */
    public function setReturnType(PHP_Reflection_AST_ClassOrInterfaceI $returnType)
    {
        $this->_returnType = $returnType;
    }

    /**
     * Returns an iterator with {@link PHP_Reflection_AST_ClassOrInterfaceI}
     * nodes thrown by this function or method.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    public function getExceptionTypes()
    {
        return new PHP_Reflection_AST_Iterator($this->_exceptionTypes);
    }

    /**
     * Adds an exception to the list of thrown exception types.
     *
     * @param PHP_Reflection_AST_ClassOrInterfaceI $exception Thrown exception.
     *
     * @return void
     */
    public function addExceptionType(PHP_Reflection_AST_ClassOrInterfaceI $exception)
    {
        if (in_array($exception, $this->_exceptionTypes, true) === false) {
            $this->_exceptionTypes[] = $exception;
        }
    }

    /**
     * Removes an exception from the list of thrown exception types.
     *
     * @param PHP_Reflection_AST_ClassOrInterfaceI $exception Thrown exception.
     *
     * @return void
     */
    public function removeExceptionType(PHP_Reflection_AST_ClassOrInterfaceI $exception)
    {
        if (($i = array_search($exception, $this->_exceptionTypes, true))) {
            unset($this->_exceptionTypes[$i]);
        }
    }

    /**
     * Returns an iterator with all method/function parameters.
     *
     * <b>NOTE:</b> All node iterators return an alphabetic ordered list of
     * nodes. Use the {@link PHP_Reflection_AST_ParameterI::getPosition()} for
     * the correct parameter position.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    public function getParameters()
    {
        $paramList = $this->getFirstChildOfType('PHP_Reflection_AST_ParameterListI');
        if ($paramList !== null) {
            $params = $paramList->getChildrenOfType('PHP_Reflection_AST_ParameterI');
        } else {
            $params = array();
        }

        $params = $this->findChildrenOfType('PHP_Reflection_AST_ParameterI');

        return new PHP_Reflection_AST_Iterator($params);
        return new PHP_Reflection_AST_Iterator($this->_parameters);
    }
}