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

use PDepend\AbstractTest;
use PDepend\Source\AST\ASTCompilationUnit;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\AST\ASTTrait;

/**
 * Test case for the default visitor implementation.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @covers \PDepend\Source\ASTVisitor\AbstractASTVisitor
 * @group unittest
 */
class DefaultVisitorTest extends AbstractTest
{
    /**
     * Tests the execution order of the default visitor implementation.
     *
     * @return void
     */
    public function testDefaultVisitOrder()
    {
        $namespaces = $this->parseCodeResourceForTest();

        $visitor = new StubAbstractASTVisitor();
        foreach ($namespaces as $namespace) {
            $namespace->accept($visitor);
        }

        $expected = array(
            'pkgA',
            'classB',
            'PDepend\\Source\\AST\\ASTCompilationUnit',
            'methodBA',
            'methodBB',
            'classA',
            'PDepend\\Source\\AST\\ASTCompilationUnit',
            'methodAB',
            'methodAA',
            'pkgB',
            'interfsC',
            'PDepend\\Source\\AST\\ASTCompilationUnit',
            'methodCB',
            'methodCA',
            'funcD',
            'PDepend\\Source\\AST\\ASTCompilationUnit'
        );

        $this->assertEquals($expected, $visitor->visits);
    }

    /**
     * testVisitorVisitsFunctionParameter
     *
     * @return void
     */
    public function testVisitorVisitsFunctionParameter()
    {
        $namespaces = $this->parseCodeResourceForTest();

        $visitor = $this->getMockBuilder('\\PDepend\\Source\\ASTVisitor\\AbstractASTVisitor')
            ->setMethods(array('visitParameter'))
            ->getMock();
        $visitor->expects($this->exactly(2))
            ->method('visitParameter');

        $visitor->visitNamespace($namespaces[0], '1');
    }

    /**
     * testVisitorVisitsMethodParameter
     *
     * @return void
     */
    public function testVisitorVisitsMethodParameter()
    {
        $namespaces = $this->parseCodeResourceForTest();

        $visitor = $this->getMockBuilder('\\PDepend\\Source\\ASTVisitor\\AbstractASTVisitor')
            ->setMethods(array('visitParameter'))
            ->getMock();
        $visitor->expects($this->exactly(3))
            ->method('visitParameter');

        $visitor->visitNamespace($namespaces[0], '1');
    }

    /**
     * testVisitorInvokesStartVisitParameterOnListener
     *
     * @return void
     */
    public function testVisitorInvokesStartVisitParameterOnListener()
    {
        $namespaces = $this->parseCodeResourceForTest();

        $listener = $this->getMockBuilder('\\PDepend\\Source\\ASTVisitor\\ASTVisitListener')
            ->getMock();
        $listener->expects($this->exactly(2))
            ->method('startVisitParameter');

        $visitor = $this->getMockBuilder('\\PDepend\\Source\\ASTVisitor\\AbstractASTVisitor')
            ->setMethods(array('getVisitListeners'))
            ->getMock();
        $visitor->addVisitListener($listener);

        $visitor->visitNamespace($namespaces[0], '1');
    }

    /**
     * testVisitorInvokesEndVisitParameterOnListener
     *
     * @return void
     */
    public function testVisitorInvokesEndVisitParameterOnListener()
    {
        $namespaces = $this->parseCodeResourceForTest();

        $listener = $this->getMockBuilder('\\PDepend\\Source\\ASTVisitor\\ASTVisitListener')
            ->getMock();
        $listener->expects($this->exactly(3))
            ->method('endVisitParameter');

        $visitor = $this->getMockBuilder('\\PDepend\\Source\\ASTVisitor\\AbstractASTVisitor')
            ->setMethods(array('getVisitListeners'))
            ->getMock();
        $visitor->addVisitListener($listener);

        $visitor->visitNamespace($namespaces[0], '1');
    }

    /**
     * testVisitorInvokesStartVisitInterfaceOnListener
     *
     * @return void
     */
    public function testVisitorInvokesStartVisitInterfaceOnListener()
    {
        $namespaces = $this->parseCodeResourceForTest();

        $listener = $this->getMockBuilder('\\PDepend\\Source\\ASTVisitor\\ASTVisitListener')
            ->getMock();
        $listener->expects($this->once())
            ->method('startVisitInterface');

        $visitor = $this->getMockBuilder('\\PDepend\\Source\\ASTVisitor\\AbstractASTVisitor')
            ->setMethods(array('getVisitListeners'))
            ->getMock();
        $visitor->addVisitListener($listener);

        $visitor->visitNamespace($namespaces[0], '1');
    }

    /**
     * testVisitorInvokesEndVisitInterfaceOnListener
     *
     * @return void
     */
    public function testVisitorInvokesEndVisitInterfaceOnListener()
    {
        $namespaces = $this->parseCodeResourceForTest();

        $listener = $this->getMockBuilder('\\PDepend\\Source\\ASTVisitor\\ASTVisitListener')
            ->getMock();
        $listener->expects($this->once())
            ->method('endVisitInterface');

        $visitor = $this->getMockBuilder('\\PDepend\\Source\\ASTVisitor\\AbstractASTVisitor')
            ->setMethods(array('getVisitListeners'))
            ->getMock();
        $visitor->addVisitListener($listener);

        $visitor->visitNamespace($namespaces[0], '1');
    }

