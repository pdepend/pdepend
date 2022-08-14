<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2017 Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @since 0.9.6
 */

namespace PDepend\Source\AST;

use BadMethodCallException;
use InvalidArgumentException;
use OutOfBoundsException;
use PDepend\Source\ASTVisitor\ASTVisitor;

/**
 * This class represents a formal parameter within the signature of a function,
 * method or closure.
 *
 * Formal parameters can include a type hint, a by reference identifier and a
 * default value. The only mandatory part is the parameter identifier.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @since 0.9.6
 */
class ASTFormalParameter extends AbstractASTNode
{
    /**
     * Defined modifiers for this property node.
     *
     * @var int
     */
    protected $modifiers = 0;

    /**
     * Checks if this parameter has a type.
     *
     * @return bool
     */
    public function hasType()
    {
        return (reset($this->nodes) instanceof ASTType);
    }

    /**
     * Returns the type of this parameter.
     *
     * @return ASTType
     */
    public function getType()
    {
        $child = $this->getChild(0);
        if ($child instanceof ASTType) {
            return $child;
        }

        throw new OutOfBoundsException('The parameter does not have a type specification.');
    }

    /**
     * This method will return <b>true</b> when the parameter is declared as a
     * variable argument list <b>...</b>.
     *
     * @return bool
     *
     * @since 2.0.7
     */
    public function isVariableArgList()
    {
        return $this->getMetadataBoolean(6);
    }

    /**
     * This method can be used to mark this parameter as passed by reference.
     *
     * @return void
    @since 2.0.7
     */
    public function setVariableArgList()
    {
        $this->setMetadataBoolean(6, true);
    }

    /**
     * This method will return <b>true</b> when the parameter is passed by
     * reference.
     *
     * @return bool
     */
    public function isPassedByReference()
    {
        return $this->getMetadataBoolean(5);
    }

    /**
     * This method can be used to mark this parameter as passed by reference.
     *
     * @return void
     */
    public function setPassedByReference()
    {
        $this->setMetadataBoolean(5, true);
    }

    /**
     * Accept method of the visitor design pattern. This method will be called
     * by a visitor during tree traversal.
     *
     * @param ASTVisitor $visitor The calling visitor instance.
     *
     * @since  0.9.12
     */
    public function accept(ASTVisitor $visitor, $data = null)
    {
        return $visitor->visitFormalParameter($this, $data);
    }

    /**
     * Returns the total number of the used property bag.
     *
     * @return int
     *
     * @since  0.10.4
     * @see    ASTNode#getMetadataSize()
     */
    protected function getMetadataSize()
    {
        return 7;
    }

    /**
     * Returns the declared modifiers for this type.
     *
     * @return int
     *
     * @since  0.9.4
     */
    public function getModifiers()
    {
        return $this->modifiers;
    }

    /**
     * This method sets a OR combined integer of the declared modifiers for this
     * node.
     *
     * This method will throw an exception when the value of given <b>$modifiers</b>
     * contains an invalid/unexpected modifier
     *
     * @param int $modifiers
     *
     * @throws BadMethodCallException
     * @throws InvalidArgumentException
     *
     * @return void
     *
     * @since  0.9.4
     */
    public function setModifiers($modifiers)
    {
        if ($this->modifiers !== 0) {
            throw new BadMethodCallException(
                'Cannot overwrite previously set constructor property modifiers.'
            );
        }

        $expected = ~State::IS_PUBLIC
            & ~State::IS_PROTECTED
            & ~State::IS_PRIVATE
            & ~State::IS_READONLY;

        if (($expected & $modifiers) !== 0) {
            throw new InvalidArgumentException('Invalid constructor property modifier given.');
        }

        $this->modifiers = $modifiers;
    }

    /**
     * Returns <b>true</b> if this node is marked as public, protected or private, otherwise the
     * returned value will be <b>false</b>.
     *
     * Can happen only on constructor promotion property.
     *
     * @return bool
     */
    public function isPromoted()
    {
        return ($this->getModifiers() & (State::IS_PUBLIC | State::IS_PROTECTED | State::IS_PRIVATE)) !== 0;
    }

    /**
     * Returns <b>true</b> if this node is marked as public, otherwise the
     * returned value will be <b>false</b>.
     *
     * Can happen only on constructor promotion property.
     *
     * @return bool
     */
    public function isPublic()
    {
        return ($this->getModifiers() & State::IS_PUBLIC) === State::IS_PUBLIC;
    }

    /**
     * Returns <b>true</b> if this node is marked as protected, otherwise the
     * returned value will be <b>false</b>.
     *
     * Can happen only on constructor promotion property.
     *
     * @return bool
     */
    public function isProtected()
    {
        return ($this->getModifiers() & State::IS_PROTECTED) === State::IS_PROTECTED;
    }

    /**
     * Returns <b>true</b> if this node is marked as private, otherwise the
     * returned value will be <b>false</b>.
     *
     * Can happen only on constructor promotion property.
     *
     * @return bool
     */
    public function isPrivate()
    {
        return ($this->getModifiers() & State::IS_PRIVATE) === State::IS_PRIVATE;
    }

    public function __sleep()
    {
        return array_merge(array('modifiers'), parent::__sleep());
    }
}
