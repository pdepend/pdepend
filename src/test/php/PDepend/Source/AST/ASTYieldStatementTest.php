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
 * Test case for the {@link \PDepend\Source\AST\ASTYieldStatement} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTForeachStatement
 * @group unittest
 */
class ASTYieldStatementTest extends \PDepend\Source\AST\ASTNodeTest
{
    /**
     * testYield
     *
     * @return void
     */
    public function testYield()
    {
        $stmt = $this->getFirstYieldStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTYieldStatement', $stmt);
    }

    /**
     * testYieldWithLiteral
     *
     * @return void
     */
    public function testYieldWithLiteral()
    {
        $stmt = $this->getFirstYieldStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTLiteral', $stmt->getChild(0));
    }

    /**
     * testYieldWithVariable
     *
     * @return void
     */
    public function testYieldWithVariable()
    {
        $stmt = $this->getFirstYieldStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $stmt->getChild(0));
    }

    /**
     * testYieldWithKeyValue
     *
     * @return void
     */
    public function testYieldWithKeyValue()
    {
        $stmt = $this->getFirstYieldStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $stmt->getChild(0));
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $stmt->getChild(1));
    }

    /**
     * testYieldWithFunctionCalls
     *
     * @return void
     */
    public function testYieldWithFunctionCalls()
    {
        $stmt = $this->getFirstYieldStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFunctionPostfix', $stmt->getChild(0));
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFunctionPostfix', $stmt->getChild(1));
    }

    /**
     * testYieldInsideForeach
     *
     * @return void
     */
    public function testYieldInsideForeach()
    {
        $stmt = $this->getFirstYieldStatementInFunction(__METHOD__);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTForeachStatement', $stmt->getParent()->getParent());
    }

    /**
     * testYieldKeyValue
     *
     * @return \PDepend\Source\AST\ASTExpression[]
     */
    public function testYieldKeyValue()
    {
        $stmt = $this->getFirstYieldStatementInFunction(__METHOD__);
        $nodes = $stmt->getChildren();

        $this->assertEquals(2, count($nodes));

        return $nodes;
    }

    /**
     * testYieldKeyValueChildNodes
     *
     * @param \PDepend\Source\AST\ASTExpression[] $nodes
     * @return void
     * @depends testYieldKeyValue
     */
    public function testYieldKeyValueChildNodes(array $nodes)
    {
        $this->assertEquals('$id', $nodes[0]->getImage());
        $this->assertEquals('$line', $nodes[1]->getImage());
    }

    /**
     * testYieldValueAssignmentSimple
     *
     * @return \PDepend\Source\AST\ASTYieldStatement
     */
    public function testYieldValueAssignmentSimple()
    {
        $yield = $this->getFirstYieldStatementInFunction(__METHOD__);
        $nodes = $yield->getChildren();

        $this->assertEquals(1, count($nodes));

        return $yield;
    }

    /**
     * testYieldValueAssignmentSimpleParent
     *
     * @param \PDepend\Source\AST\ASTStatement $yield
     * @return void
     * @depends testYieldValueAssignmentSimple
     */
    public function testYieldValueAssignmentSimpleParent(ASTStatement $yield)
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTAssignmentExpression',
            $yield->getParent()->getParent()
        );
    }

    /**
     * testYieldValueAssignmentKeyValue
     *
     * @return \PDepend\Source\AST\ASTYieldStatement
     */
    public function testYieldValueAssignmentKeyValue()
    {
        $yield = $this->getFirstYieldStatementInFunction(__METHOD__);
        $nodes = $yield->getChildren();

        $this->assertEquals(2, count($nodes));

        return $yield;
    }

    /**
     * testYieldValueAssignmentKeyValueParent
     *
     * @param \PDepend\Source\AST\ASTYieldStatement $yield
     * @return void
     * @depends testYieldValueAssignmentKeyValue
     */
    public function testYieldValueAssignmentKeyValueParent(ASTYieldStatement $yield)
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTAssignmentExpression',
            $yield->getParent()->getParent()->getParent()
        );
    }

    /**
     * testYieldValueAssignmentKeyValueChildren
     *
     * @param \PDepend\Source\AST\ASTYieldStatement $yield
     * @return void
     * @depends testYieldValueAssignmentKeyValue
     */
    public function testYieldValueAssignmentKeyValueChildren(ASTYieldStatement $yield)
    {
        $nodes = $yield->getChildren();

        $this->assertEquals('"key"', $nodes[0]->getImage());
        $this->assertEquals('2', $nodes[1]->getImage());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     * @return \PDepend\Source\AST\ASTYieldStatement
     */
    private function getFirstYieldStatementInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase,
            'PDepend\\Source\\AST\\ASTYieldStatement'
        );
    }
}
