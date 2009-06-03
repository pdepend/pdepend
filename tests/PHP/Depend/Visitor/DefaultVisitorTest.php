<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';
require_once dirname(__FILE__) . '/DefaultVisitorDummy.php';

require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/File.php';
require_once 'PHP/Depend/Code/Function.php';
require_once 'PHP/Depend/Code/Interface.php';
require_once 'PHP/Depend/Code/Method.php';
require_once 'PHP/Depend/Code/NodeIterator.php';
require_once 'PHP/Depend/Code/Package.php';
require_once 'PHP/Depend/Code/Property.php';

/**
 * Test case for the default visitor implementation.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Visitor_DefaultVisitorTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests the execution order of the default visitor implementation.
     *
     * @return void
     */
    public function testDefaultVisitOrder()
    {
        $codeUri  = self::createCodeResourceURI('visitor/' . __FUNCTION__ . '.php');
        $packages = self::parseSource($codeUri);
        
        $visitor = new PHP_Depend_Visitor_DefaultVisitorDummy();        
        foreach ($packages as $package) {
            $package->accept($visitor);
        }
        
        $expected = array(
            'pkgA',
            'classA',
            $codeUri,
            'methodAA',
            'methodAB',
            'classB',
            $codeUri,
            'methodBA',
            'methodBB',
            'pkgB',
            'interfsC',
            $codeUri,
            'methodCA',
            'methodCB',
            'funcD',
            $codeUri
        );
        
        $this->assertEquals($expected, $visitor->visits);
    }

    /**
     * Tests that the default visitor implementation emits the expected signals
     * for a closure.
     *
     * @return void
     */
    public function testClosureHandlerEmitExpectedListenerSignal()
    {
        include_once 'PHP/Depend/Code/Closure.php';
        include_once 'PHP/Depend/Visitor/ListenerI.php';

        $listener = $this->getMock('PHP_Depend_Visitor_ListenerI');
        $listener->expects($this->at(0))
                 ->method('startVisitClosure');
        $listener->expects($this->at(1))
                 ->method('endVisitClosure');

        $closure = $this->getMock('PHP_Depend_Code_Closure');

        $visitor = new PHP_Depend_Visitor_DefaultVisitorDummy();
        $visitor->addVisitListener($listener);
        $visitor->visitClosure($closure);
    }

    /**
     * Tests that the visitor emits the expected constant signal.
     *
     * @return void
     */
    public function testConstantHandlerEmitsExpectedListenerSignal()
    {
        include_once 'PHP/Depend/Code/TypeConstant.php';
        include_once 'PHP/Depend/Visitor/ListenerI.php';
        include_once 'PHP/Depend/Code/Closure.php';
        include_once 'PHP/Depend/Visitor/ListenerI.php';

        $listener = $this->getMock('PHP_Depend_Visitor_ListenerI');
        $listener->expects($this->at(0))
                 ->method('startVisitTypeConstant');
        $listener->expects($this->at(1))
                 ->method('endVisitTypeConstant');

        $constant = $this->getMock('PHP_Depend_Code_TypeConstant', array(), array('FOO'));

        $visitor = new PHP_Depend_Visitor_DefaultVisitorDummy();
        $visitor->addVisitListener($listener);
        $visitor->visitTypeConstant($constant);
    }

    /**
     * Tests that the visitor emits the expected signals for a class constant.
     *
     * @return void
     */
    public function testVisitorEmitsExpectedSignalsForClassConstant()
    {
        include_once 'PHP/Depend/Code/Class.php';
        include_once 'PHP/Depend/Code/File.php';
        include_once 'PHP/Depend/Code/TypeConstant.php';
        include_once 'PHP/Depend/Visitor/ListenerI.php';
        include_once 'PHP/Depend/Visitor/ListenerI.php';

        $listener = $this->getMock('PHP_Depend_Visitor_ListenerI');
        $listener->expects($this->at(0))
                 ->method('startVisitTypeConstant');
        $listener->expects($this->at(1))
                 ->method('endVisitTypeConstant');

        $class = $this->getMock(
            'PHP_Depend_Code_Class',
            array('getSourceFile', 'getConstants'),
            array('Clazz')
        );
        $class->expects($this->once())
            ->method('getSourceFile')
            ->will(
                $this->returnValue(
                    $this->getMock('PHP_Depend_Code_File', array(), array(null))
                )
            );
        $class->expects($this->once())
            ->method('getConstants')
            ->will(
                $this->returnValue(
                    array(
                        $this->getMock(
                            'PHP_Depend_Code_TypeConstant',
                            array(),
                            array('FOO')
                        )
                    )
                )
            );

        $visitor = new PHP_Depend_Visitor_DefaultVisitorDummy();
        $visitor->addVisitListener($listener);
        $class->accept($visitor);
    }

    /**
     * Tests that the visitor emits the expected signals for an interface constant.
     *
     * @return void
     */
    public function testVisitorEmitsExpectedSignalsForInterfaceConstant()
    {
        include_once 'PHP/Depend/Code/File.php';
        include_once 'PHP/Depend/Code/Interface.php';
        include_once 'PHP/Depend/Code/TypeConstant.php';
        include_once 'PHP/Depend/Visitor/ListenerI.php';
        include_once 'PHP/Depend/Visitor/ListenerI.php';

        $listener = $this->getMock('PHP_Depend_Visitor_ListenerI');
        $listener->expects($this->at(0))
                 ->method('startVisitTypeConstant');
        $listener->expects($this->at(1))
                 ->method('endVisitTypeConstant');

        $interface = $this->getMock(
            'PHP_Depend_Code_Interface',
            array('getSourceFile', 'getConstants'),
            array('Clazz')
        );
        $interface->expects($this->once())
            ->method('getSourceFile')
            ->will(
                $this->returnValue(
                    $this->getMock('PHP_Depend_Code_File', array(), array(null))
                )
            );
        $interface->expects($this->once())
            ->method('getConstants')
            ->will(
                $this->returnValue(
                    array(
                        $this->getMock(
                            'PHP_Depend_Code_TypeConstant',
                            array(),
                            array('FOO')
                        )
                    )
                )
            );

        $visitor = new PHP_Depend_Visitor_DefaultVisitorDummy();
        $visitor->addVisitListener($listener);
        $interface->accept($visitor);
    }

}