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
 * Test case for the {@link \PDepend\Source\AST\ASTSwitchStatement} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTSwitchStatement
 * @group unittest
 */
class ASTSwitchStatementTest extends ASTNodeTest
{
    /**
     * Tests the generated object graph of a switch statement.
     *
     * @return void
     */
    public function testSwitchStatementGraphWithBooleanExpressions()
    {
        $stmt = $this->getFirstSwitchStatementInFunction();
        $children  = $stmt->getChildren();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTExpression', $children[0]);
    }

    /**
     * Tests the generated object graph of a switch statement.
     *
     * @return void
     */
    public function testSwitchStatementGraphWithLabels()
    {
        $stmt = $this->getFirstSwitchStatementInFunction();
        $children  = $stmt->getChildren();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTSwitchLabel', $children[1]);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTSwitchLabel', $children[2]);
    }

    /**
     * testSwitchStatement
     *
     * @return \PDepend\Source\AST\ASTSwitchStatement
     * @since 1.0.2
     */
    public function testSwitchStatement()
    {
        $stmt = $this->getFirstSwitchStatementInFunction();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTSwitchStatement', $stmt);

        return $stmt;
    }

    /**
     * Tests the start line value.
     *
     * @param \PDepend\Source\AST\ASTSwitchStatement $stmt
     *
     * @return void
     * @depends testSwitchStatement
     */
    public function testSwitchStatementHasExpectedStartLine($stmt)
    {
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @param \PDepend\Source\AST\ASTSwitchStatement $stmt
     *
     * @return void
     * @depends testSwitchStatement
     */
    public function testSwitchStatementHasExpectedStartColumn($stmt)
    {
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @param \PDepend\Source\AST\ASTSwitchStatement $stmt
     *
     * @return void
     * @depends testSwitchStatement
     */
    public function testSwitchStatementHasExpectedEndLine($stmt)
    {
        $this->assertEquals(8, $stmt->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @param \PDepend\Source\AST\ASTSwitchStatement $stmt
     *
     * @return void
     * @depends testSwitchStatement
     */
    public function testSwitchStatementHasExpectedEndColumn($stmt)
    {
        $this->assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testParserIgnoresDocCommentInSwitchStatement
     *
     * @return void
     */
    public function testParserIgnoresDocCommentInSwitchStatement()
    {
        $this->getFirstSwitchStatementInFunction();
    }

    /**
     * testParserIgnoresCommentInSwitchStatement
     *
     * @return void
     */
    public function testParserIgnoresCommentInSwitchStatement()
    {
        $this->getFirstSwitchStatementInFunction();
    }

    /**
     * testInvalidStatementInSwitchStatementResultsInExpectedException
     *
     * @return void
     */
    public function testInvalidStatementInSwitchStatementResultsInExpectedException()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->getFirstSwitchStatementInFunction();
    }

    /**
     * testUnclosedSwitchStatementResultsInExpectedException
     *
     * @return void
     */
    public function testUnclosedSwitchStatementResultsInExpectedException()
    {
        $this->expectException(\PDepend\Source\Parser\TokenStreamEndException::class);

        $this->getFirstSwitchStatementInFunction();
    }

    /**
     * testSwitchStatementWithAlternativeScope
     *
     * @return \PDepend\Source\AST\ASTSwitchStatement
     * @since 1.0.2
     */
    public function testSwitchStatementWithAlternativeScope()
    {
        $stmt = $this->getFirstSwitchStatementInFunction();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTSwitchStatement', $stmt);

        return $stmt;
    }

    /**
     * testSwitchStatementAlternativeScopeHasExpectedStartLine
     *
     * @param \PDepend\Source\AST\ASTSwitchStatement $stmt
     *
     * @return void
     * @depends testSwitchStatementWithAlternativeScope
     */
    public function testSwitchStatementAlternativeScopeHasExpectedStartLine($stmt)
    {
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testSwitchStatementAlternativeScopeHasExpectedStartColumn
     *
     * @param \PDepend\Source\AST\ASTSwitchStatement $stmt
     *
     * @return void
     * @depends testSwitchStatementWithAlternativeScope
     */
    public function testSwitchStatementAlternativeScopeHasExpectedStartColumn($stmt)
    {
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testSwitchStatementAlternativeScopeHasExpectedEndLine
     *
     * @param \PDepend\Source\AST\ASTSwitchStatement $stmt
     *
     * @return void
     * @depends testSwitchStatementWithAlternativeScope
     */
    public function testSwitchStatementAlternativeScopeHasExpectedEndLine($stmt)
    {
        $this->assertEquals(25, $stmt->getEndLine());
    }

    /**
     * testSwitchStatementAlternativeScopeHasExpectedEndColumn
     *
     * @param \PDepend\Source\AST\ASTSwitchStatement $stmt
     *
     * @return void
     * @depends testSwitchStatementWithAlternativeScope
     */
    public function testSwitchStatementAlternativeScopeHasExpectedEndColumn($stmt)
    {
        $this->assertEquals(14, $stmt->getEndColumn());
    }

    /**
     * testSwitchStatementTerminatedByPhpCloseTag
     *
     * @return void
     */
    public function testSwitchStatementTerminatedByPhpCloseTag()
    {
        $stmt = $this->getFirstSwitchStatementInFunction();
        $this->assertEquals(9, $stmt->getEndColumn());
    }

    /**
     * testSwitchStatementWithNestedNonePhpCode
     *
     * @return \PDepend\Source\AST\ASTSwitch
     * @since 2.1.0
     */
    public function testSwitchStatementWithNestedNonePhpCode()
    {
        $switch = $this->getFirstSwitchStatementInFunction();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTSwitchStatement', $switch);

        return $switch;
    }

    /**
     * testSwitchStatementWithNestedNonePhpCodeStartLine
     *
     * @param \PDepend\Source\AST\ASTSwitchStatement $switch
     * @return void
     * @since 2.1.0
     * @depends testSwitchStatementWithNestedNonePhpCode
     */
    public function testSwitchStatementWithNestedNonePhpCodeStartLine(ASTSwitchStatement $switch)
    {
        $this->assertSame(5, $switch->getStartLine());
    }

    /**
     * testSwitchStatementWithNestedNonePhpCodeEndLine
     *
     * @param \PDepend\Source\AST\ASTSwitchStatement $switch
     * @return void
     * @since 2.1.0
     * @depends testSwitchStatementWithNestedNonePhpCode
     */
    public function testSwitchStatementWithNestedNonePhpCodeEndLine(ASTSwitchStatement $switch)
    {
        $this->assertSame(16, $switch->getEndLine());
    }

    /**
     * testSwitchStatementWithNestedNonePhpCodeStartColumn
     *
     * @param \PDepend\Source\AST\ASTSwitchStatement $switch
     * @return void
     * @since 2.1.0
     * @depends testSwitchStatementWithNestedNonePhpCode
     */
    public function testSwitchStatementWithNestedNonePhpCodeStartColumn(ASTSwitchStatement $switch)
    {
        $this->assertSame(7, $switch->getStartColumn());
    }

    /**
     * testSwitchStatementWithNestedNonePhpCodeEndColumn
     *
     * @param \PDepend\Source\AST\ASTSwitchStatement $switch
     * @return void
     * @since 2.1.0
     * @depends testSwitchStatementWithNestedNonePhpCode
     */
    public function testSwitchStatementWithNestedNonePhpCodeEndColumn(ASTSwitchStatement $switch)
    {
        $this->assertSame(16, $switch->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return \PDepend\Source\AST\ASTSwitchStatement
     */
    private function getFirstSwitchStatementInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTSwitchStatement'
        );
    }
}
