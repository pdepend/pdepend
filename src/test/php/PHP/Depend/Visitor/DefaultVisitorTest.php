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

/**
 * Test case for the default visitor implementation.
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
 * @covers PHP_Depend_Visitor_AbstractVisitor
 * @group pdepend
 * @group pdepend::visitor
 * @group unittest
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
        $packages = self::parseCodeResourceForTest();
        
        $visitor = new PHP_Depend_Visitor_DefaultVisitorDummy();        
        foreach ($packages as $package) {
            $package->accept($visitor);
        }
        
        $expected = array(
            'pkgA',
            'classB',
            'PHP_Depend_Code_File',
            'methodBA',
            'methodBB',
            'classA',
            'PHP_Depend_Code_File',
            'methodAB',
            'methodAA',
            'pkgB',
            'interfsC',
            'PHP_Depend_Code_File',
            'methodCB',
            'methodCA',
            'funcD',
            'PHP_Depend_Code_File'
        );
        
        self::assertEquals($expected, $visitor->visits);
    }

    /**
     * testVisitorVisitsFunctionParameter
     * 
     * @return void
     */
    public function testVisitorVisitsFunctionParameter()
    {
        $packages = self::parseCodeResourceForTest();

        $visitor = $this->getMock('PHP_Depend_Visitor_AbstractVisitor', array('visitParameter'));
        $visitor->expects($this->exactly(2))
            ->method('visitParameter');

        $visitor->visitPackage($packages->current());
    }

    /**
     * testVisitorVisitsMethodParameter
     *
     * @return void
     */
    public function testVisitorVisitsMethodParameter()
    {
        $packages = self::parseCodeResourceForTest();

        $visitor = $this->getMock('PHP_Depend_Visitor_AbstractVisitor', array('visitParameter'));
        $visitor->expects($this->exactly(3))
            ->method('visitParameter');

        $visitor->visitPackage($packages->current());
    }

    /**
     * testVisitorInvokesStartVisitParameterOnListener
     *
     * @return void
     */
    public function testVisitorInvokesStartVisitParameterOnListener()
    {
        $packages = self::parseCodeResourceForTest();
        
        $listener = $this->getMock('PHP_Depend_Visitor_ListenerI');
        $listener->expects($this->exactly(2))
            ->method('startVisitParameter');

        $visitor = $this->getMock('PHP_Depend_Visitor_AbstractVisitor', array('getVisitListeners'));
        $visitor->addVisitListener($listener);

        $visitor->visitPackage($packages->current());
    }

    /**
     * testVisitorInvokesEndVisitParameterOnListener
     *
     * @return void
     */
    public function testVisitorInvokesEndVisitParameterOnListener()
    {
        $packages = self::parseCodeResourceForTest();

        $listener = $this->getMock('PHP_Depend_Visitor_ListenerI');
        $listener->expects($this->exactly(3))
            ->method('endVisitParameter');

        $visitor = $this->getMock('PHP_Depend_Visitor_AbstractVisitor', array('getVisitListeners'));
        $visitor->addVisitListener($listener);

        $visitor->visitPackage($packages->current());
    }

    /**
     * testVisitorInvokesStartVisitInterfaceOnListener
     *
     * @return void
     */
    public function testVisitorInvokesStartVisitInterfaceOnListener()
    {
        $packages = self::parseCodeResourceForTest();

        $listener = $this->getMock('PHP_Depend_Visitor_ListenerI');
        $listener->expects($this->once())
            ->method('startVisitInterface');

        $visitor = $this->getMock('PHP_Depend_Visitor_AbstractVisitor', array('getVisitListeners'));
        $visitor->addVisitListener($listener);

        $visitor->visitPackage($packages->current());
    }

    /**
     * testVisitorInvokesEndVisitInterfaceOnListener
     *
     * @return void
     */
    public function testVisitorInvokesEndVisitInterfaceOnListener()
    {
        $packages = self::parseCodeResourceForTest();

        $listener = $this->getMock('PHP_Depend_Visitor_ListenerI');
        $listener->expects($this->once())
            ->method('endVisitInterface');

        $visitor = $this->getMock('PHP_Depend_Visitor_AbstractVisitor', array('getVisitListeners'));
        $visitor->addVisitListener($listener);

        $visitor->visitPackage($packages->current());
    }

    /**
     * testVisitorInvokesStartVisitPropertyOnListener
     *
     * @return void
     */
    public function testVisitorInvokesStartVisitPropertyOnListener()
    {
        $packages = self::parseCodeResourceForTest();

        $listener = $this->getMock('PHP_Depend_Visitor_ListenerI');
        $listener->expects($this->once())
            ->method('startVisitProperty');

        $visitor = $this->getMock('PHP_Depend_Visitor_AbstractVisitor', array('getVisitListeners'));
        $visitor->addVisitListener($listener);

        $visitor->visitPackage($packages->current());
    }

    /**
     * testVisitorInvokesEndVisitPropertyOnListener
     *
     * @return void
     */
    public function testVisitorInvokesEndVisitPropertyOnListener()
    {
        $packages = self::parseCodeResourceForTest();

        $listener = $this->getMock('PHP_Depend_Visitor_ListenerI');
        $listener->expects($this->once())
            ->method('endVisitProperty');

        $visitor = $this->getMock('PHP_Depend_Visitor_AbstractVisitor', array('getVisitListeners'));
        $visitor->addVisitListener($listener);

        $visitor->visitPackage($packages->current());
    }

    /**
     * testVisitorVisitsTrait
     *
     * @return void
     * @since 1.0.0
     */
    public function testVisitorVisitsTrait()
    {
        $package = new PHP_Depend_Code_Package('MyPackage');
        $package->addType(new PHP_Depend_Code_Trait('MyTraitOne'))
            ->setSourceFile(new PHP_Depend_Code_File(__FILE__));
        $package->addType(new PHP_Depend_Code_Trait('MyTraitTwo'))
            ->setSourceFile(new PHP_Depend_Code_File(__FILE__));

        $visitor = $this->getMock('PHP_Depend_Visitor_AbstractVisitor', array('visitTrait'));
        $visitor->expects($this->exactly(2))
            ->method('visitTrait');

        $package->accept($visitor);
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
        $trait->setSourceFile(new PHP_Depend_Code_File(__FILE__));
        $trait->addMethod($method0 = new PHP_Depend_Code_Method('m0'));
        $trait->addMethod($method1 = new PHP_Depend_Code_Method('m1'));

        $visitor = $this->getMock('PHP_Depend_Visitor_AbstractVisitor', array('visitMethod'));
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
        $trait->setSourceFile(new PHP_Depend_Code_File(__FILE__));

        $package = new PHP_Depend_Code_Package('MyPackage');
        $package->addType($trait);

        $listener = $this->getMock('PHP_Depend_Visitor_ListenerI');
        $listener->expects($this->once())
            ->method('startVisitTrait');

        $visitor = $this->getMock('PHP_Depend_Visitor_AbstractVisitor', array('getVisitListeners'));
        $visitor->addVisitListener($listener);

        $visitor->visitPackage($package);
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
        $trait->setSourceFile(new PHP_Depend_Code_File(__FILE__));

        $package = new PHP_Depend_Code_Package('MyPackage');
        $package->addType($trait);

        $listener = $this->getMock('PHP_Depend_Visitor_ListenerI');
        $listener->expects($this->once())
            ->method('endVisitTrait');

        $visitor = $this->getMock('PHP_Depend_Visitor_AbstractVisitor', array('getVisitListeners'));
        $visitor->addVisitListener($listener);

        $visitor->visitPackage($package);
    }

    /**
     * testGetVisitListenersReturnsIterator
     *
     * @return void
     */
    public function testGetVisitListenersReturnsIterator()
    {
        $visitor = $this->getMockForAbstractClass('PHP_Depend_Visitor_AbstractVisitor');
        self::assertInstanceOf('Iterator', $visitor->getVisitListeners());
    }

    /**
     * testGetVisitListenersContainsAddedListener
     *
     * @return void
     */
    public function testGetVisitListenersContainsAddedListener()
    {
        $visitor = $this->getMockForAbstractClass('PHP_Depend_Visitor_AbstractVisitor');
        $visitor->addVisitListener($this->getMock('PHP_Depend_Visitor_ListenerI'));

        self::assertEquals(1, count($visitor->getVisitListeners()));
    }
}
