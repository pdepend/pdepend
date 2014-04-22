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
use PDepend\Source\ASTVisitor\StubASTVisitor;

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
     * testGetIdReturnsExpectedObjectHash
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetIdReturnsExpectedObjectHash()
    {
        $namespace = new ASTNamespace(__FUNCTION__);
        $this->assertEquals(spl_object_hash($namespace), $namespace->getId());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTNamespace::getTypes()} method returns
     * an empty {@link \PDepend\Source\AST\ASTArtifactList}.
     *
     * @return void
     */
    public function testGetTypeNodeIterator()
    {
        $namespace = new ASTNamespace('package1');
        $types = $namespace->getTypes();
        
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTArtifactList', $types);
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
        $namespace = new ASTNamespace('package1');
        $class = new ASTClass('Class', 0, 'class.php');
        
        $namespace->addType($class);
        $this->assertEquals(1, $namespace->getTypes()->count());
    }

    /**
     * testAddTypeSetNamespaceOnAddedInstance
     *
     * @return void
     */
    public function testAddTypeSetNamespaceOnAddedInstance()
    {
        $namespace = new ASTNamespace('package1');
        $class = new ASTClass('Class', 0, 'class.php');

        $namespace->addType($class);
        $this->assertSame($namespace, $class->getNamespace());
    }
    
    /**
     * Tests that the {@link \PDepend\Source\AST\ASTNamespace::addType()}
     * reparents a class.
     *
     * @return void
     */
    public function testAddTypeReparentTheGivenInstance()
    {
        $namespace1 = new ASTNamespace('package1');
        $namespace2 = new ASTNamespace('package2');
        $class    = new ASTClass('Class', 0, 'class.php');
        
        $namespace1->addType($class);
        $namespace2->addType($class);

        $this->assertSame($namespace2, $class->getNamespace());
    }

    /**
     * testAddTypeRemovesGivenTypeFromPreviousParentPackage
     *
     * @return void
     */
    public function testAddTypeRemovesGivenTypeFromPreviousParentPackage()
    {
        $namespace1 = new ASTNamespace('package1');
        $namespace2 = new ASTNamespace('package2');
        $class    = new ASTClass('Class', 0, 'class.php');

        $namespace1->addType($class);
        $namespace2->addType($class);

        $this->assertEquals(0, $namespace1->getTypes()->count());
    }

    /**
     * Tests that you cannot add the same type multiple times to a package.
     *
     * @return void
     */
    public function testPackageAcceptsTheSameTypeOnlyOneTime()
    {
        $namespace = new ASTNamespace('foo');
        $class   = new ASTClass('Bar');

        $namespace->addType($class);
        $namespace->addType($class);

        $this->assertEquals(1, count($namespace->getClasses()));
    }

    /**
     * testGetInterfacesReturnsAnEmptyResultByDefault
     *
     * @return void
     */
    public function testGetInterfacesReturnsAnEmptyResultByDefault()
    {
        $namespace = new ASTNamespace(__FUNCTION__);
        $this->assertEquals(0, count($namespace->getInterfaces()));
    }

    /**
     * testGetInterfacesReturnsInjectInterfaceInstance
     *
     * @return void
     */
    public function testGetInterfacesReturnsInjectInterfaceInstance()
    {
        $namespace = new ASTNamespace(__FUNCTION__);
        $namespace->addType(new ASTInterface(__CLASS__));

        $this->assertEquals(1, count($namespace->getInterfaces()));
    }

    /**
     * testGetInterfacesReturnsInjectInterfaceInstance
     *
     * @return void
     */
    public function testGetInterfacesReturnsNotInjectClassInstance()
    {
        $namespace = new ASTNamespace(__FUNCTION__);
        $namespace->addType(new ASTClass(__CLASS__));

        $this->assertEquals(0, count($namespace->getInterfaces()));
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
        $namespace = new ASTNamespace('package1');
        $class2  = new ASTClass('Class2', 0, 'class2.php');

        $namespace->addType($class2);
        $namespace->removeType($class2);

        $this->assertEquals(0, $namespace->getTypes()->count());
    }

    /**
     * testRemoveTypeResetsPackageReferenceFromRemovedType
     *
     * @return void
     */
    public function testRemoveTypeResetsPackageReferenceFromRemovedType()
    {
        $namespace = new ASTNamespace('package1');
        $class   = new ASTClass('Class');

        $namespace->addType($class);
        $namespace->removeType($class);

        $this->assertNull($class->getNamespace());
    }

    /**
     * testGetTraitsReturnsExpectedNumberOfTraits
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetTraitsReturnsExpectedNumberOfTraits()
    {
        $namespace = new ASTNamespace('package');
        $namespace->addType(new ASTClass('Class'));
        $namespace->addType(new ASTTrait('Trait0'));
        $namespace->addType(new ASTInterface('Interface'));
        $namespace->addType(new ASTTrait('Trait1'));

        $this->assertEquals(2, count($namespace->getTraits()));
    }

    /**
     * testGetTraitsContainsExpectedTrait
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetTraitsContainsExpectedTrait()
    {
        $namespace = new ASTNamespace('package');
        $trait   = $namespace->addType(new ASTTrait('Trait'));

        $traits = $namespace->getTraits();
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
        $namespace = new ASTNamespace('package');
        $trait   = $namespace->addType(new ASTTrait('Trait'));

        $this->assertSame($namespace, $trait->getNamespace());
    }
    
    /**
     * Tests that the {@link \PDepend\Source\AST\ASTNamespace::getFunctions()}
     * method returns an empty {@link \PDepend\Source\AST\ASTArtifactList}.
     *
     * @return void
     */
    public function testGetFunctionsNodeIterator()
    {
        $namespace   = new ASTNamespace('package1');
        $functions = $namespace->getFunctions();
        
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTArtifactList', $functions);
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
        $namespace  = new ASTNamespace('package1');
        $function = new ASTFunction('function', 0);
        
        $namespace->addFunction($function);
        $this->assertEquals(1, $namespace->getFunctions()->count());
    }

    /**
     * testAddFunctionSetsParentPackageOnGivenInstance
     *
     * @return void
     */
    public function testAddFunctionSetsParentPackageOnGivenInstance()
    {
        $namespace  = new ASTNamespace('package1');
        $function = new ASTFunction('function', 0);

        $namespace->addFunction($function);
        $this->assertSame($namespace, $function->getNamespace());
    }
    
    /**
     * Tests that the {@link \PDepend\Source\AST\ASTNamespace::addFunction()}
     * reparents a function.
     *
     * @return void
     */
    public function testAddFunctionReparent()
    {
        $namespace1 = new ASTNamespace('package1');
        $namespace2 = new ASTNamespace('package2');
        $function = new ASTFunction('func', 0);
        
        $namespace1->addFunction($function);
        $namespace2->addFunction($function);

        $this->assertSame($namespace2, $function->getNamespace());
    }

    /**
     * testAddFunctionRemovesFunctionFromPreviousParentPackage
     *
     * @return void
     */
    public function testAddFunctionRemovesFunctionFromPreviousParentPackage()
    {
        $namespace1 = new ASTNamespace('package1');
        $namespace2 = new ASTNamespace('package2');
        $function = new ASTFunction('func', 0);

        $namespace1->addFunction($function);
        $namespace2->addFunction($function);

        $this->assertEquals(0, $namespace1->getFunctions()->count());
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
        $namespace   = new ASTNamespace('package1');
        $function1 = new ASTFunction('func1', 0);
        $function2 = new ASTFunction('func2', 0);
        
        $namespace->addFunction($function1);
        $namespace->addFunction($function2);
        $namespace->removeFunction($function2);

        $this->assertEquals(1, $namespace->getFunctions()->count());
    }

    /**
     * testRemoveFunctionSetsParentPackageToNull
     *
     * @return void
     */
    public function testRemoveFunctionSetsParentPackageToNull()
    {
        $namespace  = new ASTNamespace('nspace');
        $function = new ASTFunction('func', 0);

        $namespace->addFunction($function);
        $namespace->removeFunction($function);

        $this->assertNull($function->getNamespace());
    }
    
    /**
     * Tests the visitor accept method.
     *
     * @return void
     */
    public function testVisitorAccept()
    {
        $namespace = new ASTNamespace('package1');
        $visitor = new StubASTVisitor();
        
        $namespace->accept($visitor);
        $this->assertSame($namespace, $visitor->namespace);
    }

    /**
     * testIsUserDefinedReturnsFalseWhenPackageIsEmpty
     *
     * @return void
     */
    public function testIsUserDefinedReturnsFalseWhenPackageIsEmpty()
    {
        $namespace = new ASTNamespace('package1');
        $this->assertFalse($namespace->isUserDefined());
    }

    /**
     * testIsUserDefinedReturnsFalseWhenAllChildElementsAreNotUserDefined
     *
     * @return void
     */
    public function testIsUserDefinedReturnsFalseWhenAllChildElementsAreNotUserDefined()
    {
        $namespace = new ASTNamespace('package1');
        $namespace->addType(new ASTClass('class', 0));
        
        $this->assertFalse($namespace->isUserDefined());
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

        $namespace = new ASTNamespace('package1');
        $namespace->addType($class);

        $this->assertTrue($namespace->isUserDefined());
    }

    /**
     * testIsUserDefinedReturnsTrueWhenAtLeastOneFunctionExists
     *
     * @return void
     */
    public function testIsUserDefinedReturnsTrueWhenAtLeastOneFunctionExists()
    {
        $namespace = new ASTNamespace('package1');
        $namespace->addFunction(new ASTFunction("foo", 0));

        $this->assertTrue($namespace->isUserDefined());
    }
}
