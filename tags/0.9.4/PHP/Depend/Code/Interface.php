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

require_once 'PHP/Depend/Code/AbstractType.php';

/**
 * Representation of a code interface.
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
class PHP_Depend_Code_Interface extends PHP_Depend_Code_AbstractType
{
    /**
     * The modifiers for this interface instance, by default an interface is
     * always abstract.
     *
     * @var integer $_modifiers
     */
    private $_modifiers = PHP_Depend_ConstantsI::IS_IMPLICIT_ABSTRACT;

    /**
     * Returns <b>true</b> if this is an abstract class or an interface.
     *
     * @return boolean
     */
    public function isAbstract()
    {
        return true;
    }

    /**
     * Returns an iterator with all implementing classes.
     *
     * @return PHP_Depend_Code_NodeIterator
     * @todo TODO: Should we return all implementing classes? This would include
     *             all classes that extend a implementing classes and all classes
     *             that implement a child interface.
     */
    public function getImplementingClasses()
    {
        $classes = array();
        foreach ($this->children as $child) {
            if ($child instanceof PHP_Depend_Code_Class) {
                $classes[] = $child;
            }
        }
        return new PHP_Depend_Code_NodeIterator($classes);
    }

    /**
     * Returns an iterator with all parent, parent parent etc. interfaces.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getParentInterfaces()
    {
        $interfaces = array();
        foreach ($this->getUnfilteredRawDependencies() as $interface) {
            // Append parent interface first
            if (in_array($interface, $interfaces, true) === false) {
                $interfaces[] = $interface;
            }
            // Append parent parents
            foreach ($interface->getParentInterfaces() as $parentInterface) {
                if (in_array($parentInterface, $interfaces, true) === false) {
                    $interfaces[] = $parentInterface;
                }
            }
        }
        return new PHP_Depend_Code_NodeIterator($interfaces);
    }

    /**
     * Returns an iterator with all child interfaces.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getChildInterfaces()
    {
        $interfaces = array();
        foreach ($this->children as $child) {
            if ($child instanceof PHP_Depend_Code_Interface) {
                $interfaces[] = $child;
            }
        }
        return new PHP_Depend_Code_NodeIterator($interfaces);
    }

    /**
     * Checks that this user type is a subtype of the given <b>$type</b> instance.
     *
     * @param PHP_Depend_Code_AbstractType $type The possible parent type instance.
     *
     * @return boolean
     */
    public function isSubtypeOf(PHP_Depend_Code_AbstractType $type)
    {
        if ($type === $this) {
            return true;
        } else if ($type instanceof PHP_Depend_Code_Interface) {
            foreach ($this->getParentInterfaces() as $interface) {
                if ($interface === $type) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Returns the declared modifiers for this type.
     *
     * @return integer
     * @since 0.9.4
     */
    public function getModifiers()
    {
        return $this->_modifiers;
    }

    /**
     * This method sets a OR combined integer of the declared modifiers for this
     * node.
     *
     * This method will throw an exception when the value of given <b>$modifiers</b>
     * contains an invalid/unexpected modifier
     *
     * @param integer $modifiers The declared modifiers for this node.
     *
     * @return void
     * @throws InvalidArgumentException If the given modifier contains unexpected
     *                                  values.
     * @since 0.9.4
     */
    public function setModifiers($modifiers)
    {
        if ($this->_modifiers !== 0) {
            return;
        }

        $expected = ~PHP_Depend_ConstantsI::IS_IMPLICIT_ABSTRACT;

        if (($expected & $modifiers) !== 0) {
            throw new InvalidArgumentException('Invalid class modifier given.');
        }

        $this->_modifiers = $modifiers;
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
        $visitor->visitInterface($this);
    }

}