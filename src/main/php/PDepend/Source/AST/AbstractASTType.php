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
 * @since 1.0.0
 */

namespace PDepend\Source\AST;

use InvalidArgumentException;
use OutOfBoundsException;
use PDepend\Source\AST\ASTTraitUseStatement;
use PDepend\Source\Builder\BuilderContext;
use PDepend\Source\Tokenizer\Token;
use PDepend\Util\Cache\CacheDriver;

/**
 * Represents any valid complex php type.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @since 1.0.0
 */
abstract class AbstractASTType extends AbstractASTArtifact
{
    /**
     * The internal used cache instance.
     *
     * @var CacheDriver|null
     */
    protected $cache = null;

    /**
     * The currently used builder context.
     *
     * @var BuilderContext|null
     */
    protected $context = null;

    /**
     * The parent namespace for this class.
     *
     * @var ASTNamespace|null
     */
    private $namespace = null;

    /**
     * An <b>array</b> with all constants defined in this class or interface.
     *
     * @var array<string, mixed>|null
     */
    protected $constants = null;

    /**
     * This property will indicate that the class or interface is user defined.
     * The parser marks all classes and interfaces as user defined that have a
     * source file and were part of parsing process.
     *
     * @var bool
     */
    protected $userDefined = false;

    /**
     * List of all parsed child nodes.
     *
     * @var ASTNode[]
     */
    protected $nodes = array();

    /**
     * Name of the parent namespace for this class or interface instance. Or
     * <b>NULL</b> when no namespace was specified.
     *
     * @var string|null
     */
    protected $namespaceName = null;

    /**
     * The modifiers for this class instance.
     *
     * @var int
     */
    protected $modifiers = 0;

    /**
     * Temporary property that only holds methods during the parsing process.
     *
     * @var ASTMethod[]|null
     *
     * @since 1.0.2
     */
    protected $methods = array();


