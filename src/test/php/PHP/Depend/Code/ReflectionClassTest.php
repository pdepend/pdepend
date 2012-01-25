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
 * @covers PHP_Depend_Code_Class
 * @group pdepend
 * @group pdepend::code
 * @group unittest
 */
class PHP_Depend_Code_ReflectionClassTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests the compatibility of the <b>getConstants()</b> method implementation.
     *
     * @return void
     */
    public function testGetConstantsReturnsExpectedResultForSingleConstantDefinition()
    {
        $pdepend = self::parseClass(__FUNCTION__);
        $class   = new ReflectionClass(__FUNCTION__);

        $this->assertSame(
            $class->getConstants(),
            $pdepend->getConstants()
        );
    }

    /**
     * Tests the compatibility of the <b>getConstants()</b> method implementation.
     *
     * @return void
     */
    public function testGetConstantsReturnsExpectedResultForSingleCommaSeparatedConstantDefinition()
    {
        $pdepend = self::parseClass(__FUNCTION__);
        $class   = new ReflectionClass(__FUNCTION__);

        $this->assertSame(
            $class->getConstants(),
            $pdepend->getConstants()
        );
    }

    /**
     * Tests the compatibility of the <b>getConstants()</b> method implementation.
     *
     * @return void
     */
    public function testGetConstantsReturnsExpectedResultForMultipleConstantDefinitions()
    {
        $pdepend = self::parseClass(__FUNCTION__);
        $class   = new ReflectionClass(__FUNCTION__);

        $this->assertSame(
            $class->getConstants(),
            $pdepend->getConstants()
        );
    }

    /**
     * Tests the compatibility of the <b>getConstants()</b> method implementation.
     *
     * @return void
     */
    public function testGetConstantsReturnsExpectedResultForMultipleCommaSeparatedConstantDefinitions()
    {
        $pdepend = self::parseClass(__FUNCTION__);
        $class   = new ReflectionClass(__FUNCTION__);

        $this->assertEquals(
            $class->getConstants(),
            $pdepend->getConstants()
        );
    }

    /**
     * Tests the compatibility of the <b>getConstants()</b> method implementation.
     *
     * @return void
     */
    public function testGetConstantsReturnsExpectedResultForInheritedConstantDefinitions()
    {
        $pdepend = self::parseClass(__FUNCTION__);
        $class   = new ReflectionClass(__FUNCTION__);

        $this->assertEquals(
            $class->getConstants(),
            $pdepend->getConstants()
        );
    }

    /**
     * Tests the compatibility of the <b>getConstants()</b> method implementation.
     *
     * @return void
     */
    public function testGetConstantsReturnsExpectedResultForMultipleInheritedConstantDefinitions()
    {
        $pdepend = self::parseClass(__FUNCTION__);
        $class   = new ReflectionClass(__FUNCTION__);

        $this->assertEquals(
            $class->getConstants(),
            $pdepend->getConstants()
        );
    }

    /**
     * Tests the compatibility of the <b>getConstants()</b> method implementation.
     *
     * @return void
     */
    public function testGetConstantsReturnsExpectedResultForInheritedAndImplementedConstantDefinitions()
    {
        $pdepend = self::parseClass(__FUNCTION__);
        $class   = new ReflectionClass(__FUNCTION__);

        $this->assertEquals(
            $class->getConstants(),
            $pdepend->getConstants()
        );
    }

    /**
     * Tests the compatibility of the <b>hasConstant()</b> method implementation.
     *
     * @return void
     */
    public function testHasConstantReturnsTrueForExistingConstantDefinitionWithRegularValue()
    {
        $pdepend = self::parseClass(__FUNCTION__);
        $class   = new ReflectionClass(__FUNCTION__);

        $this->assertTrue($class->hasConstant('FOO'));
        $this->assertSame(
            $class->hasConstant('FOO'),
            $pdepend->hasConstant('FOO')
        );
    }

    /**
     * Tests the compatibility of the <b>hasConstant()</b> method implementation.
     *
     * @return void
     */
    public function testHasConstantReturnsTrueForExistingConstantDefinitionWithNullValue()
    {
        $pdepend = self::parseClass(__FUNCTION__);
        $class   = new ReflectionClass(__FUNCTION__);

        $this->assertTrue($class->hasConstant('FOO'));
        $this->assertSame(
            $class->hasConstant('FOO'),
            $pdepend->hasConstant('FOO')
        );
    }

    /**
     * Tests the compatibility of the <b>hasConstant()</b> method implementation.
     *
     * @return void
     */
    public function testHasConstantReturnsFalseForNotExistingConstantDefinition()
    {
        $pdepend = self::parseClass(__FUNCTION__);
        $class   = new ReflectionClass(__FUNCTION__);

        $this->assertFalse($class->hasConstant('FOO'));
        $this->assertSame(
            $class->hasConstant('FOO'),
            $pdepend->hasConstant('FOO')
        );
    }

    /**
     * Tests the compatibility of the <b>getConstant()</b> method implementation.
     *
     * @return void
     */
    public function testGetConstantReturnsExpectedValueForConstantDefinition()
    {
        $pdepend = self::parseClass(__FUNCTION__);
        $class   = new ReflectionClass(__FUNCTION__);

        $this->assertSame(
            $class->getConstant('FOO'),
            $pdepend->getConstant('FOO')
        );
    }

    /**
     * Tests the compatibility of the <b>getConstant()</b> method implementation.
     *
     * @return void
     */
    public function testGetConstantReturnsExpectedFalseForNotExistingConstantDefinition()
    {
        $pdepend = self::parseClass(__FUNCTION__);
        $class   = new ReflectionClass(__FUNCTION__);

        $this->assertFalse($class->getConstant('FOO'));
        $this->assertSame(
            $class->getConstant('FOO'),
            $pdepend->getConstant('FOO')
        );
    }

    /**
     * This method will return the first class found in the given file.
     *
     * @param string $testCase File name of the test file.
     *
     * @return PHP_Depend_Code_Class
     */
    protected static function parseClass($testCase)
    {
        // Include test code for native reflection
        include_once self::createCodeResourceUri('issues/067/' . $testCase . '.php');

        $packages = self::parseSource('issues/067/' . $testCase . '.php');

        foreach ($packages->current()->getClasses() as $class) {
            if ($class->getName() === $testCase) {
                return $class;
            }
        }
    }
}
