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
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Test case for the file extension filter.
 *
 * @covers \PDepend\Input\ExtensionFilter
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ExtensionFilterTest extends AbstractTestCase
{
    /**
     * testExtensionFilterAcceptsOneFileExtension
     */
    public function testExtensionFilterAcceptsOneFileExtension(): void
    {
        $actual = $this->createFilteredFileList(['php4']);
        $expected = ['file4.php4'];

        static::assertEquals($expected, $actual);
    }

    /**
     * testExtensionFilterAcceptsMultipleFileExtensions
     */
    public function testExtensionFilterAcceptsMultipleFileExtensions(): void
    {
        $actual = $this->createFilteredFileList(['inc', 'php']);
        $expected = ['file1.inc', 'file2.php'];

        static::assertEquals($expected, $actual);
    }

    public function testExtensionFilterAcceptsPhpProtocol(): void
    {
        $filter = new ExtensionFilter(['abc']);

        static::assertTrue($filter->accept('', 'php://def'));
    }

    /**
     * Creates an array with those files that were acceptable for the extension
     * filter.
     *
     * @param array<string> $includes The file extensions
     * @return array<string>
     */
    protected function createFilteredFileList(array $includes): array
    {
        $filter = new ExtensionFilter($includes);

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $this->createCodeResourceUriForTest()
            )
        );

        $actual = [];
        foreach ($files as $file) {
            if ($file instanceof SplFileInfo
                && $filter->accept($file, $file)
                && $file->isFile()
                && false === stripos($file->getPathname(), '.svn')
            ) {
                $actual[] = $file->getFilename();
            }
        }
        sort($actual);

        return $actual;
    }
}
