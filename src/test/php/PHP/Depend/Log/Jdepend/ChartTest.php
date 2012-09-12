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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Log
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';
require_once dirname(__FILE__) . '/../DummyAnalyzer.php';
require_once dirname(__FILE__) . '/DependencyAnalyzer.php';

/**
 * Test case for the jdepend chart logger.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Log
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 *
 * @covers PHP_Depend_Log_Jdepend_Chart
 * @group pdepend
 * @group pdepend::log
 * @group pdepend::log::jdepend
 * @group unittest
 */
class PHP_Depend_Log_Jdepend_ChartTest extends PHP_Depend_AbstractTest
{
    /**
     * Temporary output file.
     *
     * @var string
     */
    private $_outputFile = null;

    /**
     * setUp()
     * 
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_outputFile = self::createRunResourceURI('jdepend-test-out.svg');
        if (file_exists($this->_outputFile)) {
            unlink($this->_outputFile);
        }
    }

    /**
     * tearDown()
     *
     * @return void
     */
    protected function tearDown()
    {
        if (file_exists($this->_outputFile)) {
            unlink($this->_outputFile);
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
        $logger    = new PHP_Depend_Log_Jdepend_Chart();
        $actual    = $logger->getAcceptedAnalyzers();
        $exptected = array('PHP_Depend_Metrics_Dependency_Analyzer');

        self::assertEquals($exptected, $actual);
    }

    /**
     * Tests that the logger throws an exception if the log target wasn't
     * configured.
     *
     * @return void
     * @expectedException PHP_Depend_Log_NoLogOutputException
     */
    public function testThrowsExceptionForInvalidLogTarget()
    {
        $logger = new PHP_Depend_Log_Jdepend_Chart();
        $logger->close();
    }

    /**
     * testChartLogAcceptsValidAnalyzer
     *
     * @return void
     */
    public function testChartLogAcceptsValidAnalyzer()
    {
        $logger = new PHP_Depend_Log_Jdepend_Chart();
        self::assertTrue($logger->log(new PHP_Depend_Metrics_Dependency_Analyzer()));
    }

    /**
     * testChartLogRejectsInvalidAnalyzer
     *
     * @return void
     */
    public function testChartLogRejectsInvalidAnalyzer()
    {
        $logger = new PHP_Depend_Log_Jdepend_Chart();
        self::assertFalse($logger->log(new PHP_Depend_Log_DummyAnalyzer()));
    }

    /**
     * Tests that the logger generates an image file.
     *
     * @return void
     */
    public function testGeneratesCorrectSVGImageFile()
    {
        $nodes = new PHP_Depend_Code_NodeIterator($this->_createPackages(true, true));

        $analyzer = new PHP_Depend_Metrics_Dependency_Analyzer();
        $analyzer->analyze($nodes);

        $logger = new PHP_Depend_Log_Jdepend_Chart();
        $logger->setLogFile($this->_outputFile);
        $logger->setCode($nodes);
        $logger->log($analyzer);
        $logger->close();

        self::assertFileExists($this->_outputFile);
    }

    /**
     * testGeneratedSvgImageContainsExpectedPackages
     *
     * @return void
     */
    public function testGeneratedSvgImageContainsExpectedPackages()
    {
        $nodes = new PHP_Depend_Code_NodeIterator($this->_createPackages(true, true));

        $analyzer = new PHP_Depend_Metrics_Dependency_Analyzer();
        $analyzer->analyze($nodes);

        $logger = new PHP_Depend_Log_Jdepend_Chart();
        $logger->setLogFile($this->_outputFile);
        $logger->setCode($nodes);
        $logger->log($analyzer);
        $logger->close();

        $svg = new DOMDocument();
        $svg->load($this->_outputFile);

        $xpath = new DOMXPath($svg);
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
        $nodes = new PHP_Depend_Code_NodeIterator($this->_createPackages(true, false, true));

        $analyzer = new PHP_Depend_Metrics_Dependency_Analyzer();
        $analyzer->analyze($nodes);

        $logger = new PHP_Depend_Log_Jdepend_Chart();
        $logger->setLogFile($this->_outputFile);
        $logger->setCode($nodes);
        $logger->log($analyzer);
        $logger->close();

        $svg = new DOMDocument();
        $svg->load($this->_outputFile);

        $xpath = new DOMXPath($svg);
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
        $nodes = $this->_createPackages(true, true);

        $analyzer = new PHP_Depend_Log_Jdepend_DependencyAnalyzer();
        $analyzer->stats = array(
            $nodes[0]->getUuid()  =>  array(
                'a'   =>  0,
                'i'   =>  0,
                'd'   =>  0,
                'cc'  =>  250,
                'ac'  =>  250
            ),
            $nodes[1]->getUuid()  =>  array(
                'a'   =>  0,
                'i'   =>  0,
                'd'   =>  0,
                'cc'  =>  50,
                'ac'  =>  50
            ),
        );

        $nodes = new PHP_Depend_Code_NodeIterator($nodes);

        $logger = new PHP_Depend_Log_Jdepend_Chart();
        $logger->setLogFile($this->_outputFile);
        $logger->setCode($nodes);
        $logger->log($analyzer);

        $logger->close();

        $svg = new DOMDocument();
        $svg->load($this->_outputFile);

        $xpath = new DOMXPath($svg);
        $xpath->registerNamespace('s', 'http://www.w3.org/2000/svg');

        $ellipseA = $xpath->query("//s:ellipse[@title='package0']")->item(0);
        $matrixA  = $ellipseA->getAttribute('transform');
        preg_match('/matrix\(([^,]+),([^,]+),([^,]+),([^,]+),([^,]+),([^,]+)\)/', $matrixA, $matches);
        $this->assertEquals(1, $matches[1]);
        $this->assertEquals(1, $matches[4]);

        $ellipseB = $xpath->query("//s:ellipse[@title='package1']")->item(0);
        $matrixB  = $ellipseB->getAttribute('transform');
        preg_match('/matrix\(([^,]+),([^,]+),([^,]+),([^,]+),([^,]+),([^,]+)\)/', $matrixB, $matches);
        $this->assertEquals(0.3333333, $matches[1], null, 0.000001);
        $this->assertEquals(0.3333333, $matches[4], null, 0.000001);
    }

    /**
     * Tests that the logger generates an image file.
     *
     * @return void
     */
    public function testGeneratesImageFile()
    {
        if (extension_loaded('imagick') === false) {
            $this->markTestSkipped('No pecl/imagick extension.');
        }

        $fileName = self::createRunResourceURI('jdepend-test-out.png');
        if (file_exists($fileName)) {
            unlink($fileName);
        }

        $nodes = new PHP_Depend_Code_NodeIterator($this->_createPackages(true, true));

        $analyzer = new PHP_Depend_Metrics_Dependency_Analyzer();
        $analyzer->analyze($nodes);

        $logger = new PHP_Depend_Log_Jdepend_Chart();
        $logger->setLogFile($fileName);
        $logger->setCode($nodes);
        $logger->log($analyzer);

        $this->assertFileNotExists($fileName);
        $logger->close();
        $this->assertFileExists($fileName);

        $info = getimagesize($fileName);
        $this->assertEquals(390, $info[0]);
        $this->assertEquals(250, $info[1]);
        $this->assertEquals('image/png', $info['mime']);

        unlink($fileName);
    }

    private function _createPackages()
    {
        $packages = array();
        foreach (func_get_args() as $i => $userDefined) {
            $packages[] = $this->_createPackage(
                $userDefined,
                'package' . $i
            );
        }
        return $packages;
    }

    private function _createPackage($userDefined, $packageName)
    {
        $packageA = $this->getMock(
            'PHP_Depend_Code_Package',
            array('isUserDefined'),
            array($packageName),
            'package_' . md5(microtime())
        );
        $packageA->expects($this->atLeastOnce())
            ->method('isUserDefined')
            ->will($this->returnValue($userDefined));

        return $packageA;
    }
}
