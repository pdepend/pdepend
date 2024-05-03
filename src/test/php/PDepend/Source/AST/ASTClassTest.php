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

use PDepend\Source\AST\ASTArtifactList\CollectionArtifactFilter;
use PDepend\Source\AST\ASTArtifactList\PackageArtifactFilter;
use PDepend\Source\Builder\BuilderContext;
use PDepend\Source\Tokenizer\Token;
use PDepend\Source\ASTVisitor\StubASTVisitor;
use PDepend\Util\Cache\Driver\MemoryCacheDriver;

/**
 * Test case implementation for the \PDepend\Source\AST\ASTClass class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\AST\AbstractASTClassOrInterface
 * @covers \PDepend\Source\AST\AbstractASTType
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @group unittest
 */
class ASTClassTest extends AbstractASTArtifactTestCase
{
    /**
     * testGetAllMethodsContainsMethodsOfImplementedInterface
     *
     * @return void
     */
    public function testGetAllMethodsContainsMethodsOfImplementedInterface()
    {
        $class  = $this->getFirstClassForTestCase();
        $actual = array_keys($class->getAllMethods());
        sort($actual);

        $this->assertEquals(['bar', 'baz', 'foo'], $actual);
    }

    /**
     * testGetAllMethodsContainsMethodsOfImplementedInterfaces
     *
     * @return void
     */
    public function testGetAllMethodsContainsMethodsOfImplementedInterfaces()
    {
        $class  = $this->getFirstClassForTestCase();
        $actual = array_keys($class->getAllMethods());
        sort($actual);

        $this->assertEquals(['bar', 'baz', 'foo'], $actual);
    }

    /**
     * testGetAllMethodsContainsMethodsOfIndirectImplementedInterfaces
     *
     * @return void
     */
    public function testGetAllMethodsContainsMethodsOfIndirectImplementedInterfaces()
    {
        $class  = $this->getFirstClassForTestCase();
        $actual = array_keys($class->getAllMethods());
        sort($actual);

        $this->assertEquals(['bar', 'baz', 'foo'], $actual);
    }

    /**
     * testGetAllMethodsContainsMethodsOfParentClass
     *
     * @return void
     */
    public function testGetAllMethodsContainsMethodsOfParentClass()
    {
        $class  = $this->getFirstClassForTestCase();
        $actual = array_keys($class->getAllMethods());
        sort($actual);

        $this->assertEquals(['bar', 'baz', 'foo'], $actual);
    }

    /**
     * testGetAllMethodsContainsMethodsOfParentClasses
     *
     * @return void
     */
    public function testGetAllMethodsContainsMethodsOfParentClasses()
    {
        $class  = $this->getFirstClassForTestCase();
        $actual = array_keys($class->getAllMethods());
        sort($actual);

        $this->assertEquals(['bar', 'baz', 'foo'], $actual);
    }

    /**
     * testGetAllMethodsOnClassWithParentReturnsTraitMethod
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetAllMethodsOnClassWithParentReturnsTraitMethod()
    {
        $class   = $this->getFirstClassForTestCase();
        $methods = $class->getAllMethods();

        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTTrait',
            $methods['foo']->getParent()
        );
    }

    /**
     * testGetAllMethodsOnClassWhereTraitExcludesParentMethod
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetAllMethodsOnClassWhereTraitExcludesParentMethod()
    {
        $class   = $this->getFirstClassForTestCase();
        $methods = $class->getAllMethods();

        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTTrait',
            $methods['foo']->getParent()
        );
    }

    /**
     * testGetAllMethodsOnClassWithParentAndPrecedenceReturnsParentMethod
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetAllMethodsOnClassWithParentAndPrecedenceReturnsParentMethod()
    {
        $class   = $this->getFirstClassForTestCase();
        $methods = $class->getAllMethods();

        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTClass',
            $methods['foo']->getParent()
        );
    }

    /**
     * testGetAllMethodsOnTraitUsingTraitReturnsExpectedResult
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetAllMethodsOnTraitUsingTraitReturnsExpectedResult()
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
     * @return void
     * @since 1.0.0
     */
    public function testGetAllMethodsWithRedeclaredMethodReturnsExpectedInstance()
    {
        $class   = $this->getFirstClassForTestCase();
        $methods = $class->getAllMethods();

        $this->assertSame($class, $methods['foo']->getParent());
    }

