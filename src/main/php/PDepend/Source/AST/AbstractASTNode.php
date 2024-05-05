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
 *
 * @since 0.9.6
 */

namespace PDepend\Source\AST;

use OutOfBoundsException;
use PDepend\Source\ASTVisitor\ASTVisitor;

/**
 * This is an abstract base implementation of the ast node interface.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @since 0.9.6
 */
abstract class AbstractASTNode implements ASTNode
{
    /**
     * Parsed child nodes of this node.
     *
     * @var ASTNode[]
     */
    protected $nodes = [];

    /**
     * The parent node of this node or <b>null</b> when this node is the root
     * of a node tree.
     *
     * @var ASTNode|null
     */
    protected $parent = null;

    /**
     * An optional doc comment for this node.
     *
     * @var string
     */
    protected $comment = null;

    /**
     * Metadata for this node instance, serialized in a string. This string
     * contains the start, end line, and the start, end column and the node
     * image in a colon seperated string.
     *
     * @var string
     *
     * @since 0.10.4
     */
    protected $metadata = '::::';

    /**
     * Constructs a new ast node instance.
     *
     * @param string $image The source image for this node.
     */
    public function __construct($image = null)
    {
        $this->metadata = str_repeat(':', $this->getMetadataSize() - 1);

        $this->setImage($image);
    }

    /**
     * The magic sleep method will be called by PHP's runtime environment right
     * before an instance of this class gets serialized. It should return an
     * array with those property names that should be serialized for this class.
     *
     * @return array
     *
     * @since 0.10.0
     */
    public function __sleep()
    {
        return [
            'comment',
            'metadata',
            'nodes',
        ];
    }

    /**
     * The magic wakeup method will be called by PHP's runtime environment when
     * a previously serialized object gets unserialized. This implementation of
     * the wakeup method restores the dependencies between an ast node and the
     * node's children.
     *
     * @since 0.10.0
     */
    public function __wakeup(): void
    {
        foreach ($this->nodes as $node) {
            $node->setParent($this);
        }
    }

    /**
     * @template T of array<string, mixed>|string|null
     *
     * @param T $data
     *
     * @return T
     */
    public function accept(ASTVisitor $visitor, $data = null)
    {
        $methodName = 'visit' . substr(get_class($this), 22);
        $callable = [$visitor, $methodName];
        assert(is_callable($callable));

        return call_user_func($callable, $this, $data);
    }

    /**
     * Sets the image for this ast node.
     *
     * @param string $image
     */
    public function setImage($image): void
    {
        $this->setMetadata(4, $image);
    }

