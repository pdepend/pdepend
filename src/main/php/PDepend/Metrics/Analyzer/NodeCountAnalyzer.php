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
    /**
     * Metrics provided by the analyzer implementation.
     */
    const M_NUMBER_OF_PACKAGES   = 'nop',
          M_NUMBER_OF_CLASSES    = 'noc',
          M_NUMBER_OF_INTERFACES = 'noi',
          M_NUMBER_OF_METHODS    = 'nom',
          M_NUMBER_OF_FUNCTIONS  = 'nof';

    /**
     * Number Of Packages.
     *
     * @var int
     */
    private $nop = 0;

    /**
     * Number Of Classes.
     *
     * @var int
     */
    private $noc = 0;

    /**
     * Number Of Interfaces.
     *
     * @var int
     */
    private $noi = 0;

    /**
     * Number Of Methods.
     *
     * @var int
     */
    private $nom = 0;

    /**
     * Number Of Functions.
     *
     * @var int
     */
    private $nof = 0;

    /**
     * Collected node metrics
     *
     * @var array<string, array>
     */
    private $nodeMetrics = null;

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
     * @return array<string, mixed>
     */
    public function getNodeMetrics(ASTArtifact $artifact)
    {
        $metrics = array();
        if (isset($this->nodeMetrics[$artifact->getId()])) {
            $metrics = $this->nodeMetrics[$artifact->getId()];
        }
        return $metrics;
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
     * @return array<string, mixed>
     */
    public function getProjectMetrics()
    {
        return array(
            self::M_NUMBER_OF_PACKAGES    =>  $this->nop,
            self::M_NUMBER_OF_CLASSES     =>  $this->noc,
            self::M_NUMBER_OF_INTERFACES  =>  $this->noi,
            self::M_NUMBER_OF_METHODS     =>  $this->nom,
            self::M_NUMBER_OF_FUNCTIONS   =>  $this->nof
        );
    }

    /**
     * Processes all {@link ASTNamespace} code nodes.
     *
     * @param ASTNamespace[] $namespaces
     *
     * @return void
     */
    public function analyze($namespaces)
    {
        // Check for previous run
        if ($this->nodeMetrics === null) {
            $this->fireStartAnalyzer();

            $this->nodeMetrics = array();

            foreach ($namespaces as $namespace) {
                $namespace->accept($this);
            }

            $this->fireEndAnalyzer();
        }
    }

    public function visit($node, $value)
    {
        if ($node instanceof ASTClass) {
            return $this->visitClass($node, $value);
        }
        if ($node instanceof ASTFunction) {
            return $this->visitFunction($node, $value);
        }
        if ($node instanceof ASTInterface) {
            return $this->visitInterface($node, $value);
        }
        if ($node instanceof ASTMethod) {
            return $this->visitMethod($node, $value);
        }
        if ($node instanceof ASTNamespace) {
            return $this->visitNamespace($node, $value);
        }

        return parent::visit($node, $value);
    }

    /**
     * Visits a class node.
     */
    public function visitClass(ASTClass $class, $value)
    {
        if (false === $class->isUserDefined()) {
            return $value;
        }

        $this->fireStartClass($class);

        // Update global class count
        ++$this->noc;

        $id = $class->getNamespace()->getId();
        ++$this->nodeMetrics[$id][self::M_NUMBER_OF_CLASSES];

        $this->nodeMetrics[$class->getId()] = array(
            self::M_NUMBER_OF_METHODS  =>  0
        );

        foreach ($class->getMethods() as $method) {
            $method->accept($this);
        }

        $this->fireEndClass($class);

        return $value;
    }

    /**
     * Visits a function node.
     */
    public function visitFunction(ASTFunction $function, $value)
    {
        $this->fireStartFunction($function);

        // Update global function count
        ++$this->nof;

        $id = $function->getNamespace()->getId();
        ++$this->nodeMetrics[$id][self::M_NUMBER_OF_FUNCTIONS];

        $this->fireEndFunction($function);

        return $value;
    }

    /**
     * Visits a code interface object.
     */
    public function visitInterface(ASTInterface $interface, $value)
    {
        if (false === $interface->isUserDefined()) {
            return $value;
        }

        $this->fireStartInterface($interface);

        // Update global class count
        ++$this->noi;

        $id = $interface->getNamespace()->getId();
        ++$this->nodeMetrics[$id][self::M_NUMBER_OF_INTERFACES];

        $this->nodeMetrics[$interface->getId()] = array(
            self::M_NUMBER_OF_METHODS  =>  0
        );

        foreach ($interface->getMethods() as $method) {
            $method->accept($this);
        }

        $this->fireEndInterface($interface);

        return $value;
    }

    /**
     * Visits a method node.
     */
    public function visitMethod(ASTMethod $method, $value)
    {
        $this->fireStartMethod($method);

        // Update global method count
        ++$this->nom;

        $parent = $method->getParent();

        // Update parent class or interface
        $parentId = $parent->getId();
        ++$this->nodeMetrics[$parentId][self::M_NUMBER_OF_METHODS];

        $id = $parent->getNamespace()->getId();
        ++$this->nodeMetrics[$id][self::M_NUMBER_OF_METHODS];

        $this->fireEndMethod($method);

        return $value;
    }

    /**
     * Visits a namespace node.
     */
    public function visitNamespace(ASTNamespace $namespace, $value)
    {
        $this->fireStartNamespace($namespace);

        ++$this->nop;

        $this->nodeMetrics[$namespace->getId()] = array(
            self::M_NUMBER_OF_CLASSES     =>  0,
            self::M_NUMBER_OF_INTERFACES  =>  0,
            self::M_NUMBER_OF_METHODS     =>  0,
            self::M_NUMBER_OF_FUNCTIONS   =>  0
        );


        foreach ($namespace->getClasses() as $class) {
            $class->accept($this);
        }
        foreach ($namespace->getInterfaces() as $interface) {
            $interface->accept($this);
        }
        foreach ($namespace->getFunctions() as $function) {
            $function->accept($this);
        }

        $this->fireEndNamespace($namespace);

        return $value;
    }
}
