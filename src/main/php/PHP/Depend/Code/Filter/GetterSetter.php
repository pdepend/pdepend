<?php

class PHP_Depend_Code_Filter_GetterSetter
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
        $scope = $method->getFirstChildOfType('PHP_Depend_Code_ASTScope');

        if (!$scope) {
            return false;
        }

        $children = $scope->getChildren();

        if (count($children) != 1) {
            return false;
        }

        if (!($children[0] instanceof PHP_Depend_Code_ASTReturnStatement)) {
            return false;
        }

        $returnChildren = $children[0]->getChildren();

        if (count($returnChildren) != 1 || !($returnChildren[0] instanceof PHP_Depend_Code_ASTMemberPrimaryPrefix)) {
            return false;
        }

        $memberPrimaryPrefix = $returnChildren[0]->getChildren();

        if (count($memberPrimaryPrefix) != 2) {
            return false;
        }

        return $memberPrimaryPrefix[0] instanceof PHP_Depend_Code_ASTVariable &&
               $memberPrimaryPrefix[0]->getImage() === '$this' &&
               $memberPrimaryPrefix[1] instanceof PHP_Depend_Code_ASTPropertyPostfix;
    }

    private function containsSimpleAssignment(PHP_Depend_Code_Method $method)
    {
        return false;
    }
}

