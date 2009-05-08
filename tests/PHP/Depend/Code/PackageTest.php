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

require_once dirname(__FILE__) . '/../AbstractTest.php';
require_once dirname(__FILE__) . '/../Visitor/TestNodeVisitor.php';

require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/Function.php';
require_once 'PHP/Depend/Code/Package.php';

/**
 * Test case implementation for the PHP_Depend_Code_Package class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_Depend_Code_PackageTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests the package ctor and the {@link PHP_Depend_Code_Package::getName()}
     * method.
     *
     * @return void
     */
    public function testCreateNewPackageInstance()
    {
        $package = new PHP_Depend_Code_Package('package1');
        $this->assertEquals('package1', $package->getName());
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Package::getTypes()} method returns
     * an empty {@link PHP_Depend_Code_NodeIterator}.
     *
     * @return void
     */
    public function testGetTypeNodeIterator()
    {
        $package = new PHP_Depend_Code_Package('package1');
        $types = $package->getTypes();
        
        $this->assertType('PHP_Depend_Code_NodeIterator', $types);
        $this->assertEquals(0, $types->count());
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Package::addType()} method sets
     * the package in the {@link PHP_Depend_Code_Class} object and it tests the
     * iterator to contain the new class.
     *
     * @return void
     */
    public function testAddType()
    {
        $package = new PHP_Depend_Code_Package('package1');
        $class   = new PHP_Depend_Code_Class('Class', 0, 'class.php');
        
        $this->assertNull($class->getPackage());
        $package->addType($class);
        $this->assertSame($package, $class->getPackage());
        $this->assertEquals(1, $package->getTypes()->count());
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Package::addType()} reparents a
     * class.
     *
     * @return void
     */
    public function testAddTypeReparent()
    {
        $package1 = new PHP_Depend_Code_Package('package1');
        $package2 = new PHP_Depend_Code_Package('package2');
        $class    = new PHP_Depend_Code_Class('Class', 0, 'class.php');
        
        $package1->addType($class);
        $this->assertSame($package1, $class->getPackage());
        $this->assertSame($class, $package1->getTypes()->current());
        
        $package2->addType($class);
        $this->assertSame($package2, $class->getPackage());
        $this->assertSame($class, $package2->getTypes()->current());
        $this->assertEquals(0, $package1->getTypes()->count());
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Package::removeType()} method unsets
     * the package in the {@link PHP_Depend_Code_Class} object and it tests the
     * iterator to contain the new class.
     *
     * @return void
     */
    public function testRemoveType()
    {
        $package = new PHP_Depend_Code_Package('package1');
        $class1  = new PHP_Depend_Code_Class('Class1', 0, 'class1.php');
        $class2  = new PHP_Depend_Code_Class('Class2', 0, 'class2.php');
        
        $package->addType($class1);
        $package->addType($class2);
        $this->assertSame($package, $class2->getPackage());
        
        $package->removeType($class2);
        $this->assertNull($class2->getPackage());
        $this->assertEquals(1, $package->getTypes()->count());
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Package::getFunctions()} method 
     * returns an empty {@link PHP_Depend_Code_NodeIterator}.
     *
     * @return void
     */
    public function testGetFunctionsNodeIterator()
    {
        $package   = new PHP_Depend_Code_Package('package1');
        $functions = $package->getFunctions();
        
        $this->assertType('PHP_Depend_Code_NodeIterator', $functions);
        $this->assertEquals(0, $functions->count());
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Package::addFunction()} method sets
     * the actual package as {@link PHP_Depend_Code_Function} owner.
     *
     * @return void
     */
    public function testAddFunction()
    {
        $package  = new PHP_Depend_Code_Package('package1');
        $function = new PHP_Depend_Code_Function('function', 0);
        
        $this->assertNull($function->getPackage());
        $package->addFunction($function);
        $this->assertSame($package, $function->getPackage());
        $this->assertEquals(1, $package->getFunctions()->count());
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Package::addFunction()} reparents a
     * function.
     *
     * @return void
     */
    public function testAddFunctionReparent()
    {
        $package1 = new PHP_Depend_Code_Package('package1');
        $package2 = new PHP_Depend_Code_Package('package2');
        $function = new PHP_Depend_Code_Function('func', 0);
        
        $package1->addFunction($function);
        $this->assertSame($package1, $function->getPackage());
        $this->assertSame($function, $package1->getFunctions()->current());
        
        $package2->addFunction($function);
        $this->assertSame($package2, $function->getPackage());
        $this->assertSame($function, $package2->getFunctions()->current());
        $this->assertEquals(0, $package1->getFunctions()->count());
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Package::removeFunction()} method 
     * unsets the actual package as {@link PHP_Depend_Code_Function} owner.
     *
     * @return void
     */
    public function testRemoveFunction()
    {
        $package   = new PHP_Depend_Code_Package('package1');
        $function1 = new PHP_Depend_Code_Function('func1', 0);
        $function2 = new PHP_Depend_Code_Function('func2', 0);
        
        $package->addFunction($function1);
        $package->addFunction($function2);
        $this->assertSame($package, $function2->getPackage());
        
        $package->removeFunction($function2);
        $this->assertNull($function2->getPackage());
        $this->assertEquals(1, $package->getFunctions()->count());
    }
    
    /**
     * Tests the visitor accept method.
     *
     * @return void
     */
    public function testVisitorAccept()
    {
        $package = new PHP_Depend_Code_Package('package1');
        $visitor = new PHP_Depend_Visitor_TestNodeVisitor();
        
        $this->assertNull($visitor->package);
        $package->accept($visitor);
        $this->assertSame($package, $visitor->package);
    }
}