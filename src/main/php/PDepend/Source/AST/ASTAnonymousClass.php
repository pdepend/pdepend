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
 * @since 2.3
 */

namespace PDepend\Source\AST;

use PDepend\Source\ASTVisitor\ASTVisitor;

/**
 * Represents a php class node.
 *
 * @copyright 2008-2015 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 2.3
 */
class ASTAnonymousClass extends ASTClass implements ASTNode
{

    /**
     * Parsed child nodes of this node.
     *
     * @var \PDepend\Source\AST\ASTNode[]
     */
    protected $nodes = array();

    /**
     * The parent node of this node or <b>null</b> when this node is the root
     * of a node tree.
     *
     * @var \PDepend\Source\AST\ASTNode
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
     * @since 0.10.4
     */
    protected $metadata = '::::';

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
    public function configureLinesAndColumns(
      $startLine,
      $endLine,
      $startColumn,
      $endColumn
    ) {
        $this->setMetadataInteger(0, $startLine);
        $this->setMetadataInteger(1, $endLine);
        $this->setMetadataInteger(2, $startColumn);
        $this->setMetadataInteger(3, $endColumn);
    }

    /**
     * Returns the node instance for the given index or throws an exception.
     *
     * @param integer $index
     * @return \PDepend\Source\AST\ASTNode
     * @throws \OutOfBoundsException When no node exists at the given index.
     */
    public function getChild($index)
    {
        if (isset($this->nodes[$index])) {
            return $this->nodes[$index];
        }
        throw new \OutOfBoundsException(
          sprintf(
            'No node found at index %d in node of type: %s',
            $index,
            get_class($this)
          )
        );
    }

    /**
     * This method returns all direct children of the actual node.
     *
     * @return \PDepend\Source\AST\ASTNode[]
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
     * @param string $targetType
     * @return \PDepend\Source\AST\ASTNode
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
     * @param string $targetType Searched class or interface type.
     * @param array  &$results   Already found node instances. This parameter
     *        is only for internal usage.
     * @return \PDepend\Source\AST\ASTNode[]
     */
    public function findChildrenOfType($targetType, array &$results = array())
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
     * Returns a doc comment for this node or <b>null</b> when no comment was
     * found.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Sets the raw doc comment for this node.
     *
     * @param string $comment The doc comment block for this node.
     *
     * @return void
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
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
     * ASTVisitor method for node tree traversal.
     *
     * @param \PDepend\Source\ASTVisitor\ASTVisitor $visitor
     * @return void
     */
    public function accept(ASTVisitor $visitor)
    {
        $visitor->visitClass($this);
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
        return array_merge(
          array('comment', 'metadata', 'nodes'),
          parent::__sleep()
        );
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
        return 5;
    }
}
