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

use PDepend\Source\Tokenizer\Token;
use PDepend\Util\Cache\CacheDriver;
use RuntimeException;
use Stringable;

/**
 * This class provides an interface to a single source file.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class ASTCompilationUnit extends AbstractASTArtifact implements Stringable
{
    /**
     * The internal used cache instance.
     *
     * @since 0.10.0
     */
    protected CacheDriver $cache;

    /** The source file name/path. */
    protected ?string $fileName = null;

    /**
     * List of classes, interfaces and functions that parsed from this file.
     *
     * @var AbstractASTArtifact[]
     * @since 0.10.0
     */
    protected array $childNodes = [];

    /**
     * Was this file instance restored from the cache?
     *
     * @since 0.10.0
     */
    protected bool $cached = false;

    /** Normalized code in this file. */
    private ?string $source = null;

    /**
     * Constructs a new source file instance.
     *
     * @param string|null $fileName The source file name/path.
     */
    public function __construct(?string $fileName)
    {
        if ($fileName && str_starts_with($fileName, 'php://')) {
            $this->fileName = $fileName;
        } elseif ($fileName !== null) {
            $this->fileName = realpath($fileName) ?: null;
        }
    }

    /**
     * The magic sleep method will be called by PHP's runtime environment right
     * before it serializes an instance of this class. This method returns an
     * array with those property names that should be serialized.
     *
     * @return list<string>
     * @since  0.10.0
     */
    public function __sleep(): array
    {
        return [
            'cache',
            'childNodes',
            'comment',
            'endLine',
            'fileName',
            'startLine',
            'id',
        ];
    }

    /**
     * The magic wakeup method will is called by PHP's runtime environment when
     * a serialized instance of this class was unserialized. This implementation
     * of the wakeup method restores the references between all parsed entities
     * in this source file and this file instance.
     *
     * @see    ASTCompilationUnit::$childNodes
     * @since  0.10.0
     */
    public function __wakeup(): void
    {
        $this->cached = true;

        foreach ($this->childNodes as $childNode) {
            $childNode->setCompilationUnit($this);
        }
    }

    /**
     * Returns the string representation of this class.
     */
    public function __toString(): string
    {
        return $this->fileName ?? '';
    }

    /**
     * Returns the physical file name for this object.
     */
    public function getImage(): string
    {
        return $this->fileName ?? '';
    }

    /**
     * Returns the physical file name for this object.
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * Returns a id for this code node.
     */
    public function getId(): string
    {
        return $this->id ?? '';
    }

    /**
     * Setter method for the used parser and token cache.
     *
     * @return $this
     * @since  0.10.0
     */
    public function setCache(CacheDriver $cache): self
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Returns normalized source code with stripped whitespaces.
     */
    public function getSource(): ?string
    {
        $this->readSource();

        return $this->source;
    }

    /**
     * Returns an <b>array</b> with all tokens within this file.
     *
     * @return array<int, Token>
     */
    public function getTokens(): array
    {
        /** @var array<int, Token> */
        return (array) $this->cache
            ->type('tokens')
            ->restore($this->getId());
    }

    /**
     * Sets the tokens for this file.
     *
     * @param array<Token> $tokens The generated tokens.
     */
    public function setTokens(array $tokens): void
    {
        $this->cache
            ->type('tokens')
            ->store($this->getId(), $tokens);
    }

    /**
     * Adds a source item that was parsed from this source file.
     *
     * @since  0.10.0
     */
    public function addChild(AbstractASTArtifact $artifact): void
    {
        $this->childNodes[$artifact->getId()] = $artifact;
    }

    public function getChildren(): array
    {
        return $this->childNodes;
    }

    /**
     * Returns the start line number for this source file. For an existing file
     * this value must always be <em>1</em>, while it can be <em>0</em> for a
     * not existing dummy file.
     *
     * @since  0.10.0
     */
    public function getStartLine(): int
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
     * @since  0.10.0
     */
    public function getEndLine(): int
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
     * @since  0.10.0
     */
    public function isCached(): bool
    {
        return $this->cached;
    }

    /**
     * Reads the source file if required.
     *
     * @throws RuntimeException
     */
    protected function readSource(): void
    {
        if (
            $this->source === null &&
            $this->fileName &&
            (str_starts_with($this->fileName, 'php://') || file_exists($this->fileName))
        ) {
            $source = file_get_contents($this->fileName);
            if ($source === false) {
                throw new RuntimeException('File not found ' . $this->fileName);
            }

            $this->source = str_replace(["\r\n", "\r"], "\n", $source);

            $this->startLine = 1;
            $this->endLine = substr_count($this->source, "\n") + 1;
        }
    }
}
