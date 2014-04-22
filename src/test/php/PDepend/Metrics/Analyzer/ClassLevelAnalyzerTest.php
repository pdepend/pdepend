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
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Util\Cache\Driver\MemoryCacheDriver;

/**
 * Test case for the class level analyzer.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Metrics\Analyzer\ClassLevelAnalyzer
 * @group unittest
 */
class ClassLevelAnalyzerTest extends AbstractMetricsTest
{
    /**
     * Tests that the {@link \PDepend\Metrics\Analyzer\ClassLevelAnalyzer::analyzer()}
     * method fails with an exception if no cc analyzer was set.
     *
     * @return void
     * @expectedException RuntimeException
     */
    public function testAnalyzerFailsWithoutCCAnalyzerFail()
    {
        $namespace = new ASTNamespace('package1');
        $namespaces = new ASTArtifactList(array($namespace));

        $analyzer = new ClassLevelAnalyzer();
        $analyzer->analyze($namespaces);
    }

    /**
     * Tests that {@link \PDepend\Metrics\Analyzer\ClassLevelAnalyzer::addAnalyzer()}
     * fails for an invalid child analyzer.
     *
     * @return void
     * @expectedException InvalidArgumentException
     */
    public function testAddAnalyzerFailsForAnInvalidAnalyzerTypeFail()
    {
        $analyzer = new ClassLevelAnalyzer();
        $analyzer->addAnalyzer(new CodeRankAnalyzer());
    }

