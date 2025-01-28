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

use PDepend\Source\AST\ASTArtifactList\CollectionArtifactFilter;

/**
 * Represents an interface or a class type.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
abstract class AbstractASTClassOrInterface extends AbstractASTType
{
    /**
     * The parent for this class node.
     *
     * @since 0.9.5
     */
    protected ?ASTClassReference $parentClassReference = null;

    /**
     * List of all interfaces implemented/extended by the this type.
     *
     * @var ASTClassOrInterfaceReference[]
     */
    protected array $interfaceReferences = [];

    /**
     * An <b>array</b> with all constants defined in this class or interface.
     *
     * @var ?array<string, mixed>
     */
    protected ?array $constants = null;

    /**
     * An <b>array</b> with all constant declarators defined in this class or interface.
     *
     * @var array<string, ASTConstantDeclarator>
     */
    protected array $constantDeclarators;

    /**
     * The magic sleep method is called by the PHP runtime environment before an
     * instance of this class gets serialized. It returns an array with the
     * names of all those properties that should be cached for this class or
     * interface instance.
     *
     * @return list<string>
     * @since  0.10.0
     */
    public function __sleep(): array
    {
        return ['constants', 'interfaceReferences', 'parentClassReference', ...parent::__sleep()];
    }

    /**
     * Returns the parent class or <b>null</b> if this class has no parent.
     *
     * @throws ASTClassOrInterfaceRecursiveInheritanceException
     */
    public function getParentClass(): null|ASTClass|ASTEnum
    {
        // No parent? Stop here!
        if ($this->parentClassReference === null) {
            return null;
        }

        $parentClass = $this->parentClassReference->getType();

        if ($parentClass === $this) {
            throw new ASTClassOrInterfaceRecursiveInheritanceException($this);
        }

        // Check parent against global filter
        $collection = CollectionArtifactFilter::getInstance();
        if (!$collection->accept($parentClass)) {
            return null;
        }

        return $parentClass;
    }

    /**
     * Returns an array with all parents for the current class.
     *
     * The returned array contains class instances for each parent of this class.
     * They are ordered in the reverse inheritance order. This means that the
     * direct parent of this class is the first element in the returned array
     * and parent of this parent the second element and so on.
     *
     * @return array<int, ASTClass|ASTEnum>
     * @throws ASTClassOrInterfaceRecursiveInheritanceException
     * @since  1.0.0
     */
    public function getParentClasses(): array
    {
        $parents = [];
        $parent = $this;

        while ($parent = $parent->getParentClass()) {
            if (in_array($parent, $parents, true)) {
                throw new ASTClassOrInterfaceRecursiveInheritanceException($parent);
            }

            $parents[] = $parent;
        }

        return $parents;
    }

    /**
     * Returns a reference onto the parent class of this class node or <b>null</b>.
     *
     * @since  0.9.5
     */
    public function getParentClassReference(): ?ASTClassReference
    {
        return $this->parentClassReference;
    }

    /**
     * Sets a reference onto the parent class of this class node.
     *
     * @param ASTClassReference $classReference Reference to the declared parent class.
     * @since  0.9.5
     */
    public function setParentClassReference(ASTClassReference $classReference): void
    {
        $this->nodes[] = $classReference;
        $this->parentClassReference = $classReference;
    }

    /**
     * Returns a node iterator with all implemented interfaces.
     *
     * @return ASTArtifactList<AbstractASTClassOrInterface>
     * @since  0.9.5
     */
    public function getInterfaces(): ASTArtifactList
    {
        return new ASTArtifactList($this->getInterfacesClasses());
    }

    /**
     * Returns an array of references onto the interfaces of this class node.
     *
     * @return ASTClassOrInterfaceReference[]
     * @since  0.10.4
     */
    public function getInterfaceReferences(): array
    {
        return $this->interfaceReferences;
    }

    /**
     * Adds a interface reference node.
     *
     * @since  0.9.5
     */
    public function addInterfaceReference(ASTClassOrInterfaceReference $interfaceReference): void
    {
        $this->nodes[] = $interfaceReference;
        $this->interfaceReferences[] = $interfaceReference;
    }

    /**
     * Returns an <b>array</b> with all constants defined in this class or
     * interface.
     *
     * @return array<string, mixed>
     */
    public function getConstants(): array
    {
        $constants = $this->constants;
        if ($constants === null) {
            $constants = $this->initConstants();
            $this->constants = $constants;
        }

        return $constants;
    }

    /**
     * Returns an <b>array</b> with all constant declarators defined in this class or interface.
     *
     * @return array<string, ASTConstantDeclarator>
     */
    public function getConstantDeclarators(): array
    {
        if (!isset($this->constantDeclarators)) {
            $this->initConstantDeclarators();
        }

        return $this->constantDeclarators;
    }

    /**
     * This method returns <b>true</b> when a constant for <b>$name</b> exists,
     * otherwise it returns <b>false</b>.
     *
     * @phpstan-assert-if-true !null $this->constants
     * @param string $name Name of the searched constant.
     * @since  0.9.6
     */
    public function hasConstant(string $name): bool
    {
        if ($this->constants === null) {
            $this->constants = $this->initConstants();
        }

        return array_key_exists($name, $this->constants);
    }

    /**
     * This method will return the value of a constant for <b>$name</b> or it
     * will return <b>false</b> when no constant for that name exists.
     *
     * @param string $name Name of the searched constant.
     * @since  0.9.6
     */
    public function getConstant(string $name): mixed
    {
        if ($this->hasConstant($name)) {
            return $this->constants[$name];
        }

        return false;
    }

    /**
     * Returns a list of all methods provided by this type or one of its parents.
     *
     * @return ASTMethod[]
     * @since  0.9.10
     */
    public function getAllMethods(): array
    {
        $methods = [];
        foreach ($this->getInterfaces() as $interface) {
            foreach ($interface->getAllMethods() as $method) {
                $methods[strtolower($method->getImage())] = $method;
            }
        }

        if (is_object($parentClass = $this->getParentClass())) {
            foreach ($parentClass->getAllMethods() as $methodName => $method) {
                $methods[$methodName] = $method;
            }
        }

        foreach ($this->getTraitMethods() as $method) {
            $methods[strtolower($method->getImage())] = $method;
        }

        foreach ($this->getMethods() as $method) {
            $methods[strtolower($method->getImage())] = $method;
        }

        return $methods;
    }

    /**
     * Returns all {@link AbstractASTClassOrInterface}
     * objects this type depends on.
     */
    public function getDependencies(): ASTClassOrInterfaceReferenceIterator
    {
        $references = $this->interfaceReferences;
        if ($this->parentClassReference !== null) {
            $references[] = $this->parentClassReference;
        }

        return new ASTClassOrInterfaceReferenceIterator($references);
    }

    /**
     * Returns <b>true</b> if this is an abstract class or an interface.
     */
    abstract public function isAbstract(): bool;

    /**
     * Returns the declared modifiers for this type.
     */
    abstract public function getModifiers(): int;

    /**
     * Returns an array with all implemented interfaces.
     *
     * @return AbstractASTClassOrInterface[]
     */
    protected function getInterfacesClasses(): array
    {
        $stack = $this->getParentClasses();
        array_unshift($stack, $this);

        $interfaces = [];

        while (($top = array_pop($stack)) !== null) {
            foreach ($top->interfaceReferences as $interfaceReference) {
                $interface = $interfaceReference->getType();

                if (in_array($interface, $interfaces, true)) {
                    continue;
                }

                $interfaces[] = $interface;
                $stack[] = $interface;
            }
        }

        return $interfaces;
    }

    /**
     * This method initializes the constants defined in this class or interface.
     *
     * @return array<string, mixed>
     * @since  0.9.6
     */
    private function initConstants(): array
    {
        $constants = [];
        $declarators = $this->getConstantDeclarators();

        foreach ($declarators as $declarator) {
            $image = $declarator->getImage();
            $value = $declarator->getValue()?->getValue();

            $constants[$image] = $value;
        }

        return $constants;
    }

    /**
     * This method initializes the constants defined in this class or interface.
     *
     * @since  0.9.6
     */
    private function initConstantDeclarators(): void
    {
        $this->constantDeclarators = [];
        if (($parentClass = $this->getParentClass()) !== null) {
            $this->constantDeclarators = $parentClass->getConstantDeclarators();
        }

        foreach ($this->getInterfaces() as $interface) {
            $this->constantDeclarators = $interface->getConstantDeclarators() + $this->constantDeclarators;
        }

        $definitions = $this->findChildrenOfType(ASTConstantDefinition::class);

        foreach ($definitions as $definition) {
            $declarators = $definition->findChildrenOfType(ASTConstantDeclarator::class);

            foreach ($declarators as $declarator) {
                $image = $declarator->getImage();

                $this->constantDeclarators[$image] = $declarator;
            }
        }
    }
}
