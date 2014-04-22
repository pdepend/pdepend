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
use PDepend\Source\ASTVisitor\StubASTVisitor;

/**
 * Test case implementation for the \PDepend\Source\AST\ASTFunction class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\AbstractASTCallable
 * @covers \PDepend\Source\AST\ASTFunction
 * @group unittest
 */
class ASTFunctionTest extends AbstractASTArtifactTest
{
    /**
     * testReturnsReferenceReturnsExpectedTrue
     *
     * @return void
     */
    public function testReturnsReferenceReturnsExpectedTrue()
    {
        $function = $this->_getFirstFunctionForTestCase();
        $this->assertTrue($function->returnsReference());
    }

    /**
     * testReturnsReferenceReturnsExpectedFalse
     *
     * @return void
     */
    public function testReturnsReferenceReturnsExpectedFalse()
    {
        $function = $this->_getFirstFunctionForTestCase();
        $this->assertFalse($function->returnsReference());
    }

    /**
     * testGetStaticVariablesReturnsEmptyArrayByDefault
     *
     * @return void
     */
    public function testGetStaticVariablesReturnsEmptyArrayByDefault()
    {
        $function = $this->createItem();
        $this->assertEquals(array(), $function->getStaticVariables());
    }

    /**
     * testGetStaticVariablesReturnsFirstSetOfStaticVariables
     *
     * @return void
     */
    public function testGetStaticVariablesReturnsFirstSetOfStaticVariables()
    {
        $this->assertEquals(
            array('a' => 42, 'b' => 23),
            $this->_getFirstFunctionForTestCase()->getStaticVariables()
        );
    }

    /**
     * testGetStaticVariablesReturnsMergeOfAllStaticVariables
     *
     * @return void
     */
    public function testGetStaticVariablesReturnsMergeOfAllStaticVariables()
    {
        $this->assertEquals(
            array('a' => 42, 'b' => 23, 'c' => 17),
            $this->_getFirstFunctionForTestCase()->getStaticVariables()
        );
    }

    /**
     * Tests the ctor and the {@link \PDepend\Source\AST\ASTFunction::getName()} method.
     *
     * @return void
     */
    public function testCreateNewFunctionInstance()
    {
        $function = $this->createItem();
        $this->assertEquals('createItem', $function->getName());
    }

    /**
     * testGetStaticVariablesReturnsMergeOfAllStaticVariables
     *
     * @return void
     */
    public function testGetNamespaceReturnsNullByDefault()
    {
        $function = $this->createItem();
        $this->assertNull($function->getNamespace());
    }

    /**
     * testUnsetNamespaceWithNullWillResetPreviousPackage
     *
     * @return void
     */
    public function testUnsetNamespaceWithNullWillResetPreviousPackage()
    {
        $namespace  = new ASTNamespace('nspace');
        $function = $this->createItem();

        $function->setNamespace($namespace);
        $function->unsetNamespace();

        $this->assertNull($function->getNamespace());
    }

    /**
     * testUnsetNamespaceWithNullWillResetNamespaceNameProperty
     *
     * @return void
     */
    public function testUnsetNamespaceWithNullWillResetNamespaceNameProperty()
    {
        $function = $this->createItem();
        $function->setNamespace(new ASTNamespace(__FUNCTION__));
        $function->unsetNamespace();

        $this->assertNull($function->getNamespaceName());
    }

    /**
     * testSetNamespaceNotEstablishesBackReference
     *
     * @return void
     */
    public function testSetNamespaceNotEstablishesBackReference()
    {
        $namespace = $this->getMock(
            'PDepend\\Source\\AST\\ASTNamespace',
            array(),
            array(__FUNCTION__)
        );
        $namespace->expects($this->never())
            ->method('addFunction');

        $function = $this->createItem();
        $function->setNamespace($namespace);
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTFunction::getNamespace()} returns as
     * default value <b>null</b> and that the namespace could be set and unset.
     *
     * @return void
     */
    public function testGetSetNamespace()
    {
        $namespace  = new ASTNamespace('nspace');
        $function = $this->createItem();

        $function->setNamespace($namespace);
        $this->assertSame($namespace, $function->getNamespace());
    }

