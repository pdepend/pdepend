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
 * Test case for the {@link PHP_Depend_Code_ASTSwitchLabel} class.
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
 * @covers PHP_Depend_Code_ASTSwitchLabel
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTSwitchLabelTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     *
     * @return void
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames()
    {
        $label = new PHP_Depend_Code_ASTSwitchLabel();
        self::assertEquals(
            array(
                'default',
                'comment',
                'metadata',
                'nodes'
            ),
            $label->__sleep()
        );
    }

    /**
     * Tests that the default flag is set to <b>true</b> on the default switch
     * label.
     *
     * @return void
     */
    public function testDefaultFlagIsSetOnDefaultLabel()
    {
        $label = $this->_getFirstSwitchLabelInFunction();
        $this->assertTrue($label->isDefault());
    }

    /**
     * Tests that the default flag is set to <b>false</b> on a regular case
     * label.
     *
     * @return void
     */
    public function testDefaultFlagIsNotSetOnCaseLabel()
    {
        $label = $this->_getFirstSwitchLabelInFunction();
        $this->assertFalse($label->isDefault());
    }

    /**
     * testSwitchLabel
     *
     * @return PHP_Depend_Code_ASTSwitchLabel
     * @since 1.0.2
     */
    public function testSwitchLabel()
    {
        $label = $this->_getFirstSwitchLabelInFunction();
        $this->assertInstanceOf(PHP_Depend_Code_ASTSwitchLabel::CLAZZ, $label);

        return $label;
    }

    /**
     * Tests the start line value.
     *
     * @param PHP_Depend_Code_ASTSwitchLabel $label
     *
     * @return void
     * @depends testSwitchLabel
     */
    public function testSwitchLabelHasExpectedStartLine($label)
    {
        $this->assertEquals(6, $label->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @param PHP_Depend_Code_ASTSwitchLabel $label
     *
     * @return void
     * @depends testSwitchLabel
     */
    public function testSwitchLabelHasExpectedStartColumn($label)
    {
        $this->assertEquals(9, $label->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @param PHP_Depend_Code_ASTSwitchLabel $label
     *
     * @return void
     * @depends testSwitchLabel
     */
    public function testSwitchLabelHasExpectedEndLine($label)
    {
        $this->assertEquals(7, $label->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @param PHP_Depend_Code_ASTSwitchLabel $label
     *
     * @return void
     * @depends testSwitchLabel
     */
    public function testSwitchLabelHasExpectedEndColumn($label)
    {
        $this->assertEquals(18, $label->getEndColumn());
    }

    /**
     * testSwitchLabelCanBeTerminatedWithSemicolon
     *
     * @return void
     */
    public function testSwitchLabelCanBeTerminatedWithSemicolon()
    {
        $this->_getFirstSwitchLabelInFunction();
    }

    /**
     * testSwitchLabelWithNestedSwitchStatementHasExpectedChildren
     *
     * @return void
     */
    public function testSwitchLabelWithNestedSwitchStatementHasExpectedChildren()
    {
        $label = $this->_getFirstSwitchLabelInFunction();

        $actual = array();
        foreach ($label->getChildren() as $child) {
            $actual[] = get_class($child);
        }

        $expected = array(
            PHP_Depend_Code_ASTExpression::CLAZZ,
            PHP_Depend_Code_ASTSwitchStatement::CLAZZ,
            PHP_Depend_Code_ASTBreakStatement::CLAZZ
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testSwitchLabelDefault
     *
     * @return PHP_Depend_Code_ASTSwitchLabel
     * @since 1.0.2
     */
    public function testSwitchLabelDefault()
    {
        $label = $this->_getFirstSwitchLabelInFunction();
        $this->assertInstanceOf(PHP_Depend_Code_ASTSwitchLabel::CLAZZ, $label);

        return $label;
    }

    /**
     * Tests the start line value.
     *
     * @param PHP_Depend_Code_ASTSwitchLabel $label
     *
     * @return void
     * @depends testSwitchLabelDefault
     */
    public function testSwitchLabelDefaultHasExpectedStartLine($label)
    {
        $this->assertEquals(6, $label->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @param PHP_Depend_Code_ASTSwitchLabel $label
     *
     * @return void
     * @depends testSwitchLabelDefault
     */
    public function testSwitchLabelDefaultHasExpectedStartColumn($label)
    {
        $this->assertEquals(9, $label->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @param PHP_Depend_Code_ASTSwitchLabel $label
     *
     * @return void
     * @depends testSwitchLabelDefault
     */
    public function testSwitchLabelDefaultHasExpectedEndLine($label)
    {
        $this->assertEquals(7, $label->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @param PHP_Depend_Code_ASTSwitchLabel $label
     *
     * @return void
     * @depends testSwitchLabelDefault
     */
    public function testSwitchLabelDefaultHasExpectedEndColumn($label)
    {
        $this->assertEquals(18, $label->getEndColumn());
    }

    /**
     * testSwitchDefaultLabelCanBeTerminatedWithSemicolon
     *
     * @return void
     */
    public function testSwitchDefaultLabelCanBeTerminatedWithSemicolon()
    {
        $this->_getFirstSwitchLabelInFunction();
    }

    /**
     * testSwitchLabelDefaultWithNestedSwitchStatementHasExpectedChildren
     *
     * @return void
     */
    public function testSwitchLabelDefaultWithNestedSwitchStatementHasExpectedChildren()
    {
        $label = $this->_getFirstSwitchLabelInFunction();

        $actual = array();
        foreach ($label->getChildren() as $child) {
            $actual[] = get_class($child);
        }

        $expected = array(
            PHP_Depend_Code_ASTSwitchStatement::CLAZZ,
            PHP_Depend_Code_ASTBreakStatement::CLAZZ
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testParserHandlesSwitchLabelWithNestedScopeStatement
     *
     * @return void
     */
    public function testParserHandlesSwitchLabelWithNestedScopeStatement()
    {
        $this->_getFirstSwitchLabelInFunction();
    }

    /**
     * testParserThrowsExceptionForUnclosedSwitchLabelBody
     *
     * @return void
     * @expectedException PHP_Depend_Parser_TokenStreamEndException
     */
    public function testParserThrowsExceptionForUnclosedSwitchLabelBody()
    {
        $this->_getFirstSwitchLabelInFunction();
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return PHP_Depend_Code_ASTSwitchLabel
     */
    private function _getFirstSwitchLabelInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            PHP_Depend_Code_ASTSwitchLabel::CLAZZ
        );
    }
}
