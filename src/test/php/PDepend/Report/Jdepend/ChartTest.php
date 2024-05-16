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

namespace PDepend\Report\Jdepend;

use DOMDocument;
use DOMXPath;
use PDepend\AbstractTestCase;
use PDepend\Metrics\Analyzer\DependencyAnalyzer;
use PDepend\Report\DummyAnalyzer;
use PDepend\Report\NoLogOutputException;
use PDepend\Source\AST\AbstractASTArtifact;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTNamespace;

/**
 * Test case for the jdepend chart logger.
 *
 * @covers \PDepend\Report\Jdepend\Chart
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ChartTest extends AbstractTestCase
{
    /** Temporary output file. */
    private string $outputFile;

    /**
     * setUp()
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->outputFile = $this->createRunResourceURI('jdepend-test-out') . '.svg';
        if (file_exists($this->outputFile)) {
            unlink($this->outputFile);
        }
    }

    /**
     * tearDown()
     */
    protected function tearDown(): void
    {
        if (file_exists($this->outputFile)) {
            unlink($this->outputFile);
        }
        parent::tearDown();
    }

    /**
     * Tests that the logger returns the expected set of analyzers.
     */
    public function testReturnsExceptedAnalyzers(): void
    {
        $logger = new Chart();
        static::assertEquals(['pdepend.analyzer.dependency'], $logger->getAcceptedAnalyzers());
    }

    /**
     * Tests that the logger throws an exception if the log target wasn't
     * configured.
     */
    public function testThrowsExceptionForInvalidLogTarget(): void
    {
        $this->expectException(NoLogOutputException::class);

        $logger = new Chart();
        $logger->close();
    }

    /**
     * testChartLogAcceptsValidAnalyzer
     */
    public function testChartLogAcceptsValidAnalyzer(): void
    {
        $logger = new Chart();
        static::assertTrue($logger->log(new DependencyAnalyzer()));
    }

    /**
     * testChartLogRejectsInvalidAnalyzer
     */
    public function testChartLogRejectsInvalidAnalyzer(): void
    {
        $logger = new Chart();
        static::assertFalse($logger->log(new DummyAnalyzer()));
    }

    /**
     * Tests that the logger generates an image file.
     */
    public function testGeneratesCorrectSVGImageFile(): void
    {
        $nodes = new ASTArtifactList($this->createPackages(true, true));

        $analyzer = new DependencyAnalyzer();
        $analyzer->analyze($nodes);

        $logger = new Chart();
        $logger->setLogFile($this->outputFile);
        $logger->setArtifacts($nodes);
        $logger->log($analyzer);
        $logger->close();

        static::assertFileExists($this->outputFile);
    }

    /**
     * testGeneratedSvgImageContainsExpectedPackages
     */
    public function testGeneratedSvgImageContainsExpectedPackages(): void
    {
        $nodes = new ASTArtifactList($this->createPackages(true, true));

        $analyzer = new DependencyAnalyzer();
        $analyzer->analyze($nodes);

        $logger = new Chart();
        $logger->setLogFile($this->outputFile);
        $logger->setArtifacts($nodes);
        $logger->log($analyzer);
        $logger->close();

        $svg = new DOMDocument();
        $svg->load($this->outputFile, LIBXML_NOWARNING);

        $xpath = new DOMXPath($svg);
        $xpath->registerNamespace('s', 'http://www.w3.org/2000/svg');

        static::assertEquals(1, $xpath->query("//s:ellipse[@title='package0']")->length);
        static::assertEquals(1, $xpath->query("//s:ellipse[@title='package1']")->length);
    }

    /**
     * testGeneratesSVGImageDoesNotContainNoneUserDefinedPackages
     */
    public function testGeneratesSVGImageDoesNotContainNoneUserDefinedPackages(): void
    {
        $nodes = new ASTArtifactList($this->createPackages(true, false, true));

        $analyzer = new DependencyAnalyzer();
        $analyzer->analyze($nodes);

        $logger = new Chart();
        $logger->setLogFile($this->outputFile);
        $logger->setArtifacts($nodes);
        $logger->log($analyzer);
        $logger->close();

        $svg = new DOMDocument();
        $svg->load($this->outputFile, LIBXML_NOWARNING);

        $xpath = new DOMXPath($svg);
        $xpath->registerNamespace('s', 'http://www.w3.org/2000/svg');

        static::assertEquals(0, $xpath->query("//s:ellipse[@title='package1']")->length);
    }

    /**
     * testCalculateCorrectEllipseSize
     */
    public function testCalculateCorrectEllipseSize(): void
    {
        $nodes = $this->createPackages(true, true);

        $analyzer = $this->getMockBuilder(DependencyAnalyzer::class)
            ->getMock();
        $analyzer->expects(static::atLeastOnce())
            ->method('getStats')
            ->will(
                static::returnCallback(
                    function (AbstractASTArtifact $node) use ($nodes) {
                        $data = [
                            $nodes[0]->getId() => [
                                'a' => 0,
                                'i' => 0,
                                'd' => 0,
                                'cc' => 250,
                                'ac' => 250,
                            ],
                            $nodes[1]->getId() => [
                                'a' => 0,
                                'i' => 0,
                                'd' => 0,
                                'cc' => 50,
                                'ac' => 50,
                            ],
                        ];

                        if (isset($data[$node->getId()])) {
                            return $data[$node->getId()];
                        }

                        return [];
                    }
                )
            );

        $nodes = new ASTArtifactList($nodes);

        $logger = new Chart();
        $logger->setLogFile($this->outputFile);
        $logger->setArtifacts($nodes);
        $logger->log($analyzer);

        $logger->close();

        $svg = new DOMDocument();
        $svg->load($this->outputFile, LIBXML_NOWARNING);

        $xpath = new DOMXPath($svg);
        $xpath->registerNamespace('s', 'http://www.w3.org/2000/svg');

        $ellipseA = $xpath->query("//s:ellipse[@title='package0']")->item(0);
        $matrixA = $ellipseA->getAttribute('transform');
        preg_match('/matrix\(([^,]+),([^,]+),([^,]+),([^,]+),([^,]+),([^,]+)\)/', $matrixA, $matches);
        static::assertEquals(1, $matches[1]);
        static::assertEquals(1, $matches[4]);

        $ellipseB = $xpath->query("//s:ellipse[@title='package1']")->item(0);
        $matrixB = $ellipseB->getAttribute('transform');
        preg_match('/matrix\(([^,]+),([^,]+),([^,]+),([^,]+),([^,]+),([^,]+)\)/', $matrixB, $matches);
        static::assertEqualsWithDelta(0.3333333, $matches[1], 0.000001);
        static::assertEqualsWithDelta(0.3333333, $matches[4], 0.000001);
    }

    /**
     * Tests that the logger generates an image file.
     */
    public function testGeneratesImageFile(): void
    {
        $this->requireImagick();

        $fileName = $this->createRunResourceURI('jdepend-test-out') . '.png';
        if (file_exists($fileName)) {
            unlink($fileName);
        }

        $nodes = new ASTArtifactList($this->createPackages(true, true));

        $analyzer = new DependencyAnalyzer();
        $analyzer->analyze($nodes);

        $logger = new Chart();
        $logger->setLogFile($fileName);
        $logger->setArtifacts($nodes);
        $logger->log($analyzer);

        static::assertFileDoesNotExist($fileName);
        $logger->close();
        static::assertFileExists($fileName);

        $info = getimagesize($fileName);
        // $this->assertEquals(390, $info[0]);
        // $this->assertEquals(250, $info[1]);
        static::assertEquals('image/png', $info['mime']);

        unlink($fileName);
    }

    /**
     * @return ASTNamespace[]
     */
    private function createPackages()
    {
        $packages = [];
        foreach (func_get_args() as $i => $userDefined) {
            $packages[] = $this->createPackage(
                $userDefined,
                'package' . $i
            );
        }

        return $packages;
    }

    /**
     * @param bool $userDefined
     * @param string $packageName
     * @return ASTNamespace
     */
    private function createPackage($userDefined, $packageName)
    {
        $packageA = $this->getMockBuilder(ASTNamespace::class)
            ->onlyMethods(['isUserDefined'])
            ->setConstructorArgs([$packageName])
            ->setMockClassName(substr('package_' . md5(microtime()), 0, 18) . '_ASTNamespace')
            ->getMock();
        $packageA->expects(static::atLeastOnce())
            ->method('isUserDefined')
            ->will(static::returnValue($userDefined));

        return $packageA;
    }
}
