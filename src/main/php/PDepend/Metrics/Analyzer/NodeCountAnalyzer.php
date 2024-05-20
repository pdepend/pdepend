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
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;

/**
 * This analyzer collects different count metrics for code artifacts like
 * classes, methods, functions or namespaces.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class NodeCountAnalyzer extends AbstractAnalyzer implements AnalyzerFilterAware, AnalyzerNodeAware, AnalyzerProjectAware
{
    /** Metrics provided by the analyzer implementation. */
    private const
        M_NUMBER_OF_PACKAGES = 'nop',
        M_NUMBER_OF_CLASSES = 'noc',
        M_NUMBER_OF_INTERFACES = 'noi',
        M_NUMBER_OF_METHODS = 'nom',
        M_NUMBER_OF_FUNCTIONS = 'nof';

    /** Number Of Packages. */
    private int $nop = 0;

    /** Number Of Classes. */
    private int $noc = 0;

    /** Number Of Interfaces. */
    private int $noi = 0;

    /** Number Of Methods. */
    private int $nom = 0;

    /** Number Of Functions. */
    private int $nof = 0;

    /**
     * Collected node metrics
     *
     * @var array<string, array<string, int>>
     */
    private array $nodeMetrics;

    /**
     * This method will return an <b>array</b> with all generated metric values
     * for the given <b>$node</b> instance. If there are no metrics for the
     * requested node, this method will return an empty <b>array</b>.
     *
     * <code>
     * array(
     *     'noc'  =>  23,
     *     'nom'  =>  17,
     *     'nof'  =>  42
     * )
     * </code>
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
     *     'nop'  =>  23,
     *     'noc'  =>  17,
     *     'noi'  =>  23,
     *     'nom'  =>  42,
     *     'nof'  =>  17
     * )
     * </code>
     *
     * @return array<string, int>
     */
    public function getProjectMetrics(): array
    {
        return [
            self::M_NUMBER_OF_PACKAGES => $this->nop,
            self::M_NUMBER_OF_CLASSES => $this->noc,
            self::M_NUMBER_OF_INTERFACES => $this->noi,
            self::M_NUMBER_OF_METHODS => $this->nom,
            self::M_NUMBER_OF_FUNCTIONS => $this->nof,
        ];
    }

    /**
     * Processes all {@link ASTNamespace} code nodes.
     */
    public function analyze($namespaces): void
    {
        // Check for previous run
        if (!isset($this->nodeMetrics)) {
            $this->fireStartAnalyzer();

            $this->nodeMetrics = [];

            foreach ($namespaces as $namespace) {
                $this->dispatch($namespace);
            }

            $this->fireEndAnalyzer();
        }
    }

    /**
     * Visits a class node.
     */
    public function visitClass(ASTClass $class): void
    {
        if (false === $class->isUserDefined()) {
            return;
        }

        $this->fireStartClass($class);

        // Update global class count
        ++$this->noc;

        $id = $class->getNamespace()?->getId();
        ++$this->nodeMetrics[(string) $id][self::M_NUMBER_OF_CLASSES];

        $this->nodeMetrics[$class->getId()] = [
            self::M_NUMBER_OF_METHODS => 0,
        ];

        foreach ($class->getMethods() as $method) {
            $this->dispatch($method);
        }

        $this->fireEndClass($class);
    }

    /**
     * Visits a function node.
     */
    public function visitFunction(ASTFunction $function): void
    {
        $this->fireStartFunction($function);

        // Update global function count
        ++$this->nof;

        $id = $function->getNamespace()?->getId();
        ++$this->nodeMetrics[(string) $id][self::M_NUMBER_OF_FUNCTIONS];

        $this->fireEndFunction($function);
    }

    /**
     * Visits a code interface object.
     */
    public function visitInterface(ASTInterface $interface): void
    {
        if (false === $interface->isUserDefined()) {
            return;
        }

        $this->fireStartInterface($interface);

        // Update global class count
        ++$this->noi;

        $id = $interface->getNamespace()?->getId();
        ++$this->nodeMetrics[(string) $id][self::M_NUMBER_OF_INTERFACES];

        $this->nodeMetrics[$interface->getId()] = [
            self::M_NUMBER_OF_METHODS => 0,
        ];

        foreach ($interface->getMethods() as $method) {
            $this->dispatch($method);
        }

        $this->fireEndInterface($interface);
    }

    /**
     * Visits a method node.
     */
    public function visitMethod(ASTMethod $method): void
    {
        $this->fireStartMethod($method);

        // Update global method count
        ++$this->nom;

        $parent = $method->getParent();

        // Update parent class or interface
        $parentId = $parent?->getId();
        ++$this->nodeMetrics[(string) $parentId][self::M_NUMBER_OF_METHODS];

        $id = $parent?->getNamespace()?->getId();
        ++$this->nodeMetrics[(string) $id][self::M_NUMBER_OF_METHODS];

        $this->fireEndMethod($method);
    }

    /**
     * Visits a namespace node.
     */
    public function visitNamespace(ASTNamespace $namespace): void
    {
        $this->fireStartNamespace($namespace);

        ++$this->nop;

        $this->nodeMetrics[$namespace->getId()] = [
            self::M_NUMBER_OF_CLASSES => 0,
            self::M_NUMBER_OF_INTERFACES => 0,
            self::M_NUMBER_OF_METHODS => 0,
            self::M_NUMBER_OF_FUNCTIONS => 0,
        ];

        foreach ($namespace->getClasses() as $class) {
            $this->dispatch($class);
        }
        foreach ($namespace->getInterfaces() as $interface) {
            $this->dispatch($interface);
        }
        foreach ($namespace->getFunctions() as $function) {
            $this->dispatch($function);
        }

        $this->fireEndNamespace($namespace);
    }
}
