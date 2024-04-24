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

namespace PDepend\Metrics;

use PDepend\AbstractTest;
use PDepend\Report\DummyAnalyzer;

/**
 * Test case for the {@link \PDepend\Metrics\AnalyzerIterator} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Metrics\AnalyzerIterator
 * @group unittest
 */
class AnalyzerIteratorTest extends AbstractTest
{
    /**
     * testIteratorReturnsEnabledAnalyzerInstances
     *
     * @return void
     */
    public function testIteratorReturnsEnabledAnalyzerInstances()
    {
        $analyzer = $this->getMockBuilder(DummyAnalyzer::class)
            ->getMock();
        $analyzer->expects($this->exactly(2))
            ->method('isEnabled')
            ->will($this->returnValue(true));

        $iterator = new AnalyzerIterator(array($analyzer, $analyzer));
        $this->assertEquals(2, iterator_count($iterator));
    }

    /**
     * testIteratorDoesNotReturnDisabledAnalyzerInstances
     *
     * @return void
     */
    public function testIteratorDoesNotReturnDisabledAnalyzerInstances()
    {
        $analyzer = $this->createMock(DummyAnalyzer::class);
        $analyzer->method('isEnabled')
            ->willReturnOnConsecutiveCalls(true, false);

        $iterator = new AnalyzerIterator([$analyzer, $analyzer]);
        $this->assertCount(1, $iterator);
    }
}
