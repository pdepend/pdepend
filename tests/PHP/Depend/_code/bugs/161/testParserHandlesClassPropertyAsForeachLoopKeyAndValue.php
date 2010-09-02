<?php
class InvalidTokenObjectOperatorInForeachLoopBug161Test
{
    public static $key = null;
    public static $value = null;

    public function invalidTokenObjectOperator( array $messages )
    {
        foreach ( $messages as self::$key => self::$value )
        {
            echo self::$key, ') ', self::$value, PHP_EOL;
        }
    }
}

$obj = new InvalidTokenObjectOperatorInForeachLoopBug161Test();
$obj->invalidTokenObjectOperator( array( 'foo', 'bar', 'baz' ) );