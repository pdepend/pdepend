<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pmanuel-pichler.de>.
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
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/AbstractDependencyTest.php';
require_once dirname(__FILE__) . '/TestNodeVisitor.php';

require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/Method.php';
require_once 'PHP/Depend/Code/Package.php';

/**
 * Test case implementation for the PHP_Depend_Code_Class class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
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
        $class = new PHP_Depend_Code_Class('clazz', 'clazz.php');
        
        $this->assertEquals('clazz', $class->getName());
        $this->assertEquals('clazz.php', $class->getSourceFile());
    }
    
    /**
     * Tests that the default {@link PHP_Depend_Code_Class::isAbstract()}
     * value is <b>false</b> but could be changed.
     *
     * @return void
     */
    public function testMarkClassInstanceAsAbstract()
    {
        $class = new PHP_Depend_Code_Class('clazz', 'clazz.php');
        
        $this->assertFalse($class->isAbstract());
        $class->setAbstract(true);
        $this->assertTrue($class->isAbstract());
        $class->setAbstract(false);
        $this->assertFalse($class->isAbstract());
    }
    
    /**
     * Tests that a new {@link PHP_Depend_Code_Class} object returns an empty
     * {@link PHP_Depend_Code_NodeIterator} instance for methods.
     *
     * @return void
     */
    public function testGetMethodNodeIterator()
    {
        $class   = new PHP_Depend_Code_Class('clazz', 'clazz.php');
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
        $class  = new PHP_Depend_Code_Class('clazz', 'clazz.php');
        $method = new PHP_Depend_Code_Method('method');
        
        $this->assertNull($method->getClass());
        $class->addMethod($method);
        $this->assertSame($class, $method->getClass());
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
        $class1 = new PHP_Depend_Code_Class('clazz1', 'clazz1.php');
        $class2 = new PHP_Depend_Code_Class('clazz2', 'clazz2.php');
        $method = new PHP_Depend_Code_Method('method');
        
        $class1->addMethod($method);
        $this->assertSame($class1, $method->getClass());
        $this->assertSame($method, $class1->getMethods()->current());
        
        $class2->addMethod($method);
        $this->assertSame($class2, $method->getClass());
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
        $class   = new PHP_Depend_Code_Class('clazz', 'clazz.php');
        
        $this->assertNull($class->getPackage());
        $class->setPackage($package);
        $this->assertSame($package, $class->getPackage());
        $class->setPackage(null);
        $this->assertNull($class->getPackage());
    }
    
    /**
     * Tests the visitor accept method.
     *
     * @return void
     */
    public function testVisitorAccept()
    {
        $class   = new PHP_Depend_Code_Class('clazz', 'clazz.php');
        $visitor = new PHP_Depend_Code_TestNodeVisitor();
        
        $this->assertNull($visitor->class);
        $class->accept($visitor);
        $this->assertSame($class, $visitor->class);
        
    }
    
    /**
     * Generates a node instance that can handle dependencies.
     *
     * @return PHP_Depend_Code_DependencyNode
     */
    protected function createDependencyNode()
    {
        return new PHP_Depend_Code_Class('clazz', 'clazz.php');
    }
}