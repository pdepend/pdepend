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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Util
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

/**
 * Utility class that can be used to detect simpl scalars or internal types.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Util
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
final class PHP_Depend_Util_Type
{
    /**
     * This property contains a mapping between a unified lower case type name
     * and the corresponding PHP extension that declares this type.
     *
     * @var array(string=>string) $_typeNameToExtension
     */
    private static $_typeNameToExtension = null;

    /**
     * List of scalar php types.
     *
     * @var array(string) $_scalarTypes
     */
    private static $_scalarTypes = array(
        'array',
        'bool',
        'boolean',
        'double',
        'float',
        'int',
        'integer',
        'mixed',
        'null',
        'real',
        'resource',
        'object',
        'string',
        'void',
        'false',
        'true',
        'unknown',      // Eclipse default return type
        'unknown_type', // Eclipse default property type
    );

    /**
     * Returns <b>true</b> if the given type is internal or part of an
     * extension.
     *
     * @param string $typeName The type name.
     *
     * @return boolean
     */
    public static function isInternalType($typeName)
    {
        self::_initTypeToExtension();

        return isset(self::$_typeNameToExtension[strtolower($typeName)]);
    }

    /**
     * Returns the package/extension for the given type name. If no package
     * exists, this method will return <b>null</b>.
     *
     * @param string $typeName The type name.
     *
     * @return string
     */
    public static function getTypePackage($typeName)
    {
        self::_initTypeToExtension();

        $typeName = strtolower($typeName);
        if (isset(self::$_typeNameToExtension[$typeName])) {
            return self::$_typeNameToExtension[$typeName];
        }
        return null;
    }

    /**
     * Returns an array with all package/extension names.
     *
     * @return array(string)
     */
    public static function getInternalPackages()
    {
        self::_initTypeToExtension();

        return array_unique(array_values(self::$_typeNameToExtension));
    }

    /**
     * This method will return <b>true</b> when the given package represents a
     * php extension.
     *
     * @param string $packageName Name of a package.
     *
     * @return boolean
     */
    public static function isInternalPackage($packageName)
    {
        $packageName = strtolower($packageName);
        return in_array($packageName, self::getInternalPackages());
    }

    /**
     * This method will return <b>true</b> when the given type identifier is in
     * the list of scalar/none-object types.
     *
     * @param string $scalarType The type identifier.
     *
     * @return boolean
     */
    public static function isScalarType($scalarType)
    {
        return in_array(strtolower($scalarType), self::$_scalarTypes);
    }

    /**
     * This method reads all available classes and interfaces and checks whether
     * this type belongs to an extension or is internal. All internal and extension
     * classes are collected in an internal data structure.
     *
     * @return void
     */
    private static function _initTypeToExtension()
    {
        // Skip when already done.
        if (self::$_typeNameToExtension !== null) {
            return;
        }

        self::$_typeNameToExtension = array();

        // Collect all available classes and interfaces
        $typeNames = array_merge(get_declared_classes(), get_declared_interfaces());

        foreach ($typeNames as $typeName) {
            $reflection = new ReflectionClass($typeName);
            if ($reflection->isInternal() === false) {
                continue;
            }
            $extensionName = strtolower($reflection->getExtensionName());
            $extensionName = ($extensionName === '' ? 'standard' : $extensionName);
            $extensionName = '+' . $extensionName;

            self::$_typeNameToExtension[strtolower($typeName)] = $extensionName;
        }
    }
}
?>
