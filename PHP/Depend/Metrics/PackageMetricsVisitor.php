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

require_once 'PHP/Depend/Code/NodeVisitor.php';
require_once 'PHP/Depend/Metrics/PackageMetrics.php';

class PHP_Depend_Metrics_PackageMetricsVisitor implements PHP_Depend_Code_NodeVisitor
{
    protected $data = array();
    
    private $_metrics = null;
    
    public function getPackageMetrics()
    {
        if ($this->_metrics !== null) {
            return $this->_metrics;
        }

        $this->_metrics = array();
        
        foreach ($this->data as $pkg => $data) {
            $this->_metrics[$pkg] = new PHP_Depend_Metrics_PackageMetrics(
                $pkg, 
                $data['cc'],
                $data['ac'],
                $data['ca'],
                $data['ce']
            );
        }
        
        return new ArrayIterator($this->_metrics);
    }
    
    public function visitFunction(PHP_Depend_Code_Function $function)
    {
        
    }
    
    public function visitMethod(PHP_Depend_Code_Method $method)
    {
        $pkgName = $method->getClass()->getPackage()->getName();
        
        foreach ($method->getDependencies() as $dep) {
            $depPkgName = $dep->getPackage()->getName();
            
            if ($dep->getPackage() === $method->getClass()->getPackage()) {
                continue;
            }
            
            $this->initPackage($dep->getPackage());
            
            if (!in_array($dep->getPackage(), $this->data[$pkgName]['ce'], true)) {
                $this->data[$pkgName]['ce'][] = $dep->getPackage();
            }
            if (!in_array($method->getClass()->getPackage(), $this->data[$depPkgName]['ca'], true)) {
                $this->data[$depPkgName]['ca'][] = $method->getClass()->getPackage();
            }
        }
    }
    
    public function visitPackage(PHP_Depend_Code_Package $package)
    {
        foreach ($package->getClasses() as $class) {
            $class->accept($this);
        }
    }
    
    public function visitClass(PHP_Depend_Code_Class $class)
    {
        $pkgName = $class->getPackage()->getName();
        
        $this->initPackage($class->getPackage());
        
        if ($class->isAbstract()) {
            $this->data[$pkgName]['ac'][] = $class;
        } else {
            $this->data[$pkgName]['cc'][] = $class;
        }
        
        foreach ($class->getDependencies() as $dep) {
            $depPkgName = $dep->getPackage()->getName();
            
            if ($dep->getPackage() === $class->getPackage()) {
                continue;
            }
            
            $this->initPackage($dep->getPackage());
            
            if (!in_array($dep->getPackage(), $this->data[$pkgName]['ce'], true)) {
                $this->data[$pkgName]['ce'][] = $dep->getPackage();
            }
            if (!in_array($class->getPackage(), $this->data[$depPkgName]['ca'], true)) {
                $this->data[$depPkgName]['ca'][] = $class->getPackage();
            }
        }

        foreach ($class->getMethods() as $method) {
            $method->accept($this);
        }   
    }
    
    protected function initPackage(PHP_Depend_Code_Package $package)
    {
        $name = $package->getName();
        
        if (!isset($this->data[$name])) {
            $this->data[$name] = array(
                'cc'  =>  array(),
                'ac'  =>  array(),
                'ca'  =>  array(),
                'ce'  =>  array()
            );
        }
    }
}