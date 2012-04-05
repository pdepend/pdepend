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

/**
 * Test case for the code interface class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 *
 * @covers PHP_Depend_Code_AbstractClassOrInterface
 * @covers PHP_Depend_Code_AbstractType
 * @covers PHP_Depend_Code_Interface
 * @covers PHP_Depend_Parser
 * @group pdepend
 * @group pdepend::code
 * @group unittest
 */
class PHP_Depend_Code_InterfaceTest extends PHP_Depend_Code_AbstractItemTest
{
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

        $interface = $this->createItem();
        $interface->addChild($node1);
        $interface->addChild($node2);

        $child = $interface->getFirstChildOfType(get_class($node2));
        $this->assertSame($node2, $child);
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

        $interface = $this->createItem();
        $interface->addChild($node2);
        $interface->addChild($node3);

        $child = $interface->getFirstChildOfType(get_class($node1));
        $this->assertSame($node1, $child);
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

        $interface = $this->createItem();
        $interface->addChild($node1);
        $interface->addChild($node2);

        $child = $interface->getFirstChildOfType(
            'PHP_Depend_Code_ASTNodeI_' . md5(microtime())
        );
        $this->assertNull($child);
    }

    /**
     * Tests the result of the <b>getInterfaces()</b> method.
     *
     * @return void
     */
    public function testGetInterfacesZeroInheritance()
    {
        $packages = self::parseCodeResourceForTest();
        $package  = $packages->current();

        $interface = $package->getInterfaces()
            ->current();

        $this->assertSame(0, $interface->getInterfaces()->count());
    }

    /**
     * Tests the result of the <b>getInterfaces()</b> method.
     *
     * @return void
     */
    public function testGetInterfacesOneLevelInheritance()
    {
        $packages = self::parseCodeResourceForTest();
        $package  = $packages->current();

        $interface = $package->getInterfaces()
            ->current();

        $this->assertSame(1, $interface->getInterfaces()->count());
    }

    /**
     * Tests the result of the <b>getInterfaces()</b> method.
     *
     * @return void
     */
    public function testGetInterfacesTwoLevelInheritance()
    {
        $packages = self::parseCodeResourceForTest();
        $package  = $packages->current();

        $interface = $package->getInterfaces()
            ->current();

        $this->assertSame(4, $interface->getInterfaces()->count());
    }

    /**
     * Tests the result of the <b>getInterfaces()</b> method.
     *
     * @return void
     */
    public function testGetInterfacesComplexInheritance()
    {
        $packages = self::parseCodeResourceForTest();
        $package  = $packages->current();

        $interface = $package->getInterfaces()
            ->current();

        $this->assertSame(5, $interface->getInterfaces()->count());
    }

    /**
     * Tests that {@link PHP_Depend_Code_Interface::isSubtypeOf()} returns
     * <b>false</b> for an input class.
     *
     * @return void
     */
    public function testIsSubtypeOfReturnsFalseForNonParents()
    {
        $packages = self::parseCodeResourceForTest();
        $package  = $packages->current();

        $interfaces = $package->getInterfaces();
        $interface  = $interfaces->current();

        $interfaces->next();
        $this->assertFalse($interface->isSubtypeOf($interfaces->current()));
    }

    /**
     * Checks the {@link PHP_Depend_Code_Interface::isSubtypeOf()} method.
     *
     * @return void
     */
    public function testIsSubtypeOnInheritanceHierarchy()
    {
        $this->_testIsSubtypeOnInheritanceHierarchy(
            array(
                'A' => true,
                'B' => false,
                'C' => false,
                'D' => false,
                'E' => false,
                'F' => true
            )
        );
    }

    /**
     * Checks the {@link PHP_Depend_Code_Interface::isSubtypeOf()} method.
     *
     * @return void
     */
    public function testIsSubtypeOnInheritanceHierarchy1()
    {
        $this->_testIsSubtypeOnInheritanceHierarchy(
            array(
                'A' => true,
                'B' => true,
                'C' => true,
                'D' => true,
                'E' => true,
                'F' => true
            )
        );
    }

    /**
     * Checks the {@link PHP_Depend_Code_Interface::isSubtypeOf()} method.
     *
     * @return void
     */
    public function testIsSubtypeOnInheritanceHierarchy2()
    {
        $this->_testIsSubtypeOnInheritanceHierarchy(
            array(
                'B' => false,
                'C' => false,
                'A' => true,
                'D' => true,
                'E' => true,
                'F' => false
            )
        );
    }

    /**
     * Checks the {@link PHP_Depend_Code_Interface::isSubtypeOf()} method.
     *
     * @return void
     */
    public function testIsSubtypeOnInheritanceHierarchy3()
    {
        $this->_testIsSubtypeOnInheritanceHierarchy(
            array(
                'B' => false,
                'C' => false,
                'D' => false,
                'A' => true,
                'E' => false,
                'F' => false
            )
        );
    }

    /**
     * _testIsSubtypeOnInheritanceHierarchy
     *
     * @param array(string=>boolean) $expected Expected result.
     *
     * @return void
     */
    private function _testIsSubtypeOnInheritanceHierarchy(array $expected)
    {
        $packages = self::parseCodeResourceForTest();
        $package  = $packages->current();
        $current  = $package->getInterfaces()->current();

        $actual = array();
        foreach ($package->getInterfaces() as $interface) {
            $actual[$interface->getName()] = $current->isSubtypeOf($interface);
        }

        ksort($expected);
        ksort($actual);

        $this->assertEquals($expected, $actual);
    }

    /**
     * testGetFirstChildOfTypeFindsASTNodeInMethodDeclaration
     *
     * @return void
     */
    public function testGetFirstChildOfTypeFindsASTNodeInMethodDeclaration()
    {
        $packages = self::parseCodeResourceForTest();

        $class = $packages->current()
            ->getInterfaces()
            ->current();

        $this->assertInstanceOf(
            PHP_Depend_Code_ASTFormalParameter::CLAZZ,
            $class->getFirstChildOfType(PHP_Depend_Code_ASTFormalParameter::CLAZZ)
        );
    }

    /**
     * testGetFirstChildOfTypeFindsASTNodeInMethodDeclaration
     *
     * @return void
     */
    public function testFindChildrenOfTypeFindsASTNodeInMethodDeclarations()
    {
        $class = self::parseCodeResourceForTest()
            ->current()
            ->getInterfaces()
            ->current();

        $parameters = $class->findChildrenOfType(
            PHP_Depend_Code_ASTFormalParameter::CLAZZ
        );
        $this->assertEquals(4, count($parameters));
    }

    /**
     * Tests that the interface implementation overwrites the
     * setParentClassReference() method and throws an exception.
     *
     * @return void
     * @expectedException BadMethodCallException
     */
    public function testInterfaceThrowsExpectedExceptionOnSetParentClassReference()
    {
        $interface = $this->createItem();
        $interface->setParentClassReference(
            $this->getMock(
                'PHP_Depend_Code_ASTClassReference',
                array(),
                array(),
                '',
                false
            )
        );
    }

    /**
     * Tests the returned modifiers of an interface.
     *
     * @return void
     */
    public function testInterfaceReturnsExpectedModifiers()
    {
        $interface = $this->createItem();
        $this->assertSame(
            PHP_Depend_ConstantsI::IS_IMPLICIT_ABSTRACT,
            $interface->getModifiers()
        );
    }

    /**
     * testUnserializedInterfaceStillIsParentOfChildMethods
     *
     * @return void
     */
    public function testUnserializedInterfaceStillIsParentOfChildMethods()
    {
        $orig = $this->getFirstInterfaceForTestCase();
        $copy = unserialize(serialize($orig));

        self::assertSame($copy, $copy->getMethods()->current()->getParent());
    }

    /**
     * testUnserializedInterfaceAndChildMethodsStillReferenceTheSameFile
     *
     * @return void
     */
    public function testUnserializedInterfaceAndChildMethodsStillReferenceTheSameFile()
    {
        $orig = $this->getFirstInterfaceForTestCase();
        $copy = unserialize(serialize($orig));

        self::assertSame(
            $copy->getSourceFile(),
            $copy->getMethods()->current()->getSourceFile()
        );
    }

    /**
     * testUnserializedInterfaceStillReferencesSameParentInterface
     *
     * @return void
     */
    public function testUnserializedInterfaceStillReferencesSameParentInterface()
    {
        $orig = $this->getFirstInterfaceForTestCase();
        $copy = unserialize(serialize($orig));

        self::assertSame(
            $orig->getInterfaces()->current(),
            $copy->getInterfaces()->current()
        );
    }

    /**
     * testUnserializedInterfaceIsReturnedByMethodAsReturnClass
     *
     * @return void
     */
    public function testUnserializedInterfaceIsReturnedByMethodAsReturnClass()
    {
        $orig   = $this->getFirstInterfaceForTestCase();
        $method = $orig->getMethods()->current();

        $copy = unserialize(serialize($orig));

        self::assertSame(
            $method->getReturnClass(),
            $copy
        );
    }

    /**
     * testUnserializedInterfaceStillReferencesSamePackage
     *
     * @return void
     */
    public function testUnserializedInterfaceStillReferencesSamePackage()
    {
        $orig = $this->getFirstInterfaceForTestCase();
        $copy = unserialize(serialize($orig));

        self::assertSame(
            $orig->getPackage(),
            $copy->getPackage()
        );
    }

    /**
     * testUnserializedInterfaceRegistersToPackage
     *
     * @return void
     */
    public function testUnserializedInterfaceRegistersToPackage()
    {
        $orig = $this->getFirstInterfaceForTestCase();
        $copy = unserialize(serialize($orig));

        self::assertSame($copy, $orig->getPackage()->getInterfaces()->current());
    }

    /**
     * testUnserializedInterfaceNotAddsDublicateClassToPackage
     *
     * @return void
     */
    public function testUnserializedInterfaceNotAddsDublicateClassToPackage()
    {
        $orig = $this->getFirstInterfaceForTestCase();
        $copy = unserialize(serialize($orig));

        self::assertEquals(1, $orig->getPackage()->getInterfaces()->count());
    }

    /**
     * testGetTokensDelegatesCallToCacheRestore
     *
     * @return void
     */
    public function testGetTokensDelegatesCallToCacheRestore()
    {
        $cache = $this->getMock('PHP_Depend_Util_Cache_Driver');
        $cache->expects($this->once())
            ->method('type')
            ->with(self::equalTo('tokens'))
            ->will($this->returnValue($cache));
        $cache->expects($this->once())
            ->method('restore');

        $interface = $this->createItem();
        $interface->setCache($cache)
            ->getTokens();
    }

    /**
     * testSetTokensDelegatesCallToCacheStore
     *
     * @return void
     */
    public function testSetTokensDelegatesCallToCacheStore()
    {
        $tokens = array(new PHP_Depend_Token(1, 'a', 23, 42, 13, 17));

        $cache = $this->getMock('PHP_Depend_Util_Cache_Driver');
        $cache->expects($this->once())
            ->method('type')
            ->with(self::equalTo('tokens'))
            ->will($this->returnValue($cache));
        $cache->expects($this->once())
            ->method('store')
            ->with(self::equalTo(null), self::equalTo($tokens));

        $interface = $this->createItem();
        $interface->setCache($cache)
            ->setTokens($tokens);
    }

    /**
     * testGetStartLineReturnsZeroByDefault
     *
     * @return void
     */
    public function testGetStartLineReturnsZeroByDefault()
    {
        $interface = $this->createItem();
        self::assertSame(0, $interface->getStartLine());
    }

    /**
     * testGetStartLineReturnsStartLineOfFirstToken
     *
     * @return void
     */
    public function testGetStartLineReturnsStartLineOfFirstToken()
    {
        $cache = $this->getMock('PHP_Depend_Util_Cache_Driver');
        $cache->expects($this->once())
            ->method('type')
            ->will($this->returnValue($cache));

        $interface = $this->createItem();
        $interface->setCache($cache)
            ->setTokens(
                array(
                    new PHP_Depend_Token(1, 'a', 23, 42, 0, 0),
                    new PHP_Depend_Token(2, 'b', 17, 32, 0, 0),
                )
            );

        self::assertEquals(23, $interface->getStartLine());
    }

    /**
     * testGetEndLineReturnsZeroByDefault
     *
     * @return void
     */
    public function testGetEndLineReturnsZeroByDefault()
    {
        $interface = $this->createItem();
        self::assertSame(0, $interface->getEndLine());
    }

    /**
     * testGetParentClassReferenceReturnsNullByDefault
     *
     * @return void
     */
    public function testGetParentClassReferenceReturnsNullByDefault()
    {
        $class = $this->createItem();
        self::assertNull($class->getParentClassReference());
    }

    /**
     * testGetParentClassesReturnsEmptyArrayByDefault
     *
     * @return void
     */
    public function testGetParentClassesReturnsEmptyArrayByDefault()
    {
        $interface = $this->createItem();
        $this->assertSame(array(), $interface->getParentClasses());
    }

    /**
     * testGetParentClassesReturnsEmptyArray
     *
     * @return void
     */
    public function testGetParentClassesReturnsEmptyArray()
    {
        $interface = $this->parseCodeResourceForTest()
            ->current()
            ->getInterfaces()
            ->current();

        $this->assertSame(array(), $interface->getParentClasses());
    }

    /**
     * testGetInterfaceReferencesReturnsEmptyArrayByDefault
     *
     * @return void
     */
    public function testGetInterfaceReferencesReturnsEmptyArrayByDefault()
    {
        $interface = $this->createItem();
        self::assertSame(array(), $interface->getInterfaceReferences());
    }

    /**
     * testGetInterfaceReferencesReturnsExpectedNumberOfInterfaces
     *
     * @return void
     */
    public function testGetInterfaceReferencesReturnsExpectedNumberOfInterfaces()
    {
        $interface = $this->getFirstInterfaceForTestCase();
        self::assertEquals(3, count($interface->getInterfaceReferences()));
    }

    /**
     * testGetAllChildrenReturnsAnEmptyArrayByDefault
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetAllChildrenReturnsAnEmptyArrayByDefault()
    {
        $interface = $this->createItem();
        $this->assertSame(array(), $interface->getChildren());
    }

    /**
     * testGetAllChildrenReturnsArrayWithExpectedNumberOfNodes
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetAllChildrenReturnsArrayWithExpectedNumberOfNodes()
    {
        $interface = $this->getFirstInterfaceForTestCase();
        $this->assertSame(2, count($interface->getChildren()));
    }

    /**
     * testGetDependenciesReturnsEmptyResultByDefault
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetDependenciesReturnsEmptyResultByDefault()
    {
        $interface = $this->getFirstInterfaceForTestCase();
        $this->assertEquals(0, count($interface->getDependencies()));
    }

    /**
     * testGetDependenciesContainsExtendedInterface
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetDependenciesContainsExtendedInterface()
    {
        $interface = $this->getFirstInterfaceForTestCase();
        $this->assertEquals(1, count($interface->getDependencies()));
    }

    /**
     * testGetDependenciesContainsExtendedInterfaces
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetDependenciesContainsExtendedInterfaces()
    {
        $interface = $this->getFirstInterfaceForTestCase();
        $this->assertEquals(3, count($interface->getDependencies()));
    }

    /**
     * testGetEndLineReturnsEndLineOfLastToken
     *
     * @return void
     */
    public function testGetEndLineReturnsEndLineOfLastToken()
    {
        $cache = $this->getMock('PHP_Depend_Util_Cache_Driver');
        $cache->expects($this->once())
            ->method('type')
            ->will($this->returnValue($cache));

        $interface = $this->createItem();
        $interface->setCache($cache)
            ->setTokens(
                array(
                    new PHP_Depend_Token(1, 'a', 23, 42, 0, 0),
                    new PHP_Depend_Token(2, 'b', 17, 32, 0, 0),
                )
            );

        self::assertEquals(32, $interface->getEndLine());
    }

    /**
     * testIsAbstractReturnsAlwaysTrue
     *
     * @return void
     */
    public function testIsAbstractReturnsAlwaysTrue()
    {
        $interface = $this->createItem();
        self::assertTrue($interface->isAbstract());
    }

    /**
     * testIsUserDefinedReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsUserDefinedReturnsFalseByDefault()
    {
        $interface = $this->createItem();
        self::assertFalse($interface->isUserDefined());
    }

    /**
     * testIsUserDefinedReturnsTrueAfterSetUserDefinedCall
     *
     * @return void
     */
    public function testIsUserDefinedReturnsTrueAfterSetUserDefinedCall()
    {
        $interface = $this->createItem();
        $interface->setUserDefined();

        self::assertTrue($interface->isUserDefined());
    }

    /**
     * testGetConstantsReturnsExpectedInterfaceConstants
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetConstantsReturnsExpectedInterfaceConstants()
    {
        $interface = $this->getFirstInterfaceForTestCase();
        $this->assertEquals(array('FOO' => 42, 'BAR' => 23), $interface->getConstants());
    }

    /**
     * testIsCachedReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsCachedReturnsFalseByDefault()
    {
        $interface = $this->createItem();
        self::assertFalse($interface->isCached());
    }

    /**
     * testIsCachedReturnsFalseWhenObjectGetsSerialized
     *
     * @return void
     */
    public function testIsCachedReturnsFalseWhenObjectGetsSerialized()
    {
        $interface = $this->createItem();
        serialize($interface);

        self::assertFalse($interface->isCached());
    }

    /**
     * testMagicSleepMethodReturnsExpectedSetOfPropertyNames
     *
     * @return void
     */
    public function testMagicSleepMethodReturnsExpectedSetOfPropertyNames()
    {
        $interface = $this->createItem();
        $interface->setPackage(new PHP_Depend_Code_Package(__FUNCTION__));

        self::assertEquals(
            array(
                'constants',
                'interfaceReferences',
                'parentClassReference',
                'cache',
                'context',
                'docComment',
                'endLine',
                'modifiers',
                'name',
                'nodes',
                'packageName',
                'startLine',
                'userDefined',
                'uuid'
            ),
            $interface->__sleep()
        );
    }

    /**
     * testMagicWakeupSetsSourceFileOnChildMethods
     *
     * @return void
     */
    public function testMagicWakeupSetsSourceFileOnChildMethods()
    {
        $interface = $this->createItem();
        $method    = new PHP_Depend_Code_Method(__FUNCTION__);
        $interface->addMethod($method);

        $interface->__wakeup();

        self::assertSame($interface->getSourceFile(), $method->getSourceFile());
    }

    /**
     * testMagicWakeupCallsRegisterInterfaceOnBuilderContext
     *
     * @return void
     */
    public function testMagicWakeupCallsRegisterInterfaceOnBuilderContext()
    {
        $interface = $this->createItem();

        $context = $this->getMock('PHP_Depend_Builder_Context');
        $context->expects($this->once())
            ->method('registerInterface')
            ->with(self::isInstanceOf(PHP_Depend_Code_Interface::CLAZZ));

        $interface->setContext($context)->__wakeup();
    }

    /**
     * testAcceptInvokesVisitInterfaceOnGivenVisitor
     *
     * @return void
     */
    public function testAcceptInvokesVisitInterfaceOnGivenVisitor()
    {
        $visitor = $this->getMock('PHP_Depend_VisitorI');
        $visitor->expects($this->once())
            ->method('visitInterface')
            ->with(self::isInstanceOf(PHP_Depend_Code_Interface::CLAZZ));

        $interface = $this->createItem();
        $interface->accept($visitor);
    }

    /**
     * Creates an abstract item instance.
     *
     * @return PHP_Depend_Code_Interface
     */
    protected function createItem()
    {
        $interface = new PHP_Depend_Code_Interface(__CLASS__);
        $interface->setSourceFile(new PHP_Depend_Code_File(__FILE__));
        $interface->setCache(new PHP_Depend_Util_Cache_Driver_Memory());
        $interface->setContext($this->getMock('PHP_Depend_Builder_Context'));

        return $interface;
    }
}
