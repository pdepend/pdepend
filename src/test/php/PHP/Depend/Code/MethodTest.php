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

require_once dirname(__FILE__) . '/AbstractItemTest.php';
require_once dirname(__FILE__) . '/../Visitor/TestNodeVisitor.php';

/**
 * Test case implementation for the PHP_Depend_Code_Method class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 *
 * @covers PHP_Depend_Parser
 * @covers PHP_Depend_Code_Method
 * @covers PHP_Depend_Code_AbstractCallable
  * @group pdepend
  * @group pdepend::code
  * @group unittest
 */
class PHP_Depend_Code_MethodTest extends PHP_Depend_Code_AbstractItemTest
{
    /**
     * testIsCachedReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsCachedReturnsFalseByDefault()
    {
        $method = $this->createItem();
        self::assertFalse($method->isCached());
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

        self::assertFalse($method->isCached());
    }

    /**
     * testReturnValueOfMagicSleepContainsContextProperty
     *
     * @return void
     */
    public function testReturnValueOfMagicSleepContainsContextProperty()
    {
        $method = new PHP_Depend_Code_Method('method');
        self::assertEquals(
            array(
                'modifiers',
                'cache',
                'uuid',
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
        self::assertFalse($method->isAbstract());
    }

    /**
     * testParserSetsAbstractFlagOnMethod
     *
     * @return void
     */
    public function testParserSetsAbstractFlagOnMethod()
    {
        $method = $this->getFirstMethodInClass();
        self::assertTrue($method->isAbstract());
    }

    /**
     * testGetReturnClassForMethodWithNamespacedRootClass
     *
     * @return void
     */
    public function testGetReturnClassForMethodWithNamespacedRootClass()
    {
        $method = $this->getFirstMethodInClass();
        self::assertEquals('Foo', $method->getReturnClass()->getName());
    }

    /**
     * testGetReturnClassForMethodWithNamespacedClass
     *
     * @return void
     */
    public function testGetReturnClassForMethodWithNamespacedClass()
    {
        $method = $this->getFirstMethodInClass();
        self::assertEquals('Baz', $method->getReturnClass()->getName());
    }

    /**
     * testGetReturnClassForMethodWithNamespacedArrayRootClass
     *
     * @return void
     */
    public function testGetReturnClassForMethodWithNamespacedArrayRootClass()
    {
        $method = $this->getFirstMethodInClass();
        self::assertEquals('Foo', $method->getReturnClass()->getName());
    }

    /**
     * testGetReturnClassForMethodWithNamespacedArrayClass
     *
     * @return void
     */
    public function testGetReturnClassForMethodWithNamespacedArrayClass()
    {
        $method = $this->getFirstMethodInClass();
        self::assertEquals('Baz', $method->getReturnClass()->getName());
    }

    /**
     * testGetExceptionsForMethodWithNamespacedRootClass
     *
     * @return void
     */
    public function testGetExceptionsForMethodWithNamespacedRootClass()
    {
        $method = $this->getFirstMethodInClass();
        self::assertEquals(
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
        self::assertEquals(
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
        self::assertEquals(
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
        self::assertEquals(
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
        self::assertTrue($method->returnsReference());
    }

    /**
     * testReturnsReferenceReturnsExpectedFalse
     *
     * @return void
     */
    public function testReturnsReferenceReturnsExpectedFalse()
    {
        $method = $this->getFirstMethodInClass();
        self::assertFalse($method->returnsReference());
    }

    /**
     * testGetStaticVariablesReturnsEmptyArrayByDefault
     *
     * @return void
     */
    public function testGetStaticVariablesReturnsEmptyArrayByDefault()
    {
        $method = new PHP_Depend_Code_Method('method');
        self::assertEquals(array(), $method->getStaticVariables());
    }

    /**
     * testGetStaticVariablesReturnsFirstSetOfStaticVariables
     *
     * @return void
     */
    public function testGetStaticVariablesReturnsFirstSetOfStaticVariables()
    {
        $method = $this->getFirstMethodInClass();

        self::assertEquals(
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

        self::assertEquals(
            array('a' => 42, 'b' => 23, 'c' => 17),
            $method->getStaticVariables()
        );
    }

    /**
     * testGetSourceFileThrowsExpectedExceptionWhenNoParentWasDefined
     *
     * @return void
     * @covers PHP_Depend_Code_Exceptions_AbstractException
     * @covers PHP_Depend_Code_Exceptions_SourceNotFoundException
     * @expectedException PHP_Depend_Code_Exceptions_SourceNotFoundException
     */
    public function testGetSourceFileThrowsExpectedExceptionWhenNoParentWasDefined()
    {
        $method = new PHP_Depend_Code_Method('method');
        $method->getSourceFile();
    }

    /**
     * Tests that build interface updates the source file information for null
     * values.
     *
     * @return void
     */
    public function testSetSourceFileInformationForNullValue()
    {
        $file = new PHP_Depend_Code_File(__FILE__);

        $class = new PHP_Depend_Code_Class(__CLASS__);
        $class->setSourceFile($file);

        $method = new PHP_Depend_Code_Method(__FUNCTION__);
        $method->setParent($class);

        self::assertSame($file, $method->getSourceFile());
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
        $method = new PHP_Depend_Code_Method('method');
        self::assertNull($method->getParent());
    }

    /**
     * testSetParentWithNullResetsPreviousParentToNull
     *
     * @return void
     */
    public function testSetParentWithNullResetsPreviousParentToNull()
    {
        $class  = new PHP_Depend_Code_Class('clazz', 0, 'clazz.php');
        $method = new PHP_Depend_Code_Method('method');

        $method->setParent($class);
        $method->setParent(null);
        self::assertNull($method->getParent());
    }

    /**
     * Tests that the {@link PHP_Depend_Code_Method::getParent()} returns as
     * default value <b>null</b> and that the package could be set and unset.
     *
     * @return void
     */
    public function testGetSetParent()
    {
        $class  = new PHP_Depend_Code_Class('clazz', 0, 'clazz.php');
        $method = new PHP_Depend_Code_Method('method');

        $method->setParent($class);
        self::assertSame($class, $method->getParent());
    }

    /**
     * Tests the visitor accept method.
     *
     * @return void
     */
    public function testVisitorAccept()
    {
        $method  = new PHP_Depend_Code_Method('method', 0);
        $visitor = new PHP_Depend_Visitor_TestNodeVisitor();
        $method->accept($visitor);

        self::assertSame($method, $visitor->method);
    }

    /**
     * Tests that the {@link PHP_Depend_Code_Method::setModifiers()} method
     * fails with an exception for an invalid modifier value.
     *
     * @return void
     */
    public function testSetInvalidModifierFail()
    {
        $this->setExpectedException('InvalidArgumentException');

        $method = new PHP_Depend_Code_Method('method');
        $method->setModifiers(-1);
    }

    /**
     * Tests that the {@link PHP_Depend_Code_Method::setModifiers()} method
     * accepts the defined visibility value.
     *
     * @return void
     */
    public function testSetModifiersAcceptsPublicValue()
    {
        $method = new PHP_Depend_Code_Method('method');
        $method->setModifiers(PHP_Depend_ConstantsI::IS_PUBLIC);

        self::assertTrue(
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
        $method = new PHP_Depend_Code_Method('method');
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
        $method = new PHP_Depend_Code_Method('method');
        $method->setModifiers(PHP_Depend_ConstantsI::IS_ABSTRACT);

        $this->assertEquals(
            PHP_Depend_ConstantsI::IS_ABSTRACT,
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
        $method = new PHP_Depend_Code_Method('method');
        self::assertFalse($method->isStatic());
    }

    /**
     * Tests that the {@link PHP_Depend_Code_Method::setModifiers()} method marks
     * a method as static.
     *
     * @return void
     */
    public function testSetModifiersMarksMethodAsStatic()
    {
        $method = new PHP_Depend_Code_Method('method');
        $method->setModifiers(
            PHP_Depend_ConstantsI::IS_PROTECTED |
            PHP_Depend_ConstantsI::IS_STATIC
        );

        self::assertTrue($method->isStatic());
    }

    /**
     * testIsFinalByDefaultReturnsFalse
     *
     * @return void
     */
    public function testIsFinalByDefaultReturnsFalse()
    {
        $method = new PHP_Depend_Code_Method('method');
        self::assertFalse($method->isFinal());
    }

    /**
     * Tests that the {@link PHP_Depend_Code_Method::setModifiers()} method marks
     * a method as final.
     *
     * @return void
     */
    public function testSetModifiersMarksMethodAsFinal()
    {
        $method = new PHP_Depend_Code_Method('method');
        $method->setModifiers(
            PHP_Depend_ConstantsI::IS_PROTECTED |
            PHP_Depend_ConstantsI::IS_FINAL
        );

        self::assertTrue($method->isFinal());
    }

    /**
     * Tests that the {@link PHP_Depend_Code_Method::setModifiers()} method marks
     * a method as static+final.
     *
     * @return void
     */
    public function testSetModifiersMarksMethodAsStaticFinal()
    {
        $method = new PHP_Depend_Code_Method('method');
        $method->setModifiers(
            PHP_Depend_ConstantsI::IS_PROTECTED |
            PHP_Depend_ConstantsI::IS_STATIC |
            PHP_Depend_ConstantsI::IS_FINAL
        );

        self::assertTrue($method->isFinal() && $method->isStatic());
    }

    /**
     * Tests that the {@link PHP_Depend_Code_Method::setModifiers()} method
     * accepts the defined visibility value.
     *
     * @return void
     */
    public function testSetModifiersAcceptsProtectedValue()
    {
        $method = new PHP_Depend_Code_Method('method');
        $method->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED);

        self::assertTrue(
            $method->isProtected() &&
            !$method->isPublic() &&
            !$method->isPrivate()
        );
    }

    /**
     * Tests that the {@link PHP_Depend_Code_Method::setModifiers()} method
     * accepts the defined visibility value.
     *
     * @return void
     */
    public function testSetModifiersAcceptsPrivateValue()
    {
        $method = new PHP_Depend_Code_Method('method');
        $method->setModifiers(PHP_Depend_ConstantsI::IS_PRIVATE);

        self::assertTrue(
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
        $method = new PHP_Depend_Code_Method('method');
        self::assertFalse($method->isPublic());
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Method::getFirstChildOfType()}.
     *
     * @return void
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedFirstMatch()
    {
        $node1 = $this->getMock(
            'PHP_Depend_Code_ASTNodeI',
            array(),
            array(),
            'PHP_Depend_Code_ASTNodeI_' . md5(microtime())
        );
        $node1->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $node2 = $this->getMock(
            'PHP_Depend_Code_ASTNodeI',
            array(),
            array(),
            'PHP_Depend_Code_ASTNodeI_' . md5(microtime())
        );
        $node2->expects($this->never())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $method = new PHP_Depend_Code_Method('Method');
        $method->addChild($node1);
        $method->addChild($node2);

        $child = $method->getFirstChildOfType(get_class($node2));
        self::assertSame($node2, $child);
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Method::getFirstChildOfType()}.
     *
     * @return void
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedNestedMatch()
    {
        $node1 = $this->getMock(
            'PHP_Depend_Code_ASTNodeI',
            array(),
            array(),
            'PHP_Depend_Code_ASTNodeI_' . md5(microtime())
        );
        $node1->expects($this->never())
            ->method('getFirstChildOfType');

        $node2 = $this->getMock(
            'PHP_Depend_Code_ASTNodeI',
            array(),
            array(),
            'PHP_Depend_Code_ASTNodeI_' . md5(microtime())
        );
        $node2->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $node3 = $this->getMock(
            'PHP_Depend_Code_ASTNodeI',
            array(),
            array(),
            'PHP_Depend_Code_ASTNodeI_' . md5(microtime())
        );
        $node3->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue($node1));

        $method = new PHP_Depend_Code_Method('Method');
        $method->addChild($node2);
        $method->addChild($node3);

        $child = $method->getFirstChildOfType(get_class($node1));
        self::assertSame($node1, $child);
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Method::getFirstChildOfType()}.
     *
     * @return void
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedNull()
    {
        $node1 = $this->getMock(
            'PHP_Depend_Code_ASTNodeI',
            array(),
            array(),
            'PHP_Depend_Code_ASTNodeI_' . md5(microtime())
        );
        $node1->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $node2 = $this->getMock(
            'PHP_Depend_Code_ASTNodeI',
            array(),
            array(),
            'PHP_Depend_Code_ASTNodeI_' . md5(microtime())
        );
        $node2->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $method = new PHP_Depend_Code_Method('Method');
        $method->addChild($node1);
        $method->addChild($node2);

        $child = $method->getFirstChildOfType(
            'PHP_Depend_Code_ASTNodeI_' . md5(microtime())
        );
        self::assertNull($child);
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Method::findChildrenOfType()}.
     *
     * @return void
     */
    public function testFindChildrenOfTypeReturnsExpectedResult()
    {
        $node1 = $this->getMock(
            'PHP_Depend_Code_ASTNodeI',
            array(),
            array(),
            'PHP_Depend_Code_ASTNodeI_' . md5(microtime())
        );
        $node1->expects($this->once())
            ->method('findChildrenOfType')
            ->will($this->returnValue(array()));

        $node2 = $this->getMock(
            'PHP_Depend_Code_ASTNodeI',
            array(),
            array(),
            'PHP_Depend_Code_ASTNodeI_' . md5(microtime())
        );
        $node2->expects($this->once())
            ->method('findChildrenOfType')
            ->will($this->returnValue(array()));

        $method = new PHP_Depend_Code_Method('Method');
        $method->addChild($node1);
        $method->addChild($node2);

        $children = $method->findChildrenOfType(get_class($node2));
        self::assertSame(array($node2), $children);
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

        self::assertSame(
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

        self::assertSame(
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

        self::assertSame(
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

        self::assertSame(
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

        self::assertSame(
            $orig->getDependencies()->current(),
            $copy->getDependencies()->current()
        );
    }

    /**
     * Returns the first method defined in a source file associated with the
     * given test case.
     *
     * @return PHP_Depend_Code_Method
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
     * @return PHP_Depend_Code_AbstractItem
     */
    protected function createItem()
    {
        $method = new PHP_Depend_Code_Method('method');
        $method->setSourceFile(new PHP_Depend_Code_File(__FILE__));

        return $method;
    }
}