    /**
     * testGetAllMethodsWithAliasedMethodCollision
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetAllMethodsWithAliasedMethodCollision()
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
     * @return void
     * @since 1.0.0
     */
    public function testGetAllMethodsWithAliasedMethodTwice()
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
     * @return void
     * @since 1.0.0
     */
    public function testGetAllMethodsWithVisibilityChangedToPublic()
    {
        $class   = $this->getFirstClassForTestCase();
        $methods = $class->getAllMethods();

        $this->assertEquals(
            State::IS_PUBLIC,
            $methods['foo']->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedToProtected
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetAllMethodsWithVisibilityChangedToProtected()
    {
        $class   = $this->getFirstClassForTestCase();
        $methods = $class->getAllMethods();

        $this->assertEquals(
            State::IS_PROTECTED,
            $methods['foo']->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedToPrivate
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetAllMethodsWithVisibilityChangedToPrivate()
    {
        $class   = $this->getFirstClassForTestCase();
        $methods = $class->getAllMethods();

        $this->assertEquals(
            State::IS_PRIVATE,
            $methods['foo']->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedKeepsAbstractModifier
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetAllMethodsWithVisibilityChangedKeepsAbstractModifier()
    {
        $class   = $this->getFirstClassForTestCase();
        $methods = $class->getAllMethods();

        $this->assertEquals(
            State::IS_PROTECTED | State::IS_ABSTRACT,
            $methods['foo']->getModifiers()
        );
    }

    /**
     * testGetAllMethodsWithVisibilityChangedKeepsStaticModifier
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetAllMethodsWithVisibilityChangedKeepsStaticModifier()
    {
        $class   = $this->getFirstClassForTestCase();
        $methods = $class->getAllMethods();

        $this->assertEquals(
            State::IS_PUBLIC | State::IS_STATIC,
            $methods['foo']->getModifiers()
        );
    }

    /**
     * testGetAllMethodsHandlesTraitMethodPrecedence
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetAllMethodsHandlesTraitMethodPrecedence()
    {
        $class   = $this->getFirstClassForTestCase();
        $methods = $class->getAllMethods();

        $this->assertEquals(
            'testGetAllMethodsHandlesTraitMethodPrecedenceUsedTraitOne',
            $methods['foo']->getParent()->getName()
        );
    }

    /**
     * testGetAllMethodsExcludeTraitMethodWithPrecedence
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetAllMethodsExcludeTraitMethodWithPrecedence()
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertCount(1, $class->getAllMethods());
    }

    /**
     * testGetAllMethodsWithMethodCollisionThrowsExpectedException
     *
     * @return void
     * @since 1.0.0
     * @covers \PDepend\Source\AST\ASTTraitMethodCollisionException
     *
     * @group issue-154
     */
    public function testGetAllMethodsWithMethodCollisionThrowsExpectedException()
    {
        $this->expectException(\PDepend\Source\AST\ASTTraitMethodCollisionException::class);

        $class = $this->getFirstClassForTestCase();
        $class->getAllMethods();
    }

    /**
     * testGetAllChildrenReturnsAnEmptyArrayByDefault
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetAllChildrenReturnsAnEmptyArrayByDefault()
    {
        $class = new ASTClass(__CLASS__);
        $this->assertSame([], $class->getChildren());
    }

    /**
     * testGetAllChildrenReturnsArrayWithExpectedNumberOfNodes
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetAllChildrenReturnsArrayWithExpectedNumberOfNodes()
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertCount(2, $class->getChildren());
    }

    /**
     * testGetConstantsReturnsAnEmptyArrayByDefault
     *
     * @return void
     */
    public function testGetConstantsReturnsAnEmptyArrayByDefault()
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertEquals([], $class->getConstants());
    }

    /**
     * testGetConstantsReturnsExpectedConstant
     *
     * @return void
     */
    public function testGetConstantsReturnsExpectedConstant()
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertEquals(['FOO' => 42], $class->getConstants());
    }

    /**
     * testGetConstantsReturnsExpectedConstants
     *
     * @return void
     */
    public function testGetConstantsReturnsExpectedConstants()
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertEquals(['FOO' => 42, 'BAR' => 23], $class->getConstants());
    }

    /**
     * testGetConstantsReturnsExpectedParentConstants
     *
     * @return void
     */
    public function testGetConstantsReturnsExpectedParentConstants()
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertEquals(['FOO' => 42, 'BAR' => 23], $class->getConstants());
    }

    /**
     * testGetConstantsReturnsExpectedMergedParentAndChildConstants
     *
     * @return void
     */
    public function testGetConstantsReturnsExpectedMergedParentAndChildConstants()
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertEquals(['FOO' => 42, 'BAR' => 23], $class->getConstants());
    }

    /**
     * testGetConstantsReturnsExpectedInterfaceConstants
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetConstantsReturnsExpectedInterfaceConstants()
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertEquals(['FOO' => 42, 'BAR' => 23], $class->getConstants());
    }

    /**
     * testGetConstantReturnsFalseForNotExistentConstant
     *
     * @return void
     */
    public function testGetConstantReturnsFalseForNotExistentConstant()
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertFalse($class->getConstant('BAR'));
    }

