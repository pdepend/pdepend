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

namespace PDepend\Util;

use PDepend\AbstractTestCase;

/**
 * Test case for type utility class.
 *
 * @covers \PDepend\Util\Type
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class TypeTest extends AbstractTestCase
{
    /**
     * testIsInternalTypeDetectsInternalClassPrefixedWithBackslash
     */
    public function testIsInternalTypeDetectsInternalClassPrefixedWithBackslash(): void
    {
        static::assertTrue(Type::isInternalType('\LogicException'));
    }

    /**
     * testGetTypePackageReturnsNullWhenGivenClassIsNotExtensionClass
     */
    public function testGetTypePackageReturnsNullWhenGivenClassIsNotExtensionClass(): void
    {
        static::assertNull(Type::getTypePackage(__CLASS__));
    }

    /**
     * testIsScalarTypeReturnsTrueCaseInsensitive
     */
    public function testIsScalarTypeReturnsTrueCaseInsensitive(): void
    {
        static::assertTrue(Type::isScalarType('ArRaY'));
    }

    /**
     * testIsScalarTypeReturnsTrueMetaphone
     */
    public function testIsScalarTypeReturnsTrueMetaphone(): void
    {
        static::assertTrue(Type::isScalarType('Arrai'));
    }

    /**
     * testIsScalarTypeReturnsTrueSoundex
     */
    public function testIsScalarTypeReturnsTrueSoundex(): void
    {
        static::assertTrue(Type::isScalarType('Imteger'));
    }

    /**
     * testGetPrimitiveTypeReturnsExpectedValueForExactMatch
     */
    public function testIsPrimitiveTypeReturnsTrueForMatchingInput(): void
    {
        static::assertTrue(Type::isPrimitiveType('int'));
    }

    /**
     * testIsPrimitiveTypeReturnsFalseForNotMatchingInput
     */
    public function testIsPrimitiveTypeReturnsFalseForNotMatchingInput(): void
    {
        static::assertFalse(Type::isPrimitiveType('input'));
    }

    /**
     * testGetPrimitiveTypeReturnsExpectedValueForExactMatch
     */
    public function testGetPrimitiveTypeReturnsExpectedValueForExactMatch(): void
    {
        $actual = Type::getPrimitiveType('int');
        static::assertEquals(Type::PHP_TYPE_INTEGER, $actual);
    }

    /**
     * testGetPrimitiveTypeWorksCaseInsensitive
     */
    public function testGetPrimitiveTypeWorksCaseInsensitive(): void
    {
        $actual = Type::getPrimitiveType('INT');
        static::assertEquals(Type::PHP_TYPE_INTEGER, $actual);
    }

    /**
     * testGetPrimitiveTypeReturnsNullForNonPrimitive
     */
    public function testGetPrimitiveTypeReturnsNullForNonPrimitive(): void
    {
        static::assertNull(Type::getPrimitiveType('FooBarBaz'));
    }

    /**
     * testGetPrimitiveTypeFindsTypeByMetaphone
     */
    public function testGetPrimitiveTypeFindsTypeByMetaphone(): void
    {
        $int = Type::getPrimitiveType('indeger');
        static::assertEquals(Type::PHP_TYPE_INTEGER, $int);
    }

    /**
     * testGetPrimitiveTypeFindsTypeBySoundex
     */
    public function testGetPrimitiveTypeFindsTypeBySoundex(): void
    {
        $int = Type::getPrimitiveType('imtege');
        static::assertEquals(Type::PHP_TYPE_INTEGER, $int);
    }

    /**
     * testIsInternalPackageReturnsTrueForPhpStandardLibrary
     */
    public function testIsInternalPackageReturnsTrueForPhpStandardLibrary(): void
    {
        if (!extension_loaded('spl')) {
            static::markTestSkipped('SPL extension not loaded.');
        }
        static::assertTrue(Type::isInternalPackage('+spl'));
    }

    /**
     * testGetTypePackageReturnsExpectedExtensionNameForClassPrefixedWithBackslash
     */
    public function testGetTypePackageReturnsExpectedExtensionNameForClassPrefixedWithBackslash(): void
    {
        $extensionName = Type::getTypePackage('\LogicException');
        static::assertEquals('+spl', $extensionName);
    }

    /**
     * testIsArrayReturnsFalseForNonArrayString
     */
    public function testIsArrayReturnsFalseForNonArrayString(): void
    {
        static::assertFalse(Type::isArrayType('Pdepend'));
    }

    /**
     * testIsArrayReturnsTrueForLowerCaseArrayString
     */
    public function testIsArrayReturnsTrueForLowerCaseArrayString(): void
    {
        static::assertTrue(Type::isArrayType('array'));
    }

    /**
     * testIsArrayPerformsCheckCaseInsensitive
     */
    public function testIsArrayPerformsCheckCaseInsensitive(): void
    {
        static::assertTrue(Type::isArrayType('ArRaY'));
    }
}
