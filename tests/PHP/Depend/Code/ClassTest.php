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
 * Test case implementation for the PHP_Depend_Code_Class class.
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
 * @covers PHP_Depend_Code_Class
 */
class PHP_Depend_Code_ClassTest extends PHP_Depend_Code_AbstractItemTest
{
    /**
     * testGetAllMethodsContainsMethodsOfImplementedInterface
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetAllMethodsContainsMethodsOfImplementedInterface()
    {
        $class  = $this->getFirstClassForTestCase();
        $actual = array_keys($class->getAllMethods());
        sort($actual);

        self::assertEquals(array('bar', 'baz', 'foo'), $actual);
    }

    /**
     * testGetAllMethodsContainsMethodsOfImplementedInterfaces
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetAllMethodsContainsMethodsOfImplementedInterfaces()
    {
        $class  = $this->getFirstClassForTestCase();
        $actual = array_keys($class->getAllMethods());
        sort($actual);

        self::assertEquals(array('bar', 'baz', 'foo'), $actual);
    }

    /**
     * testGetAllMethodsContainsMethodsOfIndirectImplementedInterfaces
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetAllMethodsContainsMethodsOfIndirectImplementedInterfaces()
    {
        $class  = $this->getFirstClassForTestCase();
        $actual = array_keys($class->getAllMethods());
        sort($actual);

        self::assertEquals(array('bar', 'baz', 'foo'), $actual);
    }

    /**
     * testGetAllMethodsContainsMethodsOfParentClass
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetAllMethodsContainsMethodsOfParentClass()
    {
        $class  = $this->getFirstClassForTestCase();
        $actual = array_keys($class->getAllMethods());
        sort($actual);

        self::assertEquals(array('bar', 'baz', 'foo'), $actual);
    }

    /**
     * testGetAllMethodsContainsMethodsOfParentClasses
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetAllMethodsContainsMethodsOfParentClasses()
    {
        $class  = $this->getFirstClassForTestCase();
        $actual = array_keys($class->getAllMethods());
        sort($actual);

        self::assertEquals(array('bar', 'baz', 'foo'), $actual);
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Method::getFirstChildOfType()}.
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
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

        $class = new PHP_Depend_Code_Class('Clazz');
        $class->addChild($node1);
        $class->addChild($node2);

        $child = $class->getFirstChildOfType(get_class($node2));
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

        $class = new PHP_Depend_Code_Class('Clazz');
        $class->addChild($node2);
        $class->addChild($node3);

        $child = $class->getFirstChildOfType(get_class($node1));
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

        $class = new PHP_Depend_Code_Class('Clazz');
        $class->addChild($node1);
        $class->addChild($node2);

        $child = $class->getFirstChildOfType(
            'PHP_Depend_Code_ASTNodeI_' . md5(microtime())
        );
        $this->assertNull($child);
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
        $class  = $this->getFirstClassForTestCase();
        $params = $class->getFirstChildOfType(
            PHP_Depend_Code_ASTFormalParameter::CLAZZ
        );

        self::assertType(PHP_Depend_Code_ASTFormalParameter::CLAZZ, $params);
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
        $class  = $this->getFirstClassForTestCase();
        $params = $class->findChildrenOfType(
            PHP_Depend_Code_ASTFormalParameter::CLAZZ
        );
        
        self::assertEquals(4, count($params));
    }

    /**
     * testFindChildrenOfTypeFindsASTNodesFromVariousCodeItems
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testFindChildrenOfTypeFindsASTNodesFromVariousCodeItems()
    {
        $class  = $this->getFirstClassForTestCase();
        $params = $class->findChildrenOfType(
            PHP_Depend_Code_ASTVariableDeclarator::CLAZZ
        );
        
        self::assertEquals(2, count($params));
    }

    /**
     * testUnserializedClassStillIsParentOfChildMethods
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testUnserializedClassStillIsParentOfChildMethods()
    {
        $orig = $this->getFirstClassForTestCase();
        $copy = unserialize(serialize($orig));

        self::assertSame($copy, $copy->getMethods()->current()->getParent());
    }

    /**
     * testUnserializedClassAndChildMethodsStillReferenceTheSameFile
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testUnserializedClassAndChildMethodsStillReferenceTheSameFile()
    {
        $orig = $this->getFirstClassForTestCase();
        $copy = unserialize(serialize($orig));

        self::assertSame(
            $copy->getSourceFile(),
            $copy->getMethods()->current()->getSourceFile()
        );
    }

    /**
     * testUnserializedClassStillReferencesSameParentClass
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testUnserializedClassStillReferencesSameParentClass()
    {
        $orig = $this->getFirstClassForTestCase();
        $copy = unserialize(serialize($orig));

        self::assertSame(
            $orig->getParentClass(),
            $copy->getParentClass()
        );
    }

    /**
     * testUnserializedClassStillReferencesSameParentInterface
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testUnserializedClassStillReferencesSameParentInterface()
    {
        $orig = $this->getFirstClassForTestCase();
        $copy = unserialize(serialize($orig));

        self::assertSame(
            $orig->getInterfaces()->current(),
            $copy->getInterfaces()->current()
        );
    }

    /**
     * testUnserializedClassIsReturnedByMethodAsReturnClass
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testUnserializedClassIsReturnedByMethodAsReturnClass()
    {
        $orig   = $this->getFirstClassForTestCase();
        $method = $orig->getMethods()->current();

        $copy = unserialize(serialize($orig));
        
        self::assertSame(
            $method->getReturnClass(),
            $copy
        );
    }

    /**
     * testUnserializedClassStillReferencesSamePackage
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testUnserializedClassStillReferencesSamePackage()
    {
        $orig = $this->getFirstClassForTestCase();
        $copy = unserialize(serialize($orig));

        self::assertSame(
            $orig->getPackage(),
            $copy->getPackage()
        );
    }

    /**
     * testUnserializedClassRegistersToPackage
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testUnserializedClassRegistersToPackage()
    {
        $orig = $this->getFirstClassForTestCase();
        $copy = unserialize(serialize($orig));

        self::assertSame($copy, $orig->getPackage()->getClasses()->current());
    }

    /**
     * testUnserializedClassNotAddsDublicateClassToPackage
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testUnserializedClassNotAddsDublicateClassToPackage()
    {
        $orig = $this->getFirstClassForTestCase();
        $copy = unserialize(serialize($orig));

        self::assertEquals(1, $orig->getPackage()->getClasses()->count());
    }

    /**
     * Tests the ctor and the {@link PHP_Depend_Code_Class::getName()}.
     * 
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testCreateNewClassInstance()
    {
        $class = new PHP_Depend_Code_Class(__CLASS__);
        self::assertEquals(__CLASS__, $class->getName());
    }

    /**
     * testIsAbstractReturnsFalseByDefault
     *
     * @return void
     * @covers PHP_Depend_Code_AbstractClassOrInterface
     * @covers PHP_Depend_Code_Class
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testIsAbstractReturnsFalseByDefault()
    {
        $class = new PHP_Depend_Code_Class(__CLASS__);
        self::assertFalse($class->isAbstract());
    }
    
    /**
     * testMarkClassInstanceAsAbstract
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testMarkClassInstanceAsAbstract()
    {
        $class = new PHP_Depend_Code_Class(__CLASS__);
        $class->setModifiers(PHP_Depend_ConstantsI::IS_EXPLICIT_ABSTRACT);
        
        self::assertTrue($class->isAbstract());
    }

    /**
     * testIsFinalReturnsFalseByDefault
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testIsFinalReturnsFalseByDefault()
    {
        $class = new PHP_Depend_Code_Class(__CLASS__);
        self::assertFalse($class->isFinal());
    }

    /**
     * testMarkClassInstanceAsFinal
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testMarkClassInstanceAsFinal()
    {
        $class = new PHP_Depend_Code_Class(__CLASS__);
        $class->setModifiers(PHP_Depend_ConstantsI::IS_FINAL);

        self::assertTrue($class->isFinal());
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Class::setModifiers()} when
     * it is called with an invalid modifier.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     * @expectedException InvalidArgumentException
     */
    public function testSetModifiersThrowsExpectedExceptionForInvalidModifier()
    {
        $class = new PHP_Depend_Code_Class(__CLASS__);
        $class->setModifiers(
            PHP_Depend_ConstantsI::IS_ABSTRACT |
            PHP_Depend_ConstantsI::IS_FINAL
        );
    }
    
