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
 * @version   SVN: $Id: DefaultTest.php 675 2009-03-05 07:40:28Z mapi $
 * @link      http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Depend/Builder/Default.php';
require_once 'PHP/Depend/Code/File.php';

/**
 * Test case implementation for the default node builder implementation.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Builder_DefaultTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the node builder creates a class for the same name only once.
     *
     * @return void
     */
    public function testBuildClassUnique()
    {
        $builder = new PHP_Depend_Builder_Default();
        $class1  = $builder->buildClass('clazz1');
        $class2  = $builder->buildClass('clazz1');

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
        $defaultPackage = PHP_Depend_BuilderI::DEFAULT_PACKAGE;

        $builder = new PHP_Depend_Builder_Default();
        $class1  = $builder->buildClass('clazz1');
        $class2  = $builder->buildClass('clazz2');

        $this->assertNotNull($class1->getPackage());
        $this->assertNotNull($class2->getPackage());

        $this->assertSame($class1->getPackage(), $class2->getPackage());
        $this->assertEquals($defaultPackage, $class1->getPackage()->getName());
    }

    /**
     * Tests that the {@link PHP_Depend_Builder_Default::buildClass()} method
     * creates two different class instances for the same class name, but
     * different packages.
     *
     * @return void
     */
    public function testBuildClassCreatesTwoDifferentInstancesForDifferentPackages()
    {
        $builder = new PHP_Depend_Builder_Default();
        $class1  = $builder->buildClass('php\depend1\Parser');
        $class2  = $builder->buildClass('php\depend2\Parser');

        $this->assertNotSame($class1, $class2);
    }

    /**
     * Tests that {@link PHP_Depend_Builder_Default::buildClass()} reuses
     * an existing default package class instance with in a new specified package.
     *
     * @return void
     */
    public function testBuildClassReplacesDefaultPackageInstanceBySpecifiedPackage()
    {
        $builder = new PHP_Depend_Builder_Default();

        $defaultClass   = $builder->buildClass('Parser');
        $defaultPackage = $defaultClass->getPackage();

        $pdependClass   = $builder->buildClass('php\depend\Parser');
        $pdependPackage = $pdependClass->getPackage();

        $this->assertSame($defaultClass, $pdependClass);
        $this->assertEquals(0, $defaultPackage->getClasses()->count());
        $this->assertEquals(1, $pdependPackage->getClasses()->count());
    }

    /**
     * Tests that {@link PHP_Depend_Builder_Default::buildClass()} returns
     * a previous class instance for a specified package, if it is called for a
     * same named class in the default package.
     *
     * @return void
     */
    public function testBuildClassReusesExistingNonDefaultPackageInstanceForDefaultPackage()
    {
        $builder = new PHP_Depend_Builder_Default();

        $pdependClass   = $builder->buildClass('php\depend\Parser');
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
        $builder    = new PHP_Depend_Builder_Default();
        $interface1 = $builder->buildInterface('interface1');
        $interface2 = $builder->buildInterface('interface1');

        $this->assertType('PHP_Depend_Code_Interface', $interface1);
        $this->assertType('PHP_Depend_Code_Interface', $interface2);

        $this->assertSame($interface1, $interface2);
    }

    /**
     * Tests that the build interface method recreates an existing class as
     * interface.
     *
     * @return void
     */
    public function testBuildInterfaceForcesRecreateTypeForExistingClass()
    {
        $builder = new PHP_Depend_Builder_Default();

        $type0 = $builder->buildClassOrInterface('FooBar');
        $this->assertType('PHP_Depend_Code_Class', $type0);
        $type1 = $builder->buildInterface('FooBar');
        $this->assertType('PHP_Depend_Code_Interface', $type1);
        $type2 = $builder->buildClassOrInterface('FooBar');
        $this->assertType('PHP_Depend_Code_Interface', $type2);
    }

    public function testBuildInterfaceForcesRecreateTypeForExistingClassInDefaultPackage()
    {
        $builder = new PHP_Depend_Builder_Default();

        $defaultClass   = $builder->buildClass('ParserI');
        $defaultPackage = $defaultClass->getPackage();

        $pdependInterface = $builder->buildInterface('php\depend\ParserI');
        $pdependPackage   = $pdependInterface->getPackage();

        $this->assertNotSame($defaultClass, $pdependInterface);
        $this->assertEquals(0, $defaultPackage->getClasses()->count());
        $this->assertEquals(1, $pdependPackage->getInterfaces()->count());
    }

    /**
     * Tests that the {@link PHP_Depend_Builder_Default::buildInterface()}
     * method only removes/replaces a previously created class instance, when
     * this class is part of the default namespace. Otherwise there are two user
     * types with the same local or package internal name.
     *
     * @return void
     */
    public function testBuildInterfaceDoesntRemoveClassForSameNamedInterface()
    {
        $builder = new PHP_Depend_Builder_Default();

        $package1 = $builder->buildPackage('package1');
        $package2 = $builder->buildPackage('package2');

        $class = $builder->buildClass('Parser');
        $package1->addType($class);

        $this->assertEquals(1, $package1->getTypes()->count());

        $interface = $builder->buildInterface('Parser');

        $this->assertEquals(1, $package1->getTypes()->count());
    }

    /**
     * Tests that a type recreate forces all class dependencies to be updated.
     *
     * @return void
     */
    public function testBuildInterfaceForcesUpdateClassDependencies()
    {
        $builder = new PHP_Depend_Builder_Default();

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
        $builder = new PHP_Depend_Builder_Default();

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
        $builder = new PHP_Depend_Builder_Default();

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
        $builder = new PHP_Depend_Builder_Default();

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
     * Tests that a type recreate forces the parameter type to be updated.
     *
     * @return void
     */
    public function testBuildInterfaceForcesUpdateParameterType()
    {
        $builder = new PHP_Depend_Builder_Default();

        $param  = $builder->buildParameter('$bar', 0);
        $type0  = $builder->buildClassOrInterface('FooBar');

        $this->assertNull($param->getClass());
        $param->setClass($type0);
        $this->assertSame($type0, $param->getClass());

        $type1 = $builder->buildInterface('FooBar');
        $this->assertSame($type1, $param->getClass());
    }

    /**
     * Tests that {@link PHP_Depend_Builder_Default::buildInterface()} creates
     * different interface instances for different parent packages.
     *
     * @return void
     */
    public function testBuildInterfacesCreatesDifferentInstancesForDifferentPackages()
    {
        $builder = new PHP_Depend_Builder_Default();

        $interfaces1 = $builder->buildInterface('php\depend1\ParserI');
        $interfaces2 = $builder->buildInterface('php\depend2\ParserI');

        $this->assertNotSame($interfaces1, $interfaces2);
    }

    /**
     * Tests that {@link PHP_Depend_Builder_Default::buildInterface()}
     * replaces an existing default package interface instance, if it creates a
     * more specific version.
     *
     * @return void
     */
    public function testBuildInterfaceReplacesDefaultInstanceForSpecifiedPackage()
    {
        $builder = new PHP_Depend_Builder_Default();

        $defaultInterface = $builder->buildInterface('ParserI');
        $defaultPackage   = $defaultInterface->getPackage();

        $pdependInterface = $builder->buildInterface('php\depend\ParserI');
        $pdependPackage   = $pdependInterface->getPackage();

        $this->assertSame($defaultInterface, $pdependInterface);
        $this->assertEquals(1, $pdependPackage->getInterfaces()->count());
        $this->assertEquals(0, $defaultPackage->getInterfaces()->count());
    }

    /**
     * Tests that {@link PHP_Depend_Builder_Default::buildInterface()} returns
     * a previous interface instance for a specified package, if it is called
     * for a same named interface in the default package.
     *
     * @return void
     */
    public function testBuildInterfaceReusesExistingNonDefaultPackageInstanceForDefaultPackage()
    {
        $builder = new PHP_Depend_Builder_Default();

        $pdependInterface = $builder->buildInterface('php\depend\ParserI');
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
    public function testBuildInterfaceForcesUpdatePropertyType()
    {
        $builder = new PHP_Depend_Builder_Default();

        $property = $builder->buildProperty('$bar', 0);
        $type0    = $builder->buildClassOrInterface('PDepend');

        $property->setType($type0);
        $this->assertSame($type0, $property->getType());

        $type1 = $builder->buildInterface('PDepend');
        $this->assertSame($type1, $property->getType());
    }

    /**
     * Tests that the default builder updates an existing reference for a
     * method return type.
     *
     * @return void
     */
    public function testBuildInterfaceForcesUpdateMethodReturnType()
    {
        $builder = new PHP_Depend_Builder_Default();

        $method = $builder->buildMethod('bar', 0);
        $type0  = $builder->buildClassOrInterface('PDepend');

        $method->setReturnType($type0);
        $this->assertSame($type0, $method->getReturnType());

        $type1 = $builder->buildInterface('PDepend');
        $this->assertSame($type1, $method->getReturnType());
    }

    /**
     * Tests that the default builder updates an existing reference for a
     * method exceptiion type.
     *
     * @return void
     */
    public function testBuildInterfaceForcesUpdateMethodExceptionType()
    {
        $builder = new PHP_Depend_Builder_Default();

        $method = $builder->buildMethod('bar', 0);
        $type0  = $builder->buildClassOrInterface('PDepend');

        $method->addExceptionType($type0);
        $this->assertEquals(1, $method->getExceptionTypes()->count());
        $this->assertSame($type0, $method->getExceptionTypes()->current());

        $type1 = $builder->buildInterface('PDepend');
        $this->assertEquals(1, $method->getExceptionTypes()->count());
        $this->assertSame($type1, $method->getExceptionTypes()->current());
    }

    /**
     * Tests that the default builder updates an existing reference for a
     * function return type.
     *
     * @return void
     */
    public function testBuildInterfaceForcesUpdateFunctionReturnType()
    {
        $builder = new PHP_Depend_Builder_Default();

        $function = $builder->buildFunction('bar', 0);
        $type0    = $builder->buildClassOrInterface('PDepend');

        $function->setReturnType($type0);
        $this->assertSame($type0, $function->getReturnType());

        $type1 = $builder->buildInterface('PDepend');
        $this->assertSame($type1, $function->getReturnType());
    }

    /**
     * Tests that the default builder updates an existing reference for a
     * function exception type.
     *
     * @return void
     */
    public function testBuildInterfaceForcesUpdateFunctionExceptionType()
    {
        $builder = new PHP_Depend_Builder_Default();

        $function = $builder->buildFunction('bar', 0);
        $type0    = $builder->buildClassOrInterface('PDepend');

        $function->addExceptionType($type0);
        $this->assertEquals(1, $function->getExceptionTypes()->count());
        $this->assertSame($type0, $function->getExceptionTypes()->current());

        $type1 = $builder->buildInterface('PDepend');
        $this->assertEquals(1, $function->getExceptionTypes()->count());
        $this->assertSame($type1, $function->getExceptionTypes()->current());
    }

    /**
     * Tests the PHP_Depend_Code_Method build method.
     *
     * @return void
     */
    public function testBuildMethod()
    {
        $builder = new PHP_Depend_Builder_Default();
        $method  = $builder->buildMethod('method', 0);

        $this->assertType('PHP_Depend_Code_Method', $method);
    }

    /**
     * Tests the {@link PHP_Depend_Builder_Default::buildTypeConstant()}
     * method.
     *
     * @return void
     */
    public function testBuildConstant()
    {
        $builder  = new PHP_Depend_Builder_Default();
        $constant = $builder->buildTypeConstant('CONSTANT', 0);

        $this->assertType('PHP_Depend_Code_TypeConstant', $constant);
    }

    /**
     * Tests that the node builder creates a package for the same name only once.
     *
     * @return void
     */
    public function testBuildPackageUnique()
    {
        $builder  = new PHP_Depend_Builder_Default();
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
        $builder = new PHP_Depend_Builder_Default();

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
     * Tests the {@link PHP_Depend_Builder_Default::getPackages()} method.
     *
     * @return void
     */
    public function testGetPackages()
    {
        $builder = new PHP_Depend_Builder_Default();

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
        $builder = new PHP_Depend_Builder_Default();

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
        $defaultPackage = PHP_Depend_BuilderI::DEFAULT_PACKAGE;

        $builder   = new PHP_Depend_Builder_Default();
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
        $builder = new PHP_Depend_Builder_Default();

        $classA = $builder->buildClass('PHP_Depend_Parser');
        $classB = $builder->buildClass('php_Depend_parser');

        $this->assertSame($classA, $classB);
    }

    /**
     * Tests that the node builder works case insensitive for interface names.
     *
     * @return void
     */
    public function testBuildInterfaceWorksCaseInsensitiveIssue26()
    {
        $builder = new PHP_Depend_Builder_Default();

        $interfaceA = $builder->buildInterface('PHP_Depend_TokenizerI');
        $interfaceB = $builder->buildInterface('php_Depend_tokenizeri');

        $this->assertSame($interfaceA, $interfaceB);
    }

    /**
     * Tests that the node builder works case insensitive for interface names.
     *
     * @return void
     */
    public function testBuildClassOrInterfaceWorksCaseInsensitive1Issue26()
    {
        $builder = new PHP_Depend_Builder_Default();

        $interfaceA = $builder->buildInterface('PHP_Depend_TokenizerI');
        $interfaceB = $builder->buildClassOrInterface('php_Depend_tokenizeri');

        $this->assertSame($interfaceA, $interfaceB);
    }

    /**
     * Tests that the node builder works case insensitive for interface names.
     *
     * @return void
     */
    public function testBuildClassOrInterfaceWorksCaseInsensitive2Issue26()
    {
        $builder = new PHP_Depend_Builder_Default();

        $classA = $builder->buildClass('PHP_Depend_Parser');
        $classB = $builder->buildClassOrInterface('php_Depend_parser');

        $this->assertSame($classA, $classB);
    }

    /**
     * Tests that the node builder works case insensitive for interface names.
     *
     * @return void
     */
    public function testBuildClassOrInterfaceWorksCaseInsensitive3Issue26()
    {
        $builder = new PHP_Depend_Builder_Default();

        $classA = $builder->buildClassOrInterface('PHP_Depend_Parser');
        $classB = $builder->buildClassOrInterface('php_Depend_parser');

        $this->assertSame($classA, $classB);
    }
}