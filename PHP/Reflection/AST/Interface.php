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
 * @subpackage AST
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Reflection/AST/AbstractClassOrInterface.php';
require_once 'PHP/Reflection/AST/InterfaceI.php';

/**
 * Representation of a code interface.  
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage AST
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_AST_Interface 
       extends PHP_Reflection_AST_AbstractClassOrInterface
    implements PHP_Reflection_AST_InterfaceI
{
    /**
     * List of parent interfaces for this interface.
     *
     * @var array(PHP_Reflection_AST_InterfaceI) $_parentInterfaceList
     */
    private $_parentInterfaceList = array();
    
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
     * Returns an iterator with all {@link PHP_Reflection_AST_InterfaceI} nodes
     * that are a parent, parent parent etc. interface of this interface.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    public function getParentInterfaces()
    {
        $interfaces = $this->_parentInterfaceList;
        foreach ($interfaces as $interface) {
            // Append parent parents
            foreach ($interface->getParentInterfaces() as $parentInterface) {
                foreach ($interfaces as $existingInterface) {
                    if ($existingInterface->equals($parentInterface)) {
                        continue 2;
                    }
                }
                $interfaces[] = $parentInterface;
            }
        }
        return new PHP_Reflection_AST_Iterator($interfaces);
    }
    
    /**
     * Adds the given <b>$interface</b> to the list of parent interfaces.
     *
     * @param PHP_Reflection_AST_InterfaceI $interface The parent interface node.
     * 
     * @return void
     */
    public function addParentInterface(PHP_Reflection_AST_ClassOrInterfaceI $interface)
    {
        // Add parent interface only one time
        if (in_array($interface, $this->_parentInterfaceList, true) === false) {
            // Add interface to list of parents
            $this->_parentInterfaceList[] = $interface;
        }
    }
    
    /**
     * Returns all {@link PHP_Reflection_AST_ClassOrInterfaceI} objects this type 
     * depends on.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    public function getDependencies()
    {
        return $this->getParentInterfaces();
    }
    
    /**
     * Checks that this user type is a subtype of the given <b>$classOrInterface</b>
     * instance.
     *
     * @param PHP_Reflection_AST_ClassOrInterfaceI $classOrInterface 
     *        The possible parent node.
     * 
     * @return boolean
     */
    public function isSubtypeOf(PHP_Reflection_AST_ClassOrInterfaceI $classOrInterface)
    {
        if ($classOrInterface === $this) {
            return true;
        } else if ($classOrInterface instanceof PHP_Reflection_AST_Interface) {
            foreach ($this->getParentInterfaces() as $interface) {
                if ($interface === $classOrInterface) {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Visitor method for node tree traversal.
     *
     * @param PHP_Reflection_VisitorI $visitor The context visitor implementation.
     * 
     * @return void
     */
    public function accept(PHP_Reflection_VisitorI $visitor)
    {
        $visitor->visitInterface($this);
    }
    
}