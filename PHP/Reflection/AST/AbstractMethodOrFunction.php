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

require_once 'PHP/Reflection/AST/AbstractItem.php';
require_once 'PHP/Reflection/AST/MethodOrFunctionI.php';

/**
 * Abstract base class for callable objects.
 *
 * Callable objects is a generic parent for methods and functions.
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
abstract class PHP_Reflection_AST_AbstractMethodOrFunction 
       extends PHP_Reflection_AST_AbstractItem
    implements PHP_Reflection_AST_MethodOrFunctionI
{
    /**
     * The tokens for this function.
     *
     * @var array(mixed) $tokens
     */
    protected $tokens = array();

    /**
     * List of {@link PHP_Reflection_AST_AbstractClassOrInterface} objects this function
     * depends on.
     *
     * @var array(PHP_Reflection_AST_AbstractClassOrInterface) $dependencies
     */
    protected $dependencies = array();

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
     * @type array<PHP_Reflection_AST_Parameter>
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
     * Returns all {@link PHP_Reflection_AST_ClassOrInterfaceI} objects this 
     * function depends on.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    public function getDependencies()
    {
        $dependencies = $this->dependencies;
        foreach ($this->_parameters as $parameter) {
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
     * Adds the given {@link PHP_Reflection_AST_ClassOrInterfaceI} object
     * as dependency.
     *
     * @param PHP_Reflection_AST_ClassOrInterfaceI $type A type this function depends on.
     *
     * @return void
     */
    public function addDependency(PHP_Reflection_AST_ClassOrInterfaceI $type)
    {
        if (in_array($type, $this->dependencies, true) === false) {
            $this->dependencies[] = $type;
        }
    }

    /**
     * Removes the given {@link PHP_Reflection_AST_ClassOrInterfaceI} 
     * object from the dependency list.
     *
     * @param PHP_Reflection_AST_ClassOrInterfaceI $type A type to remove.
     *
     * @return void
     */
    public function removeDependency(PHP_Reflection_AST_ClassOrInterfaceI $type)
    {
        if (($i = array_search($type, $this->dependencies, true)) !== false) {
            // Remove from internal list
            unset($this->dependencies[$i]);
        }
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
        return new PHP_Reflection_AST_Iterator($this->_parameters);
    }

    /**
     * Adds a parameter to the list of method/function parameters.
     *
     * @param PHP_Reflection_AST_ParameterI $parameter The parameter instance.
     *
     * @return PHP_Reflection_AST_ParameterI
     */
    public function addParameter(PHP_Reflection_AST_ParameterI $parameter)
    {
        if ($parameter->getDeclaringMethodOrFunction() !== null) {
            $parameter->getDeclaringMethodOrFunction()->removeParameter($parameter);
        }
        // Set this as parent
        $parameter->setDeclaringMethodOrFunction($this);
        // Store reference
        $this->_parameters[] = $parameter;

        return $parameter;
    }

    /**
     * Removes the parameter from this callable.
     *
     * @param PHP_Reflection_AST_ParameterI $parameter The parameter instance.
     *
     * @return void
     */
    public function removeParameter(PHP_Reflection_AST_ParameterI $parameter)
    {
        if (($i = array_search($parameter, $this->_parameters, true)) !== false) {
            // Remove this parent
            $parameter->setDeclaringMethodOrFunction(null);
            // Remove internal reference
            unset($this->_parameters[$i]);
        }
    }
}