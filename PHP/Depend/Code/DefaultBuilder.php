<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pmanuel-pichler.de>.
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
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/Interface.php';
require_once 'PHP/Depend/Code/NodeBuilder.php'; 
require_once 'PHP/Depend/Code/NodeIterator.php';
require_once 'PHP/Depend/Code/Function.php';
require_once 'PHP/Depend/Code/Method.php';
require_once 'PHP/Depend/Code/Package.php';
require_once 'PHP/Depend/Code/Property.php';

/**
 * Default code tree builder implementation.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Code_DefaultBuilder implements PHP_Depend_Code_NodeBuilder
{
    /**
     * Default package which contains all functions and classes with an unknown 
     * scope. 
     *
     * @type PHP_Depend_Code_Package
     * @var PHP_Depend_Code_Package $defaultPackage
     */
    protected $defaultPackage = null;
    
    /**
     * All generated {@link PHP_Depend_Code_Class} objects
     *
     * @type array<PHP_Depend_Code_Class>
     * @var array(string=>PHP_Depend_Code_Class) $classes
     */
    protected $classes = array();
    
    /**
     * All generated {@link PHP_Depend_Code_Interface} instances.
     *
     * @type array<PHP_Depend_Code_Interface>
     * @var array(string=>PHP_Depend_Code_Interface) $interfaces
     */
    protected $interfaces = array();
    
    /**
     * All generated {@link PHP_Depend_Code_Package} objects
     *
     * @type array<PHP_Depend_Code_Package>
     * @var array(string=>PHP_Depend_Code_Package) $packages
     */
    protected $packages = array();
    
    /**
     * All generated {@link PHP_Depend_Code_Function} instances.
     *
     * @type array<PHP_Depend_Code_Function>
     * @var array(string=>PHP_Depend_Code_Function) $functions
     */
    protected $functions = array();
    
    /**
     * All generated {@link PHP_Depend_Code_Method} instances.
     *
     * @type array<PHP_Depend_Code_Method>
     * @var array(PHP_Depend_Code_Method) $methods
     */
    protected $methods = array();
    
    /**
     * Constructs a new builder instance.
     */
    public function __construct()
    {
        $this->defaultPackage = new PHP_Depend_Code_Package(self::DEFAULT_PACKAGE);
        
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
        if (isset($this->classes[$name])) {
            $instance = $this->classes[$name];
        } else if (isset($this->interfaces[$name])) {
            $instance = $this->interfaces[$name];
        } else {
            $instance = $this->buildClass($name);
        }
        return $instance;
    }
    
    /**
     * Builds a new package instance.
     *
     * @param string  $name The class name.
     * @param integer $line The line number for the class declaration.
     * 
     * @return PHP_Depend_Code_Class The created class object.
     */
    public function buildClass($name, $line = 0)
    {
        if (isset($this->classes[$name])) {
            $class = $this->classes[$name];
        } else {
            
            $className   = $this->extractTypeName($name);
            $packageName = $this->extractPackageName($name);
            
            $class = new PHP_Depend_Code_Class($className, $line);
            
            $this->classes[$className] = $class;
            
            $this->buildPackage($packageName)->addType($class);
        }
        return $class;
    }
    
    /**
     * Builds a new new interface instance.
     *
     * @param string  $name The interface name.
     * @param integer $line The line number for the interface declaration.
     * 
     * @return PHP_Depend_Code_Interface The created interface object.
     */
    public function buildInterface($name, $line = 0)
    {
        $class = null;
        if (isset($this->classes[$name])) {
            $class   = $this->classes[$name];
            $package = $class->getPackage();
            
            $package->removeType($class);
            
            unset($this->classes[$name]);
            
            $name = sprintf('%s::%s', $package->getName(), $class->getName());
        }
        
        if (isset($this->interfaces[$name])) {
            $interface = $this->interfaces[$name];
        } else {
            $typeName    = $this->extractTypeName($name);
            $packageName = $this->extractPackageName($name);
            
            $interface = new PHP_Depend_Code_Interface($typeName, $line);

            $this->interfaces[$typeName] = $interface;
            
            $this->buildPackage($packageName)->addType($interface);
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
            $this->packages[$name] = new PHP_Depend_Code_Package($name);
        }
        return $this->packages[$name];
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
        return new PHP_Depend_Code_Property($name, $line);
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
            // Create new function
            $function = new PHP_Depend_Code_Function($name, $line, $sourceFile);
            // Add to default package
            $this->defaultPackage->addFunction($function);
            // Store function reference
            $this->functions[$name] = $function;
        }
        
        if ($sourceFile !== null && $function->getSourceFile() === null) {
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
        if (($pos = strpos($qualifiedName, '::')) !== false) {
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
     *   // string(6) "global"
     * </code>
     * 
     * @param string $qualifiedName The qualified PHP 5.3 class identifier.
     * 
     * @return string
     */
    protected function extractPackageName($qualifiedName)
    {
        if (($pos = strrpos($qualifiedName, '::')) !== false) {
            return substr($qualifiedName, 0, $pos);
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
        foreach ($this->classes as $type) {
            foreach ($type->getDependencies() as $dependency) {
                if ($dependency === $class) {
                    $type->removeDependency($class);
                    $type->addDependency($interface);
                }
            }
        }
    
        foreach ($this->interfaces as $type) {
            foreach ($type->getDependencies() as $dependency) {
                if ($dependency === $class) {
                    $type->removeDependency($class);
                    $type->addDependency($interface);
                }
            }
        }
    
        foreach ($this->functions as $function) {
            foreach ($function->getDependencies() as $dependency) {
                if ($dependency === $class) {
                    $function->removeDependency($class);
                    $function->addDependency($interface);
                }
            }
        }
    
        foreach ($this->methods as $method) {
            foreach ($method->getDependencies() as $dependency) {
                if ($dependency === $class) {
                    $method->removeDependency($class);
                    $method->addDependency($interface);
                }
            }
        }
    }
}