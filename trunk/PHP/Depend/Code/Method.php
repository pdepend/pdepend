<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pmanuel-pichler.de>.
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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/AbstractCallable.php';
require_once 'PHP/Depend/Code/VisibilityAwareI.php';

/**
 * Represents a php method node.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Code_Method 
    extends PHP_Depend_Code_AbstractCallable
    implements PHP_Depend_Code_VisibilityAwareI
{
    /**
     * Marks this method as abstract.
     *
     * @type boolean
     * @var boolean $abstract
     */
    protected $abstract = false;
    
    /**
     * Set defined visibility for this method.
     *
     * @type integer
     * @var integer $visibility
     */
    protected $visibility = -1;
    
    /**
     * The parent type object.
     *
     * @type PHP_Depend_Code_AbstractType
     * @var PHP_Depend_Code_AbstractType $parent
     */
    protected $parent = null;
    
    /**
     * Returns <b>true</b> if this is an abstract method.
     *
     * @return boolean
     */
    public function isAbstract()
    {
        return $this->abstract;
    }
    
    /**
     * Marks this as an abstract method.
     *
     * @param boolean $abstract Set this to <b>true</b> for an abstract method.
     * 
     * @return void
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;
    }
    
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
    public function setVisibility($visibility)
    {
        // List of allowed visibility values
        $allowed = array(self::IS_PUBLIC, self::IS_PROTECTED, self::IS_PRIVATE);
        
        // Check for a valid value
        if (in_array($visibility, $allowed, true) === false) {
            throw new InvalidArgumentException('Invalid visibility value given.');
        }
        // Check for previous value
        if ($this->visibility === -1) {
            $this->visibility = $visibility;
        }
    }
    
    /**
     * Returns <b>true</b> if this node is marked as public, otherwise the 
     * returned value will be <b>false</b>.
     *
     * @return boolean
     */
    public function isPublic()
    {
        return ($this->visibility === self::IS_PUBLIC);
    }
    
    /**
     * Returns <b>true</b> if this node is marked as protected, otherwise the 
     * returned value will be <b>false</b>.
     *
     * @return boolean
     */
    public function isProtected()
    {
        return ($this->visibility === self::IS_PROTECTED);
    }
    
    /**
     * Returns <b>true</b> if this node is marked as private, otherwise the 
     * returned value will be <b>false</b>.
     *
     * @return boolean
     */
    public function isPrivate()
    {
        return ($this->visibility === self::IS_PRIVATE);
    }
    
    
    /**
     * Returns the parent type object or <b>null</b>
     *
     * @return PHP_Depend_Code_AbstractType|null
     */
    public function getParent()
    {
        return $this->parent;
    }
    
    /**
     * Sets the parent type object.
     *
     * @param PHP_Depend_Code_AbstractType $parent The parent type.
     * 
     * @return void
     */
    public function setParent(PHP_Depend_Code_AbstractType $parent = null)
    {
        $this->parent = $parent;
    }
    
    /**
     * Visitor method for node tree traversal.
     *
     * @param PHP_Depend_Code_NodeVisitor $visitor The context visitor 
     *                                             implementation.
     * 
     * @return void
     */
    public function accept(PHP_Depend_Code_NodeVisitor $visitor)
    {
        $visitor->visitMethod($this);
    }
}