    /**
     * testGetConstantReturnsExpectedValueForExistentConstant
     *
     * @return void
     */
    public function testGetConstantReturnsExpectedValueForExistentConstant()
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertEquals(42, $class->getConstant('BAR'));
    }

    /**
     * testGetConstantReturnsExpectedValueNullForExistentConstant
     *
     * @return void
     */
    public function testGetConstantReturnsExpectedValueNullForExistentConstant()
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertNull($class->getConstant('BAR'));
    }

    /**
     * testHasConstantReturnsFalseForNotExistentConstant
     *
     * @return void
     */
    public function testHasConstantReturnsFalseForNotExistentConstant()
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertFalse($class->hasConstant('BAR'));
    }

    /**
     * testHasConstantReturnsTrueForExistentConstant
     *
     * @return void
     */
    public function testHasConstantReturnsTrueForExistentConstant()
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertTrue($class->hasConstant('BAR'));
    }

    /**
     * testHasConstantReturnsTrueForExistentNullConstant
     *
     * @return void
     */
    public function testHasConstantReturnsTrueForExistentNullConstant()
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertTrue($class->hasConstant('BAR'));
    }

    /**
     * testGetDependenciesReturnsEmptyResultByDefault
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetDependenciesReturnsEmptyResultByDefault()
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertCount(0, $class->getDependencies());
    }

    /**
     * testGetDependenciesContainsImplementedInterface
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetDependenciesContainsImplementedInterface()
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertCount(1, $class->getDependencies());
    }

    /**
     * testGetDependenciesContainsImplementedInterfaces
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetDependenciesContainsImplementedInterfaces()
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertCount(3, $class->getDependencies());
    }

    /**
     * testGetDependenciesContainsParentClass
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetDependenciesContainsParentClass()
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertCount(1, $class->getDependencies());
    }

    /**
     * testGetDependenciesContainsParentClassAndInterfaces
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetDependenciesContainsParentClassAndInterfaces()
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertCount(3, $class->getDependencies());
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::getFirstChildOfType()}.
     *
     * @return void
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedFirstMatch()
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
     *
     * @return void
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedNestedMatch()
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
     *
     * @return void
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedNull()
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
     *
     * @return void
     */
    public function testGetFirstChildOfTypeFindsASTNodeInMethodDeclaration()
    {
        $class  = $this->getFirstClassForTestCase();
        $params = $class->getFirstChildOfType(
            'PDepend\\Source\\AST\\ASTFormalParameter'
        );

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFormalParameter', $params);
    }

    /**
     * testGetFirstChildOfTypeFindsASTNodeInMethodDeclaration
     *
     * @return void
     */
    public function testFindChildrenOfTypeFindsASTNodeInMethodDeclarations()
    {
        $class  = $this->getFirstClassForTestCase();
        $params = $class->findChildrenOfType(
            'PDepend\\Source\\AST\\ASTFormalParameter'
        );

        $this->assertCount(4, $params);
    }

    /**
     * testFindChildrenOfTypeFindsASTNodesFromVariousCodeItems
     *
     * @return void
     */
    public function testFindChildrenOfTypeFindsASTNodesFromVariousCodeItems()
    {
        $class  = $this->getFirstClassForTestCase();
        $params = $class->findChildrenOfType(
            'PDepend\\Source\\AST\\ASTVariableDeclarator'
        );

        $this->assertCount(2, $params);
    }

    /**
     * testUnserializedClassStillIsParentOfChildMethods
     *
     * @return void
     */
    public function testUnserializedClassStillIsParentOfChildMethods()
    {
        $orig = $this->getFirstClassForTestCase();
        $copy = unserialize(serialize($orig));

        $this->assertSame($copy, $copy->getMethods()->current()->getParent());
    }

    /**
     * testUnserializedClassAndChildMethodsStillReferenceTheSameFile
     *
     * @return void
     */
    public function testUnserializedClassAndChildMethodsStillReferenceTheSameFile()
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
     *
     * @return void
     */
    public function testUnserializedClassStillReferencesSameParentClass()
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
     *
     * @return void
     */
    public function testUnserializedClassStillReferencesSameParentInterface()
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
     *
     * @return void
     */
    public function testUnserializedClassIsReturnedByMethodAsReturnClass()
    {
        $orig   = $this->getFirstClassForTestCase();
        $method = $orig->getMethods()->current();

        $copy = unserialize(serialize($orig));

        $this->assertSame(
            $method->getReturnClass(),
            $copy
        );
    }

    /**
     * testUnserializedClassStillReferencesSamePackage
     *
     * @return void
     */
    public function testUnserializedClassStillReferencesSamePackage()
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
     *
     * @return void
     */
    public function testUnserializedClassRegistersToPackage()
    {
        $orig = $this->getFirstClassForTestCase();
        $copy = unserialize(serialize($orig));

        $this->assertSame($copy, $orig->getNamespace()->getClasses()->current());
    }

    /**
     * testUnserializedClassNotAddsDublicateClassToPackage
     *
     * @return void
     */
    public function testUnserializedClassNotAddsDublicateClassToPackage()
    {
        $orig = $this->getFirstClassForTestCase();
        $copy = unserialize(serialize($orig));

        $this->assertCount(1, $orig->getNamespace()->getClasses());
    }

    /**
     * Tests the ctor and the {@link \PDepend\Source\AST\ASTClass::getName()}.
     *
     * @return void
     */
    public function testCreateNewClassInstance()
    {
        $class = new ASTClass(__CLASS__);
        $this->assertEquals(__CLASS__, $class->getName());
    }

    /**
     * testIsAbstractReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsAbstractReturnsFalseByDefault()
    {
        $class = new ASTClass(__CLASS__);
        $this->assertFalse($class->isAbstract());
    }

    /**
     * testMarkClassInstanceAsAbstract
     *
     * @return void
     */
    public function testMarkClassInstanceAsAbstract()
    {
        $class = new ASTClass(__CLASS__);
        $class->setModifiers(State::IS_EXPLICIT_ABSTRACT);

        $this->assertTrue($class->isAbstract());
    }

    /**
     * testIsFinalReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsFinalReturnsFalseByDefault()
    {
        $class = new ASTClass(__CLASS__);
        $this->assertFalse($class->isFinal());
    }

    /**
     * testMarkClassInstanceAsFinal
     *
     * @return void
     */
    public function testMarkClassInstanceAsFinal()
    {
        $class = new ASTClass(__CLASS__);
        $class->setModifiers(State::IS_FINAL);

        $this->assertTrue($class->isFinal());
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTClass::setModifiers()}
     * when it is called with an invalid modifier.
     *
     * @return void
     */
    public function testSetModifiersThrowsExpectedExceptionForInvalidModifier()
    {
        $this->expectException(\InvalidArgumentException::class);

        $class = new ASTClass(__CLASS__);
        $class->setModifiers(
            2 |
            State::IS_FINAL
        );
    }

    /**
     * Tests that a new {@link \PDepend\Source\AST\ASTClass} object returns
     * an empty {@link \PDepend\Source\AST\ASTArtifactList} instance for methods.
     *
     * @return void
     */
    public function testGetMethodsNodeIteratorIsEmptyByDefault()
    {
        $class = new ASTClass(__CLASS__);
        $class->setCache(new MemoryCacheDriver());

        $this->assertEquals(0, $class->getMethods()->count());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTClass::addMethod()}
     * method adds a method to the internal list and sets the context class as
     * parent.
     *
     * @return void
     */
    public function testAddMethodStoresNewlyAddedMethodInCollection()
    {
        $class = new ASTClass(__CLASS__);
        $class->setCache(new MemoryCacheDriver());
        $class->addMethod(new ASTMethod(__FUNCTION__));

        $this->assertEquals(1, $class->getMethods()->count());
    }

    /**
     * testAddMethodSetsParentOfNewlyAddedMethod
     *
     * @return void
     */
    public function testAddMethodSetsParentOfNewlyAddedMethod()
    {
        $class = new ASTClass(__CLASS__);
        $class->setCache(new MemoryCacheDriver());

        $method = $class->addMethod(new ASTMethod(__FUNCTION__));

        $this->assertSame($class, $method->getParent());
    }

    /**
     * testGetNamespaceReturnsNullByDefault
     *
     * @return void
     */
    public function testGetNamespaceReturnsNullByDefault()
    {
        $class = new ASTClass(__CLASS__);
        $this->assertNull($class->getNamespace());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTClass::getNamespace()}
     * returns as default value <b>null</b> and that the namespace could be set
     * and unset.
     *
     * @return void
     */
    public function testGetSetNamespace()
    {
        $namespace = new ASTNamespace(__FUNCTION__);
        $class   = new ASTClass(__CLASS__);

        $class->setNamespace($namespace);
        $this->assertSame($namespace, $class->getNamespace());
    }

    /**
     * testUnsetNamespaceResetsNamespaceReference
     *
     * @return void
     */
    public function testUnsetNamespaceResetsNamespaceReference()
    {
        $class = new ASTClass(__CLASS__);

        $class->setNamespace(new ASTNamespace(__FUNCTION__));
        $class->unsetNamespace();

        $this->assertNull($class->getNamespace());
    }

    /**
     * testUnsetNamespaceResetsNamespaceNameProperty
     *
     * @return void
     * @since 0.10.2
     */
    public function testUnsetNamespaceResetsNamespaceNameProperty()
    {
        $class = new ASTClass(__CLASS__);

        $class->setNamespace(new ASTNamespace(__FUNCTION__));
        $class->unsetNamespace();

        $this->assertNull($class->getNamespaceName());
    }

    /**
     * Tests that {@link \PDepend\Source\AST\ASTClass::getInterfaces()}
     * returns the expected result.
     *
     * @return void
     */
    public function testGetInterfaces()
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
     *
     * @return void
     */
    public function testGetInterfacesByInheritance()
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
     *
     * @return void
     */
    public function testGetInterfacesByClassInheritence()
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
     *
     * @return void
     */
    public function testIsSubtypeInInheritanceHierarchy()
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
            'F' => false
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * Checks the {@link \PDepend\Source\AST\ASTClass::isSubtypeOf()} method.
     *
     * @return void
     */
    public function testIsSubtypeInClassInheritanceHierarchy()
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
            'F' => false
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * Checks the {@link \PDepend\Source\AST\ASTClass::isSubtypeOf()} method.
     *
     * @return void
     */
    public function testIsSubtypeInClassAndInterfaceInheritanceHierarchy()
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
            'F' => true
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * testGetPropertiesReturnsExpectedNumberOfProperties
     *
     * @return void
     */
    public function testGetPropertiesReturnsExpectedNumberOfProperties()
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertCount(6, $class->getProperties());
    }

    /**
     * Tests that it is not possible to overwrite previously set class modifiers.
     *
     * @return void
     */
    public function testSetModifiersThrowsExpectedExceptionOnOverwrite()
    {
        $this->expectException(\BadMethodCallException::class);

        $class = new ASTClass(__CLASS__);
        $class->setModifiers(State::IS_FINAL);
        $class->setModifiers(State::IS_FINAL);
    }

    /**
     * testGetModifiersReturnsZeroByDefault
     *
     * @return void
     */
    public function testGetModifiersReturnsZeroByDefault()
    {
        $class = new ASTClass(__CLASS__);
        $this->assertSame(0, $class->getModifiers());
    }

    /**
     * testGetModifiersReturnsInjectedModifierValue
     *
     * @return void
     */
    public function testGetModifiersReturnsInjectedModifierValue()
    {
        $class = new ASTClass(__CLASS__);
        $class->setModifiers(State::IS_FINAL);

        $this->assertSame(State::IS_FINAL, $class->getModifiers());
    }

    /**
     * Tests the visitor accept method.
     *
     * @return void
     */
    public function testVisitorAccept()
    {
        $class   = new ASTClass(__CLASS__);
        $visitor = new StubASTVisitor();

        $class->accept($visitor);
        $this->assertSame($class, $visitor->class);
    }

    /**
     * testGetTokensDelegatesCallToCacheRestore
     *
     * @return void
     */
    public function testGetTokensDelegatesCallToCacheRestore()
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
     *
     * @return void
     */
    public function testSetTokensDelegatesCallToCacheStore()
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
     *
     * @return void
     */
    public function testGetStartLineReturnsZeroByDefault()
    {
        $class = new ASTClass(__CLASS__);
        $this->assertSame(0, $class->getStartLine());
    }

    /**
     * testGetStartLineReturnsStartLineOfFirstToken
     *
     * @return void
     */
    public function testGetStartLineReturnsStartLineOfFirstToken()
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
     *
     * @return void
     */
    public function testGetEndLineReturnsZeroByDefault()
    {
        $class = new ASTClass(__CLASS__);
        $this->assertSame(0, $class->getEndLine());
    }

    /**
     * testGetEndLineReturnsEndLineOfLastToken
     *
     * @return void
     */
    public function testGetEndLineReturnsEndLineOfLastToken()
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
     *
     * @return void
     */
    public function testGetParentClassReferenceReturnsNullByDefault()
    {
        $class = new ASTClass(__CLASS__);
        $this->assertNull($class->getParentClassReference());
    }

    /**
     * testGetParentClassReturnsExpectedClassInstance
     *
     * @return void
     */
    public function testGetParentClassReturnsExpectedClassInstance()
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
     * @return void
     * @covers \PDepend\Source\AST\ASTClassOrInterfaceRecursiveInheritanceException
     */
    public function testGetParentClassThrowsExpectedExceptionWhenBothAreTheSame()
    {
        $this->expectException(\PDepend\Source\AST\ASTClassOrInterfaceRecursiveInheritanceException::class);

        $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getParentClass();
    }

    /**
     * testGetParentClassesReturnsEmptyArrayByDefault
     *
     * @return void
     */
    public function testGetParentClassesReturnsEmptyArrayByDefault()
    {
        $class = new ASTClass(__CLASS__);
        $this->assertSame([], $class->getParentClasses());
    }

    /**
     * testGetParentClassesReturnsExpectedListClasses
     *
     * @return void
     */
    public function testGetParentClassesReturnsExpectedListClasses()
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
                'testGetParentClassesReturnsExpectedListClasses_parentC'
            ],
            $classes
        );
    }

    /**
     * testGetParentReturnsNullWhenParentIsFiltered
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetParentReturnsNullWhenParentIsFiltered()
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
     * @return void
     * @covers \PDepend\Source\AST\ASTClassOrInterfaceRecursiveInheritanceException
     */
    public function testGetParentClassesThrowsExpectedExceptionForRecursiveInheritanceHierarchy()
    {
        $this->expectException(\PDepend\Source\AST\ASTClassOrInterfaceRecursiveInheritanceException::class);

        $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getParentClasses();
    }

    /**
     * testGetInterfaceReferencesReturnsEmptyArrayByDefault
     *
     * @return void
     */
    public function testGetInterfaceReferencesReturnsEmptyArrayByDefault()
    {
        $class = new ASTClass(__CLASS__);
        $this->assertSame([], $class->getInterfaceReferences());
    }

    /**
     * testGetInterfaceReferencesReturnsExpectedNumberOfInterfaces
     *
     * @return void
     */
    public function testGetInterfaceReferencesReturnsExpectedNumberOfInterfaces()
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertCount(3, $class->getInterfaceReferences());
    }

    /**
     * testIsUserDefinedReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsUserDefinedReturnsFalseByDefault()
    {
        $class = new ASTClass(__CLASS__);
        $this->assertFalse($class->isUserDefined());
    }

    /**
     * testIsUserDefinedReturnsTrueAfterSetUserDefinedCall
     *
     * @return void
     */
    public function testIsUserDefinedReturnsTrueAfterSetUserDefinedCall()
    {
        $class = $this->createItem();
        $class->setUserDefined();

        $this->assertTrue($class->isUserDefined());
    }

    /**
     * testIsCachedReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsCachedReturnsFalseByDefault()
    {
        $class = $this->createItem();
        $this->assertFalse($class->isCached());
    }

    /**
     * testIsCachedReturnsFalseWhenObjectGetsSerialized
     *
     * @return void
     */
    public function testIsCachedReturnsFalseWhenObjectGetsSerialized()
    {
        $class = $this->createItem();
        serialize($class);

        $this->assertFalse($class->isCached());
    }

    /**
     * @return void
     */
    public function testGetNamespacedName()
    {
        $class = new ASTClass('MyClass');
        $this->assertSame('MyClass', $class->getNamespacedName());
    }

    /**
     * @return void
     */
    public function testGetNamespacedNameWithNamespaceDeclaration()
    {
        $class = new ASTClass('MyClass');
        $class->setNamespace(new ASTNamespace('My\\Namespace'));

        $this->assertSame('My\\Namespace\\MyClass', $class->getNamespacedName());
    }

    /**
     * @return void
     */
    public function testGetNamespacedNameWithPackageAnnotation()
    {
        $namespace = new ASTNamespace('My\\Namespace');
        $namespace->setPackageAnnotation(true);

        $class = new ASTClass('MyClass');
        $class->setNamespace($namespace);

        $this->assertSame('MyClass', $class->getNamespacedName());
    }

    /**
     * testMagicSleepMethodReturnsExpectedSetOfPropertyNames
     *
     * @return void
     */
    public function testMagicSleepMethodReturnsExpectedSetOfPropertyNames()
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
                'id'
            ],
            $class->__sleep()
        );
    }

    /**
     * testMagicWakeupSetsSourceFileOnChildMethods
     *
     * @return void
     */
    public function testMagicWakeupSetsSourceFileOnChildMethods()
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
     *
     * @return void
     */
    public function testMagicWakeupCallsRegisterClassOnBuilderContext()
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
     * @return \PDepend\Source\AST\AbstractASTArtifact
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
