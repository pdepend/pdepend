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
use PDepend\Source\AST\ASTParameter;
use PDepend\Source\AST\ASTTrait;

/**
 * Test case for the default visit listener implementation.
 *
 * @covers \PDepend\Source\ASTVisitor\AbstractASTVisitor
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @group unittest
 */
class DefaultListenerTest extends AbstractTestCase
{
    /**
     * testDefaultImplementationCallsListeners
     */
    public function testDefaultImplementationCallsListeners(): void
    {
        $codeUri = $this->createCodeResourceUriForTest();
        $namespaces = $this->parseSource($codeUri);

        $listener = new StubAbstractASTVisitListener();
        $visitor = new StubAbstractASTVisitor();
        $visitor->addVisitListener($listener);
        $visitor->visitNamespace($namespaces[0]);

        $actual = $listener->nodes;
        $expected = [
            $codeUri . '#start' => true,
            $codeUri . '#end' => true,
            'package#start' => true,
            'package#end' => true,
            'clazz#start' => true,
            'clazz#end' => true,
            'func#start' => true,
            'func#end' => true,
            'interfs#start' => true,
            'interfs#end' => true,
            'm1#start' => true,
            'm1#end' => true,
            'm2#start' => true,
            'm2#end' => true,
            'm3#start' => true,
            'm3#end' => true,
            'm4#start' => true,
            'm4#end' => true,
            '$_p1#start' => true,
            '$_p1#end' => true,
        ];

        ksort($actual);
        ksort($expected);

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the default listener implementation delegates a class call
     * to the startVisitNode() and endVisitNode() methods.
     */
    public function testListenerCallsStartNodeEndNodeForClass(): void
    {
        $class = $this->createClassFixture(__FUNCTION__);
        $class->setCompilationUnit(new ASTCompilationUnit(__FILE__));

        $listener = new StubAbstractASTVisitListener();
        $visitor = new StubAbstractASTVisitor();
        $visitor->addVisitListener($listener);

        $visitor->dispatch($class);

        $actual = $listener->nodes;
        $expected = [
            __FUNCTION__ . '#start' => true,
            __FUNCTION__ . '#end' => true,
            realpath($GLOBALS['argv'][0]) . '#start' => true,
            realpath($GLOBALS['argv'][0]) . '#end' => true,
        ];

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the default listener implementation delegates an interface
     * call to the startVisitNode() and endVisitNode() methods.
     */
    public function testListenerCallsStartNodeEndNodeForInterface(): void
    {
        $interface = $this->createInterfaceFixture(__FUNCTION__);
        $interface->setCompilationUnit(new ASTCompilationUnit(__FILE__));

        $listener = new StubAbstractASTVisitListener();
        $visitor = new StubAbstractASTVisitor();
        $visitor->addVisitListener($listener);

        $visitor->dispatch($interface);

        $actual = $listener->nodes;
        $expected = [
            __FUNCTION__ . '#start' => true,
            __FUNCTION__ . '#end' => true,
            realpath($GLOBALS['argv'][0]) . '#start' => true,
            realpath($GLOBALS['argv'][0]) . '#end' => true,
        ];

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the default listener implementation delegates a function
     * call to the startVisitNode() and endVisitNode() methods.
     */
    public function testListenerCallsStartNodeEndNodeForFunction(): void
    {
        $function = $this->createFunctionFixture(__FUNCTION__);
        $function->setCompilationUnit(new ASTCompilationUnit(__FILE__));

        $listener = new StubAbstractASTVisitListener();
        $visitor = new StubAbstractASTVisitor();
        $visitor->addVisitListener($listener);

        $visitor->dispatch($function);

        $actual = $listener->nodes;
        $expected = [
            __FUNCTION__ . '#start' => true,
            __FUNCTION__ . '#end' => true,
            realpath($GLOBALS['argv'][0]) . '#start' => true,
            realpath($GLOBALS['argv'][0]) . '#end' => true,
        ];

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the default listener implementation delegates a method call to
     * the startVisitNode() and endVisitNode() methods.
     */
    public function testListenerCallsStartNodeEndNodeForMethod(): void
    {
        $method = $this->createMethodFixture(__FUNCTION__);
        $method->setCompilationUnit(new ASTCompilationUnit(__FILE__));

        $listener = new StubAbstractASTVisitListener();
        $visitor = new StubAbstractASTVisitor();
        $visitor->addVisitListener($listener);

        $visitor->dispatch($method);

        $actual = $listener->nodes;
        $expected = [
            __FUNCTION__ . '#start' => true,
            __FUNCTION__ . '#end' => true,
        ];

        static::assertEquals($expected, $actual);
    }

    /**
     * testListenerCallsStartVisitNodeForPassedParameterInstance
     */
    public function testListenerCallsStartVisitNodeForPassedParameterInstance(): void
    {
        $listener = $this->getMockBuilder(AbstractASTVisitListener::class)
            ->onlyMethods(['startVisitNode'])
            ->getMock();
        $listener->expects(static::once())
            ->method('startVisitNode');

        $parameter = $this->getMockBuilder(ASTParameter::class)
            ->disableOriginalConstructor()
            ->getMock();
        $listener->startVisitParameter($parameter);
    }

    /**
     * testListenerCallsEndVisitNodeForPassedParameterInstance
     */
    public function testListenerCallsEndVisitNodeForPassedParameterInstance(): void
    {
        $listener = $this->getMockBuilder(AbstractASTVisitListener::class)
            ->onlyMethods(['endVisitNode'])
            ->getMock();
        $listener->expects(static::once())
            ->method('endVisitNode');

        $parameter = $this->getMockBuilder(ASTParameter::class)
            ->disableOriginalConstructor()
            ->getMock();
        $listener->endVisitParameter($parameter);
    }

    /**
     * testListenerInvokesStartVisitNotForTrait
     *
     * @since 1.0.0
     */
    public function testListenerInvokesStartVisitNotForTrait(): void
    {
        $listener = $this->getMockBuilder(AbstractASTVisitListener::class)
            ->onlyMethods(['startVisitNode'])
            ->getMock();
        $listener->expects(static::once())
            ->method('startVisitNode');

        $listener->startVisitTrait(new ASTTrait('MyTrait'));
    }

    /**
     * testListenerInvokesEndVisitNotForTrait
     *
     * @since 1.0.0
     */
    public function testListenerInvokesEndVisitNotForTrait(): void
    {
        $listener = $this->getMockBuilder(AbstractASTVisitListener::class)
            ->onlyMethods(['endVisitNode'])
            ->getMock();
        $listener->expects(static::once())
            ->method('endVisitNode');

        $listener->endVisitTrait(new ASTTrait('MyTrait'));
    }
}
