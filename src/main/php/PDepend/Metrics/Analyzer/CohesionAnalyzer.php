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
use PDepend\Metrics\AnalyzerNodeAware;
use PDepend\Source\AST\ASTArtifact;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTMethodPostfix;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\AST\ASTProperty;
use PDepend\Source\AST\ASTPropertyPostfix;
use PDepend\Source\AST\ASTSelfReference;
use PDepend\Source\AST\ASTVariable;

/**
 * This analyzer implements several metrics that describe cohesion of classes
 * and namespaces.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class CohesionAnalyzer extends AbstractAnalyzer implements AnalyzerNodeAware
{
    /**
     * Collected cohesion metrics for classes.
     *
     * @var array<string, array<string, int>>
     */
    private array $nodeMetrics = [];

    /**
     * This method will return an <b>array</b> with all generated metric values
     * for the node with the given <b>$id</b> identifier. If there are no
     * metrics for the requested node, this method will return an empty <b>array</b>.
     *
     * <code>
     * array(
     *     'loc'    =>  42,
     *     'ncloc'  =>  17,
     *     'cc'     =>  12
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
     * Processes all {@link ASTNamespace} code nodes.
     */
    public function analyze($namespaces): void
    {
        $this->fireStartAnalyzer();

        foreach ($namespaces as $namespace) {
            $this->dispatch($namespace);
        }

        $this->fireEndAnalyzer();
    }

    /*
    public function visitProperty(ASTProperty $property)
    {
        $this->fireStartProperty($property);
        echo ltrim($property->getImage(), '$'), PHP_EOL;
        $this->fireEndProperty($property);
    }

    public function visitMethod(ASTMethod $method)
    {
        $this->fireStartMethod($method);

        $prefixes = $method->findChildrenOfType(
            ASTMemberPrimaryPrefix::class
        );
        foreach ($prefixes as $prefix) {
            $variable = $prefix->getChild(0);
            if ($variable instanceof ASTVariable
                && $variable->isThis()
            ) {
                echo "\$this->";
            } elseif ($variable instanceof ASTSelfReference) {
                echo "self::";
            } else {
                continue;
            }

            $next = $prefix->getChild(1);
            if ($next instanceof ASTMemberPrimaryPrefix) {
                $next = $next->getChild(0);
            }

            if ($next instanceof ASTPropertyPostfix) {
                echo $next->getImage(), PHP_EOL;
            } elseif ($next instanceof ASTMethodPostfix) {
                echo $next->getImage(), '()', PHP_EOL;
            }
        }

        $this->fireEndMethod($method);
    }
    */
}
