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
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Metrics/AbstractAnalyzer.php';
require_once 'PHP/Depend/Metrics/AnalyzerI.php';
require_once 'PHP/Depend/Metrics/FilterAwareI.php';
require_once 'PHP/Depend/Metrics/NodeAwareI.php';
require_once 'PHP/Depend/Metrics/ProjectAwareI.php';

/**
 * This class calculates the Cyclomatic Complexity Number(CCN) for the project,
 * methods and functions.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_CyclomaticComplexity_Analyzer
       extends PHP_Depend_Metrics_AbstractAnalyzer
    implements PHP_Depend_Metrics_AnalyzerI,
               PHP_Depend_Metrics_FilterAwareI,
               PHP_Depend_Metrics_NodeAwareI,
               PHP_Depend_Metrics_ProjectAwareI
{
    /**
     * Hash with all calculated node metrics.
     *
     * <code>
     * array(
     *     '0375e305-885a-4e91-8b5c-e25bda005438'  =>  array(
     *         'loc'    =>  42,
     *         'ncloc'  =>  17,
     *         'cc'     =>  12
     *     ),
     *     'e60c22f0-1a63-4c40-893e-ed3b35b84d0b'  =>  array(
     *         'loc'    =>  42,
     *         'ncloc'  =>  17,
     *         'cc'     =>  12
     *     )
     * )
     * </code>
     *
     * @var array(string=>array) $_nodeMetrics
     */
    private $_nodeMetrics = null;

    /**
     * The project Cyclomatic Complexity Number.
     *
     * @var integer $_ccn
     */
    private $_ccn = 0;

    /**
     * Extended Cyclomatic Complexity Number(CCN2) for the project.
     *
     * @var integer $_ccn2
     */
    private $_ccn2 = 0;

    /**
     * Processes all {@link PHP_Depend_Code_Package} code nodes.
     *
     * @param PHP_Depend_Code_NodeIterator $packages All code packages.
     *
     * @return void
     */
    public function analyze(PHP_Depend_Code_NodeIterator $packages)
    {
        if ($this->_nodeMetrics === null) {

            $this->fireStartAnalyzer();

            // Init node metrics
            $this->_nodeMetrics = array();

            // Visit all packages
            foreach ($packages as $package) {
                $package->accept($this);
            }

            $this->fireEndAnalyzer();
        }
    }

    /**
     * Returns the cyclomatic complexity for the given <b>$node</b> instance.
     *
     * @param PHP_Depend_Code_NodeI $node The context node instance.
     *
     * @return integer
     */
    public function getCCN(PHP_Depend_Code_NodeI $node)
    {
        $ccn = 0;
        if (isset($this->_nodeMetrics[$node->getUUID()])) {
            $ccn = $this->_nodeMetrics[$node->getUUID()]['ccn'];
        }
        return $ccn;
    }

    /**
     * Returns the extended cyclomatic complexity for the given <b>$node</b>
     * instance.
     *
     * @param PHP_Depend_Code_NodeI $node The context node instance.
     *
     * @return integer
     */
    public function getCCN2(PHP_Depend_Code_NodeI $node)
    {
        $ccn2 = 0;
        if (isset($this->_nodeMetrics[$node->getUUID()])) {
            $ccn2 = $this->_nodeMetrics[$node->getUUID()]['ccn2'];
        }
        return $ccn2;
    }

    /**
     * This method will return an <b>array</b> with all generated metric values
     * for the given <b>$node</b>. If there are no metrics for the requested
     * node, this method will return an empty <b>array</b>.
     *
     * @param PHP_Depend_Code_NodeI $node The context node instance.
     *
     * @return array(string=>mixed)
     */
    public function getNodeMetrics(PHP_Depend_Code_NodeI $node)
    {
        $metrics = array();
        if (isset($this->_nodeMetrics[$node->getUUID()])) {
            $metrics = $this->_nodeMetrics[$node->getUUID()];
        }
        return $metrics;
    }

    /**
     * Provides the project summary metrics as an <b>array</b>.
     *
     * @return array(string=>mixed)
     */
    public function getProjectMetrics()
    {
        return array(
            'ccn'   =>  $this->_ccn,
            'ccn2'  =>  $this->_ccn2
        );
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

        // Get all method tokens
        $tokens = $function->getTokens();

        $ccn  = $this->_calculateCCN($tokens);
        $ccn2 = $this->_calculateCCN2($tokens);

        // The method metrics
        $this->_nodeMetrics[$function->getUUID()] = array(
            'ccn'   =>  $ccn,
            'ccn2'  =>  $ccn2
        );

        // Update project metrics
        $this->_ccn  += $ccn;
        $this->_ccn2 += $ccn2;

        $this->fireEndFunction($function);
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

        // Get all method tokens
        $tokens = $method->getTokens();

        $ccn  = $this->_calculateCCN($tokens);
        $ccn2 = $this->_calculateCCN2($tokens);

        // The method metrics
        $this->_nodeMetrics[$method->getUUID()] = array(
            'ccn'   =>  $ccn,
            'ccn2'  =>  $ccn2
        );

        // Update project metrics
        $this->_ccn  += $ccn;
        $this->_ccn2 += $ccn2;

        $this->fireEndMethod($method);
    }

    /**
     * Calculates the Cyclomatic Complexity Number (CCN).
     *
     * @param array $tokens The input tokens.
     *
     * @return integer
     */
    private function _calculateCCN(array $tokens)
    {
        // List of tokens
        $countingTokens = array(
            PHP_Depend_TokenizerI::T_CASE           =>  true,
            PHP_Depend_TokenizerI::T_CATCH          =>  true,
            PHP_Depend_TokenizerI::T_ELSEIF         =>  true,
            PHP_Depend_TokenizerI::T_FOR            =>  true,
            PHP_Depend_TokenizerI::T_FOREACH        =>  true,
            PHP_Depend_TokenizerI::T_IF             =>  true,
            PHP_Depend_TokenizerI::T_QUESTION_MARK  =>  true,
            PHP_Depend_TokenizerI::T_WHILE          =>  true
        );

        $ccn = 1;
        foreach ($tokens as $token) {
            if (isset($countingTokens[$token->type])) {
                ++$ccn;
            }
        }
        return $ccn;
    }

    /**
     * Calculates the second version of the Cyclomatic Complexity Number (CCN2).
     * This version includes boolean operators like <b>&&</b>, <b>and</b>,
     * <b>or</b> and <b>||</b>.
     *
     * @param array $tokens The input tokens.
     *
     * @return integer
     */
    private function _calculateCCN2(array $tokens)
    {
        // List of tokens
        $countingTokens = array(
            PHP_Depend_TokenizerI::T_BOOLEAN_AND    =>  true,
            PHP_Depend_TokenizerI::T_BOOLEAN_OR     =>  true,
            PHP_Depend_TokenizerI::T_CASE           =>  true,
            PHP_Depend_TokenizerI::T_CATCH          =>  true,
            PHP_Depend_TokenizerI::T_ELSEIF         =>  true,
            PHP_Depend_TokenizerI::T_FOR            =>  true,
            PHP_Depend_TokenizerI::T_FOREACH        =>  true,
            PHP_Depend_TokenizerI::T_IF             =>  true,
            PHP_Depend_TokenizerI::T_LOGICAL_AND    =>  true,
            PHP_Depend_TokenizerI::T_LOGICAL_OR     =>  true,
            PHP_Depend_TokenizerI::T_QUESTION_MARK  =>  true,
            PHP_Depend_TokenizerI::T_WHILE          =>  true
        );

        $ccn2 = 1;
        foreach ($tokens as $token) {
            if (isset($countingTokens[$token->type])) {
                ++$ccn2;
            }
        }
        return $ccn2;
    }
}