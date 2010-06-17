<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008-2010, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

require_once dirname(__FILE__) . '/AbstractItemTest.php';
require_once dirname(__FILE__) . '/../Visitor/TestNodeVisitor.php';

require_once 'PHP/Depend/Code/ASTNodeI.php';
require_once 'PHP/Depend/Code/Method.php';

/**
 * Test case implementation for the PHP_Depend_Code_Method class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_Depend_Code_MethodTest extends PHP_Depend_Code_AbstractItemTest
{
    /**
     * testGetReturnClassForMethodWithNamespacedRootClass
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractCallable
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetReturnClassForMethodWithNamespacedRootClass()
    {
        $method = $this->getFirstMethodInClass(__METHOD__);
        $this->assertEquals('Foo', $method->getReturnClass()->getName());
    }

    /**
     * testGetReturnClassForMethodWithNamespacedClass
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractCallable
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetReturnClassForMethodWithNamespacedClass()
    {
        $method = $this->getFirstMethodInClass(__METHOD__);
        $this->assertEquals('Baz', $method->getReturnClass()->getName());
    }

    /**
     * testGetReturnClassForMethodWithNamespacedArrayRootClass
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractCallable
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetReturnClassForMethodWithNamespacedArrayRootClass()
    {
        $method = $this->getFirstMethodInClass(__METHOD__);
        $this->assertEquals('Foo', $method->getReturnClass()->getName());
    }

    /**
     * testGetReturnClassForMethodWithNamespacedArrayClass
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractCallable
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetReturnClassForMethodWithNamespacedArrayClass()
    {
        $method = $this->getFirstMethodInClass(__METHOD__);
        $this->assertEquals('Baz', $method->getReturnClass()->getName());
    }

    /**
     * testGetExceptionsForMethodWithNamespacedRootClass
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractCallable
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetExceptionsForMethodWithNamespacedRootClass()
    {
        $method = $this->getFirstMethodInClass(__METHOD__);
        $this->assertEquals('Exception', $method->getExceptionClasses()->current()->getName());
    }

    /**
     * testGetExceptionsForMethodWithNamespacedClass
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractCallable
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetExceptionsForMethodWithNamespacedClass()
    {
        $method = $this->getFirstMethodInClass(__METHOD__);
        $this->assertEquals('ErrorException', $method->getExceptionClasses()->current()->getName());
    }

    /**
     * testInlineDependencyForMethodWithNamespacedRootClass
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractCallable
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testInlineDependencyForMethodWithNamespacedRootClass()
    {
        $method = $this->getFirstMethodInClass(__METHOD__);
        $this->assertEquals('ASTBuilder', $method->getDependencies()->current()->getName());
    }

    /**
     * testInlineDependencyForMethodWithNamespacedClass
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractCallable
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testInlineDependencyForMethodWithNamespacedClass()
    {
        $method = $this->getFirstMethodInClass(__METHOD__);
        $this->assertEquals('ASTBuilder', $method->getDependencies()->current()->getName());
    }

    /**
     * testReturnsReferenceReturnsExpectedTrue
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractCallable
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testReturnsReferenceReturnsExpectedTrue()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $method   = $packages->current()
                        ->getClasses()
                        ->current()
                        ->getMethods()
                        ->current();
        
        $this->assertTrue($method->returnsReference());
    }
    
    /**
     * testReturnsReferenceReturnsExpectedFalse
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractCallable
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testReturnsReferenceReturnsExpectedFalse()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $method   = $packages->current()
                        ->getClasses()
                        ->current()
                        ->getMethods()
                        ->current();
                        
        $this->assertFalse($method->returnsReference());
    }
    
    /**
     * testGetStaticVariablesReturnsEmptyArrayByDefault
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractCallable
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetStaticVariablesReturnsEmptyArrayByDefault()
    {
        $method = new PHP_Depend_Code_Method('method');
        $this->assertEquals(array(), $method->getStaticVariables());
    }
    
    /**
     * testGetStaticVariablesReturnsFirstSetOfStaticVariables
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractCallable
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetStaticVariablesReturnsFirstSetOfStaticVariables()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $method   = $packages->current()
                        ->getClasses()
                        ->current()
                        ->getMethods()
                        ->current();
                        
        $this->assertEquals(array('a' => 42, 'b' => 23), $method->getStaticVariables());
    }
    
    /**
     * testGetStaticVariablesReturnsMergeOfAllStaticVariables
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractCallable
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetStaticVariablesReturnsMergeOfAllStaticVariables()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $method   = $packages->current()
                        ->getClasses()
                        ->current()
                        ->getMethods()
                        ->current();
                        
        $this->assertEquals(array('a' => 42, 'b' => 23, 'c' => 17), $method->getStaticVariables());
    }

    /**
     * testByDefaultGetSourceFileReturnsNull
     *
     * @return void
     * @covers PHP_Depend_Code_Method
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testByDefaultGetSourceFileReturnsNull()
    {
        $method = new PHP_Depend_Code_Method('method');
        $this->assertNull($method->getSourceFile());
    }

    /**
     * Tests that build interface updates the source file information for null
     * values.
     *
     * @return void
     * @covers PHP_Depend_Code_Method
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testSetSourceFileInformationForNullValue()
    {
        $method = new PHP_Depend_Code_Method('method');
        $file   = new PHP_Depend_Code_File(__FILE__);
        
        $method->setSourceFile($file);
        $this->assertSame($file, $method->getSourceFile());
    }
    
    /**
     * testByDefaultGetParentReturnsNull
     *
     * @return void
     * @covers PHP_Depend_Code_Method
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testByDefaultGetParentReturnsNull()
    {
        $method = new PHP_Depend_Code_Method('method');
        $this->assertNull($method->getParent());
    }
        
    /**
     * testSetParentWithNullResetsPreviousParentToNull
     *
     * @return void
     * @covers PHP_Depend_Code_Method
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testSetParentWithNullResetsPreviousParentToNull()
    {
        $class  = new PHP_Depend_Code_Class('clazz', 0, 'clazz.php');
        $method = new PHP_Depend_Code_Method('method');

        $method->setParent($class);
        $method->setParent(null);
        $this->assertNull($method->getParent());
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Method::getParent()} returns as
     * default value <b>null</b> and that the package could be set and unset.
     *
     * @return void
     * @covers PHP_Depend_Code_Method
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetSetParent()
    {
        $class  = new PHP_Depend_Code_Class('clazz', 0, 'clazz.php');
        $method = new PHP_Depend_Code_Method('method');

        $method->setParent($class);
        $this->assertSame($class, $method->getParent());
    }
    
    /**
     * Tests the visitor accept method.
     *
     * @return void
     * @covers PHP_Depend_Code_Method
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testVisitorAccept()
    {
        $method  = new PHP_Depend_Code_Method('method', 0);
        $visitor = new PHP_Depend_Visitor_TestNodeVisitor();
        
        $method->accept($visitor);
        $this->assertSame($method, $visitor->method);
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Method::setModifiers()} method
     * fails with an exception for an invalid modifier value.
     *
     * @return void
     * @covers PHP_Depend_Code_Method
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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
     * @covers PHP_Depend_Code_Method
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testSetModifiersAcceptsPublicValue()
    {
        $method = new PHP_Depend_Code_Method('method');
        $method->setModifiers(PHP_Depend_ConstantsI::IS_PUBLIC);
        $this->assertTrue($method->isPublic() 
                      && !$method->isProtected() 
                      && !$method->isPrivate());
    }
    
    /**
     * testIsStaticDefaultByReturnsFalse
     *
     * @return void
     * @covers PHP_Depend_Code_Method
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testIsStaticDefaultByReturnsFalse()
    {
        $method = new PHP_Depend_Code_Method('method');
        $this->assertFalse($method->isStatic());
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Method::setModifiers()} method marks
     * a method as static.
     *
     * @return void
     * @covers PHP_Depend_Code_Method
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testSetModifiersMarksMethodAsStatic()
    {
        $method = new PHP_Depend_Code_Method('method');

        $method->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED
                            | PHP_Depend_ConstantsI::IS_STATIC);
        $this->assertTrue($method->isStatic());
    }
    
    /**
     * testIsFinalByDefaultReturnsFalse
     *
     * @return void
     * @covers PHP_Depend_Code_Method
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testIsFinalByDefaultReturnsFalse()
    {
        $method = new PHP_Depend_Code_Method('method');
        $this->assertFalse($method->isFinal());
    }

    /**
     * Tests that the {@link PHP_Depend_Code_Method::setModifiers()} method marks
     * a method as final.
     *
     * @return void
     * @covers PHP_Depend_Code_Method
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testSetModifiersMarksMethodAsFinal()
    {
        $method = new PHP_Depend_Code_Method('method');

        $method->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED
                            | PHP_Depend_ConstantsI::IS_FINAL);
        $this->assertTrue($method->isFinal());
    }

    /**
     * Tests that the {@link PHP_Depend_Code_Method::setModifiers()} method marks
     * a method as static+final.
     *
     * @return void
     * @covers PHP_Depend_Code_Method
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testSetModifiersMarksMethodAsStaticFinal()
    {
        $method = new PHP_Depend_Code_Method('method');
        $method->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED
                            | PHP_Depend_ConstantsI::IS_STATIC
                            | PHP_Depend_ConstantsI::IS_FINAL);
        $this->assertTrue($method->isFinal() && $method->isStatic());
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Method::setModifiers()} method
     * accepts the defined visibility value.
     *
     * @return void
     * @covers PHP_Depend_Code_Method
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testSetModifiersAcceptsProtectedValue()
    {
        $method = new PHP_Depend_Code_Method('method');
        $method->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED);
        $this->assertTrue($method->isProtected() 
                      && !$method->isPublic()
                      && !$method->isPrivate());
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Method::setModifiers()} method
     * accepts the defined visibility value.
     *
     * @return void
     * @covers PHP_Depend_Code_Method
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testSetModifiersAcceptsPrivateValue()
    {
        $method = new PHP_Depend_Code_Method('method');
        $method->setModifiers(PHP_Depend_ConstantsI::IS_PRIVATE);
        $this->assertTrue($method->isPrivate()
                      && !$method->isPublic()
                      && !$method->isProtected());
    }
    
    /**
     * testIsPublicByDefaultReturnsFalse
     *
     * @return void
     * @covers PHP_Depend_Code_Method
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testIsPublicByDefaultReturnsFalse()
    {
        $method = new PHP_Depend_Code_Method('method');
        $this->assertFalse($method->isPublic());    
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Method::setModifiers()} method
     * ignores repeated calls if the internal value is set.
     *
     * @return void
     * @covers PHP_Depend_Code_Method
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testSetModifiersOnlyAcceptsTheFirstValue()
    {
        $method = new PHP_Depend_Code_Method('method');
        $method->setModifiers(PHP_Depend_ConstantsI::IS_PUBLIC);
        $method->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED);
        $this->assertTrue($method->isPublic());
    }

    /**
     * testtestFreeResetsParentClassToNull
     *
     * @return void
     * @covers PHP_Depend_Code_Method
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testFreeResetsParentClassToNull()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $method   = $packages->current()
                        ->getClasses()
                        ->current()
                        ->getMethods()
                        ->current();
        $method->free();

        $this->assertNull($method->getParent());
    }

    /**
     * testFreeResetsAllAssociatedASTNodes
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractCallable
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testFreeResetsAllAssociatedASTNodes()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $method   = $packages->current()
                        ->getClasses()
                        ->current()
                        ->getMethods()
                        ->current();
        $method->free();

        $this->assertEquals(array(), $method->getChildren());
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Method::getFirstChildOfType()}.
     *
     * @return void
     * @covers PHP_Depend_Code_Method
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

        $method = new PHP_Depend_Code_Method('Method');
        $method->addChild($node1);
        $method->addChild($node2);

        $child = $method->getFirstChildOfType(get_class($node2));
        $this->assertSame($node2, $child);
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Method::getFirstChildOfType()}.
     *
     * @return void
     * @covers PHP_Depend_Code_Method
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

        $method = new PHP_Depend_Code_Method('Method');
        $method->addChild($node2);
        $method->addChild($node3);

        $child = $method->getFirstChildOfType(get_class($node1));
        $this->assertSame($node1, $child);
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Method::getFirstChildOfType()}.
     *
     * @return void
     * @covers PHP_Depend_Code_Method
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

        $method = new PHP_Depend_Code_Method('Method');
        $method->addChild($node1);
        $method->addChild($node2);

        $child = $method->getFirstChildOfType('PHP_Depend_Code_ASTNodeI_' . md5(microtime()));
        $this->assertNull($child);
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Method::findChildrenOfType()}.
     *
     * @return void
     * @covers PHP_Depend_Code_Method
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

        $method = new PHP_Depend_Code_Method('Method');
        $method->addChild($node1);
        $method->addChild($node2);

        $children = $method->findChildrenOfType(get_class($node2));
        $this->assertSame(array($node2), $children);
    }

    /**
     * Returns the first method defined in a source file associated with the
     * given test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_Method
     */
    protected function getFirstMethodInClass($testCase)
    {
        return self::parseTestCaseSource($testCase)
            ->current()
            ->getClasses()
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
        return new PHP_Depend_Code_Method('method');
    }
}