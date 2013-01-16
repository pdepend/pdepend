<?php

class PHP_Depend_Code_Filter_GetterSetterFilter
    implements PHP_Depend_Code_FilterI
{
    /**
     * Accepts a node whenever its not a simple getter or setter.
     *
     * A simple getter is defined by a return statement accessing
     * an instance variable. A simple getter is defined by an
     * assignment that assigns one instance variable to one
     * passed method parameter.
     *
     * @param PHP_Depend_Code_NodeI
     */
    public function accept(PHP_Depend_Code_NodeI $method)
    {
        if (!($method instanceof PHP_Depend_Code_Method)) {
            return true;
        }

        switch (substr($method->getName(), 0, 3)) {
            case 'get':
                return ! $this->containsSimpleReturn($method);

            case 'set':
                return ! $this->containsSimpleAssignment($method);

            default:
                return true;
        }
    }

    private function containsSimpleReturn(PHP_Depend_Code_Method $method)
    {
        return false;
    }

    private function containsSimpleAssignment(PHP_Depend_Code_Method $method)
    {
        return false;
    }
}

