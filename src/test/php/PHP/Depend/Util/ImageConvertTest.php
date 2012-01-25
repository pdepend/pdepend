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
 * @subpackage Util
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for the image convert utility class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Util
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 *
 * @covers PHP_Depend_Util_ImageConvert
 * @group pdepend
 * @group pdepend::util
 * @group pdepend::util::imagick
 * @group unittest
 */
class PHP_Depend_Util_ImageConvertTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests the copy behaviour for same mime types.
     *
     * @return void
     */
    public function testConvertMakesCopyForSameMimeType()
    {
        $input  = self::createInputSvg();
        $output = self::createRunResourceURI('pdepend.out.svg');

        PHP_Depend_Util_ImageConvert::convert($input, $output);
        self::assertFileEquals($input, $output);
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

        $input  = self::createInputSvg();
        $output = self::createRunResourceURI('pdepend.out.png');

        PHP_Depend_Util_ImageConvert::convert($input, $output);
        self::assertFileExists($output);
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

        $input  = self::createInputSvg();
        $output = self::createRunResourceURI('pdepend');

        PHP_Depend_Util_ImageConvert::convert($input, $output);
        self::assertFileExists("{$output}.svg");
    }

    /**
     * testSvgFixtureContainsExpectedNumberOfFontFamilyDefinitions
     *
     * @return void
     */
    public function testSvgFixtureContainsExpectedNumberOfFontFamilyDefinitions()
    {
        $svg = file_get_contents(dirname(__FILE__) . '/_input/pyramid.svg');
        self::assertEquals(25, substr_count($svg, 'font-family:Arial'));
    }

    /**
     * Tests that the convert util recognizes the imageConvert configuration
     * for the font-family:
     *
     * @return void
     */
    public function testConvertRecognizesFontFamilyInConfiguration()
    {
        $settings                           = new stdClass();
        $settings->imageConvert             = new stdClass();
        $settings->imageConvert->fontFamily = 'Verdana';

        $config = new PHP_Depend_Util_Configuration($settings);
        PHP_Depend_Util_ConfigurationInstance::set($config);

        $input  = self::createInputSvg();
        $output = self::createRunResourceURI('pdepend.svg');

        PHP_Depend_Util_ImageConvert::convert($input, $output);

        $svg = file_get_contents($output);
        self::assertEquals(25, substr_count($svg, 'font-family:Verdana'));
    }

    /**
     * testSvgFixtureContainsExpectedNumberOfFontSizeDefinitions
     *
     * @return void
     */
    public function testSvgFixtureContainsExpectedNumberOfFontSizeDefinitions()
    {
        $svg = file_get_contents(dirname(__FILE__) . '/_input/pyramid.svg');
        self::assertEquals(25, substr_count($svg, 'font-size:11px'));
    }

    /**
     * Tests that the convert util recognizes the imageConvert configuration
     * for the font-size:
     *
     * @return void
     */
    public function testConvertRecognizesFontSizeInConfiguration()
    {
        $settings                         = new stdClass();
        $settings->imageConvert           = new stdClass();
        $settings->imageConvert->fontSize = 14;

        $config = new PHP_Depend_Util_Configuration($settings);
        PHP_Depend_Util_ConfigurationInstance::set($config);

        $input  = self::createInputSvg();
        $output = self::createRunResourceURI('pdepend.svg');

        PHP_Depend_Util_ImageConvert::convert($input, $output);

        $svg = file_get_contents($output);
        self::assertEquals(25, substr_count($svg, 'font-size:14px'));
    }

    /**
     * Returns a temporary input svg fixture.
     *
     * @return string
     */
    protected static function createInputSvg()
    {
        $input = self::createRunResourceURI(uniqid('input_')) . '.svg';
        copy(dirname(__FILE__) . '/_input/pyramid.svg', $input);

        return $input;
    }
}
