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

namespace PDepend\Util\Cache\Driver\File;

use PDepend\Util\Log;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use UnexpectedValueException;

/**
 * Simple garbage collector for PDepend's file cache.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class FileCacheGarbageCollector
{
    public const DEFAULT_TTL = 2592000; // 30 days

    private readonly int $expirationTimestamp;

    public function __construct(
        private readonly string $cacheDir,
        int $ttl = self::DEFAULT_TTL
    ) {
        $this->expirationTimestamp = time() - $ttl;
    }

    /**
     * Removes all outdated cache files and returns the number of garbage
     * collected files.
     */
    public function garbageCollect(): int
    {
        if (false === file_exists($this->cacheDir)) {
            return 0;
        }

        $count = 0;

        try {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->cacheDir),
            );
            foreach ($files as $file) {
                assert($file instanceof SplFileInfo);
                if ($this->isCollectibleFile($file)) {
                    $this->garbageCollectFile($file);
                    ++$count;
                }
            }

            return $count;
        } catch (UnexpectedValueException) {
            // This may happen if PHPMD and PDepend run in parallel
            return $count;
        }
    }

    /**
     * Checks if the given file can be removed.
     */
    private function isCollectibleFile(SplFileInfo $file): bool
    {
        if (false === $file->isFile()) {
            return false;
        }

        $time = $file->getATime();
        if ($time > $this->expirationTimestamp) {
            return false;
        }

        $time = $file->getMTime();
        if ($time > $this->expirationTimestamp) {
            return false;
        }

        return true;
    }

    /**
     * Removes the given cache file.
     */
    private function garbageCollectFile(SplFileInfo $file): void
    {
        Log::debug("Removing file '{$file->getPathname()}' from cache.");

        @unlink($file->getPathname());
    }
}
