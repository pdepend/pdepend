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
use PDepend\Metrics\Analyzer\CouplingAnalyzer;

/**
 * Test case for the coupling analyzer.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Metrics\Analyzer\CouplingAnalyzer
 * @group unittest
 */
class CouplingAnalyzerTest extends AbstractMetricsTest
{
    /**
     * testGetNodeMetricsReturnsAnEmptyArrayByDefault
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsAnEmptyArrayByDefault()
    {
        $analyzer = new CouplingAnalyzer();
        $this->assertEquals(
            array(),
            $analyzer->getNodeMetrics(
                $this->getMock('\\PDepend\\Source\\AST\\ASTArtifact')
            )
        );
    }

    /**
     * testGetNodeMetricsReturnsArrayWithExpectedSetOfMetrics
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsArrayWithExpectedSetOfMetrics()
    {
        $namespaces = self::parseCodeResourceForTest();

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
    public function testGetNodeMetricsReturnsExpectedCaWithoutDependencies()
    {
        $this->assertEquals(0, $this->_calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaWithObjectInstantiation
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaWithObjectInstantiation()
    {
        $this->assertEquals(1, $this->_calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaWithStaticReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaWithStaticReference()
    {
        $this->assertEquals(1, $this->_calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaWithReturnReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaWithReturnReference()
    {
        $this->assertEquals(1, $this->_calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaWithExceptionReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaWithExceptionReference()
    {
        $this->assertEquals(2, $this->_calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaWithPropertyReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaWithPropertyReference()
    {
        $this->assertEquals(1, $this->_calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaWithoutDuplicateCount
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaWithoutDuplicateCount()
    {
        $this->assertEquals(2, $this->_calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForParameterTypes
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaForParameterTypes()
    {
        $this->assertEquals(3, $this->_calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForParentTypeReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaForParentTypeReference()
    {
        $this->assertEquals(0, $this->_calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForChildTypeReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaForChildTypeReference()
    {
        $this->assertEquals(2, $this->_calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForFunctionReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaForFunctionReference()
    {
        $this->assertEquals(1, $this->_calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForFunctionException
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaForFunctionException()
    {
        $this->assertEquals(1, $this->_calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForFunctionReturnType
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaForFunctionReturnType()
    {
        $this->assertEquals(1, $this->_calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForFunctionParameter
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaForFunctionParameter()
    {
        $this->assertEquals(1, $this->_calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForFunctions
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaForFunctions()
    {
        $this->assertEquals(3, $this->_calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCaForFunctionCountsTypeOnce
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCaForFunctionCountsTypeOnce()
    {
        $this->assertEquals(2, $this->_calculateTypeMetric('ca'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboWithoutDependencies
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboWithoutDependencies()
    {
        $this->assertEquals(0, $this->_calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboWithObjectInstantiation
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboWithObjectInstantiation()
    {
        $this->assertEquals(1, $this->_calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboWithStaticReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboWithStaticReference()
    {
        $this->assertEquals(1, $this->_calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboWithReturnReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboWithReturnReference()
    {
        $this->assertEquals(1, $this->_calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboWithExceptionReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboWithExceptionReference()
    {
        $this->assertEquals(2, $this->_calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboWithPropertyReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboWithPropertyReference()
    {
        $this->assertEquals(1, $this->_calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboWithoutDuplicateCount
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboWithoutDuplicateCount()
    {
        $this->assertEquals(2, $this->_calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboForParameterTypes
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboForParameterTypes()
    {
        $this->assertEquals(3, $this->_calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboForParentTypeReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboForParentTypeReference()
    {
        $this->assertEquals(0, $this->_calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboForChildTypeReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboForChildTypeReference()
    {
        $this->assertEquals(2, $this->_calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboForUseInSameNamespace
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboForUseInSameNamespace()
    {
        $this->assertEquals(1, $this->_calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCboForUseInPartialSameNamespace
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCboForUseInPartialSameNamespace()
    {
        $this->assertEquals(1, $this->_calculateTypeMetric('cbo'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeWithoutDependencies
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeWithoutDependencies()
    {
        $this->assertEquals(0, $this->_calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeWithObjectInstantiation
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeWithObjectInstantiation()
    {
        $this->assertEquals(1, $this->_calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeWithStaticReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeWithStaticReference()
    {
        $this->assertEquals(1, $this->_calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeWithReturnReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeWithReturnReference()
    {
        $this->assertEquals(1, $this->_calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeWithExceptionReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeWithExceptionReference()
    {
        $this->assertEquals(2, $this->_calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeWithPropertyReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeWithPropertyReference()
    {
        $this->assertEquals(1, $this->_calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeWithoutDuplicateCount
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeWithoutDuplicateCount()
    {
        $this->assertEquals(2, $this->_calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeForParameterTypes
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeForParameterTypes()
    {
        $this->assertEquals(3, $this->_calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeForParentTypeReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeForParentTypeReference()
    {
        $this->assertEquals(0, $this->_calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeForChildTypeReference
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeForChildTypeReference()
    {
        $this->assertEquals(2, $this->_calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeForUseInSameNamespace
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeForUseInSameNamespace()
    {
        $this->assertEquals(1, $this->_calculateTypeMetric('ce'));
    }

    /**
     * testGetNodeMetricsReturnsExpectedCeForUseInPartialSameNamespace
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsExpectedCeForUseInPartialSameNamespace()
    {
        $this->assertEquals(1, $this->_calculateTypeMetric('ce'));
    }

    /**
     * Returns the specified node metric for the first type found in the
     * analyzed test source and returns the metric value for the given <b>$name</b>.
     *
     * @param string $name Name of the requested software metric.
     *
     * @return mixed
     */
    private function _calculateTypeMetric($name)
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
    public function testAnalyzerGetProjectMetricsReturnsArrayWithExpectedKeys()
    {
        $expected = array('calls', 'fanout');
        $actual   = array_keys($this->_calculateProjectMetrics());

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * functions.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectFunctionCoupling()
    {
        $expected = array('calls' => 10, 'fanout' => 7);
        $actual   = $this->_calculateProjectMetrics();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * methods.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectMethodCoupling()
    {
        $expected = array('calls' => 10, 'fanout' => 9);
        $actual   = $this->_calculateProjectMetrics();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * properties.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectPropertyCoupling()
    {
        $expected = array('calls' => 0, 'fanout' => 3);
        $actual   = $this->_calculateProjectMetrics();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * properties.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectClassCoupling()
    {
        $expected = array('calls' => 10, 'fanout' => 12);
        $actual   = $this->_calculateProjectMetrics();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * complete source.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectCoupling()
    {
        $expected = array('calls' => 30, 'fanout' => 31);
        $actual   = $this->_calculateProjectMetrics();

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
        $metrics = $this->_calculateTraitMetrics();
        $this->assertInternalType('array', $metrics);

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
    public function testGetNodeMetricsForTraitReturnsExpectedMetricSet(array $metrics)
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
    public function testCalculateCEMetricForTrait(array $metrics)
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
    public function testCalculateCBOMetricForTrait(array $metrics)
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
    public function testCalculateCAMetricForTrait(array $metrics)
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
        $this->assertInternalType('array', $metrics);

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
    public function testGetProjectMetricsForTraitReturnsExpectedMetricSet(array $metrics)
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
    public function testCalculateCallsMetricForTrait(array $metrics)
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
    public function testCalculateFanoutMetricForTrait(array $metrics)
    {
        $this->assertEquals(4, $metrics['fanout']);
    }

    /**
     * Analyzes the source code associated with the calling test method and
     * returns all measured metrics.
     *
     * @return mixed
     * @since 1.0.6
     */
    private function _calculateTraitMetrics()
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
    ) {
        $expected = array('calls' => $calls, 'fanout' => $fanout);
        $actual   = $this->_calculateProjectMetrics($testCase);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Parses the source code for the currently calling test method and returns
     * the calculated project metrics.
     *
     * @param string $testCase Optional name of the calling test case.
     *
     * @return array(string=>mixed)
     * @since 0.10.2
     */
    private function _calculateProjectMetrics($testCase = null)
    {
        $testCase = ($testCase ? $testCase : self::getCallingTestMethod());

        $analyzer = new CouplingAnalyzer();
        $analyzer->analyze(self::parseTestCaseSource($testCase));

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
