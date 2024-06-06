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
 * @since 0.10.0
 */

namespace PDepend\Util;

use OutOfRangeException;
use PDepend\AbstractTestCase;
use stdClass;

/**
 * Test case for the {@link \PDepend\Util\Configuration} class.
 *
 * @covers \PDepend\Util\Configuration
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since 0.10.0
 *
 * @group unittest
 */
class ConfigurationTest extends AbstractTestCase
{
    /**
     * testPropertyAccessForExistingValue
     */
    public function testPropertyAccessForExistingValue(): void
    {
        $settings = new stdClass();
        $settings->foo = 42;

        $configuration = new Configuration($settings);

        static::assertEquals(42, $configuration->foo);
    }

    /**
     * testPropertyAccessForNotExistingValueThrowsExpectedException
     */
    public function testPropertyAccessForNotExistingValueThrowsExpectedException(): void
    {
        $this->expectException(OutOfRangeException::class);

        $settings = new stdClass();
        $settings->foo = 42;

        $configuration = new Configuration($settings);
        static::assertNotNull($configuration->bar);
    }

    /**
     * testPropertiesAreNotWritableAndExpectedExceptionIsThrown
     */
    public function testPropertiesAreNotWritableAndExpectedExceptionIsThrown(): void
    {
        $this->expectException(OutOfRangeException::class);

        $configuration = new Configuration(new stdClass());
        $configuration->foo = 42;
    }

    /**
     * testIssetReturnsTrueForExistingValue
     */
    public function testIssetReturnsTrueForExistingValue(): void
    {
        $settings = new stdClass();
        $settings->foo = 42;

        $configuration = new Configuration($settings);

        static::assertTrue(isset($configuration->foo));
    }

    /**
     * testIssetReturnsFalseForNotExistingValue
     */
    public function testIssetReturnsFalseForNotExistingValue(): void
    {
        $settings = new stdClass();
        $settings->foo = 42;

        $configuration = new Configuration($settings);

        static::assertFalse(isset($configuration->bar));
    }
}
