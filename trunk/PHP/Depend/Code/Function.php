<?php
require_once 'PHP/Depend/Code/Node.php';

class PHP_Depend_Code_Function implements PHP_Depend_Code_Node
{
    protected $name = null;
    
    protected $package = null;
    
    protected $dependencies = array();
    
    public function __construct($name)
    {
        $this->name = $name;
    }
    
    public function getName()
    {
        return $this->name;
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
    
    public function accept(PHP_Depend_Code_NodeVisitor $visitor)
    {
        $visitor->visitFunction($this);
    }
}