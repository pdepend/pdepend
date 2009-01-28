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
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/AbstractItem.php';
require_once 'PHP/Depend/Code/TypeAwareI.php';

/**
 * This code class represents a class property.
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
class PHP_Depend_Code_Property
    extends PHP_Depend_Code_AbstractItem
    implements PHP_Depend_Code_TypeAwareI
{
    /**
     * The parent type object.
     *
     * @var PHP_Depend_Code_Class $_parent
     */
    private $_parent = null;

    /**
     * The type for this property. This value is <b>null</b> by default and for
     * scalar types.
     *
     * @var PHP_Depend_Code_AbstractType $_type
     */
    private $_type = null;

    /**
     * Defined modifiers for this property node.
     *
     * @var integer $_modifiers
     */
    private $_modifiers = 0;

    /**
     * This method sets a OR combined integer of the declared modifiers for this
     * node.
     *
     * This method will throw an exception when the value of given <b>$modifiers</b> 
     * contains an invalid/unexpected modifier
     *
     * @param integer $modifiers The declared modifiers for this node.
     *
     * @return void
     * @throws InvalidArgumentException If the given modifier contains unexpected
     *                                  values.
     * @since 0.9.4
     */
    public function setModifiers($modifiers)
    {
        if ($this->_modifiers !== 0) {
            return;
        }

        $expected = ~PHP_Depend_ConstantsI::IS_PUBLIC
                  & ~PHP_Depend_ConstantsI::IS_PROTECTED
                  & ~PHP_Depend_ConstantsI::IS_PRIVATE
                  & ~PHP_Depend_ConstantsI::IS_STATIC;

        if (($expected & $modifiers) !== 0) {
            throw new InvalidArgumentException('Invalid property modifier given.');
        }

        $this->_modifiers = $modifiers;
    }

    /**
     * Returns <b>true</b> if this node is marked as public, otherwise the
     * returned value will be <b>false</b>.
     *
     * @return boolean
     */
    public function isPublic()
    {
        return (($this->_modifiers & PHP_Depend_ConstantsI::IS_PUBLIC)
                                 === PHP_Depend_ConstantsI::IS_PUBLIC);
    }

    /**
     * Returns <b>true</b> if this node is marked as protected, otherwise the
     * returned value will be <b>false</b>.
     *
     * @return boolean
     */
    public function isProtected()
    {
        return (($this->_modifiers & PHP_Depend_ConstantsI::IS_PROTECTED)
                                 === PHP_Depend_ConstantsI::IS_PROTECTED);
    }

    /**
     * Returns <b>true</b> if this node is marked as private, otherwise the
     * returned value will be <b>false</b>.
     *
     * @return boolean
     */
    public function isPrivate()
    {
        return (($this->_modifiers & PHP_Depend_ConstantsI::IS_PRIVATE)
                                 === PHP_Depend_ConstantsI::IS_PRIVATE);
    }

    /**
     * Returns <b>true</b> when this node is declared as static, otherwise
     * the returned value will be <b>false</b>.
     *
     * @return boolean
     */
    public function isStatic()
    {
        return (($this->_modifiers & PHP_Depend_ConstantsI::IS_STATIC)
                                 === PHP_Depend_ConstantsI::IS_STATIC);
    }

    /**
     * Returns the parent class object or <b>null</b>
     *
     * @return PHP_Depend_Code_Class
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * Sets the parent class object.
     *
     * @param PHP_Depend_Code_Class $parent The parent class.
     *
     * @return void
     */
    public function setParent(PHP_Depend_Code_Class $parent = null)
    {
        $this->_parent = $parent;
    }

    /**
     * Returns the type of this property. This method will return <b>null</b>
     * for all scalar type, only class properties will have a type.
     *
     * @return PHP_Depend_Code_AbstractType
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Sets the type of this property.
     *
     * @param PHP_Depend_Code_AbstractType $type The property type.
     *
     * @return void
     */
    public function setType(PHP_Depend_Code_AbstractType $type)
    {
        $this->_type = $type;
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
        $visitor->visitProperty($this);
    }

    // DEPRECATED METHODS
    // @codeCoverageIgnoreStart

    /**
     * Sets the visibility for this node.
     *
     * The given <b>$visibility</b> value must equal to one of the defined
     * constants, otherwith this method will fail with an exception.
     *
     * @param integer $visibility The node visibility.
     *
     * @return void
     * @throws InvalidArgumentException If the given visibility is not equal to
     *                                  one of the defined visibility constants.
     * @deprecated Since version 0.9.4, use setModifiers() instead.
     */
    public function setVisibility($visibility)
    {
        fwrite(STDERR, 'Since 0.9.4 setVisibility() is deprecated.' . PHP_EOL);
    }

    // @codeCoverageIgnoreEnd
}