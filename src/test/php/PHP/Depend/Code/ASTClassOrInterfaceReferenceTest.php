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

/**
 * description
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
 * @covers PHP_Depend_Code_ASTClassOrInterfaceReference
 */
class PHP_Depend_Code_ASTClassOrInterfaceReferenceTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testReturnValueOfMagicSleepContainsContextProperty
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testReturnValueOfMagicSleepContainsContextProperty()
    {
        $reference = new PHP_Depend_Code_ASTClassOrInterfaceReference(
            $this->getBuilderContextMock(), __CLASS__
        );
        self::assertEquals(
            array(
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
            ->with($this->equalTo('visitClassOrInterfaceReference'));

        $reference = new PHP_Depend_Code_ASTClassOrInterfaceReference(
            $this->getBuilderContextMock(), __CLASS__
        );
        $reference->accept($visitor);
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
            ->with($this->equalTo('visitClassOrInterfaceReference'))
            ->will($this->returnValue(42));


        $reference = new PHP_Depend_Code_ASTClassOrInterfaceReference(
            $this->getBuilderContextMock(), __CLASS__
        );
        self::assertEquals(42, $reference->accept($visitor));
    }

    /**
     * testGetTypeDelegatesToBuilderContextGetClassOrInterface
     * 
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testGetTypeDelegatesToBuilderContextGetClassOrInterface()
    {
        $context = $this->getBuilderContextMock();
        $context->expects($this->once())
            ->method('getClassOrInterface')
            ->with($this->equalTo(__CLASS__))
            ->will($this->returnValue($this));

        $reference = new PHP_Depend_Code_ASTClassOrInterfaceReference(
            $context, __CLASS__
        );

        $reference->getType();
    }

    /**
     * testGetTypeCachesReturnValueOfBuilderContextGetClassOrInterface
     * 
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testGetTypeCachesReturnValueOfBuilderContextGetClassOrInterface()
    {
        $context = $this->getBuilderContextMock();
        $context->expects($this->exactly(1))
            ->method('getClassOrInterface')
            ->with($this->equalTo(__CLASS__))
            ->will($this->returnValue($this));

        $reference = new PHP_Depend_Code_ASTClassOrInterfaceReference(
            $context, __CLASS__
        );

        $reference->getType();
    }

    /**
     * testReferenceHasExpectedStartLine
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testReferenceHasExpectedStartLine()
    {
        $reference = $this->_getFirstReferenceInFunction(__METHOD__);
        self::assertEquals(2, $reference->getStartLine());
    }

    /**
     * testReferenceHasExpectedStartColumn
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testReferenceHasExpectedStartColumn()
    {
        $reference = $this->_getFirstReferenceInFunction(__METHOD__);
        self::assertEquals(14, $reference->getStartColumn());
    }

    /**
     * testReferenceHasExpectedEndLine
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testReferenceHasExpectedEndLine()
    {
        $reference = $this->_getFirstReferenceInFunction(__METHOD__);
        self::assertEquals(2, $reference->getEndLine());
    }

    /**
     * testReferenceHasExpectedEndColumn
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testReferenceHasExpectedEndColumn()
    {
        $reference = $this->_getFirstReferenceInFunction(__METHOD__);
        self::assertEquals(29, $reference->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTClassOrInterfaceReference
     */
    private function _getFirstReferenceInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ
        );
    }

    /**
     * Creates a concrete node implementation.
     *
     * @return PHP_Depend_Code_ASTNode
     */
    protected function createNodeInstance()
    {
        return new PHP_Depend_Code_ASTClassOrInterfaceReference(
            $this->getBuilderContextMock(),
            __CLASS__
        );
    }

    /**
     * Returns a mocked builder context instance.
     *
     * @return PHP_Depend_Builder_Context
     */
    protected function getBuilderContextMock()
    {
        return $this->getMock('PHP_Depend_Builder_Context');
    }
}
