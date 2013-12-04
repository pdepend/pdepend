<?php
class testClassFqnPostfixStructureWithSelf
{
    public function foo()
    {
        return self::class;
    }
}
