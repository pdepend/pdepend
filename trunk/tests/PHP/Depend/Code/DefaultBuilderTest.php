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

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Depend/Code/DefaultBuilder.php';

/**
 * Test case implementation for the default node builder implementation.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Code_DefaultBuilderTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the node builder creates a class for the same name only once.
     *
     * @return void
     */
    public function testBuildClassUnique()
    {
        $builder = new PHP_Depend_Code_DefaultBuilder();
        $class1  = $builder->buildClass('clazz1', 'clazz1.php');
        $class2  = $builder->buildClass('clazz1', 'clazz1.php');
        
        $this->assertType('PHP_Depend_Code_Class', $class1);
        $this->assertType('PHP_Depend_Code_Class', $class2);
        
        $this->assertSame($class1, $class2);
    }
    
    /**
     * Tests that the node builder appends a default package to all new created
     * classes.
     *
     * @return void
     */
    public function testBuildClassDefaultPackage()
    {
        $builder = new PHP_Depend_Code_DefaultBuilder();
        $class1  = $builder->buildClass('clazz1', 'clazz1.php');
        $class2  = $builder->buildClass('clazz2', 'clazz2.php');
        
        $this->assertNotNull($class1->getPackage());
        $this->assertNotNull($class2->getPackage());
        
        $this->assertSame($class1->getPackage(), $class2->getPackage());
        $this->assertEquals(PHP_Depend_Code_NodeBuilder::DEFAULT_PACKAGE, $class1->getPackage()->getName());
    }
    
    /**
     * Tests that the build interface method recreates an existing class as 
     * interface. 
     *
     * @return void
     */
    public function testBuildInterfaceForcesRecreateTypeForExistingClass()
    {
        $builder = new PHP_Depend_Code_DefaultBuilder();
        
        $type0 = $builder->buildClassOrInterface('FooBar');
        $this->assertType('PHP_Depend_Code_Class', $type0);
        $type1 = $builder->buildInterface('FooBar');
        $this->assertType('PHP_Depend_Code_Interface', $type1);
        $type2 = $builder->buildClassOrInterface('FooBar');
        $this->assertType('PHP_Depend_Code_Interface', $type2);
    }
    
    /**
     * Tests that a type recreate forces all class dependencies to be updated.
     *
     * @return void
     */
    public function testBuildInterfaceForcesUpdateClassDependencies()
    {
        $builder = new PHP_Depend_Code_DefaultBuilder();
        
        $class = $builder->buildClass('Bar');
        $type0 = $builder->buildClassOrInterface('FooBar');
        
        $class->addDependency($type0);
        $this->assertEquals(1, $class->getDependencies()->count());
        $this->assertEquals($type0, $class->getDependencies()->current());
        
        $type1 = $builder->buildInterface('FooBar');
        $this->assertEquals(1, $class->getDependencies()->count());
        $this->assertEquals($type1, $class->getDependencies()->current());
    }
    
    /**
     * Tests that a type recreate forces all interface dependencies to be updated.
     *
     * @return void
     */
    public function testBuildInterfaceForcesUpdateInterfaceDependencies()
    {
        $builder = new PHP_Depend_Code_DefaultBuilder();
        
        $interface = $builder->buildInterface('Bar');
        $type0     = $builder->buildClassOrInterface('FooBar');
        
        $interface->addDependency($type0);
        $this->assertEquals(1, $interface->getDependencies()->count());
        $this->assertEquals($type0, $interface->getDependencies()->current());
        
        $type1 = $builder->buildInterface('FooBar');
        $this->assertEquals(1, $interface->getDependencies()->count());
        $this->assertEquals($type1, $interface->getDependencies()->current());
    }
    
    /**
     * Tests that a type recreate forces all function dependencies to be updated.
     *
     * @return void
     */
    public function testBuildInterfaceForcesUpdateFunctionDependencies()
    {
        $builder = new PHP_Depend_Code_DefaultBuilder();
        
        $function = $builder->buildFunction('bar', 0);
        $type0    = $builder->buildClassOrInterface('FooBar');
        
        $function->addDependency($type0);
        $this->assertEquals(1, $function->getDependencies()->count());
        $this->assertEquals($type0, $function->getDependencies()->current());
        
        $type1 = $builder->buildInterface('FooBar');
        $this->assertEquals(1, $function->getDependencies()->count());
        $this->assertEquals($type1, $function->getDependencies()->current());
    }
    
    /**
     * Tests that a type recreate forces all method dependencies to be updated.
     *
     * @return void
     */
    public function testBuildInterfaceForcesUpdateMethodDependencies()
    {
        $builder = new PHP_Depend_Code_DefaultBuilder();
        
        $method = $builder->buildMethod('bar', 0);
        $type0  = $builder->buildClassOrInterface('FooBar');
        
        $method->addDependency($type0);
        $this->assertEquals(1, $method->getDependencies()->count());
        $this->assertEquals($type0, $method->getDependencies()->current());
        
        $type1 = $builder->buildInterface('FooBar');
        $this->assertEquals(1, $method->getDependencies()->count());
        $this->assertEquals($type1, $method->getDependencies()->current());
    }
    
    /**
     * Tests that build interface updates the source file information for null
     * values.
     *
     * @return void
     */
    public function testBuildInterfaceSetsSourceFileInformationForNull()
    {
        $builder   = new PHP_Depend_Code_DefaultBuilder();
        $interface = $builder->buildInterface('FooBar');
        
        $this->assertNull($interface->getSourceFile());
        $builder->buildInterface('FooBar', 0, 'HelloWorld.php');
        $this->assertEquals('HelloWorld.php', $interface->getSourceFile());
    }
    
    /**
     * Tests that the build interface method doesn't update an existing source
     * file info.
     *
     * @return void
     */
    public function testBuildInterfaceDoesntSetSourceFileInformationForNotNullValues()
    {
        $builder   = new PHP_Depend_Code_DefaultBuilder();
        $interface = $builder->buildInterface('FooBar', 0, 'FooBar.php');
        
        $this->assertEquals('FooBar.php', $interface->getSourceFile());
        $builder->buildInterface('FooBar', 0, 'HelloWorld.php');
        $this->assertEquals('FooBar.php', $interface->getSourceFile());
    }
    
    /**
     * Tests the PHP_Depend_Code_Method build method.
     *
     * @return void
     */
    public function testBuildMethod()
    {
        $builder = new PHP_Depend_Code_DefaultBuilder();
        $method  = $builder->buildMethod('method');
        
        $this->assertType('PHP_Depend_Code_Method', $method);
    }
    /**
     * Tests that the node builder creates a package for the same name only once.
     *
     * @return void
     */
    public function testBuildPackageUnique()
    {
        $builder  = new PHP_Depend_Code_DefaultBuilder();
        $package1 = $builder->buildPackage('package1');
        $package2 = $builder->buildPackage('package1');
        
        $this->assertType('PHP_Depend_Code_Package', $package1);
        $this->assertType('PHP_Depend_Code_Package', $package2);
        
        $this->assertSame($package1, $package2);
    }
    
    /**
     * Tests the implemented {@link IteratorAggregate}.
     *
     * @return void
     */
    public function testGetIteratorWithPackages()
    {
        $builder = new PHP_Depend_Code_DefaultBuilder();
        
        $packages = array(
            'package1'  =>  $builder->buildPackage('package1'),
            'package2'  =>  $builder->buildPackage('package2'),
            'package3'  =>  $builder->buildPackage('package3')
        );
        
        foreach ($builder as $name => $package) {
            $this->assertArrayHasKey($name, $packages);
            $this->assertEquals($name, $package->getName());
            $this->assertSame($packages[$name], $package);
        }
    }
    
    /**
     * Tests the {@link PHP_Depend_Code_DefaultBuilder::getPackages()} method.
     *
     * @return void
     */
    public function testGetPackages()
    {
        $builder = new PHP_Depend_Code_DefaultBuilder();
        
        $packages = array(
            'package1'  =>  $builder->buildPackage('package1'),
            'package2'  =>  $builder->buildPackage('package2'),
            'package3'  =>  $builder->buildPackage('package3')
        );
        
        foreach ($builder->getPackages() as $name => $package) {
            $this->assertArrayHasKey($name, $packages);
            $this->assertEquals($name, $package->getName());
            $this->assertSame($packages[$name], $package);
        }
    }
    
    /**
     * Tests that the function build method creates an unique function instance.
     *
     * @return void
     */
    public function testBuildFunctionUnique()
    {
        $builder = new PHP_Depend_Code_DefaultBuilder(); 
        
        $function1 = $builder->buildFunction('foobar', 0);
        $function2 = $builder->buildFunction('foobar', 0);
        
        $this->assertSame($function1, $function2);
    }
    
    /**
     * Tests that the node builder appends a default package to all new created
     * functions.
     *
     * @return void
     */
    public function testBuildFunctionDefaultPackage()
    {
        $builder   = new PHP_Depend_Code_DefaultBuilder();
        $function1 = $builder->buildFunction('func1');
        $function2 = $builder->buildFunction('func2');
        
        $this->assertNotNull($function1->getPackage());
        $this->assertNotNull($function2->getPackage());
        
        $this->assertSame($function1->getPackage(), $function2->getPackage());
        $this->assertEquals(PHP_Depend_Code_NodeBuilder::DEFAULT_PACKAGE, $function1->getPackage()->getName());
    }
}