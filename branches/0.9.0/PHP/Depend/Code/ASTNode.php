<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 * @since      0.9.6
 */

require_once 'PHP/Depend/Code/ASTNodeI.php';

/**
 * This is an abstract base implementation of the ast node interface.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 * @since      0.9.6
 */
abstract class PHP_Depend_Code_ASTNode implements PHP_Depend_Code_ASTNodeI
{
    /**
     * The source image for this node instance.
     *
     * @var string $image
     */
    protected $image = null;

    /**
     * Parsed child nodes of this node.
     *
     * @var array(PHP_Depend_Code_ASTNodeI) $nodes
     */
    protected $nodes = array();

    /**
     * An optional doc comment for this node.
     *
     * @var string $comment
     */
    protected $comment = null;

    /**
     * Constructs a new ast node instance.
     *
     * @param string $image The source image for this node.
     */
    public function __construct($image)
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
     * Returns the start line for this ast node.
     *
     * @return integer
     */
    public function getStartLine()
    {
        $tokens = $this->getTokens();
        $token  = reset($tokens);

        assert($token instanceof PHP_Depend_Token);
        return $token->startLine;
    }

    /**
     * Returns the start column for this ast node.
     *
     * @return integer
     */
    public function getStartColumn()
    {
        $tokens = $this->getTokens();
        $token  = reset($tokens);

        assert($token instanceof PHP_Depend_Token);
        return $token->startColumn;
    }

    /**
     * Returns the end line for this ast node.
     *
     * @return integer
     */
    public function getEndLine()
    {
        $tokens = $this->getTokens();
        $token  = end($tokens);

        assert($token instanceof PHP_Depend_Token);
        return $token->endLine;
    }

    /**
     * Returns the end column for this ast node.
     *
     * @return integer
     */
    public function getEndColumn()
    {
        $tokens = $this->getTokens();
        $token  = end($tokens);

        assert($token instanceof PHP_Depend_Token);
        return $token->endColumn;
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
        $this->nodes[] = $node;
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
     * This method will return the source tokens parsed for this node.
     *
     * @return array(PHP_Depend_Token)
     */
    public function getTokens()
    {
        $storage = PHP_Depend_StorageRegistry::get(PHP_Depend::TOKEN_STORAGE);
        return (array) $storage->restore(spl_object_hash($this), get_class($this));
    }

    /**
     * Sets the source tokens parsed for this node.
     *
     * @param array(PHP_Depend_Token) $tokens The source tokens of this node.
     *
     * @return void
     */
    public function setTokens(array $tokens)
    {
        $storage = PHP_Depend_StorageRegistry::get(PHP_Depend::TOKEN_STORAGE);
        $storage->store($tokens, spl_object_hash($this), get_class($this));
    }
}
?>
