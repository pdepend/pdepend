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

use PDepend\Metrics\AbstractAnalyzer;
use PDepend\Metrics\AggregateAnalyzer;
use PDepend\Metrics\Analyzer;
use PDepend\Metrics\AnalyzerNodeAware;
use PDepend\Source\AST\AbstractASTCallable;
use PDepend\Source\AST\ASTArtifact;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Util\Coverage\Factory;
use PDepend\Util\Coverage\Report;

/**
 * This analyzer calculates the C.R.A.P. index for methods an functions when a
 * clover coverage report was supplied. This report can be supplied by using the
 * command line option <b>--coverage-report=</b>.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class CrapIndexAnalyzer extends AbstractAnalyzer implements AggregateAnalyzer, AnalyzerNodeAware
{
    /**
     * The report option name.
     */
    public const REPORT_OPTION = 'coverage-report';
    /**
     * Metrics provided by the analyzer implementation.
     */
    private const
        M_CRAP_INDEX = 'crap',
        M_COVERAGE = 'cov';

    /**
     * Calculated crap metrics.
     *
     * @var array<string, array<string, float>>
     */
    private $metrics = null;

    /**
     * The coverage report instance representing the supplied coverage report
     * file.
     *
     * @var Report
     */
    private $report = null;

    /**
     * @var CyclomaticComplexityAnalyzer
     */
    private $ccnAnalyzer = null;

    /**
     * Returns <b>true</b> when this analyzer is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return isset($this->options[self::REPORT_OPTION]);
    }

    /**
     * Returns the calculated metrics for the given node or an empty <b>array</b>
     * when no metrics exist for the given node.
     *
     * @return array<string, float>
     */
    public function getNodeMetrics(ASTArtifact $artifact)
    {
        if (isset($this->metrics[$artifact->getId()])) {
            return $this->metrics[$artifact->getId()];
        }
        return [];
    }

    /**
     * Returns an array with analyzer class names that are required by the crap
     * index analyzers.
     *
     * @return array<string>
     */
    public function getRequiredAnalyzers()
    {
        return ['PDepend\\Metrics\\Analyzer\\CyclomaticComplexityAnalyzer'];
    }

    /**
     * Adds an analyzer that this analyzer depends on.
     *
     * @param CyclomaticComplexityAnalyzer $analyzer
     */
    public function addAnalyzer(Analyzer $analyzer): void
    {
        $this->ccnAnalyzer = $analyzer;
    }

    /**
     * Performs the crap index analysis.
     */
    public function analyze($namespaces): void
    {
        if ($this->isEnabled() && $this->metrics === null) {
            $this->doAnalyze($namespaces);
        }
    }

    /**
     * Performs the crap index analysis.
     *
     * @param ASTArtifactList<ASTNamespace> $namespaces
     */
    private function doAnalyze($namespaces): void
    {
        $this->metrics = [];

        $this->ccnAnalyzer->analyze($namespaces);

        $this->fireStartAnalyzer();

        foreach ($namespaces as $namespace) {
            $namespace->accept($this);
        }

        $this->fireEndAnalyzer();
    }

    /**
     * Visits the given method.
     */
    public function visitMethod(ASTMethod $method): void
    {
        if ($method->isAbstract() === false) {
            $this->visitCallable($method);
        }
    }

    /**
     * Visits the given function.
     */
    public function visitFunction(ASTFunction $function): void
    {
        $this->visitCallable($function);
    }

    /**
     * Visits the given callable instance.
     */
    private function visitCallable(AbstractASTCallable $callable): void
    {
        $this->metrics[$callable->getId()] = [
            self::M_CRAP_INDEX => $this->calculateCrapIndex($callable),
            self::M_COVERAGE => $this->calculateCoverage($callable),
        ];
    }

    /**
     * Calculates the crap index for the given callable.
     *
     * @return float
     */
    private function calculateCrapIndex(AbstractASTCallable $callable)
    {
        $report = $this->createOrReturnCoverageReport();

        $complexity = $this->ccnAnalyzer->getCcn2($callable);
        $coverage = $report->getCoverage($callable);

        if ($coverage === 0.0) {
            return $complexity ** 2 + $complexity;
        } elseif ($coverage > 99.5) {
            return $complexity;
        }
        return $complexity ** 2 * (1 - $coverage / 100) ** 3 + $complexity;
    }

    /**
     * Calculates the code coverage for the given callable object.
     *
     * @return float
     */
    private function calculateCoverage(AbstractASTCallable $callable)
    {
        return $this->createOrReturnCoverageReport()->getCoverage($callable);
    }

    /**
     * Returns a previously created report instance or creates a new report
     * instance.
     *
     * @return Report
     */
    private function createOrReturnCoverageReport()
    {
        if ($this->report === null) {
            $this->report = $this->createCoverageReport();
        }
        return $this->report;
    }

    /**
     * Creates a new coverage report instance.
     *
     * @return Report
     */
    private function createCoverageReport()
    {
        $factory = new Factory();
        return $factory->create($this->options['coverage-report']);
    }
}
