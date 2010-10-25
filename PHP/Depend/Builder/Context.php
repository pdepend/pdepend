<?php
interface PHP_Depend_Builder_Context
{
    /**
     * Returns the class instance for the given qualified name.
     *
     * @param string $qualifiedName Full qualified class name.
     *
     * @return PHP_Depend_Code_Class
     */
    function getClass($qualifiedName);

    /**
     * Returns a class or an interface instance for the given qualified name.
     *
     * @param string $qualifiedName Full qualified class or interface name.
     * 
     * @return PHP_Depend_Code_AbstractClassOrInterface
     */
    function getClassOrInterface($qualifiedName);
}