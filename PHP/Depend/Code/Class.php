<?php
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
    
    public function accept(PHP_Depend_Code_NodeVisitor $visitor)
    {
        $visitor->visitClass($this);
    }
}