<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2017 Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Source\AST;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTForeachStatement} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTForeachStatement
 * @group unittest
 */
class ASTForeachStatementTest extends ASTNodeTest
{
    /**
     * testThirdChildOfForeachStatementIsASTScopeStatement
     *
     * @return void
     */
    public function testThirdChildOfForeachStatementIsASTScopeStatement()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScopeStatement', $stmt->getChild(2));
    }

    /**
     * Tests the start line value.
     *
     * @return void
     */
    public function testForeachStatementHasExpectedStartLine()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @return void
     */
    public function testForeachStatementHasExpectedStartColumn()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @return void
     */
    public function testForeachStatementHasExpectedEndLine()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertEquals(6, $stmt->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @return void
     */
    public function testForeachStatementHasExpectedEndColumn()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testForeachStatementContainsListExpressionAsFirstChild
     *
     * @return void
     */
    public function testForeachStatementContainsExpressionAsFirstChild()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTExpression', $stmt->getChild(0));
    }

    /**
     * testForeachStatementWithoutKeyAndWithValue
     *
     * @return void
     */
    public function testForeachStatementWithoutKeyAndWithValue()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $stmt->getChild(1));
    }

    /**
     * testForeachStatementWithoutKeyAndWithValueByReference
     *
     * @return void
     */
    public function testForeachStatementWithoutKeyAndWithValueByReference()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTUnaryExpression', $stmt->getChild(1));
    }

    /**
     * testForeachStatementWithKeyAndValue
     *
     * @return void
     */
    public function testForeachStatementWithKeyAndValue()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $stmt->getChild(2));
    }

    /**
     * testForeachStatementWithKeyAndValueByReference
     *
     * @return void
     */
    public function testForeachStatementWithKeyAndValueByReference()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTUnaryExpression', $stmt->getChild(2));
    }

    /**
     * testForeachStatementWithObjectPropertyByReference
     *
     * @return void
     */
    public function testForeachStatementWithObjectPropertyByReference()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTUnaryExpression', $stmt->getChild(1));
    }

    /**
     * testForeachStatementWithKeyAndObjectPropertyByReference
     *
     * @return void
     */
    public function testForeachStatementWithKeyAndObjectPropertyByReference()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTUnaryExpression', $stmt->getChild(2));
    }

    /**
     * testForeachStatementWithObjectPropertyAsKey
     *
     * @return void
     */
    public function testForeachStatementWithObjectPropertyAsKey()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMemberPrimaryPrefix', $stmt->getChild(1));
    }

    /**
     * testForeachStatementWithObjectPropertyAsValue
     *
     * @return void
     */
    public function testForeachStatementWithObjectPropertyAsValue()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMemberPrimaryPrefix', $stmt->getChild(1));
    }

    /**
     * testForeachStatementWithObjectPropertyAsKeyAndValue
     *
     * @return void
     */
    public function testForeachStatementWithObjectPropertyAsKeyAndValue()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMemberPrimaryPrefix', $stmt->getChild(1));
    }

    /**
     * testForeachStatementThrowsExpectedExceptionForKeyByReference
     *
     * @return void
     */
    public function testForeachStatementThrowsExpectedExceptionForKeyByReference()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->getFirstForeachStatementInFunction(__METHOD__);
    }

    /**
     * testForeachStatementWithCommentBeforeClosingParenthesis
     *
     * @return void
     */
    public function testForeachStatementWithCommentBeforeClosingParenthesis()
    {
        $this->getFirstForeachStatementInFunction(__METHOD__);
    }

    /**
     * testForeachStatementAlternativeScopeHasExpectedStartLine
     *
     * @return void
     */
    public function testForeachStatementAlternativeScopeHasExpectedStartLine()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testForeachStatementAlternativeScopeHasExpectedStartColumn
     *
     * @return void
     */
    public function testForeachStatementAlternativeScopeHasExpectedStartColumn()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testForeachStatementAlternativeScopeHasExpectedEndLine
     *
     * @return void
     */
    public function testForeachStatementAlternativeScopeHasExpectedEndLine()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertEquals(6, $stmt->getEndLine());
    }

    /**
     * testForeachStatementAlternativeScopeHasExpectedEndColumn
     *
     * @return void
     */
    public function testForeachStatementAlternativeScopeHasExpectedEndColumn()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertEquals(15, $stmt->getEndColumn());
    }

    /**
     * testForeachStatementTerminatedByPhpCloseTag
     *
     * @return void
     */
    public function testForeachStatementTerminatedByPhpCloseTag()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertEquals(9, $stmt->getEndColumn());
    }

    /**
     * testForeachStatementWithList
     *
     * @return void
     */
    public function testForeachStatementWithList()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTListExpression', $stmt->getChild(1));
    }

    /**
     * testForeachStatementWithKeyAndList
     *
     * @return void
     */
    public function testForeachStatementWithKeyAndList()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTListExpression', $stmt->getChild(2));
    }

    /**
     * testForeachStatementWithList
     *
     * @return void
     */
    public function testForeachStatementWithShortList()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTListExpression', $stmt->getChild(1));
    }

    /**
     * testForeachStatementWithList
     *
     * @return void
     */
    public function testForeachStatementWithKeyAndShortList()
    {
        $stmt = $this->getFirstForeachStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTListExpression', $stmt->getChild(2));
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return \PDepend\Source\AST\ASTForeachStatement
     */
    private function getFirstForeachStatementInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase,
            'PDepend\\Source\\AST\\ASTForeachStatement'
        );
    }
}
