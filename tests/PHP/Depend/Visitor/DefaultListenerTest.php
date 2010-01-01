<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008-2010, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
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
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Visitor_DefaultListenerTest extends PHP_Depend_AbstractTest
{
    public function testDefaultImplementationCallsListeners()
    {
        $codeUri  = self::createCodeResourceURI('visitor/' . __FUNCTION__ . '.php');
        $packages = self::parseSource($codeUri);
        $package  = $packages->current();
        
        $listener = new PHP_Depend_Visitor_TestListener();
        $visitor  = new PHP_Depend_Visitor_DefaultVisitorDummy();
        $visitor->addVisitListener($listener);
        $visitor->visitPackage($package);
        
        $this->assertArrayHasKey($codeUri . '#start', $listener->nodes);
        $this->assertArrayHasKey($codeUri . '#end', $listener->nodes);
        $this->assertArrayHasKey('package#start', $listener->nodes);
        $this->assertArrayHasKey('package#end', $listener->nodes);
        $this->assertArrayHasKey('clazz#start', $listener->nodes);
        $this->assertArrayHasKey('clazz#end', $listener->nodes);
        $this->assertArrayHasKey('func#start', $listener->nodes);
        $this->assertArrayHasKey('func#end', $listener->nodes);
        $this->assertArrayHasKey('interfs#start', $listener->nodes);
        $this->assertArrayHasKey('interfs#end', $listener->nodes);
        $this->assertArrayHasKey('m1#start', $listener->nodes);
        $this->assertArrayHasKey('m1#end', $listener->nodes);
        $this->assertArrayHasKey('m2#start', $listener->nodes);
        $this->assertArrayHasKey('m2#end', $listener->nodes);
        $this->assertArrayHasKey('m3#start', $listener->nodes);
        $this->assertArrayHasKey('m3#end', $listener->nodes);
        $this->assertArrayHasKey('m4#start', $listener->nodes);
        $this->assertArrayHasKey('m4#end', $listener->nodes);
        $this->assertArrayHasKey('$_p1#start', $listener->nodes);
        $this->assertArrayHasKey('$_p1#end', $listener->nodes);
    }

    /**
     * Tests that the default listener implementation delegates a class call
     * to the startVisitNode() and endVisitNode() methods.
     *
     * @return void
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

        $this->assertArrayHasKey(__FUNCTION__ . '#start', $listener->nodes);
        $this->assertArrayHasKey(__FUNCTION__ . '#end', $listener->nodes);
    }

    /**
     * Tests that the default listener implementation delegates an interface
     * call to the startVisitNode() and endVisitNode() methods.
     *
     * @return void
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

        $this->assertArrayHasKey(__FUNCTION__ . '#start', $listener->nodes);
        $this->assertArrayHasKey(__FUNCTION__ . '#end', $listener->nodes);
    }

    /**
     * Tests that the default listener implementation delegates a function
     * call to the startVisitNode() and endVisitNode() methods.
     *
     * @return void
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

        $this->assertArrayHasKey(__FUNCTION__ . '#start', $listener->nodes);
        $this->assertArrayHasKey(__FUNCTION__ . '#end', $listener->nodes);
    }

    /**
     * Tests that the default listener implementation delegates a method call to
     * the startVisitNode() and endVisitNode() methods.
     *
     * @return void
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

        $this->assertArrayHasKey(__FUNCTION__ . '#start', $listener->nodes);
        $this->assertArrayHasKey(__FUNCTION__ . '#end', $listener->nodes);
    }

    /**
     * Tests that the default listener implementation delegates a closure call
     * to the startVisitNode() and endVisitNode() methods.
     *
     * @return void
     */
    public function testListenerCallsStartNodeEndNodeForClosure()
    {
        include_once 'PHP/Depend/Code/Closure.php';

        $closure = $this->getMock('PHP_Depend_Code_Closure', array('getName'));
        $closure->expects($this->atLeastOnce())
            ->method('getName')
            ->will($this->returnValue(__FUNCTION__));

        $listener = new PHP_Depend_Visitor_TestListener();
        $visitor  = new PHP_Depend_Visitor_DefaultVisitorDummy();
        $visitor->addVisitListener($listener);

        $closure->accept($visitor);

        $this->assertArrayHasKey(__FUNCTION__ . '#start', $listener->nodes);
        $this->assertArrayHasKey(__FUNCTION__ . '#end', $listener->nodes);
    }
}