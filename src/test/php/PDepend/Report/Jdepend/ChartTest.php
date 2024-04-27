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

use PDepend\AbstractTestCase;
use PDepend\Metrics\Analyzer\DependencyAnalyzer;
use PDepend\Report\DummyAnalyzer;
use PDepend\Source\AST\AbstractASTArtifact;
use PDepend\Source\AST\ASTArtifactList;

/**
 * Test case for the jdepend chart logger.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Report\Jdepend\Chart
 * @group unittest
 */
class ChartTest extends AbstractTestCase
{
    /**
     * Temporary output file.
     *
     * @var string
     */
    private $outputFile = null;

    /**
     * setUp()
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return void
     */
    public function testReturnsExceptedAnalyzers()
    {
        $logger    = new Chart();
        $this->assertEquals(array('pdepend.analyzer.dependency'), $logger->getAcceptedAnalyzers());
    }

    /**
     * Tests that the logger throws an exception if the log target wasn't
     * configured.
     *
     * @return void
     */
    public function testThrowsExceptionForInvalidLogTarget()
    {
        $this->expectException(\PDepend\Report\NoLogOutputException::class);

        $logger = new Chart();
        $logger->close();
    }

    /**
     * testChartLogAcceptsValidAnalyzer
     *
     * @return void
     */
    public function testChartLogAcceptsValidAnalyzer()
    {
        $logger = new Chart();
        $this->assertTrue($logger->log(new DependencyAnalyzer()));
    }

    /**
     * testChartLogRejectsInvalidAnalyzer
     *
     * @return void
     */
    public function testChartLogRejectsInvalidAnalyzer()
    {
        $logger = new Chart();
        $this->assertFalse($logger->log(new DummyAnalyzer()));
    }

    /**
     * Tests that the logger generates an image file.
     *
     * @return void
     */
    public function testGeneratesCorrectSVGImageFile()
    {
        $nodes = new ASTArtifactList($this->createPackages(true, true));

        $analyzer = new DependencyAnalyzer();
        $analyzer->analyze($nodes);

        $logger = new Chart();
        $logger->setLogFile($this->outputFile);
        $logger->setArtifacts($nodes);
        $logger->log($analyzer);
        $logger->close();

        $this->assertFileExists($this->outputFile);
    }

    /**
     * testGeneratedSvgImageContainsExpectedPackages
     *
     * @return void
     */
    public function testGeneratedSvgImageContainsExpectedPackages()
    {
        $nodes = new ASTArtifactList($this->createPackages(true, true));

        $analyzer = new DependencyAnalyzer();
        $analyzer->analyze($nodes);

        $logger = new Chart();
        $logger->setLogFile($this->outputFile);
        $logger->setArtifacts($nodes);
        $logger->log($analyzer);
        $logger->close();

        $svg = new \DOMDocument();
        $svg->load($this->outputFile, LIBXML_NOWARNING);

        $xpath = new \DOMXPath($svg);
        $xpath->registerNamespace('s', 'http://www.w3.org/2000/svg');

        $this->assertEquals(1, $xpath->query("//s:ellipse[@title='package0']")->length);
        $this->assertEquals(1, $xpath->query("//s:ellipse[@title='package1']")->length);
    }

    /**
     * testGeneratesSVGImageDoesNotContainNoneUserDefinedPackages
     *
     * @return void
     */
    public function testGeneratesSVGImageDoesNotContainNoneUserDefinedPackages()
    {
        $nodes = new ASTArtifactList($this->createPackages(true, false, true));

        $analyzer = new DependencyAnalyzer();
        $analyzer->analyze($nodes);

        $logger = new Chart();
        $logger->setLogFile($this->outputFile);
        $logger->setArtifacts($nodes);
        $logger->log($analyzer);
        $logger->close();

        $svg = new \DOMDocument();
        $svg->load($this->outputFile, LIBXML_NOWARNING);

        $xpath = new \DOMXPath($svg);
        $xpath->registerNamespace('s', 'http://www.w3.org/2000/svg');

        $this->assertEquals(0, $xpath->query("//s:ellipse[@title='package1']")->length);
    }

