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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Issues
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Depend.php';
require_once 'PHP/Depend/Input/ExtensionFilter.php';
require_once 'PHP/Depend/Log/LoggerI.php';
require_once 'PHP/Depend/TextUI/Command.php';
require_once 'PHP/Depend/TextUI/Runner.php';

/**
 * Test case for the catch error ticket #61.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Issues
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Issues_PHPDependCatchesParsingErrorsIssue061Test
    extends PHP_Depend_AbstractTest
{
    /**
     * List of expected parsing exceptions.
     *
     * @var array(string) $expectedExceptions
     */
    protected $expectedExceptions = array();

    /**
     * Initializes a list of expected exception messages.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->expectedExceptions = array(
            sprintf(
                'Unexpected token: function, line: 7, col: 41, file: %s.',
                self::createCodeResourceURI(
                    'issues/061/UnexpectedTokenInParameterDefaultValue.php'
                )
            )
        );
    }

    /**
     * Tests that the {@link PHP_Depend::getExceptions()} Returns a list with
     * the expected exceptions.
     *
     * @return void
     * @covers PHP_Depend
     * @group pdepend
     * @group pdepend::issues
     * @group unittest
     */
    public function testPHPDependReturnsExpectedExceptionInstances()
    {
        $logger = $this->getMock('PHP_Depend_Log_LoggerI');
        $logger->expects($this->atLeastOnce())
            ->method('getAcceptedAnalyzers')
            ->will($this->returnValue(array()));

        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(self::createCodeResourceURI('issues/061'));
        $pdepend->addFileFilter(new PHP_Depend_Input_ExtensionFilter(array('php')));
        $pdepend->addLogger($logger);
        
        $pdepend->analyze();

        $exceptions = $pdepend->getExceptions();
        $this->assertEquals(count($this->expectedExceptions), count($exceptions));

        foreach ($exceptions as $exception) {
            $this->assertTrue(
                in_array(
                    $exception->getMessage(),
                    $this->expectedExceptions
                ),
                "Unexpected exception with message: " . $exception->getMessage()
            );
        }
    }

    /**
     * Tests that the {@link PHP_Depend_TextUI_Runner::hasErrors()} method will
     * return <b>false</b> when not parsing error occured.
     *
     * @return void
     * @covers PHP_Depend_TextUI_Runner
     * @group pdepend
     * @group pdepend::issues
     * @group pdepend::textui
     * @group unittest
     */
    public function testRunnerReturnsFalseWhenNoErrorOccuredDuringTheParsingProcess()
    {
        $runner = new PHP_Depend_TextUI_Runner();
        $runner->addLogger('dummy-logger', self::createRunResourceURI('pdepend.log'));
        $runner->setSourceArguments(
            array(
                self::createCodeResourceURI('issues/061/ValidFileUnderTest.php')
            )
        );
        $runner->run();

        $this->assertFalse($runner->hasParseErrors());
    }

    /**
     * Tests that the {@link PHP_Depend_TextUI_Runner::hasErrors()} method will
     * return <b>true</b> when a parsing error occured.
     *
     * @return void
     * @covers PHP_Depend_TextUI_Runner
     * @group pdepend
     * @group pdepend::issues
     * @group pdepend::textui
     * @group unittest
     */
    public function testRunnerReturnsTrueWhenAnErrorOccuredDuringTheParsingProcess()
    {
        $runner = new PHP_Depend_TextUI_Runner();
        $runner->addLogger('dummy-logger', self::createRunResourceURI('pdepend.log'));
        $runner->setSourceArguments(
            array(
                self::createCodeResourceURI('issues/061')
            )
        );
        $runner->run();

        $this->assertTrue($runner->hasParseErrors());
    }

    /**
     * Tests that the output does not contain the error hint when the parsing
     * process was successful.
     *
     * @return void
     * @covers PHP_Depend_TextUI_Command
     * @group pdepend
     * @group pdepend::issues
     * @group pdepend::textui
     * @group unittest
     */
    public function testCommandDoesNotPrintErrorOutputOnSuccessfulParsingProcess()
    {
        $this->prepareArgv(
            array(
                '--dummy-logger=' . self::createRunResourceURI('pdepend.log'),
                self::createCodeResourceURI('issues/061/ValidFileUnderTest.php')
            )
        );

        list($exitCode, $output) = $this->runTextUICommand();

        $this->assertNotContains('Following errors occured:', $output);
    }

    /**
     * Tests that the output of {@link PHP_Depend_TextUI_Command} contains the
     * expected exception messages.
     *
     * @return void
     * @covers PHP_Depend_TextUI_Command
     * @group pdepend
     * @group pdepend::issues
     * @group pdepend::textui
     * @group unittest
     */
    public function testCommandPrintsExpectedOutputWhenAnErrorOccuredDuringTheParsingProcess()
    {
        $this->prepareArgv(
            array(
                '--dummy-logger=' . self::createRunResourceURI('pdepend.log'),
                self::createCodeResourceURI('issues/061')
            )
        );
        
        list($exitCode, $output) = $this->runTextUICommand();

        $this->assertContains('Following errors occured:', $output);
        foreach ($this->expectedExceptions as $expectedException) {
            $this->assertContains($expectedException, $output);
        }
    }

    /**
     * Sets a command line argument vector.
     *
     * @param array(string) $argv The temporary command line argument vector
     *
     * @return void
     */
    protected function prepareArgv($argv)
    {
        array_unshift($argv, __FILE__);

        $_SERVER['argv'] = $argv;
    }

    /**
     * Executes PHP_Depend's text ui command and returns the exit code and shell
     * output.
     *
     * @return array
     */
    protected function runTextUICommand()
    {
        $command = new PHP_Depend_TextUI_Command();

        ob_start();
        $exitCode = $command->run();
        $output   = ob_get_clean();

        return array($exitCode, $output);
    }
}
