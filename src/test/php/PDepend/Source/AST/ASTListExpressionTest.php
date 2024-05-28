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

use PDepend\Source\Parser\UnexpectedTokenException;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTListExpression} class.
 *
 * @covers \PDepend\Source\AST\ASTListExpression
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTListExpressionTest extends ASTNodeTestCase
{
    /**
     * testListExpression
     *
     * @since 1.0.2
     */
    public function testListExpression(): ASTListExpression
    {
        $expr = $this->getFirstListExpressionInFunction();
        static::assertInstanceOf(ASTListExpression::class, $expr);

        return $expr;
    }

    /**
     * Tests the start line value.
     *
     * @depends testListExpression
     */
    public function testListExpressionHasExpectedStartLine(ASTListExpression $expr): void
    {
        static::assertEquals(4, $expr->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @depends testListExpression
     */
    public function testListExpressionHasExpectedStartColumn(ASTListExpression $expr): void
    {
        static::assertEquals(5, $expr->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @depends testListExpression
     */
    public function testListExpressionHasExpectedEndLine(ASTListExpression $expr): void
    {
        static::assertEquals(4, $expr->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @depends testListExpression
     */
    public function testListExpressionHasExpectedEndColumn(ASTListExpression $expr): void
    {
        static::assertEquals(16, $expr->getEndColumn());
    }

    /**
     * testListExpressionWithNestedList
     *
     * @since 1.0.2
     */
    public function testListExpressionWithNestedList(): ASTListExpression
    {
        $expr = $this->getFirstListExpressionInFunction();
        static::assertInstanceOf(ASTListExpression::class, $expr);

        return $expr;
    }

    /**
     * testListExpressionWithNestedListHasExpectedStartLine
     *
     * @since 1.0.2
     *
     * @depends testListExpressionWithNestedList
     */
    public function testListExpressionWithNestedListHasExpectedStartLine(ASTListExpression $expr): void
    {
        static::assertEquals(4, $expr->getStartLine());
    }

    /**
     * testListExpressionWithNestedListHasExpectedStartColumn
     *
     * @since 1.0.2
     *
     * @depends testListExpressionWithNestedList
     */
    public function testListExpressionWithNestedListHasExpectedStartColumn(ASTListExpression $expr): void
    {
        static::assertEquals(5, $expr->getStartColumn());
    }

    /**
     * testListExpressionWithNestedListHasExpectedEndLine
     *
     * @since 1.0.2
     *
     * @depends testListExpressionWithNestedList
     */
    public function testListExpressionWithNestedListHasExpectedEndLine(ASTListExpression $expr): void
    {
        static::assertEquals(4, $expr->getEndLine());
    }

    /**
     * testListExpressionWithNestedListHasExpectedEndColumn
     *
     * @since 1.0.2
     *
     * @depends testListExpressionWithNestedList
     */
    public function testListExpressionWithNestedListHasExpectedEndColumn(ASTListExpression $expr): void
    {
        static::assertEquals(42, $expr->getEndColumn());
    }

    /**
     * Tests the list supports many variables in it
     */
    public function testListExpressionSupportsManyVariables(): void
    {
        $expr = $this->getFirstListExpressionInFunction();
        $vars = $expr->getChildren();
        static::assertCount(3, $vars);
    }

    /**
     * Tests the list supports a single variable
     */
    public function testListExpressionSupportsSingleVariable(): void
    {
        $expr = $this->getFirstListExpressionInFunction();
        $vars = $expr->getChildren();
        static::assertCount(1, $vars);
    }

    /**
     * Tests the list supports commas without variables
     */
    public function testListExpressionSupportsExtraCommas(): void
    {
        $expr = $this->getFirstListExpressionInFunction();
        $vars = $expr->getChildren();
        static::assertCount(3, $vars);
    }

    /**
     * testListExpressionWithComments
     */
    public function testListExpressionWithComments(): void
    {
        $expr = $this->getFirstListExpressionInFunction();
        $vars = $expr->getChildren();
        static::assertCount(3, $vars);
    }

    /**
     * testListExpressionWithoutChildExpression
     */
    public function testListExpressionWithoutChildExpression(): void
    {
        $expr = $this->getFirstListExpressionInFunction();
        $vars = $expr->getChildren();
        static::assertCount(0, $vars);
    }

    /**
     * testListExpressionWithVariableVariable
     */
    public function testListExpressionWithVariableVariable(): void
    {
        $expr = $this->getFirstListExpressionInFunction();
        $var = $expr->getChild(0);

        static::assertInstanceOf(ASTVariableVariable::class, $var);
    }

    /**
     * testListExpressionWithSquaredBrackets
     */
    public function testListExpressionWithSquaredBrackets(): void
    {
        $parameters = $this->getFirstNodeOfTypeInFunction(
            ASTFormalParameters::class
        );

        static::assertInstanceOf(ASTFormalParameters::class, $parameters);
    }

    /**
     * testListExpressionWithSquaredBracketsAndEmptySlot
     */
    public function testListExpressionWithSquaredBracketsAndEmptySlot(): void
    {
        $parameters = $this->getFirstNodeOfTypeInFunction(
            ASTFormalParameters::class
        );

        static::assertInstanceOf(ASTFormalParameters::class, $parameters);
    }

    /**
     * testListExpressionWithArrayAndEmptySlot
     */
    public function testListExpressionWithArrayAndEmptySlot(): void
    {
        $this->expectException(
            UnexpectedTokenException::class
        );
        $this->expectExceptionMessage(
            'Unexpected token: ,, line: 4, col: 18, file: '
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * testSymmetricArrayDestructuringEmptySlot
     */
    public function testSymmetricArrayDestructuringEmptySlot(): void
    {
        $array = $this->getFirstNodeOfTypeInFunction(
            ASTArray::class
        );
        static::assertCount(1, $array->getChildren());
        static::assertSame('$b', $array->getChild(0)->getChild(0)->getImage());
    }

    /**
     * testFunctionVoidReturnType
     */
    public function testFunctionVoidReturnType(): void
    {
        /** @var ASTScalarType $type */
        $type = $this->getFirstNodeOfTypeInFunction(
            ASTScalarType::class
        );

        static::assertSame('void', $type->getImage());
    }

    /**
     * testListExpressionWithCompoundVariable
     */
    public function testListExpressionWithCompoundVariable(): void
    {
        $expr = $this->getFirstListExpressionInFunction();
        $var = $expr->getChild(0);

        static::assertInstanceOf(ASTCompoundVariable::class, $var);
    }

    /**
     * testListExpressionWithArrayElement
     */
    public function testListExpressionWithArrayElement(): void
    {
        $expr = $this->getFirstListExpressionInFunction();
        $var = $expr->getChild(0);

        static::assertInstanceOf(ASTArrayIndexExpression::class, $var);
    }

    /**
     * testListExpressionWithObjectProperty
     */
    public function testListExpressionWithObjectProperty(): void
    {
        $expr = $this->getFirstListExpressionInFunction();
        $var = $expr->getChild(0);

        static::assertInstanceOf(ASTMemberPrimaryPrefix::class, $var);
    }

    /**
     * testListExpressionWithKeys
     *
     * @since 1.0.2
     */
    public function testListExpressionWithKeys(): ASTListExpression
    {
        $expr = $this->getFirstListExpressionInFunction();
        static::assertInstanceOf(ASTListExpression::class, $expr);

        return $expr;
    }

    /**
     * testListExpressionWithKeysAndNestedList
     *
     * @since 1.0.2
     */
    public function testListExpressionWithKeysAndNestedList(): ASTListExpression
    {
        $expr = $this->getFirstListExpressionInFunction();
        static::assertInstanceOf(ASTListExpression::class, $expr);

        return $expr;
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstListExpressionInFunction(): ASTListExpression
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTListExpression::class
        );
    }
}
