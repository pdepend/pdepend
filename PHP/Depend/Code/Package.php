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

require_once 'PHP/Depend/Code/Node.php';

class PHP_Depend_Code_Package implements PHP_Depend_Code_Node
{
    protected $name = '';
    
    protected $classes = array();
    
    protected $functions = array();
    
    public function __construct($name)
    {
        $this->name = $name;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getClasses()
    {
        return new ArrayIterator($this->classes);
    }
    
    public function addClass(PHP_Depend_Code_Class $class)
    {
        if ($class->getPackage()) {
            $class->getPackage()->removeClass($class);
        }
        
        // Set this as class package
        $class->setPackage($this);
        // Append class to internal list
        $this->classes[] = $class;
    }
    
    public function removeClass(PHP_Depend_Code_Class $class)
    {
        // Remove this package
        $class->setPackage(null);
        // Remove class from internal list
        foreach ($this->classes as $i => $c) {
            if ($c === $class) {
                unset($this->classes[$i]);
                break;
            }
        }
    }
    
    public function getFunctions()
    {
        return new ArrayIterator($this->functions);
    }
    
    public function addFunction(PHP_Depend_Code_Function $function)
    {
        $this->functions[] = $function;
    }
    
    public function removeFunction(PHP_Depend_Code_Function $function)
    {
        $this->functions = array_diff($this->functions, array($function));
    }
    
    public function accept(PHP_Depend_Code_NodeVisitor $visitor)
    {
        $visitor->visitPackage($this);
    }
}