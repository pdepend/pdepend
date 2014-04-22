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

use PDepend\Source\ASTVisitor\StubASTVisitor;

/**
 * Test case implementation for the \PDepend\Source\AST\ASTMethod class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\AbstractASTCallable
 * @covers \PDepend\Source\AST\ASTMethod
 * @group unittest
 */
class ASTMethodTest extends AbstractASTArtifactTest
{
    /**
     * testIsCachedReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsCachedReturnsFalseByDefault()
    {
        $method = $this->createItem();
        $this->assertFalse($method->isCached());
    }

    /**
     * testIsCachedReturnsFalseWhenObjectGetsSerialized
     *
     * @return void
     */
    public function testIsCachedReturnsFalseWhenObjectGetsSerialized()
    {
        $method = $this->createItem();
        serialize($method);

        $this->assertFalse($method->isCached());
    }

    /**
     * testReturnValueOfMagicSleepContainsContextProperty
     *
     * @return void
     */
    public function testReturnValueOfMagicSleepContainsContextProperty()
    {
        $method = new ASTMethod('method');
        $this->assertEquals(
            array(
                'modifiers',
                'cache',
                'id',
                'name',
                'nodes',
                'startLine',
                'endLine',
                'docComment',
                'returnsReference',
                'returnClassReference',
                'exceptionClassReferences'
            ),
            $method->__sleep()
        );
    }

    /**
     * testParserSetsAbstractFlagOnMethod
     *
     * @return void
     */
    public function testParserNotSetsAbstractFlagOnMethod()
    {
        $method = $this->getFirstMethodInClass();
        $this->assertFalse($method->isAbstract());
    }

    /**
     * testParserSetsAbstractFlagOnMethod
     *
     * @return void
     */
    public function testParserSetsAbstractFlagOnMethod()
    {
        $method = $this->getFirstMethodInClass();
        $this->assertTrue($method->isAbstract());
    }

    /**
     * testGetReturnClassForMethodWithNamespacedRootClass
     *
     * @return void
     */
    public function testGetReturnClassForMethodWithNamespacedRootClass()
    {
        $method = $this->getFirstMethodInClass();
        $this->assertEquals('Foo', $method->getReturnClass()->getName());
    }

    /**
     * testGetReturnClassForMethodWithNamespacedClass
     *
     * @return void
     */
    public function testGetReturnClassForMethodWithNamespacedClass()
    {
        $method = $this->getFirstMethodInClass();
        $this->assertEquals('Baz', $method->getReturnClass()->getName());
    }

    /**
     * testGetReturnClassForMethodWithNamespacedArrayRootClass
     *
     * @return void
     */
    public function testGetReturnClassForMethodWithNamespacedArrayRootClass()
    {
        $method = $this->getFirstMethodInClass();
        $this->assertEquals('Foo', $method->getReturnClass()->getName());
    }

    /**
     * testGetReturnClassForMethodWithNamespacedArrayClass
     *
     * @return void
     */
    public function testGetReturnClassForMethodWithNamespacedArrayClass()
    {
        $method = $this->getFirstMethodInClass();
        $this->assertEquals('Baz', $method->getReturnClass()->getName());
    }

    /**
     * testGetExceptionsForMethodWithNamespacedRootClass
     *
     * @return void
     */
    public function testGetExceptionsForMethodWithNamespacedRootClass()
    {
        $method = $this->getFirstMethodInClass();
        $this->assertEquals(
            'Exception',
            $method->getExceptionClasses()->current()->getName()
        );
    }

    /**
     * testGetExceptionsForMethodWithNamespacedClass
     *
     * @return void
     */
    public function testGetExceptionsForMethodWithNamespacedClass()
    {
        $method = $this->getFirstMethodInClass();
        $this->assertEquals(
            'ErrorException',
            $method->getExceptionClasses()->current()->getName()
        );
    }

    /**
     * testInlineDependencyForMethodWithNamespacedRootClass
     *
     * @return void
     */
    public function testInlineDependencyForMethodWithNamespacedRootClass()
    {
        $method = $this->getFirstMethodInClass();
        $this->assertEquals(
            'ASTBuilder',
            $method->getDependencies()->current()->getName()
        );
    }

    /**
     * testInlineDependencyForMethodWithNamespacedClass
     *
     * @return void
     */
    public function testInlineDependencyForMethodWithNamespacedClass()
    {
        $method = $this->getFirstMethodInClass();
        $this->assertEquals(
            'ASTBuilder',
            $method->getDependencies()->current()->getName()
        );
    }

