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

require_once 'PHP/Reflection/AST/AbstractItem.php';
require_once 'PHP/Reflection/AST/TypeAwareI.php';

/**
 * An instance of this class represents a function or method parameter within 
 * the analyzed source code.
 * 
 * <code>
 * <?php
 * class PHP_Reflection_BuilderI
 * {
 *     public function buildNode($name, $line, PHP_Reflection_AST_File $file) {
 *     }
 * }
 * 
 * function parse(PHP_Reflection_BuilderI $builder, $file) {
 * }
 * </code>
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
class PHP_Reflection_AST_Parameter 
       extends PHP_Reflection_AST_AbstractItem
    implements PHP_Reflection_AST_TypeAwareI
{
    /**
     * The parent function or method instance.
     *
     * @var PHP_Reflection_AST_AbstractMethodOrFunction $_parent
     */
    private $_parent = null;
    
    /**
     * The parameter position.
     *
     * @var integer $_position
     */
    private $_position = 0;
    
    /**
     * The type for this property. This value is <b>null</b> by default and for
     * scalar types.
     *
     * @var PHP_Reflection_AST_AbstractClassOrInterface $_type
     */
    private $_type = null;

    /**
     * Returns the parent function or method instance or <b>null</b>
     *
     * @return PHP_Reflection_AST_MethodOrFunctionI|null
     */
    public function getParent()
    {
        return $this->_parent;
    }
    
    /**
     * Sets the parent function or method object.
     *
     * @param PHP_Reflection_AST_MethodOrFunctionI $parent The parent callable.
     * 
     * @return void
     */
    public function setParent(PHP_Reflection_AST_MethodOrFunctionI $parent = null)
    {
        $this->_parent = $parent;
    }
    
    /**
     * Returns the parameter position in the method/function signature.
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->_position;
    }
    
    /**
     * Sets the parameter position in the method/function signature.
     *
     * @param integer $position The parameter position.
     * 
     * @return void
     */
    public function setPosition($position)
    {
        $this->_position = $position;
    }
    
    /**
     * Returns the type of this property. This method will return <b>null</b>
     * for all scalar type, only class properties will have a type.
     *
     * @return PHP_Reflection_AST_ClassOrInterfaceI
     */
    public function getType()
    {
        return $this->_type;
    }
    
    /**
     * Sets the type of this property.
     *
     * @param PHP_Reflection_AST_ClassOrInterfaceI $type The property type.
     * 
     * @return void
     */
    public function setType(PHP_Reflection_AST_ClassOrInterfaceI $type)
    {
        $this->_type = $type;
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
        $visitor->visitParameter($this);
    }
}