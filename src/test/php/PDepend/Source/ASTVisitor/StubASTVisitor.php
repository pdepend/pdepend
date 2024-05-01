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

namespace PDepend\Source\ASTVisitor;

use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTCompilationUnit;
use PDepend\Source\AST\ASTEnum;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\AST\ASTParameter;
use PDepend\Source\AST\ASTProperty;
use PDepend\Source\AST\ASTTrait;

/**
 * Simple test node visitor implementation.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class StubASTVisitor implements ASTVisitor
{
    /** The last visited class instance. */
    public ASTClass $class;

    /** The last visited class instance. */
    public ASTEnum $enum;

    /**
     * The last visited trait instance.
     *
     * @since 1.0.0
     */
    public ASTTrait $trait;

    /** The last visited interface instance. */
    public ASTInterface $interface;

    /** The last visited method instance. */
    public ASTMethod $method;

    /** The last visited package instance. */
    public ASTNamespace $namespace;

    /** The last visited parameter instance. */
    public ASTParameter $parameter;

    /** The last visited property instance. */
    public ASTProperty $property;

    /** The last visited function instance. */
    public ASTFunction $function;

    /**
     * Adds a new listener to this node visitor.
     */
    public function addVisitListener(ASTVisitListener $listener): void
    {
    }

    /**
     * Visits a class node.
     */
    public function visitClass(ASTClass $class): void
    {
        $this->class = $class;
    }

    /**
     * Visits an enum node.
     */
    public function visitEnum(ASTEnum $enum): void
    {
        $this->enum = $enum;
    }

    /**
     * Visits a trait node.
     *
     * @since 1.0.0
     */
    public function visitTrait(ASTTrait $trait): void
    {
        $this->trait = $trait;
    }

    /**
     * Visits a code interface object.
     */
    public function visitInterface(ASTInterface $interface): void
    {
        $this->interface = $interface;
    }

    /**
     * Visits a method node.
     */
    public function visitMethod(ASTMethod $method): void
    {
        $this->method = $method;
    }

    /**
     * Visits a package node.
     *
     * @param ASTNamespace $namespace The package class node.
     */
    public function visitNamespace(ASTNamespace $namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     * Visits a parameter node.
     */
    public function visitParameter(ASTParameter $parameter): void
    {
        $this->parameter = $parameter;
    }

    /**
     * Visits a property node.
     */
    public function visitProperty(ASTProperty $property): void
    {
        $this->property = $property;
    }

    /**
     * Visits a function node.
     */
    public function visitFunction(ASTFunction $function): void
    {
        $this->function = $function;
    }

    /**
     * Visits a file node.
     */
    public function visitCompilationUnit(ASTCompilationUnit $compilationUnit): void
    {
    }

    public function visit(ASTNode $node): void
    {
    }

    public function dispatch(ASTNode $node): void
    {
        match ($node::class) {
            ASTClass::class => $this->visitClass($node),
            ASTCompilationUnit::class => $this->visitCompilationUnit($node),
            ASTEnum::class => $this->visitEnum($node),
            ASTFunction::class => $this->visitFunction($node),
            ASTInterface::class => $this->visitInterface($node),
            ASTMethod::class => $this->visitMethod($node),
            ASTNamespace::class => $this->visitNamespace($node),
            ASTParameter::class => $this->visitParameter($node),
            ASTProperty::class => $this->visitProperty($node),
            ASTTrait::class => $this->visitTrait($node),
            default => $this->visit($node),
        };
    }
}
