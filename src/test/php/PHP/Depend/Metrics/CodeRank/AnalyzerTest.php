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

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for the code metric analyzer class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 *
 * @covers PHP_Depend_Metrics_CodeRank_Analyzer
 * @group pdepend
 * @group pdepend::metrics
 * @group pdepend::metrics::coderank
 * @group unittest
 */
class PHP_Depend_Metrics_CodeRank_AnalyzerTest extends PHP_Depend_Metrics_AbstractTest
{
    /**
     * Test input data.
     *
     * @var array(string=>array)
     */
    private $_input = array(
        'package1'    =>  array('cr'  =>  0.2775,     'rcr'  =>  0.385875),
        'package2'    =>  array('cr'  =>  0.15,       'rcr'  =>  0.47799375),
        'package3'    =>  array('cr'  =>  0.385875,   'rcr'  =>  0.2775),
        CORE_PACKAGE  =>  array('cr'  =>  0.47799375, 'rcr'  =>  0.15),
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
    );

    /**
     * The code rank analyzer.
     *
     * @var PHP_Depend_Metrics_CodeRank_Analyzer
     */
    private $_analyzer = null;

    /**
     * testCodeRankOfSimpleInheritanceExample
     *
     * @return void
     * @covers PHP_Depend_Metrics_CodeRank_InheritanceStrategy
     */
    public function testCodeRankOfSimpleInheritanceExample()
    {
        $actual = $this->getCodeRankForTestCase(__METHOD__);

        $expected = array(
            'Foo'  =>  0.15,
            'Bar'  =>  0.2775,
        );

        self::assertEquals($expected, $actual);
    }

    /**
     * testReverseCodeRankOfSimpleInheritanceExample
     *
     * @return void
     * @covers PHP_Depend_Metrics_CodeRank_InheritanceStrategy
     */
    public function testReverseCodeRankOfSimpleInheritanceExample()
    {
        $actual = $this->getReverseCodeRankForTestCase(__METHOD__);

        $expected = array(
            'Foo'  =>  0.2775,
            'Bar'  =>  0.15,
        );

        self::assertEquals($expected, $actual);
    }

    /**
     * testCodeRankOfNamespacedSameNameInheritanceExample
     *
     * @return void
     * @covers PHP_Depend_Metrics_CodeRank_InheritanceStrategy
     */
    public function testCodeRankOfNamespacedSameNameInheritanceExample()
    {
        $actual = $this->getCodeRankForTestCase(__METHOD__);
        self::assertEquals(array('Foo' =>  0.15), $actual);
    }

    /**
     * testCodeRankOfNamespacedSameNamePropertyExample
     *
     * @return void
     * @covers PHP_Depend_Metrics_CodeRank_PropertyStrategy
     */
    public function testCodeRankOfNamespacedSameNamePropertyExample()
    {
        $options = array('coderank-mode' => array('property'));
        $actual  = $this->getCodeRankForTestCase(__METHOD__, $options);

        self::assertEquals(array('Foo' =>  0.15), $actual);
    }

    /**
     * testReverseCodeRankOfNamespacedSameNamePropertyExample
     *
     * @return void
     * @covers PHP_Depend_Metrics_CodeRank_PropertyStrategy
     */
    public function testReverseCodeRankOfNamespacedSameNamePropertyExample()
    {
        $options = array('coderank-mode' => array('property'));
        $actual  = $this->getReverseCodeRankForTestCase(__METHOD__, $options);

        self::assertEquals(array('Foo' =>  0.2775), $actual);
    }

    /**
     * testCodeRankOfNamespacedSameNameMethodParamExample
     *
     * @return void
     * @covers PHP_Depend_Metrics_CodeRank_MethodStrategy
     */
    public function testCodeRankOfNamespacedSameNameMethodParamExample()
    {
        $options = array('coderank-mode' => array('method'));
        $actual  = $this->getCodeRankForTestCase(__METHOD__, $options);

        self::assertEquals(array('Foo' =>  0.15), $actual);
    }

    /**
     * testReverseCodeRankOfNamespacedSameNameMethodParamExample
     *
     * @return void
     * @covers PHP_Depend_Metrics_CodeRank_MethodStrategy
     */
    public function testReverseCodeRankOfNamespacedSameNameMethodParamExample()
    {
        $options = array('coderank-mode' => array('method'));
        $actual  = $this->getReverseCodeRankForTestCase(__METHOD__, $options);

        self::assertEquals(array('Foo' =>  0.2775), $actual);
    }

    /**
     * testCodeRankOfNamespacedSameNameMethodReturnExample
     *
     * @return void
     * @covers PHP_Depend_Metrics_CodeRank_MethodStrategy
     */
    public function testCodeRankOfNamespacedSameNameMethodReturnExample()
    {
        $options = array('coderank-mode' => array('method'));
        $actual  = $this->getReverseCodeRankForTestCase(__METHOD__, $options);

        self::assertEquals(array('Foo' =>  0.2775), $actual);
    }

