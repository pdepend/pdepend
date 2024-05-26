<?php

/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2017 Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Source\AST;

use PDepend\AbstractTestCase;
use PDepend\Source\ASTVisitor\StubASTVisitor;

/**
 * Test case implementation for the \PDepend\Source\AST\ASTNamespace class.
 *
 * @covers \PDepend\Source\AST\ASTNamespace
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTNamespaceTest extends AbstractTestCase
{
    /**
     * testGetIdReturnsExpectedObjectHash
     *
     * @since 1.0.0
     */
    public function testGetIdReturnsExpectedObjectHash(): void
    {
        $namespace = new ASTNamespace(__FUNCTION__);
        static::assertEquals(spl_object_hash($namespace), $namespace->getId());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTNamespace::getTypes()} method returns
     * an empty {@link \PDepend\Source\AST\ASTArtifactList}.
     */
    public function testGetTypeNodeIterator(): void
    {
        $namespace = new ASTNamespace('package1');
        $types = $namespace->getTypes();

        static::assertInstanceOf(ASTArtifactList::class, $types);
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTNamespace::addType()}
     * method sets the package in the {@link \PDepend\Source\AST\ASTClass}
     * object and it tests the iterator to contain the new class.
     */
    public function testAddTypeAddsTypeToPackage(): void
    {
        $namespace = new ASTNamespace('package1');
        $class = new ASTClass('Class');

        $namespace->addType($class);
        static::assertEquals(1, $namespace->getTypes()->count());
    }

    /**
     * testAddTypeSetNamespaceOnAddedInstance
     */
    public function testAddTypeSetNamespaceOnAddedInstance(): void
    {
        $namespace = new ASTNamespace('package1');
        $class = new ASTClass('Class');

        $namespace->addType($class);
        static::assertSame($namespace, $class->getNamespace());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTNamespace::addType()}
     * reparents a class.
     */
    public function testAddTypeReparentTheGivenInstance(): void
    {
        $namespace1 = new ASTNamespace('package1');
        $namespace2 = new ASTNamespace('package2');
        $class = new ASTClass('Class');

        $namespace1->addType($class);
        $namespace2->addType($class);

        static::assertSame($namespace2, $class->getNamespace());
    }

    /**
     * testAddTypeRemovesGivenTypeFromPreviousParentPackage
     */
    public function testAddTypeRemovesGivenTypeFromPreviousParentPackage(): void
    {
        $namespace1 = new ASTNamespace('package1');
        $namespace2 = new ASTNamespace('package2');
        $class = new ASTClass('Class');

        $namespace1->addType($class);
        $namespace2->addType($class);

        static::assertEquals(0, $namespace1->getTypes()->count());
    }

    /**
     * Tests that you cannot add the same type multiple times to a package.
     */
    public function testPackageAcceptsTheSameTypeOnlyOneTime(): void
    {
        $namespace = new ASTNamespace('foo');
        $class = new ASTClass('Bar');

        $namespace->addType($class);
        $namespace->addType($class);

        static::assertCount(1, $namespace->getClasses());
    }

    /**
     * testGetInterfacesReturnsAnEmptyResultByDefault
     */
    public function testGetInterfacesReturnsAnEmptyResultByDefault(): void
    {
        $namespace = new ASTNamespace(__FUNCTION__);
        static::assertCount(0, $namespace->getInterfaces());
    }

    /**
     * testGetInterfacesReturnsInjectInterfaceInstance
     */
    public function testGetInterfacesReturnsInjectInterfaceInstance(): void
    {
        $namespace = new ASTNamespace(__FUNCTION__);
        $namespace->addType(new ASTInterface(__CLASS__));

        static::assertCount(1, $namespace->getInterfaces());
    }

    /**
     * testGetInterfacesReturnsInjectInterfaceInstance
     */
    public function testGetInterfacesReturnsNotInjectClassInstance(): void
    {
        $namespace = new ASTNamespace(__FUNCTION__);
        $namespace->addType(new ASTClass(__CLASS__));

        static::assertCount(0, $namespace->getInterfaces());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTNamespace::removeType()}
     * method unsets the package in the {@link \PDepend\Source\AST\ASTClass}
     * object and it tests the iterator to contain the new class.
     */
    public function testRemoveType(): void
    {
        $namespace = new ASTNamespace('package1');
        $class2 = new ASTClass('Class2');

        $namespace->addType($class2);
        $namespace->removeType($class2);

        static::assertEquals(0, $namespace->getTypes()->count());
    }

    /**
     * testRemoveTypeResetsPackageReferenceFromRemovedType
     */
    public function testRemoveTypeResetsPackageReferenceFromRemovedType(): void
    {
        $namespace = new ASTNamespace('package1');
        $class = new ASTClass('Class');

        $namespace->addType($class);
        $namespace->removeType($class);

        static::assertNull($class->getNamespace());
    }

    /**
     * testGetTraitsReturnsExpectedNumberOfTraits
     *
     * @since 1.0.0
     */
    public function testGetTraitsReturnsExpectedNumberOfTraits(): void
    {
        $namespace = new ASTNamespace('package');
        $namespace->addType(new ASTClass('Class'));
        $namespace->addType(new ASTTrait('Trait0'));
        $namespace->addType(new ASTInterface('Interface'));
        $namespace->addType(new ASTTrait('Trait1'));

        static::assertCount(2, $namespace->getTraits());
    }

    /**
     * testGetTraitsContainsExpectedTrait
     *
     * @since 1.0.0
     */
    public function testGetTraitsContainsExpectedTrait(): void
    {
        $namespace = new ASTNamespace('package');
        $trait = $namespace->addType(new ASTTrait('Trait'));

        $traits = $namespace->getTraits();
        static::assertSame($trait, $traits[0]);
    }

    /**
     * testAddTypeSetsParentPackageOfTrait
     *
     * @since 1.0.0
     */
    public function testAddTypeSetsParentPackageOfTrait(): void
    {
        $namespace = new ASTNamespace('package');
        $trait = $namespace->addType(new ASTTrait('Trait'));

        static::assertSame($namespace, $trait->getNamespace());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTNamespace::getFunctions()}
     * method returns an empty {@link \PDepend\Source\AST\ASTArtifactList}.
     */
    public function testGetFunctionsNodeIterator(): void
    {
        $namespace = new ASTNamespace('package1');
        $functions = $namespace->getFunctions();

        static::assertInstanceOf(ASTArtifactList::class, $functions);
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTNamespace::addFunction()}
     * method sets the actual package as {@link \PDepend\Source\AST\ASTFunction}
     * owner.
     */
    public function testAddFunction(): void
    {
        $namespace = new ASTNamespace('package1');
        $function = new ASTFunction('function');

        $namespace->addFunction($function);
        static::assertEquals(1, $namespace->getFunctions()->count());
    }

    /**
     * testAddFunctionSetsParentPackageOnGivenInstance
     */
    public function testAddFunctionSetsParentPackageOnGivenInstance(): void
    {
        $namespace = new ASTNamespace('package1');
        $function = new ASTFunction('function');

        $namespace->addFunction($function);
        static::assertSame($namespace, $function->getNamespace());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTNamespace::addFunction()}
     * reparents a function.
     */
    public function testAddFunctionReparent(): void
    {
        $namespace1 = new ASTNamespace('package1');
        $namespace2 = new ASTNamespace('package2');
        $function = new ASTFunction('func');

        $namespace1->addFunction($function);
        $namespace2->addFunction($function);

        static::assertSame($namespace2, $function->getNamespace());
    }

    /**
     * testAddFunctionRemovesFunctionFromPreviousParentPackage
     */
    public function testAddFunctionRemovesFunctionFromPreviousParentPackage(): void
    {
        $namespace1 = new ASTNamespace('package1');
        $namespace2 = new ASTNamespace('package2');
        $function = new ASTFunction('func');

        $namespace1->addFunction($function);
        $namespace2->addFunction($function);

        static::assertEquals(0, $namespace1->getFunctions()->count());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTNamespace::removeFunction()}
     * method unsets the actual package as {@link \PDepend\Source\AST\ASTFunction}
     * owner.
     */
    public function testRemoveFunction(): void
    {
        $namespace = new ASTNamespace('package1');
        $function1 = new ASTFunction('func1');
        $function2 = new ASTFunction('func2');

        $namespace->addFunction($function1);
        $namespace->addFunction($function2);
        $namespace->removeFunction($function2);

        static::assertEquals(1, $namespace->getFunctions()->count());
    }

    /**
     * testRemoveFunctionSetsParentPackageToNull
     */
    public function testRemoveFunctionSetsParentPackageToNull(): void
    {
        $namespace = new ASTNamespace('nspace');
        $function = new ASTFunction('func');

        $namespace->addFunction($function);
        $namespace->removeFunction($function);

        static::assertNull($function->getNamespace());
    }

    /**
     * Tests the visitor accept method.
     */
    public function testVisitorAccept(): void
    {
        $namespace = new ASTNamespace('package1');
        $visitor = new StubASTVisitor();

        $visitor->dispatch($namespace);
        static::assertSame($namespace, $visitor->namespace);
    }

    /**
     * testIsUserDefinedReturnsFalseWhenPackageIsEmpty
     */
    public function testIsUserDefinedReturnsFalseWhenPackageIsEmpty(): void
    {
        $namespace = new ASTNamespace('package1');
        static::assertFalse($namespace->isUserDefined());
    }

    /**
     * testIsUserDefinedReturnsFalseWhenAllChildElementsAreNotUserDefined
     */
    public function testIsUserDefinedReturnsFalseWhenAllChildElementsAreNotUserDefined(): void
    {
        $namespace = new ASTNamespace('package1');
        $namespace->addType(new ASTClass('class'));

        static::assertFalse($namespace->isUserDefined());
    }

    /**
     * testIsUserDefinedReturnsTrueWhenChildElementIsUserDefined
     */
    public function testIsUserDefinedReturnsTrueWhenChildElementIsUserDefined(): void
    {
        $class = new ASTClass('class');
        $class->setUserDefined();

        $namespace = new ASTNamespace('package1');
        $namespace->addType($class);

        static::assertTrue($namespace->isUserDefined());
    }

    /**
     * testIsUserDefinedReturnsTrueWhenAtLeastOneFunctionExists
     */
    public function testIsUserDefinedReturnsTrueWhenAtLeastOneFunctionExists(): void
    {
        $namespace = new ASTNamespace('package1');
        $namespace->addFunction(new ASTFunction('foo'));

        static::assertTrue($namespace->isUserDefined());
    }

    public function testIsPackageAnnotationReturnsFalseByDefault(): void
    {
        $namespace = new ASTNamespace('namespace');
        static::assertFalse($namespace->isPackageAnnotation());
    }

    public function testIsPackageAnnotationReturnsFalseTrue(): void
    {
        $namespace = new ASTNamespace('namespace');
        $namespace->setPackageAnnotation(true);

        static::assertTrue($namespace->isPackageAnnotation());
    }
}
