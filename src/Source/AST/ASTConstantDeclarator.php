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

/**
 * This class represents a single constant declarator within a constant
 * definition.
 *
 * <code>
 * class Foo
 * {
 *     //    --------
 *     const BAR = 42;
 *     //    --------
 * }
 * </code>
 *
 * Or in a comma separated constant defintion:
 *
 * <code>
 * class Foo
 * {
 *     //    --------
 *     const BAR = 42,
 *     //    --------
 *
 *     //    --------------
 *     const BAZ = 'Foobar',
 *     //    --------------
 *
 *     //    ----------
 *     const FOO = 3.14;
 *     //    ----------
 * }
 * </code>
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class ASTConstantDeclarator extends AbstractASTNode
{
    /** The type of the constant if explicitly specified, <b>null</b> else. */
    protected ?ASTType $type = null;

    /** The initial declaration value for this node or <b>null</b>. */
    protected ?ASTValue $value = null;

    /**
     * Magic sleep method that returns an array with those property names that
     * should be cached for this node instance.
     *
     * @return list<string>
     */
    public function __sleep(): array
    {
        return ['value', ...parent::__sleep()];
    }

    /**
     * Returns the explicitly specified type of the constant.
     */
    public function getType(): ?ASTType
    {
        return $this->type;
    }

    /**
     * Set the explicitly specified type of the constant.
     */
    public function setType(?ASTType $type = null): void
    {
        $this->type = $type;
    }

    /**
     * Returns the initial declaration value for this node.
     */
    public function getValue(): ?ASTValue
    {
        return $this->value;
    }

    /**
     * Sets the declared default value for this constant node.
     */
    public function setValue(?ASTValue $value = null): void
    {
        $this->value = $value;
    }
}
