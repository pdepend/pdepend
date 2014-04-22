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

namespace PDepend\Metrics\Analyzer;

use PDepend\Metrics\AbstractMetricsTest;

/**
 * Test cases for the {@link  \PDepend\Metrics\Analyzer\CrapIndexAnalyzer} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Metrics\Analyzer\CrapIndexAnalyzer
 * @group unittest
 */
class CrapIndexAnalyzerTest extends AbstractMetricsTest
{
    /**
     * testAnalyzerReturnsExpectedDependencies
     *
     * @return void
     */
    public function testAnalyzerReturnsExpectedDependencies()
    {
        $analyzer = new CrapIndexAnalyzer();
        $actual   = $analyzer->getRequiredAnalyzers();
        $expected = array('PDepend\\Metrics\\Analyzer\\CyclomaticComplexityAnalyzer');

        $this->assertEquals($expected, $actual);
    }

    /**
     * testAnalyzerIsEnabledReturnsFalseWhenNoCoverageReportFileWasSupplied
     *
     * @return void
     */
    public function testAnalyzerIsEnabledReturnsFalseWhenNoCoverageReportFileWasSupplied()
    {
        $analyzer = new CrapIndexAnalyzer();
        
        $this->assertFalse($analyzer->isEnabled());
    }

    /**
     * testAnalyzerIsEnabledReturnsTrueWhenCoverageReportFileWasSupplied
     *
     * @return void
     */
    public function testAnalyzerIsEnabledReturnsTrueWhenCoverageReportFileWasSupplied()
    {
        $options  = array('coverage-report' => $this->_createCloverReportFile());
        $analyzer = new CrapIndexAnalyzer($options);

        $this->assertTrue($analyzer->isEnabled());
    }

    /**
     * testAnalyzerIgnoresAbstractMethods
     * 
     * @return void
     */
    public function testAnalyzerIgnoresAbstractMethods()
    {
        $metrics = $this->_calculateCrapIndex(__METHOD__, 42);
        $this->assertSame(array(), $metrics);
    }

    /**
     * testAnalyzerIgnoresInterfaceMethods
     * 
     * @return void
     */
    public function testAnalyzerIgnoresInterfaceMethods()
    {
        $metrics = $this->_calculateCrapIndex(__METHOD__, 42);
        $this->assertSame(array(), $metrics);
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
        $this->assertEquals($crapIndex, $metrics['crap'], '', 0.005);
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
        $namespaces = self::parseCodeResourceForTest();

        $options  = array('coverage-report' => $this->_createCloverReportFile());
        $analyzer = new CrapIndexAnalyzer($options);
        $analyzer->addAnalyzer($this->_createCyclomaticComplexityAnalyzerMock($ccn));
        $analyzer->analyze($namespaces);

        $namespaces->rewind();

        if ($namespaces->current()->getTypes()->count() > 0) {
            return $analyzer->getNodeMetrics(
                $namespaces->current()
                    ->getTypes()
                    ->current()
                    ->getMethods()
                    ->current()
            );
        }
        return $analyzer->getNodeMetrics(
            $namespaces->current()
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
     * @param integer $ccn
     * @return \PDepend\Metrics\Analyzer\CyclomaticComplexityAnalyzer
     */
    private function _createCyclomaticComplexityAnalyzerMock($ccn = 42)
    {
        $mock = $this->getMock('PDepend\\Metrics\\Analyzer\\CyclomaticComplexityAnalyzer');
        $mock->expects($this->any())
            ->method('getCCN2')
            ->will($this->returnValue($ccn));

        return $mock;
    }
}
