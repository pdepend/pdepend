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
 * @subpackage Builder
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/BuilderI.php';
require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/TypeConstant.php';
require_once 'PHP/Depend/Code/Interface.php';
require_once 'PHP/Depend/Code/NodeIterator.php';
require_once 'PHP/Depend/Code/Function.php';
require_once 'PHP/Depend/Code/Method.php';
require_once 'PHP/Depend/Code/Package.php';
require_once 'PHP/Depend/Code/Parameter.php';
require_once 'PHP/Depend/Code/Property.php';
require_once 'PHP/Depend/Util/Log.php';
require_once 'PHP/Depend/Util/Type.php';

/**
 * Default code tree builder implementation.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Builder
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Builder_Default implements PHP_Depend_BuilderI
{
    /**
     * Default package which contains all functions and classes with an unknown
     * scope.
     *
     * @var PHP_Depend_Code_Package $defaultPackage
     */
    protected $defaultPackage = null;

    /**
     * Default source file that acts as a dummy.
     *
     * @var PHP_Depend_Code_File $defaultFile
     */
    protected $defaultFile = null;

    /**
     * All generated {@link PHP_Depend_Code_Class} objects
     *
     * @var array(string=>PHP_Depend_Code_Class) $classes
     */
    protected $classes = array();

    /**
     * All generated {@link PHP_Depend_Code_Interface} instances.
     *
     * @var array(string=>PHP_Depend_Code_Interface) $interfaces
     */
    protected $interfaces = array();

    /**
     * All generated {@link PHP_Depend_Code_Package} objects
     *
     * @var array(string=>PHP_Depend_Code_Package) $packages
     */
    protected $packages = array();

    /**
     * All generated {@link PHP_Depend_Code_Function} instances.
     *
     * @var array(string=>PHP_Depend_Code_Function) $functions
     */
    protected $functions = array();

    /**
     * All generated {@link PHP_Depend_Code_Method} instances.
     *
     * @var array(PHP_Depend_Code_Method) $methods
     */
    protected $methods = array();

    /**
     * All generated {@link PHP_Depend_Code_Parameter} instances.
     *
     * @var array(PHP_Depend_Code_Parameter) $_parameters
     */
    private $_parameters = array();

    /**
     * All generated {@link PHP_Depend_Code_Property} instances.
     *
     * @var array(PHP_Depend_Code_Property) $_properties
     */
    private $_properties = array();

    /**
     * All generated {@link PHP_Depend_Code_TypeConstant} instances.
     *
     * @var array(PHP_Depend_Code_TypeConstant) $_typeConstants
     */
    private $_typeConstants = array();

    /**
     * Constructs a new builder instance.
     */
    public function __construct()
    {
        $this->defaultPackage = new PHP_Depend_Code_Package(self::DEFAULT_PACKAGE);
        $this->defaultFile    = new PHP_Depend_Code_File(null);

        $this->packages[self::DEFAULT_PACKAGE] = $this->defaultPackage;
    }

    /**
     * Generic build class for classes and interfaces. This method should be used
     * in cases when it is not clear what type is used in the current situation.
     * This could happen if the parser analyzes a method signature. The default
     * return type is {@link PHP_Depend_Code_Class}, but if there is already an
     * interface for this name, the method will return this instance.
     *
     * <code>
     *   $builder->buildInterface('PHP_DependI');
     *
     *   // Returns an instance of PHP_Depend_Code_Interface
     *   $builder->buildClassOrInterface('PHP_DependI');
     *
     *   // Returns an instance of PHP_Depend_Code_Class
     *   $builder->buildClassOrInterface('PHP_Depend');
     * </code>
     *
     * @param string $name The class name.
     *
     * @return PHP_Depend_Code_Class|PHP_Depend_Code_Interface
     *         The created class or interface instance.
     */
    public function buildClassOrInterface($name)
    {
        $cls = $this->extractTypeName($name);
        $pkg = $this->extractPackageName($name);

        $typeID = strtolower($cls);

        if (isset($this->classes[$typeID][$pkg])) {
            $instance = $this->classes[$typeID][$pkg];
        } else if (isset($this->interfaces[$typeID][$pkg])) {
            $instance = $this->interfaces[$typeID][$pkg];
        } else if (isset($this->classes[$typeID])) {
            $instance = reset($this->classes[$typeID]);
        } else if (isset($this->interfaces[$typeID])) {
            $instance = reset($this->interfaces[$typeID]);
        } else {
            $instance = $this->buildClass($name);
        }
        return $instance;
    }

    /**
     * Builds a new class instance or reuses a previous created class.
     *
     * Where possible you should give a qualified class name, that is prefixed
     * with the package identifier.
     *
     * <code>
     *   $builder->buildClass('php::depend::Parser');
     * </code>
     *
     * To determine the correct class, this method implements the following
     * algorithm.
     *
     * <ol>
     *   <li>Check for an exactly matching instance and reuse it.</li>
     *   <li>Check for a class instance that belongs to the default package. If
     *   such an instance exists, reuse it and replace the default package with
     *   the newly given package information.</li>
     *   <li>Check that the requested class is in the default package, if this
     *   is true, reuse the first class instance and ignore the default package.
     *   </li>
     *   <li>Create a new instance for the specified package.</li>
     * </ol>
     *
     * @param string  $name The class name.
     * @param integer $line The line number for the class declaration.
     *
     * @return PHP_Depend_Code_Class The created class object.
     */
    public function buildClass($name, $line = 0)
    {
        $cls = $this->extractTypeName($name);
        $pkg = $this->extractPackageName($name);

        $typeID = strtolower($cls);

        $class = null;

        // 1) check for an equal class version
        if (isset($this->classes[$typeID][$pkg])) {
            $class = $this->classes[$typeID][$pkg];

            // 2) check for a default version that could be replaced
        } else if (isset($this->classes[$typeID][self::DEFAULT_PACKAGE])) {
            $class = $this->classes[$typeID][self::DEFAULT_PACKAGE];

            unset($this->classes[$typeID][self::DEFAULT_PACKAGE]);

            $this->classes[$typeID][$pkg] = $class;

            $this->buildPackage($pkg)->addType($class);

            // 3) check for any version that could be used instead of the default
        } else if (isset($this->classes[$typeID]) && $this->isDefault($pkg)) {
            $class = reset($this->classes[$typeID]);

            // 4) Create a new class for the given package
        } else {
            // Debug class creation
            PHP_Depend_Util_Log::debug('Creating class "' . $name . '"');

            // Create a new class instance
            $class = new PHP_Depend_Code_Class($cls, $line);
            $class->setSourceFile($this->defaultFile);

            // Store class reference
            $this->classes[$typeID][$pkg] = $class;

            // Append to class package
            $this->buildPackage($pkg)->addType($class);
        }

        return $class;
    }

    /**
     * Builds a new code class constant instance.
     *
     * @param string $name The constant name.
     *
     * @return PHP_Depend_Code_TypeConstant The created constant object.
     */
    public function buildTypeConstant($name)
    {
        // Debug type constant creation
        PHP_Depend_Util_Log::debug('Creating type constant "' . $name . '"');

        // Create new constant instance.
        $constant = new PHP_Depend_Code_TypeConstant($name);

        // Store local reference
        $this->_typeConstants[] = $constant;

        return $constant;
    }

    /**
     * Builds a new new interface instance.
     *
     * If there is an existing class instance for the given name, this method
     * checks if this class is part of the default namespace. If this is the
     * case this method will update all references to the new interface and it
     * removes the class instance. Otherwise it creates new interface instance.
     *
     * Where possible you should give a qualified interface name, that is
     * prefixed with the package identifier.
     *
     * <code>
     *   $builder->buildInterface('php::depend::Parser');
     * </code>
     *
     * To determine the correct interface, this method implements the following
     * algorithm.
     *
     * <ol>
     *   <li>Check for an exactly matching instance and reuse it.</li>
     *   <li>Check for a interface instance that belongs to the default package.
     *   If such an instance exists, reuse it and replace the default package
     *   with the newly given package information.</li>
     *   <li>Check that the requested interface is in the default package, if
     *   this is true, reuse the first interface instance and ignore the default
     *   package.
     *   </li>
     *   <li>Create a new instance for the specified package.</li>
     * </ol>
     *
     * @param string  $name The interface name.
     * @param integer $line The line number for the interface declaration.
     *
     * @return PHP_Depend_Code_Interface The created interface object.
     */
    public function buildInterface($name, $line = 0)
    {
        $ife = $this->extractTypeName($name);
        $pkg = $this->extractPackageName($name);

        $typeID = strtolower($ife);

        $class = null;
        if (isset($this->classes[$typeID][$pkg])) {
            $class = $this->classes[$typeID][$pkg];
        } else if (isset($this->classes[$typeID][self::DEFAULT_PACKAGE])) {
            // TODO: Implement something like: allwaysIsClass(),
            //       This could be usefull for class names detected by 'new ...'
            $class = $this->classes[$typeID][self::DEFAULT_PACKAGE];
        }

        if ($class !== null) {
            $package = $class->getPackage();

            // Only reparent if the found class is part of the default package
            if ($package === $this->defaultPackage) {
                $package->removeType($class);

                unset($this->classes[$typeID][$package->getName()]);

                if (count($this->classes[$typeID]) === 0) {
                    unset($this->classes[$typeID]);
                }
            } else {
                // Unset class reference
                $class = null;
            }
        }

        // 1) check for an equal interface version
        if (isset($this->interfaces[$typeID][$pkg])) {
            $interface = $this->interfaces[$typeID][$pkg];

            // 2) check for a default version that could be replaced
        } else if (isset($this->interfaces[$typeID][self::DEFAULT_PACKAGE])) {
            $interface = $this->interfaces[$typeID][self::DEFAULT_PACKAGE];

            unset($this->interfaces[$typeID][self::DEFAULT_PACKAGE]);

            $this->interfaces[$typeID][$pkg] = $interface;

            $this->buildPackage($pkg)->addType($interface);

            // 3) check for any version that could be used instead of the default
        } else if (isset($this->interfaces[$typeID]) && $this->isDefault($pkg)) {
            $interface = reset($this->interfaces[$typeID]);

            // 4) Create a new interface for the given package
        } else {
            // Debug interface creation
            PHP_Depend_Util_Log::debug('Creating interface "' . $name . '"');

            // Create a new interface instance
            $interface = new PHP_Depend_Code_Interface($ife, $line);
            $interface->setSourceFile($this->defaultFile);

            // Store interface reference
            $this->interfaces[$typeID][$pkg] = $interface;

            // Append interface to package
            $this->buildPackage($pkg)->addType($interface);
        }

        if ($class !== null) {
            $this->replaceClassReferences($class, $interface);
        }

        return $interface;
    }

    /**
     * Builds a new method instance.
     *
     * @param string  $name The method name.
     * @param integer $line The line number with the method declaration.
     *
     * @return PHP_Depend_Code_Method The created class method object.
     */
    public function buildMethod($name, $line = 0)
    {
        // Debug method creation
        PHP_Depend_Util_Log::debug('Creating method "' . $name . '()"');

        // Create a new method instance
        $method = new PHP_Depend_Code_Method($name, $line);

        // Store instance an local map
        $this->methods[] = $method;

        return $method;
    }

    /**
     * Builds a new package instance.
     *
     * @param string $name The package name.
     *
     * @return PHP_Depend_Code_Package The created package object.
     */
    public function buildPackage($name)
    {
        if (!isset($this->packages[$name])) {
            // Debug package creation
            PHP_Depend_Util_Log::debug('Creating package "' . $name . '"');

            $this->packages[$name] = new PHP_Depend_Code_Package($name);
        }
        return $this->packages[$name];
    }

    /**
     * Builds a new parameter instance.
     *
     * @param string  $name The parameter variable name.
     * @param integer $line The line number with the parameter declaration.
     *
     * @return PHP_Depend_Code_Parameter The created parameter instance.
     */
    public function buildParameter($name, $line = 0)
    {
        // Debug parameter creation
        PHP_Depend_Util_Log::debug('Creating parameter "' . $name . '"');

        // Create a new parameter instance
        $parameter = new PHP_Depend_Code_Parameter($name, $line);

        // Store local reference
        $this->_parameters[] = $parameter;

        return $parameter;
    }

    /**
     * Builds a new property instance.
     *
     * @param string  $name The property variable name.
     * @param integer $line The line number with the property declaration.
     *
     * @return PHP_Depend_Code_Property The created property instance.
     */
    public function buildProperty($name, $line = 0)
    {
        // Debug property creation
        PHP_Depend_Util_Log::debug('Creating property "' . $name . '"');

        // Create new property instance.
        $property = new PHP_Depend_Code_Property($name, $line);

        // Store local reference
        $this->_properties[] = $property;

        return $property;
    }

    /**
     * Builds a new function instance.
     *
     * @param string               $name       The function name.
     * @param integer              $line       The line number with the function
     *                                         declaration.
     * @param PHP_Depend_Code_File $sourceFile The source file for the function.
     *
     * @return PHP_Depend_Code_Function The function instance.
     */
    public function buildFunction($name,
                                  $line = 0,
                                  PHP_Depend_Code_File $sourceFile = null)
    {
        if (isset($this->functions[$name])) {
            $function = $this->functions[$name];
        } else {
            // Debug function creation
            PHP_Depend_Util_Log::debug('Creating function "' . $name . '()"');

            // Create new function
            $function = new PHP_Depend_Code_Function($name, $line, $sourceFile);
            $function->setSourceFile($this->defaultFile);

            // Add to default package
            $this->defaultPackage->addFunction($function);
            // Store function reference
            $this->functions[$name] = $function;
        }

        if ($sourceFile !== null) {
            $function->setSourceFile($sourceFile);
        }

        return $function;
    }

    /**
     * Returns an iterator with all generated {@link PHP_Depend_Code_Package}
     * objects.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getIterator()
    {
        return $this->getPackages();
    }

    /**
     * Returns an iterator with all generated {@link PHP_Depend_Code_Package}
     * objects.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getPackages()
    {
        // Create a package array copy
        $packages = $this->packages;

        // Remove default package if empty
        if ($this->defaultPackage->getTypes()->count() === 0
         && $this->defaultPackage->getFunctions()->count() === 0) {

            unset($packages[self::DEFAULT_PACKAGE]);
        }
        return new PHP_Depend_Code_NodeIterator($packages);
    }

    /**
     * Returns <b>true</b> if the given package is the default package.
     *
     * @param string $packageName The package name.
     *
     * @return boolean
     */
    protected function isDefault($packageName)
    {
        return ($packageName === self::DEFAULT_PACKAGE);
    }

    /**
     * Extracts the type name of a qualified PHP 5.3 type identifier.
     *
     * <code>
     *   $typeName = $this->extractTypeName('foo\bar\foobar');
     *   var_dump($typeName);
     *   // Results in:
     *   // string(6) "foobar"
     * </code>
     *
     * @param string $qualifiedName The qualified PHP 5.3 type identifier.
     *
     * @return string
     */
    protected function extractTypeName($qualifiedName)
    {
        if (($pos = strrpos($qualifiedName, '\\')) !== false) {
            return substr($qualifiedName, $pos + 1);
        }
        return $qualifiedName;
    }

    /**
     * Extracts the package name of a qualified PHP 5.3 class identifier.
     *
     * If the class name doesn't contain a package identifier this method will
     * return the default identifier.
     *
     * <code>
     *   $packageName = $this->extractPackageName('foo\bar\foobar');
     *   var_dump($packageName);
     *   // Results in:
     *   // string(8) "foo\bar"
     *
     *   $packageName = $this->extractPackageName('foobar');
     *   var_dump($packageName);
     *   // Results in:
     *   // string(6) "+global"
     * </code>
     *
     * @param string $qualifiedName The qualified PHP 5.3 class identifier.
     *
     * @return string
     */
    protected function extractPackageName($qualifiedName)
    {
        if (($pos = strrpos($qualifiedName, '\\')) !== false) {
            return substr($qualifiedName, 0, $pos);
        } else if (PHP_Depend_Util_Type::isInternalType($qualifiedName)) {
            return PHP_Depend_Util_Type::getTypePackage($qualifiedName);
        }
        return self::DEFAULT_PACKAGE;
    }

    /**
     * This method will replace all existing references to the given <b>$class</b>
     * instance with the interface instance.
     *
     * <code>
     *   $class1 = $builder->buildClass('PHP_Depend');
     *   $class2 = $builder->buildClassOrInterface('PHP_DependI');
     *
     *   $class1->addDependency($class2);
     *
     *   $builder->buildInterface('PHP_DependI');
     *
     *   var_dump($class->getDependencies());
     *   // Results in
     *   // array(1) {
     *   //   [0]=>
     *   //   object(PHP_Depend_Code_Interface)#1 (0) {
     *   //   }
     *   // }
     * </code>
     *
     * @param PHP_Depend_Code_Class     $class     The old context class instance.
     * @param PHP_Depend_Code_Interface $interface Tge new interface instance.
     *
     * @return void
     */
    protected function replaceClassReferences(PHP_Depend_Code_Class $class,
                                              PHP_Depend_Code_Interface $interface)
    {
        foreach ($this->classes as $types) {
            foreach ($types as $type) {
                foreach ($type->getUnfilteredRawDependencies() as $dependency) {
                    if ($dependency === $class) {
                        $type->removeDependency($class);
                        $type->addDependency($interface);
                    }
                }
            }
        }

        foreach ($this->interfaces as $types) {
            foreach ($types as $type) {
                foreach ($type->getUnfilteredRawDependencies() as $dependency) {
                    if ($dependency === $class) {
                        $type->removeDependency($class);
                        $type->addDependency($interface);
                    }
                }
            }
        }

        foreach ($this->functions as $function) {
            foreach ($function->getUnfilteredRawDependencies() as $dependency) {
                if ($dependency === $class) {
                    $function->removeDependency($class);
                    $function->addDependency($interface);
                }
            }
            foreach ($function->getExceptionTypes() as $exceptionType) {
                if ($exceptionType === $class) {
                    $function->removeExceptionType($class);
                    $function->addExceptionType($interface);
                }
            }
            if ($function->getReturnType() === $class) {
                $function->setReturnType($interface);
            }
        }

        foreach ($this->methods as $method) {
            foreach ($method->getUnfilteredRawDependencies() as $dependency) {
                if ($dependency === $class) {
                    $method->removeDependency($class);
                    $method->addDependency($interface);
                }
            }
            foreach ($method->getUnfilteredRawExceptionTypes() as $exceptionType) {
                if ($exceptionType === $class) {
                    $method->removeExceptionType($class);
                    $method->addExceptionType($interface);
                }
            }
            if ($method->getReturnType() === $class) {
                $method->setReturnType($interface);
            }
        }

        foreach ($this->_properties as $property) {
            if ($property->getType() === $class) {
                $property->setType($interface);
            }
        }

        foreach ($this->_parameters as $parameter) {
            if ($parameter->getType() === $class) {
                $parameter->setType($interface);
            }
        }
    }
}