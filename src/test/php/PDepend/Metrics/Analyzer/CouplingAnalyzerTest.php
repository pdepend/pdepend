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
use PDepend\Source\AST\ASTArtifact;

/**
 * Test case for the coupling analyzer.
 *
 * @covers \PDepend\Metrics\Analyzer\CouplingAnalyzer
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class CouplingAnalyzerTest extends AbstractMetricsTestCase
{
    /**
     * testGetNodeMetricsReturnsAnEmptyArrayByDefault
     */
    public function testGetNodeMetricsReturnsAnEmptyArrayByDefault(): void
    {
        $astArtifact = $this->getMockBuilder(ASTArtifact::class)
            ->getMock();

        $analyzer = new CouplingAnalyzer();
        static::assertEquals(
            [],
            $analyzer->getNodeMetrics($astArtifact)
        );
    }

    /**
     * testGetNodeMetricsReturnsArrayWithExpectedSetOfMetrics
     */
    public function testGetNodeMetricsReturnsArrayWithExpectedSetOfMetrics(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $classes = $namespaces[0]->getClasses();

        $analyzer = new CouplingAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = array_keys($analyzer->getNodeMetrics($classes[0]));
        sort($metrics);

        static::assertEquals(['ca', 'cbo', 'ce'], $metrics);
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaWithoutDependencies
     */
    public function testGetNodeMetricsReturnsExpectedCaWithoutDependencies(): void
    {
        static::assertEquals(0, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaWithObjectInstantiation
     */
    public function testGetNodeMetricsReturnsExpectedCaWithObjectInstantiation(): void
    {
        static::assertEquals(1, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaWithStaticReference
     */
    public function testGetNodeMetricsReturnsExpectedCaWithStaticReference(): void
    {
        static::assertEquals(1, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaWithReturnReference
     */
    public function testGetNodeMetricsReturnsExpectedCaWithReturnReference(): void
    {
        static::assertEquals(1, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaWithExceptionReference
     */
    public function testGetNodeMetricsReturnsExpectedCaWithExceptionReference(): void
    {
        static::assertEquals(2, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaWithPropertyReference
     */
    public function testGetNodeMetricsReturnsExpectedCaWithPropertyReference(): void
    {
        static::assertEquals(1, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaWithoutDuplicateCount
     */
    public function testGetNodeMetricsReturnsExpectedCaWithoutDuplicateCount(): void
    {
        static::assertEquals(2, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForParameterTypes
     */
    public function testGetNodeMetricsReturnsExpectedCaForParameterTypes(): void
    {
        static::assertEquals(3, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForParentTypeReference
     */
    public function testGetNodeMetricsReturnsExpectedCaForParentTypeReference(): void
    {
        static::assertEquals(0, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForChildTypeReference
     */
    public function testGetNodeMetricsReturnsExpectedCaForChildTypeReference(): void
    {
        static::assertEquals(2, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForFunctionReference
     */
    public function testGetNodeMetricsReturnsExpectedCaForFunctionReference(): void
    {
        static::assertEquals(1, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForFunctionException
     */
    public function testGetNodeMetricsReturnsExpectedCaForFunctionException(): void
    {
        static::assertEquals(1, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForFunctionReturnType
     */
    public function testGetNodeMetricsReturnsExpectedCaForFunctionReturnType(): void
    {
        static::assertEquals(1, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForFunctionParameter
     */
    public function testGetNodeMetricsReturnsExpectedCaForFunctionParameter(): void
    {
        static::assertEquals(1, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForFunctions
     */
    public function testGetNodeMetricsReturnsExpectedCaForFunctions(): void
    {
        static::assertEquals(3, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForFunctionCountsTypeOnce
     */
    public function testGetNodeMetricsReturnsExpectedCaForFunctionCountsTypeOnce(): void
    {
        static::assertEquals(2, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboWithoutDependencies
     */
    public function testGetNodeMetricsReturnsExpectedCboWithoutDependencies(): void
    {
        static::assertEquals(0, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboWithObjectInstantiation
     */
    public function testGetNodeMetricsReturnsExpectedCboWithObjectInstantiation(): void
    {
        static::assertEquals(1, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboWithStaticReference
     */
    public function testGetNodeMetricsReturnsExpectedCboWithStaticReference(): void
    {
        static::assertEquals(1, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboWithReturnReference
     */
    public function testGetNodeMetricsReturnsExpectedCboWithReturnReference(): void
    {
        static::assertEquals(1, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboWithExceptionReference
     */
    public function testGetNodeMetricsReturnsExpectedCboWithExceptionReference(): void
    {
        static::assertEquals(2, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboWithPropertyReference
     */
    public function testGetNodeMetricsReturnsExpectedCboWithPropertyReference(): void
    {
        static::assertEquals(1, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboWithoutDuplicateCount
     */
    public function testGetNodeMetricsReturnsExpectedCboWithoutDuplicateCount(): void
    {
        static::assertEquals(2, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboForParameterTypes
     */
    public function testGetNodeMetricsReturnsExpectedCboForParameterTypes(): void
    {
        static::assertEquals(3, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboForParentTypeReference
     */
    public function testGetNodeMetricsReturnsExpectedCboForParentTypeReference(): void
    {
        static::assertEquals(0, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboForChildTypeReference
     */
    public function testGetNodeMetricsReturnsExpectedCboForChildTypeReference(): void
    {
        static::assertEquals(2, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboForUseInSameNamespace
     */
    public function testGetNodeMetricsReturnsExpectedCboForUseInSameNamespace(): void
    {
        static::assertEquals(1, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboForUseInPartialSameNamespace
     */
    public function testGetNodeMetricsReturnsExpectedCboForUseInPartialSameNamespace(): void
    {
        static::assertEquals(1, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeWithoutDependencies
     */
    public function testGetNodeMetricsReturnsExpectedCeWithoutDependencies(): void
    {
        static::assertEquals(0, $this->calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeWithObjectInstantiation
     */
    public function testGetNodeMetricsReturnsExpectedCeWithObjectInstantiation(): void
    {
        static::assertEquals(1, $this->calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeWithStaticReference
     */
    public function testGetNodeMetricsReturnsExpectedCeWithStaticReference(): void
    {
        static::assertEquals(1, $this->calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeWithReturnReference
     */
    public function testGetNodeMetricsReturnsExpectedCeWithReturnReference(): void
    {
        static::assertEquals(1, $this->calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeWithExceptionReference
     */
    public function testGetNodeMetricsReturnsExpectedCeWithExceptionReference(): void
    {
        static::assertEquals(2, $this->calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeWithPropertyReference
     */
    public function testGetNodeMetricsReturnsExpectedCeWithPropertyReference(): void
    {
        static::assertEquals(1, $this->calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeWithoutDuplicateCount
     */
    public function testGetNodeMetricsReturnsExpectedCeWithoutDuplicateCount(): void
    {
        static::assertEquals(2, $this->calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeForParameterTypes
     */
    public function testGetNodeMetricsReturnsExpectedCeForParameterTypes(): void
    {
        static::assertEquals(3, $this->calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeForParentTypeReference
     */
    public function testGetNodeMetricsReturnsExpectedCeForParentTypeReference(): void
    {
        static::assertEquals(0, $this->calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeForChildTypeReference
     */
    public function testGetNodeMetricsReturnsExpectedCeForChildTypeReference(): void
    {
        static::assertEquals(2, $this->calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeForUseInSameNamespace
     */
    public function testGetNodeMetricsReturnsExpectedCeForUseInSameNamespace(): void
    {
        static::assertEquals(1, $this->calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeForUseInPartialSameNamespace
     */
    public function testGetNodeMetricsReturnsExpectedCeForUseInPartialSameNamespace(): void
    {
        static::assertEquals(1, $this->calculateTypeMetric('ce'));
    }

    /**
     * Returns the specified node metric for the first type found in the
     * analyzed test source and returns the metric value for the given <b>$name</b>.
     *
     * @param string $name Name of the requested software metric.
     */
    private function calculateTypeMetric(string $name): mixed
    {
        $namespaces = $this->parseCodeResourceForTest();
        $types = $namespaces[0]->getTypes();

        $analyzer = new CouplingAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = $analyzer->getNodeMetrics($types[0]);

        return $metrics[$name];
    }

    /**
     * testAnalyzerGetProjectMetricsReturnsArrayWithExpectedKeys
     */
    public function testAnalyzerGetProjectMetricsReturnsArrayWithExpectedKeys(): void
    {
        $expected = ['calls', 'fanout'];
        $actual = array_keys($this->calculateProjectMetrics());

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * functions.
     */
    public function testAnalyzerCalculatesCorrectFunctionCoupling(): void
    {
        $expected = ['calls' => 10, 'fanout' => 7];
        $actual = $this->calculateProjectMetrics();

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * methods.
     */
    public function testAnalyzerCalculatesCorrectMethodCoupling(): void
    {
        $expected = ['calls' => 10, 'fanout' => 9];
        $actual = $this->calculateProjectMetrics();

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * properties.
     */
    public function testAnalyzerCalculatesCorrectPropertyCoupling(): void
    {
        $expected = ['calls' => 0, 'fanout' => 3];
        $actual = $this->calculateProjectMetrics();

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * properties.
     */
    public function testAnalyzerCalculatesCorrectClassCoupling(): void
    {
        $expected = ['calls' => 10, 'fanout' => 12];
        $actual = $this->calculateProjectMetrics();

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * complete source.
     */
    public function testAnalyzerCalculatesCorrectCoupling(): void
    {
        $expected = ['calls' => 30, 'fanout' => 31];
        $actual = $this->calculateProjectMetrics();

        static::assertEquals($expected, $actual);
    }

    /**
     * testGetNodeMetricsForTrait
     *
     * @return array<mixed>
     * @since 1.0.6
     */
    public function testGetNodeMetricsForTrait(): array
    {
        $metrics = $this->calculateTraitMetrics();
        static::assertIsArray($metrics);

        return $metrics;
    }

    /**
     * testGetNodeMetricsForTraitReturnsExpectedMetricSet
     *
     * @param array<string, mixed> $metrics Calculated coupling metrics.
     * @since 1.0.6
     *
     * @depends testGetNodeMetricsForTrait
     */
    public function testGetNodeMetricsForTraitReturnsExpectedMetricSet(array $metrics): void
    {
        static::assertEquals(['ca', 'cbo', 'ce'], array_keys($metrics));
    }

    /**
     * testCalculateCEMetricForTrait
     *
     * @param array<string, int> $metrics Calculated coupling metrics.
     * @since 1.0.6
     *
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateCEMetricForTrait(array $metrics): void
    {
        static::assertEquals(4, $metrics['ce']);
    }

    /**
     * testCalculateCBOMetricForTrait
     *
     * @param array<string, int> $metrics Calculated coupling metrics.
     * @since 1.0.6
     *
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateCBOMetricForTrait(array $metrics): void
    {
        static::assertEquals(4, $metrics['cbo']);
    }

    /**
     * testCalculateCAMetricForTrait
     *
     * @param array<string, int> $metrics Calculated coupling metrics.
     * @since 1.0.6
     *
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateCAMetricForTrait(array $metrics): void
    {
        static::assertEquals(0, $metrics['ca']);
    }

    /**
     * testGetProjectMetricsForTrait
     *
     * @return array<mixed>
     * @since 1.0.6
     */
    public function testGetProjectMetricsForTrait(): array
    {
        $analyzer = new CouplingAnalyzer();
        $analyzer->analyze($this->parseCodeResourceForTest());

        $metrics = $analyzer->getProjectMetrics();
        static::assertIsArray($metrics);

        return $metrics;
    }

    /**
     * testGetProjectMetricsForTraitReturnsExpectedMetricSet
     *
     * @param array<string, mixed> $metrics Calculated coupling metrics.
     * @since 1.0.6
     *
     * @depends testGetProjectMetricsForTrait
     */
    public function testGetProjectMetricsForTraitReturnsExpectedMetricSet(array $metrics): void
    {
        static::assertEquals(['calls', 'fanout'], array_keys($metrics));
    }

    /**
     * testCalculateCallsMetricForTrait
     *
     * @param array<string, int> $metrics Calculated coupling metrics.
     * @since 1.0.6
     *
     * @depends testGetProjectMetricsForTrait
     */
    public function testCalculateCallsMetricForTrait(array $metrics): void
    {
        static::assertEquals(7, $metrics['calls']);
    }

    /**
     * testCalculateFanoutMetricForTrait
     *
     * @param array<string, int> $metrics Calculated coupling metrics.
     * @since 1.0.6
     * @depends testGetProjectMetricsForTrait
     */
    public function testCalculateFanoutMetricForTrait(array $metrics): void
    {
        static::assertEquals(4, $metrics['fanout']);
    }

    /**
     * Analyzes the source code associated with the calling test method and
     * returns all measured metrics.
     *
     * @return array<string, mixed>
     * @since 1.0.6
     */
    private function calculateTraitMetrics(): array
    {
        $namespaces = $this->parseCodeResourceForTest();

        $analyzer = new CouplingAnalyzer();
        $analyzer->analyze($namespaces);

        return $analyzer->getNodeMetrics($namespaces[0]->getTraits()->current());
    }

    /**
     * Tests that the analyzer calculates the expected call count.
     *
     * @param string $testCase File with test source.
     * @param int $calls Number of expected calls.
     * @param int $fanout Expected fanout value.
     * @dataProvider dataProviderAnalyzerCalculatesExpectedCallCount
     */
    public function testAnalyzerCalculatesExpectedCallCount(
        string $testCase,
        int $calls,
        int $fanout
    ): void {
        $expected = ['calls' => $calls, 'fanout' => $fanout];
        $actual = $this->calculateProjectMetrics($testCase);

        static::assertEquals($expected, $actual);
    }

    /**
     * Parses the source code for the currently calling test method and returns
     * the calculated project metrics.
     *
     * @param string $testCase Optional name of the calling test case.
     * @return array<string, mixed>
     * @since 0.10.2
     */
    private function calculateProjectMetrics(?string $testCase = null): array
    {
        $testCase = ($testCase ?: $this->getCallingTestMethod());

        $analyzer = new CouplingAnalyzer();
        $analyzer->analyze($this->parseTestCaseSource($testCase));

        return $analyzer->getProjectMetrics();
    }

    /**
     * Data provider that returns different test files and the corresponding
     * invocation count value.
     *
     * @return list<mixed>
     */
    public static function dataProviderAnalyzerCalculatesExpectedCallCount(): array
    {
        return [
            [__METHOD__ . '#01', 0, 0],
            [__METHOD__ . '#02', 0, 0],
            [__METHOD__ . '#03', 0, 0],
            [__METHOD__ . '#04', 1, 0],
            [__METHOD__ . '#05', 1, 0],
            [__METHOD__ . '#06', 2, 0],
            [__METHOD__ . '#07', 1, 0],
            [__METHOD__ . '#08', 1, 0],
            [__METHOD__ . '#09', 1, 0],
            [__METHOD__ . '#10', 2, 0],
            [__METHOD__ . '#11', 2, 0],
            [__METHOD__ . '#12', 1, 1],
            [__METHOD__ . '#13', 0, 1],
            [__METHOD__ . '#14', 0, 1],
            [__METHOD__ . '#15', 1, 1],
            [__METHOD__ . '#16', 2, 1],
            [__METHOD__ . '#17', 4, 2],
            [__METHOD__ . '#18', 1, 0],
            [__METHOD__ . '#19', 1, 1],
        ];
    }
}
