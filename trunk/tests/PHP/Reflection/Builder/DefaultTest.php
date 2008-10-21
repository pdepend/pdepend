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
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Builder
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Reflection/Builder/Default.php';
require_once 'PHP/Reflection/AST/File.php';

/**
 * Test case implementation for the default node builder implementation.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_Builder_DefaultTest extends PHP_Reflection_AbstractTest
{
    /**
     * Tests that the node builder creates a class for the same name only once.
     *
     * @return void
     */
    public function testBuildClassUnique()
    {
        $builder = new PHP_Reflection_Builder_Default();
        $class1  = $builder->buildClass('clazz1');
        $class2  = $builder->buildClass('clazz1');
        
        $this->assertType('PHP_Reflection_AST_Class', $class1);
        $this->assertType('PHP_Reflection_AST_Class', $class2);
        
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
        $defaultPackage = PHP_Reflection_BuilderI::GLOBAL_PACKAGE;

        $builder = new PHP_Reflection_Builder_Default();
        $class1  = $builder->buildClass('clazz1');
        $class2  = $builder->buildClass('clazz2');
        
        $this->assertNotNull($class1->getPackage());
        $this->assertNotNull($class2->getPackage());
        
        $this->assertSame($class1->getPackage(), $class2->getPackage());
        $this->assertEquals($defaultPackage, $class1->getPackage()->getName());
    }
    
    /**
     * Tests that the {@link PHP_Reflection_Builder_Default::buildClass()} method
     * creates two different class instances for the same class name, but 
     * different packages.
     *
     * @return void
     */
    public function testBuildClassCreatesTwoDifferentInstancesForDifferentPackages()
    {
        $builder = new PHP_Reflection_Builder_Default();
        $class1  = $builder->buildClass('php::reflection1::Parser');
        $class2  = $builder->buildClass('php::reflection2::Parser');
        
        $this->assertNotSame($class1, $class2);
    }
    
    /**
     * Tests that {@link PHP_Reflection_Builder_Default::buildClass()} reuses
     * an existing default package class instance with in a new specified package.
     *
     * @return void
     */
    public function testBuildClassReplacesDefaultPackageInstanceBySpecifiedPackage()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
        $defaultClass   = $builder->buildClass('Parser');
        $defaultPackage = $defaultClass->getPackage();
        
        $pdependClass   = $builder->buildClass('php::depend::Parser');
        $pdependPackage = $pdependClass->getPackage();
        
        $this->assertSame($defaultClass, $pdependClass);
        $this->assertEquals(0, $defaultPackage->getClasses()->count());
        $this->assertEquals(1, $pdependPackage->getClasses()->count());
    }
    
    /**
     * Tests that {@link PHP_Reflection_Builder_Default::buildClass()} returns
     * a previous class instance for a specified package, if it is called for a
     * same named class in the default package.
     *
     * @return void
     */
    public function testBuildClassReusesExistingNonDefaultPackageInstanceForDefaultPackage()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
        $pdependClass   = $builder->buildClass('php::depend::Parser');
        $pdependPackage = $pdependClass->getPackage();
        
        $defaultClass   = $builder->buildClass('Parser');
        $defaultPackage = $defaultClass->getPackage();
        
        $this->assertSame($defaultClass, $pdependClass);
        $this->assertSame($defaultPackage, $pdependPackage);
    }
    
    /**
     * Tests that the node build generates an unique interface instance for the
     * same identifier.
     *
     * @return void
     */
    public function testBuildInterfaceUnique()
    {
        $builder    = new PHP_Reflection_Builder_Default();
        $interface1 = $builder->buildInterface('interface1');
        $interface2 = $builder->buildInterface('interface1');
        
        $this->assertType('PHP_Reflection_AST_Interface', $interface1);
        $this->assertType('PHP_Reflection_AST_Interface', $interface2);
        
        $this->assertSame($interface1, $interface2);
    }

    /**
     * Tests that the {@link PHP_Reflection_Builder_Default::buildInterface()}
     * method only removes/replaces a previously created class instance, when 
     * this class is part of the default namespace. Otherwise there are two user
     * types with the same local or package internal name.
     *
     * @return void
     */
    public function testBuildInterfaceDoesntRemoveClassForSameNamedInterface()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
        $package1 = $builder->buildPackage('package1');
        $package2 = $builder->buildPackage('package2');
        
        $class = $builder->buildClass('Parser');
        $package1->addType($class);
        
        $this->assertEquals(1, $package1->getTypes()->count());

        $interface = $builder->buildInterface('Parser');
        
        $this->assertEquals(1, $package1->getTypes()->count());
    }
    
    /**
     * Tests that a class or interface proxy is handled correct as function 
     * dependency.
     *
     * @return void
     */
    public function testBuildClassOrInterfaceAsFunctionDependency()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
        $function = $builder->buildFunction('bar', 0);
        $proxy    = $builder->buildClassOrInterfaceProxy('FooBar');
        
        $function->addDependency($proxy);
        $this->assertEquals(1, $function->getDependencies()->count());
        $this->assertSame($proxy, $function->getDependencies()->current());
        
        $interface = $builder->buildInterface('FooBar');
        $this->assertEquals(1, $function->getDependencies()->count());
        $this->assertTrue($interface->equals($function->getDependencies()->current()));
    }
    
    /**
     * Tests that a class or interface proxy is handled correct as method 
     * dependency.
     *
     * @return void
     */
    public function testBuildClassOrInterfaceAsMethodDependency()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
        $method = $builder->buildMethod('bar', 0);
        $proxy  = $builder->buildClassOrInterfaceProxy('FooBar');
        
        $method->addDependency($proxy);
        $this->assertEquals(1, $method->getDependencies()->count());
        $this->assertSame($proxy, $method->getDependencies()->current());
        
        $interface = $builder->buildInterface('FooBar');
        $this->assertEquals(1, $method->getDependencies()->count());
        $this->assertTrue($interface->equals($method->getDependencies()->current()));
    }
    
    /**
     * Tests that a class/interface proxy is handled correct as a method/function
     * parameter type.
     *
     * @return void
     */
    public function testBuildClassOrInterfaceAsParameterType()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
        $param = $builder->buildParameter('$bar', 0);
        $proxy = $builder->buildClassOrInterfaceProxy('FooBar');
        
        $this->assertNull($param->getType());
        $param->setType($proxy);
        $this->assertSame($proxy, $param->getType());
        
        $interface = $builder->buildInterface('FooBar');
        $this->assertTrue($interface->equals($param->getType()));
    }
    
    /**
     * Tests that {@link PHP_Reflection_Builder_Default::buildInterface()} creates
     * different interface instances for different parent packages.
     *
     * @return void
     */
    public function testBuildInterfacesCreatesDifferentInstancesForDifferentPackages()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
        $interfaces1 = $builder->buildInterface('php::depend1::ParserI');
        $interfaces2 = $builder->buildInterface('php::depend2::ParserI');
        
        $this->assertNotSame($interfaces1, $interfaces2);
    }
    
    /**
     * Tests that {@link PHP_Reflection_Builder_Default::buildInterface()} 
     * replaces an existing default package interface instance, if it creates a 
     * more specific version. 
     *
     * @return void
     */
    public function testBuildInterfaceReplacesDefaultInstanceForSpecifiedPackage()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
        $defaultInterface = $builder->buildInterface('ParserI');
        $defaultPackage   = $defaultInterface->getPackage();
        
        $pdependInterface = $builder->buildInterface('php::depend::ParserI');
        $pdependPackage   = $pdependInterface->getPackage();
        
        $this->assertSame($defaultInterface, $pdependInterface);
        $this->assertEquals(1, $pdependPackage->getInterfaces()->count());
        $this->assertEquals(0, $defaultPackage->getInterfaces()->count());
    }
    
    /**
     * Tests that {@link PHP_Reflection_Builder_Default::buildInterface()} returns
     * a previous interface instance for a specified package, if it is called 
     * for a same named interface in the default package.
     *
     * @return void
     */
    public function testBuildInterfaceReusesExistingNonDefaultPackageInstanceForDefaultPackage()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
        $pdependInterface = $builder->buildInterface('php::depend::ParserI');
        $pdependPackage   = $pdependInterface->getPackage();
        
        $defaultInterface = $builder->buildInterface('ParserI');
        $defaultPackage   = $defaultInterface->getPackage();
        
        $this->assertSame($defaultInterface, $pdependInterface);
        $this->assertSame($defaultPackage, $pdependPackage);
    }
    
    /**
     * Tests that the default builder updates an existing reference for a 
     * property type.
     *
     * @return void
     */
    public function testBuildClassOrInterfaceAsPropertyType()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
        $property = $builder->buildProperty('bar', 0);
        $proxy    = $builder->buildClassOrInterfaceProxy('PDepend');
        
        $property->setType($proxy);
        $this->assertSame($proxy, $property->getType());
        
        $interface = $builder->buildInterface('PDepend');
        $this->assertTrue($interface->equals($property->getType()));
    }
    
    /**
     * Tests that a class/interface proxy works as a method return type.
     *
     * @return void
     */
    public function testBuildClassOrInterfaceAsMethodReturnType()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
        $method = $builder->buildMethod('bar', 0);
        $proxy  = $builder->buildClassOrInterfaceProxy('PDepend');
        
        $method->setReturnType($proxy);
        $this->assertSame($proxy, $method->getReturnType());
        
        $interface = $builder->buildInterface('PDepend');
        $this->assertTrue($interface->equals($method->getReturnType()));
    }
    
    /**
     * Tests that a class/interface proxy works as a method exception type.
     *
     * @return void
     */
    public function testBuildClassOrInterfaceAsMethodExceptionType()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
        $method = $builder->buildMethod('bar', 0);
        $proxy  = $builder->buildClassOrInterfaceProxy('PDepend');
        
        $method->addExceptionType($proxy);
        $this->assertEquals(1, $method->getExceptionTypes()->count());
        $this->assertSame($proxy, $method->getExceptionTypes()->current());
        
        $interface = $builder->buildInterface('PDepend');
        $this->assertEquals(1, $method->getExceptionTypes()->count());
        $this->assertTrue($interface->equals($method->getExceptionTypes()->current()));
    }
    
    /**
     * Tests that a class/interface proxy works a function return type.
     *
     * @return void
     */
    public function testBuildClassOrInterfaceAsFunctionReturnType()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
        $function = $builder->buildFunction('bar', 0);
        $proxy    = $builder->buildClassOrInterfaceProxy('PDepend');
        
        $function->setReturnType($proxy);
        $this->assertSame($proxy, $function->getReturnType());
        
        $interface = $builder->buildInterface('PDepend');
        $this->assertTrue($interface->equals($function->getReturnType()));
    }
    
    /**
     * Tests that a class/interface proxy works as a function exception type.
     *
     * @return void
     */
    public function testBuildClassOrInterfaceAsFunctionExceptionType()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
        $function = $builder->buildFunction('bar', 0);
        $proxy    = $builder->buildClassOrInterfaceProxy('PDepend');
        
        $function->addExceptionType($proxy);
        $this->assertEquals(1, $function->getExceptionTypes()->count());
        $this->assertSame($proxy, $function->getExceptionTypes()->current());
        
        $interface = $builder->buildInterface('PDepend');
        $this->assertEquals(1, $function->getExceptionTypes()->count());
        $this->assertTrue($interface->equals($function->getExceptionTypes()->current()));
    }
    
    /**
     * Tests the PHP_Reflection_AST_Method build method.
     *
     * @return void
     */
    public function testBuildMethod()
    {
        $builder = new PHP_Reflection_Builder_Default();
        $method  = $builder->buildMethod('method', 0);
        
        $this->assertType('PHP_Reflection_AST_Method', $method);
    }
    
    /**
     * Tests {@link PHP_Reflection_Builder_Default::buildClassOrInterfaceConstant()}
     * 
     * @return void
     */
    public function testBuildConstant()
    {
        $builder  = new PHP_Reflection_Builder_Default();
        $constant = $builder->buildClassOrInterfaceConstant('CONSTANT', 0);
        
        $this->assertType('PHP_Reflection_AST_ClassOrInterfaceConstant', $constant);
    }
    
    /**
     * Tests that the node builder creates a package for the same name only once.
     *
     * @return void
     */
    public function testBuildPackageUnique()
    {
        $builder  = new PHP_Reflection_Builder_Default();
        $package1 = $builder->buildPackage('package1');
        $package2 = $builder->buildPackage('package1');
        
        $this->assertType('PHP_Reflection_AST_Package', $package1);
        $this->assertType('PHP_Reflection_AST_Package', $package2);
        
        $this->assertSame($package1, $package2);
    }
    
    /**
     * Tests the implemented {@link IteratorAggregate}.
     *
     * @return void
     */
    public function testGetIteratorWithPackages()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
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
     * Tests the {@link PHP_Reflection_Builder_Default::getPackages()} method.
     *
     * @return void
     */
    public function testGetPackages()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
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
        $builder = new PHP_Reflection_Builder_Default(); 
        
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
        $defaultPackage = PHP_Reflection_BuilderI::GLOBAL_PACKAGE;
        
        $builder   = new PHP_Reflection_Builder_Default();
        $function1 = $builder->buildFunction('func1', 0);
        $function2 = $builder->buildFunction('func2', 0);
        
        $this->assertNotNull($function1->getPackage());
        $this->assertNotNull($function2->getPackage());
        
        $this->assertSame($function1->getPackage(), $function2->getPackage());
        $this->assertEquals($defaultPackage, $function1->getPackage()->getName());
    }
    
    /**
     * Tests that the node builder works case insensitive for class names.
     *
     * @return void
     */
    public function testBuildClassWorksCaseInsensitiveIssue26()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
        $classA = $builder->buildClass('PHP_Reflection_Parser');
        $classB = $builder->buildClass('PHP_Reflection_parser');
        
        $this->assertSame($classA, $classB);
    }
    
    /**
     * Tests that the node builder works case insensitive for interface names.
     *
     * @return void
     */
    public function testBuildInterfaceWorksCaseInsensitiveIssue26()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
        $interfaceA = $builder->buildInterface('PHP_Reflection_TokenizerI');
        $interfaceB = $builder->buildInterface('PHP_Reflection_tokenizeri');
        
        $this->assertSame($interfaceA, $interfaceB);
    }
    
    /**
     * Tests that the node builder works case insensitive for interface names.
     *
     * @return void
     */
    public function testBuildClassOrInterfaceWorksCaseInsensitive1Issue26()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
        $interfaceA = $builder->buildInterface('PHP_Reflection_TokenizerI');
        $interfaceB = $builder->buildProxySubject('PHP_Reflection_tokenizeri');
        
        $this->assertSame($interfaceA, $interfaceB);
    }
    
    /**
     * Tests that the node builder works case insensitive for interface names.
     *
     * @return void
     */
    public function testBuildClassOrInterfaceWorksCaseInsensitive2Issue26()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
        $classA = $builder->buildClass('PHP_Reflection_Parser');
        $classB = $builder->buildProxySubject('PHP_Reflection_parser');
        
        $this->assertSame($classA, $classB);
    }
    
    /**
     * Tests that the node builder works case insensitive for interface names.
     *
     * @return void
     */
    public function testBuildClassOrInterfaceWorksCaseInsensitive3Issue26()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
        $classA = $builder->buildProxySubject('PHP_Reflection_Parser');
        $classB = $builder->buildProxySubject('PHP_Reflection_parser');
        
        $this->assertSame($classA, $classB);
    }
    
    /**
     * Tests that the node build handles PHP 5.3's syntax for the global 
     * package correct.
     * 
     * http://bugs.pdepend.org/index.php?do=details&task_id=54&project=5
     *
     * @return void
     */
    public function testBuildClassWithPHP53DefaultNamespaceSyntaxAndInternalClassNameIssue54()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
        $class = $builder->buildClass('::OutOfRangeException');
        $this->assertEquals('+spl', $class->getPackage()->getName());
    }
    
    /**
     * Tests that the node build handles PHP 5.3's syntax for the global 
     * package correct.
     * 
     * http://bugs.pdepend.org/index.php?do=details&task_id=54&project=5
     *
     * @return void
     */
    public function testBuildClassWithPHP53DefaultNamespaceSyntaxAndCustomClassNameIssue54()
    {
        $builder = new PHP_Reflection_Builder_Default();
        
        $class = $builder->buildClass('::MyClass');
        $this->assertEquals('+global', $class->getPackage()->getName());
    }
}