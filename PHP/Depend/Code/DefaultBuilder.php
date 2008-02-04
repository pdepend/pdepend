<?php
require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/NodeBuilder.php'; 
require_once 'PHP/Depend/Code/Function.php';
require_once 'PHP/Depend/Code/Method.php';
require_once 'PHP/Depend/Code/Package.php';

class PHP_Depend_Code_DefaultBuilder implements PHP_Depend_Code_NodeBuilder
{
    
    protected $defaultPackage = null;
    
    protected $classes = array();
    
    protected $packages = array();
    
    public function __construct()
    {
        $this->defaultPackage = new PHP_Depend_Code_Package(self::DEFAULT_PACKAGE);
    }

    public function buildClass($name)
    {
        if (!isset($this->classes[$name])) {
            $this->classes[$name] = new PHP_Depend_Code_Class($name);
            
            $this->defaultPackage->addClass($this->classes[$name]);
        }
        return $this->classes[$name];
    }
    
    /**
     * Enter description here...
     *
     * @param unknown_type $name
     * 
     * @return PHP_Depend_Code_Method
     */
    public function buildMethod($name)
    {
        return new PHP_Depend_Code_Method($name);
    }
    
    public function buildPackage($name)
    {
        if (!isset($this->packages[$name])) {
            $this->packages[$name] = new PHP_Depend_Code_Package($name);
        }
        return $this->packages[$name];
    }
    
    public function buildFunction($name)
    {
        return new PHP_Depend_Code_Function($name);
    }
    
    public function getIterator()
    {
        return $this->getPackages();
    }
    
    public function getPackages()
    {
        return new ArrayIterator($this->packages);
    }
}