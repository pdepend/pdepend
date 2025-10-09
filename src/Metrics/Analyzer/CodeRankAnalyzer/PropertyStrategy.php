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
use PDepend\Source\AST\ASTProperty;
use PDepend\Source\ASTVisitor\AbstractASTVisitor;

/**
 * Collects class and namespace metrics based on class properties.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class PropertyStrategy extends AbstractASTVisitor implements CodeRankStrategyI
{
    /**
     * All found nodes.
     *
     * @var array<string, array{in: string[], out: string[], name: string, type: class-string}>
     */
    private array $nodes = [];

    /**
     * Returns the collected nodes.
     *
     * @return array<string, array{in: string[], out: string[], name: string, type: class-string}>
     */
    public function getCollectedNodes(): array
    {
        return $this->nodes;
    }

    /**
     * Visits a property node.
     */
    public function visitProperty(ASTProperty $property): void
    {
        $this->fireStartProperty($property);

        if (($depClass = $property->getClass()) === null) {
            $this->fireEndProperty($property);

            return;
        }

        $depNamespace = $depClass->getNamespace();

        $class = $property->getDeclaringClass();
        $namespace = $class->getNamespace();

        if ($depClass !== $class) {
            $classId = $class->getId();
            $depClassId = $depClass->getId();

            if (isset($this->nodes[$classId])) {
                $this->nodes[$classId]['in'][] = $depClassId;
            } else {
                $this->initNode($class, in: [$depClassId]);
            }
            if (isset($this->nodes[$depClassId])) {
                $this->nodes[$depClassId]['out'][] = $classId;
            } else {
                $this->initNode($depClass, out: [$classId]);
            }
        }

        if ($depNamespace && $namespace && $depNamespace !== $namespace) {
            $namespaceId = $namespace->getId();
            $depNamespaceId = $depNamespace->getId();

            if (isset($this->nodes[$namespaceId])) {
                $this->nodes[$namespaceId]['in'][] = $depNamespaceId;
            } else {
                $this->initNode($namespace, in: [$depNamespaceId]);
            }
            if (isset($this->nodes[$depNamespaceId])) {
                $this->nodes[$depNamespaceId]['out'][] = $namespaceId;
            } else {
                $this->initNode($depNamespace, out: [$namespaceId]);
            }
        }

        $this->fireEndProperty($property);
    }

    /**
     * Initializes the temporary node container for the given <b>$node</b>.
     *
     * @param string[] $in
     * @param string[] $out
     */
    protected function initNode(AbstractASTArtifact $node, array $in = [], array $out = []): void
    {
        $this->nodes[$node->getId()] = [
            'in' => $in,
            'out' => $out,
            'name' => $node->getImage(),
            'type' => $node::class,
        ];
    }
}
