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

namespace PDepend;

use InvalidArgumentException;
use PDepend\Metrics\Analyzer\CrapIndexAnalyzer;
use PDepend\Metrics\AnalyzerFactory;
use PDepend\Report\ReportGeneratorFactory;
use PDepend\TextUI\Runner;
use PDepend\Util\Configuration;

/**
 * Test cases for the {@link \PDepend\Application} class.
 *
 * @covers \PDepend\Application
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group integration
 */
class ApplicationTest extends AbstractTestCase
{
    public function testGetRunner(): void
    {
        $application = $this->createTestApplication();
        $runner = $application->getRunner();

        static::assertInstanceOf(Runner::class, $runner);
    }

    public function testAnalyzerFactory(): void
    {
        $application = $this->createTestApplication();

        static::assertInstanceOf(AnalyzerFactory::class, $application->getAnalyzerFactory());
    }

    public function testReportGeneratorFactory(): void
    {
        $application = $this->createTestApplication();

        static::assertInstanceOf(ReportGeneratorFactory::class, $application->getReportGeneratorFactory());
    }

    public function testBinCanReadInput(): void
    {
        $cwd = getcwd();
        static::assertNotFalse($cwd);
        chdir(__DIR__ . '/../../../..');
        $bin = realpath(__DIR__ . '/../../../../src/bin/pdepend.php');
        $output = shell_exec('echo "<?php class FooBar {}" | php ' . $bin . ' --summary-xml=foo.xml -');
        static::assertNotFalse($output);
        $xml = @file_get_contents('foo.xml');
        static::assertNotFalse($xml);
        unlink('foo.xml');
        chdir($cwd);

        static::assertMatchesRegularExpression('/Parsing source files:\s*\.\s+1/', $output);
        static::assertMatchesRegularExpression('/<class\s.*name="FooBar"/', $xml);
        static::assertMatchesRegularExpression('/<file\s.*name="php:\/\/stdin"/', $xml);
    }

    public function testSetConfigurationFileAndThrowInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches(
            '(^The configuration file ".*fileThatDoesNotExists\\.txt" doesn\\\'t exist\\.$)'
        );

        $filename = __DIR__ . '/fileThatDoesNotExists.txt';

        $application = new Application();
        $application->setConfigurationFile($filename);
    }

    public function testGetConfiguration(): void
    {
        $application = $this->createTestApplication();
        $config = $application->getConfiguration();

        static::assertInstanceOf(Configuration::class, $config);
    }

    public function testGetEngine(): void
    {
        $application = $this->createTestApplication();
        $config = $application->getEngine();

        static::assertInstanceOf(Engine::class, $config);
    }

    public function testGetAvailableLoggerOptions(): void
    {
        $application = $this->createTestApplication();
        $options = $application->getAvailableLoggerOptions();

        static::assertSame([
            'message' => 'Dummy logger for tests',
            'value' => 'file',
        ], $options['--dummy-logger']);
    }

    public function testGetAvailableAnalyzerOptions(): void
    {
        $application = $this->createTestApplication();
        $options = $application->getAvailableAnalyzerOptions();

        static::assertSame([
            'message' => "Clover style CodeCoverage report, as produced by PHPUnit's --coverage-clover option.",
            'value' => 'file',
        ], $options['--' . CrapIndexAnalyzer::REPORT_OPTION]);
    }
}
