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

use PDepend\Source\Parser\TokenStreamEndException;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTSwitchLabel} class.
 *
 * @covers \PDepend\Source\AST\ASTSwitchLabel
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTSwitchLabelTest extends ASTNodeTestCase
{
    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames(): void
    {
        $label = new ASTSwitchLabel();
        static::assertEquals(
            [
                'default',
                'comment',
                'metadata',
                'nodes',
            ],
            $label->__sleep()
        );
    }

    /**
     * Tests that the default flag is set to <b>true</b> on the default switch
     * label.
     */
    public function testDefaultFlagIsSetOnDefaultLabel(): void
    {
        $label = $this->getFirstSwitchLabelInFunction();
        static::assertTrue($label->isDefault());
    }

    /**
     * Tests that the default flag is set to <b>false</b> on a regular case
     * label.
     */
    public function testDefaultFlagIsNotSetOnCaseLabel(): void
    {
        $label = $this->getFirstSwitchLabelInFunction();
        static::assertFalse($label->isDefault());
    }

    /**
     * testSwitchLabel
     *
     * @return ASTSwitchLabel
     * @since 1.0.2
     */
    public function testSwitchLabel()
    {
        $label = $this->getFirstSwitchLabelInFunction();
        static::assertInstanceOf(ASTSwitchLabel::class, $label);

        return $label;
    }

    /**
     * Tests the start line value.
     *
     * @param ASTSwitchLabel $label
     *
     * @depends testSwitchLabel
     */
    public function testSwitchLabelHasExpectedStartLine($label): void
    {
        static::assertEquals(6, $label->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @param ASTSwitchLabel $label
     *
     * @depends testSwitchLabel
     */
    public function testSwitchLabelHasExpectedStartColumn($label): void
    {
        static::assertEquals(9, $label->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @param ASTSwitchLabel $label
     *
     * @depends testSwitchLabel
     */
    public function testSwitchLabelHasExpectedEndLine($label): void
    {
        static::assertEquals(7, $label->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @param ASTSwitchLabel $label
     *
     * @depends testSwitchLabel
     */
    public function testSwitchLabelHasExpectedEndColumn($label): void
    {
        static::assertEquals(18, $label->getEndColumn());
    }

    /**
     * testSwitchLabelCanBeTerminatedWithSemicolon
     */
    public function testSwitchLabelCanBeTerminatedWithSemicolon(): void
    {
        $this->getFirstSwitchLabelInFunction();
    }

    /**
     * testSwitchLabelWithNestedSwitchStatementHasExpectedChildren
     */
    public function testSwitchLabelWithNestedSwitchStatementHasExpectedChildren(): void
    {
        $label = $this->getFirstSwitchLabelInFunction();

        $actual = [];
        foreach ($label->getChildren() as $child) {
            $actual[] = $child::class;
        }

        $expected = [
            ASTExpression::class,
            ASTSwitchStatement::class,
            ASTBreakStatement::class,
        ];

        static::assertEquals($expected, $actual);
    }

    /**
     * testSwitchLabelWithNestedNonePhpCode
     *
     * @return ASTSwitchLabel
     * @since 2.1.0
     */
    public function testSwitchLabelWithNestedNonePhpCode()
    {
        $label = $this->getFirstSwitchLabelInFunction();
        static::assertInstanceOf(ASTSwitchLabel::class, $label);

        return $label;
    }

    /**
     * testSwitchLabelWithNestedNonePhpCodeStartLine
     *
     * @since 2.1.0
     *
     * @depends testSwitchLabelWithNestedNonePhpCode
     */
    public function testSwitchLabelWithNestedNonePhpCodeStartLine(ASTSwitchLabel $label): void
    {
        static::assertSame(6, $label->getStartLine());
    }

    /**
     * testSwitchLabelWithNestedNonePhpCodeEndLine
     *
     * @since 2.1.0
     *
     * @depends testSwitchLabelWithNestedNonePhpCode
     */
    public function testSwitchLabelWithNestedNonePhpCodeEndLine(ASTSwitchLabel $label): void
    {
        static::assertSame(9, $label->getEndLine());
    }

    /**
     * testSwitchLabelWithNestedNonePhpCodeStartColumn
     *
     * @since 2.1.0
     *
     * @depends testSwitchLabelWithNestedNonePhpCode
     */
    public function testSwitchLabelWithNestedNonePhpCodeStartColumn(ASTSwitchLabel $label): void
    {
        static::assertSame(7, $label->getStartColumn());
    }

    /**
     * testSwitchLabelWithNestedNonePhpCodeEndColumn
     *
     * @since 2.1.0
     *
     * @depends testSwitchLabelWithNestedNonePhpCode
     */
    public function testSwitchLabelWithNestedNonePhpCodeEndColumn(ASTSwitchLabel $label): void
    {
        static::assertSame(5, $label->getEndColumn());
    }

    /**
     * testSwitchLabelDefault
     *
     * @return ASTSwitchLabel
     * @since 1.0.2
     */
    public function testSwitchLabelDefault()
    {
        $label = $this->getFirstSwitchLabelInFunction();
        static::assertInstanceOf(ASTSwitchLabel::class, $label);

        return $label;
    }

    /**
     * Tests the start line value.
     *
     * @param ASTSwitchLabel $label
     *
     * @depends testSwitchLabelDefault
     */
    public function testSwitchLabelDefaultHasExpectedStartLine($label): void
    {
        static::assertEquals(6, $label->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @param ASTSwitchLabel $label
     *
     * @depends testSwitchLabelDefault
     */
    public function testSwitchLabelDefaultHasExpectedStartColumn($label): void
    {
        static::assertEquals(9, $label->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @param ASTSwitchLabel $label
     *
     * @depends testSwitchLabelDefault
     */
    public function testSwitchLabelDefaultHasExpectedEndLine($label): void
    {
        static::assertEquals(7, $label->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @param ASTSwitchLabel $label
     *
     * @depends testSwitchLabelDefault
     */
    public function testSwitchLabelDefaultHasExpectedEndColumn($label): void
    {
        static::assertEquals(18, $label->getEndColumn());
    }

    /**
     * testSwitchDefaultLabelCanBeTerminatedWithSemicolon
     */
    public function testSwitchDefaultLabelCanBeTerminatedWithSemicolon(): void
    {
        $this->getFirstSwitchLabelInFunction();
    }

    /**
     * testSwitchLabelDefaultWithNestedSwitchStatementHasExpectedChildren
     */
    public function testSwitchLabelDefaultWithNestedSwitchStatementHasExpectedChildren(): void
    {
        $label = $this->getFirstSwitchLabelInFunction();

        $actual = [];
        foreach ($label->getChildren() as $child) {
            $actual[] = $child::class;
        }

        $expected = [
            ASTSwitchStatement::class,
            ASTBreakStatement::class,
        ];

        static::assertEquals($expected, $actual);
    }

    /**
     * testSwitchLabelWithNestedNonePhpCode
     *
     * @return ASTSwitchLabel
     * @since 2.1.0
     */
    public function testSwitchLabelDefaultWithNestedNonePhpCode()
    {
        $label = $this->getFirstSwitchLabelInFunction();
        static::assertInstanceOf(ASTSwitchLabel::class, $label);

        return $label;
    }

    /**
     * testSwitchLabelWithNestedNonePhpCodeStartLine
     *
     * @since 2.1.0
     *
     * @depends testSwitchLabelWithNestedNonePhpCode
     */
    public function testSwitchLabelDefaultDefaultWithNestedNonePhpCodeStartLine(ASTSwitchLabel $label): void
    {
        static::assertSame(6, $label->getStartLine());
    }

    /**
     * testSwitchLabelWithNestedNonePhpCodeEndLine
     *
     * @since 2.1.0
     *
     * @depends testSwitchLabelDefaultWithNestedNonePhpCode
     */
    public function testSwitchLabelDefaultWithNestedNonePhpCodeEndLine(ASTSwitchLabel $label): void
    {
        static::assertSame(9, $label->getEndLine());
    }

    /**
     * testSwitchLabelWithNestedNonePhpCodeStartColumn
     *
     * @since 2.1.0
     *
     * @depends testSwitchLabelDefaultWithNestedNonePhpCode
     */
    public function testSwitchLabelDefaultWithNestedNonePhpCodeStartColumn(ASTSwitchLabel $label): void
    {
        static::assertSame(7, $label->getStartColumn());
    }

    /**
     * testSwitchLabelWithNestedNonePhpCodeEndColumn
     *
     * @since 2.1.0
     *
     * @depends testSwitchLabelDefaultWithNestedNonePhpCode
     */
    public function testSwitchLabelDefaultWithNestedNonePhpCodeEndColumn(ASTSwitchLabel $label): void
    {
        static::assertSame(5, $label->getEndColumn());
    }

    /**
     * testParserHandlesSwitchLabelWithNestedScopeStatement
     */
    public function testParserHandlesSwitchLabelWithNestedScopeStatement(): void
    {
        $this->getFirstSwitchLabelInFunction();
    }

    /**
     * testParserThrowsExceptionForUnclosedSwitchLabelBody
     */
    public function testParserThrowsExceptionForUnclosedSwitchLabelBody(): void
    {
        $this->expectException(TokenStreamEndException::class);

        $this->getFirstSwitchLabelInFunction();
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return ASTSwitchLabel
     */
    private function getFirstSwitchLabelInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            ASTSwitchLabel::class
        );
    }
}
