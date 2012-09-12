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
 * Test case for the node lines of code analyzer.
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
 * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
 * @group pdepend
 * @group pdepend::metrics
 * @group pdepend::metrics::nodeloc
 * @group unittest
 */
class PHP_Depend_Metrics_NodeLoc_AnalyzerTest extends PHP_Depend_Metrics_AbstractTest
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
     * testAnalyzerCalculatesCorrectFunctionMetrics
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectFunctionMetrics()
    {
        $packages  = self::parseTestCaseSource(__METHOD__);
        $functions = $packages->current()
            ->getFunctions();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $expected = array(
            'func_with_comment'  =>  array(
                'loc'    =>  6,
                'cloc'   =>  3,
                'eloc'   =>  2,
                'lloc'   =>  0,
                'ncloc'  =>  3
            ),
            'func_without_comment'  =>  array(
                'loc'    =>  7,
                'cloc'   =>  4,
                'eloc'   =>  2,
                'lloc'   =>  0,
                'ncloc'  =>  3,
            ),
            'func_without_doc_comment'  =>  array(
                'loc'    =>  3,
                'cloc'   =>  0,
                'eloc'   =>  2,
                'lloc'   =>  0,
                'ncloc'  =>  3,
            ),
            'another_func_with_comment'  =>  array(
                'loc'    =>  4,
                'cloc'   =>  1,
                'eloc'   =>  2,
                'lloc'   =>  0,
                'ncloc'  =>  3,
            ),
        );

        $actual = array();
        foreach ($functions as $function) {
            $actual[$function->getName()] = $analyzer->getNodeMetrics($function);
        }

        ksort($expected);
        ksort($actual);
        
        self::assertEquals($expected, $actual);
    }

    /**
     * testAnalyzerCalculatesCorrectFunctionFileMetrics
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectFunctionFileMetrics()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $file     = $packages->current()
            ->getFunctions()
            ->current()
            ->getSourceFile();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $actual   = $analyzer->getNodeMetrics($file);
        $expected = array(
            'loc'    =>  31,
            'cloc'   =>  15,
            'eloc'   =>  13,
            'lloc'   =>  4,
            'ncloc'  =>  16
        );
        self::assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates the correct class, method and file
     * loc values.
     *
     * @return void
     */
    public function testAnalyzerCalculatesClassMethodsIntoNcloc()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $class    = $packages->current()
            ->getClasses()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($class);
        self::assertEquals(18, $metrics['ncloc']);
    }

    /**
     * testAnalyzerCalculatesClassPropertiesIntoNcloc
     *
     * @return void
     */
    public function testAnalyzerCalculatesClassPropertiesIntoNcloc()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $class    = $packages->current()
            ->getClasses()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($class);
        self::assertEquals(10, $metrics['ncloc']);
    }

    /**
     * testAnalyzerNotCalculatesClassPropertiesIntoEloc
     *
     * @return void
     */
    public function testAnalyzerNotCalculatesClassPropertiesIntoEloc()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $class    = $packages->current()
            ->getClasses()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($class);
        self::assertEquals(0, $metrics['eloc']);
    }

    /**
     * Tests that the analyzer calculates the correct class file metrics.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectClassFileMetrics()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $file     = $packages->current()
            ->getClasses()
            ->current()
            ->getSourceFile();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $actual   = $analyzer->getNodeMetrics($file);
        $expected = array(
            'loc'    =>  21,
            'cloc'   =>  10,
            'eloc'   =>  8,
            'lloc'   =>  4,
            'ncloc'  =>  11
        );
        self::assertEquals($expected, $actual);
    }

    /**
     * testAnalyzerCalculatesCorrectClassMetrics
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectClassMetrics()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $class    = $packages->current()
            ->getClasses()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $actual   = $analyzer->getNodeMetrics($class);
        $expected = array(
            'loc'    =>  22,
            'cloc'   =>  7,
            'eloc'   =>  3,
            'lloc'   =>  1,
            'ncloc'  =>  15
        );
        self::assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates the correct interface file value.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectInterfaceFileLoc()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $file     = $packages->current()
            ->getInterfaces()
            ->current()
            ->getSourceFile();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $actual   = $analyzer->getNodeMetrics($file);
        $expected = array(
            'loc'    =>  21,
            'cloc'   =>  10,
            'eloc'   =>  8,
            'lloc'   =>  4,
            'ncloc'  =>  11
        );
        self::assertEquals($expected, $actual);
    }

    /**
     * testAnalyzerCalculatesCorrectInterfaceLoc
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectInterfaceLoc()
    {
        $packages  = self::parseTestCaseSource(__METHOD__);
        $interface = $packages->current()
            ->getInterfaces()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $actual   = $analyzer->getNodeMetrics($interface);
        $expected = array(
            'loc'    =>  17,
            'cloc'   =>  7,
            'eloc'   =>  0,
            'lloc'   =>  0,
            'ncloc'  =>  10
        );
        self::assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer aggregates the expected project values.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectProjectMetrics()
    {
        $packages = self::parseTestCaseSource(__METHOD__);

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $actual   = $analyzer->getProjectMetrics();
        $expected = array(
            'loc'    =>  260,
            'cloc'   =>  144,
            'eloc'   =>  89,
            'lloc'   =>  40,
            'ncloc'  =>  116
        );

        self::assertEquals($expected, $actual);
    }

    /**
     * testAnalyzerCalculatesElocOfZeroForAbstractMethod
     *
     * @return void
     */
    public function testAnalyzerCalculatesElocOfZeroForAbstractMethod()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $method   = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($method);
        self::assertEquals(0, $metrics['eloc']);
    }

    /**
     * testAnalyzerCalculatesElocOfZeroForInterfaceMethod
     *
     * @return void
     */
    public function testAnalyzerCalculatesElocOfZeroForInterfaceMethod()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $method   = $packages->current()
            ->getInterfaces()
            ->current()
            ->getMethods()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($method);
        self::assertEquals(0, $metrics['eloc']);
    }

    /**
     * testAnalyzerCalculatesClassConstantsIntoNcloc
     *
     * @return void
     */
    public function testAnalyzerCalculatesClassConstantsIntoNcloc()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $class    = $packages->current()
            ->getClasses()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($class);
        self::assertEquals(8, $metrics['ncloc']);
    }

    /**
     * testAnalyzerNotCalculatesClassConstantsIntoEloc
     *
     * @return void
     */
    public function testAnalyzerNotCalculatesClassConstantsIntoEloc()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $class    = $packages->current()
            ->getClasses()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($class);
        self::assertEquals(0, $metrics['eloc']);
    }

    /**
     * testCalculatesExpectedProjectLLocForFileWithInterfaces
     *
     * @return void
     */
    public function testCalculatesExpectedProjectLLocForFileWithInterfaces()
    {
        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $metrics = $analyzer->getProjectMetrics();
        self::assertEquals(1, $metrics['lloc']);
    }

    /**
     * testAnalyzerRestoresExpectedFileMetricsFromCache
     *
     * @return void
     * @since 1.0.0
     */
    public function testAnalyzerRestoresExpectedFileMetricsFromCache()
    {
        $packages = self::parseCodeResourceForTest();
        $file     = $packages->current()
            ->getClasses()
            ->current()
            ->getSourceFile();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics0 = $analyzer->getNodeMetrics($file);

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics1 = $analyzer->getNodeMetrics($file);

        $this->assertEquals($metrics0, $metrics1);
    }

    /**
     * testAnalyzerRestoresExpectedClassMetricsFromCache
     *
     * @return void
     * @since 1.0.0
     */
    public function testAnalyzerRestoresExpectedClassMetricsFromCache()
    {
        $packages = self::parseCodeResourceForTest();
        $class    = $packages->current()
            ->getClasses()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics0 = $analyzer->getNodeMetrics($class);

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics1 = $analyzer->getNodeMetrics($class);

        $this->assertEquals($metrics0, $metrics1);
    }

    /**
     * testAnalyzerRestoresExpectedInterfaceMetricsFromCache
     *
     * @return void
     * @since 1.0.0
     */
    public function testAnalyzerRestoresExpectedInterfaceMetricsFromCache()
    {
        $packages  = self::parseCodeResourceForTest();
        $interface = $packages->current()
            ->getInterfaces()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics0 = $analyzer->getNodeMetrics($interface);

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics1 = $analyzer->getNodeMetrics($interface);

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
     * testAnalyzerRestoresExpectedProjectMetricsFromCache
     *
     * @return void
     * @since 1.0.0
     */
    public function testAnalyzerRestoresExpectedProjectMetricsFromCache()
    {
        $packages = self::parseCodeResourceForTest();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics0 = $analyzer->getProjectMetrics();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics1 = $analyzer->getProjectMetrics();

        $this->assertEquals($metrics0, $metrics1);
    }

    /**
     * testCalculatesExpectedLLocForReturnStatement
     *
     * @return void
     */
    public function testCalculatesExpectedLLocForReturnStatement()
    {
        self::assertEquals(1, $this->_calculateFunctionMetric('lloc'));
    }

    /**
     * testCalculatesExpectedLLocForIfAndElseIfStatement
     *
     * @return void
     */
    public function testCalculatesExpectedLLocForIfAndElseIfStatement()
    {
        self::assertEquals(5, $this->_calculateFunctionMetric('lloc'));
    }

    /**
     * testCalculatesExpectedLLocForForStatement
     *
     * @return void
     */
    public function testCalculatesExpectedLLocForForStatement()
    {
        self::assertEquals(3, $this->_calculateFunctionMetric('lloc'));
    }

    /**
     * testCalculatesExpectedLLocForSwitchStatement
     *
     * @return void
     */
    public function testCalculatesExpectedLLocForSwitchStatement()
    {
        self::assertEquals(7, $this->_calculateFunctionMetric('lloc'));
    }

    /**
     * testCalculatesExpectedLLocForTryCatchStatement
     *
     * @return void
     */
    public function testCalculatesExpectedLLocForTryCatchStatement()
    {
        self::assertEquals(8, $this->_calculateFunctionMetric('lloc'));
    }

    /**
     * testCalculatesExpectedLLocForForeachStatement
     *
     * @return void
     */
    public function testCalculatesExpectedLLocForForeachStatement()
    {
        self::assertEquals(2, $this->_calculateFunctionMetric('lloc'));
    }

    /**
     * testCalculatesExpectedLLocForWhileStatement
     *
     * @return void
     */
    public function testCalculatesExpectedLLocForWhileStatement()
    {
        self::assertEquals(2, $this->_calculateFunctionMetric('lloc'));
    }

    /**
     * testCalculatesExpectedLLocForDoWhileStatement
     *
     * @return void
     */
    public function testCalculatesExpectedLLocForDoWhileStatement()
    {
        self::assertEquals(3, $this->_calculateFunctionMetric('lloc'));
    }

    /**
     * testAnalyzerIgnoresFilesWithoutFileName
     *
     * @return void
     */
    public function testAnalyzerIgnoresFilesWithoutFileName()
    {
        $file = new PHP_Depend_Code_File(null);
        $file->setUuid(42);

        $analyzer = $this->_createAnalyzer();
        $analyzer->visitFile($file);

        $metrics = $analyzer->getNodeMetrics($file);
        self::assertEquals(array(), $metrics);
    }

    /**
     * Calculates the metrics of the code under test that is associated with
     * the calling test case and returns the metric value for <b>$name</b>.
     *  
     * @param string $name The name of the requested metric.
     *
     * @return mixed
     * @since 0.10.2
     */
    private function _calculateFunctionMetric($name)
    {
        $packages = self::parseTestCaseSource(self::getCallingTestMethod());
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($function);
        return $metrics[$name];
    }

    /**
     * Creates a ready to use node loc analyzer.
     *
     * @return PHP_Depend_Metrics_NodeLoc_Analyzer
     * @since 1.0.0
     */
    private function _createAnalyzer()
    {
        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->setCache($this->_cache);

        return $analyzer;
    }
}
