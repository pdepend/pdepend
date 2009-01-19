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

require_once 'PHP/Depend/Visitor/AbstractVisitor.php';
require_once 'PHP/Depend/Metrics/CodeRank/CodeRankStrategyI.php';

/**
 * Collects class and package metrics based on class properties.
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
class PHP_Depend_Metrics_CodeRank_PropertyStrategy
       extends PHP_Depend_Visitor_AbstractVisitor
    implements PHP_Depend_Metrics_CodeRank_CodeRankStrategyI
{
    /**
     * All found nodes.
     *
     * @var array(string=>array) $_nodes
     */
    private $_nodes = array();

    /**
     * Returns the collected nodes.
     *
     * @return array(string=>array)
     */
    public function getCollectedNodes()
    {
        return $this->_nodes;
    }

    /**
     * Visits a property node.
     *
     * @param PHP_Depend_Code_Property $property The property class node.
     *
     * @return void
     * @see PHP_Depend_Visitor_AbstractVisitor::visitProperty()
     */
    public function visitProperty(PHP_Depend_Code_Property $property)
    {
        $this->fireStartProperty($property);

        if (($depClass = $property->getType()) === null) {
            $this->fireEndProperty($property);
            return;
        }

        $depPackage = $depClass->getPackage();

        $class   = $property->getParent();
        $package = $class->getPackage();

        if ($depClass !== $class) {
            $this->initNode($class);
            $this->initNode($depClass);

            $this->_nodes[$class->getUUID()]['in'][]     = $depClass->getUUID();
            $this->_nodes[$depClass->getUUID()]['out'][] = $class->getUUID();
        }

        if ($depPackage !== $package) {
            $this->initNode($package);
            $this->initNode($depPackage);

            $this->_nodes[$package->getUUID()]['in'][]     = $depPackage->getUUID();
            $this->_nodes[$depPackage->getUUID()]['out'][] = $package->getUUID();
        }

        $this->fireEndProperty($property);
    }

    /**
     * Initializes the temporary node container for the given <b>$node</b>.
     *
     * @param PHP_Depend_Code_NodeI $node The context node instance.
     *
     * @return void
     */
    protected function initNode(PHP_Depend_Code_NodeI $node)
    {
        if (!isset($this->_nodes[$node->getUUID()])) {
            $this->_nodes[$node->getUUID()] = array(
                'in'   =>  array(),
                'out'  =>  array(),
                'name'  =>  $node->getName(),
                'type'  =>  get_class($node)
            );
        }
    }
}