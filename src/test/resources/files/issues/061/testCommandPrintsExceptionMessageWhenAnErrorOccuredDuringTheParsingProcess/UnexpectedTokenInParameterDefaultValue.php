<?php
/**
 * @package issue061
 */
class UnexpectedTokenInParameterDefaultValue
{
    public function bar($foobar = array(function))
    {

    }
}
?>
