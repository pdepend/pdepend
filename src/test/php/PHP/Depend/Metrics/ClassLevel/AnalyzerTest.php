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
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for the class level analyzer.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 *
 * @covers PHP_Depend_Metrics_ClassLevel_Analyzer
 * @group pdepend
 * @group pdepend::metrics
 * @group pdepend::metrics::classlevel
 * @group unittest
 */
class PHP_Depend_Metrics_ClassLevel_AnalyzerTest extends PHP_Depend_Metrics_AbstractTest
{
    /**
     * Tests that the {@link PHP_Depend_Metrics_ClassLevel_Analyzer::analyzer()}
     * method fails with an exception if no cc analyzer was set.
     *
     * @return void
     * @expectedException RuntimeException
     */
    public function testAnalyzerFailsWithoutCCAnalyzerFail()
    {
        $package  = new PHP_Depend_Code_Package('package1');
        $packages = new PHP_Depend_Code_NodeIterator(array($package));

        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->analyze($packages);
    }

    /**
     * Tests that {@link PHP_Depend_Metrics_ClassLevel_Analyzer::addAnalyzer()}
     * fails for an invalid child analyzer.
     *
     * @return void
     * @expectedException InvalidArgumentException
     */
    public function testAddAnalyzerFailsForAnInvalidAnalyzerTypeFail()
    {
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CodeRank_Analyzer());
    }

    /**
     * testGetRequiredAnalyzersReturnsExpectedClassNames
     *
     * @return void
     */
    public function testGetRequiredAnalyzersReturnsExpectedClassNames()
    {
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        self::assertEquals(
            array(PHP_Depend_Metrics_CyclomaticComplexity_Analyzer::CLAZZ),
            $analyzer->getRequiredAnalyzers()
        );
    }

    /**
     * testGetNodeMetricsReturnsArrayWithExpectedSetOfMetrics
     *
     * @return void
     */
    public function testGetNodeMetricsReturnsArrayWithExpectedSetOfMetrics()
    {
        self::assertEquals(
            array('impl', 'cis', 'csz', 'npm', 'vars', 'varsi', 'varsnp', 'wmc', 'wmci', 'wmcnp'),
            array_keys($this->_calculateClassMetrics())
        );
    }

    /**
     * Tests that the analyzer calculates the correct IMPL values.
     *
     * @return void
     */
    public function testCalculateIMPLMetric()
    {
        self::assertEquals(4, $this->_calculateClassMetric('impl'));
    }

    /**
     * Tests that the analyzer calculates the correct IMPL values.
     *
     * @return void
     */
    public function testCalculateIMPLMetric1()
    {
        self::assertEquals(6, $this->_calculateClassMetric('impl'));
    }

    /**
     * Tests that the analyzer calculates the correct IMPL values.
     *
     * @return void
     */
    public function testCalculateIMPLMetric2()
    {
        self::assertEquals(2, $this->_calculateClassMetric('impl'));
    }

    /**
     * testCalculateIMPLMetricContainsUnknownImplementedInterface
     *
     * @return void
     */
    public function testCalculateIMPLMetricContainsUnknownImplementedInterface()
    {
        self::assertEquals(1, $this->_calculateClassMetric('impl'));
    }

    /**
     * testCalculateIMPLMetricContainsUnknownIndirectImplementedInterface
     *
     * @return void
     */
    public function testCalculateIMPLMetricContainsUnknownIndirectImplementedInterface()
    {
        self::assertEquals(1, $this->_calculateClassMetric('impl'));
    }

    /**
     * testCalculateIMPLMetricContainsInternalImplementedInterface
     *
     * @return void
     */
    public function testCalculateIMPLMetricContainsInternalImplementedInterface()
    {
        self::assertEquals(1, $this->_calculateClassMetric('impl'));
    }

    /**
     * Tests that the calculated Class Interface Size(CSI) is correct.
     *
     * @return void
     */
    public function testCalculateCISMetricZeroInheritance()
    {
        self::assertEquals(2, $this->_calculateClassMetric('cis'));
    }

    /**
     * Tests that the calculated Class Interface Size(CSI) is correct.
     *
     * @return void
     */
    public function testCalculateCISMetricOneLevelInheritance()
    {
        self::assertEquals(2, $this->_calculateClassMetric('cis'));
    }

    /**
     * Tests that the calculated Class Interface Size(CSI) is correct.
     *
     * @return void
     */
    public function testCalculateCISMetricTwoLevelInheritance()
    {
        self::assertEquals(3, $this->_calculateClassMetric('cis'));
    }

    /**
     * testCalculateCISMetricOnlyCountsMethodsAndNotSumsComplexity
     *
     * @return void
     */
    public function testCalculateCISMetricOnlyCountsMethodsAndNotSumsComplexity()
    {
        self::assertEquals(2, $this->_calculateClassMetric('cis'));
    }

    /**
     * Tests that the calculated Class SiZe(CSZ) metric is correct.
     *
     * @return void
     */
    public function testCalculateCSZMetricZeroInheritance()
    {
        self::assertEquals(6, $this->_calculateClassMetric('csz'));
    }

    /**
     * Tests that the calculated Class SiZe(CSZ) metric is correct.
     *
     * @return void
     */
    public function testCalculateCSZMetricOneLevelInheritance()
    {
        self::assertEquals(4, $this->_calculateClassMetric('csz'));
    }

    /**
     * testCalculateCSZMetricOnlyCountsMethodsAndNotSumsComplexity
     *
     * @return void
     */
    public function testCalculateCSZMetricOnlyCountsMethodsAndNotSumsComplexity()
    {
        self::assertEquals(2, $this->_calculateClassMetric('csz'));
    }

    /**
     * testCalculateNpmMetricForEmptyClass
     *
     * @return void
     */
    public function testCalculateNpmMetricForEmptyClass()
    {
        self::assertEquals(0, $this->_calculateClassMetric('npm'));
    }

    /**
     * testCalculateNpmMetricForClassWithPublicMethod
     *
     * @return void
     */
    public function testCalculateNpmMetricForClassWithPublicMethod()
    {
        self::assertEquals(1, $this->_calculateClassMetric('npm'));
    }

    /**
     * testCalculateNpmMetricForClassWithPublicMethods
     *
     * @return void
     */
    public function testCalculateNpmMetricForClassWithPublicMethods()
    {
        self::assertEquals(3, $this->_calculateClassMetric('npm'));
    }

    /**
     * testCalculateNpmMetricForClassWithPublicStaticMethod
     *
     * @return void
     */
    public function testCalculateNpmMetricForClassWithPublicStaticMethod()
    {
        self::assertEquals(1, $this->_calculateClassMetric('npm'));
    }

    /**
     * testCalculateNpmMetricForClassWithProtectedMethod
     *
     * @return void
     */
    public function testCalculateNpmMetricForClassWithProtectedMethod()
    {
        self::assertEquals(0, $this->_calculateClassMetric('npm'));
    }

    /**
     * testCalculateNpmMetricForClassWithPrivateMethod
     *
     * @return void
     */
    public function testCalculateNpmMetricForClassWithPrivateMethod()
    {
        self::assertEquals(0, $this->_calculateClassMetric('npm'));
    }

    /**
     * testCalculateNpmMetricForClassWithAllVisibilityMethods
     *
     * @return void
     */
    public function testCalculateNpmMetricForClassWithAllVisibilityMethods()
    {
        self::assertEquals(1, $this->_calculateClassMetric('npm'));
    }

    /**
     * Tests that the analyzer calculates the correct VARS metric
     *
     * @return void
     */
    public function testCalculateVARSMetricZeroInheritance()
    {
        self::assertEquals(1, $this->_calculateClassMetric('vars'));
    }

    /**
     * Tests that the analyzer calculates the correct VARS metric
     *
     * @return void
     */
    public function testCalculateVARSMetricOneLevelInheritance()
    {
        self::assertEquals(3, $this->_calculateClassMetric('vars'));
    }

    /**
     * Tests that the analyzer calculates the correct VARSi metric
     *
     * @return void
     */
    public function testCalculateVARSiMetric()
    {
        self::assertEquals(4, $this->_calculateClassMetric('varsi'));
    }

    /**
     * Tests that the analyzer calculates the correct VARSi metric
     *
     * @return void
     */
    public function testCalculateVARSiMetricWithInheritance()
    {
        self::assertEquals(5, $this->_calculateClassMetric('varsi'));
    }

    /**
     * Tests that the analyzer calculates the correct VARSnp metric
     *
     * @return void
     */
    public function testCalculateVARSnpMetric()
    {
        self::assertEquals(2, $this->_calculateClassMetric('varsnp'));
    }

    /**
     * Tests that the analyzer calculates the correct VARSnp metric
     *
     * @return void
     */
    public function testCalculateVARSnpMetricWithInheritance()
    {
        self::assertEquals(1, $this->_calculateClassMetric('varsnp'));
    }

    /**
     * Tests that the analyzer calculates the correct WMC metric.
     *
     * @return void
     */
    public function testCalculateWMCMetric()
    {
        self::assertEquals(3, $this->_calculateClassMetric('wmc'));
    }

    /**
     * Tests that the analyzer calculates the correct WMC metric.
     *
     * @return void
     */
    public function testCalculateWMCMetricOneLevelInheritance()
    {
        self::assertEquals(3, $this->_calculateClassMetric('wmc'));
    }

    /**
     * Tests that the analyzer calculates the correct WMC metric.
     *
     * @return void
     */
    public function testCalculateWMCMetricTwoLevelInheritance()
    {
        self::assertEquals(3, $this->_calculateClassMetric('wmc'));
    }

    /**
     * Tests that the analyzer calculates the correct WMCi metric.
     *
     * @return void
     */
    public function testCalculateWMCiMetric()
    {
        self::assertEquals(3, $this->_calculateClassMetric('wmci'));
    }

    /**
     * Tests that the analyzer calculates the correct WMCi metric.
     *
     * @return void
     */
    public function testCalculateWMCiMetricOneLevelInheritance()
    {
        self::assertEquals(4, $this->_calculateClassMetric('wmci'));
    }

    /**
     * Tests that the analyzer calculates the correct WMCi metric.
     *
     * @return void
     */
    public function testCalculateWMCiMetricTwoLevelInheritance()
    {
        self::assertEquals(5, $this->_calculateClassMetric('wmci'));
    }

    /**
     * Tests that the analyzer calculates the correct WMCnp metric.
     *
     * @return void
     */
    public function testCalculateWMCnpMetric()
    {
        self::assertEquals(1, $this->_calculateClassMetric('wmcnp'));
    }

    /**
     * Tests that the analyzer calculates the correct WMCnp metric.
     *
     * @return void
     */
    public function testCalculateWMCnpMetricOneLevelInheritance()
    {
        self::assertEquals(2, $this->_calculateClassMetric('wmcnp'));
    }

    /**
     * Tests that the analyzer calculates the correct WMCnp metric.
     *
     * @return void
     */
    public function testCalculateWMCnpMetricTwoLevelInheritance()
    {
        self::assertEquals(1, $this->_calculateClassMetric('wmcnp'));
    }

    /**
     * Analyzes the source code associated with the given test case and returns
     * a single measured metric.
     *
     * @param string $name Name of the searched metric.
     *
     * @return mixed
     */
    private function _calculateClassMetric($name)
    {
        $metrics = $this->_calculateClassMetrics();
        return $metrics[$name];
    }

    /**
     * Analyzes the source code associated with the calling test method and
     * returns all measured metrics.
     *
     * @return mixed
     */
    private function _calculateClassMetrics()
    {
        $packages = self::parseTestCaseSource(self::getCallingTestMethod());
        $package  = $packages->current();

        $ccnAnalyzer = new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer();
        $ccnAnalyzer->setCache(new PHP_Depend_Util_Cache_Driver_Memory());

        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer($ccnAnalyzer);
        $analyzer->analyze($packages);

        return $analyzer->getNodeMetrics($package->getClasses()->current());
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

        self::assertInternalType('array', $metrics);

        return $metrics;
    }

    /**
     * testGetNodeMetricsForTraitReturnsExpectedMetricSet
     *
     * @param array $metrics Calculated class metrics.
     *
     * @return void
     * @since 1.0.6
     * @depends testGetNodeMetricsForTrait
     */
    public function testGetNodeMetricsForTraitReturnsExpectedMetricSet(array $metrics)
    {
        self::assertEquals(
            array('impl', 'cis', 'csz', 'npm', 'vars', 'varsi', 'varsnp', 'wmc', 'wmci', 'wmcnp'),
            array_keys($metrics)
        );
    }

    /**
     * Tests that the analyzer calculates the correct IMPL values.
     *
     * @param array $metrics Calculated class metrics.
     *
     * @return void
     * @since 1.0.6
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateIMPLMetricForTrait(array $metrics)
    {
        self::assertEquals(0, $metrics['impl']);
    }

    /**
     * Tests that the calculated Class Interface Size(CSI) is correct.
     *
     * @param array $metrics Calculated class metrics.
     *
     * @return void
     * @since 1.0.6
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateCISMetricForTrait(array $metrics)
    {
        self::assertEquals(2, $metrics['cis']);
    }

    /**
     * Tests that the calculated Class SiZe(CSZ) metric is correct.
     *
     * @param array $metrics Calculated class metrics.
     *
     * @return void
     * @since 1.0.6
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateCSZMetricForTrait(array $metrics)
    {
        self::assertEquals(3, $metrics['csz']);
    }

    /**
     * testCalculateNpmMetricForClassWithPublicMethod
     *
     * @param array $metrics Calculated class metrics.
     *
     * @return void
     * @since 1.0.6
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateNpmMetricForTrait(array $metrics)
    {
        self::assertEquals(2, $metrics['npm']);
    }

    /**
     * Tests that the analyzer calculates the correct VARS metric
     *
     * @param array $metrics Calculated class metrics.
     *
     * @return void
     * @since 1.0.6
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateVARSMetricForTrait(array $metrics)
    {
        self::assertEquals(0, $metrics['vars']);
    }

    /**
     * Tests that the analyzer calculates the correct VARSi metric
     *
     * @param array $metrics Calculated class metrics.
     *
     * @return void
     * @since 1.0.6
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateVARSiMetricForTrait(array $metrics)
    {
        self::assertEquals(0, $metrics['varsi']);
    }

    /**
     * Tests that the analyzer calculates the correct VARSnp metric
     *
     * @param array $metrics Calculated class metrics.
     *
     * @return void
     * @since 1.0.6
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateVARSnpMetricForTrait(array $metrics)
    {
        self::assertEquals(0, $metrics['varsnp']);
    }

    /**
     * Tests that the analyzer calculates the correct WMC metric.
     *
     * @param array $metrics Calculated class metrics.
     *
     * @return void
     * @since 1.0.6
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateWMCMetricForTrait(array $metrics)
    {
        self::assertEquals(10, $metrics['wmc']);
    }

    /**
     * Tests that the analyzer calculates the correct WMCi metric.
     *
     * @param array $metrics Calculated class metrics.
     *
     * @return void
     * @since 1.0.6
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateWMCiMetricForTrait(array $metrics)
    {
        self::assertEquals(10, $metrics['wmci']);
    }

    /**
     * Tests that the analyzer calculates the correct WMCnp metric.
     *
     * @param array $metrics Calculated class metrics.
     *
     * @return void
     * @since 1.0.6
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateWMCnpMetricForTrait(array $metrics)
    {
        self::assertEquals(8, $metrics['wmcnp']);
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
        $packages = $this->parseCodeResourceForTest();
        $package  = $packages->current();

        $ccnAnalyzer = new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer();
        $ccnAnalyzer->setCache(new PHP_Depend_Util_Cache_Driver_Memory());

        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer($ccnAnalyzer);
        $analyzer->analyze($packages);

        return $analyzer->getNodeMetrics($package->getTraits()->current());
    }
}
