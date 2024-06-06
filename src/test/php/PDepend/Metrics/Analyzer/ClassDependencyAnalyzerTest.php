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

namespace PDepend\Metrics\Analyzer;

use PDepend\Metrics\AbstractMetricsTestCase;
use PDepend\Source\Builder\Builder;

/**
 * Tests the for the package metrics visitor.
 *
 * @covers \PDepend\Metrics\Analyzer\ClassDependencyAnalyzer
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @group unittest
 */
class ClassDependencyAnalyzerTest extends AbstractMetricsTestCase
{
    /** @var Builder<mixed> The used node builder. */
    protected Builder $builder;

    /**
     * Input test data.
     *
     * @var array<string, array<mixed>>
     */
    private array $input = [
        'AbstractBase' => [
            'efferent' => [],
            'afferent' => [
                'BaseClass',
            ],
        ],
        'SomeInterface' => [
            'efferent' => [],
            'afferent' => [
                'Used',
            ],
        ],
        'SomeTrait' => [
            'efferent' => [],
            'afferent' => [],
        ],
        'Used' => [
            'efferent' => [
                'SomeInterface',
                'BaseClass',
            ],
            'afferent' => [
                'BaseClass',
            ],
        ],
        'BaseClass' => [
            'efferent' => [
                'AbstractBase',
                'Used',
            ],
            'afferent' => [
                'Used',
            ],
        ],
    ];

    /**
     * Tests the generated package metrics.
     */
    public function testGenerateMetrics(): void
    {
        $visitor = new ClassDependencyAnalyzer();

        $namespaces = self::parseCodeResourceForTest();
        $visitor->analyze($namespaces);

        $actual = [];
        foreach ($namespaces as $namespace) {
            foreach ($namespace->getTypes() as $type) {
                $actual[$type->getImage()]['efferent'] = array_map(
                    static fn($type) => $type->getImage(),
                    $visitor->getEfferents($type)
                );
                $actual[$type->getImage()]['afferent'] = array_map(
                    static fn($type) => $type->getImage(),
                    $visitor->getAfferents($type)
                );
            }
        }
        ksort($actual);

        static::assertEquals($this->input, $actual);
    }
}
