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

namespace PDepend\Issues;

use PDepend\Input\ExtensionFilter;
use PDepend\Report\Dummy\Logger;
use PDepend\TextUI\Command;

/**
 * Test case for the catch error ticket #61.
 *
 * @covers \PDepend\Engine
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class PHPDependCatchesParsingErrorsIssue061Test extends AbstractFeatureTestCase
{
    /**
     * Tests that the {@link \PDepend\Engine::getExceptions()} returns a
     * list with the expected exceptions.
     */
    public function testPHPDependReturnsExpectedExceptionInstances(): void
    {
        $pdepend = $this->createEngineFixture();
        $pdepend->addDirectory($this->createCodeResourceUriForTest());
        $pdepend->addFileFilter(new ExtensionFilter(['php']));
        $pdepend->addReportGenerator(new Logger());
        $pdepend->analyze();

        $exceptions = $pdepend->getExceptions();
        static::assertStringStartsWith(
            'Unexpected token: ), line: 7, col: 49, file:',
            $exceptions[0]->getMessage()
        );
    }

    /**
     * Tests that the {@link \PDepend\TextUI\Runner::hasErrors()} method will
     * return <b>false</b> when not parsing error occurred.
     *
     * @covers \PDepend\TextUI\Runner
     */
    public function testRunnerReturnsFalseWhenNoErrorOccurredDuringTheParsingProcess(): void
    {
        $runner = $this->createTextUiRunner();
        $runner->addReportGenerator('dummy-logger', $this->createRunResourceURI('pdepend.log'));
        $runner->setSourceArguments([$this->createCodeResourceUriForTest()]);
        $this->silentRun($runner);

        static::assertFalse($runner->hasParseErrors());
    }

    /**
     * Tests that the {@link \PDepend\TextUI\Runner::hasErrors()} method will
     * return <b>true</b> when a parsing error occurred.
     *
     * @covers \PDepend\TextUI\Runner
     */
    public function testRunnerReturnsTrueWhenAnErrorOccurredDuringTheParsingProcess(): void
    {
        $runner = $this->createTextUiRunner();
        $runner->addReportGenerator('dummy-logger', $this->createRunResourceURI('pdepend.log'));
        $runner->setSourceArguments([$this->createCodeResourceUriForTest()]);

        $this->silentRun($runner);

        static::assertTrue($runner->hasParseErrors());
    }

    /**
     * Tests that the output does not contain the error hint when the parsing
     * process was successful.
     *
     * @covers \PDepend\TextUI\Command
     */
    public function testCommandDoesNotPrintErrorOutputOnSuccessfulParsingProcess(): void
    {
        $this->prepareArgv(
            [
                '--dummy-logger=' . $this->createRunResourceURI('pdepend.log'),
                $this->createCodeResourceUriForTest(),
            ]
        );

        [$exitCode, $output] = $this->runTextUICommand();
        static::assertIsString($output);

        static::assertStringNotContainsString('Following errors occurred:', $output);
    }

    /**
     * testCommandPrintsExceptionMessageWhenAnErrorOccurredDuringTheParsingProcess
     *
     * @covers \PDepend\TextUI\Command
     */
    public function testCommandPrintsExceptionMessageWhenAnErrorOccurredDuringTheParsingProcess(): void
    {
        $this->prepareArgv(
            [
                '--dummy-logger=' . $this->createRunResourceURI('pdepend.log'),
                '--configuration=' . __DIR__ . '/../../../resources/pdepend.xml.dist',
                $this->createCodeResourceUriForTest(),
            ]
        );
        [$exitCode, $output] = $this->runTextUICommand();
        static::assertIsString($output);

        static::assertStringContainsString('Unexpected token: ), line: 7, col: 49, file:', $output);
    }

    /**
     * Sets a command line argument vector.
     *
     * @param array<string> $argv The temporary command line argument vector
     */
    protected function prepareArgv(array $argv): void
    {
        array_unshift($argv, __FILE__);

        $_SERVER['argv'] = $argv;
    }

    /**
     * Executes PDepend's text ui command and returns the exit code and shell
     * output.
     *
     * @return array<mixed>
     */
    protected function runTextUICommand(): array
    {
        $command = new Command();

        ob_start();
        $exitCode = $command->run();
        $output = ob_get_clean();

        return [$exitCode, $output];
    }
}
