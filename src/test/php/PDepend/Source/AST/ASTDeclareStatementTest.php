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
 * @since     0.10.0
 */

namespace PDepend\Source\AST;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTDeclareStatement} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since     0.10.0
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTDeclareStatement
 * @group unittest
 */
class ASTDeclareStatementTest extends \PDepend\Source\AST\ASTNodeTest
{
    /**
     * testDeclareStatementWithSingleParameter
     *
     * @return void
     */
    public function testDeclareStatementWithSingleParameter()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        $this->assertEquals(1, count($stmt->getValues()));
    }

    /**
     * testDeclareStatementWithMultipleParameter
     *
     * @return void
     */
    public function testDeclareStatementWithMultipleParameter()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        $this->assertEquals(2, count($stmt->getValues()));
    }

    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     *
     * @return void
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames()
    {
        $stmt = $this->createNodeInstance();
        $this->assertEquals(
            array(
                'values',
                'comment',
                'metadata',
                'nodes'
            ),
            $stmt->__sleep()
        );
    }

    /**
     * testDeclareStatementHasExpectedStartLine
     *
     * @return void
     */
    public function testDeclareStatementHasExpectedStartLine()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testDeclareStatementHasExpectedStartColumn
     *
     * @return void
     */
    public function testDeclareStatementHasExpectedStartColumn()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testDeclareStatementHasExpectedEndLine
     *
     * @return void
     */
    public function testDeclareStatementHasExpectedEndLine()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getEndLine());
    }

    /**
     * testDeclareStatementHasExpectedEndColumn
     *
     * @return void
     */
    public function testDeclareStatementHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        $this->assertEquals(22, $stmt->getEndColumn());
    }

    /**
     * testDeclareStatementWithScopeHasExpectedStartLine
     *
     * @return void
     */
    public function testDeclareStatementWithScopeHasExpectedStartLine()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testDeclareStatementWithScopeHasExpectedStartColumn
     *
     * @return void
     */
    public function testDeclareStatementWithScopeHasExpectedStartColumn()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testDeclareStatementWithScopeHasExpectedEndLine
     *
     * @return void
     */
    public function testDeclareStatementWithScopeHasExpectedEndLine()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        $this->assertEquals(10, $stmt->getEndLine());
    }

    /**
     * testDeclareStatementWithScopeHasExpectedEndColumn
     *
     * @return void
     */
    public function testDeclareStatementWithScopeHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testDeclareStatementWithAlternativeScopeHasExpectedStartLine
     *
     * @return void
     */
    public function testDeclareStatementWithAlternativeScopeHasExpectedStartLine()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * testDeclareStatementWithAlternativeScopeHasExpectedStartColumn
     *
     * @return void
     */
    public function testDeclareStatementWithAlternativeScopeHasExpectedStartColumn()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * testDeclareStatementWithAlternativeScopeHasExpectedEndLine
     *
     * @return void
     */
    public function testDeclareStatementWithAlternativeScopeHasExpectedEndLine()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        $this->assertEquals(9, $stmt->getEndLine());
    }

    /**
     * testDeclareStatementWithAlternativeScopeHasExpectedEndColumn
     *
     * @return void
     */
    public function testDeclareStatementWithAlternativeScopeHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstDeclareStatementInFunction(__METHOD__);
        $this->assertEquals(15, $stmt->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return \PDepend\Source\AST\ASTDeclareStatement
     */
    private function _getFirstDeclareStatementInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase,
            'PDepend\\Source\\AST\\ASTDeclareStatement'
        );
    }
}
