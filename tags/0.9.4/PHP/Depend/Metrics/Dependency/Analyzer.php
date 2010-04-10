<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Metrics/AbstractAnalyzer.php';
require_once 'PHP/Depend/Metrics/AnalyzerI.php';

/**
 * This visitor generates the metrics for the analyzed packages.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_Dependency_Analyzer
       extends PHP_Depend_Metrics_AbstractAnalyzer
    implements PHP_Depend_Metrics_AnalyzerI
{
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
     * @var array(string=>array) $_nodeMetrics
     */
    private $_nodeMetrics = null;

    protected $nodeSet = array();

    private $_efferentNodes = array();

    private $_afferentNodes = array();

    /**
     * This property holds the UUID ids of all skipable paths when this class
     * collects the cycles.
     *
     * @var array(string=>boolean) $_skipablePaths
     */
    private $_skipablePaths = array();

    /**
     * All collected cycles for the input code.
     *
     * <code>
     * array(
     *     <package-uuid> => array(
     *         PHP_Depend_Code_Package {},
     *         PHP_Depend_Code_Package {},
     *     ),
     *     <package-uuid> => array(
     *         PHP_Depend_Code_Package {},
     *         PHP_Depend_Code_Package {},
     *     ),
     * )
     * </code>
     *
     * @var array(string=>array) $_collectedCycles
     */
    private $_collectedCycles = array();

    /**
     * Processes all {@link PHP_Depend_Code_Package} code nodes.
     *
     * @param PHP_Depend_Code_NodeIterator $packages All code packages.
     *
     * @return void
     */
    public function analyze(PHP_Depend_Code_NodeIterator $packages)
    {
        if ($this->_nodeMetrics === null) {

            $this->fireStartAnalyzer();

            $this->_nodeMetrics = array();

            foreach ($packages as $package) {
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
     * @param PHP_Depend_Code_NodeI $node The context node instance.
     *
     * @return array
     */
    public function getStats(PHP_Depend_Code_NodeI $node)
    {
        $stats = array();
        if (isset($this->_nodeMetrics[$node->getUUID()])) {
            $stats = $this->_nodeMetrics[$node->getUUID()];
        }
        return $stats;
    }

    /**
     * Returns an array of all afferent nodes.
     *
     * @param PHP_Depend_Code_NodeI $node The context node instance.
     *
     * @return array(PHP_Depend_Code_NodeI)
     */
    public function getAfferents(PHP_Depend_Code_NodeI $node)
    {
        $afferents = array();
        if (isset($this->_afferentNodes[$node->getUUID()])) {
            $afferents = $this->_afferentNodes[$node->getUUID()];
        }
        return $afferents;
    }

    /**
     * Returns an array of all efferent nodes.
     *
     * @param PHP_Depend_Code_NodeI $node The context node instance.
     *
     * @return array(PHP_Depend_Code_NodeI)
     */
    public function getEfferents(PHP_Depend_Code_NodeI $node)
    {
        $efferents = array();
        if (isset($this->_efferentNodes[$node->getUUID()])) {
            $efferents = $this->_efferentNodes[$node->getUUID()];
        }
        return $efferents;
    }

    /**
     * Returns an array of nodes that build a cycle for the requested node or it
     * returns <b>null</b> if no cycle exists .
     *
     * @param PHP_Depend_Code_NodeI $node The context node instance.
     *
     * @return array(PHP_Depend_Code_NodeI)
     */
    public function getCycle(PHP_Depend_Code_NodeI $node)
    {
        if (isset($this->_collectedCycles[$node->getUUID()])) {
            return $this->_collectedCycles[$node->getUUID()];
        }
        return null;
    }

    /**
     * Visits a method node.
     *
     * @param PHP_Depend_Code_Class $method The method class node.
     *
     * @return void
     */
    public function visitMethod(PHP_Depend_Code_Method $method)
    {
        $this->fireStartMethod($method);

        // Get context package uuid
        $pkgUUID = $method->getParent()->getPackage()->getUUID();

        // Traverse all dependencies
        foreach ($method->getDependencies() as $dep) {
            // Get dependent package uuid
            $depPkgUUID = $dep->getPackage()->getUUID();

            // Skip if context and dependency are equal
            if ($depPkgUUID === $pkgUUID) {
                continue;
            }

            // Create a container for this dependency
            $this->initPackageMetric($dep->getPackage());

            if (!in_array($depPkgUUID, $this->_nodeMetrics[$pkgUUID]['ce'])) {
                $this->_nodeMetrics[$pkgUUID]['ce'][]    = $depPkgUUID;
                $this->_nodeMetrics[$depPkgUUID]['ca'][] = $pkgUUID;
            }
        }

        $this->fireEndMethod($method);
    }

    /**
     * Visits a package node.
     *
     * @param PHP_Depend_Code_Class $package The package class node.
     *
     * @return void
     */
    public function visitPackage(PHP_Depend_Code_Package $package)
    {
        $this->fireStartPackage($package);

        $this->initPackageMetric($package);

        $this->nodeSet[$package->getUUID()] = $package;

        foreach ($package->getTypes() as $type) {
            $type->accept($this);
        }

        $storage = new SplObjectStorage();
        if ($this->collectCycle($storage, $package) === true) {
            $this->_collectedCycles[$package->getUUID()] = array();
            foreach ($storage as $pkg) {
                $this->_collectedCycles[$package->getUUID()][] = $pkg;
            }
        }

        $this->fireEndPackage($package);
    }

    /**
     * Visits a class node.
     *
     * @param PHP_Depend_Code_Class $class The current class node.
     *
     * @return void
     */
    public function visitClass(PHP_Depend_Code_Class $class)
    {
        $this->fireStartClass($class);
        $this->visitType($class);
        $this->fireEndClass($class);
    }

    /**
     * Visits an interface node.
     *
     * @param PHP_Depend_Code_Interface $interface The current interface node.
     *
     * @return void
     */
    public function visitInterface(PHP_Depend_Code_Interface $interface)
    {
        $this->fireStartInterface($interface);
        $this->visitType($interface);
        $this->fireEndInterface($interface);
    }

    /**
     * Generic visit method for classes and interfaces. Both visit methods
     * delegate calls to this method.
     *
     * @param PHP_Depend_Code_AbstractType $type The context type instance.
     *
     * @return void
     */
    protected function visitType(PHP_Depend_Code_AbstractType $type)
    {
        // Get context package uuid
        $pkgUUID = $type->getPackage()->getUUID();

        // Increment total classes count
        ++$this->_nodeMetrics[$pkgUUID]['tc'];

        // Check for abstract or concrete class
        if ($type->isAbstract()) {
            ++$this->_nodeMetrics[$pkgUUID]['ac'];
        } else {
            ++$this->_nodeMetrics[$pkgUUID]['cc'];
        }

        // Traverse all dependencies
        foreach ($type->getDependencies() as $dep) {
            // Get dependent package uuid
            $depPkgUUID = $dep->getPackage()->getUUID();

            // Skip if context and dependency are equal
            if ($depPkgUUID === $pkgUUID) {
                continue;
            }

            // Create a container for this dependency
            $this->initPackageMetric($dep->getPackage());

            if (!in_array($depPkgUUID, $this->_nodeMetrics[$pkgUUID]['ce'])) {
                $this->_nodeMetrics[$pkgUUID]['ce'][]    = $depPkgUUID;
                $this->_nodeMetrics[$depPkgUUID]['ca'][] = $pkgUUID;
            }
        }

        foreach ($type->getMethods() as $method) {
            $method->accept($this);
        }
    }

    /**
     * Initializes the node metric record for the given <b>$package</b>.
     *
     * @param PHP_Depend_Code_Package $package The context package.
     *
     * @return void
     */
    protected function initPackageMetric(PHP_Depend_Code_Package $package)
    {
        $uuid = $package->getUUID();

        if (!isset($this->_nodeMetrics[$uuid])) {
            // Store a package reference
            $this->nodeSet[$uuid] = $package;

            // Create empty metrics for this package
            $this->_nodeMetrics[$uuid] = array(
                'tc'  =>  0,
                'cc'  =>  0,
                'ac'  =>  0,
                'ca'  =>  array(),
                'ce'  =>  array(),
                'a'   =>  0,
                'i'   =>  0,
                'd'   =>  0
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
        foreach ($this->_nodeMetrics as $uuid => $metrics) {

            // Store afferent nodes for uuid
            $this->_afferentNodes[$uuid] = array();
            foreach ($metrics['ca'] as $caUUID) {
                $this->_afferentNodes[$uuid][] = $this->nodeSet[$caUUID];
            }
            sort($this->_afferentNodes[$uuid]);

            // Store efferent nodes for uuid
            $this->_efferentNodes[$uuid] = array();
            foreach ($metrics['ce'] as $ceUUID) {
                $this->_efferentNodes[$uuid][] = $this->nodeSet[$ceUUID];
            }
            sort($this->_efferentNodes[$uuid]);

            $this->_nodeMetrics[$uuid]['ca'] = count($metrics['ca']);
            $this->_nodeMetrics[$uuid]['ce'] = count($metrics['ce']);
        }
    }

    /**
     * Calculates the abstractness for all analyzed nodes.
     *
     * @return void
     */
    protected function calculateAbstractness()
    {
        foreach ($this->_nodeMetrics as $uuid => $metrics) {
            if ($metrics['tc'] !== 0) {
                $this->_nodeMetrics[$uuid]['a'] = ($metrics['ac'] / $metrics['tc']);
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
        foreach ($this->_nodeMetrics as $uuid => $metrics) {
            // Count total incoming and outgoing dependencies
            $total = ($metrics['ca'] + $metrics['ce']);

            if ($total !== 0) {
                $this->_nodeMetrics[$uuid]['i'] = ($metrics['ce'] / $total);
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
        foreach ($this->_nodeMetrics as $uuid => $m) {
            $this->_nodeMetrics[$uuid]['d'] = abs(($m['a'] + $m['i']) - 1);
        }
    }

    /**
     * Collects a single cycle that is reachable by this package. All packages
     * that are part of the cylce are stored in the given {@link SplObjectStorage}
     * instance.
     *
     * @param SplObjectStorage        $storage The cycle package object store.
     * @param PHP_Depend_Code_Package $package The context code package.
     *
     * @return boolean If this method detects a cycle the return value is <b>true</b>
     *                 otherwise this method will return <b>false</b>.
     */
    protected function collectCycle(SplObjectStorage $storage,
                                    PHP_Depend_Code_Package $package)
    {
        if ($storage->contains($package)) {
            $storage->rewind();
            while (($tmp = $storage->current()) !== $package) {
                $storage->detach($tmp);
            }
            return true;
        }

        $storage->attach($package);

        foreach ($package->getTypes() as $class) {

            // Create a path identifier
            $pathID = $package->getUUID() . '#' . $class->getUUID();

            // There is no cycle for this combination, if this id already exists.
            if (isset($this->_skipablePaths[$pathID])) {
                continue;
            }

            // Traverse all direct class dependencies
            foreach ($class->getDependencies() as $dependency) {
                $pkg = $dependency->getPackage();
                if ($pkg !== $package && $this->collectCycle($storage, $pkg)) {
                    return true;
                }
            }
            // Traverse all indirect class dependencies
            foreach ($class->getMethods() as $method) {
                foreach ($method->getDependencies() as $dependency) {
                    $pkg = $dependency->getPackage();
                    if ($pkg !== $package && $this->collectCycle($storage, $pkg)) {
                        return true;
                    }
                }
            }

            // No cycle detected, so mark as skipable
            $this->_skipablePaths[$pathID] = true;
        }
        $storage->detach($package);

        return false;
    }
}