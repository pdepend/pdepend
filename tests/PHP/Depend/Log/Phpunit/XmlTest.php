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

require_once 'PHP/Depend/Log/Phpunit/Xml.php';
require_once 'PHP/Depend/Metrics/ClassLevel/Analyzer.php';
require_once 'PHP/Depend/Metrics/CodeRank/Analyzer.php';
require_once 'PHP/Depend/Metrics/Coupling/Analyzer.php';
require_once 'PHP/Depend/Metrics/CyclomaticComplexity/Analyzer.php';
require_once 'PHP/Depend/Metrics/Hierarchy/Analyzer.php';
require_once 'PHP/Depend/Metrics/Inheritance/Analyzer.php';
require_once 'PHP/Depend/Metrics/NodeCount/Analyzer.php';
require_once 'PHP/Depend/Metrics/NodeLoc/Analyzer.php';

/**
 * Test case for the phpunit logger.
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
class PHP_Depend_Log_Phpunit_XmlTest extends PHP_Depend_AbstractTest
{
    /**
     * The temp log file.
     *
     * @var string $_tempFile
     */
    private $_tempFile = null;

    /**
     * Creates the temp log file name.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_tempFile = self::createRunResourceURI('pdepend.phpunit-log.xml');
        if (file_exists($this->_tempFile)) {
            unlink($this->_tempFile);
        }
    }

    /**
     * Removes the temp log file
     *
     */
    protected function tearDown()
    {
        if (file_exists($this->_tempFile)) {
            unlink($this->_tempFile);
        }

        parent::tearDown();
    }

    /**
     * Tests that the logger returns the expected set of analyzers.
     *
     * @return void
     * @covers PHP_Depend_Log_Phpunit_Xml
     * @group pdepend
     * @group pdepend::log
     * @group pdepend::log::phpunit
     * @group unittest
     */
    public function testReturnsExceptedAnalyzers()
    {
        $logger    = new PHP_Depend_Log_Phpunit_Xml(__FILE__);
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
     * @covers PHP_Depend_Log_Phpunit_Xml
     * @group pdepend
     * @group pdepend::log
     * @group pdepend::log::phpunit
     * @group unittest
     */
    public function testThrowsExceptionForInvalidLogTarget()
    {
        $this->setExpectedException(
            'PHP_Depend_Log_NoLogOutputException',
            "The log target is not configured for 'PHP_Depend_Log_Phpunit_Xml'."
        );

        $logger = new PHP_Depend_Log_Phpunit_Xml();
        $logger->close();
    }

    /**
     * Tests the result of the phpunit logger with some real analyzers.
     *
     * @return void
     * @covers PHP_Depend_Log_Phpunit_Xml
     * @group pdepend
     * @group pdepend::logs
     * @group pdepend::logs::summary
     * @group unittest
     */
    public function testPHPUnitLoggerResult()
    {
        $packages = self::parseTestCaseSource(__METHOD__);

        $logger = new PHP_Depend_Log_Phpunit_Xml();
        $logger->setLogFile($this->_tempFile);
        $logger->setCode($packages);

        $analyzer0 = new PHP_Depend_Metrics_CyclomaticComplexity_Analyzer();
        $analyzer0->analyze($packages);

        $analyzer1 = new PHP_Depend_Metrics_ClassLevel_Analyzer();
        $analyzer1->addAnalyzer($analyzer0);
        $analyzer1->analyze($packages);

        $analyzer2 = new PHP_Depend_Metrics_CodeRank_Analyzer();
        $analyzer2->analyze($packages);

        $analyzer3 = new PHP_Depend_Metrics_Coupling_Analyzer();
        $analyzer3->analyze($packages);

        $analyzer4 = new PHP_Depend_Metrics_Hierarchy_Analyzer();
        $analyzer4->analyze($packages);

        $analyzer5 = new PHP_Depend_Metrics_Inheritance_Analyzer();
        $analyzer5->analyze($packages);

        $analyzer6 = new PHP_Depend_Metrics_NodeCount_Analyzer();
        $analyzer6->analyze($packages);

        $analyzer7 = new PHP_Depend_Metrics_NodeLoc_Analyzer();
        $analyzer7->analyze($packages);

        $logger->log($analyzer0);
        $logger->log($analyzer1);
        $logger->log($analyzer2);
        $logger->log($analyzer3);
        $logger->log($analyzer4);
        $logger->log($analyzer5);
        $logger->log($analyzer6);
        $logger->log($analyzer7);

        $logger->close();

        $actual   = file_get_contents($this->_tempFile);
        $expected = $this->_loadExpected('phpunit-log.xml');

        $this->assertXmlStringEqualsXmlString($expected, $actual);
    }

    /**
     * testPHPUnitLogNotContainsNotUserDefinedClasses
     *
     * @return void
     * @covers PHP_Depend_Log_Phpunit_Xml
     * @group pdepend
     * @group pdepend::logs
     * @group pdepend::logs::phpunit
     * @group unittest
     */
    public function testPHPUnitLogNotContainsNotUserDefinedClasses()
    {
        $logger = new PHP_Depend_Log_Phpunit_Xml();
        $logger->setLogFile($this->_tempFile);
        $logger->setCode(self::parseTestCaseSource(__METHOD__));
        $logger->close();

        $sxml = simplexml_load_file($this->_tempFile);
        $this->assertEquals(array(), $sxml->xpath('//class[@name="' . __FUNCTION__ . 'Parent"]'));
    }

    /**
     * Loads the expected log file and adjusts the file@name attribute.
     *
     * @param string $file The log file name.
     *
     * @return string
     */
    private function _loadExpected($file)
    {
        $path = realpath(dirname(__FILE__) . '/../../_code');

        $dom = new DOMDocument('1.0', 'UTF-8');

        $dom->formatOutput       = true;
        $dom->preserveWhiteSpace = false;

        $dom->load(dirname(__FILE__) . "/_expected/{$file}");
        foreach ($dom->getElementsByTagName('file') as $fileXml) {
            $name = $fileXml->getAttribute('name');
            $name = substr($name, strrpos($name, '_code') + 5);

            $fileXml->setAttribute('name', realpath("{$path}{$name}"));
        }
        return $dom->saveXML();
    }
}