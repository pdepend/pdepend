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
use PHP\Depend\Metrics\AbstractAnalyzer;
use PHP\Depend\Metrics\AnalyzerNodeAware;
use PHP\Depend\Source\AST\AbstractASTArtifact;
use PHP\Depend\Source\AST\ASTArtifact;
use PHP\Depend\Source\AST\ASTArtifactList;

/**
 * This analyzer implements several metrics that describe cohesion of classes
 * and packages.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
class PHP_Depend_Metrics_Cohesion_Analyzer extends AbstractAnalyzer implements AnalyzerNodeAware
{
    /**
     * Type of this analyzer class.
     */
    const CLAZZ = __CLASS__;

    /**
     * Metrics provided by the analyzer implementation.
     */
    const M_LCOM4  = 'lcom4';

    /**
     * Collected cohesion metrics for classes.
     *
     * @var array
     */
    private $nodeMetrics = array();

    /**
     * This method will return an <b>array</b> with all generated metric values
     * for the node with the given <b>$uuid</b> identifier. If there are no
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
     * @param \PHP\Depend\Source\AST\ASTArtifact $artifact
     * @return array(string=>mixed)
     */
    public function getNodeMetrics(ASTArtifact $artifact)
    {
        if (isset($this->nodeMetrics[$artifact->getUuid()])) {
            return $this->nodeMetrics[$artifact->getUuid()];
        }
        return array();
    }

    /**
     * Processes all {@link \PHP\Depend\Source\AST\ASTNamespace} code nodes.
     *
     * @param \PHP\Depend\Source\AST\ASTArtifactList $namespaces
     *
     * @return void
     */
    public function analyze(ASTArtifactList $namespaces)
    {
        $this->fireStartAnalyzer();

        foreach ($namespaces as $package) {
            $package->accept($this);
        }

        $this->fireEndAnalyzer();
    }

    /*
    public function visitProperty(\PHP\Depend\Source\AST\ASTProperty $property)
    {
        $this->fireStartProperty($property);
        echo ltrim($property->getName(), '$'), PHP_EOL;
        $this->fireEndProperty($property);
    }

    public function visitMethod(ASTMethod $method)
    {
        $this->fireStartMethod($method);

        $prefixes = $method->findChildrenOfType(
            \PHP\Depend\Source\AST\ASTMemberPrimaryPrefix::CLAZZ
        );
        foreach ($prefixes as $prefix) {
            $variable = $prefix->getChild(0);
            if ($variable instanceof \PHP\Depend\Source\AST\ASTVariable
                && $variable->isThis()
            ) {
                echo "\$this->";
            } else if ($variable instanceof \PHP\Depend\Source\AST\ASTSelfReference) {
                echo "self::";
            } else {
                continue;
            }

            $next = $prefix->getChild(1);
            if ($next instanceof \PHP\Depend\Source\AST\ASTMemberPrimaryPrefix) {
                $next = $next->getChild(0);
            }

            if ($next instanceof \PHP\Depend\Source\AST\ASTPropertyPostfix) {
                echo $next->getImage(), PHP_EOL;
            } else if ($next instanceof \PHP\Depend\Source\AST\ASTMethodPostfix) {
                echo $next->getImage(), '()', PHP_EOL;
            }
        }

        $this->fireEndMethod($method);
    }
    */

}
