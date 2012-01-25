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

/**
 * Test case for the jdepend xml logger.
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
 * @covers PHP_Depend_Log_Jdepend_Xml
 * @group pdepend
 * @group pdepend::log
 * @group pdepend::log::jdepend
 * @group unittest
 */
class PHP_Depend_Log_Jdepend_XmlTest extends PHP_Depend_AbstractTest
{
    /**
     * Test code structure.
     *
     * @var PHP_Depend_Code_NodeIterator $packages
     */
    protected $packages = null;

    /**
     * Test dependency analyzer.
     *
     * @var PHP_Depend_Metrics_Dependency_Analyzer $analyzer
     */
    protected $analyzer = null;

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

        $this->resultFile = self::createRunResourceURI('pdepend-log.xml');
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
     */
    public function testReturnsExceptedAnalyzers()
    {
        $logger    = new PHP_Depend_Log_Jdepend_Xml();
        $actual    = $logger->getAcceptedAnalyzers();
        $exptected = array('PHP_Depend_Metrics_Dependency_Analyzer');

        $this->assertEquals($exptected, $actual);
    }

    /**
     * Tests that the logger throws an exception if the log target wasn't
     * configured.
     *
     * @return void
     */
    public function testThrowsExceptionForInvalidLogTarget()
    {
        $this->setExpectedException(
            'PHP_Depend_Log_NoLogOutputException',
            "The log target is not configured for 'PHP_Depend_Log_Jdepend_Xml'."
        );

        $logger = new PHP_Depend_Log_Jdepend_Xml();
        $logger->close();
    }

    /**
     * Tests that {@link PHP_Depend_Log_Summary_Xml::write()} generates the
     * expected document structure for the source, but without any applied
     * metrics.
     *
     * @return void
     */
    public function testXmlLogWithoutMetrics()
    {
        $this->packages = self::parseCodeResourceForTest();

        $this->analyzer = new PHP_Depend_Metrics_Dependency_Analyzer();
        $this->analyzer->analyze($this->packages);

        $log = new PHP_Depend_Log_Jdepend_Xml();
        $log->setLogFile($this->resultFile);
        $log->setCode($this->packages);
        $log->log($this->analyzer);
        $log->close();

        $fileName = 'pdepend-log' . CORE_PACKAGE . '.xml';
        $this->assertXmlStringEqualsXmlString(
            $this->getNormalizedPathXml(dirname(__FILE__) . "/_expected/{$fileName}"),
            file_get_contents($this->resultFile)
        );
    }

    /**
     * testXmlLogAcceptsOnlyTheCorrectAnalyzer
     *
     * @return void
     */
    public function testXmlLogAcceptsOnlyTheCorrectAnalyzer()
    {
        $logger = new PHP_Depend_Log_Jdepend_Xml();

        $this->assertFalse($logger->log(new PHP_Depend_Log_DummyAnalyzer()));
        $this->assertTrue($logger->log(new PHP_Depend_Metrics_Dependency_Analyzer()));
    }

    /**
     * Normalizes the file references within the expected result document.
     *
     * @param string $fileName File name of the expected result document.
     *
     * @return string The prepared xml document
     */
    protected function getNormalizedPathXml($fileName)
    {
        $path = self::createCodeResourceUriForTest();

        return preg_replace(
            '(sourceFile="[^"]+/([^/"]+)")',
            'sourceFile="' . $path . '/\\1"',
             file_get_contents($fileName)
        );
    }

}
