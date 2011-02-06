<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
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
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 *
 * @covers PHP_Depend_Code_AbstractClassOrInterface
 * @covers PHP_Depend_Code_Interface
 */
class PHP_Depend_Code_InterfaceTest extends PHP_Depend_Code_AbstractItemTest
{
    /**
     * Tests the behavior of {@link PHP_Depend_Code_Method::getFirstChildOfType()}.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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

        $interface = new PHP_Depend_Code_Interface('Interface');
        $interface->addChild($node1);
        $interface->addChild($node2);

        $child = $interface->getFirstChildOfType(get_class($node2));
        $this->assertSame($node2, $child);
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Method::getFirstChildOfType()}.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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

        $interface = new PHP_Depend_Code_Interface('Interface');
        $interface->addChild($node2);
        $interface->addChild($node3);

        $child = $interface->getFirstChildOfType(get_class($node1));
        $this->assertSame($node1, $child);
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Method::getFirstChildOfType()}.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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

        $interface = new PHP_Depend_Code_Interface('Interface');
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetFirstChildOfTypeFindsASTNodeInMethodDeclaration()
    {
        $packages = self::parseCodeResourceForTest();

        $class = $packages->current()
            ->getInterfaces()
            ->current();

        $this->assertType(
            PHP_Depend_Code_ASTFormalParameter::CLAZZ,
            $class->getFirstChildOfType(PHP_Depend_Code_ASTFormalParameter::CLAZZ)
        );
    }

    /**
     * testGetFirstChildOfTypeFindsASTNodeInMethodDeclaration
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     * @expectedException BadMethodCallException
     */
    public function testInterfaceThrowsExpectedExceptionOnSetParentClassReference()
    {
        $interface = new PHP_Depend_Code_Interface('IFooBar');
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testInterfaceReturnsExpectedModifiers()
    {
        $interface = new PHP_Depend_Code_Interface('Foo');
        $this->assertSame(
            PHP_Depend_ConstantsI::IS_IMPLICIT_ABSTRACT,
            $interface->getModifiers()
        );
    }

    /**
     * testUnserializedInterfaceStillIsParentOfChildMethods
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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

        $interface = new PHP_Depend_Code_Interface(__CLASS__);
        $interface->setCache($cache)
            ->getTokens();
    }

    /**
     * testSetTokensDelegatesCallToCacheStore
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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

        $interface = new PHP_Depend_Code_Interface(__CLASS__);
        $interface->setCache($cache)
            ->setTokens($tokens);
    }

    /**
     * testGetStartLineReturnsZeroByDefault
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetStartLineReturnsZeroByDefault()
    {
        $interface = new PHP_Depend_Code_Interface(__CLASS__);
        self::assertSame(0, $interface->getStartLine());
    }

    /**
     * testGetStartLineReturnsStartLineOfFirstToken
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetStartLineReturnsStartLineOfFirstToken()
    {
        $cache = $this->getMock('PHP_Depend_Util_Cache_Driver');
        $cache->expects($this->once())
            ->method('type')
            ->will($this->returnValue($cache));

        $interface = new PHP_Depend_Code_Interface(__CLASS__);
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetEndLineReturnsZeroByDefault()
    {
        $interface = new PHP_Depend_Code_Interface(__CLASS__);
        self::assertSame(0, $interface->getEndLine());
    }

    /**
     * testGetEndLineReturnsEndLineOfLastToken
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetEndLineReturnsEndLineOfLastToken()
    {
        $cache = $this->getMock('PHP_Depend_Util_Cache_Driver');
        $cache->expects($this->once())
            ->method('type')
            ->will($this->returnValue($cache));

        $interface = new PHP_Depend_Code_Interface(__CLASS__);
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testIsAbstractReturnsAlwaysTrue()
    {
        $interface = new PHP_Depend_Code_Interface(__CLASS__);
        self::assertTrue($interface->isAbstract());
    }

    /**
     * testIsUserDefinedReturnsFalseByDefault
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testIsUserDefinedReturnsFalseByDefault()
    {
        $interface = new PHP_Depend_Code_Interface(__CLASS__);
        self::assertFalse($interface->isUserDefined());
    }

    /**
     * testIsUserDefinedReturnsTrueAfterSetUserDefinedCall
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testIsUserDefinedReturnsTrueAfterSetUserDefinedCall()
    {
        $interface = new PHP_Depend_Code_Interface(__CLASS__);
        $interface->setUserDefined();

        self::assertTrue($interface->isUserDefined());
    }

    /**
     * testIsCachedReturnsFalseByDefault
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testIsCachedReturnsFalseWhenObjectGetsSerialized()
    {
        $interface = $this->createItem();
        serialize($interface);

        self::assertFalse($interface->isCached());
    }

    /**
     * testIsCachedReturnsTrueAfterCallToWakeup
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testIsCachedReturnsTrueAfterCallToWakeup()
    {
        $interface = $this->createItem();
        $interface = unserialize(serialize($interface));

        self::assertTrue($interface->isCached());
    }

    /**
     * testMagicSleepMethodReturnsExpectedSetOfPropertyNames
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testMagicSleepMethodReturnsExpectedSetOfPropertyNames()
    {
        $interface = new PHP_Depend_Code_Interface(__CLASS__);
        $interface->setPackage(new PHP_Depend_Code_Package(__FUNCTION__));

        self::assertEquals(
            array(
                'cache',
                'constants',
                'context',
                'docComment',
                'endLine',
                'interfaceReferences',
                'methods',
                'modifiers',
                'name',
                'nodes',
                'packageName',
                'parentClassReference',
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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testMagicWakeupSetsSourceFileOnChildMethods()
    {
        $interface = new PHP_Depend_Code_Interface(__CLASS__);
        $method    = new PHP_Depend_Code_Method(__FUNCTION__);
        $interface->addMethod($method);

        $file = new PHP_Depend_Code_File(__FILE__);
        $interface->setSourceFile($file);
        $interface->setContext($this->getMock('PHP_Depend_Builder_Context'));
        $interface->__wakeup();

        self::assertSame($file, $method->getSourceFile());
    }

    /**
     * testMagicWakeupSetsParentOnChildMethods
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testMagicWakeupSetsParentOnChildMethods()
    {
        $interface = new PHP_Depend_Code_Interface(__CLASS__);
        $method    = new PHP_Depend_Code_Method(__FUNCTION__);
        
        $interface->addMethod($method);
        $interface->setContext($this->getMock('PHP_Depend_Builder_Context'));
        $method->setParent(null);
        $interface->__wakeup();

        self::assertSame($interface, $method->getParent());
    }

    /**
     * testMagicWakeupCallsRegisterInterfaceOnBuilderContext
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testMagicWakeupCallsRegisterInterfaceOnBuilderContext()
    {
        $interface = new PHP_Depend_Code_Interface(__CLASS__);

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
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testAcceptInvokesVisitInterfaceOnGivenVisitor()
    {
        $visitor = $this->getMock('PHP_Depend_VisitorI');
        $visitor->expects($this->once())
            ->method('visitInterface')
            ->with(self::isInstanceOf(PHP_Depend_Code_Interface::CLAZZ));

        $interface = new PHP_Depend_Code_Interface(__CLASS__);
        $interface->accept($visitor);
    }

    /**
     * Returns the first interface that could be found in the source file
     * associated with the calling test case.
     *
     * @return PHP_Depend_Code_Interface
     */
    protected function getFirstInterfaceForTestCase()
    {
        return self::parseCodeResourceForTest()
            ->current()
            ->getInterfaces()
            ->current();
    }
    
    /**
     * Creates an abstract item instance.
     *
     * @return PHP_Depend_Code_AbstractItem
     */
    protected function createItem()
    {
        $interface = new PHP_Depend_Code_Interface(__CLASS__);
        $interface->setContext($this->getMock('PHP_Depend_Builder_Context'));

        return $interface;
    }
}