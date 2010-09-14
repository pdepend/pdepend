<?php
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