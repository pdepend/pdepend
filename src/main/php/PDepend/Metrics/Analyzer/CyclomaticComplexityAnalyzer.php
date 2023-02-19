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

use PDepend\Metrics\AbstractCachingAnalyzer;
use PDepend\Metrics\AnalyzerNodeAware;
use PDepend\Metrics\AnalyzerProjectAware;
use PDepend\Source\AST\AbstractASTCallable;
use PDepend\Source\AST\ASTArtifact;
use PDepend\Source\AST\ASTBooleanAndExpression;
use PDepend\Source\AST\ASTBooleanOrExpression;
use PDepend\Source\AST\ASTCatchStatement;
use PDepend\Source\AST\ASTConditionalExpression;
use PDepend\Source\AST\ASTDoWhileStatement;
use PDepend\Source\AST\ASTElseIfStatement;
use PDepend\Source\AST\ASTForeachStatement;
use PDepend\Source\AST\ASTForStatement;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTIfStatement;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTLogicalAndExpression;
use PDepend\Source\AST\ASTLogicalOrExpression;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\AST\ASTSwitchLabel;
use PDepend\Source\AST\ASTWhileStatement;

/**
 * This class calculates the Cyclomatic Complexity Number(CCN) for the project,
 * methods and functions.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class CyclomaticComplexityAnalyzer extends AbstractCachingAnalyzer implements AnalyzerNodeAware, AnalyzerProjectAware
{
    /**
     * Metrics provided by the analyzer implementation.
     */
    const M_CYCLOMATIC_COMPLEXITY_1 = 'ccn',
          M_CYCLOMATIC_COMPLEXITY_2 = 'ccn2';

    /**
     * The project Cyclomatic Complexity Number.
     *
     * @var int
     */
    private $ccn = 0;

    /**
     * Extended Cyclomatic Complexity Number(CCN2) for the project.
     *
     * @var int
     */
    private $ccn2 = 0;

    /**
     * Processes all {@link ASTNamespace} code nodes.
     *
     * @param ASTNamespace[] $namespaces
     *
     * @return void
     */
    public function analyze($namespaces)
    {
        if ($this->metrics === null) {
            $this->loadCache();
            $this->fireStartAnalyzer();

            // Init node metrics
            $this->metrics = array();

            foreach ($namespaces as $namespace) {
                $namespace->accept($this);
            }

            $this->fireEndAnalyzer();
            $this->unloadCache();
        }
    }

    /**
     * Returns the cyclomatic complexity for the given <b>$node</b> instance.
     *
     * @return int
     */
    public function getCcn(ASTArtifact $node)
    {
        $metrics = $this->getNodeMetrics($node);
        if (isset($metrics[self::M_CYCLOMATIC_COMPLEXITY_1])) {
            return $metrics[self::M_CYCLOMATIC_COMPLEXITY_1];
        }
        return 0;
    }

    /**
     * Returns the extended cyclomatic complexity for the given <b>$node</b>
     * instance.
     *
     * @return int
     */
    public function getCcn2(ASTArtifact $node)
    {
        $metrics = $this->getNodeMetrics($node);
        if (isset($metrics[self::M_CYCLOMATIC_COMPLEXITY_2])) {
            return $metrics[self::M_CYCLOMATIC_COMPLEXITY_2];
        }
        return 0;
    }

    /**
     * This method will return an <b>array</b> with all generated metric values
     * for the given <b>$node</b>. If there are no metrics for the requested
     * node, this method will return an empty <b>array</b>.
     *
     * @return array<string, integer>
     */
    public function getNodeMetrics(ASTArtifact $artifact)
    {
        if (isset($this->metrics[$artifact->getId()])) {
            return $this->metrics[$artifact->getId()];
        }
        return array();
    }

    /**
     * Provides the project summary metrics as an <b>array</b>.
     *
     * @return array<string, integer>
     */
    public function getProjectMetrics()
    {
        return array(
            self::M_CYCLOMATIC_COMPLEXITY_1  =>  $this->ccn,
            self::M_CYCLOMATIC_COMPLEXITY_2  =>  $this->ccn2
        );
    }

    public function visit($node, $value)
    {
        if ($node instanceof ASTBooleanAndExpression) {
            return $this->visitBooleanAndExpression($node, $value);
        }
        if ($node instanceof ASTBooleanOrExpression) {
            return $this->visitBooleanOrExpression($node, $value);
        }
        if ($node instanceof ASTCatchStatement) {
            return $this->visitCatchStatement($node, $value);
        }
        if ($node instanceof ASTConditionalExpression) {
            return $this->visitConditionalExpression($node, $value);
        }
        if ($node instanceof ASTDoWhileStatement) {
            return $this->visitDoWhileStatement($node, $value);
        }
        if ($node instanceof ASTElseIfStatement) {
            return $this->visitElseIfStatement($node, $value);
        }
        if ($node instanceof ASTForeachStatement) {
            return $this->visitForeachStatement($node, $value);
        }
        if ($node instanceof ASTForStatement) {
            return $this->visitForStatement($node, $value);
        }
        if ($node instanceof ASTIfStatement) {
            return $this->visitIfStatement($node, $value);
        }
        if ($node instanceof ASTInterface) {
            return $this->visitInterface($node, $value);
        }
        if ($node instanceof ASTLogicalAndExpression) {
            return $this->visitLogicalAndExpression($node, $value);
        }
        if ($node instanceof ASTLogicalOrExpression) {
            return $this->visitLogicalOrExpression($node, $value);
        }
        if ($node instanceof ASTMethod) {
            return $this->visitMethod($node, $value);
        }
        if ($node instanceof ASTSwitchLabel) {
            return $this->visitSwitchLabel($node, $value);
        }
        if ($node instanceof ASTWhileStatement) {
            return $this->visitWhileStatement($node, $value);
        }

        return parent::visit($node, $value);
    }

    /**
     * Visits a function node.
     */
    public function visitFunction(ASTFunction $function, $value)
    {
        $this->fireStartFunction($function);

        if (false === $this->restoreFromCache($function)) {
            $this->calculateComplexity($function);
        }
        $this->updateProjectMetrics($function->getId());

        $this->fireEndFunction($function);

        return $value;
    }

    /**
     * Visits a code interface object.
     */
    public function visitInterface(ASTInterface $interface, $value)
    {
        // Empty visit method, we don't want interface metrics
        return $value;
    }

    /**
     * Visits a method node.
     */
    public function visitMethod(ASTMethod $method, $value)
    {
        $this->fireStartMethod($method);

        if (false === $this->restoreFromCache($method)) {
            $this->calculateComplexity($method);
        }
        $this->updateProjectMetrics($method->getId());

        $this->fireEndMethod($method);

        return $value;
    }

    /**
     * Visits methods, functions or closures and calculated their complexity.
     *
     * @return void
     *
     * @since  0.9.8
     */
    public function calculateComplexity(AbstractASTCallable $callable)
    {
        $data = array(
            self::M_CYCLOMATIC_COMPLEXITY_1 => 1,
            self::M_CYCLOMATIC_COMPLEXITY_2 => 1
        );

        foreach ($callable->getChildren() as $child) {
            $data = $child->accept($this, $data);
        }

        $this->metrics[$callable->getId()] = $data;
    }

    /**
     * Stores the complexity of a node and updates the corresponding project
     * values.
     *
     * @param string $nodeId Identifier of the analyzed item.
     *
     * @return void
     *
     * @since  1.0.0
     */
    private function updateProjectMetrics($nodeId)
    {
        $this->ccn  += $this->metrics[$nodeId][self::M_CYCLOMATIC_COMPLEXITY_1];
        $this->ccn2 += $this->metrics[$nodeId][self::M_CYCLOMATIC_COMPLEXITY_2];
    }

    /**
     * Visits a boolean AND-expression.
     *
     * @param ASTBooleanAndExpression $node The currently visited node.
     * @param array<string, integer>  $data The previously calculated ccn values.
     *
     * @return array<string, integer>
     *
     * @since  0.9.8
     */
    public function visitBooleanAndExpression(ASTBooleanAndExpression $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];
        return parent::visit($node, $data);
    }

    /**
     * Visits a boolean OR-expression.
     *
     * @param ASTBooleanOrExpression $node The currently visited node.
     * @param array<string, integer> $data The previously calculated ccn values.
     *
     * @return array<string, integer>
     *
     * @since  0.9.8
     */
    public function visitBooleanOrExpression(ASTBooleanOrExpression $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];
        return parent::visit($node, $data);
    }

    /**
     * Visits a switch label.
     *
     * @param ASTSwitchLabel         $node The currently visited node.
     * @param array<string, integer> $data The previously calculated ccn values.
     *
     * @return array<string, integer>
     *
     * @since  0.9.8
     */
    public function visitSwitchLabel(ASTSwitchLabel $node, $data)
    {
        if (!$node->isDefault()) {
            ++$data[self::M_CYCLOMATIC_COMPLEXITY_1];
            ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];
        }
        return parent::visit($node, $data);
    }

    /**
     * Visits a catch statement.
     *
     * @param ASTCatchStatement      $node The currently visited node.
     * @param array<string, integer> $data The previously calculated ccn values.
     *
     * @return array<string, integer>
     *
     * @since  0.9.8
     */
    public function visitCatchStatement(ASTCatchStatement $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];

        return parent::visit($node, $data);
    }

    /**
     * Visits an elseif statement.
     *
     * @param ASTElseIfStatement     $node The currently visited node.
     * @param array<string, integer> $data The previously calculated ccn values.
     *
     * @return array<string, integer>
     *
     * @since  0.9.8
     */
    public function visitElseIfStatement(ASTElseIfStatement $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];

        return parent::visit($node, $data);
    }

    /**
     * Visits a for statement.
     *
     * @param ASTForStatement        $node The currently visited node.
     * @param array<string, integer> $data The previously calculated ccn values.
     *
     * @return array<string, integer>
     *
     * @since  0.9.8
     */
    public function visitForStatement(ASTForStatement $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];

        return parent::visit($node, $data);
    }

    /**
     * Visits a foreach statement.
     *
     * @param ASTForeachStatement    $node The currently visited node.
     * @param array<string, integer> $data The previously calculated ccn values.
     *
     * @return array<string, integer>
     *
     * @since  0.9.8
     */
    public function visitForeachStatement(ASTForeachStatement $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];

        return parent::visit($node, $data);
    }

    /**
     * Visits an if statement.
     *
     * @param ASTIfStatement         $node The currently visited node.
     * @param array<string, integer> $data The previously calculated ccn values.
     *
     * @return array<string, integer>
     *
     * @since  0.9.8
     */
    public function visitIfStatement(ASTIfStatement $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];

        return parent::visit($node, $data);
    }

    /**
     * Visits a logical AND expression.
     *
     * @param ASTLogicalAndExpression $node The currently visited node.
     * @param array<string, integer>  $data The previously calculated ccn values.
     *
     * @return array<string, integer>
     *
     * @since  0.9.8
     */
    public function visitLogicalAndExpression(ASTLogicalAndExpression $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];
        return parent::visit($node, $data);
    }

    /**
     * Visits a logical OR expression.
     *
     * @param ASTLogicalOrExpression $node The currently visited node.
     * @param array<string, integer> $data The previously calculated ccn values.
     *
     * @return array<string, integer>
     *
     * @since  0.9.8
     */
    public function visitLogicalOrExpression(ASTLogicalOrExpression $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];
        return parent::visit($node, $data);
    }

    /**
     * Visits a ternary operator.
     *
     * @param ASTConditionalExpression $node The currently visited node.
     * @param array<string, integer>   $data The previously calculated ccn values.
     *
     * @return array<string, integer>
     *
     * @since  0.9.8
     */
    public function visitConditionalExpression(ASTConditionalExpression $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];

        return parent::visit($node, $data);
    }

    /**
     * Visits a while-statement.
     *
     * @param ASTWhileStatement      $node The currently visited node.
     * @param array<string, integer> $data The previously calculated ccn values.
     *
     * @return array<string, integer>
     *
     * @since  0.9.8
     */
    public function visitWhileStatement(ASTWhileStatement $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];

        return parent::visit($node, $data);
    }

    /**
     * Visits a do/while-statement.
     *
     * @param ASTDoWhileStatement    $node The currently visited node.
     * @param array<string, integer> $data The previously calculated ccn values.
     *
     * @return array<string, integer>
     *
     * @since  0.9.12
     */
    public function visitDoWhileStatement(ASTDoWhileStatement $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];

        return parent::visit($node, $data);
    }
}
