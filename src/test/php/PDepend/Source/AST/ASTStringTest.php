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

use PDepend\Source\Parser\TokenException;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTString} class.
 *
 * @covers \PDepend\Source\AST\ASTString
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTStringTest extends ASTNodeTestCase
{
    /**
     * testDoubleQuoteStringContainsTwoChildNodes
     */
    public function testDoubleQuoteStringContainsTwoChildNodes(): void
    {
        $string = $this->getFirstStringInFunction();
        static::assertCount(2, $string->getChildren());
    }

    /**
     * testDoubleQuoteStringContainsExpectedTextContent
     */
    public function testDoubleQuoteStringContainsExpectedTextContent(): void
    {
        $string = $this->getFirstStringInFunction();
        static::assertStringContainsString('Hello', $string->getChild(0)->getImage());
    }

    /**
     * testBacktickExpressionContainsTwoChildNodes
     */
    public function testBacktickExpressionContainsTwoChildNodes(): void
    {
        $string = $this->getFirstStringInFunction();
        static::assertCount(2, $string->getChildren());
    }

    /**
     * testBacktickExpressionContainsExpectedCompoundVariable
     */
    public function testBacktickExpressionContainsExpectedCompoundVariable(): void
    {
        $string = $this->getFirstStringInFunction();
        static::assertInstanceOf(ASTCompoundVariable::class, $string->getChild(0));
    }

    /**
     * testDoubleQuoteStringWithEmbeddedComplexBacktickExpression
     */
    public function testDoubleQuoteStringWithEmbeddedComplexBacktickExpression(): void
    {
        $string = $this->getFirstStringInFunction();
        $actual = [];
        foreach ($string->getChildren() as $child) {
            $actual[] = $child->getImage();
        }
        $expected = ['Issue `', '$ticketNo', '`'];

        static::assertEquals($expected, $actual);
    }

    /**
     * testBacktickExpressionWithEmbeddedComplexDoubleQuoteString
     */
    public function testBacktickExpressionWithEmbeddedComplexDoubleQuoteString(): void
    {
        $string = $this->getFirstStringInFunction();
        $actual = [];
        foreach ($string->getChildren() as $child) {
            $actual[] = $child->getImage();
        }
        $expected = ['Issue "', '$ticketNo', '"'];

        static::assertEquals($expected, $actual);
    }

    /**
     * testDoubleQuoteStringContainsVariable
     */
    public function testDoubleQuoteStringContainsVariable(): void
    {
        $string = $this->getFirstStringInFunction();
        static::assertInstanceOf(ASTVariable::class, $string->getChild(0));
    }

    /**
     * testDoubleQuoteStringContainsVariableAfterNotOperator
     */
    public function testDoubleQuoteStringContainsVariableAfterNotOperator(): void
    {
        $string = $this->getFirstStringInFunction();
        static::assertInstanceOf(ASTVariable::class, $string->getChild(1));
    }

    /**
     * testDoubleQuoteStringContainsVariableAfterSilenceOperator
     */
    public function testDoubleQuoteStringContainsVariableAfterSilenceOperator(): void
    {
        $string = $this->getFirstStringInFunction();
        static::assertInstanceOf(ASTVariable::class, $string->getChild(1));
    }

    /**
     * testDoubleQuoteStringContainsCompoundVariable
     */
    public function testDoubleQuoteStringContainsCompoundVariable(): void
    {
        $string = $this->getFirstStringInFunction();
        static::assertInstanceOf(ASTCompoundVariable::class, $string->getChild(0));
    }

    /**
     * testDoubleQuoteStringContainsCompoundExpressionAfterLiteral
     */
    public function testDoubleQuoteStringContainsCompoundExpressionAfterLiteral(): void
    {
        $string = $this->getFirstStringInFunction();
        static::assertInstanceOf(ASTCompoundExpression::class, $string->getChild(1));
    }

    /**
     * testDoubleQuoteStringContainsVariableAfterDollarTwoLiterals
     */
    public function testDoubleQuoteStringContainsVariableAfterDollarTwoLiterals(): void
    {
        $string = $this->getFirstStringInFunction();
        static::assertInstanceOf(ASTVariable::class, $string->getChild(1));
    }

    /**
     * testDoubleQuoteStringContainsDollarLiteralForVariableVariable
     */
    public function testDoubleQuoteStringContainsDollarLiteralForVariableVariable(): void
    {
        $string = $this->getFirstStringInFunction();
        static::assertInstanceOf(ASTLiteral::class, $string->getChild(0));
    }

    /**
     * Tests that an invalid literal results in the expected exception.
     */
    public function testUnclosedDoubleQuoteStringResultsInExpectedException(): void
    {
        $this->expectException(TokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testStringStartLine
     */
    public function testStringStartLine(): void
    {
        $string = $this->getFirstStringInFunction();
        static::assertSame(7, $string->getStartLine());
    }

    /**
     * testStringEndLine
     */
    public function testStringEndLine(): void
    {
        $string = $this->getFirstStringInFunction();
        static::assertSame(8, $string->getEndLine());
    }

    /**
     * testStringStartColumn
     */
    public function testStringStartColumn(): void
    {
        $string = $this->getFirstStringInFunction();
        static::assertSame(17, $string->getStartColumn());
    }

    /**
     * testStringEndColumn
     */
    public function testStringEndColumn(): void
    {
        $string = $this->getFirstStringInFunction();
        static::assertSame(8, $string->getEndColumn());
    }

    /**
     * Creates a string node.
     */
    protected function createNodeInstance(): ASTString
    {
        return new ASTString();
    }

    /**
     * Returns a test member primary prefix.
     */
    private function getFirstStringInFunction(): ASTString
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTString::class
        );
    }
}
