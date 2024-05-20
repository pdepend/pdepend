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

namespace PDepend\Metrics\Analyzer\CodeRankAnalyzer;

use PDepend\AbstractTestCase;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTNamespace;

/**
 * Test case for the code rank property strategy.
 *
 * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\PropertyStrategy
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class PropertyStrategyTest extends AbstractTestCase
{
    /**
     * testStrategyCountsCorrectTypes
     */
    public function testStrategyCountsCorrectTypes(): void
    {
        $namespaces = $this->parseCodeResourceForTest();

        $idMap = [
            'PDepend::CodeRankA' => null,
            'PDepend::CodeRankB' => null,
            'PDepend_CodeRank_ClassA' => null,
            'PDepend_CodeRank_ClassB' => null,
            'PDepend_CodeRank_ClassC' => null,
        ];

        foreach ($namespaces as $namespace) {
            foreach ($namespace->getClasses() as $class) {
                static::assertArrayHasKey($class->getImage(), $idMap);
                $idMap[$class->getImage()] = $class->getId();
            }
            if (array_key_exists($namespace->getImage(), $idMap)) {
                $idMap[$namespace->getImage()] = $namespace->getId();
            }
        }

        $expected = [
            $idMap['PDepend_CodeRank_ClassA'] => [
                'in' => [
                    $idMap['PDepend_CodeRank_ClassB'],
                    $idMap['PDepend_CodeRank_ClassC'],
                ],
                'out' => [
                    $idMap['PDepend_CodeRank_ClassC'],
                ],
                'name' => 'PDepend_CodeRank_ClassA',
                'type' => ASTClass::class,
            ],
            $idMap['PDepend_CodeRank_ClassB'] => [
                'in' => [
                    $idMap['PDepend_CodeRank_ClassC'],
                    $idMap['PDepend_CodeRank_ClassC'],
                ],
                'out' => [
                    $idMap['PDepend_CodeRank_ClassA'],
                ],
                'name' => 'PDepend_CodeRank_ClassB',
                'type' => ASTClass::class,
            ],
            $idMap['PDepend_CodeRank_ClassC'] => [
                'in' => [
                    $idMap['PDepend_CodeRank_ClassA'],
                ],
                'out' => [
                    $idMap['PDepend_CodeRank_ClassA'],
                    $idMap['PDepend_CodeRank_ClassB'],
                    $idMap['PDepend_CodeRank_ClassB'],
                ],
                'name' => 'PDepend_CodeRank_ClassC',
                'type' => ASTClass::class,
            ],
            $idMap['PDepend::CodeRankA'] => [
                'in' => [
                    $idMap['PDepend::CodeRankB'],
                    $idMap['PDepend::CodeRankB'],
                    $idMap['PDepend::CodeRankB'],
                ],
                'out' => [
                    $idMap['PDepend::CodeRankB'],
                ],
                'name' => 'PDepend::CodeRankA',
                'type' => ASTNamespace::class,
            ],
            $idMap['PDepend::CodeRankB'] => [
                'in' => [
                    $idMap['PDepend::CodeRankA'],
                ],
                'out' => [
                    $idMap['PDepend::CodeRankA'],
                    $idMap['PDepend::CodeRankA'],
                    $idMap['PDepend::CodeRankA'],
                ],
                'name' => 'PDepend::CodeRankB',
                'type' => ASTNamespace::class,
            ],
        ];

        $strategy = new PropertyStrategy();
        foreach ($namespaces as $namespace) {
            $strategy->dispatch($namespace);
        }

        $actual = $strategy->getCollectedNodes();

        static::assertEquals($expected, $actual);
    }
}
