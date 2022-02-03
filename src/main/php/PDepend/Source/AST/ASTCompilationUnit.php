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
use PDepend\Source\Tokenizer\Token;
use PDepend\Util\Cache\CacheDriver;

/**
 * This class provides an interface to a single source file.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class ASTCompilationUnit extends AbstractASTArtifact
{
    /**
     * The internal used cache instance.
     *
     * @var CacheDriver|null
     *
     * @since 0.10.0
     */
    protected $cache = null;

    /**
     * The unique identifier for this function.
     *
     * @var string|null
     */
    protected $id = null;

    /**
     * The source file name/path.
     *
     * @var string|null
     */
    protected $fileName = null;

    /**
     * The comment for this type.
     *
     * @var string|null
     */
    protected $comment = null;

    /**
     * The files start line. This property must always have the value <em>1</em>.
     *
     * @var int
     *
     * @since 0.10.0
     */
    protected $startLine = 0;

    /**
     * The files end line.
     *
     * @var int
     *
     * @since 0.10.0
     */
    protected $endLine = 0;

    /**
     * List of classes, interfaces and functions that parsed from this file.
     *
     * @var AbstractASTArtifact[]
     *
     * @since 0.10.0
     */
    protected $childNodes = array();

    /**
     * Was this file instance restored from the cache?
     *
     * @var bool
     *
     * @since 0.10.0
     */
    protected $cached = false;

    /**
     * Normalized code in this file.
     *
     * @var string|null
     */
    private $source = null;

    /**
     * Constructs a new source file instance.
     *
     * @param string|null $fileName The source file name/path.
     */
    public function __construct($fileName)
    {
        if ($fileName && strpos($fileName, 'php://') === 0) {
            $this->fileName = $fileName;
        } elseif ($fileName !== null) {
            $this->fileName = realpath($fileName) ?: null;
        }
    }

    /**
     * Returns the physical file name for this object.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->fileName;
    }

    /**
     * Returns the physical file name for this object.
     *
     * @return string|null
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Returns a id for this code node.
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the unique identifier for this file instance.
     *
     * @param string $id Identifier for this file.
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
     * Setter method for the used parser and token cache.
     *
     * @return $this
     *
     * @since  0.10.0
     */
    public function setCache(CacheDriver $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * Returns normalized source code with stripped whitespaces.
     *
     * @return string|null
     */
    public function getSource()
    {
        $this->readSource();
        return $this->source;
    }

    /**
     * Returns an <b>array</b> with all tokens within this file.
     *
     * @return array<Token>
     */
    public function getTokens()
    {
        return (array) $this->cache
            ->type('tokens')
            ->restore($this->getId());
    }

    /**
     * Sets the tokens for this file.
     *
     * @param array<Token> $tokens The generated tokens.
     *
     * @return void
     */
    public function setTokens(array $tokens)
    {
        $this->cache
            ->type('tokens')
            ->store($this->getId(), $tokens);
    }

    /**
     * Adds a source item that was parsed from this source file.
     *
     * @return void
     *
     * @since  0.10.0
     */
    public function addChild(AbstractASTArtifact $artifact)
    {
        $this->childNodes[$artifact->getId()] = $artifact;
    }

    /**
     * Returns the start line number for this source file. For an existing file
     * this value must always be <em>1</em>, while it can be <em>0</em> for a
     * not existing dummy file.
     *
     * @return int
     *
     * @since  0.10.0
     */
    public function getStartLine()
    {
        if ($this->startLine === 0) {
            $this->readSource();
        }
        return $this->startLine;
    }

    /**
     * Returns the start line number for this source file. For an existing file
     * this value must always be greater <em>0</em>, while it can be <em>0</em>
     * for a not existing dummy file.
     *
     * @return int
     *
     * @since  0.10.0
     */
    public function getEndLine()
    {
        if ($this->endLine === 0) {
            $this->readSource();
        }
        return $this->endLine;
    }

    /**
     * This method will return <b>true</b> when this file instance was restored
     * from the cache and not currently parsed. Otherwise this method will return
     * <b>false</b>.
     *
     * @return bool
     *
     * @since  0.10.0
     */
    public function isCached()
    {
        return $this->cached;
    }

    /**
     * ASTVisitor method for node tree traversal.
     *
     * @return void
     */
    public function accept(ASTVisitor $visitor)
    {
        $visitor->visitCompilationUnit($this);
    }

    /**
     * The magic sleep method will be called by PHP's runtime environment right
     * before it serializes an instance of this class. This method returns an
     * array with those property names that should be serialized.
     *
     * @return array<string>
     *
     * @since  0.10.0
     */
    public function __sleep()
    {
        return array(
            'cache',
            'childNodes',
            'comment',
            'endLine',
            'fileName',
            'startLine',
            'id'
        );
    }

    /**
     * The magic wakeup method will is called by PHP's runtime environment when
     * a serialized instance of this class was unserialized. This implementation
     * of the wakeup method restores the references between all parsed entities
     * in this source file and this file instance.
     *
     * @return void
     *
     * @since  0.10.0
     * @see    ASTCompilationUnit::$childNodes
     */
    public function __wakeup()
    {
        $this->cached = true;

        foreach ($this->childNodes as $childNode) {
            $childNode->setCompilationUnit($this);
        }
    }

    /**
     * Returns the string representation of this class.
     *
     * @return string
     */
    public function __toString()
    {
        return ($this->fileName === null ? '' : $this->fileName);
    }

    /**
     * Reads the source file if required.
     *
     * @return void
     */
    protected function readSource()
    {
        if ($this->source === null && (file_exists($this->fileName) || strpos($this->fileName, 'php://') === 0)) {
            $source = file_get_contents($this->fileName);

            $this->source = str_replace(array("\r\n", "\r"), "\n", $source);

            $this->startLine = 1;
            $this->endLine   = substr_count($this->source, "\n") + 1;
        }
    }
    
    // Deprecated methods
    // @codeCoverageIgnoreStart

    /**
     * This method can be called by the PDepend runtime environment or a
     * utilizing component to free up memory. This methods are required for
     * PHP version < 5.3 where cyclic references can not be resolved
     * automatically by PHP's garbage collector.
     *
     * @return void
     *
     * @since  0.9.12
     * @deprecated Since 0.10.0
     */
    public function free()
    {
        fwrite(STDERR, __METHOD__ . ' is deprecated since version 0.10.0' . PHP_EOL);
    }

    // @codeCoverageIgnoreEnd
}
