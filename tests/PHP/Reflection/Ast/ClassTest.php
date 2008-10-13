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
 * @subpackage Ast
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/AbstractDependencyAwareTest.php';
require_once dirname(__FILE__) . '/_dummy/TestImplAstVisitor.php';

require_once 'PHP/Reflection/Ast/Class.php';
require_once 'PHP/Reflection/Ast/Interface.php';
require_once 'PHP/Reflection/Ast/Method.php';
require_once 'PHP/Reflection/Ast/Package.php';
require_once 'PHP/Reflection/Ast/Property.php';
require_once 'PHP/Reflection/Ast/ClassOrInterfaceConstant.php';

/**
 * Test case implementation for the PHP_Reflection_Ast_Class class.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Ast
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_Ast_ClassTest extends PHP_Reflection_Ast_AbstractDependencyAwareTest
{
    /**
     * Tests the ctor with and the {@link PHP_Reflection_Ast_Class::getName()} and
     * {@link PHP_Reflection_Ast_Class::getSourceFile()} methods.
     * 
     * @return void
     */
    public function testCreateNewClassInstance()
    {
        $class = new PHP_Reflection_Ast_Class('clazz', 0);
        
        $this->assertEquals('clazz', $class->getName());
    }
    
    /**
     * Tests that the default {@link PHP_Reflection_Ast_Class::isAbstract()}
     * value is <b>false</b> but could be changed.
     *
     * @return void
     */
    public function testMarkClassInstanceAsAbstract()
    {
        $class = new PHP_Reflection_Ast_Class('clazz', 0);
        
        $this->assertFalse($class->isAbstract());
        $class->setModifiers(ReflectionClass::IS_EXPLICIT_ABSTRACT);
        $this->assertTrue($class->isAbstract());
        $class->setModifiers(0);
        $this->assertFalse($class->isAbstract());
    }
    
    /**
     * Tests that the default {@link PHP_Reflection_Ast_Class::isFinal()}
     * value is <b>false</b> but could be changed.
     *
     * @return void
     */
    public function testMarkClassInstanceAsFinal()
    {
        $class = new PHP_Reflection_Ast_Class('clazz', 0);
        
        $this->assertFalse($class->isFinal());
        $class->setModifiers(ReflectionClass::IS_FINAL);
        $this->assertTrue($class->isFinal());
        $class->setModifiers(0);
        $this->assertFalse($class->isFinal());
    }
    
    /**
     * Tests that a new {@link PHP_Reflection_Ast_Class} object returns an empty
     * {@link PHP_Reflection_Ast_Iterator} instance for methods.
     *
     * @return void
     */
    public function testGetMethodIterator()
    {
        $class   = new PHP_Reflection_Ast_Class('clazz', 0);
        $methods = $class->getMethods();
        
        $this->assertType('PHP_Reflection_Ast_Iterator', $methods);
        $this->assertEquals(0, $methods->count());
    }
    
    /**
     * Tests that the {@link PHP_Reflection_Ast_Class::addMethod()} method adds a
     * method to the internal list and sets the context class as parent.
     *
     * @return void
     */
    public function testAddNewMethod()
    {
        $class  = new PHP_Reflection_Ast_Class('clazz', 0);
        $method = new PHP_Reflection_Ast_Method('method', 0);
        
        $this->assertNull($method->getParent());
        $class->addMethod($method);
        $this->assertSame($class, $method->getParent());
        $this->assertEquals(1, $class->getMethods()->count());
    }
    
    /**
     * Tests that the {@link PHP_Reflection_Ast_Class::addMethod()} reparents the
     * new method if it already has a parent class instance.
     *
     * @return void
     */
    public function testAddNewMethodAndReparent()
    {
        $class1 = new PHP_Reflection_Ast_Class('clazz1', 0);
        $class2 = new PHP_Reflection_Ast_Class('clazz2', 0);
        $method = new PHP_Reflection_Ast_Method('method', 0);
        
        $class1->addMethod($method);
        $this->assertSame($class1, $method->getParent());
        $this->assertSame($method, $class1->getMethods()->current());
        
        $class2->addMethod($method);
        $this->assertSame($class2, $method->getParent());
        $this->assertSame($method, $class2->getMethods()->current());
        $this->assertEquals(0, $class1->getMethods()->count());
    }
    
    /**
     * Tests that the {@link PHP_Reflection_Ast_Class::getPackage()} returns as
     * default value <b>null</b> and that the package could be set and unset.
     *
     * @return void
     */
    public function testGetSetPackage()
    {
        $package = new PHP_Reflection_Ast_Package('package');
        $class   = new PHP_Reflection_Ast_Class('clazz', 0);
        
        $this->assertNull($class->getPackage());
        $class->setPackage($package);
        $this->assertSame($package, $class->getPackage());
        $class->setPackage(null);
        $this->assertNull($class->getPackage());
    }
    
    /**
     * Tests that {@link PHP_Reflection_Ast_Class#getStartLine()} works as expected.
     * 
     * @return void
     */
    public function testGetStartLineNumber()
    {
        $class = new PHP_Reflection_Ast_Class('foo', 42);
        
        $this->assertEquals(42, $class->getStartLine());
    }
    
    /**
     * Tests that {@link PHP_Reflection_Ast_Class::getImplementedInterfaces()}
     * returns the expected result.
     *
     * @return void
     */
    public function testGetImplementedInterfaces()
    {
        $interfsA = new PHP_Reflection_Ast_Interface('A');
        $interfsB = new PHP_Reflection_Ast_Interface('B');
        $interfsC = new PHP_Reflection_Ast_Interface('C');
        $interfsD = new PHP_Reflection_Ast_Interface('D');
        $interfsE = new PHP_Reflection_Ast_Interface('E');
        $interfsF = new PHP_Reflection_Ast_Interface('F');
        
        $classA = new PHP_Reflection_Ast_Class('A');
        $classB = new PHP_Reflection_Ast_Class('B');
        $classC = new PHP_Reflection_Ast_Class('C');
        
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
     * Tests that {@link PHP_Reflection_Ast_Class::addDependency()} also adds the
     * dependent classes as child types.
     *
     * @return void
     */
    public function testAddDependencyAlsoAddsChildType()
    {
        $a = new PHP_Reflection_Ast_Class('a');
        $b = new PHP_Reflection_Ast_Class('b');
        $c = new PHP_Reflection_Ast_Class('c');
        $d = new PHP_Reflection_Ast_Class('d');
        
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
     * Tests that {@link PHP_Reflection_Ast_Class::removeDependency()} also removes
     * the dependent child type.
     *
     * @return void
     */
    public function testRemoveDependencyAlsoRemovesChildType()
    {
        $a = new PHP_Reflection_Ast_Class('a');
        $b = new PHP_Reflection_Ast_Class('b');
        
        $a->addDependency($b);
        $this->assertEquals(1, $b->getChildTypes()->count());
        $a->removeDependency($b);
        $this->assertEquals(0, $b->getChildTypes()->count());
    }
    
    /**
     * Tests that {@link PHP_Reflection_Ast_Class::addChildType()} also adds the
     * dependent classes as dependencies.
     *
     * @return void
     */
    public function testAddChildTypeAlsoAddsDependency()
    {
        $a = new PHP_Reflection_Ast_Class('a');
        $b = new PHP_Reflection_Ast_Class('b');
        $c = new PHP_Reflection_Ast_Class('c');
        $d = new PHP_Reflection_Ast_Class('d');
        
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
     * Tests that {@link PHP_Reflection_Ast_Class::removeChildType()} also removes
     * the dependency instance.
     *
     * @return void
     */
    public function testRemoveChildTypeAlsoRemovesDependency()
    {
        $a = new PHP_Reflection_Ast_Class('a');
        $b = new PHP_Reflection_Ast_Class('b');
        
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
        $a = new PHP_Reflection_Ast_Class('a');
        
        $this->assertEquals(0, $a->getConstants()->count());
        $c = $a->addConstant(new PHP_Reflection_Ast_ClassOrInterfaceConstant('FOO_BAR'));
        $this->assertEquals(1, $a->getConstants()->count());
        $a->removeConstant($c);
        $this->assertEquals(0, $a->getConstants()->count());
    }
    
    /**
     * Tests that {@link PHP_Reflection_Ast_Class::addConstant()} reparents the
     * an already associated class instance.
     * 
     * @return void
     */
    public function testAddConstantReparentsClassInstance()
    {
        $a = new PHP_Reflection_Ast_Class('a');
        $b = new PHP_Reflection_Ast_Class('b');
        
        $c = $a->addConstant(new PHP_Reflection_Ast_ClassOrInterfaceConstant('FOO_BAR'));
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
        $class = new PHP_Reflection_Ast_Class('a');
        $prop1 = new PHP_Reflection_Ast_Property('$p1');
        $prop2 = new PHP_Reflection_Ast_Property('$p2');
        
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
     * Checks the {@link PHP_Reflection_Ast_Class::isSubtypeOf()} method.
     *
     * @return void
     */
    public function testIsSubtypeOnInheritanceHierarchy()
    {
        $classA = new PHP_Reflection_Ast_Class('A');
        $classB = new PHP_Reflection_Ast_Class('B');
        $classC = new PHP_Reflection_Ast_Class('C');
        
        $interfsD = new PHP_Reflection_Ast_Interface('D');
        $interfsE = new PHP_Reflection_Ast_Interface('E');
        $interfsF = new PHP_Reflection_Ast_Interface('F');
        
        $interfsD->addChildType($classA); // class A implements D, E
        $interfsE->addChildType($classA); // class A implements D, E

        $interfsF->addChildType($classC); // class C extends B implements F {}
        
        $classA->addChildType($classB); // class B extends A {} 
        $classB->addChildType($classC); // class C extends B implements F {}
        
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
        $class   = new PHP_Reflection_Ast_Class('clazz', 0);
        $visitor = new PHP_Reflection_Ast_TestImplAstVisitor();
        
        $this->assertNull($visitor->class);
        $class->accept($visitor);
        $this->assertSame($class, $visitor->class);
        
    }
    
    /**
     * Creates an abstract item instance.
     *
     * @return PHP_Reflection_Ast_AbstractItem
     */
    protected function createItem()
    {
        return new PHP_Reflection_Ast_Class('clazz', 0);
    }
    
    /**
     * Generates a node instance that can handle dependencies.
     *
     * @return PHP_Reflection_Ast_DependencyAwareI
     */
    protected function createDependencyNode()
    {
        return new PHP_Reflection_Ast_Class('clazz', 0);
    }
}