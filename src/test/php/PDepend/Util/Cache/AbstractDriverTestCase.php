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

namespace PDepend\Util\Cache;

use PDepend\AbstractTestCase;

/**
 * Abstract test case that validates the behavior of concrete driver
 * implementations.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @group unittest
 */
abstract class AbstractDriverTestCase extends AbstractTestCase
{
    /**
     * testTypeMethodReturnsSameObjectInstance
     */
    public function testTypeMethodReturnsSameObjectInstance(): void
    {
        $driver = $this->createDriver();
        static::assertSame($driver, $driver->type(__FUNCTION__));
    }

    /**
     * testRestoreMethodReturnsNullByDefault
     */
    public function testRestoreMethodReturnsNullByDefault(): void
    {
        $driver = $this->createDriver();
        static::assertNull($driver->restore(__FUNCTION__));
    }

    /**
     * testStoreMethodPersistsGivenData
     */
    public function testStoreMethodPersistsGivenData(): void
    {
        $driver = $this->createDriver();
        $driver->store(__FUNCTION__, __METHOD__);

        static::assertEquals(__METHOD__, $driver->restore(__FUNCTION__));
    }

    /**
     * testStoreMethodPersistsGivenDataWithHash
     */
    public function testStoreMethodPersistsGivenDataWithHash(): void
    {
        $driver = $this->createDriver();
        $driver->store(__FUNCTION__, __METHOD__, '#42');

        static::assertEquals(__METHOD__, $driver->restore(__FUNCTION__, '#42'));
    }

    /**
     * testRestoreMethodWithDifferentHashReturnsNull
     */
    public function testRestoreMethodWithDifferentHashReturnsNull(): void
    {
        $driver = $this->createDriver();
        $driver->store(__FUNCTION__, __METHOD__);

        static::assertNull($driver->restore(__FUNCTION__, '#42'));
    }

    /**
     * testStoreAndRestoreMethodsWithSpecialType
     */
    public function testStoreAndRestoreMethodsWithSpecialType(): void
    {
        $driver = $this->createDriver();
        $driver->type('type')->store(__FUNCTION__, __CLASS__);

        static::assertEquals(__CLASS__, $driver->type('type')->restore(__FUNCTION__));
    }

    /**
     * testStoreMethodWithSpecialTypeNotOverwriteRecordInDefaultType
     */
    public function testStoreMethodWithSpecialTypeNotOverwriteRecordInDefaultType(): void
    {
        $driver = $this->createDriver();
        $driver->store(__FUNCTION__, __METHOD__);
        $driver->type('type')->store(__FUNCTION__, __CLASS__);

        static::assertEquals(__METHOD__, $driver->restore(__FUNCTION__));
    }

    /**
     * testRemoveDeletesExistingCacheEntry
     */
    public function testRemoveDeletesExistingCacheEntry(): void
    {
        $key = __FUNCTION__ . '.tokens';
        $data = __METHOD__;

        $driver = $this->createDriver();
        $driver->store($key, $data);
        $driver->remove($key);

        static::assertNull($driver->restore($key));
    }

    /**
     * testRemoveDeletesExistingCacheEntriesWithEqualCacheKeyPrefix
     */
    public function testRemoveDeletesExistingCacheEntriesWithEqualCacheKeyPrefix(): void
    {
        $key = __FUNCTION__ . '.tokens';
        $data = __METHOD__;

        $driver = $this->createDriver();
        $driver->store($key, $data);
        $driver->remove(__FUNCTION__);

        static::assertNull($driver->restore($key));
    }

    /**
     * testRemoveDeletesExistingCacheEntryOfDifferentType
     */
    public function testRemoveDeletesExistingCacheEntryOfDifferentType(): void
    {
        $key = __FUNCTION__ . '.tokens';
        $data = __METHOD__;

        $driver = $this->createDriver();
        $driver->type('foo')->store($key, $data);
        $driver->remove($key);

        static::assertNull($driver->type('foo')->restore($key));
    }

    /**
     * testRemoveSilentlyIgnoresPatternsWithoutMatch
     */
    public function testRemoveSilentlyIgnoresPatternsWithoutMatch(): void
    {
        $key = __FUNCTION__;
        $data = __METHOD__;

        $driver = $this->createDriver();
        $driver->store($key, $data);
        $driver->remove($key . '.no-match');

        static::assertSame($data, $driver->restore($key));
    }

    /**
     * Creates a test fixture.
     */
    abstract protected function createDriver(): CacheDriver;
}
