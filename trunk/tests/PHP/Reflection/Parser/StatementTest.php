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

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for statement parsing.
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
class PHP_Reflection_Parser_StatementTest extends PHP_Reflection_AbstractTest
{
    /**
     * Tests a totally ugly notation "do foo(); while($bar);", but its valid.
     *
     * @return void
     */
    public function testParserHandlesDoStatementWithStatement()
    {
        $do = self::_parseStatement('do_with_statement.php');
        $this->assertType('PHP_Reflection_AST_DoStatementI', $do);

        $expr = $do->findChildrenOfType('PHP_Reflection_AST_ExpressionI');
        $this->assertEquals(1, count($expr));

        $stmt = $do->findChildrenOfType('PHP_Reflection_AST_StatementI');
        $this->assertEquals(1, count($stmt));

        $block = $do->findChildrenOfType('PHP_Reflection_AST_BlockI');
        $this->assertEquals(0, count($block));
    }

    public function testParserHandlesDoStatementWithEmptyBlock()
    {
        $do = self::_parseStatement('do_with_empty_block.php');
        $this->assertType('PHP_Reflection_AST_DoStatementI', $do);

        $expr = $do->findChildrenOfType('PHP_Reflection_AST_ExpressionI');
        $this->assertEquals(1, count($expr));

        $block = $do->findChildrenOfType('PHP_Reflection_AST_BlockI');
        $this->assertEquals(1, count($block));

        $stmt = $do->findChildrenOfType('PHP_Reflection_AST_StatementI');
        $this->assertEquals(0, count($stmt));
    }

    /**
     * Tests that the parser handles a for statement without any expressions.
     *
     * @return void
     */
    public function testParserHandlesForStatementWithoutExpressions()
    {
        $for = self::_parseStatement('for_without_expressions.php');
        $this->assertType('PHP_Reflection_AST_ForStatementI', $for);

        $init = $for->findChildrenOfType('PHP_Reflection_AST_ForInitI');
        $this->assertEquals(1, count($init));

        $condition = $for->findChildrenOfType('PHP_Reflection_AST_ForConditionI');
        $this->assertEquals(1, count($condition));

        $update = $for->findChildrenOfType('PHP_Reflection_AST_ForUpdateI');
        $this->assertEquals(1, count($update));

        $block = $for->findChildrenOfType('PHP_Reflection_AST_BlockI');
        $this->assertEquals(1, count($block));
    }

    /**
     * Parses a simple <b>for</b>-statement followed by a semicolon.
     *
     * @return void
     */
    public function testParserHandlesForStatementWithAnEmptyStatementInsteadOfBlock()
    {
        $for = self::_parseStatement('for_with_empty_statement.php');
        $this->assertType('PHP_Reflection_AST_ForStatementI', $for);

        $block = $for->findChildrenOfType('PHP_Reflection_AST_BlockI');
        $this->assertEquals(0, count($block));

        $stmt = $for->findChildrenOfType('PHP_Reflection_AST_StatementI');
        $this->assertEquals(1, count($stmt));
    }

    /**
     * Tests that the parser handles a simple if statement with an empty block
     * correct.
     *
     * @return void
     */
    public function testParserHandlesSimpleIfStatementWithEmptyBlock()
    {
        $if = self::_parseStatement('if_with_empty_block.php');
        $this->assertType('PHP_Reflection_AST_IfStatementI', $if);

        $elseIfs = $if->getChildrenOfType('PHP_Reflection_AST_ElseIfStatementI');
        $this->assertEquals(0, count($elseIfs));

        $else = $if->getChildrenOfType('PHP_Reflection_AST_ElseStatementI');
        $this->assertEquals(0, count($else));

        $block = $if->findChildrenOfType('PHP_Reflection_AST_BlockI');
        $this->assertEquals(1, count($block));

        $stmt = $if->findChildrenOfType('PHP_Reflection_AST_StatementI');
        $this->assertEquals(0, count($stmt));
    }

    /**
     * Tests that the parser handles an if-else combination without curly brace
     * blocks correct.
     *
     * @return void
     */
    public function testParserHandlesIfAndElseStatementWithStatementsInsteadOfBocks()
    {
        $if = self::_parseStatement('if_else_with_statements.php');
        $this->assertType('PHP_Reflection_AST_IfStatementI', $if);

        $else = $if->getFirstChildOfType('PHP_Reflection_AST_ElseStatementI');
        $this->assertNotNull($else);
        $this->assertType('PHP_Reflection_AST_ElseStatementI', $else);

        $this->assertNull($else->getFirstChildOfType('PHP_Reflection_AST_IfStatementI'));
    }