    /**
     * Returns the source image of this ast node.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->getMetadata(4);
    }

    /**
     * Returns the source image of this ast node without the namespace prefix.
     *
     * @return string
     */
    public function getImageWithoutNamespace()
    {
        $imagePath = explode('\\', $this->getMetadata(4));

        return array_pop($imagePath);
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
     *
     * @since 0.9.10
     */
    public function configureLinesAndColumns(
        $startLine,
        $endLine,
        $startColumn,
        $endColumn,
    ): void {
        $this->setMetadataInteger(0, $startLine);
        $this->setMetadataInteger(1, $endLine);
        $this->setMetadataInteger(2, $startColumn);
        $this->setMetadataInteger(3, $endColumn);
    }

    /**
     * Returns an integer value that was stored under the given index.
     *
     * @param int $index
     *
     * @return int
     *
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
     *
     * @since 0.10.4
     */
    protected function setMetadataInteger($index, $value): void
    {
        $this->setMetadata($index, (string) $value);
    }

    /**
     * Returns a boolean value that was stored under the given index.
     *
     * @param int $index
     *
     * @return bool
     *
     * @since 0.10.4
     */
    protected function getMetadataBoolean($index)
    {
        return (bool) $this->getMetadata($index);
    }

    /**
     * Stores a boolean value under the given index in the internally used data
     * string.
     *
     * @param int  $index
     * @param bool $value
     *
     * @since 0.10.4
     */
    protected function setMetadataBoolean($index, $value): void
    {
        $this->setMetadata($index, $value ? '1' : '0');
    }

    /**
     * Returns the value that was stored under the given index.
     *
     * @param int $index
     *
     * @return string
     *
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
     * @param int    $index
     * @param string $value
     *
     * @since 0.10.4
     */
    protected function setMetadata($index, $value): void
    {
        $metadata         = explode(':', $this->metadata, $this->getMetadataSize());
        $metadata[$index] = $value;

        $this->metadata = implode(':', $metadata);
    }

    /**
     * Returns the total number of the used property bag.
     *
     * @return int
     *
     * @since 0.10.4
     */
    protected function getMetadataSize()
    {
        return 5;
    }

    /**
     * Returns the node instance for the given index or throws an exception.
     *
     * @param int $index
     *
     * @return ASTNode
     *
     * @throws OutOfBoundsException When no node exists at the given index.
     */
    public function getChild($index)
    {
        if (isset($this->nodes[$index])) {
            return $this->nodes[$index];
        }
        throw new OutOfBoundsException(
            sprintf(
                'No node found at index %d in node of type: %s',
                $index,
                get_class($this),
            ),
        );
    }

    /**
     * This method returns all direct children of the actual node.
     *
     * @return ASTNode[]
     */
    public function getChildren()
    {
        return $this->nodes;
    }

    /**
     * This method will search recursive for the first child node that is an
     * instance of the given <b>$targetType</b>. The returned value will be
     * <b>null</b> if no child exists for that.
     *
     * @template T of ASTNode
     *
     * @param class-string<T> $targetType
     *
     * @return T|null
     */
    public function getFirstChildOfType($targetType)
    {
        foreach ($this->nodes as $node) {
            if ($node instanceof $targetType) {
                return $node;
            }
            if (($child = $node->getFirstChildOfType($targetType)) !== null) {
                return $child;
            }
        }
        return null;
    }

    /**
     * This method will search recursive for all child nodes that are an
     * instance of the given <b>$targetType</b>. The returned value will be
     * an empty <b>array</b> if no child exists for that.
     *
     * @template T of ASTNode
     * @template R of T
     *
     * @param class-string<T> $targetType Searched class or interface type.
     * @param R[]             $results    Already found node instances. This parameter
     *                                    is only for internal usage.
     *
     * @return T[]
     */
    public function findChildrenOfType($targetType, array &$results = [])
    {
        foreach ($this->nodes as $node) {
            if ($node instanceof $targetType) {
                $results[] = $node;
            }
            $node->findChildrenOfType($targetType, $results);
        }
        return $results;
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
     * This method adds a new child node to this node instance.
     */
    public function addChild(ASTNode $node): void
    {
        $this->nodes[] = $node;
        $node->setParent($this);
    }

    /**
     * Returns the parent node of this node or <b>null</b> when this node is
     * the root of a node tree.
     *
     * @return ?ASTNode
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Traverses up the node tree and finds all parent nodes that are instances
     * of <b>$parentType</b>.
     *
     * @param string $parentType
     *
     * @return ASTNode[]
     */
    public function getParentsOfType($parentType)
    {
        $parents = [];

        $parentNode = $this->parent;
        while (is_object($parentNode)) {
            if ($parentNode instanceof $parentType) {
                array_unshift($parents, $parentNode);
            }
            $parentNode = $parentNode->getParent();
        }
        return $parents;
    }

    /**
     * Sets the parent node of this node.
     */
    public function setParent(ASTNode $node): void
    {
        $this->parent = $node;
    }

    /**
     * Returns a doc comment for this node or <b>null</b> when no comment was
     * found.
     *
     * @return ?string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Sets the raw doc comment for this node.
     *
     * @param string $comment The doc comment block for this node.
     */
    public function setComment($comment): void
    {
        $this->comment = $comment;
    }
}