    /**
     * Tests that a new {@link PHP_Depend_Code_Class} object returns an empty
     * {@link PHP_Depend_Code_NodeIterator} instance for methods.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetMethodsNodeIteratorIsEmptyByDefault()
    {
        $class = new PHP_Depend_Code_Class(__CLASS__);
        self::assertEquals(0, $class->getMethods()->count());
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Class::addMethod()} method adds a
     * method to the internal list and sets the context class as parent.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testAddMethodStoresNewlyAddedMethodInCollection()
    {
        $class  = new PHP_Depend_Code_Class(__CLASS__);
        $method = $class->addMethod(new PHP_Depend_Code_Method(__FUNCTION__));

        self::assertEquals(1, $class->getMethods()->count());
    }

    /**
     * testAddMethodSetsParentOfNewlyAddedMethod
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testAddMethodSetsParentOfNewlyAddedMethod()
    {
        $class  = new PHP_Depend_Code_Class(__CLASS__);
        $method = $class->addMethod(new PHP_Depend_Code_Method(__FUNCTION__));
        
        self::assertSame($class, $method->getParent());
    }

    /**
     * testGetPackageReturnsNullByDefault
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetPackageReturnsNullByDefault()
    {
        $class = new PHP_Depend_Code_Class(__CLASS__);
        self::assertNull($class->getPackage());
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Class::getPackage()} returns as
     * default value <b>null</b> and that the package could be set and unset.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetSetPackage()
    {
        $package = new PHP_Depend_Code_Package(__FUNCTION__);
        $class   = new PHP_Depend_Code_Class(__CLASS__);
        
        $class->setPackage($package);
        self::assertSame($package, $class->getPackage());
    }

    /**
     * testSetPackageAcceptsNullAndResetsPackageReference
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testSetPackageAcceptsNullAndResetsPackageReference()
    {
        $class = new PHP_Depend_Code_Class(__CLASS__);

        $class->setPackage(new PHP_Depend_Code_Package(__FUNCTION__));
        $class->setPackage(null);
        self::assertNull($class->getPackage());
    }
    
    /**
     * Tests that {@link PHP_Depend_Code_Class::getInterfaces()}
     * returns the expected result.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetInterfaces()
    {
        $class = $this->getFirstClassForTestCase();

        $actual = array();
        foreach ($class->getInterfaces() as $interface) {
            $actual[] = $interface->getName();
        }
        sort($actual);

        self::assertEquals(array('A', 'C', 'E', 'F'), $actual);
    }
    
    /**
     * Tests that {@link PHP_Depend_Code_Class::getInterfaces()}
     * returns the expected result.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetInterfacesByInheritence()
    {
        $classes = self::parseCodeResourceForTest()
            ->current()
            ->getClasses();

        $classes->next();
        $class = $classes->current();

        $actual = array();
        foreach ($class->getInterfaces() as $interface) {
            $actual[$interface->getName()] = $interface->getName();
        }
        sort($actual);

        self::assertEquals(array('A', 'B', 'C', 'D', 'E', 'F'), $actual);
    }

    /**
     * Tests that {@link PHP_Depend_Code_Class::getInterfaces()}
     * returns the expected result.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetInterfacesByClassInheritence()
    {
        $class = $this->getFirstClassForTestCase();

        $actual = array();
        foreach ($class->getInterfaces() as $interface) {
            $actual[] = $interface->getName();
        }
        sort($actual);

        self::assertEquals(array('A', 'B'), $actual);
    }
    
    /**
     * Checks the {@link PHP_Depend_Code_Class::isSubtypeOf()} method.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testIsSubtypeInInheritanceHierarchy()
    {
        $types = self::parseCodeResourceForTest()
            ->current()
            ->getTypes();
        
        $class = $types->current();

        $actual = array();
        foreach ($types as $type) {
            $actual[$type->getName()] = $class->isSubtypeOf($type);
        }
        ksort($actual);

        $expected = array(
            'A' => true,
            'B' => false,
            'C' => false,
            'D' => true,
            'E' => true,
            'F' => false
        );

        self::assertEquals($expected, $actual);
    }

    /**
     * Checks the {@link PHP_Depend_Code_Class::isSubtypeOf()} method.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testIsSubtypeInClassInheritanceHierarchy()
    {
        $types = self::parseCodeResourceForTest()
            ->current()
            ->getTypes();

        $class = $types->current();

        $actual = array();
        foreach ($types as $type) {
            $actual[$type->getName()] = $class->isSubtypeOf($type);
        }
        ksort($actual);

        $expected = array(
            'A' => true,
            'B' => true,
            'C' => false,
            'D' => true,
            'E' => true,
            'F' => false
        );

        self::assertEquals($expected, $actual);
    }

    /**
     * Checks the {@link PHP_Depend_Code_Class::isSubtypeOf()} method.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testIsSubtypeInClassAndInterfaceInheritanceHierarchy()
    {
        $types = self::parseCodeResourceForTest()
            ->current()
            ->getTypes();

        $class = $types->current();

        $actual = array();
        foreach ($types as $type) {
            $actual[$type->getName()] = $class->isSubtypeOf($type);
        }
        ksort($actual);

        $expected = array(
            'A' => true,
            'B' => true,
            'C' => true,
            'D' => true,
            'E' => true,
            'F' => true
        );

        self::assertEquals($expected, $actual);
    }

    /**
     * testGetPropertiesReturnsExpectedNumberOfProperties
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetPropertiesReturnsExpectedNumberOfProperties()
    {
        $class = $this->getFirstClassForTestCase();
        self::assertEquals(6, count($class->getProperties()));
    }

    /**
     * testFreeResetsAllAssociatedProperties
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testFreeResetsAllAssociatedProperties()
    {
        $class = $this->getFirstClassForTestCase();
        $class->free();

        self::assertEquals(0, $class->getProperties()->count());
    }

    /**
     * testFreeResetsAllAssociatedParentInterfaces
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testFreeResetsAllAssociatedParentInterfaces()
    {
        $class = $this->getFirstClassForTestCase();
        $class->free();

        self::assertEquals(0, $class->getInterfaces()->count());
    }

    /**
     * testFreeResetsAllAssociatedClassMethods
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testFreeResetsAllAssociatedClassMethods()
    {
        $class = $this->getFirstClassForTestCase();
        $class->free();

        self::assertEquals(0, $class->getMethods()->count());
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
        $class = $this->getFirstClassForTestCase();
        $class->free();

        self::assertEquals(array(), $class->getChildren());
    }

    /**
     * Tests that it is not possible to overwrite previously set class modifiers.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     * @expectedException BadMethodCallException
     */
    public function testSetModifiersThrowsExpectedExceptionOnOverwrite()
    {
        $class = new PHP_Depend_Code_Class(__CLASS__);
        $class->setModifiers(PHP_Depend_ConstantsI::IS_FINAL);
        $class->setModifiers(PHP_Depend_ConstantsI::IS_FINAL);
    }