    /**
     * Tests that the parser handles a simple if() {} else if() {} statement
     * combination correct.
     *
     * @return void
     */
    public function testParserHandlesIfAndElseIfStatementWithEmptyBlocks()
    {
        $if = self::_parseStatement('if_else_if_with_empty_blocks.php');
        $this->assertType('PHP_Reflection_AST_IfStatementI', $if);

        $else = $if->getFirstChildOfType('PHP_Reflection_AST_ElseStatementI');
        $this->assertNotNull($else);
        $this->assertType('PHP_Reflection_AST_ElseStatementI', $else);

        $if2 = $else->getFirstChildOfType('PHP_Reflection_AST_IfStatementI');
        $this->assertNotNull($if2);
        $this->assertType('PHP_Reflection_AST_IfStatementI', $if2);
    }

    /**
     * Tests that the parser handles a simple if() {} else if() {} else {}
     * statement combination correct.
     *
     * @return void
     */
    public function testParserHandlesIfAndElseIfAndElseStatementWithEmptyBlocks()
    {
        $if1 = self::_parseStatement('if_else_if_else_with_empty_blocks.php');
        $this->assertType('PHP_Reflection_AST_IfStatementI', $if1);

        $else1 = $if1->getFirstChildOfType('PHP_Reflection_AST_ElseStatementI');
        $this->assertNotNull($else1);
        $this->assertType('PHP_Reflection_AST_ElseStatementI', $else1);

        $if2 = $else1->getFirstChildOfType('PHP_Reflection_AST_IfStatementI');
        $this->assertNotNull($if2);
        $this->assertType('PHP_Reflection_AST_IfStatementI', $if2);

        $else2 = $if2->getFirstChildOfType('PHP_Reflection_AST_ElseStatementI');
        $this->assertNotNull($else2);
        $this->assertType('PHP_Reflection_AST_ElseStatementI', $else2);

        $this->assertNull($else2->getFirstChildOfType('PHP_Reflection_AST_ElseStatementI'));
    }

    /**
     * Tests that the parser handles a <b>if () {} elseif () {} else {}</b>
     * combination correct.
     *
     * @return void
     */
    public function testParserHandlesAlternativeIfAndElseIfAndElseStatementWithoutBlocks()
    {
        $if = self::_parseStatement('if_elseif_else_with_statements.php');
        $this->assertType('PHP_Reflection_AST_IfStatementI', $if);

        $elseIf = $if->getFirstChildOfType('PHP_Reflection_AST_ElseIfStatementI');
        $this->assertNotNull($elseIf);
        $this->assertType('PHP_Reflection_AST_ElseIfStatementI', $elseIf);

        $else = $elseIf->getFirstChildOfType('PHP_Reflection_AST_ElseStatementI');
        $this->assertNotNull($else);
        $this->assertType('PHP_Reflection_AST_ElseStatementI', $else);
    }

    /**
     * Tests that the parser handles a regular while loop statement with two
     * expressions.
     *
     * @return void
     */
    public function testParserHandlesWhileStatementWithExpression()
    {
        $while = self::_parseStatement('while_with_expression.php');
        $this->assertType('PHP_Reflection_AST_WhileStatementI', $while);

        $exprs = $while->getChildrenOfType('PHP_Reflection_AST_ExpressionI');
        $this->assertGreaterThanOrEqual(1, count($exprs));
    }

    /**
     * Tests that the parser throws an exception for while loops without any
     * expression.
     *
     * @return void
     */
    public function testParserThrowsExceptionForWhileStatementWithoutExpression()
    {
        $this->setExpectedException(
            'PHP_Reflection_Exceptions_UnexpectedTokenException',
            'There is an unexpected token ")" on line 3'
        );

        $statement = self::_parseStatement('while_without_expression.php.fail');
    }

    /**
     * Returns the first statement declared in the given file.
     *
     * @param string $file The source file.
     *
     * @return PHP_Reflection_AST_StatementI
     */
    private static function _parseStatement($file)
    {
        $packages = self::parseSource('/parser/statements/' . $file);
        self::assertEquals(1, $packages->count());

        $package = $packages->current();
        self::assertEquals(1, $package->getFunctions()->count());
        $function = $package->getFunctions()->current();

        $block = $function->getFirstChildOfType('PHP_Reflection_AST_BlockI');
        self::assertNotNull($block);

        $statement = $block->getFirstChildOfType('PHP_Reflection_AST_StatementI');
        self::assertNotNull($statement);

        return $statement;
    }
}
?>