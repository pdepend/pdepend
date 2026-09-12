<?php

/**
 * This file is part of PDepend.
 *
 * PHP Version 8
 *
 * Copyright (c) 2008-2026 Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2026 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend;

use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTCompilationUnit;
use PDepend\Source\AST\ASTEnum;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTTrait;
use PDepend\Source\ASTVisitor\AbstractASTVisitor;

/**
 * Visits every AST node that stores its raw tokens in a {@link * \PDepend\Util\Cache\CacheDriver} instead of as a plain property.
 *
 * A worker process parses files into its own cache; that cache does not
 * survive being serialized back to the parent (the default in-memory driver
 * is explicitly process-local, see MemoryCacheDriver), so token data has to
 * be ferried across the process boundary explicitly. This visitor is used
 * on both ends: to collect the token data in the worker, and to re-attach it
 * to a working cache in the parent.
 */
class TokenExchangeVisitor extends AbstractASTVisitor
{
    /** @var callable(ASTClass|ASTCompilationUnit|ASTEnum|ASTFunction|ASTInterface|ASTMethod|ASTTrait): void */
    private $callback;

    /**
     * @param callable(ASTClass|ASTCompilationUnit|ASTEnum|ASTFunction|ASTInterface|ASTMethod|ASTTrait): void $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function visitClass(ASTClass $class): void
    {
        ($this->callback)($class);
        parent::visitClass($class);
    }

    public function visitEnum(ASTEnum $enum): void
    {
        ($this->callback)($enum);
        parent::visitEnum($enum);
    }

    public function visitTrait(ASTTrait $trait): void
    {
        ($this->callback)($trait);
        parent::visitTrait($trait);
    }

    public function visitInterface(ASTInterface $interface): void
    {
        ($this->callback)($interface);
        parent::visitInterface($interface);
    }

    public function visitFunction(ASTFunction $function): void
    {
        ($this->callback)($function);
        parent::visitFunction($function);
    }

    public function visitMethod(ASTMethod $method): void
    {
        ($this->callback)($method);
        parent::visitMethod($method);
    }

    public function visitCompilationUnit(ASTCompilationUnit $compilationUnit): void
    {
        ($this->callback)($compilationUnit);
        parent::visitCompilationUnit($compilationUnit);
    }
}
