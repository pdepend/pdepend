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

/**
 * An instance of this class represents a class or interface constant within the
 * analyzed source code.
 * 
 * <code>
 * <?php
 * class PHP_Reflection_BuilderI
 * {
 *     const UNKNOWN_PKG = '+global';
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
class PHP_Reflection_AST_ClassOrInterfaceConstant 
    extends PHP_Reflection_AST_AbstractItem
{
    /**
     * The parent type object.
     *
     * @var PHP_Reflection_AST_AbstractClassOrInterface $_parent
     */
    private $_parent = null;
    
    /**
     * The scalar value of this constant
     *
     * @var PHP_Reflection_AST_StaticScalarValueI $_value
     */
    private $_value = null;

    /**
     * Returns the parent type object or <b>null</b>
     *
     * @return PHP_Reflection_AST_AbstractClassOrInterfaceI|null
     */
    public function getParent()
    {
        return $this->_parent;
    }
    
    /**
     * Sets the parent type object.
     *
     * @param PHP_Reflection_AST_ClassOrInterfaceI $parent The parent class.
     * 
     * @return void
     */
    public function setParent(PHP_Reflection_AST_ClassOrInterfaceI $parent = null)
    {
        $this->_parent = $parent;
    }
    
    /**
     * Returns the value representation for this constant.
     *
     * @return PHP_Reflection_AST_StaticScalarValueI
     */
    public function getValue()
    {
        return $this->_value;
    }
    
    /**
     * Sets the value representation for this constant.
     *
     * @param PHP_Reflection_AST_StaticScalarValueI $value The scalar value object.
     * 
     * @return void
     */
    public function setValue(PHP_Reflection_AST_StaticScalarValueI $value)
    {
        $this->_value = $value;
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
        $visitor->visitTypeConstant($this);
    }
}