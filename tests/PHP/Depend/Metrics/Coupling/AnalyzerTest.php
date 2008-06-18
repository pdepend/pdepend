<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/Depend/Metrics/Coupling/Analyzer.php';

/**
 * Test case for the coupling analyzer.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_Coupling_AnalyzerTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * functions.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectFunctionCoupling()
    {
        $source   = dirname(__FILE__) . '/../../_code/coupling/function.php';
        $packages = self::parseSource($source);
        
        $this->assertEquals(2, $packages->count());
        $this->assertEquals(2, $packages->current()->getFunctions()->count());
        
        $analyzer = new PHP_Depend_Metrics_Coupling_Analyzer();
        $analyzer->analyze($packages);
        
        $project = $analyzer->getProjectMetrics();
        
        $this->assertArrayHasKey('fanout', $project);
        $this->assertEquals(7, $project['fanout']);
        
        $this->assertArrayHasKey('calls', $project);
        $this->assertEquals(10, $project['calls']);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * methods.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectMethodCoupling()
    {
        $source   = dirname(__FILE__) . '/../../_code/coupling/method.php';
        $packages = self::parseSource($source);
        
        $this->assertEquals(2, $packages->count());
        $this->assertEquals(1, $packages->current()->getClasses()->count());
        $this->assertEquals(1, $packages->current()->getInterfaces()->count());
        
        $analyzer = new PHP_Depend_Metrics_Coupling_Analyzer();
        $analyzer->analyze($packages);
        
        $project = $analyzer->getProjectMetrics();
        
        $this->assertArrayHasKey('fanout', $project);
        $this->assertEquals(9, $project['fanout']);
        
        $this->assertArrayHasKey('calls', $project);
        $this->assertEquals(10, $project['calls']);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * properties.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectPropertyCoupling()
    {
        $source   = dirname(__FILE__) . '/../../_code/coupling/property.php';
        $packages = self::parseSource($source);
        
        $this->assertEquals(2, $packages->count());
        $this->assertEquals(1, $packages->current()->getClasses()->count());
        
        $analyzer = new PHP_Depend_Metrics_Coupling_Analyzer();
        $analyzer->analyze($packages);
        
        $project = $analyzer->getProjectMetrics();
        
        $this->assertArrayHasKey('fanout', $project);
        $this->assertEquals(3, $project['fanout']);
        
        $this->assertArrayHasKey('calls', $project);
        $this->assertEquals(0, $project['calls']);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * properties.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectClassCoupling()
    {
        $source   = dirname(__FILE__) . '/../../_code/coupling/class.php';
        $packages = self::parseSource($source);
        
        $this->assertEquals(2, $packages->count());
        $this->assertEquals(1, $packages->current()->getClasses()->count());
        $this->assertEquals(1, $packages->current()->getInterfaces()->count());
        
        $analyzer = new PHP_Depend_Metrics_Coupling_Analyzer();
        $analyzer->analyze($packages);
        
        $project = $analyzer->getProjectMetrics();
        
        $this->assertArrayHasKey('fanout', $project);
        $this->assertEquals(12, $project['fanout']);
        
        $this->assertArrayHasKey('calls', $project);
        $this->assertEquals(10, $project['calls']);
    }

    /**
     * Tests that the analyzer calculates correct fanout and call metrics for
     * complete source.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectCoupling()
    {
        $source   = dirname(__FILE__) . '/../../_code/coupling';
        $packages = self::parseSource($source);
        
        $this->assertEquals(2, $packages->count());
        $this->assertEquals(3, $packages->current()->getClasses()->count());
        $this->assertEquals(2, $packages->current()->getInterfaces()->count());
        $this->assertEquals(2, $packages->current()->getFunctions()->count());
        
        $analyzer = new PHP_Depend_Metrics_Coupling_Analyzer();
        $analyzer->analyze($packages);
        
        $project = $analyzer->getProjectMetrics();
        
        $this->assertArrayHasKey('fanout', $project);
        $this->assertEquals(31, $project['fanout']);
        
        $this->assertArrayHasKey('calls', $project);
        $this->assertEquals(30, $project['calls']);
    }
    
    /**
     * Test case for the execution chain bug 14.
     * 
     * http://bugs.xplib.de/index.php?do=details&task_id=14&project=3
     *
     * @return void
     */
    public function testAnalyzerExecutionChainBug14()
    {
        $source   = dirname(__FILE__) . '/../../_code/bugs/14.php';
        $packages = self::parseSource($source);
        
        $this->assertEquals(1, $packages->count());
        $this->assertEquals(1, $packages->current()->getFunctions()->count());
        
        $analyzer = new PHP_Depend_Metrics_Coupling_Analyzer();
        $analyzer->analyze($packages);
        
        $project = $analyzer->getProjectMetrics();
        
        $this->assertArrayHasKey('calls', $project);
        $this->assertEquals(3, $project['calls']);
    }
}