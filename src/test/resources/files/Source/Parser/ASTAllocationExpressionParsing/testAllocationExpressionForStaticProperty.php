<?php
class testAllocationExpressionForStaticProperty
{
    private static $x = array(__CLASS__);

    public static function foo()
    {
        return new static::$x[0];
    }
}
