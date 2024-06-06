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

use InvalidArgumentException;
use PDepend\Metrics\AbstractMetricsTestCase;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Util\Cache\Driver\MemoryCacheDriver;
use RuntimeException;

/**
 * Test case for the class level analyzer.
 *
 * @covers \PDepend\Metrics\Analyzer\ClassLevelAnalyzer
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ClassLevelAnalyzerTest extends AbstractMetricsTestCase
{
    /**
     * Tests that the {@link \PDepend\Metrics\Analyzer\ClassLevelAnalyzer::analyzer()}
     * method fails with an exception if no cc analyzer was set.
     */
    public function testAnalyzerFailsWithoutCCAnalyzerFail(): void
    {
        $this->expectException(RuntimeException::class);

        $namespace = new ASTNamespace('package1');
        $namespaces = new ASTArtifactList([$namespace]);

        $analyzer = new ClassLevelAnalyzer();
        $analyzer->analyze($namespaces);
    }

    /**
     * Tests that {@link \PDepend\Metrics\Analyzer\ClassLevelAnalyzer::addAnalyzer()}
     * fails for an invalid child analyzer.
     */
    public function testAddAnalyzerFailsForAnInvalidAnalyzerTypeFail(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $analyzer = new ClassLevelAnalyzer();
        $analyzer->addAnalyzer(new CodeRankAnalyzer());
    }

    /**
     * testGetRequiredAnalyzersReturnsExpectedClassNames
     */
    public function testGetRequiredAnalyzersReturnsExpectedClassNames(): void
    {
        $analyzer = new ClassLevelAnalyzer();
        static::assertEquals(
            [CyclomaticComplexityAnalyzer::class],
            $analyzer->getRequiredAnalyzers()
        );
    }

    /**
     * testGetNodeMetricsReturnsArrayWithExpectedSetOfMetrics
     */
    public function testGetNodeMetricsReturnsArrayWithExpectedSetOfMetrics(): void
    {
        static::assertEquals(
            ['impl', 'cis', 'csz', 'npm', 'vars', 'varsi', 'varsnp', 'wmc', 'wmci', 'wmcnp'],
            array_keys($this->calculateClassMetrics())
        );
    }

    /**
     * Tests that the analyzer calculates the correct IMPL values.
     */
    public function testCalculateIMPLMetric(): void
    {
        static::assertEquals(4, $this->calculateClassMetric('impl'));
    }

    /**
     * Tests that the analyzer calculates the correct IMPL values.
     */
    public function testCalculateIMPLMetric1(): void
    {
        static::assertEquals(6, $this->calculateClassMetric('impl'));
    }

    /**
     * Tests that the analyzer calculates the correct IMPL values.
     */
    public function testCalculateIMPLMetric2(): void
    {
        static::assertEquals(2, $this->calculateClassMetric('impl'));
    }

    /**
     * testCalculateIMPLMetricContainsUnknownImplementedInterface
     */
    public function testCalculateIMPLMetricContainsUnknownImplementedInterface(): void
    {
        static::assertEquals(1, $this->calculateClassMetric('impl'));
    }

    /**
     * testCalculateIMPLMetricContainsUnknownIndirectImplementedInterface
     */
    public function testCalculateIMPLMetricContainsUnknownIndirectImplementedInterface(): void
    {
        static::assertEquals(1, $this->calculateClassMetric('impl'));
    }

    /**
     * testCalculateIMPLMetricContainsInternalImplementedInterface
     */
    public function testCalculateIMPLMetricContainsInternalImplementedInterface(): void
    {
        static::assertEquals(1, $this->calculateClassMetric('impl'));
    }

    /**
     * Tests that the calculated Class Interface Size(CSI) is correct.
     */
    public function testCalculateCISMetricZeroInheritance(): void
    {
        static::assertEquals(2, $this->calculateClassMetric('cis'));
    }

    /**
     * Tests that the calculated Class Interface Size(CSI) is correct.
     */
    public function testCalculateCISMetricOneLevelInheritance(): void
    {
        static::assertEquals(2, $this->calculateClassMetric('cis'));
    }

    /**
     * Tests that the calculated Class Interface Size(CSI) is correct.
     */
    public function testCalculateCISMetricTwoLevelInheritance(): void
    {
        static::assertEquals(3, $this->calculateClassMetric('cis'));
    }

    /**
     * testCalculateCISMetricOnlyCountsMethodsAndNotSumsComplexity
     */
    public function testCalculateCISMetricOnlyCountsMethodsAndNotSumsComplexity(): void
    {
        static::assertEquals(2, $this->calculateClassMetric('cis'));
    }

    /**
     * Tests that the calculated Class SiZe(CSZ) metric is correct.
     */
    public function testCalculateCSZMetricZeroInheritance(): void
    {
        static::assertEquals(6, $this->calculateClassMetric('csz'));
    }

    /**
     * Tests that the calculated Class SiZe(CSZ) metric is correct.
     */
    public function testCalculateCSZMetricOneLevelInheritance(): void
    {
        static::assertEquals(4, $this->calculateClassMetric('csz'));
    }

    /**
     * testCalculateCSZMetricOnlyCountsMethodsAndNotSumsComplexity
     */
    public function testCalculateCSZMetricOnlyCountsMethodsAndNotSumsComplexity(): void
    {
        static::assertEquals(2, $this->calculateClassMetric('csz'));
    }

    /**
     * testCalculateNpmMetricForEmptyClass
     */
    public function testCalculateNpmMetricForEmptyClass(): void
    {
        static::assertEquals(0, $this->calculateClassMetric('npm'));
    }

    /**
     * testCalculateNpmMetricForClassWithPublicMethod
     */
    public function testCalculateNpmMetricForClassWithPublicMethod(): void
    {
        static::assertEquals(1, $this->calculateClassMetric('npm'));
    }

    /**
     * testCalculateNpmMetricForClassWithPublicMethods
     */
    public function testCalculateNpmMetricForClassWithPublicMethods(): void
    {
        static::assertEquals(3, $this->calculateClassMetric('npm'));
    }

    /**
     * testCalculateNpmMetricForClassWithPublicStaticMethod
     */
    public function testCalculateNpmMetricForClassWithPublicStaticMethod(): void
    {
        static::assertEquals(1, $this->calculateClassMetric('npm'));
    }

    /**
     * testCalculateNpmMetricForClassWithProtectedMethod
     */
    public function testCalculateNpmMetricForClassWithProtectedMethod(): void
    {
        static::assertEquals(0, $this->calculateClassMetric('npm'));
    }

    /**
     * testCalculateNpmMetricForClassWithPrivateMethod
     */
    public function testCalculateNpmMetricForClassWithPrivateMethod(): void
    {
        static::assertEquals(0, $this->calculateClassMetric('npm'));
    }

    /**
     * testCalculateNpmMetricForClassWithAllVisibilityMethods
     */
    public function testCalculateNpmMetricForClassWithAllVisibilityMethods(): void
    {
        static::assertEquals(1, $this->calculateClassMetric('npm'));
    }

    /**
     * Tests that the analyzer calculates the correct VARS metric
     */
    public function testCalculateVARSMetricZeroInheritance(): void
    {
        static::assertEquals(1, $this->calculateClassMetric('vars'));
    }

    /**
     * Tests that the analyzer calculates the correct VARS metric
     */
    public function testCalculateVARSMetricOneLevelInheritance(): void
    {
        static::assertEquals(3, $this->calculateClassMetric('vars'));
    }

    /**
     * Tests that the analyzer calculates the correct VARSi metric
     */
    public function testCalculateVARSiMetric(): void
    {
        static::assertEquals(4, $this->calculateClassMetric('varsi'));
    }

    /**
     * Tests that the analyzer calculates the correct VARSi metric
     */
    public function testCalculateVARSiMetricWithInheritance(): void
    {
        static::assertEquals(5, $this->calculateClassMetric('varsi'));
    }

    /**
     * Tests that the analyzer calculates the correct VARSnp metric
     */
    public function testCalculateVARSnpMetric(): void
    {
        static::assertEquals(2, $this->calculateClassMetric('varsnp'));
    }

    /**
     * Tests that the analyzer calculates the correct VARSnp metric
     */
    public function testCalculateVARSnpMetricWithInheritance(): void
    {
        static::assertEquals(1, $this->calculateClassMetric('varsnp'));
    }

    /**
     * Tests that the analyzer calculates the correct WMC metric.
     */
    public function testCalculateWMCMetric(): void
    {
        static::assertEquals(3, $this->calculateClassMetric('wmc'));
    }

    /**
     * Tests that the analyzer calculates the correct WMC metric.
     */
    public function testCalculateWMCMetricOneLevelInheritance(): void
    {
        static::assertEquals(3, $this->calculateClassMetric('wmc'));
    }

    /**
     * Tests that the analyzer calculates the correct WMC metric.
     */
    public function testCalculateWMCMetricTwoLevelInheritance(): void
    {
        static::assertEquals(3, $this->calculateClassMetric('wmc'));
    }

    /**
     * Tests that the analyzer calculates the correct WMCi metric.
     */
    public function testCalculateWMCiMetric(): void
    {
        static::assertEquals(3, $this->calculateClassMetric('wmci'));
    }

    /**
     * Tests that the analyzer calculates the correct WMCi metric.
     */
    public function testCalculateWMCiMetricOneLevelInheritance(): void
    {
        static::assertEquals(4, $this->calculateClassMetric('wmci'));
    }

    /**
     * Tests that the analyzer calculates the correct WMCi metric.
     */
    public function testCalculateWMCiMetricTwoLevelInheritance(): void
    {
        static::assertEquals(5, $this->calculateClassMetric('wmci'));
    }

    /**
     * Tests that the analyzer calculates the correct WMCnp metric.
     */
    public function testCalculateWMCnpMetric(): void
    {
        static::assertEquals(1, $this->calculateClassMetric('wmcnp'));
    }

    /**
     * Tests that the analyzer calculates the correct WMCnp metric.
     */
    public function testCalculateWMCnpMetricOneLevelInheritance(): void
    {
        static::assertEquals(2, $this->calculateClassMetric('wmcnp'));
    }

    /**
     * Tests that the analyzer calculates the correct WMCnp metric.
     */
    public function testCalculateWMCnpMetricTwoLevelInheritance(): void
    {
        static::assertEquals(1, $this->calculateClassMetric('wmcnp'));
    }

    /**
     * Analyzes the source code associated with the given test case and returns
     * a single measured metric.
     *
     * @param string $name Name of the searched metric.
     */
    private function calculateClassMetric(string $name): mixed
    {
        $metrics = $this->calculateClassMetrics();

        return $metrics[$name];
    }

    /**
     * Analyzes the source code associated with the calling test method and
     * returns all measured metrics.
     *
     * @return array<string, mixed>
     */
    private function calculateClassMetrics(): array
    {
        $namespaces = $this->parseTestCaseSource($this->getCallingTestMethod());

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
     * @param array<string, mixed> $metrics Calculated class metrics.
     * @since 1.0.6
     *
     * @depends testGetNodeMetricsForTrait
     */
    public function testGetNodeMetricsForTraitReturnsExpectedMetricSet(array $metrics): void
    {
        static::assertEquals(
            ['impl', 'cis', 'csz', 'npm', 'vars', 'varsi', 'varsnp', 'wmc', 'wmci', 'wmcnp'],
            array_keys($metrics)
        );
    }

    /**
     * Tests that the analyzer calculates the correct IMPL values.
     *
     * @param array<string, int> $metrics Calculated class metrics.
     * @since 1.0.6
     *
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateIMPLMetricForTrait(array $metrics): void
    {
        static::assertEquals(0, $metrics['impl']);
    }

    /**
     * Tests that the calculated Class Interface Size(CSI) is correct.
     *
     * @param array<string, int> $metrics Calculated class metrics.
     * @since 1.0.6
     *
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateCISMetricForTrait(array $metrics): void
    {
        static::assertEquals(2, $metrics['cis']);
    }

    /**
     * Tests that the calculated Class SiZe(CSZ) metric is correct.
     *
     * @param array<string, int> $metrics Calculated class metrics.
     * @since 1.0.6
     *
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateCSZMetricForTrait(array $metrics): void
    {
        static::assertEquals(3, $metrics['csz']);
    }

    /**
     * testCalculateNpmMetricForClassWithPublicMethod
     *
     * @param array<string, int> $metrics Calculated class metrics.
     * @since 1.0.6
     *
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateNpmMetricForTrait(array $metrics): void
    {
        static::assertEquals(2, $metrics['npm']);
    }

    /**
     * Tests that the analyzer calculates the correct VARS metric
     *
     * @param array<string, int> $metrics Calculated class metrics.
     * @since 1.0.6
     *
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateVARSMetricForTrait(array $metrics): void
    {
        static::assertEquals(0, $metrics['vars']);
    }

    /**
     * Tests that the analyzer calculates the correct VARSi metric
     *
     * @param array<string, int> $metrics Calculated class metrics.
     * @since 1.0.6
     *
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateVARSiMetricForTrait(array $metrics): void
    {
        static::assertEquals(0, $metrics['varsi']);
    }

    /**
     * Tests that the analyzer calculates the correct VARSnp metric
     *
     * @param array<string, int> $metrics Calculated class metrics.
     * @since 1.0.6
     *
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateVARSnpMetricForTrait(array $metrics): void
    {
        static::assertEquals(0, $metrics['varsnp']);
    }

    /**
     * Tests that the analyzer calculates the correct WMC metric.
     *
     * @param array<string, int> $metrics Calculated class metrics.
     * @since 1.0.6
     *
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateWMCMetricForTrait(array $metrics): void
    {
        static::assertEquals(10, $metrics['wmc']);
    }

    /**
     * Tests that the analyzer calculates the correct WMCi metric.
     *
     * @param array<string, int> $metrics Calculated class metrics.
     * @since 1.0.6
     *
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateWMCiMetricForTrait(array $metrics): void
    {
        static::assertEquals(10, $metrics['wmci']);
    }

    /**
     * Tests that the analyzer calculates the correct WMCnp metric.
     *
     * @param array<string, int> $metrics Calculated class metrics.
     * @since 1.0.6
     *
     * @depends testGetNodeMetricsForTrait
     */
    public function testCalculateWMCnpMetricForTrait(array $metrics): void
    {
        static::assertEquals(8, $metrics['wmcnp']);
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

        $ccnAnalyzer = new CyclomaticComplexityAnalyzer();
        $ccnAnalyzer->setCache(new MemoryCacheDriver());

        $analyzer = new ClassLevelAnalyzer();
        $analyzer->addAnalyzer($ccnAnalyzer);
        $analyzer->analyze($namespaces);

        return $analyzer->getNodeMetrics($namespaces[0]->getTraits()->current());
    }
}
