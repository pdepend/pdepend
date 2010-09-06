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

require_once 'PHP/Depend/Input/ExtensionFilter.php';
require_once 'PHP/Depend/Input/Iterator.php';

/**
 * Test case for the php file filter iterator.
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
class PHP_Depend_Input_IteratorTest extends PHP_Depend_AbstractTest
{
    /**
     * testIteratorWithOneFileExtension
     *
     * @return void
     * @covers PHP_Depend_Input_Iterator
     * @group pdepend
     * @group pdepend::input
     * @group unittest
     */
    public function testIteratorWithOneFileExtension()
    {
        $includes  = array('php4');
        $directory = self::createCodeResourceURI('input/Iterator/' . __FUNCTION__);

        $actual   = $this->createFilteredFileList($includes, $directory);
        $expected = array('file4.php4');

        self::assertEquals($expected, $actual);
    }

    /**
     * testIteratorWithMultipleFileExtensions
     *
     * @return void
     * @covers PHP_Depend_Input_Iterator
     * @group pdepend
     * @group pdepend::input
     * @group unittest
     */
    public function testIteratorWithMultipleFileExtensions()
    {
        $includes  = array('inc', 'php');
        $directory = self::createCodeResourceURI('input/Iterator/' . __FUNCTION__);

        $actual   = $this->createFilteredFileList($includes, $directory);
        $expected = array('file1.inc', 'file2.php');

        self::assertEquals($expected, $actual);
    }

    /**
     * Creates an array of file names that were returned by the input iterator.
     *
     * @param array(string) $includes  The accepted file extension.
     * @param string        $directory The source directory
     *
     * @return array(string)
     */
    protected function createFilteredFileList(array $includes, $directory)
    {
        $filter = new PHP_Depend_Input_ExtensionFilter($includes);
        $files  = new PHP_Depend_Input_Iterator(
            new DirectoryIterator($directory), $filter
        );

        $actual = array();
        foreach ($files as $file) {
            $actual[] = $file->getFilename();
        }
        sort($actual);

        return $actual;
    }
}