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

namespace PDepend\Util\Cache\Driver;

use PDepend\Util\Cache\AbstractDriverTest;

/**
 * Test case for the {@link \PDepend\Util\Cache\Driver\FileCacheDriver} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Util\Cache\Driver\FileCacheDriver
 * @group unittest
 */
class FileCacheDriverTest extends AbstractDriverTest
{
    /**
     * Temporary cache directory.
     *
     * @var string
     */
    protected $cacheDir = null;

    /**
     * Cache TTL
     *
     * @var int
     */
    protected $cacheTtl = null;

    /**
     * Initializes a temporary working directory.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->cacheDir = $this->createRunResourceURI('cache');
        $this->cacheTtl = FileCacheDriver::DEFAULT_TTL;
    }

    /**
     * Creates a test fixture.
     *
     * @return \PDepend\Util\Cache\CacheDriver
     */
    protected function createDriver()
    {
        return new FileCacheDriver($this->cacheDir, $this->cacheTtl);
    }

    /**
     * testFileDriverStoresFileWithCacheKeyIfPresent
     *
     * @return void
     * @since 1.0.0
     */
    public function testFileDriverStoresFileWithCacheKeyIfPresent()
    {
        $cache = new FileCacheDriver($this->cacheDir, $this->cacheTtl, 'foo');
        $cache->type('bar')->store('baz', __METHOD__);

        $key = md5('baz' . 'foo');
        $dir = substr($key, 0, 2);

        $this->assertCount(1, glob("{$this->cacheDir}/{$dir}/{$key}*.bar"));
    }

    /**
     * testFileDriverRestoresFileWithCacheKeyIfPresent
     *
     * @return void
     * @since 1.0.0
     */
    public function testFileDriverRestoresFileWithCacheKeyIfPresent()
    {
        $cache = new FileCacheDriver($this->cacheDir, $this->cacheTtl, 'foo');
        $cache->type('bar')->store('baz', __METHOD__);

        $this->assertEquals(__METHOD__, $cache->type('bar')->restore('baz'));
    }

    public function testFileDriverHandlingOfCorruptCache()
    {
        $cacheKey = 'foo';
        $cacheType = 'bar';
        $storedKey = 'baz';
        $hash = md5(__METHOD__);

        // Save something
        $cache = new FileCacheDriver($this->cacheDir, $this->cacheTtl, $cacheKey);
        $cache->type($cacheType)->store($storedKey, __METHOD__, $hash);

        // Simulate a corrupt cache file by writing invalid data into the file
        $file = $this->getCacheFilePath($cacheKey, $cacheType, $storedKey);
        $corruptCacheContent = 'this is not a valid serialized value';
        $written = file_put_contents($file, $corruptCacheContent);
        if ($written !== strlen($corruptCacheContent)) {
            throw new \RuntimeException('Could not write to cache file during test. Path: ' . $file);
        }

        // Try to retrieve the cached value
        $cachedValue = $cache->type($cacheType)->restore($storedKey, $hash);
        $this->assertNull($cachedValue);
    }

    private function getCacheFilePath($cacheKey, $cacheType, $storedKey)
    {
        $key = md5($storedKey . $cacheKey);
        $dir = substr($key, 0, 2);
        $version = preg_replace('(^(\d+\.\d+).*)', '\\1', phpversion());
        return $this->cacheDir . '/' . $dir . '/' . $key . '.' . $version . '.' . $cacheType;
    }
}
