<?php
class InvalidTokenObjectOperatorInForeachLoopBug161Test
{
    public $key = null;

    public function invalidTokenObjectOperator( array $messages )
    {
        $this->key = null;
        foreach ( $messages as $this->key => $value )
        {
            echo $this->key, ') ', $value, PHP_EOL;
        }
    }
}

$obj = new InvalidTokenObjectOperatorInForeachLoopBug161Test();
$obj->invalidTokenObjectOperator( array( 'foo', 'bar', 'baz' ) );