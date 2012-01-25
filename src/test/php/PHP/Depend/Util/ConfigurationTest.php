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
 * @since      0.10.0
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for the {@link PHP_Depend_Util_Configuration} class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Util
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 * @since      0.10.0
 *
 * @covers PHP_Depend_Util_Configuration
 * @group pdepend
 * @group pdepend::util
 * @group pdepend::util::configuration
 * @group unittest
 */
class PHP_Depend_Util_ConfigurationTest extends PHP_Depend_AbstractTest
{
    /**
     * testPropertyAccessForExistingValue
     *
     * @return void
     */
    public function testPropertyAccessForExistingValue()
    {
        $settings      = new stdClass();
        $settings->foo = 42;

        $configuration = new PHP_Depend_Util_Configuration($settings);

        self::assertEquals(42, $configuration->foo);
    }

    /**
     * testPropertyAccessForNotExistingValueThrowsExpectedException
     *
     * @return void
     * @expectedException OutOfRangeException
     */
    public function testPropertyAccessForNotExistingValueThrowsExpectedException()
    {
        $settings      = new stdClass();
        $settings->foo = 42;

        $configuration = new PHP_Depend_Util_Configuration($settings);
        echo $configuration->bar;
    }

    /**
     * testPropertiesAreNotWritableAndExpectedExceptionIsThrown
     *
     * @return void
     * @expectedException OutOfRangeException
     */
    public function testPropertiesAreNotWritableAndExpectedExceptionIsThrown()
    {
        $configuration      = new PHP_Depend_Util_Configuration(new stdClass());
        $configuration->foo = 42;
    }

    /**
     * testIssetReturnsTrueForExistingValue
     *
     * @return void
     */
    public function testIssetReturnsTrueForExistingValue()
    {
        $settings      = new stdClass();
        $settings->foo = 42;

        $configuration = new PHP_Depend_Util_Configuration($settings);

        self::assertTrue(isset($configuration->foo));
    }

    /**
     * testIssetReturnsFalseForNotExistingValue
     *
     * @return void
     */
    public function testIssetReturnsFalseForNotExistingValue()
    {
        $settings      = new stdClass();
        $settings->foo = 42;

        $configuration = new PHP_Depend_Util_Configuration($settings);

        self::assertFalse(isset($configuration->bar));
    }
}