    /**
     * testReturnsReferenceReturnsExpectedTrue
     *
     * @return void
     */
    public function testReturnsReferenceReturnsExpectedTrue()
    {
        $method = $this->getFirstMethodInClass();
        $this->assertTrue($method->returnsReference());
    }

    /**
     * testReturnsReferenceReturnsExpectedFalse
     *
     * @return void
     */
    public function testReturnsReferenceReturnsExpectedFalse()
    {
        $method = $this->getFirstMethodInClass();
        $this->assertFalse($method->returnsReference());
    }

    /**
     * testGetStaticVariablesReturnsEmptyArrayByDefault
     *
     * @return void
     */
    public function testGetStaticVariablesReturnsEmptyArrayByDefault()
    {
        $method = new ASTMethod('method');
        $this->assertEquals(array(), $method->getStaticVariables());
    }

    /**
     * testGetStaticVariablesReturnsFirstSetOfStaticVariables
     *
     * @return void
     */
    public function testGetStaticVariablesReturnsFirstSetOfStaticVariables()
    {
        $method = $this->getFirstMethodInClass();

        $this->assertEquals(
            array('a' => 42, 'b' => 23),
            $method->getStaticVariables()
        );
    }

    /**
     * testGetStaticVariablesReturnsMergeOfAllStaticVariables
     *
     * @return void
     */
    public function testGetStaticVariablesReturnsMergeOfAllStaticVariables()
    {
        $method = $this->getFirstMethodInClass();

        $this->assertEquals(
            array('a' => 42, 'b' => 23, 'c' => 17),
            $method->getStaticVariables()
        );
    }

    /**
     * testGetSourceFileThrowsExpectedExceptionWhenNoParentWasDefined
     *
     * @return void
     * @covers \PDepend\Source\AST\ASTCompilationUnitNotFoundException
     * @expectedException \PDepend\Source\AST\ASTCompilationUnitNotFoundException
     */
    public function testGetSourceFileThrowsExpectedExceptionWhenNoParentWasDefined()
    {
        $method = new ASTMethod('method');
        $method->getCompilationUnit();
    }

    /**
     * Tests that build interface updates the source file information for null
     * values.
     *
     * @return void
     */
    public function testSetSourceFileInformationForNullValue()
    {
        $file = new ASTCompilationUnit(__FILE__);

        $class = new ASTClass(__CLASS__);
        $class->setCompilationUnit($file);

        $method = new ASTMethod(__FUNCTION__);
        $method->setParent($class);

        $this->assertSame($file, $method->getCompilationUnit());
    }

    /**
     * Tests that the build interface method doesn't update an existing source
     * file info.
     *
     * @return void
     */
    public function testDoesntSetSourceFileInformationForNotNullValue()
    {
        $this->markTestSkipped(
            'This test should be removed, but a default implementation exists.'
        );
    }

    /**
     * testByDefaultGetParentReturnsNull
     *
     * @return void
     */
    public function testByDefaultGetParentReturnsNull()
    {
        $method = new ASTMethod('method');
        $this->assertNull($method->getParent());
    }

    /**
     * testSetParentWithNullResetsPreviousParentToNull
     *
     * @return void
     */
    public function testSetParentWithNullResetsPreviousParentToNull()
    {
        $class  = new ASTClass('clazz');
        $method = new ASTMethod('method');

        $method->setParent($class);
        $method->setParent(null);
        $this->assertNull($method->getParent());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTMethod::getParent()} returns as
     * default value <b>null</b> and that the package could be set and unset.
     *
     * @return void
     */
    public function testGetSetParent()
    {
        $class  = new ASTClass('clazz');
        $method = new ASTMethod('method');

        $method->setParent($class);
        $this->assertSame($class, $method->getParent());
    }

    /**
     * Tests the visitor accept method.
     *
     * @return void
     */
    public function testVisitorAccept()
    {
        $method  = new ASTMethod('method', 0);
        $visitor = new StubASTVisitor();
        $method->accept($visitor);

        $this->assertSame($method, $visitor->method);
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTMethod::setModifiers()} method
     * fails with an exception for an invalid modifier value.
     *
     * @return void
     */
    public function testSetInvalidModifierFail()
    {
        $this->setExpectedException('InvalidArgumentException');

        $method = new ASTMethod('method');
        $method->setModifiers(-1);
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTMethod::setModifiers()} method
     * accepts the defined visibility value.
     *
     * @return void
     */
    public function testSetModifiersAcceptsPublicValue()
    {
        $method = new ASTMethod('method');
        $method->setModifiers(State::IS_PUBLIC);

        $this->assertTrue(
            $method->isPublic() &&
            !$method->isProtected() &&
            !$method->isPrivate()
        );
    }

