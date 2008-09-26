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
 * @subpackage Ast
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Reflection/VisibilityI.php';

/**
 * Contains the available visiblity constants for methods and class properties.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Ast
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
interface PHP_Reflection_Ast_VisibilityAwareI extends PHP_Reflection_VisibilityI
{   
    /**
     * Sets the visibility for this node. 
     * 
     * The given <b>$visibility</b> value must equal to one of the defined 
     * constants, otherwith this method will fail with an exception.
     *
     * @param integer $visibility The node visibility.
     * 
     * @return void
     * @throws InvalidArgumentException If the given visibility is not equal to
     *                                  one of the defined visibility constants.
     */
    function setVisibility($visibility);
    
    /**
     * Returns <b>true</b> if this node is marked as public, otherwise the 
     * returned value will be <b>false</b>.
     *
     * @return boolean
     */
    function isPublic();
    
    /**
     * Returns <b>true</b> if this node is marked as protected, otherwise the 
     * returned value will be <b>false</b>.
     *
     * @return boolean
     */
    function isProtected();
    
    /**
     * Returns <b>true</b> if this node is marked as private, otherwise the 
     * returned value will be <b>false</b>.
     *
     * @return boolean
     */
    function isPrivate();
}