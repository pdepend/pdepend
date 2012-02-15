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
 * Test case for the {@link PHP_Depend_Code_ASTConstantDeclarator} class.
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
 * @covers PHP_Depend_Code_ASTConstantDeclarator
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTConstantDeclaratorTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testReturnValueOfMagicSleepContainsValueProperty
     *
     * @return void
     */
    public function testReturnValueOfMagicSleepContainsValueProperty()
    {
        $node = new PHP_Depend_Code_ASTConstantDeclarator();
        self::assertEquals(
            array(
                'value',
                'comment',
                'metadata',
                'nodes'
            ),
            $node->__sleep()
        );
    }

    /**
     * testParserInjectsValueObjectIntoConstantDeclarator
     *
     * @return void
     */
    public function testParserInjectsValueObjectIntoConstantDeclarator()
    {
        $declarator = $this->_getFirstConstantDeclaratorInClass();
        self::assertInstanceOf(PHP_Depend_Code_Value::CLAZZ, $declarator->getValue());
    }

    /**
     * testParserInjectsExpectedScalarValueIntoConstantDeclarator
     *
     * @return void
     */
    public function testParserInjectsExpectedScalarValueIntoConstantDeclarator()
    {
        $declarator = $this->_getFirstConstantDeclaratorInClass();
        self::assertEquals(42, $declarator->getValue()->getValue());
    }

    /**
     * testParserInjectsExpectedHeredocValueIntoConstantDeclarator
     *
     * @return void
     * @since 0.10.9
     */
    public function testParserInjectsExpectedHeredocValueIntoConstantDeclarator()
    {
        $declarator = $this->_getFirstConstantDeclaratorInClass();
        self::assertEquals('Testing!', $declarator->getValue()->getValue());
    }

    /**
     * testConstantDeclarator
     *
     * @return PHP_Depend_Code_ASTConstantDeclarator
     * @since 1.0.2
     */
    public function testConstantDeclarator()
    {
        $declarator = $this->_getFirstConstantDeclaratorInClass();
        $this->assertInstanceOf(PHP_Depend_Code_ASTConstantDeclarator::CLAZZ, $declarator);

        return $declarator;
    }

    /**
     * testConstantDeclaratorHasExpectedStartLine
     *
     * @param PHP_Depend_Code_ASTConstantDeclarator $declarator
     *
     * @return void
     * @depends testConstantDeclarator
     */
    public function testConstantDeclaratorHasExpectedStartLine($declarator)
    {
        $this->assertEquals(5, $declarator->getStartLine());
    }

    /**
     * testConstantDeclaratorHasExpectedStartColumn
     *
     * @param PHP_Depend_Code_ASTConstantDeclarator $declarator
     *
     * @return void
     * @depends testConstantDeclarator
     */
    public function testConstantDeclaratorHasExpectedStartColumn($declarator)
    {
        $this->assertEquals(7, $declarator->getStartColumn());
    }

    /**
     * testConstantDeclaratorHasExpectedEndLine
     *
     * @param PHP_Depend_Code_ASTConstantDeclarator $declarator
     *
     * @return void
     * @depends testConstantDeclarator
     */
    public function testConstantDeclaratorHasExpectedEndLine($declarator)
    {
        $this->assertEquals(7, $declarator->getEndLine());
    }

    /**
     * testConstantDeclaratorHasExpectedEndColumn
     *
     * @param PHP_Depend_Code_ASTConstantDeclarator $declarator
     *
     * @return void
     * @depends testConstantDeclarator
     */
    public function testConstantDeclaratorHasExpectedEndColumn($declarator)
    {
        $this->assertEquals(14, $declarator->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return PHP_Depend_Code_ASTConstantDeclarator
     */
    private function _getFirstConstantDeclaratorInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(), 
            PHP_Depend_Code_ASTConstantDeclarator::CLAZZ
        );
    }
}
