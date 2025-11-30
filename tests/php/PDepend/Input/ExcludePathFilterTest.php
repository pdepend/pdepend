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

use Exception;
use PDepend\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionProperty;
use SplFileInfo;

/**
 * Test case for the exclude path filter.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
#[CoversClass(ExcludePathFilter::class)]
#[Group('unittest')]
class ExcludePathFilterTest extends AbstractTestCase
{
    /**
     * testPatternsWithSixtyThousandCharactersAcceptsRelativePatternNotInList
     *
     * This is to test that the default preg_match limit of 32,766 characters is avoided with large patterns in
     * circumstances where too many files are excluded instead of using the baseline.
     *
     * Generates 2,000 patterns of 20, 10 and 5 random alpha characters, with some slashes (about 70k characters))
     */
    public function testPatternsWithSixtyThousandCharactersAcceptsRelativeOrAbsolutePatternNotInList(): void
    {
        $patterns = $this->prepareTestPatterns();

        $filter = new ExcludePathFilter($patterns);
        static::assertTrue($filter->accept('foo0/baz0/bar0', 'C:\\blahblah\\bar'));
    }

    /**
     * testPatternsWithSixtyThousandCharactersRejectsRelativePatternFoundInList
     */
    public function testPatternsWithSixtyThousandCharactersRejectsRelativePatternFoundInList(): void
    {
        $patterns = $this->prepareTestPatterns();

        $filter = new ExcludePathFilter($patterns);
        $firstPattern = str_replace('/', '\\', $patterns[0]);
        $relativePath = 'foo\\' . $firstPattern;

        static::assertFalse($filter->accept($relativePath, 'C:\\blahblah\\bar'));
    }

    /**
     * testPatternsWithSixtyThousandCharactersRejectsAbsolutePatternFoundInList
     */
    public function testPatternsWithSixtyThousandCharactersRejectsAbsolutePatternFoundInList(): void
    {
        $patterns = $this->prepareTestPatterns();

        $filter = new ExcludePathFilter($patterns);
        $firstPattern = str_replace('/', '\\', $patterns[0]);
        $absolutePath = $firstPattern . '\\bar';

        static::assertFalse($filter->accept('/foobar/barf00', $absolutePath));
    }

    /**
     * testPatternsWithSixtyThousandCharactersSetProtectedIsBulkToTrue
     */
    public function testPatternsWithSixtyThousandCharactersSetProtectedIsBulkToTrue(): void
    {
        $patterns = $this->prepareTestPatterns();

        $filter = new ExcludePathFilter($patterns);

        $isBulk = new ReflectionProperty($filter, 'isBulk');

        static::assertTrue($isBulk->getValue($filter));
    }

    /**
     * testPatternsWithLessThanThirtyThousandCharactersSetProtectedIsBulkToFalse
     */
    public function testPatternsWithLessThanThirtyThousandCharactersSetProtectedIsBulkToFalse(): void
    {
        $filter = new ExcludePathFilter(['Just16Characters']);

        $isBulk = new ReflectionProperty($filter, 'isBulk');

        static::assertFalse($isBulk->getValue($filter));
    }

    /**
     * testEmptyPatternListSetsEmptyPattern
     */
    public function testEmptyPatternListSetsEmptyPatternAndIsAccepted(): void
    {
        $filter = new ExcludePathFilter([]);

        static::assertTrue($filter->accept('any/path', 'C:\\any\\path'));
    }

    /**
     * testEmptyPatternStringSetsEmptyPattern
     */
    public function testEmptyPatternStringSetsEmptyPatternAndIsAccepted(): void
    {
        $filter = new ExcludePathFilter(['']);

        static::assertTrue($filter->accept('any/path', 'C:\\any\\path'));
    }

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

    /**
     * testRelativePathMatchOrNot
     */
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
            if (
                $file instanceof SplFileInfo
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

    /**
     * Returns a random string with a given length.
     *
     * @throws Exception
     */
    private function randAlpha(int $length = 3): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charsLength = strlen($chars);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $chars[random_int(0, $charsLength - 1)];
        }

        return $randomString;
    }

    /**
     * Prepares a list of test patterns which loosely resemble a directory/path, e.g. abcd/efg/hijk
     *
     * @return array<string>
     */
    private function prepareTestPatterns(): array
    {
        $patterns = [];
        for ($i = 0; $i < 2000; $i++) {
            $patterns[] = $this->randAlpha(20) . '/' . $this->randAlpha(10) . '/' . $this->randAlpha(5);
        }

        return $patterns;
    }
}
