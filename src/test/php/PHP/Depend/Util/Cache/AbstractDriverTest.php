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
 * @subpackage Util_Cache
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

/**
 * Abstract test case that validates the behavior of concrete driver
 * implementations.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Util_Cache
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 *
 * @group pdepend
 * @group pdepend::util
 * @group pdepend::util::cache
 * @group unittest
 */
abstract class PHP_Depend_Util_Cache_AbstractDriverTest
    extends PHP_Depend_AbstractTest
{
    /**
     * testTypeMethodReturnsSameObjectInstance
     *
     * @return void
     */
    public function testTypeMethodReturnsSameObjectInstance()
    {
        $driver = $this->createDriver();
        self::assertSame($driver, $driver->type(__FUNCTION__));
    }

    /**
     * testRestoreMethodReturnsNullByDefault
     *
     * @return void
     */
    public function testRestoreMethodReturnsNullByDefault()
    {
        $driver = $this->createDriver();
        self::assertNull($driver->restore(__FUNCTION__));
    }

    /**
     * testStoreMethodPersistsGivenData
     *
     * @return void
     */
    public function testStoreMethodPersistsGivenData()
    {
        $driver = $this->createDriver();
        $driver->store(__FUNCTION__, __METHOD__);

        self::assertEquals(__METHOD__, $driver->restore(__FUNCTION__));
    }

    /**
     * testStoreMethodPersistsGivenDataWithHash
     *
     * @return void
     */
    public function testStoreMethodPersistsGivenDataWithHash()
    {
        $driver = $this->createDriver();
        $driver->store(__FUNCTION__, __METHOD__, '#42');

        self::assertEquals(__METHOD__, $driver->restore(__FUNCTION__, '#42'));
    }

    /**
     * testRestoreMethodWithDifferentHashReturnsNull
     *
     * @return void
     */
    public function testRestoreMethodWithDifferentHashReturnsNull()
    {
        $driver = $this->createDriver();
        $driver->store(__FUNCTION__, __METHOD__);

        self::assertNull($driver->restore(__FUNCTION__, '#42'));
    }

    /**
     * testStoreAndRestoreMethodsWithSpecialType
     *
     * @return void
     */
    public function testStoreAndRestoreMethodsWithSpecialType()
    {
        $driver = $this->createDriver();
        $driver->type('type')->store(__FUNCTION__, __CLASS__);

        self::assertEquals(__CLASS__, $driver->type('type')->restore(__FUNCTION__));
    }

    /**
     * testStoreMethodWithSpecialTypeNotOverwriteRecordInDefaultType
     *
     * @return void
     */
    public function testStoreMethodWithSpecialTypeNotOverwriteRecordInDefaultType()
    {
        $driver = $this->createDriver();
        $driver->store(__FUNCTION__, __METHOD__);
        $driver->type('type')->store(__FUNCTION__, __CLASS__);

        self::assertEquals(__METHOD__, $driver->restore(__FUNCTION__));
    }

    /**
     * testRemoveDeletesExistingCacheEntry
     *
     * @return void
     */
    public function testRemoveDeletesExistingCacheEntry()
    {
        $key  = __FUNCTION__ . '.tokens';
        $data = __METHOD__;

        $driver = $this->createDriver();
        $driver->store($key, $data);
        $driver->remove($key);

        self::assertNull($driver->restore($key));
    }

    /**
     * testRemoveDeletesExistingCacheEntriesWithEqualCacheKeyPrefix
     *
     * @return void
     */
    public function testRemoveDeletesExistingCacheEntriesWithEqualCacheKeyPrefix()
    {
        $key  = __FUNCTION__ . '.tokens';
        $data = __METHOD__;

        $driver = $this->createDriver();
        $driver->store($key, $data);
        $driver->remove(__FUNCTION__);

        self::assertNull($driver->restore($key));
    }

    /**
     * testRemoveDeletesExistingCacheEntryOfDifferentType
     *
     * @return void
     */
    public function testRemoveDeletesExistingCacheEntryOfDifferentType()
    {
        $key  = __FUNCTION__ . '.tokens';
        $data = __METHOD__;

        $driver = $this->createDriver();
        $driver->type('foo')->store($key, $data);
        $driver->remove($key);

        self::assertNull($driver->type('foo')->restore($key));
    }

    /**
     * testRemoveSilentlyIgnoresPatternsWithoutMatch
     *
     * @return void
     */
    public function testRemoveSilentlyIgnoresPatternsWithoutMatch()
    {
        $key  = __FUNCTION__;
        $data = __METHOD__;

        $driver = $this->createDriver();
        $driver->store($key, $data);
        $driver->remove($key . '.no-match');

        self::assertSame($data, $driver->restore($key));
    }

    /**
     * Creates a test fixture.
     *
     * @return PHP_Depend_Util_Cache_Driver
     */
    protected abstract function createDriver();
}
