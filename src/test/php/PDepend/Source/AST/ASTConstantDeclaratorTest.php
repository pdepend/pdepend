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
 * Test case for the {@link \PDepend\Source\AST\ASTConstantDeclarator} class.
 *
 * @covers \PDepend\Source\AST\ASTConstantDeclarator
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTConstantDeclaratorTest extends ASTNodeTestCase
{
    /**
     * testReturnValueOfMagicSleepContainsValueProperty
     */
    public function testReturnValueOfMagicSleepContainsValueProperty(): void
    {
        $node = new ASTConstantDeclarator();
        static::assertEquals(
            [
                'value',
                'comment',
                'metadata',
                'nodes',
            ],
            $node->__sleep()
        );
    }

    /**
     * testParserInjectsValueObjectIntoConstantDeclarator
     */
    public function testParserInjectsValueObjectIntoConstantDeclarator(): void
    {
        $declarator = $this->getFirstConstantDeclaratorInClass();
        static::assertInstanceOf(ASTValue::class, $declarator->getValue());
    }

    /**
     * testParserInjectsExpectedScalarValueIntoConstantDeclarator
     */
    public function testParserInjectsExpectedScalarValueIntoConstantDeclarator(): void
    {
        $declarator = $this->getFirstConstantDeclaratorInClass();
        static::assertEquals(42, $declarator->getValue()?->getValue());
    }

    /**
     * testParserInjectsExpectedHeredocValueIntoConstantDeclarator
     *
     * @since 0.10.9
     */
    public function testParserInjectsExpectedHeredocValueIntoConstantDeclarator(): void
    {
        $declarator = $this->getFirstConstantDeclaratorInClass();
        static::assertEquals('Testing!', $declarator->getValue()?->getValue());
    }

    /**
     * testConstantDeclarator
     *
     * @since 1.0.2
     */
    public function testConstantDeclarator(): ASTConstantDeclarator
    {
        $declarator = $this->getFirstConstantDeclaratorInClass();
        static::assertInstanceOf(ASTConstantDeclarator::class, $declarator);

        return $declarator;
    }

    /**
     * testConstantDeclaratorHasExpectedStartLine
     *
     * @depends testConstantDeclarator
     */
    public function testConstantDeclaratorHasExpectedStartLine(ASTConstantDeclarator $declarator): void
    {
        static::assertEquals(5, $declarator->getStartLine());
    }

    /**
     * testConstantDeclaratorHasExpectedStartColumn
     *
     * @depends testConstantDeclarator
     */
    public function testConstantDeclaratorHasExpectedStartColumn(ASTConstantDeclarator $declarator): void
    {
        static::assertEquals(7, $declarator->getStartColumn());
    }

    /**
     * testConstantDeclaratorHasExpectedEndLine
     *
     * @depends testConstantDeclarator
     */
    public function testConstantDeclaratorHasExpectedEndLine(ASTConstantDeclarator $declarator): void
    {
        static::assertEquals(7, $declarator->getEndLine());
    }

    /**
     * testConstantDeclaratorHasExpectedEndColumn
     *
     * @depends testConstantDeclarator
     */
    public function testConstantDeclaratorHasExpectedEndColumn(ASTConstantDeclarator $declarator): void
    {
        static::assertEquals(14, $declarator->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstConstantDeclaratorInClass(): ASTConstantDeclarator
    {
        return $this->getFirstNodeOfTypeInClass(
            ASTConstantDeclarator::class
        );
    }
}
