<?php
/**
 * This file is part of PHP_Depend.
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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id: Parameter.php 675 2009-03-05 07:40:28Z mapi $
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/NodeI.php';

/**
 * An instance of this class represents a function or method parameter within
 * the analyzed source code.
 *
 * <code>
 * <?php
 * class PHP_Depend_BuilderI
 * {
 *     public function buildNode($name, $line, PHP_Depend_Code_File $file) {
 *     }
 * }
 *
 * function parse(PHP_Depend_BuilderI $builder, $file) {
 * }
 * </code>
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Code_Parameter implements PHP_Depend_Code_NodeI
{
    /**
     * The name for this item.
     *
     * @var string $_name
     */
    private $_name = '';

    /**
     * The unique identifier for this function.
     *
     * @var PHP_Depend_Util_UUID $_uuid
     */
    private $_uuid = null;

    /**
     * The line number where the item declaration starts.
     *
     * @var integer $_startLine
     */
    private $_startLine = 0;

    /**
     * The line number where the item declaration ends.
     *
     * @var integer $_endLine
     */
    private $_endLine = 0;

    /**
     * The parent function or method instance.
     *
     * @var PHP_Depend_Code_AbstractCallable $_declaringFunction
     */
    private $_declaringFunction = null;

    /**
     * The parameter position.
     *
     * @var integer $_position
     */
    private $_position = 0;

    /**
     * The type for this parameter. This value is <b>null</b> by default and for
     * scalar types.
     *
     * @var PHP_Depend_Code_AbstractType $_class
     */
    private $_class = null;

    /**
     * The parameter is declared with the array type hint, when this property is
     * set to <b>true</b>.
     *
     * @var boolean $_array
     */
    private $_array = false;

    /**
     * This property is set to <b>true</b> when the parameter is passed by
     * reference.
     *
     * @var boolean $_passedByReference
     * @since 0.9.5
     */
    private $_passedByReference = false;

    /**
     * The default value for this parameter or <b>null</b> when no default value
     * was declared.
     *
     * @var PHP_Depend_Code_Value $value
     */
    private $_value = null;

    /**
     * Constructs a new parameter instance for the given <b>$name</b>.
     *
     * @param string $name The item name.
     */
    public function __construct($name)
    {
        $this->_name = $name;
        $this->_uuid = new PHP_Depend_Util_UUID();
    }

    /**
     * Returns the item name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Returns a uuid for this code node.
     *
     * @return string
     */
    public function getUUID()
    {
        return (string) $this->_uuid;
    }

    /**
     * Returns the line number where the item declaration can be found.
     *
     * @return integer
     */
    public function getStartLine()
    {
        return $this->_startLine;
    }

    /**
     * Sets the start line for this item.
     *
     * @param integer $startLine The start line for this item.
     *
     * @return void
     */
    public function setStartLine($startLine)
    {
        $this->_startLine = $startLine;
    }

    /**
     * Returns the line number where the item declaration ends.
     *
     * @return integer The last source line for this item.
     */
    public function getEndLine()
    {
        return $this->_endLine;
    }

    /**
     * Sets the end line for this item.
     *
     * @param integer $endLine The end line for this item
     *
     * @return void
     */
    public function setEndLine($endLine)
    {
        $this->_endLine = $endLine;
    }

    /**
     * Returns the parent function or method instance or <b>null</b>
     *
     * @return PHP_Depend_Code_AbstractCallable
     * @since 0.9.5
     */
    public function getDeclaringFunction()
    {
        return $this->_declaringFunction;
    }

    /**
     * Sets the parent function or method object.
     *
     * @param PHP_Depend_Code_AbstractCallable $function The parent callable.
     *
     * @return void
     * @since 0.9.5
     */
    public function setDeclaringFunction(PHP_Depend_Code_AbstractCallable $function)
    {
        $this->_declaringFunction = $function;
    }

    /**
     * Returns the parameter position in the method/function signature.
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->_position;
    }

    /**
     * Sets the parameter position in the method/function signature.
     *
     * @param integer $position The parameter position.
     *
     * @return void
     */
    public function setPosition($position)
    {
        $this->_position = $position;
    }

    /**
     * Returns the class type of this parameter. This method will return
     * <b>null</b> for all scalar type, only classes or interfaces are used.
     *
     * @return PHP_Depend_Code_AbstractType
     * @since 0.9.5
     */
    public function getClass()
    {
        return $this->_class;
    }

    /**
     * Sets the class type for this parameter. This method will only set its
     * internal state on the first call.
     *
     * @param PHP_Depend_Code_AbstractType $class The parameter class type.
     *
     * @return void
     * @since 0.9.5
     */
    public function setClass(PHP_Depend_Code_AbstractType $class)
    {
        // TODO Refs #66: Remove the "true" expression
        if (true || $this->_class === null) {
            $this->_class = $class;
        }
    }

    /**
     * This method will return <b>true</b> when the parameter is passed by
     * reference.
     *
     * @return boolean
     * @since 0.9.5
     */
    public function isPassedByReference()
    {
        return $this->_passedByReference;
    }

    /**
     * This method can be used to mark this parameter as passed by reference.
     *
     * @param boolean $passedByReference Boolean flag.
     *
     * @return void
     * @since 0.9.5
     */
    public function setPassedByReference($passedByReference)
    {
        $this->_passedByReference = (boolean) $passedByReference;
    }

    /**
     * This method will return <b>true</b> when the parameter was declared with
     * the array type hint, otherwise the it will return <b>false</b>.
     *
     * @return boolean
     * @since 0.9.5
     */
    public function isArray()
    {
        return $this->_array;
    }

    /**
     * This method sets the is array flag of this parameter. This means the
     * parameter was declared with the array type hint when this will be set to
     * <b>true</b>.
     *
     * @param boolean $array Boolean flag that indicates declared as array?
     *
     * @return void
     * @since 0.9.5
     */
    public function setArray($array)
    {
        $this->_array = (boolean) $array;
    }

    /**
     * This method will return <b>true</b> when the parameter declaration
     * contains a default value.
     *
     * @return boolean
     */
    public function isDefaultValueAvailable()
    {
        if ($this->_value === null) {
            return false;
        }
        return $this->_value->isValueAvailable();
    }

    /**
     * This method will return the declared default value for this parameter.
     * Please note that this method will return <b>null</b> when no default
     * value was declared, therefore you should combine calls to this method and
     * {@link PHP_Depend_Code_Parameter::isDefaultValueAvailable()} to detect a
     * NULL-value.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        if ($this->_value === null) {
            return null;
        }
        return $this->_value->getValue();
    }

    /**
     * This method is used by the parser to the a declared default value for
     * this parameter.
     *
     * @param PHP_Depend_Code_Value $value The declared parameter default value.
     *
     * @return void
     */
    public function setValue(PHP_Depend_Code_Value $value = null)
    {
        $this->_value = $value;
    }

    /**
     * Visitor method for node tree traversal.
     *
     * @param PHP_Depend_VisitorI $visitor The context visitor
     *                                              implementation.
     *
     * @return void
     */
    public function accept(PHP_Depend_VisitorI $visitor)
    {
        $visitor->visitParameter($this);
    }

    // Deprecated methods

    /**
     * Returns the type of this property. This method will return <b>null</b>
     * for all scalar type, only class properties will have a type.
     *
     * @return PHP_Depend_Code_AbstractType
     * @deprecated since 0.9.5
     */
    public function getType()
    {
        fwrite(STDERR, __METHOD__ . '() is deprecated since 0.9.5.' . PHP_EOL);
        return $this->getClass();
    }

    /**
     * Sets the type of this property.
     *
     * @param PHP_Depend_Code_AbstractType $type The property type.
     *
     * @return void
     * @deprecated since 0.9.5
     */
    public function setType(PHP_Depend_Code_AbstractType $type)
    {
        fwrite(STDERR, __METHOD__ . '() is deprecated since 0.9.5.' . PHP_EOL);
        $this->setClass($type);
    }

    /**
     * Returns the parent function or method instance or <b>null</b>
     *
     * @return PHP_Depend_Code_AbstractCallable|null
     * @deprecated since 0.9.5
     */
    public function getParent()
    {
        fwrite(STDERR, __METHOD__ . '() is deprecated since 0.9.5.' . PHP_EOL);
        return $this->getDeclaringFunction();
    }

    /**
     * Sets the parent function or method object.
     *
     * @param PHP_Depend_Code_AbstractCallable $parent The parent callable.
     *
     * @return void
     * @deprecated since 0.9.5
     */
    public function setParent(PHP_Depend_Code_AbstractCallable $parent = null)
    {
        fwrite(STDERR, __METHOD__ . '() is deprecated since 0.9.5.' . PHP_EOL);
        $this->setDeclaringFunction($parent);
    }
}