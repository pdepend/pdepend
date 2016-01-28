<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2015, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2015 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 0.9.6
 */

namespace PDepend\Source\AST;

/**
 * This is an abstract base implementation of the ast node interface.
 *
 * @copyright 2008-2015 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 2.3
 */
interface ASTNode
{
    /**
     * Sets the image for this ast node.
     *
     * @param string $image
     * @return void
     */
    public function setImage($image);

    /**
     * Returns the source image of this ast node.
     *
     * @return string
     */
    public function getImage();

    /**
     * Sets the start line for this ast node.
     *
     * @param integer $startLine The node start line.
     * @return void
     */
    public function setStartLine($startLine);

    /**
     * Returns the start line for this ast node.
     *
     * @return integer
     */
    public function getStartLine();

    /**
     * Sets the start column for this ast node.
     *
     * @param integer $startColumn
     * @return void
     */
    public function setStartColumn($startColumn);

    /**
     * Returns the start column for this ast node.
     *
     * @return integer
     */
    public function getStartColumn();

    /**
     * Sets the node's end line.
     *
     * @param integer $endLine
     * @return void
     */
    public function setEndLine($endLine);

    /**
     * Returns the end line for this ast node.
     *
     * @return integer
     */
    public function getEndLine();

    /**
     * Sets the node's end column.
     *
     * @param integer $endColumn
     * @return void
     */
    public function setEndColumn($endColumn);

    /**
     * Returns the end column for this ast node.
     *
     * @return integer
     */
    public function getEndColumn();

    /**
     * Returns the node instance for the given index or throws an exception.
     *
     * @param integer $index
     * @return \PDepend\Source\AST\ASTNode
     * @throws \OutOfBoundsException When no node exists at the given index.
     */
    public function getChild($index);

    /**
     * This method returns all direct children of the actual node.
     *
     * @return \PDepend\Source\AST\ASTNode[]
     */
    public function getChildren();

    /**
     * This method will search recursive for the first child node that is an
     * instance of the given <b>$targetType</b>. The returned value will be
     * <b>null</b> if no child exists for that.
     *
     * @param string $targetType
     * @return \PDepend\Source\AST\ASTNode
     */
    public function getFirstChildOfType($targetType);

    /**
     * This method will search recursive for all child nodes that are an
     * instance of the given <b>$targetType</b>. The returned value will be
     * an empty <b>array</b> if no child exists for that.
     *
     * @param string $targetType Searched class or interface type.
     * @param array &$results Already found node instances. This parameter
     *        is only for internal usage.
     * @return \PDepend\Source\AST\ASTNode[]
     */
    public function findChildrenOfType($targetType, array &$results = array());

    /**
     * This method adds a new child node at the first position of the children.
     *
     * @param \PDepend\Source\AST\ASTNode $node
     * @return void
     */
    public function prependChild(ASTNode $node);

    /**
     * This method adds a new child node to this node instance.
     *
     * @param \PDepend\Source\AST\ASTNode $node
     * @return void
     */
    public function addChild(ASTNode $node);

    /**
     * Returns the parent node of this node or <b>null</b> when this node is
     * the root of a node tree.
     *
     * @return \PDepend\Source\AST\ASTNode
     */
    public function getParent();

    /**
     * Traverses up the node tree and finds all parent nodes that are instances
     * of <b>$parentType</b>.
     *
     * @param string $parentType
     * @return \PDepend\Source\AST\ASTNode[]
     */
    public function getParentsOfType($parentType);

    /**
     * Sets the parent node of this node.
     *
     * @param \PDepend\Source\AST\ASTNode $node
     * @return void
     */
    public function setParent(ASTNode $node);

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
}
