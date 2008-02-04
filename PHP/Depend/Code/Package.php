<?php
require_once 'PHP/Depend/Code/Node.php';

class PHP_Depend_Code_Package implements PHP_Depend_Code_Node
{
    protected $name = '';
    
    protected $classes = array();
    
    protected $functions = array();
    
    public function __construct($name)
    {
        $this->name = $name;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getClasses()
    {
        return new ArrayIterator($this->classes);
    }
    
    public function addClass(PHP_Depend_Code_Class $class)
    {
        if ($class->getPackage()) {
            $class->getPackage()->removeClass($class);
        }
        
        // Set this as class package
        $class->setPackage($this);
        // Append class to internal list
        $this->classes[] = $class;
    }
    
    public function removeClass(PHP_Depend_Code_Class $class)
    {
        // Remove this package
        $class->setPackage(null);
        // Remove class from internal list
        foreach ($this->classes as $i => $c) {
            if ($c === $class) {
                unset($this->classes[$i]);
                break;
            }
        }
    }
    
    public function getFunctions()
    {
        return new ArrayIterator($this->functions);
    }
    
    public function addFunction(PHP_Depend_Code_Function $function)
    {
        $this->functions[] = $function;
    }
    
    public function removeFunction(PHP_Depend_Code_Function $function)
    {
        $this->functions = array_diff($this->functions, array($function));
    }
    
    public function accept(PHP_Depend_Code_NodeVisitor $visitor)
    {
        $visitor->visitPackage($this);
    }
}