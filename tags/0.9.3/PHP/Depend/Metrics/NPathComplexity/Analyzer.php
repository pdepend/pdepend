<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once 'PHP/Depend/Metrics/AbstractAnalyzer.php';
require_once 'PHP/Depend/Metrics/AnalyzerI.php';
require_once 'PHP/Depend/Metrics/FilterAwareI.php';
require_once 'PHP/Depend/Metrics/NodeAwareI.php';
require_once 'PHP/Depend/Util/MathUtil.php';

/**
 * This analyzer calculates the NPath complexity of functions and methods. The
 * NPath complexity metric measures the acyclic execution paths through a method
 * or function. See Nejmeh, Communications of the ACM Feb 1988 pp 188-200.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Metrics_NPathComplexity_Analyzer
       extends PHP_Depend_Metrics_AbstractAnalyzer
    implements PHP_Depend_Metrics_AnalyzerI,
               PHP_Depend_Metrics_FilterAwareI,
               PHP_Depend_Metrics_NodeAwareI
{
    /**
     * List of valid token types that are relevant for the npath complexity
     * calculation.
     *
     * @var array(integer=>boolean) $_validTokens
     */
    private static $_validTokens = array(
        PHP_Depend_ConstantsI::T_CLOSE_TAG         => true,
        PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN  => true,
        PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE => true,
        PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN  => true,
        PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE => true,
        PHP_Depend_ConstantsI::T_COMMENT           => true,
        PHP_Depend_ConstantsI::T_DOC_COMMENT       => true,
        PHP_Depend_ConstantsI::T_SEMICOLON         => true,
        PHP_Depend_ConstantsI::T_QUESTION_MARK     => true,
        PHP_Depend_ConstantsI::T_COLON             => true,
        PHP_Depend_ConstantsI::T_RETURN            => true,
        PHP_Depend_ConstantsI::T_DO                => true,
        PHP_Depend_ConstantsI::T_FOR               => true,
        PHP_Depend_ConstantsI::T_IF                => true,
        PHP_Depend_ConstantsI::T_ELSE              => true,
        PHP_Depend_ConstantsI::T_ELSEIF            => true,
        PHP_Depend_ConstantsI::T_FOREACH           => true,
        PHP_Depend_ConstantsI::T_WHILE             => true,
        PHP_Depend_ConstantsI::T_SWITCH            => true,
        PHP_Depend_ConstantsI::T_CASE              => true,
        PHP_Depend_ConstantsI::T_DEFAULT           => true,
        PHP_Depend_ConstantsI::T_BOOLEAN_AND       => true,
        PHP_Depend_ConstantsI::T_BOOLEAN_OR        => true,
        PHP_Depend_ConstantsI::T_LOGICAL_AND       => true,
        PHP_Depend_ConstantsI::T_LOGICAL_OR        => true,
        PHP_Depend_ConstantsI::T_LOGICAL_XOR       => true,
        PHP_Depend_ConstantsI::T_TRY               => true,
        PHP_Depend_ConstantsI::T_CATCH             => true,
    );

    /**
     * The current token array.
     *
     * @var array $_tokens
     */
    private $_tokens = array();

    /**
     * The current token array index position.
     *
     * @var integer $_index
     */
    private $_index = 0;

    /**
     * The length of the token array.
     *
     * @var integer $_length
     */
    private $_length = 0;

    /**
     * This property will hold the calculated NPath Complexity values for the
     * analyzed functions and methods.
     *
     * <code>
     * array(
     *     'uuid1'  =>  '17',
     *     'uuid2'  =>  '42',
     *     // ...
     * )
     * </code>
     *
     * @var array(string=>string) $_metrics
     */
    private $_metrics = null;

    /**
     * Processes all {@link PHP_Depend_Code_Package} code nodes.
     *
     * @param PHP_Depend_Code_NodeIterator $packages All code packages.
     *
     * @return void
     */
    public function analyze(PHP_Depend_Code_NodeIterator $packages)
    {
        // Check for previous run
        if ($this->_metrics === null) {

            $this->fireStartAnalyzer();

            // Init node metrics
            $this->_metrics = array();

            // Process all packages
            foreach ($packages as $package) {
                $package->accept($this);
            }

            $this->fireEndAnalyzer();
        }
    }

    /**
     * This method will return an <b>array</b> with all generated metric values
     * for the node with the given <b>$uuid</b> identifier. If there are no
     * metrics for the requested node, this method will return an empty <b>array</b>.
     *
     * <code>
     * array(
     *     'npath'  =>  '17'
     * )
     * </code>
     *
     * @param PHP_Depend_Code_NodeI $node The context node instance.
     *
     * @return array(string=>mixed)
     */
    public function getNodeMetrics(PHP_Depend_Code_NodeI $node)
    {
        $metric = array();
        if (isset($this->_metrics[$node->getUUID()])) {
            $metric = array('npath' => $this->_metrics[$node->getUUID()]);
        }
        return $metric;
    }

    /**
     * Visits a code interface object.
     *
     * @param PHP_Depend_Code_Interface $interface The context code interface.
     *
     * @return void
     * @see PHP_Depend_VisitorI::visitInterface()
     */
    public function visitInterface(PHP_Depend_Code_Interface $interface)
    {
        // Empty visit method, we don't want interface metrics
    }

    /**
     * Visits a function node.
     *
     * @param PHP_Depend_Code_Function $function The current function node.
     *
     * @return void
     * @see PHP_Depend_Visitor_AbstractVisitor::visitFunction()
     */
    public function visitFunction(PHP_Depend_Code_Function $function)
    {
        $this->fireStartFunction($function);

        $this->_calculateMethodOrFunction($function->getUUID(),
                                          $function->getTokens());

        $this->fireEndFunction($function);
    }

    /**
     * Visits a method node.
     *
     * @param PHP_Depend_Code_Class $method The method class node.
     *
     * @return void
     * @see PHP_Depend_Visitor_AbstractVisitor::visitMethod()
     */
    public function visitMethod(PHP_Depend_Code_Method $method)
    {
        $this->fireStartMethod($method);

        $this->_calculateMethodOrFunction($method->getUUID(),
                                          $method->getTokens());

        $this->fireEndMethod($method);
    }

    /**
     * This method calculates the NPath complexity for the given <b>$tokens</b>
     * array of a function or method. The result value is stored in the object's
     * <strong>$_metrics</strong> with the key <strong>$uuid</strong>.
     *
     * @param string $uuid   The unique function or method identifier.
     * @param array  $tokens The function or method body tokens.
     *
     * @return void
     */
    private function _calculateMethodOrFunction($uuid, array $tokens)
    {
        $this->_tokens = array();
        foreach ($tokens as $token) {
            if (isset(self::$_validTokens[$token->type])) {
                $this->_tokens[] = $token;
            }
        }

        $this->_index  = 0;
        $this->_length = count($this->_tokens);

        $this->_metrics[$uuid] = $this->_calculateScope();
    }

    /**
     * This method calculates the complexity of a scope '{' ... '}' or a single
     * statement. Then it returns the complexity value as a string.
     *
     * @return string
     */
    private function _calculateScopeOrStatement()
    {
        $npath = '1';
        if (($token = current($this->_tokens)) !== false) {
            if ($token->type ===  PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN) {
                $npath = $this->_calculateScope();
            } else {
                $npath = $this->_calculateStatement();
            }
        }
        return $npath;
    }

    /**
     * This method calculates the complexity of a scope block, then it returns
     * the complexity value as a string.
     *
     * @return string
     */
    private function _calculateScope()
    {
        $npath = '1';
        $scope = 0;

        while (($token = current($this->_tokens)) !== false) {
            if ($token->type === PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN) {
                next($this->_tokens);
                ++$scope;
            } else if ($token->type === PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE) {
                next($this->_tokens);
                --$scope;
            }

            if ($scope === 0) {
                break;
            }

            $value = $this->_calculateScopeOrStatement();
            $npath = PHP_Depend_Util_MathUtil::mul($npath, $value);
        }
        return $npath;
    }

    /**
     * This method calculates the NPath complexity for a statement, then it
     * returns the complexity value as a string.
     *
     * @return string
     */
    private function _calculateStatement()
    {
        $token = current($this->_tokens);
        while ($token !== false) {

            switch ($token->type) {

            case PHP_Depend_ConstantsI::T_IF:
            case PHP_Depend_ConstantsI::T_ELSEIF:
                return $this->_calculateIfStatement();

            case PHP_Depend_ConstantsI::T_WHILE:
                return $this->_calculateWhileStatement();

            case PHP_Depend_ConstantsI::T_DO:
                return $this->_calculateDoStatement();

            case PHP_Depend_ConstantsI::T_FOR:
                return $this->_calculateForStatement();

            case PHP_Depend_ConstantsI::T_FOREACH:
                return $this->_calculateForeachStatement();

            case PHP_Depend_ConstantsI::T_RETURN:
                return $this->_calculateReturnStatement();

            case PHP_Depend_ConstantsI::T_SWITCH:
                return $this->_calculateSwitchStatement();

            case PHP_Depend_ConstantsI::T_TRY:
                return $this->_calculateTryStatement();

            case PHP_Depend_ConstantsI::T_QUESTION_MARK:
                return $this->_calculateConditionalStatement();

            case PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE:
                break 2;

            default:
                next($this->_tokens);
            }
            break;
        }
        return '1';
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
     * @return string
     */
    private function _calculateIfStatement()
    {
        // Remove <if> token
        next($this->_tokens);

        $npath = $this->_sumExpressionComplexity();
        $npath = PHP_Depend_Util_MathUtil::add($npath,
                                               $this->_calculateScopeOrStatement());

        $value = '1';
        if ($token = current($this->_tokens)) {
            if ($token->type === PHP_Depend_ConstantsI::T_ELSE) {
                next($this->_tokens);
                $value = $this->_calculateScopeOrStatement();
            } else if ($token->type === PHP_Depend_ConstantsI::T_ELSEIF) {
                $value = $this->_calculateStatement();
            }
        } else {
            // FIXME: Log error
        }
        return PHP_Depend_Util_MathUtil::add($npath, $value);
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
     * @return string
     */
    private function _calculateWhileStatement()
    {
        // Remove <while> token
        next($this->_tokens);

        $npath = '1';

        $value = $this->_sumExpressionComplexity();
        $npath = PHP_Depend_Util_MathUtil::add($npath, $value);

        $value = $this->_calculateScopeOrStatement();
        $npath = PHP_Depend_Util_MathUtil::add($npath, $value);

        return $npath;
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
     * @return string
     */
    private function _calculateDoStatement()
    {
        // Remove <do> token
        next($this->_tokens);

        $npath = '1';

        $value = $this->_calculateScopeOrStatement();
        $npath = PHP_Depend_Util_MathUtil::add($npath, $value);

        // Remove <while> token
        next($this->_tokens);

        $value = $this->_sumExpressionComplexity();
        $npath = PHP_Depend_Util_MathUtil::add($npath, $value);

        return $npath;
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
     * @return string
     */
    private function _calculateForStatement()
    {
        // Remove <for> token
        next($this->_tokens);

        $npath = '1';

        $value = $this->_sumExpressionComplexity();
        $npath = PHP_Depend_Util_MathUtil::add($npath, $value);

        $value = $this->_calculateScopeOrStatement();
        $npath = PHP_Depend_Util_MathUtil::add($npath, $value);

        return $npath;
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
     * @return string
     */
    private function _calculateForeachStatement()
    {
        // Remove <foreach> token
        next($this->_tokens);

        $npath = '1';

        $value = $this->_sumExpressionComplexity();
        $npath = PHP_Depend_Util_MathUtil::add($npath, $value);

        $value = $this->_calculateScopeOrStatement();
        $npath = PHP_Depend_Util_MathUtil::add($npath, $value);

        return $npath;
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
     * @return string
     */
    private function _calculateReturnStatement()
    {
        // Remove <return> token
        next($this->_tokens);

        $npath = '0';
        while (($token = current($this->_tokens)) !== false) {
            switch ($token->type) {

            case PHP_Depend_ConstantsI::T_QUESTION_MARK:
                $compl = $this->_calculateConditionalStatement();
                $npath = PHP_Depend_Util_MathUtil::add($npath, $compl);
                continue 2;

            case PHP_Depend_ConstantsI::T_BOOLEAN_AND:
            case PHP_Depend_ConstantsI::T_BOOLEAN_OR:
            case PHP_Depend_ConstantsI::T_LOGICAL_AND:
            case PHP_Depend_ConstantsI::T_LOGICAL_OR:
            case PHP_Depend_ConstantsI::T_LOGICAL_XOR:
                $npath = PHP_Depend_Util_MathUtil::add('1', $npath);
                break;

            case PHP_Depend_ConstantsI::T_CLOSE_TAG:
            case PHP_Depend_ConstantsI::T_SEMICOLON:
                break 2;

            }

            next($this->_tokens);
        }
        return ($npath === '0' ? '1' : $npath);
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
     * @return string
     */
    private function _calculateSwitchStatement()
    {
        // Remove <switch> token
        next($this->_tokens);

        $scope = 0;

        $case  = '0';
        $npath = $this->_sumExpressionComplexity();
        while (($token = current($this->_tokens)) !== false) {
            switch ($token->type) {

            case PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN:
                ++$scope;
                $token = next($this->_tokens);
                break;

            case PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE:
                --$scope;
                $token = next($this->_tokens);
                break;

            case PHP_Depend_ConstantsI::T_CASE:
            case PHP_Depend_ConstantsI::T_DEFAULT:
                $token = next($this->_tokens);
                $npath = PHP_Depend_Util_MathUtil::add($npath, $case);
                $case  = '1';
                break;

            default:
                $comp = $this->_calculateScopeOrStatement();
                $case = PHP_Depend_Util_MathUtil::mul($case, $comp);
                break;
            }

            if ($scope === 0) {
                break;
            }

        }

        return PHP_Depend_Util_MathUtil::add($npath, $case);
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
     * @return string
     */
    private function _calculateTryStatement()
    {
        // Remove <try> token
        next($this->_tokens);

        $npath = $this->_calculateScopeOrStatement();
        while (($token = next($this->_tokens)) !== false) {

            $this->_sumExpressionComplexity();

            $compl = $this->_calculateScopeOrStatement();
            $npath = PHP_Depend_Util_MathUtil::add($compl, $npath);

            $token = current($this->_tokens);
            if ($token->type === PHP_Depend_ConstantsI::T_CATCH) {
                continue;
            }
            break;
        }
        return $npath;
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
     * @return string
     */
    private function _calculateConditionalStatement()
    {
        // Remove < ? > token
        next($this->_tokens);

        // We don't know the complexity of the first expression
        $input = array('0', '0');
        $scope = 1;
        $colon = 0;

        while (($token = current($this->_tokens)) !== false) {
            switch ($token->type) {

            case PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN:
            case PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN:
                ++$scope;
                break;

            case PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE:
            case PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE:
                --$scope;
                break;

            case PHP_Depend_ConstantsI::T_QUESTION_MARK:
                $complex  = $this->_calculateConditionalStatement();
                $input[0] = PHP_Depend_Util_MathUtil::add($complex, $input[0]);
                break;

            case PHP_Depend_ConstantsI::T_COLON:
                if ($colon === 0) {
                    array_unshift($input, '0');
                } else if ($colon === 1) {
                    // Nested conditional statement
                    break 2;
                }
                ++$colon;
                break;

            case PHP_Depend_ConstantsI::T_BOOLEAN_AND:
            case PHP_Depend_ConstantsI::T_BOOLEAN_OR:
            case PHP_Depend_ConstantsI::T_LOGICAL_AND:
            case PHP_Depend_ConstantsI::T_LOGICAL_OR:
            case PHP_Depend_ConstantsI::T_LOGICAL_XOR:
                $input[0] = PHP_Depend_Util_MathUtil::add(1, $input[0]);
                break;

            case PHP_Depend_ConstantsI::T_SEMICOLON:
            case PHP_Depend_ConstantsI::T_CLOSE_TAG:
                break 2;
            }

            if ($scope === 0) {
                break;
            }

            next($this->_tokens);
        }

        $input = array_pad(array_filter($input), 5, '1');
        $npath = '0';
        foreach ($input as $value) {
            $npath = PHP_Depend_Util_MathUtil::add($npath, $value);
        }
        return $npath;
    }

    /**
     * This method calculates the NPath Complexity of a block in parenthesis and
     * it returns the meassured value as a string.
     *
     * <code>
     * ($a && ($b || $c))
     * ($array as $a => $b)
     * ...
     * </code>
     *
     * @return string
     */
    private function _sumExpressionComplexity()
    {
        $npath = '0';
        $scope = 0;

        while (($token = current($this->_tokens)) !== false) {
            switch ($token->type) {

            case PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN:
                ++$scope;
                break;

            case PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE:
                --$scope;
                break;

            case PHP_Depend_ConstantsI::T_BOOLEAN_AND:
            case PHP_Depend_ConstantsI::T_BOOLEAN_OR:
            case PHP_Depend_ConstantsI::T_LOGICAL_AND:
            case PHP_Depend_ConstantsI::T_LOGICAL_OR:
            case PHP_Depend_ConstantsI::T_LOGICAL_XOR:
                $npath = PHP_Depend_Util_MathUtil::add('1', $npath);
                break;
            }

            next($this->_tokens);

            if ($scope === 0) {
                break;
            }
        }
        return $npath;
    }
}
?>
