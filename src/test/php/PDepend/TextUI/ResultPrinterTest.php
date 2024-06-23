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

namespace PDepend\TextUI;

use PDepend\AbstractTestCase;
use PDepend\Metrics\Analyzer\ClassLevelAnalyzer;
use PDepend\Metrics\Analyzer\DependencyAnalyzer;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\Language\PHP\PHPBuilder;
use PDepend\Source\Language\PHP\PHPTokenizerInternal;

/**
 * Test case for the default text ui result printer.
 *
 * @covers \PDepend\TextUI\ResultPrinter
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ResultPrinterTest extends AbstractTestCase
{
    /**
     * Tests the output for a single file entry.
     */
    public function testResultPrinterOutputForSingleEntry(): void
    {
        $printer = new ResultPrinter();

        ob_start();
        $printer->startFileParsing();
        $printer->endParseProcess();
        $actual = ob_get_contents();
        ob_end_clean();

        $expected = ".                                                                1\n\n";

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests the result printer with multiple entries.
     */
    public function testResultPrinterOutputForMultipleEntries(): void
    {
        $printer = new ResultPrinter();

        ob_start();
        for ($i = 0; $i < 73; ++$i) {
            $printer->startFileParsing();
        }
        $printer->endParseProcess();
        $actual = ob_get_contents();
        ob_end_clean();

        $expected = "............................................................    60\n"
                  . ".............                                                   73\n\n";

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests the result printer with multiple entries.
     */
    public function testResultPrinterForMultipleEntries(): void
    {
        // Create dummy objects
        $method = new ASTMethod('method');
        $analyzer = new ClassLevelAnalyzer();

        $printer = new ResultPrinter();

        ob_start();
        for ($i = 0; $i < 1401; ++$i) {
            $printer->startVisitMethod($method);
        }
        $printer->endAnalyzer($analyzer);
        $actual = ob_get_contents();
        ob_end_clean();

        $expected = "............................................................  1200\n"
                  . "..........                                                    1401\n\n";

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests the result printer with multiple entries.
     */
    public function testResultPrinterForCompleteLine(): void
    {
        // Create dummy objects
        $method = new ASTMethod('method');
        $analyzer = new ClassLevelAnalyzer();

        $printer = new ResultPrinter();

        ob_start();
        for ($i = 0; $i < 2400; ++$i) {
            $printer->startVisitMethod($method);
        }
        $printer->endAnalyzer($analyzer);
        $actual = ob_get_contents();
        ob_end_clean();

        $expected = "............................................................  1200\n"
                  . "............................................................  2400\n\n";

        static::assertEquals($expected, $actual);
    }

    public function testStartParseProcess(): void
    {
        self::expectOutput(
            'Parsing source files:',
            function (): void {
                $printer = new ResultPrinter();

                $printer->startParseProcess();
            }
        );
    }

    public function testStartLogProcess(): void
    {
        self::expectOutput(
            'Generating pdepend log files, this may take a moment.',
            function (): void {
                $printer = new ResultPrinter();

                $printer->startLogProcess();
            }
        );
    }

    public function testStartAnalyzer(): void
    {
        self::expectOutput(
            'Calculating Dependency metrics:',
            function (): void {
                $printer = new ResultPrinter();

                $printer->startAnalyzer(new DependencyAnalyzer());
            }
        );
    }

    public function testEmptyMethods(): void
    {
        self::expectOutput(
            '',
            function (): void {
                $printer = new ResultPrinter();

                $printer->endFileParsing();
                $printer->startAnalyzeProcess();
                $printer->endAnalyzeProcess();
                $printer->endLogProcess();
            }
        );
    }

    /**
     * @param callable(): void $action
     */
    private static function expectOutput(string $expected, callable $action): void
    {
        ob_start();
        $action();
        $output = ob_get_contents();
        ob_end_clean();

        static::assertSame($expected, trim($output ?: ''));
    }
}
