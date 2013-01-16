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

        if (!$this->containsSingleReturnStatement($children)) {
            return false;
        }

        $returnChildren = $children[0]->getChildren();

        if (count($returnChildren) != 1) {
            return false;
        }

        return $this->isMemberAcccessingThis($returnChildren[0]);
    }

    private function containsSimpleAssignment(PHP_Depend_Code_Method $method)
    {
        $scope = $method->getFirstChildOfType('PHP_Depend_Code_ASTScope');

        if (!$scope) {
            return false;
        }

        $children = $scope->getChildren();

        if (count($children) != 1 || !($children[0] instanceof PHP_Depend_Code_ASTStatement)) {
            return false;
        }

        $stmtChildren = $children[0]->getChildren();

        if (count($stmtChildren) != 1 || !($stmtChildren[0] instanceof PHP_Depend_Code_ASTAssignmentExpression)) {
            return false;
        }

        $assignmentChildren = $stmtChildren[0]->getChildren();

        if (count($assignmentChildren) != 2) {
            return false;
        }

        return $this->isMemberAcccessingThis($assignmentChildren[0]) ||
            $assignmentChildren[1] instanceof PHP_Depend_Code_Variable;
    }

    private function containsSingleReturnStatement(array $children)
    {
        if (count($children) != 1) {
            return false;
        }

        if (!($children[0] instanceof PHP_Depend_Code_ASTReturnStatement)) {
            return false;
        }

        return true;
    }

    private function isMemberAcccessingThis($node)
    {
        $memberPrimaryPrefix = $node->getChildren();

        if (count($memberPrimaryPrefix) != 2) {
            return false;
        }

        return $memberPrimaryPrefix[0] instanceof PHP_Depend_Code_ASTVariable &&
               $memberPrimaryPrefix[0]->getImage() === '$this' &&
               $memberPrimaryPrefix[1] instanceof PHP_Depend_Code_ASTPropertyPostfix;
    }
}

