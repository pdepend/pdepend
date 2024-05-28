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
use PDepend\Metrics\AnalyzerProjectAware;
use PDepend\Source\AST\ASTArtifact;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTCompilationUnit;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\Tokenizer\Token;
use PDepend\Source\Tokenizer\Tokens;

/**
 * This analyzer collects different lines of code metrics.
 *
 * It collects the total Lines Of Code(<b>loc</b>), the None Comment Lines Of
 * Code(<b>ncloc</b>), the Comment Lines Of Code(<b>cloc</b>) and a approximated
 * Executable Lines Of Code(<b>eloc</b>) for files, classes, interfaces,
 * methods, properties and function.
 *
 * The current implementation has a limitation, that affects inline comments.
 * The following code will suppress one line of code.
 *
 * <code>
 * function foo() {
 *     foobar(); // Bad behaviour...
 * }
 * </code>
 *
 * The same rule applies to class methods. mapi, <b>PLEASE, FIX THIS ISSUE.</b>
 *
 * @extends AbstractCachingAnalyzer<array<string, int>>
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class NodeLocAnalyzer extends AbstractCachingAnalyzer implements
    AnalyzerFilterAware,
    AnalyzerNodeAware,
    AnalyzerProjectAware
{
    /** Metrics provided by the analyzer implementation. */
    public const
        M_LINES_OF_CODE = 'loc',
        M_COMMENT_LINES_OF_CODE = 'cloc',
        M_EXECUTABLE_LINES_OF_CODE = 'eloc',
        M_LOGICAL_LINES_OF_CODE = 'lloc',
        M_NON_COMMENT_LINES_OF_CODE = 'ncloc';

    /**
     * Collected project metrics.
     *
     * @var array<string, int>
     */
    private array $projectMetrics = [
        self::M_LINES_OF_CODE => 0,
        self::M_COMMENT_LINES_OF_CODE => 0,
        self::M_EXECUTABLE_LINES_OF_CODE => 0,
        self::M_LOGICAL_LINES_OF_CODE => 0,
        self::M_NON_COMMENT_LINES_OF_CODE => 0,
    ];

    /**
     * Executable lines of code in a class. The method calculation increases
     * this property with each method's ELOC value.
     *
     * @since 0.9.12
     */
    private int $classExecutableLines = 0;

    /**
     * Logical lines of code in a class. The method calculation increases this
     * property with each method's LLOC value.
     *
     * @since 0.9.13
     */
    private int $classLogicalLines = 0;

    /**
     * This method will return an <b>array</b> with all generated metric values
     * for the given <b>$node</b> instance. If there are no metrics for the
     * requested node, this method will return an empty <b>array</b>.
     *
     * <code>
     * array(
     *     'loc'    =>  23,
     *     'cloc'   =>  17,
     *     'eloc'   =>  17,
     *     'ncloc'  =>  42
     * )
     * </code>
     *
     * @return array<string, int>
     */
    public function getNodeMetrics(ASTArtifact $artifact): array
    {
        return $this->metrics[$artifact->getId()] ?? [];
    }

    /**
     * Provides the project summary as an <b>array</b>.
     *
     * <code>
     * array(
     *     'loc'    =>  23,
     *     'cloc'   =>  17,
     *     'ncloc'  =>  42
     * )
     * </code>
     *
     * @return array<string, int>
     */
    public function getProjectMetrics(): array
    {
        return $this->projectMetrics;
    }

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
     * Visits a class node.
     */
    public function visitClass(ASTClass $class): void
    {
        $this->fireStartClass($class);

        $unit = $class->getCompilationUnit();
        if ($unit) {
            $this->dispatch($unit);
        }

        $this->classExecutableLines = 0;
        $this->classLogicalLines = 0;

        foreach ($class->getMethods() as $method) {
            $this->dispatch($method);
        }

        if ($this->restoreFromCache($class)) {
            $this->fireEndClass($class);

            return;
        }

        [$cloc] = $this->linesOfCode($class->getTokens(), true);

        $loc = $class->getEndLine() - $class->getStartLine() + 1;
        $ncloc = $loc - $cloc;

        $this->metrics[$class->getId()] = [
            self::M_LINES_OF_CODE => $loc,
            self::M_COMMENT_LINES_OF_CODE => $cloc,
            self::M_EXECUTABLE_LINES_OF_CODE => $this->classExecutableLines,
            self::M_LOGICAL_LINES_OF_CODE => $this->classLogicalLines,
            self::M_NON_COMMENT_LINES_OF_CODE => $ncloc,
        ];

        $this->fireEndClass($class);
    }

    /**
     * Visits a file node.
     */
    public function visitCompilationUnit(ASTCompilationUnit $compilationUnit): void
    {
        // Skip for dummy files
        if ($compilationUnit->getFileName() === null) {
            return;
        }
        // Check for initial file
        $id = $compilationUnit->getId();
        if (!$id || isset($this->metrics[$id])) {
            return;
        }

        $this->fireStartFile($compilationUnit);

        if ($this->restoreFromCache($compilationUnit)) {
            $this->updateProjectMetrics($id);
            $this->fireEndFile($compilationUnit);

            return;
        }

        [$cloc, $eloc, $lloc] = $this->linesOfCode($compilationUnit->getTokens());

        $loc = $compilationUnit->getEndLine();
        $ncloc = $loc - $cloc;

        $this->metrics[$id] = [
            self::M_LINES_OF_CODE => $loc,
            self::M_COMMENT_LINES_OF_CODE => $cloc,
            self::M_EXECUTABLE_LINES_OF_CODE => $eloc,
            self::M_LOGICAL_LINES_OF_CODE => $lloc,
            self::M_NON_COMMENT_LINES_OF_CODE => $ncloc,
        ];

        $this->updateProjectMetrics($id);

        $this->fireEndFile($compilationUnit);
    }

    /**
     * Visits a function node.
     */
    public function visitFunction(ASTFunction $function): void
    {
        $this->fireStartFunction($function);

        $unit = $function->getCompilationUnit();
        if ($unit) {
            $this->dispatch($unit);
        }

        if ($this->restoreFromCache($function)) {
            $this->fireEndFunction($function);

            return;
        }

        [$cloc, $eloc, $lloc] = $this->linesOfCode(
            $function->getTokens(),
            true,
        );

        $loc = $function->getEndLine() - $function->getStartLine() + 1;
        $ncloc = $loc - $cloc;

        $this->metrics[$function->getId()] = [
            self::M_LINES_OF_CODE => $loc,
            self::M_COMMENT_LINES_OF_CODE => $cloc,
            self::M_EXECUTABLE_LINES_OF_CODE => $eloc,
            self::M_LOGICAL_LINES_OF_CODE => $lloc,
            self::M_NON_COMMENT_LINES_OF_CODE => $ncloc,
        ];

        $this->fireEndFunction($function);
    }

    /**
     * Visits a code interface object.
     */
    public function visitInterface(ASTInterface $interface): void
    {
        $this->fireStartInterface($interface);

        $unit = $interface->getCompilationUnit();
        if ($unit) {
            $this->dispatch($unit);
        }

        foreach ($interface->getMethods() as $method) {
            $this->dispatch($method);
        }

        if ($this->restoreFromCache($interface)) {
            $this->fireEndInterface($interface);

            return;
        }

        [$cloc] = $this->linesOfCode($interface->getTokens(), true);

        $loc = $interface->getEndLine() - $interface->getStartLine() + 1;
        $ncloc = $loc - $cloc;

        $this->metrics[$interface->getId()] = [
            self::M_LINES_OF_CODE => $loc,
            self::M_COMMENT_LINES_OF_CODE => $cloc,
            self::M_EXECUTABLE_LINES_OF_CODE => 0,
            self::M_LOGICAL_LINES_OF_CODE => 0,
            self::M_NON_COMMENT_LINES_OF_CODE => $ncloc,
        ];

        $this->fireEndInterface($interface);
    }

    /**
     * Visits a method node.
     */
    public function visitMethod(ASTMethod $method): void
    {
        $this->fireStartMethod($method);

        if ($this->restoreFromCache($method)) {
            $this->fireEndMethod($method);

            return;
        }

        if ($method->isAbstract()) {
            $cloc = 0;
            $eloc = 0;
            $lloc = 0;
        } else {
            [$cloc, $eloc, $lloc] = $this->linesOfCode(
                $method->getTokens(),
                true,
            );
        }
        $loc = $method->getEndLine() - $method->getStartLine() + 1;
        $ncloc = $loc - $cloc;

        $this->metrics[$method->getId()] = [
            self::M_LINES_OF_CODE => $loc,
            self::M_COMMENT_LINES_OF_CODE => $cloc,
            self::M_EXECUTABLE_LINES_OF_CODE => $eloc,
            self::M_LOGICAL_LINES_OF_CODE => $lloc,
            self::M_NON_COMMENT_LINES_OF_CODE => $ncloc,
        ];

        $this->classExecutableLines += $eloc;
        $this->classLogicalLines += $lloc;

        $this->fireEndMethod($method);
    }

    /**
     * Updates the project metrics based on the node metrics identifier by the
     * given <b>$id</b>.
     *
     * @param string $id The unique identifier of a node.
     */
    private function updateProjectMetrics(string $id): void
    {
        foreach ($this->metrics[$id] as $metric => $value) {
            $this->projectMetrics[$metric] += $value;
        }
    }

    /**
     * Counts the Comment Lines Of Code (CLOC) and a pseudo Executable Lines Of
     * Code (ELOC) values.
     *
     * ELOC = Non Whitespace Lines + Non Comment Lines
     *
     * <code>
     * array(
     *     0  =>  23,  // Comment Lines Of Code
     *     1  =>  42   // Executable Lines Of Code
     * )
     * </code>
     *
     * @param array<int, Token> $tokens The raw token stream.
     * @param bool $search Optional boolean flag, search start.
     * @return array<int, int>
     */
    private function linesOfCode(array $tokens, bool $search = false): array
    {
        $clines = [];
        $elines = [];
        $llines = 0;

        $count = count($tokens);
        if ($search) {
            for ($i = 0; $i < $count; ++$i) {
                $token = $tokens[$i];

                if ($token->type === Tokens::T_CURLY_BRACE_OPEN) {
                    break;
                }
            }
        } else {
            $i = 0;
        }

        for (; $i < $count; ++$i) {
            $token = $tokens[$i];

            if (
                $token->type === Tokens::T_COMMENT
                || $token->type === Tokens::T_DOC_COMMENT
            ) {
                $lines = &$clines;
            } else {
                $lines = &$elines;
            }

            switch ($token->type) {
                // These statement are terminated by a semicolon
                // case Tokens::T_RETURN:
                // case Tokens::T_THROW:
                case Tokens::T_IF:
                case Tokens::T_TRY:
                case Tokens::T_CASE:
                case Tokens::T_GOTO:
                case Tokens::T_CATCH:
                case Tokens::T_WHILE:
                case Tokens::T_ELSEIF:
                case Tokens::T_SWITCH:
                case Tokens::T_DEFAULT:
                case Tokens::T_FOREACH:
                case Tokens::T_FUNCTION:
                case Tokens::T_SEMICOLON:
                    ++$llines;

                    break;

                case Tokens::T_DO:
                case Tokens::T_FOR:
                    // Because statements at least require one semicolon
                    --$llines;

                    break;
            }

            if ($token->startLine === $token->endLine) {
                $lines[$token->startLine] = true;
            } else {
                for ($j = $token->startLine; $j <= $token->endLine; ++$j) {
                    $lines[$j] = true;
                }
            }
            unset($lines);
        }

        return [count($clines), count($elines), $llines];
    }
}
