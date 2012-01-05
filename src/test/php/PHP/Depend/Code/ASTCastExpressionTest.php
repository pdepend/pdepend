<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@pdepend.org>.
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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTCastExpression} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 *
 * @covers PHP_Depend_Parser
 * @covers PHP_Depend_Code_ASTCastExpression
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTCastExpressionTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testNormalizesWhitespacesInCastExpression
     *
     * @return void
     */
    public function testNormalizesWhitespacesInCastExpression()
    {
        $expr = new PHP_Depend_Code_ASTCastExpression("\n( float )\t\r");
        $this->assertEquals('(float)', $expr->getImage());
    }

    /**
     * testNormalizesCaseInCastExpressionImage
     *
     * @return void
     */
    public function testNormalizesCaseInCastExpressionImage()
    {
        $expr = new PHP_Depend_Code_ASTCastExpression("(DouBlE)");
        $this->assertEquals('(double)', $expr->getImage());
    }

    /**
     * testIsBooleanReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsBooleanReturnsFalseByDefault()
    {
        $expr = new PHP_Depend_Code_ASTCastExpression('');
        $this->assertFalse($expr->isBoolean());
    }

    /**
     * testIsBooleanReturnsTrueForShortExpression
     *
     * @return void
     */
    public function testIsBooleanReturnsTrueForShortExpression()
    {
        $expr = new PHP_Depend_Code_ASTCastExpression('(bool)');
        $this->assertTrue($expr->isBoolean());
    }

    /**
     * testIsBooleanReturnsTrueForLongExpression
     *
     * @return void
     */
    public function testIsBooleanReturnsTrueForLongExpression()
    {
        $expr = new PHP_Depend_Code_ASTCastExpression('(boolean)');
        $this->assertTrue($expr->isBoolean());
    }

    /**
     * testIsIntegerReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsIntegerReturnsFalseByDefault()
    {
        $expr = new PHP_Depend_Code_ASTCastExpression('');
        $this->assertFalse($expr->isInteger());
    }

    /**
     * testIsIntegerReturnsTrueForShortNotation
     *
     * @return void
     */
    public function testIsIntegerReturnsTrueForShortNotation()
    {
        $expr = new PHP_Depend_Code_ASTCastExpression('(int)');
        $this->assertTrue($expr->isInteger());
    }

    /**
     * testIsIntegerReturnsTrueForLongNotation
     *
     * @return void
     */
    public function testIsIntegerReturnsTrueForLongNotation()
    {
        $expr = new PHP_Depend_Code_ASTCastExpression('(integer)');
        $this->assertTrue($expr->isInteger());
    }

    /**
     * testIsArrayReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsArrayReturnsFalseByDefault()
    {
        $expr = new PHP_Depend_Code_ASTCastExpression('');
        $this->assertFalse($expr->isArray());
    }

    /**
     * testIsArrayReturnsTrueForArrayCast
     *
     * @return void
     */
    public function testIsArrayReturnsTrueForArrayCast()
    {
        $expr = new PHP_Depend_Code_ASTCastExpression('(array)');
        $this->assertTrue($expr->isArray());
    }

    /**
     * testIsFloatReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsFloatReturnsFalseByDefault()
    {
        $expr = new PHP_Depend_Code_ASTCastExpression('');
        $this->assertFalse($expr->isFloat());
    }

    /**
     * testIsFloatReturnsTrueForRealCast
     *
     * @return void
     */
    public function testIsFloatReturnsTrueForRealCast()
    {
        $expr = new PHP_Depend_Code_ASTCastExpression('(real)');
        $this->assertTrue($expr->isFloat());
    }

    /**
     * testIsFloatReturnsTrueForFloatCast
     *
     * @return void
     */
    public function testIsFloatReturnsTrueForFloatCast()
    {
        $expr = new PHP_Depend_Code_ASTCastExpression('(float)');
        $this->assertTrue($expr->isFloat());
    }

    /**
     * testIsFloatReturnsTrueForDoubleCast
     *
     * @return void
     */
    public function testIsFloatReturnsTrueForDoubleCast()
    {
        $expr = new PHP_Depend_Code_ASTCastExpression('(double)');
        $this->assertTrue($expr->isFloat());
    }

    /**
     * testIsStringReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsStringReturnsFalseByDefault()
    {
        $expr = new PHP_Depend_Code_ASTCastExpression('( )');
        $this->assertFalse($expr->isString());
    }

    /**
     * testIsStringReturnsTrueForStringCast
     *
     * @return void
     */
    public function testIsStringReturnsTrueForStringCast()
    {
        $expr = new PHP_Depend_Code_ASTCastExpression('(string)');
        $this->assertTrue($expr->isString());
    }

    /**
     * testIsObjectReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsObjectReturnsFalseByDefault()
    {
        $expr = new PHP_Depend_Code_ASTCastExpression('( obj )');
        $this->assertFalse($expr->isObject());
    }

    /**
     * testIsObjectReturnsTrueForObjectCast
     *
     * @return void
     */
    public function testIsObjectReturnsTrueForObjectCast()
    {
        $expr = new PHP_Depend_Code_ASTCastExpression('(object)');
        $this->assertTrue($expr->isObject());
    }

    /**
     * testIsUnsetReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsUnsetReturnsFalseByDefault()
    {
        $expr = new PHP_Depend_Code_ASTCastExpression('(nu)');
        $this->assertFalse($expr->isUnset());
    }

    /**
     * testIsUnsetReturnsTrueForUnsetCast
     *
     * @return void
     */
    public function testIsUnsetReturnsTrueForUnsetCast()
    {
        $expr = new PHP_Depend_Code_ASTCastExpression('(unset)');
        $this->assertTrue($expr->isUnset());
    }

    /**
     * testParserHandlesNestedCastExpressions
     *
     * @return void
     */
    public function testParserHandlesNestedCastExpressions()
    {
        $expr = $this->_getFirstCastExpressionInFunction(__METHOD__);
        $this->assertGraphEquals(
            $expr,
            array(
                PHP_Depend_Code_ASTCastExpression::CLAZZ,
                PHP_Depend_Code_ASTCastExpression::CLAZZ,
                PHP_Depend_Code_ASTCastExpression::CLAZZ,
                PHP_Depend_Code_ASTVariable::CLAZZ
            )
        );
    }

    /**
     * testCastExpressionHasExpectedStartLine
     *
     * @return void
     */
    public function testCastExpressionHasExpectedStartLine()
    {
        $expr = $this->_getFirstCastExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * testCastExpressionHasExpectedStartColumn
     *
     * @return void
     */
    public function testCastExpressionHasExpectedStartColumn()
    {
        $expr = $this->_getFirstCastExpressionInFunction(__METHOD__);
        $this->assertEquals(12, $expr->getStartColumn());
    }

    /**
     * testCastExpressionHasExpectedEndLine
     *
     * @return void
     */
    public function testCastExpressionHasExpectedEndLine()
    {
        $expr = $this->_getFirstCastExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getEndLine());
    }

    /**
     * testCastExpressionHasExpectedEndColumn
     *
     * @return void
     */
    public function testCastExpressionHasExpectedEndColumn()
    {
        $expr = $this->_getFirstCastExpressionInFunction(__METHOD__);
        $this->assertEquals(26, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTCastExpression
     */
    private function _getFirstCastExpressionInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTCastExpression::CLAZZ
        );
    }
}
