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

use BadMethodCallException;
use InvalidArgumentException;

/**
 * Represents a php class node.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class ASTEnum extends AbstractASTClassOrInterface
{
    /**
     * @param ?ASTScalarType $type ASTScalarType if backed enumeration:
     *                             https://www.php.net/manual/en/language.enumerations.backed.php
     *   - either: ASTScalarType('string')
     *   - or:     ASTScalarType('int')
     * null if basic enumeration: https://www.php.net/manual/en/language.enumerations.basics.php
     */
    public function __construct(
        $name,
        private readonly ?ASTScalarType $type = null,
    ) {
        parent::__construct($name);
    }

    /**
     * The magic wakeup method will be called by PHP's runtime environment when
     * a serialized instance of this class was unserialized. This implementation
     * of the wakeup method will register this object in the the global class
     * context.
     *
     * @since  0.10.0
     */
    public function __wakeup(): void
    {
        parent::__wakeup();

        $this->context->registerEnum($this);
    }

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
        return ['type', ...parent::__sleep()];
    }

    /**
     * Returns ASTScalarType if backed enumeration: https://www.php.net/manual/en/language.enumerations.backed.php
     *   - either: ASTScalarType('string')
     *   - or:     ASTScalarType('int')
     * Returns null if basic enumeration: https://www.php.net/manual/en/language.enumerations.basics.php
     */
    public function getType(): ?ASTScalarType
    {
        return $this->type;
    }

    /**
     * Returns <b>true</b> if backed enumeration: https://www.php.net/manual/en/language.enumerations.backed.php
     * Returns <b>false</b> if basic enumeration: https://www.php.net/manual/en/language.enumerations.basics.php
     */
    public function isBacked(): bool
    {
        return $this->type !== null;
    }

    /**
     * Returns <b>true</b> if this is an abstract class or an interface.
     */
    public function isAbstract(): bool
    {
        return false;
    }

    /**
     * This method will return <b>true</b> when this class is declared as final.
     */
    public function isFinal(): bool
    {
        return true;
    }

    /**
     * Will return <b>true</b> if this class was declared anonymous in an
     * allocation expression.
     */
    public function isAnonymous(): bool
    {
        return false;
    }

    /**
     * Returns all case for this enum.
     *
     * @return ASTArtifactList<ASTEnumCase>
     */
    public function getCases(): ASTArtifactList
    {
        return new ASTArtifactList(
            $this->findChildrenOfType(ASTEnumCase::class),
        );
    }

    /**
     * Returns empty ASTArtifactList, enum has no properties.
     *
     * @return ASTArtifactList<ASTProperty>
     */
    public function getProperties(): ASTArtifactList
    {
        /** @var ASTProperty[] $list */
        $list = [];

        return new ASTArtifactList($list);
    }

    /**
     * @return ASTArtifactList<AbstractASTClassOrInterface>
     */
    public function getInterfaces(): ASTArtifactList
    {
        $unitEnum = new ASTInterface('UnitEnum');

        $cases = new ASTMethod('cases'); // cases(): array
        $cases->setModifiers(State::IS_STATIC | State::IS_PUBLIC);
        $cases->addChild(new ASTFormalParameters());
        $cases->addChild(new ASTTypeArray());
        $unitEnum->addMethod($cases);

        $interfaces = [$unitEnum];

        if ($this->isBacked()) {
            $backedEnum = new ASTInterface('BackedEnum');

            $methods = [
                'from' => false, // from(int|string $value): static
                'tryFrom' => true, // tryFrom(int|string $value): ?static
            ];

            foreach ($methods as $name => $nullable) {
                $from = new ASTMethod($name);
                $from->setModifiers(State::IS_STATIC | State::IS_PUBLIC);
                $parameters = new ASTFormalParameters();
                $valueParameter = new ASTFormalParameter();
                $type = $this->getType();
                if ($type) {
                    $valueParameter->addChild(clone $type);
                }
                $valueParameter->addChild(new ASTVariableDeclarator('$value'));
                $parameters->addChild($valueParameter);
                $from->addChild($parameters);
                $from->addChild(new ASTStaticReference($this->context, $this));
                $backedEnum->addMethod($from);
            }

            $interfaces[] = $backedEnum;
        }

        return new ASTArtifactList([...$interfaces, ...$this->getInterfacesClasses()]);
    }

    /**
     * Checks that this user type is a subtype of the given <b>$type</b> instance.
     */
    public function isSubtypeOf(AbstractASTType $type): bool
    {
        if ($type === $this) {
            return true;
        }
        if ($type instanceof ASTInterface) {
            foreach ($this->getInterfaces() as $interface) {
                if ($interface === $type) {
                    return true;
                }
            }
        } elseif (($parent = $this->getParentClass()) !== null) {
            if ($parent === $type) {
                return true;
            }

            return $parent->isSubtypeOf($type);
        }

        return false;
    }

    /**
     * Returns the declared modifiers for this type.
     *
     * @since  0.9.4
     */
    public function getModifiers(): int
    {
        return $this->modifiers;
    }

    /**
     * This method sets a OR combined integer of the declared modifiers for this
     * node.
     *
     * This method will throw an exception when the value of given <b>$modifiers</b>
     * contains an invalid/unexpected modifier
     *
     * @throws BadMethodCallException
     * @throws InvalidArgumentException
     * @since  0.9.4
     */
    public function setModifiers(int $modifiers): void
    {
        if ($this->modifiers !== 0) {
            throw new BadMethodCallException(
                'Cannot overwrite previously set class modifiers.',
            );
        }

        $expected = ~State::IS_EXPLICIT_ABSTRACT
                  & ~State::IS_IMPLICIT_ABSTRACT
                  & ~State::IS_FINAL;

        if (($expected & $modifiers) !== 0) {
            throw new InvalidArgumentException('Invalid class modifier given.');
        }

        $this->modifiers = $modifiers;
    }
}
