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

namespace PDepend\Report\Dependencies;

use PDepend\AbstractTest;
use PDepend\Source\AST\ASTArtifactList;

/**
 * Test case for the xml summary log.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Report\Dependencies\Xml
 * @group unittest
 */
class XmlTest extends AbstractTest
{
    /**
     * Test code structure.
     *
     * @var \PDepend\Source\AST\ASTNamespace[]
     */
    protected $namespaces = null;

    /**
     * The temporary file name for the logger result.
     *
     * @var string
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
     */
    public function testReturnsExceptedAnalyzers()
    {
        $logger    = new Xml();
        $actual    = $logger->getAcceptedAnalyzers();
        $expected = array(
            'pdepend.analyzer.class_dependency',
        );

        $this->assertEquals($expected, $actual);
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
            '\\PDepend\\Report\\NoLogOutputException',
            "The log target is not configured for 'PDepend\\Report\\Dependencies\\Xml'."
        );

        $logger = new Xml();
        $logger->close();
    }

    /**
     * testLogMethodReturnsFalseForWrongAnalyzer
     *
     * @return void
     */
    public function testLogMethodReturnsFalseForWrongAnalyzer()
    {
        $analyzer = $this->getMockBuilder('\\PDepend\\Metrics\\AnalyzerNodeAware')
            ->getMock();

        $logger = new Xml();
        $actual = $logger->log($analyzer);

        $this->assertFalse($actual);
    }

    /**
     * testLogMethodReturnsTrueForAnalyzerOfTypeClassDepenendecyAnalyzer
     *
     * @return void
     */
    public function testLogMethodReturnsTrueForAnalyzerOfTypeClassDepenendecyAnalyzer()
    {
        $analyzer = $this->getMockBuilder('\\PDepend\\Metrics\\Analyzer\\ClassDependencyAnalyzer')
            ->getMock();

        $logger = new Xml();
        $actual = $logger->log($analyzer);

        $this->assertTrue($actual);
    }

    /**
     * Tests that {@link \PDepend\Report\Dependencies\Xml::write()} generates the
     * expected document structure for the source, but without any applied
     * metrics.
     *
     * @return void
     */
    public function testXmlLogWithoutMetrics()
    {
        $this->namespaces = self::parseCodeResourceForTest();

        $log = new Xml();
        $log->setLogFile($this->resultFile);
        $log->setArtifacts($this->namespaces);
        $log->close();

        $fileName = 'xml-log-without-metrics.xml';
        $this->assertXmlStringEqualsXmlString(
            $this->getNormalizedPathXml(__DIR__ . "/_expected/{$fileName}"),
            $this->getNormalizedPathXml($this->resultFile)
        );
    }

    /**
     * Tests that {@link \PDepend\Report\Dependencies\Xml::write()} generates the
     * expected document structure for the source, with applied metrics.
     *
     * @return void
     */
    public function testXmlLogWithMetrics()
    {
        $this->namespaces = self::parseCodeResourceForTest();

        $type = $this->getMockBuilder('\\PDepend\\Source\\AST\\AbstractASTClassOrInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $type
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('class'));
        $type
            ->expects($this->any())
            ->method('getNamespaceName')
            ->will($this->returnValue('namespace'));

        $analyzer = $this->getMockBuilder('\\PDepend\\Metrics\\Analyzer\\ClassDependencyAnalyzer')
            ->getMock();
        $analyzer
            ->expects($this->any())
            ->method('getEfferents')
            ->will($this->returnValue(array($type)));
        $analyzer
            ->expects($this->any())
            ->method('getAfferents')
            ->will($this->returnValue(array($type, $type)));

        $log = new Xml();
        $log->log($analyzer);
        $log->setLogFile($this->resultFile);
        $log->setArtifacts($this->namespaces);
        $log->close();

        $fileName = 'xml-log-with-metrics.xml';
        $this->assertXmlStringEqualsXmlString(
            $this->getNormalizedPathXml(__DIR__ . "/_expected/{$fileName}"),
            $this->getNormalizedPathXml($this->resultFile)
        );
    }

    protected function getNormalizedPathXml($fileName)
    {
        return preg_replace(
            array('(file\s+name="[^"]+")', '(generated="[^"]*")'),
            array('file name="' . __FILE__ . '"', 'generated=""'),
            file_get_contents($fileName)
        );
    }
}
