<?php
class InvalidTokenObjectOperatorInForeachLoopBug161Test
{
    public static $key = null;

    public function invalidTokenObjectOperator( array $messages )
    {
        self::$key = null;
        foreach ( $messages as self::$key => $value )
        {
            echo self::$key, ') ', $value, PHP_EOL;
        }
    }
}

$obj = new InvalidTokenObjectOperatorInForeachLoopBug161Test();
$obj->invalidTokenObjectOperator( array( 'foo', 'bar', 'baz' ) );