    /**
     * testReverseCodeRankOfNamespacedSameNameMethodReturnExample
     *
     * @return void
     * @covers PHP_Depend_Metrics_CodeRank_MethodStrategy
     */
    public function testReverseCodeRankOfNamespacedSameNameMethodReturnExample()
    {
        $options = array('coderank-mode' => array('method'));
        $actual  = $this->getReverseCodeRankForTestCase(__METHOD__, $options);

        self::assertEquals(array('Foo' =>  0.2775), $actual);
    }

    /**
     * testCodeRankOfNamespacedSameNameMethodExceptionExample
     *
     * @return void
     * @covers PHP_Depend_Metrics_CodeRank_MethodStrategy
     */
    public function testCodeRankOfNamespacedSameNameMethodExceptionExample()
    {
        $options = array('coderank-mode' => array('method'));
        $actual  = $this->getCodeRankForTestCase(__METHOD__, $options);

        self::assertEquals(array('Foo' =>  0.15), $actual);
    }

    /**
     * testReverseCodeRankOfNamespacedSameNameMethodExceptionExample
     *
     * @return void
     * @covers PHP_Depend_Metrics_CodeRank_MethodStrategy
     */
    public function testReverseCodeRankOfNamespacedSameNameMethodExceptionExample()
    {
        $options = array('coderank-mode' => array('method'));
        $actual  = $this->getReverseCodeRankForTestCase(__METHOD__, $options);

        self::assertEquals(array('Foo' =>  0.2775), $actual);
    }

    /**
     * testReverseCodeRankOfNamespacedSameNameInheritanceExample
     *
     * @return void
     * @covers PHP_Depend_Metrics_CodeRank_InheritanceStrategy
     */
    public function testReverseCodeRankOfNamespacedSameNameInheritanceExample()
    {
        $actual = $this->getReverseCodeRankForTestCase(__METHOD__);
        self::assertEquals(array('Foo' => 0.2775), $actual);
    }

    /**
     * testCodeRankOfOrderExampleWithInheritanceAndMethodStrategy
     *
     * @return void
     * @covers PHP_Depend_Metrics_CodeRank_MethodStrategy
     * @covers PHP_Depend_Metrics_CodeRank_InheritanceStrategy
     */
    public function testCodeRankOfOrderExampleWithInheritanceAndMethodStrategy()
    {
        $options = array('coderank-mode' => array('inheritance', 'method'));
        $actual  = $this->getCodeRankForTestCase(__METHOD__, $options);

        $expected = array(
            'BCollection'   =>  0.58637,
            'BList'         =>  0.51338,
            'AbstractList'  =>  0.2775,
            'ArrayList'     =>  0.15,
            'Order'         =>  0.15,
        );

        self::assertEquals($expected, $actual);
    }

    /**
     * testReverseCodeRankOfOrderExampleWithInheritanceAndMethodStrategy
     *
     * @return void
     * @covers PHP_Depend_Metrics_CodeRank_MethodStrategy
     * @covers PHP_Depend_Metrics_CodeRank_InheritanceStrategy
     */
    public function testReverseCodeRankOfOrderExampleWithInheritanceAndMethodStrategy()
    {
        $options = array('coderank-mode' => array('inheritance', 'method'));
        $actual  = $this->getReverseCodeRankForTestCase(__METHOD__, $options);

        $expected = array(
            'BCollection'   =>  0.15,
            'BList'         =>  0.2775,
            'AbstractList'  =>  0.26794,
            'ArrayList'     =>  0.37775,
            'Order'         =>  0.26794,
        );

        self::assertEquals($expected, $actual);
    }

    /**
     * testCodeRankOfOrderExampleWithInheritanceAndPropertyStrategy
     *
     * @return void
     * @covers PHP_Depend_Metrics_CodeRank_PropertyStrategy
     * @covers PHP_Depend_Metrics_CodeRank_InheritanceStrategy
     */
    public function testCodeRankOfOrderExampleWithInheritanceAndPropertyStrategy()
    {
        $options = array('coderank-mode' => array('inheritance', 'property'));
        $actual  = $this->getCodeRankForTestCase(__METHOD__, $options);

        $expected = array(
            'BCollection'   =>  0.58637,
            'BList'         =>  0.51338,
            'AbstractList'  =>  0.2775,
            'ArrayList'     =>  0.15,
            'Order'         =>  0.15,
        );

        self::assertEquals($expected, $actual);
    }

    /**
     * testReverseCodeRankOfOrderExampleWithInheritanceAndPropertyStrategy
     *
     * @return void
     * @covers PHP_Depend_Metrics_CodeRank_PropertyStrategy
     * @covers PHP_Depend_Metrics_CodeRank_InheritanceStrategy
     */
    public function testReverseCodeRankOfOrderExampleWithInheritanceAndPropertyStrategy()
    {
        $options = array('coderank-mode' => array('inheritance', 'property'));
        $actual  = $this->getReverseCodeRankForTestCase(__METHOD__, $options);

        $expected = array(
            'BCollection'   =>  0.15,
            'BList'         =>  0.2775,
            'AbstractList'  =>  0.26794,
            'ArrayList'     =>  0.37775,
            'Order'         =>  0.26794,
        );

        self::assertEquals($expected, $actual);
    }

