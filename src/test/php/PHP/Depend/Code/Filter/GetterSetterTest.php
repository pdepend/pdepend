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

require_once dirname(__FILE__) . '/../../AbstractTest.php';

/**
 * Test the getter setter filter
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
 *
 * @covers PHP_Depend_Code_Filter_GetterSetter
 * @group pdepend
 * @group pdepend::code
 * @group pdepend::code::filter
 * @group unittest
 */
class PHP_Depend_Code_Filter_GetterSetterTest extends PHP_Depend_AbstractTest
{
    private $filter;

    public function setUp()
    {
        $this->filter = new PHP_Depend_Code_Filter_GetterSetter();
    }

    public function testFilterAcceptsClass()
    {
        $this->assertTrue($this->filter->accept(
            new PHP_Depend_Code_Class(__CLASS__)
        ));
    }

    public function testFilterAcceptsSimpleGetterWithNonThisAccess()
    {
        $method = $this->createSimpleGetterAccessing('$bar'); // $bar->var
        $this->assertTrue($this->filter->accept($method));
    }

    public function testFilterRejectsSimpleGetter()
    {
        $method = $this->createSimpleGetterAccessing('$this'); // $bar->var
        $this->assertFalse($this->filter->accept($method));
    }

    public function testFilterAcceptsComplexGetter()
    {
        $method = $this->createSimpleGetterAccessing('$this');

        $scope = $method->getFirstChildOfType('PHP_Depend_Code_ASTScope');
        $scope->addChild(new PHP_Depend_Code_ASTReturnStatement()); // add another return => complex

        $this->assertTrue($this->filter->accept($method));
    }

    public function testFilterAcceptsComplexSetter()
    {
        $method = $this->createSimpleSetter();

        $scope = $method->getFirstChildOfType('PHP_Depend_Code_ASTScope');
        $scope->addChild(new PHP_Depend_Code_ASTReturnStatement()); // add another return => complex

        $this->assertTrue($this->filter->accept($method));
    }

    public function testFilterRejectsSimpleSetter()
    {
        $method = $this->createSimpleSetter();
        $this->assertFalse($this->filter->accept($method));
    }

    private function createSimpleSetter()
    {
        $method = new PHP_Depend_Code_Method("setFoo");
        $method->addChild(new PHP_Depend_Code_ASTFormalParameters());
        $method->addChild($scope = new PHP_Depend_Code_ASTScope());
        $scope->addChild($stmt = new PHP_Depend_Code_ASTStatement);
        $stmt->addChild($assignment = new PHP_Depend_Code_ASTAssignmentExpression());
        $assignment->addChild($this->createMemberPrimaryPrefix('$this'));
        $assignment->addChild(new PHP_Depend_Code_ASTVariable());

        return $method;
    }

    private function createSimpleGetterAccessing($objectName)
    {
        $method = new PHP_Depend_Code_Method("getFoo");
        $method->addChild(new PHP_Depend_Code_ASTFormalParameters());
        $method->addChild($scope = new PHP_Depend_Code_ASTScope());
        $scope->addChild($return = new PHP_Depend_Code_ASTReturnStatement());
        $return->addChild($this->createMemberPrimaryPrefix($objectName));

        return $method;
    }

    private function createMemberPrimaryPrefix($objectName)
    {
        $prefix = new PHP_Depend_Code_ASTMemberPrimaryPrefix();
        $prefix->addChild(new PHP_Depend_Code_ASTVariable($objectName));
        $prefix->addChild(new PHP_Depend_Code_ASTPropertyPostfix());

        return $prefix;
    }
}

