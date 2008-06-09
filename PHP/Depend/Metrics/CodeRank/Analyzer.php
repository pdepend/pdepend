<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pmanuel-pichler.de>.
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
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/NodeVisitor/AbstractDefaultVisitor.php';
require_once 'PHP/Depend/Metrics/AnalyzerI.php';
require_once 'PHP/Depend/Metrics/NodeAwareI.php';

/**
 * Calculates the code ranke metric for classes and packages. 
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_CodeRank_Analyzer 
       extends PHP_Depend_Code_NodeVisitor_AbstractDefaultVisitor
    implements PHP_Depend_Metrics_AnalyzerI,
               PHP_Depend_Metrics_NodeAwareI
{
    /**
     * The used damping factor.
     */
    const DAMPING_FACTOR = 0.85;
    
    /**
     * All found nodes.
     *
     * @type array<array>
     * @var array(string=>array) $nodes
     */
    protected $nodes = array();
    
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
     * @type array<array>
     * @var array(string=>array) $_nodeMetrics
     */
    private $_nodeMetrics = null;
    
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
            // Init node metrics
            $this->_nodeMetrics = array();
            
            // First traverse package tree
            foreach ($packages as $package) {
                $package->accept($this);
            }
        
            // Calculate code rank metrics
            $this->buildCodeRankMetrics();
        }
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
        if (isset($this->_nodeMetrics[$node->getUUID()])) {
            return $this->_nodeMetrics[$node->getUUID()];
        }
        return array();
    }
    
    /**
     * Visits a code class object.
     *
     * @param PHP_Depend_Code_Class $class The context code class.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitorI::visitClass()
     * @see PHP_Depend_Metrics_CodeRank_Analyzer::visitType()
     */
    public function visitClass(PHP_Depend_Code_Class $class)
    {
        $this->visitType($class);
    
        foreach ($class->getMethods() as $method) {
            $method->accept($this);
        }
        foreach ($class->getProperties() as $property) {
            $property->accept($this);
        }
    }
    
    /**
     * Visits a code interface object.
     *
     * @param PHP_Depend_Code_Interface $interface The context code interface.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitorI::visitInterface()
     * @see PHP_Depend_Metrics_CodeRank_Analyzer::visitType()
     */
    public function visitInterface(PHP_Depend_Code_Interface $interface)
    {
        $this->visitType($interface);
    
        foreach ($interface->getMethods() as $method) {
            $method->accept($this);
        }
    }
    
    /**
     * Visits a code package object.
     *
     * @param PHP_Depend_Code_Package $package The context code package.
     * 
     * @return void
     * @see PHP_Depend_Code_NodeVisitorI::visitPackage()
     */
    public function visitPackage(PHP_Depend_Code_Package $package)
    {
        $this->initNode($package);
        
        foreach ($package->getTypes() as $type) {
            $type->accept($this);
        }
        foreach ($package->getFunctions() as $function) {
            $function->accept($this);
        }
    }
    
    /**
     * Generic visitor method for classes and interfaces. Both visit methods
     * delegate calls to this method.
     *
     * @param PHP_Depend_Code_AbstractType $type The context type instance.
     * 
     * @return void
     */
    protected function visitType(PHP_Depend_Code_AbstractType $type)
    {
        $pkg = $type->getPackage();
        
        $this->initNode($type);
        
        foreach ($type->getDependencies() as $dep) {
            
            $depPkg = $dep->getPackage();
            
            $this->initNode($dep);
            $this->initNode($depPkg);
            
            $this->nodes[$type->getUUID()]['in'][] = $dep->getUUID();
            $this->nodes[$dep->getUUID()]['out'][] = $type->getUUID();
            
            // No self references
            if ($pkg !== $depPkg) {
                $this->nodes[$pkg->getUUID()]['in'][]     = $depPkg->getUUID();
                $this->nodes[$depPkg->getUUID()]['out'][] = $pkg->getUUID();
            }
        }
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
        if (!isset($this->nodes[$node->getUUID()])) {
            $this->nodes[$node->getUUID()] = array(
                'in'   =>  array(),
                'out'  =>  array(),
                'name'  =>  $node->getName(),
                'type'  =>  get_class($node)
            );
        }
    }
    
    /**
     * Generates the forward and reverse code rank for the given <b>$nodes</b>.
     * 
     * @return void
     */
    protected function buildCodeRankMetrics()
    {
        foreach ($this->nodes as $uuid => $info) {
            $this->_nodeMetrics[$uuid] = array('cr'  =>  0, 'rcr'  =>  0);
        }
        foreach ($this->computeCodeRank('out', 'in') as $uuid => $rank) {
            $this->_nodeMetrics[$uuid]['cr'] = $rank;
        }
        foreach ($this->computeCodeRank('in', 'out') as $uuid => $rank) {
            $this->_nodeMetrics[$uuid]['rcr'] = $rank;
        }
    }
    
    /**
     * Calculates the code rank for the given <b>$nodes</b> set.
     * 
     * @param string $id1 Identifier for the incoming edges.
     * @param string $id2 Identifier for the outgoing edges.
     * 
     * @return array(string=>float)
     */
    protected function computeCodeRank($id1, $id2)
    {
        $d = self::DAMPING_FACTOR;
        
        $nodes = $this->nodes;
        $ranks = array();
        
        foreach (array_keys($this->nodes) as $name) {
            $ranks[$name] = 1;
        }
        
        for ($i = 0; $i < 100; $i++) {
            foreach ($this->nodes as $name => $info) {
                $rank = 0;
                foreach ($info[$id1] as $ref) {
                    $pr = $ranks[$ref];
                    $c  = count($this->nodes[$ref][$id2]);
                    
                    $rank += ($pr / $c);
                }
                $ranks[$name] = ((1 - $d)) + $d * $rank;
            }
        }
        return $ranks;
    }
}