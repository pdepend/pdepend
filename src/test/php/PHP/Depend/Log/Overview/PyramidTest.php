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
 * @subpackage Log
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';
require_once dirname(__FILE__) . '/../DummyAnalyzer.php';
require_once dirname(__FILE__) . '/CouplingAnalyzer.php';
require_once dirname(__FILE__) . '/CyclomaticComplexityAnalyzer.php';
require_once dirname(__FILE__) . '/InheritanceAnalyzer.php';
require_once dirname(__FILE__) . '/NodeCountAnalyzer.php';
require_once dirname(__FILE__) . '/NodeLocAnalyzer.php';

/**
 * Test case for the overview pyramid logger.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Log
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 *
 * @covers PHP_Depend_Log_Overview_Pyramid
 * @group pdepend
 * @group pdepend::log
 * @group pdepend::log::overview
 * @group unittest
 */
class PHP_Depend_Log_Overview_PyramidTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the logger returns the expected set of analyzers.
     *
     * @return void
     */
    public function testReturnsExceptedAnalyzers()
    {
        $logger    = new PHP_Depend_Log_Overview_Pyramid();
        $actual    = $logger->getAcceptedAnalyzers();
        $exptected = array(
            'PHP_Depend_Metrics_Coupling_Analyzer',
            'PHP_Depend_Metrics_CyclomaticComplexity_Analyzer',
            'PHP_Depend_Metrics_Inheritance_Analyzer',
            'PHP_Depend_Metrics_NodeCount_Analyzer',
            'PHP_Depend_Metrics_NodeLoc_Analyzer'
        );

        self::assertEquals($exptected, $actual);
    }

    /**
     * Tests that the logger throws an exception if the log target wasn't
     * configured.
     *
     * @return void
     */
    public function testThrowsExceptionForInvalidLogTarget()
    {
        $this->setExpectedException(
            'PHP_Depend_Log_NoLogOutputException',
            "The log target is not configured for 'PHP_Depend_Log_Overview_Pyramid'."
        );

        $logger = new PHP_Depend_Log_Overview_Pyramid();
        $logger->close();
    }

    /**
     * Tests that the log method returns <b>false</b> for an invalid logger.
     *
     * @return void
     */
    public function testPyramidDoesntAcceptInvalidAnalyzer()
    {
        $logger = new PHP_Depend_Log_Overview_Pyramid();
        self::assertFalse($logger->log(new PHP_Depend_Log_DummyAnalyzer()));
    }

    /**
     * Tests that the logger checks for the required analyzer.
     *
     * @return void
     */
    public function testCloseThrowsAnExceptionIfNoCouplingAnalyzerWasSet()
    {
        $this->setExpectedException(
            'RuntimeException',
            'Missing Coupling analyzer.'
        );

        $log = new PHP_Depend_Log_Overview_Pyramid();
        $log->setLogFile(self::createRunResourceURI('_tmp_.svg'));
        $log->log(new PHP_Depend_Log_Overview_CyclomaticComplexityAnalyzer());
        $log->log(new PHP_Depend_Log_Overview_InheritanceAnalyzer());
        $log->log(new PHP_Depend_Log_Overview_NodeCountAnalyzer());
        $log->log(new PHP_Depend_Log_Overview_NodeLocAnalyzer());
        $log->close();
    }

    /**
     * Tests that the logger checks for the required analyzer.
     *
     * @return void
     */
    public function testCloseThrowsAnExceptionIfNoCyclomaticComplexityAnalyzerWasSet()
    {
        $this->setExpectedException(
            'RuntimeException',
            'Missing Cyclomatic Complexity analyzer.'
        );

        $log = new PHP_Depend_Log_Overview_Pyramid();
        $log->setLogFile(self::createRunResourceURI('_tmp_.svg'));
        $log->log(new PHP_Depend_Log_Overview_CouplingAnalyzer());
        $log->log(new PHP_Depend_Log_Overview_InheritanceAnalyzer());
        $log->log(new PHP_Depend_Log_Overview_NodeCountAnalyzer());
        $log->log(new PHP_Depend_Log_Overview_NodeLocAnalyzer());
        $log->close();
    }

    /**
     * Tests that the logger checks for the required analyzer.
     *
     * @return void
     */
    public function testCloseThrowsAnExceptionIfNoInheritanceAnalyzerWasSet()
    {
        $this->setExpectedException(
            'RuntimeException',
            'Missing Inheritance analyzer.'
        );

        $log = new PHP_Depend_Log_Overview_Pyramid();
        $log->setLogFile(self::createRunResourceURI('_tmp_.svg'));
        $log->log(new PHP_Depend_Log_Overview_CouplingAnalyzer());
        $log->log(new PHP_Depend_Log_Overview_CyclomaticComplexityAnalyzer());
        $log->log(new PHP_Depend_Log_Overview_NodeCountAnalyzer());
        $log->log(new PHP_Depend_Log_Overview_NodeLocAnalyzer());
        $log->close();
    }

    /**
     * Tests that the logger checks for the required analyzer.
     *
     * @return void
     */
    public function testCloseThrowsAnExceptionIfNoNodeCountAnalyzerWasSet()
    {
        $this->setExpectedException(
            'RuntimeException',
            'Missing Node Count analyzer.'
        );

        $log = new PHP_Depend_Log_Overview_Pyramid();
        $log->setLogFile(self::createRunResourceURI('_tmp_.svg'));
        $log->log(new PHP_Depend_Log_Overview_CouplingAnalyzer());
        $log->log(new PHP_Depend_Log_Overview_CyclomaticComplexityAnalyzer());
        $log->log(new PHP_Depend_Log_Overview_InheritanceAnalyzer());
        $log->log(new PHP_Depend_Log_Overview_NodeLocAnalyzer());
        $log->close();
    }

    /**
     * Tests that the logger checks for the required analyzer.
     *
     * @return void
     */
    public function testCloseThrowsAnExceptionIfNoNodeLOCAnalyzerWasSet()
    {
        $this->setExpectedException(
            'RuntimeException',
            'Missing Node LOC analyzer.'
        );

        $log = new PHP_Depend_Log_Overview_Pyramid();
        $log->setLogFile(self::createRunResourceURI('_tmp_.svg'));
        $log->log(new PHP_Depend_Log_Overview_CouplingAnalyzer());
        $log->log(new PHP_Depend_Log_Overview_CyclomaticComplexityAnalyzer());
        $log->log(new PHP_Depend_Log_Overview_InheritanceAnalyzer());
        $log->log(new PHP_Depend_Log_Overview_NodeCountAnalyzer());
        $log->close();
    }

    /**
     * testCollectedAndComputedValuesInOutputSVG
     *
     * @return void
     */
    public function testCollectedAndComputedValuesInOutputSVG()
    {
        $output = self::createRunResourceURI('temp.svg');
        if (file_exists($output)) {
            unlink($output);
        }

        $log = new PHP_Depend_Log_Overview_Pyramid();
        $log->setLogFile($output);
        $log->log(new PHP_Depend_Log_Overview_CouplingAnalyzer());
        $log->log(new PHP_Depend_Log_Overview_CyclomaticComplexityAnalyzer());
        $log->log(new PHP_Depend_Log_Overview_InheritanceAnalyzer());
        $log->log(new PHP_Depend_Log_Overview_NodeCountAnalyzer());
        $log->log(new PHP_Depend_Log_Overview_NodeLocAnalyzer());
        $log->close();

        self::assertFileExists($output);

        $expected = array(
            'cyclo'         =>  5579,
            'loc'           =>  35175,
            'nom'           =>  3618,
            'noc'           =>  384,
            'nop'           =>  19,
            'andc'          =>  0.31,
            'ahh'           =>  0.12,
            'calls'         =>  15128,
            'fanout'        =>  8590,
            'cyclo-loc'     =>  0.15,
            'loc-nom'       =>  9.72,
            'nom-noc'       =>  9.42,
            'noc-nop'       =>  20.21,
            'fanout-calls'  =>  0.56,
            'calls-nom'     =>  4.18
        );

        $svg = new DOMDocument();
        $svg->load($output);

        // TODO: Replace this loop assertion
        foreach ($expected as $name => $value) {
            $elem = $svg->getElementById("pdepend.{$name}");
            self::assertInstanceOf('DOMElement', $elem);
            self::assertEquals($value, $elem->nodeValue, null, 0.01);
        }

        unlink($output);
    }
}
