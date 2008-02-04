<?php
interface PHP_Depend_Code_NodeBuilder extends IteratorAggregate
{
    const DEFAULT_PACKAGE = '__default__';
    
    /**
     * Enter description here...
     *
     * @param unknown_type $name
     * 
     * @return PHP_Depend_Code_Class
     */
    function buildClass($name);
    
    /**
     * Enter description here...
     *
     * @param unknown_type $name
     * 
     * @return PHP_Depend_Code_Package
     */
    function buildPackage($name);
    
    /**
     * Enter description here...
     *
     * @param unknown_type $name
     * 
     * @return PHP_Depend_Code_Method
     */
    function buildMethod($name);
    
    /**
     * Enter description here...
     *
     * @param unknown_type $name
     * 
     * @return PHP_Depend_Code_Function
     */
    function buildFunction($name);
}