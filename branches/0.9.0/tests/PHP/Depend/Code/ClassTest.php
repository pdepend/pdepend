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
 * @link      http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/AbstractDependencyTest.php';
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
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Code_ClassTest extends PHP_Depend_Code_AbstractDependencyTest
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
        $interfsA = new PHP_Depend_Code_Interface('A');
        $interfsB = new PHP_Depend_Code_Interface('B');
        $interfsC = new PHP_Depend_Code_Interface('C');
        $interfsD = new PHP_Depend_Code_Interface('D');
        $interfsE = new PHP_Depend_Code_Interface('E');
        $interfsF = new PHP_Depend_Code_Interface('F');
        
        $classA = new PHP_Depend_Code_Class('A');
        $classB = new PHP_Depend_Code_Class('B');
        $classC = new PHP_Depend_Code_Class('C');
        
        $interfsA->addChildType($interfsB); // interface B extends A {}
        $interfsA->addChildType($interfsC); // interface C extends A {}
        $interfsB->addChildType($interfsD); // interface D extends B, E
        $interfsE->addChildType($interfsD); // interface D extends B, E
        $interfsF->addChildType($interfsE); // interface E extends F
        
        $interfsE->addChildType($classA); // class A implements E, C {}
        $interfsC->addChildType($classA); // class A implements E, C {}
        
        $interfsD->addChildType($classB); // class B extends C implements D, A {}
        $interfsA->addChildType($classB); // class B extends C implements D, A {}
        
        $interfsC->addChildType($classC); // class C implements C {}
        
        $classC->addChildType($classB); // class B extends C implements D, A {}
        
        $interfaces = $classA->getInterfaces();
        $this->assertEquals(4, $interfaces->count());
        $this->assertSame($interfsA, $interfaces->current());
        $interfaces->next();
        $this->assertSame($interfsC, $interfaces->current());
        $interfaces->next();
        $this->assertSame($interfsE, $interfaces->current());
        $interfaces->next();
        $this->assertSame($interfsF, $interfaces->current());
        
        $interfaces = $classB->getInterfaces();
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
        
        $interfaces = $classC->getInterfaces();
        $this->assertEquals(2, $interfaces->count());
        $this->assertSame($interfsA, $interfaces->current());
        $interfaces->next();
        $this->assertSame($interfsC, $interfaces->current());
    }
    
    /**
     * Tests that {@link PHP_Depend_Code_Class::addDependency()} also adds the
     * dependent classes as child types.
     *
     * @return void
     */
    public function testAddDependencyAlsoAddsChildType()
    {
        $a = new PHP_Depend_Code_Class('a');
        $b = new PHP_Depend_Code_Class('b');
        $c = new PHP_Depend_Code_Class('c');
        $d = new PHP_Depend_Code_Class('d');
        
        $b->addDependency($a);
        $c->addDependency($a);
        
        $d->addDependency($c);
        
        $typesA = $a->getChildTypes();
        $this->assertEquals(2, $typesA->count());
        $this->assertSame($b, $typesA->current());
        $typesA->next();
        $this->assertSame($c, $typesA->current());

        $typesC = $c->getChildTypes();
        $this->assertEquals(1, $typesC->count());
        $this->assertSame($d, $typesC->current());
    }
    
    /**
     * Tests that {@link PHP_Depend_Code_Class::removeDependency()} also removes
     * the dependent child type.
     *
     * @return void
     */
    public function testRemoveDependencyAlsoRemovesChildType()
    {
        $a = new PHP_Depend_Code_Class('a');
        $b = new PHP_Depend_Code_Class('b');
        
        $a->addDependency($b);
        $this->assertEquals(1, $b->getChildTypes()->count());
        $a->removeDependency($b);
        $this->assertEquals(0, $b->getChildTypes()->count());
    }
    
    /**
     * Tests that {@link PHP_Depend_Code_Class::addChildType()} also adds the
     * dependent classes as dependencies.
     *
     * @return void
     */
    public function testAddChildTypeAlsoAddsDependency()
    {
        $a = new PHP_Depend_Code_Class('a');
        $b = new PHP_Depend_Code_Class('b');
        $c = new PHP_Depend_Code_Class('c');
        $d = new PHP_Depend_Code_Class('d');
        
        $a->addChildType($b);
        $a->addChildType($c);
        
        $c->addChildType($d);
        
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
     * Tests that {@link PHP_Depend_Code_Class::removeChildType()} also removes
     * the dependency instance.
     *
     * @return void
     */
    public function testRemoveChildTypeAlsoRemovesDependency()
    {
        $a = new PHP_Depend_Code_Class('a');
        $b = new PHP_Depend_Code_Class('b');
        
        $a->addChildType($b);
        $this->assertEquals(1, $b->getDependencies()->count());
        $a->removeChildType($b);
        $this->assertEquals(0, $b->getDependencies()->count());
    }
    
    /**
     * Tests the remove constant method.
     *
     * @return void
     */
    public function testRemoveConstant()
    {
        $a = new PHP_Depend_Code_Class('a');
        
        $this->assertEquals(0, $a->getConstants()->count());
        $c = $a->addConstant(new PHP_Depend_Code_TypeConstant('FOO_BAR'));
        $this->assertEquals(1, $a->getConstants()->count());
        $a->removeConstant($c);
        $this->assertEquals(0, $a->getConstants()->count());
    }
    
    /**
     * Tests that {@link PHP_Depend_Code_Class::addConstant()} reparents the
     * an already associated class instance.
     * 
     * @return void
     */
    public function testAddConstantReparentsClassInstance()
    {
        $a = new PHP_Depend_Code_Class('a');
        $b = new PHP_Depend_Code_Class('b');
        
        $c = $a->addConstant(new PHP_Depend_Code_TypeConstant('FOO_BAR'));
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
        $class = new PHP_Depend_Code_Class('a');
        $prop1 = new PHP_Depend_Code_Property('$p1');
        $prop2 = new PHP_Depend_Code_Property('$p2');
        
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
     * Checks the {@link PHP_Depend_Code_Class::isSubtypeOf()} method.
     *
     * @return void
     */
    public function testIsSubtypeOnInheritanceHierarchy()
    {
        $classA = new PHP_Depend_Code_Class('A');
        $classB = new PHP_Depend_Code_Class('B');
        $classC = new PHP_Depend_Code_Class('C');
        
        $interfsD = new PHP_Depend_Code_Interface('D');
        $interfsE = new PHP_Depend_Code_Interface('E');
        $interfsF = new PHP_Depend_Code_Interface('F');
        
        $interfsD->addChildType($classA); // class A implements D, E
        $interfsE->addChildType($classA); // class A implements D, E

        $interfsF->addChildType($classC); // class C extends B implements F {}
        
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
    
    /**
     * Generates a node instance that can handle dependencies.
     *
     * @return PHP_Depend_Code_DependencyNodeI
     */
    protected function createDependencyNode()
    {
        return new PHP_Depend_Code_Class('clazz', 0);
    }
}
