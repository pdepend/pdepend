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
use RuntimeException;

/**
 * This visitor generates the metrics for the analyzed namespaces.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class ClassDependencyAnalyzer extends AbstractAnalyzer
{
    /** Metrics provided by the analyzer implementation. */
    private const
        M_AFFERENT_COUPLING = 'ca',
        M_EFFERENT_COUPLING = 'ce';

    /** @var array<string, AbstractASTClassOrInterface> */
    private array $nodeSet = [];

    /** @var array<string, array<string, array<int, string>>> */
    private array $nodeRelations = [];

    /**
     * Hash with all calculated node metrics.
     *
     * @var array<string, array<string, int>>
     */
    private array $nodeMetrics;

    /**
     * Nodes in which the current analyzed class is used.
     *
     * @var array<string, array<int, AbstractASTClassOrInterface>>
     */
    private array $efferentNodes = [];

    /**
     * Nodes that is used by the current analyzed class.
     *
     * @var array<string, array<int, AbstractASTClassOrInterface>>
     */
    private array $afferentNodes = [];

    /**
     * Processes all {@link ASTNamespace} code nodes.
     */
    public function analyze($namespaces): void
    {
        if (!isset($this->nodeMetrics)) {
            $this->fireStartAnalyzer();

            $this->nodeRelations = [];
            $this->nodeMetrics = [];

            foreach ($namespaces as $namespace) {
                $this->dispatch($namespace);
            }

            $this->postProcess();

            $this->fireEndAnalyzer();
        }
    }

    /**
     * Returns an array of all afferent nodes.
     *
     * @return array<int, AbstractASTClassOrInterface>
     */
    public function getAfferents(AbstractASTArtifact $node): array
    {
        $afferents = [];
        if (isset($this->afferentNodes[$node->getId()])) {
            $afferents = $this->afferentNodes[$node->getId()];
        }

        return $afferents;
    }

    /**
     * Returns an array of all efferent nodes.
     *
     * @return array<int, AbstractASTClassOrInterface>
     */
    public function getEfferents(AbstractASTArtifact $node): array
    {
        $efferents = [];
        if (isset($this->efferentNodes[$node->getId()])) {
            $efferents = $this->efferentNodes[$node->getId()];
        }

        return $efferents;
    }

    /**
     * Visits a method node.
     *
     * @throws RuntimeException
     */
    public function visitMethod(ASTMethod $method): void
    {
        $this->fireStartMethod($method);

        $type = $method->getParent();
        if (!$type) {
            throw new RuntimeException('Method does not have a parent.');
        }
        foreach ($method->getDependencies() as $dependency) {
            $this->collectDependencies($type, $dependency);
        }

        $this->fireEndMethod($method);
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
        foreach ($type->getDependencies() as $dependency) {
            $this->collectDependencies($type, $dependency);
        }

        foreach ($type->getMethods() as $method) {
            $this->dispatch($method);
        }
    }

    /**
     * Collects the dependencies between the two given classes.
     */
    private function collectDependencies(AbstractASTClassOrInterface $typeA, AbstractASTClassOrInterface $typeB): void
    {
        $idA = $typeA->getId();
        $idB = $typeB->getId();

        if ($idB === $idA) {
            return;
        }

        // Create a container for this dependency
        $this->initTypeMetric($typeA);
        $this->initTypeMetric($typeB);

        if (!in_array($idB, $this->nodeRelations[$idA][self::M_EFFERENT_COUPLING], true)) {
            $this->nodeRelations[$idA][self::M_EFFERENT_COUPLING][] = $idB;
            $this->nodeRelations[$idB][self::M_AFFERENT_COUPLING][] = $idA;
        }
    }

    /**
     * Initializes the node metric record for the given <b>$type</b>.
     */
    protected function initTypeMetric(AbstractASTClassOrInterface $type): void
    {
        $id = $type->getId();

        if (!isset($this->nodeRelations[$id])) {
            $this->nodeSet[$id] = $type;

            $this->nodeRelations[$id] = [
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
        foreach ($this->nodeRelations as $id => $metrics) {
            $this->afferentNodes[$id] = [];
            foreach ($metrics[self::M_AFFERENT_COUPLING] as $caId) {
                $this->afferentNodes[$id][] = $this->nodeSet[$caId];
            }

            $this->efferentNodes[$id] = [];
            foreach ($metrics[self::M_EFFERENT_COUPLING] as $ceId) {
                $this->efferentNodes[$id][] = $this->nodeSet[$ceId];
            }

            $afferent = count($metrics[self::M_AFFERENT_COUPLING]);
            $efferent = count($metrics[self::M_EFFERENT_COUPLING]);

            $this->nodeMetrics[$id][self::M_AFFERENT_COUPLING] = $afferent;
            $this->nodeMetrics[$id][self::M_EFFERENT_COUPLING] = $efferent;
        }
    }

    /**
     * Collects a single cycle that is reachable by this namespace. All namespaces
     * that are part of the cylce are stored in the given <b>$list</b> array.
     *
     * @param AbstractASTArtifact[] $list
     * @return bool If this method detects a cycle the return value is <b>true</b>
     *              otherwise this method will return <b>false</b>.
     */
    protected function collectCycle(array &$list, AbstractASTArtifact $node): bool
    {
        if (in_array($node, $list, true)) {
            $list[] = $node;

            return true;
        }

        $list[] = $node;

        foreach ($this->getEfferents($node) as $efferent) {
            if ($this->collectCycle($list, $efferent)) {
                return true;
            }
        }

        if (is_int($idx = array_search($node, $list, true))) {
            unset($list[$idx]);
        }

        return false;
    }
}
