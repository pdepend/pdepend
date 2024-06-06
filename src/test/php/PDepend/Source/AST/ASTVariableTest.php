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
 * Test case for the {@link \PDepend\Source\AST\ASTVariable} class.
 *
 * @covers \PDepend\Source\AST\ASTVariable
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTVariableTest extends ASTNodeTestCase
{
    /**
     * testIsThisReturnsTrueForThisImageName
     */
    public function testIsThisReturnsTrueForThisImageName(): void
    {
        $variable = new ASTVariable('$this');
        static::assertTrue($variable->isThis());
    }

    /**
     * testIsThisReturnsFalseForThisImageButDifferentCase
     */
    public function testIsThisReturnsFalseForThisImageButDifferentCase(): void
    {
        $variable = new ASTVariable('$This');
        static::assertFalse($variable->isThis());
    }

    /**
     * testIsThisReturnsFalseForDifferentImage
     */
    public function testIsThisReturnsFalseForDifferentImage(): void
    {
        $variable = new ASTVariable('$foo');
        static::assertFalse($variable->isThis());
    }

    /**
     * testVariable
     *
     * @since 1.0.2
     */
    public function testVariable(): ASTVariable
    {
        $variable = $this->getFirstVariableInClass();
        static::assertInstanceOf(ASTVariable::class, $variable);

        return $variable;
    }

    /**
     * testVariableHasExpectedStartLine
     *
     * @depends testVariable
     */
    public function testVariableHasExpectedStartLine(ASTVariable $variable): void
    {
        static::assertEquals(6, $variable->getStartLine());
    }

    /**
     * testVariableHasExpectedStartColumn
     *
     * @depends testVariable
     */
    public function testVariableHasExpectedStartColumn(ASTVariable $variable): void
    {
        static::assertEquals(9, $variable->getStartColumn());
    }

    /**
     * testVariableHasExpectedEndLine
     *
     * @depends testVariable
     */
    public function testVariableHasExpectedEndLine(ASTVariable $variable): void
    {
        static::assertEquals(6, $variable->getEndLine());
    }

    /**
     * testVariableHasExpectedEndColumn
     *
     * @depends testVariable
     */
    public function testVariableHasExpectedEndColumn(ASTVariable $variable): void
    {
        static::assertEquals(10, $variable->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstVariableInClass(): ASTVariable
    {
        return $this->getFirstNodeOfTypeInClass(
            ASTVariable::class
        );
    }
}
