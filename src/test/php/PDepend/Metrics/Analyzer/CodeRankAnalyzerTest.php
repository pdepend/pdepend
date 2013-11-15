<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
  */

namespace PDepend\Metrics\Analyzer;

use PDepend\Metrics\AbstractMetricsTest;
use PDepend\Source\AST\ASTClass;

/**
 * Test case for the code metric analyzer class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer
 * @group unittest
 */
class CodeRankAnalyzerTest extends AbstractMetricsTest
{
    /**
     * Test input data.
     *
     * @var array(string=>array)
     */
    private $input = array(
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
     * @var \PDepend\Metrics\Analyzer\CodeRankAnalyzer
     */
    private $analyzer = null;

    /**
     * testCodeRankOfSimpleInheritanceExample
     *
     * @return void
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     */
    public function testCodeRankOfSimpleInheritanceExample()
    {
        $actual = $this->getCodeRankForTestCase();

        $expected = array(
            'Foo'  =>  0.15,
            'Bar'  =>  0.2775,
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testReverseCodeRankOfSimpleInheritanceExample
     *
     * @return void
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     */
    public function testReverseCodeRankOfSimpleInheritanceExample()
    {
        $actual = $this->getReverseCodeRankForTestCase();

        $expected = array(
            'Foo'  =>  0.2775,
            'Bar'  =>  0.15,
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testCodeRankOfNamespacedSameNameInheritanceExample
     *
     * @return void
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     */
    public function testCodeRankOfNamespacedSameNameInheritanceExample()
    {
        $actual = $this->getCodeRankForTestCase();
        $this->assertEquals(array('Foo' =>  0.15), $actual);
    }

    /**
     * testCodeRankOfNamespacedSameNamePropertyExample
     *
     * @return void
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\PropertyStrategy
     */
    public function testCodeRankOfNamespacedSameNamePropertyExample()
    {
        $options = array('coderank-mode' => array('property'));
        $actual  = $this->getCodeRankForTestCase($options);

        $this->assertEquals(array('Foo' =>  0.15), $actual);
    }

    /**
     * testReverseCodeRankOfNamespacedSameNamePropertyExample
     *
     * @return void
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\PropertyStrategy
     */
    public function testReverseCodeRankOfNamespacedSameNamePropertyExample()
    {
        $options = array('coderank-mode' => array('property'));
        $actual  = $this->getReverseCodeRankForTestCase($options);

        $this->assertEquals(array('Foo' =>  0.2775), $actual);
    }

    /**
     * testCodeRankOfNamespacedSameNameMethodParamExample
     *
     * @return void
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     */
    public function testCodeRankOfNamespacedSameNameMethodParamExample()
    {
        $options = array('coderank-mode' => array('method'));
        $actual  = $this->getCodeRankForTestCase($options);

        $this->assertEquals(array('Foo' =>  0.15), $actual);
    }

    /**
     * testReverseCodeRankOfNamespacedSameNameMethodParamExample
     *
     * @return void
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     */
    public function testReverseCodeRankOfNamespacedSameNameMethodParamExample()
    {
        $options = array('coderank-mode' => array('method'));
        $actual  = $this->getReverseCodeRankForTestCase($options);

        $this->assertEquals(array('Foo' =>  0.2775), $actual);
    }

    /**
     * testCodeRankOfNamespacedSameNameMethodReturnExample
     *
     * @return void
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     */
    public function testCodeRankOfNamespacedSameNameMethodReturnExample()
    {
        $options = array('coderank-mode' => array('method'));
        $actual  = $this->getReverseCodeRankForTestCase($options);

        $this->assertEquals(array('Foo' =>  0.2775), $actual);
    }

    /**
     * testReverseCodeRankOfNamespacedSameNameMethodReturnExample
     *
     * @return void
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     */
    public function testReverseCodeRankOfNamespacedSameNameMethodReturnExample()
    {
        $options = array('coderank-mode' => array('method'));
        $actual  = $this->getReverseCodeRankForTestCase($options);

        $this->assertEquals(array('Foo' =>  0.2775), $actual);
    }

    /**
     * testCodeRankOfNamespacedSameNameMethodExceptionExample
     *
     * @return void
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     */
    public function testCodeRankOfNamespacedSameNameMethodExceptionExample()
    {
        $options = array('coderank-mode' => array('method'));
        $actual  = $this->getCodeRankForTestCase($options);

        $this->assertEquals(array('Foo' =>  0.15), $actual);
    }

    /**
     * testReverseCodeRankOfNamespacedSameNameMethodExceptionExample
     *
     * @return void
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     */
    public function testReverseCodeRankOfNamespacedSameNameMethodExceptionExample()
    {
        $options = array('coderank-mode' => array('method'));
        $actual  = $this->getReverseCodeRankForTestCase($options);

        $this->assertEquals(array('Foo' =>  0.2775), $actual);
    }

    /**
     * testReverseCodeRankOfNamespacedSameNameInheritanceExample
     *
     * @return void
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     */
    public function testReverseCodeRankOfNamespacedSameNameInheritanceExample()
    {
        $actual = $this->getReverseCodeRankForTestCase();
        $this->assertEquals(array('Foo' => 0.2775), $actual);
    }

    /**
     * testCodeRankOfOrderExampleWithInheritanceAndMethodStrategy
     *
     * @return void
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     */
    public function testCodeRankOfOrderExampleWithInheritanceAndMethodStrategy()
    {
        $options = array('coderank-mode' => array('inheritance', 'method'));
        $actual  = $this->getCodeRankForTestCase($options);

        $expected = array(
            'BCollection'   =>  0.58637,
            'BList'         =>  0.51338,
            'AbstractList'  =>  0.2775,
            'ArrayList'     =>  0.15,
            'Order'         =>  0.15,
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testReverseCodeRankOfOrderExampleWithInheritanceAndMethodStrategy
     *
     * @return void
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     */
    public function testReverseCodeRankOfOrderExampleWithInheritanceAndMethodStrategy()
    {
        $options = array('coderank-mode' => array('inheritance', 'method'));
        $actual  = $this->getReverseCodeRankForTestCase($options);

        $expected = array(
            'BCollection'   =>  0.15,
            'BList'         =>  0.2775,
            'AbstractList'  =>  0.26794,
            'ArrayList'     =>  0.37775,
            'Order'         =>  0.26794,
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testCodeRankOfOrderExampleWithInheritanceAndPropertyStrategy
     *
     * @return void
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\PropertyStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     */
    public function testCodeRankOfOrderExampleWithInheritanceAndPropertyStrategy()
    {
        $options = array('coderank-mode' => array('inheritance', 'property'));
        $actual  = $this->getCodeRankForTestCase($options);

        $expected = array(
            'BCollection'   =>  0.58637,
            'BList'         =>  0.51338,
            'AbstractList'  =>  0.2775,
            'ArrayList'     =>  0.15,
            'Order'         =>  0.15,
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testReverseCodeRankOfOrderExampleWithInheritanceAndPropertyStrategy
     *
     * @return void
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\PropertyStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     */
    public function testReverseCodeRankOfOrderExampleWithInheritanceAndPropertyStrategy()
    {
        $options = array('coderank-mode' => array('inheritance', 'property'));
        $actual  = $this->getReverseCodeRankForTestCase($options);

        $expected = array(
            'BCollection'   =>  0.15,
            'BList'         =>  0.2775,
            'AbstractList'  =>  0.26794,
            'ArrayList'     =>  0.37775,
            'Order'         =>  0.26794,
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testCodeRankOfInternalInterfaceExample
     *
     * @return void
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\PropertyStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     */
    public function testCodeRankOfInternalInterfaceExample()
    {
        $options = array('coderank-mode' => array('inheritance', 'method', 'property'));
        $actual  = $this->getCodeRankForTestCase($options);

        $expected = array(
            'BList'         =>  0.51338,
            'AbstractList'  =>  0.2775,
            'ArrayList'     =>  0.15,
            'Order'         =>  0.15,
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * testReverseCodeRankOfInternalInterfaceExample
     *
     * @return void
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\PropertyStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     */
    public function testReverseCodeRankOfInternalInterfaceExample()
    {
        $options = array('coderank-mode' => array('inheritance', 'method', 'property'));
        $actual = $this->getReverseCodeRankForTestCase($options);
        $expected = array(
            'BList'         =>  0.2775,
            'AbstractList'  =>  0.26794,
            'ArrayList'     =>  0.37775,
            'Order'         =>  0.26794,
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests the result of the class rank calculation against previous computed
     * values.
     *
     * @return void
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\PropertyStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     */
    public function testGetNodeMetrics()
    {
        $namespaces = self::parseCodeResourceForTest();
        
        $this->analyzer = new CodeRankAnalyzer();
        $this->analyzer->analyze($namespaces);

        $expected = array();
        foreach ($namespaces as $namespace) {
            if (count($namespace->getTypes()) === 0) {
                continue;
            }
            $expected[] = array($namespace, $this->input[$namespace->getName()]);
            foreach ($namespace->getTypes() as $type) {
                $expected[] = array($type, $this->input[$type->getName()]);
            }
        }

        foreach ($expected as $key => $info) {
            $metrics = $this->analyzer->getNodeMetrics($info[0]);

            $this->assertEquals($info[1]['cr'], $metrics['cr'], '', 0.00005);
            $this->assertEquals($info[1]['rcr'], $metrics['rcr'], '', 0.00005);

            unset($expected[$key]);
        }
        $this->assertEquals(0, count($expected));
    }

    /**
     * Tests that {@link \PDepend\Metrics\Analyzer\CodeRankAnalyzer::getNodeMetrics()}
     * returns an empty <b>array</b> for an unknown identifier.
     *
     * @return void
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\PropertyStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     */
    public function testGetNodeMetricsInvalidIdentifier()
    {
        $namespaces = self::parseCodeResourceForTest();

        $this->analyzer = new CodeRankAnalyzer();
        $this->analyzer->analyze($namespaces);
        
        $class   = new ASTClass('PDepend');
        $metrics = $this->analyzer->getNodeMetrics($class);

        $this->assertInternalType('array', $metrics);
        $this->assertEquals(0, count($metrics));
    }

    protected function getCodeRankForTestCase(array $options = array())
    {
        return $this->getCodeRankOrReverseCodeRank('cr', $options);
    }

    protected function getReverseCodeRankForTestCase(array $options = array())
    {
        return $this->getCodeRankOrReverseCodeRank('rcr', $options);
    }

    protected function getCodeRankOrReverseCodeRank($metricName, array $options = array())
    {
        $namespaces = $this->parseCodeResourceForTest();

        $analyzer = new CodeRankAnalyzer($options);
        $analyzer->analyze($namespaces);

        $namespaces->rewind();

        $actual = array();
        foreach ($namespaces[0]->getTypes() as $type) {
            $metrics = $analyzer->getNodeMetrics($type);
            $actual[$type->getName()] = round($metrics[$metricName], 5);
        }
        return $actual;
    }
}
