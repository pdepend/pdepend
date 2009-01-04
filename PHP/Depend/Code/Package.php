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
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/NodeI.php';
require_once 'PHP/Depend/Code/NodeIterator.php';
require_once 'PHP/Depend/Util/UUID.php';

/**
 * Represents a php package node.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Code_Package implements PHP_Depend_Code_NodeI
{
    /**
     * The package name.
     *
     * @var string $name
     */
    protected $name = '';

    /**
     * The unique identifier for this function.
     *
     * @var PHP_Depend_Util_UUID $uuid
     */
    protected $uuid = null;

    /**
     * List of all {@link PHP_Depend_Code_AbstractType} objects for this package.
     *
     * @var array(PHP_Depend_Code_AbstractType) $types
     */
    protected $types = array();

    /**
     * List of all standalone {@link PHP_Depend_Code_Function} objects in this
     * package.
     *
     * @var array(PHP_Depend_Code_Function) $functions
     */
    protected $functions = array();

    /**
     * Constructs a new package for the given <b>$name</b>
     *
     * @param string $name The package name.
     */
    public function __construct($name)
    {
        $this->name = $name;

        $this->uuid = new PHP_Depend_Util_UUID();
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
    public function getUUID()
    {
        return (string) $this->uuid;
    }

    /**
     * Returns an iterator with all {@link PHP_Depend_Code_Class} instances
     * within this package.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getClasses()
    {
        $classes = array();
        foreach ($this->types as $type) {
            if ($type instanceof PHP_Depend_Code_Class) {
                $classes[] = $type;
            }
        }
        return new PHP_Depend_Code_NodeIterator($classes);
    }

    /**
     * Returns an iterator with all {@link PHP_Depend_Code_Interface} instances
     * within this package.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getInterfaces()
    {
        $interfaces = array();
        foreach ($this->types as $type) {
            if ($type instanceof PHP_Depend_Code_Interface) {
                $interfaces[] = $type;
            }
        }
        return new PHP_Depend_Code_NodeIterator($interfaces);
    }

    /**
     * Returns all {@link PHP_Depend_Code_AbstractType} objects in this package.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getTypes()
    {
        return new PHP_Depend_Code_NodeIterator($this->types);
    }

    /**
     * Adds the given type to this package and returns the input type instance.
     *
     * @param PHP_Depend_Code_AbstractType $type The new package type.
     *
     * @return PHP_Depend_Code_AbstractType
     */
    public function addType(PHP_Depend_Code_AbstractType $type)
    {
        // Skip if this package already contains this type
        if (in_array($type, $this->types, true)) {
            return;
        }

        if ($type->getPackage() !== null) {
            $type->getPackage()->removeType($type);
        }

        // Set this as class package
        $type->setPackage($this);
        // Append class to internal list
        $this->types[] = $type;

        return $type;
    }

    /**
     * Removes the given type instance from this package.
     *
     * @param PHP_Depend_Code_AbstractType $type The type instance to remove.
     *
     * @return void
     */
    public function removeType(PHP_Depend_Code_AbstractType $type)
    {
        if (($i = array_search($type, $this->types, true)) !== false) {
            // Remove class from internal list
            unset($this->types[$i]);
            // Remove this as parent
            $type->setPackage(null);
        }
    }

    /**
     * Returns all {@link PHP_Depend_Code_Function} objects in this package.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getFunctions()
    {
        return new PHP_Depend_Code_NodeIterator($this->functions);
    }

    /**
     * Adds the given function to this package and returns the input instance.
     *
     * @param PHP_Depend_Code_Function $function The new package function.
     *
     * @return PHP_Depend_Code_Function
     */
    public function addFunction(PHP_Depend_Code_Function $function)
    {
        if ($function->getPackage() !== null) {
            $function->getPackage()->removeFunction($function);
        }

        // Set this as function package
        $function->setPackage($this);
        // Append function to internal list
        $this->functions[] = $function;

        return $function;
    }

    /**
     * Removes the given function from this package.
     *
     * @param PHP_Depend_Code_Function $function The function to remove
     *
     * @return void
     */
    public function removeFunction(PHP_Depend_Code_Function $function)
    {
        if (($i = array_search($function, $this->functions, true)) !== false) {
            // Remove function from internal list
            unset($this->functions[$i]);
            // Remove this as parent
            $function->setPackage(null);
        }
    }

    /**
     * Visitor method for node tree traversal.
     *
     * @param PHP_Depend_VisitorI $visitor The context visitor
     *                                              implementation.
     *
     * @return void
     */
    public function accept(PHP_Depend_VisitorI $visitor)
    {
        $visitor->visitPackage($this);
    }
}