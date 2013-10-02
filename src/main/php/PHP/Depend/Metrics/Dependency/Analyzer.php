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
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

namespace PHP\Depend\Metrics\Dependency;

use PHP\Depend\Metrics\AbstractAnalyzer;
use PHP\Depend\Source\AST\AbstractASTArtifact;
use PHP\Depend\Source\AST\AbstractASTClassOrInterface;
use PHP\Depend\Source\AST\ASTArtifactList;
use PHP\Depend\Source\AST\ASTClass;
use PHP\Depend\Source\AST\ASTInterface;
use PHP\Depend\Source\AST\ASTMethod;
use PHP\Depend\Source\AST\ASTNamespace;

/**
 * This visitor generates the metrics for the analyzed packages.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
class Analyzer extends AbstractAnalyzer
{
    /**
     * Type of this analyzer class.
     */
    const CLAZZ = __CLASS__;

    /**
     * Metrics provided by the analyzer implementation.
     */
    const M_NUMBER_OF_CLASSES          = 'tc',
          M_NUMBER_OF_CONCRETE_CLASSES = 'cc',
          M_NUMBER_OF_ABSTRACT_CLASSES = 'ac',
          M_AFFERENT_COUPLING          = 'ca',
          M_EFFERENT_COUPLING          = 'ce',
          M_ABSTRACTION                = 'a',
          M_INSTABILITY                = 'i',
          M_DISTANCE                   = 'd';
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
     * @var array(string=>array)
     */
    private $nodeMetrics = null;

    protected $nodeSet = array();

    private $efferentNodes = array();

    private $afferentNodes = array();

    /**
     * All collected cycles for the input code.
     *
     * <code>
     * array(
     *     <package-uuid> => array(
     *         \PHP\Depend\Source\AST\ASTNamespace {},
     *         \PHP\Depend\Source\AST\ASTNamespace {},
     *     ),
     *     <package-uuid> => array(
     *         \PHP\Depend\Source\AST\ASTNamespace {},
     *         \PHP\Depend\Source\AST\ASTNamespace {},
     *     ),
     * )
     * </code>
     *
     * @var array(string=>array)
     */
    private $collectedCycles = array();

    /**
     * Processes all {@link \PHP\Depend\Source\AST\ASTNamespace} code nodes.
     *
     * @param \PHP\Depend\Source\AST\ASTArtifactList $namespaces
     * @return void
     */
    public function analyze(ASTArtifactList $namespaces)
    {
        if ($this->nodeMetrics === null) {

            $this->fireStartAnalyzer();

            $this->nodeMetrics = array();

            foreach ($namespaces as $package) {
                $package->accept($this);
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
     * @param \PHP\Depend\Source\AST\AbstractASTArtifact $node
     * @return array
     */
    public function getStats(AbstractASTArtifact $node)
    {
        $stats = array();
        if (isset($this->nodeMetrics[$node->getUuid()])) {
            $stats = $this->nodeMetrics[$node->getUuid()];
        }
        return $stats;
    }

    /**
     * Returns an array of all afferent nodes.
     *
     * @param \PHP\Depend\Source\AST\AbstractASTArtifact $node
     * @return \PHP\Depend\Source\AST\AbstractASTArtifact[]
     */
    public function getAfferents(AbstractASTArtifact $node)
    {
        $afferents = array();
        if (isset($this->afferentNodes[$node->getUuid()])) {
            $afferents = $this->afferentNodes[$node->getUuid()];
        }
        return $afferents;
    }

    /**
     * Returns an array of all efferent nodes.
     *
     * @param \PHP\Depend\Source\AST\AbstractASTArtifact $node
     * @return \PHP\Depend\Source\AST\AbstractASTArtifact[]
     */
    public function getEfferents(AbstractASTArtifact $node)
    {
        $efferents = array();
        if (isset($this->efferentNodes[$node->getUuid()])) {
            $efferents = $this->efferentNodes[$node->getUuid()];
        }
        return $efferents;
    }

    /**
     * Returns an array of nodes that build a cycle for the requested node or it
     * returns <b>null</b> if no cycle exists .
     *
     * @param \PHP\Depend\Source\AST\AbstractASTArtifact $node
     * @return \PHP\Depend\Source\AST\AbstractASTArtifact[]
     */
    public function getCycle(AbstractASTArtifact $node)
    {
        if (array_key_exists($node->getUuid(), $this->collectedCycles)) {
            return $this->collectedCycles[$node->getUuid()];
        }

        $list = array();
        if ($this->collectCycle($list, $node)) {
            $this->collectedCycles[$node->getUuid()] = $list;
        } else {
            $this->collectedCycles[$node->getUuid()] = null;
        }

        return $this->collectedCycles[$node->getUuid()];
    }

    /**
     * Visits a method node.
     *
     * @param \PHP\Depend\Source\AST\ASTMethod $method
     * @return void
     */
    public function visitMethod(ASTMethod $method)
    {
        $this->fireStartMethod($method);

        $package = $method->getParent()->getPackage();
        foreach ($method->getDependencies() as $dep) {
            $this->collectDependencies($package, $dep->getPackage());
        }

        $this->fireEndMethod($method);
    }

    /**
     * Visits a package node.
     *
     * @param PHP\Depend\Source\AST\ASTNamespace $namespace
     * @return void
     */
    public function visitNamespace(ASTNamespace $namespace)
    {
        $this->fireStartPackage($namespace);

        $this->initPackageMetric($namespace);

        $this->nodeSet[$namespace->getUuid()] = $namespace;

        foreach ($namespace->getTypes() as $type) {
            $type->accept($this);
        }

        $this->fireEndPackage($namespace);
    }

    /**
     * Visits a class node.
     *
     * @param \PHP\Depend\Source\AST\ASTClass $class
     * @return void
     */
    public function visitClass(ASTClass $class)
    {
        $this->fireStartClass($class);
        $this->visitType($class);
        $this->fireEndClass($class);
    }

    /**
     * Visits an interface node.
     *
     * @param \PHP\Depend\Source\AST\ASTInterface $interface
     * @return void
     */
    public function visitInterface(ASTInterface $interface)
    {
        $this->fireStartInterface($interface);
        $this->visitType($interface);
        $this->fireEndInterface($interface);
    }

    /**
     * Generic visit method for classes and interfaces. Both visit methods
     * delegate calls to this method.
     *
     * @param \PHP\Depend\Source\AST\AbstractASTClassOrInterface $type
     * @return void
     */
    protected function visitType(AbstractASTClassOrInterface $type)
    {
        // Get context package uuid
        $pkgUUID = $type->getPackage()->getUuid();

        // Increment total classes count
        ++$this->nodeMetrics[$pkgUUID][self::M_NUMBER_OF_CLASSES];

        // Check for abstract or concrete class
        if ($type->isAbstract()) {
            ++$this->nodeMetrics[$pkgUUID][self::M_NUMBER_OF_ABSTRACT_CLASSES];
        } else {
            ++$this->nodeMetrics[$pkgUUID][self::M_NUMBER_OF_CONCRETE_CLASSES];
        }

        
        foreach ($type->getDependencies() as $dep) {
            $this->collectDependencies($type->getPackage(), $dep->getPackage());
        }

        foreach ($type->getMethods() as $method) {
            $method->accept($this);
        }
    }

    /**
     * Collects the dependencies between the two given packages.
     *
     * @param \PHP\Depend\Source\AST\ASTNamespace $packageA Context/owning package.
     * @param \PHP\Depend\Source\AST\ASTNamespace $packageB Dependent package.
     *
     * @return void
     */
    private function collectDependencies(ASTNamespace $packageA, ASTNamespace $packageB)
    {
        $idA = $packageA->getUuid();
        $idB = $packageB->getUuid();

        if ($idB === $idA) {
            return;
        }

        // Create a container for this dependency
        $this->initPackageMetric($packageB);

        if (!in_array($idB, $this->nodeMetrics[$idA][self::M_EFFERENT_COUPLING])) {
            $this->nodeMetrics[$idA][self::M_EFFERENT_COUPLING][] = $idB;
            $this->nodeMetrics[$idB][self::M_AFFERENT_COUPLING][] = $idA;
        }
    }

    /**
     * Initializes the node metric record for the given <b>$package</b>.
     *
     * @param \PHP\Depend\Source\AST\ASTNamespace $namespace
     * @return void
     */
    protected function initPackageMetric(ASTNamespace $namespace)
    {
        $uuid = $namespace->getUuid();

        if (!isset($this->nodeMetrics[$uuid])) {
            // Store a package reference
            $this->nodeSet[$uuid] = $namespace;

            // Create empty metrics for this package
            $this->nodeMetrics[$uuid] = array(
                self::M_NUMBER_OF_CLASSES           =>  0,
                self::M_NUMBER_OF_CONCRETE_CLASSES  =>  0,
                self::M_NUMBER_OF_ABSTRACT_CLASSES  =>  0,
                self::M_AFFERENT_COUPLING           =>  array(),
                self::M_EFFERENT_COUPLING           =>  array(),
                self::M_ABSTRACTION                 =>  0,
                self::M_INSTABILITY                 =>  0,
                self::M_DISTANCE                    =>  0
            );
        }
    }

    /**
     * Post processes all analyzed nodes.
     *
     * @return void
     */
    protected function postProcess()
    {
        foreach ($this->nodeMetrics as $uuid => $metrics) {

            // Store afferent nodes for uuid
            $this->afferentNodes[$uuid] = array();
            foreach ($metrics[self::M_AFFERENT_COUPLING] as $caUUID) {
                $this->afferentNodes[$uuid][] = $this->nodeSet[$caUUID];
            }
            sort($this->afferentNodes[$uuid]);

            // Store efferent nodes for uuid
            $this->efferentNodes[$uuid] = array();
            foreach ($metrics[self::M_EFFERENT_COUPLING] as $ceUUID) {
                $this->efferentNodes[$uuid][] = $this->nodeSet[$ceUUID];
            }
            sort($this->efferentNodes[$uuid]);

            $afferent = count($metrics[self::M_AFFERENT_COUPLING]);
            $efferent = count($metrics[self::M_EFFERENT_COUPLING]);

            $this->nodeMetrics[$uuid][self::M_AFFERENT_COUPLING] = $afferent;
            $this->nodeMetrics[$uuid][self::M_EFFERENT_COUPLING] = $efferent;
        }
    }

    /**
     * Calculates the abstractness for all analyzed nodes.
     *
     * @return void
     */
    protected function calculateAbstractness()
    {
        foreach ($this->nodeMetrics as $uuid => $metrics) {
            if ($metrics[self::M_NUMBER_OF_CLASSES] !== 0) {
                $this->nodeMetrics[$uuid][self::M_ABSTRACTION] = (
                    $metrics[self::M_NUMBER_OF_ABSTRACT_CLASSES] /
                    $metrics[self::M_NUMBER_OF_CLASSES]
                );
            }

        }
    }

    /**
     * Calculates the instability for all analyzed nodes.
     *
     * @return void
     */
    protected function calculateInstability()
    {
        foreach ($this->nodeMetrics as $uuid => $metrics) {
            // Count total incoming and outgoing dependencies
            $total = (
                $metrics[self::M_AFFERENT_COUPLING] +
                $metrics[self::M_EFFERENT_COUPLING]
            );

            if ($total !== 0) {
                $this->nodeMetrics[$uuid][self::M_INSTABILITY] = (
                    $metrics[self::M_EFFERENT_COUPLING] / $total
                );
            }
        }
    }

    /**
     * Calculates the distance to an optimal value.
     *
     * @return void
     */
    protected function calculateDistance()
    {
        foreach ($this->nodeMetrics as $uuid => $metrics) {
            $this->nodeMetrics[$uuid][self::M_DISTANCE] = abs(
                ($metrics[self::M_ABSTRACTION] + $metrics[self::M_INSTABILITY]) - 1
            );
        }
    }

    /**
     * Collects a single cycle that is reachable by this package. All packages
     * that are part of the cylce are stored in the given <b>$list</b> array.
     *
     * @param \PHP\Depend\Source\AST\ASTNamespace[] &$list
     * @param \PHP\Depend\Source\AST\ASTNamespace $namespace
     * @return boolean If this method detects a cycle the return value is <b>true</b>
     *                 otherwise this method will return <b>false</b>.
     */
    protected function collectCycle(array &$list, ASTNamespace $namespace)
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
