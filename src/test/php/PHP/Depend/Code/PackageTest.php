<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';
require_once dirname(__FILE__) . '/../Visitor/TestNodeVisitor.php';

/**
 * Test case implementation for the PHP_Depend_Code_Package class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 *
 * @covers PHP_Depend_Code_Package
 * @group pdepend
 * @group pdepend::code
 * @group unittest
 */
class PHP_Depend_Code_PackageTest extends PHP_Depend_AbstractTest
{
    /**
     * testGetUUIDReturnsExpectedObjectHash
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetUUIDReturnsExpectedObjectHash()
    {
        $package = new PHP_Depend_Code_Package(__FUNCTION__);
        $this->assertEquals(spl_object_hash($package), $package->getUuid());
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
        
        $this->assertInstanceOf('PHP_Depend_Code_NodeIterator', $types);
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Package::addType()} method sets
     * the package in the {@link PHP_Depend_Code_Class} object and it tests the
     * iterator to contain the new class.
     *
     * @return void
     */
    public function testAddTypeAddsTypeToPackage()
    {
        $package = new PHP_Depend_Code_Package('package1');
        $class   = new PHP_Depend_Code_Class('Class', 0, 'class.php');
        
        $package->addType($class);
        self::assertEquals(1, $package->getTypes()->count());
    }

    /**
     * testAddTypeSetPackageOnAddedInstance
     *
     * @return void
     */
    public function testAddTypeSetPackageOnAddedInstance()
    {
        $package = new PHP_Depend_Code_Package('package1');
        $class   = new PHP_Depend_Code_Class('Class', 0, 'class.php');

        $package->addType($class);
        self::assertSame($package, $class->getPackage());
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Package::addType()} reparents a
     * class.
     *
     * @return void
     */
    public function testAddTypeReparentTheGivenInstance()
    {
        $package1 = new PHP_Depend_Code_Package('package1');
        $package2 = new PHP_Depend_Code_Package('package2');
        $class    = new PHP_Depend_Code_Class('Class', 0, 'class.php');
        
        $package1->addType($class);
        $package2->addType($class);

        self::assertSame($package2, $class->getPackage());
    }

    /**
     * testAddTypeRemovesGivenTypeFromPreviousParentPackage
     *
     * @return void
     */
    public function testAddTypeRemovesGivenTypeFromPreviousParentPackage()
    {
        $package1 = new PHP_Depend_Code_Package('package1');
        $package2 = new PHP_Depend_Code_Package('package2');
        $class    = new PHP_Depend_Code_Class('Class', 0, 'class.php');

        $package1->addType($class);
        $package2->addType($class);

        self::assertEquals(0, $package1->getTypes()->count());
    }

    /**
     * Tests that you cannot add the same type multiple times to a package.
     *
     * @return void
     */
    public function testPackageAcceptsTheSameTypeOnlyOneTime()
    {
        $package = new PHP_Depend_Code_Package('foo');
        $class   = new PHP_Depend_Code_Class('Bar');

        $package->addType($class);
        $package->addType($class);

        self::assertEquals(1, count($package->getClasses()));
    }

    /**
     * testGetInterfacesReturnsAnEmptyResultByDefault
     *
     * @return void
     */
    public function testGetInterfacesReturnsAnEmptyResultByDefault()
    {
        $package = new PHP_Depend_Code_Package(__FUNCTION__);
        self::assertEquals(0, count($package->getInterfaces()));
    }

    /**
     * testGetInterfacesReturnsInjectInterfaceInstance
     *
     * @return void
     */
    public function testGetInterfacesReturnsInjectInterfaceInstance()
    {
        $package = new PHP_Depend_Code_Package(__FUNCTION__);
        $package->addType(new PHP_Depend_Code_Interface(__CLASS__));

        self::assertEquals(1, count($package->getInterfaces()));
    }

    /**
     * testGetInterfacesReturnsInjectInterfaceInstance
     *
     * @return void
     */
    public function testGetInterfacesReturnsNotInjectClassInstance()
    {
        $package = new PHP_Depend_Code_Package(__FUNCTION__);
        $package->addType(new PHP_Depend_Code_Class(__CLASS__));

        self::assertEquals(0, count($package->getInterfaces()));
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
        $class2  = new PHP_Depend_Code_Class('Class2', 0, 'class2.php');

        $package->addType($class2);
        $package->removeType($class2);

        self::assertEquals(0, $package->getTypes()->count());
    }

    /**
     * testRemoveTypeResetsPackageReferenceFromRemovedType
     *
     * @return void
     */
    public function testRemoveTypeResetsPackageReferenceFromRemovedType()
    {
        $package = new PHP_Depend_Code_Package('package1');
        $class   = new PHP_Depend_Code_Class('Class');

        $package->addType($class);
        $package->removeType($class);

        self::assertNull($class->getPackage());
    }

