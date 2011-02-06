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

require_once 'PHP/Depend/Code/ASTParentReference.php';
require_once 'PHP/Depend/Code/ASTClassOrInterfaceReference.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTParentReference} class.
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
 * @covers PHP_Depend_Code_ASTParentReference
 */
class PHP_Depend_Code_ASTParentReferenceTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * The mocked reference instance.
     *
     * @var PHP_Depend_Code_ASTClassOrInterfaceReference
     */
    protected $referenceMock = null;

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
            ->with($this->equalTo('visitParentReference'));

        $reference = $this->createNodeInstance();
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
            ->with($this->equalTo('visitParentReference'))
            ->will($this->returnValue(42));

        $reference = $this->createNodeInstance();
        self::assertEquals(42, $reference->accept($visitor));
    }

    /**
     * testGetTypeDelegatesCallToInjectedReferenceObject
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testGetTypeDelegatesCallToInjectedReferenceObject()
    {
        $reference = $this->createNodeInstance();
        $this->referenceMock->expects($this->once())
            ->method('getType');

        
        $reference->getType();
    }

    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames()
    {
        $reference = $this->createNodeInstance();
        self::assertEquals(
            array(
                'reference',
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
     * testParentReferenceAllocationOutsideOfClassScopeThrowsExpectedException
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     * @expectedException PHP_Depend_Parser_InvalidStateException
     */
    public function testParentReferenceAllocationOutsideOfClassScopeThrowsExpectedException()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParentReferenceInClassWithoutParentThrowsException
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     * @expectedException PHP_Depend_Parser_InvalidStateException
     */
    public function testParentReferenceInClassWithoutParentThrowsException()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParentReferenceMemberPrimaryPrefixOutsideOfClassScopeThrowsExpectedException
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     * @expectedException PHP_Depend_Parser_InvalidStateException
     */
    public function testParentReferenceMemberPrimaryPrefixOutsideOfClassScopeThrowsExpectedException()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParentReferenceHasExpectedStartLine
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testParentReferenceHasExpectedStartLine()
    {
        $reference = $this->_getFirstParentReferenceInClass(__METHOD__);
        self::assertEquals(5, $reference->getStartLine());
    }

    /**
     * testParentReferenceHasExpectedStartColumn
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testParentReferenceHasExpectedStartColumn()
    {
        $reference = $this->_getFirstParentReferenceInClass(__METHOD__);
        self::assertEquals(20, $reference->getStartColumn());
    }

    /**
     * testParentReferenceHasExpectedEndLine
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testParentReferenceHasExpectedEndLine()
    {
        $reference = $this->_getFirstParentReferenceInClass(__METHOD__);
        self::assertEquals(5, $reference->getEndLine());
    }

    /**
     * testParentReferenceHasExpectedEndColumn
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testParentReferenceHasExpectedEndColumn()
    {
        $reference = $this->_getFirstParentReferenceInClass(__METHOD__);
        self::assertEquals(25, $reference->getEndColumn());
    }

    /**
     * Creates a concrete node implementation.
     *
     * @return PHP_Depend_Code_ASTParentReference
     */
    protected function createNodeInstance()
    {
        return new PHP_Depend_Code_ASTParentReference(
            $this->referenceMock = $this->getMock(
                'PHP_Depend_Code_ASTClassOrInterfaceReference',
                array(),
                array(null, __CLASS__),
                '',
                false
            )
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTParentReference
     */
    private function _getFirstParentReferenceInClass($testCase)
    {
        return $this->getFirstNodeOfTypeInClass(
            $testCase, PHP_Depend_Code_ASTParentReference::CLAZZ
        );
    }
}
