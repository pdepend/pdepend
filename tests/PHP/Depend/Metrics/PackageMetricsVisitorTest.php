<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pmanuel-pichler.de>.
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
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Depend/Metrics/PackageMetricsVisitorTest.php';

/**
 * Tests the for the package metrics visitor.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_PackageMetricsVisitorTest extends PHP_Depend_AbstractTest
{
    /**
     * The used node builder.
     *
     * @type PHP_Depend_Code_DefaultBuilder
     * @var PHP_Depend_Code_DefaultBuilder $builder
     */
    protected $builder = null;
    
    /**
     * Sets up the code builder.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        
        $sourceFile    = dirname(__FILE__) . '/../data/mixed_code.php';
        $tokenizer     = new PHP_Depend_Code_Tokenizer_InternalTokenizer($sourceFile);
        $this->builder = new PHP_Depend_Code_DefaultBuilder();
        $parser        = new PHP_Depend_Parser($tokenizer, $this->builder);
        
        $parser->parse();
    }
    
    /**
     * Tests the generated package metrics.
     *
     * @return void
     */
    public function testGenerateMetrics()
    {
        $expected = array(
            'pkg1'  =>  array(
                'abstractness'      =>  0,
                'instability'       =>  1,
                'efferentCoupling'  =>  2,
                'afferentCoupling'  =>  0
            ),
            'pkg2'  =>  array(
                'abstractness'      =>  1,
                'instability'       =>  0,
                'efferentCoupling'  =>  0,
                'afferentCoupling'  =>  1
            ),
            'pkg3'  =>  array(
                'abstractness'      =>  1,
                'instability'       =>  0.5,
                'efferentCoupling'  =>  1,
                'afferentCoupling'  =>  1
            ),
        );
        
        $visitor = new PHP_Depend_Metrics_PackageMetricsVisitor();
        foreach ($this->builder->getPackages() as $package) {
            $package->accept($visitor);
        }
        $metrics0 = $visitor->getPackageMetrics(); 
        foreach ($metrics0 as $metrics) {
            if (!isset($expected[$metrics->getName()])) {
                continue;
            }

            $data = $expected[$metrics->getName()];

            $this->assertEquals($data['abstractness'], $metrics->abstractness());
            $this->assertEquals($data['instability'], $metrics->instability());
            $this->assertEquals($data['efferentCoupling'], $metrics->efferentCoupling());
            $this->assertEquals($data['afferentCoupling'], $metrics->afferentCoupling());
            
            unset($expected[$metrics->getName()]);
        }
        
        $this->assertEquals(0, count($expected));
    }
    
    /**
     * Tests that metrics are only generated once.
     *
     * @return void
     */
    public function testGenerateUniqueMetricsInstance()
    {
        $visitor = new PHP_Depend_Metrics_PackageMetricsVisitor();
        foreach ($this->builder->getPackages() as $package) {
            $package->accept($visitor);
        }
        $metrics0 = $visitor->getPackageMetrics();
        $metrics1 = $visitor->getPackageMetrics();
        
        $this->assertNotNull($metrics0);
        $this->assertNotNull($metrics1);
        
        $this->assertSame($metrics0, $metrics1);
    }
}