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

use PDepend\Source\AST\AbstractASTArtifact;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTCompilationUnit;
use PDepend\Source\AST\ASTEnum;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\AST\ASTParameter;
use PDepend\Source\AST\ASTProperty;
use PDepend\Source\AST\ASTTrait;

/**
 * This abstract class provides a default implementation of the node visitor
 * listener.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
abstract class AbstractASTVisitListener implements ASTVisitListener
{
    /**
     * Is called when the visitor starts a new class instance.
     */
    public function startVisitClass(ASTClass $class): void
    {
        $this->startVisitNode($class);
    }

    /**
     * Is called when the visitor ends with a class instance.
     */
    public function endVisitClass(ASTClass $class): void
    {
        $this->endVisitNode($class);
    }

    /**
     * Is called when the visitor starts a new enum instance.
     */
    public function startVisitEnum(ASTEnum $enum): void
    {
        $this->startVisitNode($enum);
    }

    /**
     * Is called when the visitor ends with an enum instance.
     */
    public function endVisitEnum(ASTEnum $enum): void
    {
        $this->endVisitNode($enum);
    }

    /**
     * Is called when the visitor starts a new trait instance.
     *
     * @since  1.0.0
     */
    public function startVisitTrait(ASTTrait $trait): void
    {
        $this->startVisitNode($trait);
    }

    /**
     * Is called when the visitor ends with a trait instance.
     *
     * @since  1.0.0
     */
    public function endVisitTrait(ASTTrait $trait): void
    {
        $this->endVisitNode($trait);
    }

    /**
     * Is called when the visitor starts a new file instance.
     */
    public function startVisitFile(ASTCompilationUnit $compilationUnit): void
    {
        $this->startVisitNode($compilationUnit);
    }

    /**
     * Is called when the visitor ends with a file instance.
     */
    public function endVisitFile(ASTCompilationUnit $compilationUnit): void
    {
        $this->endVisitNode($compilationUnit);
    }

    /**
     * Is called when the visitor starts a new function instance.
     */
    public function startVisitFunction(ASTFunction $function): void
    {
        $this->startVisitNode($function);
    }

    /**
     * Is called when the visitor ends with a function instance.
     */
    public function endVisitFunction(ASTFunction $function): void
    {
        $this->endVisitNode($function);
    }

    /**
     * Is called when the visitor starts a new interface instance.
     */
    public function startVisitInterface(ASTInterface $interface): void
    {
        $this->startVisitNode($interface);
    }

    /**
     * Is called when the visitor ends with an interface instance.
     */
    public function endVisitInterface(ASTInterface $interface): void
    {
        $this->endVisitNode($interface);
    }

    /**
     * Is called when the visitor starts a new method instance.
     */
    public function startVisitMethod(ASTMethod $method): void
    {
        $this->startVisitNode($method);
    }

    /**
     * Is called when the visitor ends with a method instance.
     */
    public function endVisitMethod(ASTMethod $method): void
    {
        $this->endVisitNode($method);
    }

    /**
     * Is called when the visitor starts a new namespace instance.
     */
    public function startVisitNamespace(ASTNamespace $namespace): void
    {
        $this->startVisitNode($namespace);
    }

    /**
     * Is called when the visitor ends with a namespace instance.
     */
    public function endVisitNamespace(ASTNamespace $namespace): void
    {
        $this->endVisitNode($namespace);
    }

    /**
     * Is called when the visitor starts a new parameter instance.
     */
    public function startVisitParameter(ASTParameter $parameter): void
    {
        $this->startVisitNode($parameter);
    }

    /**
     * Is called when the visitor ends with a parameter instance.
     */
    public function endVisitParameter(ASTParameter $parameter): void
    {
        $this->endVisitNode($parameter);
    }

    /**
     * Is called when the visitor starts a new property instance.
     */
    public function startVisitProperty(ASTProperty $property): void
    {
        $this->startVisitNode($property);
    }

    /**
     * Is called when the visitor ends with a property instance.
     */
    public function endVisitProperty(ASTProperty $property): void
    {
        $this->endVisitNode($property);
    }

    /**
     * Generic notification method that is called for every node start.
     */
    protected function startVisitNode(AbstractASTArtifact $node): void
    {
    }

    /**
     * Generic notification method that is called when the node processing ends.
     */
    protected function endVisitNode(AbstractASTArtifact $node): void
    {
    }
}