    /**
     * testGetNamespaceNameReturnsNullByDefault
     *
     * @return void
     */
    public function testGetNamespaceNameReturnsNullByDefault()
    {
        $this->assertNull($this->createItem()->getNamespaceName());
    }

    /**
     * testGetNamespaceNameReturnsNameOfInjectedPackage
     *
     * @return void
     */
    public function testGetNamespaceNameReturnsNameOfInjectedPackage()
    {
        $function = $this->createItem();
        $function->setNamespace(new ASTNamespace(__FUNCTION__));

        $this->assertEquals(__FUNCTION__, $function->getNamespaceName());
    }

    /**
     * testIsCachedReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsCachedReturnsFalseByDefault()
    {
        $function = $this->createItem();
        $this->assertFalse($function->isCached());
    }

    /**
     * testIsCachedReturnsFalseWhenObjectGetsSerialized
     *
     * @return void
     */
    public function testIsCachedReturnsFalseWhenObjectGetsSerialized()
    {
        $function = $this->createItem();
        serialize($function);

        $this->assertFalse($function->isCached());
    }

    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     *
     * @return void
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames()
    {
        $function = $this->createItem();
        $this->assertEquals(
            array(
                'context',
                'namespaceName',
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
            $function->__sleep()
        );
    }

    /**
     * testSetTokensDelegatesToCacheStoreMethod
     *
     * @return void
     */
    public function testSetTokensDelegatesToCacheStoreMethod()
    {
        $tokens = array(new Token(1, '$foo', 3, 3, 0, 0));

        $cache = $this->createCacheFixture();
        $cache->expects($this->once())
            ->method('type')
            ->will($this->returnValue($cache));
        $cache->expects($this->once())
            ->method('store')
            ->with(self::equalTo(null), self::equalTo($tokens));

        $function = $this->createItem();
        $function->setCache($cache)
            ->setTokens($tokens);
    }

    /**
     * testGetTokensDelegatesToCacheRestoreMethod
     *
     * @return void
     */
    public function testGetTokensDelegatesToCacheRestoreMethod()
    {
        $cache = $this->createCacheFixture();
        $cache->expects($this->once())
            ->method('type')
            ->will($this->returnValue($cache));
        $cache->expects($this->once())
            ->method('restore')
            ->with(self::equalTo(null))
            ->will(self::returnValue(array()));

        $function = $this->createItem();
        $function->setCache($cache)
            ->getTokens();
    }

    /**
     * testGetTokensReturnsArrayEvenWhenCacheReturnsNull
     *
     * @return void
     */
    public function testGetTokensReturnsArrayEvenWhenCacheReturnsNull()
    {
        $cache = $this->createCacheFixture();
        $cache->expects($this->once())
            ->method('type')
            ->will($this->returnValue($cache));
        $cache->expects($this->once())
            ->method('restore')
            ->with(self::equalTo(null))
            ->will(self::returnValue(null));

        $function = $this->createItem();
        $function->setCache($cache);

        $this->assertSame(array(), $function->getTokens());
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTFunction::getFirstChildOfType()}.
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

        $function = $this->createItem();
        $function->addChild($node1);
        $function->addChild($node2);

        $child = $function->getFirstChildOfType(get_class($node2));
        $this->assertSame($node2, $child);
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTFunction::getFirstChildOfType()}.
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

        $function = $this->createItem();
        $function->addChild($node2);
        $function->addChild($node3);

