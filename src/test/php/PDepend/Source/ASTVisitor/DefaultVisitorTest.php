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

use PDepend\AbstractTestCase;
use PDepend\Source\AST\ASTCompilationUnit;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\AST\ASTTrait;

/**
 * Test case for the default visitor implementation.
 *
 * @covers \PDepend\Source\ASTVisitor\AbstractASTVisitor
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @group unittest
 */
class DefaultVisitorTest extends AbstractTestCase
{
    /**
     * Tests the execution order of the default visitor implementation.
     */
    public function testDefaultVisitOrder(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $visitor = new StubAbstractASTVisitor();
        foreach ($namespaces as $namespace) {
            $visitor->dispatch($namespace);
        }

        $expected = [
            'pkgA',
            'classB',
            ASTCompilationUnit::class,
            'methodBA',
            'methodBB',
            'classA',
            ASTCompilationUnit::class,
            'methodAB',
            'methodAA',
            'pkgB',
            'interfsC',
            ASTCompilationUnit::class,
            'methodCB',
            'methodCA',
            'funcD',
            ASTCompilationUnit::class,
        ];

        static::assertEquals($expected, $visitor->visits);
    }

    /**
     * testVisitorVisitsFunctionParameter
     */
    public function testVisitorVisitsFunctionParameter(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $visitor = $this->getMockBuilder(AbstractASTVisitor::class)
            ->onlyMethods(['visitParameter'])
            ->getMock();
        $visitor->expects(static::exactly(2))
            ->method('visitParameter');

        $visitor->visitNamespace($namespaces[0]);
    }

    /**
     * testVisitorVisitsMethodParameter
     */
    public function testVisitorVisitsMethodParameter(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $visitor = $this->getMockBuilder(AbstractASTVisitor::class)
            ->onlyMethods(['visitParameter'])
            ->getMock();
        $visitor->expects(static::exactly(3))
            ->method('visitParameter');

        $visitor->visitNamespace($namespaces[0]);
    }

    /**
     * testVisitorInvokesStartVisitParameterOnListener
     */
    public function testVisitorInvokesStartVisitParameterOnListener(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $listener = $this->getMockBuilder(ASTVisitListener::class)
            ->getMock();
        $listener->expects(static::exactly(2))
            ->method('startVisitParameter');

        $visitor = $this->getMockBuilder(AbstractASTVisitor::class)
            ->onlyMethods(['getVisitListeners'])
            ->getMock();
        $visitor->addVisitListener($listener);

        $visitor->visitNamespace($namespaces[0]);
    }

    /**
     * testVisitorInvokesEndVisitParameterOnListener
     */
    public function testVisitorInvokesEndVisitParameterOnListener(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $listener = $this->getMockBuilder(ASTVisitListener::class)
            ->getMock();
        $listener->expects(static::exactly(3))
            ->method('endVisitParameter');

        $visitor = $this->getMockBuilder(AbstractASTVisitor::class)
            ->onlyMethods(['getVisitListeners'])
            ->getMock();
        $visitor->addVisitListener($listener);

        $visitor->visitNamespace($namespaces[0]);
    }

    /**
     * testVisitorInvokesStartVisitInterfaceOnListener
     */
    public function testVisitorInvokesStartVisitInterfaceOnListener(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $listener = $this->getMockBuilder(ASTVisitListener::class)
            ->getMock();
        $listener->expects(static::once())
            ->method('startVisitInterface');

        $visitor = $this->getMockBuilder(AbstractASTVisitor::class)
            ->onlyMethods(['getVisitListeners'])
            ->getMock();
        $visitor->addVisitListener($listener);

        $visitor->visitNamespace($namespaces[0]);
    }

    /**
     * testVisitorInvokesEndVisitInterfaceOnListener
     */
    public function testVisitorInvokesEndVisitInterfaceOnListener(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $listener = $this->getMockBuilder(ASTVisitListener::class)
            ->getMock();
        $listener->expects(static::once())
            ->method('endVisitInterface');

        $visitor = $this->getMockBuilder(AbstractASTVisitor::class)
            ->onlyMethods(['getVisitListeners'])
            ->getMock();
        $visitor->addVisitListener($listener);

        $visitor->visitNamespace($namespaces[0]);
    }

    /**
     * testVisitorInvokesStartVisitPropertyOnListener
     */
    public function testVisitorInvokesStartVisitPropertyOnListener(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $listener = $this->getMockBuilder(ASTVisitListener::class)
            ->getMock();
        $listener->expects(static::once())
            ->method('startVisitProperty');

        $visitor = $this->getMockBuilder(AbstractASTVisitor::class)
            ->onlyMethods(['getVisitListeners'])
            ->getMock();
        $visitor->addVisitListener($listener);

        $visitor->visitNamespace($namespaces[0]);
    }

