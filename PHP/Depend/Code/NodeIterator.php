<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pdepend.org>.
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
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/NodeI.php';
require_once 'PHP/Depend/Code/NodeIterator/CompositeFilter.php';
require_once 'PHP/Depend/Code/NodeIterator/StaticFilter.php';

/**
 * Iterator for code nodes.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Code_NodeIterator implements Iterator, Countable
{
    /**
     * The unfiltered input array of {@link PHP_Depend_Code_NodeI} objects.
     *
     * @var array(PHP_Depend_Code_NodeI) $_input
     */
    private $_input = array();

    /**
     * List of {@link PHP_Depend_Code_NodeI} objects in this iterator.
     *
     * @var array(PHP_Depend_Code_NodeI) $_nodes
     */
    private $_nodes = array();

    /**
     * The global filter instance.
     *
     * @var PHP_Depend_Code_NodeIterator_CompositeFilter $_filter
     */
    private $_filter = null;

    /**
     * Constructs a new node iterator from the given {@link PHP_Depend_Code_NodeI}
     * node array.
     *
     * @param array(PHP_Depend_Code_NodeI) $nodes List of code nodes.
     *
     * @throws RuntimeException If the array contains something different from
     *                          a {@link PHP_Depend_Code_NodeI} object.
     */
    public function __construct(array $nodes)
    {
        // First check all input nodes
        foreach ($nodes as $node) {
            if (!($node instanceof PHP_Depend_Code_NodeI)) {
                throw new RuntimeException('Invalid object given.');
            }
            $this->_input[$node->getName()] = $node;
        }
        // Sort by name
        ksort($this->_input);

        $staticFilter = PHP_Depend_Code_NodeIterator_StaticFilter::getInstance();

        // Apply global filters
        $this->_filter = new PHP_Depend_Code_NodeIterator_CompositeFilter();
        $this->_filter->addFilter($staticFilter);

        $this->_init();
    }

    /**
     * Appends a filter to this iterator.
     *
     * A call to this method will reset the internal pointer.
     *
     * @param PHP_Depend_Code_NodeIterator_FilterI $filter The filter instance.
     *
     * @return void
     */
    public function addFilter(PHP_Depend_Code_NodeIterator_FilterI $filter)
    {
        $this->_filter->addFilter($filter);
        $this->_init();
    }

    /**
     * Returns the number of {@link PHP_Depend_Code_NodeI} objects in this iterator.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->_nodes);
    }

    /**
     * Returns the current node or <b>false</b>
     *
     * @return PHP_Depend_Code_NodeI|false
     */
    public function current()
    {
        return current($this->_nodes);
    }

    /**
     * Returns the name of the current {@link PHP_Depend_Code_NodeI}.
     *
     * @return string
     */
    public function key()
    {
        return key($this->_nodes);
    }

    /**
     * Moves the internal pointer to the next {@link PHP_Depend_Code_NodeI}.
     *
     * @return void
     */
    public function next()
    {
        next($this->_nodes);
    }

    /**
     * Rewinds the internal pointer.
     *
     * @return void
     */
    public function rewind()
    {
        reset($this->_nodes);
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
     * This method initializes a filtered list of nodes. If no filter is
     * registered, this method will simply use the input node list.
     *
     * @return void
     */
    private function _init()
    {
        if ($this->_filter->count() === 0) {
            $this->_nodes = $this->_input;
            return;
        }

        $this->_nodes = array();
        foreach ($this->_input as $name => $node) {
            if ($this->_filter->accept($node) === true) {
                $this->_nodes[$name] = $node;
            }
        }

        reset($this->_nodes);
    }
}