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

    public function testParserHandlesLogicalXorExpressionWithBooleanLiterals()
    {
        $xor = self::_parseExpression('logical_xor_with_boolean_literals.php');
        $this->assertType('PHP_Reflection_AST_LogicalXorExpressionI', $xor);

        $expr = $xor->findChildrenOfType('PHP_Reflection_AST_ExpressionI');
        $this->assertEquals(2, count($expr));

        $this->assertType('PHP_Reflection_AST_BooleanLiteralI', $expr[0]);
        $this->assertTrue($expr[0]->isTrue());
        $this->assertFalse($expr[0]->isFalse());

        $this->assertType('PHP_Reflection_AST_BooleanLiteralI', $expr[1]);
        $this->assertTrue($expr[1]->isFalse());
        $this->assertFalse($expr[1]->isTrue());
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

        $expression = $statement->getFirstChildOfType('PHP_Reflection_AST_ExpressionI');
        self::assertNotNull($expression);

        return $expression;
    }
}
?>