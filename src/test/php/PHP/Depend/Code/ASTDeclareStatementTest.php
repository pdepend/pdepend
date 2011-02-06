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
 * @since      0.10.0
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

require_once 'PHP/Depend/Code/ASTDeclareStatement.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTDeclareStatement} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 * @since      0.10.0
 *
 * @covers PHP_Depend_Parser
 * @covers PHP_Depend_Builder_Default
 * @covers PHP_Depend_Code_ASTDeclareStatement
 */
class PHP_Depend_Code_ASTDeclareStatementTest extends PHP_Depend_Code_ASTNodeTest
{
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
            ->with($this->equalTo('visitDeclareStatement'));

        $stmt = new PHP_Depend_Code_ASTDeclareStatement();
        $stmt->accept($visitor);
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
            ->with($this->equalTo('visitDeclareStatement'))
            ->will($this->returnValue(42));

        $stmt = new PHP_Depend_Code_ASTDeclareStatement();
        self::assertEquals(42, $stmt->accept($visitor));
    }

    /**
     * testDeclareStatementWithSingleParameter
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDeclareStatementWithSingleParameter()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        self::assertEquals(1, count($stmt->getValues()));
    }

    /**
     * testDeclareStatementWithMultipleParameter
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDeclareStatementWithMultipleParameter()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        self::assertEquals(2, count($stmt->getValues()));
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
        $stmt = $this->createNodeInstance();
        self::assertEquals(
            array(
                'values',
                'image',
                'comment',
                'startLine',
                'startColumn',
                'endLine',
                'endColumn',
                'nodes'
            ),
            $stmt->__sleep()
        );
    }

    /**
     * testDeclareStatementHasExpectedStartLine
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDeclareStatementHasExpectedStartLine()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        self::assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testDeclareStatementHasExpectedStartColumn
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDeclareStatementHasExpectedStartColumn()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        self::assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testDeclareStatementHasExpectedEndLine
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDeclareStatementHasExpectedEndLine()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        self::assertEquals(4, $stmt->getEndLine());
    }

    /**
     * testDeclareStatementHasExpectedEndColumn
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDeclareStatementHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        self::assertEquals(22, $stmt->getEndColumn());
    }

    /**
     * testDeclareStatementWithScopeHasExpectedStartLine
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDeclareStatementWithScopeHasExpectedStartLine()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        self::assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testDeclareStatementWithScopeHasExpectedStartColumn
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDeclareStatementWithScopeHasExpectedStartColumn()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        self::assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testDeclareStatementWithScopeHasExpectedEndLine
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDeclareStatementWithScopeHasExpectedEndLine()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        self::assertEquals(10, $stmt->getEndLine());
    }

    /**
     * testDeclareStatementWithScopeHasExpectedEndColumn
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDeclareStatementWithScopeHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        self::assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testDeclareStatementWithAlternativeScopeHasExpectedStartLine
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDeclareStatementWithAlternativeScopeHasExpectedStartLine()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        self::assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testDeclareStatementWithAlternativeScopeHasExpectedStartColumn
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDeclareStatementWithAlternativeScopeHasExpectedStartColumn()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        self::assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testDeclareStatementWithAlternativeScopeHasExpectedEndLine
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDeclareStatementWithAlternativeScopeHasExpectedEndLine()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        self::assertEquals(9, $stmt->getEndLine());
    }

    /**
     * testDeclareStatementWithAlternativeScopeHasExpectedEndColumn
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testDeclareStatementWithAlternativeScopeHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        self::assertEquals(15, $stmt->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTDeclareStatement
     */
    private function _getFirstDeclareStatementInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTDeclareStatement::CLAZZ
        );
    }
}