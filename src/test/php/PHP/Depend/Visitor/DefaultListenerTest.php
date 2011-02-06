<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';
require_once dirname(__FILE__) . '/DefaultVisitorDummy.php';
require_once dirname(__FILE__) . '/TestListener.php';

require_once 'PHP/Depend/Code/Parameter.php';

/**
 * Test case for the default visit listener implementation.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Visitor_DefaultListenerTest extends PHP_Depend_AbstractTest
{
    /**
     * testDefaultImplementationCallsListeners
     * 
     * @return void
     * @covers PHP_Depend_Visitor_AbstractListener
     * @group pdepend
     * @group pdepend::visitor
     * @group unittest
     */
    public function testDefaultImplementationCallsListeners()
    {
        $codeUri  = self::createCodeResourceURI('visitor/' . __FUNCTION__ . '.php');
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
     * @covers PHP_Depend_Visitor_AbstractListener
     * @group pdepend
     * @group pdepend::visitor
     * @group unittest
     */
    public function testListenerCallsStartNodeEndNodeForClass()
    {
        include_once 'PHP/Depend/Code/Class.php';

        $class = $this->getMock(
            'PHP_Depend_Code_Class',
            array(
                'getName', 'getSourceFile'
            ),
            array(__FUNCTION__)
        );
        $class->expects($this->atLeastOnce())
            ->method('getName')
            ->will($this->returnValue(__FUNCTION__));
        $class->expects($this->atLeastOnce())
            ->method('getSourceFile')
            ->will(
                $this->returnValue(
                    $this->getMock('PHP_Depend_Code_File', array(), array(null))
                )
            );

        $listener = new PHP_Depend_Visitor_TestListener();
        $visitor  = new PHP_Depend_Visitor_DefaultVisitorDummy();
        $visitor->addVisitListener($listener);

        $class->accept($visitor);

        $actual   = $listener->nodes;
        $expected = array(
            __FUNCTION__ . '#start' => true,
            __FUNCTION__ . '#end' => true,
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the default listener implementation delegates an interface
     * call to the startVisitNode() and endVisitNode() methods.
     *
     * @return void
     * @covers PHP_Depend_Visitor_AbstractListener
     * @group pdepend
     * @group pdepend::visitor
     * @group unittest
     */
    public function testListenerCallsStartNodeEndNodeForInterface()
    {
        include_once 'PHP/Depend/Code/Interface.php';

        $interface = $this->getMock(
            'PHP_Depend_Code_Interface',
            array(
                'getName', 'getSourceFile'
            ),
            array(__FUNCTION__)
        );
        $interface->expects($this->atLeastOnce())
            ->method('getName')
            ->will($this->returnValue(__FUNCTION__));
        $interface->expects($this->atLeastOnce())
            ->method('getSourceFile')
            ->will(
                $this->returnValue(
                    $this->getMock('PHP_Depend_Code_File', array(), array(null))
                )
            );

        $listener = new PHP_Depend_Visitor_TestListener();
        $visitor  = new PHP_Depend_Visitor_DefaultVisitorDummy();
        $visitor->addVisitListener($listener);

        $interface->accept($visitor);

        $actual   = $listener->nodes;
        $expected = array(
            __FUNCTION__ . '#start' => true,
            __FUNCTION__ . '#end' => true,
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the default listener implementation delegates a function
     * call to the startVisitNode() and endVisitNode() methods.
     *
     * @return void
     * @covers PHP_Depend_Visitor_AbstractListener
     * @group pdepend
     * @group pdepend::visitor
     * @group unittest
     */
    public function testListenerCallsStartNodeEndNodeForFunction()
    {
        include_once 'PHP/Depend/Code/Function.php';

        $function = $this->getMock(
            'PHP_Depend_Code_Function',
            array(
                'getName', 'getSourceFile', 'getParameters'
            ),
            array(__FUNCTION__)
        );
        $function->expects($this->atLeastOnce())
            ->method('getName')
            ->will($this->returnValue(__FUNCTION__));
        $function->expects($this->atLeastOnce())
            ->method('getSourceFile')
            ->will(
                $this->returnValue(
                    $this->getMock('PHP_Depend_Code_File', array(), array(null))
                )
            );
        $function->expects($this->atLeastOnce())
            ->method('getParameters')
            ->will($this->returnValue(array()));

        $listener = new PHP_Depend_Visitor_TestListener();
        $visitor  = new PHP_Depend_Visitor_DefaultVisitorDummy();
        $visitor->addVisitListener($listener);

        $function->accept($visitor);

        $actual   = $listener->nodes;
        $expected = array(
            __FUNCTION__ . '#start' => true,
            __FUNCTION__ . '#end' => true,
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the default listener implementation delegates a method call to
     * the startVisitNode() and endVisitNode() methods.
     *
     * @return void
     * @covers PHP_Depend_Visitor_AbstractListener
     * @group pdepend
     * @group pdepend::visitor
     * @group unittest
     */
    public function testListenerCallsStartNodeEndNodeForMethod()
    {
        include_once 'PHP/Depend/Code/Method.php';

        $method = $this->getMock(
            'PHP_Depend_Code_Method',
            array(
                'getName', 'getSourceFile', 'getParameters'
            ),
            array(__FUNCTION__)
        );
        $method->expects($this->atLeastOnce())
            ->method('getName')
            ->will($this->returnValue(__FUNCTION__));
        $method->expects($this->atLeastOnce())
            ->method('getParameters')
            ->will($this->returnValue(array()));

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
     * @covers PHP_Depend_Visitor_AbstractListener
     * @group pdepend
     * @group pdepend::visitor
     * @group unittest
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
     * @covers PHP_Depend_Visitor_AbstractListener
     * @group pdepend
     * @group pdepend::visitor
     * @group unittest
     */
    public function testListenerCallsEndVisitNodeForPassedParameterInstance()
    {
        $listener = $this->getMock('PHP_Depend_Visitor_AbstractListener', array('endVisitNode'));
        $listener->expects($this->once())
            ->method('endVisitNode');

        $parameter = $this->getMock('PHP_Depend_Code_Parameter', array(), array(null), '', false);
        $listener->endVisitParameter($parameter);
    }
}
