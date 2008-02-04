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
    
    public function __construct($name, $cc, $ac, $ca, $ce)
    {
        $this->name = $name;
        
        $this->cc = (int) $cc;
        $this->ac = (int) $ac;
        $this->ca = (int) $ca;
        $this->ce = (int) $ce;
        
        $this->a = ($ac / ($cc + $ac));
        $this->i = (($ce + $ca) === 0 ? 0 : ($ce / ($ce + $ca)));
        $this->d = ($this->a + $this->i);
    }
    
    public function getName()
    {
        return $this->name;
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