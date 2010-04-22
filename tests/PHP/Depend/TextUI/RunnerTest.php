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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage TextUI
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';
require_once dirname(__FILE__) . '/../Log/Dummy/Logger.php';

require_once 'PHP/Depend/TextUI/Runner.php';

/**
 * Test case for the text ui runner.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage TextUI
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_TextUI_RunnerTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the runner exits with an exception for an invalud source
     * directory.
     *
     * @return void
     * @covers PHP_Depend_TextUI_Runner
     * @group pdepend
     * @group pdepend::textui
     * @group unittest
     */
    public function testRunnerThrowsRuntimeExceptionForInvalidSourceDirectory()
    {
        $runner = new PHP_Depend_TextUI_Runner();
        $runner->setSourceArguments(array('foo/bar'));

        $this->setExpectedException(
            'RuntimeException',
            "Invalid directory 'foo/bar' added.",
            PHP_Depend_TextUI_Runner::EXCEPTION_EXIT
        );

        $runner->run();
    }

    /**
     * Tests that the runner stops processing if no logger is specified.
     *
     * @return void
     * @covers PHP_Depend_TextUI_Runner
     * @group pdepend
     * @group pdepend::textui
     * @group unittest
     */
    public function testRunnerThrowsRuntimeExceptionIfNoLoggerIsSpecified()
    {
        $runner = new PHP_Depend_TextUI_Runner();
        $runner->setSourceArguments(array(dirname(__FILE__). '/../_code/code-without-comments'));

        $this->setExpectedException(
            'RuntimeException',
            'No output specified',
            PHP_Depend_TextUI_Runner::EXCEPTION_EXIT
        );

        $runner->run();
    }

    /**
     * testRunnerUsesCorrectFileFilter
     *
     * @return void
     * @covers PHP_Depend_TextUI_Runner
     * @group pdepend
     * @group pdepend::textui
     * @group unittest
     */
    public function testRunnerUsesCorrectFileFilter()
    {
        $expected = array(
            'pdepend.test'  =>  array(
                'functions'   =>  1,
                'classes'     =>  1,
                'interfaces'  =>  0,
                'exceptions'  =>  0
            ),
            'pdepend.test2'  =>  array(
                'functions'   =>  0,
                'classes'     =>  1,
                'interfaces'  =>  0,
                'exceptions'  =>  0
            )
        );

        $runner = new PHP_Depend_TextUI_Runner();
        $runner->setWithoutAnnotations();
        $runner->setFileExtensions(array('inc'));

        $actual = $this->_runRunnerAndReturnStatistics(
            $runner,
            self::createCodeResourceURI('textui/Runner/' . __FUNCTION__)
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the runner handles the <b>--without-annotations</b> option
     * correct.
     *
     * @return void
     * @covers PHP_Depend_TextUI_Runner
     * @group pdepend
     * @group pdepend::textui
     * @group unittest
     */
    public function testRunnerHandlesWithoutAnnotationsOptionCorrect()
    {
        $expected = array(
            'pdepend.test'  =>  array(
                'functions'   =>  1,
                'classes'     =>  1,
                'interfaces'  =>  0,
                'exceptions'  =>  0
            ),
            'pdepend.test2'  =>  array(
                'functions'   =>  0,
                'classes'     =>  1,
                'interfaces'  =>  0,
                'exceptions'  =>  0
            )
        );

        $runner = new PHP_Depend_TextUI_Runner();
        $runner->setWithoutAnnotations();

        $actual = $this->_runRunnerAndReturnStatistics(
            $runner,
            self::createCodeResourceURI('function.inc')
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testSupportBadDocumentation
     *
     * @return void
     * @covers PHP_Depend_TextUI_Runner
     * @group pdepend
     * @group pdepend::textui
     * @group unittest
     */
    public function testSupportBadDocumentation()
    {
        $expected = array(
            '+global'  =>  array(
                'functions'   =>  1,
                'classes'     =>  7,
                'interfaces'  =>  3,
                'exceptions'  =>  0
            )
        );
        
        $runner = new PHP_Depend_TextUI_Runner();
        $actual = $this->_runRunnerAndReturnStatistics(
            $runner,
            self::createCodeResourceURI('code-without-comments')
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testRunnerHasParseErrorsReturnsFalseForValidSource
     *
     * @return void
     * @covers PHP_Depend_TextUI_Runner
     * @group pdepend
     * @group pdepend::textui
     * @group unittest
     */
    public function testRunnerHasParseErrorsReturnsFalseForValidSource()
    {
        $runner = new PHP_Depend_TextUI_Runner();
        $runner->addLogger('dummy-logger', self::createRunResourceURI('pdepend.dummy'));
        $runner->setSourceArguments(array(self::createCodeResourceURI('textui/Runner/' . __FUNCTION__ . '.php')));
        $runner->run();

        $this->assertFalse($runner->hasParseErrors());
    }

    /**
     * testRunnerHasParseErrorsReturnsTrueForInvalidSource
     *
     * @return void
     * @covers PHP_Depend_TextUI_Runner
     * @group pdepend
     * @group pdepend::textui
     * @group unittest
     */
    public function testRunnerHasParseErrorsReturnsTrueForInvalidSource()
    {
        $runner = new PHP_Depend_TextUI_Runner();
        $runner->addLogger('dummy-logger', self::createRunResourceURI('pdepend.dummy'));
        $runner->setSourceArguments(array(self::createCodeResourceURI('textui/Runner/' . __FUNCTION__ . '.php')));

        try {
            $runner->run();
        } catch (Exception $e) {}

        $this->assertTrue($runner->hasParseErrors());
    }

    /**
     * testRunnerGetParseErrorsReturnsArrayWithParsingExceptionMessages
     *
     * @return void
     * @covers PHP_Depend_TextUI_Runner
     * @group pdepend
     * @group pdepend::textui
     * @group unittest
     */
    public function testRunnerGetParseErrorsReturnsArrayWithParsingExceptionMessages()
    {
        $runner = new PHP_Depend_TextUI_Runner();
        $runner->addLogger('dummy-logger', self::createRunResourceURI('pdepend.dummy'));
        $runner->setSourceArguments(array(self::createCodeResourceURI('textui/Runner/' . __FUNCTION__ . '.php')));

        try {
            $runner->run();
        } catch (Exception $e) {}

        $errors = $runner->getParseErrors();
        $this->assertContains('Unexpected token: }, line: 10, col: 1, file: ', $errors[0]);

        $this->assertTrue($runner->hasParseErrors());
    }

    /**
     * testRunnerThrowsExceptionForUndefinedLoggerClass
     *
     * @return void
     * @covers PHP_Depend_TextUI_Runner
     * @group pdepend
     * @group pdepend::textui
     * @group unittest
     * @expectedException RuntimeException
     */
    public function testRunnerThrowsExceptionForUndefinedLoggerClass()
    {
        $runner = new PHP_Depend_TextUI_Runner();
        $runner->addLogger('FooBarLogger', self::createRunResourceURI('log.xml'));
        $runner->run();
    }

    /**
     * Executes the runner class and returns an array with package statistics.
     *
     * @param array  PHP_Depend_TextUI_Runner $runner   The runner instance.
     * @param string                          $pathName The source path.
     *
     * @return array
     */
    private function _runRunnerAndReturnStatistics(PHP_Depend_TextUI_Runner $runner, $pathName)
    {
        $logFile = self::createRunResourceURI('pdepend.dummy');

        $runner->setSourceArguments(array($pathName));
        $runner->addLogger('dummy-logger', $logFile);

        ob_start();
        $runner->run();
        ob_end_clean();

        $data = unserialize(file_get_contents($logFile));
        $code = $data['code'];

        $actual = array();
        foreach ($code as $package) {
            $exceptions = 0;
            foreach ($package->getFunctions() as $function) {
                $exceptions += $function->getExceptionClasses()->count();
            }

            $actual[$package->getName()] = array(
                'functions'   =>  $package->getFunctions()->count(),
                'classes'     =>  $package->getClasses()->count(),
                'interfaces'  =>  $package->getInterfaces()->count(),
                'exceptions'  =>  $exceptions
            );
        }
        ksort($actual);

        return $actual;
    }
}
