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
 * @subpackage Util
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

/**
 * A collection that contains no duplicate nodes. More formally, sets contain
 * no pair of nodes <b>$n1</b> and <b>$n2</b> such that <b>$n1->equals($n2)</b>.
 * As implied by its name, this class models the mathematical set abstraction. 
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Util
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Util_NodeSet implements IteratorAggregate
{
    /**
     * This property holds all nodes within this set.
     *
     * @var array(PHP_Reflection_AST_NodeI) $_nodeList
     */
    private $_nodeList = array();
    
    /**
     * Adds the node to this set if it is not already present. More formally, adds 
     * the node <b>$node</b> to this set if the set contains no node <b>$n</b> that
     * return <b>true</b> for <b>$n->equals($node)</b>. If the set already contains
     * a node, the call leaves the set unchanged and returns <b>false</b>.  
     *
     * @param PHP_Reflection_AST_NodeI $node The node to be added.
     * 
     * @return boolean
     */
    public function add(PHP_Reflection_AST_NodeI $node)
    {
        if ($this->contains($node) === true) {
            return false;
        }
        $this->_nodeList[] = $node;
        
        return true;
    }
    
    /**
     * Returns <b>true</b> if this set already contains the specified node. 
     * Formally, returns <b>true</b> if and only if this set contains a node 
     * <b>$n</b> that return <b>true</b> for <b>$node->equals($n)</b>. 
     *
     * @param PHP_Reflection_AST_NodeI $node Thats presence is to be tested.
     *  
     * @return boolean
     */
    public function contains(PHP_Reflection_AST_NodeI $node)
    {
        foreach ($this->_nodeList as $n) {
            if ($n->equals($node) === true) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Returns the number of elements in this set (its cardinality).
     *
     * @return integer
     */
    public function size()
    {
        return count($this->_nodeList);
    }
    
    /**
     * Returns an iterator over the nodes in this set. The nodes are returned in
     * no particular order. 
     *
     * @return Iterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_nodeList);
    }
}