<?php
/**
 * @package package10
 */
class VariableClassNamesBug10
{
    public function foo10($className)
    {
        $object = new $className();
    }
}
