<?php
class testMagicClassConstantDoesNotResultInExceptionAsArrayDefaultValue
{
    protected $foo = array(__CLASS__);
}