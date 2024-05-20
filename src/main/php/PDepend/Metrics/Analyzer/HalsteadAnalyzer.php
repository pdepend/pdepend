<?php

/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2015, Matthias Mullie <pdepend@mullie.eu>.
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
 * @copyright 2015 Matthias Mullie. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Metrics\Analyzer;

use PDepend\Metrics\AbstractCachingAnalyzer;
use PDepend\Metrics\AnalyzerNodeAware;
use PDepend\Source\AST\AbstractASTCallable;
use PDepend\Source\AST\ASTArtifact;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\Tokenizer\Tokens;

/**
 * This class calculates the Halstead Complexity Measures for the project,
 * methods and functions.
 *
 * @extends AbstractCachingAnalyzer<array<string, int>>
 *
 * @copyright 2015 Matthias Mullie. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class HalsteadAnalyzer extends AbstractCachingAnalyzer implements AnalyzerNodeAware
{
    /** Metrics provided by the analyzer implementation. */
    public const
        M_HALSTEAD_LENGTH = 'hnt', // N = N1 + N2 (total operators + operands)
        M_HALSTEAD_VOCABULARY = 'hnd', // n = n1 + n2 (distinct operators + operands)
        M_HALSTEAD_VOLUME = 'hv', // V = N * log2(n)
        M_HALSTEAD_DIFFICULTY = 'hd', // D = (n1 / 2) * (N2 / n2)
        M_HALSTEAD_LEVEL = 'hl', // L = 1 / D
        M_HALSTEAD_EFFORT = 'he', // E = V * D
        M_HALSTEAD_TIME = 'ht', // T = E / 18
        M_HALSTEAD_BUGS = 'hb', // B = (E ** (2/3)) / 3000
        M_HALSTEAD_CONTENT = 'hi'; // I = (V / D)

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
     * This method will return an <b>array</b> with all generated basis metrics
     * for the given <b>$node</b> (n1, n2, N1, N2). If there are no metrics for
     * the requested node, this method will return an empty <b>array</b>.
     *
     * @return array<string, int>
     */
    public function getNodeBasisMetrics(ASTArtifact $artifact): array
    {
        return $this->metrics[$artifact->getId()] ?? [];
    }

    /**
     * This method will return an <b>array</b> with all generated metric values
     * for the given <b>$node</b>. If there are no metrics for the requested
     * node, this method will return an empty <b>array</b>.
     *
     * @return array<string, float|int>
     */
    public function getNodeMetrics(ASTArtifact $artifact): array
    {
        $basis = $this->getNodeBasisMetrics($artifact);
        if ($basis) {
            return $this->calculateHalsteadMeasures($basis);
        }

        return [];
    }

    /**
     * Visits a function node.
     */
    public function visitFunction(ASTFunction $function): void
    {
        $this->fireStartFunction($function);

        if (false === $this->restoreFromCache($function)) {
            $this->calculateHalsteadBasis($function);
        }

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
            $this->calculateHalsteadBasis($method);
        }

        $this->fireEndMethod($method);
    }

    /**
     * @see http://www.scribd.com/doc/99533/Halstead-s-Operators-and-Operands-in-C-C-JAVA-by-Indranil-Nandy
     */
    public function calculateHalsteadBasis(AbstractASTCallable $callable): void
    {
        $operators = [];
        $operands = [];

        $skipUntil = null;

        $tokens = $callable->getTokens();
        foreach ($tokens as $i => $token) {
            /*
             * Some operations should be ignored, e.g. function declarations.
             * When we encounter a new function, we'll skip all tokens until we
             * find the closing token.
             */
            if ($skipUntil !== null) {
                if ($token->type === $skipUntil) {
                    $skipUntil = null;
                }

                continue;
            }

            switch ($token->type) {
                // A pair of parenthesis is considered a single operator.
                case Tokens::T_PARENTHESIS_CLOSE:
                case Tokens::T_CURLY_BRACE_CLOSE:
                case Tokens::T_SQUARED_BRACKET_CLOSE:
                case Tokens::T_ANGLE_BRACKET_CLOSE:
                    break;

                case Tokens::T_GOTO:
                    // A label is considered an operator if it is used as the target
                    // of a GOTO statement.
                    $operators[] = $token->image;
                    // Ignore next token as operand but count as operator instead.
                    $skipUntil = $tokens[$i + 1]->type;
                    $operators[] = $tokens[$i + 1]->image;

                    break;

                case Tokens::T_IF:
                case Tokens::T_FOR:
                case Tokens::T_FOREACH:
                case Tokens::T_WHILE:
                case Tokens::T_CATCH:
                    // case Tokens::T_SWITCH: // not followed by ()
                    // case Tokens::T_TRY: // not followed by ()
                    // case Tokens::T_DO: // always comes with while, which accounts for () already
                    /*
                     * Control structures case ...: for (...) if (...)
                     * switch (...) while(...) and try-catch (...) are treated in a
                     * special way. The colon and the parentheses are considered to
                     * be a part of the constructs. The case and the colon or the
                     * “for (...)”, “if (...)”, “switch (...)”, “while(...)”,
                     * “try-catch( )” are counted together as one operator.
                     */
                    $operators[] = $token->image;
                    /*
                     * These are always followed by parenthesis, which would add
                     * another operator (only opening parenthesis counts)
                     * so we'll have to skip that one.
                     */
                    $skipUntil = Tokens::T_PARENTHESIS_OPEN;

                    break;

                case Tokens::T_COLON:
                    /*
                     * The ternary operator ‘?’ followed by ‘:’ is considered a
                     * single operator as it is equivalent to “if-else” construct.+
                     *
                     * Colon is used after keyword, where it counts as part of
                     * that operator, or in ternary operator, where it also
                     * counts as 1.
                     */
                    break;

                case Tokens::T_DOC_COMMENT:
                case Tokens::T_COMMENT:
                    // The comments are considered neither an operator nor an operand.
                    break;

                case Tokens::T_NEW:
                    /*
                     * `new` is considered same as the function call, mainly because
                     * it's equivalent to the function call.
                     */
                    break;

                case Tokens::T_ARRAY:
                    /*
                     * Like T_IF & co, array(..) needs 3 tokens ("array", "(" and
                     * ")") for what's essentially just 1 operator.
                     */
                    break;

                case Tokens::T_NULLSAFE_OBJECT_OPERATOR:
                case Tokens::T_OBJECT_OPERATOR:
                case Tokens::T_DOUBLE_COLON:
                    /*
                     * Class::method or $object->method both only count as 1
                     * identifier, even though they consist of 3 tokens.
                     */
                    // Glue ->/:: and before & after parts together.
                    $image = array_pop($operands) . $token->image . $tokens[$i + 1]->image;
                    $operands[] = $image;

                    // Skip next part (would be seen as operand)
                    $skipUntil = $tokens[$i + 1]->type;

                    break;

                case Tokens::T_START_HEREDOC:
                case Tokens::T_END_HEREDOC:
                    // Ignore HEREDOC delimiters.
                    break;

                case Tokens::T_OPEN_TAG:
                case Tokens::T_CLOSE_TAG:
                case Tokens::T_NO_PHP:
                    // Ignore PHP open & close tags and non-PHP content.
                    break;

                case Tokens::T_FUNCTION:
                    /*
                     * The function name is considered a single operator when it
                     * appears as calling a function, but when it appears in
                     * declarations or in function definitions it is not counted as
                     * operator.
                     * Default parameter assignments are not counted.
                     */
                    // Because `)` could appear in default argument assignment
                    // (`$var = array()`), we need to skip until `{`, but that
                    // one should be included in operators.
                    $skipUntil = Tokens::T_CURLY_BRACE_OPEN;
                    $operators[] = '{';

                    break;

                case Tokens::T_VAR:
                case Tokens::T_CONST:
                    /*
                     * When variables or constants appear in declaration they are
                     * not considered as operands, they are considered operands only
                     * when they appear with operators in expressions.
                     */
                    $skipUntil = Tokens::T_SEMICOLON;

                    break;

                case Tokens::T_STRING:
                    // `define` is T_STRING, just like any other identifier.
                    if ($token->image === 'define') {
                        // Undo all of "define", "(", name, ",", value, ")"
                        $skipUntil = Tokens::T_PARENTHESIS_CLOSE;
                    } else {
                        $operands[] = $token->image;
                    }

                    break;

                case Tokens::T_CONSTANT_ENCAPSED_STRING:
                case Tokens::T_VARIABLE:
                case Tokens::T_LNUMBER:
                case Tokens::T_DNUMBER:
                case Tokens::T_NUM_STRING:
                case Tokens::T_NULL:
                case Tokens::T_TRUE:
                case Tokens::T_FALSE:
                case Tokens::T_CLASS_FQN:
                case Tokens::T_LINE:
                case Tokens::T_METHOD_C:
                case Tokens::T_NS_C:
                case Tokens::T_DIR:
                case Tokens::T_ENCAPSED_AND_WHITESPACE: // content of HEREDOC
                    $operands[] = $token->image;

                    break;

                default:
                    // Everything else is an operator.
                    $operators[] = $token->image;

                    break;
            }
        }

        $this->metrics[$callable->getId()] = [
            'n1' => count(array_unique($operators)),
            'n2' => count(array_unique($operands)),
            'N1' => count($operators),
            'N2' => count($operands),
        ];
    }

    /**
     * Calculates Halstead measures from n1, n2, N1 & N2.
     *
     * @param array<string, int> $basis [n1, n2, N1, N2]
     * @return array<string, float|int>
     * @see http://www.verifysoft.com/en_halstead_metrics.html
     * @see http://www.grammatech.com/codesonar/workflow-features/halstead
     */
    public function calculateHalsteadMeasures(array $basis): array
    {
        $measures = [];
        $measures[self::M_HALSTEAD_LENGTH] = $basis['N1'] + $basis['N2'];
        $measures[self::M_HALSTEAD_VOCABULARY] = $basis['n1'] + $basis['n2'];
        $measures[self::M_HALSTEAD_VOLUME] =
            $measures[self::M_HALSTEAD_LENGTH] * log($measures[self::M_HALSTEAD_VOCABULARY], 2);
        $measures[self::M_HALSTEAD_DIFFICULTY] = ($basis['n1'] / 2) * ($basis['N1'] / ($basis['n2'] ?: 1));
        $measures[self::M_HALSTEAD_LEVEL] = 1 / ($measures[self::M_HALSTEAD_DIFFICULTY] ?: 1);
        $measures[self::M_HALSTEAD_EFFORT] =
            $measures[self::M_HALSTEAD_VOLUME] * $measures[self::M_HALSTEAD_DIFFICULTY];
        $measures[self::M_HALSTEAD_TIME] = $measures[self::M_HALSTEAD_EFFORT] / 18;
        $measures[self::M_HALSTEAD_BUGS] = $measures[self::M_HALSTEAD_EFFORT] ** (2 / 3) / 3000;
        $measures[self::M_HALSTEAD_CONTENT] =
            $measures[self::M_HALSTEAD_VOLUME] / ($measures[self::M_HALSTEAD_DIFFICULTY] ?: 1);

        return $measures;
    }
}
