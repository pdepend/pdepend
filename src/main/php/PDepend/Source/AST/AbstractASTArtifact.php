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

use PDepend\Source\ASTVisitor\ASTVisitor;

/**
 * Abstract base class for code item.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
abstract class AbstractASTArtifact implements ASTArtifact
{
    /**
     * The name for this item.
     *
     * @var string
     */
    protected $name = '';

    /**
     * The unique identifier for this function.
     *
     * @var string|null
     */
    protected $id = null;

    /**
     * The line number where the item declaration starts.
     *
     * @var int
     */
    protected $startLine = 0;

    /**
     * The line number where the item declaration ends.
     *
     * @var int
     */
    protected $endLine = 0;

    /**
     * The source file for this item.
     *
     * @var ASTCompilationUnit|null
     */
    protected $compilationUnit = null;

    /**
     * The comment for this type.
     *
     * @var string|null
     */
    protected $comment = null;

    /**
     * Constructs a new item for the given <b>$name</b>.
     *
     * @param string $name The item name.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the source image of this ast node.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->name;
    }

    /**
     * Returns the item name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the item name.
     *
     * @param string $name The item name.
     *
     * @return void
     *
     * @since  1.0.0
     */
    public function setName($name)
    {
        $this->name = $name;
    }



    /**
     * Returns a id for this code node.
     *
     * @return string
     */
    public function getId()
    {
        if ($this->id === null) {
            $this->id = md5(uniqid('', true));
        }
        return $this->id;
    }

    /**
     * Sets the unique identifier for this node instance.
     *
     * @param string $id Identifier for this node.
     *
     * @return void
     *
     * @since  0.9.12
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns the source file for this item.
     *
     * @return ASTCompilationUnit|null
     */
    public function getCompilationUnit()
    {
        return $this->compilationUnit;
    }

    /**
     * Sets the source file for this item.
     *
     * @return void
     */
    public function setCompilationUnit(ASTCompilationUnit $compilationUnit)
    {
        if ($this->compilationUnit === null || $this->compilationUnit->getName() === null) {
            $this->compilationUnit = $compilationUnit;
        }
    }

    /**
     * Returns a doc comment for this node or <b>null</b> when no comment was
     * found.
     *
     * @return string|null
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Sets the raw doc comment for this node.
     *
     * @param string $comment
     *
     * @return void
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Returns the line number where the class or interface declaration starts.
     *
     * @return int
     */
    public function getStartLine()
    {
        return $this->startLine;
    }

    /**
     * Returns the line number where the class or interface declaration ends.
     *
     * @return int
     */
    public function getEndLine()
    {
        return $this->endLine;
    }

    // BEGIN@deprecated

    /**
     * Returns the doc comment for this item or <b>null</b>.
     *
     * @return string
     *
     * @deprecated Use getComment() inherit from ASTNode instead.
     */
    public function getDocComment()
    {
        return $this->getComment();
    }

    /**
     * Sets the doc comment for this item.
     *
     * @param string $docComment
     *
     * @return void
     *
     * @deprecated Use setComment() inherit from ASTNode instead.
     */
    public function setDocComment($docComment)
    {
        $this->setComment($docComment);
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

        return call_user_func(array($visitor, $methodName), $this, $data);
    }

    // END@deprecated
}
