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
 * Test case for the {@link \PDepend\Source\AST\ASTLiteral} class.
 *
 * @covers \PDepend\Source\AST\ASTLiteral
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTLiteralTest extends ASTNodeTestCase
{
    /**
     * testLiteralWithBooleanTrueExpression
     */
    public function testLiteralWithBooleanTrueExpression(): void
    {
        $literal = $this->getFirstLiteralInFunction();
        static::assertEquals('True', $literal->getImage());
    }

    /**
     * testLiteralWithBooleanFalseExpression
     */
    public function testLiteralWithBooleanFalseExpression(): void
    {
        $literal = $this->getFirstLiteralInFunction();
        static::assertEquals('False', $literal->getImage());
    }

    /**
     * testLiteralWithIntegerExpression
     */
    public function testLiteralWithIntegerExpression(): void
    {
        $literal = $this->getFirstLiteralInFunction();
        static::assertEquals('42', $literal->getImage());
    }

    /**
     * testLiteralWithSignedIntegerExpression
     */
    public function testLiteralWithSignedIntegerExpression(): void
    {
        $literal = $this->getFirstLiteralInFunction();
        static::assertEquals('42', $literal->getImage());
    }

    /**
     * testLiteralWithFloatExpression
     */
    public function testLiteralWithFloatExpression(): void
    {
        $literal = $this->getFirstLiteralInFunction();
        static::assertEquals('42.23', $literal->getImage());
    }

    /**
     * testLiteralWithSignedFloatExpression
     */
    public function testLiteralWithSignedFloatExpression(): void
    {
        $literal = $this->getFirstLiteralInFunction();
        static::assertEquals('42.23', $literal->getImage());
    }

    /**
     * testLiteralWithNullExpression
     */
    public function testLiteralWithNullExpression(): void
    {
        $literal = $this->getFirstLiteralInFunction();
        static::assertEquals('NULL', $literal->getImage());
    }

    /**
     * testLiteralWithZeroIntegerValue
     *
     * @since 1.0.0
     */
    public function testLiteralWithZeroIntegerValue(): void
    {
        $literal = $this->getFirstLiteralInFunction();
        static::assertEquals('0', $literal->getImage());
    }

    /**
     * testLiteralWithZeroOctalIntegerValue
     *
     * @since 1.0.0
     */
    public function testLiteralWithZeroOctalIntegerValue(): void
    {
        $literal = $this->getFirstLiteralInFunction();
        static::assertEquals('00', $literal->getImage());
    }

    /**
     * testLiteralWithZeroHexIntegerValue
     *
     * @since 1.0.0
     */
    public function testLiteralWithZeroHexIntegerValue(): void
    {
        $literal = $this->getFirstLiteralInFunction();
        static::assertEquals('0x0', $literal->getImage());
    }

    /**
     * testLiteralWithZeroBinaryIntegerValue
     *
     * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
     * @since 1.0.0
     */
    public function testLiteralWithZeroBinaryIntegerValue(): void
    {
        $literal = $this->getFirstLiteralInFunction();
        static::assertEquals('0b0', $literal->getImage());
    }

    /**
     * testLiteralWithNonZeroOctalIntegerValue
     *
     * @since 1.0.0
     */
    public function testLiteralWithNonZeroOctalIntegerValue(): void
    {
        $literal = $this->getFirstLiteralInFunction();
        static::assertEquals('02342', $literal->getImage());
    }

    /**
     * testLiteralWithNonZeroHexIntegerValue
     *
     * @since 1.0.0
     */
    public function testLiteralWithNonZeroHexIntegerValue(): void
    {
        $literal = $this->getFirstLiteralInFunction();
        static::assertEquals('0x926', $literal->getImage());
    }

    /**
     * testLiteralWithNonZeroBinaryIntegerValue
     *
     * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
     * @since 1.0.0
     */
    public function testLiteralWithNonZeroBinaryIntegerValue(): void
    {
        $literal = $this->getFirstLiteralInFunction();
        static::assertEquals('0b100100100110', $literal->getImage());
    }

    /**
     * testLiteralWithZeroFloatValue
     *
     * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
     * @since 2.16.0
     */
    public function testLiteralWithZeroFloatValue(): void
    {
        $class = $this->getFirstClassForTestCase();
        $properties = $class->getProperties();

        /** @var ASTProperty $property */
        $property = $properties[0];

        static::assertSame(0.0, $property->getDefaultValue());
    }

    /**
     * testLiteralWithCurlyBraceFollowedByCompoundExpression
     *
     * @since 1.0.0
     */
    public function testLiteralWithCurlyBraceFollowedByCompoundExpression(): void
    {
        $literal = $this->getFirstLiteralInFunction();
        static::assertEquals('{', $literal->getImage());
    }

    /**
     * Tests that an invalid literal results in the expected exception.
     */
    public function testUnclosedDoubleQuoteStringResultsInExpectedException(): void
    {
        $this->expectException(TokenStreamEndException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * Creates a literal node.
     */
    protected function createNodeInstance(): ASTLiteral
    {
        return new ASTLiteral("'" . __METHOD__ . "'");
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstLiteralInFunction(): ASTLiteral
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTLiteral::class
        );
    }
}
