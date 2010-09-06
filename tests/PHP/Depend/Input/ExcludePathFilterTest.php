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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Input
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Depend/Input/ExcludePathFilter.php';

/**
 * Test case for the exclude path filter.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Input
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Input_ExcludePathFilterTest extends PHP_Depend_AbstractTest
{
    /**
     * testExcludePathFilterRejectsFile
     *
     * @return void
     * @covers PHP_Depend_Input_ExcludePathFilter
     * @group pdepend
     * @group pdepend::input
     * @group unittest
     */
    public function testExcludePathFilterRejectsFile()
    {
        $excludes  = array('/package2.php');
        $directory = self::createCodeResourceURI('input/ExcludePathFilter/' . __FUNCTION__);

        $actual   = $this->createFilteredFileList($excludes, $directory);
        $expected = array('package1.php', 'package3.php');

        self::assertEquals($expected, $actual);
    }

    /**
     * testExcludePathFilterRejectsFiles
     *
     * @return void
     * @covers PHP_Depend_Input_ExcludePathFilter
     * @group pdepend
     * @group pdepend::input
     * @group unittest
     */
    public function testExcludePathFilterRejectsFiles()
    {
        $excludes  = array('/package2.php', 'package1.php');
        $directory = self::createCodeResourceURI('input/ExcludePathFilter/' . __FUNCTION__);

        $actual   = $this->createFilteredFileList($excludes, $directory);
        $expected = array('package3.php');

        self::assertEquals($expected, $actual);
    }

    /**
     * testExcludePathFilterRejectsDirectory
     *
     * @return void
     * @covers PHP_Depend_Input_ExcludePathFilter
     * @group pdepend
     * @group pdepend::input
     * @group unittest
     */
    public function testExcludePathFilterRejectsDirectory()
    {
        $excludes  = array('/package1');
        $directory = self::createCodeResourceURI('input/ExcludePathFilter/' . __FUNCTION__);

        $actual   = $this->createFilteredFileList($excludes, $directory);
        $expected = array('file2.php', 'file3.php');

        self::assertEquals($expected, $actual);
    }

    /**
     * testExcludePathFilterRejectsDirectories
     *
     * @return void
     * @covers PHP_Depend_Input_ExcludePathFilter
     * @group pdepend
     * @group pdepend::input
     * @group unittest
     */
    public function testExcludePathFilterRejectsDirectories()
    {
        $excludes  = array('/package1', 'package3');
        $directory = self::createCodeResourceURI('input/ExcludePathFilter/' . __FUNCTION__);

        $actual   = $this->createFilteredFileList($excludes, $directory);
        $expected = array('file2.php');

        self::assertEquals($expected, $actual);
    }

    /**
     * testExcludePathFilterRejectsFilesAndDirectories
     *
     * @return void
     * @covers PHP_Depend_Input_ExcludePathFilter
     * @group pdepend
     * @group pdepend::input
     * @group unittest
     */
    public function testExcludePathFilterRejectsFilesAndDirectories()
    {
        $excludes  = array('/package1', 'package3/file3.php');
        $directory = self::createCodeResourceURI('input/ExcludePathFilter/' . __FUNCTION__);

        $actual   = $this->createFilteredFileList($excludes, $directory);
        $expected = array('file2.php');

        self::assertEquals($expected, $actual);
    }

    /**
     * Creates an array with those files that were acceptable for the exclude
     * path filter.
     *
     * @param array(string) $excludes  The filtered patterns
     * @param string        $directory The input directory
     *
     * @return array(string)
     */
    protected function createFilteredFileList(array $excludes, $directory)
    {
        $filter = new PHP_Depend_Input_ExcludePathFilter($excludes);

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );

        $actual = array();
        foreach ($files as $file) {
            if ($filter->accept($file) && $file->isFile()) {
                $actual[] = $file->getFilename();
            }
        }
        sort($actual);

        return $actual;
    }
}