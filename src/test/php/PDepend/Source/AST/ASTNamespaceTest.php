<?php
/**
 * This file is part of PDepend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
  */

namespace PDepend\Source\AST;

use PDepend\AbstractTest;
use PDepend\TreeVisitor\TestNodeVisitor;

/**
 * Test case implementation for the \PDepend\Source\AST\ASTNamespace class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\AST\ASTNamespace
 * @group unittest
 */
class ASTNamespaceTest extends AbstractTest
{
    /**
     * testGetUUIDReturnsExpectedObjectHash
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetUUIDReturnsExpectedObjectHash()
    {
        $package = new ASTNamespace(__FUNCTION__);
        $this->assertEquals(spl_object_hash($package), $package->getUuid());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTNamespace::getTypes()} method returns
     * an empty {@link \PDepend\Source\AST\ASTArtifactList}.
     *
     * @return void
     */
    public function testGetTypeNodeIterator()
    {
        $package = new ASTNamespace('package1');
        $types = $package->getTypes();
        
        $this->assertInstanceOf(ASTArtifactList::CLAZZ, $types);
    }
    
    /**
     * Tests that the {@link \PDepend\Source\AST\ASTNamespace::addType()}
     * method sets the package in the {@link \PDepend\Source\AST\ASTClass}
     * object and it tests the iterator to contain the new class.
     *
     * @return void
     */
    public function testAddTypeAddsTypeToPackage()
    {
        $package = new ASTNamespace('package1');
        $class   = new ASTClass('Class', 0, 'class.php');
        
        $package->addType($class);
        $this->assertEquals(1, $package->getTypes()->count());
    }

    /**
     * testAddTypeSetPackageOnAddedInstance
     *
     * @return void
     */
    public function testAddTypeSetPackageOnAddedInstance()
    {
        $package = new ASTNamespace('package1');
        $class   = new ASTClass('Class', 0, 'class.php');

        $package->addType($class);
        $this->assertSame($package, $class->getPackage());
    }
    
    /**
     * Tests that the {@link \PDepend\Source\AST\ASTNamespace::addType()}
     * reparents a class.
     *
     * @return void
     */
    public function testAddTypeReparentTheGivenInstance()
    {
        $package1 = new ASTNamespace('package1');
        $package2 = new ASTNamespace('package2');
        $class    = new ASTClass('Class', 0, 'class.php');
        
        $package1->addType($class);
        $package2->addType($class);

        $this->assertSame($package2, $class->getPackage());
    }

    /**
     * testAddTypeRemovesGivenTypeFromPreviousParentPackage
     *
     * @return void
     */
    public function testAddTypeRemovesGivenTypeFromPreviousParentPackage()
    {
        $package1 = new ASTNamespace('package1');
        $package2 = new ASTNamespace('package2');
        $class    = new ASTClass('Class', 0, 'class.php');

        $package1->addType($class);
        $package2->addType($class);

        $this->assertEquals(0, $package1->getTypes()->count());
    }

    /**
     * Tests that you cannot add the same type multiple times to a package.
     *
     * @return void
     */
    public function testPackageAcceptsTheSameTypeOnlyOneTime()
    {
        $package = new ASTNamespace('foo');
        $class   = new ASTClass('Bar');

        $package->addType($class);
        $package->addType($class);

        $this->assertEquals(1, count($package->getClasses()));
    }

    /**
     * testGetInterfacesReturnsAnEmptyResultByDefault
     *
     * @return void
     */
    public function testGetInterfacesReturnsAnEmptyResultByDefault()
    {
        $package = new ASTNamespace(__FUNCTION__);
        $this->assertEquals(0, count($package->getInterfaces()));
    }

    /**
     * testGetInterfacesReturnsInjectInterfaceInstance
     *
     * @return void
     */
    public function testGetInterfacesReturnsInjectInterfaceInstance()
    {
        $package = new ASTNamespace(__FUNCTION__);
        $package->addType(new ASTInterface(__CLASS__));

        $this->assertEquals(1, count($package->getInterfaces()));
    }

    /**
     * testGetInterfacesReturnsInjectInterfaceInstance
     *
     * @return void
     */
    public function testGetInterfacesReturnsNotInjectClassInstance()
    {
        $package = new ASTNamespace(__FUNCTION__);
        $package->addType(new ASTClass(__CLASS__));

        $this->assertEquals(0, count($package->getInterfaces()));
    }
    
    /**
     * Tests that the {@link \PDepend\Source\AST\ASTNamespace::removeType()}
     * method unsets the package in the {@link \PDepend\Source\AST\ASTClass}
     * object and it tests the iterator to contain the new class.
     *
     * @return void
     */
    public function testRemoveType()
    {
        $package = new ASTNamespace('package1');
        $class2  = new ASTClass('Class2', 0, 'class2.php');

        $package->addType($class2);
        $package->removeType($class2);

        $this->assertEquals(0, $package->getTypes()->count());
    }

    /**
     * testRemoveTypeResetsPackageReferenceFromRemovedType
     *
     * @return void
     */
    public function testRemoveTypeResetsPackageReferenceFromRemovedType()
    {
        $package = new ASTNamespace('package1');
        $class   = new ASTClass('Class');

        $package->addType($class);
        $package->removeType($class);

        $this->assertNull($class->getPackage());
    }

