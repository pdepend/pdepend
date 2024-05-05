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
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTCastExpression
 *
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
        $this->assertEquals('(float)', $expr->getImage());
    }

    /**
     * testNormalizesCaseInCastExpressionImage
     */
    public function testNormalizesCaseInCastExpressionImage(): void
    {
        $expr = new ASTCastExpression("(DouBlE)");
        $this->assertEquals('(double)', $expr->getImage());
    }

    /**
     * testIsBooleanReturnsFalseByDefault
     */
    public function testIsBooleanReturnsFalseByDefault(): void
    {
        $expr = new ASTCastExpression('');
        $this->assertFalse($expr->isBoolean());
    }

    /**
     * testIsBooleanReturnsTrueForShortExpression
     */
    public function testIsBooleanReturnsTrueForShortExpression(): void
    {
        $expr = new ASTCastExpression('(bool)');
        $this->assertTrue($expr->isBoolean());
    }

    /**
     * testIsBooleanReturnsTrueForLongExpression
     */
    public function testIsBooleanReturnsTrueForLongExpression(): void
    {
        $expr = new ASTCastExpression('(boolean)');
        $this->assertTrue($expr->isBoolean());
    }

    /**
     * testIsIntegerReturnsFalseByDefault
     */
    public function testIsIntegerReturnsFalseByDefault(): void
    {
        $expr = new ASTCastExpression('');
        $this->assertFalse($expr->isInteger());
    }

    /**
     * testIsIntegerReturnsTrueForShortNotation
     */
    public function testIsIntegerReturnsTrueForShortNotation(): void
    {
        $expr = new ASTCastExpression('(int)');
        $this->assertTrue($expr->isInteger());
    }

    /**
     * testIsIntegerReturnsTrueForLongNotation
     */
    public function testIsIntegerReturnsTrueForLongNotation(): void
    {
        $expr = new ASTCastExpression('(integer)');
        $this->assertTrue($expr->isInteger());
    }

    /**
     * testIsArrayReturnsFalseByDefault
     */
    public function testIsArrayReturnsFalseByDefault(): void
    {
        $expr = new ASTCastExpression('');
        $this->assertFalse($expr->isArray());
    }

    /**
     * testIsArrayReturnsTrueForArrayCast
     */
    public function testIsArrayReturnsTrueForArrayCast(): void
    {
        $expr = new ASTCastExpression('(array)');
        $this->assertTrue($expr->isArray());
    }

    /**
     * testIsFloatReturnsFalseByDefault
     */
    public function testIsFloatReturnsFalseByDefault(): void
    {
        $expr = new ASTCastExpression('');
        $this->assertFalse($expr->isFloat());
    }

    /**
     * testIsFloatReturnsTrueForRealCast
     */
    public function testIsFloatReturnsTrueForRealCast(): void
    {
        $expr = new ASTCastExpression('(real)');
        $this->assertTrue($expr->isFloat());
    }

    /**
     * testIsFloatReturnsTrueForFloatCast
     */
    public function testIsFloatReturnsTrueForFloatCast(): void
    {
        $expr = new ASTCastExpression('(float)');
        $this->assertTrue($expr->isFloat());
    }

    /**
     * testIsFloatReturnsTrueForDoubleCast
     */
    public function testIsFloatReturnsTrueForDoubleCast(): void
    {
        $expr = new ASTCastExpression('(double)');
        $this->assertTrue($expr->isFloat());
    }

    /**
     * testIsStringReturnsFalseByDefault
     */
    public function testIsStringReturnsFalseByDefault(): void
    {
        $expr = new ASTCastExpression('( )');
        $this->assertFalse($expr->isString());
    }

    /**
     * testIsStringReturnsTrueForStringCast
     */
    public function testIsStringReturnsTrueForStringCast(): void
    {
        $expr = new ASTCastExpression('(string)');
        $this->assertTrue($expr->isString());
    }

    /**
     * testIsObjectReturnsFalseByDefault
     */
    public function testIsObjectReturnsFalseByDefault(): void
    {
        $expr = new ASTCastExpression('( obj )');
        $this->assertFalse($expr->isObject());
    }

    /**
     * testIsObjectReturnsTrueForObjectCast
     */
    public function testIsObjectReturnsTrueForObjectCast(): void
    {
        $expr = new ASTCastExpression('(object)');
        $this->assertTrue($expr->isObject());
    }

    /**
     * testIsUnsetReturnsFalseByDefault
     */
    public function testIsUnsetReturnsFalseByDefault(): void
    {
        $expr = new ASTCastExpression('(nu)');
        $this->assertFalse($expr->isUnset());
    }

    /**
     * testIsUnsetReturnsTrueForUnsetCast
     */
    public function testIsUnsetReturnsTrueForUnsetCast(): void
    {
        $expr = new ASTCastExpression('(unset)');
        $this->assertTrue($expr->isUnset());
    }

    /**
     * testParserHandlesNestedCastExpressions
     */
    public function testParserHandlesNestedCastExpressions(): void
    {
        $expr = $this->getFirstCastExpressionInFunction(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            [
                'PDepend\\Source\\AST\\ASTCastExpression',
                'PDepend\\Source\\AST\\ASTCastExpression',
                'PDepend\\Source\\AST\\ASTCastExpression',
                'PDepend\\Source\\AST\\ASTVariable',
            ]
        );
    }

    /**
     * testCastExpressionHasExpectedStartLine
     */
    public function testCastExpressionHasExpectedStartLine(): void
    {
        $expr = $this->getFirstCastExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * testCastExpressionHasExpectedStartColumn
     */
    public function testCastExpressionHasExpectedStartColumn(): void
    {
        $expr = $this->getFirstCastExpressionInFunction(__METHOD__);
        $this->assertEquals(12, $expr->getStartColumn());
    }

    /**
     * testCastExpressionHasExpectedEndLine
     */
    public function testCastExpressionHasExpectedEndLine(): void
    {
        $expr = $this->getFirstCastExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getEndLine());
    }

    /**
     * testCastExpressionHasExpectedEndColumn
     */
    public function testCastExpressionHasExpectedEndColumn(): void
    {
        $expr = $this->getFirstCastExpressionInFunction(__METHOD__);
        $this->assertEquals(26, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return ASTCastExpression
     */
    private function getFirstCastExpressionInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase,
            'PDepend\\Source\\AST\\ASTCastExpression'
        );
    }
}
