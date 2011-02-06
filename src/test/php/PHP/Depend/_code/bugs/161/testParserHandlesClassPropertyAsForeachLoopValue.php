<?php
class InvalidTokenObjectOperatorInForeachLoopBug161Test
{
    public static $value = null;

    public function invalidTokenObjectOperator( array $messages )
    {
        self::$value = null;
        foreach ( $messages as $key => self::$value )
        {
            echo $key, ') ', self::$value, PHP_EOL;
        }
    }
}

$obj = new InvalidTokenObjectOperatorInForeachLoopBug161Test();
$obj->invalidTokenObjectOperator( array( 'foo', 'bar', 'baz' ) );