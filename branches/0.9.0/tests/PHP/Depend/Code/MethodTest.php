<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';
require_once dirname(__FILE__) . '/../Visitor/TestNodeVisitor.php';

require_once 'PHP/Depend/Code/ASTNodeI.php';
require_once 'PHP/Depend/Code/Method.php';

/**
 * Test case implementation for the PHP_Depend_Code_Method class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_Depend_Code_MethodTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that build interface updates the source file information for null
     * values.
     *
     * @return void
     */
    public function testSetSourceFileInformationForNullValue()
    {
        $item = new PHP_Depend_Code_Method('method');
        $file = new PHP_Depend_Code_File(__FILE__);

        $this->assertNull($item->getSourceFile());
        $item->setSourceFile($file);
        $this->assertSame($file, $item->getSourceFile());
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
        $method = new PHP_Depend_Code_Method('method', 0);
        
        $this->assertNull($method->getParent());
        $method->setParent($class);
        $this->assertSame($class, $method->getParent());
        $method->setParent(null);
        $this->assertNull($method->getParent());
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
        
        $this->assertNull($visitor->method);
        $method->accept($visitor);
        $this->assertSame($method, $visitor->method);
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
        $this->assertTrue($method->isPublic());
        $this->assertFalse($method->isProtected());
        $this->assertFalse($method->isPrivate());
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
        $this->assertFalse($method->isStatic());

        $method->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED
                            | PHP_Depend_ConstantsI::IS_STATIC);
        $this->assertTrue($method->isStatic());
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
        $this->assertFalse($method->isFinal());

        $method->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED
                            | PHP_Depend_ConstantsI::IS_FINAL);
        $this->assertTrue($method->isFinal());
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
        $this->assertFalse($method->isFinal() || $method->isStatic());

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
     */
    public function testSetModifiersAcceptsProtectedValue()
    {
        $method = new PHP_Depend_Code_Method('method');
        $method->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED);
        $this->assertTrue($method->isProtected());
        $this->assertFalse($method->isPublic());
        $this->assertFalse($method->isPrivate());
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
        $this->assertTrue($method->isPrivate());
        $this->assertFalse($method->isPublic());
        $this->assertFalse($method->isProtected());
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Method::setModifiers()} method
     * ignores repeated calls if the internal value is set.
     *
     * @return void
     */
    public function testSetModifiersOnlyAcceptsTheFirstValue()
    {
        $method = new PHP_Depend_Code_Method('method');
        $this->assertFalse($method->isPublic());
        $method->setModifiers(PHP_Depend_ConstantsI::IS_PUBLIC);
        $this->assertTrue($method->isPublic());
        $method->setModifiers(PHP_Depend_ConstantsI::IS_PROTECTED);
        $this->assertTrue($method->isPublic());
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
}