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

use PDepend\AbstractTest;

/**
 * Test case for the {@link \PDepend\Util\Cache\Driver\File\FileCacheGarbageCollector} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Util\Cache\Driver\File\FileCacheGarbageCollector
 * @group unittest
 */
class FileCacheGarbageCollectorTest extends AbstractTest
{
    /**
     * Temporary cache directory.
     *
     * @var string
     */
    protected $cacheDir;

    /**
     * Initializes a temporary working directory.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->cacheDir = $this->createRunResourceURI('cache') . '/';
        mkdir($this->cacheDir);
    }

    /**
     * @return void
     */
    public function testKeepsRecentFiles()
    {
        $this->createFile();
        $this->createFile();

        $garbageCollector = new FileCacheGarbageCollector($this->cacheDir);
        $this->assertSame(0, $garbageCollector->garbageCollect());
    }

    /**
     * @return void
     */
    public function testRemovesOutdatedFiles()
    {
        $time = 31 * 86400;

        $this->createFile($time, $time);
        $this->createFile($time, $time);

        $garbageCollector = new FileCacheGarbageCollector($this->cacheDir);
        $this->assertSame(2, $garbageCollector->garbageCollect());
    }

    /**
     * @return void
     */
    public function testKeepsFilesWithRecentATime()
    {
        $time = 31 * 86400;

        $this->createFile($time, $time);
        $this->createFile($time);

        $garbageCollector = new FileCacheGarbageCollector($this->cacheDir);
        $this->assertSame(1, $garbageCollector->garbageCollect());
    }

    /**
     * @return void
     */
    public function testKeepsFilesWithRecentMTime()
    {
        $time = 31 * 86400;

        $this->createFile($time, $time);
        $this->createFile(0, $time);

        $garbageCollector = new FileCacheGarbageCollector($this->cacheDir);
        $this->assertSame(1, $garbageCollector->garbageCollect());
    }

    protected function createFile($mtime = 0, $atime = 0)
    {

        $time = time();

        $mtime = $time - $mtime;
        $atime = $time - $atime;

        $file = uniqid($this->cacheDir);

        touch($file, $mtime, $atime);

        return $file;
    }
}
