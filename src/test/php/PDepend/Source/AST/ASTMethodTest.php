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

use InvalidArgumentException;
use PDepend\Source\ASTVisitor\StubASTVisitor;

/**
 * Test case implementation for the \PDepend\Source\AST\ASTMethod class.
 *
 * @covers \PDepend\Source\AST\AbstractASTCallable
 * @covers \PDepend\Source\AST\ASTMethod
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTMethodTest extends AbstractASTArtifactTestCase
{
    /**
     * testIsCachedReturnsFalseByDefault
     */
    public function testIsCachedReturnsFalseByDefault(): void
    {
        $method = $this->createItem();
        static::assertInstanceOf(ASTMethod::class, $method);

        static::assertFalse($method->isCached());
    }

    /**
     * testIsCachedReturnsFalseWhenObjectGetsSerialized
     */
    public function testIsCachedReturnsFalseWhenObjectGetsSerialized(): void
    {
        $method = $this->createItem();
        serialize($method);
        static::assertInstanceOf(ASTMethod::class, $method);

        static::assertFalse($method->isCached());
    }

    /**
     * testReturnValueOfMagicSleepContainsContextProperty
     */
    public function testReturnValueOfMagicSleepContainsContextProperty(): void
    {
        $method = new ASTMethod('method');
        static::assertEquals(
            [
                'modifiers',
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
            $method->__sleep()
        );
    }

    /**
     * testParserSetsAbstractFlagOnMethod
     */
    public function testParserNotSetsAbstractFlagOnMethod(): void
    {
        $method = $this->getFirstMethodInClass();
        static::assertFalse($method->isAbstract());
    }

    /**
     * testParserSetsAbstractFlagOnMethod
     */
    public function testParserSetsAbstractFlagOnMethod(): void
    {
        $method = $this->getFirstMethodInClass();
        static::assertTrue($method->isAbstract());
    }

    /**
     * testGetReturnClassForMethodWithNamespacedRootClass
     */
    public function testGetReturnClassForMethodWithNamespacedRootClass(): void
    {
        $method = $this->getFirstMethodInClass();
        static::assertEquals('Foo', $method->getReturnClass()?->getImage());
    }

    /**
     * testGetReturnClassForMethodWithNamespacedClass
     */
    public function testGetReturnClassForMethodWithNamespacedClass(): void
    {
        $method = $this->getFirstMethodInClass();
        static::assertEquals('Baz', $method->getReturnClass()?->getImage());
    }

    /**
     * testGetReturnClassForMethodWithNamespacedArrayRootClass
     */
    public function testGetReturnClassForMethodWithNamespacedArrayRootClass(): void
    {
        $method = $this->getFirstMethodInClass();
        static::assertEquals('Foo', $method->getReturnClass()?->getImage());
    }

    /**
     * testGetReturnClassForMethodWithNamespacedArrayClass
     */
    public function testGetReturnClassForMethodWithNamespacedArrayClass(): void
    {
        $method = $this->getFirstMethodInClass();
        static::assertEquals('Baz', $method->getReturnClass()?->getImage());
    }

    /**
     * testGetExceptionsForMethodWithNamespacedRootClass
     */
    public function testGetExceptionsForMethodWithNamespacedRootClass(): void
    {
        $method = $this->getFirstMethodInClass();
        static::assertEquals(
            'Exception',
            $method->getExceptionClasses()->current()->getImage()
        );
    }

    /**
     * testGetExceptionsForMethodWithNamespacedClass
     */
    public function testGetExceptionsForMethodWithNamespacedClass(): void
    {
        $method = $this->getFirstMethodInClass();
        static::assertEquals(
            'ErrorException',
            $method->getExceptionClasses()->current()->getImage()
        );
    }

    /**
     * testInlineDependencyForMethodWithNamespacedRootClass
     */
    public function testInlineDependencyForMethodWithNamespacedRootClass(): void
    {
        $method = $this->getFirstMethodInClass();
        static::assertEquals(
            'ASTBuilder',
            $method->getDependencies()->current()->getImage()
        );
    }

    /**
     * testInlineDependencyForMethodWithNamespacedClass
     */
    public function testInlineDependencyForMethodWithNamespacedClass(): void
    {
        $method = $this->getFirstMethodInClass();
        static::assertEquals(
            'ASTBuilder',
            $method->getDependencies()->current()->getImage()
        );
    }

    /**
     * testReturnsReferenceReturnsExpectedTrue
     */
    public function testReturnsReferenceReturnsExpectedTrue(): void
    {
        $method = $this->getFirstMethodInClass();
        static::assertTrue($method->returnsReference());
    }

    /**
     * testReturnsReferenceReturnsExpectedFalse
     */
    public function testReturnsReferenceReturnsExpectedFalse(): void
    {
        $method = $this->getFirstMethodInClass();
        static::assertFalse($method->returnsReference());
    }

    /**
     * testGetStaticVariablesReturnsEmptyArrayByDefault
     */
    public function testGetStaticVariablesReturnsEmptyArrayByDefault(): void
    {
        $method = new ASTMethod('method');
        static::assertEquals([], $method->getStaticVariables());
    }

    /**
     * testGetStaticVariablesReturnsFirstSetOfStaticVariables
     */
    public function testGetStaticVariablesReturnsFirstSetOfStaticVariables(): void
    {
        $method = $this->getFirstMethodInClass();

        static::assertEquals(
            ['a' => 42, 'b' => 23],
            $method->getStaticVariables()
        );
    }

    /**
     * testGetStaticVariablesReturnsMergeOfAllStaticVariables
     */
    public function testGetStaticVariablesReturnsMergeOfAllStaticVariables(): void
    {
        $method = $this->getFirstMethodInClass();

        static::assertEquals(
            ['a' => 42, 'b' => 23, 'c' => 17],
            $method->getStaticVariables()
        );
    }

    /**
     * testGetSourceFileThrowsExpectedExceptionWhenNoParentWasDefined
     *
     * @covers \PDepend\Source\AST\ASTCompilationUnitNotFoundException
     */
    public function testGetSourceFileThrowsExpectedExceptionWhenNoParentWasDefined(): void
    {
        $this->expectException(ASTCompilationUnitNotFoundException::class);

        $method = new ASTMethod('method');
        $method->getCompilationUnit();
    }

    /**
     * Tests that build interface updates the source file information for null
     * values.
     */
    public function testSetSourceFileInformationForNullValue(): void
    {
        $file = new ASTCompilationUnit(__FILE__);

        $class = new ASTClass(__CLASS__);
        $class->setCompilationUnit($file);

        $method = new ASTMethod(__FUNCTION__);
        $method->setParent($class);

        static::assertSame($file, $method->getCompilationUnit());
    }

    /**
     * testByDefaultGetParentReturnsNull
     */
    public function testByDefaultGetParentReturnsNull(): void
    {
        $method = new ASTMethod('method');
        static::assertNull($method->getParent());
    }

    /**
     * testSetParentWithNullResetsPreviousParentToNull
     */
    public function testSetParentWithNullResetsPreviousParentToNull(): void
    {
        $class = new ASTClass('clazz');
        $method = new ASTMethod('method');

        $method->setParent($class);
        $method->setParent(null);
        static::assertNull($method->getParent());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTMethod::getParent()} returns as
     * default value <b>null</b> and that the package could be set and unset.
     */
    public function testGetSetParent(): void
    {
        $class = new ASTClass('clazz');
        $method = new ASTMethod('method');

        $method->setParent($class);
        static::assertSame($class, $method->getParent());
    }

    /**
     * Tests the visitor accept method.
     */
    public function testVisitorAccept(): void
    {
        $method = new ASTMethod('method');
        $visitor = new StubASTVisitor();
        $visitor->dispatch($method);

        static::assertSame($method, $visitor->method);
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTMethod::setModifiers()} method
     * fails with an exception for an invalid modifier value.
     */
    public function testSetInvalidModifierFail(): void
    {
        $this->expectException('InvalidArgumentException');

        $method = new ASTMethod('method');
        $method->setModifiers(-1);
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTMethod::setModifiers()} method
     * accepts the defined visibility value.
     */
    public function testSetModifiersAcceptsPublicValue(): void
    {
        $method = new ASTMethod('method');
        $method->setModifiers(State::IS_PUBLIC);

        static::assertTrue(
            $method->isPublic() &&
            !$method->isProtected() &&
            !$method->isPrivate()
        );
    }

    /**
     * testGetModifiersReturnsZeroByDefault
     *
     * @since 1.0.0
     */
    public function testGetModifiersReturnsZeroByDefault(): void
    {
        $method = new ASTMethod('method');
        static::assertSame(0, $method->getModifiers());
    }

    /**
     * testGetModifiersReturnsPreviousSetValue
     *
     * @since 1.0.0
     */
    public function testGetModifiersReturnsPreviousSetValue(): void
    {
        $method = new ASTMethod('method');
        $method->setModifiers(State::IS_ABSTRACT);

        static::assertEquals(
            State::IS_ABSTRACT,
            $method->getModifiers()
        );
    }

    /**
     * testIsStaticDefaultByReturnsFalse
     */
    public function testIsStaticDefaultByReturnsFalse(): void
    {
        $method = new ASTMethod('method');
        static::assertFalse($method->isStatic());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTMethod::setModifiers()} method marks
     * a method as static.
     */
    public function testSetModifiersMarksMethodAsStatic(): void
    {
        $method = new ASTMethod('method');
        $method->setModifiers(
            State::IS_PROTECTED |
            State::IS_STATIC
        );

        static::assertTrue($method->isStatic());
    }

    /**
     * testIsFinalByDefaultReturnsFalse
     */
    public function testIsFinalByDefaultReturnsFalse(): void
    {
        $method = new ASTMethod('method');
        static::assertFalse($method->isFinal());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTMethod::setModifiers()} method marks
     * a method as final.
     */
    public function testSetModifiersMarksMethodAsFinal(): void
    {
        $method = new ASTMethod('method');
        $method->setModifiers(
            State::IS_PROTECTED |
            State::IS_FINAL
        );

        static::assertTrue($method->isFinal());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTMethod::setModifiers()} method marks
     * a method as static+final.
     */
    public function testSetModifiersMarksMethodAsStaticFinal(): void
    {
        $method = new ASTMethod('method');
        $method->setModifiers(
            State::IS_PROTECTED |
            State::IS_STATIC |
            State::IS_FINAL
        );

        static::assertTrue($method->isFinal() && $method->isStatic());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTMethod::setModifiers()} method
     * accepts the defined visibility value.
     */
    public function testSetModifiersAcceptsProtectedValue(): void
    {
        $method = new ASTMethod('method');
        $method->setModifiers(State::IS_PROTECTED);

        static::assertTrue(
            $method->isProtected() &&
            !$method->isPublic() &&
            !$method->isPrivate()
        );
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTMethod::setModifiers()} method
     * accepts the defined visibility value.
     */
    public function testSetModifiersAcceptsPrivateValue(): void
    {
        $method = new ASTMethod('method');
        $method->setModifiers(State::IS_PRIVATE);

        static::assertTrue(
            $method->isPrivate() &&
            !$method->isPublic() &&
            !$method->isProtected()
        );
    }

    /**
     * testIsPublicByDefaultReturnsFalse
     */
    public function testIsPublicByDefaultReturnsFalse(): void
    {
        $method = new ASTMethod('method');
        static::assertFalse($method->isPublic());
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::getFirstChildOfType()}.
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

        $method = new ASTMethod('Method');
        $method->addChild($node1);
        $method->addChild($node2);

        $child = $method->getFirstChildOfType($node2::class);
        static::assertSame($node2, $child);
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::getFirstChildOfType()}.
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

        $method = new ASTMethod('Method');
        $method->addChild($node2);
        $method->addChild($node3);

        $child = $method->getFirstChildOfType($node1::class);
        static::assertSame($node1, $child);
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::getFirstChildOfType()}.
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

        $method = new ASTMethod('Method');
        $method->addChild($node1);
        $method->addChild($node2);

        /** @var class-string<ASTNode> */
        $class = 'Class_' . __FUNCTION__ . '_' . md5(microtime());
        $child = $method->getFirstChildOfType($class);
        static::assertNull($child);
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::findChildrenOfType()}.
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

        $method = new ASTMethod('Method');
        $method->addChild($node1);
        $method->addChild($node2);

        $children = $method->findChildrenOfType($node2::class);
        static::assertSame([$node2], $children);
    }

    /**
     * testUnserializedMethodStillReferencesSameDependency
     */
    public function testUnserializedMethodStillReferencesSameDependency(): void
    {
        $orig = $this->getFirstMethodInClass();
        $copy = unserialize(serialize($orig));
        static::assertInstanceOf(ASTMethod::class, $copy);

        static::assertSame(
            $orig->getDependencies()->current(),
            $copy->getDependencies()->current()
        );
    }

    /**
     * testUnserializedMethodStillReferencesSameReturnClass
     */
    public function testUnserializedMethodStillReferencesSameReturnClass(): void
    {
        $orig = $this->getFirstMethodInClass();
        $copy = unserialize(serialize($orig));
        static::assertInstanceOf(ASTMethod::class, $copy);

        static::assertSame(
            $orig->getReturnClass(),
            $copy->getReturnClass()
        );
    }

    /**
     * testUnserializedMethodStillReferencesSameParameterClass
     */
    public function testUnserializedMethodStillReferencesSameParameterClass(): void
    {
        $orig = $this->getFirstMethodInClass();
        $copy = unserialize(serialize($orig));
        static::assertInstanceOf(ASTMethod::class, $copy);

        static::assertSame(
            $orig->getDependencies()->current(),
            $copy->getDependencies()->current()
        );
    }

    /**
     * testUnserializedMethodStillReferencesSameExceptionClass
     */
    public function testUnserializedMethodStillReferencesSameExceptionClass(): void
    {
        $orig = $this->getFirstMethodInClass();
        $copy = unserialize(serialize($orig));
        static::assertInstanceOf(ASTMethod::class, $copy);

        static::assertSame(
            $orig->getExceptionClasses()->current(),
            $copy->getExceptionClasses()->current()
        );
    }

    /**
     * testUnserializedMethodStillReferencesSameDependencyInterface
     */
    public function testUnserializedMethodStillReferencesSameDependencyInterface(): void
    {
        $orig = $this->getFirstMethodInClass();
        $copy = unserialize(serialize($orig));
        static::assertInstanceOf(ASTMethod::class, $copy);

        static::assertSame(
            $orig->getDependencies()->current(),
            $copy->getDependencies()->current()
        );
    }

    /**
     * Returns the first method defined in a source file associated with the
     * given test case.
     */
    protected function getFirstMethodInClass(): ASTMethod
    {
        return $this->parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current()
            ->getMethods()
            ->current();
    }

    /**
     * Creates an abstract item instance.
     */
    protected function createItem(): AbstractASTArtifact
    {
        $method = new ASTMethod('method');
        $method->setCompilationUnit(new ASTCompilationUnit(__FILE__));

        return $method;
    }

    public function testSetTokensWithEmptyArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('An AST node should contain at least one token');

        $method = new ASTMethod('FooBar');
        $method->setTokens([]);
    }
}
