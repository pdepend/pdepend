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
 * @subpackage Util_Configuration
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 * @since      0.10.0
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

/**
 * Test case for the {@link PHP_Depend_Util_Configuration_Factory} class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Util_Configuration
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 * @since      0.10.0
 *
 * @covers PHP_Depend_Util_Configuration_Factory
 * @group pdepend
 * @group pdepend::util
 * @group pdepend::util::configuration
 * @group unittest
 */
class PHP_Depend_Util_Configuration_FactoryTest extends PHP_Depend_AbstractTest
{
    /**
     * The current working directory.
     *
     * @var string
     */
    protected $workingDir = null;

    /**
     * Changes the current working directory.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->workingDir = getcwd();

        chdir(dirname(__FILE__));
    }

    /**
     * Restores the original working directory.
     *
     * @return void
     */
    protected function tearDown()
    {
        chdir($this->workingDir);

        parent::tearDown();
    }

    /**
     * testDefaultConfigurationHasExpectedCacheDriver
     *
     * @return void
     */
    public function testDefaultConfigurationHasExpectedCacheDriver()
    {
        $factory = new PHP_Depend_Util_Configuration_Factory();
        $config  = $factory->createDefault();

        self::assertEquals('file', $config->cache->driver);
    }

    /**
     * testDefaultConfigurationHasExpectedCacheLocation
     *
     * @return void
     */
    public function testDefaultConfigurationHasExpectedCacheLocation()
    {
        $factory = new PHP_Depend_Util_Configuration_Factory();
        $config  = $factory->createDefault();

        self::assertEquals(
            PHP_Depend_Util_FileUtil::getUserHomeDirOrSysTempDir() . '/.pdepend',
            $config->cache->location
        );
    }

    /**
     * testDefaultConfigurationHasExpectedFontFamily
     *
     * @return void
     */
    public function testDefaultConfigurationHasExpectedFontFamily()
    {
        $factory = new PHP_Depend_Util_Configuration_Factory();
        $config  = $factory->createDefault();

        self::assertEquals('Arial', $config->imageConvert->fontFamily);
    }

    /**
     * testDefaultConfigurationHasExpectedFontSize
     *
     * @return void
     */
    public function testDefaultConfigurationHasExpectedFontSize()
    {
        $factory = new PHP_Depend_Util_Configuration_Factory();
        $config  = $factory->createDefault();

        self::assertEquals(11, $config->imageConvert->fontSize);
    }

    /**
     * testCreateDefaultOverwritesSettingsWithValuesDefinedInXmlDist
     *
     * @return void
     */
    public function testCreateDefaultOverwritesSettingsWithValuesDefinedInXmlDist()
    {
        chdir(self::createCodeResourceUriForTest());

        $factory = new PHP_Depend_Util_Configuration_Factory();
        $config  = $factory->createDefault();

        self::assertEquals(23, $config->imageConvert->fontSize);
    }

    /**
     * testCreateDefaultOverwritesSettingsWithValuesDefinedInXml
     *
     * @return void
     */
    public function testCreateDefaultOverwritesSettingsWithValuesDefinedInXml()
    {
        chdir(self::createCodeResourceUriForTest());

        $factory = new PHP_Depend_Util_Configuration_Factory();
        $config  = $factory->createDefault();

        self::assertEquals(42, $config->imageConvert->fontSize);
    }

    /**
     * testCreateDefaultOverwritesSettingsWithValuesDefinedInXmlAndXmlDist
     *
     * @return void
     */
    public function testCreateDefaultOverwritesSettingsWithValuesDefinedInXmlAndXmlDist()
    {
        chdir(self::createCodeResourceUriForTest());

        $factory = new PHP_Depend_Util_Configuration_Factory();
        $config  = $factory->createDefault();

        self::assertEquals(42, $config->imageConvert->fontSize);
    }

    /**
     * testDefaultConfigurationHasExpectedParserNesting
     *
     * @return void
     */
    public function testDefaultConfigurationHasExpectedParserNesting()
    {
        $factory = new PHP_Depend_Util_Configuration_Factory();
        $config  = $factory->createDefault();

        self::assertEquals(8192, $config->parser->nesting);
    }

    /**
     * testCreateForNotExistingFileThrowsExpectedException
     *
     * @return void
     * @expectedException InvalidArgumentException
     */
    public function testCreateForNotExistingFileThrowsExpectedException()
    {
        $factory = new PHP_Depend_Util_Configuration_Factory();
        $factory->create(md5(microtime()) . '.xml');
    }

    /**
     * testCreateReturnsExpectedConfigurationInstance
     *
     * @return void
     */
    public function testCreateReturnsExpectedConfigurationInstance()
    {
        $file = self::createCodeResourceUriForTest() . '/pdepend.xml';

        $factory = new PHP_Depend_Util_Configuration_Factory();
        $config  = $factory->create($file);

        self::assertEquals(42, $config->imageConvert->fontSize);
    }
}
