<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since     0.10.0
 */

namespace PHP\Depend\Util\Configuration;

/**
 * Test case for the {@link \PHP\Depend\Util\Configuration\Parser} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since 0.10.0
 *
 * @covers \PHP\Depend\Util\Configuration\Parser
 * @group pdepend
 * @group pdepend::util
 * @group pdepend::util::configuration
 * @group unittest
 */
class ParserTest extends \PHP_Depend_AbstractTest
{
    /**
     * testParserHandlesEmptyConfigurationFile
     *
     * @return void
     */
    public function testParserHandlesEmptyConfigurationFile()
    {
        $parser = new Parser(new \stdClass());
        $this->assertNotNull($parser->parse($this->getTestConfiguration('pdepend.xml')));
    }

    /**
     * testParserHandlesCacheDriverConfigurationValue
     *
     * @return void
     */
    public function testParserHandlesCacheDriverConfigurationValue()
    {
        $parser = new Parser($this->createFixture());
        $values = $parser->parse($this->getTestConfiguration('pdepend.xml'));

        $this->assertEquals('memory', $values->cache->driver);
    }

    /**
     * testParserHandlesCacheLocationConfigurationValue
     *
     * @return void
     */
    public function testParserHandlesCacheLocationConfigurationValue()
    {
        $parser = new Parser($this->createFixture());
        $values = $parser->parse($this->getTestConfiguration('pdepend.xml'));

        $this->assertEquals('/foo/bar/baz', $values->cache->location);
    }

    /**
     * testParserHandlesImagickFontFamilyConfigurationValue
     *
     * @return void
     */
    public function testParserHandlesImagickFontFamilyConfigurationValue()
    {
        $parser = new Parser($this->createFixture());
        $values = $parser->parse($this->getTestConfiguration('pdepend.xml'));

        $this->assertEquals('Courier New', $values->imageConvert->fontFamily);
    }

    /**
     * testParserHandlesImagickFontSizeConfigurationValue
     *
     * @return void
     */
    public function testParserHandlesImagickFontSizeConfigurationValue()
    {
        $parser = new Parser($this->createFixture());
        $values = $parser->parse($this->getTestConfiguration('pdepend.xml'));

        $this->assertEquals(23, $values->imageConvert->fontSize);
    }

    /**
     * testParserHandlesParserNestingConfigurationValue
     *
     * @return void
     */
    public function testParserHandlesParserNestingConfigurationValue()
    {
        $parser = new Parser($this->createFixture());
        $values = $parser->parse($this->getTestConfiguration('pdepend.xml'));

        $this->assertEquals(423, $values->parser->nesting);
    }

    /**
     * testParserModifiesConfigurationAdaptive
     *
     * @return void
     */
    public function testParserModifiesConfigurationAdaptive()
    {
        $parser = new Parser($this->createFixture());
        $parser->parse($this->getTestConfiguration('pdepend.xml.dist'));

        $values = $parser->parse($this->getTestConfiguration('pdepend.xml'));

        $this->assertEquals(23, $values->imageConvert->fontSize);
    }

    /**
     * testParserOverwritesAlreadyDefinedConfigurationValues
     *
     * @return void
     */
    public function testParserOverwritesAlreadyDefinedConfigurationValues()
    {
        $parser = new Parser($this->createFixture());
        $parser->parse($this->getTestConfiguration('pdepend.xml.dist'));

        $values = $parser->parse($this->getTestConfiguration('pdepend.xml'));

        $this->assertEquals('Courier New', $values->imageConvert->fontFamily);
    }

    /**
     * Returns a full qualified configuration file name.
     *
     * @param string $file The local config file name.
     * @return string
     */
    protected function getTestConfiguration($file)
    {
        return self::createCodeResourceUriForTest() . '/' . $file;
    }

    /**
     * Creates a test configuration fixture.
     *
     * @return \stdClass
     */
    protected function createFixture()
    {
        return json_decode(
            '{
                "cache": {
                    "driver":   "foo",
                    "location": "/foo"
                },
                "imageConvert": {
                    "fontFamily": "Arial",
                    "fontSize":   42
                },
                "parser": {
                    "nesting": 4096
                }
            }'
        );
    }
}
