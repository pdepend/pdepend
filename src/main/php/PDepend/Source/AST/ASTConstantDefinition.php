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
 */

namespace PDepend\Source\AST;

use InvalidArgumentException;

/**
 * This class represents a single constant definition as it can occure in
 * class definition or interface declaration.
 *
 * <code>
 * class Foo
 * {
 * //  ------------------------
 *     const FOO = 42, BAR = 23;
 * //  ------------------------
 * }
 * </code>
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class ASTConstantDefinition extends AbstractASTNode
{
    /**
     * This method returns a OR combined integer of the declared modifiers for
     * this property.
     */
    public function getModifiers(): int
    {
        return $this->getMetadataInteger(5);
    }

    /**
     * This method sets a OR combined integer of the declared modifiers for this
     * node.
     *
     * This method will throw an exception when the value of given <b>$modifiers</b>
     * contains an invalid/unexpected modifier
     *
     * @param int $modifiers The declared modifiers for this node.
     * @throws InvalidArgumentException If the given modifier contains unexpected values.
     */
    public function setModifiers(int $modifiers): void
    {
        $expected = ~State::IS_PUBLIC
            & ~State::IS_PROTECTED
            & ~State::IS_PRIVATE
            & ~State::IS_FINAL;

        if (($expected & $modifiers) !== 0) {
            throw new InvalidArgumentException(
                'Invalid field modifiers given, allowed modifiers are ' .
                'IS_PUBLIC, IS_PROTECTED, IS_PRIVATE and IS_FINAL.',
            );
        }

        $this->setMetadataInteger(5, $modifiers);
    }

    /**
     * Returns <b>true</b> if this node is marked as public, otherwise the
     * returned value will be <b>false</b>.
     */
    public function isPublic(): bool
    {
        return (($this->getModifiers() & State::IS_PUBLIC) === State::IS_PUBLIC);
    }

    /**
     * Returns <b>true</b> if this node is marked as protected, otherwise the
     * returned value will be <b>false</b>.
     */
    public function isProtected(): bool
    {
        return (($this->getModifiers() & State::IS_PROTECTED) === State::IS_PROTECTED);
    }

    /**
     * Returns <b>true</b> if this node is marked as private, otherwise the
     * returned value will be <b>false</b>.
     */
    public function isPrivate(): bool
    {
        return (($this->getModifiers() & State::IS_PRIVATE) === State::IS_PRIVATE);
    }

    /**
     * Returns the total number of the used property bag.
     *
     * @see    ASTNode#getMetadataSize()
     * @since  0.10.4
     */
    protected function getMetadataSize(): int
    {
        return 6;
    }
}
