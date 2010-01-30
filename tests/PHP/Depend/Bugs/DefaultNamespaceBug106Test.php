<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2010, Manuel Pichler <mapi@pdepend.org>.
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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Bugs
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

require_once 'PHP/Depend.php';
require_once 'PHP/Depend/Log/Summary/Xml.php';
require_once 'PHP/Depend/Util/Type.php';

/**
 * Test case for ticket #106, where internal classes appear in the metrics log
 * file.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Bugs
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Bugs_DefaultNamespaceBug106Test extends PHP_Depend_Bugs_AbstractTest
{
    /**
     * testAllocatedInternalClassWithLeadingBackslashNotAppearsInSummaryLogFile
     *
     * @return void
     * @covers stdClass
     * @group pdepend
     * @group pdepend::bugs
     * @group regressiontest
     */
    public function testAllocatedInternalClassWithLeadingBackslashNotAppearsInSummaryLogFile()
    {
        $this->_runForTestCase(__METHOD__);
    }

    /**
     * testExtendedInternalClassWithLeadingBackslashNotAppearsInSummaryLogFile
     *
     * @return void
     * @covers stdClass
     * @group pdepend
     * @group pdepend::bugs
     * @group regressiontest
     */
    public function testExtendedInternalClassWithLeadingBackslashNotAppearsInSummaryLogFile()
    {
        $this->_runForTestCase(__METHOD__);
    }

    /**
     * Runs PHP_Depend with the summary logger, against a source file associated
     * with the given test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return void
     */
    private function _runForTestCase($testCase)
    {
        $uri = $this->createRunResourceURI(__FUNCTION__);

        $logger = new PHP_Depend_Log_Summary_Xml();
        $logger->setLogFile($uri);

        $pdepend = new PHP_Depend();
        $pdepend->addFile(self::getSourceFileForTestCase($testCase));
        $pdepend->addLogger($logger);
        $pdepend->analyze();

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->load($uri);

        $xpath = new DOMXPath($dom);
        $result = $xpath->query('//class[@name="RuntimeException"]');

        $this->assertEquals(0, $result->length);
    }
}