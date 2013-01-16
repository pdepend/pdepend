<?php

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

    private function createSimpleGetterAccessing($objectName)
    {
        $method = new PHP_Depend_Code_Method("getFoo");
        $method->addChild(new PHP_Depend_Code_ASTFormalParameters());
        $method->addChild($scope = new PHP_Depend_Code_ASTScope());
        $scope->addChild($return = new PHP_Depend_Code_ASTReturnStatement());
        $return->addChild($prefix = new PHP_Depend_Code_ASTMemberPrimaryPrefix());
        $prefix->addChild(new PHP_Depend_Code_ASTVariable($objectName));
        $prefix->addChild(new PHP_Depend_Code_ASTPropertyPostfix());

        return $method;
    }
}

