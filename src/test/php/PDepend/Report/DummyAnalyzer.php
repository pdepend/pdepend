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

namespace PDepend\Report;

use PDepend\Metrics\AnalyzerListener;
use PDepend\Metrics\AnalyzerNodeAware;
use PDepend\Metrics\AnalyzerProjectAware;
use PDepend\Source\AST\ASTArtifact;

/**
 * Simple dummy analyzer.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class DummyAnalyzer implements AnalyzerNodeAware, AnalyzerProjectAware
{
    /** Test project metrics */
    public array $projectMetrics = [];

    /** Test node metrics. */
    public array $nodeMetrics = [];

    /**
     * Constructs a new analyzer instance.
     *
     * @param array<string, mixed> $options Global option array, every analyzer
     *                                      can extract the required options.
     */
    public function __construct(array $options = [])
    {
    }

    /**
     * Returns the project metrics.
     *
     * @return array<string, int>
     */
    public function getProjectMetrics(): array
    {
        return $this->projectMetrics;
    }

    /**
     * Returns the node metrics.
     *
     * @return array<string, int>
     */
    public function getNodeMetrics(ASTArtifact $artifact): array
    {
        return $this->nodeMetrics[$artifact->getImage()] ?? [];
    }

    /**
     * Adds a listener to this analyzer.
     *
     * @param AnalyzerListener $listener The listener instance.
     */
    public function addAnalyzeListener(AnalyzerListener $listener): void
    {
    }

    /**
     * Removes the listener from this analyzer.
     *
     * @param AnalyzerListener $listener The listener instance.
     */
    public function removeAnalyzeListener(AnalyzerListener $listener): void
    {
    }

    /**
     * Processes all {@link \PDepend\Source\AST\ASTNamespace} code nodes.
     */
    public function analyze($namespaces): void
    {
    }

    /**
     * By default all analyzers are enabled. Overwrite this method to provide
     * state based disabling/enabling.
     *
     * @since 0.9.10
     */
    public function isEnabled(): bool
    {
        return true;
    }

    /**
     * Set global options
     *
     * @param array<string, mixed> $options
     * @since 2.0.1
     */
    public function setOptions(array $options = []): void
    {
    }
}
