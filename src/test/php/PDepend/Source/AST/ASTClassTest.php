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

use BadMethodCallException;
use InvalidArgumentException;
use PDepend\Source\AST\ASTArtifactList\CollectionArtifactFilter;
use PDepend\Source\AST\ASTArtifactList\PackageArtifactFilter;
use PDepend\Source\ASTVisitor\StubASTVisitor;
use PDepend\Source\Tokenizer\Token;
use PDepend\Util\Cache\Driver\MemoryCacheDriver;

/**
 * Test case implementation for the \PDepend\Source\AST\ASTClass class.
 *
 * @covers \PDepend\Source\AST\AbstractASTClassOrInterface
 * @covers \PDepend\Source\AST\AbstractASTType
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTClassTest extends AbstractASTArtifactTestCase
{
    /**
     * testGetAllMethodsContainsMethodsOfImplementedInterface
     */
    public function testGetAllMethodsContainsMethodsOfImplementedInterface(): void
    {
        $class = $this->getFirstClassForTestCase();
        $actual = array_keys($class->getAllMethods());
        sort($actual);

        $this->assertEquals(['bar', 'baz', 'foo'], $actual);
    }

    /**
     * testGetAllMethodsContainsMethodsOfImplementedInterfaces
     */
    public function testGetAllMethodsContainsMethodsOfImplementedInterfaces(): void
    {
        $class = $this->getFirstClassForTestCase();
        $actual = array_keys($class->getAllMethods());
        sort($actual);

        $this->assertEquals(['bar', 'baz', 'foo'], $actual);
    }

    /**
     * testGetAllMethodsContainsMethodsOfIndirectImplementedInterfaces
     */
    public function testGetAllMethodsContainsMethodsOfIndirectImplementedInterfaces(): void
    {
        $class = $this->getFirstClassForTestCase();
        $actual = array_keys($class->getAllMethods());
        sort($actual);

        $this->assertEquals(['bar', 'baz', 'foo'], $actual);
    }

    /**
     * testGetAllMethodsContainsMethodsOfParentClass
     */
    public function testGetAllMethodsContainsMethodsOfParentClass(): void
    {
        $class = $this->getFirstClassForTestCase();
        $actual = array_keys($class->getAllMethods());
        sort($actual);

        $this->assertEquals(['bar', 'baz', 'foo'], $actual);
    }

    /**
     * testGetAllMethodsContainsMethodsOfParentClasses
     */
    public function testGetAllMethodsContainsMethodsOfParentClasses(): void
    {
        $class = $this->getFirstClassForTestCase();
        $actual = array_keys($class->getAllMethods());
        sort($actual);

        $this->assertEquals(['bar', 'baz', 'foo'], $actual);
    }

    /**
     * testGetAllMethodsOnClassWithParentReturnsTraitMethod
     *
     * @since 1.0.0
     */
    public function testGetAllMethodsOnClassWithParentReturnsTraitMethod(): void
    {
        $class = $this->getFirstClassForTestCase();
        $methods = $class->getAllMethods();

        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTTrait',
            $methods['foo']->getParent()
        );
    }

    /**
     * testGetAllMethodsOnClassWhereTraitExcludesParentMethod
     *
     * @since 1.0.0
     */
    public function testGetAllMethodsOnClassWhereTraitExcludesParentMethod(): void
    {
        $class = $this->getFirstClassForTestCase();
        $methods = $class->getAllMethods();

        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTTrait',
            $methods['foo']->getParent()
        );
    }

    /**
     * testGetAllMethodsOnClassWithParentAndPrecedenceReturnsParentMethod
     *
     * @since 1.0.0
     */
    public function testGetAllMethodsOnClassWithParentAndPrecedenceReturnsParentMethod(): void
    {
        $class = $this->getFirstClassForTestCase();
        $methods = $class->getAllMethods();

        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTClass',
            $methods['foo']->getParent()
        );
    }

    /**
     * testGetAllMethodsOnTraitUsingTraitReturnsExpectedResult
     *
     * @since 1.0.0
     */
    public function testGetAllMethodsOnTraitUsingTraitReturnsExpectedResult(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertEquals(
            ['foo', 'bar', 'baz'],
            array_keys($class->getAllMethods())
        );
    }

    /**
     * testGetAllMethodsWithRedeclaredMethodReturnsExpectedInstance
     *
     * @since 1.0.0
     */
    public function testGetAllMethodsWithRedeclaredMethodReturnsExpectedInstance(): void
    {
        $class = $this->getFirstClassForTestCase();
        $methods = $class->getAllMethods();

        $this->assertSame($class, $methods['foo']->getParent());
    }

    /**
     * testGetAllMethodsWithAliasedMethodCollision
     *
     * @since 1.0.0
     */
    public function testGetAllMethodsWithAliasedMethodCollision(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertEquals(
            ['foo', 'bar'],
            array_keys($class->getAllMethods())
        );
    }

    /**
     * testGetAllMethodsWithAliasedMethodTwice
     *
     * @since 1.0.0
     */
    public function testGetAllMethodsWithAliasedMethodTwice(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertEquals(
            ['foo', 'bar'],
            array_keys($class->getAllMethods())
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedToPublic
     *
     * @since 1.0.0
     */
    public function testGetAllMethodsWithVisibilityChangedToPublic(): void
    {
        $class = $this->getFirstClassForTestCase();
        $methods = $class->getAllMethods();

        $this->assertEquals(
            State::IS_PUBLIC,
            $methods['foo']->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedToProtected
     *
     * @since 1.0.0
     */
    public function testGetAllMethodsWithVisibilityChangedToProtected(): void
    {
        $class = $this->getFirstClassForTestCase();
        $methods = $class->getAllMethods();

        $this->assertEquals(
            State::IS_PROTECTED,
            $methods['foo']->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedToPrivate
     *
     * @since 1.0.0
     */
    public function testGetAllMethodsWithVisibilityChangedToPrivate(): void
    {
        $class = $this->getFirstClassForTestCase();
        $methods = $class->getAllMethods();

        $this->assertEquals(
            State::IS_PRIVATE,
            $methods['foo']->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedKeepsAbstractModifier
     *
     * @since 1.0.0
     */
    public function testGetAllMethodsWithVisibilityChangedKeepsAbstractModifier(): void
    {
        $class = $this->getFirstClassForTestCase();
        $methods = $class->getAllMethods();

        $this->assertEquals(
            State::IS_PROTECTED | State::IS_ABSTRACT,
            $methods['foo']->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedKeepsStaticModifier
     *
     * @since 1.0.0
     */
    public function testGetAllMethodsWithVisibilityChangedKeepsStaticModifier(): void
    {
        $class = $this->getFirstClassForTestCase();
        $methods = $class->getAllMethods();

        $this->assertEquals(
            State::IS_PUBLIC | State::IS_STATIC,
            $methods['foo']->getModifiers()
        );
    }

    /**
     * testGetAllMethodsHandlesTraitMethodPrecedence
     *
     * @since 1.0.0
     */
    public function testGetAllMethodsHandlesTraitMethodPrecedence(): void
    {
        $class = $this->getFirstClassForTestCase();
        $methods = $class->getAllMethods();

        $this->assertEquals(
            'testGetAllMethodsHandlesTraitMethodPrecedenceUsedTraitOne',
            $methods['foo']->getParent()->getName()
        );
    }

    /**
     * testGetAllMethodsExcludeTraitMethodWithPrecedence
     *
     * @since 1.0.0
     */
    public function testGetAllMethodsExcludeTraitMethodWithPrecedence(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertCount(1, $class->getAllMethods());
    }

    /**
     * testGetAllMethodsWithMethodCollisionThrowsExpectedException
     *
     * @covers \PDepend\Source\AST\ASTTraitMethodCollisionException
     * @since 1.0.0
     *
     * @group issue-154
     */
    public function testGetAllMethodsWithMethodCollisionThrowsExpectedException(): void
    {
        $this->expectException(ASTTraitMethodCollisionException::class);

        $class = $this->getFirstClassForTestCase();
        $class->getAllMethods();
    }

    /**
     * testGetAllChildrenReturnsAnEmptyArrayByDefault
     *
     * @since 1.0.0
     */
    public function testGetAllChildrenReturnsAnEmptyArrayByDefault(): void
    {
        $class = new ASTClass(__CLASS__);
        $this->assertSame([], $class->getChildren());
    }

    /**
     * testGetAllChildrenReturnsArrayWithExpectedNumberOfNodes
     *
     * @since 1.0.0
     */
    public function testGetAllChildrenReturnsArrayWithExpectedNumberOfNodes(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertCount(3, $class->getChildren());
    }

    /**
     * testGetConstantsReturnsAnEmptyArrayByDefault
     */
    public function testGetConstantsReturnsAnEmptyArrayByDefault(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertEquals([], $class->getConstants());
    }

    /**
     * testGetConstantsReturnsExpectedConstant
     */
    public function testGetConstantsReturnsExpectedConstant(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertEquals(['FOO' => 42], $class->getConstants());
    }

    /**
     * testGetConstantsReturnsExpectedConstants
     */
    public function testGetConstantsReturnsExpectedConstants(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertEquals(['FOO' => 42, 'BAR' => 23], $class->getConstants());
    }

    /**
     * testGetConstantsReturnsExpectedParentConstants
     */
    public function testGetConstantsReturnsExpectedParentConstants(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertEquals(['FOO' => 42, 'BAR' => 23], $class->getConstants());
    }

    /**
     * testGetConstantsReturnsExpectedMergedParentAndChildConstants
     */
    public function testGetConstantsReturnsExpectedMergedParentAndChildConstants(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertEquals(['FOO' => 42, 'BAR' => 23], $class->getConstants());
    }

    /**
     * testGetConstantsReturnsExpectedInterfaceConstants
     *
     * @since 1.0.0
     */
    public function testGetConstantsReturnsExpectedInterfaceConstants(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertEquals(['FOO' => 42, 'BAR' => 23], $class->getConstants());
    }

    /**
     * testGetConstantReturnsFalseForNotExistentConstant
     */
    public function testGetConstantReturnsFalseForNotExistentConstant(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertFalse($class->getConstant('BAR'));
    }

    /**
     * testGetConstantReturnsExpectedValueForExistentConstant
     */
    public function testGetConstantReturnsExpectedValueForExistentConstant(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertEquals(42, $class->getConstant('BAR'));
    }

    /**
     * testGetConstantReturnsExpectedValueNullForExistentConstant
     */
    public function testGetConstantReturnsExpectedValueNullForExistentConstant(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertNull($class->getConstant('BAR'));
    }

    /**
     * testHasConstantReturnsFalseForNotExistentConstant
     */
    public function testHasConstantReturnsFalseForNotExistentConstant(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertFalse($class->hasConstant('BAR'));
    }

    /**
     * testHasConstantReturnsTrueForExistentConstant
     */
    public function testHasConstantReturnsTrueForExistentConstant(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertTrue($class->hasConstant('BAR'));
    }

    /**
     * testHasConstantReturnsTrueForExistentNullConstant
     */
    public function testHasConstantReturnsTrueForExistentNullConstant(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertTrue($class->hasConstant('BAR'));
    }

    /**
     * testGetDependenciesReturnsEmptyResultByDefault
     *
     * @since 1.0.0
     */
    public function testGetDependenciesReturnsEmptyResultByDefault(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertCount(0, $class->getDependencies());
    }

    /**
     * testGetDependenciesContainsImplementedInterface
     *
     * @since 1.0.0
     */
    public function testGetDependenciesContainsImplementedInterface(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertCount(1, $class->getDependencies());
    }

    /**
     * testGetDependenciesContainsImplementedInterfaces
     *
     * @since 1.0.0
     */
    public function testGetDependenciesContainsImplementedInterfaces(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertCount(3, $class->getDependencies());
    }

    /**
     * testGetDependenciesContainsParentClass
     *
     * @since 1.0.0
     */
    public function testGetDependenciesContainsParentClass(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertCount(1, $class->getDependencies());
    }

    /**
     * testGetDependenciesContainsParentClassAndInterfaces
     *
     * @since 1.0.0
     */
    public function testGetDependenciesContainsParentClassAndInterfaces(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertCount(3, $class->getDependencies());
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::getFirstChildOfType()}.
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedFirstMatch(): void
    {
        $node1 = $this->getMockBuilder('PDepend\\Source\\AST\\ASTNode')
            ->setMockClassName('Class_' . __FUNCTION__ . '_' . md5(microtime()))
            ->getMock();
        $node1->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $node2 = $this->getMockBuilder('PDepend\\Source\\AST\\ASTNode')
            ->setMockClassName('Class_' . __FUNCTION__ . '_' . md5(microtime()))
            ->getMock();
        $node2->expects($this->never())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $class = new ASTClass('Clazz');
        $class->addChild($node1);
        $class->addChild($node2);

        $child = $class->getFirstChildOfType($node2::class);
        $this->assertSame($node2, $child);
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::getFirstChildOfType()}.
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedNestedMatch(): void
    {
        $node1 = $this->getMockBuilder('PDepend\\Source\\AST\\ASTNode')
            ->setMockClassName('Class_' . __FUNCTION__ . '_' . md5(microtime()))
            ->getMock();
        $node1->expects($this->never())
            ->method('getFirstChildOfType');

        $node2 = $this->getMockBuilder('PDepend\\Source\\AST\\ASTNode')
            ->setMockClassName('Class_' . __FUNCTION__ . '_' . md5(microtime()))
            ->getMock();
        $node2->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $node3 = $this->getMockBuilder('PDepend\\Source\\AST\\ASTNode')
            ->setMockClassName('Class_' . __FUNCTION__ . '_' . md5(microtime()))
            ->getMock();
        $node3->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue($node1));

        $class = new ASTClass('Clazz');
        $class->addChild($node2);
        $class->addChild($node3);

        $child = $class->getFirstChildOfType($node1::class);
        $this->assertSame($node1, $child);
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::getFirstChildOfType()}.
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedNull(): void
    {
        $node1 = $this->getMockBuilder('PDepend\\Source\\AST\\ASTNode')
            ->setMockClassName('Class_' . __FUNCTION__ . '_' . md5(microtime()))
            ->getMock();
        $node1->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $node2 = $this->getMockBuilder('PDepend\\Source\\AST\\ASTNode')
            ->setMockClassName('Class_' . __FUNCTION__ . '_' . md5(microtime()))
            ->getMock();
        $node2->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $class = new ASTClass('Clazz');
        $class->setCache(new MemoryCacheDriver());
        $class->addChild($node1);
        $class->addChild($node2);

        $child = $class->getFirstChildOfType(
            'Class_' . __FUNCTION__ . '_' . md5(microtime())
        );
        $this->assertNull($child);
    }

    /**
     * testGetFirstChildOfTypeFindsASTNodeInMethodDeclaration
     */
    public function testGetFirstChildOfTypeFindsASTNodeInMethodDeclaration(): void
    {
        $class = $this->getFirstClassForTestCase();
        $params = $class->getFirstChildOfType(
            'PDepend\\Source\\AST\\ASTFormalParameter'
        );

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFormalParameter', $params);
    }

    /**
     * testGetFirstChildOfTypeFindsASTNodeInMethodDeclaration
     */
    public function testFindChildrenOfTypeFindsASTNodeInMethodDeclarations(): void
    {
        $class = $this->getFirstClassForTestCase();
        $params = $class->findChildrenOfType(
            'PDepend\\Source\\AST\\ASTFormalParameter'
        );

        $this->assertCount(4, $params);
    }

    /**
     * testFindChildrenOfTypeFindsASTNodesFromVariousCodeItems
     */
    public function testFindChildrenOfTypeFindsASTNodesFromVariousCodeItems(): void
    {
        $class = $this->getFirstClassForTestCase();
        $params = $class->findChildrenOfType(
            'PDepend\\Source\\AST\\ASTVariableDeclarator'
        );

        $this->assertCount(2, $params);
    }

    /**
     * testUnserializedClassStillIsParentOfChildMethods
     */
    public function testUnserializedClassStillIsParentOfChildMethods(): void
    {
        $orig = $this->getFirstClassForTestCase();
        $copy = unserialize(serialize($orig));

        $this->assertSame($copy, $copy->getMethods()->current()->getParent());
    }

    /**
     * testUnserializedClassAndChildMethodsStillReferenceTheSameFile
     */
    public function testUnserializedClassAndChildMethodsStillReferenceTheSameFile(): void
    {
        $orig = $this->getFirstClassForTestCase();
        $copy = unserialize(serialize($orig));

        $this->assertSame(
            $copy->getCompilationUnit(),
            $copy->getMethods()->current()->getCompilationUnit()
        );
    }

    /**
     * testUnserializedClassStillReferencesSameParentClass
     */
    public function testUnserializedClassStillReferencesSameParentClass(): void
    {
        $orig = $this->getFirstClassForTestCase();
        $copy = unserialize(serialize($orig));

        $this->assertSame(
            $orig->getParentClass(),
            $copy->getParentClass()
        );
    }

    /**
     * testUnserializedClassStillReferencesSameParentInterface
     */
    public function testUnserializedClassStillReferencesSameParentInterface(): void
    {
        $orig = $this->getFirstClassForTestCase();
        $copy = unserialize(serialize($orig));

        $this->assertSame(
            $orig->getInterfaces()->current(),
            $copy->getInterfaces()->current()
        );
    }

    /**
     * testUnserializedClassIsReturnedByMethodAsReturnClass
     */
    public function testUnserializedClassIsReturnedByMethodAsReturnClass(): void
    {
        $orig = $this->getFirstClassForTestCase();
        $method = $orig->getMethods()->current();

        $copy = unserialize(serialize($orig));

        $this->assertSame(
            $method->getReturnClass(),
            $copy
        );
    }

    /**
     * testUnserializedClassStillReferencesSamePackage
     */
    public function testUnserializedClassStillReferencesSamePackage(): void
    {
        $orig = $this->getFirstClassForTestCase();
        $copy = unserialize(serialize($orig));

        $this->assertSame(
            $orig->getNamespace(),
            $copy->getNamespace()
        );
    }

    /**
     * testUnserializedClassRegistersToPackage
     */
    public function testUnserializedClassRegistersToPackage(): void
    {
        $orig = $this->getFirstClassForTestCase();
        $copy = unserialize(serialize($orig));

        $this->assertSame($copy, $orig->getNamespace()->getClasses()->current());
    }

    /**
     * testUnserializedClassNotAddsDublicateClassToPackage
     */
    public function testUnserializedClassNotAddsDublicateClassToPackage(): void
    {
        $orig = $this->getFirstClassForTestCase();
        $copy = unserialize(serialize($orig));

        $this->assertCount(1, $orig->getNamespace()->getClasses());
    }

    /**
     * Tests the ctor and the {@link \PDepend\Source\AST\ASTClass::getName()}.
     */
    public function testCreateNewClassInstance(): void
    {
        $class = new ASTClass(__CLASS__);
        $this->assertEquals(__CLASS__, $class->getName());
    }

    /**
     * testIsAbstractReturnsFalseByDefault
     */
    public function testIsAbstractReturnsFalseByDefault(): void
    {
        $class = new ASTClass(__CLASS__);
        $this->assertFalse($class->isAbstract());
    }

    /**
     * testMarkClassInstanceAsAbstract
     */
    public function testMarkClassInstanceAsAbstract(): void
    {
        $class = new ASTClass(__CLASS__);
        $class->setModifiers(State::IS_EXPLICIT_ABSTRACT);

        $this->assertTrue($class->isAbstract());
    }

    /**
     * testIsFinalReturnsFalseByDefault
     */
    public function testIsFinalReturnsFalseByDefault(): void
    {
        $class = new ASTClass(__CLASS__);
        $this->assertFalse($class->isFinal());
    }

    /**
     * testMarkClassInstanceAsFinal
     */
    public function testMarkClassInstanceAsFinal(): void
    {
        $class = new ASTClass(__CLASS__);
        $class->setModifiers(State::IS_FINAL);

        $this->assertTrue($class->isFinal());
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTClass::setModifiers()}
     * when it is called with an invalid modifier.
     */
    public function testSetModifiersThrowsExpectedExceptionForInvalidModifier(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $class = new ASTClass(__CLASS__);
        $class->setModifiers(
            2 |
            State::IS_FINAL
        );
    }

    /**
     * Tests that a new {@link \PDepend\Source\AST\ASTClass} object returns
     * an empty {@link \PDepend\Source\AST\ASTArtifactList} instance for methods.
     */
    public function testGetMethodsNodeIteratorIsEmptyByDefault(): void
    {
        $class = new ASTClass(__CLASS__);
        $class->setCache(new MemoryCacheDriver());

        $this->assertEquals(0, $class->getMethods()->count());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTClass::addMethod()}
     * method adds a method to the internal list and sets the context class as
     * parent.
     */
    public function testAddMethodStoresNewlyAddedMethodInCollection(): void
    {
        $class = new ASTClass(__CLASS__);
        $class->setCache(new MemoryCacheDriver());
        $class->addMethod(new ASTMethod(__FUNCTION__));

        $this->assertEquals(1, $class->getMethods()->count());
    }

    /**
     * testAddMethodSetsParentOfNewlyAddedMethod
     */
    public function testAddMethodSetsParentOfNewlyAddedMethod(): void
    {
        $class = new ASTClass(__CLASS__);
        $class->setCache(new MemoryCacheDriver());

        $method = $class->addMethod(new ASTMethod(__FUNCTION__));

        $this->assertSame($class, $method->getParent());
    }

    /**
     * testGetNamespaceReturnsNullByDefault
     */
    public function testGetNamespaceReturnsNullByDefault(): void
    {
        $class = new ASTClass(__CLASS__);
        $this->assertNull($class->getNamespace());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTClass::getNamespace()}
     * returns as default value <b>null</b> and that the namespace could be set
     * and unset.
     */
    public function testGetSetNamespace(): void
    {
        $namespace = new ASTNamespace(__FUNCTION__);
        $class = new ASTClass(__CLASS__);

        $class->setNamespace($namespace);
        $this->assertSame($namespace, $class->getNamespace());
    }

    /**
     * testUnsetNamespaceResetsNamespaceReference
     */
    public function testUnsetNamespaceResetsNamespaceReference(): void
    {
        $class = new ASTClass(__CLASS__);

        $class->setNamespace(new ASTNamespace(__FUNCTION__));
        $class->unsetNamespace();

        $this->assertNull($class->getNamespace());
    }

    /**
     * testUnsetNamespaceResetsNamespaceNameProperty
     *
     * @since 0.10.2
     */
    public function testUnsetNamespaceResetsNamespaceNameProperty(): void
    {
        $class = new ASTClass(__CLASS__);

        $class->setNamespace(new ASTNamespace(__FUNCTION__));
        $class->unsetNamespace();

        $this->assertNull($class->getNamespaceName());
    }

    /**
     * Tests that {@link \PDepend\Source\AST\ASTClass::getInterfaces()}
     * returns the expected result.
     */
    public function testGetInterfaces(): void
    {
        $class = $this->getFirstClassForTestCase();

        $actual = [];
        foreach ($class->getInterfaces() as $interface) {
            $actual[] = $interface->getName();
        }
        sort($actual);

        $this->assertEquals(['A', 'C', 'E', 'F'], $actual);
    }

    /**
     * Tests that {@link \PDepend\Source\AST\ASTClass::getInterfaces()}
     * returns the expected result.
     */
    public function testGetInterfacesByInheritance(): void
    {
        $classes = $this->parseCodeResourceForTest()
            ->current()
            ->getClasses();

        $classes->next();
        $class = $classes->current();

        $actual = [];
        foreach ($class->getInterfaces() as $interface) {
            $actual[$interface->getName()] = $interface->getName();
        }
        sort($actual);

        $this->assertEquals(['A', 'B', 'C', 'D', 'E', 'F'], $actual);
    }

    /**
     * Tests that {@link \PDepend\Source\AST\ASTClass::getInterfaces()}
     * returns the expected result.
     */
    public function testGetInterfacesByClassInheritence(): void
    {
        $class = $this->getFirstClassForTestCase();

        $actual = [];
        foreach ($class->getInterfaces() as $interface) {
            $actual[] = $interface->getName();
        }
        sort($actual);

        $this->assertEquals(['A', 'B'], $actual);
    }

    /**
     * Checks the {@link \PDepend\Source\AST\ASTClass::isSubtypeOf()} method.
     */
    public function testIsSubtypeInInheritanceHierarchy(): void
    {
        $types = $this->parseCodeResourceForTest()
            ->current()
            ->getTypes();

        $class = $types->current();

        $actual = [];
        foreach ($types as $type) {
            $actual[$type->getName()] = $class->isSubtypeOf($type);
        }
        ksort($actual);

        $expected = [
            'A' => true,
            'B' => false,
            'C' => false,
            'D' => true,
            'E' => true,
            'F' => false,
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * Checks the {@link \PDepend\Source\AST\ASTClass::isSubtypeOf()} method.
     */
    public function testIsSubtypeInClassInheritanceHierarchy(): void
    {
        $types = $this->parseCodeResourceForTest()
            ->current()
            ->getTypes();

        $class = $types->current();

        $actual = [];
        foreach ($types as $type) {
            $actual[$type->getName()] = $class->isSubtypeOf($type);
        }
        ksort($actual);

        $expected = [
            'A' => true,
            'B' => true,
            'C' => false,
            'D' => true,
            'E' => true,
            'F' => false,
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * Checks the {@link \PDepend\Source\AST\ASTClass::isSubtypeOf()} method.
     */
    public function testIsSubtypeInClassAndInterfaceInheritanceHierarchy(): void
    {
        $types = $this->parseCodeResourceForTest()
            ->current()
            ->getTypes();

        $class = $types->current();

        $actual = [];
        foreach ($types as $type) {
            $actual[$type->getName()] = $class->isSubtypeOf($type);
        }
        ksort($actual);

        $expected = [
            'A' => true,
            'B' => true,
            'C' => true,
            'D' => true,
            'E' => true,
            'F' => true,
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * testGetPropertiesReturnsExpectedNumberOfProperties
     */
    public function testGetPropertiesReturnsExpectedNumberOfProperties(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertCount(6, $class->getProperties());
    }

    /**
     * Tests that it is not possible to overwrite previously set class modifiers.
     */
    public function testSetModifiersThrowsExpectedExceptionOnOverwrite(): void
    {
        $this->expectException(BadMethodCallException::class);

        $class = new ASTClass(__CLASS__);
        $class->setModifiers(State::IS_FINAL);
        $class->setModifiers(State::IS_FINAL);
    }

    /**
     * testGetModifiersReturnsZeroByDefault
     */
    public function testGetModifiersReturnsZeroByDefault(): void
    {
        $class = new ASTClass(__CLASS__);
        $this->assertSame(0, $class->getModifiers());
    }

    /**
     * testGetModifiersReturnsInjectedModifierValue
     */
    public function testGetModifiersReturnsInjectedModifierValue(): void
    {
        $class = new ASTClass(__CLASS__);
        $class->setModifiers(State::IS_FINAL);

        $this->assertSame(State::IS_FINAL, $class->getModifiers());
    }

    /**
     * Tests the visitor accept method.
     */
    public function testVisitorAccept(): void
    {
        $class = new ASTClass(__CLASS__);
        $visitor = new StubASTVisitor();

        $class->accept($visitor);
        $this->assertSame($class, $visitor->class);
    }

    /**
     * testGetTokensDelegatesCallToCacheRestore
     */
    public function testGetTokensDelegatesCallToCacheRestore(): void
    {
        $cache = $this->createCacheFixture();
        $cache->expects($this->once())
            ->method('type')
            ->with($this->equalTo('tokens'))
            ->will($this->returnValue($cache));
        $cache->expects($this->once())
            ->method('restore');

        $class = new ASTClass(__CLASS__);
        $class->setCache($cache)
            ->getTokens();
    }

    /**
     * testSetTokensDelegatesCallToCacheStore
     */
    public function testSetTokensDelegatesCallToCacheStore(): void
    {
        $tokens = [new Token(1, 'a', 23, 42, 13, 17)];

        $cache = $this->createCacheFixture();
        $cache->expects($this->once())
            ->method('type')
            ->with($this->equalTo('tokens'))
            ->will($this->returnValue($cache));
        $cache->expects($this->once())
            ->method('store')
            ->with($this->isType('string'), $this->equalTo($tokens));

        $class = new ASTClass(__CLASS__);
        $class->setCache($cache)
            ->setTokens($tokens);
    }

    /**
     * testGetStartLineReturnsZeroByDefault
     */
    public function testGetStartLineReturnsZeroByDefault(): void
    {
        $class = new ASTClass(__CLASS__);
        $this->assertSame(0, $class->getStartLine());
    }

    /**
     * testGetStartLineReturnsStartLineOfFirstToken
     */
    public function testGetStartLineReturnsStartLineOfFirstToken(): void
    {
        $cache = $this->createCacheFixture();
        $cache->expects($this->once())
            ->method('type')
            ->will($this->returnValue($cache));

        $class = new ASTClass(__CLASS__);
        $class->setCache($cache)
            ->setTokens(
                [
                    new Token(1, 'a', 23, 42, 0, 0),
                    new Token(2, 'b', 17, 32, 0, 0),
                ]
            );

        $this->assertEquals(23, $class->getStartLine());
    }

    /**
     * testGetEndLineReturnsZeroByDefault
     */
    public function testGetEndLineReturnsZeroByDefault(): void
    {
        $class = new ASTClass(__CLASS__);
        $this->assertSame(0, $class->getEndLine());
    }

    /**
     * testGetEndLineReturnsEndLineOfLastToken
     */
    public function testGetEndLineReturnsEndLineOfLastToken(): void
    {
        $cache = $this->createCacheFixture();
        $cache->expects($this->once())
            ->method('type')
            ->will($this->returnValue($cache));

        $class = new ASTClass(__CLASS__);
        $class->setCache($cache)
            ->setTokens(
                [
                    new Token(1, 'a', 23, 42, 0, 0),
                    new Token(2, 'b', 17, 32, 0, 0),
                ]
            );

        $this->assertEquals(32, $class->getEndLine());
    }

    /**
     * testGetParentClassReferenceReturnsNullByDefault
     */
    public function testGetParentClassReferenceReturnsNullByDefault(): void
    {
        $class = new ASTClass(__CLASS__);
        $this->assertNull($class->getParentClassReference());
    }

    /**
     * testGetParentClassReturnsExpectedClassInstance
     */
    public function testGetParentClassReturnsExpectedClassInstance(): void
    {
        $parent = $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getParentClass();

        $this->assertNotNull($parent);
    }

    /**
     * testGetParentClassThrowsExpectedExceptionWhenBothAreTheSame
     *
     * @covers \PDepend\Source\AST\ASTClassOrInterfaceRecursiveInheritanceException
     */
    public function testGetParentClassThrowsExpectedExceptionWhenBothAreTheSame(): void
    {
        $this->expectException(ASTClassOrInterfaceRecursiveInheritanceException::class);

        $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getParentClass();
    }

    /**
     * testGetParentClassesReturnsEmptyArrayByDefault
     */
    public function testGetParentClassesReturnsEmptyArrayByDefault(): void
    {
        $class = new ASTClass(__CLASS__);
        $this->assertSame([], $class->getParentClasses());
    }

    /**
     * testGetParentClassesReturnsExpectedListClasses
     */
    public function testGetParentClassesReturnsExpectedListClasses(): void
    {
        $classes = $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getParentClasses();

        foreach ($classes as $i => $class) {
            $classes[$i] = $class->getName();
        }

        $this->assertEquals(
            [
                'testGetParentClassesReturnsExpectedListClasses_parentA',
                'testGetParentClassesReturnsExpectedListClasses_parentB',
                'testGetParentClassesReturnsExpectedListClasses_parentC',
            ],
            $classes
        );
    }

    /**
     * testGetParentReturnsNullWhenParentIsFiltered
     *
     * @since 1.0.0
     */
    public function testGetParentReturnsNullWhenParentIsFiltered(): void
    {
        CollectionArtifactFilter::getInstance()->setFilter(
            new PackageArtifactFilter(['org.pdepend.filter'])
        );

        $class = $this->getFirstClassForTestCase();
        $this->assertNull($class->getParentClass());
    }

    /**
     * testGetParentClassesThrowsExpectedExceptionForRecursiveInheritanceHierarchy
     *
     * @covers \PDepend\Source\AST\ASTClassOrInterfaceRecursiveInheritanceException
     */
    public function testGetParentClassesThrowsExpectedExceptionForRecursiveInheritanceHierarchy(): void
    {
        $this->expectException(ASTClassOrInterfaceRecursiveInheritanceException::class);

        $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getParentClasses();
    }

    /**
     * testGetInterfaceReferencesReturnsEmptyArrayByDefault
     */
    public function testGetInterfaceReferencesReturnsEmptyArrayByDefault(): void
    {
        $class = new ASTClass(__CLASS__);
        $this->assertSame([], $class->getInterfaceReferences());
    }

    /**
     * testGetInterfaceReferencesReturnsExpectedNumberOfInterfaces
     */
    public function testGetInterfaceReferencesReturnsExpectedNumberOfInterfaces(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertCount(3, $class->getInterfaceReferences());
    }

    /**
     * testIsUserDefinedReturnsFalseByDefault
     */
    public function testIsUserDefinedReturnsFalseByDefault(): void
    {
        $class = new ASTClass(__CLASS__);
        $this->assertFalse($class->isUserDefined());
    }

    /**
     * testIsUserDefinedReturnsTrueAfterSetUserDefinedCall
     */
    public function testIsUserDefinedReturnsTrueAfterSetUserDefinedCall(): void
    {
        $class = $this->createItem();
        $class->setUserDefined();

        $this->assertTrue($class->isUserDefined());
    }

    /**
     * testIsCachedReturnsFalseByDefault
     */
    public function testIsCachedReturnsFalseByDefault(): void
    {
        $class = $this->createItem();
        $this->assertFalse($class->isCached());
    }

    /**
     * testIsCachedReturnsFalseWhenObjectGetsSerialized
     */
    public function testIsCachedReturnsFalseWhenObjectGetsSerialized(): void
    {
        $class = $this->createItem();
        serialize($class);

        $this->assertFalse($class->isCached());
    }

    public function testGetNamespacedName(): void
    {
        $class = new ASTClass('MyClass');
        $this->assertSame('MyClass', $class->getNamespacedName());
    }

    public function testGetNamespacedNameWithNamespaceDeclaration(): void
    {
        $class = new ASTClass('MyClass');
        $class->setNamespace(new ASTNamespace('My\\Namespace'));

        $this->assertSame('My\\Namespace\\MyClass', $class->getNamespacedName());
    }

    public function testGetNamespacedNameWithPackageAnnotation(): void
    {
        $namespace = new ASTNamespace('My\\Namespace');
        $namespace->setPackageAnnotation(true);

        $class = new ASTClass('MyClass');
        $class->setNamespace($namespace);

        $this->assertSame('MyClass', $class->getNamespacedName());
    }

    /**
     * testMagicSleepMethodReturnsExpectedSetOfPropertyNames
     */
    public function testMagicSleepMethodReturnsExpectedSetOfPropertyNames(): void
    {
        $class = new ASTClass(__CLASS__);
        $class->setCache(new MemoryCacheDriver());
        $class->setNamespace(new ASTNamespace(__FUNCTION__));

        $this->assertEquals(
            [
                'constants',
                'interfaceReferences',
                'parentClassReference',
                'cache',
                'context',
                'comment',
                'endLine',
                'modifiers',
                'name',
                'nodes',
                'namespaceName',
                'startLine',
                'userDefined',
                'id',
            ],
            $class->__sleep()
        );
    }

    /**
     * testMagicWakeupSetsSourceFileOnChildMethods
     */
    public function testMagicWakeupSetsSourceFileOnChildMethods(): void
    {
        $class = new ASTClass(__CLASS__);
        $class->setCache(new MemoryCacheDriver());

        $method = new ASTMethod(__FUNCTION__);
        $class->addMethod($method);

        $context = $this->getMockBuilder('PDepend\\Source\\Builder\\BuilderContext')
            ->getMock();
        $class->setContext($context);

        $file = new ASTCompilationUnit(__FILE__);
        $class->setCompilationUnit($file);
        $class->__wakeup();

        $this->assertSame($file, $method->getCompilationUnit());
    }

    /**
     * testMagicWakeupCallsRegisterClassOnBuilderContext
     */
    public function testMagicWakeupCallsRegisterClassOnBuilderContext(): void
    {
        $class = new ASTClass(__CLASS__);

        $context = $this->getMockBuilder('PDepend\\Source\\Builder\\BuilderContext')
            ->getMock();
        $context->expects($this->once())
            ->method('registerClass')
            ->with($this->isInstanceOf('PDepend\\Source\\AST\\ASTClass'));

        $class->setContext($context)->__wakeup();
    }

    /**
     * Creates an abstract item instance.
     *
     * @return AbstractASTArtifact
     */
    protected function createItem()
    {
        $class = new ASTClass(__CLASS__);
        $class->setCompilationUnit(new ASTCompilationUnit(__FILE__));
        $class->setCache(new MemoryCacheDriver());

        $context = $this->getMockBuilder('PDepend\\Source\\Builder\\BuilderContext')
            ->getMock();
        $class->setContext($context);

        return $class;
    }
}
