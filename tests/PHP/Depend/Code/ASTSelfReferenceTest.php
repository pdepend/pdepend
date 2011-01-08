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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

require_once 'PHP/Depend/Code/ASTSelfReference.php';
require_once 'PHP/Depend/Code/Class.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTSelfReference} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 *
 * @covers PHP_Depend_Parser
 * @covers PHP_Depend_Builder_Default
 * @covers PHP_Depend_Code_ASTSelfReference
 */
class PHP_Depend_Code_ASTSelfReferenceTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testGetTypeReturnsInjectedConstructorTargetArgument
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testGetTypeReturnsInjectedConstructorTargetArgument()
    {
        $target  = $this->getMockForAbstractClass('PHP_Depend_Code_AbstractClassOrInterface', array(__CLASS__));
        $context = $this->getMock('PHP_Depend_Builder_Context');

        $reference = new PHP_Depend_Code_ASTSelfReference($context, $target);
        self::assertSame($target, $reference->getType());
    }

    /**
     * testGetTypeInvokesBuilderContextWhenTypeInstanceIsNull
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testGetTypeInvokesBuilderContextWhenTypeInstanceIsNull()
    {
        $target = $this->getMockForAbstractClass('PHP_Depend_Code_AbstractClassOrInterface', array(__CLASS__));

        $builder = $this->getMock('PHP_Depend_BuilderI');
        $builder->expects($this->once())
            ->method('getClassOrInterface');

        $context = new PHP_Depend_Builder_Context_GlobalStatic($builder);

        $reference = new PHP_Depend_Code_ASTSelfReference($context, $target);
        $reference = unserialize(serialize($reference));
        $reference->getType();
    }

    /**
     * testAcceptInvokesVisitOnGivenVisitor
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptInvokesVisitOnGivenVisitor()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitSelfReference'));

        $node = $this->createNodeInstance();
        $node->accept($visitor);
    }

    /**
     * testAcceptReturnsReturnValueOfVisitMethod
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptReturnsReturnValueOfVisitMethod()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitSelfReference'))
            ->will($this->returnValue(42));

        $node = $this->createNodeInstance();
        self::assertEquals(42, $node->accept($visitor));
    }

    /**
     * testSelfReferenceAllocationOutsideOfClassScopeThrowsExpectedException
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     * @expectedException PHP_Depend_Parser_InvalidStateException
     */
    public function testSelfReferenceAllocationOutsideOfClassScopeThrowsExpectedException()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testSelfReferenceMemberPrimaryPrefixOutsideOfClassScopeThrowsExpectedException
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     * @expectedException PHP_Depend_Parser_InvalidStateException
     */
    public function testSelfReferenceMemberPrimaryPrefixOutsideOfClassScopeThrowsExpectedException()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testMagicSelfReturnsExpectedSetOfPropertyNames
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testMagicSelfReturnsExpectedSetOfPropertyNames()
    {
        $reference = $this->createNodeInstance();
        self::assertEquals(
            array(
                'qualifiedName',
                'context',
                'image',
                'comment',
                'startLine',
                'startColumn',
                'endLine',
                'endColumn',
                'nodes'
            ),
            $reference->__sleep()
        );
    }

    /**
     * testSelfReferenceHasExpectedStartLine
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testSelfReferenceHasExpectedStartLine()
    {
        $ref = $this->_getFirstSelfReferenceInClass(__METHOD__);
        $this->assertEquals(5, $ref->getStartLine());
    }

    /**
     * testSelfReferenceHasExpectedStartColumn
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testSelfReferenceHasExpectedStartColumn()
    {
        $ref = $this->_getFirstSelfReferenceInClass(__METHOD__);
        $this->assertEquals(13, $ref->getStartColumn());
    }

    /**
     * testSelfReferenceHasExpectedEndLine
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testSelfReferenceHasExpectedEndLine()
    {
        $ref = $this->_getFirstSelfReferenceInClass(__METHOD__);
        $this->assertEquals(5, $ref->getEndLine());
    }

    /**
     * testSelfReferenceHasExpectedEndColumn
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testSelfReferenceHasExpectedEndColumn()
    {
        $ref = $this->_getFirstSelfReferenceInClass(__METHOD__);
        $this->assertEquals(16, $ref->getEndColumn());
    }

    /**
     * Creates a concrete node implementation.
     *
     * @return PHP_Depend_Code_ASTSelfReference
     */
    protected function createNodeInstance()
    {
        return new PHP_Depend_Code_ASTSelfReference(
            $this->getMock('PHP_Depend_Builder_Context'),
            $this->getMockForAbstractClass('PHP_Depend_Code_AbstractClassOrInterface', array(__CLASS__))
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTSelfReference
     */
    private function _getFirstSelfReferenceInClass($testCase)
    {
        return $this->getFirstNodeOfTypeInClass(
            $testCase, PHP_Depend_Code_ASTSelfReference::CLAZZ
        );
    }
}