    /**
     * testVisitorInvokesEndVisitPropertyOnListener
     */
    public function testVisitorInvokesEndVisitPropertyOnListener(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $listener = $this->getMockBuilder(ASTVisitListener::class)
            ->getMock();
        $listener->expects(static::once())
            ->method('endVisitProperty');

        $visitor = $this->getMockBuilder(AbstractASTVisitor::class)
            ->onlyMethods(['getVisitListeners'])
            ->getMock();
        $visitor->addVisitListener($listener);

        $visitor->visitNamespace($namespaces[0]);
    }

    /**
     * testVisitorVisitsTrait
     *
     * @since 1.0.0
     */
    public function testVisitorVisitsTrait(): void
    {
        $namespace = new ASTNamespace('MyPackage');
        $namespace->addType(new ASTTrait('MyTraitOne'))
            ->setCompilationUnit(new ASTCompilationUnit(__FILE__));
        $namespace->addType(new ASTTrait('MyTraitTwo'))
            ->setCompilationUnit(new ASTCompilationUnit(__FILE__));

        $visitor = $this->getMockBuilder(AbstractASTVisitor::class)
            ->onlyMethods(['visitTrait'])
            ->getMock();
        $visitor->expects(static::exactly(2))
            ->method('visitTrait');

        $visitor->dispatch($namespace);
    }

    /**
     * testVisitorInvokesAcceptOnTraitMethods
     *
     * @since 1.0.0
     */
    public function testVisitorInvokesAcceptOnTraitMethods(): void
    {
        $trait = $this->createTraitFixture();
        $trait->setCompilationUnit(new ASTCompilationUnit(__FILE__));
        $trait->addMethod($method0 = new ASTMethod('m0'));
        $trait->addMethod($method1 = new ASTMethod('m1'));

        $visitor = $this->getMockBuilder(AbstractASTVisitor::class)
            ->onlyMethods(['visitMethod'])
            ->getMock();

        $visitor->expects(static::exactly(2))
            ->method('visitMethod');

        $visitor->dispatch($trait);
    }

    /**
     * testVisitorInvokesStartTraitOnListener
     *
     * @since 1.0.0
     */
    public function testVisitorInvokesStartTraitOnListener(): void
    {
        $trait = $this->createTraitFixture();
        $trait->setCompilationUnit(new ASTCompilationUnit(__FILE__));

        $namespace = new ASTNamespace('MyPackage');
        $namespace->addType($trait);

        $listener = $this->getMockBuilder(ASTVisitListener::class)
            ->getMock();
        $listener->expects(static::once())
            ->method('startVisitTrait');

        $visitor = $this->getMockBuilder(AbstractASTVisitor::class)
            ->onlyMethods(['getVisitListeners'])
            ->getMock();
        $visitor->addVisitListener($listener);

        $visitor->visitNamespace($namespace);
    }

    /**
     * testVisitorInvokesEndTraitOnListener
     *
     * @since 1.0.0
     */
    public function testVisitorInvokesEndTraitOnListener(): void
    {
        $trait = $this->createTraitFixture();
        $trait->setCompilationUnit(new ASTCompilationUnit(__FILE__));

        $namespace = new ASTNamespace('MyPackage');
        $namespace->addType($trait);

        $listener = $this->createMock(ASTVisitListener::class);
        $listener->expects(static::once())
            ->method('endVisitTrait');

        $visitor = $this->getMockBuilder(AbstractASTVisitor::class)
            ->onlyMethods(['getVisitListeners'])
            ->getMock();
        $visitor->addVisitListener($listener);

        $visitor->visitNamespace($namespace);
    }

    /**
     * testGetVisitListenersReturnsIterator
     */
    public function testGetVisitListenersReturnsIterator(): void
    {
        $visitor = $this->getMockForAbstractClass(AbstractASTVisitor::class);
        static::assertInstanceOf('Iterator', $visitor->getVisitListeners());
    }

    /**
     * testGetVisitListenersContainsAddedListener
     */
    public function testGetVisitListenersContainsAddedListener(): void
    {
        $visitor = $this->getMockForAbstractClass(AbstractASTVisitor::class);

        $listener = $this->getMockBuilder(ASTVisitListener::class)
            ->getMock();
        $visitor->addVisitListener($listener);

        static::assertCount(1, $visitor->getVisitListeners());
    }
}
