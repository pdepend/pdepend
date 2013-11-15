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
use PDepend\Source\AST\ASTArtifactList\CollectionArtifactFilter;
use PDepend\Source\AST\ASTArtifactList\PackageArtifactFilter;
use PDepend\Source\AST\ASTClass;

/**
 * Test case for the inheritance analyzer.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @covers \PDepend\Metrics\Analyzer\InheritanceAnalyzer
 * @group unittest
 */
class InheritanceAnalyzerTest extends AbstractMetricsTest
{
    /**
     * Tests that the analyzer calculates the correct average number of derived
     * classes.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectANDCValue()
    {
        $filter = CollectionArtifactFilter::getInstance();
        $filter->setFilter(new PackageArtifactFilter(array('library')));

        $namespaces = self::parseTestCaseSource(__METHOD__);
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $project = $analyzer->getProjectMetrics();

        $this->assertEquals(0.7368, $project['andc'], null, 0.0001);
    }

    /**
     * Tests that the analyzer calculates the correct average hierarchy height.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectAHHValue()
    {
        $filter = CollectionArtifactFilter::getInstance();
        $filter->setFilter(new PackageArtifactFilter(array('library')));

        $namespaces = self::parseTestCaseSource(__METHOD__);
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $project = $analyzer->getProjectMetrics();

        $this->assertEquals(1, $project['ahh']);
    }

    /**
     * testCalculatesExpectedNoccMetricForClassWithoutChildren
     *
     * @return void
     */
    public function testCalculatesExpectedNoccMetricForClassWithoutChildren()
    {
        $this->assertEquals(0, $this->_getCalculatedMetric(__METHOD__, 'nocc'));
    }

    /**
     * testCalculatesExpectedNoccMetricForClassWithDirectChildren
     *
     * @return void
     */
    public function testCalculatesExpectedNoccMetricForClassWithDirectChildren()
    {
        $this->assertEquals(3, $this->_getCalculatedMetric(__METHOD__, 'nocc'));
    }

    /**
     * testCalculatesExpectedNoccMetricForClassWithDirectAndIndirectChildren
     *
     * @return void
     */
    public function testCalculatesExpectedNoccMetricForClassWithDirectAndIndirectChildren()
    {
        $this->assertEquals(1, $this->_getCalculatedMetric(__METHOD__, 'nocc'));
    }

    /**
     * Tests that the analyzer calculates the correct DIT values.
     *
     * @return void
     */
    public function testCalculateDITMetricNoInheritance()
    {
        $this->assertEquals(0, $this->_getCalculatedMetric(__METHOD__, 'dit'));
    }

    /**
     * Tests that the analyzer calculates the correct DIT values.
     *
     * @return void
     */
    public function testCalculateDITMetricOneLevelInheritance()
    {
        $this->assertEquals(1, $this->_getCalculatedMetric(__METHOD__, 'dit'));
    }

    /**
     * Tests that the analyzer calculates the correct DIT values.
     *
     * @return void
     */
    public function testCalculateDITMetricTwoLevelNoInheritance()
    {
        $this->assertEquals(2, $this->_getCalculatedMetric(__METHOD__, 'dit'));
    }

    /**
     * Tests that the analyzer calculates the correct DIT values.
     *
     * @return void
     */
    public function testCalculateDITMetricThreeLevelNoInheritance()
    {
        $this->assertEquals(3, $this->_getCalculatedMetric(__METHOD__, 'dit'));
    }

    /**
     * Tests that the analyzer calculates the correct DIT values.
     *
     * @return void
     */
    public function testCalculateDITMetricFourLevelNoInheritance()
    {
        $this->assertEquals(4, $this->_getCalculatedMetric(__METHOD__, 'dit'));
    }

    /**
     * testCalculateDITMetricForUnknownParentIncrementsMetricWithTwo
     *
     * @return void
     */
    public function testCalculateDITMetricForUnknownParentIncrementsMetricWithTwo()
    {
        $this->assertEquals(3, $this->_getCalculatedMetric(__METHOD__, 'dit'));
    }

