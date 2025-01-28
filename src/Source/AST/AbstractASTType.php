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
 * @since 1.0.0
 */

namespace PDepend\Source\AST;

use InvalidArgumentException;
use PDepend\Source\Builder\BuilderContext;
use PDepend\Source\Tokenizer\Token;
use PDepend\Util\Cache\CacheDriver;

/**
 * Represents any valid complex php type.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 1.0.0
 */
abstract class AbstractASTType extends AbstractASTArtifact
{
    /** The internal used cache instance. */
    protected CacheDriver $cache;

    /** The currently used builder context. */
    protected BuilderContext $context;

    /**
     * This property will indicate that the class or interface is user defined.
     * The parser marks all classes and interfaces as user defined that have a
     * source file and were part of parsing process.
     */
    protected bool $userDefined = false;

    /**
     * List of all parsed child nodes.
     *
     * @var ASTNode[]
     */
    protected array $nodes = [];

    /**
     * Name of the parent namespace for this class or interface instance. Or
     * <b>NULL</b> when no namespace was specified.
     */
    protected ?string $namespaceName = null;

    /** The modifiers for this class instance. */
    protected int $modifiers = 0;

    /**
     * Temporary property that only holds methods during the parsing process.
     *
     * @var ASTMethod[]|null
     * @since 1.0.2
     */
    protected ?array $methods = [];

    /** The parent namespace for this class. */
    private ?ASTNamespace $namespace = null;

    /**
     * The magic sleep method is called by the PHP runtime environment before an
     * instance of this class gets serialized. It returns an array with the
     * names of all those properties that should be cached for this class or
     * interface instance.
     *
     * @return list<string>
     */
    public function __sleep(): array
    {
        if (is_array($this->methods)) {
            $this->cache
                ->type('methods')
                ->store($this->getId(), $this->methods);

            $this->methods = null;
        }

        return [
            'cache',
            'context',
            'comment',
            'endLine',
            'modifiers',
            'name',
            'nodes',
            'namespaceName',
            'startLine',
            'userDefined',
            'id',
        ];
    }

    /**
     * The magic wakeup method is called by the PHP runtime environment when a
     * serialized instance of this class gets unserialized and all properties
     * are restored. This implementation of the <b>__wakeup()</b> method sets
     * a flag that this object was restored from the cache and it restores the
     * dependency between this class or interface and it's child methods.
     */
    public function __wakeup(): void
    {
        $this->methods = null;

        foreach ($this->nodes as $node) {
            $node->setParent($this);
        }
    }

    /**
     * Setter method for the currently used token cache, where this class or
     * interface instance can store the associated tokens.
     *
     * @return $this
     */
    public function setCache(CacheDriver $cache): self
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Sets the currently active builder context.
     *
     * @return $this
     */
    public function setContext(BuilderContext $context): self
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Adds a parsed child node to this node.
     */
    public function addChild(ASTNode $node): void
    {
        $this->nodes[] = $node;
        $node->setParent($this);
    }

    /**
     * Returns all child nodes of this class.
     *
     * @return ASTNode[]
     */
    public function getChildren(): array
    {
        return $this->nodes;
    }

    /**
     * This method will return <b>true</b> when this type has a declaration in
     * the analyzed source files.
     */
    public function isUserDefined(): bool
    {
        return $this->userDefined;
    }

    /**
     * This method can be used to mark a type as user defined. User defined
     * means that the type has a valid declaration in the analyzed source files.
     */
    public function setUserDefined(): void
    {
        $this->userDefined = true;
    }

    /**
     * Returns all {@link ASTMethod} objects in this type.
     *
     * @return ASTArtifactList<ASTMethod>
     */
    public function getMethods(): ASTArtifactList
    {
        if (is_array($this->methods)) {
            return new ASTArtifactList($this->methods);
        }

        /** @var ASTMethod[] */
        $methods = (array) $this->cache
            ->type('methods')
            ->restore($this->getId());

        if ($this instanceof AbstractASTClassOrInterface) {
            foreach ($methods as $method) {
                $method->compilationUnit = $this->compilationUnit;
                $method->setParent($this);
            }
        }

        return new ASTArtifactList($methods);
    }

