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
 * @since 0.9.6
 */

namespace PDepend\Source\AST;

use PDepend\Source\Builder\BuilderContext;
use RuntimeException;

/**
 * This is a special reference container that is used whenever the keyword
 * <b>self</b> is used to reference a class or interface.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 0.9.6
 */
class ASTSelfReference extends ASTClassOrInterfaceReference
{
    /** The source image of this node. */
    public const IMAGE = 'self';

    /**
     * The currently used builder context.
     *
     * @since 0.10.0
     */
    protected ?BuilderContext $context = null;

    /**
     * The full qualified class name, including the namespace or namespace name.
     *
     * @since 0.10.0
     * @todo  To reduce memory usage, move property into new metadata string
     */
    protected string $qualifiedName;

    /**
     * Constructs a new type holder instance.
     */
    public function __construct(BuilderContext $context, AbstractASTClassOrInterface $target)
    {
        $this->context = $context;
        $this->typeInstance = $target;
    }

    /**
     * The magic sleep method will be called by PHP's runtime environment right
     * before an instance of this class gets serialized. It should return an
     * array with those property names that should be serialized for this class.
     *
     * @return list<string>
     * @since  0.10.0
     */
    public function __sleep(): array
    {
        $this->qualifiedName = $this->getType()->getNamespaceName() . '\\' .
                               $this->getType()->getImage();

        return ['qualifiedName', ...parent::__sleep()];
    }

    /**
     * Returns the visual representation for this node type.
     *
     * @since  0.10.4
     */
    public function getImage(): string
    {
        return self::IMAGE;
    }

    /**
     * Returns the class or interface instance that this node instance represents.
     *
     * @throws RuntimeException
     * @since  0.10.0
     */
    public function getType(): AbstractASTClassOrInterface
    {
        if ($this->typeInstance === null) {
            if (!$this->context) {
                throw new RuntimeException('No context set');
            }
            $this->typeInstance = $this->context
                ->getClassOrInterface($this->qualifiedName);
        }

        return $this->typeInstance;
    }
}