        $child = $function->getFirstChildOfType(get_class($node1));
        $this->assertSame($node1, $child);
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTFunction::getFirstChildOfType()}.
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

        $function = $this->createItem();
        $function->addChild($node1);
        $function->addChild($node2);

        $child = $function->getFirstChildOfType('PDepend\\Class' . md5(microtime()));
        $this->assertNull($child);
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTFunction::findChildrenOfType()}.
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

        $function = $this->createItem();
        $function->addChild($node1);
        $function->addChild($node2);

        $children = $function->findChildrenOfType(get_class($node2));
        $this->assertSame(array($node2), $children);
    }

    /**
     * testUnserializedFunctionStillReferencesSameDependency
     *
     * @return void
     */
    public function testUnserializedFunctionStillReferencesSameDependency()
    {
        $orig = $this->_getFirstFunctionForTestCase();
        $copy = unserialize(serialize($orig));

        $this->assertSame(
            $orig->getDependencies()->current(),
            $copy->getDependencies()->current()
        );
    }

    /**
     * testUnserializedFunctionStillReferencesSameReturnClass
     *
     * @return void
     */
    public function testUnserializedFunctionStillReferencesSameReturnClass()
    {
        $orig = $this->_getFirstFunctionForTestCase();
        $copy = unserialize(serialize($orig));

        $this->assertSame(
            $orig->getReturnClass(),
            $copy->getReturnClass()
        );
    }

    /**
     * testUnserializedFunctionStillReferencesSameParameterClass
     *
     * @return void
     */
    public function testUnserializedFunctionStillReferencesSameParameterClass()
    {
        $orig = $this->_getFirstFunctionForTestCase();
        $copy = unserialize(serialize($orig));

        $this->assertSame(
            $orig->getDependencies()->current(),
            $copy->getDependencies()->current()
        );
    }

    /**
     * testUnserializedFunctionStillReferencesSameExceptionClass
     *
     * @return void
     */
    public function testUnserializedFunctionStillReferencesSameExceptionClass()
    {
        $orig = $this->_getFirstFunctionForTestCase();
        $copy = unserialize(serialize($orig));

        $this->assertSame(
            $orig->getExceptionClasses()->current(),
            $copy->getExceptionClasses()->current()
        );
    }

    /**
     * testUnserializedFunctionStillReferencesSameDependencyInterface
     *
     * @return void
     */
    public function testUnserializedFunctionStillReferencesSameDependencyInterface()
    {
        $orig = $this->_getFirstFunctionForTestCase();
        $copy = unserialize(serialize($orig));

        $this->assertSame(
            $orig->getDependencies()->current(),
            $copy->getDependencies()->current()
        );
    }

    /**
     * testUnserializedFunctionStillReferencesSamePackage
     *
     * @return void
     */
    public function testUnserializedFunctionStillReferencesSamePackage()
    {
        $orig = $this->_getFirstFunctionForTestCase();
        $copy = unserialize(serialize($orig));

        $this->assertSame($orig->getNamespace(), $copy->getNamespace());
    }

    /**
     * testUnserializedFunctionIsInSameNamespace
     *
     * @return void
     */
    public function testUnserializedFunctionIsInSameNamespace()
    {
        $orig = $this->_getFirstFunctionForTestCase();
        $copy = unserialize(serialize($orig));

        $this->assertEquals(
            'Baz',
            $copy->getNamespace()->getClasses()->current()->getName()
        );
    }

    /**
     * testUnserializedFunctionNotAddsDublicateToPackage
     *
     * @return void
     */
    public function testUnserializedFunctionNotAddsDublicateToPackage()
    {
        $orig = $this->_getFirstFunctionForTestCase();
        $copy = unserialize(serialize($orig));

        $this->assertEquals(1, count($copy->getNamespace()->getFunctions()));
    }

    /**
     * testUnserializedFunctionIsChildOfParentPackage
     *
     * @return void
     */
    public function testUnserializedFunctionIsChildOfParentPackage()
    {
        $orig = $this->_getFirstFunctionForTestCase();
        $copy = unserialize(serialize($orig));

        $this->assertSame($copy, $orig->getNamespace()->getFunctions()->current());
    }

    /**
     * Tests the visitor accept method.
     *
     * @return void
     */
    public function testVisitorAccept()
    {
        $function = $this->createItem();
        $visitor  = new StubASTVisitor();

        $function->accept($visitor);
        $this->assertSame($function, $visitor->function);
    }

    /**
     * This method will return the first function instance within the source
     * file of the calling test case.
     *
     * @return ASTFunction
     */
    private function _getFirstFunctionForTestCase()
    {
        return self::parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current();
    }

    /**
     * Creates an abstract item instance.
     *
     * @return \PDepend\Source\AST\AbstractASTArtifact
     */
    protected function createItem()
    {
        $function = new ASTFunction(__FUNCTION__);
        $function->setCompilationUnit(new ASTCompilationUnit(__FILE__));
        $function->setContext($this->getMock('PDepend\\Source\\Builder\\BuilderContext'));

        return $function;
    }
}
