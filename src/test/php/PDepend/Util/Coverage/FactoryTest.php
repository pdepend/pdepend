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
 * Test case for the {@link \PDepend\Util\Coverage\Factory} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Util\Coverage\Factory
 * @group unittest
 */
class FactoryTest extends AbstractTest
{
    /**
     * testCreateReturnsCloverReportInstanceForCloverInputFile
     *
     * @return void
     */
    public function testCreateReturnsCloverReportInstanceForCloverInputFile()
    {
        $factory = new Factory();
        $report  = $factory->create(__DIR__ . '/_files/clover.xml');

        $this->assertInstanceOf('PDepend\\Util\\Coverage\\CloverReport', $report);
    }

    /**
     * testCreateMethodThrowsExceptionWhenFileDoesNotExist
     *
     * @return void
     * @expectedException \RuntimeException
     */
    public function testCreateMethodThrowsExceptionWhenFileDoesNotExist()
    {
        $factory = new Factory();
        $factory->create(__FUNCTION__);
    }

    /**
     * testCreateMethodThrowsExceptionWhenFileIsNotValidXml
     *
     * @return void
     * @expectedException \RuntimeException
     */
    public function testCreateMethodThrowsExceptionWhenFileIsNotValidXml()
    {
        $factory = new Factory();
        $factory->create(__FILE__);
    }

    /**
     * testCreateMethodThrowsExceptionForUnsupportedReportFormat
     *
     * @return void
     * @expectedException \RuntimeException
     */
    public function testCreateMethodThrowsExceptionForUnsupportedReportFormat()
    {
        $factory = new Factory();
        $factory->create(dirname(__FILE__) . '/_files/fail.xml');
    }
}
