<?php
class testClassFqnPostfixStructureWithParent extends ArrayAccess
{
    public function foo()
    {
        return parent::class;
    }
}
