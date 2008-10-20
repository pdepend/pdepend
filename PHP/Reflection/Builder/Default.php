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
require_once 'PHP/Reflection/AST/ArrayExpression.php';
require_once 'PHP/Reflection/AST/ArrayElement.php';
require_once 'PHP/Reflection/AST/Class.php';
require_once 'PHP/Reflection/AST/ClassOrInterfaceConstant.php';
require_once 'PHP/Reflection/AST/ClassOrInterfaceConstantValue.php';
require_once 'PHP/Reflection/AST/ClassOrInterfaceProxy.php';
require_once 'PHP/Reflection/AST/ConstantValue.php';
require_once 'PHP/Reflection/AST/Interface.php';
require_once 'PHP/Reflection/AST/Iterator.php';
require_once 'PHP/Reflection/AST/Function.php';
require_once 'PHP/Reflection/AST/MemberFalseValue.php';
require_once 'PHP/Reflection/AST/MemberNullValue.php';
require_once 'PHP/Reflection/AST/MemberNumericValue.php';
require_once 'PHP/Reflection/AST/MemberScalarValue.php';
require_once 'PHP/Reflection/AST/MemberTrueValue.php';
require_once 'PHP/Reflection/AST/Method.php';
require_once 'PHP/Reflection/AST/Package.php';
require_once 'PHP/Reflection/AST/Parameter.php';
require_once 'PHP/Reflection/AST/Property.php';

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
     * @type PHP_Reflection_AST_Package
     * @var PHP_Reflection_AST_Package $defaultPackage
     */
    protected $defaultPackage = null;
    
    /**
     * Default source file that acts as a dummy.
     *
     * @type PHP_Reflection_AST_File
     * @var PHP_Reflection_AST_File $defaultFile
     */
    protected $defaultFile = null;
    
    /**
     * All generated {@link PHP_Reflection_AST_Class} objects
     *
     * @type array<PHP_Reflection_AST_Class>
     * @var array(string=>PHP_Reflection_AST_Class) $classes
     */
    protected $classes = array();
    
    /**
     * All generated {@link PHP_Reflection_AST_Interface} instances.
     *
     * @type array<PHP_Reflection_AST_Interface>
     * @var array(string=>PHP_Reflection_AST_Interface) $interfaces
     */
    protected $interfaces = array();
    
    /**
     * All generated {@link PHP_Reflection_AST_Package} objects
     *
     * @type array<PHP_Reflection_AST_Package>
     * @var array(string=>PHP_Reflection_AST_Package) $packages
     */
    protected $packages = array();
    
    /**
     * All generated {@link PHP_Reflection_AST_Function} instances.
     *
     * @var array(string=>PHP_Reflection_AST_Function) $functions
     */
    protected $functions = array();
    
    /**
     * All generated {@link PHP_Reflection_AST_Method} instances.
     *
     * @var array(PHP_Reflection_AST_Method) $methods
     */
    protected $methods = array();
    
    /**
     * All generated {@link PHP_Reflection_AST_Parameter} instances.
     *
     * @var array(PHP_Reflection_AST_Parameter) $_parameters
     */
    private $_parameters = array();
    
    /**
     * All generated {@link PHP_Reflection_AST_Property} instances.
     *
     * @var array(PHP_Reflection_AST_Property) $_properties
     */
    private $_properties = array();
    
    /**
     * All generated {@link PHP_Reflection_AST_ClassOrInterfaceConstant} instances.
     *
     * @type array<PHP_Reflection_AST_ClassOrInterfaceConstant>
     * @var array(PHP_Reflection_AST_ClassOrInterfaceConstant) $_typeConstants
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
     * @var array(PHP_Reflection_AST_ClassOrInterfaceProxy) $_proxyCache
     */
    private $_proxyCache = array();
    
    /**
     * Constructs a new builder instance.
     */
    public function __construct()
    {
        $this->defaultPackage = new PHP_Reflection_AST_Package(self::GLOBAL_PACKAGE);
        $this->defaultFile    = new PHP_Reflection_AST_File(null);
        
        $this->packages[self::GLOBAL_PACKAGE] = $this->defaultPackage;
        
        $this->_internalTypes = PHP_Reflection_InternalTypes::getInstance();
    }
    
    /**
     * Generic build class for classes and interfaces. This method should be used
     * in cases when it is not clear what type is used in the current situation.
     * This could happen if the parser analyzes a method signature. The default 
     * return type is {@link PHP_Reflection_AST_Class}, but if there is already
     * an interface for this name, the method will return this instance.
     * 
     * <code>
     *   $builder->buildInterface('PHP_ReflectionI');
     * 
     *   // Returns an instance of PHP_Reflection_AST_Interface
     *   $builder->buildClassOrInterface('PHP_ReflectionI');
     * 
     *   // Returns an instance of PHP_Reflection_AST_Class
     *   $builder->buildClassOrInterface('PHP_Reflection');
     * </code>
     *
     * @param string $name The class name.
     * 
     * @return PHP_Reflection_AST_Class|PHP_Reflection_AST_Interface 
     *         The created class or interface instance.
     */
    public function buildProxySubject($name)
    {
        $cls = $this->extractTypeName($name);
        $pkg = $this->extractPackageName($name);
        
        $typeID = strtolower($cls);
        if (isset($this->classes[$typeID][$pkg])) {
            $instance = $this->classes[$typeID][$pkg];
        } else if (isset($this->interfaces[$typeID][$pkg])) {
            $instance = $this->interfaces[$typeID][$pkg];
        } else if ($pkg === self::GLOBAL_PACKAGE) {
            if (isset($this->classes[$typeID])) {
                $instance = reset($this->classes[$typeID]);
            } else if (isset($this->interfaces[$typeID])) {
                $instance = reset($this->interfaces[$typeID]);
            } else {
                $instance = $this->buildClass($name);
            }
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
     * @return PHP_Reflection_AST_Class The created class object.
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
            $class = new PHP_Reflection_AST_Class($cls, $line);
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
     * @return PHP_Reflection_AST_ClassOrInterfaceConstant The created constant object.
     */
    public function buildTypeConstant($name)
    {
        // Create new constant instance.
        $constant = new PHP_Reflection_AST_ClassOrInterfaceConstant($name);
        
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
     * @return PHP_Reflection_AST_Interface The created interface object.
     */
    public function buildInterface($name, $line = 0)
    {
        $ife = $this->extractTypeName($name);
        $pkg = $this->extractPackageName($name);
        
        $localName = strtolower($ife);
        
        $class = null;
        if (isset($this->classes[$localName][$pkg])) {
            $class   = $this->classes[$localName][$pkg];
            $package = $class->getPackage();
            
            // Only reparent if the found class is part of the default package
            if ($package === $this->defaultPackage) {
                $package->removeType($class);
            
                unset($this->classes[$localName][$package->getName()]);
                
                if (count($this->classes[$localName]) === 0) {
                    unset($this->classes[$localName]);
                }
            } else {
                // Unset class reference
                $class = null;
            }
        }
        
        // 1) check for an equal interface version
        if (isset($this->interfaces[$localName][$pkg])) {
            $interface = $this->interfaces[$localName][$pkg];
            
            // 2) check for a default version that could be replaced
        } else if (isset($this->interfaces[$localName][self::GLOBAL_PACKAGE])) {
            $interface = $this->interfaces[$localName][self::GLOBAL_PACKAGE];
            
            unset($this->interfaces[$localName][self::GLOBAL_PACKAGE]);
            
            $this->interfaces[$localName][$pkg] = $interface;
            
            $this->buildPackage($pkg)->addType($interface);
            
            // 3) check for any version that could be used instead of the default
        } else if (isset($this->interfaces[$localName]) && $this->isDefault($pkg)) {
            $interface = reset($this->interfaces[$localName]);
            
            // 4) Create a new interface for the given package
        } else {
            // Create a new interface instance
            $interface = new PHP_Reflection_AST_Interface($ife, $line);
            $interface->setSourceFile($this->defaultFile);

            // Store interface reference
            $this->interfaces[$localName][$pkg] = $interface;
            
            // Append interface to package
            $this->buildPackage($pkg)->addType($interface);
        }
        
        return $interface;
    }
    
    /**
     * Builds a new method instance.
     *
     * @param string  $name The method name.
     * @param integer $line The line number with the method declaration.
     * 
     * @return PHP_Reflection_AST_Method The created class method object.
     */
    public function buildMethod($name, $line = 0)
    {
        // Create a new method instance
        $method = new PHP_Reflection_AST_Method($name, $line);
        
        // Store instance an local map
        $this->methods[] = $method;
        
        return $method;
    }
    
    /**
     * Builds a new package instance.
     *
     * @param string $name The package name.
     * 
     * @return PHP_Reflection_AST_Package The created package object.
     */
    public function buildPackage($name)
    {
        if (!isset($this->packages[$name])) {
            $this->packages[$name] = new PHP_Reflection_AST_Package($name);
        }
        return $this->packages[$name];
    }
    
    /**
     * Builds a new parameter instance.
     *
     * @param string  $name The parameter variable name.
     * @param integer $line The line number with the parameter declaration.
     * 
     * @return PHP_Reflection_AST_Parameter The created parameter instance.
     */
    public function buildParameter($name, $line = 0)
    {
        // Create a new parameter instance
        $parameter = new PHP_Reflection_AST_Parameter($name, $line);
        
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
     * @return PHP_Reflection_AST_Property The created property instance.
     */
    public function buildProperty($name, $line = 0)
    {
        // Create new property instance.
        $property = new PHP_Reflection_AST_Property($name, $line);
        
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
     * @return PHP_Reflection_AST_Function The function instance.
     */
    public function buildFunction($name, $line = 0)
    {
        if (isset($this->functions[$name])) {
            $function = $this->functions[$name];
        } else {
            // Create new function
            $function = new PHP_Reflection_AST_Function($name, $line);
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
     * @return PHP_Reflection_AST_ArrayExpression
     */
    public function buildArrayExpression()
    {
        return new PHP_Reflection_AST_ArrayExpression();
    }
    
    /**
     * Builds an array element instance.
     *
     * @return PHP_Reflection_AST_ArrayElement
     */
    public function buildArrayElement()
    {
        return new PHP_Reflection_AST_ArrayElement();
    }
    
    /**
     * Builds a constant reference instance.
     * 
     * @param string $identifier The constant identifier.
     *
     * @return PHP_Reflection_AST_ConstantValue
     */
    public function buildConstantValue($identifier)
    {
        return new PHP_Reflection_AST_ConstantValue($identifier);
    }
    
    /**
     * Builds a class or interface constant reference instance.
     *
     * @param PHP_Reflection_AST_ClassOrInterfaceI $owner      The owner node.
     * @param string                               $identifier The constant name.
     * 
     * @return PHP_Reflection_AST_ClassOrInterfaceConstantValue
     */
    public function buildClassOrInterfaceConstantValue(
                    PHP_Reflection_AST_ClassOrInterfaceI $owner, $identifier)
    {
        return new PHP_Reflection_AST_ClassOrInterfaceConstantValue($owner, $identifier);
    }
    
    /**
     * Builds a class or interface proxy instance.
     *
     * The identifier of the proxied class or interface.
     * 
     * @return PHP_Reflection_AST_ClassOrInterfaceProxy
     */
    public function buildClassOrInterfaceProxy($identifier)
    {
        $proxyID = strtolower($identifier);
        if (!isset($this->_proxyCache[$proxyID])) {
            // Create a new node proxy
            $proxy = new PHP_Reflection_AST_ClassOrInterfaceProxy($this, $identifier); 
            // Cache proxy instance
            $this->_proxyCache[$proxyID] = $proxy;
        }
        return $this->_proxyCache[$proxyID]; 
    }
    
    /**
     * Builds a new null value instance.
     *
     * @return PHP_Reflection_AST_MemberNullValue
     */
    public function buildNullValue()
    {
        return PHP_Reflection_AST_MemberNullValue::flyweight();
    }
    
    /**
     * Builds a new true value instance.
     *
     * @return PHP_Reflection_AST_MemberTrueValue
     */
    public function buildTrueValue()
    {
        return PHP_Reflection_AST_MemberTrueValue::flyweight();
    }
    
    /**
     * Builds a new false value instance.
     *
     * @return PHP_Reflection_AST_MemberFalseValue
     */
    public function buildFalseValue()
    {
        return PHP_Reflection_AST_MemberFalseValue::flyweight();
    }

    /**
     * Builds a new numeric value instance.
     *
     * @param integer $type     The type of this value.
     * @param string  $value    The string representation of the php value.
     * @param boolean $negative Is this numeric value negative?
     * 
     * @return PHP_Reflection_AST_MemberNumericValue
     */
    public function buildNumericValue($type, $value, $negative)
    {
        return new PHP_Reflection_AST_MemberNumericValue($type, $value, $negative);
    }
    
    /**
     * Builds a new scalar value instance.
     *
     * @param integer $type  The type of this value.
     * @param string  $value The string representation of the php value.
     * 
     * @return PHP_Reflection_AST_MemberScalarValue
     */
    public function buildScalarValue($type, $value = null)
    {
        return new PHP_Reflection_AST_MemberScalarValue($type, $value);
    }
    
    /**
     * Returns an iterator with all generated {@link PHP_Reflection_AST_Package}
     * objects.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    public function getIterator()
    {
        return $this->getPackages();
    }
    
    /**
     * Returns an iterator with all generated {@link PHP_Reflection_AST_Package}
     * objects.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    public function getPackages()
    {
        // Finally realize proxies
        foreach ($this->_proxyCache as $proxy) {
            $package = $proxy->getPackage();
            if (in_array($package, $this->packages, true) === false) {
                $this->packages[] = $package;
            }
        }
        
        // Create a package array copy
        $packages = $this->packages;
        
        // Remove default package if empty
        if ($this->defaultPackage->getTypes()->count() === 0  
         && $this->defaultPackage->getFunctions()->count() === 0) {

            unset($packages[self::GLOBAL_PACKAGE]);
        }
        
        return new PHP_Reflection_AST_Iterator($packages);
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
        if (preg_match('#^::[a-z_][a-z0-9_]*$#i', $name)) {
            $name = substr($name, 2);
        }
        
        if (($pos = strrpos($name, '::')) !== false) {
            return substr($name, 0, $pos);
        } else if ($this->_internalTypes->isInternal($name)) {
            return $this->_internalTypes->getTypePackage($name);
        }
        return self::GLOBAL_PACKAGE; 
    }
}