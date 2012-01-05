<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@pdepend.org>.
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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Util
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for type utility class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Util
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 *
 * @covers PHP_Depend_Util_Type
 * @group pdepend
 * @group pdepend::util
 * @group unittest
 */
class PHP_Depend_Util_TypeTest extends PHP_Depend_AbstractTest
{
    /**
     * testIsInternalTypeDetectsInternalClassPrefixedWithBackslash
     *
     * @return void
     */
    public function testIsInternalTypeDetectsInternalClassPrefixedWithBackslash()
    {
        self::assertTrue(PHP_Depend_Util_Type::isInternalType('\LogicException'));
    }

    /**
     * testGetTypePackageReturnsNullWhenGivenClassIsNotExtensionClass
     *
     * @return void
     */
    public function testGetTypePackageReturnsNullWhenGivenClassIsNotExtensionClass()
    {
        self::assertNull(PHP_Depend_Util_Type::getTypePackage(__CLASS__));
    }

    /**
     * testIsScalarTypeReturnsTrueCaseInsensitive
     *
     * @return void
     */
    public function testIsScalarTypeReturnsTrueCaseInsensitive()
    {
        self::assertTrue(PHP_Depend_Util_Type::isScalarType('ArRaY'));
    }

    /**
     * testIsScalarTypeReturnsTrueMetaphone
     *
     * @return void
     */
    public function testIsScalarTypeReturnsTrueMetaphone()
    {
        self::assertTrue(PHP_Depend_Util_Type::isScalarType('Arrai'));
    }

    /**
     * testIsScalarTypeReturnsTrueSoundex
     *
     * @return void
     */
    public function testIsScalarTypeReturnsTrueSoundex()
    {
        self::assertTrue(PHP_Depend_Util_Type::isScalarType('Imteger'));
    }

    /**
     * testGetPrimitiveTypeReturnsExpectedValueForExactMatch
     *
     * @return void
     */
    public function testIsPrimitiveTypeReturnsTrueForMatchingInput()
    {
        self::assertTrue(PHP_Depend_Util_Type::isPrimitiveType('int'));
    }

    /**
     * testIsPrimitiveTypeReturnsFalseForNotMatchingInput
     *
     * @return void
     */
    public function testIsPrimitiveTypeReturnsFalseForNotMatchingInput()
    {
        self::assertFalse(PHP_Depend_Util_Type::isPrimitiveType('input'));
    }

    /**
     * testGetPrimitiveTypeReturnsExpectedValueForExactMatch
     *
     * @return void
     */
    public function testGetPrimitiveTypeReturnsExpectedValueForExactMatch()
    {
        $actual = PHP_Depend_Util_Type::getPrimitiveType('int');
        self::assertEquals(PHP_Depend_Util_Type::PHP_TYPE_INTEGER, $actual);
    }

    /**
     * testGetPrimitiveTypeWorksCaseInsensitive
     *
     * @return void
     */
    public function testGetPrimitiveTypeWorksCaseInsensitive()
    {
        $actual = PHP_Depend_Util_Type::getPrimitiveType('INT');
        self::assertEquals(PHP_Depend_Util_Type::PHP_TYPE_INTEGER, $actual);
    }

    /**
     * testGetPrimitiveTypeReturnsNullForNonPrimitive
     *
     * @return void
     */
    public function testGetPrimitiveTypeReturnsNullForNonPrimitive()
    {
        self::assertNull(PHP_Depend_Util_Type::getPrimitiveType('FooBarBaz'));
    }

    /**
     * testGetPrimitiveTypeFindsTypeByMetaphone
     *
     * @return void
     */
    public function testGetPrimitiveTypeFindsTypeByMetaphone()
    {
        $int = PHP_Depend_Util_Type::getPrimitiveType('indeger');
        self::assertEquals(PHP_Depend_Util_Type::PHP_TYPE_INTEGER, $int);
    }

    /**
     * testGetPrimitiveTypeFindsTypeBySoundex
     *
     * @return void
     */
    public function testGetPrimitiveTypeFindsTypeBySoundex()
    {
        $int = PHP_Depend_Util_Type::getPrimitiveType('imtege');
        self::assertEquals(PHP_Depend_Util_Type::PHP_TYPE_INTEGER, $int);
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
        self::assertTrue(PHP_Depend_Util_Type::isInternalPackage('+spl'));
    }

    /**
     * testGetTypePackageReturnsExpectedExtensionNameForClassPrefixedWithBackslash
     *
     * @return void
     */
    public function testGetTypePackageReturnsExpectedExtensionNameForClassPrefixedWithBackslash()
    {
        $extensionName = PHP_Depend_Util_Type::getTypePackage('\LogicException');
        self::assertEquals('+spl', $extensionName);
    }
    
    /**
     * testIsArrayReturnsFalseForNonArrayString
     *
     * @return void
     */
    public function testIsArrayReturnsFalseForNonArrayString()
    {
        self::assertFalse(PHP_Depend_Util_Type::isArrayType('Pdepend'));
    }

    /**
     * testIsArrayReturnsTrueForLowerCaseArrayString
     *
     * @return void
     */
    public function testIsArrayReturnsTrueForLowerCaseArrayString()
    {
        self::assertTrue(PHP_Depend_Util_Type::isArrayType('array'));
    }

    /**
     * testIsArrayPerformsCheckCaseInsensitive
     *
     * @return void
     */
    public function testIsArrayPerformsCheckCaseInsensitive()
    {
        self::assertTrue(PHP_Depend_Util_Type::isArrayType('ArRaY'));
    }
}