    /**
     * testGetModifiersReturnsZeroByDefault
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetModifiersReturnsZeroByDefault()
    {
        $method = new ASTMethod('method');
        $this->assertSame(0, $method->getModifiers());
    }

    /**
     * testGetModifiersReturnsPreviousSetValue
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetModifiersReturnsPreviousSetValue()
    {
        $method = new ASTMethod('method');
        $method->setModifiers(State::IS_ABSTRACT);

        $this->assertEquals(
            State::IS_ABSTRACT,
            $method->getModifiers()
        );
    }

    /**
     * testIsStaticDefaultByReturnsFalse
     *
     * @return void
     */
    public function testIsStaticDefaultByReturnsFalse()
    {
        $method = new ASTMethod('method');
        $this->assertFalse($method->isStatic());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTMethod::setModifiers()} method marks
     * a method as static.
     *
     * @return void
     */
    public function testSetModifiersMarksMethodAsStatic()
    {
        $method = new ASTMethod('method');
        $method->setModifiers(
            State::IS_PROTECTED |
            State::IS_STATIC
        );

        $this->assertTrue($method->isStatic());
    }

    /**
     * testIsFinalByDefaultReturnsFalse
     *
     * @return void
     */
    public function testIsFinalByDefaultReturnsFalse()
    {
        $method = new ASTMethod('method');
        $this->assertFalse($method->isFinal());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTMethod::setModifiers()} method marks
     * a method as final.
     *
     * @return void
     */
    public function testSetModifiersMarksMethodAsFinal()
    {
        $method = new ASTMethod('method');
        $method->setModifiers(
            State::IS_PROTECTED |
            State::IS_FINAL
        );

        $this->assertTrue($method->isFinal());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTMethod::setModifiers()} method marks
     * a method as static+final.
     *
     * @return void
     */
    public function testSetModifiersMarksMethodAsStaticFinal()
    {
        $method = new ASTMethod('method');
        $method->setModifiers(
            State::IS_PROTECTED |
            State::IS_STATIC |
            State::IS_FINAL
        );

        $this->assertTrue($method->isFinal() && $method->isStatic());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTMethod::setModifiers()} method
     * accepts the defined visibility value.
     *
     * @return void
     */
    public function testSetModifiersAcceptsProtectedValue()
    {
        $method = new ASTMethod('method');
        $method->setModifiers(State::IS_PROTECTED);

        $this->assertTrue(
            $method->isProtected() &&
            !$method->isPublic() &&
            !$method->isPrivate()
        );
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTMethod::setModifiers()} method
     * accepts the defined visibility value.
     *
     * @return void
     */
    public function testSetModifiersAcceptsPrivateValue()
    {
        $method = new ASTMethod('method');
        $method->setModifiers(State::IS_PRIVATE);

        $this->assertTrue(
            $method->isPrivate() &&
            !$method->isPublic() &&
            !$method->isProtected()
        );
    }

    /**
     * testIsPublicByDefaultReturnsFalse
     *
     * @return void
     */
    public function testIsPublicByDefaultReturnsFalse()
    {
        $method = new ASTMethod('method');
        $this->assertFalse($method->isPublic());
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::getFirstChildOfType()}.
     *
     * @return void
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedFirstMatch()
    {
        $node1 = $this->getMock(
            'PDepend\\Source\\AST\\ASTNode',
            array(),
            array(),
            'Class_' . __FUNCTION__ . '_' . md5(microtime())
        );
        $node1->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $node2 = $this->getMock(
            'PDepend\\Source\\AST\\ASTNode',
            array(),
            array(),
            'Class_' . __FUNCTION__ . '_' . md5(microtime())
        );
        $node2->expects($this->never())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $method = new ASTMethod('Method');
        $method->addChild($node1);
        $method->addChild($node2);

        $child = $method->getFirstChildOfType(get_class($node2));
        $this->assertSame($node2, $child);
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::getFirstChildOfType()}.
     *
     * @return void
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedNestedMatch()
    {
        $node1 = $this->getMock(
            'PDepend\\Source\\AST\\ASTNode',
            array(),
            array(),
            'Class_' . __FUNCTION__ . '_' . md5(microtime())
        );
        $node1->expects($this->never())
            ->method('getFirstChildOfType');

        $node2 = $this->getMock(
            'PDepend\\Source\\AST\\ASTNode',
            array(),
            array(),
            'Class_' . __FUNCTION__ . '_' . md5(microtime())
        );
        $node2->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $node3 = $this->getMock(
            'PDepend\\Source\\AST\\ASTNode',
            array(),
            array(),
            'Class_' . __FUNCTION__ . '_' . md5(microtime())
        );
        $node3->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue($node1));

