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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/NodeVisitor/VisitListenerI.php';

/**
 * 
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
abstract class PHP_Depend_Code_NodeVisitor_AbstractDefaultVisitListener
    implements PHP_Depend_Code_NodeVisitor_VisitListenerI
{
    public function startVisitClass(PHP_Depend_Code_Class $class)
    {
        $this->startVisitNode($class);
    }
    
    public function endVisitClass(PHP_Depend_Code_Class $class)
    {
        $this->endVisitNode($class);
    }
    
    public function startVisitFile(PHP_Depend_Code_File $file)
    {
        $this->startVisitNode($file);
    }
    
    public function endVisitFile(PHP_Depend_Code_File $file)
    {
        $this->endVisitNode($file);
    }
    
    public function startVisitFunction(PHP_Depend_Code_Function $function)
    {
        $this->startVisitNode($function);
    }
    
    public function endVisitFunction(PHP_Depend_Code_Function $function)
    {
        $this->endVisitNode($function);
    }
    
    public function startVisitInterface(PHP_Depend_Code_Interface $interface)
    {
        $this->startVisitNode($interface);
    }
    
    public function endVisitInterface(PHP_Depend_Code_Interface $interface)
    {
        $this->endVisitNode($interface);
    }
    
    public function startVisitMethod(PHP_Depend_Code_Method $method)
    {
        $this->startVisitNode($method);
    }
    
    public function endVisitMethod(PHP_Depend_Code_Method $method)
    {
        $this->endVisitNode($method);
    }
    
    public function startVisitPackage(PHP_Depend_Code_Package $package)
    {
        $this->startVisitNode($package);
    }
    
    public function endVisitPackage(PHP_Depend_Code_Package $package)
    {
        $this->endVisitNode($package);
    }
    
    public function startVisitProperty(PHP_Depend_Code_Property $property)
    {
        $this->startVisitNode($property);
    }
    
    public function endVisitProperty(PHP_Depend_Code_Property $property)
    {
        $this->endVisitNode($property);
    }
    
    protected function startVisitNode(PHP_Depend_Code_NodeI $node)
    {
        
    }
    
    protected function endVisitNode(PHP_Depend_Code_NodeI $node)
    {
        
    }
}