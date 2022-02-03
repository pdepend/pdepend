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

/**
 * This is an abstract base implementation of the ast node interface.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @since 2.3
 */
interface ASTNode
{
    /**
     * Returns the source image of this ast node.
     *
     * @return string
     */
    public function getImage();

    /**
     * Returns the start line for this ast node.
     *
     * @return int
     */
    public function getStartLine();

    /**
     * Returns the start column for this ast node.
     *
     * @return int
     */
    public function getStartColumn();

    /**
     * Returns the end line for this ast node.
     *
     * @return int
     */
    public function getEndLine();

    /**
     * Returns the end column for this ast node.
     *
     * @return int
     */
    public function getEndColumn();

    /**
     * Returns the node instance for the given index or throws an exception.
     *
     * @param int $index
     *
     * @throws OutOfBoundsException When no node exists at the given index.
     *
     * @return AbstractASTNode
     */
    public function getChild($index);

    /**
     * This method returns all direct children of the actual node.
     *
     * @return ASTNode[]
     */
    public function getChildren();

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
    public function getFirstChildOfType($targetType);

    /**
     * This method will search recursive for all child nodes that are an
     * instance of the given <b>$targetType</b>. The returned value will be
     * an empty <b>array</b> if no child exists for that.
     *
     * @template T of ASTNode
     *
     * @param class-string<T> $targetType Searched class or interface type.
     * @param T[]             $results    Already found node instances. This parameter
     *                                    is only for internal usage.
     *
     * @return T[]
     */
    public function findChildrenOfType($targetType, array &$results = array());

    /**
     * Returns the parent node of this node or <b>null</b> when this node is
     * the root of a node tree.
     *
     * @return ASTNode
     */
    public function getParent();

    /**
     * Sets the parent node of this node.
     *
     * @return void
     */
    public function setParent(ASTNode $node);

    /**
     * Traverses up the node tree and finds all parent nodes that are instances
     * of <b>$parentType</b>.
     *
     * @param string $parentType
     *
     * @return ASTNode[]
     */
    public function getParentsOfType($parentType);

    /**
     * Returns a doc comment for this node or <b>null</b> when no comment was
     * found.
     *
     * @return string
     */
    public function getComment();

    /**
     * Sets the raw doc comment for this node.
     *
     * @param string $comment The doc comment block for this node.
     *
     * @return void
     */
    public function setComment($comment);

    /**
     * For better performance we have moved the single setter methods for the
     * node columns and lines into this configure method.
     *
     * @param int $startLine
     * @param int $endLine
     * @param int $startColumn
     * @param int $endColumn
     *
     * @return void
     *
     * @since 0.9.10
     */
    public function configureLinesAndColumns($startLine, $endLine, $startColumn, $endColumn);
}
