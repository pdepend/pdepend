<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

/**
 * This class implements a filter that skips simple getters and setters.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @author     Benjamin Eberlei <kontakt@beberlei.de>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
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

