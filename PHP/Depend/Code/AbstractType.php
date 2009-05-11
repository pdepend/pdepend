<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once 'PHP/Depend/Code/AbstractItem.php';
require_once 'PHP/Depend/Util/UUID.php';

/**
 * Represents an interface or a class type.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
abstract class PHP_Depend_Code_AbstractType extends PHP_Depend_Code_AbstractItem
{
    /**
     * List of {@link PHP_Depend_Code_AbstractType} objects this type depends on.
     *
     * @var array(PHP_Depend_Code_AbstractType) $dependencies
     */
    protected $dependencies = array();

    /**
     * List of all interfaces implemented/extended by the this type.
     *
     * @var array(PHP_Depend_Code_InterfaceReference) $_interfaceReferences
     */
    private $_interfaceReferences = array();

    /**
     * The parent package for this class.
     *
     * @var PHP_Depend_Code_Package $_package
     */
    private $_package = null;

    /**
     * List of {@link PHP_Depend_Code_Method} objects in this class.
     *
     * @var array(PHP_Depend_Code_Method) $_methods
     */
    private $_methods = array();

    /**
     * The tokens for this type.
     *
     * @var array(array) $_tokens
     */
    private $_tokens = array();

    /**
     * List of {@link PHP_Depend_Code_TypeConstant} objects that belong to this
     * type.
     *
     * @var array(PHP_Depend_Code_TypeConstant) $_constants
     */
    private $_constants = array();

    /**
     * Type position within the source code file.
     *
     * @var integer $_position
     */
    private $_position = 0;

    /**
     * This property will indicate that the class or interface is user defined.
     * The parser marks all classes and interfaces as user defined that have a
     * source file and were part of parsing process.
     *
     * @var boolean $_userDefined
     * @since 0.9.5
     */
    private $_userDefined = false;

    /**
     * This method will return <b>true</b> when this type has a declaration in
     * the analyzed source files.
     *
     * @return boolean
     * @since 0.9.5
     */
    public function isUserDefined()
    {
        return $this->_userDefined;
    }

    /**
     * This method can be used to mark a type as user defined. User defined
     * means that the type has a valid declaration in the analyzed source files.
     *
     * @return void
     * @since 0.9.5
     */
    public function setUserDefined()
    {
        $this->_userDefined = true;
    }

    /**
     * Returns a node iterator with all implemented interfaces.
     *
     * @return PHP_Depend_Code_NodeIterator
     * @since 0.9.5
     */
    public function getInterfaces()
    {
        $nodes = array();

        foreach ($this->_interfaceReferences as $interfaceReference) {
            $interface = $interfaceReference->getType();
            if (in_array($interface, $nodes, true) === true) {
                continue;
            }
            $nodes[] = $interface;
            foreach ($interface->getInterfaces() as $parentInterface) {
                if (in_array($parentInterface, $nodes, true) === false) {
                    $nodes[] = $parentInterface;
                }
            }
        }

        foreach ($this->getUnfilteredRawDependencies() as $dependency) {
            // Add parent interfaces of parent class
            if ($dependency instanceof PHP_Depend_Code_Class) {
                foreach ($dependency->getInterfaces() as $interface) {
                    $nodes[] = $interface;
                }
                continue;
            }
        }
        return new PHP_Depend_Code_NodeIterator($nodes);
    }

    /**
     * Adds a interface reference node.
     *
     * @param PHP_Depend_Code_InterfaceReference $interfaceReference The extended
     *        or implemented interface reference.
     *
     * @return void
     */
    public function addInterfaceReference(
        PHP_Depend_Code_InterfaceReference $interfaceReference
    ) {
        $this->_interfaceReferences[] = $interfaceReference;
    }

    /**
     * Returns all {@link PHP_Depend_Code_TypeConstant} objects in this type.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getConstants()
    {
        return new PHP_Depend_Code_NodeIterator($this->_constants);
    }

    /**
     * Adds the given constant to this type.
     *
     * @param PHP_Depend_Code_TypeConstant $constant A new type constant.
     *
     * @return PHP_Depend_Code_TypeConstant
     */
    public function addConstant(PHP_Depend_Code_TypeConstant $constant)
    {
        if ($constant->getParent() !== null) {
            $constant->getParent()->removeConstant($constant);
        }
        // Set this as owner type
        $constant->setParent($this);
        // Store constant
        $this->_constants[] = $constant;

        return $constant;
    }

    /**
     * Removes the given constant from this type.
     *
     * @param PHP_Depend_Code_TypeConstant $constant The constant to remove.
     *
     * @return void
     */
    public function removeConstant(PHP_Depend_Code_TypeConstant $constant)
    {
        if (($i = array_search($constant, $this->_constants, true)) !== false) {
            // Remove this as owner
            $constant->setParent(null);
            // Remove from internal list
            unset($this->_constants[$i]);
        }
    }

    /**
     * Returns all {@link PHP_Depend_Code_Method} objects in this type.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getMethods()
    {
        return new PHP_Depend_Code_NodeIterator($this->_methods);
    }

    /**
     * Adds the given method to this type.
     *
     * @param PHP_Depend_Code_Method $method A new type method.
     *
     * @return PHP_Depend_Code_Method
     */
    public function addMethod(PHP_Depend_Code_Method $method)
    {
        if ($method->getParent() !== null) {
            $method->getParent()->removeMethod($method);
        }
        // Set this as owner type
        $method->setParent($this);
        // Store method
        $this->_methods[] = $method;

        return $method;
    }

    /**
     * Removes the given method from this class.
     *
     * @param PHP_Depend_Code_Method $method The method to remove.
     *
     * @return void
     */
    public function removeMethod(PHP_Depend_Code_Method $method)
    {
        if (($i = array_search($method, $this->_methods, true)) !== false) {
            // Remove this as owner
            $method->setParent(null);
            // Remove from internal list
            unset($this->_methods[$i]);
        }
    }

    /**
     * Returns all {@link PHP_Depend_Code_AbstractType} objects this type depends on.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getDependencies()
    {
        return new PHP_Depend_Code_NodeIterator($this->dependencies);
    }

    /**
     * Returns an unfiltered, raw array of {@link PHP_Depend_Code_AbstractType}
     * objects this type depends on. This method is only for internal usage.
     *
     * @return array(PHP_Depend_Code_AbstractType)
     * @access private
     */
    public function getUnfilteredRawDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Adds the given {@link PHP_Depend_Code_AbstractType} object as dependency.
     *
     * @param PHP_Depend_Code_AbstractType $type A type this function depends on.
     *
     * @return void
     */
    public function addDependency(PHP_Depend_Code_AbstractType $type)
    {
        if (array_search($type, $this->dependencies, true) === false) {
            // Store type dependency
            $this->dependencies[] = $type;
        }
    }

    /**
     * Removes the given {@link PHP_Depend_Code_AbstractType} object from the
     * dependency list.
     *
     * @param PHP_Depend_Code_AbstractType $type A type to remove.
     *
     * @return void
     */
    public function removeDependency(PHP_Depend_Code_AbstractType $type)
    {
        if (($i = array_search($type, $this->dependencies, true)) !== false) {
            // Remove from internal list
            unset($this->dependencies[$i]);
        }
    }

    /**
     * Returns an <b>array</b> with all tokens within this type.
     *
     * @return array(PHP_Depend_Token)
     */
    public function getTokens()
    {
        $storage = PHP_Depend_StorageRegistry::get(PHP_Depend::TOKEN_STORAGE);
        return (array) $storage->restore($this->getUUID(), 'tokens-type');
    }

    /**
     * Sets the tokens for this type.
     *
     * @param array(PHP_Depend_Token) $tokens The generated tokens.
     *
     * @return void
     */
    public function setTokens(array $tokens)
    {
        $storage = PHP_Depend_StorageRegistry::get(PHP_Depend::TOKEN_STORAGE);
        $storage->store($tokens, $this->getUUID(), 'tokens-type');
    }

    /**
     * Returns the parent package for this class.
     *
     * @return PHP_Depend_Code_Package
     */
    public function getPackage()
    {
        return $this->_package;
    }

    /**
     * Sets the parent package for this class.
     *
     * @param PHP_Depend_Code_Package $package The parent package.
     *
     * @return void
     */
    public function setPackage(PHP_Depend_Code_Package $package = null)
    {
        $this->_package = $package;
    }

    /**
     * Returns <b>true</b> if this is an abstract class or an interface.
     *
     * @return boolean
     */
    public abstract function isAbstract();

    /**
     * Checks that this user type is a subtype of the given <b>$type</b> instance.
     *
     * @param PHP_Depend_Code_AbstractType $type The possible parent type instance.
     *
     * @return boolean
     */
    public abstract function isSubtypeOf(PHP_Depend_Code_AbstractType $type);

    /**
     * Returns the declared modifiers for this type.
     *
     * @return integer
     */
    public abstract function getModifiers();
}