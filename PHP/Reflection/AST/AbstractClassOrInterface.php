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

require_once 'PHP/Reflection/AST/AbstractSourceElement.php';
require_once 'PHP/Reflection/AST/ClassOrInterfaceI.php';

/**
 * Represents an interface or a class type.
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
abstract class PHP_Reflection_AST_AbstractClassOrInterface
       extends PHP_Reflection_AST_AbstractSourceElement 
    implements PHP_Reflection_AST_ClassOrInterfaceI
{
    /**
     * The parent package for this class.
     *
     * @type PHP_Reflection_AST_Package
     * @var PHP_Reflection_AST_Package $_package
     */
    private $_package = null;
    
    /**
     * List of {@link PHP_Reflection_AST_Method} objects in this class.
     *
     * @var array(PHP_Reflection_AST_Method) $_methods
     */
    private $_methods = array();
    
    /**
     * The tokens for this type.
     *
     * @type array<array>
     * @var array(array) $_tokens
     */
    private $_tokens = array();
    
    /**
     * List of {@link PHP_Reflection_AST_ClassOrInterfaceConstant} objects that
     * belong to this type. 
     *
     * @type array<PHP_Reflection_AST_ClassOrInterfaceConstant>
     * @var array(PHP_Reflection_AST_ClassOrInterfaceConstant) $_constants
     */
    private $_constants = array();
    
    /**
     * Type position within the source code file.
     *
     * @type integer
     * @var integer $_position
     */
    private $_position = 0;
    
    /**
     * Returns all {@link PHP_Reflection_AST_ClassOrInterfaceConstant} objects
     * in this type.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    public function getConstants()
    {
        return new PHP_Reflection_AST_Iterator($this->_constants);
    }
    
    /**
     * Adds the given constant to this type.
     *
     * @param PHP_Reflection_AST_ClassOrInterfaceConstant $constant
     * A new type constant.
     * 
     * @return PHP_Reflection_AST_ClassOrInterfaceConstant
     */
    public function addConstant(
                        PHP_Reflection_AST_ClassOrInterfaceConstant $constant)
    {
        if ($constant->getParent() !== null) {
            $constant->getParent()->removeConstant($constant);
        }
        // Set this as owner type
        $constant->setParent($this);
        // Store constant
        $this->_constants[] = $constant;
        
        return $constant;
    }
    
    /**
     * Removes the given constant from this type.
     *
     * @param PHP_Reflection_AST_ClassOrInterfaceConstant $constant
     * The constant to remove.
     * 
     * @return void
     */
    public function removeConstant(
                      PHP_Reflection_AST_ClassOrInterfaceConstant $constant)
    {
        if (($i = array_search($constant, $this->_constants, true)) !== false) {
            // Remove this as owner
            $constant->setParent(null);
            // Remove from internal list
            unset($this->_constants[$i]);
        }
    }
    
    /**
     * Returns all {@link PHP_Reflection_AST_MethodI} objects in this type.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    public function getMethods()
    {
        return new PHP_Reflection_AST_Iterator($this->_methods);
    }
    
    /**
     * Adds the given method to this type.
     *
     * @param PHP_Reflection_AST_Method $method A new type method.
     * 
     * @return PHP_Reflection_AST_Method
     */
    public function addMethod(PHP_Reflection_AST_Method $method)
    {
        if ($method->getParent() !== null) {
            $method->getParent()->removeMethod($method);
        }
        // Set this as owner type
        $method->setParent($this);
        // Store method
        $this->_methods[] = $method;
        
        return $method;
    }
    
    /**
     * Removes the given method from this class.
     *
     * @param PHP_Reflection_AST_Method $method The method to remove.
     * 
     * @return void
     */
    public function removeMethod(PHP_Reflection_AST_Method $method)
    {
        if (($i = array_search($method, $this->_methods, true)) !== false) {
            // Remove this as owner
            $method->setParent(null);
            // Remove from internal list
            unset($this->_methods[$i]);
        }
    }
    
    /**
     * Returns an <b>array</b> with all tokens within this type.
     *
     * @return array(array)
     */
    public function getTokens()
    {
        return $this->_tokens;
    }
    
    /**
     * Sets the tokens for this type.
     *
     * @param array(array) $tokens The generated tokens.
     * 
     * @return void
     */
    public function setTokens(array $tokens)
    {
        $this->_tokens = $tokens;
    }
    
    /**
     * Returns the parent package for this class.
     *
     * @return PHP_Reflection_AST_Package
     */
    public function getPackage()
    {
        return $this->_package;
    }
    
    /**
     * Sets the parent package for this class.
     *
     * @param PHP_Reflection_AST_Package $package The parent package.
     * 
     * @return void
     */
    public function setPackage(PHP_Reflection_AST_Package $package = null)
    {
        $this->_package = $package;
    }
    
    /**
     * Returns the position of this type within the source file.
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->_position;
    }
    
    /**
     * Sets the source position of this type.
     *
     * @param integer $position Position within the source file.
     * 
     * @return void
     */
    public function setPosition($position)
    {
        $this->_position = (int) $position;
    }
}