    /**
     * testCalculateCorrectEllipseSize
     *
     * @return void
     */
    public function testCalculateCorrectEllipseSize()
    {
        $nodes = $this->createPackages(true, true);

        $analyzer = $this->getMockBuilder('\\PDepend\\Metrics\\Analyzer\\DependencyAnalyzer')
            ->getMock();
        $analyzer->expects($this->atLeastOnce())
            ->method('getStats')
            ->will(
                $this->returnCallback(
                    function (AbstractASTArtifact $node) use ($nodes) {
                        $data = array(
                            $nodes[0]->getId()  =>  array(
                                'a'   =>  0,
                                'i'   =>  0,
                                'd'   =>  0,
                                'cc'  =>  250,
                                'ac'  =>  250
                            ),
                            $nodes[1]->getId()  =>  array(
                                'a'   =>  0,
                                'i'   =>  0,
                                'd'   =>  0,
                                'cc'  =>  50,
                                'ac'  =>  50
                            ),
                        );

                        if (isset($data[$node->getId()])) {
                            return $data[$node->getId()];
                        }
                        return array();
                    }
                )
            );

        $nodes = new ASTArtifactList($nodes);

        $logger = new Chart();
        $logger->setLogFile($this->outputFile);
        $logger->setArtifacts($nodes);
        $logger->log($analyzer);

        $logger->close();

        $svg = new \DOMDocument();
        $svg->load($this->outputFile, LIBXML_NOWARNING);

        $xpath = new \DOMXPath($svg);
        $xpath->registerNamespace('s', 'http://www.w3.org/2000/svg');

        $ellipseA = $xpath->query("//s:ellipse[@title='package0']")->item(0);
        $matrixA  = $ellipseA->getAttribute('transform');
        preg_match('/matrix\(([^,]+),([^,]+),([^,]+),([^,]+),([^,]+),([^,]+)\)/', $matrixA, $matches);
        $this->assertEquals(1, $matches[1]);
        $this->assertEquals(1, $matches[4]);

        $ellipseB = $xpath->query("//s:ellipse[@title='package1']")->item(0);
        $matrixB  = $ellipseB->getAttribute('transform');
        preg_match('/matrix\(([^,]+),([^,]+),([^,]+),([^,]+),([^,]+),([^,]+)\)/', $matrixB, $matches);
        $this->assertEqualsWithDelta(0.3333333, $matches[1], 0.000001);
        $this->assertEqualsWithDelta(0.3333333, $matches[4], 0.000001);
    }

    /**
     * Tests that the logger generates an image file.
     *
     * @return void
     */
    public function testGeneratesImageFile()
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

        $this->assertFileDoesNotExist($fileName);
        $logger->close();
        $this->assertFileExists($fileName);

        $info = getimagesize($fileName);
        //$this->assertEquals(390, $info[0]);
        //$this->assertEquals(250, $info[1]);
        $this->assertEquals('image/png', $info['mime']);

        unlink($fileName);
    }

    /**
     * @return \PDepend\Source\AST\ASTNamespace[]
     */
    private function createPackages()
    {
        $packages = array();
        foreach (func_get_args() as $i => $userDefined) {
            $packages[] = $this->createPackage(
                $userDefined,
                'package' . $i
            );
        }
        return $packages;
    }

    /**
     * @param boolean $userDefined
     * @param string $packageName
     * @return \PDepend\Source\AST\ASTNamespace
     */
    private function createPackage($userDefined, $packageName)
    {
        $packageA = $this->getMockBuilder('\\PDepend\\Source\\AST\\ASTNamespace')
            ->onlyMethods(array('isUserDefined'))
            ->setConstructorArgs(array($packageName))
            ->setMockClassName(substr('package_' . md5(microtime()), 0, 18) . '_ASTNamespace')
            ->getMock();
        $packageA->expects($this->atLeastOnce())
            ->method('isUserDefined')
            ->will($this->returnValue($userDefined));

        return $packageA;
    }
}
