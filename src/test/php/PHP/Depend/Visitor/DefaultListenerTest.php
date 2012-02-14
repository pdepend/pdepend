<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@pdepend.org>.
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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';
require_once dirname(__FILE__) . '/DefaultVisitorDummy.php';
require_once dirname(__FILE__) . '/TestListener.php';

/**
 * Test case for the default visit listener implementation.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 *
 * @covers PHP_Depend_Visitor_AbstractListener
 * @group pdepend
 * @group pdepend::visitor
 * @group unittest
 */
class PHP_Depend_Visitor_DefaultListenerTest extends PHP_Depend_AbstractTest
{
    /**
     * testDefaultImplementationCallsListeners
     * 
     * @return void
     */
    public function testDefaultImplementationCallsListeners()
    {
        $codeUri  = self::createCodeResourceUriForTest();
        $packages = self::parseSource($codeUri);

        $listener = new PHP_Depend_Visitor_TestListener();
        $visitor  = new PHP_Depend_Visitor_DefaultVisitorDummy();
        $visitor->addVisitListener($listener);
        $visitor->visitPackage($packages->current());

        $actual   = $listener->nodes;
        $expected = array(
            $codeUri . '#start'  =>  true,
            $codeUri . '#end'  =>  true,
            'package#start'  =>  true,
            'package#end'  =>  true,
            'clazz#start'  =>  true,
            'clazz#end'  =>  true,
            'func#start'  =>  true,
            'func#end'  =>  true,
            'interfs#start'  =>  true,
            'interfs#end'  =>  true,
            'm1#start'  =>  true,
            'm1#end'  =>  true,
            'm2#start'  =>  true,
            'm2#end'  =>  true,
            'm3#start'  =>  true,
            'm3#end'  =>  true,
            'm4#start'  =>  true,
            'm4#end'  =>  true,
            '$_p1#start'  =>  true,
            '$_p1#end'  =>  true,
        );

        ksort($actual);
        ksort($expected);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the default listener implementation delegates a class call
     * to the startVisitNode() and endVisitNode() methods.
     *
     * @return void
     */
    public function testListenerCallsStartNodeEndNodeForClass()
    {
        $class = $this->createClassFixture(__FUNCTION__);
        $class->setSourceFile(new PHP_Depend_Code_File(__FILE__));

        $listener = new PHP_Depend_Visitor_TestListener();
        $visitor  = new PHP_Depend_Visitor_DefaultVisitorDummy();
        $visitor->addVisitListener($listener);

        $class->accept($visitor);

        $actual   = $listener->nodes;
        $expected = array(
            __FUNCTION__ . '#start' => true,
            __FUNCTION__ . '#end' => true,
            realpath($GLOBALS['argv'][0]) . '#start' => true,
            realpath($GLOBALS['argv'][0]) . '#end' => true,
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the default listener implementation delegates an interface
     * call to the startVisitNode() and endVisitNode() methods.
     *
     * @return void
     */
    public function testListenerCallsStartNodeEndNodeForInterface()
    {
        $interface = $this->createInterfaceFixture(__FUNCTION__);
        $interface->setSourceFile(new PHP_Depend_Code_File(__FILE__));

        $listener = new PHP_Depend_Visitor_TestListener();
        $visitor  = new PHP_Depend_Visitor_DefaultVisitorDummy();
        $visitor->addVisitListener($listener);

        $interface->accept($visitor);

        $actual   = $listener->nodes;
        $expected = array(
            __FUNCTION__ . '#start' => true,
            __FUNCTION__ . '#end' => true,
            realpath($GLOBALS['argv'][0]) . '#start' => true,
            realpath($GLOBALS['argv'][0]) . '#end' => true,
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the default listener implementation delegates a function
     * call to the startVisitNode() and endVisitNode() methods.
     *
     * @return void
     */
    public function testListenerCallsStartNodeEndNodeForFunction()
    {
        $function = $this->createFunctionFixture(__FUNCTION__);
        $function->setSourceFile(new PHP_Depend_Code_File(__FILE__));

        $listener = new PHP_Depend_Visitor_TestListener();
        $visitor  = new PHP_Depend_Visitor_DefaultVisitorDummy();
        $visitor->addVisitListener($listener);

        $function->accept($visitor);

        $actual   = $listener->nodes;
        $expected = array(
            __FUNCTION__ . '#start' => true,
            __FUNCTION__ . '#end' => true,
            realpath($GLOBALS['argv'][0]) . '#start' => true,
            realpath($GLOBALS['argv'][0]) . '#end' => true,
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the default listener implementation delegates a method call to
     * the startVisitNode() and endVisitNode() methods.
     *
     * @return void
     */
    public function testListenerCallsStartNodeEndNodeForMethod()
    {
        $method = $this->createMethodFixture(__FUNCTION__);
        $method->setSourceFile(new PHP_Depend_Code_File(__FILE__));

        $listener = new PHP_Depend_Visitor_TestListener();
        $visitor  = new PHP_Depend_Visitor_DefaultVisitorDummy();
        $visitor->addVisitListener($listener);

        $method->accept($visitor);

        $actual   = $listener->nodes;
        $expected = array(
            __FUNCTION__ . '#start' => true,
            __FUNCTION__ . '#end' => true,
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testListenerCallsStartVisitNodeForPassedParameterInstance
     *
     * @return void
     */
    public function testListenerCallsStartVisitNodeForPassedParameterInstance()
    {
        $listener = $this->getMock('PHP_Depend_Visitor_AbstractListener', array('startVisitNode'));
        $listener->expects($this->once())
            ->method('startVisitNode');

        $parameter = $this->getMock('PHP_Depend_Code_Parameter', array(), array(null), '', false);
        $listener->startVisitParameter($parameter);
    }

    /**
     * testListenerCallsEndVisitNodeForPassedParameterInstance
     *
     * @return void
     */
    public function testListenerCallsEndVisitNodeForPassedParameterInstance()
    {
        $listener = $this->getMock('PHP_Depend_Visitor_AbstractListener', array('endVisitNode'));
        $listener->expects($this->once())
            ->method('endVisitNode');

        $parameter = $this->getMock('PHP_Depend_Code_Parameter', array(), array(null), '', false);
        $listener->endVisitParameter($parameter);
    }

    /**
     * testListenerInvokesStartVisitNotForTrait
     *
     * @return void
     * @since 1.0.0
     */
    public function testListenerInvokesStartVisitNotForTrait()
    {
        $listener = $this->getMock('PHP_Depend_Visitor_AbstractListener', array('startVisitNode'));
        $listener->expects($this->once())
            ->method('startVisitNode');

        $listener->startVisitTrait(new PHP_Depend_Code_Trait('MyTrait'));
    }

    /**
     * testListenerInvokesEndVisitNotForTrait
     *
     * @return void
     * @since 1.0.0
     */
    public function testListenerInvokesEndVisitNotForTrait()
    {
        $listener = $this->getMock('PHP_Depend_Visitor_AbstractListener', array('endVisitNode'));
        $listener->expects($this->once())
            ->method('endVisitNode');

        $listener->endVisitTrait(new PHP_Depend_Code_Trait('MyTrait'));
    }
}
