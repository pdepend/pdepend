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
 * Test case for the {@link \PDepend\Source\AST\ASTListExpression} class.
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTListExpression
 *
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
     * @return ASTListExpression
     *
     * @since 1.0.2
     */
    public function testListExpression()
    {
        $expr = $this->getFirstListExpressionInFunction();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTListExpression', $expr);

        return $expr;
    }

    /**
     * Tests the start line value.
     *
     * @param ASTListExpression $expr
     *
     * @depends testListExpression
     */
    public function testListExpressionHasExpectedStartLine($expr): void
    {
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @param ASTListExpression $expr
     *
     * @depends testListExpression
     */
    public function testListExpressionHasExpectedStartColumn($expr): void
    {
        $this->assertEquals(5, $expr->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @param ASTListExpression $expr
     *
     * @depends testListExpression
     */
    public function testListExpressionHasExpectedEndLine($expr): void
    {
        $this->assertEquals(4, $expr->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @param ASTListExpression $expr
     *
     * @depends testListExpression
     */
    public function testListExpressionHasExpectedEndColumn($expr): void
    {
        $this->assertEquals(16, $expr->getEndColumn());
    }

    /**
     * testListExpressionWithNestedList
     *
     * @return ASTListExpression
     *
     * @since 1.0.2
     */
    public function testListExpressionWithNestedList()
    {
        $expr = $this->getFirstListExpressionInFunction();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTListExpression', $expr);

        return $expr;
    }

    /**
     * testListExpressionWithNestedListHasExpectedStartLine
     *
     * @param ASTListExpression $expr
     *
     * @since 1.0.2
     *
     * @depends testListExpressionWithNestedList
     */
    public function testListExpressionWithNestedListHasExpectedStartLine($expr): void
    {
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * testListExpressionWithNestedListHasExpectedStartColumn
     *
     * @param ASTListExpression $expr
     *
     * @since 1.0.2
     *
     * @depends testListExpressionWithNestedList
     */
    public function testListExpressionWithNestedListHasExpectedStartColumn($expr): void
    {
        $this->assertEquals(5, $expr->getStartColumn());
    }

    /**
     * testListExpressionWithNestedListHasExpectedEndLine
     *
     * @param ASTListExpression $expr
     *
     * @since 1.0.2
     *
     * @depends testListExpressionWithNestedList
     */
    public function testListExpressionWithNestedListHasExpectedEndLine($expr): void
    {
        $this->assertEquals(4, $expr->getEndLine());
    }

    /**
     * testListExpressionWithNestedListHasExpectedEndColumn
     *
     * @param ASTListExpression $expr
     *
     * @since 1.0.2
     *
     * @depends testListExpressionWithNestedList
     */
    public function testListExpressionWithNestedListHasExpectedEndColumn($expr): void
    {
        $this->assertEquals(42, $expr->getEndColumn());
    }

    /**
     * Tests the list supports many variables in it
     */
    public function testListExpressionSupportsManyVariables(): void
    {
        $expr = $this->getFirstListExpressionInFunction();
        $vars = $expr->getChildren();
        $this->assertCount(3, $vars);
    }

    /**
     * Tests the list supports a single variable
     */
    public function testListExpressionSupportsSingleVariable(): void
    {
        $expr = $this->getFirstListExpressionInFunction();
        $vars = $expr->getChildren();
        $this->assertCount(1, $vars);
    }

    /**
     * Tests the list supports commas without variables
     */
    public function testListExpressionSupportsExtraCommas(): void
    {
        $expr = $this->getFirstListExpressionInFunction();
        $vars = $expr->getChildren();
        $this->assertCount(3, $vars);
    }

    /**
     * testListExpressionWithComments
     */
    public function testListExpressionWithComments(): void
    {
        $expr = $this->getFirstListExpressionInFunction();
        $vars = $expr->getChildren();
        $this->assertCount(3, $vars);
    }

    /**
     * testListExpressionWithoutChildExpression
     */
    public function testListExpressionWithoutChildExpression(): void
    {
        $expr = $this->getFirstListExpressionInFunction();
        $vars = $expr->getChildren();
        $this->assertCount(0, $vars);
    }

    /**
     * testListExpressionWithVariableVariable
     */
    public function testListExpressionWithVariableVariable(): void
    {
        $expr = $this->getFirstListExpressionInFunction();
        $var  = $expr->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariableVariable', $var);
    }

    /**
     * testListExpressionWithSquaredBrackets
     */
    public function testListExpressionWithSquaredBrackets(): void
    {
        $parameters = $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTFormalParameters'
        );

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFormalParameters', $parameters);
    }

    /**
     * testListExpressionWithSquaredBracketsAndEmptySlot
     */
    public function testListExpressionWithSquaredBracketsAndEmptySlot(): void
    {
        $parameters = $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTFormalParameters'
        );

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFormalParameters', $parameters);
    }

    /**
     * testListExpressionWithArrayAndEmptySlot
     */
    public function testListExpressionWithArrayAndEmptySlot(): void
    {
        $this->expectException(
            '\\PDepend\\Source\\Parser\\UnexpectedTokenException'
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
        /** @var ASTArray $expr */
        $array = $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTArray'
        );
        $this->assertCount(1, $array->getChildren());
        $this->assertSame('$b', $array->getChild(0)->getChild(0)->getImage());
    }

    /**
     * testFunctionVoidReturnType
     */
    public function testFunctionVoidReturnType(): void
    {
        /** @var ASTScalarType $type */
        $type = $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            'PDepend\Source\AST\ASTScalarType'
        );

        $this->assertSame('void', $type->getImage());
    }

    /**
     * testListExpressionWithCompoundVariable
     */
    public function testListExpressionWithCompoundVariable(): void
    {
        $expr = $this->getFirstListExpressionInFunction();
        $var  = $expr->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTCompoundVariable', $var);
    }

    /**
     * testListExpressionWithArrayElement
     */
    public function testListExpressionWithArrayElement(): void
    {
        $expr = $this->getFirstListExpressionInFunction();
        $var  = $expr->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTArrayIndexExpression', $var);
    }

    /**
     * testListExpressionWithObjectProperty
     */
    public function testListExpressionWithObjectProperty(): void
    {
        $expr = $this->getFirstListExpressionInFunction();
        $var  = $expr->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMemberPrimaryPrefix', $var);
    }

    /**
     * testListExpressionWithKeys
     *
     * @return ASTListExpression
     *
     * @since 1.0.2
     */
    public function testListExpressionWithKeys()
    {
        $expr = $this->getFirstListExpressionInFunction();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTListExpression', $expr);

        return $expr;
    }

    /**
     * testListExpressionWithKeysAndNestedList
     *
     * @return ASTListExpression
     *
     * @since 1.0.2
     */
    public function testListExpressionWithKeysAndNestedList()
    {
        $expr = $this->getFirstListExpressionInFunction();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTListExpression', $expr);

        return $expr;
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return ASTListExpression
     */
    private function getFirstListExpressionInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTListExpression'
        );
    }
}
