<?php
class testClassFqnPostfixStructureWithParent extends DivisionByZeroError
{
    public function foo()
    {
        return parent::class;
    }
}
