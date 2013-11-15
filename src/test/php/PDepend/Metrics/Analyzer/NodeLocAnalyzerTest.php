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
use PDepend\Source\AST\ASTCompilationUnit;
use PDepend\Util\Cache\Driver\MemoryCacheDriver;

/**
 * Test case for the node lines of code analyzer.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Metrics\AbstractCachingAnalyzer
 * @covers \PDepend\Metrics\Analyzer\NodeLocAnalyzer
 * @group unittest
 */
class NodeLocAnalyzerTest extends AbstractMetricsTest
{
    /**
     * @var \PDepend\Util\Cache\CacheDriver
     * @since 1.0.0
     */
    private $cache;

    /**
     * Initializes a in memory cache.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->cache = new MemoryCacheDriver();
    }

    /**
     * testAnalyzerCalculatesCorrectFunctionMetrics
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectFunctionMetrics()
    {
        $namespaces  = self::parseTestCaseSource(__METHOD__);
        $functions = $namespaces->current()
            ->getFunctions();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

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
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * testAnalyzerCalculatesCorrectFunctionFileMetrics
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectFunctionFileMetrics()
    {
        $namespaces = self::parseTestCaseSource(__METHOD__);
        $file     = $namespaces->current()
            ->getFunctions()
            ->current()
            ->getCompilationUnit();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

        $actual   = $analyzer->getNodeMetrics($file);
        $expected = array(
            'loc'    =>  31,
            'cloc'   =>  15,
            'eloc'   =>  13,
            'lloc'   =>  4,
            'ncloc'  =>  16
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates the correct class, method and file
     * loc values.
     *
     * @return void
     */
    public function testAnalyzerCalculatesClassMethodsIntoNcloc()
    {
        $namespaces = self::parseTestCaseSource(__METHOD__);
        $class    = $namespaces->current()
            ->getClasses()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = $analyzer->getNodeMetrics($class);
        $this->assertEquals(18, $metrics['ncloc']);
    }

    /**
     * testAnalyzerCalculatesClassPropertiesIntoNcloc
     *
     * @return void
     */
    public function testAnalyzerCalculatesClassPropertiesIntoNcloc()
    {
        $namespaces = self::parseTestCaseSource(__METHOD__);
        $class    = $namespaces->current()
            ->getClasses()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = $analyzer->getNodeMetrics($class);
        $this->assertEquals(10, $metrics['ncloc']);
    }

    /**
     * testAnalyzerNotCalculatesClassPropertiesIntoEloc
     *
     * @return void
     */
    public function testAnalyzerNotCalculatesClassPropertiesIntoEloc()
    {
        $namespaces = self::parseTestCaseSource(__METHOD__);
        $class    = $namespaces->current()
            ->getClasses()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = $analyzer->getNodeMetrics($class);
        $this->assertEquals(0, $metrics['eloc']);
    }

    /**
     * Tests that the analyzer calculates the correct class file metrics.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectClassFileMetrics()
    {
        $namespaces = self::parseTestCaseSource(__METHOD__);
        $file     = $namespaces->current()
            ->getClasses()
            ->current()
            ->getCompilationUnit();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

        $actual   = $analyzer->getNodeMetrics($file);
        $expected = array(
            'loc'    =>  21,
            'cloc'   =>  10,
            'eloc'   =>  8,
            'lloc'   =>  4,
            'ncloc'  =>  11
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * testAnalyzerCalculatesCorrectClassMetrics
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectClassMetrics()
    {
        $namespaces = self::parseTestCaseSource(__METHOD__);
        $class    = $namespaces->current()
            ->getClasses()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

        $actual   = $analyzer->getNodeMetrics($class);
        $expected = array(
            'loc'    =>  22,
            'cloc'   =>  7,
            'eloc'   =>  3,
            'lloc'   =>  1,
            'ncloc'  =>  15
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates the correct interface file value.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectInterfaceFileLoc()
    {
        $namespaces = self::parseTestCaseSource(__METHOD__);
        $file     = $namespaces->current()
            ->getInterfaces()
            ->current()
            ->getCompilationUnit();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

        $actual   = $analyzer->getNodeMetrics($file);
        $expected = array(
            'loc'    =>  21,
            'cloc'   =>  10,
            'eloc'   =>  8,
            'lloc'   =>  4,
            'ncloc'  =>  11
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * testAnalyzerCalculatesCorrectInterfaceLoc
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectInterfaceLoc()
    {
        $namespaces = self::parseTestCaseSource(__METHOD__);
        $interface = $namespaces->current()
            ->getInterfaces()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

        $actual   = $analyzer->getNodeMetrics($interface);
        $expected = array(
            'loc'    =>  17,
            'cloc'   =>  7,
            'eloc'   =>  0,
            'lloc'   =>  0,
            'ncloc'  =>  10
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer aggregates the expected project values.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectProjectMetrics()
    {
        $namespaces = self::parseTestCaseSource(__METHOD__);

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

        $actual   = $analyzer->getProjectMetrics();
        $expected = array(
            'loc'    =>  261,
            'cloc'   =>  144,
            'eloc'   =>  89,
            'lloc'   =>  40,
            'ncloc'  =>  117
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testAnalyzerCalculatesElocOfZeroForAbstractMethod
     *
     * @return void
     */
    public function testAnalyzerCalculatesElocOfZeroForAbstractMethod()
    {
        $namespaces = self::parseTestCaseSource(__METHOD__);
        $method   = $namespaces->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = $analyzer->getNodeMetrics($method);
        $this->assertEquals(0, $metrics['eloc']);
    }

