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
require_once dirname(__FILE__) . '/../Visitor/TestNodeVisitor.php';

require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/Interface.php';
require_once 'PHP/Depend/Code/Method.php';
require_once 'PHP/Depend/Code/Package.php';
require_once 'PHP/Depend/Code/Property.php';
require_once 'PHP/Depend/Code/ASTFormalParameter.php';
require_once 'PHP/Depend/Code/ASTVariableDeclarator.php';

/**
 * Test case implementation for the PHP_Depend_Code_Class class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_Depend_Code_ClassTest extends PHP_Depend_Code_AbstractItemTest
{
    /**
     * testGetAllMethodsContainsMethodsOfImplementedInterface
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetAllMethodsContainsMethodsOfImplementedInterface()
    {
        $class = self::parseTestCaseSource(__METHOD__)
            ->current()
            ->getClasses()
            ->current();

        $actual   = array_keys($class->getAllMethods());
        $expected = array('foo', 'bar', 'baz');

        sort($actual);
        sort($expected);

        $this->assertEquals($expected, $actual);
    }

    /**
     * testGetAllMethodsContainsMethodsOfImplementedInterfaces
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetAllMethodsContainsMethodsOfImplementedInterfaces()
    {
        $class = self::parseTestCaseSource(__METHOD__)
            ->current()
            ->getClasses()
            ->current();

        $actual   = array_keys($class->getAllMethods());
        $expected = array('foo', 'bar', 'baz');

        sort($actual);
        sort($expected);

        $this->assertEquals($expected, $actual);
    }

    /**
     * testGetAllMethodsContainsMethodsOfIndirectImplementedInterfaces
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetAllMethodsContainsMethodsOfIndirectImplementedInterfaces()
    {
        $class = self::parseTestCaseSource(__METHOD__)
            ->current()
            ->getClasses()
            ->current();

        $actual   = array_keys($class->getAllMethods());
        $expected = array('foo', 'bar', 'baz');

        sort($actual);
        sort($expected);

        $this->assertEquals($expected, $actual);
    }

    /**
     * testGetAllMethodsContainsMethodsOfParentClass
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetAllMethodsContainsMethodsOfParentClass()
    {
        $class = self::parseTestCaseSource(__METHOD__)
            ->current()
            ->getClasses()
            ->current();

        $actual   = array_keys($class->getAllMethods());
        $expected = array('foo', 'bar', 'baz');

        sort($actual);
        sort($expected);

        $this->assertEquals($expected, $actual);
    }

    /**
     * testGetAllMethodsContainsMethodsOfParentClasses
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetAllMethodsContainsMethodsOfParentClasses()
    {
        $class = self::parseTestCaseSource(__METHOD__)
            ->current()
            ->getClasses()
            ->current();

        $actual   = array_keys($class->getAllMethods());
        $expected = array('foo', 'bar', 'baz');

        sort($actual);
        sort($expected);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Method::getFirstChildOfType()}.
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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

        $class = new PHP_Depend_Code_Class('Clazz');
        $class->addChild($node1);
        $class->addChild($node2);

        $child = $class->getFirstChildOfType(get_class($node2));
        $this->assertSame($node2, $child);
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Method::getFirstChildOfType()}.
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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

        $class = new PHP_Depend_Code_Class('Clazz');
        $class->addChild($node2);
        $class->addChild($node3);

        $child = $class->getFirstChildOfType(get_class($node1));
        $this->assertSame($node1, $child);
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Method::getFirstChildOfType()}.
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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

        $class = new PHP_Depend_Code_Class('Clazz');
        $class->addChild($node1);
        $class->addChild($node2);

        $child = $class->getFirstChildOfType('PHP_Depend_Code_ASTNodeI_' . md5(microtime()));
        $this->assertNull($child);
    }

    /**
     * testGetFirstChildOfTypeFindsASTNodeInMethodDeclaration
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetFirstChildOfTypeFindsASTNodeInMethodDeclaration()
    {
        $packages = self::parseTestCaseSource(__METHOD__);

        $class = $packages->current()
            ->getClasses()
            ->current();

        $parameter = $class->getFirstChildOfType(PHP_Depend_Code_ASTFormalParameter::CLAZZ);
        $this->assertType(PHP_Depend_Code_ASTFormalParameter::CLAZZ, $parameter);
    }

    /**
     * testGetFirstChildOfTypeFindsASTNodeInMethodDeclaration
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testFindChildrenOfTypeFindsASTNodeInMethodDeclarations()
    {
        $packages = self::parseTestCaseSource(__METHOD__);

        $class = $packages->current()
            ->getClasses()
            ->current();

        $parameters = $class->findChildrenOfType(PHP_Depend_Code_ASTFormalParameter::CLAZZ);
        $this->assertEquals(4, count($parameters));
    }

    /**
     * testFindChildrenOfTypeFindsASTNodesFromVariousCodeItems
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testFindChildrenOfTypeFindsASTNodesFromVariousCodeItems()
    {
        $packages = self::parseTestCaseSource(__METHOD__);

        $class = $packages->current()
            ->getClasses()
            ->current();

        $parameters = $class->findChildrenOfType(PHP_Depend_Code_ASTVariableDeclarator::CLAZZ);
        $this->assertEquals(2, count($parameters));
    }

    /**
     * Tests the ctor and the {@link PHP_Depend_Code_Class::getName()}.
     * 
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @covers PHP_Depend_Code_Class
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @covers PHP_Depend_Code_Class
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @covers PHP_Depend_Code_Class
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @covers PHP_Depend_Code_Class
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @covers PHP_Depend_Code_Class
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetMethodNodeIterator()
    {
        $class   = new PHP_Depend_Code_Class('clazz', 0);
        $methods = $class->getMethods();
        
        $this->assertEquals(0, $methods->count());
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Class::addMethod()} method adds a
     * method to the internal list and sets the context class as parent.
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @covers PHP_Depend_Code_Class
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * Tests that the {@link PHP_Depend_Code_Class::getPackage()} returns as
     * default value <b>null</b> and that the package could be set and unset.
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @covers PHP_Depend_Code_Class
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * Tests that {@link PHP_Depend_Code_Class::getInterfaces()}
     * returns the expected result.
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @covers PHP_Depend_Code_Class
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetInterfaces()
    {
        $packages = self::parseSource('code/Class/' . __FUNCTION__ . '.php');

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
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @covers PHP_Depend_Code_Class
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetInterfacesByInheritence()
    {
        $packages = self::parseSource('code/Class/' . __FUNCTION__ . '.php');

        $classes = $packages->current()
            ->getClasses();

        $classes->next();
        $class = $classes->current();

        $expected = array(
            'A' => 'A',
            'B' => 'B',
            'C' => 'C',
            'D' => 'D',
            'E' => 'E',
            'F' => 'F'
        );

        $interfaces = $class->getInterfaces();
        $this->assertEquals(count($expected), $interfaces->count());

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
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @covers PHP_Depend_Code_Class
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetInterfacesByClassInheritence()
    {
        $packages = self::parseSource('code/Class/' . __FUNCTION__ . '.php');

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
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @covers PHP_Depend_Code_Class
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testIsSubtypeInInheritanceHierarchy()
    {
        $packages = self::parseSource('code/Class/' . __FUNCTION__ . '.php');

        $class = $packages->current()
            ->getClasses()
            ->current();

        $actual = array();
        foreach ($packages->current()->getTypes() as $classOrInterface) {
            $actual[$classOrInterface->getName()] = $class->isSubtypeOf($classOrInterface);
        }
        ksort($actual);

        $expected = array(
            'A' => true,
            'B' => false,
            'C' => false,
            'D' => true,
            'E' => true,
            'F' => false
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * Checks the {@link PHP_Depend_Code_Class::isSubtypeOf()} method.
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @covers PHP_Depend_Code_Class
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testIsSubtypeInClassInheritanceHierarchy()
    {
        $packages = self::parseSource('code/Class/' . __FUNCTION__ . '.php');

        $class = $packages->current()
            ->getClasses()
            ->current();

        $actual = array();
        foreach ($packages->current()->getTypes() as $classOrInterface) {
            $actual[$classOrInterface->getName()] = $class->isSubtypeOf($classOrInterface);
        }
        ksort($actual);

        $expected = array(
            'A' => true,
            'B' => true,
            'C' => false,
            'D' => true,
            'E' => true,
            'F' => false
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * Checks the {@link PHP_Depend_Code_Class::isSubtypeOf()} method.
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @covers PHP_Depend_Code_Class
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testIsSubtypeInClassAndInterfaceInheritanceHierarchy()
    {
        $packages = self::parseSource('code/Class/' . __FUNCTION__ . '.php');

        $class = $packages->current()
            ->getClasses()
            ->current();

        $actual = array();
        foreach ($packages->current()->getTypes() as $classOrInterface) {
            $actual[$classOrInterface->getName()] = $class->isSubtypeOf($classOrInterface);
        }
        ksort($actual);

        $expected = array(
            'A' => true,
            'B' => true,
            'C' => true,
            'D' => true,
            'E' => true,
            'F' => true
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testFreeResetsAllAssociatedProperties
     *
     * @return void
     * @covers PHP_Depend_Code_Class
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testFreeResetsAllAssociatedProperties()
    {
        $packages = self::parseSource('code/Class/' . __FUNCTION__ . '.php');

        $class = $packages->current()->getClasses()->current();
        $class->free();

        $this->assertEquals(0, $class->getProperties()->count());
    }

    /**
     * testFreeResetsAllAssociatedParentInterfaces
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @covers PHP_Depend_Code_Class
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testFreeResetsAllAssociatedParentInterfaces()
    {
        $packages = self::parseSource('code/Class/' . __FUNCTION__ . '.php');

        $class = $packages->current()->getClasses()->current();
        $class->free();

        $this->assertEquals(0, $class->getInterfaces()->count());
    }

    /**
     * testFreeResetsAllAssociatedClassMethods
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @covers PHP_Depend_Code_Class
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testFreeResetsAllAssociatedClassMethods()
    {
        $packages = self::parseSource('code/Class/' . __FUNCTION__ . '.php');

        $class = $packages->current()->getClasses()->current();
        $class->free();

        $this->assertEquals(0, $class->getMethods()->count());
    }

    /**
     * testFreeResetsAllAssociatedASTNodes
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @covers PHP_Depend_Code_Class
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testFreeResetsAllAssociatedASTNodes()
    {
        $packages = self::parseSource('code/Class/' . __FUNCTION__ . '.php');

        $class = $packages->current()->getClasses()->current();
        $class->free();

        $this->assertEquals(array(), $class->getChildren());
    }

    /**
     * Tests that it is not possible to overwrite previously set class modifiers.
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @covers PHP_Depend_Code_Class
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     * @expectedException BadMethodCallException
     */
    public function testSetModifiersThrowsExpectedExceptionOnOverwrite()
    {
        $class = new PHP_Depend_Code_Class('FooBar');
        $class->setModifiers(PHP_Depend_ConstantsI::IS_FINAL);
        $class->setModifiers(PHP_Depend_ConstantsI::IS_FINAL);
    }
    
    /**
     * Tests the visitor accept method.
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @covers PHP_Depend_Code_Class
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testVisitorAccept()
    {
        $class   = new PHP_Depend_Code_Class('clazz', 0);
        $visitor = new PHP_Depend_Visitor_TestNodeVisitor();

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
