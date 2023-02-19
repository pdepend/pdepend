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
use PDepend\Source\AST\AbstractASTType;
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
class ClassDependencyAnalyzer extends AbstractAnalyzer
{
    /**
     * Metrics provided by the analyzer implementation.
     */
    const M_AFFERENT_COUPLING          = 'ca',
          M_EFFERENT_COUPLING          = 'ce';

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
     * @var array<string, array>
     */
    private $nodeMetrics = null;

    /**
     * @var array<string, ASTNamespace>
     */
    protected $nodeSet = array();

    /**
     * Nodes in which the current analyzed class is used.
     *
     * @var array<string, array<int, AbstractASTType>>
     */
    private $efferentNodes = array();

    /**
     * Nodes that is used by the current analyzed class.
     *
     * @var array<string, array<int, AbstractASTType>>
     */
    private $afferentNodes = array();

    /**
     * Processes all {@link ASTNamespace} code nodes.
     *
     * @param ASTNamespace[] $namespaces
     *
     * @return void
     */
    public function analyze($namespaces)
    {
        if ($this->nodeMetrics === null) {
            $this->fireStartAnalyzer();

            $this->nodeMetrics = array();

            foreach ($namespaces as $namespace) {
                $namespace->accept($this);
            }

            $this->postProcess();

            $this->fireEndAnalyzer();
        }
    }

    /**
     * Returns an array of all afferent nodes.
     *
     * @return AbstractASTType[]
     */
    public function getAfferents(AbstractASTArtifact $node)
    {
        $afferents = array();
        if (isset($this->afferentNodes[$node->getId()])) {
            $afferents = $this->afferentNodes[$node->getId()];
        }
        return $afferents;
    }

    /**
     * Returns an array of all efferent nodes.
     *
     * @return AbstractASTType[]
     */
    public function getEfferents(AbstractASTArtifact $node)
    {
        $efferents = array();
        if (isset($this->efferentNodes[$node->getId()])) {
            $efferents = $this->efferentNodes[$node->getId()];
        }
        return $efferents;
    }

    public function visit($node, $value)
    {
        if ($node instanceof ASTMethod) {
            return $this->visitMethod($node, $value);
        }
        if ($node instanceof ASTNamespace) {
            return $this->visitNamespace($node, $value);
        }
        if ($node instanceof ASTClass) {
            return $this->visitClass($node, $value);
        }
        if ($node instanceof ASTInterface) {
            return $this->visitInterface($node, $value);
        }

        return parent::visit($node, $value);
    }

    /**
     * Visits a method node.
     */
    public function visitMethod(ASTMethod $method, $value)
    {
        $this->fireStartMethod($method);

        $type = $method->getParent();
        foreach ($method->getDependencies() as $dependency) {
            $this->collectDependencies($type, $dependency);
        }

        $this->fireEndMethod($method);

        return $value;
    }

    /**
     * Visits a namespace node.
     */
    public function visitNamespace(ASTNamespace $namespace, $value)
    {
        $this->fireStartNamespace($namespace);

        $this->nodeSet[$namespace->getId()] = $namespace;

        foreach ($namespace->getTypes() as $type) {
            $type->accept($this);
        }

        $this->fireEndNamespace($namespace);

        return $value;
    }

    /**
     * Visits a class node.
     */
    public function visitClass(ASTClass $class, $value)
    {
        $this->fireStartClass($class);
        $this->visitType($class);
        $this->fireEndClass($class);

        return $value;
    }

    /**
     * Visits an interface node.
     */
    public function visitInterface(ASTInterface $interface, $value)
    {
        $this->fireStartInterface($interface);
        $this->visitType($interface);
        $this->fireEndInterface($interface);

        return $value;
    }

    /**
     * Generic visit method for classes and interfaces. Both visit methods
     * delegate calls to this method.
     *
     * @return void
     */
    protected function visitType(AbstractASTClassOrInterface $type)
    {
        $id = $type->getId();

        foreach ($type->getDependencies() as $dependency) {
            $this->collectDependencies($type, $dependency);
        }

        foreach ($type->getMethods() as $method) {
            $method->accept($this);
        }
    }

    /**
     * Collects the dependencies between the two given classes.
     *
     * @return void
     */
    private function collectDependencies(AbstractASTClassOrInterface $typeA, AbstractASTClassOrInterface $typeB)
    {
        $idA = $typeA->getId();
        $idB = $typeB->getId();

        if ($idB === $idA) {
            return;
        }

        // Create a container for this dependency
        $this->initTypeMetric($typeA);
        $this->initTypeMetric($typeB);

        if (!in_array($idB, $this->nodeMetrics[$idA][self::M_EFFERENT_COUPLING])) {
            $this->nodeMetrics[$idA][self::M_EFFERENT_COUPLING][] = $idB;
            $this->nodeMetrics[$idB][self::M_AFFERENT_COUPLING][] = $idA;
        }
    }

    /**
     * Initializes the node metric record for the given <b>$type</b>.
     *
     * @return void
     */
    protected function initTypeMetric(AbstractASTClassOrInterface $type)
    {
        $id = $type->getId();

        if (!isset($this->nodeMetrics[$id])) {
            $this->nodeSet[$id] = $type;

            $this->nodeMetrics[$id] = array(
                self::M_AFFERENT_COUPLING =>  array(),
                self::M_EFFERENT_COUPLING =>  array(),
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
        foreach ($this->nodeMetrics as $id => $metrics) {
            $this->afferentNodes[$id] = array();
            foreach ($metrics[self::M_AFFERENT_COUPLING] as $caId) {
                $this->afferentNodes[$id][] = $this->nodeSet[$caId];
            }

            $this->efferentNodes[$id] = array();
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
     *
     * @return bool If this method detects a cycle the return value is <b>true</b>
     *              otherwise this method will return <b>false</b>.
     */
    protected function collectCycle(array &$list, AbstractASTArtifact $node)
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
