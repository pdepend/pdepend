<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 * @since      0.9.6
 */

/**
 * This is an abstract base implementation of the ast node interface.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 * @since      0.9.6
 */
abstract class PHP_Depend_Code_ASTNode implements PHP_Depend_Code_ASTNodeI
{
    /**
     * The type of this class.
     * 
     * @since 0.10.0
     */
    const CLAZZ = __CLASS__;

    /**
     * The source image for this node instance.
     *
     * @var string
     */
    protected $image = null;

    /**
     * Parsed child nodes of this node.
     *
     * @var array(PHP_Depend_Code_ASTNodeI)
     */
    protected $nodes = array();

    /**
     * The parent node of this node or <b>null</b> when this node is the root
     * of a node tree.
     *
     * @var PHP_Depend_Code_ASTNodeI
     */
    protected $parent = null;

    /**
     * An optional doc comment for this node.
     *
     * @var string $comment
     */
    protected $comment = null;

    /**
     * The start line for this node.
     *
     * @var integer
     */
    protected $startLine = 0;

    /**
     * The end line for this node.
     *
     * @var integer
     */
    protected $endLine = 0;

    /**
     * The start column for this node.
     *
     * @var integer
     */
    protected $startColumn = 0;

    /**
     * The end column for this node.
     *
     * @var integer
     */
    protected $endColumn = 0;

    /**
     * Constructs a new ast node instance.
     *
     * @param string $image The source image for this node.
     */
    public function __construct($image = null)
    {
        $this->image = $image;
    }

    /**
     * Returns the source image of this ast node.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets the start line for this ast node.
     *
     * @param integer $startLine The node start line.
     *
     * @return void
     * @since 0.9.12
     */
    public function setStartLine($startLine)
    {
        $this->startLine = $startLine;
    }

    /**
     * Returns the start line for this ast node.
     *
     * @return integer
     */
    public function getStartLine()
    {
        return $this->startLine;
    }

    /**
     * Sets the start column for this ast node.
     *
     * @param integer $startColumn The node start column.
     *
     * @return void
     * @since 0.9.12
     */
    public function setStartColumn($startColumn)
    {
        $this->startColumn = $startColumn;
    }

    /**
     * Returns the start column for this ast node.
     *
     * @return integer
     */
    public function getStartColumn()
    {
        return $this->startColumn;
    }

    /**
     * Sets the node's end line.
     *
     * @param integer $endLine The node's end line.
     *
     * @return void
     * @since 0.9.12
     */
    public function setEndLine($endLine)
    {
        $this->endLine = $endLine;
    }

    /**
     * Returns the end line for this ast node.
     *
     * @return integer
     */
    public function getEndLine()
    {
        return $this->endLine;
    }

    /**
     * Sets the node's end column.
     *
     * @param integer $endColumn The node's end column.
     *
     * @return void
     * @since 0.9.12
     */
    public function setEndColumn($endColumn)
    {
        $this->endColumn = $endColumn;
    }

    /**
     * Returns the end column for this ast node.
     *
     * @return integer
     */
    public function getEndColumn()
    {
        return $this->endColumn;
    }

    /**
     * For better performance we have moved the single setter methods for the
     * node columns and lines into this configure method.
     *
     * @param integer $startLine   The node's start line.
     * @param integer $endLine     The node's end line.
     * @param integer $startColumn The node's start column.
     * @param integer $endColumn   The node's end column.
     *
     * @return void
     * @since 0.9.10
     */
    public function configureLinesAndColumns(
        $startLine,
        $endLine,
        $startColumn,
        $endColumn
    ) {
        $this->startLine   = $startLine;
        $this->startColumn = $startColumn;
        $this->endLine     = $endLine;
        $this->endColumn   = $endColumn;
    }

    /**
     * Returns the node instance for the given index or throws an exception.
     *
     * @param integer $index Index of the requested node.
     *
     * @return PHP_Depend_Code_ASTNodeI
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
                get_class($this)
            )
        );
    }

    /**
     * This method returns all direct children of the actual node.
     *
     * @return array(PHP_Depend_Code_ASTNodeI)
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
     * @param string $targetType Searched class or interface type.
     *
     * @return PHP_Depend_Code_ASTNodeI
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
     *
     * @return array(PHP_Depend_Code_ASTNodeI)
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
     * This method adds a new child node to this node instance.
     *
     * @param PHP_Depend_Code_ASTNodeI $node The new child node.
     *
     * @return void
     */
    public function addChild(PHP_Depend_Code_ASTNodeI $node)
    {
        // Store child node
        $this->nodes[] = $node;

        // Set this as parent
        $node->setParent($this);
    }

    /**
     * Returns the parent node of this node or <b>null</b> when this node is
     * the root of a node tree.
     *
     * @return PHP_Depend_Code_ASTNodeI
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Traverses up the node tree and finds all parent nodes that are instances
     * of <b>$parentType</b>.
     *
     * @param string $parentType Class/interface type you are looking for,
     *
     * @return array(PHP_Depend_Code_ASTNodeI)
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
     * Sets the parent node of this node.
     *
     * @param PHP_Depend_Code_ASTNodeI $node The parent node of this node.
     *
     * @return void
     */
    public function setParent(PHP_Depend_Code_ASTNodeI $node)
    {
        $this->parent = $node;
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
     * This method can be called by the PHP_Depend runtime environment or a
     * utilizing component to free up memory. This methods are required for
     * PHP version < 5.3 where cyclic references can not be resolved
     * automatically by PHP's garbage collector.
     *
     * @return void
     * @since 0.9.12
     */
    public function free()
    {
        $this->_removeReferenceToParentNode();
        $this->_removeReferencesToChildNodes();
    }

    /**
     * The magic sleep method will be called by PHP's runtime environment right
     * before an instance of this class gets serialized. It should return an
     * array with those property names that should be serialized for this class.
     *
     * @return array(string)
     * @since 0.10.0
     */
    public function  __sleep()
    {
        return array(
            'image',
            'comment',
            'startLine',
            'startColumn',
            'endLine',
            'endColumn',
            'nodes'
        );
    }

    /**
     * The magic wakeup method will be called by PHP's runtime environment when
     * a previously serialized object gets unserialized. This implementation of
     * the wakeup method restores the dependencies between an ast node and the
     * node's children.
     *
     * @return void
     * @since 0.10.0
     */
    public function __wakeup()
    {
        foreach ($this->nodes as $node) {
            $node->parent = $this;
        }
    }

    /**
     * Removes the reference to the parent node instance.
     *
     * @return void
     * @since 0.9.12
     */
    private function _removeReferenceToParentNode()
    {
        $this->parent = null;
    }

    /**
     * Removes the reference between this node and its child nodes.
     *
     * @return void
     * @since 0.9.12
     */
    private function _removeReferencesToChildNodes()
    {
        foreach ($this->nodes as $node) {
            $node->free();
        }
        $this->nodes = array();
    }
}
