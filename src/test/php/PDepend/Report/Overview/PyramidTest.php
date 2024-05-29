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

namespace PDepend\Report\Overview;

use DOMDocument;
use DOMElement;
use PDepend\AbstractTestCase;
use PDepend\Metrics\Analyzer\CouplingAnalyzer;
use PDepend\Metrics\Analyzer\CyclomaticComplexityAnalyzer;
use PDepend\Metrics\Analyzer\InheritanceAnalyzer;
use PDepend\Metrics\Analyzer\NodeCountAnalyzer;
use PDepend\Metrics\Analyzer\NodeLocAnalyzer;
use PDepend\Report\DummyAnalyzer;
use PDepend\Report\NoLogOutputException;

/**
 * Test case for the overview pyramid logger.
 *
 * @covers \PDepend\Report\Overview\Pyramid
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class PyramidTest extends AbstractTestCase
{
    /**
     * Tests that the logger returns the expected set of analyzers.
     */
    public function testReturnsExceptedAnalyzers(): void
    {
        $logger = new Pyramid();
        $actual = $logger->getAcceptedAnalyzers();
        $exptected = [
            'pdepend.analyzer.coupling',
            'pdepend.analyzer.cyclomatic_complexity',
            'pdepend.analyzer.inheritance',
            'pdepend.analyzer.node_count',
            'pdepend.analyzer.node_loc',
        ];

        static::assertEquals($exptected, $actual);
    }

    /**
     * Tests that the logger throws an exception if the log target wasn't
     * configured.
     *
     * @covers \PDepend\Report\NoLogOutputException
     */
    public function testThrowsExceptionForInvalidLogTarget(): void
    {
        $this->expectException(
            NoLogOutputException::class
        );
        $this->expectExceptionMessage(
            "The log target is not configured for 'PDepend\\Report\\Overview\\Pyramid'."
        );

        $logger = new Pyramid();
        $logger->close();
    }

    /**
     * Tests that the log method returns <b>false</b> for an invalid logger.
     */
    public function testPyramidDoesntAcceptInvalidAnalyzer(): void
    {
        $logger = new Pyramid();
        static::assertFalse($logger->log(new DummyAnalyzer()));
    }

    /**
     * Tests that the logger checks for the required analyzer.
     */
    public function testCloseThrowsAnExceptionIfNoCouplingAnalyzerWasSet(): void
    {
        $this->expectException(
            '\RuntimeException'
        );
        $this->expectExceptionMessage(
            'Missing Coupling analyzer.'
        );

        $log = new Pyramid();
        $log->setLogFile($this->createRunResourceURI('_tmp_.svg'));
        $log->log($this->createComplexityAnalyzer());
        $log->log($this->createInheritanceAnalyzer());
        $log->log($this->createNodeCountAnalyzer());
        $log->log($this->createNodeLocAnalyzer());
        $log->close();
    }

    /**
     * Tests that the logger checks for the required analyzer.
     */
    public function testCloseThrowsAnExceptionIfNoCyclomaticComplexityAnalyzerWasSet(): void
    {
        $this->expectException(
            '\RuntimeException'
        );
        $this->expectExceptionMessage(
            'Missing Cyclomatic Complexity analyzer.'
        );

        $log = new Pyramid();
        $log->setLogFile($this->createRunResourceURI('_tmp_.svg'));
        $log->log($this->createCouplingAnalyzer());
        $log->log($this->createInheritanceAnalyzer());
        $log->log($this->createNodeCountAnalyzer());
        $log->log($this->createNodeLocAnalyzer());
        $log->close();
    }

    /**
     * Tests that the logger checks for the required analyzer.
     */
    public function testCloseThrowsAnExceptionIfNoInheritanceAnalyzerWasSet(): void
    {
        $this->expectException(
            '\RuntimeException'
        );
        $this->expectExceptionMessage(
            'Missing Inheritance analyzer.'
        );

        $log = new Pyramid();
        $log->setLogFile($this->createRunResourceURI('_tmp_.svg'));
        $log->log($this->createCouplingAnalyzer());
        $log->log($this->createComplexityAnalyzer());
        $log->log($this->createNodeCountAnalyzer());
        $log->log($this->createNodeLocAnalyzer());
        $log->close();
    }

    /**
     * Tests that the logger checks for the required analyzer.
     */
    public function testCloseThrowsAnExceptionIfNoNodeCountAnalyzerWasSet(): void
    {
        $this->expectException(
            '\RuntimeException'
        );
        $this->expectExceptionMessage(
            'Missing Node Count analyzer.'
        );

        $log = new Pyramid();
        $log->setLogFile($this->createRunResourceURI('_tmp_.svg'));
        $log->log($this->createCouplingAnalyzer());
        $log->log($this->createComplexityAnalyzer());
        $log->log($this->createInheritanceAnalyzer());
        $log->log($this->createNodeLocAnalyzer());
        $log->close();
    }

    /**
     * Tests that the logger checks for the required analyzer.
     */
    public function testCloseThrowsAnExceptionIfNoNodeLOCAnalyzerWasSet(): void
    {
        $this->expectException(
            '\RuntimeException'
        );
        $this->expectExceptionMessage(
            'Missing Node LOC analyzer.'
        );

        $log = new Pyramid();
        $log->setLogFile($this->createRunResourceURI('_tmp_.svg'));
        $log->log($this->createCouplingAnalyzer());
        $log->log($this->createComplexityAnalyzer());
        $log->log($this->createInheritanceAnalyzer());
        $log->log($this->createNodeCountAnalyzer());
        $log->close();
    }

    /**
     * testCollectedAndComputedValuesInOutputSVG
     */
    public function testCollectedAndComputedValuesInOutputSVG(): void
    {
        $output = $this->createRunResourceURI('temp') . '.svg';
        if (file_exists($output)) {
            unlink($output);
        }

        $log = new Pyramid();
        $log->setLogFile($output);
        $log->log($this->createCouplingAnalyzer());
        $log->log($this->createComplexityAnalyzer());
        $log->log($this->createInheritanceAnalyzer());
        $log->log($this->createNodeCountAnalyzer());
        $log->log($this->createNodeLocAnalyzer());
        $log->close();

        static::assertFileExists($output);

        $expected = [
            'cyclo' => 5579,
            'loc' => 35175,
            'nom' => 3618,
            'noc' => 384,
            'nop' => 19,
            'andc' => 0.31,
            'ahh' => 0.12,
            'calls' => 15128,
            'fanout' => 8590,
            'cyclo-loc' => 0.15,
            'loc-nom' => 9.72,
            'nom-noc' => 9.42,
            'noc-nop' => 20.21,
            'fanout-calls' => 0.56,
            'calls-nom' => 4.18,
        ];

        $svg = new DOMDocument();
        $svg->load($output, LIBXML_NOWARNING);

        // TODO: Replace this loop assertion
        foreach ($expected as $name => $value) {
            $elem = $svg->getElementById("pdepend.{$name}");
            static::assertInstanceOf(DOMElement::class, $elem);
            static::assertEqualsWithDelta($value, $elem->nodeValue, 0.01);
        }

        unlink($output);
    }

    private function createCouplingAnalyzer(): CouplingAnalyzer
    {
        $mock = $this->getMockBuilder(CouplingAnalyzer::class)
            ->getMock();
        $mock->expects(static::any())
            ->method('getProjectMetrics')
            ->will(static::returnValue(
                [
                    'fanout' => 8590,
                    'calls' => 15128,
                ]
            ));

        return $mock;
    }

    private function createComplexityAnalyzer(): CyclomaticComplexityAnalyzer
    {
        $mock = $this->getMockBuilder(CyclomaticComplexityAnalyzer::class)
            ->getMock();
        $mock->expects(static::any())
            ->method('getProjectMetrics')
            ->will(static::returnValue(
                [
                    'ccn2' => 5579,
                ]
            ));

        return $mock;
    }

    private function createInheritanceAnalyzer(): InheritanceAnalyzer
    {
        $mock = $this->getMockBuilder(InheritanceAnalyzer::class)
            ->getMock();
        $mock->expects(static::any())
            ->method('getProjectMetrics')
            ->will(static::returnValue(
                [
                    'andc' => 0.31,
                    'ahh' => 0.12,
                ]
            ));

        return $mock;
    }

    private function createNodeCountAnalyzer(): NodeCountAnalyzer
    {
        $mock = $this->getMockBuilder(NodeCountAnalyzer::class)
            ->getMock();
        $mock->expects(static::any())
            ->method('getProjectMetrics')
            ->will(static::returnValue(
                [
                    'nop' => 19,
                    'noc' => 384,
                    'nom' => 2018,
                    'nof' => 1600,
                ]
            ));

        return $mock;
    }

    private function createNodeLocAnalyzer(): NodeLocAnalyzer
    {
        $mock = $this->getMockBuilder(NodeLocAnalyzer::class)
            ->getMock();
        $mock->expects(static::any())
            ->method('getProjectMetrics')
            ->will(static::returnValue(
                [
                    'eloc' => 35175,
                ]
            ));

        return $mock;
    }
}
