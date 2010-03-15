<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2010, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/Depend/Metrics/NodeLoc/Analyzer.php';

/**
 * Test case for the node lines of code analyzer.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Metrics_NodeLoc_AnalyzerTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the analyzer calculates the correct function and file loc
     * values.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectFunctionAndFileLoc()
    {
        $packages = self::parseSource('comments/function.php');

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $expectedValues = array(
            'func_with_comment'  =>  array(
                'loc'    =>  6,
                'cloc'   =>  3,
                'eloc'   =>  2,
                'ncloc'  =>  3
            ),
            'func_without_comment'  =>  array(
                'loc'    =>  7,
                'cloc'   =>  4,
                'eloc'   =>  2,
                'ncloc'  =>  3,
            ),
            'func_without_doc_comment'  =>  array(
                'loc'    =>  3,
                'cloc'   =>  0,
                'eloc'   =>  2,
                'ncloc'  =>  3,
            ),
            'another_func_with_comment'  =>  array(
                'loc'    =>  4,
                'cloc'   =>  1,
                'eloc'   =>  2,
                'ncloc'  =>  3,
            ),
        );

        $packages->rewind();
        $functions = $packages->current()->getFunctions();

        foreach ($functions as $function) {
            $this->assertArrayHasKey($function->getName(), $expectedValues);

            $expected = $expectedValues[$function->getName()];
            $actual   = $analyzer->getNodeMetrics($function);

            $this->assertEquals($expected, $actual, 'Function: ' . $function->getName());

            unset($expectedValues[$function->getName()]);
        }
        $this->assertEquals(0, count($expectedValues));

        $functions->rewind();
        $file = $functions->current()->getSourceFile();

        $actual   = $analyzer->getNodeMetrics($file);
        $expected = array(
            'loc'    =>  31,
            'cloc'   =>  15,
            'eloc'   =>  13,
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
    public function testAnalyzerCalculatesCorrectMethodAndClassAndFileLoc()
    {
        $source   = dirname(__FILE__) . '/../../_code/comments/method.php';
        $packages = self::parseSource($source);

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $packages->rewind();
        $class = $packages->current()->getClasses()->current();

        $actual   = $analyzer->getNodeMetrics($class);
        $expected = array(
            'loc'    =>  30,
            'cloc'   =>  12,
            'eloc'   =>  14,
            'ncloc'  =>  18
        );

        $this->assertEquals($expected, $actual);

        $expectedValues = array(
            'method_with_comment'  =>  array(
                'loc'    =>  6,
                'cloc'   =>  3,
                'eloc'   =>  2,
                'ncloc'  =>  3,
            ),
            'method_without_comment'  =>  array(
                'loc'    =>  3,
                'cloc'   =>  0,
                'eloc'   =>  2,
                'ncloc'  =>  3,
            ),
            'method_without_doc_comment'  =>  array(
                'loc'    =>  3,
                'cloc'   =>  0,
                'eloc'   =>  2,
                'ncloc'  =>  3,
            ),
            'another_method_with_comment'  =>  array(
                'loc'    =>  5,
                'cloc'   =>  2,
                'eloc'   =>  2,
                'ncloc'  =>  3,
            ),
        );

        $methods = $class->getMethods();
        foreach ($methods as $method) {
            $this->assertArrayHasKey($method->getName(), $expectedValues);

            $actual   = $analyzer->getNodeMetrics($method);
            $expected = $expectedValues[$method->getName()];

            $this->assertEquals($expected, $actual, 'Method: ' . $method->getName());

            unset($expectedValues[$method->getName()]);
        }
        $this->assertEquals(0, count($expectedValues));

        $actual   = $analyzer->getNodeMetrics($class->getSourceFile());
        $expected = array(
            'loc'    =>  34,
            'cloc'   =>  15,
            'eloc'   =>  16,
            'ncloc'  =>  19
        );

        $this->assertEquals($expected, $actual);

    }

    /**
     * Tests that the analyzer calculates the correct interface, method and file
     * loc values.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectMethodAndInterfaceAndFileLoc()
    {
        $source   = dirname(__FILE__) . '/../../_code/comments/method2.php';
        $packages = self::parseSource($source);

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $packages->rewind();
        $interface = $packages->current()->getInterfaces()->current();

        $actual   = $analyzer->getNodeMetrics($interface);
        $expected = array(
            'loc'    =>  17,
            'cloc'   =>  7,
            'eloc'   =>  0,
            'ncloc'  =>  10
        );

        $this->assertEquals($expected, $actual);

        $expectedValues = array(
            'method_with_comment'  =>  array(
                'loc'    =>  1,
                'cloc'   =>  0,
                'eloc'   =>  0,
                'ncloc'  =>  1,
            ),
            'method_without_comment'  =>  array(
                'loc'    =>  2,
                'cloc'   =>  0,
                'eloc'   =>  0,
                'ncloc'  =>  2,
            ),
            'method_without_doc_comment'  =>  array(
                'loc'    =>  1,
                'cloc'   =>  0,
                'eloc'   =>  0,
                'ncloc'  =>  1,
            ),
            'another_method_with_comment'  =>  array(
                'loc'    =>  1,
                'cloc'   =>  0,
                'eloc'   =>  0,
                'ncloc'  =>  1,
            ),
        );

        $methods = $interface->getMethods();
        foreach ($methods as $method) {
            $this->assertArrayHasKey($method->getName(), $expectedValues);

            $actual   = $analyzer->getNodeMetrics($method);
            $expected = $expectedValues[$method->getName()];

            $this->assertEquals($expected, $actual, 'Method: ' . $method->getName());

            unset($expectedValues[$method->getName()]);
        }
        $this->assertEquals(0, count($expectedValues));

        $actual   = $analyzer->getNodeMetrics($interface->getSourceFile());
        $expected = array(
            'loc'    =>  25,
            'cloc'   =>  13,
            'eloc'   =>  9,
            'ncloc'  =>  12
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates the correct class, property and file
     * loc values.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectPropertyAndClassAndFileLoc()
    {
        $source   = dirname(__FILE__) . '/../../_code/comments/property.php';
        $packages = self::parseSource($source);

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $packages->rewind();
        $class = $packages->current()->getClasses()->current();

        $actual   = $analyzer->getNodeMetrics($class);
        $expected = array(
            'loc'    =>  17,
            'cloc'   =>  7,
            'eloc'   =>  6,
            'ncloc'  =>  10
        );

        $this->assertEquals($expected, $actual);

        $actual   = $analyzer->getNodeMetrics($class->getSourceFile());
        $expected = array(
            'loc'    =>  21,
            'cloc'   =>  10,
            'eloc'   =>  8,
            'ncloc'  =>  11
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates the correct class and file
     * loc values.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectClassAndFileLoc()
    {
        $packages = self::parseTestCaseSource(__METHOD__);

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $packages->rewind();
        $class = $packages->current()->getClasses()->current();

        $actual   = $analyzer->getNodeMetrics($class);
        $expected = array(
            'loc'    =>  17,
            'cloc'   =>  7,
            'eloc'   =>  6,
            'ncloc'  =>  10
        );
        $this->assertEquals($expected, $actual);

        $actual   = $analyzer->getNodeMetrics($class->getSourceFile());
        $expected = array(
            'loc'    =>  21,
            'cloc'   =>  10,
            'eloc'   =>  8,
            'ncloc'  =>  11
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the analyzer calculates the correct interface and file
     * loc values.
     *
     * @return void
     */
    public function testAnalyzerCalculatesCorrectInterfaceAndFileLoc()
    {
        $packages = self::parseTestCaseSource(__METHOD__);

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $packages->rewind();
        $interface = $packages->current()->getInterfaces()->current();

        $actual   = $analyzer->getNodeMetrics($interface);
        $expected = array(
            'loc'    =>  17,
            'cloc'   =>  7,
            'eloc'   =>  0,
            'ncloc'  =>  10
        );
        $this->assertEquals($expected, $actual);

        $actual   = $analyzer->getNodeMetrics($interface->getSourceFile());
        $expected = array(
            'loc'    =>  21,
            'cloc'   =>  10,
            'eloc'   =>  8,
            'ncloc'  =>  11
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
        $source   = dirname(__FILE__) . '/../../_code/comments/';
        $packages = self::parseSource($source);

        $analyzer = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer->analyze($packages);

        $actual   = $analyzer->getProjectMetrics();
        $expected = array(
            'loc'    =>  260,
            'cloc'   =>  144,
            'eloc'   =>  89,
            'ncloc'  =>  116
        );

        $this->assertEquals($expected, $actual);
    }
}
