<?php
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