    /**
     * testCalculateDITMetricForInternalParentIncrementsMetricWithTwo
     *
     * @return void
     */
    public function testCalculateDITMetricForInternalParentIncrementsMetricWithTwo()
    {
        $this->assertEquals(3, $this->_getCalculatedMetric(__METHOD__, 'dit'));
    }

    /**
     * Tests that {@link \PDepend\Metrics\Analyzer\InheritanceAnalyzer::analyze()}
     * calculates the expected DIT values.
     *
     * @return void
     */
    public function testCalculateDepthOfInheritanceForSeveralClasses()
    {
        $namespaces = $this->parseCodeResourceForTest();

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $actual = array();
        foreach ($namespaces[0]->getClasses() as $class) {
            $metrics = $analyzer->getNodeMetrics($class);
            
            $actual[$class->getName()] = $metrics['dit'];
        }
        ksort($actual);

        $expected = array(
            'A' => 0,
            'B' => 1,
            'C' => 1,
            'D' => 2,
            'E' => 3,
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testCalculatesExpectedMaxDepthOfInheritanceTreeMetric
     *
     * @return void
     */
    public function testCalculatesExpectedMaxDepthOfInheritanceTreeMetric()
    {
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $metrics = $analyzer->getProjectMetrics();
        $this->assertEquals(3, $metrics['maxDIT']);
    }

    /**
     * testCalculatesExpectedNoamMetricForClassWithoutParent
     *
     * @return void
     */
    public function testCalculatesExpectedNoamMetricForClassWithoutParent()
    {
        $this->assertEquals(0, $this->_getCalculatedMetric(__METHOD__, 'noam'));
    }

    /**
     * testCalculatesExpectedNoamMetricForClassWithDirectParent
     *
     * @return void
     */
    public function testCalculatesExpectedNoamMetricForClassWithDirectParent()
    {
        $this->assertEquals(2, $this->_getCalculatedMetric(__METHOD__, 'noam'));
    }

    /**
     * testCalculatesExpectedNoamMetricForClassWithIndirectParent
     *
     * @return void
     */
    public function testCalculatesExpectedNoamMetricForClassWithIndirectParent()
    {
        $this->assertEquals(2, $this->_getCalculatedMetric(__METHOD__, 'noam'));
    }

    /**
     * testCalculatesExpectedNoomMetricForClassWithoutParent
     *
     * @return void
     */
    public function testCalculatesExpectedNoomMetricForClassWithoutParent()
    {
        $this->assertEquals(0, $this->_getCalculatedMetric(__METHOD__, 'noom'));
    }

    /**
     * testCalculatesExpectedNoomMetricForClassWithParent
     *
     * @return void
     */
    public function testCalculatesExpectedNoomMetricForClassWithParent()
    {
        $this->assertEquals(2, $this->_getCalculatedMetric(__METHOD__, 'noom'));
    }

    /**
     * testCalculatesExpectedNoomMetricForClassWithParentPrivateMethods
     *
     * @return void
     */
    public function testCalculatesExpectedNoomMetricForClassWithParentPrivateMethods()
    {
        $this->assertEquals(1, $this->_getCalculatedMetric(__METHOD__, 'noom'));
    }

    /**
     * testAnalyzerIgnoresClassesThatAreNotUserDefined
     *
     * @return void
     */
    public function testAnalyzerIgnoresClassesThatAreNotUserDefined()
    {
        $class = new ASTClass(null);

        $analyzer = $this->createAnalyzer();
        $analyzer->visitClass($class);

        $metrics = $analyzer->getNodeMetrics($class);
        $this->assertEquals(array(), $metrics);
    }

    /**
     * Analyzes the source associated with the calling test and returns the
     * calculated metric value.
     *
     * @param string $testCase Name of the calling test case.
     * @param string $metric   Name of the searched metric.
     *
     * @return mixed
     */
    private function _getCalculatedMetric($testCase, $metric)
    {
        $namespaces = self::parseTestCaseSource($testCase);
        $namespace  = $namespaces->current();

        $analyzer = $this->createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = $analyzer->getNodeMetrics($namespace->getClasses()->current());
        return $metrics[$metric];
    }

    /**
     * @return \PDepend\Metrics\Analyzer\InheritanceAnalyzer
     */
    private function createAnalyzer()
    {
        return new InheritanceAnalyzer();
    }
}
