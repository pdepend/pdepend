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
 * @subpackage Code
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/NodeI.php';

/**
 * Iterator for code nodes.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Code_NodeIterator implements Iterator, Countable
{
    /**
     * List of {@link PHP_Depend_Code_NodeI} objects in this iterator.
     *
     * @type array<PHP_Depend_Code_NodeI>
     * @var array(PHP_Depend_Code_NodeI)$nodes
     */
    protected $nodes = array();
    
    /**
     * List of all assciated filter instances.
     * 
     * @type array<PHP_Depend_Code_NodeIterator_FilterI>
     * @var array(PHP_Depend_Code_NodeIterator_FilterI) $_filters
     */
    private $_filters = array();
    
    /**
     * Constructs a new node iterator from the given {@link PHP_Depend_Code_NodeI}
     * node array.
     *
     * @param array(PHP_Depend_Code_NodeI) $nodes      List of code nodes.
     * @param string                       $typeFilter Optional node type filter.
     * 
     * @throws RuntimeException If the array contains something different from
     *                          a {@link PHP_Depend_Code_NodeI} object.
     */
    public function __construct(array $nodes, $typeFilter = null) 
    {
        foreach ($nodes as $node) {
            if (!($node instanceof PHP_Depend_Code_NodeI)) {
                throw new RuntimeException('Invalid object given.');
            }
            if ($typeFilter === null || $node instanceof $typeFilter) {
                $this->nodes[$node->getName()] = $node;
            }
        }
        
        ksort($this->nodes);
        
        $this->rewind();
    }
    
    /**
     * Appends a filter to this iterator.
     *
     * @param PHP_Depend_Code_NodeIterator_FilterI $filter The filter instance.
     * 
     * @return void
     */
    public function addFilter(PHP_Depend_Code_NodeIterator_FilterI $filter)
    {
        if (in_array($filter, $this->_filters, true) === false) {
            $this->_filters[] = $filter;
        }
    }
    
    /**
     * Returns the number of {@link PHP_Depend_Code_NodeI} objects in this iterator.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->nodes);
    }
    
    /**
     * Returns the current node or <b>false</b>
     *
     * @return PHP_Depend_Code_NodeI|false
     */
    public function current()
    {
        return current($this->nodes);
    }
    
    /**
     * Returns the name of the current {@link PHP_Depend_Code_NodeI}.
     *
     * @return string
     */
    public function key()
    {
        return key($this->nodes);
    }
    
    /**
     * Moves the internal pointer to the next {@link PHP_Depend_Code_NodeI}.
     *
     * @return void
     */
    public function next()
    {
        next($this->nodes);
        $this->_filterNext();
    }
    
    /**
     * Rewinds the internal pointer.
     *
     * @return void
     */
    public function rewind()
    {
        reset($this->nodes);
        $this->_filterNext();
    }

    /**
     * Returns <b>true</b> while there is a next {@link PHP_Depend_Code_NodeI}.
     *
     * @return boolean
     */
    public function valid()
    {
        return ($this->current() !== false);
    }
    
    /**
     * Moves the internal pointer to the next valid node. If no filter is 
     * registered, this method will simply return.
     *
     * @return void
     */
    private function _filterNext()
    {
        if (count($this->_filters) === 0) {
            return;
        }
        
        while (($node = $this->current()) !== false)
        {
            foreach ($this->_filters as $filter) {
                if ($filter->accept($node) === false) {
                    $this->next();
                    continue 2;
                }
            }
            break;
        }
    }
}