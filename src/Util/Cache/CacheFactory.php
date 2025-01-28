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

namespace PDepend\Util\Cache;

use Exception;
use InvalidArgumentException;
use PDepend\Util\Cache\Driver\FileCacheDriver;
use PDepend\Util\Cache\Driver\MemoryCacheDriver;
use PDepend\Util\Configuration;
use RuntimeException;
use stdClass;

/**
 * Factory that encapsulates the creation of a concrete cache instance.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 0.10.0
 */
class CacheFactory
{
    private const DEFAULT_TTL = 2592000; // 30 days

    /**
     * Singleton property that holds existing cache instances.
     *
     * @var array<string, CacheDriver>
     */
    protected array $caches = [];

    /**
     * Constructs a new cache factory instance for the given configuration.
     *
     * @param Configuration $configuration The system configuration.
     */
    public function __construct(
        protected Configuration $configuration,
    ) {
    }

    /**
     * Creates a new instance or returns an existing cache for the given cache
     * identifier.
     *
     * @param string $cacheKey The name/identifier for the cache instance.
     * @throws Exception
     */
    public function create(?string $cacheKey = null): CacheDriver
    {
        if (false === isset($this->caches[$cacheKey])) {
            $this->caches[$cacheKey] = $this->createCache($cacheKey);
        }

        return $this->caches[$cacheKey];
    }

    /**
     * Creates a cache instance based on the supplied configuration.
     *
     * @param string|null $cacheKey The name/identifier for the cache instance.
     * @throws InvalidArgumentException If the configured cache driver is unknown.
     * @throws Exception
     */
    protected function createCache(?string $cacheKey = null): CacheDriver
    {
        assert($this->configuration->cache instanceof stdClass);
        assert(is_string($this->configuration->cache->driver));
        assert(is_string($this->configuration->cache->location));
        assert(is_int($this->configuration->cache->ttl));

        return match ($this->configuration->cache->driver) {
            'file' => $this->createFileCache(
                $this->configuration->cache->location,
                $this->configuration->cache->ttl,
                $cacheKey,
            ),
            'memory' => $this->createMemoryCache(),
            default => throw new InvalidArgumentException(
                "Unknown cache driver '{$this->configuration->cache->driver}' given.",
            ),
        };
    }

    /**
     * Creates a new file system based cache instance.
     *
     * @param string $location Cache root directory.
     * @param int $ttl Cache ttl
     * @param string|null $cacheKey The name/identifier for the cache instance.
     * @throws RuntimeException
     */
    protected function createFileCache(
        string $location,
        int $ttl = self::DEFAULT_TTL,
        ?string $cacheKey = null
    ): FileCacheDriver {
        return new FileCacheDriver($location, $ttl, $cacheKey);
    }

    /**
     * Creates an in memory cache instance.
     *
     * @throws Exception
     */
    protected function createMemoryCache(): MemoryCacheDriver
    {
        return new MemoryCacheDriver();
    }
}
