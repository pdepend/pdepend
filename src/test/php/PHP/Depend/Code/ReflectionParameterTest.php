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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for the api compatiblity between PHP's native reflection api and
 * this userland implementation.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 *
 * @covers PHP_Depend_Code_Parameter
 * @group pdepend
 * @group pdepend::code
 * @group unittest
 */
class PHP_Depend_Code_ReflectionParameterTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the output of __toString() is compatible with the native
     * reflection api.
     *
     * @return void
     */
    public function testToStringReturnsExpectedStringForSimpleParameterIssue67()
    {
        // Include test code for native reflection
        include_once self::createCodeResourceUri('issues/067-022-parameter-__toString.php');

        $native   = new ReflectionParameter('foo_067_022', 0);
        $userland = self::parseParameter('issues/067-022-parameter-__toString.php');

        $this->assertSame((string) $native, (string) $userland);
    }

    /**
     * Tests that the output of __toString() is compatible with the native
     * reflection api.
     *
     * @return void
     */
    public function testToStringReturnsExpectedStringForSimpleParameterWithDefaultValueNullIssue67()
    {
        // Include test code for native reflection
        include_once self::createCodeResourceUri('issues/067-023-parameter-__toString.php');

        $native   = new ReflectionParameter('foo_067_023', 0);
        $userland = self::parseParameter('issues/067-023-parameter-__toString.php');

        $this->assertSame((string) $native, (string) $userland);
    }

    /**
     * Tests that the output of __toString() is compatible with the native
     * reflection api.
     *
     * @return void
     */
    public function testToStringReturnsExpectedStringForSimpleParameterWithDefaultValueFalseAndReferenceIssue67()
    {
        // Include test code for native reflection
        include_once self::createCodeResourceUri('issues/067-024-parameter-__toString.php');

        $native   = new ReflectionParameter('foo_067_024', 1);
        $userland = self::parseParameter('issues/067-024-parameter-__toString.php');

        $this->assertSame((string) $native, (string) $userland);
    }

    /**
     * Tests that the output of __toString() is compatible with the native
     * reflection api.
     *
     * @return void
     */
    public function testToStringReturnsExpectedStringForParameterTypeArrayWithDefaultValueArrayIssue67()
    {
        // Include test code for native reflection
        include_once self::createCodeResourceUri('issues/067-025-parameter-__toString.php');

        $native   = new ReflectionParameter('foo_067_025', 1);
        $userland = self::parseParameter('issues/067-025-parameter-__toString.php');

        $this->assertSame((string) $native, (string) $userland);
    }

    /**
     * Tests that the output of __toString() is compatible with the native
     * reflection api.
     *
     * @return void
     */
    public function testToStringReturnsExpectedStringForParameterWithDefaultValueTypeIssue67()
    {
        // Include test code for native reflection
        include_once self::createCodeResourceUri('issues/067-026-parameter-__toString.php');

        $native   = new ReflectionParameter('foo_067_026', 0);
        $userland = self::parseParameter('issues/067-026-parameter-__toString.php');

        $this->assertSame((string) $native, (string) $userland);
    }

    /**
     * Tests that the output of __toString() is compatible with the native
     * reflection api.
     *
     * @return void
     */
    public function testToStringReturnsExpectedStringForParameterArrayWithDefaultValueNullIssue67()
    {
        // Include test code for native reflection
        include_once self::createCodeResourceUri('issues/067-027-parameter-__toString.php');

        $native   = new ReflectionParameter('foo_067_027', 0);
        $userland = self::parseParameter('issues/067-027-parameter-__toString.php');

        $this->assertSame((string) $native, (string) $userland);
    }

    /**
     * Tests that the output of __toString() is compatible with the native
     * reflection api.
     *
     * @return void
     */
    public function testToStringReturnsExpectedStringForParameterWithDefaultValueArrayIssue67()
    {
        // Include test code for native reflection
        include_once self::createCodeResourceUri('issues/067-028-parameter-__toString.php');

        $native   = new ReflectionParameter('foo_067_028', 0);
        $userland = self::parseParameter('issues/067-028-parameter-__toString.php');

        $this->assertSame((string) $native, (string) $userland);
    }

    /**
     * Tests that the output of __toString() is compatible with the native
     * reflection api.
     *
     * @return void
     */
    public function testToStringReturnsExpectedStringForParameterWithDefaultValueStringIssue67()
    {
        // Include test code for native reflection
        include_once self::createCodeResourceUri('issues/067-029-parameter-__toString.php');

        $native   = new ReflectionParameter('foo_067_029', 0);
        $userland = self::parseParameter('issues/067-029-parameter-__toString.php');

        $this->assertSame((string) $native, (string) $userland);
    }

    /**
     * Tests that the output of __toString() is compatible with the native
     * reflection api.
     *
     * @return void
     */
    public function testToStringReturnsExpectedStringForParameterWithDefaultValueString2Issue67()
    {
        // Include test code for native reflection
        include_once self::createCodeResourceUri('issues/067-030-parameter-__toString.php');

        $native   = new ReflectionParameter('foo_067_030', 0);
        $userland = self::parseParameter('issues/067-030-parameter-__toString.php');

        $this->assertSame((string) $native, (string) $userland);
    }

    /**
     * Tests that the output of __toString() is compatible with the native
     * reflection api.
     *
     * @return void
     */
    public function testToStringReturnsExpectedStringForParameterWithDefaultValueBooleanTrue()
    {
        // Include test code for native reflection
        include_once self::createCodeResourceUri('issues/067/' . __FUNCTION__ . '.php');

        $native   = new ReflectionParameter(__FUNCTION__, 0);
        $userland = self::parseParameter('issues/067/' . __FUNCTION__ . '.php');

        $this->assertSame((string) $native, (string) $userland);
    }

    /**
     * Tests that the output of __toString() is compatible with the native
     * reflection api.
     *
     * @return void
     */
    public function testToStringReturnsExpectedStringForParameterWithDefaultValueFloat()
    {
        // Include test code for native reflection
        include_once self::createCodeResourceUri('issues/067/' . __FUNCTION__ . '.php');

        $native   = new ReflectionParameter(__FUNCTION__, 0);
        $userland = self::parseParameter('issues/067/' . __FUNCTION__ . '.php');

        $this->assertSame((string) $native, (string) $userland);
    }

    /**
     * This method will return the last parameter of the first function found in
     * the given file.
     *
     * @param string $fileName File name of the test file.
     *
     * @return PHP_Depend_Code_Parameter
     */
    protected static function parseParameter($fileName)
    {
        $parameters = self::parseSource($fileName)
            ->current()
            ->getFunctions()
            ->current()
            ->getParameters();

        return end($parameters);
    }
}
