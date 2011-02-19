<?php
class PHP_Depend_Parser
{
    const C_FOO = 42;
    public function parse($foo = self::C_FOO, Bar $bar, $foobar = array(C_FOOBAR)) {
    }
}