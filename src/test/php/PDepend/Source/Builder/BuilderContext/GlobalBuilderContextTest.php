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

namespace PDepend\Source\Builder\BuilderContext;

use PDepend\AbstractTestCase;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTTrait;

/**
 * Test case for the {@link \PDepend\Source\Builder\BuilderContext\GlobalBuilderContext}
 * class.
 *
 * @covers \PDepend\Source\Builder\BuilderContext\GlobalBuilderContext
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class GlobalBuilderContextTest extends AbstractTestCase
{
    /**
     * testRegisterTraitCallsRestoreClassOnBuilder
     *
     * @since 1.0.0
     */
    public function testRegisterTraitCallsRestoreClassOnBuilder(): void
    {
        $builder = $this->getMockBuilder('\\PDepend\\Source\\Builder\\Builder')
            ->getMock();
        $builder->expects($this->once())
            ->method('restoreTrait')
            ->with($this->isInstanceOf('PDepend\\Source\\AST\\ASTTrait'));

        $context = new GlobalBuilderContext($builder);
        $context->registerTrait(new ASTTrait(__CLASS__));
    }

    /**
     * testRegisterClassCallsRestoreClassOnBuilder
     */
    public function testRegisterClassCallsRestoreClassOnBuilder(): void
    {
        $builder = $this->getMockBuilder('\\PDepend\\Source\\Builder\\Builder')
            ->getMock();
        $builder->expects($this->once())
            ->method('restoreClass')
            ->with($this->isInstanceOf('PDepend\\Source\\AST\\ASTClass'));

        $context = new GlobalBuilderContext($builder);
        $context->registerClass(new ASTClass(__CLASS__));
    }

    /**
     * testRegisterInterfaceCallsRestoreInterfaceOnBuilder
     */
    public function testRegisterInterfaceCallsRestoreInterfaceOnBuilder(): void
    {
        $builder = $this->getMockBuilder('\\PDepend\\Source\\Builder\\Builder')
            ->getMock();
        $builder->expects($this->once())
            ->method('restoreInterface')
            ->with($this->isInstanceOf('PDepend\\Source\\AST\\ASTInterface'));

        $context = new GlobalBuilderContext($builder);
        $context->registerInterface(new ASTInterface(__CLASS__));
    }

    /**
     * testRegisterFunctionCallsRestoreFunctionOnBuilder
     */
    public function testRegisterFunctionCallsRestoreFunctionOnBuilder(): void
    {
        $builder = $this->getMockBuilder('\\PDepend\\Source\\Builder\\Builder')
            ->getMock();
        $builder->expects($this->once())
            ->method('restoreFunction')
            ->with($this->isInstanceOf('PDepend\\Source\\AST\\ASTFunction'));

        $context = new GlobalBuilderContext($builder);
        $context->registerFunction(new ASTFunction(__CLASS__));
    }

    /**
     * testGetTraitDelegatesCallToWrappedBuilder
     *
     * @since 1.0.0
     */
    public function testGetTraitDelegatesCallToWrappedBuilder(): void
    {
        $builder = $this->getMockBuilder('\\PDepend\\Source\\Builder\\Builder')
            ->getMock();
        $builder->expects($this->once())
            ->method('getTrait')
            ->with($this->equalTo(__CLASS__));

        $context = new GlobalBuilderContext($builder);
        $context->getTrait(__CLASS__);
    }

    /**
     * testGetClassDelegatesCallToWrappedBuilder
     */
    public function testGetClassDelegatesCallToWrappedBuilder(): void
    {
        $builder = $this->getMockBuilder('\\PDepend\\Source\\Builder\\Builder')
            ->getMock();
        $builder->expects($this->once())
            ->method('getClass')
            ->with($this->equalTo(__CLASS__));

        $context = new GlobalBuilderContext($builder);
        $context->getClass(__CLASS__);
    }

    /**
     * testGetClassOrInterfaceDelegatesCallToWrappedBuilder
     */
    public function testGetClassOrInterfaceDelegatesCallToWrappedBuilder(): void
    {
        $builder = $this->getMockBuilder('\\PDepend\\Source\\Builder\\Builder')
            ->getMock();
        $builder->expects($this->once())
            ->method('getClassOrInterface')
            ->with($this->equalTo(__CLASS__));

        $context = new GlobalBuilderContext($builder);
        $context->getClassOrInterface(__CLASS__);
    }
}
