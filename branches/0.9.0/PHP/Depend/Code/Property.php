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
 * @link       http://pdepend.org/
 */

require_once 'PHP/Depend/Code/AbstractItem.php';

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
 * @link       http://pdepend.org/
 */
class PHP_Depend_Code_Property extends PHP_Depend_Code_AbstractItem
{
    /**
     * The parent type object.
     *
     * @var PHP_Depend_Code_Class $_parent
     */
    private $_parent = null;

    /**
     * A reference instance for the php type of this property.
     *
     * @var PHP_Depend_Code_ClassOrInterfaceReference $_classReference
     */
    private $_classReference = null;

    /**
     * Defined modifiers for this property node.
     *
     * @var integer $_modifiers
     */
    private $_modifiers = 0;

    /**
     * The source tokens used for this parameter declaration.
     *
     * @var array(PHP_Depend_Token) $_tokens
     * @since 0.9.5
     */
    private $_tokens = null;

    /**
     * The default value for this property or <b>null</b> when no default value
     * was declared.
     *
     * @var PHP_Depend_Code_Value $value
     * @since 0.9.6
     */
    private $_value = null;

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
     * @return PHP_Depend_Code_AbstractClassOrInterface
     * @since 0.9.5
     */
    public function getClass()
    {
        if ($this->_classReference === null) {
            return null;
        }
        return $this->_classReference->getType();
    }

    /**
     * Sets a reference instance for the php type of this property.
     *
     * @param PHP_Depend_Code_ClassOrInterfaceReference $classReference Holder
     *        instance for the declared property type.
     *
     * @return void
     * @since 0.9.5
     */
    public function setClassReference(
        PHP_Depend_Code_ClassOrInterfaceReference $classReference
    ) {
        $this->_classReference = $classReference;
    }

    /**
     * Returns the source tokens used for this property declaration.
     *
     * @return array(PHP_Depend_Token)
     * @since 0.9.6
     */
    public function getTokens()
    {
        return $this->_tokens;
    }

    /**
     * Sets the source tokens used for this property declaration.
     *
     * @param array(PHP_Depend_Token) $tokens The source tokens.
     *
     * @return void
     * @since 0.9.6
     */
    public function setTokens(array $tokens)
    {
        if ($this->_tokens === null) {
            $this->_tokens = $tokens;
        }
    }

    /**
     * Returns the line number where the property declaration can be found.
     *
     * @return integer
     * @since 0.9.6
     */
    public function getStartLine()
    {
        assert(($token = reset($this->_tokens)) instanceof PHP_Depend_Token);
        return $token->startLine;
    }

    /**
     * Returns the line number where the property declaration ends.
     *
     * @return integer
     * @since 0.9.6
     */
    public function getEndLine()
    {
        assert(($token = end($this->_tokens)) instanceof PHP_Depend_Token);
        return $token->endLine;
    }

    /**
     * This method will return the default value for this property instance or
     * <b>null</b> when this property was only declared and not initialized.
     *
     * @return mixed
     * @since 0.9.6
     */
    public function getValue()
    {
        if ($this->_value === null) {
            return null;
        }
        return $this->_value->getValue();
    }

    /**
     * This method is used by the parser to the a declared default value for
     * this property.
     *
     * @param PHP_Depend_Code_Value $value The declared property default value.
     *
     * @return void
     * @since 0.9.6
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

    /**
     * Returns the type of this property. This method will return <b>null</b>
     * for all scalar type, only class properties will have a type.
     *
     * @return PHP_Depend_Code_AbstractClassOrInterface
     * @deprecated Since version 0.9.5, use getClass() instead.
     */
    public function getType()
    {
        fwrite(STDERR, 'Since 0.9.5 getType() is deprecated.' . PHP_EOL);
        return $this->getClass();
    }

    /**
     * Sets the type of this property.
     *
     * @param PHP_Depend_Code_AbstractClassOrInterface $type The property type.
     *
     * @return void
     * @deprecated Since version 0.9.5, use setClassReference() instead.
     */
    public function setType(PHP_Depend_Code_AbstractClassOrInterface $type)
    {
        fwrite(STDERR, 'Since 0.9.5 setType() is deprecated.' . PHP_EOL);
    }

    // @codeCoverageIgnoreEnd
}