    /**
     * testGetTraitsReturnsExpectedNumberOfTraits
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetTraitsReturnsExpectedNumberOfTraits()
    {
        $package = new ASTNamespace('package');
        $package->addType(new ASTClass('Class'));
        $package->addType(new ASTTrait('Trait0'));
        $package->addType(new ASTInterface('Interface'));
        $package->addType(new ASTTrait('Trait1'));

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
        $package = new ASTNamespace('package');
        $trait   = $package->addType(new ASTTrait('Trait'));

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
        $package = new ASTNamespace('package');
        $trait   = $package->addType(new ASTTrait('Trait'));

        $this->assertSame($package, $trait->getPackage());
    }
    
    /**
     * Tests that the {@link \PDepend\Source\AST\ASTNamespace::getFunctions()}
     * method returns an empty {@link \PDepend\Source\AST\ASTArtifactList}.
     *
     * @return void
     */
    public function testGetFunctionsNodeIterator()
    {
        $package   = new ASTNamespace('package1');
        $functions = $package->getFunctions();
        
        $this->assertInstanceOf(ASTArtifactList::CLAZZ, $functions);
    }
    
    /**
     * Tests that the {@link \PDepend\Source\AST\ASTNamespace::addFunction()}
     * method sets the actual package as {@link \PDepend\Source\AST\ASTFunction}
     * owner.
     *
     * @return void
     */
    public function testAddFunction()
    {
        $package  = new ASTNamespace('package1');
        $function = new ASTFunction('function', 0);
        
        $package->addFunction($function);
        $this->assertEquals(1, $package->getFunctions()->count());
    }

    /**
     * testAddFunctionSetsParentPackageOnGivenInstance
     *
     * @return void
     */
    public function testAddFunctionSetsParentPackageOnGivenInstance()
    {
        $package  = new ASTNamespace('package1');
        $function = new ASTFunction('function', 0);

        $package->addFunction($function);
        $this->assertSame($package, $function->getPackage());
    }
    
    /**
     * Tests that the {@link \PDepend\Source\AST\ASTNamespace::addFunction()}
     * reparents a function.
     *
     * @return void
     */
    public function testAddFunctionReparent()
    {
        $package1 = new ASTNamespace('package1');
        $package2 = new ASTNamespace('package2');
        $function = new ASTFunction('func', 0);
        
        $package1->addFunction($function);
        $package2->addFunction($function);

        $this->assertSame($package2, $function->getPackage());
    }

    /**
     * testAddFunctionRemovesFunctionFromPreviousParentPackage
     *
     * @return void
     */
    public function testAddFunctionRemovesFunctionFromPreviousParentPackage()
    {
        $package1 = new ASTNamespace('package1');
        $package2 = new ASTNamespace('package2');
        $function = new ASTFunction('func', 0);

        $package1->addFunction($function);
        $package2->addFunction($function);

        $this->assertEquals(0, $package1->getFunctions()->count());
    }
    
    /**
     * Tests that the {@link \PDepend\Source\AST\ASTNamespace::removeFunction()}
     * method unsets the actual package as {@link \PDepend\Source\AST\ASTFunction}
     * owner.
     *
     * @return void
     */
    public function testRemoveFunction()
    {
        $package   = new ASTNamespace('package1');
        $function1 = new ASTFunction('func1', 0);
        $function2 = new ASTFunction('func2', 0);
        
        $package->addFunction($function1);
        $package->addFunction($function2);
        $package->removeFunction($function2);

        $this->assertEquals(1, $package->getFunctions()->count());
    }

    /**
     * testRemoveFunctionSetsParentPackageToNull
     *
     * @return void
     */
    public function testRemoveFunctionSetsParentPackageToNull()
    {
        $package  = new ASTNamespace('package');
        $function = new ASTFunction('func', 0);

        $package->addFunction($function);
        $package->removeFunction($function);

        $this->assertNull($function->getPackage());
    }
    
    /**
     * Tests the visitor accept method.
     *
     * @return void
     */
    public function testVisitorAccept()
    {
        $package = new ASTNamespace('package1');
        $visitor = new TestNodeVisitor();
        
        $package->accept($visitor);
        $this->assertSame($package, $visitor->package);
    }

    /**
     * testIsUserDefinedReturnsFalseWhenPackageIsEmpty
     *
     * @return void
     */
    public function testIsUserDefinedReturnsFalseWhenPackageIsEmpty()
    {
        $package = new ASTNamespace('package1');
        $this->assertFalse($package->isUserDefined());
    }

    /**
     * testIsUserDefinedReturnsFalseWhenAllChildElementsAreNotUserDefined
     *
     * @return void
     */
    public function testIsUserDefinedReturnsFalseWhenAllChildElementsAreNotUserDefined()
    {
        $package = new ASTNamespace('package1');
        $package->addType(new ASTClass('class', 0));
        
        $this->assertFalse($package->isUserDefined());
    }

    /**
     * testIsUserDefinedReturnsTrueWhenChildElementIsUserDefined
     *
     * @return void
     */
    public function testIsUserDefinedReturnsTrueWhenChildElementIsUserDefined()
    {
        $class = new ASTClass('class', 0);
        $class->setUserDefined();

        $package = new ASTNamespace('package1');
        $package->addType($class);

        $this->assertTrue($package->isUserDefined());
    }

    /**
     * testIsUserDefinedReturnsTrueWhenAtLeastOneFunctionExists
     *
     * @return void
     */
    public function testIsUserDefinedReturnsTrueWhenAtLeastOneFunctionExists()
    {
        $package = new ASTNamespace('package1');
        $package->addFunction(new ASTFunction("foo", 0));

        $this->assertTrue($package->isUserDefined());
    }
}
