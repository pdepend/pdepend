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

namespace PDepend\Metrics\Analyzer\CodeRankAnalyzer;

use PDepend\Source\AST\AbstractASTArtifact;
use PDepend\Source\AST\AbstractASTClassOrInterface;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\ASTVisitor\AbstractASTVisitor;

/**
 * Collects class and namespace metrics based on class and interface methods.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class MethodStrategy extends AbstractASTVisitor implements CodeRankStrategyI
{
    /**
     * All found nodes.
     *
     * @var array<string, array{in: string[], out: string[], name: string, type: class-string}>
     */
    private $nodes = [];

    /**
     * Returns the collected nodes.
     *
     * @return array<string, array{in: string[], out: string[], name: string, type: class-string}>
     */
    public function getCollectedNodes()
    {
        return $this->nodes;
    }

    /**
     * Visits a method node.
     */
    public function visitMethod(ASTMethod $method): void
    {
        $this->fireStartMethod($method);

        // Get owner type
        $type = $method->getParent();

        if (($depType = $method->getReturnClass()) !== null) {
            $this->processType($type, $depType);
        }
        foreach ($method->getExceptionClasses() as $depType) {
            $this->processType($type, $depType);
        }
        foreach ($method->getDependencies() as $depType) {
            $this->processType($type, $depType);
        }

        $this->fireEndMethod($method);
    }

    /**
     * Extracts the coupling information between the two given types and their
     * parent namespacess.
     */
    private function processType(AbstractASTClassOrInterface $type, AbstractASTClassOrInterface $dependency): void
    {
        if ($type !== $dependency) {
            $this->initNode($type);
            $this->initNode($dependency);

            $this->nodes[$type->getId()]['in'][] = $dependency->getId();
            $this->nodes[$dependency->getId()]['out'][] = $type->getId();
        }

        $namespace = $type->getNamespace();
        $dependencyNamespace = $dependency->getNamespace();

        if ($namespace !== $dependencyNamespace) {
            $this->initNode($namespace);
            $this->initNode($dependencyNamespace);

            $this->nodes[$namespace->getId()]['in'][] = $dependencyNamespace->getId();
            $this->nodes[$dependencyNamespace->getId()]['out'][] = $namespace->getId();
        }
    }

    /**
     * Initializes the temporary node container for the given <b>$node</b>.
     */
    private function initNode(AbstractASTArtifact $node): void
    {
        if (!isset($this->nodes[$node->getId()])) {
            $this->nodes[$node->getId()] = [
                'in' => [],
                'out' => [],
                'name' => $node->getImage(),
                'type' => $node::class,
            ];
        }
    }
}
