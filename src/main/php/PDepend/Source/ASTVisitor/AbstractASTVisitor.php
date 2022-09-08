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
use PDepend\Source\AST\AbstractASTClassOrInterface;
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
use RuntimeException;

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
    private $listeners = array();

    /**
     * Returns an iterator with all registered visit listeners.
     *
     * @return Iterator<ASTVisitListener>
     */
    public function getVisitListeners()
    {
        return new ArrayIterator($this->listeners);
    }

    /**
     * Adds a new listener to this node visitor.
     *
     * @return void
     */
    public function addVisitListener(ASTVisitListener $listener)
    {
        if (in_array($listener, $this->listeners, true) === false) {
            $this->listeners[] = $listener;
        }
    }

    /**
     * Visits a class node.
     *
     * @return void
     */
    public function visitClass(ASTClass $class)
    {
        $this->fireStartClass($class);

        $class->getCompilationUnit()->accept($this);

        foreach ($class->getProperties() as $property) {
            $property->accept($this);
        }
        foreach ($class->getMethods() as $method) {
            $method->accept($this);
        }

        $this->fireEndClass($class);
    }

    /**
     * Visits a class node.
     *
     * @return void
     */
    public function visitEnum(ASTEnum $enum)
    {
        $this->fireStartEnum($enum);

        $enum->getCompilationUnit()->accept($this);

        foreach ($enum->getProperties() as $property) {
            $property->accept($this);
        }
        foreach ($enum->getMethods() as $method) {
            $method->accept($this);
        }

        $this->fireEndEnum($enum);
    }

    /**
     * Visits a trait node.
     *
     * @return void
     *
     * @since  1.0.0
     */
    public function visitTrait(ASTTrait $trait)
    {
        $this->fireStartTrait($trait);

        $trait->getCompilationUnit()->accept($this);

        foreach ($trait->getMethods() as $method) {
            $method->accept($this);
        }

        $this->fireEndTrait($trait);
    }

    /**
     * Visits a file node.
     *
     * @return void
     */
    public function visitCompilationUnit(ASTCompilationUnit $compilationUnit)
    {
        $this->fireStartFile($compilationUnit);
        $this->fireEndFile($compilationUnit);
    }

    /**
     * Visits a function node.
     *
     * @return void
     */
    public function visitFunction(ASTFunction $function)
    {
        $this->fireStartFunction($function);

        $function->getCompilationUnit()->accept($this);

        foreach ($function->getParameters() as $parameter) {
            $parameter->accept($this);
        }

        $this->fireEndFunction($function);
    }

    /**
     * Visits a code interface object.
     *
     * @return void
     */
    public function visitInterface(ASTInterface $interface)
    {
        $this->fireStartInterface($interface);

        $interface->getCompilationUnit()->accept($this);

        foreach ($interface->getMethods() as $method) {
            $method->accept($this);
        }

        $this->fireEndInterface($interface);
    }

    /**
     * Visits a method node.
     *
     * @return void
     */
    public function visitMethod(ASTMethod $method)
    {
        $this->fireStartMethod($method);

        foreach ($method->getParameters() as $parameter) {
            $parameter->accept($this);
        }

        $this->fireEndMethod($method);
    }

    /**
     * Visits a namespace node.
     *
     * @return void
     */
    public function visitNamespace(ASTNamespace $namespace)
    {
        $this->fireStartNamespace($namespace);

        foreach ($namespace->getClasses() as $class) {
            $class->accept($this);
        }
        foreach ($namespace->getInterfaces() as $interface) {
            $interface->accept($this);
        }
        foreach ($namespace->getTraits() as $trait) {
            $trait->accept($this);
        }
        foreach ($namespace->getEnums() as $enum) {
            $enum->accept($this);
        }
        foreach ($namespace->getFunctions() as $function) {
            $function->accept($this);
        }

        $this->fireEndNamespace($namespace);
    }

    /**
     * Visits a parameter node.
     *
     * @return void
     */
    public function visitParameter(ASTParameter $parameter)
    {
        $this->fireStartParameter($parameter);
        $this->fireEndParameter($parameter);
    }

    /**
     * Visits a property node.
     *
     * @return void
     */
    public function visitProperty(ASTProperty $property)
    {
        $this->fireStartProperty($property);
        $this->fireEndProperty($property);
    }


    /**
     * Magic call method used to provide simplified visitor implementations.
     * With this method we can call <b>visit${NodeClassName}</b> on each node.
     *
     * <code>
     * $visitor->visitAllocationExpression($alloc);
     *
     * $visitor->visitStatement($stmt);
     * </code>
     *
     * All visit methods takes two argument. The first argument is the current
     * context ast node and the second argument is a data array or object that
     * is used to collect data.
     *
     * The return value of this method is the second input argument, modified
     * by the concrete visit method.
     *
     * @param string            $method Name of the called method.
     * @param array<int, mixed> $args   Array with method argument.
     *
     * @since  0.9.12
     */
    public function __call($method, $args)
    {
        if (!isset($args[1])) {
            throw new RuntimeException("No node to visit provided for $method.");
        }

        $value = $args[1];
        foreach ($args[0]->getChildren() as $child) {
            $value = $child->accept($this, $value);
        }
        return $value;
    }

    /**
     * Sends a start class event.
     *
     * @return void
     */
    protected function fireStartClass(ASTClass $class)
    {
        foreach ($this->listeners as $listener) {
            $listener->startVisitClass($class);
        }
    }

    /**
     * Sends an end class event.
     *
     * @return void
     */
    protected function fireEndClass(ASTClass $class)
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitClass($class);
        }
    }

    /**
     * Sends a start enum event.
     *
     * @return void
     */
    protected function fireStartEnum(ASTEnum $enum)
    {
        foreach ($this->listeners as $listener) {
            $listener->startVisitEnum($enum);
        }
    }

    /**
     * Sends an end enum event.
     *
     * @return void
     */
    protected function fireEndEnum(ASTEnum $enum)
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitEnum($enum);
        }
    }

    /**
     * Sends a start trait event.
     *
     * @return void
     */
    protected function fireStartTrait(ASTTrait $trait)
    {
        foreach ($this->listeners as $listener) {
            $listener->startVisitTrait($trait);
        }
    }

    /**
     * Sends an end trait event.
     *
     * @return void
     */
    protected function fireEndTrait(ASTTrait $trait)
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitTrait($trait);
        }
    }

    /**
     * Sends a start file event.
     *
     * @return void
     */
    protected function fireStartFile(ASTCompilationUnit $compilationUnit)
    {
        foreach ($this->listeners as $listener) {
            $listener->startVisitFile($compilationUnit);
        }
    }

    /**
     * Sends an end file event.
     *
     * @return void
     */
    protected function fireEndFile(ASTCompilationUnit $compilationUnit)
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitFile($compilationUnit);
        }
    }

    /**
     * Sends a start function event.
     *
     * @return void
     */
    protected function fireStartFunction(ASTFunction $function)
    {
        foreach ($this->listeners as $listener) {
            $listener->startVisitFunction($function);
        }
    }

    /**
     * Sends an end function event.
     *
     * @return void
     */
    protected function fireEndFunction(ASTFunction $function)
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitFunction($function);
        }
    }

    /**
     * Sends a start interface event.
     *
     * @return void
     */
    protected function fireStartInterface(ASTInterface $interface)
    {
        foreach ($this->listeners as $listener) {
            $listener->startVisitInterface($interface);
        }
    }

    /**
     * Sends an end interface event.
     *
     * @return void
     */
    protected function fireEndInterface(ASTInterface $interface)
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitInterface($interface);
        }
    }

    /**
     * Sends a start method event.
     *
     * @return void
     */
    protected function fireStartMethod(ASTMethod $method)
    {
        foreach ($this->listeners as $listener) {
            $listener->startVisitMethod($method);
        }
    }

    /**
     * Sends an end method event.
     *
     * @return void
     */
    protected function fireEndMethod(ASTMethod $method)
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitMethod($method);
        }
    }

    /**
     * Sends a start namespace event.
     *
     * @return void
     */
    protected function fireStartNamespace(ASTNamespace $namespace)
    {
        foreach ($this->listeners as $listener) {
            $listener->startVisitNamespace($namespace);
        }
    }

    /**
     * Sends an end namespace event.
     *
     * @return void
     */
    protected function fireEndNamespace(ASTNamespace $namespace)
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitNamespace($namespace);
        }
    }

    /**
     * Sends a start parameter event.
     *
     * @return void
     */
    protected function fireStartParameter(ASTParameter $parameter)
    {
        foreach ($this->listeners as $listener) {
            $listener->startVisitParameter($parameter);
        }
    }

    /**
     * Sends a end parameter event.
     *
     * @return void
     */
    protected function fireEndParameter(ASTParameter $parameter)
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitParameter($parameter);
        }
    }

    /**
     * Sends a start property event.
     *
     * @return void
     */
    protected function fireStartProperty(ASTProperty $property)
    {
        foreach ($this->listeners as $listener) {
            $listener->startVisitProperty($property);
        }
    }

    /**
     * Sends an end property event.
     *
     * @return void
     */
    protected function fireEndProperty(ASTProperty $property)
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitProperty($property);
        }
    }
}
