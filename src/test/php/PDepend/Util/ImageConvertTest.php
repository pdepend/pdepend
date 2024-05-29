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

namespace PDepend\Util;

use PDepend\AbstractTestCase;
use stdClass;

/**
 * Test case for the image convert utility class.
 *
 * @covers \PDepend\Util\ImageConvert
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @group unittest
 */
class ImageConvertTest extends AbstractTestCase
{
    /**
     * Tests the copy behaviour for same mime types.
     */
    public function testConvertMakesCopyForSameMimeType(): void
    {
        $input = $this->createInputSvg();
        $output = $this->createRunResourceURI('pdepend.out') . '.svg';

        ImageConvert::convert($input, $output);
        static::assertFileEquals($input, $output);
    }

    /**
     * Tests the image convert behaviour of the image magick execution path.
     */
    public function testConvertWithImageMagickExtension(): void
    {
        $this->requireImagick();

        $input = $this->createInputSvg();
        $output = $this->createRunResourceURI('pdepend.out') . '.png';

        ImageConvert::convert($input, $output);
        static::assertFileExists($output);
    }

    /**
     * Tests that the image convert util appends the default extension as fallback.
     */
    public function testConvertAppendDefaultFileExtensionAsFallback(): void
    {
        if (!extension_loaded('imagick')) {
            static::markTestSkipped('No pecl/imagick extension.');
        }

        $input = $this->createInputSvg();
        $output = $this->createRunResourceURI('pdepend');

        ImageConvert::convert($input, $output);
        static::assertFileExists("{$output}.svg");
    }

    /**
     * testSvgFixtureContainsExpectedNumberOfFontFamilyDefinitions
     */
    public function testSvgFixtureContainsExpectedNumberOfFontFamilyDefinitions(): void
    {
        $svg = file_get_contents(__DIR__ . '/_input/pyramid.svg') ?: '';
        static::assertEquals(25, substr_count($svg, 'font-family:Arial'));
    }

    /**
     * Tests that the convert util recognizes the imageConvert configuration
     * for the font-family:
     */
    public function testConvertRecognizesFontFamilyInConfiguration(): void
    {
        $settings = new stdClass();
        $settings->imageConvert = new stdClass();
        $settings->imageConvert->fontFamily = 'Verdana';

        $config = new Configuration($settings);
        ConfigurationInstance::set($config);

        $input = $this->createInputSvg();
        $output = $this->createRunResourceURI('pdepend') . '.svg';

        ImageConvert::convert($input, $output);

        $svg = file_get_contents($output) ?: '';
        static::assertEquals(25, substr_count($svg, 'font-family:Verdana'));
    }

    /**
     * testSvgFixtureContainsExpectedNumberOfFontSizeDefinitions
     */
    public function testSvgFixtureContainsExpectedNumberOfFontSizeDefinitions(): void
    {
        $svg = file_get_contents(__DIR__ . '/_input/pyramid.svg') ?: '';
        static::assertEquals(25, substr_count($svg, 'font-size:11px'));
    }

    /**
     * Tests that the convert util recognizes the imageConvert configuration
     * for the font-size:
     */
    public function testConvertRecognizesFontSizeInConfiguration(): void
    {
        $settings = new stdClass();
        $settings->imageConvert = new stdClass();
        $settings->imageConvert->fontSize = 14;

        $config = new Configuration($settings);
        ConfigurationInstance::set($config);

        $input = $this->createInputSvg();
        $output = $this->createRunResourceURI('pdepend') . '.svg';

        ImageConvert::convert($input, $output);

        $svg = file_get_contents($output) ?: '';
        static::assertEquals(25, substr_count($svg, 'font-size:14px'));
    }

    /**
     * Returns a temporary input svg fixture.
     */
    protected function createInputSvg(): string
    {
        $input = $this->createRunResourceURI(uniqid('input_')) . '.svg';
        copy(__DIR__ . '/_input/pyramid.svg', $input);

        return $input;
    }
}
