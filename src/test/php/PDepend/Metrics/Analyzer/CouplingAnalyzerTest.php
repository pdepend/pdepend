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
use PDepend\Metrics\Analyzer\CouplingAnalyzer;

/**
 * Test case for the coupling analyzer.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Metrics\Analyzer\CouplingAnalyzer
 * @group unittest
 */
class CouplingAnalyzerTest extends AbstractMetricsTestCase
{
    /**
     * testGetNodeMetricsReturnsAnEmptyArrayByDefault
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsAnEmptyArrayByDefault(): void
    {
        $astArtifact = $this->getMockBuilder('\\PDepend\\Source\\AST\\ASTArtifact')
            ->getMock();

        $analyzer = new CouplingAnalyzer();
        $this->assertEquals(
            array(),
            $analyzer->getNodeMetrics($astArtifact)
        );
    }

    /**
     * testGetNodeMetricsReturnsArrayWithExpectedSetOfMetrics
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsArrayWithExpectedSetOfMetrics(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $classes = $namespaces[0]->getClasses();

        $analyzer = new CouplingAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = array_keys($analyzer->getNodeMetrics($classes[0]));
        sort($metrics);

        $this->assertEquals(array('ca', 'cbo', 'ce'), $metrics);
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaWithoutDependencies
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaWithoutDependencies(): void
    {
        $this->assertEquals(0, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaWithObjectInstantiation
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaWithObjectInstantiation(): void
    {
        $this->assertEquals(1, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaWithStaticReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaWithStaticReference(): void
    {
        $this->assertEquals(1, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaWithReturnReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaWithReturnReference(): void
    {
        $this->assertEquals(1, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaWithExceptionReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaWithExceptionReference(): void
    {
        $this->assertEquals(2, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaWithPropertyReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaWithPropertyReference(): void
    {
        $this->assertEquals(1, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaWithoutDuplicateCount
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaWithoutDuplicateCount(): void
    {
        $this->assertEquals(2, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForParameterTypes
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaForParameterTypes(): void
    {
        $this->assertEquals(3, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForParentTypeReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaForParentTypeReference(): void
    {
        $this->assertEquals(0, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForChildTypeReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaForChildTypeReference(): void
    {
        $this->assertEquals(2, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForFunctionReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaForFunctionReference(): void
    {
        $this->assertEquals(1, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForFunctionException
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaForFunctionException(): void
    {
        $this->assertEquals(1, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForFunctionReturnType
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaForFunctionReturnType(): void
    {
        $this->assertEquals(1, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForFunctionParameter
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaForFunctionParameter(): void
    {
        $this->assertEquals(1, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForFunctions
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaForFunctions(): void
    {
        $this->assertEquals(3, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForFunctionCountsTypeOnce
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaForFunctionCountsTypeOnce(): void
    {
        $this->assertEquals(2, $this->calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboWithoutDependencies
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboWithoutDependencies(): void
    {
        $this->assertEquals(0, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboWithObjectInstantiation
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboWithObjectInstantiation(): void
    {
        $this->assertEquals(1, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboWithStaticReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboWithStaticReference(): void
    {
        $this->assertEquals(1, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboWithReturnReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboWithReturnReference(): void
    {
        $this->assertEquals(1, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboWithExceptionReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboWithExceptionReference(): void
    {
        $this->assertEquals(2, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboWithPropertyReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboWithPropertyReference(): void
    {
        $this->assertEquals(1, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboWithoutDuplicateCount
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboWithoutDuplicateCount(): void
    {
        $this->assertEquals(2, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboForParameterTypes
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboForParameterTypes(): void
    {
        $this->assertEquals(3, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboForParentTypeReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboForParentTypeReference(): void
    {
        $this->assertEquals(0, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboForChildTypeReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboForChildTypeReference(): void
    {
        $this->assertEquals(2, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboForUseInSameNamespace
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboForUseInSameNamespace(): void
    {
        $this->assertEquals(1, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboForUseInPartialSameNamespace
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboForUseInPartialSameNamespace(): void
    {
        $this->assertEquals(1, $this->calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeWithoutDependencies
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeWithoutDependencies(): void
    {
        $this->assertEquals(0, $this->calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeWithObjectInstantiation
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeWithObjectInstantiation(): void
    {
        $this->assertEquals(1, $this->calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeWithStaticReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeWithStaticReference(): void
    {
        $this->assertEquals(1, $this->calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeWithReturnReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeWithReturnReference(): void
    {
        $this->assertEquals(1, $this->calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeWithExceptionReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeWithExceptionReference(): void
    {
        $this->assertEquals(2, $this->calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeWithPropertyReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeWithPropertyReference(): void
    {
        $this->assertEquals(1, $this->calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeWithoutDuplicateCount
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeWithoutDuplicateCount(): void
    {
        $this->assertEquals(2, $this->calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeForParameterTypes
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeForParameterTypes(): void
    {
        $this->assertEquals(3, $this->calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeForParentTypeReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeForParentTypeReference(): void
    {
        $this->assertEquals(0, $this->calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeForChildTypeReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeForChildTypeReference(): void
    {
        $this->assertEquals(2, $this->calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeForUseInSameNamespace
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeForUseInSameNamespace(): void
    {
        $this->assertEquals(1, $this->calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeForUseInPartialSameNamespace
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeForUseInPartialSameNamespace(): void
    {
        $this->assertEquals(1, $this->calculateTypeMetric('ce'));
    }

    /**
     * Returns the specified node metric for the first type found in the
     * analyzed test source and returns the metric value for the given <b>$name</b>.
     *
     * @param string $name Name of the requested software metric.
     */
    private function calculateTypeMetric($name): mixed
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
     *
     * @return void
     */
    public function testAnalyzerGetProjectMetricsReturnsArrayWithExpectedKeys(): void
    {
        $expected = array('calls', 'fanout');
        $actual   = array_keys($this->calculateProjectMetrics());

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * functions.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectFunctionCoupling(): void
    {
        $expected = array('calls' => 10, 'fanout' => 7);
        $actual   = $this->calculateProjectMetrics();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * methods.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectMethodCoupling(): void
    {
        $expected = array('calls' => 10, 'fanout' => 9);
        $actual   = $this->calculateProjectMetrics();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * properties.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectPropertyCoupling(): void
    {
        $expected = array('calls' => 0, 'fanout' => 3);
        $actual   = $this->calculateProjectMetrics();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * properties.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectClassCoupling(): void
    {
        $expected = array('calls' => 10, 'fanout' => 12);
        $actual   = $this->calculateProjectMetrics();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * complete source.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectCoupling(): void
    {
        $expected = array('calls' => 30, 'fanout' => 31);
        $actual   = $this->calculateProjectMetrics();

        $this->assertEquals($expected, $actual);
    }

    /**
     * testGetNodeMetricsForTrait
     *
     * @return array
     * @since 1.0.6
     */
    public function testGetNodeMetricsForTrait()
    {
        $metrics = $this->calculateTraitMetrics();
        $this->assertIsArray($metrics);

        return $metrics;
    }

    /**
     * testGetNodeMetricsForTraitReturnsExpectedMetricSet
     *
     * @param array $metrics Calculated coupling metrics.
     *
     * @return void
     * @since 1.0.6
     * @depends testGetNodeMetricsForTrait
     */
    public function testGetNodeMetricsForTraitReturnsExpectedMetricSet(array $metrics): void
    {
        $this->assertEquals(array('ca', 'cbo', 'ce'), array_keys($metrics));
    }

    /**
     * testCalculateCEMetricForTrait
     *
     * @param array $metrics Calculated coupling metrics.
     *
     * @return void
     * @since 1.0.6
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateCEMetricForTrait(array $metrics): void
    {
        $this->assertEquals(4, $metrics['ce']);
    }

    /**
     * testCalculateCBOMetricForTrait
     *
     * @param array $metrics Calculated coupling metrics.
     *
     * @return void
     * @since 1.0.6
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateCBOMetricForTrait(array $metrics): void
    {
        $this->assertEquals(4, $metrics['cbo']);
    }

    /**
     * testCalculateCAMetricForTrait
     *
     * @param array $metrics Calculated coupling metrics.
     *
     * @return void
     * @since 1.0.6
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateCAMetricForTrait(array $metrics): void
    {
        $this->assertEquals(0, $metrics['ca']);
    }

    /**
     * testGetProjectMetricsForTrait
     *
     * @return array
     * @since 1.0.6
     */
    public function testGetProjectMetricsForTrait()
    {
        $analyzer = new CouplingAnalyzer();
        $analyzer->analyze($this->parseCodeResourceForTest());

        $metrics = $analyzer->getProjectMetrics();
        $this->assertIsArray($metrics);

        return $metrics;
    }

    /**
     * testGetProjectMetricsForTraitReturnsExpectedMetricSet
     *
     * @param array $metrics Calculated coupling metrics.
     *
     * @return void
     * @since 1.0.6
     * @depends testGetProjectMetricsForTrait
     */
    public function testGetProjectMetricsForTraitReturnsExpectedMetricSet(array $metrics): void
    {
        $this->assertEquals(array('calls', 'fanout'), array_keys($metrics));
    }

    /**
     * testCalculateCallsMetricForTrait
     *
     * @param array $metrics Calculated coupling metrics.
     *
     * @return void
     * @since 1.0.6
     * @depends testGetProjectMetricsForTrait
     */
    public function testCalculateCallsMetricForTrait(array $metrics): void
    {
        $this->assertEquals(7, $metrics['calls']);
    }

    /**
     * testCalculateFanoutMetricForTrait
     *
     * @param array $metrics Calculated coupling metrics.
     *
     * @return void
     * @since 1.0.6
     * @depends testGetProjectMetricsForTrait
     */
    public function testCalculateFanoutMetricForTrait(array $metrics): void
    {
        $this->assertEquals(4, $metrics['fanout']);
    }

    /**
     * Analyzes the source code associated with the calling test method and
     * returns all measured metrics.
     *
     * @return array<string, mixed>
     * @since 1.0.6
     */
    private function calculateTraitMetrics()
    {
        $namespaces = $this->parseCodeResourceForTest();

        $analyzer = new CouplingAnalyzer();
        $analyzer->analyze($namespaces);

        return $analyzer->getNodeMetrics($namespaces[0]->getTraits()->current());
    }

    /**
     * Tests that the analyzer calculates the expected call count.
     *
     * @param string  $testCase File with test source.
     * @param integer $calls    Number of expected calls.
     * @param integer $fanout   Expected fanout value.
     *
     * @return void
     * @dataProvider dataProviderAnalyzerCalculatesExpectedCallCount
     */
    public function testAnalyzerCalculatesExpectedCallCount(
        $testCase,
        $calls,
        $fanout
    ): void {
        $expected = ['calls' => $calls, 'fanout' => $fanout];
        $actual   = $this->calculateProjectMetrics($testCase);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Parses the source code for the currently calling test method and returns
     * the calculated project metrics.
     *
     * @param string $testCase Optional name of the calling test case.
     *
     * @return array<string, mixed>
     * @since 0.10.2
     */
    private function calculateProjectMetrics($testCase = null)
    {
        $testCase = ($testCase ? $testCase : $this->getCallingTestMethod());

        $analyzer = new CouplingAnalyzer();
        $analyzer->analyze($this->parseTestCaseSource($testCase));

        return $analyzer->getProjectMetrics();
    }

    /**
     * Data provider that returns different test files and the corresponding
     * invocation count value.
     *
     * @return array
     */
    public static function dataProviderAnalyzerCalculatesExpectedCallCount()
    {
        return array(
            array(__METHOD__ . '#01', 0, 0),
            array(__METHOD__ . '#02', 0, 0),
            array(__METHOD__ . '#03', 0, 0),
            array(__METHOD__ . '#04', 1, 0),
            array(__METHOD__ . '#05', 1, 0),
            array(__METHOD__ . '#06', 2, 0),
            array(__METHOD__ . '#07', 1, 0),
            array(__METHOD__ . '#08', 1, 0),
            array(__METHOD__ . '#09', 1, 0),
            array(__METHOD__ . '#10', 2, 0),
            array(__METHOD__ . '#11', 2, 0),
            array(__METHOD__ . '#12', 1, 1),
            array(__METHOD__ . '#13', 0, 1),
            array(__METHOD__ . '#14', 0, 1),
            array(__METHOD__ . '#15', 1, 1),
            array(__METHOD__ . '#16', 2, 1),
            array(__METHOD__ . '#17', 4, 2),
            array(__METHOD__ . '#18', 1, 0),
            array(__METHOD__ . '#19', 1, 1),
        );
    }
}
