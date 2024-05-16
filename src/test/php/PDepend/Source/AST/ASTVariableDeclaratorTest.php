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
 * Test case for the {@link \PDepend\Source\AST\ASTVariableDeclarator} class.
 *
 * @covers \PDepend\Source\AST\ASTVariableDeclarator
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTVariableDeclaratorTest extends ASTNodeTestCase
{
    /**
     * testGetValueReturnsNullByDefault
     */
    public function testGetValueReturnsNullByDefault(): void
    {
        $declarator = new ASTVariableDeclarator();
        static::assertNull($declarator->getValue());
    }

    /**
     * testGetValueReturnsInjectedValueInstance
     */
    public function testGetValueReturnsInjectedValueInstance(): void
    {
        $declarator = new ASTVariableDeclarator();
        $declarator->setValue(new ASTValue());

        static::assertInstanceOf(ASTValue::class, $declarator->getValue());
    }

    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames(): void
    {
        $declarator = new ASTVariableDeclarator();
        static::assertEquals(
            [
                'value',
                'comment',
                'metadata',
                'nodes',
            ],
            $declarator->__sleep()
        );
    }

    /**
     * testVariableDeclarator
     *
     * @return ASTVariableDeclarator
     * @since 1.0.2
     */
    public function testVariableDeclarator()
    {
        $declarator = $this->getFirstVariableDeclaratorInFunction();
        static::assertInstanceOf(ASTVariableDeclarator::class, $declarator);

        return $declarator;
    }

    /**
     * testVariableDeclaratorHasExpectedStartLine
     *
     * @depends testVariableDeclarator
     */
    public function testVariableDeclaratorHasExpectedStartLine(ASTVariableDeclarator $declarator): void
    {
        static::assertEquals(4, $declarator->getStartLine());
    }

    /**
     * testVariableDeclaratorHasExpectedStartColumn
     *
     * @depends testVariableDeclarator
     */
    public function testVariableDeclaratorHasExpectedStartColumn(ASTVariableDeclarator $declarator): void
    {
        static::assertEquals(12, $declarator->getStartColumn());
    }

    /**
     * testVariableDeclaratorHasExpectedEndLine
     *
     * @depends testVariableDeclarator
     */
    public function testVariableDeclaratorHasExpectedEndLine(ASTVariableDeclarator $declarator): void
    {
        static::assertEquals(4, $declarator->getEndLine());
    }

    /**
     * testVariableDeclaratorHasExpectedEndColumn
     *
     * @depends testVariableDeclarator
     */
    public function testVariableDeclaratorHasExpectedEndColumn(ASTVariableDeclarator $declarator): void
    {
        static::assertEquals(17, $declarator->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return ASTVariableDeclarator
     */
    private function getFirstVariableDeclaratorInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            ASTVariableDeclarator::class
        );
    }
}
