<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/Depend/Metrics/NodeCount/Analyzer.php';

/**
 * Test case for the node count analyzer.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_NodeCount_AnalyzerTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the analyzer calculates the correct number of packages value.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectNumberOfPackages()
    {
        $packages = self::parseSource('/metrics/node-count/packages.php');
        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze($packages);
        
        $project = $analyzer->getProjectMetrics();
        
        $this->assertArrayHasKey('nop', $project);
        $this->assertEquals(3, $project['nop']);
    }
    
    /**
     * Tests that the analyzer calculates the correct number of classes values.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectNumberOfClasses()
    {
        $packages = self::parseSource('/metrics/node-count/classes.php');
        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze($packages);
        
        $project = $analyzer->getProjectMetrics();
        
        $this->assertArrayHasKey('noc', $project);
        $this->assertEquals(6, $project['noc']);
        
        $expected = array('A' => 3, 'B' => 2, 'C' => 1);
        foreach ($packages as $package) {
            // Get package name
            $name = $package->getName();
            // Check for valid package
            $this->assertArrayHasKey($name, $expected);
            // Get node metrics
            $metrics = $analyzer->getNodeMetrics($package);
            // Check for noc key
            $this->assertArrayHasKey('noc', $metrics);
            // Check noc value
            $this->assertEquals($expected[$name], $metrics['noc']);
            // Drop offset
            unset($expected[$name]);            
        }
        
        $this->assertEquals(0, count($expected));
    }
    
    /**
     * Tests that the analyzer calculates the correct number of interfaces values.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectNumberOfInterfaces()
    {
        $packages = self::parseSource('/metrics/node-count/interfaces.php');
        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze($packages);
        
        $project = $analyzer->getProjectMetrics();
        
        $this->assertArrayHasKey('noi', $project);
        $this->assertEquals(4, $project['noi']);
        
        $expected = array('A' => 1, 'B' => 2, 'C' => 1);
        foreach ($packages as $package) {
            // Get package identifier
            $name = $package->getName();
            // Check for valid package
            $this->assertArrayHasKey($name, $expected);
            // Get node metrics
            $metrics = $analyzer->getNodeMetrics($package);
            // Check for noi key
            $this->assertArrayHasKey('noi', $metrics);
            // Check noi value
            $this->assertEquals($expected[$name], $metrics['noi']);
            // Drop offset
            unset($expected[$name]);
        }
        
        $this->assertEquals(0, count($expected));
    }
    
    /**
     * Tests that the analyzer calculates the correct number of methods values.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectNumberOfMethods()
    {
        $packages = self::parseSource('/metrics/node-count/methods.php');
        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze($packages);
        
        $project = $analyzer->getProjectMetrics();

        $this->assertArrayHasKey('nom', $project);
        $this->assertEquals(9, $project['nom']);
        
        $expected = array(
            'A'         => 4, 
            'B'         => 3, 
            'C'         => 2,
            'clsa1'     => 2,
            'clsa2'     => 1,
            'interfsa3' => 1,
            'interfsb1' => 2,
            'interfsb2' => 1,
            'interfsc1' => 2
        );
        
        foreach ($packages as $package) {
            // Get package name
            $name = $package->getName();
            // Assert valid package name
            $this->assertArrayHasKey($name, $expected);
            // Get node metric
            $metrics = $analyzer->getNodeMetrics($package);
            // Check for nom key
            $this->assertArrayHasKey('nom', $metrics);
            // Check nom value
            $this->assertEquals($expected[$name], $metrics['nom']);
            // Remove package offset
            unset($expected[$name]);
            
            // Check all children
            foreach ($package->getTypes() as $type) {
                // Get type name
                $name = $type->getName();
                // Assert valid type name
                $this->assertArrayHasKey($name, $expected);
                // Get node metric
                $metrics = $analyzer->getNodeMetrics($type);
                // Check for nom key
                $this->assertArrayHasKey('nom', $metrics);
                // Check nom value
                $this->assertEquals($expected[$name], $metrics['nom']);
                // Remove type offset
                unset($expected[$name]);                
            }
        }
        
        $this->assertEquals(0, count($expected));        
    }
    
    /**
     * Tests that the analyzer calculates the correct number of functions values.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectNumberOfFunctions()
    {
        $packages = self::parseSource('/metrics/node-count/functions/');
        $analyzer = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer->analyze($packages);

        $project = $analyzer->getProjectMetrics();
        
        $this->assertArrayHasKey('nof', $project);
        $this->assertEquals(6, $project['nof']);
        
        $expected = array('A' => 3, 'B' => 2, 'C' => 1);
        foreach ($packages as $package) {
            // Get package identifier
            $name = $package->getName();
            // Check for valid package
            $this->assertArrayHasKey($name, $expected);
            // Get node metrics
            $metrics = $analyzer->getNodeMetrics($package);
            // Check for nof key
            $this->assertArrayHasKey('nof', $metrics);
            // Check nof value
            $this->assertEquals($expected[$name], $metrics['nof']);
            // Drop offset
            unset($expected[$name]);
        }
        
        $this->assertEquals(0, count($expected));
    }
}