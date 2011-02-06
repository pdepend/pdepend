<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case implementation for the default node builder implementation.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 *
 * @covers PHP_Depend_Builder_Default
 */
class PHP_Depend_Builder_DefaultTest extends PHP_Depend_AbstractTest
{
    /**
     * testBuilderAddsMultiplePackagesForClassesToListOfPackages
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuilderAddsMultiplePackagesForClassesToListOfPackages()
    {
        $builder = $this->createBuilder();

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
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuilderAddsMultiplePackagesForFunctionsToListOfPackages()
    {
        $builder = $this->createBuilder();

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
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuilderNotAddsNewPackagesOnceItHasReturnedTheListOfPackages()
    {
        $builder = $this->createBuilder();

        $package = $builder->buildPackage(__FUNCTION__);
        $package->addFunction($builder->buildFunction(__FUNCTION__));

        $builder->getPackages();
        
        $package = $builder->buildPackage(__CLASS__);
        $package->addType($builder->buildClass(__CLASS__));

        $this->assertEquals(1, $builder->getPackages()->count());
    }

    /**
     * testRestoreFunctionAddsFunctionToPackage
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testRestoreFunctionAddsFunctionToPackage()
    {
        $builder = $this->createBuilder();
        $package = $builder->buildPackage(__CLASS__);

        $function = new PHP_Depend_Code_Function(__FUNCTION__);
        $function->setPackage($package);

        $builder->restoreFunction($function);
        self::assertEquals(1, count($package->getFunctions()));
    }

    /**
     * testRestoreFunctionUsesGetPackageNameMethod
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testRestoreFunctionUsesGetPackageNameMethod()
    {
        $function = $this->getMock(
            PHP_Depend_Code_Function::CLAZZ, array(), array(__FUNCTION__)
        );
        $function->expects($this->once())
            ->method('getPackageName');

        $builder = $this->createBuilder();
        $builder->restoreFunction($function);
    }

    /**
     * Tests that the node builder creates a class for the same name only once.
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildClassUnique()
    {
        $builder = $this->createBuilder();

        $class = $builder->buildClass(__FUNCTION__);
        $class->setPackage($builder->buildPackage(__FUNCTION__));

        $builder->restoreClass($class);
        
        self::assertSame($class, $builder->getClass(__FUNCTION__));
    }

    /**
     * Tests that the {@link PHP_Depend_Builder_Default::buildClass()} method
     * creates two different class instances for the same class name, but
     * different packages.
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildClassCreatesTwoDifferentInstancesForDifferentPackages()
    {
        $builder = $this->createBuilder();
        
        $class1 = $builder->buildClass('php\depend1\Parser');
        $class2 = $builder->buildClass('php\depend2\Parser');

        $this->assertNotSame($class1, $class2);
    }

    /**
     * Tests that {@link PHP_Depend_Builder_Default::buildClass()} returns
     * a previous class instance for a specified package, if it is called for a
     * same named class in the default package.
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildClassReusesExistingNonDefaultPackageInstanceForDefaultPackage()
    {
        $builder = $this->createBuilder();

        $class1 = $builder->buildClass('php\depend\Parser');
        $class1->setPackage($builder->buildPackage(__FUNCTION__));

        $builder->restoreClass($class1);

        self::assertSame($class1, $builder->getClass('Parser'));
        self::assertSame(
            $class1->getPackage(),
            $builder->getClass('Parser')->getPackage()
        );
    }

    /**
     * Tests that the node build generates an unique interface instance for the
     * same identifier.
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildInterfaceUnique()
    {
        $builder = $this->createBuilder();
        
        $interface = $builder->buildInterface(__FUNCTION__);
        $interface->setPackage($builder->buildPackage(__FUNCTION__));

        $builder->restoreInterface($interface);

        self::assertSame($interface, $builder->getInterface(__FUNCTION__));
    }

    /**
     * Tests that the {@link PHP_Depend_Builder_Default::buildInterface()}
     * method only removes/replaces a previously created class instance, when
     * this class is part of the default namespace. Otherwise there are two user
     * types with the same local or package internal name.
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildInterfaceDoesntRemoveClassForSameNamedInterface()
    {
        $builder = $this->createBuilder();

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
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildInterfacesCreatesDifferentInstancesForDifferentPackages()
    {
        $builder = $this->createBuilder();

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
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testCanCreateMultipleInterfaceInstancesWithIdenticalNames()
    {
        $builder = $this->createBuilder();

        $interface1 = $builder->buildInterface('php\depend\ParserI');
        $interface2 = $builder->buildInterface('php\depend\ParserI');

        $this->assertNotSame($interface1, $interface2);
        self::assertSame(
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
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildInterfaceReusesExistingNonDefaultPackageInstanceForDefaultPackage()
    {
        $builder = $this->createBuilder();

        $interface = $builder->buildInterface('php\depend\ParserI');
        $interface->setPackage($builder->buildPackage(__FUNCTION__));

        $builder->restoreInterface($interface);

        self::assertSame($builder->getInterface('ParserI'), $interface);
        self::assertSame(
            $builder->getInterface('ParserI')->getPackage(),
            $interface->getPackage()
        );
    }

    /**
     * Tests the PHP_Depend_Code_Method build method.
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildMethod()
    {
        $builder = $this->createBuilder();
        self::assertType('PHP_Depend_Code_Method', $builder->buildMethod('method'));
    }

    /**
     * Tests that the node builder creates a package for the same name only once.
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildPackageUnique()
    {
        $builder  = $this->createBuilder();
        $package1 = $builder->buildPackage('package1');
        $package2 = $builder->buildPackage('package1');

        self::assertSame($package1, $package2);
    }

    /**
     * Tests the implemented {@link IteratorAggregate}.
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testGetIteratorWithPackages()
    {
        $builder = $this->createBuilder();

        $packages = array(
            'package1'  =>  $builder->buildPackage('package1'),
            'package2'  =>  $builder->buildPackage('package2'),
            'package3'  =>  $builder->buildPackage('package3')
        );

        foreach ($builder as $name => $package) {
            self::assertSame($packages[$name], $package);
        }
    }

    /**
     * Tests the {@link PHP_Depend_Builder_Default::getPackages()} method.
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testGetPackages()
    {
        $builder = $this->createBuilder();

        $packages = array(
            'package1'  =>  $builder->buildPackage('package1'),
            'package2'  =>  $builder->buildPackage('package2'),
            'package3'  =>  $builder->buildPackage('package3')
        );

        foreach ($builder->getPackages() as $name => $package) {
            self::assertSame($packages[$name], $package);
        }
    }

    /**
     * There was a missing check within an if statement, so that the builder
     * has alway overwritten previously created instances.
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildClassDoesNotOverwritePreviousInstances()
    {
        $builder = $this->createBuilder();

        $class0 = $builder->buildClass('FooBar');
        $class0->setPackage($builder->buildPackage(__FUNCTION__));

        $builder->restoreClass($class0);

        $class1 = $builder->buildClass('FooBar');
        $class1->setPackage($builder->buildPackage(__FUNCTION__));

        $builder->restoreClass($class1);

        $this->assertNotSame($class0, $class1);
        self::assertSame($class0, $builder->getClass('FooBar'));
    }

    /**
     * There was a missing check within an if statement, so that the builder
     * has alway overwritten previously created instances.
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildInterfaceDoesNotOverwritePreviousInstances()
    {
        $builder = $this->createBuilder();

        $interface = $builder->buildInterface('FooBar');
        $interface->setPackage($builder->buildPackage(__FUNCTION__));

        $builder->restoreInterface($interface);

        $this->assertNotSame($interface, $builder->buildInterface('FooBar'));
        self::assertSame($interface, $builder->getInterface('FooBar'));
    }

    /**
     * Tests that the node builder works case insensitive for class names.
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildClassWorksCaseInsensitiveIssue26()
    {
        $builder = $this->createBuilder();

        $class = $builder->buildClass('PHP_Depend_Parser');
        $class->setPackage($builder->buildPackage(__FUNCTION__));

        $builder->restoreClass($class);

        self::assertSame($class, $builder->getClass('php_Depend_parser'));
    }

    /**
     * Tests that the node builder works case insensitive for interface names.
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildInterfaceWorksCaseInsensitiveIssue26()
    {
        $builder = $this->createBuilder();

        $interface = $builder->buildInterface('PHP_Depend_TokenizerI');
        $interface->setPackage($builder->buildPackage(__FUNCTION__));

        $builder->restoreInterface($interface);

        self::assertSame(
            $interface,
            $builder->getInterface('php_Depend_tokenizeri')
        );
    }

    /**
     * Tests that the node builder works case insensitive for interface names.
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildClassOrInterfaceWorksCaseInsensitive1Issue26()
    {
        $builder = $this->createBuilder();

        $interface = $builder->buildInterface('PHP_Depend_TokenizerI');
        $interface->setPackage($builder->buildPackage(__FUNCTION__));

        $builder->restoreInterface($interface);

        self::assertSame(
            $interface,
            $builder->getClassOrInterface('php_Depend_tokenizeri')
        );
    }

    /**
     * Tests that the node builder works case insensitive for interface names.
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildClassOrInterfaceWorksCaseInsensitive2Issue26()
    {
        $builder = $this->createBuilder();

        $class = $builder->buildClass('PHP_Depend_Parser');
        $class->setPackage($builder->buildPackage(__FUNCTION__));

        $builder->restoreClass($class);

        self::assertSame($class, $builder->getClassOrInterface('php_Depend_parser'));
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildASTClassOrInterfaceReferenceThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = $this->createBuilder();
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
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildClassThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = $this->createBuilder();
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
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildASTClassReferenceThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = $this->createBuilder();
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
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildClosureThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = $this->createBuilder();
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
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildInterfaceThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = $this->createBuilder();
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
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildMethodThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = $this->createBuilder();
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
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildFunctionThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = $this->createBuilder();
        $builder->buildFunction('func');

        // Freeze object
        $builder->getInterface('Inter');

        $this->setExpectedException(
            'BadMethodCallException',
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildFunction('prop');
    }

    /**
     * testBuildASTPrimitiveTypeReturnsInstanceOfExpectedType
     *
     * @return void
     * @group pdepend
     * @group pdepend::builder
     * @group unittest
     */
    public function testBuildASTPrimitiveTypeReturnsInstanceOfExpectedType()
    {
        $builder  = $this->createBuilder();
        $instance = $builder->buildASTPrimitiveType(__FUNCTION__);

        self::assertType(PHP_Depend_Code_ASTPrimitiveType::CLAZZ, $instance);
    }

    /**
     * Creates a clean builder test instance.
     *
     * @return PHP_Depend_Builder_Default
     */
    protected function createBuilder()
    {
        $builder = new PHP_Depend_Builder_Default();
        $builder->setCache($this->getMock('PHP_Depend_Util_Cache_Driver'));

        return $builder;
    }
}