    /**
     * testGetRequiredAnalyzersReturnsExpectedClassNames
     *
     * @return void
     */
    public function testGetRequiredAnalyzersReturnsExpectedClassNames()
    {
        $analyzer = new ClassLevelAnalyzer();
        $this->assertEquals(
            array('PDepend\\Metrics\\Analyzer\\CyclomaticComplexityAnalyzer'),
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
        $this->assertEquals(
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
        $this->assertEquals(4, $this->_calculateClassMetric('impl'));
    }

    /**
     * Tests that the analyzer calculates the correct IMPL values.
     *
     * @return void
     */
    public function testCalculateIMPLMetric1()
    {
        $this->assertEquals(6, $this->_calculateClassMetric('impl'));
    }

    /**
     * Tests that the analyzer calculates the correct IMPL values.
     *
     * @return void
     */
    public function testCalculateIMPLMetric2()
    {
        $this->assertEquals(2, $this->_calculateClassMetric('impl'));
    }

    /**
     * testCalculateIMPLMetricContainsUnknownImplementedInterface
     *
     * @return void
     */
    public function testCalculateIMPLMetricContainsUnknownImplementedInterface()
    {
        $this->assertEquals(1, $this->_calculateClassMetric('impl'));
    }

    /**
     * testCalculateIMPLMetricContainsUnknownIndirectImplementedInterface
     *
     * @return void
     */
    public function testCalculateIMPLMetricContainsUnknownIndirectImplementedInterface()
    {
        $this->assertEquals(1, $this->_calculateClassMetric('impl'));
    }

    /**
     * testCalculateIMPLMetricContainsInternalImplementedInterface
     *
     * @return void
     */
    public function testCalculateIMPLMetricContainsInternalImplementedInterface()
    {
        $this->assertEquals(1, $this->_calculateClassMetric('impl'));
    }

    /**
     * Tests that the calculated Class Interface Size(CSI) is correct.
     *
     * @return void
     */
    public function testCalculateCISMetricZeroInheritance()
    {
        $this->assertEquals(2, $this->_calculateClassMetric('cis'));
    }

    /**
     * Tests that the calculated Class Interface Size(CSI) is correct.
     *
     * @return void
     */
    public function testCalculateCISMetricOneLevelInheritance()
    {
        $this->assertEquals(2, $this->_calculateClassMetric('cis'));
    }

    /**
     * Tests that the calculated Class Interface Size(CSI) is correct.
     *
     * @return void
     */
    public function testCalculateCISMetricTwoLevelInheritance()
    {
        $this->assertEquals(3, $this->_calculateClassMetric('cis'));
    }

    /**
     * testCalculateCISMetricOnlyCountsMethodsAndNotSumsComplexity
     *
     * @return void
     */
    public function testCalculateCISMetricOnlyCountsMethodsAndNotSumsComplexity()
    {
        $this->assertEquals(2, $this->_calculateClassMetric('cis'));
    }

    /**
     * Tests that the calculated Class SiZe(CSZ) metric is correct.
     *
     * @return void
     */
    public function testCalculateCSZMetricZeroInheritance()
    {
        $this->assertEquals(6, $this->_calculateClassMetric('csz'));
    }

    /**
     * Tests that the calculated Class SiZe(CSZ) metric is correct.
     *
     * @return void
     */
    public function testCalculateCSZMetricOneLevelInheritance()
    {
        $this->assertEquals(4, $this->_calculateClassMetric('csz'));
    }

    /**
     * testCalculateCSZMetricOnlyCountsMethodsAndNotSumsComplexity
     *
     * @return void
     */
    public function testCalculateCSZMetricOnlyCountsMethodsAndNotSumsComplexity()
    {
        $this->assertEquals(2, $this->_calculateClassMetric('csz'));
    }

    /**
     * testCalculateNpmMetricForEmptyClass
     *
     * @return void
     */
    public function testCalculateNpmMetricForEmptyClass()
    {
        $this->assertEquals(0, $this->_calculateClassMetric('npm'));
    }

    /**
     * testCalculateNpmMetricForClassWithPublicMethod
     *
     * @return void
     */
    public function testCalculateNpmMetricForClassWithPublicMethod()
    {
        $this->assertEquals(1, $this->_calculateClassMetric('npm'));
    }

    /**
     * testCalculateNpmMetricForClassWithPublicMethods
     *
     * @return void
     */
    public function testCalculateNpmMetricForClassWithPublicMethods()
    {
        $this->assertEquals(3, $this->_calculateClassMetric('npm'));
    }

    /**
     * testCalculateNpmMetricForClassWithPublicStaticMethod
     *
     * @return void
     */
    public function testCalculateNpmMetricForClassWithPublicStaticMethod()
    {
        $this->assertEquals(1, $this->_calculateClassMetric('npm'));
    }

    /**
     * testCalculateNpmMetricForClassWithProtectedMethod
     *
     * @return void
     */
    public function testCalculateNpmMetricForClassWithProtectedMethod()
    {
        $this->assertEquals(0, $this->_calculateClassMetric('npm'));
    }

    /**
     * testCalculateNpmMetricForClassWithPrivateMethod
     *
     * @return void
     */
    public function testCalculateNpmMetricForClassWithPrivateMethod()
    {
        $this->assertEquals(0, $this->_calculateClassMetric('npm'));
    }

    /**
     * testCalculateNpmMetricForClassWithAllVisibilityMethods
     *
     * @return void
     */
    public function testCalculateNpmMetricForClassWithAllVisibilityMethods()
    {
        $this->assertEquals(1, $this->_calculateClassMetric('npm'));
    }

    /**
     * Tests that the analyzer calculates the correct VARS metric
     *
     * @return void
     */
    public function testCalculateVARSMetricZeroInheritance()
    {
        $this->assertEquals(1, $this->_calculateClassMetric('vars'));
    }

    /**
     * Tests that the analyzer calculates the correct VARS metric
     *
     * @return void
     */
    public function testCalculateVARSMetricOneLevelInheritance()
    {
        $this->assertEquals(3, $this->_calculateClassMetric('vars'));
    }

    /**
     * Tests that the analyzer calculates the correct VARSi metric
     *
     * @return void
     */
    public function testCalculateVARSiMetric()
    {
        $this->assertEquals(4, $this->_calculateClassMetric('varsi'));
    }

    /**
     * Tests that the analyzer calculates the correct VARSi metric
     *
     * @return void
     */
    public function testCalculateVARSiMetricWithInheritance()
    {
        $this->assertEquals(5, $this->_calculateClassMetric('varsi'));
    }

    /**
     * Tests that the analyzer calculates the correct VARSnp metric
     *
     * @return void
     */
    public function testCalculateVARSnpMetric()
    {
        $this->assertEquals(2, $this->_calculateClassMetric('varsnp'));
    }

    /**
     * Tests that the analyzer calculates the correct VARSnp metric
     *
     * @return void
     */
    public function testCalculateVARSnpMetricWithInheritance()
    {
        $this->assertEquals(1, $this->_calculateClassMetric('varsnp'));
    }

    /**
     * Tests that the analyzer calculates the correct WMC metric.
     *
     * @return void
     */
    public function testCalculateWMCMetric()
    {
        $this->assertEquals(3, $this->_calculateClassMetric('wmc'));
    }

    /**
     * Tests that the analyzer calculates the correct WMC metric.
     *
     * @return void
     */
    public function testCalculateWMCMetricOneLevelInheritance()
    {
        $this->assertEquals(3, $this->_calculateClassMetric('wmc'));
    }

    /**
     * Tests that the analyzer calculates the correct WMC metric.
     *
     * @return void
     */
    public function testCalculateWMCMetricTwoLevelInheritance()
    {
        $this->assertEquals(3, $this->_calculateClassMetric('wmc'));
    }

    /**
     * Tests that the analyzer calculates the correct WMCi metric.
     *
     * @return void
     */
    public function testCalculateWMCiMetric()
    {
        $this->assertEquals(3, $this->_calculateClassMetric('wmci'));
    }

    /**
     * Tests that the analyzer calculates the correct WMCi metric.
     *
     * @return void
     */
    public function testCalculateWMCiMetricOneLevelInheritance()
    {
        $this->assertEquals(4, $this->_calculateClassMetric('wmci'));
    }

    /**
     * Tests that the analyzer calculates the correct WMCi metric.
     *
     * @return void
     */
    public function testCalculateWMCiMetricTwoLevelInheritance()
    {
        $this->assertEquals(5, $this->_calculateClassMetric('wmci'));
    }

    /**
     * Tests that the analyzer calculates the correct WMCnp metric.
     *
     * @return void
     */
    public function testCalculateWMCnpMetric()
    {
        $this->assertEquals(1, $this->_calculateClassMetric('wmcnp'));
    }

    /**
     * Tests that the analyzer calculates the correct WMCnp metric.
     *
     * @return void
     */
    public function testCalculateWMCnpMetricOneLevelInheritance()
    {
        $this->assertEquals(2, $this->_calculateClassMetric('wmcnp'));
    }

    /**
     * Tests that the analyzer calculates the correct WMCnp metric.
     *
     * @return void
     */
    public function testCalculateWMCnpMetricTwoLevelInheritance()
    {
        $this->assertEquals(1, $this->_calculateClassMetric('wmcnp'));
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
        $namespaces = self::parseTestCaseSource(self::getCallingTestMethod());

        $ccnAnalyzer = new CyclomaticComplexityAnalyzer();
        $ccnAnalyzer->setCache(new MemoryCacheDriver());

        $analyzer = new ClassLevelAnalyzer();
        $analyzer->addAnalyzer($ccnAnalyzer);
        $analyzer->analyze($namespaces);

        return $analyzer->getNodeMetrics($namespaces[0]->getClasses()->current());
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
     * @param array $metrics Calculated class metrics.
     *
     * @return void
     * @since 1.0.6
     * @depends testGetNodeMetricsForTrait
     */
    public function testGetNodeMetricsForTraitReturnsExpectedMetricSet(array $metrics)
    {
        $this->assertEquals(
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
        $this->assertEquals(0, $metrics['impl']);
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
        $this->assertEquals(2, $metrics['cis']);
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
        $this->assertEquals(3, $metrics['csz']);
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
        $this->assertEquals(2, $metrics['npm']);
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
        $this->assertEquals(0, $metrics['vars']);
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
        $this->assertEquals(0, $metrics['varsi']);
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
        $this->assertEquals(0, $metrics['varsnp']);
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
        $this->assertEquals(10, $metrics['wmc']);
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
        $this->assertEquals(10, $metrics['wmci']);
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
        $this->assertEquals(8, $metrics['wmcnp']);
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

        $ccnAnalyzer = new CyclomaticComplexityAnalyzer();
        $ccnAnalyzer->setCache(new MemoryCacheDriver());

        $analyzer = new ClassLevelAnalyzer();
        $analyzer->addAnalyzer($ccnAnalyzer);
        $analyzer->analyze($namespaces);

        return $analyzer->getNodeMetrics($namespaces[0]->getTraits()->current());
    }
}
