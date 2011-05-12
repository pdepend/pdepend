<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

/**
 * Iterator for code nodes.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Code_NodeIterator implements Iterator, Countable
{
    /**
     * List of {@link PHP_Depend_Code_NodeI} objects in this iterator.
     *
     * @var array(PHP_Depend_Code_NodeI) $_nodes
     */
    private $_nodes = array();

    /**
     * Total number of available nodes.
     *
     * @var integer
     */
    private $_count = 0;

    /**
     * Current internal offset.
     *
     * @var integer
     */
    private $_offset = 0;

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
        $filter = PHP_Depend_Code_Filter_Collection::getInstance();

        $nodeKeys = array();
        foreach ($nodes as $node) {
            $uuid = $node->getUUID();
            if (!isset($nodeKeys[$uuid]) && $filter->accept($node)) {
                $nodeKeys[$uuid] = $uuid;
                $this->_nodes[]  = $node;

                ++$this->_count;
            }
        }
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
        if ($this->_offset >= $this->_count) {
            return false;
        }
        return $this->_nodes[$this->_offset];
    }

    /**
     * Returns the name of the current {@link PHP_Depend_Code_NodeI}.
     *
     * @return string
     */
    public function key()
    {
        return $this->_nodes[$this->_offset]->getName();
    }

    /**
     * Moves the internal pointer to the next {@link PHP_Depend_Code_NodeI}.
     *
     * @return void
     */
    public function next()
    {
        ++$this->_offset;
    }

    /**
     * Rewinds the internal pointer.
     *
     * @return void
     */
    public function rewind()
    {
        $this->_offset = 0;
    }

    /**
     * Returns <b>true</b> while there is a next {@link PHP_Depend_Code_NodeI}.
     *
     * @return boolean
     */
    public function valid()
    {
        return ($this->_offset < $this->_count);
    }

    /**
     * This method can be called by the PHP_Depend runtime environment or a
     * utilizing component to free up memory. This methods are required for
     * PHP version < 5.3 where cyclic references can not be resolved
     * automatically by PHP's garbage collector.
     *
     * @return void
     * @since 0.9.12
     */
    public function free()
    {
        foreach ($this->_nodes as $node) {
            $node->free();
        }
        $this->_nodes = array();
    }
}
