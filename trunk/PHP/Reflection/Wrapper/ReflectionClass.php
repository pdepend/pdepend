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

require_once 'PHP/Reflection/Wrapper/ReflectionMethod.php';

/**
 * This is wrapper around PHP_Reflection's {@link PHP_Reflection_AST_Class} that
 * is compatible with PHP's internal <b>ReflectionClass</b>.
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
class PHP_Reflection_Wrapper_ReflectionClass extends ReflectionClass
{
    /**
     * The wrapper class instance.
     *
     * @var PHP_Reflection_AST_ClassI $_class
     */
    private $_class = null;
    
    /**
     * Constructs a new reflection wrapper for classes.
     *
     * @param PHP_Reflection_AST_ClassI $class The wrapped ast class instance.
     */
    public function __construct(PHP_Reflection_AST_ClassI $class)
    {
        $this->_class = $class;
    }
    
    /**
     * Returns the class name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_class->getName();
    }
    
    /**
     * Returns <b>true</b> when this is an internal class.
     *
     * @return boolean
     */
    public function isInternal()
    {
        // FIXME: Implement this method
    }
    
    /**
     * Returns <b>true</b> when this is an user defined class.
     *
     * @return boolean
     */
    public function isUserDefined()
    {
        // FIXME: Implement this method
    }
    
    /**
     * Returns <b>true</b> when this class can be instantiated.
     *
     * @return boolean
     */
    public function isInstantiable()
    {
        // FIXME: Implement this method
    }
    
    /**
     * Tests that a constant for the given name exists.
     *
     * @param string $name The constant name.
     * 
     * @return boolean
     */
    public function hasConstant($name)
    {
        $constantName = strtolower($name);
        foreach ($this->_class->getConstants() as $constant) {
            if (strtolower($constant->getName()) === $constantName) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Tests that a method for the given name exists.
     *
     * @param string $name The method name.
     * 
     * @return boolean
     */
    public function hasMethod($name)
    {
        $methodName = strtolower($name);
        foreach ($this->getMethods() as $method) {
            if (strtolower($method->getName()) === $methodName) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Tests that a property for the given name exists.
     *
     * @param string $name The property name.
     * 
     * @return boolean
     */
    public function hasProperty($name)
    {
        // FIXME: Implement this method
    }
    
    /**
     * Returns the file name where this class was defined or <b>false</b>.
     *
     * @return string
     */
    public function getFileName()
    {
        // Stupid: Breaks polymorphism :-(
        if ($this->_class instanceof PHP_Reflection_AST_SourceElementI) { 
            return $this->_class->getSourceFile()->getFileName();
        }
        return false;
    }
    
    /**
     * Returns the start line where this class was defined or <b>false</b>. 
     *
     * @return integer
     */
    public function getStartLine()
    {
        // Stupid: Breaks polymorphism :-(
        if ($this->_class instanceof PHP_Reflection_AST_SourceElementI) { 
            return $this->_class->getLine();
        }
        return false;
    }
    
    /**
     * Returns the line where the declaration of this class ends or <b>false</b>. 
     *
     * @return integer
     */
    public function getEndLine()
    {
        // Stupid: Breaks polymorphism :-(
        if ($this->_class instanceof PHP_Reflection_AST_AbstractSourceElement) { 
            return $this->_class->getEndLine();
        }
        return false;
    }
    
    /**
     * Returns the doc comment for this class or <b>false</b>. 
     *
     * @return integer
     */
    public function getDocComment()
    {
        $comment = '';
        
        // Stupid: Breaks polymorphism :-(
        if ($this->_class instanceof PHP_Reflection_AST_AbstractSourceElement) {
            $comment = trim($this->_class->getDocComment());
        }
        return ($comment === '' ? false : $comment);
    }
    
    /**
     * Returns the constructor of the context class or <b>null</b> when no ctor
     * exists.
     *
     * @return ReflectionMethod
     */
    public function getConstructor()
    {
        // FIXME: Implement this method
    }
    
    /**
     * Returns the method instance for the given <b>$name</b> or throws an 
     * exception when no matching method exists.
     *
     * @param string $name The method name.
     * 
     * @return ReflectionMethod
     * @throws ReflectionException If no matching method exists.
     */
    public function getMethod($name)
    {
        try {
            $method = $this->_class->getMethod($name);
            return new PHP_Reflection_Wrapper_ReflectionMethod($method);
        } catch (PHP_Reflection_Exceptions_UnknownNodeException $e) {
            $message = sprintf('Method %s does not exist in %s on line %d',
                $name,
                __FILE__,
                __LINE__);
            throw new ReflectionException($message);
        }
    }
    
    /**
     * Returns an <b>array</b> with all methods defined in this class.
     * 
     * <code>
     * $ref = new ReflectionClass('PHP_Reflection');
     * // Returns all final public methods
     * $ref->getMethods(ReflectionMethod::IS_PUBLIC|ReflectionMethod::IS_FINAL);
     * </code>
     * 
     * @param integer $filter An optional filter for the returned methods.
     *
     * @return array(ReflectionMethod)
     */
    public function getMethods($filter = 0)
    {
        $methods = array();
        foreach ($this->_class->getMethods() as $method) {
            if ($filter !== 0 && ($method->getModifiers() & $filter) === 0) {
                continue;
            }
            $name = strtolower($method->getName());
            if (isset($methods[$name])) {
                continue;
            }
            $methods[$name] = new PHP_Reflection_Wrapper_ReflectionMethod($method);
        }
        
        // Append methods of parent class
        if (($parent = $this->getParentClass()) !== false) {
            foreach ($parent->getMethods($filter) as $method) {
                $name = strtolower($method->getName());
                if (isset($methods[$name])) {
                    continue;
                }
                $methods[$name] = $method;
            }
        }
        
        // Append methods of parent interfaces
        foreach ($this->getInterfaces() as $interface) {
            foreach ($parent->getMethods($filter) as $method) {
                $name = strtolower($method->getName());
                if (isset($methods[$name])) {
                    continue;
                }
                $methods[$name] = $method;
            }
        }
        return array_values($methods);
    }
    
    /**
     * Returns the property instance for the given <b>$name</b> or throws an 
     * exception when no matching property exists.
     *
     * @param string $name The property name.
     * 
     * @return ReflectionProperty
     * @throws ReflectionException If no matching property exists.
     */
    public function getProperty($name)
    {
            try {
            // FIXME: Implement this method
            $this->_class->getProperty($name);
        } catch (PHP_Reflection_Exceptions_UnknownNodeException $e) {
            $message = sprintf('Property %s does not exist in %s on line %d',
                $name,
                __FILE__,
                __LINE__);
            throw new ReflectionException($message);
        }        
    }
    
    /**
     * Returns an <b>array</b> with all properties defined in this class.
     * 
     * <code>
     * $ref = new ReflectionClass('PHP_Reflection');
     * // Returns all final public properties
     * $ref->getProperties(
     *  ReflectionProperty::IS_PUBLIC|ReflectionProperty::IS_STATIC
     * );
     * </code>
     * 
     * @param integer $filter An optional filter for the returned properties.
     *
     * @return array(ReflectionProperty)
     */
    public function getProperties($filter = 0)
    {
        // FIXME: Implement this method
    }
    
    /**
     * Returns the constant value for the given <b>$name</b> or <b>false</b>
     * when no matching constant exists.
     *
     * @param string $name The constant name.
     * 
     * @return mixed
     */
    public function getConstant($name)
    {
            try {
            // FIXME: Implement this method
            $this->_class->getProperty($name);
        } catch (PHP_Reflection_Exceptions_UnknownNodeException $e) {
            return false;
        }        
    }
    
    /**
     * Returns an <b>array</b> with all constants defined in this class.
     *
     * @return array(mixed)
     */
    public function getConstants()
    {
        // FIXME: Implement this method
    }
    
    /**
     * Returns an <b>array</b> with all implemented interfaces.
     *
     * @return array(ReflectionClass)
     */
    public function getInterfaces()
    {
        // FIXME: Implement this method
        return array();
    }
    
    /**
     * Returns <b>false</b> because this is a class and not an interface.
     *
     * @return boolean
     */
    public function isInterface()
    {
        return false;
    }
    
    /**
     * Returns <b>true</b> when this class is declared as abstract.
     *
     * @return boolean
     */
    public function isAbstract()
    {
        return $this->_class->isAbstract();
    }
    
    /**
     * Returns <b>true</b> when this class is declared as final, otherwise the
     * return value will be <b>false</b>.
     *
     * @return boolean
     */
    public function isFinal()
    {
        return $this->_class->isFinal();
    }
    
    /**
     * Returns the modifiers for this class.
     *
     * @return integer
     */
    public function getModifiers()
    {
        // FIXME: What should we do with implicit abstract?
    }
    
    /**
     * Returns <b>true</b> when the given <b>$object</b> is an instance of the
     * context class, otherwise the return value will be <b>false</b>.
     *
     * @param stdClass $object The object to check.
     * 
     * @return boolean
     */
    public function isInstance($object)
    {
        // TODO: Full qualified namespace names
        $className = $this->_class->getName();
        return ($object instanceof $className);
    }
    
    /**
     * Creates a new instance of the context class.
     * 
     * @param mixed $args Mixed arguments for the ctor.
     *
     * @return stdClass
     */
    public function newInstance($args)
    {
        // FIXME: Implement this method
    }
    
    /**
     * Creates a new instance of the context class.
     *
     * @param array $args Constructor arguments similar to call_user_func_array().
     * 
     * @return stdClass
     */
    public function newInstanceArgs(array $args = array())
    {
        // FIXME: Implement this method
    }
    
    /**
     * Returns the parent class for the context class or <b>false</b> if no 
     * parent exists.
     *
     * @return ReflectionClass
     */
    public function getParentClass()
    {
        if (($parentClass = $this->_class->getParentClass()) === null) {
            return false;
        }
        return new PHP_Reflection_Wrapper_ReflectionClass($parentClass);
    }
    
    /**
     * Checks if the the given class is a sub class or the context class.
     *
     * @param ReflectionClass|string $class The possible child class.
     * 
     * @return boolean
     */
    public function isSubclassOf($class)
    {
        // FIXME: Implement this method
    }
    
    /**
     * Returns an array with all static properties of this class.
     *
     * @return array(string=>mixed)
     */
    public function getStaticProperties()
    {
        // FIXME: Implement this method
    }
    
    /**
     * Returns the static property value for <b>$name</b>.
     *
     * @param string $name    The name of the static property.
     * @param mixed  $default A default value that should be used when no property
     *                        for $name exists or the property has no default
     *                        value.
     */
    public function getStaticPropertyValue($name, $default = null)
    {
        // FIXME: Implement this method
    }
    
    /**
     * Sets the value of a public static class property.
     *
     * @param string $name  The name of the static property.
     * @param mixed  $value The new property value.
     */
    public function setStaticPropertyValue($name, $value)
    {
        // FIXME: Implement this method
    }
    
    /**
     * Returns the default values of all properties in the context class.
     * 
     * <code>
     * class PHP_Reflection {
     *     private $_x = 42;
     *     protected $y;
     *     public static $z = 3.14;
     * }
     * 
     * // Results in
     * array(
     *     '_x' => 42,
     *     'y'  => null,
     *     'z'  => null
     * )
     * </code>
     *
     * @return array(string=>mixed)
     */
    public function getDefaultProperties()
    {
        // FIXME: Implement this method
    }
    
    /**
     * Returns <b>true</b> when this class is directly usable as loop context,
     * otherwise the returned value is <b>false</b>.
     *
     * @return boolean
     */
    public function isIterateable()
    {
        // FIXME: Implement this method
    }
    
    /**
     * Returns <b>true</b> when this class implements an interface named 
     * <b>$name</b>.
     *
     * @param string $name The interface name.
     * 
     * @return boolean
     */
    public function implementsInterface($name)
    {
        // FIXME: Implement this method
    }
    
    /**
     * Returns an instance of the extension where this class was defined. If the
     * class is not part of an extension this method will return <b>null</b>.
     *
     * @return ReflectionExtension
     */
    public function getExtension()
    {
        // FIXME: Implement this method
    }
    
    /**
     * Returns the name of the extension where this class was defined. If this
     * class isn't part of an extension the returned value will be <b>false</b>.
     *
     * @return string
     */
    public function getExtensionName()
    {
        if (($extension = $this->getExtension()) === null) {
            return false; 
        }
        return $extension->getName();
    }
    
    /**
     * Returns a string representation of this class.
     *
     * @return string
     */
    public function __toString()
    {
        // FIXME: Implement this method
    }
}