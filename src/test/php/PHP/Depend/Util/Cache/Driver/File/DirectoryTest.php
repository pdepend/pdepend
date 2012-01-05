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
 * @subpackage Util_Cache_Driver_File
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 * @since      0.10.0
 */

require_once dirname(__FILE__) . '/../../../../AbstractTest.php';

/**
 * Test case for the {@link PHP_Depend_Util_Cache_Driver_File_Directory} class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Util_Cache_Driver_File
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 * @since      0.10.0
 *
 * @covers PHP_Depend_Util_Cache_Driver_File_Directory
 * @group pdepend
 * @group pdepend::util
 * @group pdepend::util::cache
 * @group pdepend::util::cache::file
 * @group unittest
 */
class PHP_Depend_Util_Cache_Driver_File_DirectoryTest extends PHP_Depend_AbstractTest
{
    /**
     * Temporary cache directory.
     *
     * @var string
     */
    protected $cacheDir = null;

    /**
     * File with the cache version information.
     *
     * @var string
     */
    protected $versionFile = null;

    /**
     * Initializes a temporary workling directory.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->cacheDir    = self::createRunResourceURI('cache');
        $this->versionFile = "{$this->cacheDir}/_version";
    }

    /**
     * testCreatesNotExistingCacheDirectory
     *
     * @return void
     */
    public function testCreatesNotExistingCacheDirectory()
    {
        new PHP_Depend_Util_Cache_Driver_File_Directory($this->cacheDir);
        self::assertFileExists($this->cacheDir);
    }

    /**
     * testAddsCacheVersionFileToNewlyCreatedCache
     *
     * @return void
     */
    public function testAddsCacheVersionFileToNewlyCreatedCache()
    {
        new PHP_Depend_Util_Cache_Driver_File_Directory($this->cacheDir);
        self::assertFileExists($this->versionFile);
    }

    /**
     * testCacheVersionFileContainsExpectedVersionString
     *
     * @return void
     */
    public function testCacheVersionFileContainsExpectedVersionString()
    {
        new PHP_Depend_Util_Cache_Driver_File_Directory($this->cacheDir);
        self::assertEquals(
            PHP_Depend_Util_Cache_Driver::VERSION,
            file_get_contents($this->versionFile)
        );
    }

    /**
     * testOverwritesPreviousCacheVersionFileWithActualVersionString
     *
     * @return void
     */
    public function testOverwritesPreviousCacheVersionFileWithActualVersionString()
    {
        mkdir($this->cacheDir, 0755, true);
        file_put_contents($this->versionFile, '1234567890');

        new PHP_Depend_Util_Cache_Driver_File_Directory($this->cacheDir);
        self::assertEquals(
            PHP_Depend_Util_Cache_Driver::VERSION,
            file_get_contents($this->versionFile)
        );
    }

    /**
     * testDeletesCacheFileIfVersionFileNotExists
     *
     * @return void
     */
    public function testDeletesCacheFileIfVersionFileNotExists()
    {
        $cacheFile = "{$this->cacheDir}/test.file";

        mkdir($this->cacheDir, 0755, true);
        file_put_contents($cacheFile, 'Manuel Pichler');
        file_put_contents($this->versionFile, '1234567890');

        new PHP_Depend_Util_Cache_Driver_File_Directory($this->cacheDir);
        self::assertFileNotExists($cacheFile);
    }

    /**
     * testDeletesCacheDirectoryIfVersionFileNotExists
     *
     * @return void
     */
    public function testDeletesCacheDirectoryIfVersionFileNotExists()
    {
        $cacheDir = "{$this->cacheDir}/test.dir";

        mkdir($cacheDir, 0755, true);
        file_put_contents($this->versionFile, '1234567890');

        new PHP_Depend_Util_Cache_Driver_File_Directory($this->cacheDir);
        self::assertFileNotExists($cacheDir);
    }

    /**
     * testDeletesCacheDirectoriesRecusiveIfVersionFileNotExists
     *
     * @return void
     */
    public function testDeletesCacheDirectoriesRecusiveIfVersionFileNotExists()
    {
        $cacheDir  = "{$this->cacheDir}/test/dir";
        $cacheFile = "{$this->cacheDir}/test/test.file";

        mkdir($cacheDir, 0755, true);
        file_put_contents($cacheFile, __FUNCTION__);
        file_put_contents($this->versionFile, '1234567890');

        new PHP_Depend_Util_Cache_Driver_File_Directory($this->cacheDir);
        self::assertFileNotExists("{$this->cacheDir}/test");
    }

    /**
     * testCreateCacheDirectoryReturnsExpectedSubDirectory
     *
     * @return void
     */
    public function testCreateCacheDirectoryReturnsExpectedSubDirectory()
    {
        $dir  = new PHP_Depend_Util_Cache_Driver_File_Directory($this->cacheDir);
        $path = $dir->createCacheDirectory('abcdef0123456789');

        self::assertEquals("{$this->cacheDir}/ab", $path);
    }

    /**
     * testCreateCacheDirectoryAlsoCreatesThePhysicalDirectory
     *
     * @return void
     */
    public function testCreateCacheDirectoryAlsoCreatesThePhysicalDirectory()
    {
        $dir = new PHP_Depend_Util_Cache_Driver_File_Directory($this->cacheDir);
        self::assertFileExists($dir->createCacheDirectory('abcdef0123456789'));
    }
}
