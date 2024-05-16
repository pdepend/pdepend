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
use PDepend\Metrics\AnalyzerFilterAware;
use PDepend\Metrics\AnalyzerNodeAware;
use PDepend\Metrics\AnalyzerProjectAware;
use PDepend\Source\AST\ASTArtifact;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTNamespace;

/**
 * This analyzer provides two project related inheritance metrics.
 *
 * <b>ANDC - Average Number of Derived Classes</b>: The average number of direct
 * subclasses of a class. This metric only covers classes in the analyzed system,
 * no library or environment classes are covered.
 *
 * <b>AHH - Average Hierarchy Height</b>: The computed average of all inheritance
 * trees within the analyzed system, external classes or interfaces are ignored.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class InheritanceAnalyzer extends AbstractAnalyzer implements
    AnalyzerFilterAware,
    AnalyzerNodeAware,
    AnalyzerProjectAware
{
    /** Metrics provided by the analyzer implementation. */
    private const
        M_AVERAGE_NUMBER_DERIVED_CLASSES = 'andc',
        M_AVERAGE_HIERARCHY_HEIGHT = 'ahh',
        M_DEPTH_OF_INHERITANCE_TREE = 'dit',
        M_NUMBER_OF_ADDED_METHODS = 'noam',
        M_NUMBER_OF_OVERWRITTEN_METHODS = 'noom',
        M_NUMBER_OF_DERIVED_CLASSES = 'nocc',
        M_MAXIMUM_INHERITANCE_DEPTH = 'maxDIT';

    /**
     * Contains the max inheritance depth for all root classes within the
     * analyzed system. The array size is equal to the number of analyzed root
     * classes.
     *
     * @var array<int>
     */
    private $rootClasses = [];

    /**
     * The maximum depth of inheritance tree value within the analyzed source code.
     *
     * @var int
     */
    private $maxDIT = 0;

    /**
     * The average number of derived classes.
     *
     * @var float
     */
    private $andc = 0;

    /**
     * The average hierarchy height.
     *
     * @var float
     */
    private $ahh = 0;

    /**
     * Total number of classes.
     *
     * @var int
     */
    private $numberOfClasses = 0;

    /**
     * Total number of derived classes.
     *
     * @var int
     */
    private $numberOfDerivedClasses = 0;

    /**
     * Metrics calculated for a single source node.
     *
     * @var array<string, array<string, int>>
     */
    private array $nodeMetrics;

    /**
     * This method will return an <b>array</b> with all generated metric values
     * for the given <b>$node</b>. If there are no metrics for the requested
     * node, this method will return an empty <b>array</b>.
     *
     * @return array<string, int>
     */
    public function getNodeMetrics(ASTArtifact $artifact): array
    {
        return $this->nodeMetrics[$artifact->getId()] ?? [];
    }

    /**
     * Provides the project summary as an <b>array</b>.
     *
     * <code>
     * array(
     *     'andc'  =>  0.73,
     *     'ahh'   =>  0.56
     * )
     * </code>
     *
     * @return array<string, float|int>
     */
    public function getProjectMetrics(): array
    {
        return [
            self::M_AVERAGE_NUMBER_DERIVED_CLASSES => $this->andc,
            self::M_AVERAGE_HIERARCHY_HEIGHT => $this->ahh,
            self::M_MAXIMUM_INHERITANCE_DEPTH => $this->maxDIT,
        ];
    }

    /**
     * Processes all {@link ASTNamespace} code nodes.
     */
    public function analyze($namespaces): void
    {
        if (!isset($this->nodeMetrics)) {
            $this->nodeMetrics = [];

            $this->fireStartAnalyzer();
            $this->doAnalyze($namespaces);
            $this->fireEndAnalyzer();
        }
    }

    /**
     * Calculates several inheritance related metrics for the given source
     * namespaces.
     *
     * @param ASTArtifactList<ASTNamespace> $namespaces
     * @since  0.9.10
     */
    private function doAnalyze($namespaces): void
    {
        foreach ($namespaces as $namespace) {
            $namespace->accept($this);
        }

        if ($this->numberOfClasses > 0) {
            $this->andc = $this->numberOfDerivedClasses / $this->numberOfClasses;
        }
        if (($count = count($this->rootClasses)) > 0) {
            $this->ahh = array_sum($this->rootClasses) / $count;
        }
    }

    /**
     * Visits a class node.
     */
    public function visitClass(ASTClass $class): void
    {
        if (!$class->isUserDefined()) {
            return;
        }

        $this->fireStartClass($class);

        $this->initNodeMetricsForClass($class);

        $this->calculateNumberOfDerivedClasses($class);
        $this->calculateNumberOfAddedAndOverwrittenMethods($class);
        $this->calculateDepthOfInheritanceTree($class);

        $this->fireEndClass($class);
    }

    /**
     * Calculates the number of derived classes.
     *
     * @since  0.9.5
     */
    private function calculateNumberOfDerivedClasses(ASTClass $class): void
    {
        $id = $class->getId();
        $parentClass = $class->getParentClass();
        if ($parentClass !== null && $parentClass->isUserDefined()) {
            $id = $parentClass->getId();

            ++$this->numberOfDerivedClasses;
            ++$this->nodeMetrics[$id][self::M_NUMBER_OF_DERIVED_CLASSES];
        }
    }

    /**
     * Calculates the maximum HIT for the given class.
     *
     * @since  0.9.10
     */
    private function calculateDepthOfInheritanceTree(ASTClass $class): void
    {
        $dit = 0;
        $id = $class->getId();
        $root = $class->getId();

        foreach ($class->getParentClasses() as $parent) {
            if (!$parent->isUserDefined()) {
                ++$dit;
            }
            ++$dit;
            $root = $parent->getId();
        }

        // Collect max dit value
        $this->maxDIT = max($this->maxDIT, $dit);

        if (empty($this->rootClasses[$root]) || $this->rootClasses[$root] < $dit) {
            $this->rootClasses[$root] = $dit;
        }
        $this->nodeMetrics[$id][self::M_DEPTH_OF_INHERITANCE_TREE] = $dit;
    }

    /**
     * Calculates two metrics. The number of added methods and the number of
     * overwritten methods.
     *
     * @since  0.9.10
     */
    private function calculateNumberOfAddedAndOverwrittenMethods(ASTClass $class): void
    {
        $parentClass = $class->getParentClass();
        if ($parentClass === null) {
            return;
        }

        $parentMethodNames = [];
        foreach ($parentClass->getAllMethods() as $method) {
            $parentMethodNames[$method->getImage()] = $method->isAbstract();
        }

        $numberOfAddedMethods = 0;
        $numberOfOverwrittenMethods = 0;

        foreach ($class->getAllMethods() as $method) {
            if ($method->getParent() !== $class) {
                continue;
            }

            if (isset($parentMethodNames[$method->getImage()])) {
                if (!$parentMethodNames[$method->getImage()]) {
                    ++$numberOfOverwrittenMethods;
                }
            } else {
                ++$numberOfAddedMethods;
            }
        }

        $id = $class->getId();

        $this->nodeMetrics[$id][self::M_NUMBER_OF_ADDED_METHODS] = $numberOfAddedMethods;
        $this->nodeMetrics[$id][self::M_NUMBER_OF_OVERWRITTEN_METHODS] = $numberOfOverwrittenMethods;
    }

    /**
     * Initializes a empty metric container for the given class node.
     *
     * @since  0.9.10
     */
    private function initNodeMetricsForClass(ASTClass $class): void
    {
        $id = $class->getId();
        if (isset($this->nodeMetrics[$id])) {
            return;
        }

        ++$this->numberOfClasses;

        $this->nodeMetrics[$id] = [
            self::M_DEPTH_OF_INHERITANCE_TREE => 0,
            self::M_NUMBER_OF_ADDED_METHODS => 0,
            self::M_NUMBER_OF_DERIVED_CLASSES => 0,
            self::M_NUMBER_OF_OVERWRITTEN_METHODS => 0,
        ];

        foreach ($class->getParentClasses() as $parent) {
            $this->initNodeMetricsForClass($parent);
        }
    }
}
