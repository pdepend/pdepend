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

namespace PDepend\Util\Cache\Driver;

use PDepend\Util\Cache\CacheDriver;
use Random\RandomException;

/**
 * A memory based cache implementation.
 *
 * This class implements the {@link CacheDriver} interface based
 * on an in memory data structure. This means that all cached entries will get
 * lost when the php process exits.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 0.10.0
 */
class MemoryCacheDriver implements CacheDriver
{
    /** Default cache entry type. */
    private const ENTRY_TYPE = 'cache';

    /**
     * The in memory cache.
     *
     * @var array<string, array{?string, mixed}>
     */
    protected array $cache = [];

    /** Current cache entry type. */
    protected string $type = self::ENTRY_TYPE;

    /** Unique identifier within the same cache instance. */
    protected string $staticId;

    /**
     * Global stack, mainly used during testing.
     *
     * @var array<string, array<string, array{?string, mixed}>>
     */
    protected static array $staticCache = [];

    /**
     * Instantiates a new in memory cache instance.
     *
     * @throws RandomException
     */
    public function __construct()
    {
        $this->staticId = bin2hex(random_bytes(20));
    }

    /**
     * PHP's magic serialize sleep method.
     *
     * @since  1.0.2
     */
    public function __sleep(): array
    {
        self::$staticCache[$this->staticId] = $this->cache;

        return ['staticId'];
    }

    /**
     * PHP's magic serialize wakeup method.
     *
     * @since  1.0.2
     */
    public function __wakeup(): void
    {
        $this->cache = self::$staticCache[$this->staticId] ?? [];
    }

    /**
     * Sets the type for the next <em>store()</em> or <em>restore()</em> method
     * call. A type is something like a namespace or group for cache entries.
     *
     * Note that the cache type will be reset after each storage method call, so
     * you must invoke right before every call to <em>restore()</em> or
     * <em>store()</em>.
     *
     * @param string $type The name or object type for the next storage method call.
     * @return $this
     */
    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * This method will store the given <em>$data</em> under <em>$key</em>. This
     * method can be called with a third parameter that will be used as a
     * verification token, when the a cache entry gets restored. If the stored
     * hash and the supplied hash are not identical, that cache entry will be
     * removed and not returned.
     *
     * @param string $key The cache key for the given data.
     * @param mixed $data Any data that should be cached.
     * @param string $hash Optional hash that will be used for verification.
     */
    public function store(string $key, mixed $data, ?string $hash = null): void
    {
        $this->cache[$this->getCacheKey($key)] = [$hash, $data];
    }

    /**
     * This method tries to restore an existing cache entry for the given
     * <em>$key</em>. If a matching entry exists, this method verifies that the
     * given <em>$hash</em> and the the value stored with cache entry are equal.
     * Then it returns the cached entry. Otherwise this method will return
     * <b>NULL</b>.
     *
     * @param string $key The cache key for the given data.
     * @param string $hash Optional hash that will be used for verification.
     */
    public function restore(string $key, ?string $hash = null): mixed
    {
        $cacheKey = $this->getCacheKey($key);
        if (isset($this->cache[$cacheKey]) && $this->cache[$cacheKey][0] === $hash) {
            return $this->cache[$cacheKey][1];
        }

        return null;
    }

    /**
     * This method will remove an existing cache entry for the given identifier.
     * It will delete all cache entries where the cache key start with the given
     * <b>$pattern</b>. If no matching entry exists, this method simply does
     * nothing.
     *
     * @param string $pattern The cache key pattern.
     */
    public function remove(string $pattern): void
    {
        foreach (array_keys($this->cache) as $key) {
            if (str_starts_with($key, $pattern)) {
                unset($this->cache[$key]);
            }
        }
    }

    /**
     * Creates a prepared cache entry identifier, based on the given <em>$key</em>
     * and the <em>$type</em> property. Note that this method resets the cache
     * type, so that it is only valid for a single call.
     *
     * @param string $key The concrete object key.
     */
    protected function getCacheKey(string $key): string
    {
        $type = $this->type;
        $this->type = self::ENTRY_TYPE;

        return "{$key}.{$type}";
    }
}
