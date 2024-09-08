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

use OutOfBoundsException;

/**
 * Abstract base class for code item.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
abstract class AbstractASTArtifact implements ASTArtifact
{
    /** The name for this item. */
    protected string $name = '';

    /** The unique identifier for this function. */
    protected string $id;

    /** The line number where the item declaration starts. */
    protected int $startLine = 0;

    /** The line number where the item declaration ends. */
    protected int $endLine = 0;

    protected int $startColumn = 0;

    protected int $endColumn = 0;

    /**
     * The parent node of this node or <b>null</b> when this node is the root
     * of a node tree.
     */
    protected ?ASTNode $parent = null;

    /** The source file for this item. */
    protected ?ASTCompilationUnit $compilationUnit = null;

    /** The comment for this type. */
    protected ?string $comment = null;

    /**
     * Constructs a new item for the given <b>$name</b>.
     *
     * @param ?string $name The item name.
     */
    public function __construct(?string $name = null)
    {
        $this->name = $name ?? '';
    }

    /**
     * Returns the source image of this ast node.
     */
    public function getImage(): string
    {
        return $this->name;
    }

    /**
     * Sets the item name.
     *
     * @param string $name The item name.
     * @since  1.0.0
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Returns a id for this code node.
     */
    public function getId(): string
    {
        if (!isset($this->id)) {
            $this->id = md5(uniqid('', true));
        }

        return $this->id;
    }

    /**
     * Sets the unique identifier for this node instance.
     *
     * @param string $id Identifier for this node.
     * @since  0.9.12
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * Returns the source file for this item.
     */
    public function getCompilationUnit(): ?ASTCompilationUnit
    {
        return $this->compilationUnit;
    }

    /**
     * Sets the source file for this item.
     */
    public function setCompilationUnit(?ASTCompilationUnit $compilationUnit): void
    {
        if ($this->compilationUnit?->getFileName() === null) {
            $this->compilationUnit = $compilationUnit;
        }
    }

    public function getChild(int $index): ASTNode
    {
        $children = $this->getChildren();
        if (isset($children[$index])) {
            return $children[$index];
        }

        throw new OutOfBoundsException(
            sprintf(
                'No node found at index %d in node of type: %s',
                $index,
                static::class
            )
        );
    }

    public function getChildren(): array
    {
        return [];
    }

    public function getFirstChildOfType($targetType): ?ASTNode
    {
        $children = $this->getChildren();
        foreach ($children as $node) {
            if ($node instanceof $targetType) {
                return $node;
            }
            if (($child = $node->getFirstChildOfType($targetType)) !== null) {
                return $child;
            }
        }

        return null;
    }

    public function findChildrenOfType($targetType, array &$results = []): array
    {
        $children = $this->getChildren();
        foreach ($children as $node) {
            if ($node instanceof $targetType) {
                $results[] = $node;
            }
            $node->findChildrenOfType($targetType, $results);
        }

        return $results;
    }

    public function getParent(): ?ASTNode
    {
        return $this->parent;
    }

    public function setParent(?ASTNode $node): void
    {
        $this->parent = $node;
    }

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
     * Returns a doc comment for this node or <b>null</b> when no comment was
     * found.
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Sets the raw doc comment for this node.
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    public function configureLinesAndColumns(int $startLine, int $endLine, int $startColumn, int $endColumn): void
    {
        $this->startLine = $startLine;
        $this->endLine = $endLine;
        $this->startColumn = $startColumn;
        $this->endColumn = $endColumn;
    }

    /**
     * Returns the line number where the class or interface declaration starts.
     */
    public function getStartLine(): int
    {
        return $this->startLine;
    }

    public function getStartColumn(): int
    {
        return $this->startColumn;
    }

    /**
     * Returns the line number where the class or interface declaration ends.
     */
    public function getEndLine(): int
    {
        return $this->endLine;
    }

    public function getEndColumn(): int
    {
        return $this->endColumn;
    }
}