        $method = new ASTMethod('Method');
        $method->addChild($node2);
        $method->addChild($node3);

        $child = $method->getFirstChildOfType(get_class($node1));
        $this->assertSame($node1, $child);
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::getFirstChildOfType()}.
     *
     * @return void
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedNull()
    {
        $node1 = $this->getMock(
            'PDepend\\Source\\AST\\ASTNode',
            array(),
            array(),
            'Class_' . __FUNCTION__ . '_' . md5(microtime())
        );
        $node1->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $node2 = $this->getMock(
            'PDepend\\Source\\AST\\ASTNode',
            array(),
            array(),
            'Class_' . __FUNCTION__ . '_' . md5(microtime())
        );
        $node2->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $method = new ASTMethod('Method');
        $method->addChild($node1);
        $method->addChild($node2);

        $child = $method->getFirstChildOfType(
            'Class_' . __FUNCTION__ . '_' . md5(microtime())
        );
        $this->assertNull($child);
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::findChildrenOfType()}.
     *
     * @return void
     */
    public function testFindChildrenOfTypeReturnsExpectedResult()
    {
        $node1 = $this->getMock(
            'PDepend\\Source\\AST\\ASTNode',
            array(),
            array(),
            'Class_' . __FUNCTION__ . '_' . md5(microtime())
        );
        $node1->expects($this->once())
            ->method('findChildrenOfType')
            ->will($this->returnValue(array()));

        $node2 = $this->getMock(
            'PDepend\\Source\\AST\\ASTNode',
            array(),
            array(),
            'Class_' . __FUNCTION__ . '_' . md5(microtime())
        );
        $node2->expects($this->once())
            ->method('findChildrenOfType')
            ->will($this->returnValue(array()));

        $method = new ASTMethod('Method');
        $method->addChild($node1);
        $method->addChild($node2);

        $children = $method->findChildrenOfType(get_class($node2));
        $this->assertSame(array($node2), $children);
    }

    /**
     * testUnserializedMethodStillReferencesSameDependency
     *
     * @return void
     */
    public function testUnserializedMethodStillReferencesSameDependency()
    {
        $orig = $this->getFirstMethodInClass();
        $copy = unserialize(serialize($orig));

        $this->assertSame(
            $orig->getDependencies()->current(),
            $copy->getDependencies()->current()
        );
    }

    /**
     * testUnserializedMethodStillReferencesSameReturnClass
     *
     * @return void
     */
    public function testUnserializedMethodStillReferencesSameReturnClass()
    {
        $orig = $this->getFirstMethodInClass();
        $copy = unserialize(serialize($orig));

        $this->assertSame(
            $orig->getReturnClass(),
            $copy->getReturnClass()
        );
    }

    /**
     * testUnserializedMethodStillReferencesSameParameterClass
     *
     * @return void
     */
    public function testUnserializedMethodStillReferencesSameParameterClass()
    {
        $orig = $this->getFirstMethodInClass();
        $copy = unserialize(serialize($orig));

        $this->assertSame(
            $orig->getDependencies()->current(),
            $copy->getDependencies()->current()
        );
    }

    /**
     * testUnserializedMethodStillReferencesSameExceptionClass
     *
     * @return void
     */
    public function testUnserializedMethodStillReferencesSameExceptionClass()
    {
        $orig = $this->getFirstMethodInClass();
        $copy = unserialize(serialize($orig));

        $this->assertSame(
            $orig->getExceptionClasses()->current(),
            $copy->getExceptionClasses()->current()
        );
    }

    /**
     * testUnserializedMethodStillReferencesSameDependencyInterface
     *
     * @return void
     */
    public function testUnserializedMethodStillReferencesSameDependencyInterface()
    {
        $orig = $this->getFirstMethodInClass();
        $copy = unserialize(serialize($orig));

        $this->assertSame(
            $orig->getDependencies()->current(),
            $copy->getDependencies()->current()
        );
    }

    /**
     * Returns the first method defined in a source file associated with the
     * given test case.
     *
     * @return \PDepend\Source\AST\ASTMethod
     */
    protected function getFirstMethodInClass()
    {
        return self::parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current()
            ->getMethods()
            ->current();
    }

    /**
     * Creates an abstract item instance.
     *
     * @return \PDepend\Source\AST\AbstractASTArtifact
     */
    protected function createItem()
    {
        $method = new ASTMethod('method');
        $method->setCompilationUnit(new ASTCompilationUnit(__FILE__));

        return $method;
    }
}
