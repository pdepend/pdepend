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

use PDepend\Source\Builder\BuilderContext;
use PDepend\Source\Tokenizer\Token;
use PDepend\Util\Cache\Driver\MemoryCacheDriver;

/**
 * Test case for the code interface class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\AST\AbstractASTClassOrInterface
 * @covers \PDepend\Source\AST\AbstractASTType
 * @covers \PDepend\Source\AST\ASTInterface
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @group unittest
 */
class ASTInterfaceTest extends AbstractASTArtifactTest
{
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
            'Mock_' . __FUNCTION__ . '_' . md5(microtime())
        );
        $node1->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $node2 = $this->getMock(
            'PDepend\\Source\\AST\\ASTNode',
            array(),
            array(),
            'Mock_' . __FUNCTION__ . '_' . md5(microtime())
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
            'Mock_' . __FUNCTION__ . '_' . md5(microtime())
        );
        $node1->expects($this->never())
            ->method('getFirstChildOfType');

        $node2 = $this->getMock(
            'PDepend\\Source\\AST\\ASTNode',
            array(),
            array(),
            'Mock_' . __FUNCTION__ . '_' . md5(microtime())
        );
        $node2->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $node3 = $this->getMock(
            'PDepend\\Source\\AST\\ASTNode',
            array(),
            array(),
            'Mock_' . __FUNCTION__ . '_' . md5(microtime())
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
            'Mock_' . __FUNCTION__ . '_' . md5(microtime())
        );
        $node1->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $node2 = $this->getMock(
            'PDepend\\Source\\AST\\ASTNode',
            array(),
            array(),
            'Mock_' . __FUNCTION__ . '_' . md5(microtime())
        );
        $node2->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $interface = $this->createItem();
        $interface->addChild($node1);
        $interface->addChild($node2);

        $child = $interface->getFirstChildOfType(
            'Mock_' . __FUNCTION__ . '_' . md5(microtime())
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
        $namespaces = self::parseCodeResourceForTest();
        $namespace = $namespaces[0];

        $interfaces = $namespace->getInterfaces();

        $this->assertSame(0, count($interfaces[0]->getInterfaces()));
    }

    /**
     * Tests the result of the <b>getInterfaces()</b> method.
     *
     * @return void
     */
    public function testGetInterfacesOneLevelInheritance()
    {
        $namespaces = self::parseCodeResourceForTest();
        $namespace = $namespaces[0];

        $interface = $namespace->getInterfaces()
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
        $namespaces = self::parseCodeResourceForTest();
        $interface = $namespaces[0]->getInterfaces()
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
        $namespaces = self::parseCodeResourceForTest();
        $interface = $namespaces[0]->getInterfaces()
            ->current();

        $this->assertSame(5, $interface->getInterfaces()->count());
    }

    /**
     * Tests that {@link \PDepend\Source\AST\ASTInterface::isSubtypeOf()}
     * returns <b>false</b> for an input class.
     *
     * @return void
     */
    public function testIsSubtypeOfReturnsFalseForNonParents()
    {
        $namespaces = self::parseCodeResourceForTest();
        $interfaces = $namespaces[0]->getInterfaces();

        $this->assertFalse($interfaces[0]->isSubtypeOf($interfaces[1]));
    }

    /**
     * Checks the {@link \PDepend\Source\AST\ASTInterface::isSubtypeOf()}
     * method.
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
     * Checks the {@link \PDepend\Source\AST\ASTInterface::isSubtypeOf()} method.
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
     * Checks the {@link \PDepend\Source\AST\ASTInterface::isSubtypeOf()} method.
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
     * Checks the {@link \PDepend\Source\AST\ASTInterface::isSubtypeOf()} method.
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
        $namespaces = self::parseCodeResourceForTest();
        $namespace = $namespaces->current();
        $current  = $namespace->getInterfaces()->current();

        $actual = array();
        foreach ($namespace->getInterfaces() as $interface) {
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
        $namespaces = self::parseCodeResourceForTest();

        $class = $namespaces->current()
            ->getInterfaces()
            ->current();

        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTFormalParameter',
            $class->getFirstChildOfType('PDepend\\Source\\AST\\ASTFormalParameter')
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
            'PDepend\\Source\\AST\\ASTFormalParameter'
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
                '\\PDepend\\Source\\AST\\ASTClassReference',
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
            State::IS_IMPLICIT_ABSTRACT,
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

        $this->assertSame($copy, $copy->getMethods()->current()->getParent());
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

        $this->assertSame(
            $copy->getCompilationUnit(),
            $copy->getMethods()->current()->getCompilationUnit()
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

        $this->assertSame(
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

        $this->assertSame(
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

        $this->assertSame(
            $orig->getNamespace(),
            $copy->getNamespace()
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

        $this->assertSame($copy, $orig->getNamespace()->getInterfaces()->current());
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

        $this->assertEquals(1, $orig->getNamespace()->getInterfaces()->count());
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
        $tokens = array(new Token(1, 'a', 23, 42, 13, 17));

        $cache = $this->createCacheFixture();
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
        $this->assertSame(0, $interface->getStartLine());
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

        $interface = $this->createItem();
        $interface->setCache($cache)
            ->setTokens(
                array(
                    new Token(1, 'a', 23, 42, 0, 0),
                    new Token(2, 'b', 17, 32, 0, 0),
                )
            );

        $this->assertEquals(23, $interface->getStartLine());
    }

    /**
     * testGetEndLineReturnsZeroByDefault
     *
     * @return void
     */
    public function testGetEndLineReturnsZeroByDefault()
    {
        $interface = $this->createItem();
        $this->assertSame(0, $interface->getEndLine());
    }

    /**
     * testGetParentClassReferenceReturnsNullByDefault
     *
     * @return void
     */
    public function testGetParentClassReferenceReturnsNullByDefault()
    {
        $class = $this->createItem();
        $this->assertNull($class->getParentClassReference());
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
        $this->assertSame(array(), $interface->getInterfaceReferences());
    }

    /**
     * testGetInterfaceReferencesReturnsExpectedNumberOfInterfaces
     *
     * @return void
     */
    public function testGetInterfaceReferencesReturnsExpectedNumberOfInterfaces()
    {
        $interface = $this->getFirstInterfaceForTestCase();
        $this->assertEquals(3, count($interface->getInterfaceReferences()));
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
        $cache = $this->createCacheFixture();
        $cache->expects($this->once())
            ->method('type')
            ->will($this->returnValue($cache));

        $interface = $this->createItem();
        $interface->setCache($cache)
            ->setTokens(
                array(
                    new Token(1, 'a', 23, 42, 0, 0),
                    new Token(2, 'b', 17, 32, 0, 0),
                )
            );

        $this->assertEquals(32, $interface->getEndLine());
    }

    /**
     * testIsAbstractReturnsAlwaysTrue
     *
     * @return void
     */
    public function testIsAbstractReturnsAlwaysTrue()
    {
        $interface = $this->createItem();
        $this->assertTrue($interface->isAbstract());
    }

    /**
     * testIsUserDefinedReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsUserDefinedReturnsFalseByDefault()
    {
        $interface = $this->createItem();
        $this->assertFalse($interface->isUserDefined());
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

        $this->assertTrue($interface->isUserDefined());
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
        $this->assertFalse($interface->isCached());
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

        $this->assertFalse($interface->isCached());
    }

    /**
     * testMagicSleepMethodReturnsExpectedSetOfPropertyNames
     *
     * @return void
     */
    public function testMagicSleepMethodReturnsExpectedSetOfPropertyNames()
    {
        $interface = $this->createItem();
        $interface->setNamespace(new ASTNamespace(__FUNCTION__));

        $this->assertEquals(
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
                'namespaceName',
                'startLine',
                'userDefined',
                'id'
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
        $method    = new ASTMethod(__FUNCTION__);
        $interface->addMethod($method);

        $interface->__wakeup();

        $this->assertSame($interface->getCompilationUnit(), $method->getCompilationUnit());
    }

    /**
     * testMagicWakeupCallsRegisterInterfaceOnBuilderContext
     *
     * @return void
     */
    public function testMagicWakeupCallsRegisterInterfaceOnBuilderContext()
    {
        $interface = $this->createItem();

        $context = $this->getMock('PDepend\\Source\\Builder\\BuilderContext');
        $context->expects($this->once())
            ->method('registerInterface')
            ->with(self::isInstanceOf('PDepend\\Source\\AST\\ASTInterface'));

        $interface->setContext($context)->__wakeup();
    }

    /**
     * testAcceptInvokesVisitInterfaceOnGivenVisitor
     *
     * @return void
     */
    public function testAcceptInvokesVisitInterfaceOnGivenVisitor()
    {
        $visitor = $this->getMock('\\PDepend\\Source\\ASTVisitor\\ASTVisitor');
        $visitor->expects($this->once())
            ->method('visitInterface')
            ->with(self::isInstanceOf('PDepend\\Source\\AST\\ASTInterface'));

        $interface = $this->createItem();
        $interface->accept($visitor);
    }

    /**
     * Creates an abstract item instance.
     *
     * @return \PDepend\Source\AST\ASTInterface
     */
    protected function createItem()
    {
        $interface = new ASTInterface(__CLASS__);
        $interface->setCompilationUnit(new ASTCompilationUnit(__FILE__));
        $interface->setCache(new MemoryCacheDriver());
        $interface->setContext($this->getMock('PDepend\\Source\\Builder\\BuilderContext'));

        return $interface;
    }
}
