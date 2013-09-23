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

/**
 * Dummy implementation of the default visitor.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
class DefaultVisitorDummy extends AbstractTreeVisitor
{
    /**
     * Collected visit order.
     *
     * @var array(string=>integer)
     */
    public $visits = array();

    /**
     * Visits a class node.
     *
     * @param \PHP\Depend\Source\AST\ASTClass $class
     * @return void
     */
    public function visitClass(ASTClass $class)
    {
        $this->visits[] = $class->getName();

        parent::visitClass($class);
    }

    /**
     * Visits a file node.
     *
     * @param \PHP\Depend\Source\AST\ASTCompilationUnit $compilationUnit
     * @return void
     */
    public function visitFile(ASTCompilationUnit $compilationUnit)
    {
        $this->visits[] = get_class($compilationUnit);

        parent::visitFile($compilationUnit);
    }

    /**
     * Visits a function node.
     *
     * @param \PHP\Depend\Source\AST\ASTFunction $function
     * @return void
     */
    public function visitFunction(ASTFunction $function)
    {
        $this->visits[] = $function->getName();

        parent::visitFunction($function);
    }

    /**
     * Visits a code interface object.
     *
     * @param \PHP\Depend\Source\AST\ASTInterface $interface
     * @return void
     */
    public function visitInterface(ASTInterface $interface)
    {
        $this->visits[] = $interface->getName();

        parent::visitInterface($interface);
    }

    /**
     * Visits a method node.
     *
     * @param \PHP\Depend\Source\AST\ASTMethod $method
     * @return void
     */
    public function visitMethod(ASTMethod $method)
    {
        $this->visits[] = $method->getName();

        parent::visitMethod($method);
    }

    /**
     * Visits a package node.
     *
     * @param \PHP\Depend\Source\AST\ASTNamespace $namespace The package class node.
     * @return void
     */
    public function visitNamespace(ASTNamespace $namespace)
    {
        $this->visits[] = $namespace->getName();

        parent::visitNamespace($namespace);
    }

    /**
     * Visits a property node.
     *
     * @param \PHP_Depend_Code_Property $property The property class node.
     * @return void
     */
    public function visitProperty(\PHP_Depend_Code_Property $property)
    {
        $this->visits[] = $property->getName();

        parent::visitProperty($property);
    }
}
