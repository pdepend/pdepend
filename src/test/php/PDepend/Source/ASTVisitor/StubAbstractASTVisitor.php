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
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\AST\ASTProperty;

/**
 * Dummy implementation of the default visitor.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class StubAbstractASTVisitor extends AbstractASTVisitor
{
    /**
     * Collected visit order.
     *
     * @var array<string, integer>
     */
    public $visits = array();

    public function visit($node, $value)
    {
        if ($node instanceof ASTCompilationUnit) {
            return $this->visitCompilationUnit($node, $value);
        }
        if ($node instanceof ASTFunction) {
            return $this->visitFunction($node, $value);
        }
        if ($node instanceof ASTInterface) {
            return $this->visitInterface($node, $value);
        }
        if ($node instanceof ASTMethod) {
            return $this->visitMethod($node, $value);
        }
        if ($node instanceof ASTNamespace) {
            return $this->visitNamespace($node, $value);
        }
        if ($node instanceof ASTProperty) {
            return $this->visitProperty($node, $value);
        }
        if ($node instanceof ASTClass) {
            return $this->visitClass($node, $value);
        }

        return parent::visit($node, $value);
    }

    /**
     * Visits a class node.
     *
     * @param \PDepend\Source\AST\ASTClass $class
     */
    public function visitClass(ASTClass $class, $value)
    {
        $this->visits[] = $class->getName();

        return parent::visitClass($class, $value);
    }

    /**
     * Visits a file node.
     *
     * @param \PDepend\Source\AST\ASTCompilationUnit $compilationUnit
     */
    public function visitCompilationUnit(ASTCompilationUnit $compilationUnit, $value)
    {
        $this->visits[] = get_class($compilationUnit);

        return parent::visitCompilationUnit($compilationUnit, $value);
    }

    /**
     * Visits a function node.
     *
     * @param \PDepend\Source\AST\ASTFunction $function
     */
    public function visitFunction(ASTFunction $function, $value)
    {
        $this->visits[] = $function->getName();

        return parent::visitFunction($function, $value);
    }

    /**
     * Visits a code interface object.
     *
     * @param \PDepend\Source\AST\ASTInterface $interface
     */
    public function visitInterface(ASTInterface $interface, $value)
    {
        $this->visits[] = $interface->getName();

        return parent::visitInterface($interface, $value);
    }

    /**
     * Visits a method node.
     *
     * @param \PDepend\Source\AST\ASTMethod $method
     */
    public function visitMethod(ASTMethod $method, $value)
    {
        $this->visits[] = $method->getName();

        return parent::visitMethod($method, $value);
    }

    /**
     * Visits a package node.
     *
     * @param \PDepend\Source\AST\ASTNamespace $namespace The package class node.
     */
    public function visitNamespace(ASTNamespace $namespace, $value)
    {
        $this->visits[] = $namespace->getName();

        return parent::visitNamespace($namespace, $value);
    }

    /**
     * Visits a property node.
     *
     * @param \PDepend\Source\AST\ASTProperty $property
     */
    public function visitProperty(ASTProperty $property, $value)
    {
        $this->visits[] = $property->getName();

        return parent::visitProperty($property, $value);
    }
}
