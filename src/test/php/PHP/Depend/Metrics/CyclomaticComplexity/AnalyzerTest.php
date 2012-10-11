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
 * Test case for the cyclomatic analyzer.
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
 * @covers PHP_Depend_Metrics_AbstractCachingAnalyzer
 * @covers PHP_Depend_Metrics_CyclomaticComplexity_Analyzer
 * @group pdepend
 * @group pdepend::metrics
 * @group pdepend::metrics::cyclomaticcomplexity
 * @group unittest
 */
class PHP_Depend_Metrics_CyclomaticComplexity_AnalyzerTest 
    extends PHP_Depend_Metrics_AbstractTest
{
    /**
     * @var PHP_Depend_Util_Cache_Driver
     * @since 1.0.0
     */
    private $_cache;

    /**
     * Initializes a in memory cache.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_cache = new PHP_Depend_Util_Cache_Driver_Memory();
    }

    /**
     * testGetCCNReturnsZeroForUnknownNode
     *
     * @return void
     */
    public function testGetCCNReturnsZeroForUnknownNode()
    {
        $analyzer = $this->_createAnalyzer();
        self::assertEquals(0, $analyzer->getCcn($this->getMock('PHP_Depend_Code_NodeI')));
    }

    /**
     * testGetCCN2ReturnsZeroForUnknownNode
     *
     * @return void
     */
    public function testGetCCN2ReturnsZeroForUnknownNode()
    {
        $analyzer = $this->_createAnalyzer();
        self::assertEquals(0, $analyzer->getCcn2($this->getMock('PHP_Depend_Code_NodeI')));
    }

    /**
     * Tests that the analyzer calculates the correct function cc numbers.
     *
     * @return void
     */
    public function testCalculateFunctionCCNAndCNN2()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $actual   = array();
        $expected = array(
            'pdepend1' => array('ccn' => 5, 'ccn2' => 6),
            'pdepend2' => array('ccn' => 7, 'ccn2' => 10)
        );
        
        foreach ($package->getFunctions() as $function) {
            $actual[$function->getName()] = $analyzer->getNodeMetrics($function);
        }

        ksort($expected);
        ksort($actual);

        self::assertEquals($expected, $actual);
    }

    /**
     * testCalculateFunctionCCNAndCNN2ProjectMetrics
     *
     * @return void
     */
    public function testCalculateFunctionCCNAndCNN2ProjectMetrics()
    {
        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $expected = array('ccn' => 12, 'ccn2' => 16);
        $actual   = $analyzer->getProjectMetrics();

        self::assertEquals($expected, $actual);
    }
    
    /**
     * Tests that the analyzer calculates the correct method cc numbers.
     *
     * @return void
     */
    public function testCalculateMethodCCNAndCNN2()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $classes = $package->getClasses();
        $methods = $classes->current()->getMethods();

        $actual   = array();
        $expected = array(
            'pdepend1' => array('ccn' => 5, 'ccn2' => 6),
            'pdepend2' => array('ccn' => 7, 'ccn2' => 10)
        );
        
        foreach ($methods as $method) {
            $actual[$method->getName()] = $analyzer->getNodeMetrics($method);
        }

        ksort($expected);
        ksort($actual);

        self::assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer also detects a conditional expression nested in a
     * compound expression.
     *
     * @return void
     */
    public function testCalculateCCNWithConditionalExprInCompoundExpr()
    {
        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $expected = array('ccn' => 2, 'ccn2' => 2);
        $actual   = $analyzer->getProjectMetrics();

        self::assertEquals($expected, $actual);
    }

    /**
     * testCalculateExpectedCCNForDoWhileStatement
     *
     * @return void
     */
    public function testCalculateExpectedCCNForDoWhileStatement()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        self::assertEquals(3, $analyzer->getCcn($function));
    }

    /**
     * testCalculateExpectedCCN2ForDoWhileStatement
     *
     * @return void
     */
    public function testCalculateExpectedCCN2ForDoWhileStatement()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        self::assertEquals(3, $analyzer->getCcn2($function));
    }

    /**
     * Tests that the analyzer ignores the default label in a switch statement.
     *
     * @return void
     */
    public function testCalculateCCNIgnoresDefaultLabelInSwitchStatement()
    {
        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $expected = array('ccn' => 3, 'ccn2' => 3);
        $actual   = $analyzer->getProjectMetrics();

        self::assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer counts all case labels in a switch statement.
     *
     * @return void
     */
    public function testCalculateCCNCountsAllCaseLabelsInSwitchStatement()
    {
        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $expected = array('ccn' => 4, 'ccn2' => 4);
        $actual   = $analyzer->getProjectMetrics();

        self::assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer detects expressions in a for loop.
     *
     * @return void
     */
    public function testCalculateCCNDetectsExpressionsInAForLoop()
    {
        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $expected = array('ccn' => 2, 'ccn2' => 4);
        $actual   = $analyzer->getProjectMetrics();

        self::assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer detects expressions in a while loop.
     *
     * @return void
     */
    public function testCalculateCCNDetectsExpressionsInAWhileLoop()
    {
        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $expected = array('ccn' => 2, 'ccn2' => 4);
        $actual   = $analyzer->getProjectMetrics();

        self::assertEquals($expected, $actual);
    }
    
    /**
     * Tests that the analyzer aggregates the correct project metrics.
     *
     * @return void
     */
    public function testCalculateProjectMetrics()
    {
        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));
        
        $expected = array('ccn' => 24, 'ccn2' => 32);
        $actual   = $analyzer->getProjectMetrics();

        self::assertEquals($expected, $actual);
    }
    
    /**
     * testAnalyzerAlsoCalculatesCCNAndCCN2OfClosureInMethod
     *
     * @return void
     */
    public function testAnalyzerAlsoCalculatesCCNAndCCN2OfClosureInMethod()
    {
        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $expected = array('ccn' => 3, 'ccn2' => 3);
        $actual   = $analyzer->getProjectMetrics();

        self::assertEquals($expected, $actual);
    }

    /**
     * testAnalyzerRestoresExpectedFunctionMetricsFromCache
     *
     * @return void
     * @since 1.0.0
     */
    public function testAnalyzerRestoresExpectedFunctionMetricsFromCache()
    {
        $packages = self::parseCodeResourceForTest();
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics0 = $analyzer->getNodeMetrics($function);

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics1 = $analyzer->getNodeMetrics($function);

        $this->assertEquals($metrics0, $metrics1);
    }

    /**
     * testAnalyzerRestoresExpectedMethodMetricsFromCache
     *
     * @return void
     * @since 1.0.0
     */
    public function testAnalyzerRestoresExpectedMethodMetricsFromCache()
    {
        $packages = self::parseCodeResourceForTest();
        $method   = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics0 = $analyzer->getNodeMetrics($method);

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics1 = $analyzer->getNodeMetrics($method);

        $this->assertEquals($metrics0, $metrics1);
    }

    /**
     * Returns a pre configured ccn analyzer.
     *
     * @return PHP_Depend_Metrics_CyclomaticComplexity_Analyzer
     * @since 1.0.0
     */
    private function _createAnalyzer()
    {
        $analyzer = new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer();
        $analyzer->setCache($this->_cache);

        return $analyzer;
    }
}
