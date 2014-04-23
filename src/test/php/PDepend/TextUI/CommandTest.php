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
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\TextUI;

use PDepend\AbstractTest;
use PDepend\Util\ConfigurationInstance;
use PHPUnit_Framework_TestCase;

/**
 * Test case for the text ui command.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\TextUI\Command
 * @group unittest
 */
class CommandTest extends AbstractTest
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
    private $usageOutput = "Usage: pdepend [options] [logger] <dir[,dir[,...]]>\n\n";

    protected function setUp()
    {
        parent::setUp();

        $data = json_decode(file_get_contents(__DIR__ . '/../../../../../composer.json'));

        $this->versionOutput = sprintf("PDepend %s\n\n", $data->version);
    }

    /**
     * Tests the result of the print version option.
     *
     * @return void
     */
    public function testPrintVersion()
    {
        list(, $actual) = $this->executeCommand(array('--version'));
        $this->assertEquals($this->versionOutput, $actual);
    }

    /**
     * testPrintVersionReturnsExitCodeSuccess
     *
     * @return void
     */
    public function testPrintVersionReturnsExitCodeSuccess()
    {
        list($exitCode, ) = $this->executeCommand(array('--version'));
        $this->assertEquals(Runner::SUCCESS_EXIT, $exitCode);
    }

    /**
     * Tests the result of the print usage option.
     *
     * @return void
     */
    public function testPrintUsage()
    {
        list(, $actual) = $this->executeCommand(array('--usage'));
        $this->assertEquals($this->versionOutput . $this->usageOutput, $actual);
    }

    /**
     * testPrintUsageReturnsExitCodeSuccess
     *
     * @return void
     */
    public function testPrintUsageReturnsExitCodeSuccess()
    {
        list($exitCode, ) = $this->executeCommand(array('--usage'));
        $this->assertEquals(Runner::SUCCESS_EXIT, $exitCode);
    }

    /**
     * Tests the output of the print help option.
     *
     * @return void
     */
    public function testPrintHelp()
    {
        list(, $actual) = $this->executeCommand(array('--help'));
        $this->assertHelpOutput($actual);
    }

    /**
     * testPrintHelpReturnsExitCodeSuccess
     *
     * @return void
     */
    public function testPrintHelpReturnsExitCodeSuccess()
    {
        list($exitCode, ) = $this->executeCommand(array('--help'));
        $this->assertEquals(Runner::SUCCESS_EXIT, $exitCode);
    }

    /**
     * Tests that the command exits with an cli error if no $argv array exists.
     *
     * @return void
     */
    public function testCommandCliReturnsErrorExitCodeIfNoArgvArrayExists()
    {
        list($exitCode, ) = $this->executeCommand();
        $this->assertEquals(Command::CLI_ERROR, $exitCode);
    }

    /**
     * testCommandCliErrorMessageIfNoArgvArrayExists
     *
     * @return void
     */
    public function testCommandCliErrorMessageIfNoArgvArrayExists()
    {
        list(, $actual) = $this->executeCommand();
        $startsWith = 'Unknown error, no $argv array available.' . PHP_EOL . PHP_EOL;
        $this->assertHelpOutput($actual, $startsWith);
    }

    /**
     * Tests that the command exits with a cli error for an empty option list.
     *
     * @return void
     */
    public function testCommandDisplaysHelpIfNoOptionsWereSpecified()
    {
        list(, $actual) = $this->executeCommand(array());
        $this->assertHelpOutput($actual);
    }

    /**
     * testCommandReturnsErrorExitCodeIfNoOptionsWereSpecified
     *
     * @return void
     */
    public function testCommandReturnsErrorExitCodeIfNoOptionsWereSpecified()
    {
        list($exitCode, ) = $this->executeCommand(array());
        $this->assertEquals(Command::CLI_ERROR, $exitCode);
    }

    /**
     * Tests that the command starts the text ui runner.
     *
     * @return void
     */
    public function testCommandStartsProcessWithDummyLogger()
    {
        $logFile  = self::createRunResourceURI();
        $resource = self::createCodeResourceUriForTest();

        set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

        $argv = array(
            '--suffix=inc',
            '--ignore=code-5.2.x',
            '--exclude=pdepend.test2',
            '--configuration=' . __DIR__ . '/../../../resources/pdepend.xml.dist',
            '--dummy-logger=' . $logFile,
            $resource
        );

        list($exitCode, ) = $this->executeCommand($argv);

        $this->assertEquals(Runner::SUCCESS_EXIT, $exitCode);
        $this->assertFileExists($logFile);
    }

    /**
     * testCommandReturnsExitCodeSuccessByDefault
     *
     * @return void
     */
    public function testCommandReturnsExitCodeSuccessByDefault()
    {
        $logFile  = self::createRunResourceURI();
        $resource = self::createCodeResourceUriForTest();

        set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

        $argv = array(
            '--suffix=inc',
            '--configuration=' . __DIR__ . '/../../../resources/pdepend.xml.dist',
            '--dummy-logger=' . $logFile,
            $resource
        );

        list($exitCode, ) = $this->executeCommand($argv);
        $this->assertEquals(Runner::SUCCESS_EXIT, $exitCode);
    }

    /**
     * Tests that the command exits with a cli error for an unknown option.
     *
     * @return void
     */
    public function testCommandExitsWithCliErrorForUnknownOption()
    {
        list($exitCode, ) = $this->executeCommand(array('--unknown'));
        $this->assertEquals(Command::CLI_ERROR, $exitCode);
    }

    /**
     * Tests that the command handles the <b>--without-annotations</b> option
     * correct.
     *
     * @return void
     */
    public function testCommandHandlesWithoutAnnotationsOptionCorrect()
    {
        $expected = array(
            'pdepend.test'  =>  array(
                'functions'   =>  array('foo'),
                'classes'     =>  array('MyException'),
                'interfaces'  =>  array(),
                'exceptions'  =>  array()
            ),
            'pdepend.test2'  =>  array(
                'functions'   =>  array(),
                'classes'     =>  array('YourException'),
                'interfaces'  =>  array(),
                'exceptions'  =>  array()
            )
        );

        $actual = $this->runCommandAndReturnStatistics(
            array(
                '--suffix=inc',
                '--without-annotations',
                '--coderank-mode=property'
            ),
            self::createCodeResourceUriForTest()
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testCommandHandlesBadDocumentedSourceCode
     *
     * @return void
     */
    public function testCommandHandlesBadDocumentedSourceCode()
    {
        $expected = array(
            '+global'  =>  array(
                'functions'   =>  array('pkg3_foo'),
                'classes'     =>  array(
                    'Bar',
                    'pkg1Bar',
                    'pkg1Barfoo',
                    'pkg1Foo',
                    'pkg1Foobar',
                    'pkg2Bar',
                    'pkg2Barfoo',
                    'pkg2Foobar',
                ),
                'interfaces'  =>  array(
                    'pkg1FooI',
                    'pkg2FooI',
                    'pkg3FooI'
                ),
                'exceptions'  =>  array()
            )
        );

        $actual = $this->runCommandAndReturnStatistics(
            array(),
            self::createCodeResourceUriForTest()
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * Executes the command class and returns an array with namespace statistics.
     *
     * @param array $argv
     * @param string $pathName
     * @return array
     */
    private function runCommandAndReturnStatistics(array $argv, $pathName)
    {
        $logFile = self::createRunResourceURI();

        $argv[] = '--dummy-logger=' . $logFile;
        $argv[] = '--configuration=' . __DIR__ . '/../../../resources/pdepend.xml.dist';
        $argv[] = $pathName;

        if (file_exists($logFile)) {
            unlink($logFile);
        }

        $this->executeCommand($argv);

        $data = unserialize(file_get_contents($logFile));
        $code = $data['code'];

        $actual = array();
        foreach ($code as $namespace) {
            $statistics = array(
                'functions'   =>  array(),
                'classes'     =>  array(),
                'interfaces'  =>  array(),
                'exceptions'  =>  array()
            );
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
     *
     * @return void
     */
    public function testCommandHandlesIniOptionWithoutValueToON()
    {
        // Get backup
        if (($backup = ini_set('html_errors', 'off')) === false) {
            $this->markTestSkipped('Cannot alter ini setting "html_errors".');
        }

        $this->executeCommand(
            array(
                '-d',
                'html_errors',
                '--dummy-logger=' . self::createRunResourceURI(),
                __FILE__
            )
        );

        $this->assertEquals('on', ini_get('html_errors'));

        ini_set('html_errors', $backup);
    }

    /**
     * Tests that the text ui command handles an ini option "-d key=value" correct.
     *
     * @return void
     */
    public function testCommandHandlesIniOptionWithValue()
    {
        // Get backup
        if (($backup = ini_set('html_errors', 'on')) === false) {
            $this->markTestSkipped('Cannot alter ini setting "html_errors".');
        }

        $this->executeCommand(
            array(
                '-d',
                'html_errors=off',
                '--dummy-logger=' . self::createRunResourceURI(),
                __FILE__
            )
        );

        $this->assertEquals('off', ini_get('html_errors'));

        ini_set('html_errors', $backup);
    }

    /**
     * Tests that the command sets a configuration instance for a specified
     * config file.
     *
     * @return void
     */
    public function testCommandHandlesConfigurationFileCorrect()
    {
        // Sample config file
        $configFile = self::createRunResourceURI('config.xml');
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

        $argv = array(
            '--configuration=' . $configFile,
            '--dummy-logger=' . self::createRunResourceURI(),
            __FILE__
        );

        // Result previous instance
        ConfigurationInstance::set(null);

        $this->executeCommand($argv);

        $config = ConfigurationInstance::get();
        $this->assertEquals('memory', $config->cache->driver);
    }

    /**
     * testTextUiCommandOutputContainsExpectedCoverageReportOption
     *
     * @return void
     */
    public function testTextUiCommandOutputContainsExpectedCoverageReportOption()
    {
        list(, $actual) = $this->executeCommand(array());
        $this->assertContains('--coverage-report=<file>', $actual);
    }

    /**
     * testTextUiCommandFailesWithExpectedErrorCodeWhenCoverageReportFileDoesNotExist
     *
     * @return void
     */
    public function testTextUiCommandFailesWithExpectedErrorCodeWhenCoverageReportFileDoesNotExist()
    {
        $argv = array(
            '--coverage-report=' . self::createRunResourceURI('foobar'),
            '--dummy-logger=' . self::createRunResourceURI(),
            __FILE__,
        );

        list($exitCode, ) = $this->executeCommand($argv);

        $this->assertEquals(Command::INPUT_ERROR, $exitCode);
    }

    /**
     * testTextUiCommandAcceptsExistingFileForCoverageReportOption
     *
     * @return void
     */
    public function testTextUiCommandAcceptsExistingFileForCoverageReportOption()
    {
        $argv = array(
            '--coverage-report=' . dirname(__FILE__) . '/_files/clover.xml',
            '--dummy-logger=' . self::createRunResourceURI(),
            '--configuration=' . __DIR__ . '/../../../resources/pdepend.xml.dist',
            __FILE__,
        );

        list($exitCode, ) = $this->executeCommand($argv);

        $this->assertEquals(Runner::SUCCESS_EXIT, $exitCode);
    }

    /**
     * Tests that the command fails for an invalid config file.
     *
     * @return void
     */
    public function testCommandFailsIfAnInvalidConfigFileWasSpecified()
    {
        $configFile = self::createRunResourceURI('config.xml');

        $argv = array('--configuration=' . $configFile, __FILE__);

        list($exitCode, $actual) = $this->executeCommand($argv);

        $this->assertSame(Command::CLI_ERROR, $exitCode);
        $this->assertContains(
            sprintf('The configuration file "%s" doesn\'t exist.', $configFile),
            $actual
        );
    }

    /**
     * Tests the help output with an optional prolog text.
     *
     * @param string $actual     The cli output.
     * @param string $prologText Optional prolog text.
     *
     * @return void
     */
    protected function assertHelpOutput($actual, $prologText = '')
    {
        $startsWith = $prologText . $this->versionOutput . $this->usageOutput;
        $startsWith = '/^' . preg_quote($startsWith) . '/';
        $this->assertRegExp($startsWith, $actual);

        $this->assertRegExp('(  --configuration=<file>[ ]+Optional\s+PDepend\s+configuration\s+file\.)', $actual);
        $this->assertRegExp('(  --suffix=<ext\[,\.{3}\]>[ ]+List\s+of\s+valid\s+PHP\s+file\s+extensions\.)', $actual);
        $this->assertRegExp('(  --ignore=<dir\[,\.{3}\]>[ ]+List\s+of\s+exclude\s+directories\.)', $actual);
        $this->assertRegExp('(  --exclude=<pkg\[,\.{3}\]>[ ]+List\s+of\s+exclude\s+namespaces\.)', $actual);
        $this->assertRegExp('(  --without-annotations[ ]+Do\s+not\s+parse\s+doc\s+comment\s+annotations\.)', $actual);
        $this->assertRegExp('(  --help[ ]+Print\s+this\s+help\s+text\.)', $actual);
        $this->assertRegExp('(  --version[ ]+Print\s+the\s+current\s+version\.)', $actual);
        $this->assertRegExp('(  -d key\[=value\][ ]+Sets\s+a\s+php.ini\s+value\.)', $actual);
    }

    /**
     * Executes the text ui command and returns the exit code and the output as
     * an array <b>array($exitCode, $output)</b>.
     *
     * @param array $argv The cli parameters.
     *
     * @return array(mixed)
     */
    private function executeCommand(array $argv = null)
    {
        $this->prepareArgv($argv);

        ob_start();
        $command = new Command();
        $exitCode = $command->run();
        $output   = ob_get_contents();
        ob_end_clean();

        return array($exitCode, $output);
    }

    /**
     * Prepares a fake <b>$argv</b>.
     *
     * @param array $argv The cli parameters.
     *
     * @return void
     */
    private function prepareArgv(array $argv = null)
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
