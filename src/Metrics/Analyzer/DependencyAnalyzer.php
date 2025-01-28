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
use PDepend\Source\AST\AbstractASTArtifact;
use PDepend\Source\AST\AbstractASTClassOrInterface;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;

/**
 * This visitor generates the metrics for the analyzed namespaces.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class DependencyAnalyzer extends AbstractAnalyzer
{
    /** Metrics provided by the analyzer implementation. */
    private const
        M_NUMBER_OF_CLASSES = 'tc',
        M_NUMBER_OF_CONCRETE_CLASSES = 'cc',
        M_NUMBER_OF_ABSTRACT_CLASSES = 'ac',
        M_AFFERENT_COUPLING = 'ca',
        M_EFFERENT_COUPLING = 'ce',
        M_ABSTRACTION = 'a',
        M_INSTABILITY = 'i',
        M_DISTANCE = 'd';

    /** @var array<string, ASTNamespace> */
    private array $nodeSet = [];

    /**
     * Hash with all calculated node metrics.
     *
     * <code>
     * array(
     *     '0375e305-885a-4e91-8b5c-e25bda005438'  =>  array(
     *         'loc'    =>  42,
     *         'ncloc'  =>  17,
     *         'cc'     =>  12
     *     ),
     *     'e60c22f0-1a63-4c40-893e-ed3b35b84d0b'  =>  array(
     *         'loc'    =>  42,
     *         'ncloc'  =>  17,
     *         'cc'     =>  12
     *     )
     * )
     * </code>
     *
     * @var array<string, array<string, int>>
     */
    private array $nodeMetrics;

    /** @var array<string, array<string, list<string>>> */
    private array $nodeCollector;

    /**
     * Nodes in which the current analyzed dependency is used.
     *
     * @var array<string, array<int, ASTNamespace>>
     */
    private array $efferentNodes = [];

    /**
     * Nodes that is used by the current analyzed node.
     *
     * @var array<string, array<int, ASTNamespace>>
     */
    private array $afferentNodes = [];

    /**
     * All collected cycles for the input code.
     *
     * <code>
     * array(
     *     <namespace-id> => array(
     *         ASTNamespace {},
     *         ASTNamespace {},
     *     ),
     *     <namespace-id> => array(
     *         ASTNamespace {},
     *         ASTNamespace {},
     *     ),
     * )
     * </code>
     *
     * @var array<string, array<int, AbstractASTArtifact>|null>
     */
    private array $collectedCycles = [];

    /**
     * Processes all {@link ASTNamespace} code nodes.
     */
    public function analyze($namespaces): void
    {
        if (!isset($this->nodeMetrics)) {
            $this->fireStartAnalyzer();

            $this->nodeMetrics = [];
            $this->nodeCollector = [];

            foreach ($namespaces as $namespace) {
                $this->dispatch($namespace);
            }

            $this->postProcess();

            $this->calculateAbstractness();
            $this->calculateInstability();
            $this->calculateDistance();

            $this->fireEndAnalyzer();
        }
    }

    /**
     * Returns the statistics for the requested node.
     *
     * @return array<string, int>
     */
    public function getStats(AbstractASTArtifact $node): array
    {
        $stats = [];
        if (isset($this->nodeMetrics[$node->getId()])) {
            $stats = $this->nodeMetrics[$node->getId()];
        }

        return $stats;
    }

    /**
     * Returns an array of all afferent nodes.
     *
     * @return AbstractASTArtifact[]
     */
    public function getAfferents(AbstractASTArtifact $node): array
    {
        $afferents = [];
        if (isset($this->afferentNodes[$node->getId()])) {
            $afferents = $this->afferentNodes[$node->getId()];
        }
        ksort($afferents);

        return $afferents;
    }

    /**
     * Returns an array of all efferent nodes.
     *
     * @return ASTNamespace[]
     */
    public function getEfferents(AbstractASTArtifact $node): array
    {
        $efferents = [];
        if (isset($this->efferentNodes[$node->getId()])) {
            $efferents = $this->efferentNodes[$node->getId()];
        }
        ksort($efferents);

        return $efferents;
    }

    /**
     * Returns an array of nodes that build a cycle for the requested node or it
     * returns <b>null</b> if no cycle exists .
     *
     * @param ASTNamespace $node
     * @return AbstractASTArtifact[]|null
     */
    public function getCycle(AbstractASTArtifact $node): ?array
    {
        if (array_key_exists($node->getId(), $this->collectedCycles)) {
            return $this->collectedCycles[$node->getId()];
        }

        $list = [];
        if ($this->collectCycle($list, $node)) {
            $this->collectedCycles[$node->getId()] = $list;
        } else {
            $this->collectedCycles[$node->getId()] = null;
        }

        return $this->collectedCycles[$node->getId()];
    }

    /**
     * Visits a method node.
     */
    public function visitMethod(ASTMethod $method): void
    {
        $this->fireStartMethod($method);

        $namespace = $method->getParent()?->getNamespace();
        if ($namespace) {
            foreach ($method->getDependencies() as $dependency) {
                $dependencyNamespace = $dependency->getNamespace();
                if ($dependencyNamespace) {
                    $this->collectDependencies($namespace, $dependencyNamespace);
                }
            }
        }

        $this->fireEndMethod($method);
    }

    /**
     * Visits a namespace node.
     */
    public function visitNamespace(ASTNamespace $namespace): void
    {
        $this->fireStartNamespace($namespace);

        $this->initNamespaceMetric($namespace);

        $this->nodeSet[$namespace->getId()] = $namespace;

        foreach ($namespace->getTypes() as $type) {
            $this->dispatch($type);
        }

        $this->fireEndNamespace($namespace);
    }

    /**
     * Visits a class node.
     */
    public function visitClass(ASTClass $class): void
    {
        $this->fireStartClass($class);
        $this->visitType($class);
        $this->fireEndClass($class);
    }

    /**
     * Visits an interface node.
     */
    public function visitInterface(ASTInterface $interface): void
    {
        $this->fireStartInterface($interface);
        $this->visitType($interface);
        $this->fireEndInterface($interface);
    }

    /**
     * Generic visit method for classes and interfaces. Both visit methods
     * delegate calls to this method.
     */
    protected function visitType(AbstractASTClassOrInterface $type): void
    {
        $namespace = $type->getNamespace();
        $id = (string) $namespace?->getId();

        // Increment total classes count
        ++$this->nodeMetrics[$id][self::M_NUMBER_OF_CLASSES];

        // Check for abstract or concrete class
        if ($type->isAbstract()) {
            ++$this->nodeMetrics[$id][self::M_NUMBER_OF_ABSTRACT_CLASSES];
        } else {
            ++$this->nodeMetrics[$id][self::M_NUMBER_OF_CONCRETE_CLASSES];
        }

        if ($namespace) {
            foreach ($type->getDependencies() as $dependency) {
                $dependencyNamespace = $dependency->getNamespace();
                if ($dependencyNamespace) {
                    $this->collectDependencies($namespace, $dependencyNamespace);
                }
            }
        }

        foreach ($type->getMethods() as $method) {
            $this->dispatch($method);
        }
    }

    /**
     * Collects the dependencies between the two given namespaces.
     */
    private function collectDependencies(ASTNamespace $namespaceA, ASTNamespace $namespaceB): void
    {
        $idA = $namespaceA->getId();
        $idB = $namespaceB->getId();

        if ($idB === $idA) {
            return;
        }

        // Create a container for this dependency
        $this->initNamespaceMetric($namespaceB);

        if (!in_array($idB, $this->nodeCollector[$idA][self::M_EFFERENT_COUPLING], true)) {
            $this->nodeCollector[$idA][self::M_EFFERENT_COUPLING][] = $idB;
            $this->nodeCollector[$idB][self::M_AFFERENT_COUPLING][] = $idA;
        }
    }

    /**
     * Initializes the node metric record for the given <b>$namespace</b>.
     */
    protected function initNamespaceMetric(ASTNamespace $namespace): void
    {
        $id = $namespace->getId();

        if (!isset($this->nodeMetrics[$id])) {
            $this->nodeSet[$id] = $namespace;

            $this->nodeMetrics[$id] = [
                self::M_NUMBER_OF_CLASSES => 0,
                self::M_NUMBER_OF_CONCRETE_CLASSES => 0,
                self::M_NUMBER_OF_ABSTRACT_CLASSES => 0,
                self::M_AFFERENT_COUPLING => 0,
                self::M_EFFERENT_COUPLING => 0,
                self::M_ABSTRACTION => 0,
                self::M_INSTABILITY => 0,
                self::M_DISTANCE => 0,
            ];

            $this->nodeCollector[$id] = [
                self::M_AFFERENT_COUPLING => [],
                self::M_EFFERENT_COUPLING => [],
            ];
        }
    }

    /**
     * Post processes all analyzed nodes.
     */
    protected function postProcess(): void
    {
        foreach ($this->nodeCollector as $id => $metrics) {
            $this->afferentNodes[$id] = [];
            foreach ($metrics[self::M_AFFERENT_COUPLING] as $caId) {
                $this->afferentNodes[$id][] = $this->nodeSet[$caId];
            }

            sort($this->afferentNodes[$id]);

            $this->efferentNodes[$id] = [];
            foreach ($metrics[self::M_EFFERENT_COUPLING] as $ceId) {
                $this->efferentNodes[$id][] = $this->nodeSet[$ceId];
            }

            sort($this->efferentNodes[$id]);

            $afferent = count($metrics[self::M_AFFERENT_COUPLING]);
            $efferent = count($metrics[self::M_EFFERENT_COUPLING]);

            $this->nodeMetrics[$id][self::M_AFFERENT_COUPLING] = $afferent;
            $this->nodeMetrics[$id][self::M_EFFERENT_COUPLING] = $efferent;
        }
    }

    /**
     * Calculates the abstractness for all analyzed nodes.
     */
    protected function calculateAbstractness(): void
    {
        foreach ($this->nodeMetrics as $id => $metrics) {
            if ($metrics[self::M_NUMBER_OF_CLASSES] !== 0) {
                $this->nodeMetrics[$id][self::M_ABSTRACTION] = (
                    $metrics[self::M_NUMBER_OF_ABSTRACT_CLASSES] /
                    $metrics[self::M_NUMBER_OF_CLASSES]
                );
            }
        }
    }

    /**
     * Calculates the instability for all analyzed nodes.
     */
    protected function calculateInstability(): void
    {
        foreach ($this->nodeMetrics as $id => $metrics) {
            // Count total incoming and outgoing dependencies
            $total = (
                $metrics[self::M_AFFERENT_COUPLING] +
                $metrics[self::M_EFFERENT_COUPLING]
            );

            if ($total !== 0) {
                $this->nodeMetrics[$id][self::M_INSTABILITY] = (
                    $metrics[self::M_EFFERENT_COUPLING] / $total
                );
            }
        }
    }

    /**
     * Calculates the distance to an optimal value.
     */
    protected function calculateDistance(): void
    {
        foreach ($this->nodeMetrics as $id => $metrics) {
            $this->nodeMetrics[$id][self::M_DISTANCE] = abs(
                ($metrics[self::M_ABSTRACTION] + $metrics[self::M_INSTABILITY]) - 1,
            );
        }
    }

    /**
     * Collects a single cycle that is reachable by this namespace. All namespaces
     * that are part of the cylce are stored in the given <b>$list</b> array.
     *
     * @param array<int, ASTNamespace> $list
     * @return bool If this method detects a cycle the return value is <b>true</b>
     *              otherwise this method will return <b>false</b>.
     */
    protected function collectCycle(array &$list, ASTNamespace $namespace): bool
    {
        if (in_array($namespace, $list, true)) {
            $list[] = $namespace;

            return true;
        }

        $list[] = $namespace;

        foreach ($this->getEfferents($namespace) as $efferent) {
            if ($this->collectCycle($list, $efferent)) {
                return true;
            }
        }

        if (is_int($idx = array_search($namespace, $list, true))) {
            unset($list[$idx]);
        }

        return false;
    }
}
