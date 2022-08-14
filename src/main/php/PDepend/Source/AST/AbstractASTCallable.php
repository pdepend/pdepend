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

use PDepend\Source\AST\AbstractASTNode;
use PDepend\Source\AST\ASTVariableDeclarator;
use PDepend\Source\Tokenizer\Token;
use PDepend\Util\Cache\CacheDriver;

/**
 * Abstract base class for callable objects.
 *
 * Callable objects is a generic parent for methods and functions.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
abstract class AbstractASTCallable extends AbstractASTArtifact implements ASTCallable
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
     * A reference instance for the return value of this callable. By
     * default and for any scalar type this property is <b>null</b>.
     *
     * @var ASTClassOrInterfaceReference|null
     *
     * @since 0.9.5
     */
    protected $returnClassReference = null;

    /**
     * List of all exceptions classes referenced by this callable.
     *
     * @var ASTClassOrInterfaceReference[]
     *
     * @since 0.9.5
     */
    protected $exceptionClassReferences = array();

    /**
     * Does this callable return a value by reference?
     *
     * @var bool
     */
    protected $returnsReference = false;

    /**
     * List of all parsed child nodes.
     *
     * @var AbstractASTNode[]
     *
     * @since 0.9.6
     */
    protected $nodes = array();

    /**
     * The start line number of the method or function declaration.
     *
     * @var int
     *
     * @since 0.9.12
     */
    protected $startLine = 0;

    /**
     * The end line number of the method or function declaration.
     *
     * @var int
     *
     * @since 0.9.12
     */
    protected $endLine = 0;

    /**
     * List of method/function parameters.
     *
     * @var ASTParameter[]|null
     */
    private $parameters = null;

    /**
     * Setter method for the currently used token cache, where this callable
     * instance can store the associated tokens.
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
     * Adds a parsed child node to this node.
     *
     * @param AbstractASTNode $node A parsed child node instance.
     *
     * @return void
     * @access private
     *
     * @since  0.9.6
     */
    public function addChild(ASTNode $node)
    {
        $this->nodes[] = $node;
    }

    /**
     * Returns all child nodes of this method.
     *
     * @return AbstractASTNode[]
     *
     * @since  0.9.8
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
     * @since  0.9.6
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
     * Will find all children for the given type.
     *
     * @template T of ASTNode
     *
     * @param class-string<T> $targetType Searched class or interface type.
     * @param T[]             $results    The found children.
     *
     * @return T[]
     * @access private
     *
     * @since  0.9.6
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
     * Returns the tokens found in the function body.
     *
     * @return array<mixed>
     */
    public function getTokens()
    {
        return (array) $this->cache
            ->type('tokens')
            ->restore($this->id);
    }

    /**
     * Sets the tokens found in the function body.
     *
     * @param Token[] $tokens The body tokens.
     *
     * @return void
     */
    public function setTokens(array $tokens)
    {
        $this->startLine = reset($tokens)->startLine;
        $this->endLine   = end($tokens)->endLine;

        $this->cache
            ->type('tokens')
            ->store($this->id, $tokens);
    }

    /**
     * Returns the line number where the callable declaration starts.
     *
     * @return int
     *
     * @since  0.9.6
     */
    public function getStartLine()
    {
        return $this->startLine;
    }

    /**
     * Returns the line number where the callable declaration ends.
     *
     * @return int
     *
     * @since  0.9.6
     */
    public function getEndLine()
    {
        return $this->endLine;
    }

    /**
     * Returns all {@link AbstractASTClassOrInterface}
     * objects this function depends on.
     *
     * @return ASTClassOrInterfaceReferenceIterator
     */
    public function getDependencies()
    {
        return new ASTClassOrInterfaceReferenceIterator(
            $this->findChildrenOfType(
                'PDepend\\Source\\AST\\ASTClassOrInterfaceReference'
            )
        );
    }

    /**
     * This method will return a class or interface instance that represents
     * the return value of this callable. The returned value will be <b>null</b>
     * if there is no return value or the return value is scalat.
     *
     * @return AbstractASTClassOrInterface|null
     *
     * @since  0.9.5
     */
    public function getReturnClass()
    {
        if ($this->returnClassReference) {
            return $this->returnClassReference->getType();
        }
        if (($node = $this->getReturnType()) instanceof ASTClassOrInterfaceReference) {
            return $node->getType();
        }
        return null;
    }

    /**
     * Tests if this callable has a return class and return <b>true</b> if it is
     * configured.
     *
     * @return bool
     *
     * @since 2.2.4
     */
    public function hasReturnClass()
    {
        if ($this->returnClassReference) {
            return true;
        }
        if (($node = $this->getReturnType()) instanceof ASTClassOrInterfaceReference) {
            return true;
        }
        return false;
    }

    /**
     * @return ASTType|null
     */
    public function getReturnType()
    {
        foreach ($this->nodes as $node) {
            if ($node instanceof ASTType) {
                return $node;
            }
        }
        return null;
    }

    /**
     * This method can be used to set a reference instance for the declared
     * function return type.
     *
     * @param ASTClassOrInterfaceReference $classReference Holder instance for the declared function return type.
     *
     * @return void
     *
     * @since  0.9.5
     */
    public function setReturnClassReference(ASTClassOrInterfaceReference $classReference)
    {
        $this->returnClassReference = $classReference;
    }

    /**
     * Adds a reference holder for a thrown exception class or interface to
     * this callable.
     *
     * @param ASTClassOrInterfaceReference $classReference A reference instance for a thrown exception.
     *
     * @return void
     *
     * @since  0.9.5
     */
    public function addExceptionClassReference(
        ASTClassOrInterfaceReference $classReference
    ) {
        $this->exceptionClassReferences[] = $classReference;
    }

    /**
     * Returns an iterator with thrown exception
     * {@link AbstractASTClassOrInterface} instances.
     *
     * @return ASTClassOrInterfaceReferenceIterator
     */
    public function getExceptionClasses()
    {
        return new ASTClassOrInterfaceReferenceIterator(
            $this->exceptionClassReferences
        );
    }

    /**
     * Returns an array with all method/function parameters.
     *
     * @return ASTParameter[]|null
     */
    public function getParameters()
    {
        if ($this->parameters === null) {
            $this->initParameters();
        }
        return $this->parameters;
    }

    /**
     * This method will return <b>true</b> when this method returns a value by
     * reference, otherwise the return value will be <b>false</b>.
     *
     * @return bool
     *
     * @since  0.9.5
     */
    public function returnsReference()
    {
        return $this->returnsReference;
    }

    /**
     * A call to this method will flag the callable instance with the returns
     * reference flag, which means that the context function or method returns
     * a value by reference.
     *
     * @return void
     *
     * @since  0.9.5
     */
    public function setReturnsReference()
    {
        $this->returnsReference = true;
    }

    /**
     * Returns an array with all declared static variables.
     *
     * @return array<string, mixed>
     *
     * @since  0.9.6
     */
    public function getStaticVariables()
    {
        $staticVariables = array();

        $declarations = $this->findChildrenOfType(
            'PDepend\\Source\\AST\\ASTStaticVariableDeclaration'
        );
        foreach ($declarations as $declaration) {
            $variables = $declaration->findChildrenOfType(
                'PDepend\\Source\\AST\\ASTVariableDeclarator'
            );
            foreach ($variables as $variable) {
                $image = $variable->getImage();
                $value = $variable->getValue();
                if ($value !== null) {
                    $value = $value->getValue();
                }

                $staticVariables[substr($image, 1)] = $value;
            }
        }
        return $staticVariables;
    }

    /**
     * This method will return <b>true</b> when this callable instance was
     * restored from the cache and not currently parsed. Otherwise this method
     * will return <b>false</b>.
     *
     * @return bool
     *
     * @since  0.10.0
     */
    public function isCached()
    {
        return $this->compilationUnit->isCached();
    }

    /**
     * This method will initialize the <b>$_parameters</b> property.
     *
     * @return void
     *
     * @since  0.9.6
     */
    private function initParameters()
    {
        $parameters = array();

        $formalParameters = $this->getFirstChildOfType(
            'PDepend\\Source\\AST\\ASTFormalParameters'
        );

        $formalParameters = $formalParameters->findChildrenOfType(
            'PDepend\\Source\\AST\\ASTFormalParameter'
        );

        foreach ($formalParameters as $formalParameter) {
            $parameter = new ASTParameter($formalParameter);
            $parameter->setDeclaringFunction($this);
            $parameter->setPosition(count($parameters));

            $parameters[] = $parameter;
        }

        $optional = true;
        foreach (array_reverse($parameters) as $parameter) {
            if ($parameter->isDefaultValueAvailable() === false) {
                $optional = false;
            }
            $parameter->setOptional($optional);
        }

        $this->parameters = $parameters;
    }

    /**
     * The magic sleep method will be called by the PHP engine when this class
     * gets serialized. It returns an array with those properties that should be
     * cached for all callable instances.
     *
     * @return array
     *
     * @since  0.10.0
     */
    public function __sleep()
    {
        return array(
            'cache',
            'id',
            'name',
            'nodes',
            'startLine',
            'endLine',
            'comment',
            'returnsReference',
            'returnClassReference',
            'exceptionClassReferences'
        );
    }

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
     */
    public function free()
    {
        trigger_error(__METHOD__ . '() is deprecated.', E_USER_DEPRECATED);
    }

    // @codeCoverageIgnoreEnd
}
