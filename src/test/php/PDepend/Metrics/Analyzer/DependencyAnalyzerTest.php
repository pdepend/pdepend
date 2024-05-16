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
 * @covers \PDepend\Metrics\Analyzer\DependencyAnalyzer
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @group unittest
 */
class DependencyAnalyzerTest extends AbstractMetricsTestCase
{
    /** The used node builder. */
    protected Builder $builder;

    /**
     * Input test data.
     *
     * @var array<string, array>
     */
    private array $input = [
        '+global' => [
            'tc' => 0,
            'cc' => 0,
            'ac' => 0,
            'ca' => 1,
            'ce' => 0,
            'a' => 0,
            'i' => 0,
            'd' => 1,
        ],
        'pkg1' => [
            'tc' => 1,
            'cc' => 1,
            'ac' => 0,
            'ca' => 0,
            'ce' => 2,
            'a' => 0,
            'i' => 1,
            'd' => 0,
        ],
        'pkg2' => [
            'tc' => 1,
            'cc' => 0,
            'ac' => 1,
            'ca' => 1,
            'ce' => 0,
            'a' => 1,
            'i' => 0,
            'd' => 0,
        ],
        'pkg3' => [
            'tc' => 1,
            'cc' => 0,
            'ac' => 1,
            'ca' => 1,
            'ce' => 1,
            'a' => 1,
            'i' => 0.5,
            'd' => 0.5,
        ],
    ];

    /**
     * Tests the generated package metrics.
     */
    public function testGenerateMetrics(): void
    {
        $visitor = new DependencyAnalyzer();

        $namespaces = $this->parseCodeResourceForTest();
        $visitor->analyze($namespaces);

        $actual = [];
        foreach ($namespaces as $namespace) {
            $actual[$namespace->getImage()] = $visitor->getStats($namespace);
        }

        static::assertEquals($this->input, $actual);
    }
}