    /**
     * testGetModifiersReturnsZeroByDefault
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetModifiersReturnsZeroByDefault()
    {
        $class = new PHP_Depend_Code_Class(__CLASS__);
        self::assertSame(0, $class->getModifiers());
    }

    /**
     * testGetModifiersReturnsInjectedModifierValue
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetModifiersReturnsInjectedModifierValue()
    {
        $class = new PHP_Depend_Code_Class(__CLASS__);
        $class->setModifiers(PHP_Depend_ConstantsI::IS_FINAL);

        self::assertSame(PHP_Depend_ConstantsI::IS_FINAL, $class->getModifiers());
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
        $class   = new PHP_Depend_Code_Class(__CLASS__);
        $visitor = new PHP_Depend_Visitor_TestNodeVisitor();

        $class->accept($visitor);
        self::assertSame($class, $visitor->class);
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

        $class = new PHP_Depend_Code_Class(__CLASS__);
        $class->setCache($cache)
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

        $class = new PHP_Depend_Code_Class(__CLASS__);
        $class->setCache($cache)
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
        $class = new PHP_Depend_Code_Class(__CLASS__);
        self::assertSame(0, $class->getStartLine());
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

        $class = new PHP_Depend_Code_Class(__CLASS__);
        $class->setCache($cache)
            ->setTokens(
                array(
                    new PHP_Depend_Token(1, 'a', 23, 42, 0, 0),
                    new PHP_Depend_Token(2, 'b', 17, 32, 0, 0),
                )
            );

        self::assertEquals(23, $class->getStartLine());
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
        $class = new PHP_Depend_Code_Class(__CLASS__);
        self::assertSame(0, $class->getEndLine());
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

        $class = new PHP_Depend_Code_Class(__CLASS__);
        $class->setCache($cache)
            ->setTokens(
                array(
                    new PHP_Depend_Token(1, 'a', 23, 42, 0, 0),
                    new PHP_Depend_Token(2, 'b', 17, 32, 0, 0),
                )
            );

        self::assertEquals(32, $class->getEndLine());
    }

    /**
     * testGetParentClassReferenceReturnsNullByDefault
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetParentClassReferenceReturnsNullByDefault()
    {
        $class = new PHP_Depend_Code_Class(__CLASS__);
        self::assertNull($class->getParentClassReference());
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
        $class = new PHP_Depend_Code_Class(__CLASS__);
        self::assertFalse($class->isUserDefined());
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
        $class = $this->createItem();
        $class->setUserDefined();

        self::assertTrue($class->isUserDefined());
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
        $class = $this->createItem();
        self::assertFalse($class->isCached());
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
        $class = $this->createItem();
        serialize($class);

        self::assertFalse($class->isCached());
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
        $class = $this->createItem();
        $class = unserialize(serialize($class));

        self::assertTrue($class->isCached());
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
        $class = new PHP_Depend_Code_Class(__CLASS__);
        $class->setPackage(new PHP_Depend_Code_Package(__FUNCTION__));
        
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
            $class->__sleep()
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
        $class = new PHP_Depend_Code_Class(__CLASS__);

        $method = new PHP_Depend_Code_Method(__FUNCTION__);
        $class->addMethod($method);
        $class->setContext($this->getMock('PHP_Depend_Builder_Context'));

        $file = new PHP_Depend_Code_File(__FILE__);
        $class->setSourceFile($file);
        $class->__wakeup();

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
        $class  = new PHP_Depend_Code_Class(__CLASS__);
        $method = new PHP_Depend_Code_Method(__FUNCTION__);

        $class->addMethod($method);
        $class->setContext($this->getMock('PHP_Depend_Builder_Context'));
        $method->setParent(null);
        $class->__wakeup();

        self::assertSame($class, $method->getParent());
    }

    /**
     * testMagicWakeupCallsRegisterClassOnBuilderContext
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testMagicWakeupCallsRegisterClassOnBuilderContext()
    {
        $class = new PHP_Depend_Code_Class(__CLASS__);

        $context = $this->getMock('PHP_Depend_Builder_Context');
        $context->expects($this->once())
            ->method('registerClass')
            ->with(self::isInstanceOf(PHP_Depend_Code_Class::CLAZZ));

        $class->setContext($context)->__wakeup();
    }

    /**
     * Returns the first class that could be found in the source file associated
     * with the calling test case.
     *
     * @return PHP_Depend_Code_Class
     */
    protected function getFirstClassForTestCase()
    {
        return self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current();
    }
    
    /**
     * Creates an abstract item instance.
     *
     * @return PHP_Depend_Code_AbstractItem
     */
    protected function createItem()
    {
        $class = new PHP_Depend_Code_Class(__CLASS__);
        $class->setContext($this->getMock('PHP_Depend_Builder_Context'));

        return $class;
    }
}
