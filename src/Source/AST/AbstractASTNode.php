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
 * @since 0.9.6
 */

namespace PDepend\Source\AST;

use OutOfBoundsException;

/**
 * This is an abstract base implementation of the ast node interface.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 0.9.6
 */
abstract class AbstractASTNode implements ASTNode
{
    /**
     * Parsed child nodes of this node.
     *
     * @var ASTNode[]
     */
    protected array $nodes = [];

    /**
     * The parent node of this node or <b>null</b> when this node is the root
     * of a node tree.
     */
    protected ?ASTNode $parent = null;

    /** An optional doc comment for this node. */
    protected ?string $comment = null;

    /**
     * Metadata for this node instance, serialized in a string. This string
     * contains the start, end line, and the start, end column and the node
     * image in a colon seperated string.
     *
     * @since 0.10.4
     */
    protected string $metadata = '::::';

    /**
     * Constructs a new ast node instance.
     *
     * @param ?string $image The source image for this node.
     */
    public function __construct(?string $image = null)
    {
        $this->metadata = str_repeat(':', $this->getMetadataSize() - 1);

        $this->setImage($image);
    }

    /**
     * The magic sleep method will be called by PHP's runtime environment right
     * before an instance of this class gets serialized. It should return an
     * array with those property names that should be serialized for this class.
     *
     * @return list<string>
     * @since 0.10.0
     */
    public function __sleep(): array
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
     * Sets the image for this ast node.
     */
    public function setImage(?string $image): void
    {
        $this->setMetadata(4, $image ?? '');
    }

    /**
     * Returns the source image of this ast node.
     */
    public function getImage(): string
    {
        return $this->getMetadata(4);
    }

    /**
     * Returns the source image of this ast node without the namespace prefix.
     */
    public function getImageWithoutNamespace(): string
    {
        $imagePath = explode('\\', $this->getMetadata(4));

        return array_pop($imagePath);
    }

    /**
     * Returns the start line for this ast node.
     */
    public function getStartLine(): int
    {
        return $this->getMetadataInteger(0);
    }

    /**
     * Returns the start column for this ast node.
     */
    public function getStartColumn(): int
    {
        return $this->getMetadataInteger(2);
    }

    /**
     * Returns the end line for this ast node.
     */
    public function getEndLine(): int
    {
        return $this->getMetadataInteger(1);
    }

    /**
     * Returns the end column for this ast node.
     */
    public function getEndColumn(): int
    {
        return $this->getMetadataInteger(3);
    }

    /**
     * For better performance we have moved the single setter methods for the
     * node columns and lines into this configure method.
     *
     * @since 0.9.10
     */
    public function configureLinesAndColumns(
        int $startLine,
        int $endLine,
        int $startColumn,
        int $endColumn,
    ): void {
        $this->setMetadataInteger(0, $startLine);
        $this->setMetadataInteger(1, $endLine);
        $this->setMetadataInteger(2, $startColumn);
        $this->setMetadataInteger(3, $endColumn);
    }

    /**
     * Returns an integer value that was stored under the given index.
     *
     * @since 0.10.4
     */
    protected function getMetadataInteger(int $index): int
    {
        return (int) $this->getMetadata($index);
    }

    /**
     * Stores an integer value under the given index in the internally used data
     * string.
     *
     * @since 0.10.4
     */
    protected function setMetadataInteger(int $index, int $value): void
    {
        $this->setMetadata($index, (string) $value);
    }

    /**
     * Returns a boolean value that was stored under the given index.
     *
     * @since 0.10.4
     */
    protected function getMetadataBoolean(int $index): bool
    {
        return (bool) $this->getMetadata($index);
    }

    /**
     * Stores a boolean value under the given index in the internally used data
     * string.
     *
     * @since 0.10.4
     */
    protected function setMetadataBoolean(int $index, bool $value): void
    {
        $this->setMetadata($index, $value ? '1' : '0');
    }

    /**
     * Returns the value that was stored under the given index.
     *
     * @since 0.10.4
     */
    protected function getMetadata(int $index): string
    {
        $metadata = explode(':', $this->metadata, $this->getMetadataSize());

        return $metadata[$index];
    }

    /**
     * Stores the given value under the given index in an internal storage
     * container.
     *
     * @since 0.10.4
     */
    protected function setMetadata(int $index, string $value): void
    {
        $metadata = explode(':', $this->metadata, $this->getMetadataSize());
        $metadata[$index] = $value;

        $this->metadata = implode(':', $metadata);
    }

    /**
     * Returns the total number of the used property bag.
     *
     * @since 0.10.4
     */
    protected function getMetadataSize(): int
    {
        return 5;
    }

    /**
     * Returns the node instance for the given index or throws an exception.
     *
     * @throws OutOfBoundsException When no node exists at the given index.
     */
    public function getChild(int $index): ASTNode
    {
        if (isset($this->nodes[$index])) {
            return $this->nodes[$index];
        }

        throw new OutOfBoundsException(
            sprintf(
                'No node found at index %d in node of type: %s',
                $index,
                static::class,
            ),
        );
    }

    /**
     * This method returns all direct children of the actual node.
     *
     * @return ASTNode[]
     */
    public function getChildren(): array
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
     * @return T|null
     */
    public function getFirstChildOfType($targetType): ?ASTNode
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
     *
     * @param class-string<T> $targetType Searched class or interface type.
     * @param T[] $results Already found node instances. This parameter
     *                     is only for internal usage.
     * @return T[]
     */
    public function findChildrenOfType($targetType, array &$results = []): array
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
     */
    public function getParent(): ?ASTNode
    {
        return $this->parent;
    }

    /**
     * Traverses up the node tree and finds all parent nodes that are instances
     * of <b>$parentType</b>.
     *
     * @return ASTNode[]
     */
    public function getParentsOfType(string $parentType): array
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
    public function setParent(?ASTNode $node): void
    {
        $this->parent = $node;
    }

    /**
     * Returns a doc comment for this node or <b>null</b> when no comment was
     * found.
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Sets the raw doc comment for this node.
     *
     * @param ?string $comment The doc comment block for this node.
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }
}
