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

use RuntimeException;
use Stringable;

/**
 * An instance of this class represents a function or method parameter within
 * the analyzed source code.
 *
 * <code>
 * <?php
 * class Builder
 * {
 *     public function buildNode($name, $line, ASTCompilationUnit $unit) {
 *     }
 * }
 *
 * function parse(Builder $builder, $file) {
 * }
 * </code>
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class ASTParameter extends AbstractASTArtifact implements Stringable
{
    /** The parent function or method instance. */
    private AbstractASTCallable $declaringFunction;

    /** The parameter position. */
    private int $position = 0;

    /** Is this parameter optional or mandatory? */
    private bool $optional = false;

    /** The wrapped variable declarator instance. */
    private readonly ASTVariableDeclarator $variableDeclarator;

    /**
     * Constructs a new parameter instance for the given AST node.
     *
     * @param ASTFormalParameter $formalParameter The wrapped formal parameter instance.
     * @throws RuntimeException
     */
    public function __construct(
        private readonly ASTFormalParameter $formalParameter,
    ) {
        $variableDeclarator = $this->formalParameter->getFirstChildOfType(
            ASTVariableDeclarator::class,
        );
        if (!$variableDeclarator) {
            throw new RuntimeException('Missing variable decleration');
        }
        $this->variableDeclarator = $variableDeclarator;

        $this->id = spl_object_hash($this);
    }

    /**
     * This method returns a string representation of this parameter.
     */
    public function __toString(): string
    {
        $required = $this->isOptional() ? 'optional' : 'required';
        $reference = $this->isPassedByReference() ? '&' : '';

        $typeHint = '';
        if ($this->isArray()) {
            $typeHint = ' array';
        } elseif ($class = $this->getClass()) {
            $typeHint = ' ' . $class->getImage();
        }

        $default = '';
        if ($this->isDefaultValueAvailable()) {
            $default = ' = ';

            $value = $this->getDefaultValue();
            if ($value === null) {
                $default .= 'NULL';
                $typeHint .= ($typeHint !== '' ? ' or NULL' : '');
            } elseif (is_bool($value)) {
                $default .= $value ? 'true' : 'false';
            } elseif (is_array($value)) {
                $default .= 'Array';
            } elseif (is_string($value)) {
                $default .= "'" . $value . "'";
            } elseif ($value instanceof Stringable || is_int($value) || is_float($value)) {
                $default .= $value;
            }
        }

        return sprintf(
            'Parameter #%d [ <%s>%s %s%s%s ]',
            $this->position,
            $required,
            $typeHint,
            $reference,
            $this->getImage(),
            $default,
        );
    }

    /**
     * Returns the item name.
     */
    public function getImage(): string
    {
        return $this->variableDeclarator->getImage();
    }

    /**
     * Returns the line number where the item declaration can be found.
     */
    public function getStartLine(): int
    {
        return $this->formalParameter->getStartLine();
    }

    /**
     * Returns the line number where the item declaration ends.
     *
     * @return int The last source line for this item.
     */
    public function getEndLine(): int
    {
        return $this->formalParameter->getEndLine();
    }

    /**
     * Returns the parent function or method instance or <b>null</b>
     *
     * @since  0.9.5
     */
    public function getDeclaringFunction(): ?AbstractASTCallable
    {
        return $this->declaringFunction;
    }

    /**
     * Sets the parent function or method object.
     *
     * @since  0.9.5
     */
    public function setDeclaringFunction(AbstractASTCallable $function): void
    {
        $this->declaringFunction = $function;
    }

    /**
     * This method will return the class where the parent method was declared.
     * The returned value will be <b>null</b> if the parent is a function.
     *
     * @since  0.9.5
     * @todo Review this for refactoring, maybe create a empty getParent()?
     */
    public function getDeclaringClass(): ?AbstractASTClassOrInterface
    {
        if ($this->declaringFunction instanceof ASTMethod) {
            return $this->declaringFunction->getParent();
        }

        return null;
    }

    /**
     * Returns the parameter position in the method/function signature.
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Sets the parameter position in the method/function signature.
     *
     * @param int $position The parameter position.
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * Returns the class type of this parameter. This method will return
     * <b>null</b> for all scalar type, only classes or interfaces are used.
     *
     * @since  0.9.5
     */
    public function getClass(): ?AbstractASTClassOrInterface
    {
        $classReference = $this->formalParameter->getFirstChildOfType(
            ASTClassOrInterfaceReference::class,
        );
        if ($classReference === null) {
            return null;
        }

        return $classReference->getType();
    }

    /**
     * This method will return <b>true</b> when the parameter is passed by
     * reference.
     *
     * @since  0.9.5
     */
    public function isPassedByReference(): bool
    {
        return $this->formalParameter->isPassedByReference();
    }

    /**
     * This method will return <b>true</b> when the parameter was declared with
     * the array type hint, otherwise the it will return <b>false</b>.
     *
     * @since  0.9.5
     */
    public function isArray(): bool
    {
        $node = $this->formalParameter->getChild(0);

        return ($node instanceof ASTTypeArray);
    }

    /**
     * This method will return <b>true</b> when current parameter is a simple
     * scalar or it is an <b>array</b> or type explicit declared with a default
     * value <b>null</b>.
     *
     * @since  0.9.5
     */
    public function allowsNull(): bool
    {
        $node = $this->formalParameter->getChild(0);

        if ($this->isTypeAllowingNull($node)) {
            return true;
        }

        if (
            !($node instanceof ASTTypeArray)
            && !($node instanceof ASTScalarType)
            && $this->getClass() === null
        ) {
            return true;
        }

        if ($this->isDefaultValueAvailable() && $this->getDefaultValue() === null) {
            return true;
        }

        return false;
    }

    /**
     * This method will return <b>true</b> when this parameter is optional and
     * can be left blank on invocation.
     *
     * @since  0.9.5
     */
    public function isOptional(): bool
    {
        return $this->optional;
    }

    /**
     * This method can be used to mark a parameter optional. Note that a
     * parameter is only optional when it has a default value an no following
     * parameter has no default value.
     *
     * @param bool $optional Boolean flag that marks this parameter a
     *                       optional or not.
     * @since  0.9.5
     */
    public function setOptional(bool $optional): void
    {
        $this->optional = (bool) $optional;
    }

    /**
     * This method will return <b>true</b> when the parameter declaration
     * contains a default value.
     *
     * @since  0.9.5
     */
    public function isDefaultValueAvailable(): bool
    {
        $value = $this->variableDeclarator->getValue();
        if ($value === null) {
            return false;
        }

        return $value->isValueAvailable();
    }

    /**
     * This method will return the declared default value for this parameter.
     * Please note that this method will return <b>null</b> when no default
     * value was declared, therefore you should combine calls to this method and
     * {@link ASTParameter::isDefaultValueAvailable()} to
     * detect a NULL-value.
     *
     * @since  0.9.5
     */
    public function getDefaultValue(): mixed
    {
        $value = $this->variableDeclarator->getValue();

        return $value === null ? null : $value->getValue();
    }

    /**
     * Returns the wrapped formal parameter instance.
     */
    public function getFormalParameter(): ASTFormalParameter
    {
        return $this->formalParameter;
    }

    private function isTypeAllowingNull(ASTNode $node): bool
    {
        if ($node instanceof ASTUnionType) {
            foreach ($node->getChildren() as $child) {
                if ($this->isTypeAllowingNull($child)) {
                    return true;
                }
            }
        }

        return $node instanceof ASTScalarType && $node->isNull();
    }
}
