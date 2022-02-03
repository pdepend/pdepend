<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2017 Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Source\AST;

use ArrayAccess;
use BadMethodCallException;
use Countable;
use Iterator;
use OutOfBoundsException;
use PDepend\Source\AST\ASTArtifactList\CollectionArtifactFilter;
use ReturnTypeWillChange;

/**
 * Iterator for code nodes.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @template T of ASTArtifact
 * @implements \Iterator<int|string, T>
 * @implements \ArrayAccess<int|string, T>
 */
class ASTArtifactList implements ArrayAccess, Iterator, Countable
{
    /**
     * List of {@link ASTArtifact} objects in
     * this iterator.
     *
     * @var T[]
     */
    private $artifacts = array();

    /**
     * Total number of available nodes.
     *
     * @var int
     */
    private $count = 0;

    /**
     * Current internal offset.
     *
     * @var int
     */
    private $offset = 0;

    /**
     * Constructs a new node iterator from the given {@link ASTArtifact}
     * node array.
     *
     * @param T[] $artifacts
     */
    public function __construct(array $artifacts)
    {
        $filter = CollectionArtifactFilter::getInstance();

        $nodeKeys = array();
        foreach ($artifacts as $artifact) {
            $id = $artifact->getId();

            if (isset($nodeKeys[$id])) {
                continue;
            }

            if ($filter->accept($artifact)) {
                $nodeKeys[$id] = $id;
                $this->artifacts[]  = $artifact;

                ++$this->count;
            }
        }
    }

    /**
     * Returns the number of {@link ASTArtifact}
     * objects in this iterator.
     *
     * @return int
     */
    #[ReturnTypeWillChange]
    public function count()
    {
        return count($this->artifacts);
    }

    /**
     * Returns the current node or <b>false</b>
     *
     * @return false|T
     */
    #[ReturnTypeWillChange]
    public function current()
    {
        if ($this->offset >= $this->count) {
            return false;
        }
        return $this->artifacts[$this->offset];
    }

    /**
     * Returns the name of the current {@link ASTArtifact}.
     *
     * @return string
     */
    #[ReturnTypeWillChange]
    public function key()
    {
        return $this->artifacts[$this->offset]->getName();
    }

    /**
     * Moves the internal pointer to the next {@link ASTArtifact}.
     *
     * @return void
     */
    #[ReturnTypeWillChange]
    public function next()
    {
        ++$this->offset;
    }

    /**
     * Rewinds the internal pointer.
     *
     * @return void
     */
    #[ReturnTypeWillChange]
    public function rewind()
    {
        $this->offset = 0;
    }

    /**
     * Returns <b>true</b> while there is a next {@link ASTArtifact}.
     *
     * @return bool
     */
    #[ReturnTypeWillChange]
    public function valid()
    {
        return ($this->offset < $this->count);
    }

    /**
     * Whether a offset exists
     *
     * @param mixed $offset An offset to check for.
     *
     * @return bool Returns true on success or false on failure. The return
     *              value will be casted to boolean if non-boolean was returned.
     *
     * @since  1.0.0
     * @link   http://php.net/manual/en/arrayaccess.offsetexists.php
     */
    #[ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->artifacts[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @param mixed $offset
     *
     * @throws OutOfBoundsException
     *
     * @return T Can return all value types.
     *
     * @since  1.0.0
     * @link   http://php.net/manual/en/arrayaccess.offsetget.php
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if (isset($this->artifacts[$offset])) {
            return $this->artifacts[$offset];
        }
        throw new OutOfBoundsException("The offset {$offset} does not exist.");
    }

    /**
     * Offset to set
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @throws BadMethodCallException
     *
     * @return void
     *
     * @since  1.0.0
     * @link   http://php.net/manual/en/arrayaccess.offsetset.php
     */
    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException('Not supported operation.');
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset
     *
     * @throws BadMethodCallException
     *
     * @return void
     *
     * @since  1.0.0
     * @link   http://php.net/manual/en/arrayaccess.offsetunset.php
     */
    #[ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException('Not supported operation.');
    }
}
