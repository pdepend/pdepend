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

namespace PDepend\TextUI;

use PDepend\AbstractTestCase;
use PDepend\MockCommand;
use PDepend\Util\ConfigurationInstance;
use PDepend\Util\Log;
use ReflectionClass;

/**
 * Test case for the text ui command.
 *
 * @covers \PDepend\TextUI\Command
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class CommandTest extends AbstractTestCase
{
    /**
     * Expected output of the --version option.
     *
     * @var string
     */
    private $versionOutput;

    /**
     * Expected output of the --usage option.
     *
     * @var string
     */
    private $usageOutput;

    protected function setUp(): void
    {
        parent::setUp();

        $data = @parse_ini_file(__DIR__ . '/../../../../../build.properties');

        $this->versionOutput = sprintf('PDepend %s%s%s', $data['project.version'], PHP_EOL, PHP_EOL);
        $this->usageOutput = 'Usage: pdepend [options] [logger] <dir[,dir[,...]]>' . PHP_EOL . PHP_EOL;
    }

    /**
     * Tests the result of the print version option.
     */
    public function testPrintVersion(): void
    {
        [, $actual] = $this->executeCommand(['--version']);
        $this->assertEquals($this->versionOutput, $actual);
    }

    /**
     * testPrintVersionReturnsExitCodeSuccess
     */
    public function testPrintVersionReturnsExitCodeSuccess(): void
    {
        [$exitCode, ] = $this->executeCommand(['--version']);
        $this->assertEquals(Runner::SUCCESS_EXIT, $exitCode);
    }

    /**
     * Tests the result of the print usage option.
     */
    public function testPrintUsage(): void
    {
        [, $actual] = $this->executeCommand(['--usage']);
        $this->assertEquals($this->versionOutput . $this->usageOutput, $actual);
    }

    /**
     * testPrintUsageReturnsExitCodeSuccess
     */
    public function testPrintUsageReturnsExitCodeSuccess(): void
    {
        [$exitCode, ] = $this->executeCommand(['--usage']);
        $this->assertEquals(Runner::SUCCESS_EXIT, $exitCode);
    }

    /**
     * Tests the output of the print help option.
     */
    public function testPrintHelp(): void
    {
        [, $actual] = $this->executeCommand(['--help']);
        $this->assertHelpOutput($actual);
    }

    /**
     * testPrintHelpReturnsExitCodeSuccess
     */
    public function testPrintHelpReturnsExitCodeSuccess(): void
    {
        [$exitCode, ] = $this->executeCommand(['--help']);
        $this->assertEquals(Runner::SUCCESS_EXIT, $exitCode);
    }

    /**
     * Tests that the command exits with an cli error if no $argv array exists.
     */
    public function testCommandCliReturnsErrorExitCodeIfNoArgvArrayExists(): void
    {
        [$exitCode, ] = $this->executeCommand();
        $this->assertEquals(Command::CLI_ERROR, $exitCode);
    }

    /**
     * testCommandCliErrorMessageIfNoArgvArrayExists
     */
    public function testCommandCliErrorMessageIfNoArgvArrayExists(): void
    {
        [, $actual] = $this->executeCommand();
        $startsWith = 'Unknown error, no $argv array available.' . PHP_EOL . PHP_EOL;
        $this->assertHelpOutput($actual, $startsWith);
    }

    /**
     * Tests that the command exits with a cli error for an empty option list.
     */
    public function testCommandDisplaysHelpIfNoOptionsWereSpecified(): void
    {
        [, $actual] = $this->executeCommand([]);
        $this->assertHelpOutput($actual);
    }

    /**
     * testCommandReturnsErrorExitCodeIfNoOptionsWereSpecified
     */
    public function testCommandReturnsErrorExitCodeIfNoOptionsWereSpecified(): void
    {
        [$exitCode, ] = $this->executeCommand([]);
        $this->assertEquals(Command::CLI_ERROR, $exitCode);
    }

    /**
     * Tests that the command starts the text ui runner.
     */
    public function testCommandStartsProcessWithDummyLogger(): void
    {
        $logFile = $this->createRunResourceURI();
        $resource = $this->createCodeResourceUriForTest();

        set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__);

        $argv = [
            '--suffix=inc',
            '--ignore=code-5.2.x',
            '--exclude=pdepend.test2',
            '--configuration=' . __DIR__ . '/../../../resources/pdepend.xml.dist',
            '--dummy-logger=' . $logFile,
            $resource,
        ];

        [$exitCode, ] = $this->executeCommand($argv);

        $this->assertEquals(Runner::SUCCESS_EXIT, $exitCode);
        $this->assertFileExists($logFile);
    }

    /**
     * testCommandReturnsExitCodeSuccessByDefault
     */
    public function testCommandReturnsExitCodeSuccessByDefault(): void
    {
        $logFile = $this->createRunResourceURI();
        $resource = $this->createCodeResourceUriForTest();

        set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__);

        $argv = [
            '--suffix=inc',
            '--configuration=' . __DIR__ . '/../../../resources/pdepend.xml.dist',
            '--dummy-logger=' . $logFile,
            $resource,
        ];

        [$exitCode, ] = $this->executeCommand($argv);
        $this->assertEquals(Runner::SUCCESS_EXIT, $exitCode);
    }

    /**
     * Tests that the command exits with a cli error for an unknown option.
     */
    public function testCommandExitsWithCliErrorForUnknownOption(): void
    {
        [$exitCode, ] = $this->executeCommand(['--unknown']);
        $this->assertEquals(Command::CLI_ERROR, $exitCode);
    }

    /**
     * Tests that the command handles the <b>--without-annotations</b> option
     * correct.
     */
    public function testCommandHandlesWithoutAnnotationsOptionCorrect(): void
    {
        $expected = [
            'pdepend.test' => [
                'functions' => ['foo'],
                'classes' => ['MyException'],
                'interfaces' => [],
                'exceptions' => [],
            ],
            'pdepend.test2' => [
                'functions' => [],
                'classes' => ['YourException'],
                'interfaces' => [],
                'exceptions' => [],
            ],
        ];

        $actual = $this->runCommandAndReturnStatistics(
            [
                '--suffix=inc',
                '--without-annotations',
                '--coderank-mode=property',
            ],
            $this->createCodeResourceUriForTest()
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testCommandHandlesBadDocumentedSourceCode
     */
    public function testCommandHandlesBadDocumentedSourceCode(): void
    {
        $expected = [
            '+global' => [
                'functions' => ['pkg3_foo'],
                'classes' => [
                    'Bar',
                    'pkg1Bar',
                    'pkg1Barfoo',
                    'pkg1Foo',
                    'pkg1Foobar',
                    'pkg2Bar',
                    'pkg2Barfoo',
                    'pkg2Foobar',
                ],
                'interfaces' => [
                    'pkg1FooI',
                    'pkg2FooI',
                    'pkg3FooI',
                ],
                'exceptions' => [],
            ],
        ];

        $actual = $this->runCommandAndReturnStatistics(
            [],
            $this->createCodeResourceUriForTest()
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * Executes the command class and returns an array with namespace statistics.
     *
     * @param string $pathName
     * @return array
     */
    private function runCommandAndReturnStatistics(array $argv, $pathName)
    {
        $logFile = $this->createRunResourceURI();

        $argv[] = '--dummy-logger=' . $logFile;
        $argv[] = '--configuration=' . __DIR__ . '/../../../resources/pdepend.xml.dist';
        $argv[] = $pathName;

        if (file_exists($logFile)) {
            unlink($logFile);
        }

        $this->executeCommand($argv);

        $data = unserialize(file_get_contents($logFile));
        $code = $data['code'];

        $actual = [];
        foreach ($code as $namespace) {
            $statistics = [
                'functions' => [],
                'classes' => [],
                'interfaces' => [],
                'exceptions' => [],
            ];
            foreach ($namespace->getFunctions() as $function) {
                $statistics['functions'][] = $function->getName();
                foreach ($function->getExceptionClasses() as $exception) {
                    $statistics['exceptions'][] = $exception->getName();
                }
            }

            foreach ($namespace->getClasses() as $class) {
                $statistics['classes'][] = $class->getName();
            }

            foreach ($namespace->getInterfaces() as $interface) {
                $statistics['interfaces'][] = $interface->getName();
            }

            sort($statistics['functions']);
            sort($statistics['classes']);
            sort($statistics['interfaces']);
            sort($statistics['exceptions']);

            $actual[$namespace->getName()] = $statistics;
        }
        ksort($actual);

        return $actual;
    }

    /**
     * Tests that the command interpretes a "-d key" as "on".
     */
    public function testCommandHandlesIniOptionWithoutValueToON(): void
    {
        // Get backup
        if (($backup = ini_set('html_errors', 'off')) === false) {
            $this->markTestSkipped('Cannot alter ini setting "html_errors".');
        }

        $this->executeCommand(
            [
                '-d',
                'html_errors',
                '--dummy-logger=' . $this->createRunResourceURI(),
                __FILE__,
            ]
        );

        $this->assertEquals('on', ini_get('html_errors'));

        ini_set('html_errors', $backup);
    }

    /**
     * Tests that the text ui command handles an ini option "-d key=value" correct.
     */
    public function testCommandHandlesIniOptionWithValue(): void
    {
        // Get backup
        if (($backup = ini_set('html_errors', 'on')) === false) {
            $this->markTestSkipped('Cannot alter ini setting "html_errors".');
        }

        $this->executeCommand(
            [
                '-d',
                'html_errors=off',
                '--dummy-logger=' . $this->createRunResourceURI(),
                __FILE__,
            ]
        );

        $this->assertEquals('off', ini_get('html_errors'));

        ini_set('html_errors', $backup);
    }

    /**
     * Tests that the command sets a configuration instance for a specified
     * config file.
     */
    public function testCommandHandlesConfigurationFileCorrect(): void
    {
        // Sample config file
        $configFile = $this->createRunResourceURI('config.xml');
        // Write a dummy config file.
        file_put_contents(
            $configFile,
            '<?xml version="1.0"?>
             <symfony:container xmlns:symfony="http://symfony.com/schema/dic/services"
                 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                 xmlns="http://pdepend.org/schema/dic/pdepend"
                 xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
               <config>
                   <cache>
                     <driver>memory</driver>
                   </cache>
               </config>
             </symfony:container>'
        );

        $argv = [
            '--configuration=' . $configFile,
            '--dummy-logger=' . $this->createRunResourceURI(),
            __FILE__,
        ];

        // Result previous instance
        ConfigurationInstance::set(null);

        $this->executeCommand($argv);

        $config = ConfigurationInstance::get();
        $this->assertEquals('memory', $config->cache->driver);
    }

    /**
     * testTextUiCommandOutputContainsExpectedCoverageReportOption
     */
    public function testTextUiCommandOutputContainsExpectedCoverageReportOption(): void
    {
        [, $actual] = $this->executeCommand([]);
        $this->assertStringContainsString('--coverage-report=<file>', $actual);
    }

    /**
     * testTextUiCommandFailesWithExpectedErrorCodeWhenCoverageReportFileDoesNotExist
     */
    public function testTextUiCommandFailesWithExpectedErrorCodeWhenCoverageReportFileDoesNotExist(): void
    {
        $filePath = $this->createRunResourceURI('foobar');
        unlink($filePath);
        $argv = [
            '--coverage-report=' . $filePath,
            '--dummy-logger=' . $this->createRunResourceURI(),
            __FILE__,
        ];

        [$exitCode, ] = $this->executeCommand($argv);

        $this->assertEquals(Command::INPUT_ERROR, $exitCode);
    }

    /**
     * testTextUiCommandAcceptsExistingFileForCoverageReportOption
     */
    public function testTextUiCommandAcceptsExistingFileForCoverageReportOption(): void
    {
        $argv = [
            '--coverage-report=' . __DIR__ . '/_files/clover.xml',
            '--dummy-logger=' . $this->createRunResourceURI(),
            '--configuration=' . __DIR__ . '/../../../resources/pdepend.xml.dist',
            __FILE__,
        ];

        [$exitCode, ] = $this->executeCommand($argv);

        $this->assertEquals(Runner::SUCCESS_EXIT, $exitCode);
    }

    /**
     * Tests that the command fails for an invalid config file.
     */
    public function testCommandFailsIfAnInvalidConfigFileWasSpecified(): void
    {
        $configFile = $this->createRunResourceURI('config') . '.xml';

        $argv = ['--configuration=' . $configFile, __FILE__];

        [$exitCode, $actual] = $this->executeCommand($argv);

        $this->assertSame(Command::CLI_ERROR, $exitCode);
        $this->assertStringContainsString(
            sprintf('The configuration file "%s" doesn\'t exist.', $configFile),
            $actual
        );
    }

    public function testQuietModeWillSuppressVersionAndStatistics(): void
    {
        $argv = [
            '--quiet',
            '--coverage-report=' . __DIR__ . '/_files/clover.xml',
            '--dummy-logger=' . $this->createRunResourceURI(),
            '--configuration=' . __DIR__ . '/../../../resources/pdepend.xml.dist',
            __FILE__,
        ];

        [$exitCode, $actual] = $this->executeCommand($argv);

        $this->assertEquals(Runner::SUCCESS_EXIT, $exitCode);
        $this->assertEmpty('', $actual);
    }

    public function testErrorDisplay(): void
    {
        ob_start();
        $exitCode = MockCommand::main();
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertSame('Critical error:' . PHP_EOL . '===============' . PHP_EOL . 'Bad usage', trim($output));
        $this->assertSame(42, $exitCode);
    }

    public function testDebugErrorDisplay(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'err');
        $streamProperty = new ReflectionClass(Log::class);
        $streamProperty->setStaticPropertyValue('stream', fopen($file, 'a+'));

        Log::setSeverity(Log::DEBUG);

        ob_start();
        $exitCode = MockCommand::main();
        $output = ob_get_contents();
        ob_end_clean();

        Log::setSeverity(2);
        $error = file_get_contents($file);
        unlink($file);
        $streamProperty->setStaticPropertyValue('stream', STDERR);

        $this->assertSame('Critical error:' . PHP_EOL . '===============' . PHP_EOL . 'Bad usage', trim($output));
        $this->assertSame(42, $exitCode);
        $this->assertMatchesRegularExpression('/^
                \nRuntimeException\(Bad\susage\)\n
                ##\s.+[\/\\\\]MockCommand\.php\(20\)\n
                #0 .+[\/\\\\]Command\.php\(\d+\):\sPDepend\MockCommand->printVersion\(\)\n
                [\s\S]+\n\n
                Caused\sby:\n
                BadMethodCallException\(Cause\)\n
                ##\s.+[\/\\\\]MockCommand\.php\(25\)\n
                #0\s.+[\/\\\\]MockCommand\.php\(18\):\sPDepend\MockCommand->getCause\(\)\n
            /x', str_replace("\r", '', $error));
    }

    /**
     * Tests the help output with an optional prolog text.
     *
     * @param string $actual The cli output.
     * @param string $prologText Optional prolog text.
     */
    protected function assertHelpOutput($actual, $prologText = ''): void
    {
        $startsWith = $prologText . $this->versionOutput . $this->usageOutput;
        $startsWith = '/^' . preg_quote($startsWith) . '/';
        $this->assertMatchesRegularExpression($startsWith, $actual);

        $this->assertMatchesRegularExpression('(  --configuration=<file>[ ]+Optional\s+PDepend\s+configuration\s+file\.)', $actual);
        $this->assertMatchesRegularExpression('(  --suffix=<ext\[,\.{3}\]>[ ]+List\s+of\s+valid\s+PHP\s+file\s+extensions\.)', $actual);
        $this->assertMatchesRegularExpression('(  --ignore=<dir\[,\.{3}\]>[ ]+List\s+of\s+exclude\s+directories\.)', $actual);
        $this->assertMatchesRegularExpression('(  --exclude=<pkg\[,\.{3}\]>[ ]+List\s+of\s+exclude\s+namespaces\.)', $actual);
        $this->assertMatchesRegularExpression('(  --without-annotations[ ]+Do\s+not\s+parse\s+doc\s+comment\s+annotations\.)', $actual);
        $this->assertMatchesRegularExpression('(  --help[ ]+Print\s+this\s+help\s+text\.)', $actual);
        $this->assertMatchesRegularExpression('(  --version[ ]+Print\s+the\s+current\s+version\.)', $actual);
        $this->assertMatchesRegularExpression('(  -d key\[=value\][ ]+Sets\s+a\s+php.ini\s+value\.)', $actual);
        $this->assertMatchesRegularExpression('(  --quiet[ ]+Prints\s+errors\s+only\.)', $actual);
    }

    /**
     * Executes the text ui command and returns the exit code and the output as
     * an array <b>array($exitCode, $output)</b>.
     *
     * @param ?array $argv The cli parameters.
     * @return array<mixed>
     */
    private function executeCommand(?array $argv = null)
    {
        $this->prepareArgv($argv);

        ob_start();
        $exitCode = Command::main();
        $output = ob_get_contents();
        ob_end_clean();

        return [$exitCode, $output];
    }

    /**
     * Prepares a fake <b>$argv</b>.
     *
     * @param ?array $argv The cli parameters.
     */
    private function prepareArgv(?array $argv = null): void
    {
        unset($_SERVER['argv']);

        if ($argv !== null) {
            // Add dummy file
            array_unshift($argv, __FILE__);

            // Replace global $argv
            $_SERVER['argv'] = $argv;
        }
    }
}
