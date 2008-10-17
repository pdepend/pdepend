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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/Depend/Metrics/CodeRank/Analyzer.php';

/**
 * Test case for the code metric analyzer class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_CodeRank_AnalyzerTest extends PHP_Depend_AbstractTest
{
    /**
     * The expected test data.
     *
     * @type array<array>
     * @var array(string=>array) $_expected
     */
    private $_expected = array(
        'package1'    =>  array('cr'  =>  0.2775,     'rcr'  =>  0.385875),
        'package2'    =>  array('cr'  =>  0.15,       'rcr'  =>  0.47799375),
        'package3'    =>  array('cr'  =>  0.385875,   'rcr'  =>  0.2775),
        '+standard'   =>  array('cr'  =>  0.47799375, 'rcr'  =>  0.15),
        '+global'     =>  array('cr'  =>  0.15,       'rcr'  =>  0.15),
        'pkg1Foo'     =>  array('cr'  =>  0.15,       'rcr'  =>  0.181875),
        'pkg2FooI'    =>  array('cr'  =>  0.15,       'rcr'  =>  0.181875),
        'pkg2Bar'     =>  array('cr'  =>  0.15,       'rcr'  =>  0.1755),
        'pkg2Foobar'  =>  array('cr'  =>  0.15,       'rcr'  =>  0.1755),
        'pkg1Barfoo'  =>  array('cr'  =>  0.15,       'rcr'  =>  0.207375),
        'pkg2Barfoo'  =>  array('cr'  =>  0.15,       'rcr'  =>  0.207375),
        'pkg1Foobar'  =>  array('cr'  =>  0.15,       'rcr'  =>  0.411375),
        'pkg1FooI'    =>  array('cr'  =>  0.5325,     'rcr'  =>  0.15),
        'pkg1Bar'     =>  array('cr'  =>  0.59625,    'rcr'  =>  0.15),
        'pkg3FooI'    =>  array('cr'  =>  0.21375,    'rcr'  =>  0.2775),
        'Iterator'    =>  array('cr'  =>  0.3316875,  'rcr'  =>  0.15),
        'Bar'         =>  array('cr'  =>  0.15,       'rcr'  =>  0.15)
    );
    
    /**
     * Tests the result of the class rank calculation against previous computed
     * values.
     *
     * @return void
     */
    public function testGetNodeMetrics()
    {
        $packages = self::parseSource('/metrics/coderank/metrics/');
        $analyzer = new PHP_Depend_Metrics_CodeRank_Analyzer();
        $analyzer->analyze($packages);
        
        foreach ($packages as $package) {
            // Get package name
            $name = $package->getName();
            // Check that key exists
            $this->assertArrayHasKey($name, $this->_expected);
            // Get metric
            $metric = $analyzer->getNodeMetrics($package);
if (empty($metric)) {
    echo PHP_EOL, 'leer: ', $name;
}

            $this->assertArrayHasKey('cr', $metric, 'Missing cr value for: ' . $name);
            $this->assertArrayHasKey('rcr', $metric, 'Missing rcr value for: ' . $name);
            
            // Compare values
            $this->assertEquals($this->_expected[$name]['cr'], $metric['cr'], '', 0.00005);
            $this->assertEquals($this->_expected[$name]['rcr'], $metric['rcr'], '', 0.00005);
            // Remove package offset
            unset($this->_expected[$name]);
            
            foreach ($package->getTypes() as $type) {
                // Get type name
                $name = $type->getName();
                // Check that key exists
                $this->assertArrayHasKey($name, $this->_expected);
                // Get metric
                $metric = $analyzer->getNodeMetrics($type);
                // Compare values
                $this->assertEquals($this->_expected[$name]['cr'], $metric['cr'], '', 0.00005);
                $this->assertEquals($this->_expected[$name]['rcr'], $metric['rcr'], '', 0.00005);
                // Remove type offset
                unset($this->_expected[$name]);                
            }
        }
        $this->assertEquals(0, count($this->_expected));
    }
}