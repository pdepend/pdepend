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

use InvalidArgumentException;
use PDepend\Source\ASTVisitor\ASTVisitor;

/**
 * This class represents arguments as they are supplied to functions or
 * constructors invocations.
 *
 * <code>
 * //      ------------
 * Foo::bar($x, $y, $z);
 * //      ------------
 *
 * //       ------------
 * $foo->bar($x, $y, $z);
 * //       ------------
 * </code>
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @since 0.9.6
 */
class ASTArguments extends AbstractASTNode
{
    /**
     * This method will return true if the argument list is declared as foo(...)
     *
     * @return bool
     *
     * @since 2.11.0
     */
    public function isVariadicPlaceholder()
    {
        return $this->getMetadataBoolean(4);
    }

    /**
     * This method can be used to mark the argument list as variadic placeholder
     *
     * @return void
     * @since 2.11.0
     */
    public function setVariadicPlaceholder()
    {
        $this->setMetadataBoolean(4, true);
    }

    /**
     * Rather the given arguments list can still take one more argument.
     *
     * @return bool
     */
    public function acceptsMoreArguments()
    {
        return true;
    }

    /**
     * This method adds a new child node to this node instance.
     *
     * @return void
     */
    public function addChild(ASTNode $node)
    {
        if (!$this->acceptsMoreArguments()) {
            throw new InvalidArgumentException('No more arguments allowed.');
        }

        parent::addChild($node);
    }

    /**
     * Accept method of the visitor design pattern. This method will be called
     * by a visitor during tree traversal.
     *
     * @since 0.9.12
     */
    public function accept(ASTVisitor $visitor, $data = null)
    {
        return $visitor->visitArguments($this, $data);
    }
}
