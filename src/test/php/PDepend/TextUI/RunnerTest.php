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

/**
 * Test case for the text ui runner.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\TextUI\Runner
 * @group unittest
 */
class RunnerTest extends AbstractTest
{
    /**
     * Tests that the runner exits with an exception for an invalud source
     * directory.
     *
     * @return void
     * @expectedException \RuntimeException
     */
    public function testRunnerThrowsRuntimeExceptionForInvalidSourceDirectory()
    {
        $runner = $this->createTextUiRunner();
        $runner->setSourceArguments(array('foo/bar'));
        $runner->run();
    }

    /**
     * Tests that the runner stops processing if no logger is specified.
     *
     * @return void
     * @expectedException \RuntimeException
     */
    public function testRunnerThrowsRuntimeExceptionIfNoLoggerIsSpecified()
    {
        $runner = $this->createTextUiRunner();
        $runner->setSourceArguments(array(self::createCodeResourceUriForTest()));
        $runner->run();
    }

    /**
     * testRunnerUsesCorrectFileFilter
     *
     * @return void
     */
    public function testRunnerUsesCorrectFileFilter()
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

        $runner = $this->createTextUiRunner();
        $runner->setWithoutAnnotations();
        $runner->setFileExtensions(array('inc'));

        $actual = $this->_runRunnerAndReturnStatistics(
            $runner,
            self::createCodeResourceUriForTest()
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the runner handles the <b>--without-annotations</b> option
     * correct.
     *
     * @return void
     */
    public function testRunnerHandlesWithoutAnnotationsOptionCorrect()
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

        $runner = $this->createTextUiRunner();
        $runner->setWithoutAnnotations();

        $actual = $this->_runRunnerAndReturnStatistics(
            $runner,
            self::createCodeResourceUriForTest()
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testSupportBadDocumentation
     *
     * @return void
     */
    public function testSupportBadDocumentation()
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

        $runner = $this->createTextUiRunner();
        $actual = $this->_runRunnerAndReturnStatistics(
            $runner,
            self::createCodeResourceUriForTest()
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testRunnerHasParseErrorsReturnsFalseForValidSource
     *
     * @return void
     */
    public function testRunnerHasParseErrorsReturnsFalseForValidSource()
    {
        $runner = $this->createTextUiRunner();
        $runner->addReportGenerator('dummy-logger', self::createRunResourceURI());
        $runner->setSourceArguments(array(self::createCodeResourceUriForTest()));

        $this->silentRun($runner);

        $this->assertFalse($runner->hasParseErrors());
    }

    /**
     * testRunnerHasParseErrorsReturnsTrueForInvalidSource
     *
     * @return void
     */
    public function testRunnerHasParseErrorsReturnsTrueForInvalidSource()
    {
        $runner = $this->createTextUiRunner();
        $runner->addReportGenerator('dummy-logger', self::createRunResourceURI());
        $runner->setSourceArguments(array(self::createCodeResourceUriForTest()));

        $this->silentRun($runner);

        $this->assertTrue($runner->hasParseErrors());
    }

    /**
     * testRunnerGetParseErrorsReturnsArrayWithParsingExceptionMessages
     *
     * @return void
     */
    public function testRunnerGetParseErrorsReturnsArrayWithParsingExceptionMessages()
    {
        $runner = $this->createTextUiRunner();
        $runner->addReportGenerator('dummy-logger', self::createRunResourceURI());
        $runner->setSourceArguments(array(self::createCodeResourceUriForTest()));

        ob_start();
        $runner->run();
        ob_end_clean();

        $errors = $runner->getParseErrors();
        $this->assertContains('Unexpected token: }, line: 10, col: 1, file: ', $errors[0]);
    }

    /**
     * testRunnerThrowsExceptionForUndefinedLoggerClass
     *
     * @return void
     * @expectedException \RuntimeException
     */
    public function testRunnerThrowsExceptionForUndefinedLoggerClass()
    {
        $runner = $this->createTextUiRunner();
        $runner->addReportGenerator('FooBarLogger', self::createRunResourceURI());
        $runner->run();
    }

    /**
     * Executes the runner class and returns an array with namespace statistics.
     *
     * @param \PDepend\TextUI\Runner $runner The runner instance.
     * @param $pathName The source path.
     * @return array
     */
    private function _runRunnerAndReturnStatistics(Runner $runner, $pathName)
    {
        $logFile = self::createRunResourceURI();

        $runner->setSourceArguments(array($pathName));
        $runner->addReportGenerator('dummy-logger', $logFile);

        $this->silentRun($runner);

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
}
