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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/NodeBuilder.php'; 
require_once 'PHP/Depend/Code/NodeIterator.php';
require_once 'PHP/Depend/Code/Function.php';
require_once 'PHP/Depend/Code/Method.php';
require_once 'PHP/Depend/Code/Package.php';

/**
 * Default code tree builder implementation.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
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
     * All generated {@link PHP_Depend_Code_Package} objects
     *
     * @type array<PHP_Depend_Code_Package>
     * @var array(string=>PHP_Depend_Code_Package) $packages
     */
    protected $packages = array();
    
    /**
     * Constructs a new builder instance.
     */
    public function __construct()
    {
        $this->defaultPackage = new PHP_Depend_Code_Package(self::DEFAULT_PACKAGE);
        
        $this->packages[self::DEFAULT_PACKAGE] = $this->defaultPackage;
    }
    
    /**
     * Builds a new package instance.
     *
     * @param string  $name       The class name.
     * @param integer $line       The line number for the class declaration.
     * @param string  $sourceFile The source file for the class.
     * 
     * @return PHP_Depend_Code_Class The created class object.
     */
    public function buildClass($name, $line = 0, $sourceFile = null)
    {
        if (isset($this->classes[$name])) {
            $class = $this->classes[$name];
        } else {
            
            $className   = $name;
            $packageName = self::DEFAULT_PACKAGE;
            
            if (strpos($className, '::') !== false) {
                $parts = explode('::', $className);
                
                $className   = array_pop($parts);
                $packageName = join('::', $parts); 
            }
            
            $class = new PHP_Depend_Code_Class($className, $line, $sourceFile);
            
            $this->classes[$name] = $class;
            
            $this->buildPackage($packageName)->addClass($class);
        }
        if ($sourceFile !== null) {
            $class->setSourceFile($sourceFile);
        }
        return $class;
    }
    
    /**
     * Builds a new method instance.
     *
     * @param string  $name The method name.
     * @param integer $line The line number with the method declaration.
     * 
     * @return PHP_Depend_Code_Method The created class method object.
     */
    public function buildMethod($name, $line)
    {
        return new PHP_Depend_Code_Method($name, $line);
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
     * Builds a new function instance.
     *
     * @param string  $name The function name.
     * @param integer $line The line number with the function declaration.
     * 
     * @return PHP_Depend_Code_Function The function instance.
     */
    public function buildFunction($name, $line)
    {
        // Create new function
        $function = new PHP_Depend_Code_Function($name, $line);
        // Add to default package
        $this->defaultPackage->addFunction($function);
        
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
        if ($this->defaultPackage->getClasses()->count() === 0 
         && $this->defaultPackage->getFunctions()->count() === 0) {

            unset($packages[self::DEFAULT_PACKAGE]);
        }
        return new PHP_Depend_Code_NodeIterator($packages);
    }
}