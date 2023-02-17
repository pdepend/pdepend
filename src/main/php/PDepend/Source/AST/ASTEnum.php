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
use PDepend\Source\ASTVisitor\ASTVisitor;

/**
 * Represents a php class node.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class ASTEnum extends AbstractASTClassOrInterface
{
    /**
     * ASTScalarType if backed enumeration: https://www.php.net/manual/en/language.enumerations.backed.php
     *   - either: ASTScalarType('string')
     *   - or:     ASTScalarType('int')
     * null if basic enumeration: https://www.php.net/manual/en/language.enumerations.basics.php
     *
     * @var ASTScalarType
     */
    private $type;

    public function __construct($name, ASTScalarType $type = null)
    {
        parent::__construct($name);

        $this->type = $type;
    }

    /**
     * Returns ASTScalarType if backed enumeration: https://www.php.net/manual/en/language.enumerations.backed.php
     *   - either: ASTScalarType('string')
     *   - or:     ASTScalarType('int')
     * Returns null if basic enumeration: https://www.php.net/manual/en/language.enumerations.basics.php
     *
     * @return ASTScalarType|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns <b>true</b> if backed enumeration: https://www.php.net/manual/en/language.enumerations.backed.php
     * Returns <b>false</b> if basic enumeration: https://www.php.net/manual/en/language.enumerations.basics.php
     *
     * @return bool
     */
    public function isBacked()
    {
        return $this->type !== null;
    }

    /**
     * Returns <b>true</b> if this is an abstract class or an interface.
     *
     * @return bool
     */
    public function isAbstract()
    {
        return false;
    }

    /**
     * This method will return <b>true</b> when this class is declared as final.
     *
     * @return bool
     */
    public function isFinal()
    {
        return true;
    }

    /**
     * Will return <b>true</b> if this class was declared anonymous in an
     * allocation expression.
     *
     * @return bool
     */
    public function isAnonymous()
    {
        return false;
    }

    /**
     * Returns all case for this enum.
     *
     * @return ASTArtifactList<ASTEnumCase>
     */
    public function getCases()
    {
        return new ASTArtifactList(
            $this->findChildrenOfType('PDepend\\Source\\AST\\ASTEnumCase')
        );
    }

    /**
     * Returns empty ASTArtifactList, enum has no properties.
     *
     * @return ASTArtifactList<ASTProperty>
     */
    public function getProperties()
    {
        /** @var ASTProperty[] $list */
        $list = array();

        return new ASTArtifactList($list);
    }

    /**
     * @return ASTArtifactList<AbstractASTClassOrInterface>
     */
    public function getInterfaces()
    {
        $unitEnum = new ASTInterface('UnitEnum');

        $cases = new ASTMethod('cases'); // cases(): array
        $cases->setModifiers(State::IS_STATIC | State::IS_PUBLIC);
        $cases->addChild(new ASTFormalParameters());
        $cases->addChild(new ASTTypeArray());
        $unitEnum->addMethod($cases);

        $interfaces = array($unitEnum);

        if ($this->isBacked()) {
            $backedEnum = new ASTInterface('BackedEnum');

            $methods = array(
                'from' => false, // from(int|string $value): static
                'tryFrom' => true, // tryFrom(int|string $value): ?static
            );

            foreach ($methods as $name => $nullable) {
                $from = new ASTMethod($name);
                $from->setModifiers(State::IS_STATIC | State::IS_PUBLIC);
                $parameters = new ASTFormalParameters();
                $valueParameter = new ASTFormalParameter();
                $valueParameter->addChild(clone $this->getType());
                $valueParameter->addChild(new ASTVariableDeclarator('$value'));
                $parameters->addChild($valueParameter);
                $from->addChild($parameters);
                $from->addChild(new ASTStaticReference($this->context, $this));
                $backedEnum->addMethod($from);
            }

            $interfaces[] = $backedEnum;
        }

        return new ASTArtifactList(array_merge($interfaces, $this->getInterfacesClasses()));
    }

    /**
     * Checks that this user type is a subtype of the given <b>$type</b> instance.
     *
     * @return bool
     */
    public function isSubtypeOf(AbstractASTType $type)
    {
        if ($type === $this) {
            return true;
        } elseif ($type instanceof ASTInterface) {
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
     * @return int
     *
     * @since  0.9.4
     */
    public function getModifiers()
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
     * @param int $modifiers
     *
     * @throws BadMethodCallException
     * @throws InvalidArgumentException
     *
     * @return void
     *
     * @since  0.9.4
     */
    public function setModifiers($modifiers)
    {
        if ($this->modifiers !== 0) {
            throw new BadMethodCallException(
                'Cannot overwrite previously set class modifiers.'
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

    /**
     * The magic wakeup method will be called by PHP's runtime environment when
     * a serialized instance of this class was unserialized. This implementation
     * of the wakeup method will register this object in the the global class
     * context.
     *
     * @return void
     *
     * @since  0.10.0
     */
    public function __wakeup()
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
     * @return array
     */
    public function __sleep()
    {
        return array_merge(
            array('type'),
            parent::__sleep()
        );
    }
}
