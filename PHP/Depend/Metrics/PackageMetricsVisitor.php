<?php
require_once 'PHP/Depend/Code/NodeVisitor.php';
require_once 'PHP/Depend/Metrics/PackageMetrics.php';

class PHP_Depend_Metrics_PackageMetricsVisitor implements PHP_Depend_Code_NodeVisitor
{
    protected $data = array();
    
    protected $visible = array();
    
    protected $metrics = null;
    
    public function getMetrics()
    {
        if ($this->metrics !== null) {
            return $this->metrics;
        }

        $this->metrics = array();
        
        foreach ($this->visible as $pkg) {
            $this->metrics[] = new PHP_Depend_Metrics_Metrics(
                $pkg,
                $this->data[$pkg]['cc'],
                $this->data[$pkg]['ac'],
                count($this->data[$pkg]['ca']),
                count($this->data[$pkg]['ce'])
            );
        }
        
        return $this->metrics;
    }
    
    public function visitFunction(PHP_Depend_Code_Function $function)
    {
        
    }
    
    public function visitMethod(PHP_Depend_Code_Method $method)
    {
        $pkgName = $method->getClass()->getPackage()->getName();
        
        foreach ($method->getDependencies() as $dep) {
            $depPkgName = $dep->getPackage()->getName();
            
            if ($depPkgName === $pkgName) {
                continue;
            }
            
            $this->initPackage($depPkgName);
            
            $this->data[$pkgName]['ce'][$depPkgName] = true;
            $this->data[$depPkgName]['ca'][$pkgName] = true;
        }
    }
    
    public function visitPackage(PHP_Depend_Code_Package $package)
    {
        if ($package->getName() === PHP_Depend_Code_NodeBuilder::DEFAULT_PACKAGE) {
            return;
        }
        
        $this->visible[] = $package->getName();
        
        foreach ($package->getClasses() as $class) {
            $class->accept($this);
        }
    }
    
    public function visitClass(PHP_Depend_Code_Class $class)
    {
        $pkgName = $class->getPackage()->getName();
        
        $this->initPackage($pkgName);
        
        if ($class->isAbstract()) {
            ++$this->data[$pkgName]['ac'];
        } else {
            ++$this->data[$pkgName]['cc'];
        }
        
        foreach ($class->getDependencies() as $dep) {
            $depPkgName = $dep->getPackage()->getName();
            
            if ($depPkgName === $pkgName) {
                continue;
            }
            
            $this->initPackage($depPkgName);
            
            $this->data[$pkgName]['ce'][$depPkgName] = true;
            $this->data[$depPkgName]['ca'][$pkgName] = true;
        }

        foreach ($class->getMethods() as $method) {
            $method->accept($this);
        }   
    }
    
    protected function initPackage($name)
    {
        if (!isset($this->data[$name])) {
            $this->data[$name] = array(
                'cc'  =>  0,
                'ac'  =>  0,
                'ca'  =>  array(),
                'ce'  =>  array()
            );
        }
    }
}