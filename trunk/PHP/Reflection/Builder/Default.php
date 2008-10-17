<?php
/**
 * This file is part of PHP_Reflection.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pdepend.org>.
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
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Builder
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Reflection/InternalTypes.php';
require_once 'PHP/Reflection/BuilderI.php'; 
require_once 'PHP/Reflection/Ast/ArrayExpression.php';
require_once 'PHP/Reflection/Ast/ArrayElement.php';
require_once 'PHP/Reflection/Ast/Class.php';
require_once 'PHP/Reflection/Ast/ClassOrInterfaceConstant.php';
require_once 'PHP/Reflection/Ast/ClassOrInterfaceConstantValue.php';
require_once 'PHP/Reflection/Ast/ClassOrInterfaceProxy.php';
require_once 'PHP/Reflection/Ast/ConstantValue.php';
require_once 'PHP/Reflection/Ast/Interface.php';
require_once 'PHP/Reflection/Ast/Iterator.php';
require_once 'PHP/Reflection/Ast/Function.php';
require_once 'PHP/Reflection/Ast/MemberFalseValue.php';
require_once 'PHP/Reflection/Ast/MemberNullValue.php';
require_once 'PHP/Reflection/Ast/MemberNumericValue.php';
require_once 'PHP/Reflection/Ast/MemberScalarValue.php';
require_once 'PHP/Reflection/Ast/MemberTrueValue.php';
require_once 'PHP/Reflection/Ast/Method.php';
require_once 'PHP/Reflection/Ast/Package.php';
require_once 'PHP/Reflection/Ast/Parameter.php';
require_once 'PHP/Reflection/Ast/Property.php';

/**
 * Default code tree builder implementation.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Builder
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_Builder_Default implements PHP_Reflection_BuilderI
{
    /**
     * Default package which contains all functions and classes with an unknown 
     * scope. 
     *
     * @type PHP_Reflection_Ast_Package
     * @var PHP_Reflection_Ast_Package $defaultPackage
     */
    protected $defaultPackage = null;
    
    /**
     * Default source file that acts as a dummy.
     *
     * @type PHP_Reflection_Ast_File
     * @var PHP_Reflection_Ast_File $defaultFile
     */
    protected $defaultFile = null;
    
    /**
     * All generated {@link PHP_Reflection_Ast_Class} objects
     *
     * @type array<PHP_Reflection_Ast_Class>
     * @var array(string=>PHP_Reflection_Ast_Class) $classes
     */
    protected $classes = array();
    
    /**
     * All generated {@link PHP_Reflection_Ast_Interface} instances.
     *
     * @type array<PHP_Reflection_Ast_Interface>
     * @var array(string=>PHP_Reflection_Ast_Interface) $interfaces
     */
    protected $interfaces = array();
    
    /**
     * All generated {@link PHP_Reflection_Ast_Package} objects
     *
     * @type array<PHP_Reflection_Ast_Package>
     * @var array(string=>PHP_Reflection_Ast_Package) $packages
     */
    protected $packages = array();
    
    /**
     * All generated {@link PHP_Reflection_Ast_Function} instances.
     *
     * @var array(string=>PHP_Reflection_Ast_Function) $functions
     */
    protected $functions = array();
    
    /**
     * All generated {@link PHP_Reflection_Ast_Method} instances.
     *
     * @var array(PHP_Reflection_Ast_Method) $methods
     */
    protected $methods = array();
    
    /**
     * All generated {@link PHP_Reflection_Ast_Parameter} instances.
     *
     * @var array(PHP_Reflection_Ast_Parameter) $_parameters
     */
    private $_parameters = array();
    
    /**
     * All generated {@link PHP_Reflection_Ast_Property} instances.
     *
     * @var array(PHP_Reflection_Ast_Property) $_properties
     */
    private $_properties = array();
    
    /**
     * All generated {@link PHP_Reflection_Ast_ClassOrInterfaceConstant} instances.
     *
     * @type array<PHP_Reflection_Ast_ClassOrInterfaceConstant>
     * @var array(PHP_Reflection_Ast_ClassOrInterfaceConstant) $_typeConstants
     */
    private $_typeConstants = array();
    
    /**
     * The internal types class.
     *
     * @type PHP_Reflection_InternalTypes
     * @var PHP_Reflection_InternalTypes $_internalTypes
     */
    private $_internalTypes = null;
    
    /**
     * Cache for already created class or interface proxy instances.
     * 
     * @var array(PHP_Reflection_Ast_ClassOrInterfaceProxy) $_proxyCache
     */
    private $_proxyCache = array();
    
    /**
     * Constructs a new builder instance.
     */
    public function __construct()
    {
        $this->defaultPackage = new PHP_Reflection_Ast_Package(self::GLOBAL_PACKAGE);
        $this->defaultFile    = new PHP_Reflection_Ast_File(null);
        
        $this->packages[self::GLOBAL_PACKAGE] = $this->defaultPackage;
        
        $this->_internalTypes = PHP_Reflection_InternalTypes::getInstance();
    }
    
    /**
     * Generic build class for classes and interfaces. This method should be used
     * in cases when it is not clear what type is used in the current situation.
     * This could happen if the parser analyzes a method signature. The default 
     * return type is {@link PHP_Reflection_Ast_Class}, but if there is already
     * an interface for this name, the method will return this instance.
     * 
     * <code>
     *   $builder->buildInterface('PHP_ReflectionI');
     * 
     *   // Returns an instance of PHP_Reflection_Ast_Interface
     *   $builder->buildClassOrInterface('PHP_ReflectionI');
     * 
     *   // Returns an instance of PHP_Reflection_Ast_Class
     *   $builder->buildClassOrInterface('PHP_Reflection');
     * </code>
     *
     * @param string $name The class name.
     * 
     * @return PHP_Reflection_Ast_Class|PHP_Reflection_Ast_Interface 
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
     * @return PHP_Reflection_Ast_Class The created class object.
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
        } else if (isset($this->classes[$typeID][self::GLOBAL_PACKAGE])) {
            $class = $this->classes[$typeID][self::GLOBAL_PACKAGE];
            
            unset($this->classes[$typeID][self::GLOBAL_PACKAGE]);
            
            $this->classes[$typeID][$pkg] = $class;
            
            $this->buildPackage($pkg)->addType($class);
            
            // 3) check for any version that could be used instead of the default
        } else if (isset($this->classes[$typeID]) && $this->isDefault($pkg)) {
            $class = reset($this->classes[$typeID]);
            
            // 4) Create a new class for the given package
        } else {
            
            // Create a new class instance
            $class = new PHP_Reflection_Ast_Class($cls, $line);
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
     * @return PHP_Reflection_Ast_ClassOrInterfaceConstant The created constant object.
     */
    public function buildTypeConstant($name)
    {
        // Create new constant instance.
        $constant = new PHP_Reflection_Ast_ClassOrInterfaceConstant($name);
        
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
     * @return PHP_Reflection_Ast_Interface The created interface object.
     */
    public function buildInterface($name, $line = 0)
    {
        $ife = $this->extractTypeName($name);
        $pkg = $this->extractPackageName($name);
        
        $typeID = strtolower($ife);
        
        $class = null;
        if (isset($this->classes[$typeID][$pkg])) {
            $class = $this->classes[$typeID][$pkg];
        } else if (isset($this->classes[$typeID][self::GLOBAL_PACKAGE])) {
            // TODO: Implement something like: allwaysIsClass(),
            //       This could be usefull for class names detected by 'new ...'
            $class = $this->classes[$typeID][self::GLOBAL_PACKAGE];
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
        } else if (isset($this->interfaces[$typeID][self::GLOBAL_PACKAGE])) {
            $interface = $this->interfaces[$typeID][self::GLOBAL_PACKAGE];
            
            unset($this->interfaces[$typeID][self::GLOBAL_PACKAGE]);
            
            $this->interfaces[$typeID][$pkg] = $interface;
            
            $this->buildPackage($pkg)->addType($interface);
            
            // 3) check for any version that could be used instead of the default
        } else if (isset($this->interfaces[$typeID]) && $this->isDefault($pkg)) {
            $interface = reset($this->interfaces[$typeID]);
            
            // 4) Create a new interface for the given package
        } else {
            // Create a new interface instance
            $interface = new PHP_Reflection_Ast_Interface($ife, $line);
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
     * @return PHP_Reflection_Ast_Method The created class method object.
     */
    public function buildMethod($name, $line = 0)
    {
        // Create a new method instance
        $method = new PHP_Reflection_Ast_Method($name, $line);
        
        // Store instance an local map
        $this->methods[] = $method;
        
        return $method;
    }
    
    /**
     * Builds a new package instance.
     *
     * @param string $name The package name.
     * 
     * @return PHP_Reflection_Ast_Package The created package object.
     */
    public function buildPackage($name)
    {
        if (!isset($this->packages[$name])) {
            $this->packages[$name] = new PHP_Reflection_Ast_Package($name);
        }
        return $this->packages[$name];
    }
    
    /**
     * Builds a new parameter instance.
     *
     * @param string  $name The parameter variable name.
     * @param integer $line The line number with the parameter declaration.
     * 
     * @return PHP_Reflection_Ast_Parameter The created parameter instance.
     */
    public function buildParameter($name, $line = 0)
    {
        // Create a new parameter instance
        $parameter = new PHP_Reflection_Ast_Parameter($name, $line);
        
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
     * @return PHP_Reflection_Ast_Property The created property instance.
     */
    public function buildProperty($name, $line = 0)
    {
        // Create new property instance.
        $property = new PHP_Reflection_Ast_Property($name, $line);
        
        // Store local reference
        $this->_properties[] = $property;
        
        return $property;
    }
    
    /**
     * Builds a new function instance.
     *
     * @param string  $name The function name.
     * @param integer $line The line number with the function declaration.
     * 
     * @return PHP_Reflection_Ast_Function The function instance.
     */
    public function buildFunction($name, $line = 0)
    {
        if (isset($this->functions[$name])) {
            $function = $this->functions[$name];
        } else {
            // Create new function
            $function = new PHP_Reflection_Ast_Function($name, $line);
            $function->setSourceFile($this->defaultFile);
            
            // Add to default package
            $this->defaultPackage->addFunction($function);
            // Store function reference
            $this->functions[$name] = $function;
        }
        
        return $function;
    }
    
    /**
     * Builds a new array value instance.
     *
     * @return PHP_Reflection_Ast_ArrayExpression
     */
    public function buildArrayExpression()
    {
        return new PHP_Reflection_Ast_ArrayExpression();
    }
    
    /**
     * Builds an array element instance.
     *
     * @return PHP_Reflection_Ast_ArrayElement
     */
    public function buildArrayElement()
    {
        return new PHP_Reflection_Ast_ArrayElement();
    }
    
    /**
     * Builds a constant reference instance.
     * 
     * @param string $identifier The constant identifier.
     *
     * @return PHP_Reflection_Ast_ConstantValue
     */
    public function buildConstantValue($identifier)
    {
        return new PHP_Reflection_Ast_ConstantValue($identifier);
    }
    
    /**
     * Builds a class or interface constant reference instance.
     *
     * @param PHP_Reflection_Ast_ClassOrInterfaceI $owner      The owner node.
     * @param string                               $identifier The constant name.
     * 
     * @return PHP_Reflection_Ast_ClassOrInterfaceConstantValue
     */
    public function buildClassOrInterfaceConstantValue(
                    PHP_Reflection_Ast_ClassOrInterfaceI $owner, $identifier)
    {
        return new PHP_Reflection_Ast_ClassOrInterfaceConstantValue($owner, $identifier);
    }
    
    /**
     * Builds a class or interface proxy instance.
     *
     * The identifier of the proxied class or interface.
     * 
     * @return PHP_Reflection_Ast_ClassOrInterfaceProxy
     */
    public function buildClassOrInterfaceProxy($identifier)
    {
        $proxyID = strtolower($identifier);
        if (!isset($this->_proxyCache[$proxyID])) {
            // Create a new node proxy
            $proxy = new PHP_Reflection_Ast_ClassOrInterfaceProxy($this, $identifier); 
            // Cache proxy instance
            $this->_proxyCache[$proxyID] = $proxy;
        }
        return $this->_proxyCache[$proxyID]; 
    }
    
    /**
     * Builds a new null value instance.
     *
     * @return PHP_Reflection_Ast_MemberNullValue
     */
    public function buildNullValue()
    {
        return PHP_Reflection_Ast_MemberNullValue::flyweight();
    }
    
    /**
     * Builds a new true value instance.
     *
     * @return PHP_Reflection_Ast_MemberTrueValue
     */
    public function buildTrueValue()
    {
        return PHP_Reflection_Ast_MemberTrueValue::flyweight();
    }
    
    /**
     * Builds a new false value instance.
     *
     * @return PHP_Reflection_Ast_MemberFalseValue
     */
    public function buildFalseValue()
    {
        return PHP_Reflection_Ast_MemberFalseValue::flyweight();
    }

    /**
     * Builds a new numeric value instance.
     *
     * @param integer $type     The type of this value.
     * @param string  $value    The string representation of the php value.
     * @param boolean $negative Is this numeric value negative?
     * 
     * @return PHP_Reflection_Ast_MemberNumericValue
     */
    public function buildNumericValue($type, $value, $negative)
    {
        return new PHP_Reflection_Ast_MemberNumericValue($type, $value, $negative);
    }
    
    /**
     * Builds a new scalar value instance.
     *
     * @param integer $type  The type of this value.
     * @param string  $value The string representation of the php value.
     * 
     * @return PHP_Reflection_Ast_MemberScalarValue
     */
    public function buildScalarValue($type, $value = null)
    {
        return new PHP_Reflection_Ast_MemberScalarValue($type, $value);
    }
    
    /**
     * Returns an iterator with all generated {@link PHP_Reflection_Ast_Package}
     * objects.
     *
     * @return PHP_Reflection_Ast_Iterator
     */
    public function getIterator()
    {
        return $this->getPackages();
    }
    
    /**
     * Returns an iterator with all generated {@link PHP_Reflection_Ast_Package}
     * objects.
     *
     * @return PHP_Reflection_Ast_Iterator
     */
    public function getPackages()
    {
        // Create a package array copy
        $packages = $this->packages;
        
        // Remove default package if empty
        if ($this->defaultPackage->getTypes()->count() === 0  
         && $this->defaultPackage->getFunctions()->count() === 0) {

            unset($packages[self::GLOBAL_PACKAGE]);
        }
        return new PHP_Reflection_Ast_Iterator($packages);
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
        return ($packageName === self::GLOBAL_PACKAGE);
    }
    
    /**
     * Extracts the type name of a qualified PHP 5.3 type identifier.
     *
     * <code>
     *   $typeName = $this->extractTypeName('foo::bar::foobar');
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
        if (($pos = strrpos($qualifiedName, '::')) !== false) {
            return substr($qualifiedName, $pos + 2);
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
     *   $packageName = $this->extractPackageName('foo::bar::foobar');
     *   var_dump($packageName);
     *   // Results in:
     *   // string(8) "foo::bar"
     * 
     *   $packageName = $this->extractPackageName('foobar');
     *   var_dump($packageName);
     *   // Results in:
     *   // string(6) "+global"
     * 
     *   $packageName = $this->extractPackageName('::foobar');
     *   var_dump($packageName);
     *   // Results in:
     *   // string(6) "+global"
     * 
     *   $packageName = $this->extractPackageName('::Iterator');
     *   var_dump($packageName);
     *   // Results in:
     *   // string(6) "+spl"
     * </code>
     * 
     * @param string $qualifiedName The qualified PHP 5.3 class identifier.
     * 
     * @return string
     */
    protected function extractPackageName($qualifiedName)
    {
        $name = $qualifiedName;
        if (preg_match('#^::[a-z_][a-z0-9_]+$#i', $name)) {
            $name = substr($name, 2);
        }
        
        if (($pos = strrpos($name, '::')) !== false) {
            return substr($name, 0, $pos);
        } else if ($this->_internalTypes->isInternal($name)) {
            return $this->_internalTypes->getTypePackage($name);
        }
        return self::GLOBAL_PACKAGE; 
    }
    
    /**
     * This method will replace all existing references to the given <b>$class</b>
     * instance with the interface instance. 
     * 
     * <code>
     *   $class1 = $builder->buildClass('PHP_Reflection');
     *   $class2 = $builder->buildClassOrInterface('PHP_ReflectionI');
     * 
     *   $class1->addDependency($class2);
     * 
     *   $builder->buildInterface('PHP_ReflectionI');
     * 
     *   var_dump($class1->getDependencies());
     *   // Results in
     *   // array(1) {
     *   //   [0]=>
     *   //   object(PHP_Reflection_Ast_Interface)#1 (0) {
     *   //   }
     *   // }
     * </code>
     *
     * @param PHP_Reflection_Ast_Class     $class The old context class instance.
     * @param PHP_Reflection_Ast_Interface $iface The new interface instance.
     * 
     * @return void
     */
    protected function replaceClassReferences(PHP_Reflection_Ast_Class $class,
                                              PHP_Reflection_Ast_Interface $iface)
    {
        foreach ($this->functions as $function) {
            foreach ($function->getDependencies() as $dependency) {
                if ($dependency === $class) {
                    $function->removeDependency($class);
                    $function->addDependency($iface);
                }
            }
            foreach ($function->getExceptionTypes() as $exceptionType) {
                if ($exceptionType === $class) {
                    $function->removeExceptionType($class);
                    $function->addExceptionType($iface);
                }
            }
            if ($function->getReturnType() === $class) {
                $function->setReturnType($iface);
            }
        }
    
        foreach ($this->methods as $method) {
            foreach ($method->getDependencies() as $dependency) {
                if ($dependency === $class) {
                    $method->removeDependency($class);
                    $method->addDependency($iface);
                }
            }
            foreach ($method->getExceptionTypes() as $exceptionType) {
                if ($exceptionType === $class) {
                    $method->removeExceptionType($class);
                    $method->addExceptionType($iface);
                }
            }
            if ($method->getReturnType() === $class) {
                $method->setReturnType($iface);
            }
        }
        
        foreach ($this->_properties as $property) {
            if ($property->getType() === $class) {
                $property->setType($iface);
            }
        }
        
        foreach ($this->_parameters as $parameter) {
            if ($parameter->getType() === $class) {
                $parameter->setType($iface);
            }
        }
    }
}