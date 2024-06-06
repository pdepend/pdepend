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

namespace PDepend\Report\Jdepend;

use PDepend\AbstractTestCase;
use PDepend\Metrics\Analyzer\DependencyAnalyzer;
use PDepend\Report\DummyAnalyzer;
use PDepend\Report\NoLogOutputException;

/**
 * Test case for the jdepend xml logger.
 *
 * @covers \PDepend\Report\Jdepend\Xml
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class XmlTest extends AbstractTestCase
{
    /** Test dependency analyzer. */
    protected DependencyAnalyzer $analyzer;

    /** The temporary file name for the logger result. */
    protected string $resultFile;

    /**
     * Creates the package structure from a test source file.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->resultFile = $this->createRunResourceURI('pdepend-log.xml');
    }

    /**
     * Removes the temporary log files.
     */
    protected function tearDown(): void
    {
        @unlink($this->resultFile);

        parent::tearDown();
    }

    /**
     * Tests that the logger returns the expected set of analyzers.
     */
    public function testReturnsExceptedAnalyzers(): void
    {
        $logger = new Xml();
        $actual = $logger->getAcceptedAnalyzers();
        $expected = ['pdepend.analyzer.dependency'];

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the logger throws an exception if the log target wasn't
     * configured.
     */
    public function testThrowsExceptionForInvalidLogTarget(): void
    {
        $this->expectException(
            NoLogOutputException::class
        );
        $this->expectExceptionMessage(
            "The log target is not configured for 'PDepend\\Report\\Jdepend\\Xml'."
        );

        $logger = new Xml();
        $logger->close();
    }

    /**
     * testXmlLogAcceptsOnlyTheCorrectAnalyzer
     */
    public function testXmlLogAcceptsOnlyTheCorrectAnalyzer(): void
    {
        $logger = new Xml();

        static::assertFalse($logger->log(new DummyAnalyzer()));
        static::assertTrue($logger->log(new DependencyAnalyzer()));
    }

    /**
     * Normalizes the file references within the expected result document.
     *
     * @param string $fileName File name of the expected result document.
     * @return string The prepared xml document
     */
    protected function getNormalizedPathXml(string $fileName): string
    {
        $path = $this->createCodeResourceUriForTest();

        $string = preg_replace(
            '(sourceFile="[^"]+/([^/"]+)")',
            'sourceFile="' . $path . '/\\1"',
            file_get_contents($fileName) ?: ''
        );
        static::assertNotNull($string);

        return $string;
    }
}
