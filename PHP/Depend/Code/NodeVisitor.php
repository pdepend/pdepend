<?php
interface PHP_Depend_Code_NodeVisitor
{
    function visitClass(PHP_Depend_Code_Class $class);
    
    function visitMethod(PHP_Depend_Code_Method $method);
    
    function visitPackage(PHP_Depend_Code_Package $package);
    
    function visitFunction(PHP_Depend_Code_Function $function);
}