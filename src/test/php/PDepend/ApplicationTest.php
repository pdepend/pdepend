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

/**
 * Test cases for the {@link \PDepend\Application} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Application
 * @group integration
 */
class ApplicationTest extends AbstractTest
{
    public function testGetRunner()
    {
        $application = $this->createTestApplication();
        $runner = $application->getRunner();

        $this->assertInstanceOf('PDepend\TextUI\Runner', $runner);
    }

    public function testAnalyzerFactory()
    {
        $application = $this->createTestApplication();

        $this->assertInstanceOf('PDepend\Metrics\AnalyzerFactory', $application->getAnalyzerFactory());
    }

    public function testReportGeneratorFactory()
    {
        $application = $this->createTestApplication();

        $this->assertInstanceOf('PDepend\Report\ReportGeneratorFactory', $application->getReportGeneratorFactory());
    }

    public function testBinCanReadInput()
    {
        $cwd = getcwd();
        chdir(__DIR__ . '/../../../..');
        $bin = realpath(__DIR__ . '/../../../../src/bin/pdepend.php');
        $output = shell_exec('echo "<?php class FooBar {}" | php ' . $bin . ' --summary-xml=foo.xml -');
        $xml = @file_get_contents('foo.xml');
        unlink('foo.xml');
        chdir($cwd);

        $this->assertRegExp('/Parsing source files:\s*\.\s+1/', $output);
        $this->assertRegExp('/<class\s.*name="FooBar"/', $xml);
        $this->assertRegExp('/<file\s.*name="php:\/\/stdin"/', $xml);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessageRegExp (^The configuration file ".*fileThatDoesNotExists\.txt" doesn\'t exist\.$)
     */
    public function testSetConfigurationFileAndThrowInvalidArgumentException() 
    {
        $filename = __DIR__ . '/fileThatDoesNotExists.txt';

        $application = new \PDepend\Application();
        $application->setConfigurationFile($filename);
    }

    public function testGetConfiguration()
    {
        $application = $this->createTestApplication();
        $config = $application->getConfiguration();

        $this->assertInstanceOf('PDepend\Util\Configuration', $config);
    }

    public function testGetEngine()
    {
        $application = $this->createTestApplication();
        $config = $application->getEngine();

        $this->assertInstanceOf('PDepend\Engine', $config);
    }
}
