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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

require_once dirname(__FILE__) . '/AbstractItemTest.php';
require_once dirname(__FILE__) . '/../Visitor/TestNodeVisitor.php';

require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/Interface.php';
require_once 'PHP/Depend/Code/Method.php';
require_once 'PHP/Depend/Code/Package.php';
require_once 'PHP/Depend/Code/Property.php';
require_once 'PHP/Depend/Code/TypeConstant.php';

/**
 * Test case implementation for the PHP_Depend_Code_Class class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_Depend_Code_ClassTest extends PHP_Depend_Code_AbstractItemTest
{
    /**
     * Tests the ctor with and the {@link PHP_Depend_Code_Class::getName()} and
     * {@link PHP_Depend_Code_Class::getSourceFile()} methods.
     * 
     * @return void
     */
    public function testCreateNewClassInstance()
    {
        $class = new PHP_Depend_Code_Class('clazz', 0);
        
        $this->assertEquals('clazz', $class->getName());
    }
    
    /**
     * Tests that the default {@link PHP_Depend_Code_Class::isAbstract()}
     * value is <b>false</b> but could be changed.
     *
     * @return void
     */
    public function testMarkClassInstanceAsAbstract()
    {
        $class = new PHP_Depend_Code_Class('clazz', 0);
        
        $this->assertFalse($class->isAbstract());
        $class->setModifiers(PHP_Depend_ConstantsI::IS_EXPLICIT_ABSTRACT);
        $this->assertTrue($class->isAbstract());
    }

    /**
     * Tests that the default behavior of {@link PHP_Depend_Code_Class::isFinal()}
     * is a return value <b>false</b> that can be changed with the correct
     * modifier.
     *
     * @return void
     */
    public function testMarkClassInstanceAsFinal()
    {
        $class = new PHP_Depend_Code_Class('clazz', 0);

        $this->assertFalse($class->isFinal());
        $class->setModifiers(PHP_Depend_ConstantsI::IS_FINAL);
        $this->assertTrue($class->isFinal());
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Class::setModifiers()} when
     * it is called with an invalid modifier.
     *
     * @return void
     */
    public function testSetModifiersThrowsExpectedExceptionForInvalidModifier()
    {
        $class = new PHP_Depend_Code_Class('clazz');

        $this->setExpectedException('InvalidArgumentException');
        $class->setModifiers(PHP_Depend_ConstantsI::IS_ABSTRACT
                           | PHP_Depend_ConstantsI::IS_FINAL);
    }
    
    /**
     * Tests that a new {@link PHP_Depend_Code_Class} object returns an empty
     * {@link PHP_Depend_Code_NodeIterator} instance for methods.
     *
     * @return void
     */
    public function testGetMethodNodeIterator()
    {
        $class   = new PHP_Depend_Code_Class('clazz', 0);
        $methods = $class->getMethods();
        
        $this->assertType('PHP_Depend_Code_NodeIterator', $methods);
        $this->assertEquals(0, $methods->count());
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Class::addMethod()} method adds a
     * method to the internal list and sets the context class as parent.
     *
     * @return void
     */
    public function testAddNewMethod()
    {
        $class  = new PHP_Depend_Code_Class('clazz', 0);
        $method = new PHP_Depend_Code_Method('method', 0);
        
        $this->assertNull($method->getParent());
        $class->addMethod($method);
        $this->assertSame($class, $method->getParent());
        $this->assertEquals(1, $class->getMethods()->count());
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Class::addMethod()} reparents the
     * new method if it already has a parent class instance.
     *
     * @return void
     */
    public function testAddNewMethodAndReparent()
    {
        $class1 = new PHP_Depend_Code_Class('clazz1', 0);
        $class2 = new PHP_Depend_Code_Class('clazz2', 0);
        $method = new PHP_Depend_Code_Method('method', 0);
        
        $class1->addMethod($method);
        $this->assertSame($class1, $method->getParent());
        $this->assertSame($method, $class1->getMethods()->current());
        
        $class2->addMethod($method);
        $this->assertSame($class2, $method->getParent());
        $this->assertSame($method, $class2->getMethods()->current());
        $this->assertEquals(0, $class1->getMethods()->count());
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Class::getPackage()} returns as
     * default value <b>null</b> and that the package could be set and unset.
     *
     * @return void
     */
    public function testGetSetPackage()
    {
        $package = new PHP_Depend_Code_Package('package');
        $class   = new PHP_Depend_Code_Class('clazz');
        
        $this->assertNull($class->getPackage());
        $class->setPackage($package);
        $this->assertSame($package, $class->getPackage());
        $class->setPackage(null);
        $this->assertNull($class->getPackage());
    }
    
    /**
     * Tests that {@link PHP_Depend_Code_Class#getStartLine()} works as expected.
     * 
     * @return void
     */
    public function testGetStartLineNumber()
    {
        $class = new PHP_Depend_Code_Class('foo');
        $class->setStartLine(42);
        
        $this->assertEquals(42, $class->getStartLine());
    }
    
    /**
     * Tests that {@link PHP_Depend_Code_Class::getInterfaces()}
     * returns the expected result.
     *
     * @return void
     */
    public function testGetInterfaces()
    {
        $packages = self::parseSource('code/class/' . __FUNCTION__ . '.php');

        $class = $packages->current()
            ->getClasses()
            ->current();

        $expected = array('A' => 'A', 'C' => 'C', 'E' => 'E', 'F' => 'F');

        $interfaces = $class->getInterfaces();
        $this->assertSame(4, $interfaces->count());

        foreach ($interfaces as $interface) {
            $this->assertArrayHasKey($interface->getName(), $expected);
            unset($expected[$interface->getName()]);
        }
    }
    
    /**
     * Tests that {@link PHP_Depend_Code_Class::getInterfaces()}
     * returns the expected result.
     *
     * @return void
     */
    public function testGetInterfacesByInheritence()
    {
        $packages = self::parseSource('code/class/' . __FUNCTION__ . '.php');

        $class = $packages->current()
            ->getClasses()
            ->current();

        $expected = array(
            'A' => 'A',
            'B' => 'B',
            'C' => 'C',
            'D' => 'D',
            'E' => 'E',
            'F' => 'F'
        );

        $interfaces = $class->getInterfaces();
        $this->assertSame(count($expected), $interfaces->count());

        foreach ($interfaces as $interface) {
            $this->assertArrayHasKey($interface->getName(), $expected);
            unset($expected[$interface->getName()]);
        }
    }

    /**
     * Tests that {@link PHP_Depend_Code_Class::getInterfaces()}
     * returns the expected result.
     *
     * @return void
     */
    public function testGetInterfacesByClassInheritence()
    {
        $packages = self::parseSource('code/class/' . __FUNCTION__ . '.php');

        $class = $packages->current()
            ->getClasses()
            ->current();

        $expected = array(
            'A' => 'A',
            'B' => 'B',
        );

        $interfaces = $class->getInterfaces();
        $this->assertSame(count($expected), $interfaces->count());

        foreach ($interfaces as $interface) {
            $this->assertArrayHasKey($interface->getName(), $expected);
            unset($expected[$interface->getName()]);
        }
    }
    
    /**
     * Checks the {@link PHP_Depend_Code_Class::isSubtypeOf()} method.
     *
     * @return void
     */
    public function testIsSubtypeInInheritanceHierarchy()
    {
        $packages = self::parseSource('code/class/' . __FUNCTION__ . '.php');
        $package  = $packages->current();

        $class = $package->getClasses()
            ->current();

        $expected = array(
            'A' => true,
            'B' => false,
            'C' => false,
            'D' => true,
            'E' => true,
            'F' => false
        );

        foreach ($package->getTypes() as $classOrInterface) {
            $this->assertArrayHasKey($classOrInterface->getName(), $expected);
            $this->assertSame(
                $expected[$classOrInterface->getName()],
                $class->isSubtypeOf($classOrInterface)
            );
        }
    }

    /**
     * Checks the {@link PHP_Depend_Code_Class::isSubtypeOf()} method.
     *
     * @return void
     */
    public function testIsSubtypeInClassInheritanceHierarchy()
    {
        $packages = self::parseSource('code/class/' . __FUNCTION__ . '.php');
        $package  = $packages->current();

        $class = $package->getClasses()
            ->current();

        $expected = array(
            'A' => true,
            'B' => true,
            'C' => false,
            'D' => true,
            'E' => true,
            'F' => false
        );

        foreach ($package->getTypes() as $classOrInterface) {
            $this->assertArrayHasKey($classOrInterface->getName(), $expected);
            $this->assertSame(
                $expected[$classOrInterface->getName()],
                $class->isSubtypeOf($classOrInterface)
            );
        }
    }

    /**
     * Checks the {@link PHP_Depend_Code_Class::isSubtypeOf()} method.
     *
     * @return void
     */
    public function testIsSubtypeInClassAndInterfaceInheritanceHierarchy()
    {
        $packages = self::parseSource('code/class/' . __FUNCTION__ . '.php');
        $package  = $packages->current();

        $class = $package->getClasses()
            ->current();

        $expected = array(
            'A' => true,
            'B' => true,
            'C' => true,
            'D' => true,
            'E' => true,
            'F' => true
        );

        foreach ($package->getTypes() as $classOrInterface) {
            $this->assertArrayHasKey($classOrInterface->getName(), $expected);
            $this->assertSame(
                $expected[$classOrInterface->getName()],
                $class->isSubtypeOf($classOrInterface)
            );
        }
    }

    /**
     * Tests that it is not possible to overwrite previously set class modifiers.
     *
     * @return void
     */
    public function testSetModifiersThrowsExpectedExceptionOnOverwrite()
    {
        $class = new PHP_Depend_Code_Class('FooBar');
        $class->setModifiers(PHP_Depend_ConstantsI::IS_FINAL);

        $this->setExpectedException(
            'BadMethodCallException',
            'Cannot overwrite previously set class modifiers.'
        );

        $class->setModifiers(PHP_Depend_ConstantsI::IS_FINAL);
    }
    
    /**
     * Tests the visitor accept method.
     *
     * @return void
     */
    public function testVisitorAccept()
    {
        $class   = new PHP_Depend_Code_Class('clazz', 0);
        $visitor = new PHP_Depend_Visitor_TestNodeVisitor();
        
        $this->assertNull($visitor->class);
        $class->accept($visitor);
        $this->assertSame($class, $visitor->class);
        
    }
    
    /**
     * Creates an abstract item instance.
     *
     * @return PHP_Depend_Code_AbstractItem
     */
    protected function createItem()
    {
        return new PHP_Depend_Code_Class('clazz', 0);
    }
}
