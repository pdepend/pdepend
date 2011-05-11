<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

/**
 * Represents an interface or a class type.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
abstract class PHP_Depend_Code_AbstractClassOrInterface
       extends PHP_Depend_Code_AbstractItem
{
    /**
     * The internal used cache instance.
     *
     * @var PHP_Depend_Util_Cache_Driver
     * @since 0.10.0
     */
    protected $cache = null;

    /**
     * The currently used builder context.
     *
     * @var PHP_Depend_Builder_Context
     * @since 0.10.0
     */
    protected $context = null;

    /**
     * The parent for this class node.
     *
     * @var PHP_Depend_Code_ASTClassReference
     * @since 0.9.5
     */
    protected $parentClassReference = null;

    /**
     * List of all interfaces implemented/extended by the this type.
     *
     * @var array(PHP_Depend_Code_ASTClassOrInterfaceReference)
     */
    protected $interfaceReferences = array();

    /**
     * The parent package for this class.
     *
     * @var PHP_Depend_Code_Package
     */
    private $_package = null;

    /**
     * List of {@link PHP_Depend_Code_Method} objects in this class.
     *
     * @var array(PHP_Depend_Code_Method)
     */
    protected $methods = array();

    /**
     * An <b>array</b> with all constants defined in this class or interface.
     *
     * @var array(string=>mixed)
     */
    protected $constants = null;

    /**
     * This property will indicate that the class or interface is user defined.
     * The parser marks all classes and interfaces as user defined that have a
     * source file and were part of parsing process.
     *
     * @var boolean
     * @since 0.9.5
     */
    protected $userDefined = false;

    /**
     * List of all parsed child nodes.
     *
     * @var array(PHP_Depend_Code_ASTNodeI)
     * @since 0.9.6
     */
    protected $nodes = array();

    /**
     * The start line number of the class or interface declaration.
     *
     * @var integer
     * @since 0.9.12
     */
    protected $startLine = 0;

    /**
     * The end line number of the class or interface declaration.
     *
     * @var integer
     * @since 0.9.12
     */
    protected $endLine = 0;

    /**
     * Name of the parent package for this class or interface instance. Or
     * <b>NULL</b> when no package was specified.
     *
     * @var string
     * @since 0.10.0
     */
    protected $packageName = null;

    /**
     * Was this class or interface instance restored from the cache?
     *
     * @var boolean
     * @since 0.10.0
     */
    protected $cached = false;

    /**
     * Setter method for the currently used token cache, where this class or
     * interface instance can store the associated tokens.
     *
     * @param PHP_Depend_Util_Cache_Driver $cache The currently used cache instance.
     *
     * @return PHP_Depend_Code_AbstractClassOrInterface
     * @since 0.10.0
     */
    public function setCache(PHP_Depend_Util_Cache_Driver $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * Sets the currently active builder context.
     *
     * @param PHP_Depend_Builder_Context $context Current builder context.
     *
     * @return PHP_Depend_Code_AbstractClassOrInterface
     * @since 0.10.0
     */
    public function setContext(PHP_Depend_Builder_Context $context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Adds a parsed child node to this node.
     *
     * @param PHP_Depend_Code_ASTNodeI $node A parsed child node instance.
     *
     * @return void
     * @access private
     * @since 0.9.6
     */
    public function addChild(PHP_Depend_Code_ASTNodeI $node)
    {
        $this->nodes[] = $node;
    }

    /**
     * Returns all child nodes of this class.
     *
     * @return array(PHP_Depend_Code_ASTNodeI)
     * @since 0.9.12
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
     * @param string $targetType Searched class or interface type.
     *
     * @return PHP_Depend_Code_ASTNodeI
     * @access private
     * @since 0.9.6
     * @todo Refactor $_methods property to getAllMethods() when it exists.
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
        foreach ($this->methods as $method) {
            if (($child = $method->getFirstChildOfType($targetType)) !== null) {
                return $child;
            }
        }
        return null;
    }

    /**
     * Will find all children for the given type.
     *
     * @param string $targetType The target class or interface type.
     * @param array  &$results   The found children.
     *
     * @return array(PHP_Depend_Code_ASTNodeI)
     * @access private
     * @since 0.9.6
     * @todo Refactor $_methods property to getAllMethods() when it exists.
     */
    public function findChildrenOfType($targetType, array &$results = array())
    {
        foreach ($this->nodes as $node) {
            if ($node instanceof $targetType) {
                $results[] = $node;
            }
            $node->findChildrenOfType($targetType, $results);
        }
        foreach ($this->methods as $method) {
            $method->findChildrenOfType($targetType, $results);
        }
        return $results;
    }

    /**
     * This method will return <b>true</b> when this type has a declaration in
     * the analyzed source files.
     *
     * @return boolean
     * @since 0.9.5
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
     * @since 0.9.5
     */
    public function setUserDefined()
    {
        $this->userDefined = true;
    }

    /**
     * Returns the parent class or <b>null</b> if this class has no parent.
     *
     * @return PHP_Depend_Code_Class
     */
    public function getParentClass()
    {
        // No parent? Stop here!
        if ($this->parentClassReference === null) {
            return null;
        }

        $parentClass = $this->parentClassReference->getType();

        // Check parent against global filter
        $collection = PHP_Depend_Code_Filter_Collection::getInstance();
        if ($collection->accept($parentClass) === false) {
            return null;
        }

        return $parentClass;
    }

    /**
     * Returns a reference onto the parent class of this class node or <b>null</b>.
     *
     * @return PHP_Depend_Code_ASTClassReference
     * @since 0.9.5
     */
    public function getParentClassReference()
    {
        return $this->parentClassReference;
    }

    /**
     * Sets a reference onto the parent class of this class node.
     *
     * @param PHP_Depend_Code_ASTClassReference $classReference Reference to the
     *        declared parent class.
     *
     * @return void
     * @since 0.9.5
     */
    public function setParentClassReference(
        PHP_Depend_Code_ASTClassReference $classReference
    ) {
        $this->nodes[]              = $classReference;
        $this->parentClassReference = $classReference;
    }

    /**
     * Returns a node iterator with all implemented interfaces.
     *
     * @return PHP_Depend_Code_NodeIterator
     * @since 0.9.5
     */
    public function getInterfaces()
    {
        $stack = array($this);

        if ($this->parentClassReference !== null) {
            array_unshift($stack, $this->parentClassReference->getType());
        }

        $parents = array();
        $interfaces = array();

        while (($top = array_pop($stack)) !== null) {

            foreach ($top->interfaceReferences as $interfaceReference) {
                $interface = $interfaceReference->getType();
                if (in_array($interface, $interfaces, true) === true) {
                    continue;
                }
                $interfaces[] = $interface;
                $stack[] = $interface;
            }

            if ($top->parentClassReference !== null) {
                $class = $top->parentClassReference->getType();
                if (!in_array($class, $parents, true)) {
                    $stack[] = $class;
                    $parents[] = $class;
                }
            }
        }

        return new PHP_Depend_Code_NodeIterator($interfaces);
    }

    /**
     * Returns an array of references onto the interfaces of this class node.
     *
     * @return array
     * @since 0.10.4
     */
    public function getInterfaceReferences()
    {
        return $this->interfaceReferences;
    }

    /**
     * Adds a interface reference node.
     *
     * @param PHP_Depend_Code_ASTClassOrInterfaceReference $interfaceReference The
     *        extended or implemented interface reference.
     *
     * @return void
     * @since 0.9.5
     */
    public function addInterfaceReference(
        PHP_Depend_Code_ASTClassOrInterfaceReference $interfaceReference
    ) {
        $this->nodes[]               = $interfaceReference;
        $this->interfaceReferences[] = $interfaceReference;
    }

    /**
     * Returns an <b>array</b> with all constants defined in this class or
     * interface.
     *
     * @return array(string=>mixed)
     */
    public function getConstants()
    {
        if ($this->constants === null) {
            $this->_initConstants();
        }
        return $this->constants;
    }

    /**
     * This method returns <b>true</b> when a constant for <b>$name</b> exists,
     * otherwise it returns <b>false</b>.
     *
     * @param string $name Name of the searched constant.
     *
     * @return boolean
     * @since 0.9.6
     */
    public function hasConstant($name)
    {
        if ($this->constants === null) {
            $this->_initConstants();
        }
        return array_key_exists($name, $this->constants);
    }

    /**
     * This method will return the value of a constant for <b>$name</b> or it
     * will return <b>false</b> when no constant for that name exists.
     *
     * @param string $name Name of the searched constant.
     *
     * @return mixed
     * @since 0.9.6
     */
    public function getConstant($name)
    {
        if ($this->hasConstant($name) === true) {
            return $this->constants[$name];
        }
        return false;
    }

    /**
     * Returns a list of all methods provided by this type or one of its parents.
     *
     * @return array(PHP_Depend_Code_Method)
     * @since 0.9.10
     */
    public function getAllMethods()
    {
        $methods = array();
        foreach ($this->getInterfaces() as $interface) {
            foreach ($interface->getAllMethods() as $method) {
                $methods[$method->getName()] = $method;
            }
        }

        $parentClass = $this->getParentClass();
        if (is_object($parentClass)) {
            foreach ($parentClass->getAllMethods() as $method) {
                $methods[$method->getName()] = $method;
            }
        }

        foreach ($this->methods as $method) {
            $methods[$method->getName()] = $method;
        }

        return $methods;
    }

    /**
     * Returns all {@link PHP_Depend_Code_Method} objects in this type.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getMethods()
    {
        return new PHP_Depend_Code_NodeIterator($this->methods);
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
        $method->setParent($this);

        $this->methods[] = $method;

        return $method;
    }

    /**
     * Returns all {@link PHP_Depend_Code_AbstractClassOrInterface} objects this
     * type depends on.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getDependencies()
    {
        $references = $this->interfaceReferences;
        if ($this->parentClassReference !== null) {
            $references[] = $this->parentClassReference;
        }

        return new PHP_Depend_Code_ClassOrInterfaceReferenceIterator($references);
    }

    /**
     * Returns an <b>array</b> with all tokens within this type.
     *
     * @return array(PHP_Depend_Token)
     */
    public function getTokens()
    {
        return (array) $this->cache
            ->type('tokens')
            ->restore($this->uuid);
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
        $this->startLine = reset($tokens)->startLine;
        $this->endLine   = end($tokens)->endLine;

        $this->cache
            ->type('tokens')
            ->store($this->uuid, $tokens);
    }

    /**
     * Returns the line number where the class or interface declaration starts.
     *
     * @return integer
     * @since 0.9.6
     */
    public function getStartLine()
    {
        return $this->startLine;
    }

    /**
     * Returns the line number where the class or interface declaration ends.
     *
     * @return integer
     * @since 0.9.6
     */
    public function getEndLine()
    {
        return $this->endLine;
    }

    /**
     * Returns the name of the parent package.
     *
     * @return string
     * @since 0.10.0
     */
    public function getPackageName()
    {
        return $this->packageName;
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
    public function setPackage(PHP_Depend_Code_Package $package)
    {
        $this->_package    = $package;
        $this->packageName = $package->getName();
    }

    /**
     * Resets the associated package reference.
     *
     * @return void
     * @since 0.10.2
     */
    public function unsetPackage()
    {
        $this->_package    = null;
        $this->packageName = null;
    }

    /**
     * This method will return <b>true</b> when this class or interface instance
     * was restored from the cache and not currently parsed. Otherwise this
     * method will return <b>false</b>.
     *
     * @return boolean
     * @since 0.10.0
     */
    public function isCached()
    {
        return $this->cached;
    }

    /**
     * Returns <b>true</b> if this is an abstract class or an interface.
     *
     * @return boolean
     */
    public abstract function isAbstract();

    /**
     * Checks that this user type is a subtype of the given <b>$type</b>
     * instance.
     *
     * @param PHP_Depend_Code_AbstractClassOrInterface $type The possible parent
     *        type instance.
     *
     * @return boolean
     */
    public abstract function isSubtypeOf(
        PHP_Depend_Code_AbstractClassOrInterface $type
    );

    /**
     * Returns the declared modifiers for this type.
     *
     * @return integer
     */
    public abstract function getModifiers();

    /**
     * This method initializes the constants defined in this class or interface.
     *
     * @return void
     * @since 0.9.6
     */
    private function _initConstants()
    {
        $this->constants = array();
        if (($parentClass = $this->getParentClass()) !== null) {
            $this->constants = $parentClass->getConstants();
        }

        foreach ($this->getInterfaces() as $interface) {
            $this->constants = array_merge(
                $this->constants,
                $interface->getConstants()
            );
        }

        $definitions = $this->findChildrenOfType(
            PHP_Depend_Code_ASTConstantDefinition::CLAZZ
        );

        foreach ($definitions as $definition) {
            $declarators = $definition->findChildrenOfType(
                PHP_Depend_Code_ASTConstantDeclarator::CLAZZ
            );

            foreach ($declarators as $declarator) {
                $image = $declarator->getImage();
                $value = $declarator->getValue()->getValue();

                $this->constants[$image] = $value;
            }
        }
    }

    /**
     * The magic sleep method is called by the PHP runtime environment before an
     * instance of this class gets serialized. It returns an array with the
     * names of all those properties that should be cached for this class or
     * interface instance.
     *
     * @return array(string)
     * @since 0.10.0
     */
    public function __sleep()
    {
        return array(
            'cache',
            'constants',
            'context',
            'docComment',
            'endLine',
            'interfaceReferences',
            'methods',
            'modifiers',
            'name',
            'nodes',
            'packageName',
            'parentClassReference',
            'startLine',
            'userDefined',
            'uuid'
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
     * @since 0.10.0
     */
    public function __wakeup()
    {
        $this->cached = true;

        foreach ($this->methods as $method) {
            $method->sourceFile = $this->sourceFile;
            $method->setParent($this);
        }
    }

    /**
     * This method can be called by the PHP_Depend runtime environment or a
     * utilizing component to free up memory. This methods are required for
     * PHP version < 5.3 where cyclic references can not be resolved
     * automatically by PHP's garbage collector.
     *
     * @return void
     * @since 0.9.12
     */
    public function free()
    {
        $this->_removeReferenceToPackage();
        $this->_removeReferencesToMethods();
        $this->_removeReferencesToNodes();
        $this->_removeReferencesToReferences();
    }

    /**
     * Free memory consumed by the methods associated with this class/interface
     * instance.
     *
     * @return void
     * @since 0.9.12
     */
    private function _removeReferencesToMethods()
    {
        $this->getMethods()->free();
        $this->methods = array();
    }

    /**
     * Free memory consumed by the ast nodes associated with this class/interface
     * instance.
     *
     * @return void
     * @since 0.9.12
     */
    private function _removeReferencesToNodes()
    {
        foreach ($this->nodes as $node) {
            $node->free();
        }
        $this->nodes = array();
    }

    /**
     * Free memory consumed by the parent package of this class/interface
     * instance.
     *
     * @return void
     * @since 0.9.12
     */
    private function _removeReferenceToPackage()
    {
        $this->_package = null;
    }

    /**
     * Free memory consumed by references to/from this class/interface instance.
     *
     * @return void
     * @since 0.9.12
     */
    private function _removeReferencesToReferences()
    {
        $this->getDependencies()->free();

        $this->interfaceReferences  = array();
        $this->parentClassReference = null;
    }
}
