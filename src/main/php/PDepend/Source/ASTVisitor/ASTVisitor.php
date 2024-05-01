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
 * Base interface for visitors that work on the generated node tree.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
interface ASTVisitor
{
    /**
     * Adds a new listener to this node visitor.
     */
    public function addVisitListener(ASTVisitListener $listener): void;

    /**
     * Visits a class node.
     */
    public function visitClass(ASTClass $class): void;

    /**
     * Visits an enum node.
     */
    public function visitEnum(ASTEnum $enum): void;

    /**
     * Visits a trait node.
     *
     * @since  1.0.0
     */
    public function visitTrait(ASTTrait $trait): void;

    /**
     * Visits a file node.
     */
    public function visitCompilationUnit(ASTCompilationUnit $compilationUnit): void;

    /**
     * Visits a function node.
     */
    public function visitFunction(ASTFunction $function): void;

    /**
     * Visits a code interface object.
     */
    public function visitInterface(ASTInterface $interface): void;

    /**
     * Visits a method node.
     */
    public function visitMethod(ASTMethod $method): void;

    /**
     * Visits a namespace node.
     */
    public function visitNamespace(ASTNamespace $namespace): void;

    /**
     * Visits a parameter node.
     */
    public function visitParameter(ASTParameter $parameter): void;

    /**
     * Visits a property node.
     */
    public function visitProperty(ASTProperty $property): void;

    /**
     * Visit child nodes of the given node.
     */
    public function visit(ASTNode $node): void;

    public function dispatch(ASTNode $node): void;
}
