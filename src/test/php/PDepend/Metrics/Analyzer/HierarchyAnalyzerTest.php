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
use PDepend\Source\AST\ASTClass;

/**
 * Test case for the hierarchy analyzer.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @covers \PDepend\Metrics\Analyzer\HierarchyAnalyzer
   * @group unittest
 */
class HierarchyAnalyzerTest extends AbstractMetricsTest
{
    /**
     * testCalculatesExpectedNumberOfLeafClasses
     *
     * @return void
     */
    public function testCalculatesExpectedNumberOfLeafClasses()
    {
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $metrics = $analyzer->getProjectMetrics();
        $this->assertEquals(2, $metrics['leafs']);
    }

    /**
     * testCalculatesExpectedNumberOfAbstractClasses
     *
     * @return void
     */
    public function testCalculatesExpectedNumberOfAbstractClasses()
    {
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $metrics = $analyzer->getProjectMetrics();
        $this->assertEquals(1, $metrics['clsa']);
    }

    /**
     * testCalculatesExpectedNumberOfConcreteClasses
     *
     * @return void
     */
    public function testCalculatesExpectedNumberOfConcreteClasses()
    {
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $metrics = $analyzer->getProjectMetrics();
        $this->assertEquals(2, $metrics['clsc']);
    }

    /**
     * testCalculatesExpectedNumberOfRootClasses
     *
     * @return void
     */
    public function testCalculatesExpectedNumberOfRootClasses()
    {
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $metrics = $analyzer->getProjectMetrics();
        $this->assertEquals(1, $metrics['roots']);
    }

    /**
     * testCalculatedLeafsMetricDoesNotContainNotUserDefinedClasses
     *
     * @return void
     */
    public function testCalculatedLeafsMetricDoesNotContainNotUserDefinedClasses()
    {
        $analyzer = $this->createAnalyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $metrics = $analyzer->getProjectMetrics();
        $this->assertEquals(0, $metrics['leafs']);
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
     * Tests that {@link \PDepend\Metrics\Analyzer\HierarchyAnalyzer::getNodeMetrics()}
     * returns an empty <b>array</b> for an unknown node id.
     *
     * @return void
     */
    public function testGetNodeMetricsForUnknownId()
    {
        $class    = new ASTClass('PDepend');
        $analyzer = $this->createAnalyzer();

        $this->assertSame(array(), $analyzer->getNodeMetrics($class));
    }

    /**
     * @return \PDepend\Metrics\Analyzer\HierarchyAnalyzer
     */
    private function createAnalyzer()
    {
        return new HierarchyAnalyzer();
    }
}
