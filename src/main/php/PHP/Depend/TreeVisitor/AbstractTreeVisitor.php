<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

namespace PHP\Depend\TreeVisitor;

use PHP\Depend\Source\AST\ASTClass;
use PHP\Depend\Source\AST\ASTCompilationUnit;
use PHP\Depend\Source\AST\ASTFunction;
use PHP\Depend\Source\AST\ASTInterface;
use PHP\Depend\Source\AST\ASTMethod;
use PHP\Depend\Source\AST\ASTNamespace;
use PHP\Depend\Source\AST\ASTParameter;
use PHP\Depend\Source\AST\ASTProperty;
use PHP\Depend\Source\AST\ASTTrait;

/**
 * This abstract visitor implementation provides a default traversal algorithm
 * that can be used for custom visitors.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
abstract class AbstractTreeVisitor implements TreeVisitor
{
    /**
     * List of all registered listeners.
     *
     * @var \PHP\Depend\TreeVisitor\TreeVisitListener
     */
    private $listeners = array();

    /**
     * Returns an iterator with all registered visit listeners.
     *
     * @return Iterator
     */
    public function getVisitListeners()
    {
        return new \ArrayIterator($this->listeners);
    }

    /**
     * Adds a new listener to this node visitor.
     *
     * @param \PHP\Depend\TreeVisitor\TreeVisitListener $listener
     * @return void
     */
    public function addVisitListener(TreeVisitListener $listener)
    {
        if (in_array($listener, $this->listeners, true) === false) {
            $this->listeners[] = $listener;
        }
    }

    /**
     * Visits a class node.
     *
     * @param ASTClass $class
     * @return void
     */
    public function visitClass(ASTClass $class)
    {
        $this->fireStartClass($class);

        $class->getSourceFile()->accept($this);

        foreach ($class->getProperties() as $property) {
            $property->accept($this);
        }
        foreach ($class->getMethods() as $method) {
            $method->accept($this);
        }

        $this->fireEndClass($class);
    }

    /**
     * Visits a trait node.
     *
     * @param \PHP\Depend\Source\AST\ASTTrait $trait
     * @return void
     * @since 1.0.0
     */
    public function visitTrait(ASTTrait $trait)
    {
        $this->fireStartTrait($trait);

        $trait->getSourceFile()->accept($this);

        foreach ($trait->getMethods() as $method) {
            $method->accept($this);
        }

        $this->fireEndTrait($trait);
    }

    /**
     * Visits a file node.
     *
     * @param \PHP\Depend\Source\AST\ASTCompilationUnit $compilationUnit
     * @return void
     */
    public function visitFile(ASTCompilationUnit $compilationUnit)
    {
        $this->fireStartFile($compilationUnit);
        $this->fireEndFile($compilationUnit);
    }

    /**
     * Visits a function node.
     *
     * @param ASTFunction $function
     * @return void
     */
    public function visitFunction(ASTFunction $function)
    {
        $this->fireStartFunction($function);

        $function->getSourceFile()->accept($this);

        foreach ($function->getParameters() as $parameter) {
            $parameter->accept($this);
        }

        $this->fireEndFunction($function);
    }

    /**
     * Visits a code interface object.
     *
     * @param ASTInterface $interface
     * @return void
     */
    public function visitInterface(ASTInterface $interface)
    {
        $this->fireStartInterface($interface);

        $interface->getSourceFile()->accept($this);

        foreach ($interface->getMethods() as $method) {
            $method->accept($this);
        }

        $this->fireEndInterface($interface);
    }

    /**
     * Visits a method node.
     *
     * @param \PHP\Depend\Source\AST\ASTMethod $method
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
     * Visits a package node.
     *
     * @param ASTNamespace $namespace
     * @return void
     */
    public function visitNamespace(ASTNamespace $namespace)
    {
        $this->fireStartPackage($namespace);

        foreach ($namespace->getClasses() as $class) {
            $class->accept($this);
        }
        foreach ($namespace->getInterfaces() as $interface) {
            $interface->accept($this);
        }
        foreach ($namespace->getTraits() as $trait) {
            $trait->accept($this);
        }
        foreach ($namespace->getFunctions() as $function) {
            $function->accept($this);
        }

        $this->fireEndPackage($namespace);
    }

    /**
     * Visits a parameter node.
     *
     * @param \PHP\Depend\Source\AST\ASTParameter $parameter
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
     * @param \PHP\Depend\Source\AST\ASTProperty $property
     * @return void
     */
    public function visitProperty(ASTProperty $property)
    {
        $this->fireStartProperty($property);
        $this->fireEndProperty($property);
    }

    /**
     * Sends a start class event.
     *
     * @param \PHP\Depend\Source\AST\ASTClass $class
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
     * @param \PHP\Depend\Source\AST\ASTClass $class
     * @return void
     */
    protected function fireEndClass(ASTClass $class)
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitClass($class);
        }
    }

    /**
     * Sends a start trait event.
     *
     * @param \PHP\Depend\Source\AST\ASTTrait $trait
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
     * @param \PHP\Depend\Source\AST\ASTTrait $trait
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
     * @param \PHP\Depend\Source\AST\ASTCompilationUnit $compilationUnit
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
     * @param \PHP\Depend\Source\AST\ASTCompilationUnit $compilationUnit
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
     * @param ASTFunction $function
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
     * @param \PHP\Depend\Source\AST\ASTFunction $function
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
     * @param \PHP\Depend\Source\AST\ASTInterface $interface
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
     * @param \PHP\Depend\Source\AST\ASTInterface $interface
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
     * @param \PHP\Depend\Source\AST\ASTMethod $method
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
     * @param \PHP\Depend\Source\AST\ASTMethod $method
     * @return void
     */
    protected function fireEndMethod(ASTMethod $method)
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitMethod($method);
        }
    }

    /**
     * Sends a start package event.
     *
     * @param \PHP\Depend\Source\AST\ASTNamespace $namespace
     * @return void
     */
    protected function fireStartPackage(ASTNamespace $namespace)
    {
        foreach ($this->listeners as $listener) {
            $listener->startVisitPackage($namespace);
        }
    }

    /**
     * Sends an end package event.
     *
     * @param \PHP\Depend\Source\AST\ASTNamespace $namespace
     * @return void
     */
    protected function fireEndPackage(ASTNamespace $namespace)
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitPackage($namespace);
        }
    }

    /**
     * Sends a start parameter event.
     *
     * @param \PHP\Depend\Source\AST\ASTParameter $parameter
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
     * @param \PHP\Depend\Source\AST\ASTParameter $parameter
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
     * @param \PHP\Depend\Source\AST\ASTProperty $property
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
     * @param \PHP\Depend\Source\AST\ASTProperty $property
     * @return void
     */
    protected function fireEndProperty(ASTProperty $property)
    {
        foreach ($this->listeners as $listener) {
            $listener->endVisitProperty($property);
        }
    }
}
