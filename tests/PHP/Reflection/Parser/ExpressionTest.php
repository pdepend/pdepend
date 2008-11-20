<?php
/**
 * This file is part of PHP_Reflection.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pdepend.org>.
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
 * @package    PHP_Reflection
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Reflection/AbstractTest.php';

/**
 * Test case for expression parsing.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_Parser_ExpressionTest extends PHP_Reflection_AbstractTest
{
    /**
     * Tests parsing of a simple variable expression <b>$a;</b>.
     *
     * @return void
     */
    public function testParserHandlesSimpleVariableExpression()
    {
        $var = self::_parseExpression('simple_variable.php');
        $this->assertType('PHP_Reflection_AST_VariableExpressionI', $var);
        $this->assertEquals('$a', $var->getName());
    }

    /**
     * Tests the parsing of a simple logical AND expression with variables.
     *
     * @return void
     */
    public function testParserHandlesLogicalAndExpressionWithVariables()
    {
        $and = self::_parseExpression('logical_and_with_variables.php');
        $this->assertType('PHP_Reflection_AST_LogicalAndExpressionI', $and);

        $expr = $and->findChildrenOfType('PHP_Reflection_AST_ExpressionI');
        $this->assertEquals(2, count($expr));

        $this->assertType('PHP_Reflection_AST_VariableExpressionI', $expr[0]);
        $this->assertEquals('$foo', $expr[0]->getName());

        $this->assertType('PHP_Reflection_AST_VariableExpressionI', $expr[1]);
        $this->assertEquals('$bar', $expr[1]->getName());
    }

    /**
     * Tests the parsing of logical OR-expressions with variables.
     *
     * @return void
     */
    public function testParserHandlesLogicalOrExpressionWithVariables()
    {
        $or = self::_parseExpression('logical_or_with_variables.php');
        $this->assertType('PHP_Reflection_AST_LogicalOrExpressionI', $or);

        $expr = $or->findChildrenOfType('PHP_Reflection_AST_ExpressionI');
        $this->assertEquals(2, count($expr));

        $this->assertType('PHP_Reflection_AST_VariableExpressionI', $expr[0]);
        $this->assertEquals('$barfoo', $expr[0]->getName());

        $this->assertType('PHP_Reflection_AST_VariableExpressionI', $expr[1]);
        $this->assertEquals('$foobar', $expr[1]->getName());
    }

    /**
     * Tests the parsing of logical XOR-expressions with two boolean literal
     * nodes.
     *
     * @return void
     */
    public function testParserHandlesLogicalXorExpressionWithBooleanLiterals()
    {
        $xor = self::_parseExpression('logical_xor_with_boolean_literals.php');
        $this->assertType('PHP_Reflection_AST_LogicalXorExpressionI', $xor);

        $lits = $xor->findChildrenOfType('PHP_Reflection_AST_SourceElementI');
        $this->assertEquals(2, count($lits));

        $this->assertType('PHP_Reflection_AST_BooleanLiteralI', $lits[0]);
        $this->assertTrue($lits[0]->isTrue());
        $this->assertFalse($lits[0]->isFalse());

        $this->assertType('PHP_Reflection_AST_BooleanLiteralI', $lits[1]);
        $this->assertTrue($lits[1]->isFalse());
        $this->assertFalse($lits[1]->isTrue());
    }

    /**
     * Tests the parsing of a boolean AND-expression with boolean and null literals.
     *
     * @return void
     */
    public function testParserHandlesBooleanAndExpressionWithBooleanAndNullLiterals()
    {
        $and = self::_parseExpression('boolean_and_with_boolean_and_null_literals.php');
        $this->assertType('PHP_Reflection_AST_BooleanAndExpressionI', $and);

        $lits = $and->findChildrenOfType('PHP_Reflection_AST_SourceElementI');
        $this->assertEquals(2, count($lits));

        $this->assertType('PHP_Reflection_AST_BooleanLiteralI', $lits[0]);
        $this->assertTrue($lits[0]->isTrue());

        $this->assertType('PHP_Reflection_AST_NullLiteralI', $lits[1]);
    }

    /**
     * Tests the parsing of a boolean OR-expression with numeric literals.
     *
     * @return void
     */
    public function testParserHandlesBooleanOrExpressionWithNumberLiterals()
    {
        $or = self::_parseExpression('boolean_or_with_numeric_literals.php');
        $this->assertType('PHP_Reflection_AST_BooleanOrExpressionI', $or);

        $lits = $or->findChildrenOfType('PHP_Reflection_AST_SourceElementI');
        $this->assertEquals(2, count($lits));

        $this->assertType('PHP_Reflection_AST_LiteralI', $lits[0]);
        $this->assertTrue($lits[0]->isInt());
        $this->assertEquals('23', $lits[0]->getData());

        $this->assertType('PHP_Reflection_AST_LiteralI', $lits[1]);
        $this->assertTrue($lits[1]->isFloat());
        $this->assertEquals('42.0', $lits[1]->getData());
    }

    /**
     * Tests that the parser handles a conditional ?: expression.
     *
     * @return void
     */
    public function testParserHandlesConditionalExpressionWithVariableAndLiterals()
    {
        $expr = self::_parseExpression('conditional_with_variable_and_literals.php');
        $this->assertType('PHP_Reflection_AST_ConditionalExpressionI', $expr);

        $exprs = $expr->getChildrenOfType('PHP_Reflection_AST_SourceElementI');
        $this->assertEquals(3, count($exprs));

        $this->assertType('PHP_Reflection_AST_VariableExpressionI', $exprs[0]);
        $this->assertEquals('$foo', $exprs[0]->getName());

        $this->assertType('PHP_Reflection_AST_LiteralI', $exprs[1]);
        $this->assertTrue($exprs[1]->isString());
        $this->assertEquals("'bar'", $exprs[1]->getData());

        $this->assertType('PHP_Reflection_AST_NullLiteralI', $exprs[2]);
    }

    public function testParserHandlesConditinalExpressionIfsetorVariableAndLiteral()
    {
        $expr = self::_parseExpression('conditional_ifsetor_with_literals.php53');
        $this->assertType('PHP_Reflection_AST_ConditionalExpressionI', $expr);

        $exprs = $expr->getChildrenOfType('PHP_Reflection_AST_SourceElementI');
        $this->assertEquals(2, count($exprs));

        $this->assertType('PHP_Reflection_AST_VariableExpressionI', $exprs[0]);
        $this->assertEquals('$foo', $exprs[0]->getName());

        $this->assertType('PHP_Reflection_AST_LiteralI', $exprs[1]);
        $this->assertTrue($exprs[1]->isString());
        $this->assertEquals("'bar'", $exprs[1]->getData());
    }

    /**
     * Tests a combined expression of addition and multiplication.
     *
     * <code>
     * $a * $b + $c
     * // ----------------------------
     * // - AdditiveExpression
     * //   - MultiplicativeExpression
     * //     - $a
     * //     - $b
     * //   - $c
     * // ----------------------------
     * </code>
     *
     * @return void
     */
    public function testParserHandlesExpressionOrderForAdditionAndMultiplication()
    {
        $expr = self::_parseExpression('multiplication_and_addition.php');
    }

    /**
     * Tests a combined expression of addition and multiplication.
     *
     * <code>
     * $a + $b * $c
     * // ----------------------------
     * // - AdditiveExpression
     * //   - $a
     * //   - MultiplicativeExpression
     * //     - $b
     * //     - $c
     * // ----------------------------
     * </code>
     *
     * @return void
     */
    public function testParserHandlesExpressionOrderForMultiplicationAndAddition()
    {
        $expr = self::_parseExpression('addition_and_multiplication.php');

        $add = $expr->getFirstChildOfType('PHP_Reflection_AST_ExpressionI');
        $this->assertType('PHP_Reflection_AST_AdditiveExpressionI', $add);
    }

    /**
     * Returns the first expression declared in the given file.
     *
     * @param string $file The source file.
     *
     * @return PHP_Reflection_AST_ExpressionI
     */
    private static function _parseExpression($file)
    {
        $packages = self::parseSource('/parser/expressions/' . $file);
        self::assertEquals(1, $packages->count());

        $package = $packages->current();
        self::assertEquals(1, $package->getFunctions()->count());
        $function = $package->getFunctions()->current();

        $block = $function->getFirstChildOfType('PHP_Reflection_AST_BlockI');
        self::assertNotNull($block);

        $statement = $block->getFirstChildOfType('PHP_Reflection_AST_StatementI');
        self::assertNotNull($statement);

        $expression = $statement->getFirstChildOfType('PHP_Reflection_AST_SourceElementI');
        self::assertNotNull($expression);

        return $expression;
    }
}
?>