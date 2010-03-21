<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2010, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Depend/Util/Type.php';

/**
 * Test case for type utility class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Util
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Util_TypeTest extends PHP_Depend_AbstractTest
{
    /**
     * testIsInternalTypeDetectsInternalClassPrefixedWithBackslash
     *
     * @return void
     * @covers PHP_Depend_Util_Type::isInternalType
     * @group pdepend
     * @group pdepend::util
     * @group unittest
     */
    public function testIsInternalTypeDetectsInternalClassPrefixedWithBackslash()
    {
        $this->assertTrue(PHP_Depend_Util_Type::isInternalType('\LogicException'));
    }

    /**
     * testGetTypePackageReturnsNullWhenGivenClassIsNotExtensionClass
     *
     * @return void
     * @covers PHP_Depend_Util_Type
     * @group pdepend
     * @group pdepend::util
     * @group unittest
     */
    public function testGetTypePackageReturnsNullWhenGivenClassIsNotExtensionClass()
    {
        $this->assertNull(PHP_Depend_Util_Type::getTypePackage(__CLASS__));
    }

    /**
     * testIsScalarTypeReturnsTrueCaseInsensitive
     *
     * @return void
     * @covers PHP_Depend_Util_Type
     * @group pdepend
     * @group pdepend::util
     * @group unittest
     */
    public function testIsScalarTypeReturnsTrueCaseInsensitive()
    {
        $this->assertTrue(PHP_Depend_Util_Type::isScalarType('ArRaY'));
    }

    /**
     * testIsScalarTypeReturnsTrueMetaphone
     *
     * @return void
     * @covers PHP_Depend_Util_Type
     * @group pdepend
     * @group pdepend::util
     * @group unittest
     */
    public function testIsScalarTypeReturnsTrueMetaphone()
    {
        $this->assertTrue(PHP_Depend_Util_Type::isScalarType('Arrai'));
    }

    /**
     * testIsScalarTypeReturnsTrueSoundex
     *
     * @return void
     * @covers PHP_Depend_Util_Type
     * @group pdepend
     * @group pdepend::util
     * @group unittest
     */
    public function testIsScalarTypeReturnsTrueSoundex()
    {
        $this->assertTrue(PHP_Depend_Util_Type::isScalarType('Imteger'));
    }

    /**
     * testGetPrimitiveTypeFindsTypeByMetaphone
     *
     * @return void
     * @covers PHP_Depend_Util_Type::getPrimitiveType
     * @group pdepend
     * @group pdepend::util
     * @group unittest
     */
    public function testGetPrimitiveTypeFindsTypeByMetaphone()
    {
        $int = PHP_Depend_Util_Type::getPrimitiveType('indeger');
        $this->assertEquals(PHP_Depend_Util_Type::PHP_TYPE_INTEGER, $int);
    }

    /**
     * testGetPrimitiveTypeFindsTypeBySoundex
     *
     * @return void
     * @covers PHP_Depend_Util_Type::getPrimitiveType
     * @group pdepend
     * @group pdepend::util
     * @group unittest
     */
    public function testGetPrimitiveTypeFindsTypeBySoundex()
    {
        $int = PHP_Depend_Util_Type::getPrimitiveType('imtege');
        $this->assertEquals(PHP_Depend_Util_Type::PHP_TYPE_INTEGER, $int);
    }

    /**
     * testIsInternalPackageReturnsTrueForPhpStandardLibrary
     *
     * @return void
     * @covers PHP_Depend_Util_Type
     * @group pdepend
     * @group pdepend::util
     * @group unittest
     */
    public function testIsInternalPackageReturnsTrueForPhpStandardLibrary()
    {
        if (!extension_loaded('spl')) {
            $this->markTestSkipped('SPL extension not loaded.');
        }
        $this->assertTrue(PHP_Depend_Util_Type::isInternalPackage('+spl'));
    }

    /**
     * testGetTypePackageReturnsExpectedExtensionNameForClassPrefixedWithBackslash
     *
     * @return void
     * @covers PHP_Depend_Util_Type::getTypePackage
     * @group pdepend
     * @group pdepend::util
     * @group unittest
     */
    public function testGetTypePackageReturnsExpectedExtensionNameForClassPrefixedWithBackslash()
    {
        $extensionName = PHP_Depend_Util_Type::getTypePackage('\LogicException');
        $this->assertEquals('+spl', $extensionName);
    }
}