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
use PDepend\Source\Builder\BuilderContext;
use PDepend\Source\Tokenizer\Token;
use PDepend\Util\Cache\Driver\MemoryCacheDriver;

/**
 * Test case for the code interface class.
 *
 * @covers \PDepend\Source\AST\AbstractASTClassOrInterface
 * @covers \PDepend\Source\AST\AbstractASTType
 * @covers \PDepend\Source\AST\ASTInterface
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTInterfaceTest extends AbstractASTArtifactTestCase
{
    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::getFirstChildOfType()}.
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedFirstMatch(): void
    {
        /** @var class-string */
        $class = 'Mock_' . __FUNCTION__ . '_' . md5(microtime());
        $node1 = $this->getMockBuilder(ASTNode::class)
            ->setMockClassName($class)
            ->getMock();
        $node1->expects(static::once())
            ->method('getFirstChildOfType')
            ->will(static::returnValue(null));

        /** @var class-string */
        $class = 'Mock_' . __FUNCTION__ . '_' . md5(microtime());
        $node2 = $this->getMockBuilder(ASTNode::class)
            ->setMockClassName($class)
            ->getMock();
        $node2->expects(static::never())
            ->method('getFirstChildOfType')
            ->will(static::returnValue(null));

        $interface = $this->createItem();
        $interface->addChild($node1);
        $interface->addChild($node2);

        $child = $interface->getFirstChildOfType($node2::class);
        static::assertSame($node2, $child);
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::getFirstChildOfType()}.
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedNestedMatch(): void
    {
        /** @var class-string */
        $class = 'Mock_' . __FUNCTION__ . '_' . md5(microtime());
        $node1 = $this->getMockBuilder(ASTNode::class)
            ->setMockClassName($class)
            ->getMock();
        $node1->expects(static::never())
            ->method('getFirstChildOfType');

        /** @var class-string */
        $class = 'Mock_' . __FUNCTION__ . '_' . md5(microtime());
        $node2 = $this->getMockBuilder(ASTNode::class)
            ->setMockClassName($class)
            ->getMock();
        $node2->expects(static::once())
            ->method('getFirstChildOfType')
            ->will(static::returnValue(null));

        /** @var class-string */
        $class = 'Mock_' . __FUNCTION__ . '_' . md5(microtime());
        $node3 = $this->getMockBuilder(ASTNode::class)
            ->setMockClassName($class)
            ->getMock();
        $node3->expects(static::once())
            ->method('getFirstChildOfType')
            ->will(static::returnValue($node1));

        $interface = $this->createItem();
        $interface->addChild($node2);
        $interface->addChild($node3);

        $child = $interface->getFirstChildOfType($node1::class);
        static::assertSame($node1, $child);
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::getFirstChildOfType()}.
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedNull(): void
    {
        /** @var class-string */
        $class = 'Mock_' . __FUNCTION__ . '_' . md5(microtime());
        $node1 = $this->getMockBuilder(ASTNode::class)
            ->setMockClassName($class)
            ->getMock();
        $node1->expects(static::once())
            ->method('getFirstChildOfType')
            ->will(static::returnValue(null));

        /** @var class-string */
        $class = 'Mock_' . __FUNCTION__ . '_' . md5(microtime());
        $node2 = $this->getMockBuilder(ASTNode::class)
            ->setMockClassName($class)
            ->getMock();
        $node2->expects(static::once())
            ->method('getFirstChildOfType')
            ->will(static::returnValue(null));

        $interface = $this->createItem();
        $interface->addChild($node1);
        $interface->addChild($node2);

        /** @var class-string<ASTNode> */
        $class = 'Mock_' . __FUNCTION__ . '_' . md5(microtime());
        $child = $interface->getFirstChildOfType($class);
        static::assertNull($child);
    }

    /**
     * Tests the result of the <b>getInterfaces()</b> method.
     */
    public function testGetInterfacesZeroInheritance(): void
    {
        $namespaces = $this->parseCodeResourceForTest();
        $namespace = $namespaces[0];

        $interfaces = $namespace->getInterfaces();

        static::assertCount(0, $interfaces[0]->getInterfaces());
    }

    /**
     * Tests the result of the <b>getInterfaces()</b> method.
     */
    public function testGetInterfacesOneLevelInheritance(): void
    {
        $namespaces = $this->parseCodeResourceForTest();
        $namespace = $namespaces[0];

        $interface = $namespace->getInterfaces()
            ->current();

        static::assertSame(1, $interface->getInterfaces()->count());
    }

    /**
     * Tests the result of the <b>getInterfaces()</b> method.
     */
    public function testGetInterfacesTwoLevelInheritance(): void
    {
        $namespaces = $this->parseCodeResourceForTest();
        $interface = $namespaces[0]->getInterfaces()
            ->current();

        static::assertSame(4, $interface->getInterfaces()->count());
    }

    /**
     * Tests the result of the <b>getInterfaces()</b> method.
     */
    public function testGetInterfacesComplexInheritance(): void
    {
        $namespaces = $this->parseCodeResourceForTest();
        $interface = $namespaces[0]->getInterfaces()
            ->current();

        static::assertSame(5, $interface->getInterfaces()->count());
    }

    /**
     * Tests that {@link \PDepend\Source\AST\ASTInterface::isSubtypeOf()}
     * returns <b>false</b> for an input class.
     */
    public function testIsSubtypeOfReturnsFalseForNonParents(): void
    {
        $namespaces = $this->parseCodeResourceForTest();
        $interfaces = $namespaces[0]->getInterfaces();

        static::assertFalse($interfaces[0]->isSubtypeOf($interfaces[1]));
    }

    /**
     * Checks the {@link \PDepend\Source\AST\ASTInterface::isSubtypeOf()}
     * method.
     */
    public function testIsSubtypeOnInheritanceHierarchy(): void
    {
        $this->doTestIsSubtypeOnInheritanceHierarchy(
            [
                'A' => true,
                'B' => false,
                'C' => false,
                'D' => false,
                'E' => false,
                'F' => true,
            ]
        );
    }

    /**
     * Checks the {@link \PDepend\Source\AST\ASTInterface::isSubtypeOf()} method.
     */
    public function testIsSubtypeOnInheritanceHierarchy1(): void
    {
        $this->doTestIsSubtypeOnInheritanceHierarchy(
            [
                'A' => true,
                'B' => true,
                'C' => true,
                'D' => true,
                'E' => true,
                'F' => true,
            ]
        );
    }

    /**
     * Checks the {@link \PDepend\Source\AST\ASTInterface::isSubtypeOf()} method.
     */
    public function testIsSubtypeOnInheritanceHierarchy2(): void
    {
        $this->doTestIsSubtypeOnInheritanceHierarchy(
            [
                'B' => false,
                'C' => false,
                'A' => true,
                'D' => true,
                'E' => true,
                'F' => false,
            ]
        );
    }

    /**
     * Checks the {@link \PDepend\Source\AST\ASTInterface::isSubtypeOf()} method.
     */
    public function testIsSubtypeOnInheritanceHierarchy3(): void
    {
        $this->doTestIsSubtypeOnInheritanceHierarchy(
            [
                'B' => false,
                'C' => false,
                'D' => false,
                'A' => true,
                'E' => false,
                'F' => false,
            ]
        );
    }

    /**
     * _testIsSubtypeOnInheritanceHierarchy
     *
     * @param array<string, bool> $expected Expected result.
     */
    private function doTestIsSubtypeOnInheritanceHierarchy(array $expected): void
    {
        $namespaces = $this->parseCodeResourceForTest();
        $namespace = $namespaces->current();
        $current = $namespace->getInterfaces()->current();

        $actual = [];
        foreach ($namespace->getInterfaces() as $interface) {
            $actual[$interface->getImage()] = $current->isSubtypeOf($interface);
        }

        ksort($expected);
        ksort($actual);

        static::assertEquals($expected, $actual);
    }

    /**
     * testGetFirstChildOfTypeFindsASTNodeInMethodDeclaration
     */
    public function testGetFirstChildOfTypeFindsASTNodeInMethodDeclaration(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $class = $namespaces->current()
            ->getInterfaces()
            ->current();

        static::assertInstanceOf(
            ASTFormalParameter::class,
            $class->getFirstChildOfType(ASTFormalParameter::class)
        );
    }

    /**
     * testGetFirstChildOfTypeFindsASTNodeInMethodDeclaration
     */
    public function testFindChildrenOfTypeFindsASTNodeInMethodDeclarations(): void
    {
        $class = $this->parseCodeResourceForTest()
            ->current()
            ->getInterfaces()
            ->current();

        $parameters = $class->findChildrenOfType(
            ASTFormalParameter::class
        );
        static::assertCount(4, $parameters);
    }

    /**
     * Tests that the interface implementation overwrites the
     * setParentClassReference() method and throws an exception.
     */
    public function testInterfaceThrowsExpectedExceptionOnSetParentClassReference(): void
    {
        $this->expectException(BadMethodCallException::class);

        $interface = $this->createItem();

        $reference = $this->getMockBuilder(ASTClassReference::class)
            ->disableOriginalConstructor()
            ->getMock();
        $interface->setParentClassReference($reference);
    }

    /**
     * Tests the returned modifiers of an interface.
     */
    public function testInterfaceReturnsExpectedModifiers(): void
    {
        $interface = $this->createItem();
        static::assertSame(
            State::IS_IMPLICIT_ABSTRACT,
            $interface->getModifiers()
        );
    }

    /**
     * testUnserializedInterfaceStillIsParentOfChildMethods
     */
    public function testUnserializedInterfaceStillIsParentOfChildMethods(): void
    {
        $orig = $this->getFirstInterfaceForTestCase();
        $copy = unserialize(serialize($orig));
        static::assertInstanceOf(ASTInterface::class, $copy);

        static::assertSame($copy, $copy->getMethods()->current()->getParent());
    }

    /**
     * testUnserializedInterfaceAndChildMethodsStillReferenceTheSameFile
     */
    public function testUnserializedInterfaceAndChildMethodsStillReferenceTheSameFile(): void
    {
        $orig = $this->getFirstInterfaceForTestCase();
        $copy = unserialize(serialize($orig));
        static::assertInstanceOf(ASTInterface::class, $copy);

        static::assertSame(
            $copy->getCompilationUnit(),
            $copy->getMethods()->current()->getCompilationUnit()
        );
    }

    /**
     * testUnserializedInterfaceStillReferencesSameParentInterface
     */
    public function testUnserializedInterfaceStillReferencesSameParentInterface(): void
    {
        $orig = $this->getFirstInterfaceForTestCase();
        $copy = unserialize(serialize($orig));
        static::assertInstanceOf(ASTInterface::class, $copy);

        static::assertSame(
            $orig->getInterfaces()->current(),
            $copy->getInterfaces()->current()
        );
    }

    /**
     * testUnserializedInterfaceIsReturnedByMethodAsReturnClass
     */
    public function testUnserializedInterfaceIsReturnedByMethodAsReturnClass(): void
    {
        $orig = $this->getFirstInterfaceForTestCase();
        $method = $orig->getMethods()->current();

        $copy = unserialize(serialize($orig));
        static::assertInstanceOf(ASTInterface::class, $copy);

        static::assertSame(
            $method->getReturnClass(),
            $copy
        );
    }

    /**
     * testUnserializedInterfaceStillReferencesSamePackage
     */
    public function testUnserializedInterfaceStillReferencesSamePackage(): void
    {
        $orig = $this->getFirstInterfaceForTestCase();
        $copy = unserialize(serialize($orig));
        static::assertInstanceOf(ASTInterface::class, $copy);

        static::assertSame(
            $orig->getNamespace(),
            $copy->getNamespace()
        );
    }

    /**
     * testUnserializedInterfaceRegistersToPackage
     */
    public function testUnserializedInterfaceRegistersToPackage(): void
    {
        $orig = $this->getFirstInterfaceForTestCase();
        $copy = unserialize(serialize($orig));
        static::assertInstanceOf(ASTInterface::class, $copy);

        static::assertSame($copy, $orig->getNamespace()?->getInterfaces()->current());
    }

    /**
     * testUnserializedInterfaceNotAddsDublicateClassToPackage
     */
    public function testUnserializedInterfaceNotAddsDublicateClassToPackage(): void
    {
        $orig = $this->getFirstInterfaceForTestCase();
        $copy = unserialize(serialize($orig));
        static::assertInstanceOf(ASTInterface::class, $copy);

        static::assertEquals(1, $orig->getNamespace()?->getInterfaces()->count());
    }

    /**
     * testGetTokensDelegatesCallToCacheRestore
     */
    public function testGetTokensDelegatesCallToCacheRestore(): void
    {
        $cache = $this->createCacheFixture();
        $cache->expects(static::once())
            ->method('type')
            ->with(static::equalTo('tokens'))
            ->will(static::returnValue($cache));
        $cache->expects(static::once())
            ->method('restore');

        $interface = $this->createItem();
        $interface->setCache($cache)
            ->getTokens();
    }

    /**
     * testSetTokensDelegatesCallToCacheStore
     */
    public function testSetTokensDelegatesCallToCacheStore(): void
    {
        $tokens = [new Token(1, 'a', 23, 42, 13, 17)];

        $cache = $this->createCacheFixture();
        $cache->expects(static::once())
            ->method('type')
            ->with(static::equalTo('tokens'))
            ->will(static::returnValue($cache));
        $cache->expects(static::once())
            ->method('store')
            ->with(static::isType('string'), static::equalTo($tokens));

        $interface = $this->createItem();
        $interface->setCache($cache)
            ->setTokens($tokens);
    }

    /**
     * testGetStartLineReturnsZeroByDefault
     */
    public function testGetStartLineReturnsZeroByDefault(): void
    {
        $interface = $this->createItem();
        static::assertSame(0, $interface->getStartLine());
    }

    /**
     * testGetStartLineReturnsStartLineOfFirstToken
     */
    public function testGetStartLineReturnsStartLineOfFirstToken(): void
    {
        $cache = $this->createCacheFixture();
        $cache->expects(static::once())
            ->method('type')
            ->will(static::returnValue($cache));

        $interface = $this->createItem();
        $interface->setCache($cache)
            ->setTokens(
                [
                    new Token(1, 'a', 23, 42, 0, 0),
                    new Token(2, 'b', 17, 32, 0, 0),
                ]
            );

        static::assertEquals(23, $interface->getStartLine());
    }

    /**
     * testGetEndLineReturnsZeroByDefault
     */
    public function testGetEndLineReturnsZeroByDefault(): void
    {
        $interface = $this->createItem();
        static::assertSame(0, $interface->getEndLine());
    }

    /**
     * testGetParentClassReferenceReturnsNullByDefault
     */
    public function testGetParentClassReferenceReturnsNullByDefault(): void
    {
        $class = $this->createItem();
        static::assertNull($class->getParentClassReference());
    }

    /**
     * testGetParentClassesReturnsEmptyArrayByDefault
     */
    public function testGetParentClassesReturnsEmptyArrayByDefault(): void
    {
        $interface = $this->createItem();
        static::assertSame([], $interface->getParentClasses());
    }

    /**
     * testGetParentClassesReturnsEmptyArray
     */
    public function testGetParentClassesReturnsEmptyArray(): void
    {
        $interface = $this->parseCodeResourceForTest()
            ->current()
            ->getInterfaces()
            ->current();

        static::assertSame([], $interface->getParentClasses());
    }

    /**
     * testGetInterfaceReferencesReturnsEmptyArrayByDefault
     */
    public function testGetInterfaceReferencesReturnsEmptyArrayByDefault(): void
    {
        $interface = $this->createItem();
        static::assertSame([], $interface->getInterfaceReferences());
    }

    /**
     * testGetInterfaceReferencesReturnsExpectedNumberOfInterfaces
     */
    public function testGetInterfaceReferencesReturnsExpectedNumberOfInterfaces(): void
    {
        $interface = $this->getFirstInterfaceForTestCase();
        static::assertCount(3, $interface->getInterfaceReferences());
    }

    /**
     * testGetAllChildrenReturnsAnEmptyArrayByDefault
     *
     * @since 1.0.0
     */
    public function testGetAllChildrenReturnsAnEmptyArrayByDefault(): void
    {
        $interface = $this->createItem();
        static::assertSame([], $interface->getChildren());
    }

    /**
     * testGetAllChildrenReturnsArrayWithExpectedNumberOfNodes
     *
     * @since 1.0.0
     */
    public function testGetAllChildrenReturnsArrayWithExpectedNumberOfNodes(): void
    {
        $interface = $this->getFirstInterfaceForTestCase();
        static::assertCount(3, $interface->getChildren());
    }

    /**
     * testGetDependenciesReturnsEmptyResultByDefault
     *
     * @since 1.0.0
     */
    public function testGetDependenciesReturnsEmptyResultByDefault(): void
    {
        $interface = $this->getFirstInterfaceForTestCase();
        static::assertCount(0, $interface->getDependencies());
    }

    /**
     * testGetDependenciesContainsExtendedInterface
     *
     * @since 1.0.0
     */
    public function testGetDependenciesContainsExtendedInterface(): void
    {
        $interface = $this->getFirstInterfaceForTestCase();
        static::assertCount(1, $interface->getDependencies());
    }

    /**
     * testGetDependenciesContainsExtendedInterfaces
     *
     * @since 1.0.0
     */
    public function testGetDependenciesContainsExtendedInterfaces(): void
    {
        $interface = $this->getFirstInterfaceForTestCase();
        static::assertCount(3, $interface->getDependencies());
    }

    /**
     * testGetEndLineReturnsEndLineOfLastToken
     */
    public function testGetEndLineReturnsEndLineOfLastToken(): void
    {
        $cache = $this->createCacheFixture();
        $cache->expects(static::once())
            ->method('type')
            ->will(static::returnValue($cache));

        $interface = $this->createItem();
        $interface->setCache($cache)
            ->setTokens(
                [
                    new Token(1, 'a', 23, 42, 0, 0),
                    new Token(2, 'b', 17, 32, 0, 0),
                ]
            );

        static::assertEquals(32, $interface->getEndLine());
    }

    /**
     * testIsAbstractReturnsAlwaysTrue
     */
    public function testIsAbstractReturnsAlwaysTrue(): void
    {
        $interface = $this->createItem();
        static::assertTrue($interface->isAbstract());
    }

    /**
     * testIsUserDefinedReturnsFalseByDefault
     */
    public function testIsUserDefinedReturnsFalseByDefault(): void
    {
        $interface = $this->createItem();
        static::assertFalse($interface->isUserDefined());
    }

    /**
     * testIsUserDefinedReturnsTrueAfterSetUserDefinedCall
     */
    public function testIsUserDefinedReturnsTrueAfterSetUserDefinedCall(): void
    {
        $interface = $this->createItem();
        $interface->setUserDefined();

        static::assertTrue($interface->isUserDefined());
    }

    /**
     * testGetConstantsReturnsExpectedInterfaceConstants
     *
     * @since 1.0.0
     */
    public function testGetConstantsReturnsExpectedInterfaceConstants(): void
    {
        $interface = $this->getFirstInterfaceForTestCase();
        static::assertEquals(['FOO' => 42, 'BAR' => 23], $interface->getConstants());
    }

    /**
     * testIsCachedReturnsFalseByDefault
     */
    public function testIsCachedReturnsFalseByDefault(): void
    {
        $interface = $this->createItem();
        static::assertFalse($interface->isCached());
    }

    /**
     * testIsCachedReturnsFalseWhenObjectGetsSerialized
     */
    public function testIsCachedReturnsFalseWhenObjectGetsSerialized(): void
    {
        $interface = $this->createItem();
        serialize($interface);

        static::assertFalse($interface->isCached());
    }

    public function testGetNamespacedName(): void
    {
        $interface = new ASTInterface('MyInterface');
        static::assertSame('MyInterface', $interface->getNamespacedName());
    }

    public function testGetNamespacedNameWithNamespaceDeclaration(): void
    {
        $interface = new ASTInterface('MyInterface');
        $interface->setNamespace(new ASTNamespace('My\\Namespace'));

        static::assertSame('My\\Namespace\\MyInterface', $interface->getNamespacedName());
    }

    public function testGetNamespacedNameWithPackageAnnotation(): void
    {
        $namespace = new ASTNamespace('My\\Namespace');
        $namespace->setPackageAnnotation(true);

        $interface = new ASTInterface('MyInterface');
        $interface->setNamespace($namespace);

        static::assertSame('MyInterface', $interface->getNamespacedName());
    }

    /**
     * testMagicSleepMethodReturnsExpectedSetOfPropertyNames
     */
    public function testMagicSleepMethodReturnsExpectedSetOfPropertyNames(): void
    {
        $interface = $this->createItem();
        $interface->setNamespace(new ASTNamespace(__FUNCTION__));

        static::assertEquals(
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
            $interface->__sleep()
        );
    }

    /**
     * testMagicWakeupSetsSourceFileOnChildMethods
     */
    public function testMagicWakeupSetsSourceFileOnChildMethods(): void
    {
        $interface = $this->createItem();
        $method = new ASTMethod(__FUNCTION__);
        $interface->addMethod($method);

        $interface->__wakeup();

        static::assertSame($interface->getCompilationUnit(), $method->getCompilationUnit());
    }

    /**
     * testMagicWakeupCallsRegisterInterfaceOnBuilderContext
     */
    public function testMagicWakeupCallsRegisterInterfaceOnBuilderContext(): void
    {
        $interface = $this->createItem();

        $context = $this->getMockBuilder(BuilderContext::class)
            ->getMock();
        $context->expects(static::once())
            ->method('registerInterface')
            ->with(static::isInstanceOf(ASTInterface::class));

        $interface->setContext($context)->__wakeup();
    }

    /**
     * Creates an abstract item instance.
     */
    protected function createItem(): ASTInterface
    {
        $interface = new ASTInterface(__CLASS__);
        $interface->setCompilationUnit(new ASTCompilationUnit(__FILE__));
        $interface->setCache(new MemoryCacheDriver());

        $context = $this->getMockBuilder(BuilderContext::class)
            ->getMock();
        $interface->setContext($context);

        return $interface;
    }
}
