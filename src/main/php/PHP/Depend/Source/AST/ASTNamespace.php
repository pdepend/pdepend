<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

namespace PHP\Depend\Source\AST;

use PHP\Depend\Source\AST\ASTFunction;
use PHP\Depend\TreeVisitor\TreeVisitor;

/**
 * Represents a php package node.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
class ASTNamespace implements \PHP_Depend_Code_NodeI
{
    /**
     * The type of this class.
     *
     * @since 0.10.0
     */
    const CLAZZ = __CLASS__;

    /**
     * The package name.
     *
     * @var string
     */
    protected $name = '';

    /**
     * The unique identifier for this function.
     *
     * @var string
     */
    protected $uuid = null;

    /**
     * List of all {@link \PHP\Depend\Source\AST\AbstractASTClassOrInterface}
     * objects for this package.
     *
     * @var \PHP\Depend\Source\AST\AbstractASTClassOrInterface[]
     */
    protected $types = array();

    /**
     * List of all standalone {@link \PHP\Depend\Source\AST\ASTFunction} objects
     * in this package.
     *
     * @var \PHP\Depend\Source\AST\ASTFunction[]
     */
    protected $functions = array();

    /**
     * Does this package contain user defined functions, classes or interfaces?
     *
     * @var boolean
     */
    private $userDefined = null;

    /**
     * Constructs a new package for the given <b>$name</b>
     *
     * @param string $name The package name.
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->uuid = spl_object_hash($this);
    }

    /**
     * Returns the package name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns a uuid for this code node.
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Returns <b>true</b> when at least one artifact <b>function</b> or a
     * <b>class/method</b> is user defined. Otherwise this method will return
     * <b>false</b>.
     *
     * @return boolean
     * @since 0.9.10
     */
    public function isUserDefined()
    {
        if ($this->userDefined === null) {
            $this->userDefined = $this->checkUserDefined();
        }
        return $this->userDefined;
    }

    /**
     * Returns <b>true</b> when at least one artifact <b>function</b> or a
     * <b>class/method</b> is user defined. Otherwise this method will return
     * <b>false</b>.
     *
     * @return boolean
     * @since 0.9.10
     */
    private function checkUserDefined()
    {
        foreach ($this->types as $type) {
            if ($type->isUserDefined()) {
                return true;
            }
        }
        return (count($this->functions) > 0);
    }

    /**
     * Returns an array with all {@link \PHP\Depend\Source\AST\ASTTrait}
     * instances declared in this package.
     *
     * @return array
     * @since 1.0.0
     */
    public function getTraits()
    {
        return $this->getTypesOfType(ASTTrait::CLAZZ);
    }

    /**
     * Returns an iterator with all {@link \PHP\Depend\Source\AST\ASTClass}
     * instances within this package.
     *
     * @return \PHP_Depend_Code_NodeIterator
     */
    public function getClasses()
    {
        return $this->getTypesOfType(ASTClass::CLAZZ);
    }

    /**
     * Returns an iterator with all {@link \PHP\Depend\Source\AST\ASTInterface}
     * instances within this package.
     *
     * @return \PHP_Depend_Code_NodeIterator
     */
    public function getInterfaces()
    {
        return $this->getTypesOfType(ASTInterface::CLAZZ);
    }

    /**
     * Returns an iterator with all types of the given <b>$className</b> in this
     * package.
     *
     * @param string $className The class/type we are looking for.
     *
     * @return \PHP_Depend_Code_NodeIterator
     * @since 1.0.0
     */
    private function getTypesOfType($className)
    {
        $types = array();
        foreach ($this->types as $type) {
            if (get_class($type) === $className) {
                $types[] = $type;
            }
        }
        return new \PHP_Depend_Code_NodeIterator($types);
    }

    /**
     * Returns all {@link \PHP\Depend\Source\AST\AbstractASTType} objects in
     * this package.
     *
     * @return \PHP_Depend_Code_NodeIterator
     */
    public function getTypes()
    {
        return new \PHP_Depend_Code_NodeIterator($this->types);
    }

    /**
     * Adds the given type to this package and returns the input type instance.
     *
     * @param \PHP\Depend\Source\AST\AbstractASTType $type
     * @return \PHP\Depend\Source\AST\AbstractASTType
     */
    public function addType(AbstractASTType $type)
    {
        // Skip if this package already contains this type
        if (in_array($type, $this->types, true)) {
            return $type;
        }

        if ($type->getPackage() !== null) {
            $type->getPackage()->removeType($type);
        }

        // Set this as class package
        $type->setPackage($this);
        // Append class to internal list
        $this->types[$type->getUuid()] = $type;

        return $type;
    }

    /**
     * Removes the given type instance from this package.
     *
     * @param \PHP\Depend\Source\AST\AbstractASTType $type
     * @return void
     */
    public function removeType(AbstractASTType $type)
    {
        if (($index = array_search($type, $this->types, true)) !== false) {
            // Remove class from internal list
            unset($this->types[$index]);
            // Remove this as parent
            $type->unsetPackage();
        }
    }

    /**
     * Returns all {@link \PHP\Depend\Source\AST\ASTFunction} objects in this package.
     *
     * @return \PHP_Depend_Code_NodeIterator
     */
    public function getFunctions()
    {
        return new \PHP_Depend_Code_NodeIterator($this->functions);
    }

    /**
     * Adds the given function to this package and returns the input instance.
     *
     * @param \PHP\Depend\Source\AST\ASTFunction $function
     * @return \PHP\Depend\Source\AST\ASTFunction
     */
    public function addFunction(ASTFunction $function)
    {
        if ($function->getPackage() !== null) {
            $function->getPackage()->removeFunction($function);
        }

        // Set this as function package
        $function->setPackage($this);
        // Append function to internal list
        $this->functions[$function->getUuid()] = $function;

        return $function;
    }

    /**
     * Removes the given function from this package.
     *
     * @param \PHP\Depend\Source\AST\ASTFunction $function
     * @return void
     */
    public function removeFunction(ASTFunction $function)
    {
        if (($index = array_search($function, $this->functions, true)) !== false) {
            // Remove function from internal list
            unset($this->functions[$index]);
            // Remove this as parent
            $function->unsetPackage();
        }
    }

    /**
     * TreeVisitor method for node tree traversal.
     *
     * @param \PHP\Depend\TreeVisitor\TreeVisitor $visitor
     * @return void
     */
    public function accept(TreeVisitor $visitor)
    {
        $visitor->visitNamespace($this);
    }
}
