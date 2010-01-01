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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

require_once dirname(__FILE__) . '/AbstractItemTest.php';

require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/ASTClassReference.php';
require_once 'PHP/Depend/Code/Interface.php';

/**
 * Test case for the code interface class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_Depend_Code_InterfaceTest extends PHP_Depend_Code_AbstractItemTest
{
    /**
     * Tests the behavior of {@link PHP_Depend_Code_Method::getFirstChildOfType()}.
     *
     * @return void
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedFirstMatch()
    {
        $node1 = $this->getMock(
            'PHP_Depend_Code_ASTNodeI',
            array(),
            array(),
            'PHP_Depend_Code_ASTNodeI_' . md5(microtime())
        );
        $node1->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $node2 = $this->getMock(
            'PHP_Depend_Code_ASTNodeI',
            array(),
            array(),
            'PHP_Depend_Code_ASTNodeI_' . md5(microtime())
        );
        $node2->expects($this->never())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $interface = new PHP_Depend_Code_Interface('Interface');
        $interface->addChild($node1);
        $interface->addChild($node2);

        $child = $interface->getFirstChildOfType(get_class($node2));
        $this->assertSame($node2, $child);
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Method::getFirstChildOfType()}.
     *
     * @return void
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedNestedMatch()
    {
        $node1 = $this->getMock(
            'PHP_Depend_Code_ASTNodeI',
            array(),
            array(),
            'PHP_Depend_Code_ASTNodeI_' . md5(microtime())
        );
        $node1->expects($this->never())
            ->method('getFirstChildOfType');

        $node2 = $this->getMock(
            'PHP_Depend_Code_ASTNodeI',
            array(),
            array(),
            'PHP_Depend_Code_ASTNodeI_' . md5(microtime())
        );
        $node2->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $node3 = $this->getMock(
            'PHP_Depend_Code_ASTNodeI',
            array(),
            array(),
            'PHP_Depend_Code_ASTNodeI_' . md5(microtime())
        );
        $node3->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue($node1));

        $interface = new PHP_Depend_Code_Interface('Interface');
        $interface->addChild($node2);
        $interface->addChild($node3);

        $child = $interface->getFirstChildOfType(get_class($node1));
        $this->assertSame($node1, $child);
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Method::getFirstChildOfType()}.
     *
     * @return void
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedNull()
    {
        $node1 = $this->getMock(
            'PHP_Depend_Code_ASTNodeI',
            array(),
            array(),
            'PHP_Depend_Code_ASTNodeI_' . md5(microtime())
        );
        $node1->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $node2 = $this->getMock(
            'PHP_Depend_Code_ASTNodeI',
            array(),
            array(),
            'PHP_Depend_Code_ASTNodeI_' . md5(microtime())
        );
        $node2->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $interface = new PHP_Depend_Code_Interface('Interface');
        $interface->addChild($node1);
        $interface->addChild($node2);

        $child = $interface->getFirstChildOfType('PHP_Depend_Code_ASTNodeI_' . md5(microtime()));
        $this->assertNull($child);
    }

    /**
     * Tests the result of the <b>getInterfaces()</b> method.
     *
     * @return void
     */
    public function testGetInterfacesZeroInheritance()
    {
        $packages = self::parseSource('code/interface/' . __FUNCTION__ . '.php');
        $package  = $packages->current();

        $interface = $package->getInterfaces()
            ->current();

        $this->assertSame(0, $interface->getInterfaces()->count());
    }

    /**
     * Tests the result of the <b>getInterfaces()</b> method.
     *
     * @return void
     */
    public function testGetInterfacesOneLevelInheritance()
    {
        $packages = self::parseSource('code/interface/' . __FUNCTION__ . '.php');
        $package  = $packages->current();

        $interface = $package->getInterfaces()
            ->current();

        $this->assertSame(1, $interface->getInterfaces()->count());
    }

    /**
     * Tests the result of the <b>getInterfaces()</b> method.
     *
     * @return void
     */
    public function testGetInterfacesTwoLevelInheritance()
    {
        $packages = self::parseSource('code/interface/' . __FUNCTION__ . '.php');
        $package  = $packages->current();

        $interface = $package->getInterfaces()
            ->current();

        $this->assertSame(4, $interface->getInterfaces()->count());
    }

    /**
     * Tests the result of the <b>getInterfaces()</b> method.
     *
     * @return void
     */
    public function testGetInterfacesComplexInheritance()
    {
        $packages = self::parseSource('code/interface/' . __FUNCTION__ . '.php');
        $package  = $packages->current();

        $interface = $package->getInterfaces()
            ->current();

        $this->assertSame(5, $interface->getInterfaces()->count());
    }
    
    /**
     * Tests that {@link PHP_Depend_Code_Interface::isSubtypeOf()} returns
     * <b>false</b> for an input class.
     *
     * @return void
     */
    public function testIsSubtypeOfReturnsFalseForNonParents()
    {
        $packages = self::parseSource('code/interface/' . __FUNCTION__ . '.php');
        $package  = $packages->current();

        $interfaces = $package->getInterfaces();
        $interface  = $interfaces->current();

        $interfaces->next();
        while ($interfaces->valid()) {
            $this->assertFalse(
                $interface->isSubtypeOf($interfaces->current())
            );
            $interfaces->next();
        }
    }
    
    /**
     * Checks the {@link PHP_Depend_Code_Interface::isSubtypeOf()} method.
     *
     * @return void
     */
    public function testIsSubtypeOnInheritanceHierarchy()
    {
        $packages = self::parseSource('code/interface/' . __FUNCTION__ . '.php');
        $package  = $packages->current();
        
        $expected = array(
            'A' => true,
            'B' => false,
            'C' => false,
            'D' => false,
            'E' => false,
            'F' => true,
        );

        $current = $package->getInterfaces()->current();
        foreach ($package->getInterfaces() as $interface) {
            $this->assertSame(
                $expected[$interface->getName()],
                $current->isSubtypeOf($interface)
            );
        }
    }

    /**
     * Checks the {@link PHP_Depend_Code_Interface::isSubtypeOf()} method.
     *
     * @return void
     */
    public function testIsSubtypeOnInheritanceHierarchy1()
    {
        $packages = self::parseSource('code/interface/' . __FUNCTION__ . '.php');
        $package  = $packages->current();

        $expected = array(
            'A' => true,
            'B' => true,
            'C' => true,
            'D' => true,
            'E' => true,
            'F' => true,
        );

        $current = $package->getInterfaces()->current();
        foreach ($package->getInterfaces() as $interface) {
            $this->assertSame(
                $expected[$interface->getName()],
                $current->isSubtypeOf($interface)
            );
        }
    }

    /**
     * Checks the {@link PHP_Depend_Code_Interface::isSubtypeOf()} method.
     *
     * @return void
     */
    public function testIsSubtypeOnInheritanceHierarchy2()
    {
        $packages = self::parseSource('code/interface/' . __FUNCTION__ . '.php');
        $package  = $packages->current();

        $expected = array(
            'B' => false,
            'C' => false,
            'A' => true,
            'D' => true,
            'E' => true,
            'F' => false,
        );

        $current = $package->getInterfaces()->current();
        foreach ($package->getInterfaces() as $interface) {
            $this->assertSame(
                $expected[$interface->getName()],
                $current->isSubtypeOf($interface)
            );
        }
    }

    /**
     * Checks the {@link PHP_Depend_Code_Interface::isSubtypeOf()} method.
     *
     * @return void
     */
    public function testIsSubtypeOnInheritanceHierarchy3()
    {
        $packages = self::parseSource('code/interface/' . __FUNCTION__ . '.php');
        $package  = $packages->current();

        $expected = array(
            'B' => false,
            'C' => false,
            'D' => false,
            'A' => true,
            'E' => false,
            'F' => false,
        );

        $current = $package->getInterfaces()->current();
        foreach ($package->getInterfaces() as $interface) {
            $this->assertSame(
                $expected[$interface->getName()],
                $current->isSubtypeOf($interface)
            );
        }
    }

    /**
     * Tests that the interface implementation overwrites the
     * setParentClassReference() method and throws an exception.
     *
     * @return void
     */
    public function testInterfaceThrowsExpectedExceptionOnSetParentClassReference()
    {
        $interface = new PHP_Depend_Code_Interface('IFooBar');

        $this->setExpectedException(
            'BadMethodCallException',
            'Unsupported method PHP_Depend_Code_Interface::setParentClassReference() called.'
        );

        $interface->setParentClassReference(
            $this->getMock('PHP_Depend_Code_ASTClassReference', array(), array(), '', false)
        );
    }

    /**
     * Tests the returned modifiers of an interface.
     *
     * @return void
     */
    public function testInterfaceReturnsExpectedModifiers()
    {
        $interface = new PHP_Depend_Code_Interface('Foo');
        $this->assertSame(
            PHP_Depend_ConstantsI::IS_IMPLICIT_ABSTRACT,
            $interface->getModifiers()
        );
    }
    
    /**
     * Creates an abstract item instance.
     *
     * @return PHP_Depend_Code_AbstractItem
     */
    protected function createItem()
    {
        return new PHP_Depend_Code_Interface('interfs');
    }
}