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
 * Test case for the {@link \PDepend\Source\AST\ASTScopeStatement} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTScopeStatement
 * @group unittest
 */
class ASTScopeStatementTest extends \PDepend\Source\AST\ASTNodeTest
{
    /**
     * testParserHandlesInlineScopeStatement
     *
     * @return \PDepend\Source\AST\ASTScopeStatement
     * @since 1.0.0
     */
    public function testParserHandlesInlineScopeStatement()
    {
        $stmt = $this->_getFirstScopeStatementInFunction();
        $this->assertEquals(1, count($stmt->getChildren()));

        return $stmt;
    }

    /**
     * testInlineScopeStatementHasExpectedStartLine
     *
     * @param \PDepend\Source\AST\ASTScopeStatement $stmt
     *
     * @return \PDepend\Source\AST\ASTScopeStatement
     * @since 1.0.0
     * @depends testParserHandlesInlineScopeStatement
     */
    public function testInlineScopeStatementHasExpectedStartLine($stmt)
    {
        $this->assertEquals(4, $stmt->getStartLine());

        return $stmt;
    }

    /**
     * testInlineScopeStatementHasExpectedStartColumn
     *
     * @param \PDepend\Source\AST\ASTScopeStatement $stmt
     *
     * @return \PDepend\Source\AST\ASTScopeStatement
     * @since 1.0.0
     * @depends testInlineScopeStatementHasExpectedStartLine
     */
    public function testInlineScopeStatementHasExpectedStartColumn($stmt)
    {
        $this->assertEquals(5, $stmt->getStartColumn());

        return $stmt;
    }

    /**
     * testInlineScopeStatementHasExpectedEndLine
     *
     * @param \PDepend\Source\AST\ASTScopeStatement $stmt
     *
     * @return \PDepend\Source\AST\ASTScopeStatement
     * @since 1.0.0
     * @depends testInlineScopeStatementHasExpectedStartColumn
     */
    public function testInlineScopeStatementHasExpectedEndLine($stmt)
    {
        $this->assertEquals(5, $stmt->getEndLine());

        return $stmt;
    }

    /**
     * testInlineScopeStatementHasExpectedEndColumn
     *
     * @param \PDepend\Source\AST\ASTScopeStatement $stmt
     *
     * @return \PDepend\Source\AST\ASTScopeStatement
     * @since 1.0.0
     * @depends testInlineScopeStatementHasExpectedEndLine
     */
    public function testInlineScopeStatementHasExpectedEndColumn($stmt)
    {
        $this->assertEquals(20, $stmt->getEndColumn());
    }

    /**
     * testScopeStatement
     *
     * @return \PDepend\Source\AST\ASTScopeStatement
     * @since 1.0.2
     */
    public function testScopeStatement()
    {
        $stmt = $this->_getFirstScopeStatementInFunction();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScopeStatement', $stmt);

        return $stmt;
    }
    
    /**
     * Tests that the scope-statement has the expected start line value.
     *
     * @param \PDepend\Source\AST\ASTScopeStatement $stmt
     *
     * @return void
     * @depends testScopeStatement
     */
    public function testScopeStatementHasExpectedStartLine($stmt)
    {
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * Tests that the scope-statement has the expected start column value.
     *
     * @param \PDepend\Source\AST\ASTScopeStatement $stmt
     *
     * @return void
     * @depends testScopeStatement
     */
    public function testScopeStatementHasExpectedStartColumn($stmt)
    {
        $this->assertEquals(34, $stmt->getStartColumn());
    }

    /**
     * Tests that the scope-statement has the expected end line value.
     *
     * @param \PDepend\Source\AST\ASTScopeStatement $stmt
     *
     * @return void
     * @depends testScopeStatement
     */
    public function testScopeStatementHasExpectedEndLine($stmt)
    {
        $this->assertEquals(6, $stmt->getEndLine());
    }

    /**
     * Tests that the scope-statement has the expected end column value.
     *
     * @param \PDepend\Source\AST\ASTScopeStatement $stmt
     *
     * @return void
     * @depends testScopeStatement
     */
    public function testScopeStatementHasExpectedEndColumn($stmt)
    {
        $this->assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testScopeStatementWithAlternative
     * 
     * @return \PDepend\Source\AST\ASTScopeStatement
     * @since 1.0.2
     */
    public function testScopeStatementWithAlternative()
    {
        $stmt = $this->_getFirstScopeStatementInFunction();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScopeStatement', $stmt);

        return $stmt;
    }

    /**
     * testScopeStatementWithAlternativeHasExpectedStartLine
     *
     * @param \PDepend\Source\AST\ASTScopeStatement $stmt
     *
     * @return void
     * @depends testScopeStatementWithAlternative
     */
    public function testScopeStatementWithAlternativeHasExpectedStartLine($stmt)
    {
        $this->assertEquals(6, $stmt->getStartLine());
    }

    /**
     * testScopeStatementWithAlternativeHasExpectedStartColumn
     *
     * @param \PDepend\Source\AST\ASTScopeStatement $stmt
     *
     * @return void
     * @depends testScopeStatementWithAlternative
     */
    public function testScopeStatementWithAlternativeHasExpectedStartColumn($stmt)
    {
        $this->assertEquals(13, $stmt->getStartColumn());
    }

    /**
     * testScopeStatementWithAlternativeHasExpectedEndLine
     *
     * @param \PDepend\Source\AST\ASTScopeStatement $stmt
     *
     * @return void
     * @depends testScopeStatementWithAlternative
     */
    public function testScopeStatementWithAlternativeHasExpectedEndLine($stmt)
    {
        $this->assertEquals(17, $stmt->getEndLine());
    }

    /**
     * testScopeStatementWithAlternativeHasExpectedEndColumn
     *
     * @param \PDepend\Source\AST\ASTScopeStatement $stmt
     *
     * @return void
     * @depends testScopeStatementWithAlternative
     */
    public function testScopeStatementWithAlternativeHasExpectedEndColumn($stmt)
    {
        $this->assertEquals(15, $stmt->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return \PDepend\Source\AST\ASTScopeStatement
     */
    private function _getFirstScopeStatementInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTScopeStatement'
        );
    }
}
