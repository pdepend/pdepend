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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

/**
 * Tests the for the package metrics visitor.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 *
 * @covers PHP_Depend_Metrics_Dependency_Analyzer
 * @group pdepend
 * @group pdepend::metrics
 * @group pdepend::metrics::dependency
 * @group unittest
 */
class PHP_Depend_Metrics_Dependency_AnalyzerTest extends PHP_Depend_AbstractTest
{
    /**
     * The used node builder.
     *
     * @var PHP_Depend_Builder_Default
     */
    protected $builder = null;

    /**
     * Input test data.
     *
     * @var array(string=>array)
     */
    private $_input = array(
        '+global'  =>  array(
            'tc'  =>  0,
            'cc'  =>  0,
            'ac'  =>  0,
            'ca'  =>  1,
            'ce'  =>  0,
            'a'   =>  0,
            'i'   =>  0,
            'd'   =>  1
        ),
        'pkg1'  =>  array(
            'tc'  =>  1,
            'cc'  =>  1,
            'ac'  =>  0,
            'ca'  =>  0,
            'ce'  =>  2,
            'a'   =>  0,
            'i'   =>  1,
            'd'   =>  0
        ),
        'pkg2'  =>  array(
            'tc'  =>  1,
            'cc'  =>  0,
            'ac'  =>  1,
            'ca'  =>  1,
            'ce'  =>  0,
            'a'   =>  1,
            'i'   =>  0,
            'd'   =>  0
        ),
        'pkg3'  =>  array(
            'tc'  =>  1,
            'cc'  =>  0,
            'ac'  =>  1,
            'ca'  =>  1,
            'ce'  =>  1,
            'a'   =>  1,
            'i'   =>  0.5,
            'd'   =>  0.5,
        ),
    );

    /**
     * Tests the generated package metrics.
     *
     * @return void
     */
    public function testGenerateMetrics()
    {
        $visitor = new PHP_Depend_Metrics_Dependency_Analyzer();

        $packages = self::parseCodeResourceForTest();
        $visitor->analyze($packages);

        $actual = array();
        foreach ($packages as $package) {
            $actual[$package->getName()] = $visitor->getStats($package);
        }

        self::assertEquals($this->_input, $actual);
    }
}
