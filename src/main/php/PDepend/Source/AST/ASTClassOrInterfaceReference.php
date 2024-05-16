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
 * @since 0.9.5
 */

namespace PDepend\Source\AST;

use PDepend\Source\Builder\BuilderContext;

/**
 * This class is used as a placeholder for unknown classes or interfaces. It
 * will resolve the concrete type instance on demand.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 0.9.5
 */
class ASTClassOrInterfaceReference extends ASTType
{
    /** The global AST builder context. */
    protected ?BuilderContext $context = null;

    /**
     * An already loaded type instance.
     *
     * @var AbstractASTClassOrInterface|null
     */
    protected $typeInstance = null;

    /**
     * Constructs a new type holder instance.
     *
     * @param string $qualifiedName
     */
    public function __construct(BuilderContext $context, $qualifiedName)
    {
        parent::__construct($qualifiedName);

        $this->context = $context;
    }

    /**
     * Magic method which returns the names of all those properties that should
     * be cached for this node instance.
     *
     * @return array
     * @since  0.10.0
     */
    public function __sleep()
    {
        return ['context', ...parent::__sleep()];
    }

    /**
     * Returns the concrete type instance associated with with this placeholder.
     *
     * @return AbstractASTClassOrInterface
     */
    public function getType()
    {
        if ($this->typeInstance === null) {
            $this->typeInstance = $this->context->getClassOrInterface(
                $this->getImage(),
            );
        }

        return $this->typeInstance;
    }
}
