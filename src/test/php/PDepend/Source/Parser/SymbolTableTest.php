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

namespace PDepend\Source\Parser;

use PDepend\AbstractTestCase;

/**
 * Test case for the {@link SymbolTable} class.
 *
 * @covers \PDepend\Source\Parser\SymbolTable
 *
 * @group unittest
 */
class SymbolTableTest extends AbstractTestCase
{
    /**
     * Tests that no symbol can be added to a symbol table without active scope.
     */
    public function testCannotAddSymbolToASymbolTableWithoutActiveScope(): void
    {
        $this->expectException(
            NoActiveScopeException::class
        );
        $this->expectExceptionMessage(
            'No active scope in symbol table.'
        );

        $symbolTable = new SymbolTable();
        $symbolTable->destroyScope();
        $symbolTable->add('key', 'value');
    }

    /**
     * Tests that cannot perform lookup on a symbol table without active scope.
     */
    public function testCannotPerformLookupOnASymbolTableWithoutActiveScope(): void
    {
        $this->expectException(
            NoActiveScopeException::class
        );
        $this->expectExceptionMessage(
            'No active scope in symbol table.'
        );

        $symbolTable = new SymbolTable();
        $symbolTable->destroyScope();
        $symbolTable->lookup('key');
    }

    /**
     * Tests that cannot reset a scope, if there is no active scope.
     */
    public function testCannotResetWithoutActiveScope(): void
    {
        $this->expectException(
            NoActiveScopeException::class
        );
        $this->expectExceptionMessage(
            'No active scope in symbol table.'
        );

        $symbolTable = new SymbolTable();
        $symbolTable->destroyScope();
        $symbolTable->resetScope();
    }

    /**
     * Tests that cannot destroy a scope, if there is no active scope.
     */
    public function testCannotDestroyWithoutActiveScope(): void
    {
        $this->expectException(
            NoActiveScopeException::class
        );
        $this->expectExceptionMessage(
            'No active scope in symbol table.'
        );

        $symbolTable = new SymbolTable();
        $symbolTable->destroyScope();
        $symbolTable->destroyScope();
    }

    /**
     * Tests that a symbol can be added to a symbol table,
     * and it the key is case-insensitive.
     */
    public function testCanAdd(): void
    {
        $symbolTable = new SymbolTable();

        $key = 'keYWithDifferentCases';
        $lookupKey = 'KeywithdifferenTcases';
        $value = 'value';

        $symbolTable->add($key, $value);

        static::assertSame($value, $symbolTable->lookup($lookupKey));
    }

    /**
     * Tests that there may be multiple nested scopes.
     */
    public function testCanCreateAndAddToSeveralScopes(): void
    {
        $symbolTable = new SymbolTable();

        $firstLevelKey = 'firstLevelKey';
        $firstLevelValue = 'firstLevelValue';

        $symbolTable->add($firstLevelKey, $firstLevelValue);

        $symbolTable->createScope();

        $secondLevelKey = 'secondLevelKey';
        $secondLevelValue = 'secondLevelValue';

        $symbolTable->add($secondLevelKey, $secondLevelValue);

        // There must be boths keys in the current active scope.
        static::assertSame($firstLevelValue, $symbolTable->lookup($firstLevelKey));
        static::assertSame($secondLevelValue, $symbolTable->lookup($secondLevelKey));

        $symbolTable->destroyScope();

        // After destroying there must be no keys from previously active scope.
        static::assertNull($symbolTable->lookup($secondLevelKey));
    }

    /**
     * Tests that current active scope can be reset.
     */
    public function testCanResetActiveScope(): void
    {
        $symbolTable = new SymbolTable();

        $key = 'key';
        $value = 'value';

        $symbolTable->add($key, $value);

        static::assertSame($value, $symbolTable->lookup($key));

        $symbolTable->resetScope();

        static::assertNull($symbolTable->lookup($key));
    }
}
