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
 * @link      http://pdepend.org/
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
        $class2  = $builder->getClass('clazz1');

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
     * Tests that {@link PHP_Depend_Builder_Default::buildClass()} returns
     * a previous class instance for a specified package, if it is called for a
     * same named class in the default package.
     *
     * @return void
     */
    public function testBuildClassReusesExistingNonDefaultPackageInstanceForDefaultPackage()
    {
        $builder = new PHP_Depend_Builder_Default();

        $class1 = $builder->buildClass('php\depend\Parser');
        $class2 = $builder->getClass('Parser');

        $this->assertSame($class1, $class2);
        $this->assertSame(
            $class1->getPackage(),
            $class2->getPackage()
        );
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
        $interface2 = $builder->getInterface('interface1');

        $this->assertType('PHP_Depend_Code_Interface', $interface1);
        $this->assertType('PHP_Depend_Code_Interface', $interface2);

        $this->assertSame($interface1, $interface2);
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
    public function testCanCreateMultipleInterfaceInstancesWithIdenticalNames()
    {
        $builder = new PHP_Depend_Builder_Default();

        $interface1 = $builder->buildInterface('php\depend\ParserI');
        $interface2 = $builder->buildInterface('php\depend\ParserI');

        $this->assertNotSame($interface1, $interface2);
        $this->assertSame(
            $interface1->getPackage(),
            $interface2->getPackage()
        );
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

        $defaultInterface = $builder->getInterface('ParserI');
        $defaultPackage   = $defaultInterface->getPackage();

        $this->assertSame($defaultInterface, $pdependInterface);
        $this->assertSame($defaultPackage, $pdependPackage);
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
     * There was a missing check within an if statement, so that the builder
     * has alway overwritten previously created instances.
     *
     * @return void
     */
    public function testBuildClassDoesNotOverwritePreviousInstances()
    {
        $builder = new PHP_Depend_Builder_Default();

        $class1 = $builder->buildClass('FooBar');
        $class2 = $builder->buildClass('FooBar');

        $this->assertNotSame($class1, $class2);
        $this->assertSame($class1, $builder->getClass('FooBar'));
    }

    /**
     * There was a missing check within an if statement, so that the builder
     * has alway overwritten previously created instances.
     *
     * @return void
     */
    public function testBuildInterfaceDoesNotOverwritePreviousInstances()
    {
        $builder = new PHP_Depend_Builder_Default();

        $interface1 = $builder->buildInterface('FooBar');
        $interface2 = $builder->buildInterface('FooBar');

        $this->assertNotSame($interface1, $interface2);
        $this->assertSame($interface1, $builder->getInterface('FooBar'));
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
        $classB = $builder->getClass('php_Depend_parser');

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
        $interfaceB = $builder->getInterface('php_Depend_tokenizeri');

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
        $interfaceB = $builder->getClassOrInterface('php_Depend_tokenizeri');

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
        $classB = $builder->getClassOrInterface('php_Depend_parser');

        $this->assertSame($classA, $classB);
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     *
     * @return void
     */
    public function testBuildClassOrInterfaceReferenceThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = new PHP_Depend_Builder_Default();
        $builder->buildClassOrInterfaceReference('Foo');

        // Freeze object
        $builder->getClass('Foo');

        $this->setExpectedException(
            'BadMethodCallException',
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildClassOrInterfaceReference('Bar');
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     *
     * @return void
     */
    public function testBuildClassThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = new PHP_Depend_Builder_Default();
        $builder->buildClass('Foo');

        // Freeze object
        $builder->getClass('Foo');

        $this->setExpectedException(
            'BadMethodCallException',
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildClass('Bar');
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     *
     * @return void
     */
    public function testBuildClassReferenceThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = new PHP_Depend_Builder_Default();
        $builder->buildClassReference('Foo');

        // Freeze object
        $builder->getClass('Foo');

        $this->setExpectedException(
            'BadMethodCallException',
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildClassReference('Bar');
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     *
     * @return void
     */
    public function testBuildClosureThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = new PHP_Depend_Builder_Default();
        $builder->buildClosure('clo');

        // Freeze object
        $builder->getClass('Foo');

        $this->setExpectedException(
            'BadMethodCallException',
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildClosure('sure');
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     *
     * @return void
     */
    public function testBuildTypeConstantThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = new PHP_Depend_Builder_Default();
        $builder->buildTypeConstant('cons');

        // Freeze object
        $builder->getClass('Foo');

        $this->setExpectedException(
            'BadMethodCallException',
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildTypeConstant('tant');
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     *
     * @return void
     */
    public function testBuildInterfaceThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = new PHP_Depend_Builder_Default();
        $builder->buildInterface('Inter');

        // Freeze object
        $builder->getInterface('Inter');

        $this->setExpectedException(
            'BadMethodCallException',
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildInterface('Face');
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     *
     * @return void
     */
    public function testBuildInterfaceReferenceThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = new PHP_Depend_Builder_Default();
        $builder->buildInterfaceReference('Inter');

        // Freeze object
        $builder->getInterface('Inter');

        $this->setExpectedException(
            'BadMethodCallException',
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildInterfaceReference('Face');
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     *
     * @return void
     */
    public function testBuildMethodThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = new PHP_Depend_Builder_Default();
        $builder->buildMethod('call');

        // Freeze object
        $builder->getInterface('Inter');

        $this->setExpectedException(
            'BadMethodCallException',
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildMethod('invoke');
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     *
     * @return void
     */
    public function testBuildParameterThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = new PHP_Depend_Builder_Default();
        $builder->buildParameter('param');

        // Freeze object
        $builder->getInterface('Inter');

        $this->setExpectedException(
            'BadMethodCallException',
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildParameter('param');
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     *
     * @return void
     */
    public function testBuildPropertyThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = new PHP_Depend_Builder_Default();
        $builder->buildProperty('prop');

        // Freeze object
        $builder->getInterface('Inter');

        $this->setExpectedException(
            'BadMethodCallException',
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildProperty('prop');
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     *
     * @return void
     */
    public function testBuildFunctionThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = new PHP_Depend_Builder_Default();
        $builder->buildFunction('func');

        // Freeze object
        $builder->getInterface('Inter');

        $this->setExpectedException(
            'BadMethodCallException',
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildFunction('prop');
    }
}