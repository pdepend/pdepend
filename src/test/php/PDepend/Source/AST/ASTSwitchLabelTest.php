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
 * Test case for the {@link \PDepend\Source\AST\ASTSwitchLabel} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTSwitchLabel
 * @group unittest
 */
class ASTSwitchLabelTest extends ASTNodeTestCase
{
    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     *
     * @return void
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames(): void
    {
        $label = new \PDepend\Source\AST\ASTSwitchLabel();
        $this->assertEquals(
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
    public function testDefaultFlagIsSetOnDefaultLabel(): void
    {
        $label = $this->getFirstSwitchLabelInFunction();
        $this->assertTrue($label->isDefault());
    }

    /**
     * Tests that the default flag is set to <b>false</b> on a regular case
     * label.
     *
     * @return void
     */
    public function testDefaultFlagIsNotSetOnCaseLabel(): void
    {
        $label = $this->getFirstSwitchLabelInFunction();
        $this->assertFalse($label->isDefault());
    }

    /**
     * testSwitchLabel
     *
     * @return \PDepend\Source\AST\ASTSwitchLabel
     * @since 1.0.2
     */
    public function testSwitchLabel()
    {
        $label = $this->getFirstSwitchLabelInFunction();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTSwitchLabel', $label);

        return $label;
    }

    /**
     * Tests the start line value.
     *
     * @param \PDepend\Source\AST\ASTSwitchLabel $label
     *
     * @return void
     * @depends testSwitchLabel
     */
    public function testSwitchLabelHasExpectedStartLine($label): void
    {
        $this->assertEquals(6, $label->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @param \PDepend\Source\AST\ASTSwitchLabel $label
     *
     * @return void
     * @depends testSwitchLabel
     */
    public function testSwitchLabelHasExpectedStartColumn($label): void
    {
        $this->assertEquals(9, $label->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @param \PDepend\Source\AST\ASTSwitchLabel $label
     *
     * @return void
     * @depends testSwitchLabel
     */
    public function testSwitchLabelHasExpectedEndLine($label): void
    {
        $this->assertEquals(7, $label->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @param \PDepend\Source\AST\ASTSwitchLabel $label
     *
     * @return void
     * @depends testSwitchLabel
     */
    public function testSwitchLabelHasExpectedEndColumn($label): void
    {
        $this->assertEquals(18, $label->getEndColumn());
    }

    /**
     * testSwitchLabelCanBeTerminatedWithSemicolon
     *
     * @return void
     */
    public function testSwitchLabelCanBeTerminatedWithSemicolon(): void
    {
        $this->getFirstSwitchLabelInFunction();
    }

    /**
     * testSwitchLabelWithNestedSwitchStatementHasExpectedChildren
     *
     * @return void
     */
    public function testSwitchLabelWithNestedSwitchStatementHasExpectedChildren(): void
    {
        $label = $this->getFirstSwitchLabelInFunction();

        $actual = array();
        foreach ($label->getChildren() as $child) {
            $actual[] = get_class($child);
        }

        $expected = array(
            'PDepend\\Source\\AST\\ASTExpression',
            'PDepend\\Source\\AST\\ASTSwitchStatement',
            'PDepend\\Source\\AST\\ASTBreakStatement'
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testSwitchLabelWithNestedNonePhpCode
     *
     * @return \PDepend\Source\AST\ASTSwitchLabel
     * @since 2.1.0
     */
    public function testSwitchLabelWithNestedNonePhpCode()
    {
        $label = $this->getFirstSwitchLabelInFunction();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTSwitchLabel', $label);

        return $label;
    }

    /**
     * testSwitchLabelWithNestedNonePhpCodeStartLine
     *
     * @param \PDepend\Source\AST\ASTSwitchLabel $label
     * @return void
     * @since 2.1.0
     * @depends testSwitchLabelWithNestedNonePhpCode
     */
    public function testSwitchLabelWithNestedNonePhpCodeStartLine(ASTSwitchLabel $label): void
    {
        $this->assertSame(6, $label->getStartLine());
    }

    /**
     * testSwitchLabelWithNestedNonePhpCodeEndLine
     *
     * @param \PDepend\Source\AST\ASTSwitchLabel $label
     * @return void
     * @since 2.1.0
     * @depends testSwitchLabelWithNestedNonePhpCode
     */
    public function testSwitchLabelWithNestedNonePhpCodeEndLine(ASTSwitchLabel $label): void
    {
        $this->assertSame(9, $label->getEndLine());
    }

    /**
     * testSwitchLabelWithNestedNonePhpCodeStartColumn
     *
     * @param \PDepend\Source\AST\ASTSwitchLabel $label
     * @return void
     * @since 2.1.0
     * @depends testSwitchLabelWithNestedNonePhpCode
     */
    public function testSwitchLabelWithNestedNonePhpCodeStartColumn(ASTSwitchLabel $label): void
    {
        $this->assertSame(7, $label->getStartColumn());
    }

    /**
     * testSwitchLabelWithNestedNonePhpCodeEndColumn
     *
     * @param \PDepend\Source\AST\ASTSwitchLabel $label
     * @return void
     * @since 2.1.0
     * @depends testSwitchLabelWithNestedNonePhpCode
     */
    public function testSwitchLabelWithNestedNonePhpCodeEndColumn(ASTSwitchLabel $label): void
    {
        $this->assertSame(5, $label->getEndColumn());
    }

    /**
     * testSwitchLabelDefault
     *
     * @return \PDepend\Source\AST\ASTSwitchLabel
     * @since 1.0.2
     */
    public function testSwitchLabelDefault()
    {
        $label = $this->getFirstSwitchLabelInFunction();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTSwitchLabel', $label);

        return $label;
    }

    /**
     * Tests the start line value.
     *
     * @param \PDepend\Source\AST\ASTSwitchLabel $label
     *
     * @return void
     * @depends testSwitchLabelDefault
     */
    public function testSwitchLabelDefaultHasExpectedStartLine($label): void
    {
        $this->assertEquals(6, $label->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @param \PDepend\Source\AST\ASTSwitchLabel $label
     *
     * @return void
     * @depends testSwitchLabelDefault
     */
    public function testSwitchLabelDefaultHasExpectedStartColumn($label): void
    {
        $this->assertEquals(9, $label->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @param \PDepend\Source\AST\ASTSwitchLabel $label
     *
     * @return void
     * @depends testSwitchLabelDefault
     */
    public function testSwitchLabelDefaultHasExpectedEndLine($label): void
    {
        $this->assertEquals(7, $label->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @param \PDepend\Source\AST\ASTSwitchLabel $label
     *
     * @return void
     * @depends testSwitchLabelDefault
     */
    public function testSwitchLabelDefaultHasExpectedEndColumn($label): void
    {
        $this->assertEquals(18, $label->getEndColumn());
    }

    /**
     * testSwitchDefaultLabelCanBeTerminatedWithSemicolon
     *
     * @return void
     */
    public function testSwitchDefaultLabelCanBeTerminatedWithSemicolon(): void
    {
        $this->getFirstSwitchLabelInFunction();
    }

    /**
     * testSwitchLabelDefaultWithNestedSwitchStatementHasExpectedChildren
     *
     * @return void
     */
    public function testSwitchLabelDefaultWithNestedSwitchStatementHasExpectedChildren(): void
    {
        $label = $this->getFirstSwitchLabelInFunction();

        $actual = array();
        foreach ($label->getChildren() as $child) {
            $actual[] = get_class($child);
        }

        $expected = array(
            'PDepend\\Source\\AST\\ASTSwitchStatement',
            'PDepend\\Source\\AST\\ASTBreakStatement'
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testSwitchLabelWithNestedNonePhpCode
     *
     * @return \PDepend\Source\AST\ASTSwitchLabel
     * @since 2.1.0
     */
    public function testSwitchLabelDefaultWithNestedNonePhpCode()
    {
        $label = $this->getFirstSwitchLabelInFunction();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTSwitchLabel', $label);

        return $label;
    }

    /**
     * testSwitchLabelWithNestedNonePhpCodeStartLine
     *
     * @param \PDepend\Source\AST\ASTSwitchLabel $label
     * @return void
     * @since 2.1.0
     * @depends testSwitchLabelWithNestedNonePhpCode
     */
    public function testSwitchLabelDefaultDefaultWithNestedNonePhpCodeStartLine(ASTSwitchLabel $label): void
    {
        $this->assertSame(6, $label->getStartLine());
    }

    /**
     * testSwitchLabelWithNestedNonePhpCodeEndLine
     *
     * @param \PDepend\Source\AST\ASTSwitchLabel $label
     * @return void
     * @since 2.1.0
     * @depends testSwitchLabelDefaultWithNestedNonePhpCode
     */
    public function testSwitchLabelDefaultWithNestedNonePhpCodeEndLine(ASTSwitchLabel $label): void
    {
        $this->assertSame(9, $label->getEndLine());
    }

    /**
     * testSwitchLabelWithNestedNonePhpCodeStartColumn
     *
     * @param \PDepend\Source\AST\ASTSwitchLabel $label
     * @return void
     * @since 2.1.0
     * @depends testSwitchLabelDefaultWithNestedNonePhpCode
     */
    public function testSwitchLabelDefaultWithNestedNonePhpCodeStartColumn(ASTSwitchLabel $label): void
    {
        $this->assertSame(7, $label->getStartColumn());
    }

    /**
     * testSwitchLabelWithNestedNonePhpCodeEndColumn
     *
     * @param \PDepend\Source\AST\ASTSwitchLabel $label
     * @return void
     * @since 2.1.0
     * @depends testSwitchLabelDefaultWithNestedNonePhpCode
     */
    public function testSwitchLabelDefaultWithNestedNonePhpCodeEndColumn(ASTSwitchLabel $label): void
    {
        $this->assertSame(5, $label->getEndColumn());
    }

    /**
     * testParserHandlesSwitchLabelWithNestedScopeStatement
     *
     * @return void
     */
    public function testParserHandlesSwitchLabelWithNestedScopeStatement(): void
    {
        $this->getFirstSwitchLabelInFunction();
    }

    /**
     * testParserThrowsExceptionForUnclosedSwitchLabelBody
     *
     * @return void
     */
    public function testParserThrowsExceptionForUnclosedSwitchLabelBody(): void
    {
        $this->expectException(\PDepend\Source\Parser\TokenStreamEndException::class);

        $this->getFirstSwitchLabelInFunction();
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return \PDepend\Source\AST\ASTSwitchLabel
     */
    private function getFirstSwitchLabelInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTSwitchLabel'
        );
    }
}
