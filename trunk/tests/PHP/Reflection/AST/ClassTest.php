<?php
/**
 * This file is part of PHP_Reflection.
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
 * @package    PHP_Reflection
 * @subpackage AST
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/AbstractSourceElementTest.php';
require_once dirname(__FILE__) . '/_dummy/TestImplAstVisitor.php';

require_once 'PHP/Reflection/AST/Class.php';
require_once 'PHP/Reflection/AST/Interface.php';
require_once 'PHP/Reflection/AST/Method.php';
require_once 'PHP/Reflection/AST/Package.php';
require_once 'PHP/Reflection/AST/Property.php';
require_once 'PHP/Reflection/AST/ClassOrInterfaceConstant.php';

/**
 * Test case implementation for the PHP_Reflection_AST_Class class.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage AST
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_AST_ClassTest extends PHP_Reflection_AST_AbstractSourceElementTest
{
    /**
     * Tests the ctor with and the {@link PHP_Reflection_AST_Class::getName()} and
     * {@link PHP_Reflection_AST_Class::getSourceFile()} methods.
     * 
     * @return void
     */
    public function testCreateNewClassInstance()
    {
        $class = new PHP_Reflection_AST_Class('clazz', 0);
        
        $this->assertEquals('clazz', $class->getName());
    }
    
    /**
     * Tests that the default {@link PHP_Reflection_AST_Class::isAbstract()}
     * value is <b>false</b> but could be changed.
     *
     * @return void
     */
    public function testMarkClassInstanceAsAbstract()
    {
        $class = new PHP_Reflection_AST_Class('clazz', 0);
        
        $this->assertFalse($class->isAbstract());
        $class->setModifiers(ReflectionClass::IS_EXPLICIT_ABSTRACT);
        $this->assertTrue($class->isAbstract());
        $class->setModifiers(0);
        $this->assertFalse($class->isAbstract());
    }
    
    /**
     * Tests that the default {@link PHP_Reflection_AST_Class::isFinal()}
     * value is <b>false</b> but could be changed.
     *
     * @return void
     */
    public function testMarkClassInstanceAsFinal()
    {
        $class = new PHP_Reflection_AST_Class('clazz', 0);
        
        $this->assertFalse($class->isFinal());
        $class->setModifiers(ReflectionClass::IS_FINAL);
        $this->assertTrue($class->isFinal());
        $class->setModifiers(0);
        $this->assertFalse($class->isFinal());
    }
    
    /**
     * Tests that a new {@link PHP_Reflection_AST_Class} object returns an empty
     * {@link PHP_Reflection_AST_Iterator} instance for methods.
     *
     * @return void
     */
    public function testGetMethodIterator()
    {
        $class   = new PHP_Reflection_AST_Class('clazz', 0);
        $methods = $class->getMethods();
        
        $this->assertType('PHP_Reflection_AST_Iterator', $methods);
        $this->assertEquals(0, $methods->count());
    }
    
    /**
     * Tests that the {@link PHP_Reflection_AST_Class::addMethod()} method adds a
     * method to the internal list and sets the context class as parent.
     *
     * @return void
     */
    public function testAddNewMethod()
    {
        $class  = new PHP_Reflection_AST_Class('clazz', 0);
        $method = new PHP_Reflection_AST_Method('method', 0);
        
        $this->assertNull($method->getParent());
        $class->addMethod($method);
        $this->assertSame($class, $method->getParent());
        $this->assertEquals(1, $class->getMethods()->count());
    }
    
    /**
     * Tests that the {@link PHP_Reflection_AST_Class::addMethod()} reparents the
     * new method if it already has a parent class instance.
     *
     * @return void
     */
    public function testAddNewMethodAndReparent()
    {
        $class1 = new PHP_Reflection_AST_Class('clazz1', 0);
        $class2 = new PHP_Reflection_AST_Class('clazz2', 0);
        $method = new PHP_Reflection_AST_Method('method', 0);
        
        $class1->addMethod($method);
        $this->assertSame($class1, $method->getParent());
        $this->assertSame($method, $class1->getMethods()->current());
        
        $class2->addMethod($method);
        $this->assertSame($class2, $method->getParent());
        $this->assertSame($method, $class2->getMethods()->current());
        $this->assertEquals(0, $class1->getMethods()->count());
    }
    
    /**
     * Tests that the {@link PHP_Reflection_AST_Class::getPackage()} returns as
     * default value <b>null</b> and that the package could be set and unset.
     *
     * @return void
     */
    public function testGetSetPackage()
    {
        $package = new PHP_Reflection_AST_Package('package');
        $class   = new PHP_Reflection_AST_Class('clazz', 0);
        
        $this->assertNull($class->getPackage());
        $class->setPackage($package);
        $this->assertSame($package, $class->getPackage());
        $class->setPackage(null);
        $this->assertNull($class->getPackage());
    }
    
    /**
     * Tests that {@link PHP_Reflection_AST_Class#getLine()} works as expected.
     * 
     * @return void
     */
    public function testGetStartLineNumber()
    {
        $class = new PHP_Reflection_AST_Class('foo', 42);
        
        $this->assertEquals(42, $class->getLine());
    }
    
    /**
     * Tests that {@link PHP_Reflection_AST_Class::getImplementedInterfaces()}
     * returns the expected result.
     *
     * @return void
     */
    public function testGetImplementedInterfaces()
    {
        $interfsA = new PHP_Reflection_AST_Interface('A');
        $interfsB = new PHP_Reflection_AST_Interface('B');
        $interfsC = new PHP_Reflection_AST_Interface('C');
        $interfsD = new PHP_Reflection_AST_Interface('D');
        $interfsE = new PHP_Reflection_AST_Interface('E');
        $interfsF = new PHP_Reflection_AST_Interface('F');
        
        $classA = new PHP_Reflection_AST_Class('A');
        $classB = new PHP_Reflection_AST_Class('B');
        $classC = new PHP_Reflection_AST_Class('C');

        $interfsB->addParentInterface($interfsA); // interface B extends A {}
        $interfsC->addParentInterface($interfsA); // interface C extends A {}
        $interfsD->addParentInterface($interfsB); // interface D extends B, E
        $interfsD->addParentInterface($interfsE); // interface D extends B, E
        $interfsE->addParentInterface($interfsF); // interface E extends F
        
        $classA->addImplementedInterface($interfsE); // class A implements E, C {}
        $classA->addImplementedInterface($interfsC); // class A implements E, C {}
        
        $classB->addImplementedInterface($interfsA); // class B extends C implements D, A {}
        $classB->addImplementedInterface($interfsD); // class B extends C implements D, A {}
        
        $classC->addImplementedInterface($interfsC); // class C implements C {}
        
        $classB->setParentClass($classC); // class B extends C implements D, A {}
        
        $interfaces = $classA->getImplementedInterfaces();
        $expected   = array($interfsE, $interfsF, $interfsA, $interfsC);
        
        $this->assertEquals(count($expected), $interfaces->count());
        foreach ($interfaces as $interface) {
            $idx = array_search($interface, $expected, true);
            $this->assertTrue(is_int($idx));
            $this->assertSame($expected[$idx], $interface);
            
            unset($expected[$idx]);
        }
        $this->assertEquals(0, count($expected));
        
        $interfaces = $classB->getImplementedInterfaces();
        $expected   = array(
            $interfsA, $interfsB, $interfsC, 
            $interfsD, $interfsE, $interfsF,
        );
        
        $this->assertEquals(count($expected), $interfaces->count());
        foreach ($interfaces as $interface) {
            $idx = array_search($interface, $expected, true);
            $this->assertTrue(is_int($idx));
            $this->assertSame($expected[$idx], $interface);
            
            unset($expected[$idx]);
        }
        $this->assertEquals(0, count($expected));
        
        $interfaces = $classC->getImplementedInterfaces();
        $expected   = array($interfsA, $interfsC);
        
        $this->assertEquals(count($expected), $interfaces->count());
        foreach ($interfaces as $interface) {
            $idx = array_search($interface, $expected, true);
            $this->assertTrue(is_int($idx));
            $this->assertSame($expected[$idx], $interface);
            
            unset($expected[$idx]);
        }
        $this->assertEquals(0, count($expected));
    }
    
    /**
     * Tests that {@link PHP_Reflection_AST_Class::addChildType()} also adds the
     * dependent classes as dependencies.
     *
     * @return void
     */
    public function testAddChildTypeAlsoAddsDependency()
    {
        $a = new PHP_Reflection_AST_Class('a');
        $b = new PHP_Reflection_AST_Class('b');
        $c = new PHP_Reflection_AST_Class('c');
        $d = new PHP_Reflection_AST_Class('d');
        
        $b->setParentClass($a);
        $c->setParentClass($a);
        
        $d->setParentClass($c);
        
        $depB = $b->getDependencies();
        $this->assertEquals(1, $depB->count());
        $this->assertSame($a, $depB->current());
        
        $depC = $c->getDependencies();
        $this->assertEquals(1, $depC->count());
        $this->assertSame($a, $depC->current());
        
        $depD = $d->getDependencies();
        $this->assertEquals(1, $depD->count());
        $this->assertSame($c, $depD->current());
    }
    
    /**
     * Tests the remove constant method.
     *
     * @return void
     */
    public function testRemoveConstant()
    {
        $a = new PHP_Reflection_AST_Class('a');
        
        $this->assertEquals(0, $a->getConstants()->count());
        $c = $a->addConstant(new PHP_Reflection_AST_ClassOrInterfaceConstant('FOO_BAR'));
        $this->assertEquals(1, $a->getConstants()->count());
        $a->removeConstant($c);
        $this->assertEquals(0, $a->getConstants()->count());
    }
    
    /**
     * Tests that {@link PHP_Reflection_AST_Class::addConstant()} reparents the
     * an already associated class instance.
     * 
     * @return void
     */
    public function testAddConstantReparentsClassInstance()
    {
        $a = new PHP_Reflection_AST_Class('a');
        $b = new PHP_Reflection_AST_Class('b');
        
        $c = $a->addConstant(new PHP_Reflection_AST_ClassOrInterfaceConstant('FOO_BAR'));
        $this->assertSame($a, $c->getParent());
        $b->addConstant($c);
        $this->assertSame($b, $c->getParent());
    }
    
    /**
     * Tests that 
     *
     */
    public function testRemovePropertyAlsoUnsetsParentClass()
    {
        $class = new PHP_Reflection_AST_Class('a');
        $prop1 = new PHP_Reflection_AST_Property('$p1');
        $prop2 = new PHP_Reflection_AST_Property('$p2');
        
        $class->addProperty($prop1);
        $this->assertSame($class, $prop1->getParent());
        
        $class->addProperty($prop2);
        $this->assertSame($class, $prop2->getParent());
        
        $this->assertEquals(2, $class->getProperties()->count());
        
        $class->removeProperty($prop1);
        $this->assertNull($prop1->getParent());
        $this->assertEquals(1, $class->getProperties()->count());
    }
    
    /**
     * Tests the getProperty() method.
     *
     * @return void
     */
    public function testGetProperyName()
    {
        $class = new PHP_Reflection_AST_Class('clazz');
        $class->addProperty(new PHP_Reflection_AST_Property('a'));
        $class->addProperty(new PHP_Reflection_AST_Property('b'));
        
        $property = $class->getProperty('b');
        $this->assertNotNull($property);
        $this->assertEquals('b', $property->getName());
    }
    
    /**
     * Tests that {@link PHP_Reflection_AST_Class::getProperty()} throws an 
     * exception for an unknown property name.
     *
     * @return void
     */
    public function testGetPropertyWithInvalidNameThrowsExceptionFail()
    {
        $class = new PHP_Reflection_AST_Class('clazz');
        $class->addProperty(new PHP_Reflection_AST_Property('a'));
        
        $this->setExpectedException(
            'PHP_Reflection_Exceptions_UnknownNodeException',
            'Unknown child node requested: b'
        );
        
        $property = $class->getProperty('b');
    }
    
    /**
     * Checks the {@link PHP_Reflection_AST_Class::isSubtypeOf()} method.
     *
     * @return void
     */
    public function testIsSubtypeOnInheritanceHierarchy()
    {
        $classA = new PHP_Reflection_AST_Class('A');
        $classB = new PHP_Reflection_AST_Class('B');
        $classC = new PHP_Reflection_AST_Class('C');
        
        $interfsD = new PHP_Reflection_AST_Interface('D');
        $interfsE = new PHP_Reflection_AST_Interface('E');
        $interfsF = new PHP_Reflection_AST_Interface('F');
        
        $classA->addImplementedInterface($interfsD); // class A implements D, E
        $classA->addImplementedInterface($interfsE); // class A implements D, E

        $classC->addImplementedInterface($interfsF); // class C extends B implements F {}
        
        $classB->setParentClass($classA); // class B extends A {} 
        $classC->setParentClass($classB); // class C extends B implements F {}
        
        $this->assertTrue($classA->isSubtypeOf($classA));
        $this->assertFalse($classA->isSubtypeOf($classB));
        $this->assertFalse($classA->isSubtypeOf($classC));
        $this->assertTrue($classA->isSubtypeOf($interfsD));
        $this->assertTrue($classA->isSubtypeOf($interfsE));
        $this->assertFalse($classA->isSubtypeOf($interfsF));
        
        $this->assertTrue($classB->isSubtypeOf($classA));
        $this->assertTrue($classB->isSubtypeOf($classB));
        $this->assertFalse($classB->isSubtypeOf($classC));
        $this->assertTrue($classB->isSubtypeOf($interfsD));
        $this->assertTrue($classB->isSubtypeOf($interfsE));
        $this->assertFalse($classB->isSubtypeOf($interfsF));
        
        $this->assertTrue($classC->isSubtypeOf($classA));
        $this->assertTrue($classC->isSubtypeOf($classB));
        $this->assertTrue($classC->isSubtypeOf($classC));
        $this->assertTrue($classC->isSubtypeOf($interfsD));
        $this->assertTrue($classC->isSubtypeOf($interfsE));
        $this->assertTrue($classC->isSubtypeOf($interfsF));
    }
    
    /**
     * Tests the constant getter method.
     *
     * @return void
     */
    public function testGetConstant()
    {
        $const1 = new PHP_Reflection_AST_ClassOrInterfaceConstant('const1');
        $const2 = new PHP_Reflection_AST_ClassOrInterfaceConstant('const2');
        
        $class = new PHP_Reflection_AST_Class('clazz');
        $class->addConstant($const1);
        $class->addConstant($const2);
        
        $this->assertSame($const1, $class->getConstant('const1'));
        $this->assertSame($const2, $class->getConstant('const2'));
    }
    
    /**
     * Tests the constant getter method with a not defined constant, which should
     * result in an exception.
     *
     * @return void
     */
    public function testGetConstantForInvalidConstantNameFail()
    {
        $class = new PHP_Reflection_AST_Class('clazz');
        
        $this->setExpectedException(
            'PHP_Reflection_Exceptions_UnknownNodeException',
            'Unknown child node requested: const1'
        );
        $class->getConstant('const1');
    }
    
    /**
     * Tests the method getter method.
     *
     * @return void
     */
    public function testGetMethod()
    {
        $method1 = new PHP_Reflection_AST_Method('method1');
        $method2 = new PHP_Reflection_AST_Method('method2');
        
        $class = new PHP_Reflection_AST_Class('clazz');
        $class->addMethod($method1);
        $class->addMethod($method2);
        
        $this->assertSame($method1, $class->getMethod('method1'));
        $this->assertSame($method2, $class->getMethod('method2'));
    }
    
    /**
     * Tests the method getter method with a not defined method, which should
     * result in an exception.
     *
     * @return void
     */
    public function testGetMethodForInvalidConstantNameFail()
    {
        $class = new PHP_Reflection_AST_Class('clazz');
        
        $this->setExpectedException(
            'PHP_Reflection_Exceptions_UnknownNodeException',
            'Unknown child node requested: method1'
        );
        $class->getMethod('method1');
    }
    
    /**
     * Tests the visitor accept method.
     *
     * @return void
     */
    public function testVisitorAccept()
    {
        $class   = new PHP_Reflection_AST_Class('clazz', 0);
        $visitor = new PHP_Reflection_AST_TestImplAstVisitor();
        
        $this->assertNull($visitor->class);
        $class->accept($visitor);
        $this->assertSame($class, $visitor->class);
        
    }
    
    /**
     * Creates an abstract item instance.
     *
     * @return PHP_Reflection_AST_AbstractSourceElement
     */
    protected function createItem()
    {
        return new PHP_Reflection_AST_Class('clazz', 0);
    }
    
    /**
     * Generates a node instance that can handle dependencies.
     *
     * @return PHP_Reflection_AST_DependencyAwareI
     */
    protected function createDependencyNode()
    {
        return new PHP_Reflection_AST_Class('clazz', 0);
    }
}