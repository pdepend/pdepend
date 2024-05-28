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
 * @extends AbstractCachingAnalyzer<array<string, int>>
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class CyclomaticComplexityAnalyzer extends AbstractCachingAnalyzer implements AnalyzerNodeAware, AnalyzerProjectAware
{
    /** Metrics provided by the analyzer implementation. */
    private const
        M_CYCLOMATIC_COMPLEXITY_1 = 'ccn',
        M_CYCLOMATIC_COMPLEXITY_2 = 'ccn2';

    /** @var array<string, int> */
    private array $complexityCollector;

    /** The project Cyclomatic Complexity Number. */
    private int $ccn = 0;

    /** Extended Cyclomatic Complexity Number(CCN2) for the project. */
    private int $ccn2 = 0;

    /**
     * Processes all {@link ASTNamespace} code nodes.
     */
    public function analyze($namespaces): void
    {
        if (!isset($this->metrics)) {
            $this->loadCache();
            $this->fireStartAnalyzer();

            // Init node metrics
            $this->metrics = [];

            foreach ($namespaces as $namespace) {
                $this->dispatch($namespace);
            }

            $this->fireEndAnalyzer();
            $this->unloadCache();
        }
    }

    /**
     * Returns the cyclomatic complexity for the given <b>$node</b> instance.
     */
    public function getCcn(ASTArtifact $node): int
    {
        $metrics = $this->getNodeMetrics($node);

        return $metrics[self::M_CYCLOMATIC_COMPLEXITY_1] ?? 0;
    }

    /**
     * Returns the extended cyclomatic complexity for the given <b>$node</b>
     * instance.
     */
    public function getCcn2(ASTArtifact $node): int
    {
        $metrics = $this->getNodeMetrics($node);

        return $metrics[self::M_CYCLOMATIC_COMPLEXITY_2] ?? 0;
    }

    /**
     * This method will return an <b>array</b> with all generated metric values
     * for the given <b>$node</b>. If there are no metrics for the requested
     * node, this method will return an empty <b>array</b>.
     *
     * @return array<string, int>
     */
    public function getNodeMetrics(ASTArtifact $artifact): array
    {
        return $this->metrics[$artifact->getId()] ?? [];
    }

    /**
     * Provides the project summary metrics as an <b>array</b>.
     *
     * @return array<string, int>
     */
    public function getProjectMetrics(): array
    {
        return [
            self::M_CYCLOMATIC_COMPLEXITY_1 => $this->ccn,
            self::M_CYCLOMATIC_COMPLEXITY_2 => $this->ccn2,
        ];
    }

    /**
     * Visits a function node.
     */
    public function visitFunction(ASTFunction $function): void
    {
        $this->fireStartFunction($function);

        if (false === $this->restoreFromCache($function)) {
            $this->calculateComplexity($function);
        }
        $this->updateProjectMetrics($function->getId());

        $this->fireEndFunction($function);
    }

    /**
     * Visits a code interface object.
     */
    public function visitInterface(ASTInterface $interface): void
    {
        // Empty visit method, we don't want interface metrics
    }

    /**
     * Visits a method node.
     */
    public function visitMethod(ASTMethod $method): void
    {
        $this->fireStartMethod($method);

        if (false === $this->restoreFromCache($method)) {
            $this->calculateComplexity($method);
        }
        $this->updateProjectMetrics($method->getId());

        $this->fireEndMethod($method);
    }

    /**
     * Visits methods, functions or closures and calculated their complexity.
     *
     * @since  0.9.8
     */
    public function calculateComplexity(AbstractASTCallable $callable): void
    {
        $this->complexityCollector = [
            self::M_CYCLOMATIC_COMPLEXITY_1 => 1,
            self::M_CYCLOMATIC_COMPLEXITY_2 => 1,
        ];

        $this->visit($callable);

        $this->metrics[$callable->getId()] = $this->complexityCollector;
    }

    /**
     * Stores the complexity of a node and updates the corresponding project
     * values.
     *
     * @param string $nodeId Identifier of the analyzed item.
     * @since  1.0.0
     */
    private function updateProjectMetrics(string $nodeId): void
    {
        $this->ccn += $this->metrics[$nodeId][self::M_CYCLOMATIC_COMPLEXITY_1] ?? 0;
        $this->ccn2 += $this->metrics[$nodeId][self::M_CYCLOMATIC_COMPLEXITY_2] ?? 0;
    }

    /**
     * Visits a boolean AND-expression.
     *
     * @param ASTBooleanAndExpression $node The currently visited node.
     * @since  0.9.8
     */
    public function visitBooleanAndExpression(ASTBooleanAndExpression $node): void
    {
        ++$this->complexityCollector[self::M_CYCLOMATIC_COMPLEXITY_2];

        $this->visit($node);
    }

    /**
     * Visits a boolean OR-expression.
     *
     * @param ASTBooleanOrExpression $node The currently visited node.
     * @since  0.9.8
     */
    public function visitBooleanOrExpression(ASTBooleanOrExpression $node): void
    {
        ++$this->complexityCollector[self::M_CYCLOMATIC_COMPLEXITY_2];

        $this->visit($node);
    }

    /**
     * Visits a switch label.
     *
     * @param ASTSwitchLabel $node The currently visited node.
     * @since  0.9.8
     */
    public function visitSwitchLabel(ASTSwitchLabel $node): void
    {
        if (!$node->isDefault()) {
            ++$this->complexityCollector[self::M_CYCLOMATIC_COMPLEXITY_1];
            ++$this->complexityCollector[self::M_CYCLOMATIC_COMPLEXITY_2];
        }

        $this->visit($node);
    }

    /**
     * Visits a catch statement.
     *
     * @param ASTCatchStatement $node The currently visited node.
     * @since  0.9.8
     */
    public function visitCatchStatement(ASTCatchStatement $node): void
    {
        ++$this->complexityCollector[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$this->complexityCollector[self::M_CYCLOMATIC_COMPLEXITY_2];

        $this->visit($node);
    }

    /**
     * Visits an elseif statement.
     *
     * @param ASTElseIfStatement $node The currently visited node.
     * @since  0.9.8
     */
    public function visitElseIfStatement(ASTElseIfStatement $node): void
    {
        ++$this->complexityCollector[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$this->complexityCollector[self::M_CYCLOMATIC_COMPLEXITY_2];

        $this->visit($node);
    }

    /**
     * Visits a for statement.
     *
     * @param ASTForStatement $node The currently visited node.
     * @since  0.9.8
     */
    public function visitForStatement(ASTForStatement $node): void
    {
        ++$this->complexityCollector[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$this->complexityCollector[self::M_CYCLOMATIC_COMPLEXITY_2];

        $this->visit($node);
    }

    /**
     * Visits a foreach statement.
     *
     * @param ASTForeachStatement $node The currently visited node.
     * @since  0.9.8
     */
    public function visitForeachStatement(ASTForeachStatement $node): void
    {
        ++$this->complexityCollector[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$this->complexityCollector[self::M_CYCLOMATIC_COMPLEXITY_2];

        $this->visit($node);
    }

    /**
     * Visits an if statement.
     *
     * @param ASTIfStatement $node The currently visited node.
     * @since  0.9.8
     */
    public function visitIfStatement(ASTIfStatement $node): void
    {
        ++$this->complexityCollector[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$this->complexityCollector[self::M_CYCLOMATIC_COMPLEXITY_2];

        $this->visit($node);
    }

    /**
     * Visits a logical AND expression.
     *
     * @param ASTLogicalAndExpression $node The currently visited node.
     * @since  0.9.8
     */
    public function visitLogicalAndExpression(ASTLogicalAndExpression $node): void
    {
        ++$this->complexityCollector[self::M_CYCLOMATIC_COMPLEXITY_2];

        $this->visit($node);
    }

    /**
     * Visits a logical OR expression.
     *
     * @param ASTLogicalOrExpression $node The currently visited node.
     * @since  0.9.8
     */
    public function visitLogicalOrExpression(ASTLogicalOrExpression $node): void
    {
        ++$this->complexityCollector[self::M_CYCLOMATIC_COMPLEXITY_2];

        $this->visit($node);
    }

    /**
     * Visits a ternary operator.
     *
     * @param ASTConditionalExpression $node The currently visited node.
     * @since  0.9.8
     */
    public function visitConditionalExpression(ASTConditionalExpression $node): void
    {
        ++$this->complexityCollector[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$this->complexityCollector[self::M_CYCLOMATIC_COMPLEXITY_2];

        $this->visit($node);
    }

    /**
     * Visits a while-statement.
     *
     * @param ASTWhileStatement $node The currently visited node.
     * @since  0.9.8
     */
    public function visitWhileStatement(ASTWhileStatement $node): void
    {
        ++$this->complexityCollector[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$this->complexityCollector[self::M_CYCLOMATIC_COMPLEXITY_2];

        $this->visit($node);
    }

    /**
     * Visits a do/while-statement.
     *
     * @param ASTDoWhileStatement $node The currently visited node.
     * @since  0.9.12
     */
    public function visitDoWhileStatement(ASTDoWhileStatement $node): void
    {
        ++$this->complexityCollector[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$this->complexityCollector[self::M_CYCLOMATIC_COMPLEXITY_2];

        $this->visit($node);
    }

    public function dispatch(ASTNode $node): void
    {
        match ($node::class) {
            ASTBooleanAndExpression::class => $this->visitBooleanAndExpression($node),
            ASTBooleanOrExpression::class => $this->visitBooleanOrExpression($node),
            ASTCatchStatement::class => $this->visitCatchStatement($node),
            ASTConditionalExpression::class => $this->visitConditionalExpression($node),
            ASTDoWhileStatement::class => $this->visitDoWhileStatement($node),
            ASTElseIfStatement::class => $this->visitElseIfStatement($node),
            ASTForeachStatement::class => $this->visitForeachStatement($node),
            ASTForStatement::class => $this->visitForStatement($node),
            ASTIfStatement::class => $this->visitIfStatement($node),
            ASTLogicalAndExpression::class => $this->visitLogicalAndExpression($node),
            ASTLogicalOrExpression::class => $this->visitLogicalOrExpression($node),
            ASTSwitchLabel::class => $this->visitSwitchLabel($node),
            ASTWhileStatement::class => $this->visitWhileStatement($node),
            default => parent::dispatch($node),
        };
    }
}
