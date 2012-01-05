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
 * @subpackage Input
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for the php file filter iterator.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Input
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 *
 * @covers PHP_Depend_Input_Iterator
 * @group pdepend
 * @group pdepend::input
 * @group unittest
 */
class PHP_Depend_Input_IteratorTest extends PHP_Depend_AbstractTest
{
    /**
     * testIteratorWithOneFileExtension
     *
     * @return void
     */
    public function testIteratorWithOneFileExtension()
    {
        $actual   = $this->createFilteredFileList(array('php4'));
        $expected = array('file4.php4');

        self::assertEquals($expected, $actual);
    }

    /**
     * testIteratorWithMultipleFileExtensions
     *
     * @return void
     */
    public function testIteratorWithMultipleFileExtensions()
    {
        $actual   = $this->createFilteredFileList(array('inc', 'php'));
        $expected = array('file1.inc', 'file2.php');

        self::assertEquals($expected, $actual);
    }

    /**
     * testIteratorPassesLocalPathToFilterWhenRootIsPresent
     *
     * @return void
     */
    public function testIteratorPassesLocalPathToFilterWhenRootIsPresent()
    {
        $filter = $this->getMock('PHP_Depend_Input_FilterI');
        $filter->expects($this->once())
            ->method('accept')
            ->with(self::equalTo(DIRECTORY_SEPARATOR . basename(__FILE__)));
        
        $iterator = new PHP_Depend_Input_Iterator(
            new ArrayIterator(array(new SplFileInfo(__FILE__))),
            $filter,
            dirname(__FILE__)
        );
        $iterator->accept();
    }

    /**
     * testIteratorPassesAbsolutePathToFilterWhenNoRootIsPresent
     *
     * @return void
     */
    public function testIteratorPassesAbsolutePathToFilterWhenNoRootIsPresent()
    {
        $files = new ArrayIterator(array(new SplFileInfo(__FILE__)));

        $filter = $this->getMock('PHP_Depend_Input_FilterI');
        $filter->expects($this->once())
            ->method('accept')
            ->with(self::equalTo(__FILE__), self::equalTo(__FILE__));

        $iterator = new PHP_Depend_Input_Iterator($files, $filter);
        $iterator->accept();
    }

    /**
     * testIteratorPassesAbsolutePathToFilterWhenRootNotMatches
     *
     * @return void
     */
    public function testIteratorPassesAbsolutePathToFilterWhenRootNotMatches()
    {
        $files = new ArrayIterator(array(new SplFileInfo(__FILE__)));

        $filter = $this->getMock('PHP_Depend_Input_FilterI');
        $filter->expects($this->once())
            ->method('accept')
            ->with(self::equalTo(__FILE__), self::equalTo(__FILE__));

        $iterator = new PHP_Depend_Input_Iterator($files, $filter, 'c:\foo');
        $iterator->accept();
    }

    /**
     * Creates an array of file names that were returned by the input iterator.
     *
     * @param array(string) $extensions The accepted file extension.
     *
     * @return array(string)
     */
    protected function createFilteredFileList(array $extensions)
    {
        $files  = new PHP_Depend_Input_Iterator(
            new DirectoryIterator(self::createCodeResourceUriForTest()),
            new PHP_Depend_Input_ExtensionFilter($extensions)
        );

        $actual = array();
        foreach ($files as $file) {
            $actual[] = $file->getFilename();
        }
        sort($actual);

        return $actual;
    }
}
