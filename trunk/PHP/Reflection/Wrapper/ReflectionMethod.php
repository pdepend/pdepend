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
 * @subpackage Wrapper
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Reflection/Wrapper/ReflectionClass.php';

/**
 * This is wrapper around PHP_Reflection's {@link PHP_Reflection_AST_Method} that
 * is compatible with PHP's internal <b>ReflectionMethod</b> class.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Wrapper
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_Wrapper_ReflectionMethod extends ReflectionMethod
{
    /**
     * The wrapped PHP_Reflection method instance.
     *
     * @var PHP_Reflection_AST_MethodI $_method
     */
    private $_method = null;
    
    /**
     * Constructs a new method wrapper for the given ast node.
     *
     * @param PHP_Reflection_AST_MethodI $method The wrapped method instance.
     */
    public function __construct(PHP_Reflection_AST_MethodI $method)
    {
        $this->_method = $method;
    }
    
    /**
     * Will invoke this method on the given <b>$object</b>.
     *
     * @param stdClass $object The context object instance.
     * @param mixed    $args   Optional arguments.
     * 
     * @return mixed The method result.
     */
    public function invoke($object, $args = null)
    {
        // FIXME: Implement this method
    }
    
    /**
     * Will invoke this method on the given <b>$object</b>.
     *
     * @param stdClass $object The context object instance.
     * @param array    $args   Optional argument array similar to call_func_array().
     * 
     * @return mixed The method result.
     */
    public function invokeArgs($object, array $args = array())
    {
        // FIXME: Implement this method
    }
    
    /**
     * Returns <b>true</b> when this method was defined as final.
     *
     * @return boolean
     */
    public function isFinal()
    {
        return $this->_method->isFinal();
    }
    
    /**
     * Returns <b>true</b> when this method was defined as abstract.
     *
     * @return boolean
     */
    public function isAbstract()
    {
        return $this->_method->isAbstract();
    }
    
    /**
     * Returns <b>true</b> when this method was defined as public.
     *
     * @return boolean
     */
    public function isPublic()
    {
        return $this->_method->isPublic();
    }
    
    /**
     * Returns <b>true</b> when this method was defined as private.
     *
     * @return boolean
     */
    public function isPrivate()
    {
        return $this->_method->isPrivate();
    }
    
    /**
     * Returns <b>true</b> when this method was defined as protected.
     *
     * @return boolean
     */
    public function isProtected()
    {
        return $this->_method->isProtected();
    }
    
    /**
     * Returns <b>true</b> when this method was defined as static.
     *
     * @return boolean
     */
    public function isStatic()
    {
        return $this->_method->isStatic();
    }
    
    /**
     * Returns <b>true</b> when this method is the constructor.
     *
     * @return boolean
     */
    public function isConstructor()
    {
        return $this->_method->isConstructor();
    }
    
    /**
     * Returns <b>true</b> when this method is the destructor.
     *
     * @return boolean
     */
    public function isDestructor()
    {
        return $this->_method->isDestructor();
    }
    
    /**
     * Returns the defined modifiers for this method.
     *
     * @return integer
     */
    public function getModifiers()
    {
        return $this->_method->getModifiers();
    }
    
    /**
     * Returns a closure for this method.
     *
     * @return mixed
     */
    public function getClosure()
    {
        throw new ReflectionException('Method getClosure() is not implemented.');
    }
    
    /**
     * Returns the declaring class for this method.
     *
     * @return ReflectionClass
     */
    public function getDeclaringClass()
    {
        $parent = $this->_method->getParent();
        if ($parent instanceof PHP_Reflection_AST_ClassI) {
            return new PHP_Reflection_Wrapper_ReflectionClass($parent);
        }
        return new PHP_Reflection_Wrapper_ReflectionInterface($parent);
    }
    
    /**
     * Returns the name of this method.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_method->getName();
    }
    
    /**
     * Returns <b>true</b> when this method was defined by an internal class.
     * 
     * <code>
     * class ArrayIt extends ArrayIterator {
     *     public function reset() {
     *     }
     * }
     * $ref = new ReflectionMethod('ArrayIt', 'reset');
     * $ref->isInternal(); // Results in: false
     * </code>
     *
     * @return boolean
     */
    public function isInternal()
    {
        // FIXME: Implement this method
    }
    
    /**
     * This method returns <b>true</b> when this method is user defined.
     *
     * @return boolean
     */
    public function isUserDefined()
    {
        return !$this->isInternal();
    }
    
    /**
     * Returns the file name where this method was defined or <b>false</b>.
     *
     * @return string
     */
    public function getFileName()
    {
        $fileName = null;
        if ($this->_method instanceof PHP_Reflection_AST_AbstractSourceElement) {
            $fileName = $this->_method->getSourceFile()->getFileName();
        }
        return ($fileName === null ? false : $fileName);
    }
    
    /**
     * Returns the start line where this method was defined or <b>false</b>.
     *
     * @return string
     */
    public function getStartLine()
    {
        $line = 0;
        if ($this->_method instanceof PHP_Reflection_AST_AbstractSourceElement) {
            $line = $this->_method->getLine();
        }
        return ($line <= 0 ? false : $line);
    }
    
    /**
     * Returns the end line where this method was defined or <b>false</b>.
     *
     * @return string
     */
    public function getEndLine()
    {
        $line = 0;
        if ($this->_method instanceof PHP_Reflection_AST_AbstractSourceElement) {
            $line = $this->_method->getEndLine();
        }
        return ($line <= 0 ? false : $line);
    }
    
    /**
     * Returns the doc comment for this method or <b>false</b>.
     *
     * @return string
     */
    public function getDocComment()
    {
        $comment = '';
        if ($this->_method instanceof PHP_Reflection_AST_AbstractSourceElement) {
            $comment = $this->_method->getDocComment();
        }
        return ($comment === '' ? false : $comment);
    }
    
    /**
     * Returns static what?
     *
     * @return array()
     */
    public function getStaticVariables()
    {
        // FIXME: Implement this method
    }
    
    /**
     * Returns <b>true</b> when this method returns a value as reference.
     *
     * @return boolean
     */
    public function returnsReference()
    {
        // FIXME: Implement this method
    }
    
    /**
     * Returns all parameters of the method signature.
     *
     * @return array(ReflectionParameter)
     */
    public function getParameters()
    {
        // FIXME: Implement this method
    }
    
    /**
     * Returns the number of all parameters in the the method signature.
     *
     * @return integer
     */
    public function getNumberOfParameters()
    {
        return $this->_method->getParameters()->count();
    }
    
    /**
     * Returns the number of required parameters.
     *
     * @return integer
     */
    public function getNumberOfRequiredParameters()
    {
        foreach ($this->_method->getParameters() as $parameter) {
            
        }
        // FIXME: Implement this method
    }
    
    /**
     * Returns a string representation of this method.
     *
     * @return string
     */
    public function __toString()
    {
        // FIXME: Implement this method
    }
}