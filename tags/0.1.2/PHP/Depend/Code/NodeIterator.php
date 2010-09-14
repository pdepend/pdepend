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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/Node.php';

/**
 * Iterator for code nodes.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Code_NodeIterator implements Iterator, Countable
{
    /**
     * List of {@link PHP_Depend_Code_Node} objects in this iterator.
     *
     * @type array<PHP_Depend_Code_Node>
     * @var array(PHP_Depend_Code_Node)$nodes
     */
    protected $nodes = array();
    
    /**
     * Constructs a new node iterator from the given {@link PHP_Depend_Code_Node}
     * node array.
     *
     * @param array(PHP_Depend_Code_Node) $nodes List of code nodes.
     * 
     * @throws RuntimeException If the array contains something different from
     *                          a {@link PHP_Depend_Code_Node} object.
     */
    public function __construct(array $nodes) 
    {
        foreach ($nodes as $node) {
            if (!($node instanceof PHP_Depend_Code_Node)) {
                throw new RuntimeException('Invalid object given.');
            }
            $this->nodes[$node->getName()] = $node;
        }
    }
    
    /**
     * Returns the number of {@link PHP_Depend_Code_Node} objects in this iterator.
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
     * @return PHP_Depend_Code_Node|false
     */
    public function current()
    {
        return current($this->nodes);
    }
    
    /**
     * Returns the name of the current {@link PHP_Depend_Code_Node}.
     *
     * @return string
     */
    public function key()
    {
        return key($this->nodes);
    }
    
    /**
     * Moves the internal pointer to the next {@link PHP_Depend_Code_Node}.
     *
     * @return void
     */
    public function next()
    {
        next($this->nodes);
    }
    
    /**
     * Rewinds the internal pointer.
     *
     * @return void
     */
    public function rewind()
    {
        reset($this->nodes);
    }

    /**
     * Returns <b>true</b> while there is a next {@link PHP_Depend_Code_Node}.
     *
     * @return boolean
     */
    public function valid()
    {
        return ($this->current() !== false);
    }
}