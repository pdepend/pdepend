<?php
require_once 'PHP/Depend/Code/Node.php';

class PHP_Depend_Code_Method extends PHP_Depend_Code_Function
{
    protected $class = null;
    
    public function getClass()
    {
        return $this->class;
    }
    
    public function setClass(PHP_Depend_Code_Class $class = null)
    {
        $this->class = $class;
    }
    
    
    public function accept(PHP_Depend_Code_NodeVisitor $visitor)
    {
        $visitor->visitMethod($this);
    }
}