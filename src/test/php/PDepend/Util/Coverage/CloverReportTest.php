<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Util\Coverage;

use PDepend\AbstractTest;

/**
 * Test case for the {@link \PDepend\Util\Coverage\CloverReport} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Util\Coverage\CloverReport
 * @group unittest
 */
class CloverReportTest extends AbstractTest
{
    /**
     * testReportReturnsExpected0PercentCoverage
     *
     * @return void
     */
    public function testReportReturnsExpected0PercentCoverage()
    {
        $report   = $this->_createCloverReport();
        $coverage = $report->getCoverage($this->_createMethodMock(__FUNCTION__));

        $this->assertEquals(0, $coverage);
    }

    /**
     * testReportReturnsExpected50PercentCoverage
     *
     * @return void
     */
    public function testReportReturnsExpected50PercentCoverage()
    {
        $report   = $this->_createCloverReport();
        $coverage = $report->getCoverage($this->_createMethodMock(__FUNCTION__));

        $this->assertEquals(50, $coverage);
    }

    /**
     * testReportReturnsExpected100PercentCoverage
     *
     * @return void
     */
    public function testReportReturnsExpected100PercentCoverage()
    {
        $report   = $this->_createCloverReport();
        $coverage = $report->getCoverage($this->_createMethodMock(__FUNCTION__));

        $this->assertEquals(100, $coverage);
    }

    /**
     * testNamespacedReportReturnsExpected0PercentCoverage
     *
     * @return void
     */
    public function testNamespacedReportReturnsExpected0PercentCoverage()
    {
        $report   = $this->_createNamespacedCloverReport();
        $coverage = $report->getCoverage($this->_createMethodMock(__FUNCTION__));

        $this->assertEquals(0, $coverage);
    }

    /**
     * testNamespacedReportReturnsExpected50PercentCoverage
     *
     * @return void
     */
    public function testNamespacedReportReturnsExpected50PercentCoverage()
    {
        $report   = $this->_createNamespacedCloverReport();
        $coverage = $report->getCoverage($this->_createMethodMock(__FUNCTION__));

        $this->assertEquals(50, $coverage);
    }

    /**
     * testNamespacedReportReturnsExpected100PercentCoverage
     *
     * @return void
     */
    public function testNamespacedReportReturnsExpected100PercentCoverage()
    {
        $report   = $this->_createNamespacedCloverReport();
        $coverage = $report->getCoverage($this->_createMethodMock(__FUNCTION__));

        $this->assertEquals(100, $coverage);
    }

    /**
     * testGetCoverageReturnsZeroCoverageWhenNoMatchingEntryExists
     *
     * @return void
     */
    public function testGetCoverageReturnsZeroCoverageWhenNoMatchingEntryExists()
    {
        $report   = $this->_createCloverReport();
        $coverage = $report->getCoverage($this->_createMethodMock(__FUNCTION__));

        $this->assertEquals(0, $coverage);
    }

    /**
     * Creates a clover coverage report instance.
     *
     * @return \PDepend\Util\Coverage\CloverReport
     */
    private function _createCloverReport()
    {
        $sxml = simplexml_load_file(dirname(__FILE__) . '/_files/clover.xml');
        return new CloverReport($sxml);
    }

    /**
     * Creates a clover coverage report instance.
     *
     * @return \PDepend\Util\Coverage\CloverReport
     */
    private function _createNamespacedCloverReport()
    {
        $sxml = simplexml_load_file(dirname(__FILE__) . '/_files/clover-namespaced.xml');
        return new CloverReport($sxml);
    }

    /**
     * Creates a mocked method instance.
     *
     * @param string $name Name of the mock method.
     * @return \PDepend\Source\AST\ASTMethod
     */
    private function _createMethodMock($name)
    {
        $file = $this->getMock('\\PDepend\\Source\\AST\\ASTCompilationUnit', array(), array(null));
        $file->expects($this->any())
            ->method('getFileName')
            ->will($this->returnValue('/' . $name . '.php'));

        $method = $this->getMock('\\PDepend\\Source\\AST\\ASTMethod', array(), array($name));
        $method->expects($this->once())
            ->method('getCompilationUnit')
            ->will($this->returnValue($file));
        $method->expects($this->once())
            ->method('getStartLine')
            ->will($this->returnValue(1));
        $method->expects($this->once())
            ->method('getEndLine')
            ->will($this->returnValue(4));

        return $method;
    }
}
