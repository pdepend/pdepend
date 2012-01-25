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
 * @subpackage Metrics_CrapIndex
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test cases for the {@link PHP_Depend_Metrics_CrapIndex_Analyzer} class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics_CrapIndex
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 *
 * @covers PHP_Depend_Metrics_CrapIndex_Analyzer
 * @group pdepend
 * @group pdepend::metrics
 * @group pdepend::metrics::crapindex
 * @group unittest
 */
class PHP_Depend_Metrics_CrapIndex_AnalyzerTest extends PHP_Depend_Metrics_AbstractTest
{
    /**
     * testAnalyzerReturnsExpectedDependencies
     *
     * @return void
     */
    public function testAnalyzerReturnsExpectedDependencies()
    {
        $analyzer = new PHP_Depend_Metrics_CrapIndex_Analyzer();
        $actual   = $analyzer->getRequiredAnalyzers();
        $expected = array(PHP_Depend_Metrics_CyclomaticComplexity_Analyzer::CLAZZ);

        self::assertEquals($expected, $actual);
    }

    /**
     * testAnalyzerIsEnabledReturnsFalseWhenNoCoverageReportFileWasSupplied
     *
     * @return void
     */
    public function testAnalyzerIsEnabledReturnsFalseWhenNoCoverageReportFileWasSupplied()
    {
        $analyzer = new PHP_Depend_Metrics_CrapIndex_Analyzer();
        
        self::assertFalse($analyzer->isEnabled());
    }

    /**
     * testAnalyzerIsEnabledReturnsTrueWhenCoverageReportFileWasSupplied
     *
     * @return void
     */
    public function testAnalyzerIsEnabledReturnsTrueWhenCoverageReportFileWasSupplied()
    {
        $options  = array('coverage-report' => $this->_createCloverReportFile());
        $analyzer = new PHP_Depend_Metrics_CrapIndex_Analyzer($options);

        self::assertTrue($analyzer->isEnabled());
    }

    /**
     * testAnalyzerIgnoresAbstractMethods
     * 
     * @return void
     */
    public function testAnalyzerIgnoresAbstractMethods()
    {
        $metrics = $this->_calculateCrapIndex(__METHOD__, 42);
        self::assertSame(array(), $metrics);
    }

    /**
     * testAnalyzerIgnoresInterfaceMethods
     * 
     * @return void
     */
    public function testAnalyzerIgnoresInterfaceMethods()
    {
        $metrics = $this->_calculateCrapIndex(__METHOD__, 42);
        self::assertSame(array(), $metrics);
    }

    /**
     * testAnalyzerReturnsExpectedResultForMethodWithoutCoverage
     * 
     * @return void
     */
    public function testAnalyzerReturnsExpectedResultForMethodWithoutCoverage()
    {
        $this->_testCrapIndexCalculation(__METHOD__, 12, 156);
    }

    /**
     * testAnalyzerReturnsExpectedResultForMethodWith100PercentCoverage
     *
     * @return void
     */
    public function testAnalyzerReturnsExpectedResultForMethodWith100PercentCoverage()
    {
        $this->_testCrapIndexCalculation(__METHOD__, 12, 12);
    }

    /**
     * testAnalyzerReturnsExpectedResultForMethodWith50PercentCoverage
     *
     * @return void
     */
    public function testAnalyzerReturnsExpectedResultForMethodWith50PercentCoverage()
    {
        $this->_testCrapIndexCalculation(__METHOD__, 12, 30);
    }

    /**
     * testAnalyterReturnsExpectedResultForMethodWithoutCoverageData
     *
     * @return void
     */
    public function testAnalyterReturnsExpectedResultForMethodWithoutCoverageData()
    {
        $this->_testCrapIndexCalculation(__METHOD__, 12, 156);
    }

    /**
     * testAnalyterReturnsExpectedResultForFunctionWithoutCoverageData
     *
     * @return void
     */
    public function testAnalyterReturnsExpectedResultForFunctionWithoutCoverageData()
    {
        $this->_testCrapIndexCalculation(__METHOD__, 12, 156);
    }

    /**
     * Tests the crap index algorithm implementation.
     *
     * @param string  $testCase  Name of the calling test case.
     * @param integer $ccn       The entire cyclomatic complexity number.
     * @param integer $crapIndex The expected crap index.
     *
     * @return void
     */
    private function _testCrapIndexCalculation($testCase, $ccn, $crapIndex)
    {
        $metrics = $this->_calculateCrapIndex($testCase, $ccn);
        self::assertEquals($crapIndex, $metrics['crap'], '', 0.005);
    }

    /**
     * Calculates the crap index.
     *
     * @param string  $testCase Name of the calling test case.
     * @param integer $ccn      The entire cyclomatic complexity number.
     *
     * @return array
     */
    private function _calculateCrapIndex($testCase, $ccn)
    {
        $packages = self::parseCodeResourceForTest();

        $options  = array('coverage-report' => $this->_createCloverReportFile());
        $analyzer = new PHP_Depend_Metrics_CrapIndex_Analyzer($options);
        $analyzer->addAnalyzer($this->_createCyclomaticComplexityAnalyzerMock($ccn));
        $analyzer->analyze($packages);

        $packages->rewind();

        if ($packages->current()->getTypes()->count() > 0) {
            return $analyzer->getNodeMetrics(
                $packages->current()
                    ->getTypes()
                    ->current()
                    ->getMethods()
                    ->current()
            );
        }
        return $analyzer->getNodeMetrics(
            $packages->current()
                ->getFunctions()
                ->current()
        );
    }

    /**
     * Creates a temporary clover report file that can be used for a single test.
     *
     * @return string
     */
    private function _createCloverReportFile()
    {
        $pathName = self::createRunResourceURI('clover.xml');

        $content = file_get_contents(dirname(__FILE__) . '/_files/clover.xml');
        $content = str_replace('${pathName}', dirname(self::createCodeResourceUriForTest()), $content);
        file_put_contents($pathName, $content);

        return $pathName;
    }

    /**
     * Creates a mocked instance of the cyclomatic complexity analyzer.
     *
     * @param integer $ccn The expected ccn result value.
     *
     * @return PHP_Depend_Metrics_CyclomaticComplexity_Analyzer
     */
    private function _createCyclomaticComplexityAnalyzerMock($ccn = 42)
    {
        $mock = $this->getMock(PHP_Depend_Metrics_CyclomaticComplexity_Analyzer::CLAZZ);
        $mock->expects($this->any())
            ->method('getCCN2')
            ->will($this->returnValue($ccn));

        return $mock;
    }
}
