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
 * @author     Joey Mazzarelli
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

require_once 'PHP/Depend/Code/ASTListExpression.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTListExpression} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @author     Joey Mazzarelli
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Code_ASTListExpressionTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testAcceptInvokesVisitOnGivenVisitor
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTListExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptInvokesVisitOnGivenVisitor()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitListExpression'));

        $expr = new PHP_Depend_Code_ASTListExpression();
        $expr->accept($visitor);
    }

    /**
     * testAcceptReturnsReturnValueOfVisitMethod
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTListExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptReturnsReturnValueOfVisitMethod()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitListExpression'))
            ->will($this->returnValue(42));

        $expr = new PHP_Depend_Code_ASTListExpression();
        self::assertEquals(42, $expr->accept($visitor));
    }

    /**
     * Tests the start line value.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTListExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testListExpressionHasExpectedStartLine()
    {
        $stmt = $this->_getFirstListExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTListExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testListExpressionHasExpectedStartColumn()
    {
        $stmt = $this->_getFirstListExpressionInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTListExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testListExpressionHasExpectedEndLine()
    {
        $stmt = $this->_getFirstListExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTListExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testListExpressionHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstListExpressionInFunction(__METHOD__);
        $this->assertEquals(16, $stmt->getEndColumn());
    }

    /**
     * Tests the list supports many variables in it
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTListExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testListExpressionSupportsManyVariables()
    {
        $stmt = $this->_getFirstListExpressionInFunction(__METHOD__);
        $vars = $stmt->getChildren();
        $this->assertEquals(3, count($vars));
    }

    /**
     * Tests the list supports a single variable
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTListExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testListExpressionSupportsSingleVariable()
    {
        $stmt = $this->_getFirstListExpressionInFunction(__METHOD__);
        $vars = $stmt->getChildren();
        $this->assertEquals(1, count($vars));
    }

    /**
     * Tests the list supports commas without variables
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTListExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testListExpressionSupportsExtraCommas()
    {
        $stmt = $this->_getFirstListExpressionInFunction(__METHOD__);
        $vars = $stmt->getChildren();
        $this->assertEquals(3, count($vars));
    }

    /**
     * testListExpressionWithComments
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTListExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testListExpressionWithComments()
    {
        $stmt = $this->_getFirstListExpressionInFunction(__METHOD__);
        $vars = $stmt->getChildren();
        $this->assertEquals(3, count($vars));
    }

    /**
     * testListExpressionWithoutChildExpression
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTListExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testListExpressionWithoutChildExpression()
    {
        $stmt = $this->_getFirstListExpressionInFunction(__METHOD__);
        $vars = $stmt->getChildren();
        $this->assertEquals(0, count($vars));
    }

    /**
     * testListExpressionWithVariableVariable
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTListExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testListExpressionWithVariableVariable()
    {
        $stmt = $this->_getFirstListExpressionInFunction(__METHOD__);
        $var  = $stmt->getChild(0);

        $this->assertType(PHP_Depend_Code_ASTVariableVariable::CLAZZ, $var);
    }

    /**
     * testListExpressionWithCompoundVariable
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTListExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testListExpressionWithCompoundVariable()
    {
        $stmt = $this->_getFirstListExpressionInFunction(__METHOD__);
        $var  = $stmt->getChild(0);

        $this->assertType(PHP_Depend_Code_ASTCompoundVariable::CLAZZ, $var);
    }

    /**
     * testListExpressionWithArrayElement
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTListExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testListExpressionWithArrayElement()
    {
        $stmt = $this->_getFirstListExpressionInFunction(__METHOD__);
        $var  = $stmt->getChild(0);

        $this->assertType(PHP_Depend_Code_ASTArrayIndexExpression::CLAZZ, $var);
    }

    /**
     * testListExpressionWithObjectProperty
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTListExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testListExpressionWithObjectProperty()
    {
        $stmt = $this->_getFirstListExpressionInFunction(__METHOD__);
        $var  = $stmt->getChild(0);

        $this->assertType(PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ, $var);
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTListExpression
     */
    private function _getFirstListExpressionInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTListExpression::CLAZZ
        );
    }
}
