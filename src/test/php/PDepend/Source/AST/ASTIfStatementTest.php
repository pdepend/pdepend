<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Source\AST;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTIfStatement} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTIfStatement
 * @group unittest
 */
class ASTIfStatementTest extends \PDepend\Source\AST\ASTNodeTest
{
    /**
     * testHasElseMethodReturnsFalseByDefault
     *
     * @return void
     */
    public function testHasElseMethodReturnsFalseByDefault()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertFalse($stmt->hasElse());
    }

    /**
     * testHasElseMethodReturnsTrueWhenElseIfBranchExists
     *
     * @return void
     */
    public function testHasElseMethodReturnsTrueWhenElseIfBranchExists()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertTrue($stmt->hasElse());
    }

    /**
     * testHasElseMethodReturnsTrueWhenElseBranchWithIfExists
     *
     * @return void
     */
    public function testHasElseMethodReturnsTrueWhenElseBranchWithIfExists()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertTrue($stmt->hasElse());
    }

    /**
     * testHasElseMethodReturnsTrueWhenElseBranchExists
     *
     * @return void
     */
    public function testHasElseMethodReturnsTrueWhenElseBranchExists()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertTrue($stmt->hasElse());
    }
    
    /**
     * Tests the generated object graph of an if statement.
     *
     * @return void
     */
    public function testIfStatementGraphWithBooleanExpressions()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(2, count($stmt->getChildren()));
    }

    /**
     * testFirstChildOfIfStatementIsInstanceOfExpression
     *
     * @return void
     */
    public function testFirstChildOfIfStatementIsInstanceOfExpression()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTExpression', $stmt->getChild(0));
    }

    /**
     * testSecondChildOfIfStatementIsInstanceOfScopeStatement
     *
     * @return void
     */
    public function testSecondChildOfIfStatementIsInstanceOfScopeStatement()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScopeStatement', $stmt->getChild(1));
    }

    /**
     * testParserThrowsExpectedExceptionWhenIfStatementHasNoBody
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionWhenIfStatementHasNoBody()
    {
        $this->_getFirstIfStatementInFunction(__METHOD__);
    }

    /**
     * Tests the start line value.
     *
     * @return void
     */
    public function testIfStatementHasExpectedStartLine()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @return void
     */
    public function testIfStatementHasExpectedStartColumn()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @return void
     */
    public function testIfStatementHasExpectedEndLine()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(6, $stmt->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @return void
     */
    public function testIfStatementHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testIfStatementAlternativeScopeHasExpectedStartLine
     *
     * @return void
     */
    public function testIfStatementAlternativeScopeHasExpectedStartLine()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testIfStatementAlternativeScopeHasExpectedStartColumn
     *
     * @return void
     */
    public function testIfStatementAlternativeScopeHasExpectedStartColumn()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testIfStatementAlternativeScopeHasExpectedEndLine
     *
     * @return void
     */
    public function testIfStatementAlternativeScopeHasExpectedEndLine()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(10, $stmt->getEndLine());
    }

    /**
     * testIfStatementAlternativeScopeHasExpectedEndColumn
     *
     * @return void
     */
    public function testIfStatementAlternativeScopeHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(9, $stmt->getEndColumn());
    }

    /**
     * testIfElseStatementAlternativeScopeHasExpectedStartLine
     *
     * @return void
     */
    public function testIfElseStatementAlternativeScopeHasExpectedStartLine()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testIfElseStatementAlternativeScopeHasExpectedStartColumn
     *
     * @return void
     */
    public function testIfElseStatementAlternativeScopeHasExpectedStartColumn()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testIfElseStatementAlternativeScopeHasExpectedEndLine
     *
     * @return void
     */
    public function testIfElseStatementAlternativeScopeHasExpectedEndLine()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(8, $stmt->getEndLine());
    }

    /**
     * testIfElseStatementAlternativeScopeHasExpectedEndColumn
     *
     * @return void
     */
    public function testIfElseStatementAlternativeScopeHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(10, $stmt->getEndColumn());
    }

    /**
     * testElseStatementAlternativeScopeHasExpectedStartLine
     *
     * @return void
     */
    public function testElseStatementAlternativeScopeHasExpectedStartLine()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__)->getChild(2);
        $this->assertEquals(7, $stmt->getStartLine());
    }

    /**
     * testElseStatementAlternativeScopeHasExpectedStartColumn
     *
     * @return void
     */
    public function testElseStatementAlternativeScopeHasExpectedStartColumn()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__)->getChild(2);
        $this->assertEquals(13, $stmt->getStartColumn());
    }

    /**
     * testElseStatementAlternativeScopeHasExpectedEndLine
     *
     * @return void
     */
    public function testElseStatementAlternativeScopeHasExpectedEndLine()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__)->getChild(2);
        $this->assertEquals(10, $stmt->getEndLine());
    }

    /**
     * testElseStatementAlternativeScopeHasExpectedEndColumn
     *
     * @return void
     */
    public function testElseStatementAlternativeScopeHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__)->getChild(2);
        $this->assertEquals(17, $stmt->getEndColumn());
    }

    /**
     * testIfStatementTerminatedByPhpCloseTag
     *
     * @return void
     */
    public function testIfStatementTerminatedByPhpCloseTag()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(9, $stmt->getEndColumn());
    }

    /**
     * testIfStatementWithElseContainsExpectedNumberOfChildNodes
     *
     * @return void
     */
    public function testIfStatementWithElseContainsExpectedNumberOfChildNodes()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(3, count($stmt->getChildren()));
    }

    /**
     * testThirdChildOfIfStatementIsInstanceOfScopeStatementForElse
     *
     * @return void
     */
    public function testThirdChildOfIfStatementIsInstanceOfScopeStatementForElse()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScopeStatement', $stmt->getChild(2));
    }

    /**
     * testThirdChildOfIfStatementIsInstanceOfExpressionForElseIf
     *
     * @return void
     */
    public function testThirdChildOfIfStatementIsInstanceOfIfStatementForElseIf()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTIfStatement', $stmt->getChild(2));
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return \PDepend\Source\AST\ASTIfStatement
     */
    private function _getFirstIfStatementInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, 'PDepend\\Source\\AST\\ASTIfStatement'
        );
    }
}