    /**
     * testAnalyzerCalculatesElocOfZeroForInterfaceMethod
     *
     * @return void
     */
    public function testAnalyzerCalculatesElocOfZeroForInterfaceMethod()
    {
        $namespaces = self::parseTestCaseSource(__METHOD__);
        $method   = $namespaces->current()
            ->getInterfaces()
            ->current()
            ->getMethods()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = $analyzer->getNodeMetrics($method);
        $this->assertEquals(0, $metrics['eloc']);
    }

    /**
     * testAnalyzerCalculatesClassConstantsIntoNcloc
     *
     * @return void
     */
    public function testAnalyzerCalculatesClassConstantsIntoNcloc()
    {
        $namespaces = self::parseTestCaseSource(__METHOD__);
        $class    = $namespaces->current()
            ->getClasses()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = $analyzer->getNodeMetrics($class);
        $this->assertEquals(8, $metrics['ncloc']);
    }

    /**
     * testAnalyzerNotCalculatesClassConstantsIntoEloc
     *
     * @return void
     */
    public function testAnalyzerNotCalculatesClassConstantsIntoEloc()
    {
        $namespaces = self::parseTestCaseSource(__METHOD__);
        $class    = $namespaces->current()
            ->getClasses()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = $analyzer->getNodeMetrics($class);
        $this->assertEquals(0, $metrics['eloc']);
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
        $this->assertEquals(1, $metrics['lloc']);
    }

    /**
     * testAnalyzerRestoresExpectedFileMetricsFromCache
     *
     * @return void
     * @since 1.0.0
     */
    public function testAnalyzerRestoresExpectedFileMetricsFromCache()
    {
        $namespaces = self::parseCodeResourceForTest();
        $file     = $namespaces->current()
            ->getClasses()
            ->current()
            ->getCompilationUnit();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics0 = $analyzer->getNodeMetrics($file);

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

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
        $namespaces = self::parseCodeResourceForTest();
        $class    = $namespaces->current()
            ->getClasses()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics0 = $analyzer->getNodeMetrics($class);

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

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
        $namespaces  = self::parseCodeResourceForTest();
        $interface = $namespaces->current()
            ->getInterfaces()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics0 = $analyzer->getNodeMetrics($interface);

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

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
        $namespaces = self::parseCodeResourceForTest();
        $method   = $namespaces->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics0 = $analyzer->getNodeMetrics($method);

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

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
        $namespaces = self::parseCodeResourceForTest();
        $function = $namespaces->current()
            ->getFunctions()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics0 = $analyzer->getNodeMetrics($function);

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

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
        $namespaces = self::parseCodeResourceForTest();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics0 = $analyzer->getProjectMetrics();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

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
        $this->assertEquals(1, $this->_calculateFunctionMetric('lloc'));
    }

    /**
     * testCalculatesExpectedLLocForIfAndElseIfStatement
     *
     * @return void
     */
    public function testCalculatesExpectedLLocForIfAndElseIfStatement()
    {
        $this->assertEquals(5, $this->_calculateFunctionMetric('lloc'));
    }

    /**
     * testCalculatesExpectedLLocForForStatement
     *
     * @return void
     */
    public function testCalculatesExpectedLLocForForStatement()
    {
        $this->assertEquals(3, $this->_calculateFunctionMetric('lloc'));
    }

    /**
     * testCalculatesExpectedLLocForSwitchStatement
     *
     * @return void
     */
    public function testCalculatesExpectedLLocForSwitchStatement()
    {
        $this->assertEquals(7, $this->_calculateFunctionMetric('lloc'));
    }

    /**
     * testCalculatesExpectedLLocForTryCatchStatement
     *
     * @return void
     */
    public function testCalculatesExpectedLLocForTryCatchStatement()
    {
        $this->assertEquals(8, $this->_calculateFunctionMetric('lloc'));
    }

    /**
     * testCalculatesExpectedLLocForForeachStatement
     *
     * @return void
     */
    public function testCalculatesExpectedLLocForForeachStatement()
    {
        $this->assertEquals(2, $this->_calculateFunctionMetric('lloc'));
    }

    /**
     * testCalculatesExpectedLLocForWhileStatement
     *
     * @return void
     */
    public function testCalculatesExpectedLLocForWhileStatement()
    {
        $this->assertEquals(2, $this->_calculateFunctionMetric('lloc'));
    }

    /**
     * testCalculatesExpectedLLocForDoWhileStatement
     *
     * @return void
     */
    public function testCalculatesExpectedLLocForDoWhileStatement()
    {
        $this->assertEquals(3, $this->_calculateFunctionMetric('lloc'));
    }

    /**
     * testAnalyzerIgnoresFilesWithoutFileName
     *
     * @return void
     */
    public function testAnalyzerIgnoresFilesWithoutFileName()
    {
        $compilationUnit = new ASTCompilationUnit(null);
        $compilationUnit->setId(42);

        $analyzer = $this->_createAnalyzer();
        $analyzer->visitCompilationUnit($compilationUnit);

        $metrics = $analyzer->getNodeMetrics($compilationUnit);
        $this->assertEquals(array(), $metrics);
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
        $namespaces = self::parseTestCaseSource(self::getCallingTestMethod());
        $function = $namespaces->current()
            ->getFunctions()
            ->current();

        $analyzer = $this->_createAnalyzer();
        $analyzer->analyze($namespaces);

        $metrics = $analyzer->getNodeMetrics($function);
        return $metrics[$name];
    }

    /**
     * Creates a ready to use node loc analyzer.
     *
     * @return \PDepend\Metrics\Analyzer\NodeLocAnalyzer
     * @since 1.0.0
     */
    private function _createAnalyzer()
    {
        $analyzer = new NodeLocAnalyzer();
        $analyzer->setCache($this->cache);

        return $analyzer;
    }
}
