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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once 'PHP/Depend/Metrics/AbstractAnalyzer.php';
require_once 'PHP/Depend/Metrics/AnalyzerI.php';
require_once 'PHP/Depend/Metrics/ProjectAwareI.php';

/**
 * This analyzer collects coupling values for the hole project. It calculates
 * all function and method <b>calls</b> and the <b>fanout</b>, that means the
 * number of referenced types.
 *
 * The FANOUT calculation is based on the definition used by the apache maven
 * project.
 *
 * <ul>
 *   <li>field declarations (Uses doc comment annotations)</li>
 *   <li>formal parameters and return types (The return type uses doc comment
 *   annotations)</li>
 *   <li>throws declarations (Uses doc comment annotations)</li>
 *   <li>local variables</li>
 * </ul>
 *
 * http://www.jajakarta.org/turbine/en/turbine/maven/reference/metrics.html
 *
 * The implemented algorithm counts each type only once for a method and function.
 * Any type that is either a supertype or a subtype of the class is not counted.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Metrics_Coupling_Analyzer
       extends PHP_Depend_Metrics_AbstractAnalyzer
    implements PHP_Depend_Metrics_AnalyzerI,
               PHP_Depend_Metrics_ProjectAwareI
{
    /**
     * The number of method or function calls.
     *
     * @var integer $_calls
     */
    private $_calls = -1;

    /**
     * Number of fanouts.
     *
     * @var integer $_fanout
     */
    private $_fanout = -1;

    /**
     * This array holds tokens types that are valid PHP callable identifiers.
     *
     * @var array(integer)
     */
    private $_callableTokens = array(
        PHP_Depend_TokenizerI::T_STRING,
        PHP_Depend_TokenizerI::T_VARIABLE
    );

    /**
     * This array holds token types that are used in method invocation chains.
     *
     * @var array(integer)
     */
    private $_methodChainTokens = array(
        PHP_Depend_TokenizerI::T_DOUBLE_COLON,
        PHP_Depend_TokenizerI::T_OBJECT_OPERATOR,
    );

    /**
     * Provides the project summary as an <b>array</b>.
     *
     * <code>
     * array(
     *     'calls'   =>  23,
     *     'fanout'  =>  42
     * )
     * </code>
     *
     * @return array(string=>mixed)
     */
    public function getProjectMetrics()
    {
        return array(
            'calls'   =>  $this->_calls,
            'fanout'  =>  $this->_fanout
        );
    }

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
        if ($this->_calls === -1) {

            $this->fireStartAnalyzer();

            // Init metrics
            $this->_calls  = 0;
            $this->_fanout = 0;

            // Process all packages
            foreach ($packages as $package) {
                $package->accept($this);
            }

            $this->fireEndAnalyzer();
        }
    }

    /**
     * Visits a function node.
     *
     * @param PHP_Depend_Code_Function $function The current function node.
     *
     * @return void
     * @see PHP_Depend_VisitorI::visitFunction()
     */
    public function visitFunction(PHP_Depend_Code_Function $function)
    {
        $this->fireStartFunction($function);

        $fanouts = array();
        if (($type = $function->getReturnClass()) !== null) {
            $fanouts[] = $type;
            ++$this->_fanout;
        }
        foreach ($function->getExceptionClasses() as $type) {
            if (in_array($type, $fanouts, true) === false) {
                $fanouts[] = $type;
                ++$this->_fanout;
            }
        }
        foreach ($function->getDependencies() as $type) {
            if (in_array($type, $fanouts, true) === false) {
                ++$this->_fanout;
            }
        }

        $this->_countCalls($function);

        $this->fireEndFunction($function);
    }

    /**
     * Visits a method node.
     *
     * @param PHP_Depend_Code_Class $method The method class node.
     *
     * @return void
     * @see PHP_Depend_VisitorI::visitMethod()
     */
    public function visitMethod(PHP_Depend_Code_Method $method)
    {
        $this->fireStartMethod($method);

        $parent = $method->getParent();

        $fanouts = array();
        if (($type = $method->getReturnClass()) !== null) {
            if (!$type->isSubtypeOf($parent) && !$parent->isSubtypeOf($type)) {
                $fanouts[] = $type;
                ++$this->_fanout;
            }
        }
        foreach ($method->getExceptionClasses() as $type) {
            if (in_array($type, $fanouts, true) === true) {
                continue;
            }
            if (!$type->isSubtypeOf($parent) && !$parent->isSubtypeOf($type)) {
                $fanouts[] = $type;
                ++$this->_fanout;
            }
        }
        foreach ($method->getDependencies() as $type) {
            if (in_array($type, $fanouts, true) === true) {
                continue;
            }
            if (!$type->isSubtypeOf($parent) && !$parent->isSubtypeOf($type)) {
                $fanouts[] = $type;
                ++$this->_fanout;
            }
        }

        $this->_countCalls($method);

        $this->fireEndMethod($method);
    }

    /**
     * Visits a property node.
     *
     * @param PHP_Depend_Code_Property $property The property class node.
     *
     * @return void
     * @see PHP_Depend_VisitorI::visitProperty()
     */
    public function visitProperty(PHP_Depend_Code_Property $property)
    {
        $this->fireStartProperty($property);

        // Check for not null
        if (($type = $property->getClass()) !== null) {
            $declaringClass = $property->getDeclaringClass();

            // Only increment if these types are not part of the same hierarchy
            if (!$type->isSubtypeOf($declaringClass)
                && !$declaringClass->isSubtypeOf($type)
            ) {
                ++$this->_fanout;
            }
        }

        $this->fireEndProperty($property);
    }

    /**
     * Counts all calls within the given <b>$callable</b>
     *
     * @param PHP_Depend_Code_AbstractCallable $callable Context callable.
     *
     * @return void
     */
    private function _countCalls(PHP_Depend_Code_AbstractCallable $callable)
    {
        $called = array();

        $tokens = $callable->getTokens();
        $count  = count($tokens);
        for ($i = $this->_findOpenCurlyBrace($tokens); $i < $count; ++$i) {

            if ($this->_isCallableOpenParenthesis($tokens, $i) === false) {
                continue;
            }

            if ($this->_isMethodInvocation($tokens, $i) === true) {
                $image = $this->_getInvocationChainImage($tokens, $i);
            } else if ($this->_isFunctionInvocation($tokens, $i) === true) {
                $image = $tokens[$i - 1]->image;
            } else {
                $image = null;
            }

            if ($image !== null) {
                $called[$image] = $image;
            }
        }

        $this->_calls += count($called);
    }

    /**
     * Finds the offset of the curly brace that opens the function or method
     * body.
     *
     * @param array(PHP_Depend_Token) $tokens The function or method tokens.
     *
     * @return integer
     */
    private function _findOpenCurlyBrace(array $tokens)
    {
        foreach ($tokens as $i => $token) {
            if ($token->type === PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN) {
                return $i;
            }
        }

        return count($tokens);
    }

    /**
     * Returns <b>true</b> when the actual token is a parenthesis and the
     * previous token is a valid callable identifier.
     *
     * @param array(PHP_Depend_Token) $tokens The function or method tokens.
     * @param integer                 $index  The actual token array index.
     *
     * @return boolean
     */
    private function _isCallableOpenParenthesis(array $tokens, $index)
    {
        return ($tokens[$index]->type === PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN
            && isset($tokens[$index - 1]) === true
            && in_array($tokens[$index - 1]->type, $this->_callableTokens) === true);
    }

    /**
     * Returns <b>true</b> when the actual index is a function invocation and
     * not an object allocate expression.
     *
     * @param array(PHP_Depend_Token) $tokens The function or method tokens.
     * @param integer                 $index  The actual token array index.
     *
     * @return boolean
     */
    private function _isFunctionInvocation(array $tokens, $index)
    {
        return (isset($tokens[$index - 2]) === false
            || $tokens[$index - 2]->type !== PHP_Depend_TokenizerI::T_NEW);
    }

    /**
     * Returns <b>true</b> when the actual index is part of a method invocation
     * chain expression.
     *
     * @param array(PHP_Depend_Token) $tokens The function or method tokens.
     * @param integer                 $index  The actual token array index.
     *
     * @return boolean
     */
    private function _isMethodInvocation(array $tokens, $index)
    {
        return (isset($tokens[$index - 2]) === true &&
            in_array($tokens[$index - 2]->type, $this->_methodChainTokens));
    }

    /**
     * This method returns the source image of a method invocation chain.
     *
     * @param array(PHP_Depend_Token) $tokens The function or method tokens.
     * @param integer                 $index  The actual token array index.
     *
     * @return string
     */
    private function _getInvocationChainImage(array $tokens, $index)
    {
        $image = $tokens[$index - 2]->image . $tokens[$index - 1]->image;
        for ($j = $index - 3; $j >= 0; --$j) {
            if (!in_array($tokens[$j]->type, $this->_callableTokens)
                && !in_array($tokens[$j]->type, $this->_methodChainTokens)
            ) {
                break;
            }
            $image = $tokens[$j]->image . $image;
        }
        return $image;
    }
}