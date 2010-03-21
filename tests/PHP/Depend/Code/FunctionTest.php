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

require_once 'PHP/Depend/Code/Function.php';
require_once 'PHP/Depend/Code/Package.php';

/**
 * Test case implementation for the PHP_Depend_Code_Function class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_Depend_Code_FunctionTest extends PHP_Depend_Code_AbstractItemTest
{
    /**
     * testFreeResetsParentPackageToNull
     *
     * @return void
     * @covers PHP_Depend_Code_Function
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testFreeResetsParentPackageToNull()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
                        ->getFunctions()
                        ->current();
        $function->free();

        $this->assertNull($function->getPackage());
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
        $function = $packages->current()
                        ->getFunctions()
                        ->current();
        $function->free();

        $this->assertEquals(array(), $function->getChildren());
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
        $function = $packages->current()
                        ->getFunctions()
                        ->current();
                        
        $this->assertTrue($function->returnsReference());
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
        $function = $packages->current()
                        ->getFunctions()
                        ->current();
                        
        $this->assertFalse($function->returnsReference());
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
        $function = new PHP_Depend_Code_Function('func');
        $this->assertEquals(array(), $function->getStaticVariables());
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
        $function = $packages->current()
                        ->getFunctions()
                        ->current();
                        
        $this->assertEquals(array('a' => 42, 'b' => 23), $function->getStaticVariables());
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
        $function = $packages->current()
                        ->getFunctions()
                        ->current();
                        
        $this->assertEquals(array('a' => 42, 'b' => 23, 'c' => 17), $function->getStaticVariables());
    }
    
    /**
     * Tests that build interface updates the source file information for null
     * values.
     *
     * @return void
     */
    public function testSetSourceFileInformationForNullValue()
    {
        $item = new PHP_Depend_Code_Function('func');
        $file = new PHP_Depend_Code_File(__FILE__);

        $this->assertNull($item->getSourceFile());
        $item->setSourceFile($file);
        $this->assertSame($file, $item->getSourceFile());
    }
    
    /**
     * Tests the ctor and the {@link PHP_Depend_Code_Function::getName()} method.
     *
     * @return void
     */
    public function testCreateNewFunctionInstance()
    {
        $function = new PHP_Depend_Code_Function('func');
        $this->assertEquals('func', $function->getName());
    }
    
    /**
     * testGetStaticVariablesReturnsMergeOfAllStaticVariables
     *
     * @return void
     * @covers PHP_Depend_Code_Function
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetPackageReturnsNullByDefault()
    {
        $function = new PHP_Depend_Code_Function('func');
        $this->assertNull($function->getPackage());
    }
    
    /**
     * testGetStaticVariablesReturnsMergeOfAllStaticVariables
     *
     * @return void
     * @covers PHP_Depend_Code_Function
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
        $this->assertNull($function->getPackage());
    }
    
    /**
     * Tests that the {@link PHP_Depend_Code_Function::getPackage()} returns as
     * default value <b>null</b> and that the package could be set and unset.
     *
     * @return void
     */
    public function testGetSetPackage()
    {
        $package  = new PHP_Depend_Code_Package('package');
        $function = new PHP_Depend_Code_Function('func');
        
        $function->setPackage($package);
        $this->assertSame($package, $function->getPackage());
    }
    
    /**
     * Tests that {@link PHP_Depend_Code_Function#getTokens()} works as expected.
     * 
     * @return void
     */
    public function testGetTokens()
    {
        $tokens = array(
            new PHP_Depend_Token(1, '$foo', 3, 3, 0, 0),
            new PHP_Depend_Token(2, '=', 3, 3, 0, 0),
            new PHP_Depend_Token(3, '42', 3, 3, 0, 0),
            new PHP_Depend_Token(4, ';', 3, 3, 0, 0),
        );
        
        $function = new PHP_Depend_Code_Function('function1');
        $function->setTokens($tokens);
        
        $this->assertEquals($tokens, $function->getTokens());
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Function::getFirstChildOfType()}.
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

        $function = new PHP_Depend_Code_Function('Method');
        $function->addChild($node1);
        $function->addChild($node2);

        $child = $function->getFirstChildOfType(get_class($node2));
        $this->assertSame($node2, $child);
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Function::getFirstChildOfType()}.
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

        $function = new PHP_Depend_Code_Function('Method');
        $function->addChild($node2);
        $function->addChild($node3);

        $child = $function->getFirstChildOfType(get_class($node1));
        $this->assertSame($node1, $child);
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Function::getFirstChildOfType()}.
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

        $function = new PHP_Depend_Code_Function('Method');
        $function->addChild($node1);
        $function->addChild($node2);

        $child = $function->getFirstChildOfType('PHP_Depend_Code_ASTNodeI_' . md5(microtime()));
        $this->assertNull($child);
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Function::findChildrenOfType()}.
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

        $function = new PHP_Depend_Code_Function('Method');
        $function->addChild($node1);
        $function->addChild($node2);

        $children = $function->findChildrenOfType(get_class($node2));
        $this->assertSame(array($node2), $children);
    }
    
    /**
     * Tests the visitor accept method.
     *
     * @return void
     */
    public function testVisitorAccept()
    {
        $function = new PHP_Depend_Code_Function('func');
        $visitor  = new PHP_Depend_Visitor_TestNodeVisitor();
        
        $function->accept($visitor);
        $this->assertSame($function, $visitor->function);
    }
    
    /**
     * Creates an abstract item instance.
     *
     * @return PHP_Depend_Code_AbstractItem
     */
    protected function createItem()
    {
        return new PHP_Depend_Code_Function('func');
    }
}
