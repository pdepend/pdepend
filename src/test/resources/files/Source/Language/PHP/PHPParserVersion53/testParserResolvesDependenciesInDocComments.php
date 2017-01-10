<?php
use A\Dependent\Property;
use A\Dependent\Exception;
use A\Dependent\ReturnValue;

class DependenciesInDocComments
{
    /**
     * @var Property
     */
    public $foo;

    /**
     * @param Dependency $x
     * @throws Exception
     * @return ReturnValue
     */
    public function foo($x)
    {

    }
}
