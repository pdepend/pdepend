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

class PHP_Depend_Code_Class implements PHP_Depend_Code_Node
{
    protected $name = '';
    
    protected $package = null;
    
    protected $abstract = false;
    
    protected $methods = array();
    
    protected $dependencies = array();
    
    public function __construct($name)
    {
        $this->name = $name;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function isAbstract()
    {
        return $this->abstract;
    }
    
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;
    }
    
    public function getMethods()
    {
        return new ArrayIterator($this->methods);
    }
    
    public function addMethod(PHP_Depend_Code_Method $method)
    {
        if ($method->getClass() !== null) {
            $method->getClass()->removeMethod($method);
        }
        // Set this as owner class
        $method->setClass($this);
        // Store clas
        $this->methods[] = $method;
    }
    
    public function removeMethod(PHP_Depend_Code_Method $method)
    {
        foreach ($this->methods as $idx => $m) {
            if ($m === $method) {
                // Remove this as owner
                $method->setClass(null);
                // Remove from internal list
                unset($this->methods[$idx]);
                break;
            }
        }
    }
    
    public function getDependencies()
    {
        return new ArrayIterator($this->dependencies);
    }
    
    public function addDependency(PHP_Depend_Code_Class $class)
    {
        $this->dependencies[] = $class;
    }
    
    public function removeDependency(PHP_Depend_Code_Class $class)
    {
        $this->dependencies = array_diff($this->dependencies, array($class));
    }
    
    public function getPackage()
    {
        return $this->package;
    }
    
    public function setPackage(PHP_Depend_Code_Package $package = null)
    {
        $this->package = $package;
    }
    
    /**
     * Visitor method for node tree traversal.
     *
     * @param PHP_Depend_Code_NodeVisitor $visitor The context visitor implementation.
     * 
     * @return void
     */
    public function accept(PHP_Depend_Code_NodeVisitor $visitor)
    {
        $visitor->visitClass($this);
    }
}