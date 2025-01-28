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
 * Represents a php method node.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class ASTMethod extends AbstractASTCallable
{
    protected ?AbstractASTClassOrInterface $parentClass = null;

    /** Defined modifiers for this property node. */
    protected int $modifiers = 0;

    /**
     * The magic sleep method will be called by the PHP engine when this class
     * gets serialized. It returns an array with those properties that should be
     * cached for method instances.
     *
     * @return list<string>
     * @since  0.10.0
     */
    public function __sleep(): array
    {
        return ['modifiers', ...parent::__sleep()];
    }

    /**
     * This method returns a OR combined integer of the declared modifiers for
     * this method.
     *
     * @since  1.0.0
     */
    public function getModifiers(): int
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
     * @throws InvalidArgumentException If the given modifier contains unexpected values.
     * @since  0.9.4
     */
    public function setModifiers(int $modifiers): void
    {
        $expected = ~State::IS_PUBLIC
                  & ~State::IS_PROTECTED
                  & ~State::IS_PRIVATE
                  & ~State::IS_STATIC
                  & ~State::IS_ABSTRACT
                  & ~State::IS_FINAL;

        if (($expected & $modifiers) !== 0) {
            throw new InvalidArgumentException('Invalid method modifier given.');
        }

        $this->modifiers = $modifiers;
    }

    /**
     * Returns <b>true</b> if this is an abstract method.
     */
    public function isAbstract(): bool
    {
        return (($this->modifiers & State::IS_ABSTRACT) === State::IS_ABSTRACT);
    }

    /**
     * Returns <b>true</b> if this node is marked as public, otherwise the
     * returned value will be <b>false</b>.
     */
    public function isPublic(): bool
    {
        return (($this->modifiers & State::IS_PUBLIC) === State::IS_PUBLIC);
    }

    /**
     * Returns <b>true</b> if this node is marked as protected, otherwise the
     * returned value will be <b>false</b>.
     */
    public function isProtected(): bool
    {
        return (($this->modifiers & State::IS_PROTECTED) === State::IS_PROTECTED);
    }

    /**
     * Returns <b>true</b> if this node is marked as private, otherwise the
     * returned value will be <b>false</b>.
     */
    public function isPrivate(): bool
    {
        return (($this->modifiers & State::IS_PRIVATE) === State::IS_PRIVATE);
    }

    /**
     * Returns <b>true</b> when this node is declared as static, otherwise the
     * returned value will be <b>false</b>.
     */
    public function isStatic(): bool
    {
        return (($this->modifiers & State::IS_STATIC) === State::IS_STATIC);
    }

    /**
     * Returns <b>true</b> when this node is declared as final, otherwise the
     * returned value will be <b>false</b>.
     */
    public function isFinal(): bool
    {
        return (($this->modifiers & State::IS_FINAL) === State::IS_FINAL);
    }

    /**
     * Returns the parent type object or <b>null</b>
     */
    public function getParent(): ?AbstractASTClassOrInterface
    {
        return $this->parentClass;
    }

    /**
     * Sets the parent type object.
     *
     * @param AbstractASTClassOrInterface|null $parent
     */
    public function setParent(?ASTNode $parent): void
    {
        $this->parentClass = $parent;
    }

    /**
     * Returns the source file where this method was declared.
     *
     * @throws ASTCompilationUnitNotFoundException When no parent was set.
     * @since  0.10.0
     */
    public function getCompilationUnit(): ?ASTCompilationUnit
    {
        if ($this->parentClass === null) {
            throw new ASTCompilationUnitNotFoundException($this);
        }

        return $this->parentClass->getCompilationUnit();
    }
}