    /**
     * testGetTraitsReturnsExpectedNumberOfTraits
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetTraitsReturnsExpectedNumberOfTraits()
    {
        $package = new PHP_Depend_Code_Package('package');
        $package->addType(new PHP_Depend_Code_Class('Class'));
        $package->addType(new PHP_Depend_Code_Trait('Trait0'));
        $package->addType(new PHP_Depend_Code_Interface('Interface'));
        $package->addType(new PHP_Depend_Code_Trait('Trait1'));

        $this->assertEquals(2, count($package->getTraits()));
    }

    /**
     * testGetTraitsContainsExpectedTrait
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetTraitsContainsExpectedTrait()
    {
        $package = new PHP_Depend_Code_Package('package');
        $trait   = $package->addType(new PHP_Depend_Code_Trait('Trait'));

        $traits = $package->getTraits();
        $this->assertSame($trait, $traits[0]);
    }

    /**
     * testAddTypeSetsParentPackageOfTrait
     *
     * @return void
     * @since 1.0.0
     */
    public function testAddTypeSetsParentPackageOfTrait()
    {
        $package = new PHP_Depend_Code_Package('package');
        $trait   = $package->addType(new PHP_Depend_Code_Trait('Trait'));

        $this->assertSame($package, $trait->getPackage());
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
        
        self::assertInstanceOf('PHP_Depend_Code_NodeIterator', $functions);
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
        
        $package->addFunction($function);
        self::assertEquals(1, $package->getFunctions()->count());
    }

    /**
     * testAddFunctionSetsParentPackageOnGivenInstance
     *
     * @return void
     */
    public function testAddFunctionSetsParentPackageOnGivenInstance()
    {
        $package  = new PHP_Depend_Code_Package('package1');
        $function = new PHP_Depend_Code_Function('function', 0);

        $package->addFunction($function);
        self::assertSame($package, $function->getPackage());
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
        $package2->addFunction($function);

        self::assertSame($package2, $function->getPackage());
    }

    /**
     * testAddFunctionRemovesFunctionFromPreviousParentPackage
     *
     * @return void
     */
    public function testAddFunctionRemovesFunctionFromPreviousParentPackage()
    {
        $package1 = new PHP_Depend_Code_Package('package1');
        $package2 = new PHP_Depend_Code_Package('package2');
        $function = new PHP_Depend_Code_Function('func', 0);

        $package1->addFunction($function);
        $package2->addFunction($function);

        self::assertEquals(0, $package1->getFunctions()->count());
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
        $package->removeFunction($function2);

        self::assertEquals(1, $package->getFunctions()->count());
    }

    /**
     * testRemoveFunctionSetsParentPackageToNull
     *
     * @return void
     */
    public function testRemoveFunctionSetsParentPackageToNull()
    {
        $package  = new PHP_Depend_Code_Package('package');
        $function = new PHP_Depend_Code_Function('func', 0);

        $package->addFunction($function);
        $package->removeFunction($function);

        self::assertNull($function->getPackage());
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
        
        $package->accept($visitor);
        self::assertSame($package, $visitor->package);
    }

    /**
     * testIsUserDefinedReturnsFalseWhenPackageIsEmpty
     *
     * @return void
     */
    public function testIsUserDefinedReturnsFalseWhenPackageIsEmpty()
    {
        $package = new PHP_Depend_Code_Package('package1');
        self::assertFalse($package->isUserDefined());
    }

    /**
     * testIsUserDefinedReturnsFalseWhenAllChildElementsAreNotUserDefined
     *
     * @return void
     */
    public function testIsUserDefinedReturnsFalseWhenAllChildElementsAreNotUserDefined()
    {
        $package = new PHP_Depend_Code_Package('package1');
        $package->addType(new PHP_Depend_Code_Class('class', 0));
        
        self::assertFalse($package->isUserDefined());
    }

    /**
     * testIsUserDefinedReturnsTrueWhenChildElementIsUserDefined
     *
     * @return void
     */
    public function testIsUserDefinedReturnsTrueWhenChildElementIsUserDefined()
    {
        $class = new PHP_Depend_Code_Class('class', 0);
        $class->setUserDefined();

        $package = new PHP_Depend_Code_Package('package1');
        $package->addType($class);

        self::assertTrue($package->isUserDefined());
    }

    /**
     * testIsUserDefinedReturnsTrueWhenAtLeastOneFunctionExists
     *
     * @return void
     */
    public function testIsUserDefinedReturnsTrueWhenAtLeastOneFunctionExists()
    {
        $package = new PHP_Depend_Code_Package('package1');
        $package->addFunction(new PHP_Depend_Code_Function("foo", 0));

        self::assertTrue($package->isUserDefined());
    }
}
