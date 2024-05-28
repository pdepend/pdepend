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

use ArrayIterator;
use Iterator;
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
 * This abstract visitor implementation provides a default traversal algorithm
 * that can be used for custom visitors.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
abstract class AbstractASTVisitor implements ASTVisitor
{
    /**
     * List of all registered listeners.
     *
     * @var ASTVisitListener[]
     */
    private array $listeners = [];

    /**
     * Returns an iterator with all registered visit listeners.
     *
     * @return Iterator<ASTVisitListener>
     */
    public function getVisitListeners(): Iterator
    {
        return new ArrayIterator($this->listeners);
    }

    /**
     * Adds a new listener to this node visitor.
     */
    public function addVisitListener(ASTVisitListener $listener): void
    {
        if (!in_array($listener, $this->listeners, true)) {
            $this->listeners[] = $listener;
        }
    }

    /**
     * Visits a class node.
     */
    public function visitClass(ASTClass $class): void
    {
        $this->fireStartClass($class);

        $unit = $class->getCompilationUnit();
        if ($unit) {
            $this->dispatch($unit);
        }

        foreach ($class->getProperties() as $property) {
            $this->dispatch($property);
        }
        foreach ($class->getMethods() as $method) {
            $this->dispatch($method);
        }

        $this->fireEndClass($class);
    }

    /**
     * Visits a class node.
     */
    public function visitEnum(ASTEnum $enum): void
    {
        $this->fireStartEnum($enum);

        $unit = $enum->getCompilationUnit();
        if ($unit) {
            $this->dispatch($unit);
        }

        foreach ($enum->getProperties() as $property) {
            $this->dispatch($property);
        }
        foreach ($enum->getMethods() as $method) {
            $this->dispatch($method);
        }

        $this->fireEndEnum($enum);
    }

    /**
     * Visits a trait node.
     *
     * @since  1.0.0
     */
    public function visitTrait(ASTTrait $trait): void
    {
        $this->fireStartTrait($trait);

        $unit = $trait->getCompilationUnit();
        if ($unit) {
            $this->dispatch($unit);
        }

        foreach ($trait->getMethods() as $method) {
            $this->dispatch($method);
        }

        $this->fireEndTrait($trait);
    }

    /**
     * Visits a file node.
     */
    public function visitCompilationUnit(ASTCompilationUnit $compilationUnit): void
    {
        $this->fireStartFile($compilationUnit);
        $this->fireEndFile($compilationUnit);
    }

    /**
     * Visits a function node.
     */
    public function visitFunction(ASTFunction $function): void
    {
        $this->fireStartFunction($function);

        $unit = $function->getCompilationUnit();
        if ($unit) {
            $this->dispatch($unit);
        }

        foreach ($function->getParameters() as $parameter) {
            $this->dispatch($parameter);
        }

        $this->fireEndFunction($function);
    }

    /**
     * Visits a code interface object.
     */
    public function visitInterface(ASTInterface $interface): void
    {
        $this->fireStartInterface($interface);

        $unit = $interface->getCompilationUnit();
        if ($unit) {
            $this->dispatch($unit);
        }

        foreach ($interface->getMethods() as $method) {
            $this->dispatch($method);
        }

        $this->fireEndInterface($interface);
    }

    /**
     * Visits a method node.
     */
    public function visitMethod(ASTMethod $method): void
    {
        $this->fireStartMethod($method);

        foreach ($method->getParameters() as $parameter) {
            $this->dispatch($parameter);
        }

        $this->fireEndMethod($method);
    }

    /**
     * Visits a namespace node.
     */
    public function visitNamespace(ASTNamespace $namespace): void
    {
        $this->fireStartNamespace($namespace);

        foreach ($namespace->getClasses() as $class) {
            $this->dispatch($class);
        }
        foreach ($namespace->getInterfaces() as $interface) {
            $this->dispatch($interface);
        }
        foreach ($namespace->getTraits() as $trait) {
            $this->dispatch($trait);
        }
        foreach ($namespace->getEnums() as $enum) {
            $this->dispatch($enum);
        }
        foreach ($namespace->getFunctions() as $function) {
            $this->dispatch($function);
        }

        $this->fireEndNamespace($namespace);
    }

