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

use InvalidArgumentException;
use PDepend\Metrics\AbstractAnalyzer;
use PDepend\Metrics\AggregateAnalyzer;
use PDepend\Metrics\Analyzer;
use PDepend\Metrics\AnalyzerFilterAware;
use PDepend\Metrics\AnalyzerNodeAware;
use PDepend\Source\AST\AbstractASTClassOrInterface;
use PDepend\Source\AST\AbstractASTType;
use PDepend\Source\AST\ASTArtifact;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTEnum;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\AST\ASTProperty;
use PDepend\Source\AST\ASTTrait;
use RuntimeException;

/**
 * Generates some class level based metrics. This analyzer is based on the
 * metrics specified in the following document.
 *
 * http://www.aivosto.com/project/help/pm-oo-misc.html
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class ClassLevelAnalyzer extends AbstractAnalyzer implements AggregateAnalyzer, AnalyzerFilterAware, AnalyzerNodeAware
{
    /** Metrics provided by the analyzer implementation. */
    public const M_IMPLEMENTED_INTERFACES = 'impl',
        M_CLASS_INTERFACE_SIZE = 'cis',
        M_CLASS_SIZE = 'csz',
        M_NUMBER_OF_PUBLIC_METHODS = 'npm',
        M_PROPERTIES = 'vars',
        M_PROPERTIES_INHERIT = 'varsi',
        M_PROPERTIES_NON_PRIVATE = 'varsnp',
        M_WEIGHTED_METHODS = 'wmc',
        M_WEIGHTED_METHODS_INHERIT = 'wmci',
        M_WEIGHTED_METHODS_NON_PRIVATE = 'wmcnp';

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

    /** The internal used cyclomatic complexity analyzer. */
    private CyclomaticComplexityAnalyzer $cyclomaticAnalyzer;

    /**
     * Processes all {@link ASTNamespace} code nodes.
     *
     * @throws RuntimeException
     */
    public function analyze($namespaces): void
    {
        if (!isset($this->nodeMetrics)) {
            // First check for the require cc analyzer
            if (!isset($this->cyclomaticAnalyzer)) {
                throw new RuntimeException('Missing required CC analyzer.');
            }

            $this->fireStartAnalyzer();

            $this->cyclomaticAnalyzer->analyze($namespaces);

            // Init node metrics
            $this->nodeMetrics = [];

            // Visit all nodes
            foreach ($namespaces as $namespace) {
                $this->dispatch($namespace);
            }

            $this->fireEndAnalyzer();
        }
    }

    /**
     * This method must return an <b>array</b> of class names for required
     * analyzers.
     *
     * @return array<string>
     */
    public function getRequiredAnalyzers(): array
    {
        return [CyclomaticComplexityAnalyzer::class];
    }

    /**
     * Adds a required sub analyzer.
     *
     * @param Analyzer $analyzer The sub analyzer instance.
     * @throws InvalidArgumentException
     */
    public function addAnalyzer(Analyzer $analyzer): void
    {
        if ($analyzer instanceof CyclomaticComplexityAnalyzer) {
            $this->cyclomaticAnalyzer = $analyzer;
        } else {
            throw new InvalidArgumentException('CC Analyzer required.');
        }
    }

    /**
     * This method will return an <b>array</b> with all generated metric values
     * for the given <b>$node</b>. If there are no metrics for the requested
     * node, this method will return an empty <b>array</b>.
     *
     * @return array<string, float|int>
     */
    public function getNodeMetrics(ASTArtifact $artifact): array
    {
        return $this->nodeMetrics[$artifact->getId()] ?? [];
    }

    /**
     * Visits a class node.
     */
    public function visitClass(ASTClass $class): void
    {
        $this->fireStartClass($class);
        $this->calculateAbstractASTClassOrInterfaceMetrics($class);
        $this->fireEndClass($class);
    }

    /**
     * Visits a code interface object.
     */
    public function visitInterface(ASTInterface $interface): void
    {
        // Empty visit method, we don't want interface metrics
    }

    /**
     * Visits a trait node.
     *
     * @since  1.0.0
     */
    public function visitTrait(ASTTrait $trait): void
    {
        $this->fireStartTrait($trait);

        $wmci = $this->calculateWmciForTrait($trait);

        $this->nodeMetrics[$trait->getId()] = [
            self::M_IMPLEMENTED_INTERFACES => 0,
            self::M_CLASS_INTERFACE_SIZE => 0,
            self::M_CLASS_SIZE => 0,
            self::M_NUMBER_OF_PUBLIC_METHODS => 0,
            self::M_PROPERTIES => 0,
            self::M_PROPERTIES_INHERIT => 0,
            self::M_PROPERTIES_NON_PRIVATE => 0,
            self::M_WEIGHTED_METHODS => 0,
            self::M_WEIGHTED_METHODS_INHERIT => $wmci,
            self::M_WEIGHTED_METHODS_NON_PRIVATE => 0,
        ];

        foreach ($trait->getProperties() as $property) {
            $this->dispatch($property);
        }
        foreach ($trait->getMethods() as $method) {
            $this->dispatch($method);
        }

        $this->fireEndTrait($trait);
    }

    /**
     * Visits a enum node.
     *
     * @since  2.12.1
     */
    public function visitEnum(ASTEnum $enum): void
    {
        $this->fireStartEnum($enum);
        $this->calculateAbstractASTClassOrInterfaceMetrics($enum);
        $this->fireEndEnum($enum);
    }

    /**
     * Visits a method node.
     *
     * @throws RuntimeException
     */
    public function visitMethod(ASTMethod $method): void
    {
        $this->fireStartMethod($method);

        $parent = $method->getParent();
        if (!$parent) {
            throw new RuntimeException('Method has no parent.');
        }
        $id = $parent->getId();

        $ccn = $this->cyclomaticAnalyzer->getCcn2($method);

        // Increment Weighted Methods Per Class(WMC) value
        $this->nodeMetrics[$id][self::M_WEIGHTED_METHODS] += $ccn;
        // Increment Class Size(CSZ) value
        ++$this->nodeMetrics[$id][self::M_CLASS_SIZE];

        // Increment Non Private values
        if ($method->isPublic()) {
            ++$this->nodeMetrics[$id][self::M_NUMBER_OF_PUBLIC_METHODS];
            // Increment Non Private WMC value
            $this->nodeMetrics[$id][self::M_WEIGHTED_METHODS_NON_PRIVATE] += $ccn;
            // Increment Class Interface Size(CIS) value
            ++$this->nodeMetrics[$id][self::M_CLASS_INTERFACE_SIZE];
        }

        $this->fireEndMethod($method);
    }

    /**
     * Visits a property node.
     */
    public function visitProperty(ASTProperty $property): void
    {
        $this->fireStartProperty($property);

        $id = $property->getDeclaringClass()->getId();

        // Increment VARS value
        ++$this->nodeMetrics[$id][self::M_PROPERTIES];
        // Increment Class Size(CSZ) value
        ++$this->nodeMetrics[$id][self::M_CLASS_SIZE];

        // Increment Non Private values
        if ($property->isPublic()) {
            // Increment Non Private VARS value
            ++$this->nodeMetrics[$id][self::M_PROPERTIES_NON_PRIVATE];
            // Increment Class Interface Size(CIS) value
            ++$this->nodeMetrics[$id][self::M_CLASS_INTERFACE_SIZE];
        }

        $this->fireEndProperty($property);
    }

    /**
     * Calculates the Variables Inheritance of a class metric, this method only
     * counts protected and public properties of parent classes.
     *
     * @param ASTClass|ASTEnum|ASTTrait $class The context class instance.
     */
    private function calculateVarsi(ASTClass|ASTEnum|ASTTrait $class): int
    {
        // List of properties, this method only counts not overwritten properties
        $properties = [];
        // Collect all properties of the context class
        foreach ($class->getProperties() as $prop) {
            $properties[$prop->getImage()] = true;
        }

        foreach ($class->getParentClasses() as $parent) {
            foreach ($parent->getProperties() as $prop) {
                if (!$prop->isPrivate() && !isset($properties[$prop->getImage()])) {
                    $properties[$prop->getImage()] = true;
                }
            }
        }

        return count($properties);
    }

    /**
     * Calculates the Weight Method Per Class metric, this method only counts
     * protected and public methods of parent classes.
     */
    private function calculateWmciForClass(AbstractASTClassOrInterface $class): int
    {
        $ccn = $this->calculateWmci($class);

        foreach ($class->getParentClasses() as $parent) {
            foreach ($parent->getMethods() as $method) {
                if ($method->isPrivate()) {
                    continue;
                }
                if (isset($ccn[$name = $method->getImage()])) {
                    continue;
                }
                $ccn[$name] = $this->cyclomaticAnalyzer->getCcn2($method);
            }
        }

        return array_sum($ccn);
    }

    /**
     * Calculates the Weight Method Per Class metric for a trait.
     *
     * @since  1.0.6
     */
    private function calculateWmciForTrait(ASTTrait $trait): int
    {
        return array_sum($this->calculateWmci($trait));
    }

    /**
     * Calculates the Weight Method Per Class metric.
     *
     * @return int[]
     * @since  1.0.6
     */
    private function calculateWmci(AbstractASTType $type): array
    {
        $ccn = [];

        foreach ($type->getMethods() as $method) {
            $ccn[$method->getImage()] = $this->cyclomaticAnalyzer->getCcn2($method);
        }

        return $ccn;
    }

    private function calculateAbstractASTClassOrInterfaceMetrics(ASTClass|ASTEnum|ASTTrait $class): void
    {
        $impl = count($class->getInterfaces());
        $varsi = $this->calculateVarsi($class);
        $wmci = $this->calculateWmciForClass($class);

        $this->nodeMetrics[$class->getId()] = [
            self::M_IMPLEMENTED_INTERFACES => $impl,
            self::M_CLASS_INTERFACE_SIZE => 0,
            self::M_CLASS_SIZE => 0,
            self::M_NUMBER_OF_PUBLIC_METHODS => 0,
            self::M_PROPERTIES => 0,
            self::M_PROPERTIES_INHERIT => $varsi,
            self::M_PROPERTIES_NON_PRIVATE => 0,
            self::M_WEIGHTED_METHODS => 0,
            self::M_WEIGHTED_METHODS_INHERIT => $wmci,
            self::M_WEIGHTED_METHODS_NON_PRIVATE => 0,
        ];

        if ($class instanceof ASTClass) {
            foreach ($class->getProperties() as $property) {
                $this->dispatch($property);
            }
        }

        foreach ($class->getMethods() as $method) {
            $this->dispatch($method);
        }
    }
}
