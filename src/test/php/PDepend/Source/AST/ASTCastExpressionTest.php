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
 * Test case for the {@link \PDepend\Source\AST\ASTCastExpression} class.
 *
 * @covers \PDepend\Source\AST\ASTCastExpression
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTCastExpressionTest extends ASTNodeTestCase
{
    /**
     * testNormalizesWhitespacesInCastExpression
     */
    public function testNormalizesWhitespacesInCastExpression(): void
    {
        $expr = new ASTCastExpression("\n( float )\t\r");
        static::assertEquals('(float)', $expr->getImage());
    }

    /**
     * testNormalizesCaseInCastExpressionImage
     */
    public function testNormalizesCaseInCastExpressionImage(): void
    {
        $expr = new ASTCastExpression('(DouBlE)');
        static::assertEquals('(double)', $expr->getImage());
    }

    /**
     * testIsBooleanReturnsFalseByDefault
     */
    public function testIsBooleanReturnsFalseByDefault(): void
    {
        $expr = new ASTCastExpression('');
        static::assertFalse($expr->isBoolean());
    }

    /**
     * testIsBooleanReturnsTrueForShortExpression
     */
    public function testIsBooleanReturnsTrueForShortExpression(): void
    {
        $expr = new ASTCastExpression('(bool)');
        static::assertTrue($expr->isBoolean());
    }

    /**
     * testIsBooleanReturnsTrueForLongExpression
     */
    public function testIsBooleanReturnsTrueForLongExpression(): void
    {
        $expr = new ASTCastExpression('(boolean)');
        static::assertTrue($expr->isBoolean());
    }

    /**
     * testIsIntegerReturnsFalseByDefault
     */
    public function testIsIntegerReturnsFalseByDefault(): void
    {
        $expr = new ASTCastExpression('');
        static::assertFalse($expr->isInteger());
    }

    /**
     * testIsIntegerReturnsTrueForShortNotation
     */
    public function testIsIntegerReturnsTrueForShortNotation(): void
    {
        $expr = new ASTCastExpression('(int)');
        static::assertTrue($expr->isInteger());
    }

    /**
     * testIsIntegerReturnsTrueForLongNotation
     */
    public function testIsIntegerReturnsTrueForLongNotation(): void
    {
        $expr = new ASTCastExpression('(integer)');
        static::assertTrue($expr->isInteger());
    }

    /**
     * testIsArrayReturnsFalseByDefault
     */
    public function testIsArrayReturnsFalseByDefault(): void
    {
        $expr = new ASTCastExpression('');
        static::assertFalse($expr->isArray());
    }

    /**
     * testIsArrayReturnsTrueForArrayCast
     */
    public function testIsArrayReturnsTrueForArrayCast(): void
    {
        $expr = new ASTCastExpression('(array)');
        static::assertTrue($expr->isArray());
    }

    /**
     * testIsFloatReturnsFalseByDefault
     */
    public function testIsFloatReturnsFalseByDefault(): void
    {
        $expr = new ASTCastExpression('');
        static::assertFalse($expr->isFloat());
    }

    /**
     * testIsFloatReturnsTrueForRealCast
     */
    public function testIsFloatReturnsTrueForRealCast(): void
    {
        $expr = new ASTCastExpression('(real)');
        static::assertTrue($expr->isFloat());
    }

    /**
     * testIsFloatReturnsTrueForFloatCast
     */
    public function testIsFloatReturnsTrueForFloatCast(): void
    {
        $expr = new ASTCastExpression('(float)');
        static::assertTrue($expr->isFloat());
    }

    /**
     * testIsFloatReturnsTrueForDoubleCast
     */
    public function testIsFloatReturnsTrueForDoubleCast(): void
    {
        $expr = new ASTCastExpression('(double)');
        static::assertTrue($expr->isFloat());
    }

    /**
     * testIsStringReturnsFalseByDefault
     */
    public function testIsStringReturnsFalseByDefault(): void
    {
        $expr = new ASTCastExpression('( )');
        static::assertFalse($expr->isString());
    }

    /**
     * testIsStringReturnsTrueForStringCast
     */
    public function testIsStringReturnsTrueForStringCast(): void
    {
        $expr = new ASTCastExpression('(string)');
        static::assertTrue($expr->isString());
    }

    /**
     * testIsObjectReturnsFalseByDefault
     */
    public function testIsObjectReturnsFalseByDefault(): void
    {
        $expr = new ASTCastExpression('( obj )');
        static::assertFalse($expr->isObject());
    }

    /**
     * testIsObjectReturnsTrueForObjectCast
     */
    public function testIsObjectReturnsTrueForObjectCast(): void
    {
        $expr = new ASTCastExpression('(object)');
        static::assertTrue($expr->isObject());
    }

    /**
     * testIsUnsetReturnsFalseByDefault
     */
    public function testIsUnsetReturnsFalseByDefault(): void
    {
        $expr = new ASTCastExpression('(nu)');
        static::assertFalse($expr->isUnset());
    }

    /**
     * testIsUnsetReturnsTrueForUnsetCast
     */
    public function testIsUnsetReturnsTrueForUnsetCast(): void
    {
        $expr = new ASTCastExpression('(unset)');
        static::assertTrue($expr->isUnset());
    }

    /**
     * testParserHandlesNestedCastExpressions
     */
    public function testParserHandlesNestedCastExpressions(): void
    {
        $expr = $this->getFirstCastExpressionInFunction();
        $this->assertGraphEquals(
            $expr,
            [
                ASTCastExpression::class,
                ASTCastExpression::class,
                ASTCastExpression::class,
                ASTVariable::class,
            ]
        );
    }

    /**
     * testCastExpressionHasExpectedStartLine
     */
    public function testCastExpressionHasExpectedStartLine(): void
    {
        $expr = $this->getFirstCastExpressionInFunction();
        static::assertEquals(4, $expr->getStartLine());
    }

    /**
     * testCastExpressionHasExpectedStartColumn
     */
    public function testCastExpressionHasExpectedStartColumn(): void
    {
        $expr = $this->getFirstCastExpressionInFunction();
        static::assertEquals(12, $expr->getStartColumn());
    }

    /**
     * testCastExpressionHasExpectedEndLine
     */
    public function testCastExpressionHasExpectedEndLine(): void
    {
        $expr = $this->getFirstCastExpressionInFunction();
        static::assertEquals(4, $expr->getEndLine());
    }

    /**
     * testCastExpressionHasExpectedEndColumn
     */
    public function testCastExpressionHasExpectedEndColumn(): void
    {
        $expr = $this->getFirstCastExpressionInFunction();
        static::assertEquals(26, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstCastExpressionInFunction(): ASTCastExpression
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTCastExpression::class
        );
    }
}
