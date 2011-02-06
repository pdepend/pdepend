<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/File.php';
require_once 'PHP/Depend/Code/Interface.php';
require_once 'PHP/Depend/Code/Method.php';
require_once 'PHP/Depend/Code/NodeIterator.php';
require_once 'PHP/Depend/Code/Package.php';
require_once 'PHP/Depend/Code/Property.php';
require_once 'PHP/Depend/Metrics/ClassLevel/Analyzer.php';
require_once 'PHP/Depend/Metrics/CodeRank/Analyzer.php';
require_once 'PHP/Depend/Metrics/CyclomaticComplexity/Analyzer.php';

/**
 * Test case for the class level analyzer.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 *
 * @covers PHP_Depend_Metrics_ClassLevel_Analyzer
 */
class PHP_Depend_Metrics_ClassLevel_AnalyzerTest extends PHP_Depend_Metrics_AbstractTest
{
    /**
     * Tests that the {@link PHP_Depend_Metrics_ClassLevel_Analyzer::analyzer()}
     * method fails with an exception if no cc analyzer was set.
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
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
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     * @expectedException InvalidArgumentException
     */
    public function testAddAnalyzerFailsForAnInvalidAnalyzerTypeFail()
    {
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CodeRank_Analyzer());
    }
    
    /**
     * Tests that the analyzer calculates the correct IMPL values.
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateIMPLMetric()
    {
        self::assertEquals(4, $this->_calculateMetric(__METHOD__, 'impl'));
    }

    /**
     * Tests that the analyzer calculates the correct IMPL values.
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateIMPLMetric1()
    {
        self::assertEquals(6, $this->_calculateMetric(__METHOD__, 'impl'));
    }

    /**
     * Tests that the analyzer calculates the correct IMPL values.
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateIMPLMetric2()
    {
        self::assertEquals(2, $this->_calculateMetric(__METHOD__, 'impl'));
    }

    /**
     * testCalculateIMPLMetricContainsUnknownImplementedInterface
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateIMPLMetricContainsUnknownImplementedInterface()
    {
        self::assertEquals(1, $this->_calculateMetric(__METHOD__, 'impl'));
    }

    /**
     * testCalculateIMPLMetricContainsUnknownIndirectImplementedInterface
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateIMPLMetricContainsUnknownIndirectImplementedInterface()
    {
        self::assertEquals(1, $this->_calculateMetric(__METHOD__, 'impl'));
    }

    /**
     * testCalculateIMPLMetricContainsInternalImplementedInterface
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateIMPLMetricContainsInternalImplementedInterface()
    {
        self::assertEquals(1, $this->_calculateMetric(__METHOD__, 'impl'));
    }
    
    /**
     * Tests that the calculated Class Interface Size(CSI) is correct.
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateCISMetricZeroInheritance()
    {
        self::assertEquals(2, $this->_calculateMetric(__METHOD__, 'cis'));
    }

    /**
     * Tests that the calculated Class Interface Size(CSI) is correct.
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateCISMetricOneLevelInheritance()
    {
        self::assertEquals(2, $this->_calculateMetric(__METHOD__, 'cis'));
    }

    /**
     * Tests that the calculated Class Interface Size(CSI) is correct.
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateCISMetricTwoLevelInheritance()
    {
        self::assertEquals(3, $this->_calculateMetric(__METHOD__, 'cis'));
    }

    /**
     * testCalculateCISMetricOnlyCountsMethodsAndNotSumsComplexity
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateCISMetricOnlyCountsMethodsAndNotSumsComplexity()
    {
        self::assertEquals(2, $this->_calculateMetric(__METHOD__, 'cis'));
    }
    
    /**
     * Tests that the calculated Class SiZe(CSZ) metric is correct.
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateCSZMetricZeroInheritance()
    {
        self::assertEquals(6, $this->_calculateMetric(__METHOD__, 'csz'));
    }

    /**
     * Tests that the calculated Class SiZe(CSZ) metric is correct.
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateCSZMetricOneLevelInheritance()
    {
        self::assertEquals(4, $this->_calculateMetric(__METHOD__, 'csz'));
    }

    /**
     * testCalculateCSZMetricOnlyCountsMethodsAndNotSumsComplexity
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateCSZMetricOnlyCountsMethodsAndNotSumsComplexity()
    {
        self::assertEquals(2, $this->_calculateMetric(__METHOD__, 'csz'));
    }
    
    /**
     * Tests that the analyzer calculates the correct VARS metric
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateVARSMetricZeroInheritance()
    {
        self::assertEquals(1, $this->_calculateMetric(__METHOD__, 'vars'));
    }
    
    /**
     * Tests that the analyzer calculates the correct VARS metric
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateVARSMetricOneLevelInheritance()
    {
        self::assertEquals(3, $this->_calculateMetric(__METHOD__, 'vars'));
    }
    
    /**
     * Tests that the analyzer calculates the correct VARSi metric
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateVARSiMetric()
    {
        self::assertEquals(4, $this->_calculateMetric(__METHOD__, 'varsi'));
    }

    /**
     * Tests that the analyzer calculates the correct VARSi metric
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateVARSiMetricWithInheritance()
    {
        self::assertEquals(5, $this->_calculateMetric(__METHOD__, 'varsi'));
    }
    
    /**
     * Tests that the analyzer calculates the correct VARSnp metric
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateVARSnpMetric()
    {
        self::assertEquals(2, $this->_calculateMetric(__METHOD__, 'varsnp'));
    }

    /**
     * Tests that the analyzer calculates the correct VARSnp metric
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateVARSnpMetricWithInheritance()
    {
        self::assertEquals(1, $this->_calculateMetric(__METHOD__, 'varsnp'));
    }
    
    /**
     * Tests that the analyzer calculates the correct WMC metric. 
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateWMCMetric()
    {
        self::assertEquals(3, $this->_calculateMetric(__METHOD__, 'wmc'));
    }

    /**
     * Tests that the analyzer calculates the correct WMC metric.
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateWMCMetricOneLevelInheritance()
    {
        self::assertEquals(3, $this->_calculateMetric(__METHOD__, 'wmc'));
    }

    /**
     * Tests that the analyzer calculates the correct WMC metric.
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateWMCMetricTwoLevelInheritance()
    {
        self::assertEquals(3, $this->_calculateMetric(__METHOD__, 'wmc'));
    }
    
    /**
     * Tests that the analyzer calculates the correct WMCi metric. 
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */    
    public function testCalculateWMCiMetric()
    {
        self::assertEquals(3, $this->_calculateMetric(__METHOD__, 'wmci'));
    }

    /**
     * Tests that the analyzer calculates the correct WMCi metric.
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateWMCiMetricOneLevelInheritance()
    {
        self::assertEquals(4, $this->_calculateMetric(__METHOD__, 'wmci'));
    }

    /**
     * Tests that the analyzer calculates the correct WMCi metric.
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateWMCiMetricTwoLevelInheritance()
    {
        self::assertEquals(5, $this->_calculateMetric(__METHOD__, 'wmci'));
    }
    
    /**
     * Tests that the analyzer calculates the correct WMCnp metric. 
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateWMCnpMetric()
    {
        self::assertEquals(1, $this->_calculateMetric(__METHOD__, 'wmcnp'));
    }

    /**
     * Tests that the analyzer calculates the correct WMCnp metric.
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateWMCnpMetricOneLevelInheritance()
    {
        self::assertEquals(2, $this->_calculateMetric(__METHOD__, 'wmcnp'));
    }

    /**
     * Tests that the analyzer calculates the correct WMCnp metric.
     *
     * @return void
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::classlevel
     * @group unittest
     */
    public function testCalculateWMCnpMetricTwoLevelInheritance()
    {
        self::assertEquals(1, $this->_calculateMetric(__METHOD__, 'wmcnp'));
    }

    /**
     * Analyzes the source code associated with the given test case and returns
     * a single measured metric.
     *
     * @param string $testCase Name of the calling test case.
     * @param string $metric   Name of the searched metric.
     *
     * @return mixed
     */
    private function _calculateMetric($testCase, $metric)
    {
        $packages = self::parseTestCaseSource($testCase);
        $package  = $packages->current();

        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($package->getClasses()->current());
        return $metrics[$metric];
    }
}