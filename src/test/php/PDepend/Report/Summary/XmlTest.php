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

namespace PDepend\Report\Summary;

use PDepend\AbstractTestCase;
use PDepend\Metrics\AnalyzerNodeAware;
use PDepend\Metrics\AnalyzerProjectAware;
use PDepend\Report\NoLogOutputException;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTNamespace;

/**
 * Test case for the xml summary log.
 *
 * @covers \PDepend\Report\Summary\Xml
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class XmlTest extends AbstractTestCase
{
    /**
     * Test code structure.
     *
     * @var ASTArtifactList<ASTNamespace>
     */
    protected ASTArtifactList $namespaces;

    /** The temporary file name for the logger result. */
    protected string $resultFile;

    /**
     * Creates the package structure from a test source file.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->resultFile = $this->createRunResourceURI('log-summary.xml');
    }

    /**
     * Removes the temporary log files.
     */
    protected function tearDown(): void
    {
        @unlink($this->resultFile);

        parent::tearDown();
    }

    /**
     * Tests that the logger returns the expected set of analyzers.
     */
    public function testReturnsExceptedAnalyzers(): void
    {
        $logger = new Xml();
        $actual = $logger->getAcceptedAnalyzers();
        $expected = [
            'pdepend.analyzer.cyclomatic_complexity',
            'pdepend.analyzer.node_loc',
            'pdepend.analyzer.npath_complexity',
            'pdepend.analyzer.inheritance',
            'pdepend.analyzer.node_count',
            'pdepend.analyzer.hierarchy',
            'pdepend.analyzer.crap_index',
            'pdepend.analyzer.code_rank',
            'pdepend.analyzer.coupling',
            'pdepend.analyzer.class_level',
            'pdepend.analyzer.cohesion',
            'pdepend.analyzer.halstead',
            'pdepend.analyzer.maintainability',
        ];

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the logger throws an exception if the log target wasn't
     * configured.
     */
    public function testThrowsExceptionForInvalidLogTarget(): void
    {
        $this->expectException(
            NoLogOutputException::class
        );
        $this->expectExceptionMessage(
            "The log target is not configured for 'PDepend\\Report\\Summary\\Xml'."
        );

        $logger = new Xml();
        $logger->close();
    }

    /**
     * testLogMethodReturnsTrueForAnalyzerOfTypeProjectAware
     */
    public function testLogMethodReturnsTrueForAnalyzerOfTypeProjectAware(): void
    {
        $analyzer = $this->getMockBuilder(AnalyzerProjectAware::class)
            ->getMock();

        $logger = new Xml();
        $actual = $logger->log($analyzer);

        static::assertTrue($actual);
    }

    /**
     * testLogMethodReturnsTrueForAnalyzerOfTypeNodeAware
     */
    public function testLogMethodReturnsTrueForAnalyzerOfTypeNodeAware(): void
    {
        $analyzer = $this->getMockBuilder(AnalyzerNodeAware::class)
            ->getMock();

        $logger = new Xml();
        $actual = $logger->log($analyzer);

        static::assertTrue($actual);
    }

    /**
     * Tests that {@link \PDepend\Report\Summary\Xml::write()} generates the
     * expected document structure for the source, but without any applied
     * metrics.
     */
    public function testXmlLogWithoutMetrics(): void
    {
        $this->namespaces = $this->parseCodeResourceForTest();

        $log = new Xml();
        $log->setLogFile($this->resultFile);
        $log->setArtifacts($this->namespaces);
        $log->close();

        $fileName = 'xml-log-without-metrics.xml';
        static::assertXmlStringEqualsXmlString(
            $this->getNormalizedPathXml(__DIR__ . "/_expected/{$fileName}"),
            $this->getNormalizedPathXml($this->resultFile)
        );
    }

    /**
     * Tests that the xml logger generates the expected xml document for an
     * empty source code structure.
     */
    public function testProjectAwareAnalyzerWithoutCode(): void
    {
        $metricsOne = ['interfs' => 42, 'cls' => 23];
        $resultOne = new AnalyzerProjectAwareDummy($metricsOne);

        $metricsTwo = ['ncloc' => 1742, 'loc' => 4217];
        $resultTwo = new AnalyzerProjectAwareDummy($metricsTwo);

        $log = new Xml();
        $log->setLogFile($this->resultFile);
        $log->setArtifacts(new ASTArtifactList([]));
        $log->log($resultOne);
        $log->log($resultTwo);

        $log->close();

        $fileName = 'project-aware-result-set-without-code.xml';
        static::assertXmlStringEqualsXmlString(
            $this->getNormalizedPathXml(__DIR__ . "/_expected/{$fileName}"),
            $this->getNormalizedPathXml($this->resultFile)
        );
    }

    /**
     * testAnalyzersThatImplementProjectAndNodeAwareAsExpected
     */
    public function testAnalyzersThatImplementProjectAndNodeAwareAsExpected(): void
    {
        $this->namespaces = $this->parseCodeResourceForTest();

        $analyzer = new AnalyzerNodeAndProjectAwareDummy(
            ['foo' => 42, 'bar' => 23],
            ['baz' => 23, 'foobar' => 42]
        );

        $log = new Xml();
        $log->setLogFile($this->resultFile);
        $log->setArtifacts($this->namespaces);
        $log->log($analyzer);

        $log->close();

        $fileName = 'node-and-project-aware-result-set.xml';
        static::assertXmlStringEqualsXmlString(
            $this->getNormalizedPathXml(__DIR__ . "/_expected/{$fileName}"),
            $this->getNormalizedPathXml($this->resultFile)
        );
    }

    /**
     * @dataProvider dataProviderNodeAware
     */
    public function testNodeAwareAnalyzer(string $fixture, string $expectation): void
    {
        $this->namespaces = $this->parseSource($fixture);

        $input = [
            ['loc' => 42], ['ncloc' => 23],
            ['loc' => 9], ['ncloc' => 7],
            ['loc' => 101], ['ncloc' => 99],
            ['loc' => 90], ['ncloc' => 80],
            ['loc' => 50], ['ncloc' => 45],
            ['loc' => 30], ['ncloc' => 22],
            ['loc' => 9], ['ncloc' => 9],
            ['loc' => 3], ['ncloc' => 3],
            ['loc' => 42], ['ncloc' => 23],
            ['loc' => 33], ['ncloc' => 20],
            ['loc' => 9], ['ncloc' => 7],
        ];

        $metricsOne = [];
        $metricsTwo = [];
        foreach ($this->namespaces as $namespace) {
            $metricsOne[$namespace->getId()] = array_shift($input) ?? [];
            $metricsTwo[$namespace->getId()] = array_shift($input) ?? [];
            foreach ($namespace->getClasses() as $class) {
                $metricsOne[$class->getId()] = array_shift($input) ?? [];
                $metricsTwo[$class->getId()] = array_shift($input) ?? [];
                foreach ($class->getMethods() as $method) {
                    $metricsOne[$method->getId()] = array_shift($input) ?? [];
                    $metricsTwo[$method->getId()] = array_shift($input) ?? [];
                }
            }
            foreach ($namespace->getFunctions() as $function) {
                $metricsOne[$function->getId()] = array_shift($input) ?? [];
                $metricsTwo[$function->getId()] = array_shift($input) ?? [];
            }
        }

        $resultOne = new AnalyzerNodeAwareDummy($metricsOne);
        $resultTwo = new AnalyzerNodeAwareDummy($metricsTwo);

        $log = new Xml();
        $log->setLogFile($this->resultFile);
        $log->setArtifacts($this->namespaces);
        $log->log($resultOne);
        $log->log($resultTwo);

        $log->close();

        static::assertXmlStringEqualsXmlString(
            $this->getNormalizedPathXml(__DIR__ . "/_expected/{$expectation}"),
            $this->getNormalizedPathXml($this->resultFile)
        );
    }

    /**
     * @return list<list<string>>
     */
    public static function dataProviderNodeAware(): array
    {
        return [
            [
                'Report/Summary/Xml/testNodeAwareAnalyzerWithNamespaces.php',
                'node-aware-result-set-with-namespaces.xml',
            ],
            [
                'Report/Summary/Xml/testNodeAwareAnalyzerWithPackages.php',
                'node-aware-result-set-with-packages.xml',
            ],
        ];
    }

    protected function getNormalizedPathXml(string $fileName): string
    {
        $string = preg_replace(
            ['(file\s+name="[^"]+")', '(generated="[^"]*")'],
            ['file name="' . __FILE__ . '"', 'generated=""'],
            file_get_contents($fileName) ?: ''
        );
        static::assertNotNull($string);

        return $string;
    }
}
