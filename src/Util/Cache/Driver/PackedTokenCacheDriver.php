<?php

/**
 * This file is part of PDepend.
 *
 * PHP Version 8
 *
 * Copyright (c) 2008-2026 Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2026 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Util\Cache\Driver;

use PDepend\Util\Cache\CacheDriver;
use PDepend\WorkerProtocol;

/**
 * Worker results carry their token streams as a packed blob.
 */
final class PackedTokenCacheDriver implements CacheDriver
{
    /** Cache entry type used for token streams. */
    private const TOKEN_TYPE = 'tokens';

    /** Default cache entry type. */
    private const ENTRY_TYPE = 'cache';

    /** Current cache entry type. */
    private string $type = self::ENTRY_TYPE;

    /**
     * Packed token streams, keyed by node id.
     *
     * @var array<string, array{string, int, int}>
     */
    private array $packed = [];

    public function __construct(
        private readonly CacheDriver $inner,
    ) {
    }

    /**
     * Registers the packed token streams of a single worker result.
     */
    public function addPackedTokens(string $blob): void
    {
        foreach (WorkerProtocol::indexTokens($blob) as $id => [$offset, $length]) {
            $this->packed[$id] = [$blob, $offset, $length];
        }
    }

    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function store(string $key, mixed $data, ?string $hash = null): void
    {
        $type = $this->consumeType();

        // A fresh token stream for this node supersedes the packed one.
        if ($type === self::TOKEN_TYPE) {
            unset($this->packed[$key]);
        }

        $this->inner->type($type)->store($key, $data, $hash);
    }

    public function restore(string $key, ?string $hash = null): mixed
    {
        $type = $this->consumeType();

        if ($type === self::TOKEN_TYPE && isset($this->packed[$key])) {
            [$blob, $offset, $length] = $this->packed[$key];

            return WorkerProtocol::unpackTokens($blob, $offset, $length);
        }

        return $this->inner->type($type)->restore($key, $hash);
    }

    public function remove(string $pattern): void
    {
        foreach (array_keys($this->packed) as $key) {
            if (str_starts_with($key, $pattern)) {
                unset($this->packed[$key]);
            }
        }

        $this->inner->remove($pattern);
    }

    private function consumeType(): string
    {
        $type = $this->type;
        $this->type = self::ENTRY_TYPE;

        return $type;
    }
}
