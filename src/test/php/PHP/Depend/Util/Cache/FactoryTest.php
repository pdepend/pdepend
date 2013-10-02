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
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

namespace PHP\Depend\Util\Cache;

use PHP\Depend\AbstractTest;
use PHP\Depend\Util\Cache\Driver\File;
use PHP\Depend\Util\Cache\Driver\Memory;

/**
 * Test case for the {@link \PHP\Depend\Util\Cache\Factory} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @covers \PHP\Depend\Util\Cache\Factory
 * @group unittest
 */
class FactoryTest extends AbstractTest
{
    /**
     * testCreateReturnsDriverInstance
     *
     * @return void
     */
    public function testCreateReturnsDriverInstance()
    {
        $factory = new Factory(
            $this->createConfigurationFixture()
        );
        $this->assertInstanceOf('\\PHP\\Depend\\Util\\Cache\\Driver', $factory->create());
    }

    /**
     * testCreateHasSingletonBehaviorForIdenticalCacheNames
     *
     * @return void
     */
    public function testCreateHasSingletonBehaviorForIdenticalCacheNames()
    {
        $factory = new Factory(
            $this->createConfigurationFixture()
        );

        $cache0 = $factory->create();
        $cache1 = $factory->create();

        $this->assertSame($cache0, $cache1);
    }

    /**
     * testCreateReturnsDifferentInstancesForDifferentCacheNames
     *
     * @return void
     */
    public function testCreateReturnsDifferentInstancesForDifferentCacheNames()
    {
        $factory = new Factory(
            $this->createConfigurationFixture()
        );

        $cache0 = $factory->create();
        $cache1 = $factory->create(__FUNCTION__);

        $this->assertNotSame($cache0, $cache1);
    }

    /**
     * testCreateReturnsCacheInstanceOfTypeFile
     *
     * @return void
     */
    public function testCreateReturnsCacheInstanceOfTypeFile()
    {
        $this->changeWorkingDirectory();

        $this->assertInstanceOf(File::CLAZZ, $this->createFactoryFixture()->create());
    }

    /**
     * testCreateReturnsCacheInstanceOfTypeMemory
     *
     * @return void
     */
    public function testCreateReturnsCacheInstanceOfTypeMemory()
    {
        $this->changeWorkingDirectory();

        $this->assertInstanceOf(Memory::CLAZZ, $this->createFactoryFixture()->create());
    }

    /**
     * testCreateThrowsExpectedExceptionForUnknownCacheDriver
     *
     * @return void
     * @expectedException \InvalidArgumentException
     */
    public function testCreateThrowsExpectedExceptionForUnknownCacheDriver()
    {
        $this->changeWorkingDirectory();

        $factory = $this->createFactoryFixture();
        $factory->create();
    }

    /**
     * Creates a prepared factory instance.
     *
     * @return \PHP\Depend\Util\Cache\Factory
     */
    protected function createFactoryFixture()
    {
        return new Factory($this->createConfigurationFixture());
    }
}
