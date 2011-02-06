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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/Depend/Metrics/Dependency/Analyzer.php';

/**
 * Tests the for the package metrics visitor.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_Depend_Metrics_Dependency_AnalyzerTest extends PHP_Depend_AbstractTest
{
    /**
     * The used node builder.
     *
     * @var PHP_Depend_Builder_Default $builder
     */
    protected $builder = null;

    /**
     * Input test data.
     *
     * @var array(string=>array) $_input
     */
    private $_input = array(
        'pkg1'  =>  array(
            'abstractness'  =>  0,
            'instability'   =>  1,
            'efferent'      =>  2,
            'afferent'      =>  0
        ),
        'pkg2'  =>  array(
            'abstractness'  =>  1,
            'instability'   =>  0,
            'efferent'      =>  0,
            'afferent'      =>  1
        ),
        'pkg3'  =>  array(
            'abstractness'  =>  1,
            'instability'   =>  0.5,
            'efferent'      =>  1,
            'afferent'      =>  1
        ),
    );

    /**
     * Expected test data.
     *
     * @var array(string=>array) $_expected
     */
    private $_expected = array();

    /**
     * Sets up the code builder.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $packages = self::parseSource(dirname(__FILE__) . '/../../_code/mixed_code.php');
        foreach ($packages as $pkg) {
            if (isset($this->_input[$pkg->getUUID()])) {
                $this->_expected[$pkg->getUUID()] = $this->_input[$pkg->getName()];
            }
        }
    }

    /**
     * Tests the generated package metrics.
     *
     * @return void
     * @covers PHP_Depend_Metrics_Dependency_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::dependency
     * @group unittest
     */
    public function testGenerateMetrics()
    {
        $visitor = new PHP_Depend_Metrics_Dependency_Analyzer();

        $packages = self::parseSource(dirname(__FILE__) . '/../../_code/mixed_code.php');
        foreach ($packages as $package) {
            $package->accept($visitor);
        }

        foreach ($packages as $package) {

            $uuid = $package->getUUID();

            if (!isset($this->_expected[$uuid])) {
                continue;
            }

            $expected = $this->_expected[$uuid];
            $actual   = $visitor->getStats($package);

            $this->assertEquals($expected['abstractness'], $actual['a']);
            $this->assertEquals($expected['instability'], $actual['i']);
            $this->assertEquals($expected['efferent'], $actual['ce']);
            $this->assertEquals($expected['afferent'], $actual['ca']);

            unset($this->_expected[$uuid]);
        }

        $this->assertEquals(0, count($this->_expected));
    }
}