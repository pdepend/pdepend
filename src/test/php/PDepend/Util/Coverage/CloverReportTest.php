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

namespace PDepend\Util\Coverage;

use PDepend\AbstractTestCase;
use PDepend\Source\AST\ASTCompilationUnit;
use PDepend\Source\AST\ASTMethod;
use SimpleXMLElement;

/**
 * Test case for the {@link \PDepend\Util\Coverage\CloverReport} class.
 *
 * @covers \PDepend\Util\Coverage\CloverReport
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class CloverReportTest extends AbstractTestCase
{
    /**
     * testReportReturnsExpected0PercentCoverage
     */
    public function testReportReturnsExpected0PercentCoverage(): void
    {
        $report = $this->createCloverReport();
        $coverage = $report->getCoverage($this->createMethodMock(__FUNCTION__));

        static::assertEquals(0, $coverage);
    }

    /**
     * testReportReturnsExpected50PercentCoverage
     */
    public function testReportReturnsExpected50PercentCoverage(): void
    {
        $report = $this->createCloverReport();
        $coverage = $report->getCoverage($this->createMethodMock(__FUNCTION__));

        static::assertEquals(50, $coverage);
    }

    /**
     * testReportReturnsExpected100PercentCoverage
     */
    public function testReportReturnsExpected100PercentCoverage(): void
    {
        $report = $this->createCloverReport();
        $coverage = $report->getCoverage($this->createMethodMock(__FUNCTION__));

        static::assertEquals(100, $coverage);
    }

    /**
     * testReportReturnsExpected100PercentCoverageWithCoverageIgnore
     */
    public function testReportReturnsExpected100PercentCoverageWithCoverageIgnore(): void
    {
        $report = $this->createCloverReport();
        $coverage = $report->getCoverage($this->createMethodMock(__FUNCTION__));

        static::assertEquals(100, $coverage);
    }

    /**
     * testReportReturnsExpected0PercentCoverageForOneLineMethod
     */
    public function testReportReturnsExpected0PercentCoverageForOneLineMethod(): void
    {
        $report = $this->createCloverReport();
        $coverage = $report->getCoverage($this->createMethodMock(__FUNCTION__));

        static::assertEquals(0, $coverage);
    }

    /**
     * testNamespacedReportReturnsExpected0PercentCoverage
     */
    public function testNamespacedReportReturnsExpected0PercentCoverage(): void
    {
        $report = $this->createNamespacedCloverReport();
        $coverage = $report->getCoverage($this->createMethodMock(__FUNCTION__));

        static::assertEquals(0, $coverage);
    }

    /**
     * testNamespacedReportReturnsExpected50PercentCoverage
     */
    public function testNamespacedReportReturnsExpected50PercentCoverage(): void
    {
        $report = $this->createNamespacedCloverReport();
        $coverage = $report->getCoverage($this->createMethodMock(__FUNCTION__));

        static::assertEquals(50, $coverage);
    }

    /**
     * testNamespacedReportReturnsExpected100PercentCoverage
     */
    public function testNamespacedReportReturnsExpected100PercentCoverage(): void
    {
        $report = $this->createNamespacedCloverReport();
        $coverage = $report->getCoverage($this->createMethodMock(__FUNCTION__));

        static::assertEquals(100, $coverage);
    }

    /**
     * testGetCoverageReturnsZeroCoverageWhenNoMatchingEntryExists
     */
    public function testGetCoverageReturnsZeroCoverageWhenNoMatchingEntryExists(): void
    {
        $report = $this->createCloverReport();
        $coverage = $report->getCoverage($this->createMethodMock(__FUNCTION__));

        static::assertEquals(0, $coverage);
    }

    /**
     * Creates a clover coverage report instance.
     */
    private function createCloverReport(): CloverReport
    {
        $sxml = simplexml_load_file(__DIR__ . '/_files/clover.xml');
        static::assertInstanceOf(SimpleXMLElement::class, $sxml);

        return new CloverReport($sxml);
    }

    /**
     * Creates a clover coverage report instance.
     */
    private function createNamespacedCloverReport(): CloverReport
    {
        $sxml = simplexml_load_file(__DIR__ . '/_files/clover-namespaced.xml');
        static::assertInstanceOf(SimpleXMLElement::class, $sxml);

        return new CloverReport($sxml);
    }

    /**
     * Creates a mocked method instance.
     *
     * @param string $name Name of the mock method.
     */
    private function createMethodMock(string $name, int $startLine = 1, int $endLine = 4): ASTMethod
    {
        $file = $this->getMockBuilder(ASTCompilationUnit::class)
            ->setConstructorArgs([null])
            ->getMock();
        $file->expects(static::any())
            ->method('getFileName')
            ->will(static::returnValue('/' . $name . '.php'));

        $method = $this->getMockBuilder(ASTMethod::class)
            ->setConstructorArgs([$name])
            ->getMock();
        $method->expects(static::once())
            ->method('getCompilationUnit')
            ->will(static::returnValue($file));
        $method->expects(static::once())
            ->method('getStartLine')
            ->will(static::returnValue($startLine));
        $method->expects(static::once())
            ->method('getEndLine')
            ->will(static::returnValue($endLine));

        return $method;
    }
}
