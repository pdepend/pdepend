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
 * @since 0.10.0
 */

namespace PDepend\Source\Builder\BuilderContext;

use PDepend\Source\AST\AbstractASTClassOrInterface;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTEnum;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTTrait;
use PDepend\Source\Builder\Builder;
use PDepend\Source\Builder\BuilderContext;

/**
 * This class provides the default implementation of the builder context.
 *
 * This class utilizes the simple <b>static</b> language construct to share the
 * context instance between all using objects.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 0.10.0
 */
class GlobalBuilderContext implements BuilderContext
{
    /**
     * The currently used ast builder.
     *
     * @var Builder<mixed>
     */
    protected static Builder $builder;

    /**
     * Constructs a new builder context instance.
     *
     * @param Builder<mixed> $builder The currently used ast builder.
     */
    public function __construct(Builder $builder)
    {
        self::$builder = $builder;
    }

    /**
     * This method can be used to register an existing function in the current
     * application context.
     */
    public function registerFunction(ASTFunction $function): void
    {
        self::$builder->restoreFunction($function);
    }

    /**
     * This method can be used to register an existing trait in the current
     * class context.
     *
     * @since  1.0.0
     */
    public function registerTrait(ASTTrait $trait): void
    {
        self::$builder->restoreTrait($trait);
    }

    /**
     * This method can be used to register an existing class in the current
     * class context.
     *
     * @param ASTClass $class The class instance.
     */
    public function registerClass(ASTClass $class): void
    {
        self::$builder->restoreClass($class);
    }

    /**
     * This method can be used to register an existing enum in the current
     * context.
     *
     * @param ASTEnum $class The enum instance.
     */
    public function registerEnum(ASTEnum $class): void
    {
        self::$builder->restoreEnum($class);
    }

    /**
     * This method can be used to register an existing interface in the current
     * class context.
     */
    public function registerInterface(ASTInterface $interface): void
    {
        self::$builder->restoreInterface($interface);
    }

    /**
     * Returns the trait instance for the given qualified name.
     *
     * @param string $qualifiedName
     * @return ASTTrait
     * @since  1.0.0
     */
    public function getTrait($qualifiedName)
    {
        return $this->getBuilder()->getTrait($qualifiedName);
    }

    /**
     * Returns the class instance for the given qualified name.
     *
     * @param string $qualifiedName
     * @return ASTClass
     */
    public function getClass($qualifiedName)
    {
        return $this->getBuilder()->getClass($qualifiedName);
    }

    /**
     * Returns a class or an interface instance for the given qualified name.
     *
     * @param string $qualifiedName
     * @return AbstractASTClassOrInterface
     */
    public function getClassOrInterface($qualifiedName)
    {
        return $this->getBuilder()->getClassOrInterface($qualifiedName);
    }

    /**
     * Returns the currently used builder instance.
     *
     * @return Builder<mixed>
     */
    protected function getBuilder()
    {
        return self::$builder;
    }
}
