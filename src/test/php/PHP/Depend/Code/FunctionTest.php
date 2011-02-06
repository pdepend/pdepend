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
require_once dirname(__FILE__) . '/../Visitor/TestNodeVisitor.php';

/**
 * Test case implementation for the PHP_Depend_Code_Function class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 *
 * @covers PHP_Depend_Parser
 * @covers PHP_Depend_Code_Function
 * @covers PHP_Depend_Code_AbstractCallable
 */
class PHP_Depend_Code_FunctionTest extends PHP_Depend_Code_AbstractItemTest
{
    /**
     * testFreeResetsParentPackageToNull
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testFreeResetsParentPackageToNull()
    {
        $function = $this->_getFirstFunctionForTestCase();
        $function->free();

        self::assertNull($function->getPackage());
    }

    /**
     * testFreeResetsAllAssociatedASTNodes
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testFreeResetsAllAssociatedASTNodes()
    {
        $function = $this->_getFirstFunctionForTestCase();
        $function->free();

        self::assertEquals(array(), $function->getChildren());
    }

    /**
     * testReturnsReferenceReturnsExpectedTrue
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testReturnsReferenceReturnsExpectedTrue()
    {
        $function = $this->_getFirstFunctionForTestCase();
        self::assertTrue($function->returnsReference());
    }

    /**
     * testReturnsReferenceReturnsExpectedFalse
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testReturnsReferenceReturnsExpectedFalse()
    {
        $function = $this->_getFirstFunctionForTestCase();
        self::assertFalse($function->returnsReference());
    }
    
    /**
     * testGetStaticVariablesReturnsEmptyArrayByDefault
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetStaticVariablesReturnsEmptyArrayByDefault()
    {
        $function = new PHP_Depend_Code_Function('func');
        self::assertEquals(array(), $function->getStaticVariables());
    }
    
    /**
     * testGetStaticVariablesReturnsFirstSetOfStaticVariables
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetStaticVariablesReturnsFirstSetOfStaticVariables()
    {
        $function = $this->_getFirstFunctionForTestCase();

        self::assertEquals(
            array('a' => 42, 'b' => 23),
            $function->getStaticVariables()
        );
    }
    
    /**
     * testGetStaticVariablesReturnsMergeOfAllStaticVariables
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetStaticVariablesReturnsMergeOfAllStaticVariables()
    {
        $function = $this->_getFirstFunctionForTestCase();
                        
        self::assertEquals(
            array('a' => 42, 'b' => 23, 'c' => 17),
            $function->getStaticVariables()
        );
    }
    
    /**
     * Tests that build interface updates the source file information for null
     * values.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testSetSourceFileInformationForNullValue()
    {
        $item = new PHP_Depend_Code_Function('func');
        $file = new PHP_Depend_Code_File(__FILE__);

        self::assertNull($item->getSourceFile());
        $item->setSourceFile($file);
        self::assertSame($file, $item->getSourceFile());
    }
    
    /**
     * Tests the ctor and the {@link PHP_Depend_Code_Function::getName()} method.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testCreateNewFunctionInstance()
    {
        $function = new PHP_Depend_Code_Function('func');
        self::assertEquals('func', $function->getName());
    }
    
    /**
     * testGetStaticVariablesReturnsMergeOfAllStaticVariables
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetPackageReturnsNullByDefault()
    {
        $function = new PHP_Depend_Code_Function('func');
        self::assertNull($function->getPackage());
    }
    
    /**
     * testGetStaticVariablesReturnsMergeOfAllStaticVariables
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testSetPackageWithNullWillResetPreviousPackage()
    {
        $package  = new PHP_Depend_Code_Package('package');
        $function = new PHP_Depend_Code_Function('func');
        
        $function->setPackage($package);
        $function->setPackage(null);
        self::assertNull($function->getPackage());
    }

    /**
     * testSetPackageWithNullWillResetPackageNameProperty
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testSetPackageWithNullWillResetPackageNameProperty()
    {
        $function = new PHP_Depend_Code_Function(__FUNCTION__);
        $function->setPackage(new PHP_Depend_Code_Package(__FUNCTION__));
        $function->setPackage(null);
        self::assertNull($function->getPackage());
    }

    /**
     * testSetPackageNotEstablishesBackReference
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testSetPackageNotEstablishesBackReference()
    {
        $package = $this->getMock(
            PHP_Depend_Code_Package::CLAZZ, 
            array(),
            array(__FUNCTION__)
        );
        $package->expects($this->never())
            ->method('addFunction');

        $function = new PHP_Depend_Code_Function(__FUNCTION__);
        $function->setPackage($package);
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Function::getPackage()} returns as
     * default value <b>null</b> and that the package could be set and unset.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetSetPackage()
    {
        $package  = new PHP_Depend_Code_Package('package');
        $function = new PHP_Depend_Code_Function('func');
        
        $function->setPackage($package);
        self::assertSame($package, $function->getPackage());
    }

    /**
     * testGetPackageNameReturnsNullByDefault
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetPackageNameReturnsNullByDefault()
    {
        $function = $this->createItem();
        self::assertNull($function->getPackageName());
    }

    /**
     * testGetPackageNameReturnsNameOfInjectedPackage
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetPackageNameReturnsNameOfInjectedPackage()
    {
        $function = $this->createItem();
        $function->setPackage(new PHP_Depend_Code_Package(__FUNCTION__));

        self::assertEquals(__FUNCTION__, $function->getPackageName());
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
        $function = $this->createItem();
        self::assertFalse($function->isCached());
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
        $function = $this->createItem();
        serialize($function);

        self::assertFalse($function->isCached());
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
        $function = $this->createItem();
        $function = unserialize(serialize($function));

        self::assertTrue($function->isCached());
    }

    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames()
    {
        $function = $this->createItem();
        self::assertEquals(
            array(
                'context',
                'packageName',
                'cache',
                'uuid',
                'name',
                'startLine',
                'endLine',
                'docComment',
                'returnsReference',
                'returnClassReference',
                'exceptionClassReferences',
                '___temp___'
            ),
            $function->__sleep()
        );
    }

    /**
     * testSetTokensDelegatesToCacheStoreMethod
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testSetTokensDelegatesToCacheStoreMethod()
    {
        $tokens = array(new PHP_Depend_Token(1, '$foo', 3, 3, 0, 0));

        $cache = $this->getMock('PHP_Depend_Util_Cache_Driver');
        $cache->expects($this->once())
            ->method('type')
            ->will($this->returnValue($cache));
        $cache->expects($this->once())
            ->method('store')
            ->with(self::equalTo(null), self::equalTo($tokens));

        $function = new PHP_Depend_Code_Function(__FUNCTION__);
        $function->setCache($cache)
            ->setTokens($tokens);
    }

    /**
     * testGetTokensDelegatesToCacheRestoreMethod
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetTokensDelegatesToCacheRestoreMethod()
    {
        $cache = $this->getMock('PHP_Depend_Util_Cache_Driver');
        $cache->expects($this->once())
            ->method('type')
            ->will($this->returnValue($cache));
        $cache->expects($this->once())
            ->method('restore')
            ->with(self::equalTo(null))
            ->will(self::returnValue(array()));

        $function = new PHP_Depend_Code_Function(__FUNCTION__);
        $function->setCache($cache)
            ->getTokens();
    }

    /**
     * testGetTokensReturnsArrayEvenWhenCacheReturnsNull
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetTokensReturnsArrayEvenWhenCacheReturnsNull()
    {
        $cache = $this->getMock('PHP_Depend_Util_Cache_Driver');
        $cache->expects($this->once())
            ->method('type')
            ->will($this->returnValue($cache));
        $cache->expects($this->once())
            ->method('restore')
            ->with(self::equalTo(null))
            ->will(self::returnValue(null));

        $function = new PHP_Depend_Code_Function(__FUNCTION__);
        $function->setCache($cache);

        self::assertSame(array(), $function->getTokens());
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Function::getFirstChildOfType()}.
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

        $function = new PHP_Depend_Code_Function('Method');
        $function->addChild($node1);
        $function->addChild($node2);

        $child = $function->getFirstChildOfType(get_class($node2));
        self::assertSame($node2, $child);
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Function::getFirstChildOfType()}.
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

        $function = new PHP_Depend_Code_Function('Method');
        $function->addChild($node2);
        $function->addChild($node3);

        $child = $function->getFirstChildOfType(get_class($node1));
        self::assertSame($node1, $child);
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Function::getFirstChildOfType()}.
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

        $function = new PHP_Depend_Code_Function('Method');
        $function->addChild($node1);
        $function->addChild($node2);

        $child = $function->getFirstChildOfType('PHP_Depend_' . md5(microtime()));
        self::assertNull($child);
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Function::findChildrenOfType()}.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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

        $function = new PHP_Depend_Code_Function('Method');
        $function->addChild($node1);
        $function->addChild($node2);

        $children = $function->findChildrenOfType(get_class($node2));
        self::assertSame(array($node2), $children);
    }

    /**
     * testUnserializedFunctionStillReferencesSameDependency
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testUnserializedFunctionStillReferencesSameDependency()
    {
        $orig = $this->_getFirstFunctionForTestCase();
        $copy = unserialize(serialize($orig));

        self::assertSame(
            $orig->getDependencies()->current(),
            $copy->getDependencies()->current()
        );
    }

    /**
     * testUnserializedFunctionStillReferencesSameReturnClass
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testUnserializedFunctionStillReferencesSameReturnClass()
    {
        $orig = $this->_getFirstFunctionForTestCase();
        $copy = unserialize(serialize($orig));

        self::assertSame(
            $orig->getReturnClass(),
            $copy->getReturnClass()
        );
    }

    /**
     * testUnserializedFunctionStillReferencesSameParameterClass
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testUnserializedFunctionStillReferencesSameParameterClass()
    {
        $orig = $this->_getFirstFunctionForTestCase();
        $copy = unserialize(serialize($orig));

        self::assertSame(
            $orig->getDependencies()->current(),
            $copy->getDependencies()->current()
        );
    }

    /**
     * testUnserializedFunctionStillReferencesSameExceptionClass
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testUnserializedFunctionStillReferencesSameExceptionClass()
    {
        $orig = $this->_getFirstFunctionForTestCase();
        $copy = unserialize(serialize($orig));

        self::assertSame(
            $orig->getExceptionClasses()->current(),
            $copy->getExceptionClasses()->current()
        );
    }

    /**
     * testUnserializedFunctionStillReferencesSameDependencyInterface
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testUnserializedFunctionStillReferencesSameDependencyInterface()
    {
        $orig = $this->_getFirstFunctionForTestCase();
        $copy = unserialize(serialize($orig));

        self::assertSame(
            $orig->getDependencies()->current(),
            $copy->getDependencies()->current()
        );
    }

    /**
     * testUnserializedFunctionStillReferencesSamePackage
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testUnserializedFunctionStillReferencesSamePackage()
    {
        $orig = $this->_getFirstFunctionForTestCase();
        $copy = unserialize(serialize($orig));

        self::assertSame($orig->getPackage(), $copy->getPackage());
    }

    /**
     * testUnserializedFunctionIsInSameNamespace
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testUnserializedFunctionIsInSameNamespace()
    {
        $orig = $this->_getFirstFunctionForTestCase();
        $copy = unserialize(serialize($orig));

        self::assertEquals(
            'Baz',
            $copy->getPackage()->getClasses()->current()->getName()
        );
    }

    /**
     * testUnserializedFunctionNotAddsDublicateToPackage
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testUnserializedFunctionNotAddsDublicateToPackage()
    {
        $orig = $this->_getFirstFunctionForTestCase();
        $copy = unserialize(serialize($orig));

        self::assertEquals(1, count($copy->getPackage()->getFunctions()));
    }

    /**
     * testUnserializedFunctionIsChildOfParentPackage
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testUnserializedFunctionIsChildOfParentPackage()
    {
        $orig = $this->_getFirstFunctionForTestCase();
        $copy = unserialize(serialize($orig));

        self::assertSame($copy, $orig->getPackage()->getFunctions()->current());
    }

    /**
     * Tests the visitor accept method.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testVisitorAccept()
    {
        $function = new PHP_Depend_Code_Function('func');
        $visitor  = new PHP_Depend_Visitor_TestNodeVisitor();

        $function->accept($visitor);
        self::assertSame($function, $visitor->function);
    }

    /**
     * This method will return the first function instance within the source
     * file of the calling test case.
     *
     * @return PHP_Depend_Code_Function
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
     * @return PHP_Depend_Code_AbstractItem
     */
    protected function createItem()
    {
        $function = new PHP_Depend_Code_Function(__FUNCTION__);
        $function->setContext($this->getMock('PHP_Depend_Builder_Context'));

        return $function;
    }
}
