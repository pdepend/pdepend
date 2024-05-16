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

namespace PDepend\Integration;

use PDepend\AbstractTestCase;
use PDepend\Input\ExcludePathFilter;

/**
 * Tests the integration of the {@link \PDepend\Engine} class and the
 * input filter class {@link \PDepend\Input\ExcludePathFilter}.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group integrationtest
 */
class DependExcludePathFilterTest extends AbstractTestCase
{
    /**
     * testPDependFiltersSingleFileWithPattern
     */
    public function testPDependFiltersSingleFileWithPattern(): void
    {
        $this->changeWorkingDirectory();

        $directory = $this->createCodeResourceUriForTest();
        $pattern = '*' . DIRECTORY_SEPARATOR . 'Integration' . DIRECTORY_SEPARATOR . '*';

        $pdepend = $this->createEngineFixture();
        $pdepend->addFile($this->createCodeResourceUriForTest() . '/Integration/FilteredClass.php');
        $pdepend->addFileFilter(
            new ExcludePathFilter([$pattern])
        );

        static::assertCount(0, $pdepend->analyze());
    }

    /**
     * testPDependFiltersByRelativePath
     */
    public function testPDependFiltersByRelativePath(): void
    {
        $this->changeWorkingDirectory();

        $directory = $this->createCodeResourceUriForTest();
        $pattern = DIRECTORY_SEPARATOR . 'Integration';

        $pdepend = $this->createEngineFixture();
        $pdepend->addDirectory($directory);
        $pdepend->addFileFilter(
            new ExcludePathFilter([$pattern])
        );

        static::assertCount(0, $pdepend->analyze());
    }

    /**
     * testPDependFiltersByAbsolutePath
     */
    public function testPDependFiltersByAbsolutePath(): void
    {
        $this->changeWorkingDirectory();

        $directory = $this->createCodeResourceUriForTest();
        $pattern = $directory . DIRECTORY_SEPARATOR . 'Integration';

        if (str_starts_with($directory, '/scratch/')) {
            static::markTestSkipped('Not sure why, but this test fails @CloudBees');
        }

        $pdepend = $this->createEngineFixture();
        $pdepend->addDirectory($directory);
        $pdepend->addFileFilter(
            new ExcludePathFilter([$pattern])
        );

        static::assertCount(
            1,
            $pdepend->analyze(),
            sprintf(
                'Pattern "%s" does not match in directory "%s".',
                $pattern,
                $directory
            )
        );
    }

    /**
     * testPDependNotFiltersByOverlappingPathMatch
     */
    public function testPDependNotFiltersByOverlappingPathMatch(): void
    {
        $this->changeWorkingDirectory();

        $directory = $this->createCodeResourceUriForTest();
        $pattern = __FUNCTION__ . DIRECTORY_SEPARATOR . 'Integration';

        $pdepend = $this->createEngineFixture();
        $pdepend->addDirectory($directory);
        $pdepend->addFileFilter(
            new ExcludePathFilter([$pattern])
        );

        static::assertCount(1, $pdepend->analyze());
    }
}
