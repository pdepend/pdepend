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
use PDepend\Metrics\AnalyzerFilterAware;
use PDepend\Metrics\AnalyzerNodeAware;
use PDepend\Source\AST\AbstractASTCallable;
use PDepend\Source\AST\ASTArtifact;
use PDepend\Source\AST\ASTBooleanAndExpression;
use PDepend\Source\AST\ASTBooleanOrExpression;
use PDepend\Source\AST\ASTConditionalExpression;
use PDepend\Source\AST\ASTDoWhileStatement;
use PDepend\Source\AST\ASTElseIfStatement;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTForeachStatement;
use PDepend\Source\AST\ASTForStatement;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTIfStatement;
use PDepend\Source\AST\ASTLogicalAndExpression;
use PDepend\Source\AST\ASTLogicalOrExpression;
use PDepend\Source\AST\ASTLogicalXorExpression;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\AST\ASTReturnStatement;
use PDepend\Source\AST\ASTStatement;
use PDepend\Source\AST\ASTSwitchLabel;
use PDepend\Source\AST\ASTSwitchStatement;
use PDepend\Source\AST\ASTTryStatement;
use PDepend\Source\AST\ASTWhileStatement;

/**
 * This analyzer calculates the NPath complexity of functions and methods. The
 * NPath complexity metric measures the acyclic execution paths through a method
 * or function. See Nejmeh, Communications of the ACM Feb 1988 pp 188-200.
 *
 * @extends AbstractCachingAnalyzer<int>
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class NPathComplexityAnalyzer extends AbstractCachingAnalyzer implements AnalyzerFilterAware, AnalyzerNodeAware
{
    /** Metrics provided by the analyzer implementation. */
    private const M_NPATH_COMPLEXITY = 'npath';

    private int $complexityCollector;

    /** @var list<int> */
    private array $complexityCollectorStack = [];

    /**
     * Processes all {@link ASTNamespace} code nodes.
     */
    public function analyze($namespaces): void
    {
        if (!isset($this->metrics)) {
            $this->loadCache();
            $this->fireStartAnalyzer();

            $this->metrics = [];
            foreach ($namespaces as $namespace) {
                $this->dispatch($namespace);
            }

            $this->fireEndAnalyzer();
            $this->unloadCache();
        }
    }

    /**
     * This method will return an <b>array</b> with all generated metric values
     * for the node with the given <b>$id</b> identifier. If there are no
     * metrics for the requested node, this method will return an empty <b>array</b>.
     *
     * <code>
     * array(
     *     'npath'  =>  17
     * )
     * </code>
     *
     * @return array<string, int>
     */
    public function getNodeMetrics(ASTArtifact $artifact): array
    {
        if (isset($this->metrics[$artifact->getId()])) {
            return [self::M_NPATH_COMPLEXITY => $this->metrics[$artifact->getId()]];
        }

        return [];
    }

    /**
     * Visits a function node.
     */
    public function visitFunction(ASTFunction $function): void
    {
        $this->fireStartFunction($function);

        if (!$this->restoreFromCache($function)) {
            $this->calculateComplexity($function);
        }

        $this->fireEndFunction($function);
    }

    /**
     * Visits a method node.
     */
    public function visitMethod(ASTMethod $method): void
    {
        $this->fireStartMethod($method);

        if (!$this->restoreFromCache($method)) {
            $this->calculateComplexity($method);
        }

        $this->fireEndMethod($method);
    }

    /**
     * This method will calculate the NPath complexity for the given callable
     * instance.
     *
     * @since  0.9.12
     */
    protected function calculateComplexity(AbstractASTCallable $callable): void
    {
        $this->complexityCollector = 1;
        $this->visit($callable);
        $this->metrics[$callable->getId()] = $this->complexityCollector;
    }

    /**
     * This method calculates the NPath Complexity of a conditional-statement,
     * the meassured value is then returned as a string.
     *
     * <code>
     * <expr1> ? <expr2> : <expr3>
     *
     * -- NP(?) = NP(<expr1>) + NP(<expr2>) + NP(<expr3>) + 2 --
     * </code>
     *
     * @since  0.9.12
     */
    public function visitConditionalExpression(ASTConditionalExpression $node): void
    {
        $npath = 0;

        // Calculate the complexity of the condition
        $parent = $node->getParent();
        if ($parent) {
            $npath = $this->sumComplexity($parent->getChild(0));
        }

        // New PHP 5.3 ifsetor-operator $x ?: $y
        if (count($node->getChildren()) === 1) {
            $npath *= 2;
        }

        // The complexity of each child has no minimum
        foreach ($node->getChildren() as $child) {
            $npath += $this->sumComplexity($child);
        }

        // Add 2 for the branching per the NPath spec
        $npath += 2;

        $this->complexityCollector *= $npath;
    }

    /**
     * This method calculates the NPath Complexity of a do-while-statement, the
     * meassured value is then returned as a string.
     *
     * <code>
     * do
     *   <do-range>
     * while (<expr>)
     * S;
     *
     * -- NP(do) = NP(<do-range>) + NP(<expr>) + 1 --
     * </code>
     *
     * @param ASTDoWhileStatement $node The currently visited node.
     * @since  0.9.12
     */
    public function visitDoWhileStatement(ASTDoWhileStatement $node): void
    {
        $this->pushComplexityCollector();
        $this->dispatch($node->getChild(0));
        $expr = $this->sumComplexity($node->getChild(1));

        $npath = $this->complexityCollector + $expr + 1;

        $this->popComplexityCollector();
        $this->complexityCollector *= $npath;
    }

    /**
     * This method calculates the NPath Complexity of an elseif-statement, the
     * meassured value is then returned as a string.
     *
     * <code>
     * elseif (<expr>)
     *   <elseif-range>
     * S;
     *
     * -- NP(elseif) = NP(<elseif-range>) + NP(<expr>) + 1 --
     *
     *
     * elseif (<expr>)
     *   <elseif-range>
     * else
     *   <else-range>
     * S;
     *
     * -- NP(if) = NP(<if-range>) + NP(<expr>) + NP(<else-range> --
     * </code>
     *
     * @param ASTElseIfStatement $node The currently visited node.
     * @since  0.9.12
     */
    public function visitElseIfStatement(ASTElseIfStatement $node): void
    {
        $npath = $this->sumComplexity($node->getChild(0));
        foreach ($node->getChildren() as $child) {
            if ($child instanceof ASTStatement) {
                $this->pushComplexityCollector();
                $this->dispatch($child);
                $npath += $this->complexityCollector;
                $this->popComplexityCollector();
            }
        }

        if (!$node->hasElse()) {
            ++$npath;
        }

        $this->complexityCollector *= $npath;
    }

    /**
     * This method calculates the NPath Complexity of a for-statement, the
     * meassured value is then returned as a string.
     *
     * <code>
     * for (<expr1>; <expr2>; <expr3>)
     *   <for-range>
     * S;
     *
     * -- NP(for) = NP(<for-range>) + NP(<expr1>) + NP(<expr2>) + NP(<expr3>) + 1 --
     * </code>
     *
     * @param ASTForStatement $node The currently visited node.
     * @since  0.9.12
     */
    public function visitForStatement(ASTForStatement $node): void
    {
        $npath = 1;
        foreach ($node->getChildren() as $child) {
            if ($child instanceof ASTStatement) {
                $this->pushComplexityCollector();
                $this->dispatch($child);
                $npath += $this->complexityCollector;
                $this->popComplexityCollector();
            } elseif ($child instanceof ASTExpression) {
                $npath += $this->sumComplexity($child);
            }
        }

        $this->complexityCollector *= $npath;
    }

    /**
     * This method calculates the NPath Complexity of a for-statement, the
     * meassured value is then returned as a string.
     *
     * <code>
     * fpreach (<expr>)
     *   <foreach-range>
     * S;
     *
     * -- NP(foreach) = NP(<foreach-range>) + NP(<expr>) + 1 --
     * </code>
     *
     * @param ASTForeachStatement $node The currently visited node.
     * @since  0.9.12
     */
    public function visitForeachStatement(ASTForeachStatement $node): void
    {
        $npath = $this->sumComplexity($node->getChild(0)) + 1;

        foreach ($node->getChildren() as $child) {
            if ($child instanceof ASTStatement) {
                $this->pushComplexityCollector();
                $this->dispatch($child);
                $npath += $this->complexityCollector;
                $this->popComplexityCollector();
            }
        }

        $this->complexityCollector *= $npath;
    }

    /**
     * This method calculates the NPath Complexity of an if-statement, the
     * meassured value is then returned as a string.
     *
     * <code>
     * if (<expr>)
     *   <if-range>
     * S;
     *
     * -- NP(if) = NP(<if-range>) + NP(<expr>) + 1 --
     *
     *
     * if (<expr>)
     *   <if-range>
     * else
     *   <else-range>
     * S;
     *
     * -- NP(if) = NP(<if-range>) + NP(<expr>) + NP(<else-range> --
     * </code>
     *
     * @param ASTIfStatement $node The currently visited node.
     * @since  0.9.12
     */
    public function visitIfStatement(ASTIfStatement $node): void
    {
        $npath = $this->sumComplexity($node->getChild(0));

        foreach ($node->getChildren() as $child) {
            if ($child instanceof ASTStatement) {
                $this->pushComplexityCollector();
                $this->dispatch($child);
                $npath += $this->complexityCollector;
                $this->popComplexityCollector();
            }
        }

        if (!$node->hasElse()) {
            ++$npath;
        }

        $this->complexityCollector *= $npath;
    }

    /**
     * This method calculates the NPath Complexity of a return-statement, the
     * meassured value is then returned as a string.
     *
     * <code>
     * return <expr>;
     *
     * -- NP(return) = NP(<expr>) --
     * </code>
     *
     * @param ASTReturnStatement $node The currently visited node.
     * @since  0.9.12
     */
    public function visitReturnStatement(ASTReturnStatement $node): void
    {
        $this->complexityCollector *= $this->sumComplexity($node) ?: 1;
    }

    /**
     * This method calculates the NPath Complexity of a switch-statement, the
     * meassured value is then returned as a string.
     *
     * <code>
     * switch (<expr>)
     *   <case-range1>
     *   <case-range2>
     *   ...
     *   <default-range>
     *
     * -- NP(switch) = NP(<expr>) + NP(<default-range>) +  NP(<case-range1>) ... --
     * </code>
     *
     * @param ASTSwitchStatement $node The currently visited node.
     * @since  0.9.12
     */
    public function visitSwitchStatement(ASTSwitchStatement $node): void
    {
        $npath = $this->sumComplexity($node->getChild(0));
        foreach ($node->getChildren() as $child) {
            if ($child instanceof ASTSwitchLabel) {
                $this->pushComplexityCollector();
                $this->dispatch($child);
                $npath += $this->complexityCollector;
                $this->popComplexityCollector();
            }
        }

        $this->complexityCollector *= $npath;
    }

    /**
     * This method calculates the NPath Complexity of a try-catch-statement, the
     * meassured value is then returned as a string.
     *
     * <code>
     * try
     *   <try-range>
     * catch
     *   <catch-range>
     *
     * -- NP(try) = NP(<try-range>) + NP(<catch-range>) --
     *
     *
     * try
     *   <try-range>
     * catch
     *   <catch-range1>
     * catch
     *   <catch-range2>
     * ...
     *
     * -- NP(try) = NP(<try-range>) + NP(<catch-range1>) + NP(<catch-range2>) ... --
     * </code>
     *
     * @param ASTTryStatement $node The currently visited node.
     * @since  0.9.12
     */
    public function visitTryStatement(ASTTryStatement $node): void
    {
        $npath = 0;
        foreach ($node->getChildren() as $child) {
            if ($child instanceof ASTStatement) {
                $this->pushComplexityCollector();
                $this->dispatch($child);
                $npath += $this->complexityCollector;
                $this->popComplexityCollector();
            }
        }

        $this->complexityCollector *= $npath;
    }

    /**
     * This method calculates the NPath Complexity of a while-statement, the
     * meassured value is then returned as a string.
     *
     * <code>
     * while (<expr>)
     *   <while-range>
     * S;
     *
     * -- NP(while) = NP(<while-range>) + NP(<expr>) + 1 --
     * </code>
     *
     * @param ASTWhileStatement $node The currently visited node.
     * @since  0.9.12
     */
    public function visitWhileStatement(ASTWhileStatement $node): void
    {
        $expr = $this->sumComplexity($node->getChild(0));
        $this->pushComplexityCollector();
        $this->dispatch($node->getChild(1));

        $npath = $this->complexityCollector + $expr + 1;

        $this->popComplexityCollector();
        $this->complexityCollector *= $npath;
    }

    public function dispatch(ASTNode $node): void
    {
        match ($node::class) {
            ASTConditionalExpression::class => $this->visitConditionalExpression($node),
            ASTDoWhileStatement::class => $this->visitDoWhileStatement($node),
            ASTElseIfStatement::class => $this->visitElseIfStatement($node),
            ASTForeachStatement::class => $this->visitForeachStatement($node),
            ASTForStatement::class => $this->visitForStatement($node),
            ASTIfStatement::class => $this->visitIfStatement($node),
            ASTReturnStatement::class => $this->visitReturnStatement($node),
            ASTSwitchStatement::class => $this->visitSwitchStatement($node),
            ASTTryStatement::class => $this->visitTryStatement($node),
            ASTWhileStatement::class => $this->visitWhileStatement($node),
            default => parent::dispatch($node),
        };
    }

    /**
     * Calculates the expression sum of the given node.
     *
     * @param ASTNode $node The currently visited node.
     * @since  0.9.12
     * @todo   I don't like this method implementation, it should be possible to
     *       implement this method with more visitor behavior for the boolean
     *       and logical expressions.
     */
    public function sumComplexity(ASTNode $node): int
    {
        $sum = 0;
        $this->pushComplexityCollector();
        if ($node instanceof ASTConditionalExpression) {
            $this->dispatch($node);
            $sum += $this->complexityCollector;
        } elseif (
            $node instanceof ASTBooleanAndExpression
            || $node instanceof ASTBooleanOrExpression
            || $node instanceof ASTLogicalAndExpression
            || $node instanceof ASTLogicalOrExpression
            || $node instanceof ASTLogicalXorExpression
        ) {
            ++$sum;
        } else {
            foreach ($node->getChildren() as $child) {
                $sum += $this->sumComplexity($child);
            }
        }

        $this->popComplexityCollector();

        return $sum;
    }

    /**
     * Push the current collector value onto the stack
     */
    private function pushComplexityCollector(): void
    {
        $this->complexityCollectorStack[] = $this->complexityCollector;
        $this->complexityCollector = 1;
    }

    /**
     * Pop the last collector value off the stack and return it
     */
    private function popComplexityCollector(): void
    {
        $this->complexityCollector = array_pop($this->complexityCollectorStack) ?: 1;
    }
}
