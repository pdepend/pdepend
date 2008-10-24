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

require_once 'PHP/Reflection/AST/ClassOrInterfaceI.php';

/**
 * This is a proxy implementation of the class or interface node interface.
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
class PHP_Reflection_AST_ClassOrInterfaceProxy
    implements PHP_Reflection_AST_ClassOrInterfaceI
{
    /**
     * The creating node builder instance.
     *
     * @var PHP_Reflection_BuilderI $_builder
     */
    private $_builder = null;
    
    /**
     * The identifier for the underlying class or interface instance.
     *
     * @var string $_identifier
     */
    private $_identifier = null;

    /**
     * Constructs a new class or interface proxy.
     *
     * @param PHP_Reflection_BuilderI $builder The creating node builder instance.
     * @param unknown_type $identifier
     */
    public function __construct(PHP_Reflection_BuilderI $builder, $identifier)
    {
        $this->_builder    = $builder;
        $this->_identifier = $identifier;
    }
    
    /**
     * Returns <b>true</b> if this is an abstract class or an interface.
     *
     * @return boolean
     */
    public function isAbstract()
    {
        return $this->getRealSubject()->isAbstract();
    }
    
    /**
     * Returns all {@link PHP_Reflection_AST_ClassOrInterfaceConstant} objects 
     * in this class or interface node.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    public function getConstants()
    {
        return $this->getRealSubject()->getConstants();
    }
    
    /**
     * Returns all {@link PHP_Reflection_AST_ClassOrInterfaceI} objects this node 
     * depends on.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    public function getDependencies()
    {
        return $this->getRealSubject()->getDependencies();
    }
    
    /**
     * Returns all {@link PHP_Reflection_AST_MethodI} objects in this type.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    public function getMethods()
    {
        return $this->getRealSubject()->getMethods();
    }
    
    /**
     * Returns the parent package for this class.
     *
     * @return PHP_Reflection_AST_Package
     */
    public function getPackage()
    {
        return $this->getRealSubject()->getPackage();
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
        return $this->getRealSubject()->isSubtypeOf($classOrInterface);
    }
    
    /**
     * Returns the name for this code node.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_identifier;
    }
    
    /**
     * Returns a uuid for this code node.
     *
     * @return string
     */
    public function getUUID()
    {
        return $this->getRealSubject()->getUUID();
    }
    
    /**
     * Compares two node instances to be equal. You should always use this method
     * instead of a direct comparsion of two nodes, because the syntax tree uses
     * proxy implementations to represent some items.
     *
     * @param PHP_Reflection_AST_NodeI $node The node to compare to.
     * 
     * @return boolean
     */
    public function equals(PHP_Reflection_AST_NodeI $node)
    {
        return ($this->getRealSubject()->getUUID() === $node->getUUID());
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
        $this->getRealSubject()->accept($visitor);
    }
    
    /**
     * Returns the real subject behind this proxy. 
     *
     * @return PHP_Reflection_AST_ClassOrInterfaceI
     */
    protected function getRealSubject()
    {
        return $this->_builder->findClassOrInterfaceSubject($this->_identifier);
    }
}