    /**
     * Setter method for the currently used token cache, where this class or
     * interface instance can store the associated tokens.
     *
     * @return $this
     */
    public function setCache(CacheDriver $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * Sets the currently active builder context.
     *
     * @return $this
     */
    public function setContext(BuilderContext $context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Adds a parsed child node to this node.
     *
     * @return void
     * @access private
     */
    public function addChild(ASTNode $node)
    {
        $this->nodes[] = $node;
    }

    /**
     * Returns the child at the given index.
     *
     * @param int $index
     *
     * @throws OutOfBoundsException
     *
     * @return ASTNode
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
     * Returns all child nodes of this class.
     *
     * @return ASTNode[]
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
     * @template T of ASTNode
     *
     * @param class-string<T> $targetType Searched class or interface type.
     *
     * @return T|null
     * @access private
     *
     * @todo   Refactor $_methods property to getAllMethods() when it exists.
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
        $methods = $this->getMethods();
        foreach ($methods as $method) {
            if (($child = $method->getFirstChildOfType($targetType)) !== null) {
                return $child;
            }
        }

        return null;
    }

    /**
     * Will find all children for the given type.
     *
     * @template T of ASTNode
     * @template R of T
     *
     * @param class-string<T> $targetType The target class or interface type.
     * @param R[]             $results    The found children.
     *
     * @return T[]
     * @access private
     *
     * @todo   Refactor $_methods property to getAllMethods() when it exists.
     */
    public function findChildrenOfType($targetType, array &$results = array())
    {
        foreach ($this->nodes as $node) {
            if ($node instanceof $targetType) {
                $results[] = $node;
            }
            $node->findChildrenOfType($targetType, $results);
        }

        foreach ($this->getMethods() as $method) {
            $method->findChildrenOfType($targetType, $results);
        }

        return $results;
    }

    /**
     * This method will return <b>true</b> when this type has a declaration in
     * the analyzed source files.
     *
     * @return bool
     */
    public function isUserDefined()
    {
        return $this->userDefined;
    }

    /**
     * This method can be used to mark a type as user defined. User defined
     * means that the type has a valid declaration in the analyzed source files.
     *
     * @return void
     */
    public function setUserDefined()
    {
        $this->userDefined = true;
    }

    /**
     * Returns all {@link ASTMethod} objects in this type.
     *
     * @return ASTArtifactList<ASTMethod>
     */
    public function getMethods()
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
     *
     * @return ASTMethod
     */
    public function addMethod(ASTMethod $method)
    {
        if ($this instanceof AbstractASTClassOrInterface) {
            $method->setParent($this);
        }

        $this->methods[] = $method;

        return $method;
    }

    /**
     * Returns an array with {@link ASTMethod} objects
     * that are imported through traits.
     *
     * @return ASTMethod[]
     *
     * @since  1.0.0
     */
    protected function getTraitMethods()
    {
        $methods = array();

        /** @var ASTTraitUseStatement[] */
        $uses = $this->findChildrenOfType(
            'PDepend\\Source\\AST\\ASTTraitUseStatement'
        );

        foreach ($uses as $use) {
            $priorMethods = array();
            $precedences = $use->findChildrenOfType('PDepend\\Source\\AST\\ASTTraitAdaptationPrecedence');

            foreach ($precedences as $precedence) {
                $priorMethods[strtolower($precedence->getImage())] = true;
            }
            foreach ($use->getAllMethods() as $method) {
                foreach ($uses as $use2) {
                    if ($use2->hasExcludeFor($method)) {
                        continue 2;
                    }
                }

                $name = strtolower($method->getName());

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

    /**
     * Returns an <b>array</b> with all tokens within this type.
     *
     * @return Token[]
     */
    public function getTokens()
    {
        return (array) $this->cache
            ->type('tokens')
            ->restore($this->getId());
    }

    /**
     * Sets the tokens for this type.
     *
     * @param Token[] $tokens
     *
     * @return void
     */
    public function setTokens(array $tokens, Token $startToken = null)
    {
        if ($tokens === array()) {
            throw new InvalidArgumentException('An AST node should contain at least one token');
        }

        if (!$startToken) {
            $startToken = reset($tokens);
        }

        $this->startLine = $startToken->startLine;
        $this->endLine   = end($tokens)->endLine;

        $this->cache
            ->type('tokens')
            ->store($this->getId(), $tokens);
    }

    /**
     * @return string
     */
    public function getNamespacedName()
    {
        if (null === $this->namespace || $this->namespace->isPackageAnnotation()) {
            return $this->name;
        }
        return sprintf('%s\\%s', $this->namespaceName, $this->name);
    }

    /**
     * Returns the name of the parent namespace.
     *
     * @return string
     */
    public function getNamespaceName()
    {
        return $this->namespaceName;
    }

    /**
     * Returns the parent namespace for this class.
     *
     * @return ASTNamespace
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Sets the parent namespace for this type.
     *
     * @return void
     */
    public function setNamespace(ASTNamespace $namespace)
    {
        $this->namespace = $namespace;
        $this->namespaceName = $namespace->getName();
    }

    /**
     * Resets the associated namespace reference.
     *
     * @return void
     */
    public function unsetNamespace()
    {
        $this->namespace = null;
        $this->namespaceName = null;
    }

    /**
     * This method will return <b>true</b> when this class or interface instance
     * was restored from the cache and not currently parsed. Otherwise this
     * method will return <b>false</b>.
     *
     * @return bool
     */
    public function isCached()
    {
        return $this->compilationUnit->isCached();
    }

    /**
     * Returns a list of all methods provided by this type or one of its parents.
     *
     * @return ASTMethod[]
     */
    abstract public function getAllMethods();

    /**
     * Checks that this user type is a subtype of the given <b>$type</b>
     * instance.
     *
     * @return bool
     *
     * @since  1.0.6
     */
    abstract public function isSubtypeOf(AbstractASTType $type);

    /**
     * The magic sleep method is called by the PHP runtime environment before an
     * instance of this class gets serialized. It returns an array with the
     * names of all those properties that should be cached for this class or
     * interface instance.
     *
     * @return array
     */
    public function __sleep()
    {
        if (is_array($this->methods)) {
            $this->cache
                ->type('methods')
                ->store($this->getId(), $this->methods);

            $this->methods = null;
        }

        return array(
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
            'id'
        );
    }

    /**
     * The magic wakeup method is called by the PHP runtime environment when a
     * serialized instance of this class gets unserialized and all properties
     * are restored. This implementation of the <b>__wakeup()</b> method sets
     * a flag that this object was restored from the cache and it restores the
     * dependency between this class or interface and it's child methods.
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->methods = null;
    }
}
