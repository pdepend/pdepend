<?php
class InvalidTokenObjectOperatorInForeachLoopBug161Test
{
    public $key = null;
    public $value = null;

    public function invalidTokenObjectOperator( array $messages )
    {
        $this->key   = null;
        $this->value = null;
        foreach ( $messages as $this->key => $this->value )
        {
            echo $this->key, ') ', $this->value, PHP_EOL;
        }
    }
}

$obj = new InvalidTokenObjectOperatorInForeachLoopBug161Test();
$obj->invalidTokenObjectOperator( array( 'foo', 'bar', 'baz' ) );