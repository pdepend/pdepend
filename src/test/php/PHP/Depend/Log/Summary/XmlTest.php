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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Log
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';
require_once dirname(__FILE__) . '/AnalyzerNodeAwareDummy.php';
require_once dirname(__FILE__) . '/AnalyzerProjectAwareDummy.php';
require_once dirname(__FILE__) . '/AnalyzerNodeAndProjectAwareDummy.php';

require_once 'PHP/Depend/Log/Summary/Xml.php';
require_once 'PHP/Depend/Metrics/NodeAwareI.php';
require_once 'PHP/Depend/Metrics/ProjectAwareI.php';

/**
 * Test case for the xml summary log.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Log
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Log_Summary_XmlTest extends PHP_Depend_AbstractTest
{
    /**
     * Test code structure.
     *
     * @var PHP_Depend_Code_NodeIterator $packages
     */
    protected $packages = null;

    /**
     * The test file name.
     *
     * @var string $testFile
     */
    protected $testFileName = null;

    /**
     * The temporary file name for the logger result.
     *
     * @var string $resultFile
     */
    protected $resultFile = null;

    /**
     * Creates the package structure from a test source file.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->testFileName = dirname(__FILE__) . '/../../_code/mixed_code.php';
        $this->testFileName = realpath($this->testFileName);

        $this->packages   = self::parseSource($this->testFileName);
        $this->resultFile = self::createRunResourceURI('log-summary.xml');
    }

    /**
     * Removes the temporary log files.
     *
     * @return void
     */
    protected function tearDown()
    {
        @unlink($this->resultFile);

        parent::tearDown();
    }

    /**
     * Tests that the logger returns the expected set of analyzers.
     *
     * @return void
     * @covers PHP_Depend_Log_Summary_Xml
     * @group pdepend
     * @group pdepend::log
     * @group pdepend::log::summary
     * @group unittest
     */
    public function testReturnsExceptedAnalyzers()
    {
        $logger    = new PHP_Depend_Log_Summary_Xml();
        $actual    = $logger->getAcceptedAnalyzers();
        $exptected = array(
            'PHP_Depend_Metrics_NodeAwareI',
            'PHP_Depend_Metrics_ProjectAwareI'
        );

        $this->assertEquals($exptected, $actual);
    }

    /**
     * Tests that the logger throws an exception if the log target wasn't
     * configured.
     *
     * @return void
     * @covers PHP_Depend_Log_Summary_Xml
     * @group pdepend
     * @group pdepend::log
     * @group pdepend::log::summary
     * @group unittest
     */
    public function testThrowsExceptionForInvalidLogTarget()
    {
        $this->setExpectedException(
            'PHP_Depend_Log_NoLogOutputException',
            "The log target is not configured for 'PHP_Depend_Log_Summary_Xml'."
        );

        $logger = new PHP_Depend_Log_Summary_Xml();
        $logger->close();
    }

    /**
     * testLogMethodReturnsTrueForAnalyzerOfTypeProjectAware
     *
     * @return void
     * @covers PHP_Depend_Log_Summary_Xml
     * @group pdepend
     * @group pdepend::logs
     * @group pdepend::logs::summary
     * @group unittest
     */
    public function testLogMethodReturnsTrueForAnalyzerOfTypeProjectAware()
    {
        $logger = new PHP_Depend_Log_Summary_Xml();
        $actual = $logger->log($this->getMock('PHP_Depend_Metrics_ProjectAwareI'));

        $this->assertTrue($actual);
    }

    /**
     * testLogMethodReturnsTrueForAnalyzerOfTypeNodeAware
     *
     * @return void
     * @covers PHP_Depend_Log_Summary_Xml
     * @group pdepend
     * @group pdepend::logs
     * @group pdepend::logs::summary
     * @group unittest
     */
    public function testLogMethodReturnsTrueForAnalyzerOfTypeNodeAware()
    {
        $logger = new PHP_Depend_Log_Summary_Xml();
        $actual = $logger->log($this->getMock('PHP_Depend_Metrics_NodeAwareI'));

        $this->assertTrue($actual);
    }

    /**
     * Tests that {@link PHP_Depend_Log_Summary_Xml::write()} generates the
     * expected document structure for the source, but without any applied
     * metrics.
     *
     * @return void
     * @covers PHP_Depend_Log_Summary_Xml
     * @group pdepend
     * @group pdepend::log
     * @group pdepend::log::summary
     * @group unittest
     */
    public function testXmlLogWithoutMetrics()
    {
        $log = new PHP_Depend_Log_Summary_Xml();
        $log->setLogFile($this->resultFile);
        $log->setCode($this->packages);
        $log->close();

        $fileName = 'xml-log-without-metrics.xml';
        $this->assertXmlStringEqualsXmlString(
            $this->getNormalizedPathXml(dirname(__FILE__) . "/_expected/{$fileName}"),
            $this->getNormalizedPathXml($this->resultFile)
        );
    }

    /**
     * Tests that the xml logger generates the expected xml document for an
     * empty source code structure.
     *
     * @return void
     * @covers PHP_Depend_Log_Summary_Xml
     * @group pdepend
     * @group pdepend::log
     * @group pdepend::log::summary
     * @group unittest
     */
    public function testProjectAwareAnalyzerWithoutCode()
    {
        $metricsOne = array('interfs'  =>  42, 'cls'  =>  23);
        $resultOne  = new PHP_Depend_Log_Summary_AnalyzerProjectAwareDummy($metricsOne);

        $metricsTwo = array('ncloc'  =>  1742, 'loc'  =>  4217);
        $resultTwo  = new PHP_Depend_Log_Summary_AnalyzerProjectAwareDummy($metricsTwo);

        $log = new PHP_Depend_Log_Summary_Xml();
        $log->setLogFile($this->resultFile);
        $log->setCode(new PHP_Depend_Code_NodeIterator(array()));
        $log->log($resultOne);
        $log->log($resultTwo);

        $log->close();

        $fileName = 'project-aware-result-set-without-code.xml';
        $this->assertXmlStringEqualsXmlString(
            $this->getNormalizedPathXml(dirname(__FILE__) . "/_expected/{$fileName}"),
            $this->getNormalizedPathXml($this->resultFile)
        );
    }

    /**
     * testAnalyzersThatImplementProjectAndNodeAwareAsExpected
     *
     * @return void
     * @covers PHP_Depend_Log_Summary_Xml
     * @group pdepend
     * @group pdepend::log
     * @group pdepend::log::summary
     * @group unittest
     */
    public function testAnalyzersThatImplementProjectAndNodeAwareAsExpected()
    {
        $analyzer = new PHP_Depend_Log_Summary_AnalyzerNodeAndProjectAwareDummy(
            array('foo' => 42, 'bar' => 23),
            array('baz' => 23, 'foobar' => 42)
        );

        $log = new PHP_Depend_Log_Summary_Xml();
        $log->setLogFile($this->resultFile);
        $log->setCode($this->packages);
        $log->log($analyzer);

        $log->close();

        $fileName = 'node-and-project-aware-result-set.xml';
        $this->assertXmlStringEqualsXmlString(
            $this->getNormalizedPathXml(dirname(__FILE__) . "/_expected/{$fileName}"),
            $this->getNormalizedPathXml($this->resultFile)
        );
    }

    /**
     * testNodeAwareAnalyzer
     *
     * @return void
     * @covers PHP_Depend_Log_Summary_Xml
     * @group pdepend
     * @group pdepend::log
     * @group pdepend::log::summary
     * @group unittest
     */
    public function testNodeAwareAnalyzer()
    {
        $input = array(
            array('loc'  =>  42),  array('ncloc'  =>  23),
            array('loc'  =>  9),   array('ncloc'  =>  7),
            array('loc'  =>  101), array('ncloc'  =>  99),
            array('loc'  =>  90),  array('ncloc'  =>  80),
            array('loc'  =>  50),  array('ncloc'  =>  45),
            array('loc'  =>  30),  array('ncloc'  =>  22),
            array('loc'  =>  9),   array('ncloc'  =>  9),
            array('loc'  =>  3),   array('ncloc'  =>  3),
            array('loc'  =>  42),  array('ncloc'  =>  23),
            array('loc'  =>  33),  array('ncloc'  =>  20),
            array('loc'  =>  9),   array('ncloc'  =>  7),
        );

        $metricsOne = array();
        $metricsTwo = array();
        foreach ($this->packages as $package) {
            $metricsOne[$package->getUUID()] = array_shift($input);
            $metricsTwo[$package->getUUID()] = array_shift($input);
            foreach ($package->getClasses() as $class) {
                $metricsOne[$class->getUUID()] = array_shift($input);
                $metricsTwo[$class->getUUID()] = array_shift($input);
                foreach ($class->getMethods() as $method) {
                    $metricsOne[$method->getUUID()] = array_shift($input);
                    $metricsTwo[$method->getUUID()] = array_shift($input);
                }
            }
            foreach ($package->getFunctions() as $function) {
                $metricsOne[$function->getUUID()] = array_shift($input);
                $metricsTwo[$function->getUUID()] = array_shift($input);
            }
        }

        $resultOne = new PHP_Depend_Log_Summary_AnalyzerNodeAwareDummy($metricsOne);
        $resultTwo = new PHP_Depend_Log_Summary_AnalyzerNodeAwareDummy($metricsTwo);

        $log = new PHP_Depend_Log_Summary_Xml();
        $log->setLogFile($this->resultFile);
        $log->setCode($this->packages);
        $log->log($resultOne);
        $log->log($resultTwo);

        $log->close();

        $fileName = 'node-aware-result-set.xml';
        $this->assertXmlStringEqualsXmlString(
            $this->getNormalizedPathXml(dirname(__FILE__) . "/_expected/{$fileName}"),
            $this->getNormalizedPathXml($this->resultFile)
        );
    }

    protected function getNormalizedPathXml($fileName)
    {
        $dom                     = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->load($fileName);

        // Adjust file path
        foreach ($dom->getElementsByTagName('file') as $file) {
            $file->setAttribute('name', $this->testFileName);
        }
        $dom->documentElement->setAttribute('generated', '2010-02-22T08:26:51');

        return $dom->saveXML();
    }
}
