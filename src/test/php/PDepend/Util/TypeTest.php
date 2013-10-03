<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Util;

use PDepend\AbstractTest;

/**
 * Test case for type utility class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Util\Type
 * @group unittest
 */
class TypeTest extends AbstractTest
{
    /**
     * testIsInternalTypeDetectsInternalClassPrefixedWithBackslash
     *
     * @return void
     */
    public function testIsInternalTypeDetectsInternalClassPrefixedWithBackslash()
    {
        $this->assertTrue(Type::isInternalType('\LogicException'));
    }

    /**
     * testGetTypePackageReturnsNullWhenGivenClassIsNotExtensionClass
     *
     * @return void
     */
    public function testGetTypePackageReturnsNullWhenGivenClassIsNotExtensionClass()
    {
        $this->assertNull(Type::getTypePackage(__CLASS__));
    }

    /**
     * testIsScalarTypeReturnsTrueCaseInsensitive
     *
     * @return void
     */
    public function testIsScalarTypeReturnsTrueCaseInsensitive()
    {
        $this->assertTrue(Type::isScalarType('ArRaY'));
    }

    /**
     * testIsScalarTypeReturnsTrueMetaphone
     *
     * @return void
     */
    public function testIsScalarTypeReturnsTrueMetaphone()
    {
        $this->assertTrue(Type::isScalarType('Arrai'));
    }

    /**
     * testIsScalarTypeReturnsTrueSoundex
     *
     * @return void
     */
    public function testIsScalarTypeReturnsTrueSoundex()
    {
        $this->assertTrue(Type::isScalarType('Imteger'));
    }

    /**
     * testGetPrimitiveTypeReturnsExpectedValueForExactMatch
     *
     * @return void
     */
    public function testIsPrimitiveTypeReturnsTrueForMatchingInput()
    {
        $this->assertTrue(Type::isPrimitiveType('int'));
    }

    /**
     * testIsPrimitiveTypeReturnsFalseForNotMatchingInput
     *
     * @return void
     */
    public function testIsPrimitiveTypeReturnsFalseForNotMatchingInput()
    {
        $this->assertFalse(Type::isPrimitiveType('input'));
    }

    /**
     * testGetPrimitiveTypeReturnsExpectedValueForExactMatch
     *
     * @return void
     */
    public function testGetPrimitiveTypeReturnsExpectedValueForExactMatch()
    {
        $actual = Type::getPrimitiveType('int');
        $this->assertEquals(Type::PHP_TYPE_INTEGER, $actual);
    }

    /**
     * testGetPrimitiveTypeWorksCaseInsensitive
     *
     * @return void
     */
    public function testGetPrimitiveTypeWorksCaseInsensitive()
    {
        $actual = Type::getPrimitiveType('INT');
        $this->assertEquals(Type::PHP_TYPE_INTEGER, $actual);
    }

    /**
     * testGetPrimitiveTypeReturnsNullForNonPrimitive
     *
     * @return void
     */
    public function testGetPrimitiveTypeReturnsNullForNonPrimitive()
    {
        $this->assertNull(Type::getPrimitiveType('FooBarBaz'));
    }

    /**
     * testGetPrimitiveTypeFindsTypeByMetaphone
     *
     * @return void
     */
    public function testGetPrimitiveTypeFindsTypeByMetaphone()
    {
        $int = Type::getPrimitiveType('indeger');
        $this->assertEquals(Type::PHP_TYPE_INTEGER, $int);
    }

    /**
     * testGetPrimitiveTypeFindsTypeBySoundex
     *
     * @return void
     */
    public function testGetPrimitiveTypeFindsTypeBySoundex()
    {
        $int = Type::getPrimitiveType('imtege');
        $this->assertEquals(Type::PHP_TYPE_INTEGER, $int);
    }

    /**
     * testIsInternalPackageReturnsTrueForPhpStandardLibrary
     *
     * @return void
     */
    public function testIsInternalPackageReturnsTrueForPhpStandardLibrary()
    {
        if (!extension_loaded('spl')) {
            $this->markTestSkipped('SPL extension not loaded.');
        }
        $this->assertTrue(Type::isInternalPackage('+spl'));
    }

    /**
     * testGetTypePackageReturnsExpectedExtensionNameForClassPrefixedWithBackslash
     *
     * @return void
     */
    public function testGetTypePackageReturnsExpectedExtensionNameForClassPrefixedWithBackslash()
    {
        $extensionName = Type::getTypePackage('\LogicException');
        $this->assertEquals('+spl', $extensionName);
    }
    
    /**
     * testIsArrayReturnsFalseForNonArrayString
     *
     * @return void
     */
    public function testIsArrayReturnsFalseForNonArrayString()
    {
        $this->assertFalse(Type::isArrayType('Pdepend'));
    }

    /**
     * testIsArrayReturnsTrueForLowerCaseArrayString
     *
     * @return void
     */
    public function testIsArrayReturnsTrueForLowerCaseArrayString()
    {
        $this->assertTrue(Type::isArrayType('array'));
    }

    /**
     * testIsArrayPerformsCheckCaseInsensitive
     *
     * @return void
     */
    public function testIsArrayPerformsCheckCaseInsensitive()
    {
        $this->assertTrue(Type::isArrayType('ArRaY'));
    }
}
