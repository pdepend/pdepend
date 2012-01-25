<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';
require_once dirname(__FILE__) . '/../Log/Dummy/Logger.php';

/**
 * Test case for the text ui runner.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage TextUI
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 *
 * @covers PHP_Depend_TextUI_Runner
 * @group pdepend
 * @group pdepend::textui
 * @group unittest
 */
class PHP_Depend_TextUI_RunnerTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the runner exits with an exception for an invalud source
     * directory.
     *
     * @return void
     * @expectedException RuntimeException
     */
    public function testRunnerThrowsRuntimeExceptionForInvalidSourceDirectory()
    {
        $runner = $this->createTextUiRunnerFixture();
        $runner->setSourceArguments(array('foo/bar'));
        $runner->run();
    }

    /**
     * Tests that the runner stops processing if no logger is specified.
     *
     * @return void
     * @expectedException RuntimeException
     */
    public function testRunnerThrowsRuntimeExceptionIfNoLoggerIsSpecified()
    {
        $runner = $this->createTextUiRunnerFixture();
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

        $runner = $this->createTextUiRunnerFixture();
        $runner->setWithoutAnnotations();
        $runner->setFileExtensions(array('inc'));

        $actual = $this->_runRunnerAndReturnStatistics(
            $runner,
            self::createCodeResourceUriForTest()
        );

        self::assertEquals($expected, $actual);
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

        $runner = $this->createTextUiRunnerFixture();
        $runner->setWithoutAnnotations();

        $actual = $this->_runRunnerAndReturnStatistics(
            $runner, self::createCodeResourceUriForTest()
        );

        self::assertEquals($expected, $actual);
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

        $runner = $this->createTextUiRunnerFixture();
        $actual = $this->_runRunnerAndReturnStatistics(
            $runner, self::createCodeResourceUriForTest()
        );

        self::assertEquals($expected, $actual);
    }

    /**
     * testRunnerHasParseErrorsReturnsFalseForValidSource
     *
     * @return void
     */
    public function testRunnerHasParseErrorsReturnsFalseForValidSource()
    {
        $runner = $this->createTextUiRunnerFixture();
        $runner->addLogger('dummy-logger', self::createRunResourceURI());
        $runner->setSourceArguments(array(self::createCodeResourceUriForTest()));
        $runner->run();

        self::assertFalse($runner->hasParseErrors());
    }

    /**
     * testRunnerHasParseErrorsReturnsTrueForInvalidSource
     *
     * @return void
     */
    public function testRunnerHasParseErrorsReturnsTrueForInvalidSource()
    {
        $runner = $this->createTextUiRunnerFixture();
        $runner->addLogger('dummy-logger', self::createRunResourceURI());
        $runner->setSourceArguments(array(self::createCodeResourceUriForTest()));
        $runner->run();

        self::assertTrue($runner->hasParseErrors());
    }

    /**
     * testRunnerGetParseErrorsReturnsArrayWithParsingExceptionMessages
     *
     * @return void
     */
    public function testRunnerGetParseErrorsReturnsArrayWithParsingExceptionMessages()
    {
        $runner = $this->createTextUiRunnerFixture();
        $runner->addLogger('dummy-logger', self::createRunResourceURI());
        $runner->setSourceArguments(array(self::createCodeResourceUriForTest()));
        $runner->run();

        $errors = $runner->getParseErrors();
        self::assertContains('Unexpected token: }, line: 10, col: 1, file: ', $errors[0]);
    }

    /**
     * testRunnerThrowsExceptionForUndefinedLoggerClass
     *
     * @return void
     * @expectedException RuntimeException
     */
    public function testRunnerThrowsExceptionForUndefinedLoggerClass()
    {
        $runner = $this->createTextUiRunnerFixture();
        $runner->addLogger('FooBarLogger', self::createRunResourceURI());
        $runner->run();
    }

    /**
     * Creates a test fixture of the {@link PHP_Depend_TextUI_Runner} class.
     *
     * @return PHP_Depend_TextUI_Runner
     * @since 0.10.0
     */
    protected function createTextUiRunnerFixture()
    {
        $fixture = new PHP_Depend_TextUI_Runner();
        $fixture->setConfiguration($this->createConfigurationFixture());

        return $fixture;
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
        $logFile = self::createRunResourceURI();

        $runner->setSourceArguments(array($pathName));
        $runner->addLogger('dummy-logger', $logFile);

        ob_start();
        $runner->run();
        ob_end_clean();

        $data = unserialize(file_get_contents($logFile));
        $code = $data['code'];

        $actual = array();
        foreach ($code as $package) {
            $statistics = array(
                'functions'   =>  array(),
                'classes'     =>  array(),
                'interfaces'  =>  array(),
                'exceptions'  =>  array()
            );
            foreach ($package->getFunctions() as $function) {
                $statistics['functions'][] = $function->getName();
                foreach ($function->getExceptionClasses() as $exception) {
                    $statistics['exceptions'][] = $exception->getName();
                }
            }

            foreach ($package->getClasses() as $class) {
                $statistics['classes'][] = $class->getName();
            }

            foreach ($package->getInterfaces() as $interface) {
                $statistics['interfaces'][] = $interface->getName();
            }

            sort($statistics['functions']);
            sort($statistics['classes']);
            sort($statistics['interfaces']);
            sort($statistics['exceptions']);

            $actual[$package->getName()] = $statistics;
        }
        ksort($actual);

        return $actual;
    }
}