    /**
     * testVisitorInvokesStartVisitPropertyOnListener
     *
     * @return void
     */
    public function testVisitorInvokesStartVisitPropertyOnListener()
    {
        $namespaces = $this->parseCodeResourceForTest();

        $listener = $this->getMockBuilder('\\PDepend\\Source\\ASTVisitor\\ASTVisitListener')
            ->getMock();
        $listener->expects($this->once())
            ->method('startVisitProperty');

        $visitor = $this->getMockBuilder('\\PDepend\\Source\\ASTVisitor\\AbstractASTVisitor')
            ->setMethods(array('getVisitListeners'))
            ->getMock();
        $visitor->addVisitListener($listener);

        $visitor->visitNamespace($namespaces[0], '1');
    }

    /**
     * testVisitorInvokesEndVisitPropertyOnListener
     *
     * @return void
     */
    public function testVisitorInvokesEndVisitPropertyOnListener()
    {
        $namespaces = $this->parseCodeResourceForTest();

        $listener = $this->getMockBuilder('\\PDepend\\Source\\ASTVisitor\\ASTVisitListener')
            ->getMock();
        $listener->expects($this->once())
            ->method('endVisitProperty');

        $visitor = $this->getMockBuilder('\\PDepend\\Source\\ASTVisitor\\AbstractASTVisitor')
            ->setMethods(array('getVisitListeners'))
            ->getMock();
        $visitor->addVisitListener($listener);

        $visitor->visitNamespace($namespaces[0], '1');
    }

    /**
     * testVisitorVisitsTrait
     *
     * @return void
     * @since 1.0.0
     */
    public function testVisitorVisitsTrait()
    {
        $namespace = new ASTNamespace('MyPackage');
        $namespace->addType(new ASTTrait('MyTraitOne'))
            ->setCompilationUnit(new ASTCompilationUnit(__FILE__));
        $namespace->addType(new ASTTrait('MyTraitTwo'))
            ->setCompilationUnit(new ASTCompilationUnit(__FILE__));

        $visitor = $this->getMockBuilder('\\PDepend\\Source\\ASTVisitor\\AbstractASTVisitor')
            ->setMethods(array('visitTrait'))
            ->getMock();
        $visitor->expects($this->exactly(2))
            ->method('visitTrait');

        $namespace->accept($visitor);
    }

    /**
     * testVisitorInvokesAcceptOnTraitMethods
     *
     * @return void
     * @since 1.0.0
     */
    public function testVisitorInvokesAcceptOnTraitMethods()
    {
        $trait = $this->createTraitFixture();
        $trait->setCompilationUnit(new ASTCompilationUnit(__FILE__));
        $trait->addMethod($method0 = new ASTMethod('m0'));
        $trait->addMethod($method1 = new ASTMethod('m1'));

        $visitor = $this->getMockBuilder('\\PDepend\\Source\\ASTVisitor\\AbstractASTVisitor')
            ->setMethods(array('visitMethod'))
            ->getMock();
        $visitor->expects($this->at(0))
            ->method('visitMethod')
            ->with($this->equalTo($method0));
        $visitor->expects($this->at(1))
            ->method('visitMethod')
            ->with($this->equalTo($method1));

        $trait->accept($visitor);
    }

    /**
     * testVisitorInvokesStartTraitOnListener
     *
     * @return void
     * @since 1.0.0
     */
    public function testVisitorInvokesStartTraitOnListener()
    {
        $trait = $this->createTraitFixture();
        $trait->setCompilationUnit(new ASTCompilationUnit(__FILE__));

        $namespace = new ASTNamespace('MyPackage');
        $namespace->addType($trait);

        $listener = $this->getMockBuilder('\\PDepend\\Source\\ASTVisitor\\ASTVisitListener')
            ->getMock();
        $listener->expects($this->once())
            ->method('startVisitTrait');

        $visitor = $this->getMockBuilder('\\PDepend\\Source\\ASTVisitor\\AbstractASTVisitor')
            ->setMethods(array('getVisitListeners'))
            ->getMock();
        $visitor->addVisitListener($listener);

        $visitor->visitNamespace($namespace, '1');
    }

    /**
     * testVisitorInvokesEndTraitOnListener
     *
     * @return void
     * @since 1.0.0
     */
    public function testVisitorInvokesEndTraitOnListener()
    {
        $trait = $this->createTraitFixture();
        $trait->setCompilationUnit(new ASTCompilationUnit(__FILE__));

        $namespace = new ASTNamespace('MyPackage');
        $namespace->addType($trait);

        $listener = $this->getMockBuilder('\\PDepend\\Source\\ASTVisitor\\ASTVisitListener')
            ->getMock();
        $listener->expects($this->once())
            ->method('endVisitTrait');

        $visitor = $this->getMockBuilder('\\PDepend\\Source\\ASTVisitor\\AbstractASTVisitor')
            ->setMethods(array('getVisitListeners'))
            ->getMock();
        $visitor->addVisitListener($listener);

        $visitor->visitNamespace($namespace, '1');
    }

    /**
     * testGetVisitListenersReturnsIterator
     *
     * @return void
     */
    public function testGetVisitListenersReturnsIterator()
    {
        $visitor = $this->getAbstractClassMock('\\PDepend\\Source\\ASTVisitor\\AbstractASTVisitor');
        $this->assertInstanceOf('Iterator', $visitor->getVisitListeners());
    }

    /**
     * testGetVisitListenersContainsAddedListener
     *
     * @return void
     */
    public function testGetVisitListenersContainsAddedListener()
    {
        $visitor = $this->getAbstractClassMock('\\PDepend\\Source\\ASTVisitor\\AbstractASTVisitor');

        $listener = $this->getMockBuilder('\\PDepend\\Source\\ASTVisitor\\ASTVisitListener')
            ->getMock();
        $visitor->addVisitListener($listener);

        $this->assertEquals(1, count($visitor->getVisitListeners()));
    }
}
