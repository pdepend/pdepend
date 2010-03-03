<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2010, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/Depend/Storage/FileEngine.php';

/**
 * Test case for the {@link PHP_Depend_Util_Cache_FileEngineTest} class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Util_Cache
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Util_Cache_FileEngineTest extends PHP_Depend_AbstractTest
{
    /**
     * Cache group fixture.
     */
    const GROUP_FIXTURE = 'test_group_fixture';

    /**
     * testEngineCreatesRequiredCacheRootDirectory
     *
     * @return void
     * @covers PHP_Depend_Storage_FileEngine
     * @group pdepend
     * @group pdepend::util
     * @group pdepend::util::cache
     * @group unittest
     */
    public function testEngineCreatesRequiredCacheRootDirectory()
    {
        $dir = self::createRunResourceURI('cache');

        $engine = new PHP_Depend_Storage_FileEngine($dir);
        $engine->setPrune();
        $engine->store(array(), 42, self::GROUP_FIXTURE, 23);

        $this->assertFileExists($this->_getCacheDirname($dir));
    }

    /**
     * testEngineAddsInstanceTokenToCacheKeyWhenFlaggedAsPrune
     *
     * @return void
     * @covers PHP_Depend_Storage_FileEngine
     * @group pdepend
     * @group pdepend::util
     * @group pdepend::util::cache
     * @group unittest
     */
    public function testEngineAddsInstanceTokenToCacheKeyWhenFlaggedAsPrune()
    {
        $dir = self::createRunResourceURI('cache');

        $engine = new PHP_Depend_Storage_FileEngine($dir);
        $engine->setPrune();
        $engine->store(array(), 42, self::GROUP_FIXTURE, 23);

        $files = glob($this->_getCacheDirname($dir) . '/42.*_*.23.data');
        $this->assertEquals(1, count($files));
    }

    /**
     * testEngineNotAddsInstanceTokenToCacheKeyWhenNotFlaggedAsPrune
     *
     * @return void
     * @covers PHP_Depend_Storage_FileEngine
     * @group pdepend
     * @group pdepend::util
     * @group pdepend::util::cache
     * @group unittest
     */
    public function testEngineNotAddsInstanceTokenToCacheKeyWhenNotFlaggedAsPrune()
    {
        $dir = self::createRunResourceURI('cache');

        $engine = new PHP_Depend_Storage_FileEngine($dir);
        $engine->store(array(), 42, self::GROUP_FIXTURE, 23);

        $files = glob($this->_getCacheDirname($dir) . '/42.23.data');
        $this->assertEquals(1, count($files));
    }

    /**
     * testEngineRestoreReturnsPreviousSavedRecord
     *
     * @return void
     * @covers PHP_Depend_Storage_FileEngine
     * @group pdepend
     * @group pdepend::util
     * @group pdepend::util::cache
     * @group unittest
     */
    public function testEngineRestoreReturnsPreviousSavedRecord()
    {
        $dir = self::createRunResourceURI('cache');

        $data = array(__CLASS__, __FUNCTION__, __METHOD__);

        $engine = new PHP_Depend_Storage_FileEngine($dir);
        $engine->store($data, 42, self::GROUP_FIXTURE, 23);
        $this->assertEquals($data, $engine->restore(42, self::GROUP_FIXTURE, 23));
    }

    /**
     * testEngineRestoreReturnsNullWhenNoMatchingRecordExists
     *
     * @return void
     * @covers PHP_Depend_Storage_FileEngine
     * @group pdepend
     * @group pdepend::util
     * @group pdepend::util::cache
     * @group unittest
     */
    public function testEngineRestoreReturnsNullWhenNoMatchingRecordExists()
    {
        $engine = new PHP_Depend_Storage_FileEngine();
        $engine->setMaxLifetime(0);
        $engine->setProbability(100);
        $this->assertNull($engine->restore(42, self::GROUP_FIXTURE, 23));
    }

    /**
     * testEngineGarbageCollectorRemovesTemporaryCacheData
     *
     * @return void
     * @covers PHP_Depend_Storage_FileEngine
     * @group pdepend
     * @group pdepend::util
     * @group pdepend::util::cache
     * @group unittest
     */
    public function testEngineGarbageCollectorRemovesTemporaryCacheData()
    {
        $dir = self::createRunResourceURI('cache');

        $engine = new PHP_Depend_Storage_FileEngine($dir);
        $engine->setMaxLifetime(10);
        $engine->setProbability(100);
        $engine->store(array(), 42, self::GROUP_FIXTURE, 23);

        $file = $this->_getCacheDirname($dir) . '/42.23.data';
        touch($file, time() - 86400);

        unset($engine);

        $this->assertFileNotExists($file);
    }

    /**
     * testEngineOnlyDeletesCacheDataFromSelfCreatedGroups
     *
     * @return void
     * @covers PHP_Depend_Storage_FileEngine
     * @group pdepend
     * @group pdepend::util
     * @group pdepend::util::cache
     * @group unittest
     */
    public function testEngineOnlyDeletesCacheDataFromSelfCreatedGroups()
    {
        $dir = self::createRunResourceURI('cache');

        $engine = new PHP_Depend_Storage_FileEngine($dir);
        $engine->setMaxLifetime(10);
        $engine->setProbability(100);
        $engine->store(array(), 42, self::GROUP_FIXTURE, 23);

        $file = $this->_getCacheDirname($dir) . '/foo.data';
        touch($file, time());

        unset($engine);

        $this->assertFileExists($file);
    }

    /**
     * Returns the cache root directory.
     *
     * @return string
     */
    private function _getCacheDirname($dir)
    {
        $dir = $dir . '/pdepend_storage';
        if (function_exists('posix_getuid') === true) {
            $dir .= '-' . posix_getuid();
        }
        return $dir . '/' . self::GROUP_FIXTURE;
    }
}