    /**
     * testCodeRankOfInternalInterfaceExample
     *
     * @return void
     * @covers PHP_Depend_Metrics_CodeRank_MethodStrategy
     * @covers PHP_Depend_Metrics_CodeRank_PropertyStrategy
     * @covers PHP_Depend_Metrics_CodeRank_InheritanceStrategy
     */
    public function testCodeRankOfInternalInterfaceExample()
    {
        $options = array('coderank-mode' => array('inheritance', 'method', 'property'));
        $actual  = $this->getCodeRankForTestCase(__METHOD__, $options);

        $expected = array(
            'BList'         =>  0.51338,
            'AbstractList'  =>  0.2775,
            'ArrayList'     =>  0.15,
            'Order'         =>  0.15,
        );

        self::assertEquals($expected, $actual);
    }

    /**
     * testReverseCodeRankOfInternalInterfaceExample
     *
     * @return void
     * @covers PHP_Depend_Metrics_CodeRank_MethodStrategy
     * @covers PHP_Depend_Metrics_CodeRank_PropertyStrategy
     * @covers PHP_Depend_Metrics_CodeRank_InheritanceStrategy
     */
    public function testReverseCodeRankOfInternalInterfaceExample()
    {
        $options = array('coderank-mode' => array('inheritance', 'method', 'property'));
        $actual  = $this->getReverseCodeRankForTestCase(__METHOD__, $options);

        $expected = array(
            'BList'         =>  0.2775,
            'AbstractList'  =>  0.26794,
            'ArrayList'     =>  0.37775,
            'Order'         =>  0.26794,
        );

        self::assertEquals($expected, $actual);
    }

    /**
     * Tests the result of the class rank calculation against previous computed
     * values.
     *
     * @return void
     * @covers PHP_Depend_Metrics_CodeRank_MethodStrategy
     * @covers PHP_Depend_Metrics_CodeRank_PropertyStrategy
     * @covers PHP_Depend_Metrics_CodeRank_InheritanceStrategy
     */
    public function testGetNodeMetrics()
    {
        $packages = self::parseCodeResourceForTest();
        
        $this->_analyzer = new PHP_Depend_Metrics_CodeRank_Analyzer();
        $this->_analyzer->analyze($packages);

        $expected = array();
        foreach ($packages as $package) {
            if ($package->getTypes()->count() === 0) {
                continue;
            }
            $expected[] = array($package, $this->_input[$package->getName()]);
            foreach ($package->getTypes() as $type) {
                $expected[] = array($type, $this->_input[$type->getName()]);
            }
        }

        foreach ($expected as $key => $info) {
            $metrics = $this->_analyzer->getNodeMetrics($info[0]);

            self::assertEquals($info[1]['cr'], $metrics['cr'], '', 0.00005);
            self::assertEquals($info[1]['rcr'], $metrics['rcr'], '', 0.00005);

            unset($expected[$key]);
        }
        self::assertEquals(0, count($expected));
    }

    /**
     * Tests that {@link PHP_Depend_Metrics_CodeRank_Analyzer::getNodeMetrics()}
     * returns an empty <b>array</b> for an unknown identifier.
     *
     * @return void
     * @covers PHP_Depend_Metrics_CodeRank_MethodStrategy
     * @covers PHP_Depend_Metrics_CodeRank_PropertyStrategy
     * @covers PHP_Depend_Metrics_CodeRank_InheritanceStrategy
     */
    public function testGetNodeMetricsInvalidIdentifier()
    {
        $packages = self::parseCodeResourceForTest();

        $this->_analyzer = new PHP_Depend_Metrics_CodeRank_Analyzer();
        $this->_analyzer->analyze($packages);
        
        $class   = new PHP_Depend_Code_Class('PDepend');
        $metrics = $this->_analyzer->getNodeMetrics($class);

        self::assertInternalType('array', $metrics);
        self::assertEquals(0, count($metrics));
    }

    protected function getCodeRankForTestCase($testCase, array $options = array())
    {
        return $this->getCodeRankOrReverseCodeRank($testCase, 'cr', $options);
    }

    protected function getReverseCodeRankForTestCase($testCase, array $options = array())
    {
        return $this->getCodeRankOrReverseCodeRank($testCase, 'rcr', $options);
    }

    protected function getCodeRankOrReverseCodeRank($testCase, $metricName, array $options = array())
    {
        $packages = self::parseTestCaseSource($testCase);

        $analyzer = new PHP_Depend_Metrics_CodeRank_Analyzer($options);
        $analyzer->analyze($packages);

        $packages->rewind();

        $actual = array();
        foreach ($packages->current()->getTypes() as $type) {
            $metrics = $analyzer->getNodeMetrics($type);
            $actual[$type->getName()] = round($metrics[$metricName], 5);
        }
        return $actual;
    }
}
