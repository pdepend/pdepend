<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@pdepend.org>.
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
 * @subpackage Log
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

/**
 * Dummy implementation of an analyzer.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Log
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Log_Summary_AnalyzerNodeAndProjectAwareDummy
    implements PHP_Depend_Metrics_AnalyzerI,
               PHP_Depend_Metrics_NodeAwareI,
               PHP_Depend_Metrics_ProjectAwareI
{
    /**
     * Dummy project metrics.
     *
     * @var array(string=>mixed) $projectMetrics
     */
    protected $projectMetrics = null;

    /**
     * Dummy node metrics.
     *
     * @var array(string=>array) $nodeMetrics
     */
    protected $nodeMetrics = null;

    /**
     * Constructs a new analyzer dummy instance.
     *
     * @param array(string=>mixed) $projectMetrics Dummy project metrics.
     * @param array(string=>array) $nodeMetrics    Dummy node metrics.
     */
    public function __construct(array $projectMetrics = array(), array $nodeMetrics = array())
    {
        $this->projectMetrics = $projectMetrics;
        $this->nodeMetrics    = $nodeMetrics;
    }

    /**
     * Adds a listener to this analyzer.
     *
     * @param PHP_Depend_Metrics_ListenerI $listener The listener instance.
     *
     * @return void
     */
    public function addAnalyzeListener(PHP_Depend_Metrics_ListenerI $listener) {
    }

    /**
     * Removes the listener from this analyzer.
     *
     * @param PHP_Depend_Metrics_ListenerI $listener The listener instance.
     *
     * @return void
     */
    public function removeAnalyzeListener(PHP_Depend_Metrics_ListenerI $listener) {
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
    }

    /**
     * By default all analyzers are enabled. Overwrite this method to provide
     * state based disabling/enabling.
     *
     * @return boolean
     * @since 0.9.10
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * Returns the project metrics.
     *
     * @return array(string=>mixed)
     * @see PHP_Depend_Metrics_ProjectAwareI::getProjectMetrics()
     */
    public function getProjectMetrics()
    {
        return $this->projectMetrics;
    }

    /**
     * Returns an array with metrics for the requested node.
     *
     * @param PHP_Depend_Code_NodeI $node The context node instance.
     *
     * @return array(string=>mixed)
     * @see PHP_Depend_Metrics_NodeAwareI::getNodeMetrics()
     */
    public function getNodeMetrics(PHP_Depend_Code_NodeI $node)
    {
        return $this->nodeMetrics;
    }
}
