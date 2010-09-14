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

class PHP_Depend_Metrics_PackageMetrics
{
    protected $name = '';
    
    protected $cc = 0;
    
    protected $ac = 0;
    
    protected $ca = 0;
    
    protected $ce = 0;
    
    protected $a = 0;
    
    protected $i = 0;
    
    protected $d = 0;
    
    protected $tc = 0;
    
    protected $concreteClasses = array();
    
    protected $abstractClasses = array();
    
    protected $efferentCouplings = array();
    
    protected $afferentCouplings = array();
    
    public function __construct($name, array $cc, array $ac, array $ca, array $ce)
    {
        $this->concreteClasses   = $cc;
        $this->abstractClasses   = $ac;
        $this->efferentCouplings = $ce;
        $this->afferentCouplings = $ca;

        $this->name = $name;
        
        $this->cc = count($cc);
        $this->ac = count($ac);
        $this->ca = count($ca);
        $this->ce = count($ce);
        $this->tc = ($this->cc + $this->ac);
        
        $this->a = (($this->cc + $this->ac) === 0 ? 0 : ($this->ac / ($this->cc + $this->ac)));
        $this->i = (($this->ce + $this->ca) === 0 ? 0 : ($this->ce / ($this->ce + $this->ca)));
        $this->d = abs(($this->a + $this->i) - 1);
    }
    
    public function getConcreteClasses()
    {
        return $this->concreteClasses;
    }
    
    public function getAbstractClasses()
    {
        return $this->abstractClasses;
    }
    
    public function getAfferentCouplings()
    {
        return $this->afferentCouplings;
    }
    
    public function getEfferentCouplings()
    {
        return $this->efferentCouplings;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getTC()
    {
        return $this->tc;
    }
    
    public function getCC()
    {
        return $this->cc;
    }
    
    public function getAC()
    {
        return $this->ac;
    }
    
    public function getCA()
    {
        return $this->ca;
    }
    
    public function getCE()
    {
        return $this->ce;
    }
    
    public function getA()
    {
        return $this->a;
    }
    
    public function getI()
    {
        return $this->i;
    }
    
    public function getD()
    {
        return $this->d;
    }
}