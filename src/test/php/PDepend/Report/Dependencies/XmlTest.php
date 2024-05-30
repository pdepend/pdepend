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

use PDepend\AbstractTestCase;
use PDepend\Metrics\Analyzer\ClassDependencyAnalyzer;
use PDepend\Metrics\AnalyzerNodeAware;
use PDepend\Report\NoLogOutputException;
use PDepend\Source\AST\AbstractASTClassOrInterface;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTNamespace;

/**
 * Test case for the xml summary log.
 *
 * @covers \PDepend\Report\Dependencies\Xml
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

        $this->resultFile = self::createRunResourceURI('log-summary.xml');
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
            'pdepend.analyzer.class_dependency',
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
            "The log target is not configured for 'PDepend\\Report\\Dependencies\\Xml'."
        );

        $logger = new Xml();
        $logger->close();
    }

    /**
     * testLogMethodReturnsFalseForWrongAnalyzer
     */
    public function testLogMethodReturnsFalseForWrongAnalyzer(): void
    {
        $analyzer = $this->getMockBuilder(AnalyzerNodeAware::class)
            ->getMock();

        $logger = new Xml();
        $actual = $logger->log($analyzer);

        static::assertFalse($actual);
    }

    /**
     * testLogMethodReturnsTrueForAnalyzerOfTypeClassDepenendecyAnalyzer
     */
    public function testLogMethodReturnsTrueForAnalyzerOfTypeClassDepenendecyAnalyzer(): void
    {
        $analyzer = $this->getMockBuilder(ClassDependencyAnalyzer::class)
            ->getMock();

        $logger = new Xml();
        $actual = $logger->log($analyzer);

        static::assertTrue($actual);
    }

    /**
     * Tests that {@link \PDepend\Report\Dependencies\Xml::write()} generates the
     * expected document structure for the source, but without any applied
     * metrics.
     */
    public function testXmlLogWithoutMetrics(): void
    {
        $this->namespaces = self::parseCodeResourceForTest();

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
     * Tests that {@link \PDepend\Report\Dependencies\Xml::write()} generates the
     * expected document structure for the source, with applied metrics.
     */
    public function testXmlLogWithMetrics(): void
    {
        $this->namespaces = self::parseCodeResourceForTest();

        $type = $this->getMockBuilder(AbstractASTClassOrInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $type
            ->expects(static::any())
            ->method('getImage')
            ->will(static::returnValue('class'));
        $type
            ->expects(static::any())
            ->method('getNamespaceName')
            ->will(static::returnValue('namespace'));

        $analyzer = $this->getMockBuilder(ClassDependencyAnalyzer::class)
            ->getMock();
        $analyzer
            ->expects(static::any())
            ->method('getEfferents')
            ->will(static::returnValue([$type]));
        $analyzer
            ->expects(static::any())
            ->method('getAfferents')
            ->will(static::returnValue([$type, $type]));

        $log = new Xml();
        $log->log($analyzer);
        $log->setLogFile($this->resultFile);
        $log->setArtifacts($this->namespaces);
        $log->close();

        $fileName = 'xml-log-with-metrics.xml';
        static::assertXmlStringEqualsXmlString(
            $this->getNormalizedPathXml(__DIR__ . "/_expected/{$fileName}"),
            $this->getNormalizedPathXml($this->resultFile)
        );
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
