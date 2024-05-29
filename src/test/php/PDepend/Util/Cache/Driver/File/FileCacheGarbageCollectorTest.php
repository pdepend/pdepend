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

use PDepend\AbstractTestCase;

/**
 * Test case for the {@link \PDepend\Util\Cache\Driver\File\FileCacheGarbageCollector} class.
 *
 * @covers \PDepend\Util\Cache\Driver\File\FileCacheGarbageCollector
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class FileCacheGarbageCollectorTest extends AbstractTestCase
{
    /** Temporary cache directory. */
    protected string $cacheDir;

    /** Cache TTL */
    protected int $cacheTtl = FileCacheGarbageCollector::DEFAULT_TTL;

    /**
     * Initializes a temporary working directory.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $tmp = $this->createRunResourceURI('cache');
        unlink($tmp);
        $this->cacheDir = $tmp . '/';
        mkdir($this->cacheDir);
    }

    public function testKeepsRecentFiles(): void
    {
        $this->createFile();
        $this->createFile();

        $garbageCollector = new FileCacheGarbageCollector($this->cacheDir, $this->cacheTtl);
        static::assertSame(0, $garbageCollector->garbageCollect());
    }

    public function testRemovesOutdatedFiles(): void
    {
        $time = 31 * 86400;

        $this->createFile($time, $time);
        $this->createFile($time, $time);

        $garbageCollector = new FileCacheGarbageCollector($this->cacheDir, $this->cacheTtl);
        static::assertSame(2, $garbageCollector->garbageCollect());
    }

    public function testKeepsFilesWithRecentATime(): void
    {
        $time = 31 * 86400;

        $this->createFile($time, $time);
        $this->createFile($time);

        $garbageCollector = new FileCacheGarbageCollector($this->cacheDir, $this->cacheTtl);
        static::assertSame(1, $garbageCollector->garbageCollect());
    }

    public function testKeepsFilesWithRecentMTime(): void
    {
        $time = 31 * 86400;

        $this->createFile($time, $time);
        $this->createFile(0, $time);

        $garbageCollector = new FileCacheGarbageCollector($this->cacheDir, $this->cacheTtl);
        static::assertSame(1, $garbageCollector->garbageCollect());
    }

    protected function createFile(int $mtime = 0, int $atime = 0): string
    {
        $time = time();

        $mtime = $time - $mtime;
        $atime = $time - $atime;

        $file = uniqid($this->cacheDir);

        touch($file, $mtime, $atime);

        return $file;
    }
}
