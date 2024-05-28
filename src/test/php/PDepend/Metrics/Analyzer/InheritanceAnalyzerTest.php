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
use PDepend\Source\AST\ASTArtifactList\CollectionArtifactFilter;
use PDepend\Source\AST\ASTArtifactList\PackageArtifactFilter;
use PDepend\Source\AST\ASTClass;

/**
 * Test case for the inheritance analyzer.
 *
 * @covers \PDepend\Metrics\Analyzer\InheritanceAnalyzer
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @group unittest
 */
class InheritanceAnalyzerTest extends AbstractMetricsTestCase
{
    /**
     * Tests that the analyzer calculates the correct average number of derived
     * classes.
     */
    public function testAnalyzerCalculatesCorrectANDCValue(): void
    {
        $filter = CollectionArtifactFilter::getInstance();
        $filter->setFilter(new PackageArtifactFilter(['library']));

        $namespaces = $this->parseTestCaseSource(__METHOD__);
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $project = $analyzer->getProjectMetrics();

        static::assertEqualsWithDelta(0.7368, $project['andc'], 0.0001);
    }

    /**
     * Tests that the analyzer calculates the correct average hierarchy height.
     */
    public function testAnalyzerCalculatesCorrectAHHValue(): void
    {
        $filter = CollectionArtifactFilter::getInstance();
        $filter->setFilter(new PackageArtifactFilter(['library']));

        $namespaces = $this->parseTestCaseSource(__METHOD__);
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $project = $analyzer->getProjectMetrics();

        static::assertEquals(1, $project['ahh']);
    }

    /**
     * testCalculatesExpectedNoccMetricForClassWithoutChildren
     */
    public function testCalculatesExpectedNoccMetricForClassWithoutChildren(): void
    {
        static::assertEquals(0, $this->getCalculatedMetric(__METHOD__, 'nocc'));
    }

    /**
     * testCalculatesExpectedNoccMetricForClassWithDirectChildren
     */
    public function testCalculatesExpectedNoccMetricForClassWithDirectChildren(): void
    {
        static::assertEquals(3, $this->getCalculatedMetric(__METHOD__, 'nocc'));
    }

    /**
     * testCalculatesExpectedNoccMetricForClassWithDirectAndIndirectChildren
     */
    public function testCalculatesExpectedNoccMetricForClassWithDirectAndIndirectChildren(): void
    {
        static::assertEquals(1, $this->getCalculatedMetric(__METHOD__, 'nocc'));
    }

    /**
     * Tests that the analyzer calculates the correct DIT values.
     */
    public function testCalculateDITMetricNoInheritance(): void
    {
        static::assertEquals(0, $this->getCalculatedMetric(__METHOD__, 'dit'));
    }

    /**
     * Tests that the analyzer calculates the correct DIT values.
     */
    public function testCalculateDITMetricOneLevelInheritance(): void
    {
        static::assertEquals(1, $this->getCalculatedMetric(__METHOD__, 'dit'));
    }

    /**
     * Tests that the analyzer calculates the correct DIT values.
     */
    public function testCalculateDITMetricTwoLevelNoInheritance(): void
    {
        static::assertEquals(2, $this->getCalculatedMetric(__METHOD__, 'dit'));
    }

    /**
     * Tests that the analyzer calculates the correct DIT values.
     */
    public function testCalculateDITMetricThreeLevelNoInheritance(): void
    {
        static::assertEquals(3, $this->getCalculatedMetric(__METHOD__, 'dit'));
    }

    /**
     * Tests that the analyzer calculates the correct DIT values.
     */
    public function testCalculateDITMetricFourLevelNoInheritance(): void
    {
        static::assertEquals(4, $this->getCalculatedMetric(__METHOD__, 'dit'));
    }

    /**
     * testCalculateDITMetricForUnknownParentIncrementsMetricWithTwo
     */
    public function testCalculateDITMetricForUnknownParentIncrementsMetricWithTwo(): void
    {
        static::assertEquals(3, $this->getCalculatedMetric(__METHOD__, 'dit'));
    }

