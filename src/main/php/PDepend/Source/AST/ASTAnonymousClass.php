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
 * @since 2.3
 */

namespace PDepend\Source\AST;

/**
 * Represents a php class node.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 2.3
 */
class ASTAnonymousClass extends ASTClass implements ASTNode
{
    /**
     * Metadata for this node instance, serialized in a string. This string
     * contains the start, end line, and the start, end column and the node
     * image in a colon separated string.
     *
     * @var string
     * @since 0.10.4
     */
    protected $metadata = ':::';

    /**
     * The magic sleep method will be called by PHP's runtime environment right
     * before an instance of this class gets serialized. It should return an
     * array with those property names that should be serialized for this class.
     *
     * @return array
     * @since 0.10.0
     */
    public function __sleep()
    {
        return ['metadata', ...parent::__sleep()];
    }

    /**
     * The magic wakeup method will be called by PHP's runtime environment when
     * a serialized instance of this class was unserialized. This implementation
     * of the wakeup method will register this object in the the global class
     * context.
     */
    public function __wakeup(): void
    {
        $this->methods = null;

        foreach ($this->nodes as $node) {
            $node->setParent($this);
        }

        parent::__wakeup();
    }

    /**
     * @param string $image
     */
    public function setImage($image): void
    {
        $this->setName($image);
    }

    /**
     * Returns the start line for this ast node.
     *
     * @return int
     */
    public function getStartLine()
    {
        return $this->getMetadataInteger(0);
    }

    /**
     * Returns the start column for this ast node.
     *
     * @return int
     */
    public function getStartColumn()
    {
        return $this->getMetadataInteger(2);
    }

    /**
     * Returns the end line for this ast node.
     *
     * @return int
     */
    public function getEndLine()
    {
        return $this->getMetadataInteger(1);
    }

    /**
     * Returns the end column for this ast node.
     *
     * @return int
     */
    public function getEndColumn()
    {
        return $this->getMetadataInteger(3);
    }

    /**
     * For better performance we have moved the single setter methods for the
     * node columns and lines into this configure method.
     *
     * @param int $startLine
     * @param int $endLine
     * @param int $startColumn
     * @param int $endColumn
     * @since 0.9.10
     */
    public function configureLinesAndColumns($startLine, $endLine, $startColumn, $endColumn): void
    {
        $this->setMetadataInteger(0, $startLine);
        $this->setMetadataInteger(1, $endLine);
        $this->setMetadataInteger(2, $startColumn);
        $this->setMetadataInteger(3, $endColumn);
    }

    /**
     * This method adds a new child node at the first position of the children.
     */
    public function prependChild(ASTNode $node): void
    {
        array_unshift($this->nodes, $node);
        $node->setParent($this);
    }

    /**
     * Will return <b>true</b> if this class was declared anonymous in an
     * allocation expression.
     *
     * @return bool
     */
    public function isAnonymous()
    {
        return true;
    }

    /**
     * Returns an integer value that was stored under the given index.
     *
     * @param int $index
     * @return int
     * @since 0.10.4
     */
    protected function getMetadataInteger($index)
    {
        return (int) $this->getMetadata($index);
    }

    /**
     * Stores an integer value under the given index in the internally used data
     * string.
     *
     * @param int $index
     * @param int $value
     * @since 0.10.4
     */
    protected function setMetadataInteger($index, $value): void
    {
        $this->setMetadata($index, (string) $value);
    }

    /**
     * Returns the value that was stored under the given index.
     *
     * @param int $index
     * @return string
     * @since 0.10.4
     */
    protected function getMetadata($index)
    {
        $metadata = explode(':', $this->metadata, $this->getMetadataSize());

        return $metadata[$index];
    }

    /**
     * Stores the given value under the given index in an internal storage
     * container.
     *
     * @param int $index
     * @param string $value
     * @since 0.10.4
     */
    protected function setMetadata($index, $value): void
    {
        $metadata = explode(':', $this->metadata, $this->getMetadataSize());
        $metadata[$index] = $value;

        $this->metadata = implode(':', $metadata);
    }

    /**
     * Returns the total number of the used property bag.
     *
     * @return int
     * @since 0.10.4
     */
    protected function getMetadataSize()
    {
        return 4;
    }
}
