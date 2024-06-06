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

namespace PDepend\Metrics\Analyzer;

use PDepend\Metrics\AbstractMetricsTestCase;

/**
 * Test cases for the {@link \PDepend\Metrics\Analyzer\CrapIndexAnalyzer} class.
 *
 * @covers \PDepend\Metrics\Analyzer\CrapIndexAnalyzer
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class CrapIndexAnalyzerTest extends AbstractMetricsTestCase
{
    /**
     * testAnalyzerReturnsExpectedDependencies
     */
    public function testAnalyzerReturnsExpectedDependencies(): void
    {
        $analyzer = new CrapIndexAnalyzer();
        $actual = $analyzer->getRequiredAnalyzers();
        $expected = [CyclomaticComplexityAnalyzer::class];

        static::assertEquals($expected, $actual);
    }

    /**
     * testAnalyzerIsEnabledReturnsFalseWhenNoCoverageReportFileWasSupplied
     */
    public function testAnalyzerIsEnabledReturnsFalseWhenNoCoverageReportFileWasSupplied(): void
    {
        $analyzer = new CrapIndexAnalyzer();

        static::assertFalse($analyzer->isEnabled());
    }

    /**
     * testAnalyzerIsEnabledReturnsTrueWhenCoverageReportFileWasSupplied
     */
    public function testAnalyzerIsEnabledReturnsTrueWhenCoverageReportFileWasSupplied(): void
    {
        $options = ['coverage-report' => $this->createCloverReportFile()];
        $analyzer = new CrapIndexAnalyzer($options);

        static::assertTrue($analyzer->isEnabled());
    }

    /**
     * testAnalyzerIgnoresAbstractMethods
     */
    public function testAnalyzerIgnoresAbstractMethods(): void
    {
        $metrics = $this->calculateCrapIndex(__METHOD__, 42);
        static::assertSame([], $metrics);
    }

    /**
     * testAnalyzerIgnoresInterfaceMethods
     */
    public function testAnalyzerIgnoresInterfaceMethods(): void
    {
        $metrics = $this->calculateCrapIndex(__METHOD__, 42);
        static::assertSame([], $metrics);
    }

    /**
     * testAnalyzerReturnsExpectedResultForMethodWithoutCoverage
     */
    public function testAnalyzerReturnsExpectedResultForMethodWithoutCoverage(): void
    {
        $this->doTestCrapIndexCalculation(__METHOD__, 12, 156);
    }

    /**
     * testAnalyzerReturnsExpectedResultForMethodWith100PercentCoverage
     */
    public function testAnalyzerReturnsExpectedResultForMethodWith100PercentCoverage(): void
    {
        $this->doTestCrapIndexCalculation(__METHOD__, 12, 12);
    }

    /**
     * testAnalyzerReturnsExpectedResultForMethodWith50PercentCoverage
     */
    public function testAnalyzerReturnsExpectedResultForMethodWith50PercentCoverage(): void
    {
        $this->doTestCrapIndexCalculation(__METHOD__, 12, 30);
    }

    /**
     * testAnalyterReturnsExpectedResultForMethodWithoutCoverageData
     */
    public function testAnalyterReturnsExpectedResultForMethodWithoutCoverageData(): void
    {
        $this->doTestCrapIndexCalculation(__METHOD__, 12, 156);
    }

    /**
     * testAnalyterReturnsExpectedResultForFunctionWithoutCoverageData
     */
    public function testAnalyterReturnsExpectedResultForFunctionWithoutCoverageData(): void
    {
        $this->doTestCrapIndexCalculation(__METHOD__, 12, 156);
    }

    /**
     * Tests the crap index algorithm implementation.
     *
     * @param string $testCase Name of the calling test case.
     * @param int $ccn The entire cyclomatic complexity number.
     * @param int $crapIndex The expected crap index.
     */
    private function doTestCrapIndexCalculation(string $testCase, int $ccn, int $crapIndex): void
    {
        $metrics = $this->calculateCrapIndex($testCase, $ccn);
        static::assertEqualsWithDelta($crapIndex, $metrics['crap'], 0.005);
    }

    /**
     * Calculates the crap index.
     *
     * @param string $testCase Name of the calling test case.
     * @param int $ccn The entire cyclomatic complexity number.
     * @return array<mixed>
     */
    private function calculateCrapIndex(string $testCase, int $ccn): array
    {
        $namespaces = $this->parseCodeResourceForTest();

        $options = ['coverage-report' => $this->createCloverReportFile()];
        $analyzer = new CrapIndexAnalyzer($options);
        $analyzer->addAnalyzer($this->createCyclomaticComplexityAnalyzerMock($ccn));
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
     */
    private function createCloverReportFile(): string
    {
        $pathName = $this->createRunResourceURI('clover.xml');

        $content = file_get_contents(__DIR__ . '/_files/clover.xml') ?: '';
        $directory = dirname($this->createCodeResourceUriForTest()) . DIRECTORY_SEPARATOR;
        $content = str_replace('${pathName}', $directory, $content);
        file_put_contents($pathName, $content);

        return $pathName;
    }

    /**
     * Creates a mocked instance of the cyclomatic complexity analyzer.
     */
    private function createCyclomaticComplexityAnalyzerMock(int $ccn = 42): CyclomaticComplexityAnalyzer
    {
        $mock = $this->getMockBuilder(CyclomaticComplexityAnalyzer::class)
            ->getMock();
        $mock->expects(static::any())
            ->method('getCCN2')
            ->will(static::returnValue($ccn));

        return $mock;
    }
}
