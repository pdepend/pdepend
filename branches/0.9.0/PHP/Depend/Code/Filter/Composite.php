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
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/FilterI.php';

/**
 * Composite filter implementation.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Code_Filter_Composite
    implements PHP_Depend_Code_FilterI, Countable, IteratorAggregate
{
    /**
     * List of all registered filters.
     *
     * @var array(PHP_Depend_Code_FilterI) $_filters
     */
    private $_filters = array();

    /**
     * Adds a child filter to this instance.
     *
     * @param PHP_Depend_Code_FilterI $filter The new child filter.
     *
     * @return void
     */
    public function addFilter(PHP_Depend_Code_FilterI $filter)
    {
        if (in_array($filter, $this->_filters, true) === false) {
            $this->_filters[] = $filter;
        }
    }

    /**
     * Removes a child filter from this instance.
     *
     * @param PHP_Depend_Code_FilterI $filter The child filter.
     *
     * @return void
     */
    public function removeFilter(PHP_Depend_Code_FilterI $filter)
    {
        if (($idx = array_search($filter, $this->_filters, true)) !== false) {
            unset($this->_filters[$idx]);
        }
    }

    /**
     * Returns <b>true</b> if the given node should be part of the node iterator,
     * otherwise this method will return <b>false</b>.
     *
     * @param PHP_Depend_Code_NodeI $node The context node instance.
     *
     * @return boolean
     */
    public function accept(PHP_Depend_Code_NodeI $node)
    {
        foreach ($this->_filters as $filter) {
            if ($filter->accept($node) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns the number of available filters.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->_filters);
    }

    /**
     * Returns an iterator with all registered filter instances.
     *
     * @return Iterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_filters);
    }
}