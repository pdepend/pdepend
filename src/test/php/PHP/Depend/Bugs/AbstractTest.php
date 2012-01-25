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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Bugs
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Abstract test case for the "Bugs" package.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Bugs
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
abstract class PHP_Depend_Bugs_AbstractTest extends PHP_Depend_AbstractTest
{
    /**
     * Creates the PDepend summary report for the source associated with the
     * calling test case.
     *
     * @return string
     * @since 0.10.0
     */
    protected function createSummaryXmlForCallingTest()
    {
        $this->changeWorkingDirectory(
            $this->createCodeResourceURI('config/')
        );

        $file = self::createRunResourceURI('summary.xml');

        $log = new PHP_Depend_Log_Summary_Xml();
        $log->setLogFile($file);

        $pdepend = $this->createPDependFixture();
        $pdepend->addFile(self::createCodeResourceUriForTest());
        $pdepend->addLogger($log);
        $pdepend->analyze();

        return $file;
    }

    /**
     * Parses the source of a test case file.
     *
     * @param string  $testCase          Full test case name.
     * @param boolean $ignoreAnnotations Ignore annotations?
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public static function parseTestCaseSource($testCase, $ignoreAnnotations = false)
    {
        return self::parseSource(
            self::getSourceFileForTestCase($testCase), $ignoreAnnotations
        );
    }

    /**
     * Returns the source file for the given test case.
     *
     * @param string $testCase The qualified test case name.
     *
     * @return string
     */
    protected static function getSourceFileForTestCase($testCase)
    {
        list($class, $method) = explode('::', $testCase);

        preg_match('(Bug(\d+)Test$)', $class, $match);

        return self::createCodeResourceURI(
            sprintf('bugs/%s/%s.php', $match[1], $method)
        );
    }
}
