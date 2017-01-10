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

use PDepend\Source\ASTVisitor\ASTVisitor;

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
     * The parent node of this node or <b>null</b> when this node is the root
     * of a node tree.
     *
     * @var \PDepend\Source\AST\ASTNode
     */
    protected $parent = null;

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
     * @param string $image
     * @return void
     */
    public function setImage($image)
    {
        $this->setName($image);
    }

    /**
     * Returns the source image of this ast node.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->getName();
    }

    /**
     * Returns the start line for this ast node.
     *
     * @return integer
     */
    public function getStartLine()
    {
        return $this->getMetadataInteger(0);
    }

    /**
     * Returns the start column for this ast node.
     *
     * @return integer
     */
    public function getStartColumn()
    {
        return $this->getMetadataInteger(2);
    }

    /**
     * Returns the end line for this ast node.
     *
     * @return integer
     */
    public function getEndLine()
    {
        return $this->getMetadataInteger(1);
    }

    /**
     * Returns the end column for this ast node.
     *
     * @return integer
     */
    public function getEndColumn()
    {
        return $this->getMetadataInteger(3);
    }

    /**
     * For better performance we have moved the single setter methods for the
     * node columns and lines into this configure method.
     *
     * @param integer $startLine
     * @param integer $endLine
     * @param integer $startColumn
     * @param integer $endColumn
     * @return void
     * @since 0.9.10
     */
    public function configureLinesAndColumns($startLine, $endLine, $startColumn, $endColumn)
    {
        $this->setMetadataInteger(0, $startLine);
        $this->setMetadataInteger(1, $endLine);
        $this->setMetadataInteger(2, $startColumn);
        $this->setMetadataInteger(3, $endColumn);
    }

    /**
     * Returns the parent node of this node or <b>null</b> when this node is
     * the root of a node tree.
     *
     * @return \PDepend\Source\AST\ASTNode
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the parent node of this node.
     *
     * @param \PDepend\Source\AST\ASTNode $node
     * @return void
     */
    public function setParent(ASTNode $node)
    {
        $this->parent = $node;
    }

    /**
     * Traverses up the node tree and finds all parent nodes that are instances
     * of <b>$parentType</b>.
     *
     * @param string $parentType
     * @return \PDepend\Source\AST\ASTNode[]
     */
    public function getParentsOfType($parentType)
    {
        $parents = array();

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
     * This method adds a new child node at the first position of the children.
     *
     * @param \PDepend\Source\AST\ASTNode $node
     * @return void
     */
    public function prependChild(ASTNode $node)
    {
        array_unshift($this->nodes, $node);
        $node->setParent($this);
    }

    /**
     * Will return <b>true</b> if this class was declared anonymous in an
     * allocation expression.
     *
     * @return boolean
     */
    public function isAnonymous()
    {
        return true;
    }

    /**
     * @param \PDepend\Source\ASTVisitor\ASTVisitor $visitor
     * @param mixed $data
     * @return void
     */
    public function accept(ASTVisitor $visitor, $data = null)
    {
        return $visitor->visitAnonymousClass($this, $data);
    }

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
        return array_merge(array('metadata'), parent::__sleep());
    }

    /**
     * The magic wakeup method will be called by PHP's runtime environment when
     * a serialized instance of this class was unserialized. This implementation
     * of the wakeup method will register this object in the the global class
     * context.
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->methods = null;

        foreach ($this->nodes as $node) {
            $node->setParent($this);
        }

        parent::__wakeup();

    }

    /**
     * Returns an integer value that was stored under the given index.
     *
     * @param integer $index
     * @return integer
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
     * @param integer $index
     * @param integer $value
     * @return void
     * @since 0.10.4
     */
    protected function setMetadataInteger($index, $value)
    {
        $this->setMetadata($index, $value);
    }

    /**
     * Returns the value that was stored under the given index.
     *
     * @param integer $index
     * @return mixed
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
     * @param integer $index
     * @param mixed $value
     * @return void
     * @since 0.10.4
     */
    protected function setMetadata($index, $value)
    {
        $metadata         = explode(':', $this->metadata, $this->getMetadataSize());
        $metadata[$index] = $value;

        $this->metadata = join(':', $metadata);
    }

    /**
     * Returns the total number of the used property bag.
     *
     * @return integer
     * @since 0.10.4
     */
    protected function getMetadataSize()
    {
        return 4;
    }
}
