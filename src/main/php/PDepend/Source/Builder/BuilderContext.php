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

namespace PDepend\Source\Builder;

use PDepend\Source\AST\AbstractASTClassOrInterface;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTEnum;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTTrait;

/**
 * Base interface for a builder context.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 0.10.0
 */
interface BuilderContext
{
    /**
     * This method can be used to register an existing function in the current
     * application context.
     */
    public function registerFunction(ASTFunction $function): void;

    /**
     * This method can be used to register an existing trait in the current
     * class context.
     *
     * @since  1.0.0
     */
    public function registerTrait(ASTTrait $trait): void;

    /**
     * This method can be used to register an existing class in the current
     * class context.
     */
    public function registerClass(ASTClass $class): void;

    /**
     * This method can be used to register an existing enum in the current
     * context.
     */
    public function registerEnum(ASTEnum $class): void;

    /**
     * This method can be used to register an existing interface in the current
     * class context.
     */
    public function registerInterface(ASTInterface $interface): void;

    /**
     * Returns the trait instance for the given qualified name.
     *
     * @param string $qualifiedName Full qualified trait name.
     * @since  1.0.0
     */
    public function getTrait(string $qualifiedName): ASTTrait;

    /**
     * Returns the class instance for the given qualified name.
     */
    public function getClass(string $qualifiedName): ASTClass|ASTEnum;

    /**
     * Returns a class or an interface instance for the given qualified name.
     */
    public function getClassOrInterface(string $qualifiedName): AbstractASTClassOrInterface;
}