    /**
     * testCalculateDITMetricForInternalParentIncrementsMetricWithTwo
     */
    public function testCalculateDITMetricForInternalParentIncrementsMetricWithTwo(): void
    {
        static::assertEquals(3, $this->getCalculatedMetric(__METHOD__, 'dit'));
    }

    /**
     * Tests that {@link \PDepend\Metrics\Analyzer\InheritanceAnalyzer::analyze()}
     * calculates the expected DIT values.
     */
    public function testCalculateDepthOfInheritanceForSeveralClasses(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $actual = [];
        foreach ($namespaces[0]->getClasses() as $class) {
            $metrics = $analyzer->getNodeMetrics($class);

            $actual[$class->getImage()] = $metrics['dit'];
        }
        ksort($actual);

        $expected = [
            'A' => 0,
            'B' => 1,
            'C' => 1,
            'D' => 2,
            'E' => 3,
        ];

        static::assertEquals($expected, $actual);
    }

    /**
     * testCalculatesExpectedMaxDepthOfInheritanceTreeMetric
     */
    public function testCalculatesExpectedMaxDepthOfInheritanceTreeMetric(): void
    {
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($this->parseTestCaseSource(__METHOD__));

        $metrics = $analyzer->getProjectMetrics();
        static::assertEquals(3, $metrics['maxDIT']);
    }

    /**
     * testCalculatesExpectedNoamMetricForClassWithoutParent
     */
    public function testCalculatesExpectedNoamMetricForClassWithoutParent(): void
    {
        static::assertEquals(0, $this->getCalculatedMetric(__METHOD__, 'noam'));
    }

    /**
     * testCalculatesExpectedNoamMetricForClassWithDirectParent
     */
    public function testCalculatesExpectedNoamMetricForClassWithDirectParent(): void
    {
        static::assertEquals(2, $this->getCalculatedMetric(__METHOD__, 'noam'));
    }

    /**
     * testCalculatesExpectedNoamMetricForClassWithIndirectParent
     */
    public function testCalculatesExpectedNoamMetricForClassWithIndirectParent(): void
    {
        static::assertEquals(2, $this->getCalculatedMetric(__METHOD__, 'noam'));
    }

    /**
     * testCalculatesExpectedNoomMetricForClassWithoutParent
     */
    public function testCalculatesExpectedNoomMetricForClassWithoutParent(): void
    {
        static::assertEquals(0, $this->getCalculatedMetric(__METHOD__, 'noom'));
    }

    /**
     * testCalculatesExpectedNoomMetricForClassWithParent
     */
    public function testCalculatesExpectedNoomMetricForClassWithParent(): void
    {
        static::assertEquals(2, $this->getCalculatedMetric(__METHOD__, 'noom'));
    }

    /**
     * testCalculatesExpectedNoomMetricForClassWithParentPrivateMethods
     */
    public function testCalculatesExpectedNoomMetricForClassWithParentPrivateMethods(): void
    {
        static::assertEquals(1, $this->getCalculatedMetric(__METHOD__, 'noom'));
    }

    /**
     * testAnalyzerIgnoresClassesThatAreNotUserDefined
     */
    public function testAnalyzerIgnoresClassesThatAreNotUserDefined(): void
    {
        $class = new ASTClass(null);

        $analyzer = $this->createAnalyzer();
        $analyzer->visitClass($class);

        $metrics = $analyzer->getNodeMetrics($class);
        static::assertEquals([], $metrics);
    }

    /**
     * Analyzes the source associated with the calling test and returns the
     * calculated metric value.
     *
     * @param string $testCase Name of the calling test case.
     * @param string $metric Name of the searched metric.
     */
    private function getCalculatedMetric(string $testCase, string $metric): mixed
    {
        $namespaces = $this->parseTestCaseSource($testCase);
        $namespace = $namespaces->current();

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = $analyzer->getNodeMetrics($namespace->getClasses()->current());

        return $metrics[$metric];
    }

    private function createAnalyzer(): InheritanceAnalyzer
    {
        return new InheritanceAnalyzer();
    }
}
