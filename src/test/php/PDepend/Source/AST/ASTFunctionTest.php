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

use PDepend\Source\ASTVisitor\StubASTVisitor;
use PDepend\Source\Builder\BuilderContext;
use PDepend\Source\Tokenizer\Token;

/**
 * Test case implementation for the \PDepend\Source\AST\ASTFunction class.
 *
 * @covers \PDepend\Source\AST\AbstractASTCallable
 * @covers \PDepend\Source\AST\ASTFunction
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTFunctionTest extends AbstractASTArtifactTestCase
{
    /**
     * testReturnsReferenceReturnsExpectedTrue
     */
    public function testReturnsReferenceReturnsExpectedTrue(): void
    {
        $function = $this->getFirstFunctionForTestCaseInternal();
        static::assertTrue($function->returnsReference());
    }

    /**
     * testReturnsReferenceReturnsExpectedFalse
     */
    public function testReturnsReferenceReturnsExpectedFalse(): void
    {
        $function = $this->getFirstFunctionForTestCaseInternal();
        static::assertFalse($function->returnsReference());
    }

    /**
     * testGetStaticVariablesReturnsEmptyArrayByDefault
     */
    public function testGetStaticVariablesReturnsEmptyArrayByDefault(): void
    {
        $function = $this->createItem();
        static::assertEquals([], $function->getStaticVariables());
    }

    /**
     * testGetStaticVariablesReturnsFirstSetOfStaticVariables
     */
    public function testGetStaticVariablesReturnsFirstSetOfStaticVariables(): void
    {
        static::assertEquals(
            ['a' => 42, 'b' => 23],
            $this->getFirstFunctionForTestCaseInternal()->getStaticVariables()
        );
    }

    /**
     * testGetStaticVariablesReturnsMergeOfAllStaticVariables
     */
    public function testGetStaticVariablesReturnsMergeOfAllStaticVariables(): void
    {
        static::assertEquals(
            ['a' => 42, 'b' => 23, 'c' => 17],
            $this->getFirstFunctionForTestCaseInternal()->getStaticVariables()
        );
    }

    /**
     * Tests the ctor and the {@link \PDepend\Source\AST\ASTFunction::getName()} method.
     */
    public function testCreateNewFunctionInstance(): void
    {
        $function = $this->createItem();
        static::assertEquals('createItem', $function->getImage());
    }

    /**
     * testGetStaticVariablesReturnsMergeOfAllStaticVariables
     */
    public function testGetNamespaceReturnsNullByDefault(): void
    {
        $function = $this->createItem();
        static::assertNull($function->getNamespace());
    }

    /**
     * testUnsetNamespaceWithNullWillResetPreviousPackage
     */
    public function testUnsetNamespaceWithNullWillResetPreviousPackage(): void
    {
        $namespace = new ASTNamespace('nspace');
        $function = $this->createItem();

        $function->setNamespace($namespace);
        $function->unsetNamespace();

        static::assertNull($function->getNamespace());
    }

    /**
     * testUnsetNamespaceWithNullWillResetNamespaceNameProperty
     */
    public function testUnsetNamespaceWithNullWillResetNamespaceNameProperty(): void
    {
        $function = $this->createItem();
        $function->setNamespace(new ASTNamespace(__FUNCTION__));
        $function->unsetNamespace();

        static::assertNull($function->getNamespaceName());
    }

    /**
     * testClassReferenceForJavaStyleArrayNotation
     */
    public function testClassReferenceForJavaStyleArrayNotation(): ASTClass
    {
        $function = $this->getFirstFunctionForTestCaseInternal();
        $type = $function->getReturnClass();

        static::assertInstanceOf(ASTClass::class, $type);
        static::assertEquals('Sindelfingen', $type->getImage());

        return $type;
    }

    /**
     * @depends testClassReferenceForJavaStyleArrayNotation
     */
    public function testNamespaceForJavaStyleArrayNotation(AbstractASTClassOrInterface $type): void
    {
        static::assertEquals('Java\\Style', $type->getNamespaceName());
    }

    /**
     * testSetNamespaceNotEstablishesBackReference
     */
    public function testSetNamespaceNotEstablishesBackReference(): void
    {
        $namespace = $this->getMockBuilder(ASTNamespace::class)
            ->setConstructorArgs([__FUNCTION__])
            ->getMock();
        $namespace->expects(static::never())
            ->method('addFunction');

        $function = $this->createItem();
        $function->setNamespace($namespace);
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTFunction::getNamespace()} returns as
     * default value <b>null</b> and that the namespace could be set and unset.
     */
    public function testGetSetNamespace(): void
    {
        $namespace = new ASTNamespace('nspace');
        $function = $this->createItem();

        $function->setNamespace($namespace);
        static::assertSame($namespace, $function->getNamespace());
    }

    /**
     * testGetNamespaceNameReturnsNullByDefault
     */
    public function testGetNamespaceNameReturnsNullByDefault(): void
    {
        static::assertNull($this->createItem()->getNamespaceName());
    }

    /**
     * testGetNamespaceNameReturnsNameOfInjectedPackage
     */
    public function testGetNamespaceNameReturnsNameOfInjectedPackage(): void
    {
        $function = $this->createItem();
        $function->setNamespace(new ASTNamespace(__FUNCTION__));

        static::assertEquals(__FUNCTION__, $function->getNamespaceName());
    }

    /**
     * testIsCachedReturnsFalseByDefault
     */
    public function testIsCachedReturnsFalseByDefault(): void
    {
        $function = $this->createItem();
        static::assertFalse($function->isCached());
    }

    /**
     * testIsCachedReturnsFalseWhenObjectGetsSerialized
     */
    public function testIsCachedReturnsFalseWhenObjectGetsSerialized(): void
    {
        $function = $this->createItem();
        serialize($function);

        static::assertFalse($function->isCached());
    }

    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames(): void
    {
        $function = $this->createItem();
        static::assertEquals(
            [
                'context',
                'namespaceName',
                'cache',
                'id',
                'name',
                'nodes',
                'startLine',
                'endLine',
                'comment',
                'returnsReference',
                'returnClassReference',
                'exceptionClassReferences',
            ],
            $function->__sleep()
        );
    }

    /**
     * testSetTokensDelegatesToCacheStoreMethod
     */
    public function testSetTokensDelegatesToCacheStoreMethod(): void
    {
        $tokens = [new Token(1, '$foo', 3, 3, 0, 0)];

        $cache = $this->createCacheFixture();
        $cache->expects(static::once())
            ->method('type')
            ->will(static::returnValue($cache));
        $cache->expects(static::once())
            ->method('store')
            ->with(static::isType('string'), static::equalTo($tokens));

        $function = $this->createItem();
        $function->setCache($cache)
            ->setTokens($tokens);
    }

    /**
     * testGetTokensDelegatesToCacheRestoreMethod
     */
    public function testGetTokensDelegatesToCacheRestoreMethod(): void
    {
        $cache = $this->createCacheFixture();
        $cache->expects(static::once())
            ->method('type')
            ->will(static::returnValue($cache));
        $cache->expects(static::once())
            ->method('restore')
            ->with(static::isType('string'))
            ->will(static::returnValue([]));

        $function = $this->createItem();
        $function->setCache($cache)
            ->getTokens();
    }

    /**
     * testGetTokensReturnsArrayEvenWhenCacheReturnsNull
     */
    public function testGetTokensReturnsArrayEvenWhenCacheReturnsNull(): void
    {
        $cache = $this->createCacheFixture();
        $cache->expects(static::once())
            ->method('type')
            ->will(static::returnValue($cache));
        $cache->expects(static::once())
            ->method('restore')
            ->with(static::isType('string'))
            ->will(static::returnValue(null));

        $function = $this->createItem();
        $function->setCache($cache);

        static::assertSame([], $function->getTokens());
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTFunction::getFirstChildOfType()}.
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedFirstMatch(): void
    {
        /** @var class-string */
        $class = 'Class_' . __FUNCTION__ . '_' . md5(microtime());
        $node1 = $this->getMockBuilder(AbstractASTNode::class)
            ->setMockClassName($class)
            ->getMock();
        $node1->expects(static::once())
            ->method('getFirstChildOfType')
            ->will(static::returnValue(null));

        /** @var class-string */
        $class = 'Class_' . __FUNCTION__ . '_' . md5(microtime());
        $node2 = $this->getMockBuilder(AbstractASTNode::class)
            ->setMockClassName($class)
            ->getMock();
        $node2->expects(static::never())
            ->method('getFirstChildOfType')
            ->will(static::returnValue(null));

        $function = $this->createItem();
        $function->addChild($node1);
        $function->addChild($node2);

        $child = $function->getFirstChildOfType($node2::class);
        static::assertSame($node2, $child);
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTFunction::getFirstChildOfType()}.
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedNestedMatch(): void
    {
        /** @var class-string */
        $class = 'Class_' . __FUNCTION__ . '_' . md5(microtime());
        $node1 = $this->getMockBuilder(AbstractASTNode::class)
            ->setMockClassName($class)
            ->getMock();
        $node1->expects(static::never())
            ->method('getFirstChildOfType');

        /** @var class-string */
        $class = 'Class_' . __FUNCTION__ . '_' . md5(microtime());
        $node2 = $this->getMockBuilder(AbstractASTNode::class)
            ->setMockClassName($class)
            ->getMock();
        $node2->expects(static::once())
            ->method('getFirstChildOfType')
            ->will(static::returnValue(null));

        /** @var class-string */
        $class = 'Class_' . __FUNCTION__ . '_' . md5(microtime());
        $node3 = $this->getMockBuilder(AbstractASTNode::class)
            ->setMockClassName($class)
            ->getMock();
        $node3->expects(static::once())
            ->method('getFirstChildOfType')
            ->will(static::returnValue($node1));

        $function = $this->createItem();
        $function->addChild($node2);
        $function->addChild($node3);

        $child = $function->getFirstChildOfType($node1::class);
        static::assertSame($node1, $child);
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTFunction::getFirstChildOfType()}.
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedNull(): void
    {
        /** @var class-string */
        $class = 'Class_' . __FUNCTION__ . '_' . md5(microtime());
        $node1 = $this->getMockBuilder(AbstractASTNode::class)
            ->setMockClassName($class)
            ->getMock();
        $node1->expects(static::once())
            ->method('getFirstChildOfType')
            ->will(static::returnValue(null));

        /** @var class-string */
        $class = 'Class_' . __FUNCTION__ . '_' . md5(microtime());
        $node2 = $this->getMockBuilder(AbstractASTNode::class)
            ->setMockClassName($class)
            ->getMock();
        $node2->expects(static::once())
            ->method('getFirstChildOfType')
            ->will(static::returnValue(null));

        $function = $this->createItem();
        $function->addChild($node1);
        $function->addChild($node2);

        /** @var class-string<ASTNode> */
        $class = 'PDepend\\Class' . md5(microtime());
        $child = $function->getFirstChildOfType($class);
        static::assertNull($child);
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTFunction::findChildrenOfType()}.
     */
    public function testFindChildrenOfTypeReturnsExpectedResult(): void
    {
        /** @var class-string */
        $class = 'Class_' . __FUNCTION__ . '_' . md5(microtime());
        $node1 = $this->getMockBuilder(AbstractASTNode::class)
            ->setMockClassName($class)
            ->getMock();
        $node1->expects(static::once())
            ->method('findChildrenOfType')
            ->will(static::returnValue([]));

        /** @var class-string */
        $class = 'Class_' . __FUNCTION__ . '_' . md5(microtime());
        $node2 = $this->getMockBuilder(AbstractASTNode::class)
            ->setMockClassName($class)
            ->getMock();
        $node2->expects(static::once())
            ->method('findChildrenOfType')
            ->will(static::returnValue([]));

        $function = $this->createItem();
        $function->addChild($node1);
        $function->addChild($node2);

        $children = $function->findChildrenOfType($node2::class);
        static::assertSame([$node2], $children);
    }

    /**
     * testUnserializedFunctionStillReferencesSameDependency
     */
    public function testUnserializedFunctionStillReferencesSameDependency(): void
    {
        $orig = $this->getFirstFunctionForTestCaseInternal();
        $copy = unserialize(serialize($orig));
        static::assertInstanceOf(ASTFunction::class, $copy);

        static::assertSame(
            $orig->getDependencies()->current(),
            $copy->getDependencies()->current()
        );
    }

    /**
     * testUnserializedFunctionStillReferencesSameReturnClass
     */
    public function testUnserializedFunctionStillReferencesSameReturnClass(): void
    {
        $orig = $this->getFirstFunctionForTestCaseInternal();
        $copy = unserialize(serialize($orig));
        static::assertInstanceOf(ASTFunction::class, $copy);

        static::assertSame(
            $orig->getReturnClass(),
            $copy->getReturnClass()
        );
    }

    /**
     * testUnserializedFunctionStillReferencesSameParameterClass
     */
    public function testUnserializedFunctionStillReferencesSameParameterClass(): void
    {
        $orig = $this->getFirstFunctionForTestCaseInternal();
        $copy = unserialize(serialize($orig));
        static::assertInstanceOf(ASTFunction::class, $copy);

        static::assertSame(
            $orig->getDependencies()->current(),
            $copy->getDependencies()->current()
        );
    }

    /**
     * testUnserializedFunctionStillReferencesSameExceptionClass
     */
    public function testUnserializedFunctionStillReferencesSameExceptionClass(): void
    {
        $orig = $this->getFirstFunctionForTestCaseInternal();
        $copy = unserialize(serialize($orig));
        static::assertInstanceOf(ASTFunction::class, $copy);

        static::assertSame(
            $orig->getExceptionClasses()->current(),
            $copy->getExceptionClasses()->current()
        );
    }

    /**
     * testUnserializedFunctionStillReferencesSameDependencyInterface
     */
    public function testUnserializedFunctionStillReferencesSameDependencyInterface(): void
    {
        $orig = $this->getFirstFunctionForTestCaseInternal();
        $copy = unserialize(serialize($orig));
        static::assertInstanceOf(ASTFunction::class, $copy);

        static::assertSame(
            $orig->getDependencies()->current(),
            $copy->getDependencies()->current()
        );
    }

    /**
     * testUnserializedFunctionStillReferencesSamePackage
     */
    public function testUnserializedFunctionStillReferencesSamePackage(): void
    {
        $orig = $this->getFirstFunctionForTestCaseInternal();
        $copy = unserialize(serialize($orig));
        static::assertInstanceOf(ASTFunction::class, $copy);

        static::assertSame($orig->getNamespace(), $copy->getNamespace());
    }

    /**
     * testUnserializedFunctionIsInSameNamespace
     */
    public function testUnserializedFunctionIsInSameNamespace(): void
    {
        $orig = $this->getFirstFunctionForTestCaseInternal();
        $copy = unserialize(serialize($orig));
        static::assertInstanceOf(ASTFunction::class, $copy);

        static::assertEquals(
            'Baz',
            $copy->getNamespace()?->getClasses()->current()->getImage()
        );
    }

    /**
     * testUnserializedFunctionNotAddsDublicateToPackage
     */
    public function testUnserializedFunctionNotAddsDublicateToPackage(): void
    {
        $orig = $this->getFirstFunctionForTestCaseInternal();
        $copy = unserialize(serialize($orig));
        static::assertInstanceOf(ASTFunction::class, $copy);

        static::assertCount(1, $copy->getNamespace()?->getFunctions() ?? []);
    }

    /**
     * testUnserializedFunctionIsChildOfParentPackage
     */
    public function testUnserializedFunctionIsChildOfParentPackage(): void
    {
        $orig = $this->getFirstFunctionForTestCaseInternal();
        $copy = unserialize(serialize($orig));
        static::assertInstanceOf(ASTFunction::class, $copy);

        static::assertSame($copy, $orig->getNamespace()?->getFunctions()->current());
    }

    /**
     * Tests the visitor accept method.
     */
    public function testVisitorAccept(): void
    {
        $function = $this->createItem();
        $visitor = new StubASTVisitor();

        $visitor->dispatch($function);
        static::assertSame($function, $visitor->function);
    }

    /**
     * This method will return the first function instance within the source
     * file of the calling test case.
     */
    private function getFirstFunctionForTestCaseInternal(): ASTFunction
    {
        return $this->parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current();
    }

    /**
     * Creates an abstract item instance.
     */
    protected function createItem(): ASTFunction
    {
        $function = new ASTFunction(__FUNCTION__);
        $function->setCompilationUnit(new ASTCompilationUnit(__FILE__));

        $context = $this->getMockBuilder(BuilderContext::class)
            ->getMock();
        $function->setContext($context);

        return $function;
    }
}
