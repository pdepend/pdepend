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

require_once 'PHP/Depend/Metrics/CyclomaticComplexity/Analyzer.php';

/**
 * Test case for the cyclomatic analyzer.
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
class PHP_Depend_Metrics_CyclomaticComplexity_AnalyzerTest 
    extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the analyzer calculates the correct function cc numbers.
     *
     * @return void
     */
    public function testCalculateFunctionCCNAndCNN2()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/../../_code/ccn/function.php');
        $analyzer = new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer();
        $analyzer->analyze($packages);
        
        $packages->rewind();
        
        $this->assertEquals(2, $packages->count()); // +global & +standard

        foreach ($packages as $package) {
            if ($package->getName() === '+global') {
                break;
            }
        }

        $functions = $package->getFunctions();
        $this->assertEquals(2, $functions->count());
        
        $expected = array(
            'pdepend1'  =>  array('ccn'  =>  5, 'ccn2'  =>  6),
            'pdepend2'  =>  array('ccn'  =>  7, 'ccn2'  =>  10)
        );
        
        foreach ($functions as $function) {
            $metrics = $analyzer->getNodeMetrics($function);
            
            $this->assertEquals(
                $expected[$function->getName()]['ccn'], 
                $analyzer->getCCN($function)
            );
            $this->assertEquals(
                $expected[$function->getName()]['ccn2'], 
                $analyzer->getCCN2($function)
            );
        }
        
        $expected = array('ccn'  =>  12, 'ccn2'  =>  16);
        $this->assertEquals($expected, $analyzer->getProjectMetrics());
    }
    
    /**
     * Tests that the analyzer calculates the correct method cc numbers.
     *
     * @return void
     */
    public function testCalculateMethodCCNAndCNN2()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/../../_code/ccn/method.php');
        $analyzer = new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer();
        $analyzer->analyze($packages);
        
        $packages->rewind();
        
        $this->assertEquals(2, $packages->count()); // +global & +standard|+core
        foreach ($packages as $package) {
            if ($package->getName() === '+global') {
                break;
            }
        }

        $classes = $package->getClasses();
        $this->assertEquals(1, $classes->count());
        $methods = $classes->current()->getMethods();
        $this->assertEquals(2, $methods->count());
        
        $expected = array(
            'pdepend1'  =>  array('ccn'  =>  5, 'ccn2'  =>  6),
            'pdepend2'  =>  array('ccn'  =>  7, 'ccn2'  =>  10)
        );
        
        foreach ($methods as $method) {
            $metrics = $analyzer->getNodeMetrics($method);
            
            $this->assertEquals(
                $expected[$method->getName()], 
                $analyzer->getNodeMetrics($method)
            );
        }
    }
    
    /**
     * Tests that the analyzer aggregates the correct project metrics.
     *
     * @return void
     */
    public function testCalculateProjectMetrics()
    {
        $packages = self::parseSource(dirname(__FILE__) . '/../../_code/ccn');
        $analyzer = new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer();
        $analyzer->analyze($packages);
        
        $expected = array('ccn'  =>  24, 'ccn2'  =>  32);
        $this->assertEquals($expected, $analyzer->getProjectMetrics());
    }
}