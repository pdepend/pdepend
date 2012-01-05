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
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for the {@link PHP_Depend_Metrics_AnalyzerIterator} class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 *
 * @covers PHP_Depend_Metrics_AnalyzerIterator
 * @group pdepend
 * @group pdepend::metrics
 * @group unittest
 */
class PHP_Depend_Metrics_AnalyzerIteratorTest extends PHP_Depend_AbstractTest
{
    /**
     * testIteratorReturnsEnabledAnalyzerInstances
     *
     * @return void
     */
    public function testIteratorReturnsEnabledAnalyzerInstances()
    {
        $analyzer = $this->getMock('PHP_Depend_Metrics_AnalyzerI');
        $analyzer->expects($this->exactly(2))
            ->method('isEnabled')
            ->will($this->returnValue(true));

        $iterator = new PHP_Depend_Metrics_AnalyzerIterator(array($analyzer, $analyzer));
        self::assertEquals(2, iterator_count($iterator));
    }

    /**
     * testIteratorDoesNotReturnDisabledAnalyzerInstances
     *
     * @return void
     */
    public function testIteratorDoesNotReturnDisabledAnalyzerInstances()
    {
        $analyzer = $this->getMock('PHP_Depend_Metrics_AnalyzerI');
        $analyzer->expects($this->at(0))
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $analyzer->expects($this->at(1))
            ->method('isEnabled')
            ->will($this->returnValue(false));

        $iterator = new PHP_Depend_Metrics_AnalyzerIterator(array($analyzer, $analyzer));
        self::assertEquals(1, iterator_count($iterator));
    }
}
