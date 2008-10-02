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
require_once dirname(__FILE__) . '/_dummy/TestImplAnalyzer.php';

require_once 'PHP/Depend/Metrics/ClassLevel/Analyzer.php';
require_once 'PHP/Depend/Metrics/CyclomaticComplexity/Analyzer.php';

/**
 * Test case for the class level analyzer.
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
class PHP_Depend_Metrics_ClassLevel_AnalyzerTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the {@link PHP_Depend_Metrics_ClassLevel_Analyzer::analyzer()}
     * method fails with an exception if no cc analyzer was set.
     *
     * @return void
     */
    public function testAnalyzerFailsWithoutCCAnalyzerFail()
    {
        $packages = self::parseSource('/metrics/class-level/simple-package');
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        
        $this->setExpectedException('RuntimeException', 'Missing required CC analyzer.');
        
        $analyzer->analyze($packages);
    }
    
    /**
     * Tests that {@link PHP_Depend_Metrics_ClassLevel_Analyzer::addAnalyzer()}
     * fails for an invalid child analyzer.
     *
     * @return void
     */
    public function testAddAnalyzerFailsForAnInvalidAnalyzerTypeFail()
    {
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $this->setExpectedException('InvalidArgumentException', 'CC Analyzer required.');
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_ClassLevel_TestImplAnalyzer());
    }
    
    /**
     * Tests that the analyzer calculates the correct DIT values.
     *
     * @return void
     */
    public function testCalculateDITMetric()
    {
        $packages = self::parseSource('/metrics/class-level/dit/');
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);
        
        $packages->rewind();
        
        $expected = array('A' => 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 3, 'F' => 4);
        foreach ($packages->current()->getClasses() as $class) {
            // Get class name
            $name = $class->getName();
            // Check name is valid
            $this->assertArrayHasKey($name, $expected);
            // Fetch class metric
            $metric = $analyzer->getNodeMetrics($class);
            // Check dit exists
            $this->assertArrayHasKey('dit', $metric);
            // Compare values
            $this->assertEquals($expected[$name], $metric['dit']);
            // Remove offset
            unset($expected[$name]);
        }
        // Check that we test all
        $this->assertEquals(0, count($expected));
    }
    
    /**
     * Tests that the analyzer calculates the correct IMPL values.
     *
     * @return void
     */
    public function testCalculateIMPLMetric()
    {
        $packages = self::parseSource('/metrics/class-level/impl/');
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);
        
        $expected = array('A' => 4, 'B' => 6, 'C' => 2);
        foreach ($packages as $package) {
            foreach ($package->getClasses() as $class) {
                // Get class name
                $name = $class->getName();
                // Check for valid class name
                $this->assertArrayHasKey($name, $expected);
                // Get class metric
                $metric = $analyzer->getNodeMetrics($class);
                // Check for impl key
                $this->assertArrayHasKey('impl', $metric);
                // Compare values
                $this->assertEquals($expected[$name], $metric['impl']);
                // Remove offset
                unset($expected[$name]);
            }
        }
        // Check that we catch all
        $this->assertEquals(0, count($expected));
    }
    
    /**
     * Tests that the calculated Class Interface Size(CSI) is correct.
     *
     * @return void
     */
    public function testCalculateCISMetric()
    {
        $packages = self::parseSource('/metrics/class-level/cis/');
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);
        
        $expected = array('A' => 2, 'B' => 2, 'C' => 3);
        foreach ($packages as $package) {
            foreach ($package->getClasses() as $class) {
                // Get class name
                $name = $class->getName();
                // Check for valid class name
                $this->assertArrayHasKey($name, $expected);
                // Get class metric
                $metric = $analyzer->getNodeMetrics($class);
                // Check for cis key
                $this->assertArrayHasKey('cis', $metric);
                // Compare values
                $this->assertEquals($expected[$name], $metric['cis']);
                // Remove offset
                unset($expected[$name]);
            }
        }
        // Check that we catch all
        $this->assertEquals(0, count($expected));
    }
    
    /**
     * Tests that the calculated Class SiZe(CSZ) metric is correct.
     *
     * @return void
     */
    public function testCalculateCSZMetric()
    {
        $packages = self::parseSource('/metrics/class-level/csz');
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);
        
        $expected = array('A' => 3, 'B' => 5, 'C' => 1, 'I' => true);
        foreach ($packages as $package) {
            // Check classes
            foreach ($package->getClasses() as $class) {
                // Get class name
                $name = $class->getName();
                // Check for valid class name
                $this->assertArrayHasKey($name, $expected);
                // Get class metric
                $metric = $analyzer->getNodeMetrics($class);
                // Check for csz key
                $this->assertArrayHasKey('csz', $metric);
                // Compare values
                $this->assertEquals($expected[$name], $metric['csz']);
                // Remove offset
                unset($expected[$name]);
            }
            // Check interfaces
            foreach ($package->getInterfaces() as $interface) {
                // Get interface name
                $name = $interface->getName();
                // Check for valid interface name
                $this->assertArrayHasKey($name, $expected);
                // Get empty metric
                $metric = $analyzer->getNodeMetrics($interface);
                // Check that empty is array
                $this->assertEquals(array(), $metric);
                // Remove offset
                unset($expected[$name]);
            }
        }
        // Check that all match
        $this->assertEquals(0, count($expected));
    }
    
    /**
     * Tests that the analyzer calculates the correct VARS metric
     *
     * @return void
     */
    public function testCalculateVARSMetric()
    {
        $packages = self::parseSource('/metrics/class-level/vars');
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);
        
        $expected = array('A' => 2, 'B' => 3, 'C' => 0, 'I' => 0);
        foreach ($packages as $package) {
            // Check classes
            foreach ($package->getClasses() as $class) {
                // Get class name
                $name = $class->getName();
                // Check for valid class name
                $this->assertArrayHasKey($name, $expected);
                // Get class metric
                $metric = $analyzer->getNodeMetrics($class);
                // Check for vars key
                $this->assertArrayHasKey('vars', $metric);
                // Compare values
                $this->assertEquals($expected[$name], $metric['vars']);
                // Remove offset
                unset($expected[$name]);
            }
            // Check interfaces
            foreach ($package->getInterfaces() as $interface) {
                // Get interface name
                $name = $interface->getName();
                // Check for valid interface name
                $this->assertArrayHasKey($name, $expected);
                // Get empty metric
                $metric = $analyzer->getNodeMetrics($interface);
                // Check that empty is array
                $this->assertEquals(array(), $metric);
                // Remove offset
                unset($expected[$name]);
            }
        }
        // Check that all match
        $this->assertEquals(0, count($expected));
    }
    
    /**
     * Tests that the analyzer calculates the correct VARSi metric
     *
     * @return void
     */
    public function testCalculateVARSiMetric()
    {
        $packages = self::parseSource('/metrics/class-level/varsi');
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);

        $expected = array('A' => 2, 'B' => 4, 'C' => 4, 'I' => 0);
        foreach ($packages as $package) {
            // Check classes
            foreach ($package->getClasses() as $class) {
                // Get class name
                $name = $class->getName();
                // Check for valid class name
                $this->assertArrayHasKey($name, $expected);
                // Get class metric
                $metric = $analyzer->getNodeMetrics($class);
                // Check for varsi key
                $this->assertArrayHasKey('varsi', $metric);
                // Compare values
                $this->assertEquals($expected[$name], $metric['varsi'], $name);
                // Remove offset
                unset($expected[$name]);
            }
            // Check interfaces
            foreach ($package->getInterfaces() as $interface) {
                // Get interface name
                $name = $interface->getName();
                // Check for valid interface name
                $this->assertArrayHasKey($name, $expected);
                // Get empty metric
                $metric = $analyzer->getNodeMetrics($interface);
                // Check that empty is array
                $this->assertEquals(array(), $metric);
                // Remove offset
                unset($expected[$name]);
            }
        }
        // Check that all match
        $this->assertEquals(0, count($expected));
    }
    
    /**
     * Tests that the analyzer calculates the correct VARSnp metric
     *
     * @return void
     */
    public function testCalculateVARSnpMetric()
    {
        $packages = self::parseSource('/metrics/class-level/varsnp/');
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);
        
        $expected = array('A' => 1, 'B' => 2, 'C' => 0, 'I' => 0);
        foreach ($packages as $package) {
            // Check classes
            foreach ($package->getClasses() as $class) {
                // Get class name
                $name = $class->getName();
                // Check for valid class name
                $this->assertArrayHasKey($name, $expected);
                // Get class metric
                $metric = $analyzer->getNodeMetrics($class);
                // Check for varsnp key
                $this->assertArrayHasKey('varsnp', $metric);
                // Compare values
                $this->assertEquals($expected[$name], $metric['varsnp'], $name);
                // Remove offset
                unset($expected[$name]);
            }
            // Check interfaces
            foreach ($package->getInterfaces() as $interface) {
                // Get interface name
                $name = $interface->getName();
                // Check for valid interface name
                $this->assertArrayHasKey($name, $expected);
                // Get empty metric
                $metric = $analyzer->getNodeMetrics($interface);
                // Check that empty is array
                $this->assertEquals(array(), $metric);
                // Remove offset
                unset($expected[$name]);
            }
        }
        // Check that all match
        $this->assertEquals(0, count($expected));
    }
    
    /**
     * Tests that the analyzer calculates the correct WMC metric. 
     *
     * @return void
     */
    public function testCalculateWMCMetric()
    {
        $packages = self::parseSource('/metrics/class-level/wmc/');
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);
        
        $expected = array('A' => 2, 'B' => 4, 'C' => 4, 'I' => 0);
        foreach ($packages as $package) {
            // Check classes
            foreach ($package->getClasses() as $class) {
                // Get class name
                $name = $class->getName();
                // Check for valid class name
                $this->assertArrayHasKey($name, $expected);
                // Get class metric
                $metric = $analyzer->getNodeMetrics($class);
                // Check for wmc key
                $this->assertArrayHasKey('wmc', $metric);
                // Compare values
                $this->assertEquals($expected[$name], $metric['wmc'], $name);
                // Remove offset
                unset($expected[$name]);
            }
            // Check interfaces
            foreach ($package->getInterfaces() as $interface) {
                // Get interface name
                $name = $interface->getName();
                // Check for valid interface name
                $this->assertArrayHasKey($name, $expected);
                // Get empty metric
                $metric = $analyzer->getNodeMetrics($interface);
                // Check that empty is array
                $this->assertEquals(array(), $metric);
                // Remove offset
                unset($expected[$name]);
            }
        }
        // Check that all match
        $this->assertEquals(0, count($expected));
    }
    
    /**
     * Tests that the analyzer calculates the correct WMCi metric. 
     *
     * @return void
     */    
    public function testCalculateWMCiMetric()
    {
        $packages = self::parseSource('/metrics/class-level/wmci/');
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);
        
        $expected = array('A' => 2, 'B' => 4, 'C' => 5, 'I' => 0);
        foreach ($packages as $package) {
            // Check classes
            foreach ($package->getClasses() as $class) {
                // Get class name
                $name = $class->getName();
                // Check for valid class name
                $this->assertArrayHasKey($name, $expected);
                // Get class metric
                $metric = $analyzer->getNodeMetrics($class);
                // Check for wmci key
                $this->assertArrayHasKey('wmci', $metric);
                // Compare values
                $this->assertEquals($expected[$name], $metric['wmci'], $name);
                // Remove offset
                unset($expected[$name]);
            }
            // Check interfaces
            foreach ($package->getInterfaces() as $interface) {
                // Get interface name
                $name = $interface->getName();
                // Check for valid interface name
                $this->assertArrayHasKey($name, $expected);
                // Get empty metric
                $metric = $analyzer->getNodeMetrics($interface);
                // Check that empty is array
                $this->assertEquals(array(), $metric);
                // Remove offset
                unset($expected[$name]);
            }
        }
        // Check that all match
        $this->assertEquals(0, count($expected));
    }
    
    /**
     * Tests that the analyzer calculates the correct WMCnp metric. 
     *
     * @return void
     */    
    public function testCalculateWMCnpMetric()
    {
        $packages = self::parseSource('/metrics/class-level/wmcnp/');
        $analyzer = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer->addAnalyzer(new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer());
        $analyzer->analyze($packages);
        
        $expected = array('A' => 0, 'B' => 3, 'C' => 4, 'I' => 0);
        foreach ($packages as $package) {
            // Check classes
            foreach ($package->getClasses() as $class) {
                // Get class name
                $name = $class->getName();
                // Check for valid class name
                $this->assertArrayHasKey($name, $expected);
                // Get class metric
                $metric = $analyzer->getNodeMetrics($class);
                // Check for wmcnp key
                $this->assertArrayHasKey('wmcnp', $metric);
                // Compare values
                $this->assertEquals($expected[$name], $metric['wmcnp'], $name);
                // Remove offset
                unset($expected[$name]);
            }
            // Check interfaces
            foreach ($package->getInterfaces() as $interface) {
                // Get interface name
                $name = $interface->getName();
                // Check for valid interface name
                $this->assertArrayHasKey($name, $expected);
                // Get empty metric
                $metric = $analyzer->getNodeMetrics($interface);
                // Check that empty is array
                $this->assertEquals(array(), $metric);
                // Remove offset
                unset($expected[$name]);
            }
        }
        // Check that all match
        $this->assertEquals(0, count($expected));
    }
}