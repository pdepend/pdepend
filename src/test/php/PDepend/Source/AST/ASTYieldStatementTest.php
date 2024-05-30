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
 * Test case for the {@link \PDepend\Source\AST\ASTYieldStatement} class.
 *
 * @covers \PDepend\Source\AST\ASTForeachStatement
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTYieldStatementTest extends ASTNodeTestCase
{
    /**
     * testYield
     */
    public function testYield(): void
    {
        $stmt = $this->getFirstYieldStatementInFunction();
        static::assertInstanceOf(ASTYieldStatement::class, $stmt);
    }

    /**
     * testYieldAssignment
     */
    public function testYieldAssignment(): void
    {
        $stmt = $this->getFirstYieldStatementInFunction();

        static::assertInstanceOf(ASTYieldStatement::class, $stmt);
        $assignment = $stmt->getParent();
        static::assertInstanceOf(ASTAssignmentExpression::class, $assignment);
        static::assertSame('$result', $assignment->getChild(0)->getImage());

        static::assertSame([
            ASTLiteral::class,
        ], array_map('get_class', $stmt->getChildren()));
        static::assertSame('23', $stmt->getChild(0)->getImage());
    }

    /**
     * testYieldWithLiteral
     */
    public function testYieldWithLiteral(): void
    {
        $stmt = $this->getFirstYieldStatementInFunction();
        static::assertInstanceOf(ASTLiteral::class, $stmt->getChild(0));
    }

    /**
     * testYieldWithVariable
     */
    public function testYieldWithVariable(): void
    {
        $stmt = $this->getFirstYieldStatementInFunction();
        static::assertInstanceOf(ASTVariable::class, $stmt->getChild(0));
    }

    /**
     * testYieldWithKeyValue
     */
    public function testYieldWithKeyValue(): void
    {
        $stmt = $this->getFirstYieldStatementInFunction();
        static::assertInstanceOf(ASTVariable::class, $stmt->getChild(0));
        static::assertInstanceOf(ASTVariable::class, $stmt->getChild(1));
    }

    /**
     * testYieldWithFunctionCalls
     */
    public function testYieldWithFunctionCalls(): void
    {
        $stmt = $this->getFirstYieldStatementInFunction();
        static::assertInstanceOf(ASTFunctionPostfix::class, $stmt->getChild(0));
        static::assertInstanceOf(ASTFunctionPostfix::class, $stmt->getChild(1));
    }

    /**
     * testYieldInsideForeach
     */
    public function testYieldInsideForeach(): void
    {
        $stmt = $this->getFirstYieldStatementInFunction();
        static::assertInstanceOf(ASTForeachStatement::class, $stmt->getParent()?->getParent());
    }

    /**
     * testYieldKeyValue
     *
     * @return ASTExpression[]
     */
    public function testYieldKeyValue(): array
    {
        $stmt = $this->getFirstYieldStatementInFunction();

        /** @var ASTExpression[] */
        $nodes = $stmt->getChildren();

        static::assertCount(2, $nodes);

        return $nodes;
    }

    /**
     * testYieldKeyValueChildNodes
     *
     * @param ASTExpression[] $nodes
     *
     * @depends testYieldKeyValue
     */
    public function testYieldKeyValueChildNodes(array $nodes): void
    {
        static::assertEquals('$id', $nodes[0]->getImage());
        static::assertEquals('$line', $nodes[1]->getImage());
    }

    /**
     * testYieldValueAssignmentSimple
     */
    public function testYieldValueAssignmentSimple(): ASTYieldStatement
    {
        $yield = $this->getFirstYieldStatementInFunction();
        $nodes = $yield->getChildren();

        static::assertCount(1, $nodes);

        return $yield;
    }

    /**
     * testYieldValueAssignmentSimpleParent
     *
     * @depends testYieldValueAssignmentSimple
     */
    public function testYieldValueAssignmentSimpleParent(ASTStatement $yield): void
    {
        static::assertInstanceOf(
            ASTAssignmentExpression::class,
            $yield->getParent()?->getParent()
        );
    }

    /**
     * testYieldValueAssignmentKeyValue
     */
    public function testYieldValueAssignmentKeyValue(): ASTYieldStatement
    {
        $yield = $this->getFirstYieldStatementInFunction();
        $nodes = $yield->getChildren();

        static::assertCount(2, $nodes);

        return $yield;
    }

    /**
     * testYieldValueAssignmentKeyValueParent
     *
     * @depends testYieldValueAssignmentKeyValue
     */
    public function testYieldValueAssignmentKeyValueParent(ASTYieldStatement $yield): void
    {
        static::assertInstanceOf(
            ASTAssignmentExpression::class,
            $yield->getParent()?->getParent()?->getParent()
        );
    }

    /**
     * testYieldValueAssignmentKeyValueChildren
     *
     * @depends testYieldValueAssignmentKeyValue
     */
    public function testYieldValueAssignmentKeyValueChildren(ASTYieldStatement $yield): void
    {
        $nodes = $yield->getChildren();

        static::assertEquals('"key"', $nodes[0]->getImage());
        static::assertEquals('2', $nodes[1]->getImage());
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstYieldStatementInFunction(): ASTYieldStatement
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTYieldStatement::class
        );
    }
}
