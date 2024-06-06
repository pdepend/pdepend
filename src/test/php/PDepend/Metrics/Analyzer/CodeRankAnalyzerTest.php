<?php

/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2017 Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Metrics\Analyzer;

use PDepend\Metrics\AbstractMetricsTestCase;
use PDepend\Source\AST\ASTClass;

/**
 * Test case for the code metric analyzer class.
 *
 * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class CodeRankAnalyzerTest extends AbstractMetricsTestCase
{
    /**
     * Test input data.
     *
     * @var array<string, array<mixed>>
     */
    private array $input = [
        'package1' => ['cr' => 0.2775, 'rcr' => 0.385875],
        'package2' => ['cr' => 0.15, 'rcr' => 0.47799375],
        'package3' => ['cr' => 0.385875, 'rcr' => 0.2775],
        'core' => ['cr' => 0.47799375, 'rcr' => 0.15],
        'pkg1Foo' => ['cr' => 0.15, 'rcr' => 0.181875],
        'pkg2FooI' => ['cr' => 0.15, 'rcr' => 0.181875],
        'pkg2Bar' => ['cr' => 0.15, 'rcr' => 0.1755],
        'pkg2Foobar' => ['cr' => 0.15, 'rcr' => 0.1755],
        'pkg1Barfoo' => ['cr' => 0.15, 'rcr' => 0.207375],
        'pkg2Barfoo' => ['cr' => 0.15, 'rcr' => 0.207375],
        'pkg1Foobar' => ['cr' => 0.15, 'rcr' => 0.411375],
        'pkg1FooI' => ['cr' => 0.5325, 'rcr' => 0.15],
        'pkg1Bar' => ['cr' => 0.59625, 'rcr' => 0.15],
        'pkg3FooI' => ['cr' => 0.21375, 'rcr' => 0.2775],
        'Iterator' => ['cr' => 0.3316875, 'rcr' => 0.15],
    ];

    /** The code rank analyzer. */
    private CodeRankAnalyzer $analyzer;

    /**
     * testCodeRankOfSimpleInheritanceExample
     *
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     */
    public function testCodeRankOfSimpleInheritanceExample(): void
    {
        $actual = $this->getCodeRankForTestCase();

        $expected = [
            'Foo' => 0.15,
            'Bar' => 0.2775,
        ];

        static::assertEquals($expected, $actual);
    }

    /**
     * testReverseCodeRankOfSimpleInheritanceExample
     *
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     */
    public function testReverseCodeRankOfSimpleInheritanceExample(): void
    {
        $actual = $this->getReverseCodeRankForTestCase();

        $expected = [
            'Foo' => 0.2775,
            'Bar' => 0.15,
        ];

        static::assertEquals($expected, $actual);
    }

    /**
     * testCodeRankOfNamespacedSameNameInheritanceExample
     *
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     */
    public function testCodeRankOfNamespacedSameNameInheritanceExample(): void
    {
        $actual = $this->getCodeRankForTestCase();
        static::assertEquals(['Foo' => 0.15], $actual);
    }

    /**
     * testCodeRankOfNamespacedSameNamePropertyExample
     *
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\PropertyStrategy
     */
    public function testCodeRankOfNamespacedSameNamePropertyExample(): void
    {
        $options = ['coderank-mode' => ['property']];
        $actual = $this->getCodeRankForTestCase($options);

        static::assertEquals(['Foo' => 0.15], $actual);
    }

    /**
     * testReverseCodeRankOfNamespacedSameNamePropertyExample
     *
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\PropertyStrategy
     */
    public function testReverseCodeRankOfNamespacedSameNamePropertyExample(): void
    {
        $options = ['coderank-mode' => ['property']];
        $actual = $this->getReverseCodeRankForTestCase($options);

        static::assertEquals(['Foo' => 0.2775], $actual);
    }

    /**
     * testCodeRankOfNamespacedSameNameMethodParamExample
     *
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     */
    public function testCodeRankOfNamespacedSameNameMethodParamExample(): void
    {
        $options = ['coderank-mode' => ['method']];
        $actual = $this->getCodeRankForTestCase($options);

        static::assertEquals(['Foo' => 0.15], $actual);
    }

    /**
     * testReverseCodeRankOfNamespacedSameNameMethodParamExample
     *
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     */
    public function testReverseCodeRankOfNamespacedSameNameMethodParamExample(): void
    {
        $options = ['coderank-mode' => ['method']];
        $actual = $this->getReverseCodeRankForTestCase($options);

        static::assertEquals(['Foo' => 0.2775], $actual);
    }

    /**
     * testCodeRankOfNamespacedSameNameMethodReturnExample
     *
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     */
    public function testCodeRankOfNamespacedSameNameMethodReturnExample(): void
    {
        $options = ['coderank-mode' => ['method']];
        $actual = $this->getReverseCodeRankForTestCase($options);

        static::assertEquals(['Foo' => 0.2775], $actual);
    }

    /**
     * testReverseCodeRankOfNamespacedSameNameMethodReturnExample
     *
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     */
    public function testReverseCodeRankOfNamespacedSameNameMethodReturnExample(): void
    {
        $options = ['coderank-mode' => ['method']];
        $actual = $this->getReverseCodeRankForTestCase($options);

        static::assertEquals(['Foo' => 0.2775], $actual);
    }

    /**
     * testCodeRankOfNamespacedSameNameMethodExceptionExample
     *
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     */
    public function testCodeRankOfNamespacedSameNameMethodExceptionExample(): void
    {
        $options = ['coderank-mode' => ['method']];
        $actual = $this->getCodeRankForTestCase($options);

        static::assertEquals(['Foo' => 0.15], $actual);
    }

    /**
     * testReverseCodeRankOfNamespacedSameNameMethodExceptionExample
     *
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     */
    public function testReverseCodeRankOfNamespacedSameNameMethodExceptionExample(): void
    {
        $options = ['coderank-mode' => ['method']];
        $actual = $this->getReverseCodeRankForTestCase($options);

        static::assertEquals(['Foo' => 0.2775], $actual);
    }

    /**
     * testReverseCodeRankOfNamespacedSameNameInheritanceExample
     *
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     */
    public function testReverseCodeRankOfNamespacedSameNameInheritanceExample(): void
    {
        $actual = $this->getReverseCodeRankForTestCase();
        static::assertEquals(['Foo' => 0.2775], $actual);
    }

    /**
     * testCodeRankOfOrderExampleWithInheritanceAndMethodStrategy
     *
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     */
    public function testCodeRankOfOrderExampleWithInheritanceAndMethodStrategy(): void
    {
        $options = ['coderank-mode' => ['inheritance', 'method']];
        $actual = $this->getCodeRankForTestCase($options);

        $expected = [
            'BCollection' => 0.58637,
            'BList' => 0.51338,
            'AbstractList' => 0.2775,
            'ArrayList' => 0.15,
            'Order' => 0.15,
        ];

        static::assertEquals($expected, $actual);
    }

    /**
     * testReverseCodeRankOfOrderExampleWithInheritanceAndMethodStrategy
     *
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     */
    public function testReverseCodeRankOfOrderExampleWithInheritanceAndMethodStrategy(): void
    {
        $options = ['coderank-mode' => ['inheritance', 'method']];
        $actual = $this->getReverseCodeRankForTestCase($options);

        $expected = [
            'BCollection' => 0.15,
            'BList' => 0.2775,
            'AbstractList' => 0.26794,
            'ArrayList' => 0.37775,
            'Order' => 0.26794,
        ];

        static::assertEquals($expected, $actual);
    }

    /**
     * testCodeRankOfOrderExampleWithInheritanceAndPropertyStrategy
     *
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\PropertyStrategy
     */
    public function testCodeRankOfOrderExampleWithInheritanceAndPropertyStrategy(): void
    {
        $options = ['coderank-mode' => ['inheritance', 'property']];
        $actual = $this->getCodeRankForTestCase($options);

        $expected = [
            'BCollection' => 0.58637,
            'BList' => 0.51338,
            'AbstractList' => 0.2775,
            'ArrayList' => 0.15,
            'Order' => 0.15,
        ];

        static::assertEquals($expected, $actual);
    }

    /**
     * testReverseCodeRankOfOrderExampleWithInheritanceAndPropertyStrategy
     *
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\PropertyStrategy
     */
    public function testReverseCodeRankOfOrderExampleWithInheritanceAndPropertyStrategy(): void
    {
        $options = ['coderank-mode' => ['inheritance', 'property']];
        $actual = $this->getReverseCodeRankForTestCase($options);

        $expected = [
            'BCollection' => 0.15,
            'BList' => 0.2775,
            'AbstractList' => 0.26794,
            'ArrayList' => 0.37775,
            'Order' => 0.26794,
        ];

        static::assertEquals($expected, $actual);
    }

    /**
     * testCodeRankOfInternalInterfaceExample
     *
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\PropertyStrategy
     */
    public function testCodeRankOfInternalInterfaceExample(): void
    {
        $options = ['coderank-mode' => ['inheritance', 'method', 'property']];
        $actual = $this->getCodeRankForTestCase($options);

        $expected = [
            'BList' => 0.51338,
            'AbstractList' => 0.2775,
            'ArrayList' => 0.15,
            'Order' => 0.15,
        ];

        static::assertEquals($expected, $actual);
    }

    /**
     * testReverseCodeRankOfInternalInterfaceExample
     *
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\PropertyStrategy
     */
    public function testReverseCodeRankOfInternalInterfaceExample(): void
    {
        $options = ['coderank-mode' => ['inheritance', 'method', 'property']];
        $actual = $this->getReverseCodeRankForTestCase($options);
        $expected = [
            'BList' => 0.2775,
            'AbstractList' => 0.26794,
            'ArrayList' => 0.37775,
            'Order' => 0.26794,
        ];

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests the result of the class rank calculation against previous computed
     * values.
     *
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\PropertyStrategy
     */
    public function testGetNodeMetrics(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $this->analyzer = new CodeRankAnalyzer();
        $this->analyzer->analyze($namespaces);

        $expected = [];
        foreach ($namespaces as $namespace) {
            if (count($namespace->getTypes()) === 0) {
                continue;
            }
            $expected[] = [$namespace, $this->input[$namespace->getImage()]];
            foreach ($namespace->getTypes() as $type) {
                $expected[] = [$type, $this->input[$type->getImage()]];
            }
        }

        foreach ($expected as $key => $info) {
            $metrics = $this->analyzer->getNodeMetrics($info[0]);

            static::assertEqualsWithDelta($info[1]['cr'], $metrics['cr'], 0.00005);
            static::assertEqualsWithDelta($info[1]['rcr'], $metrics['rcr'], 0.00005);

            unset($expected[$key]);
        }
        static::assertCount(0, $expected);
    }

    /**
     * Tests that {@link \PDepend\Metrics\Analyzer\CodeRankAnalyzer::getNodeMetrics()}
     * returns an empty <b>array</b> for an unknown identifier.
     *
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\InheritanceStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
     * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\PropertyStrategy
     */
    public function testGetNodeMetricsInvalidIdentifier(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $this->analyzer = new CodeRankAnalyzer();
        $this->analyzer->analyze($namespaces);

        $class = new ASTClass('PDepend');
        $metrics = $this->analyzer->getNodeMetrics($class);

        static::assertIsArray($metrics);
        static::assertCount(0, $metrics);
    }

    /**
     * @param array<string, array<int, string>|string> $options
     * @return array<mixed>
     */
    protected function getCodeRankForTestCase(array $options = [])
    {
        return $this->getCodeRankOrReverseCodeRank('cr', $options);
    }

    /**
     * @param array<string, array<int, string>|string> $options
     * @return array<mixed>
     */
    protected function getReverseCodeRankForTestCase(array $options = [])
    {
        return $this->getCodeRankOrReverseCodeRank('rcr', $options);
    }

    /**
     * @param array<string, array<int, string>|string> $options
     * @return array<mixed>
     */
    protected function getCodeRankOrReverseCodeRank(string $metricName, array $options = [])
    {
        $namespaces = $this->parseCodeResourceForTest();

        $analyzer = new CodeRankAnalyzer($options);
        $analyzer->analyze($namespaces);

        $namespaces->rewind();

        $actual = [];
        foreach ($namespaces[0]->getTypes() as $type) {
            $metrics = $analyzer->getNodeMetrics($type);
            $actual[$type->getImage()] = round($metrics[$metricName], 5);
        }

        return $actual;
    }
}
