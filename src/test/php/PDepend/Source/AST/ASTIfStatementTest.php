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
 * Test case for the {@link \PDepend\Source\AST\ASTIfStatement} class.
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTIfStatement
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTIfStatementTest extends ASTNodeTestCase
{
    /**
     * testHasElseMethodReturnsFalseByDefault
     */
    public function testHasElseMethodReturnsFalseByDefault(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertFalse($stmt->hasElse());
    }

    /**
     * testHasElseMethodReturnsTrueWhenElseIfBranchExists
     */
    public function testHasElseMethodReturnsTrueWhenElseIfBranchExists(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertTrue($stmt->hasElse());
    }

    /**
     * testHasElseMethodReturnsTrueWhenElseBranchWithIfExists
     */
    public function testHasElseMethodReturnsTrueWhenElseBranchWithIfExists(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertTrue($stmt->hasElse());
    }

    /**
     * testHasElseMethodReturnsTrueWhenElseBranchExists
     */
    public function testHasElseMethodReturnsTrueWhenElseBranchExists(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertTrue($stmt->hasElse());
    }

    /**
     * Tests the generated object graph of an if statement.
     */
    public function testIfStatementGraphWithBooleanExpressions(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertCount(2, $stmt->getChildren());
    }

    /**
     * testFirstChildOfIfStatementIsInstanceOfExpression
     */
    public function testFirstChildOfIfStatementIsInstanceOfExpression(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTExpression', $stmt->getChild(0));
    }

    /**
     * testSecondChildOfIfStatementIsInstanceOfScopeStatement
     */
    public function testSecondChildOfIfStatementIsInstanceOfScopeStatement(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScopeStatement', $stmt->getChild(1));
    }

    /**
     * testParserThrowsExpectedExceptionWhenIfStatementHasNoBody
     */
    public function testParserThrowsExpectedExceptionWhenIfStatementHasNoBody(): void
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->getFirstIfStatementInFunction(__METHOD__);
    }

    /**
     * Tests the start line value.
     */
    public function testIfStatementHasExpectedStartLine(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * Tests the start column value.
     */
    public function testIfStatementHasExpectedStartColumn(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * Tests the end line value.
     */
    public function testIfStatementHasExpectedEndLine(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(6, $stmt->getEndLine());
    }

    /**
     * Tests the end column value.
     */
    public function testIfStatementHasExpectedEndColumn(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testIfStatementAlternativeScopeHasExpectedStartLine
     */
    public function testIfStatementAlternativeScopeHasExpectedStartLine(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testIfStatementAlternativeScopeHasExpectedStartColumn
     */
    public function testIfStatementAlternativeScopeHasExpectedStartColumn(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testIfStatementAlternativeScopeHasExpectedEndLine
     */
    public function testIfStatementAlternativeScopeHasExpectedEndLine(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(10, $stmt->getEndLine());
    }

    /**
     * testIfStatementAlternativeScopeHasExpectedEndColumn
     */
    public function testIfStatementAlternativeScopeHasExpectedEndColumn(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(9, $stmt->getEndColumn());
    }

    /**
     * testIfElseStatementAlternativeScopeHasExpectedStartLine
     */
    public function testIfElseStatementAlternativeScopeHasExpectedStartLine(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testIfElseStatementAlternativeScopeHasExpectedStartColumn
     */
    public function testIfElseStatementAlternativeScopeHasExpectedStartColumn(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testIfElseStatementAlternativeScopeHasExpectedEndLine
     */
    public function testIfElseStatementAlternativeScopeHasExpectedEndLine(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(8, $stmt->getEndLine());
    }

    /**
     * testIfElseStatementAlternativeScopeHasExpectedEndColumn
     */
    public function testIfElseStatementAlternativeScopeHasExpectedEndColumn(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(10, $stmt->getEndColumn());
    }

    /**
     * testElseStatementAlternativeScopeHasExpectedStartLine
     */
    public function testElseStatementAlternativeScopeHasExpectedStartLine(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__)->getChild(2);
        $this->assertEquals(7, $stmt->getStartLine());
    }

    /**
     * testElseStatementAlternativeScopeHasExpectedStartColumn
     */
    public function testElseStatementAlternativeScopeHasExpectedStartColumn(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__)->getChild(2);
        $this->assertEquals(13, $stmt->getStartColumn());
    }

    /**
     * testElseStatementAlternativeScopeHasExpectedEndLine
     */
    public function testElseStatementAlternativeScopeHasExpectedEndLine(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__)->getChild(2);
        $this->assertEquals(10, $stmt->getEndLine());
    }

    /**
     * testElseStatementAlternativeScopeHasExpectedEndColumn
     */
    public function testElseStatementAlternativeScopeHasExpectedEndColumn(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__)->getChild(2);
        $this->assertEquals(17, $stmt->getEndColumn());
    }

    /**
     * testIfStatementTerminatedByPhpCloseTag
     */
    public function testIfStatementTerminatedByPhpCloseTag(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(9, $stmt->getEndColumn());
    }

    /**
     * testIfStatementWithElseContainsExpectedNumberOfChildNodes
     */
    public function testIfStatementWithElseContainsExpectedNumberOfChildNodes(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertCount(3, $stmt->getChildren());
    }

    /**
     * testThirdChildOfIfStatementIsInstanceOfScopeStatementForElse
     */
    public function testThirdChildOfIfStatementIsInstanceOfScopeStatementForElse(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScopeStatement', $stmt->getChild(2));
    }

    /**
     * testThirdChildOfIfStatementIsInstanceOfExpressionForElseIf
     */
    public function testThirdChildOfIfStatementIsInstanceOfIfStatementForElseIf(): void
    {
        $stmt = $this->getFirstIfStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTIfStatement', $stmt->getChild(2));
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     * @return ASTIfStatement
     */
    private function getFirstIfStatementInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase,
            'PDepend\\Source\\AST\\ASTIfStatement'
        );
    }
}
