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

use PDepend\Source\Builder\BuilderContext;

/**
 * Represents a php function node.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class ASTFunction extends AbstractASTCallable
{
    /**
     * The currently used builder context.
     *
     * @since 0.10.0
     */
    protected BuilderContext $context;

    /**
     * The name of the parent namespace for this function. We use this property
     * to restore the parent namespace while we unserialize a cached object tree.
     */
    protected ?string $namespaceName = null;

    /**
     * The parent namespace for this function.
     *
     * @since 0.10.0
     */
    private ?ASTNamespace $namespace = null;

    /**
     * The magic sleep method will be called by the PHP engine when this class
     * gets serialized. It returns an array with those properties that should be
     * cached for all function instances.
     *
     * @return list<string>
     * @since  0.10.0
     */
    public function __sleep(): array
    {
        return ['context', 'namespaceName', ...parent::__sleep()];
    }

    /**
     * The magic wakeup method will be called by PHP's runtime environment when
     * a serialized instance of this class was unserialized. This implementation
     * of the wakeup method will register this object in the the global function
     * context.
     *
     * @since  0.10.0
     */
    public function __wakeup(): void
    {
        $this->context->registerFunction($this);
    }

    /**
     * Sets the currently active builder context.
     *
     * @param BuilderContext $context Current builder context.
     * @return $this
     * @since  0.10.0
     */
    public function setContext(BuilderContext $context): self
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Returns the parent namespace for this function.
     */
    public function getNamespace(): ?ASTNamespace
    {
        return $this->namespace;
    }

    /**
     * Sets the parent namespace for this function.
     */
    public function setNamespace(ASTNamespace $namespace): void
    {
        $this->namespaceName = $namespace->getImage();
        $this->namespace = $namespace;
    }

    /**
     * Resets the namespace associated with this function node.
     *
     * @since  0.10.2
     */
    public function unsetNamespace(): void
    {
        $this->namespaceName = null;
        $this->namespace = null;
    }

    /**
     * Returns the name of the parent namespace/package or <b>NULL</b> when this
     * function does not belong to a namespace.
     *
     * @since  0.10.0
     */
    public function getNamespaceName(): ?string
    {
        return $this->namespaceName;
    }
}
