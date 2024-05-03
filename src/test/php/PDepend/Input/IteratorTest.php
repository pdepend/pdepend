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

namespace PDepend\Input;

use PDepend\AbstractTestCase;

/**
 * Test case for the php file filter iterator.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Input\Iterator
 * @group unittest
 */
class IteratorTest extends AbstractTestCase
{
    /**
     * testIteratorWithOneFileExtension
     *
     * @return void
     */
    public function testIteratorWithOneFileExtension(): void
    {
        $actual   = $this->createFilteredFileList(['php4']);
        $expected = ['file4.php4'];

        $this->assertEquals($expected, $actual);
    }

    /**
     * testIteratorWithMultipleFileExtensions
     *
     * @return void
     */
    public function testIteratorWithMultipleFileExtensions(): void
    {
        $actual   = $this->createFilteredFileList(['inc', 'php']);
        $expected = ['file1.inc', 'file2.php'];

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that iterator returns only files.
     *
     * @return void
     */
    public function testIteratorReturnsOnlyFiles(): void
    {
        $directory=$this->createCodeResourceUriForTest();
        $pattern = $directory . DIRECTORY_SEPARATOR . 'Ignored';

        $files  = new Iterator(
            new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)),
            new ExcludePathFilter([$pattern])
        );

        $actual = [];
        foreach ($files as $file) {
            $actual[] = $file->getFilename();
        }
        sort($actual);

        $expected = ['file.php', 'file_process.php'];
        
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * testIteratorPassesLocalPathToFilterWhenRootIsPresent
     *
     * @return void
     */
    public function testIteratorPassesLocalPathToFilterWhenRootIsPresent(): void
    {
        $filter = $this->getMockBuilder('\\PDepend\\Input\\Filter')
            ->getMock();
        $filter->expects($this->once())
            ->method('accept')
            ->with($this->equalTo(DIRECTORY_SEPARATOR . basename(__FILE__)));
        
        $iterator = new Iterator(
            new \ArrayIterator([new \SplFileInfo(__FILE__)]),
            $filter,
            __DIR__
        );
        $iterator->accept();
    }

    /**
     * testIteratorPassesAbsolutePathToFilterWhenNoRootIsPresent
     *
     * @return void
     */
    public function testIteratorPassesAbsolutePathToFilterWhenNoRootIsPresent(): void
    {
        $files = new \ArrayIterator([new \SplFileInfo(__FILE__)]);

        $filter = $this->getMockBuilder('\\PDepend\\Input\\Filter')
            ->getMock();
        $filter->expects($this->once())
            ->method('accept')
            ->with($this->equalTo(__FILE__), $this->equalTo(__FILE__));

        $iterator = new Iterator($files, $filter);
        $iterator->accept();
    }

    /**
     * testIteratorPassesAbsolutePathToFilterWhenRootNotMatches
     *
     * @return void
     */
    public function testIteratorPassesAbsolutePathToFilterWhenRootNotMatches(): void
    {
        $files = new \ArrayIterator([new \SplFileInfo(__FILE__)]);

        $filter = $this->getMockBuilder('\\PDepend\\Input\\Filter')
            ->getMock();
        $filter->expects($this->once())
            ->method('accept')
            ->with($this->equalTo(__FILE__), $this->equalTo(__FILE__));

        $iterator = new Iterator($files, $filter, 'c:\foo');
        $iterator->accept();
    }

    /**
     * Creates an array of file names that were returned by the input iterator.
     *
     * @param array<string> $extensions The accepted file extension.
     *
     * @return array<string>
     */
    protected function createFilteredFileList(array $extensions)
    {
        $files  = new Iterator(
            new \DirectoryIterator($this->createCodeResourceUriForTest()),
            new ExtensionFilter($extensions)
        );

        $actual = [];
        foreach ($files as $file) {
            $actual[] = $file->getFilename();
        }
        sort($actual);

        return $actual;
    }
}
