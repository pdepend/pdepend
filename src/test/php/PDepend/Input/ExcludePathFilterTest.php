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
 * Test case for the exclude path filter.
 *
 * @covers \PDepend\Input\ExcludePathFilter
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ExcludePathFilterTest extends AbstractTestCase
{
    /**
     * testAbsoluteUnixPathAsFilterPatternMatches
     */
    public function testAbsoluteUnixPathAsFilterPatternMatches(): void
    {
        $filter = new ExcludePathFilter(['/foo/bar']);
        static::assertFalse($filter->accept('/baz', '/foo/bar/baz'));
    }

    /**
     * testAbsoluteUnixPathAsFilterPatternNotMatches
     */
    public function testAbsoluteUnixPathAsFilterPatternNotMatches(): void
    {
        $filter = new ExcludePathFilter(['/foo/bar']);
        static::assertTrue($filter->accept('/foo/baz/bar', '/foo/baz/bar'));
    }

    public function testRelativePathMatchOrNot(): void
    {
        $filter = new ExcludePathFilter(['link-to/bar']);
        static::assertFalse($filter->accept('foo\\link-to\\bar', 'C:\\real-path-to\\bar'));
        static::assertTrue($filter->accept('real-path-to\\bar', 'C:\\real-path-to\\bar'));
        $filter = new ExcludePathFilter(['*/foo/bar']);
        static::assertFalse($filter->accept('foo\\link-to\\bar\\nested', 'C:\\biz\\foo\\bar\\nested'));
        static::assertTrue($filter->accept('foo\\link-to\\bar\\nested', 'C:\\biz\\baz\\bar\\nested'));
    }

    /**
     * testUnixPathAsFilterPatternNotMatchesPartial
     */
    public function testUnixPathAsFilterPatternNotMatchesPartial(): void
    {
        $pattern = 'PDepend-git/PHP';
        $absolute = '/home/manuel/workspace/PDepend-git/PDepend.php';
        $relative = '/PDepend.php';

        $filter = new ExcludePathFilter([$pattern]);
        static::assertTrue($filter->accept($relative, $absolute));
    }

    /**
     * testAbsoluteWindowsPathAsFilterPatternMatches
     */
    public function testAbsoluteWindowsPathAsFilterPatternMatches(): void
    {
        $filter = new ExcludePathFilter(['c:\workspace\bar']);
        static::assertFalse($filter->accept('\baz', 'c:\workspace\bar\baz'));
    }

    /**
     * testAbsoluteWindowsPathAsFilterPatternNotMatches
     */
    public function testAbsoluteWindowsPathAsFilterPatternNotMatches(): void
    {
        $filter = new ExcludePathFilter(['c:\workspace\\']);
        static::assertTrue($filter->accept('c:\workspac\bar', 'c:\workspac\bar'));
    }

    /**
     * testWindowsPathAsFilterPatternNotMatchesPartial
     */
    public function testWindowsPathAsFilterPatternNotMatchesPartial(): void
    {
        $pattern = 'PDepend-git\PHP';
        $absolute = 'c:\workspace\PDepend-git\PDepend.php';
        $relative = '\PDepend.php';

        $filter = new ExcludePathFilter([$pattern]);
        static::assertTrue($filter->accept($relative, $absolute));
    }

    /**
     * testExcludePathFilterRejectsFile
     */
    public function testExcludePathFilterRejectsFile(): void
    {
        $actual = $this->createFilteredFileList([DIRECTORY_SEPARATOR . 'package2.php']);
        $expected = ['package1.php', 'package3.php'];

        static::assertEquals($expected, $actual);
    }

    /**
     * testExcludePathFilterRejectsFiles
     */
    public function testExcludePathFilterRejectsFiles(): void
    {
        $actual = $this->createFilteredFileList([DIRECTORY_SEPARATOR . 'package2.php', '*1.php']);
        $expected = ['package3.php'];

        static::assertEquals($expected, $actual);
    }

    /**
     * testExcludePathFilterRejectsDirectory
     */
    public function testExcludePathFilterRejectsDirectory(): void
    {
        $actual = $this->createFilteredFileList([DIRECTORY_SEPARATOR . 'package1']);
        $expected = ['file2.php', 'file3.php'];

        static::assertEquals($expected, $actual);
    }

    /**
     * testExcludePathFilterRejectsDirectories
     */
    public function testExcludePathFilterRejectsDirectories(): void
    {
        $actual = $this->createFilteredFileList([DIRECTORY_SEPARATOR . 'package1', 'package3']);
        $expected = ['file2.php'];

        static::assertEquals($expected, $actual);
    }

    /**
     * testExcludePathFilterRejectsFilesAndDirectories
     */
    public function testExcludePathFilterRejectsFilesAndDirectories(): void
    {
        $actual = $this->createFilteredFileList(
            [DIRECTORY_SEPARATOR . 'package1', DIRECTORY_SEPARATOR . 'file3.php']
        );
        $expected = ['file2.php'];

        static::assertEquals($expected, $actual);
    }

    /**
     * Creates an array with those files that were acceptable for the exclude
     * path filter.
     *
     * @param array<string> $excludes The filtered patterns
     * @return array<string>
     */
    protected function createFilteredFileList(array $excludes): array
    {
        $filter = new ExcludePathFilter($excludes);

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
