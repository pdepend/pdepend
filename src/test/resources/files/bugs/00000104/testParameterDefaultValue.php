<?php
class testParameterDefaultValue
{
    protected function foo($bar = [ 'a' => array( 'b', [ 23, 42 => 'baz' ] ) ])
    {
    }
}