    /**
     * Visits a parameter node.
     */
    public function visitParameter(ASTParameter $parameter): void
    {
        $this->fireStartParameter($parameter);
        $this->fireEndParameter($parameter);
    }

    /**
     * Visits a property node.
     */
    public function visitProperty(ASTProperty $property): void
    {
        $this->fireStartProperty($property);
        $this->fireEndProperty($property);
    }

    public function visit(ASTNode $node): void
    {
        foreach ($node->getChildren() as $child) {
            $this->dispatch($child);
        }
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

    /**
     * Sends a start class event.
     */
    protected function fireStartClass(ASTClass $class): void
    {
        foreach ($this->listeners as $listener) {
            $listener->startVisitClass($class);
        }
    }

    /**
     * Sends an end class event.
     */
    protected function fireEndClass(ASTClass $class): void
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitClass($class);
        }
    }

    /**
     * Sends a start enum event.
     */
    protected function fireStartEnum(ASTEnum $enum): void
    {
        foreach ($this->listeners as $listener) {
            $listener->startVisitEnum($enum);
        }
    }

    /**
     * Sends an end enum event.
     */
    protected function fireEndEnum(ASTEnum $enum): void
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitEnum($enum);
        }
    }

    /**
     * Sends a start trait event.
     */
    protected function fireStartTrait(ASTTrait $trait): void
    {
        foreach ($this->listeners as $listener) {
            $listener->startVisitTrait($trait);
        }
    }

    /**
     * Sends an end trait event.
     */
    protected function fireEndTrait(ASTTrait $trait): void
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitTrait($trait);
        }
    }

    /**
     * Sends a start file event.
     */
    protected function fireStartFile(ASTCompilationUnit $compilationUnit): void
    {
        foreach ($this->listeners as $listener) {
            $listener->startVisitFile($compilationUnit);
        }
    }

    /**
     * Sends an end file event.
     */
    protected function fireEndFile(ASTCompilationUnit $compilationUnit): void
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitFile($compilationUnit);
        }
    }

    /**
     * Sends a start function event.
     */
    protected function fireStartFunction(ASTFunction $function): void
    {
        foreach ($this->listeners as $listener) {
            $listener->startVisitFunction($function);
        }
    }

    /**
     * Sends an end function event.
     */
    protected function fireEndFunction(ASTFunction $function): void
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitFunction($function);
        }
    }

    /**
     * Sends a start interface event.
     */
    protected function fireStartInterface(ASTInterface $interface): void
    {
        foreach ($this->listeners as $listener) {
            $listener->startVisitInterface($interface);
        }
    }

    /**
     * Sends an end interface event.
     */
    protected function fireEndInterface(ASTInterface $interface): void
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitInterface($interface);
        }
    }

    /**
     * Sends a start method event.
     */
    protected function fireStartMethod(ASTMethod $method): void
    {
        foreach ($this->listeners as $listener) {
            $listener->startVisitMethod($method);
        }
    }

    /**
     * Sends an end method event.
     */
    protected function fireEndMethod(ASTMethod $method): void
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitMethod($method);
        }
    }

    /**
     * Sends a start namespace event.
     */
    protected function fireStartNamespace(ASTNamespace $namespace): void
    {
        foreach ($this->listeners as $listener) {
            $listener->startVisitNamespace($namespace);
        }
    }

    /**
     * Sends an end namespace event.
     */
    protected function fireEndNamespace(ASTNamespace $namespace): void
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitNamespace($namespace);
        }
    }

    /**
     * Sends a start parameter event.
     */
    protected function fireStartParameter(ASTParameter $parameter): void
    {
        foreach ($this->listeners as $listener) {
            $listener->startVisitParameter($parameter);
        }
    }

    /**
     * Sends a end parameter event.
     */
    protected function fireEndParameter(ASTParameter $parameter): void
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitParameter($parameter);
        }
    }

    /**
     * Sends a start property event.
     */
    protected function fireStartProperty(ASTProperty $property): void
    {
        foreach ($this->listeners as $listener) {
            $listener->startVisitProperty($property);
        }
    }

    /**
     * Sends an end property event.
     */
    protected function fireEndProperty(ASTProperty $property): void
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitProperty($property);
        }
    }
}
