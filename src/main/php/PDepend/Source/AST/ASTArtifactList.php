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

/**
 * Iterator for code nodes.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @template T of ASTArtifact
 *
 * @implements \Iterator<int|string, T>
 * @implements \ArrayAccess<int|string, T>
 */
class ASTArtifactList implements ArrayAccess, Countable, Iterator
{
    /**
     * List of {@link ASTArtifact} objects in
     * this iterator.
     *
     * @var T[]
     */
    private array $artifacts = [];

    /** Total number of available nodes. */
    private int $count = 0;

    /** Current internal offset. */
    private int $offset = 0;

    /**
     * Constructs a new node iterator from the given {@link ASTArtifact}
     * node array.
     *
     * @param T[] $artifacts
     */
    public function __construct(array $artifacts)
    {
        $filter = CollectionArtifactFilter::getInstance();

        $nodeKeys = [];
        foreach ($artifacts as $artifact) {
            $id = $artifact->getId();

            if (isset($nodeKeys[$id])) {
                continue;
            }

            if ($filter->accept($artifact)) {
                $nodeKeys[$id] = $id;
                $this->artifacts[] = $artifact;

                ++$this->count;
            }
        }
    }

    /**
     * Returns the number of {@link ASTArtifact}
     * objects in this iterator.
     */
    public function count(): int
    {
        return count($this->artifacts);
    }

    /**
     * Returns the current node
     *
     * @return T
     * @throws OutOfBoundsException
     */
    public function current(): ASTArtifact
    {
        if ($this->offset >= $this->count) {
            throw new OutOfBoundsException('The offset does not exist.');
        }

        return $this->artifacts[$this->offset];
    }

    /**
     * Returns the name of the current {@link ASTArtifact}.
     */
    public function key(): string
    {
        return $this->artifacts[$this->offset]->getImage();
    }

    /**
     * Moves the internal pointer to the next {@link ASTArtifact}.
     */
    public function next(): void
    {
        ++$this->offset;
    }

    /**
     * Rewinds the internal pointer.
     */
    public function rewind(): void
    {
        $this->offset = 0;
    }

    /**
     * Returns <b>true</b> while there is a next {@link ASTArtifact}.
     */
    public function valid(): bool
    {
        return ($this->offset < $this->count);
    }

    /**
     * Whether a offset exists
     *
     * @param mixed $offset An offset to check for.
     * @return bool Returns true on success or false on failure. The return
     *              value will be casted to boolean if non-boolean was returned.
     * @link   http://php.net/manual/en/arrayaccess.offsetexists.php
     * @since  1.0.0
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->artifacts[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @param int|string $offset
     * @return T Can return all value types.
     * @throws OutOfBoundsException
     * @link   http://php.net/manual/en/arrayaccess.offsetget.php
     * @since  1.0.0
     */
    public function offsetGet(mixed $offset): ASTArtifact
    {
        if (isset($this->artifacts[$offset])) {
            return $this->artifacts[$offset];
        }

        throw new OutOfBoundsException("The offset {$offset} does not exist.");
    }

    /**
     * Offset to set
     *
     * @param int|string $offset
     * @param T $value
     * @throws BadMethodCallException
     * @link   http://php.net/manual/en/arrayaccess.offsetset.php
     * @since  1.0.0
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new BadMethodCallException('Not supported operation.');
    }

    /**
     * Offset to unset
     *
     * @param int|string $offset
     * @throws BadMethodCallException
     * @link   http://php.net/manual/en/arrayaccess.offsetunset.php
     * @since  1.0.0
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new BadMethodCallException('Not supported operation.');
    }
}
