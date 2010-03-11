<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2010, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2010 Manuel Pichler. All rights reserved.
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
 * @copyright 2008-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_Depend_Builder_DefaultTest extends PHP_Depend_AbstractTest
{
    /**
     * testBuilderAddsMultiplePackagesForClassesToListOfPackages
     *
     * @return void
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuilderAddsMultiplePackagesForClassesToListOfPackages()
    {
        $builder = new PHP_Depend_Builder_Default();

        $package = $builder->buildPackage(__FUNCTION__);
        $package->addType($builder->buildClass(__FUNCTION__));

        $package = $builder->buildPackage(__CLASS__);
        $package->addType($builder->buildClass(__CLASS__));

        $this->assertEquals(2, $builder->getPackages()->count());
    }

    /**
     * testBuilderAddsMultiplePackagesForFunctionsToListOfPackages
     *
     * @return void
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuilderAddsMultiplePackagesForFunctionsToListOfPackages()
    {
        $builder = new PHP_Depend_Builder_Default();

        $package = $builder->buildPackage(__FUNCTION__);
        $builder->buildFunction(__FUNCTION__);

        $package = $builder->buildPackage(__CLASS__);
        $builder->buildFunction(__CLASS__);

        $this->assertEquals(2, $builder->getPackages()->count());
    }

    /**
     * testBuilderNotAddsNewPackagesOnceItHasReturnedTheListOfPackages
     *
     * @return void
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuilderNotAddsNewPackagesOnceItHasReturnedTheListOfPackages()
    {
        $builder = new PHP_Depend_Builder_Default();

        $package = $builder->buildPackage(__FUNCTION__);
        $package->addFunction($builder->buildFunction(__FUNCTION__));

        $builder->getPackages();
        
        $package = $builder->buildPackage(__CLASS__);
        $package->addType($builder->buildClass(__CLASS__));

        $this->assertEquals(1, $builder->getPackages()->count());
    }

    /**
     * Tests that the node builder creates a class for the same name only once.
     *
     * @return void
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildClassUnique()
    {
        $builder = new PHP_Depend_Builder_Default();

        $class = $builder->buildClass(__FUNCTION__);
        $class->setPackage($builder->buildPackage(__FUNCTION__));
        
        $this->assertSame($class, $builder->getClass(__FUNCTION__));
    }

    /**
     * Tests that the {@link PHP_Depend_Builder_Default::buildClass()} method
     * creates two different class instances for the same class name, but
     * different packages.
     *
     * @return void
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
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
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildClassReusesExistingNonDefaultPackageInstanceForDefaultPackage()
    {
        $builder = new PHP_Depend_Builder_Default();

        $class1 = $builder->buildClass('php\depend\Parser');
        $class1->setPackage($builder->buildPackage(__FUNCTION__));

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
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildInterfaceUnique()
    {
        $builder = new PHP_Depend_Builder_Default();
        
        $interface = $builder->buildInterface(__FUNCTION__);
        $interface->setPackage($builder->buildPackage(__FUNCTION__));

        $this->assertSame($interface, $builder->getInterface(__FUNCTION__));
    }

    /**
     * Tests that the {@link PHP_Depend_Builder_Default::buildInterface()}
     * method only removes/replaces a previously created class instance, when
     * this class is part of the default namespace. Otherwise there are two user
     * types with the same local or package internal name.
     *
     * @return void
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
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
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
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
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
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
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildInterfaceReusesExistingNonDefaultPackageInstanceForDefaultPackage()
    {
        $builder = new PHP_Depend_Builder_Default();

        $pdependInterface = $builder->buildInterface('php\depend\ParserI');
        $pdependInterface->setPackage($builder->buildPackage(__FUNCTION__));

        $defaultInterface = $builder->getInterface('ParserI');

        $this->assertSame($defaultInterface, $pdependInterface);
        $this->assertSame($defaultInterface->getPackage(), $pdependInterface->getPackage());
    }

    /**
     * Tests the PHP_Depend_Code_Method build method.
     *
     * @return void
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildMethod()
    {
        $builder = new PHP_Depend_Builder_Default();
        $method  = $builder->buildMethod('method', 0);

        $this->assertType('PHP_Depend_Code_Method', $method);
    }

    /**
     * Tests that the node builder creates a package for the same name only once.
     *
     * @return void
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
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
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
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
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
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
     * There was a missing check within an if statement, so that the builder
     * has alway overwritten previously created instances.
     *
     * @return void
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildClassDoesNotOverwritePreviousInstances()
    {
        $builder = new PHP_Depend_Builder_Default();

        $class1 = $builder->buildClass('FooBar');
        $class1->setPackage($builder->buildPackage(__FUNCTION__));

        $class2 = $builder->buildClass('FooBar');
        $class2->setPackage($builder->buildPackage(__FUNCTION__));

        $this->assertNotSame($class1, $class2);
        $this->assertSame($class1, $builder->getClass('FooBar'));
    }

    /**
     * There was a missing check within an if statement, so that the builder
     * has alway overwritten previously created instances.
     *
     * @return void
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildInterfaceDoesNotOverwritePreviousInstances()
    {
        $builder = new PHP_Depend_Builder_Default();

        $interface = $builder->buildInterface('FooBar');
        $interface->setPackage($builder->buildPackage(__FUNCTION__));

        $this->assertNotSame($interface, $builder->buildInterface('FooBar'));
        $this->assertSame($interface, $builder->getInterface('FooBar'));
    }

    /**
     * Tests that the node builder works case insensitive for class names.
     *
     * @return void
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildClassWorksCaseInsensitiveIssue26()
    {
        $builder = new PHP_Depend_Builder_Default();

        $class = $builder->buildClass('PHP_Depend_Parser');
        $class->setPackage($builder->buildPackage(__FUNCTION__));

        $this->assertSame($class, $builder->getClass('php_Depend_parser'));
    }

    /**
     * Tests that the node builder works case insensitive for interface names.
     *
     * @return void
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildInterfaceWorksCaseInsensitiveIssue26()
    {
        $builder = new PHP_Depend_Builder_Default();

        $interface = $builder->buildInterface('PHP_Depend_TokenizerI');
        $interface->setPackage($builder->buildPackage(__FUNCTION__));

        $this->assertSame($interface, $builder->getInterface('php_Depend_tokenizeri'));
    }

    /**
     * Tests that the node builder works case insensitive for interface names.
     *
     * @return void
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildClassOrInterfaceWorksCaseInsensitive1Issue26()
    {
        $builder = new PHP_Depend_Builder_Default();

        $interface = $builder->buildInterface('PHP_Depend_TokenizerI');
        $interface->setPackage($builder->buildPackage(__FUNCTION__));

        $this->assertSame($interface, $builder->getClassOrInterface('php_Depend_tokenizeri'));
    }

    /**
     * Tests that the node builder works case insensitive for interface names.
     *
     * @return void
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildClassOrInterfaceWorksCaseInsensitive2Issue26()
    {
        $builder = new PHP_Depend_Builder_Default();

        $class = $builder->buildClass('PHP_Depend_Parser');
        $class->setPackage($builder->buildPackage(__FUNCTION__));

        $this->assertSame($class, $builder->getClassOrInterface('php_Depend_parser'));
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     *
     * @return void
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildASTClassOrInterfaceReferenceThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = new PHP_Depend_Builder_Default();
        $builder->buildASTClassOrInterfaceReference('Foo');

        // Freeze object
        $builder->getClass('Foo');

        $this->setExpectedException(
            'BadMethodCallException',
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildASTClassOrInterfaceReference('Bar');
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     *
     * @return void
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
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
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildASTClassReferenceThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = new PHP_Depend_Builder_Default();
        $builder->buildASTClassReference('Foo');

        // Freeze object
        $builder->getClass('Foo');

        $this->setExpectedException(
            'BadMethodCallException',
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildASTClassReference('Bar');
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     *
     * @return void
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
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
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
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
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
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
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
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
     * @covers PHP_Depend_Builder_Default
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
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