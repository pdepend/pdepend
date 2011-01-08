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

require_once 'PHP/Depend/Metrics/NodeLoc/Analyzer.php';

/**
 * Test case for the node lines of code analyzer.
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
class PHP_Depend_Metrics_NodeLoc_AnalyzerTest extends PHP_Depend_Metrics_AbstractTest
{
    /**
     * testAnalyzerCalculatesCorrectFunctionMetrics
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testAnalyzerCalculatesCorrectFunctionMetrics()
    {
        $packages  = self::parseTestCaseSource(__METHOD__);
        $functions = $packages->current()
            ->getFunctions();

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
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
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * testAnalyzerCalculatesCorrectFunctionFileMetrics
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testAnalyzerCalculatesCorrectFunctionFileMetrics()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $file     = $packages->current()
            ->getFunctions()
            ->current()
            ->getSourceFile();

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

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
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testAnalyzerCalculatesClassMethodsIntoNcloc()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $class    = $packages->current()
            ->getClasses()
            ->current();

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($class);
        $this->assertEquals(18, $metrics['ncloc']);
    }

    /**
     * testAnalyzerCalculatesClassPropertiesIntoNcloc
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testAnalyzerCalculatesClassPropertiesIntoNcloc()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $class    = $packages->current()
            ->getClasses()
            ->current();

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($class);
        $this->assertEquals(10, $metrics['ncloc']);
    }

    /**
     * testAnalyzerNotCalculatesClassPropertiesIntoEloc
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testAnalyzerNotCalculatesClassPropertiesIntoEloc()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $class    = $packages->current()
            ->getClasses()
            ->current();

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($class);
        $this->assertEquals(0, $metrics['eloc']);
    }

    /**
     * Tests that the analyzer calculates the correct class file metrics.
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testAnalyzerCalculatesCorrectClassFileMetrics()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $file     = $packages->current()
            ->getClasses()
            ->current()
            ->getSourceFile();

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

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
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testAnalyzerCalculatesCorrectClassMetrics()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $class    = $packages->current()
            ->getClasses()
            ->current();

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

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
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testAnalyzerCalculatesCorrectInterfaceFileLoc()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $file     = $packages->current()
            ->getInterfaces()
            ->current()
            ->getSourceFile();

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

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
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testAnalyzerCalculatesCorrectInterfaceLoc()
    {
        $packages  = self::parseTestCaseSource(__METHOD__);
        $interface = $packages->current()
            ->getInterfaces()
            ->current();

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

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
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testAnalyzerCalculatesCorrectProjectMetrics()
    {
        $packages = self::parseSource('metrics/NodeLoc/' . __FUNCTION__);

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $actual   = $analyzer->getProjectMetrics();
        $expected = array(
            'loc'    =>  260,
            'cloc'   =>  144,
            'eloc'   =>  89,
            'lloc'   =>  40,
            'ncloc'  =>  116
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testAnalyzerCalculatesElocOfZeroForAbstractMethod
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testAnalyzerCalculatesElocOfZeroForAbstractMethod()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $method   = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($method);
        $this->assertEquals(0, $metrics['eloc']);
    }

    /**
     * testAnalyzerCalculatesElocOfZeroForInterfaceMethod
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testAnalyzerCalculatesElocOfZeroForInterfaceMethod()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $method   = $packages->current()
            ->getInterfaces()
            ->current()
            ->getMethods()
            ->current();

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($method);
        $this->assertEquals(0, $metrics['eloc']);
    }

    /**
     * testAnalyzerCalculatesClassConstantsIntoNcloc
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group   pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testAnalyzerCalculatesClassConstantsIntoNcloc()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $class    = $packages->current()
            ->getClasses()
            ->current();

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($class);
        $this->assertEquals(8, $metrics['ncloc']);
    }

    /**
     * testAnalyzerNotCalculatesClassConstantsIntoEloc
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testAnalyzerNotCalculatesClassConstantsIntoEloc()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $class    = $packages->current()
            ->getClasses()
            ->current();

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($class);
        $this->assertEquals(0, $metrics['eloc']);
    }

    /**
     * testCalculatesExpectedProjectLLocForFileWithInterfaces
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testCalculatesExpectedProjectLLocForFileWithInterfaces()
    {
        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $metrics = $analyzer->getProjectMetrics();
        $this->assertEquals(1, $metrics['lloc']);
    }

    /**
     * testCalculatesExpectedLLocForReturnStatement
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testCalculatesExpectedLLocForReturnStatement()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($function);
        $this->assertEquals(1, $metrics['lloc']);
    }

    /**
     * testCalculatesExpectedLLocForIfAndElseIfStatement
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testCalculatesExpectedLLocForIfAndElseIfStatement()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($function);
        $this->assertEquals(5, $metrics['lloc']);
    }

    /**
     * testCalculatesExpectedLLocForForStatement
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testCalculatesExpectedLLocForForStatement()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($function);
        $this->assertEquals(3, $metrics['lloc']);
    }

    /**
     * testCalculatesExpectedLLocForSwitchStatement
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testCalculatesExpectedLLocForSwitchStatement()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($function);
        $this->assertEquals(7, $metrics['lloc']);
    }

    /**
     * testCalculatesExpectedLLocForTryCatchStatement
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testCalculatesExpectedLLocForTryCatchStatement()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($function);
        $this->assertEquals(8, $metrics['lloc']);
    }

    /**
     * testCalculatesExpectedLLocForForeachStatement
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testCalculatesExpectedLLocForForeachStatement()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($function);
        $this->assertEquals(2, $metrics['lloc']);
    }

    /**
     * testCalculatesExpectedLLocForWhileStatement
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testCalculatesExpectedLLocForWhileStatement()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($function);
        $this->assertEquals(2, $metrics['lloc']);
    }

    /**
     * testCalculatesExpectedLLocForDoWhileStatement
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testCalculatesExpectedLLocForDoWhileStatement()
    {
        $packages = self::parseTestCaseSource(__METHOD__);
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $metrics = $analyzer->getNodeMetrics($function);
        $this->assertEquals(3, $metrics['lloc']);
    }

    /**
     * testAnalyzerIgnoresFilesWithoutFileName
     *
     * @return void
     * @covers PHP_Depend_Metrics_NodeLoc_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::nodeloc
     * @group unittest
     */
    public function testAnalyzerIgnoresFilesWithoutFileName()
    {
        $file = new PHP_Depend_Code_File(null);
        $file->setUUID(42);

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->visitFile($file);

        $metrics = $analyzer->getNodeMetrics($file);
        $this->assertEquals(array(), $metrics);
    }
}
