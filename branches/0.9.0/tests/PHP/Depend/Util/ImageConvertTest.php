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
 * @subpackage Util
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Depend/Util/Configuration.php';
require_once 'PHP/Depend/Util/ConfigurationInstance.php';
require_once 'PHP/Depend/Util/ImageConvert.php';

/**
 * Test case for the image convert utility class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Util
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Util_ImageConvertTest extends PHP_Depend_AbstractTest
{
    /**
     * The temporary output file.
     *
     * @var string
     */
    private $_out = null;

    /**
     * Removes temporary output files.
     *
     * @return void
     */
    protected function tearDown()
    {
        if (file_exists($this->_out)) {
            unlink($this->_out);
        }

        parent::tearDown();
    }

    /**
     * Tests the copy behaviour for same mime types.
     *
     * @return void
     */
    public function testConvertMakesCopyForSameMimeType()
    {
        $input      = dirname(__FILE__) . '/_input/pyramid.svg';
        $this->_out = self::createRunResourceURI('pdepend.out.svg');

        $this->assertFileNotExists($this->_out);

        PHP_Depend_Util_ImageConvert::convert($input, $this->_out);

        $this->assertFileExists($this->_out);
        $this->assertFileEquals($input, $this->_out);
    }

    /**
     * Tests the image convert behaviour of the image magick execution path.
     *
     * @return void
     */
    public function testConvertWithImageMagickExtension()
    {
        if (extension_loaded('imagick') === false) {
            $this->markTestSkipped('No pecl/imagick extension.');
        }

        $input      = dirname(__FILE__) . '/_input/pyramid.svg';
        $this->_out = self::createRunResourceURI('pdepend.out.png');

        $this->assertFileNotExists($this->_out);
        PHP_Depend_Util_ImageConvert::convert($input, $this->_out);
        $this->assertFileExists($this->_out);
    }

    /**
     * Tests that the image convert util appends the default extension as fallback.
     *
     * @return void
     */
    public function testConvertAppendDefaultFileExtensionAsFallback()
    {
        if (extension_loaded('imagick') === false) {
            $this->markTestSkipped('No pecl/imagick extension.');
        }

        $input      = dirname(__FILE__) . '/_input/pyramid.svg';
        $this->_out = self::createRunResourceURI('pdepend');

        $this->assertFileNotExists($this->_out);
        PHP_Depend_Util_ImageConvert::convert($input, $this->_out);
        $this->assertFileNotExists($this->_out);

        $this->_out .= '.svg';

        $this->assertFileExists($this->_out);
    }

    /**
     * Tests that the convert util recognizes the imageConvert configuration
     * for the font-family:
     *
     * @return void
     */
    public function testConvertRecognizesFontFamilyInConfiguration()
    {
        $config = new PHP_Depend_Util_Configuration('<?xml version="1.0"?>
        <configuration>
          <imageConvert>
            <fontFamily>Verdana</fontFamily>
          </imageConvert>
        </configuration>
        ');
        PHP_Depend_Util_ConfigurationInstance::set($config);

        $this->_out = self::createRunResourceURI('pdepend.svg');
        copy(dirname(__FILE__) . '/_input/pyramid.svg', $this->_out);

        $svg = file_get_contents($this->_out);
        preg_match_all('/font-family:\s*Arial/', $svg, $matches);
        $expectedArial = count($matches[0]);
        preg_match_all('/font-family:\s*Verdana/', $svg, $matches);
        $expectedVerdana = count($matches[0]);

        $this->assertEquals(0, $expectedVerdana);

        PHP_Depend_Util_ImageConvert::convert($this->_out, $this->_out);

        $svg = file_get_contents($this->_out);
        preg_match_all('/font-family:\s*Arial/', $svg, $matches);
        $actualArial = count($matches[0]);
        preg_match_all('/font-family:\s*Verdana/', $svg, $matches);
        $actualVerdana = count($matches[0]);

        $this->assertEquals(0, $actualArial);
        $this->assertEquals($expectedArial, $actualVerdana);
    }

    /**
     * Tests that the convert util recognizes the imageConvert configuration
     * for the font-size:
     *
     * @return void
     */
    public function testConvertRecognizesFontSizeInConfiguration()
    {
        $config = new PHP_Depend_Util_Configuration('<?xml version="1.0"?>
        <configuration>
          <imageConvert>
            <fontSize>14</fontSize>
          </imageConvert>
        </configuration>
        ');
        PHP_Depend_Util_ConfigurationInstance::set($config);

        $this->_out = self::createRunResourceURI('pdepend.svg');
        copy(dirname(__FILE__) . '/_input/pyramid.svg', $this->_out);

        $svg = file_get_contents($this->_out);
        preg_match_all('/font-size:\s*11px/', $svg, $matches);
        $expected11 = count($matches[0]);
        preg_match_all('/font-size:\s*14px/', $svg, $matches);
        $expected14 = count($matches[0]);


        $this->assertEquals(25, $expected11);
        $this->assertEquals(0, $expected14);

        PHP_Depend_Util_ImageConvert::convert($this->_out, $this->_out);

        $svg = file_get_contents($this->_out);
        preg_match_all('/font-size:\s*11px/', $svg, $matches);
        $actual11 = count($matches[0]);
        preg_match_all('/font-size:\s*14px/', $svg, $matches);
        $actual14 = count($matches[0]);

        $this->assertEquals(0, $actual11);
        $this->assertEquals(25, $actual14);
    }
}