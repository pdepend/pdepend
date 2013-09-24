<?php
class testAllocationExpressionForObjectProperty
{
    public function foo( stdClass $x )
    {
        return new $x->classes[0];
    }
}
