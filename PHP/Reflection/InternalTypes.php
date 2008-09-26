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
 * @category  PHP
 * @package   PHP_Reflection
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

/**
 * This utility class provides access to all internal classes. 
 *
 * @category  PHP
 * @package   PHP_Reflection
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Reflection_InternalTypes
{
    /**
     * Singleton instance for this class.
     *
     * @type PHP_Reflection_InternalTypes
     * @var PHP_Reflection_InternalTypes $_instance
     */
    private static $_instance = null;
    
    /**
     * Singleton method for the internal types class.
     *
     * @return PHP_Reflection_InternalTypes
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new PHP_Reflection_InternalTypes();
        }
        return self::$_instance;
    }
    
    /**
     * Map of all internal types.
     *
     * @type array<array>
     * @var array(string=>array) $_types
     */
    private $_types = array();
    
    /**
     * List of packages/extensions with classes or interfaces.
     *
     * @type array<string>
     * @var array(string=>string) $_packages
     */
    private $_packages = array();
    
    /**
     * Constructs a new internal types instance.
     */
    private function __construct()
    {
        $types = array_merge(get_declared_classes(), get_declared_interfaces());
        foreach ($types as $type) {
            $reflection = new ReflectionClass($type);
            if ($reflection->isInternal() === false) {
                continue;
            }
            $extension = $reflection->getExtensionName();
            if ($extension === false) {
                $extension = 'standard';
            }
            $extension = '+' . strtolower($extension);
    
            if (!isset($packages[$extension])) {
                $packages[$extension] = array();
            }
            $this->_types[strtolower($type)] = array(
                'package'  =>  $extension,
                'name'     =>  $type
            );
            
            $this->_packages[$extension] = true;
        }
    }
    
    /**
     * Returns <b>true</b> if the given type is internal or part of an
     * extension.
     *
     * @param string $typeName The type name.
     * 
     * @return boolean
     */
    public function isInternal($typeName)
    {
        return isset($this->_types[strtolower($typeName)]);
    }
    
    /**
     * Returns an array with all package/extension names.
     *
     * @return array(string)
     */
    public function getInternalPackages()
    {
        return array_keys($this->_packages);
    }
    
    /**
     * Returns the package/extension for the given type name. If no package
     * exists, this method will return <b>null</b>.
     *
     * @param string $typeName The type name.
     * 
     * @return string
     */
    public function getTypePackage($typeName)
    {
        $package = null;
        if ($this->isInternal($typeName)) {
            $package = $this->_types[strtolower($typeName)]['package'];
        }
        return $package;
    }
}