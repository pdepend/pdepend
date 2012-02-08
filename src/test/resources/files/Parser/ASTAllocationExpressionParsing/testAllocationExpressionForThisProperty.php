<?php
class testAllocationExpressionForThisProperty
{
    private $x = array(__CLASS__);

    public function foo()
    {
        return new $this->x[0]();
    }
}
