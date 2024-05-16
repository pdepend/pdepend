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
 * @since 0.10.2
 */

namespace PDepend\Source\AST;

use PDepend\AbstractTestCase;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTValue} class.
 *
 * @covers \PDepend\Source\AST\ASTValue
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 0.10.2
 *
 * @group unittest
 */
class ASTValueTest extends AbstractTestCase
{
    /**
     * testIsValueAvailableReturnsFalseByDefault
     */
    public function testIsValueAvailableReturnsFalseByDefault(): void
    {
        $value = new ASTValue();
        static::assertFalse($value->isValueAvailable());
    }

    /**
     * testIsValueAvailableReturnsTrueWhenValueWasSet
     */
    public function testIsValueAvailableReturnsTrueWhenValueWasSet(): void
    {
        $value = new ASTValue();
        $value->setValue(42);

        static::assertTrue($value->isValueAvailable());
    }

    /**
     * testIsValueAvailableReturnsTrueForNullValue
     */
    public function testIsValueAvailableReturnsTrueForNullValue(): void
    {
        $value = new ASTValue();
        $value->setValue(null);

        static::assertTrue($value->isValueAvailable());
    }

    /**
     * testGetValueReturnsNullByDefault
     */
    public function testGetValueReturnsNullByDefault(): void
    {
        $value = new ASTValue();
        static::assertNull($value->getValue());
    }

    /**
     * testGetValueReturnsPreviouslySetValue
     */
    public function testGetValueReturnsPreviouslySetValue(): void
    {
        $value = new ASTValue();
        $value->setValue(42);

        static::assertEquals(42, $value->getValue());
    }

    /**
     * testSetValueMutatesInternalStateOnlyOnce
     */
    public function testSetValueMutatesInternalStateOnlyOnce(): void
    {
        $value = new ASTValue();
        $value->setValue(42);
        $value->setValue(23);

        static::assertEquals(42, $value->getValue());
    }
}
