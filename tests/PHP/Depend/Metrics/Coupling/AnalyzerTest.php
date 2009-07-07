<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/Depend/Token.php';
require_once 'PHP/Depend/ConstantsI.php';
require_once 'PHP/Depend/Metrics/Coupling/Analyzer.php';

/**
 * Test case for the coupling analyzer.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Metrics_Coupling_AnalyzerTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * functions.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectFunctionCoupling()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();
        
        $this->assertEquals(2, $package->getFunctions()->count());

        $analyzer = new PHP_Depend_Metrics_Coupling_Analyzer();
        $analyzer->analyze($packages);

        $project = $analyzer->getProjectMetrics();

        $this->assertArrayHasKey('fanout', $project);
        $this->assertEquals(7, $project['fanout']);

        $this->assertArrayHasKey('calls', $project);
        $this->assertEquals(10, $project['calls']);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * methods.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectMethodCoupling()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();
        
        $this->assertEquals(1, $package->getClasses()->count());
        $this->assertEquals(1, $package->getInterfaces()->count());

        $analyzer = new PHP_Depend_Metrics_Coupling_Analyzer();
        $analyzer->analyze($packages);

        $project = $analyzer->getProjectMetrics();

        $this->assertArrayHasKey('fanout', $project);
        $this->assertEquals(9, $project['fanout']);

        $this->assertArrayHasKey('calls', $project);
        $this->assertEquals(10, $project['calls']);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * properties.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectPropertyCoupling()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();
        
        $this->assertSame('default\package', $package->getName());
        $this->assertSame(1, $package->getClasses()->count());

        $analyzer = new PHP_Depend_Metrics_Coupling_Analyzer();
        $analyzer->analyze($packages);

        $project = $analyzer->getProjectMetrics();

        $this->assertArrayHasKey('fanout', $project);
        $this->assertEquals(3, $project['fanout']);

        $this->assertArrayHasKey('calls', $project);
        $this->assertEquals(0, $project['calls']);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * properties.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectClassCoupling()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $this->assertEquals(1, $package->getClasses()->count());
        $this->assertEquals(1, $package->getInterfaces()->count());

        $analyzer = new PHP_Depend_Metrics_Coupling_Analyzer();
        $analyzer->analyze($packages);

        $project = $analyzer->getProjectMetrics();

        $this->assertArrayHasKey('fanout', $project);
        $this->assertEquals(12, $project['fanout']);

        $this->assertArrayHasKey('calls', $project);
        $this->assertEquals(10, $project['calls']);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * complete source.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectCoupling()
    {
        $packages = self::parseSource('metrics/coupling');
        $package = $packages->current();

        $this->assertEquals(3, $package->getClasses()->count());
        $this->assertEquals(2, $package->getInterfaces()->count());
        $this->assertEquals(2, $package->getFunctions()->count());

        $analyzer = new PHP_Depend_Metrics_Coupling_Analyzer();
        $analyzer->analyze($packages);

        $project = $analyzer->getProjectMetrics();

        $this->assertArrayHasKey('fanout', $project);
        $this->assertEquals(31, $project['fanout']);

        $this->assertArrayHasKey('calls', $project);
        $this->assertEquals(30, $project['calls']);
    }

    /**
     * Tests that the analyzer calculates the expected call count.
     *
     * @param array(PHP_Depend_Token) $tokens The method/function tokens.
     * @param integer                 $calls  Number of expected calls.
     *
     * @return void
     * @dataProvider dataProviderAnalyzerCalculatesExpectedCallCount
     */
    public function testAnalyzerCalculatesExpectedCallCount(array $tokens, $calls)
    {
        $function = $this->getMock('PHP_Depend_Code_Function', array(), array(null));
        $function->expects($this->atLeastOnce())
            ->method('getTokens')
            ->will($this->returnValue($tokens));
        $function->expects($this->atLeastOnce())
            ->method('getExceptionClasses')
            ->will($this->returnValue(array()));
        $function->expects($this->atLeastOnce())
            ->method('getDependencies')
            ->will($this->returnValue(array()));

        $analyzer = new PHP_Depend_Metrics_Coupling_Analyzer();
        $analyzer->analyze($this->getMock('PHP_Depend_Code_NodeIterator', array(), array(array())));
        $analyzer->visitFunction($function);

        $project = $analyzer->getProjectMetrics();
        $this->assertSame($calls, $project['calls']);
    }

    /**
     * Test case for the execution chain bug 14.
     *
     * http://bugs.xplib.de/index.php?do=details&task_id=14&project=3
     *
     * @return void
     */
    public function testAnalyzerExecutionChainBug14()
    {
        $source   = dirname(__FILE__) . '/../../_code/bugs/014.php';
        $packages = self::parseSource($source);

        $this->assertEquals(1, $packages->count());
        $this->assertEquals(1, $packages->current()->getFunctions()->count());

        $analyzer = new PHP_Depend_Metrics_Coupling_Analyzer();
        $analyzer->analyze($packages);

        $project = $analyzer->getProjectMetrics();

        $this->assertArrayHasKey('calls', $project);
        $this->assertEquals(3, $project['calls']);
    }

    /**
     * Data provider that returns different token array that can be used as
     * function or method tokens.
     *
     * @return array
     */
    public static function dataProviderAnalyzerCalculatesExpectedCallCount()
    {
        return array(
            array(
                array(), 0
            ),
            array(
                array(
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0, 0, 0),
                ),
                0
            ),
            array(
                array(
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'foo', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0, 0, 0),
                ),
                0
            ),
            array(
                array(
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$foo', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0, 0, 0),
                ),
                0
            ),
            array(
                array(
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'foo', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0, 0, 0),
                ),
                1
            ),
            array(
                array(
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'foo', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'foo', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0, 0, 0),
                ),
                1
            ),
            array(
                array(
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'foo', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'bar', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0, 0, 0),
                ),
                2
            ),
            array(
                array(
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$foo', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0, 0, 0),
                ),
                1
            ),
            array(
                array(
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$foo', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_OBJECT_OPERATOR, '->', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'bar', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0, 0, 0),
                ),
                1
            ),
            array(
                array(
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$foo', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_OBJECT_OPERATOR, '->', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$bar', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0, 0, 0),
                ),
                1
            ),
            array(
                array(
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$foo', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_OBJECT_OPERATOR, '->', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'bar', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_OBJECT_OPERATOR, '->', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'baz', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0, 0, 0),
                ),
                2
            ),
            array(
                array(
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$foo', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_OBJECT_OPERATOR, '->', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'bar', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_OBJECT_OPERATOR, '->', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'bar', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0, 0, 0),
                ),
                2
            ),
            array(
                array(
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'foo', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_DOUBLE_COLON, '::', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'bar', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0, 0, 0),
                ),
                1
            ),
            array(
                array(
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'foo', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_DOUBLE_COLON, '::', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'bar', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_OBJECT_OPERATOR, '->', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'bar', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0, 0, 0),
                    new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0, 0, 0),
                ),
                2
            ),
        );
    }
}