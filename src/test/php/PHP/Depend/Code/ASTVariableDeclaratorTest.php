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
 * Test case for the {@link PHP_Depend_Code_ASTVariableDeclarator} class.
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
 * @covers PHP_Depend_Code_ASTVariableDeclarator
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTVariableDeclaratorTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testGetValueReturnsNullByDefault
     *
     * @return void
     */
    public function testGetValueReturnsNullByDefault()
    {
        $declarator = new PHP_Depend_Code_ASTVariableDeclarator();
        self::assertNull($declarator->getValue());
    }

    /**
     * testGetValueReturnsInjectedValueInstance
     *
     * @return void
     */
    public function testGetValueReturnsInjectedValueInstance()
    {
        $declarator = new PHP_Depend_Code_ASTVariableDeclarator();
        $declarator->setValue(new PHP_Depend_Code_Value());

        self::assertInstanceOf(PHP_Depend_Code_Value::CLAZZ, $declarator->getValue());
    }

    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     *
     * @return void
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames()
    {
        $declarator = new PHP_Depend_Code_ASTVariableDeclarator();
        self::assertEquals(
            array(
                'value',
                'comment',
                'metadata',
                'nodes'
            ),
            $declarator->__sleep()
        );
    }

    /**
     * testVariableDeclarator
     *
     * @return PHP_Depend_Code_ASTVariableDeclarator
     * @since 1.0.2
     */
    public function testVariableDeclarator()
    {
        $declarator = $this->_getFirstVariableDeclaratorInFunction();
        $this->assertInstanceOf(PHP_Depend_Code_ASTVariableDeclarator::CLAZZ, $declarator);

        return $declarator;
    }

    /**
     * testVariableDeclaratorHasExpectedStartLine
     *
     * @param PHP_Depend_Code_ASTVariableDeclarator $declarator
     *
     * @return void
     * @depends testVariableDeclarator
     */
    public function testVariableDeclaratorHasExpectedStartLine($declarator)
    {
        $this->assertEquals(4, $declarator->getStartLine());
    }

    /**
     * testVariableDeclaratorHasExpectedStartColumn
     *
     * @param PHP_Depend_Code_ASTVariableDeclarator $declarator
     *
     * @return void
     * @depends testVariableDeclarator
     */
    public function testVariableDeclaratorHasExpectedStartColumn($declarator)
    {
        $this->assertEquals(12, $declarator->getStartColumn());
    }

    /**
     * testVariableDeclaratorHasExpectedEndLine
     *
     * @param PHP_Depend_Code_ASTVariableDeclarator $declarator
     *
     * @return void
     * @depends testVariableDeclarator
     */
    public function testVariableDeclaratorHasExpectedEndLine($declarator)
    {
        $this->assertEquals(4, $declarator->getEndLine());
    }

    /**
     * testVariableDeclaratorHasExpectedEndColumn
     *
     * @param PHP_Depend_Code_ASTVariableDeclarator $declarator
     *
     * @return void
     * @depends testVariableDeclarator
     */
    public function testVariableDeclaratorHasExpectedEndColumn($declarator)
    {
        $this->assertEquals(17, $declarator->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return PHP_Depend_Code_ASTVariableDeclarator
     */
    private function _getFirstVariableDeclaratorInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            PHP_Depend_Code_ASTVariableDeclarator::CLAZZ
        );
    }
}
