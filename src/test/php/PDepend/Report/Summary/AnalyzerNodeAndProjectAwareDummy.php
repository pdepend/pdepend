<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Report\Summary;

use PDepend\Metrics\AnalyzerListener;
use PDepend\Metrics\AnalyzerNodeAware;
use PDepend\Metrics\AnalyzerProjectAware;
use PDepend\Source\AST\ASTArtifact;

/**
 * Dummy implementation of an analyzer.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class AnalyzerNodeAndProjectAwareDummy implements AnalyzerNodeAware, AnalyzerProjectAware
{
    /**
     * Dummy project metrics.
     *
     * @var array(string=>mixed)
     */
    protected $projectMetrics = null;

    /**
     * Dummy node metrics.
     *
     * @var array(string=>array)
     */
    protected $nodeMetrics = null;

    /**
     * Constructs a new analyzer dummy instance.
     *
     * @param array(string=>mixed) $projectMetrics Dummy project metrics.
     * @param array(string=>array) $nodeMetrics    Dummy node metrics.
     */
    public function __construct(array $projectMetrics = array(), array $nodeMetrics = array())
    {
        $this->projectMetrics = $projectMetrics;
        $this->nodeMetrics    = $nodeMetrics;
    }

    /**
     * Adds a listener to this analyzer.
     *
     * @param AnalyzerListener $listener The listener instance.
     * @return void
     */
    public function addAnalyzeListener(AnalyzerListener $listener) {
    }

    /**
     * Removes the listener from this analyzer.
     *
     * @param \PDepend\Metrics\AnalyzerListener $listener The listener instance.
     * @return void
     */
    public function removeAnalyzeListener(AnalyzerListener $listener) {
    }

    /**
     * Processes all {@link \PDepend\Source\AST\ASTNamespace} code nodes.
     *
     * @param \PDepend\Source\AST\ASTNamespace[] $namespaces
     * @return void
     */
    public function analyze($namespaces)
    {
    }

    /**
     * By default all analyzers are enabled. Overwrite this method to provide
     * state based disabling/enabling.
     *
     * @return boolean
     * @since 0.9.10
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * Returns the project metrics.
     *
     * @return array(string=>mixed)
     */
    public function getProjectMetrics()
    {
        return $this->projectMetrics;
    }

    /**
     * Returns an array with metrics for the requested node.
     *
     * @param \PDepend\Source\AST\ASTArtifact $artifact
     * @return array(string=>mixed)
     */
    public function getNodeMetrics(ASTArtifact $artifact)
    {
        return $this->nodeMetrics;
    }
}
