<?php
class testAllocationExpressionForSelfProperty
{
    private static $x = array(__CLASS__);

    public static function foo()
    {
        return new self::$x[0];
    }
}
