<?php
/**
 * This file is part of PHP_Reflection.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/AbstractItemTest.php';
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
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_AST_ClassTest extends PHP_Reflection_AST_AbstractItemTest
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
     * Tests that {@link PHP_Reflection_AST_Class#getStartLine()} works as expected.
     * 
     * @return void
     */
    public function testGetStartLineNumber()
    {
        $class = new PHP_Reflection_AST_Class('foo', 42);
        
        $this->assertEquals(42, $class->getStartLine());
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

        $interfsA->addChildInterface($interfsB); // interface B extends A {}
        $interfsA->addChildInterface($interfsC); // interface C extends A {}
        $interfsB->addChildInterface($interfsD); // interface D extends B, E
        $interfsE->addChildInterface($interfsD); // interface D extends B, E
        $interfsF->addChildInterface($interfsE); // interface E extends F
        
        $interfsE->addImplementingClass($classA); // class A implements E, C {}
        $interfsC->addImplementingClass($classA); // class A implements E, C {}
        
        $interfsD->addImplementingClass($classB); // class B extends C implements D, A {}
        $interfsA->addImplementingClass($classB); // class B extends C implements D, A {}
        
        $interfsC->addImplementingClass($classC); // class C implements C {}
        
        $classC->addChildClass($classB); // class B extends C implements D, A {}
        
        $interfaces = $classA->getImplementedInterfaces();
        $this->assertEquals(4, $interfaces->count());
        $this->assertSame($interfsA, $interfaces->current());
        $interfaces->next();
        $this->assertSame($interfsC, $interfaces->current());
        $interfaces->next();
        $this->assertSame($interfsE, $interfaces->current());
        $interfaces->next();
        $this->assertSame($interfsF, $interfaces->current());
        
        $interfaces = $classB->getImplementedInterfaces();
        $this->assertEquals(6, $interfaces->count());
        $this->assertSame($interfsA, $interfaces->current());
        $interfaces->next();
        $this->assertSame($interfsB, $interfaces->current());
        $interfaces->next();
        $this->assertSame($interfsC, $interfaces->current());
        $interfaces->next();
        $this->assertSame($interfsD, $interfaces->current());
        $interfaces->next();
        $this->assertSame($interfsE, $interfaces->current());
        $interfaces->next();
        $this->assertSame($interfsF, $interfaces->current());
        
        $interfaces = $classC->getImplementedInterfaces();
        $this->assertEquals(2, $interfaces->count());
        $this->assertSame($interfsA, $interfaces->current());
        $interfaces->next();
        $this->assertSame($interfsC, $interfaces->current());
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
        
        $a->addChildClass($b);
        $a->addChildClass($c);
        
        $c->addChildClass($d);
        
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
        
        $interfsD->addImplementingClass($classA); // class A implements D, E
        $interfsE->addImplementingClass($classA); // class A implements D, E

        $interfsF->addImplementingClass($classC); // class C extends B implements F {}
        
        $classA->addChildClass($classB); // class B extends A {} 
        $classB->addChildClass($classC); // class C extends B implements F {}
        
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
     * @return PHP_Reflection_AST_AbstractItem
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