    /**
     * Adds the given method to this type.
     */
    public function addMethod(ASTMethod $method): ASTMethod
    {
        if ($this instanceof AbstractASTClassOrInterface) {
            $method->setParent($this);
        }

        $this->methods[] = $method;

        return $method;
    }

    /**
     * Returns an <b>array</b> with all tokens within this type.
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
     * Sets the tokens for this type.
     *
     * @param Token[] $tokens
     * @throws InvalidArgumentException
     */
    public function setTokens(array $tokens, ?Token $startToken = null): void
    {
        if ($tokens === []) {
            throw new InvalidArgumentException('An AST node should contain at least one token');
        }

        if (!$startToken) {
            $startToken = reset($tokens);
        }

        $this->startLine = $startToken->startLine;
        $this->startColumn = $startToken->startColumn;
        $this->endLine = end($tokens)->endLine;
        $this->endColumn = end($tokens)->endColumn;

        $this->cache
            ->type('tokens')
            ->store($this->getId(), $tokens);
    }

    public function getNamespacedName(): string
    {
        if (null === $this->namespace || $this->namespace->isPackageAnnotation()) {
            return $this->name;
        }

        return sprintf('%s\\%s', $this->namespaceName, $this->name);
    }

    /**
     * Returns the name of the parent namespace.
     */
    public function getNamespaceName(): ?string
    {
        return $this->namespaceName;
    }

    /**
     * Returns the parent namespace for this class.
     */
    public function getNamespace(): ?ASTNamespace
    {
        return $this->namespace;
    }

    /**
     * Sets the parent namespace for this type.
     */
    public function setNamespace(ASTNamespace $namespace): void
    {
        $this->namespace = $namespace;
        $this->namespaceName = $namespace->getImage();
    }

    /**
     * Resets the associated namespace reference.
     */
    public function unsetNamespace(): void
    {
        $this->namespace = null;
        $this->namespaceName = null;
    }

    /**
     * This method will return <b>true</b> when this class or interface instance
     * was restored from the cache and not currently parsed. Otherwise this
     * method will return <b>false</b>.
     */
    public function isCached(): bool
    {
        return $this->compilationUnit?->isCached() ?? false;
    }

    /**
     * Returns a list of all methods provided by this type or one of its parents.
     *
     * @return ASTMethod[]
     */
    abstract public function getAllMethods(): array;

    /**
     * Checks that this user type is a subtype of the given <b>$type</b>
     * instance.
     *
     * @since  1.0.6
     */
    abstract public function isSubtypeOf(self $type): bool;

    /**
     * Returns an array with {@link ASTMethod} objects
     * that are imported through traits.
     *
     * @return ASTMethod[]
     * @throws ASTTraitMethodCollisionException
     * @since  1.0.0
     */
    protected function getTraitMethods(): array
    {
        $methods = [];

        /** @var ASTTraitUseStatement[] */
        $uses = $this->findChildrenOfType(
            ASTTraitUseStatement::class,
        );

        foreach ($uses as $use) {
            $priorMethods = [];
            $precedences = $use->findChildrenOfType(ASTTraitAdaptationPrecedence::class);

            foreach ($precedences as $precedence) {
                $priorMethods[strtolower($precedence->getImage())] = true;
            }
            foreach ($use->getAllMethods() as $method) {
                foreach ($uses as $use2) {
                    if ($use2->hasExcludeFor($method)) {
                        continue 2;
                    }
                }

                $name = strtolower($method->getImage());

                if (!isset($methods[$name]) || isset($priorMethods[$name])) {
                    $methods[$name] = $method;

                    continue;
                }

                if ($methods[$name]->isAbstract()) {
                    $methods[$name] = $method;

                    continue;
                }

                if ($method->isAbstract()) {
                    continue;
                }

                throw new ASTTraitMethodCollisionException($method, $this);
            }
        }

        return $methods;
    }
}
