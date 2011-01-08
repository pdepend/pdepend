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

require_once 'PHP/Depend/Code/NodeI.php';
require_once 'PHP/Depend/Metrics/CyclomaticComplexity/Analyzer.php';

/**
 * Test case for the cyclomatic analyzer.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Metrics_CyclomaticComplexity_AnalyzerTest 
    extends PHP_Depend_Metrics_AbstractTest
{
    /**
     * testGetCCNReturnsZeroForUnknownNode
     *
     * @return void
     * @covers PHP_Depend_Metrics_CyclomaticComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::cyclomaticcomplexity
     * @group unittest
     */
    public function testGetCCNReturnsZeroForUnknownNode()
    {
        $analyzer = new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer();
        $this->assertEquals(0, $analyzer->getCCN($this->getMock('PHP_Depend_Code_NodeI')));
    }

    /**
     * testGetCCN2ReturnsZeroForUnknownNode
     *
     * @return void
     * @covers PHP_Depend_Metrics_CyclomaticComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::cyclomaticcomplexity
     * @group unittest
     */
    public function testGetCCN2ReturnsZeroForUnknownNode()
    {
        $analyzer = new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer();
        $this->assertEquals(0, $analyzer->getCCN2($this->getMock('PHP_Depend_Code_NodeI')));
    }

    /**
     * Tests that the analyzer calculates the correct function cc numbers.
     *
     * @return void
     * @covers PHP_Depend_Metrics_CyclomaticComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::cyclomaticcomplexity
     * @group unittest
     */
    public function testCalculateFunctionCCNAndCNN2()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $analyzer = new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer();
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

        $this->assertEquals($expected, $actual);
    }

    /**
     * testCalculateFunctionCCNAndCNN2ProjectMetrics
     *
     * @return void
     * @covers PHP_Depend_Metrics_CyclomaticComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::cyclomaticcomplexity
     * @group unittest
     */
    public function testCalculateFunctionCCNAndCNN2ProjectMetrics()
    {
        $analyzer = new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $expected = array('ccn' => 12, 'ccn2' => 16);
        $actual   = $analyzer->getProjectMetrics();

        $this->assertEquals($expected, $actual);
    }
    
    /**
     * Tests that the analyzer calculates the correct method cc numbers.
     *
     * @return void
     * @covers PHP_Depend_Metrics_CyclomaticComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::cyclomaticcomplexity
     * @group unittest
     */
    public function testCalculateMethodCCNAndCNN2()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $package  = $packages->current();

        $analyzer = new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer();
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

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer also detects a conditional expression nested in a
     * compound expression.
     *
     * @return void
     * @covers PHP_Depend_Metrics_CyclomaticComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::cyclomaticcomplexity
     * @group unittest
     */
    public function testCalculateCCNWithConditionalExprInCompoundExpr()
    {
        $analyzer = new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $expected = array('ccn' => 2, 'ccn2' => 2);
        $actual   = $analyzer->getProjectMetrics();

        $this->assertEquals($expected, $actual);
    }

    /**
     * testCalculateExpectedCCNForDoWhileStatement
     *
     * @return void
     * @covers PHP_Depend_Metrics_CyclomaticComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::cyclomaticcomplexity
     * @group unittest
     */
    public function testCalculateExpectedCCNForDoWhileStatement()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $analyzer = new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer();
        $analyzer->analyze($packages);

        $this->assertEquals(3, $analyzer->getCCN($function));
    }

    /**
     * testCalculateExpectedCCN2ForDoWhileStatement
     *
     * @return void
     * @covers PHP_Depend_Metrics_CyclomaticComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::cyclomaticcomplexity
     * @group unittest
     */
    public function testCalculateExpectedCCN2ForDoWhileStatement()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $analyzer = new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer();
        $analyzer->analyze($packages);

        $this->assertEquals(3, $analyzer->getCCN2($function));
    }

    /**
     * Tests that the analyzer ignores the default label in a switch statement.
     *
     * @return void
     * @covers PHP_Depend_Metrics_CyclomaticComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::cyclomaticcomplexity
     * @group unittest
     */
    public function testCalculateCCNIgnoresDefaultLabelInSwitchStatement()
    {
        $analyzer = new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $expected = array('ccn' => 3, 'ccn2' => 3);
        $actual   = $analyzer->getProjectMetrics();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer counts all case labels in a switch statement.
     *
     * @return void
     * @covers PHP_Depend_Metrics_CyclomaticComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::cyclomaticcomplexity
     * @group unittest
     */
    public function testCalculateCCNCountsAllCaseLabelsInSwitchStatement()
    {
        $analyzer = new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $expected = array('ccn' => 4, 'ccn2' => 4);
        $actual   = $analyzer->getProjectMetrics();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer detects expressions in a for loop.
     *
     * @return void
     * @covers PHP_Depend_Metrics_CyclomaticComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::cyclomaticcomplexity
     * @group unittest
     */
    public function testCalculateCCNDetectsExpressionsInAForLoop()
    {
        $analyzer = new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $expected = array('ccn' => 2, 'ccn2' => 4);
        $actual   = $analyzer->getProjectMetrics();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer detects expressions in a while loop.
     *
     * @return void
     * @covers PHP_Depend_Metrics_CyclomaticComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::cyclomaticcomplexity
     * @group unittest
     */
    public function testCalculateCCNDetectsExpressionsInAWhileLoop()
    {
        $analyzer = new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $expected = array('ccn' => 2, 'ccn2' => 4);
        $actual   = $analyzer->getProjectMetrics();

        $this->assertEquals($expected, $actual);
    }
    
    /**
     * Tests that the analyzer aggregates the correct project metrics.
     *
     * @return void
     * @covers PHP_Depend_Metrics_CyclomaticComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::cyclomaticcomplexity
     * @group unittest
     */
    public function testCalculateProjectMetrics()
    {
        $analyzer = new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));
        
        $expected = array('ccn' => 24, 'ccn2' => 32);
        $actual   = $analyzer->getProjectMetrics();

        $this->assertEquals($expected, $actual);
    }
    
    /**
     * testAnalyzerAlsoCalculatesCCNAndCCN2OfClosureInMethod
     *
     * @return void
     * @covers PHP_Depend_Metrics_CyclomaticComplexity_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::cyclomaticcomplexity
     * @group unittest
     */
    public function testAnalyzerAlsoCalculatesCCNAndCCN2OfClosureInMethod()
    {
        $analyzer = new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $expected = array('ccn' => 3, 'ccn2' => 3);
        $actual   = $analyzer->getProjectMetrics();

        $this->assertEquals($expected, $actual);
    }
}